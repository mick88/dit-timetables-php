<?php
require_once('tmt_miner.php');
require_once('ical_export.php');
require_once('log.php');

$config = require('config.php');

class calendar
{
	public static function academicYear()
	{
		if ((int)date("m") < 9)
			return (date("Y", strtotime("-1 year")) . date("y"));
		else
			return (date("Y") . date("y", strtotime("+1 year")));
	}

	public static function filter($timetable, $filters)
	{
		$ret = array();
		foreach ($timetable as $day => $classes)
		{
			$ret[$day] = array();
			foreach ($classes as $id => $class)
			{
				$filtered = 0;
				foreach ($class as $attrib => $value)
				{
					if (isset($filters[$attrib]) and !stristr($value, $filters[$attrib]))
					{
						$filtered = 1;
						break;
					}
				}

				if ($filtered == 0)
					$ret[$day][] = $class;
			}
		}

		return $ret;
	}

	public static function load($course, $semester, $filters)
	{
		global $config;

		$course = strtoupper($course);
		$code = strtok($course, '/');
		$academicyear = self::academicYear();
		$weeks = $semester == 1 ? '4-17' : '23-37';
		$cachefile = $config['cache_folder'] . '/' . str_replace('/', '.', strtolower($course)) . '.' . $semester . '.json';

		$timetable;

		// Check if cache exists and hasn't expired
		if (file_exists($cachefile) and filemtime($cachefile) > (time() - $config['cache_time']))
		{
			// Load cache
			$timetable = json_decode(file_get_contents($cachefile), true);
		}
		else
		{
			// Mine data from webtimetables
			$html = tmt_miner::get($academicyear, $code, $course, $weeks);
			if (!$html)
				self::failure();
			
			$timetable = tmt_miner::process($html);
			if (!$timetable)
				self::failure();
			
			// Store cache
			file_put_contents($cachefile, json_encode($timetable));
		}
		
		$timetable = self::filter($timetable, $filters);
		$calendar = ical_export::load($timetable);

		self::output($calendar);
	}

	public static function output($calendar)
	{
		// Set output details, so people can download it etc
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename="timetable.ics"');
		echo $calendar;
	}

	public static function failure()
	{
		// On failure, 404, so Google Calendar won't update
		header('HTTP/1.0 404 Not Found');
		exit;
	}
}


$filters = array();
foreach ($_REQUEST as $k => $v)
{
	if (substr($k, 0, 2) == 'f_')
	{
		$filters[substr($k, 2)] = $v;
	}
}

calendar::load($_REQUEST['course'], $_REQUEST['semester'], $filters);
log::write('calendar.log', date('d/m/y H:i:s') . ' [' . $_SERVER['REMOTE_ADDR'] . '] ' . json_encode($_GET));

?>