<?php

require_once('../simpletest/autorun.php');

class  MinerTest extends UnitTestCase
{
	function setUp()
	{
		require_once '../tmt_miner.php';
	}
	
	function tearDown()
	{
	}
	
	function testResultNotNull()
	{
		$result = tmt_miner::get(201314, 'DT211', 'DT211/3', '4-16');
		$this->assertNotNull($result);
	}
}
?>