<?php
require_once('../simpletest/autorun.php');

class ConfigTest extends UnitTestCase
{
	private $config;
	
	function setUp()
	{
		$this->config = require '../config.php';
	}
	
	function testValuesExist()
	{
		$config = $this->config;
		
		$this->assertNotNull($config);
		
		$this->assertNotNull($config['url_root']);
		$this->assertNotNull($config['url_portal']);
		$this->assertNotNull($config['url_login']);
		$this->assertNotNull($config['curl_options']);
		
		$this->assertFalse(empty($config['url_portal']));
	}
	
	function testUrls()
	{
		$config = $this->config;
		
		$res = curl_init($config['url_root']);
		$this->assertFalse($res == false, "curl_init must return a resource. ".$res." returned instead");
		
		$options_result = curl_setopt_array($res, $config['curl_options']);
		
		$result = curl_exec($res);
		$this->assertFalse($result === false, curl_error($res));
		
		curl_close($res);
	}
}
?>