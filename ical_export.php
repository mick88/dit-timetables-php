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
DTSTART:%s
DTEND:%s
SUMMARY:%s
DESCRIPTION:%s
RRULE:FREQ=WEEKLY
END:VEVENT
";

class ical_export
{
	public static function event($day, $range, $module, $info)
	{
		global $ical_event_format;

		$range = explode('-', $range);

		$start = explode(':', $range[0]);
		$start = mktime($start[0], $start[1], 0, 1, 3 + $day, 2000);

		$end = explode(':', $range[1]);
		$end = mktime($end[0], $end[1], 0, 1, 3 + $day, 2000);

		$event = sprintf($ical_event_format,
				md5(uniqid(mt_rand(), true)),
				date('Ymd') . 'T' . date('His') . 'Z',
				date('Ymd', $start) . 'T' . date('His', $start) . 'Z',
				date('Ymd', $end) . 'T' . date('His', $end) . 'Z',
				$module,
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
					$class['activitytype'] . '\n' .
					$class['siteroomcode'] . '\n' .
					$class['lecturer']);
			}
		}

		$ics .= $ical_foot;

		return $ics;
	}
}

/*
$dat = tmt_miner::get(201213, 'DT228', 'DT228/2', '1-37');
$tmt = tmt_miner::process($dat);

echo ical_export::load($tmt);
*/
?>