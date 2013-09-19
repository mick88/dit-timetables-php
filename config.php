<?php
$config = array();

$config['debug'] = false; // (bool)$_REQUEST['debug'];

// Main urls for the portal and login
$config['url_root'] = 'https://www.dit.ie/timetables/';
$config['url_portal'] = $config['url_root'] . 'PortalServ';
$config['url_login'] = $config['url_portal'] .
	'?reqtype=login&username=students&userpassword=timetables';

// Timetable request url
// Format:  Academic year(201213), Course(DT228), Course/Year(DT228/2), Weeks(4-16)
$config['url_timetable'] = $config['url_portal'] .
	'?reqtype=timetable&ttType=CLASS&sKey=%s|%s|%s&weeks=%s';

// Ajax request url
// Format: Academic year (201314), Module id
$config['url_ajax'] = $config['url_root'] . 'AjaxServ?reqtype=ajaxtteventdetails&pageAction=MODULE&currentJsp=/timetables/jsp/timetable2/timetable.jsp&eventId=%s|%s';

$config['curl_options'] = array(
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true,

		CURLOPT_USERAGENT => 'mozilla/5.0 (windows nt 6.1; wow64) applewebkit/535.1 (khtml, like gecko) chrome/14.0.835.186 safari/535.1',
		CURLOPT_AUTOREFERER => true,
		CURLOPT_COOKIEFILE => 'cookies.txt', // Enable cookies
		CURLOPT_COOKIEJAR => 'cookies.txt',
		CURLOPT_SSL_VERIFYPEER => false, // Ignore SSL

		CURLOPT_ENCODING => '',

		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT => 8
	);

$config['log_maxsize'] = 3000000; // 3MB
$config['log_rotate'] = 4;

$config['log_file_calendar'] = 'calendar.log';

$config['cache_folder'] = 'cache';
$config['cache_time'] = 3.5 * 60 * 60;

return $config;
?>