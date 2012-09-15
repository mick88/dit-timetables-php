<?php
require_once('tmt_loader.php');
require_once('ical_export.php');
require_once('log.php');

$config = require('config.php');

// Prepare filters
$filters = array();
foreach ($_REQUEST as $k => $v)
{
	if (substr($k, 0, 2) == 'f_')
	{
		$filters[substr($k, 2)] = $v;
	}
}

// Load timetable
$timetable = tmt_loader::load($_REQUEST['course'], $_REQUEST['semester'], $filters);
if (!$timetable)
{
	header('HTTP/1.0 404 Not Found');
	exit;
}

// Export timetable as iCal
$calendar = ical_export::load($timetable);

// Output calendar
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="timetable.ics"');
echo $calendar;

// Log access
log::write($config['log_file_calendar'], date('d/m/y H:i:s') . ' [' . $_SERVER['REMOTE_ADDR'] . '] ' . json_encode($_GET));

?>