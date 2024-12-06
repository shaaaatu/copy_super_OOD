<?php
class Logger
{
	private static $instance;
	private $start_micro;
	private $start_second;
	private $logs;

	private function __construct()
	{
		$this->startLog();
	}

	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new Logger;
		return (self::$instance);
	}

	private function startLog()
	{
		[$this->start_micro, $this->start_second] = explode(' ', microtime());
		$this->logs = new stdClass;
	}

	public function log($text)
	{
		$this->logs->{$this->getTime()} = $text;
	}

	private function getTime()
	{
		[$log_micro, $log_second] = explode(' ', microtime());
		$elapsed_micro = $log_micro - $this->start_micro;
		$elapsed_second = $log_second - $this->start_second;
		$elapsed_time = $elapsed_micro + $elapsed_second;
		/* return ($elapsed_time); */
		return (number_format($elapsed_time, 8, '.', ''));
	}

	public function get($key)
	{
		return ($this->$key ?? NULL);
	}
}
?>
