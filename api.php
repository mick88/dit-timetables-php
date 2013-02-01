<?php

if (!isset($_REQUEST['course']) or !isset($_REQUEST['semester']))
{
	die('Unable to load timetable and no cache is available. Did you put in your course code?');
}

require_once('tmt_loader.php');
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

$json = array("success" => $timetable != NULL, "data" => $timetable);
echo(json_encode($json));
?>