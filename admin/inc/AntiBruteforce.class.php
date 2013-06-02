<?php


class AntiBruteforce
{
	/**
	 * @var SK_Config_Section
	 */
	private $config_section;

	/**
	 * @var AntiBruteforce
	 */
	private static $instance;

	private function __construct()
	{
		$this->config_section = SK_Config::section('antibruteforce');
	}


	/**
	 * @return AntiBruteforce
	 */
	public static function getInstance()
	{
		if ( isset( self::$instance ) )
		{
			return $instance;
		}

		return new self();
	}


	public function trackTry($success)
	{
		$success = (bool) $success;
		if ($success)
		{
			$this->success();
		}
		else
		{
			$this->faild();
		}
	}

	private function success()
	{
		$this->reset();
	}

	private function resetTryCounter()
	{
		$this->config_section->set('try_number', 0);
	}

	private function increaseTryCounter()
	{
		$try_num = (int) $this->config_section->try_number;
		$try_num++;
		$this->config_section->set('try_number', $try_num);
	}

	private function faild()
	{
		$this->increaseTryCounter();

		if ( $this->isLocked() )
		{
			$this->lock();
		}
	}

	private function lock()
	{
		$this->config_section->set('lock_stamp', time());
	}

	private function reset()
	{
		$this->config_section->set('lock_stamp', 0);
		$this->resetTryCounter();
	}

	public function isLocked()
	{
		return ( $this->config_section->try_number >= $this->config_section->try_count );
	}

	public function getLockTime()
	{
		return $this->config_section->lock_time;
	}

	public static function cron_Process()
	{
		$o = self::getInstance();

		$lock_time = $o->config_section->lock_time * 60;
		$lock_stamp = $o->config_section->lock_stamp;

		if ( $lock_stamp == 0 )
		{
			return;
		}

		if ( ( $lock_stamp + $lock_time ) < time() )
		{
			$o->reset();
		}
	}
}