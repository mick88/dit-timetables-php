<?php
require_once('tmt_miner.php');

$ical_head = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//declancurran.me/timetables//NONSGML v1.0//EN
";
$ical_foot = "END:VCALENDAR";
$ical_event_format = "BEGIN:VEVENT
UID:%s@declancurran.me
DTSTAMP:%s
DTSTART;TZID=Europe/Dublin:%s
DTEND;TZID=Europe/Dublin:%s
SUMMARY:%s
LOCATION:%s
DESCRIPTION:%s
RRULE:FREQ=WEEKLY
END:VEVENT
";

class ical_export
{
	public static function event($day, $timerange, $module, $location, $info)
	{
		global $ical_event_format;

		$timerange = explode('-', $timerange);

		$start = explode(':', $timerange[0]);
		$start = mktime($start[0], $start[1], 0, 1, 4 + $day, 2010);

		$end = explode(':', $timerange[1]);
		$end = mktime($end[0], $end[1], 0, 1, 4 + $day, 2010);

		$event = sprintf($ical_event_format,
				md5(uniqid(mt_rand(), true)),
				date('Ymd') . 'T' . date('His'),
				date('Ymd', $start) . 'T' . date('His', $start),
				date('Ymd', $end) . 'T' . date('His', $end),
				$module,
				$location,
				$info
			);

		return $event;
	}

	public static function load($timetable)
	{
		global $ical_head, $ical_foot;
		$ics = $ical_head;

		foreach ($timetable as $day => $classes)
		{
			foreach ($classes as $id => $class)
			{
				$ics .= ical_export::event($day, $class['time'], $class['module'], 
					$class['siteroomcode'],
					$class['activitytype'] . '\n' .
					$class['lecturer']);
			}
		}

		$ics .= $ical_foot;

		return $ics;
	}
}
?>