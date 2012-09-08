<?php

$config = require_once('config.php');

class tmt_miner
{
	// Retrieve data from webtimetables
	// Example input: 201213, 'DT228', 'DT228/2', '4-16'
	public static function get($academicyear, $coursecode, $course, $weeks)
	{
		global $config;

		// Step 1: Log in
		// Go to portal login with details in url, get cookies
		$curl = curl_init($config['url_login']);
		curl_setopt_array($curl, $config['curl_options']);

		if (!curl_exec($curl))
		{
			curl_close($curl);

			if ($config['debug'])
				die("Unable to log into webtimetables: <br />\n" . curl_error($curl));

			return false;
		}

		// Step 2: Get timetable data
		// Format url, request with cookies from login, return data
		curl_setopt($curl, CURLOPT_URL, 
			sprintf($config['url_timetable'], $academicyear, $coursecode, $course, $weeks));

		$data;
		if (!($data = curl_exec($curl)))
		{
			curl_close($curl);

			if ($config['debug'])
				die("Unable to retrieve timetable data: <br />\n" . curl_error($curl));

			return false;
		}
		curl_close($curl);

		return $data;
	}

	public static function process($data)
	{
		global $config;

		// New DOM parser
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->recover = true;

		// Attempt to load HTML
		if ($config['debug'])
			$doc->loadHTML($data);
		else
			@$doc->loadHTML($data);
		
		$xpath = new DOMXPath($doc);

		// Find the main table
		$rows = $xpath->query('/html/body/div/div/form/table/tbody/tr');

		if (!$rows or $rows->length < 1)
		{
			if ($config['debug'])
				die('Unable to find any timetable data');
			return false;
		}

		$day = -1;
		$timetable = array();

		// Loop through rows of the table
		for ($i = 0; $i < $rows->length; $i++)
		{
			$thisrow = $rows->item($i);

			// If the row has an empty header, then it's the first element before the next day
			if($thisrow->firstChild->nodeName == 'th' and
				$thisrow->firstChild->nodeValue == '')
			{
				$day++;
				$timetable[$day] = array();
			}
			else
			{
				// Loop through cells in the row
				for ($j = 0; $j < $thisrow->childNodes->length; $j++)
				{
					$thiscell = $thisrow->childNodes->item($j);

					// Find the details in the cell
					$details = $xpath->query('table/tr/td/font', $thiscell);
					if ($details and $details->length > 0)
					{
						$class = array();

						// Add all data into the array
						// Classes
						$class['classgroup'] = substr($details->item(0)->textContent, 0, -2);
						$class['classgroupcode'] = substr($details->item(1)->textContent, 0, -2);

						// If there are sub groups
						if ($details->length > 8)
						{
							$class['clsgrpsubgrp'] = substr($details->item(2)->textContent, 0, -2);
							$class['clsgrpsubgrpcode'] = substr($details->item(3)->textContent, 0, -2);
						}

						// This data is always at the end, so add it from the end
						$class['weeks'] = substr($details->item($details->length - 1)->textContent, 0, -2);
						$class['time'] = substr($details->item($details->length - 2)->textContent, 0, -2);;
						$class['lecturer'] = substr($details->item($details->length - 3)->textContent, 0, -2);;
						$class['siteroomcode'] = substr($details->item($details->length - 4)->textContent, 0, -2);;
						$class['activitytype'] = substr($details->item($details->length - 5)->textContent, 0, -2);;
						$class['module'] = substr($details->item($details->length - 6)->textContent, 0, -2);;

						// Add to timetable array
						$timetable[$day][] = $class;
					}
				}
			}
		}

		return $timetable;
	}
}

$dat = tmt_miner::get(201213, 'DT228', 'DT228/2', '1-37');
$tmt = tmt_miner::process($dat);

print_r($tmt);

?>