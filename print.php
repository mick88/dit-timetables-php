<?php
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
if (!$timetable)
{
	die('Unable to load timetable and no cache is available. Did you put in your course code?');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>declancurran.me/timetables</title>
	<style type="text/css">
		body
		{
			background-color: #FFFFFF;
			color: #222222;
			font-family:Helvetica,Arial,'DejaVu Sans','Liberation Sans',Freesans,sans-serif;
			margin: 10px;
		}

		a, a:visited, a:hover, a:active
		{
			color: #000000;
		}

		#main
		{
			width: 1027px;
			/background-color: #DDDDDD;
			padding: 2px;
		}

		#content
		{
			float: right;
			position: relative;
			width: 966px;
			height: 655px;
			/background-color: #ffbbdd;
		}

		.class, .head, .time
		{
			width: 160px;
			height: 40px;
			/margin-left: 1px;
			/margin-bottom: 1px;
			border-right: solid 1px grey;
			border-bottom: solid 1px grey;

			text-align: center;
			/background-color: #999999;
		}

		.class
		{
			font-size: 0.71em;
			line-height: 18px;
			border: solid 1px grey;
		}

		.time
		{
			width: 60px;
			line-height: 36px;
			float: left;
		}

		.head
		{
			height: 20px;
			float: left;
		}

		.linkbox
		{
			float: left;
			width: 60px;
			height: 20px;
			border-right: solid 1px grey;
			border-bottom: solid 1px grey;
			text-align: center;
		}
	</style>

	<style type="text/css" id="classtypes">
		.Lecture
		{
			background-color: #EFBCC4;
		}
		.Laboratory
		{
			background-color: #B9ECBC;
		}
		.Tutorial
		{
			background-color: #C6BDE9;
		}
	</style>

	<script type="text/javascript">
		function colortoggle(link)
		{
			var style = document.getElementById("classtypes");

			// Toggle color visibility
			style.disabled = !style.disabled;

			if (style.disabled)
				link.innerHTML = "color";
			else
				link.innerHTML = "b/w";
		}
	</script>
</head>
<body>
	<div id="main">
		<div class="linkbox"><a href="#" onclick="colortoggle(this);" id="colorlink">b/w</a></div>
		<div class="head">Mon</div>
		<div class="head">Tue</div>
		<div class="head">Wed</div>
		<div class="head">Thur</div>
		<div class="head">Fri</div>
		<div class="head">Sat</div>
		<div style="clear:both;"></div>
		<div id="content">
<?php
	$blockformat = "\t\t\t<div style=\"position:absolute; left: %dpx; top: %dpx; height: %dpx;\" class=\"class %s\">%s</div>\n";
	$latestclass = 0;
	foreach ($timetable as $day => $classes)
	{
		$x = $day * 161 - 1; // -1 for border
		foreach ($classes as $id => $class)
		{
			$times = explode('-', $class['time']);

			$start = explode(':', $times[0]);
			$end = explode(':', $times[1]);

			$start = (int)$start[0] + (int)$start[1] / 60;
			$end = (int)$end[0] + (int)$end[1] / 60;

			$y = ($start - 7) * 41 - 1; // -1 for border
			$len = $end - $start;

			printf($blockformat, $x, $y, $len * 40 + ($len - 1), $class['activitytype'], 
				substr($class['module'], 0, 18) . '<br />' . $class['siteroomcode']);
		}
	}
?>
		</div>
<?php
	for($i = 7; $i < 23; $i++)
	{
?>
		<div class="time"><?php echo $i; ?>:00</div>
<?php
	}
?>
		<div style="clear:both;"></div>
	</div>
</body>
</html>