<?php
class Authenticate
{
	private $db;
	private $ssid;
	private static $instance = NULL;
	private $login_record;
	private $permission;
	private $config;
	private $t;

	private function __construct(Logger $t, Config $config, Database $db)
	{
		$this->t = $t;
		$this->config = $config;
		$this->db = $db->getDB();
		$this->permission = 51;
	}

	public static function getInstance(Logger $t, Config $config, Database $db)
	{
		if (!self::$instance)
			self::$instance = new Authenticate($t, $config, $db);
		return (self::$instance);
	}

	public function authenticate()
	{
		$this->t->log("Start Authenticate");
		$this->ssid = $this->getSsid();
		if (!$this->ssid)
		{
			$this->t->log("SSID not found continue with permission 51");
			return ;
		}
		$this->getLoginData();
		if (empty($this->login_record))
		{
			$this->t->log("Login data not found continue with permission 51");
			return ;
		}
		if ($this->isExpired())
		{
			$this->t->log("This session is already expired continue with permission 51");
			return ;
		}
		$this->t->log("Successful Authenticate updating permission");
		$this->updateLastused();
		$this->permission = $this->login_record['users_permission'];
	}

	private function getSsid()
	{
		if (isset($_COOKIE['SSID']))
			$ssid = $_COOKIE['SSID'];
		else if (isset($_POST['request_ssid']))
			$ssid = $_POST['request_ssid'];
		return ($ssid);
	}

	private function getLoginData()
	{
		$sql = "SELECT * FROM logins WHERE token='$this->ssid'";
		$result = $this->db->query($sql);
		if (!$result)
		{
			exit();
		}
		$row = $result->fetch_assoc();
		return ($row);
	}

	private function isExpired()
	{
		$lastused = strtotime($this->login_record['lastused']);
		$now = strtotime('now');
		return ($now - $lastused > 200000);
	}

	private function updateLastused()
	{
		$sql = "UPDATE logins SET lastused=NOW() WHERE id='{$this->login_record['id']}'";
		$this->db->query($sql);
	}

	public function checkPermission()
	{
		$commands = $this->config->get('commands');
		$request_type = $this->config->get('request_type');
		if (in_array($request_type, $commands[$this->permission]))
			return (true);
		return (false);
	}
}
?>
