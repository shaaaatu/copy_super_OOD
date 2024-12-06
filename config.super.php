<?php
class Config
{
	private $request_type;
	private $request_data;
	private static $instance = NULL;
	private $services;
	/* public $type; */
	private $commands;
	private $t;

	private function __construct(Logger $t)
	{
		$this->t = $t;
		$this->request_type = $this->setRequestType();
		$this->request_data = $this->setRequestData();
		/* $this->type = new stdClass; */
		$this->services = [
			'login' => ['login', 'check_login'],
			'test' => ['hello'],
		];

		/* $this->type->login = [ */
		/* 	'login', */
		/* 	'check_login' */
		/* ]; */

		/* $this->type->test = [ */
		/* 	'hello' */
		/* ]; */

		$this->commands[1] = [
			'login',
			'check_login',
			'hello',
		];

		$this->commands[50] = [
			'login',
			'check_login',
			'hello',
		];

		$this->commands[51] = [
			'login',
			'check_login',
			'hello',
		];
	}

	public static function getInstance(Logger $t)
	{
		if (!self::$instance)
			self::$instance = new Config($t);
		return (self::$instance);
	}

	private function setRequestType()
	{
		if (!isset($_POST['request_type']))
		{
			$this->t->log("undefined request type finish");
			exit();
		}
		$request_type = $_POST['request_type'];
		return ($request_type);
	}

	private function setRequestData()
	{
		if (!isset($_POST['request_data']))
		{
			$this->t->log("undefined request data continue...");
			return (NULL);
		}
		$request_data = json_decode($_POST['request_data']);
		return ($request_data);
	}

	public function get($key)
	{
		return ($this->$key ?? NULL);
	}
}

class Database
{
	private static $instance;
	private $db;
	private $t;

	private function __construct(Logger $t)
	{
		$this->t = $t;
		$this->db = $this->connectDB();
	}

	public static function getInstance(Logger $t)
	{
		if (!self::$instance)
			self::$instance = new Database($t);
		return (self::$instance);
	}

	private function connectDB()
	{
		$this->t->log("connecting DB...");
		$config = require_once $_SERVER['DOCUMENT_ROOT'] . '/conn.php';
		$db = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
		if ($db->connect_error)
		{
			$this->t->log("error occured while connecting db");
			exit();
		}
		$db->set_charset("utf8");
		$this->t->log("connect db successful");
		return ($db);
	}

	public function getDB()
	{
		return ($this->db);
	}
}
?>
