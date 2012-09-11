<?php
require_once('tmt_miner.php');
require_once('ical_export.php');
require_once('log.php');

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
		$course = strtoupper($course);
		$code = strtok($course, '/');
		$academicyear = self::academicYear();
		$weeks = $semester == 1 ? '4-17' : '23-37';

		$html = tmt_miner::get($academicyear, $code, $course, $weeks);
		if (!$html)
			self::failure();
		
		$timetable = tmt_miner::process($html);
		if (!$timetable)
			self::failure();
		
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