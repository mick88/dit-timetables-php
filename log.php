<?php
$config = require('config.php');

class log
{
	public static function write($f, $t)
	{
		global $config;

		$f0 = $f . '.0';
		if (file_exists($f0) and filesize($f0) > $config['log_maxsize'])
		{
			self::rotate($f);
			touch($f0);
		}
		elseif (!file_exists($f0))
		{
			touch($f0);
		}

		file_put_contents($f0, $t . "\n", FILE_APPEND);
	}

	private static function rotate($f)
	{
		global $config;

		if (file_exists($f . '.' . $config['log_rotate']))
		{
			unlink($f . '.' . $config['log_rotate']);
		}

		for ($i = $config['log_rotate'] - 1; $i >= 0; $i--)
		{
			if (file_exists($f . '.' . $i))
			{
				rename($f . '.' . $i, $f . '.' . ($i + 1));
			}
		}
	}
}

?>