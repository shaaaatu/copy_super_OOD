<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/authentication.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/execution.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.super.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/log.php';
header('Content-Type: application/json');

function outputResult($config, $result, $t)
{
	$request_data = $config->get('request_data');
	if (isset($request_data->test))
	{
		$apiresult = $result;
	}
	else
	{
		if (isset($request_data->debug))
		{
			$logs = $t->get('logs');
			$result = array_merge(array('verbose' => $logs), array('result' => $result));
		}
		$apiresult = json_encode($result, JSON_PRETTY_PRINT);
	}
	echo $apiresult;
}

$t = Logger::getInstance();
$config = Config::getInstance($t);
$db = Database::getInstance($t);
$auth = Authenticate::getInstance($t, $config, $db);
$auth->authenticate();
$auth->checkPermission();
$execution = Execution::getInstance($t, $config, $db);
$result = $execution->executeRequest();
outputResult($config, $result, $t);
?>
