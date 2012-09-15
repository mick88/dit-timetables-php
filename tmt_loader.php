<?php
require_once('tmt_miner.php');

$config = require('config.php');

class tmt_loader
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
				return false;
			
			$timetable = tmt_miner::process($html);
			if (!$timetable)
				return false;
			
			// Store cache
			file_put_contents($cachefile, json_encode($timetable));
		}
		
		if (count($filters) > 0)
			$timetable = self::filter($timetable, $filters);
		
		return $timetable;
	}
}