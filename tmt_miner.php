<?php
$config = require('config.php');

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
			if ($config['debug'])
			{
				echo ("Unable to log into webtimetables: " . curl_error($curl));
				curl_close($curl);
				exit;
			}
			else
			{
				curl_close($curl);
			}

			return false;
		}

		// Step 2: Get timetable data
		// Format url, request with cookies from login, return data
		curl_setopt($curl, CURLOPT_URL, 
			sprintf($config['url_timetable'], $academicyear, $coursecode, $course, $weeks));

		$data;
		if (!($data = curl_exec($curl)))
		{
			if ($config['debug'])
			{
				echo ("Unable to retrieve timetable data: " . curl_error($curl));
				curl_close($curl);
				exit;
			}
			else
			{
				curl_close($curl);
			}

			return false;
		}
		
		curl_close($curl);

		return $data;
	}

	// Find data in HTML, put it into an array
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
		$rows = $xpath->query('//*[@id="scrollContent"]/div');

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
			$rowid = $rows->item($i)->getAttribute('id');

			// If the row's id starts with r, then it's seperating days
			if(substr($rowid, 0, 1) == 'r')
			{
				$day++;
				$timetable[$day] = array();
			}
			else
			{
				// Find the details in the cell
				$details = $xpath->query('table/tr/td', $thisrow);

				if ($details and $details->length > 0)
				{
					$class = array();

					// Add all data into the array
					// Classes
					$class['classgroup'] = $details->item(0)->textContent;
					$class['classgroupcode'] = $details->item(1)->textContent;

					if ($details->item(2)->textContent != '-' and $details->item(3)->textContent != '-')
					{
						$class['clsgrpsubgrp'] = $details->item(2)->textContent;
						$class['clsgrpsubgrpcode'] = $details->item(3)->textContent;
					}

					// This data is always at the end, so add it from the end
					$class['module'] = $details->item(4)->textContent;

					if ($class['module'] == '-')
					{
						// Mine the ajax site for module name
						$curl = curl_init(
							sprintf($config['url_ajax'], tmt_loader::academicYear(), substr($rowid, 1))
								);
						curl_setopt_array($curl, $config['curl_options']);

						$moduledata;
						if (!($moduledata = curl_exec($curl)))
						{
							curl_close($curl);
						}
						else
						{
							$mdoc = new DOMDocument();
							@$mdoc->loadHTML($moduledata);
							$mdoc->preserveWhiteSpace = false;
							$mdoc->recover = true;


							$mxpath = new DOMXPath($mdoc);

							// Find the main table
							$mrows = $mxpath->query('/html/body/table/tr[2]/td');
							$class['module'] = $mrows->item(0)->textContent;
						}
					}

					$class['activitytype'] = $details->item(5)->textContent;
					$class['siteroomcode'] = $details->item(6)->textContent;
					$class['lecturer'] = $details->item(7)->textContent;

					$class['time'] = substr($details->item(8)->textContent, 0, 5) . '-' . substr($details->item(8)->textContent, -5);
					$class['weeks'] = $details->item(9)->textContent;

					// Add to timetable array
					$timetable[$day][] = $class;
				}
			}
		}

		return $timetable;
	}
}

?>