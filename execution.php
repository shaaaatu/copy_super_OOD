<?php
class Execution
{
	private static $instance = NULL;
	private $output;
	private $config;
	private $db;
	private $t;

	private function __construct(Logger $t, Config $config, Database $db)
	{
		$this->t = $t;
		$this->config = $config;
		$this->db = $db;
	}

	public static function getInstance(Logger $t, Config $config, Database $db)
	{
		if (!self::$instance)
		{
			self::$instance = new Execution($t, $config, $db);
		}
		return (self::$instance);
	}

	public function executeRequest()
	{
		$this->t->log("Executing...");
		foreach ($this->config->get('services') as $key => $service)
		{
			if (in_array($this->config->get('request_type'), $service))
				require_once $_SERVER['DOCUMENT_ROOT'] . "/service/" . $key . "/" . $this->config->get('request_type') . ".php";
		}
		return ($this->output);
	}
}
?>
