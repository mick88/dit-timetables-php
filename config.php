<?php
$config = array();

$config['debug'] = false; // (bool)$_REQUEST['debug'];

// Main urls for the portal and login
$config['url_portal'] = 'http://webtimetables.dit.ie/TTSuiteRBLIVE/PortalServ';
$config['url_login'] = $config['url_portal'] .
	'?reqtype=login&username=students&userpassword=timetables';

// Timetable request url
// Format:  Academic year(201213), Course(DT228), Course/Year(DT228/2), Weeks(4-16)
$config['url_timetable'] = $config['url_portal'] .
	'?reqtype=timetable&sType=class&sKey=%s|%s|%s&sWeeks=%s';

$config['curl_options'] = array(
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true,

		CURLOPT_USERAGENT => 'mozilla/5.0 (windows nt 6.1; wow64) applewebkit/535.1 (khtml, like gecko) chrome/14.0.835.186 safari/535.1',
		CURLOPT_AUTOREFERER => true,
		CURLOPT_COOKIEFILE => '', // Enable cookies with empty string

		CURLOPT_ENCODING => '',

		CURLOPT_CONNECTTIMEOUT => 8,
		CURLOPT_TIMEOUT => 10
	);

return $config;
?>