<?php
require_once dirname(__FILE__) . '/test.conn.php';

function progress()
{
	global $testData;
	global $ch;
	global $passedTests;
	global $totalTests;
	global $barLength;

	foreach ($testData as $index => $data)
	{
		echo "\nRunning Test " . ($index + 1) . "/$totalTests...\n";
		$testProgress = 0;
		$testbarLength = 50;
		$testProgressBar = str_repeat(' ', $testbarLength);
		echo "[" . $testProgressBar . "] 0%";
		flush();

		curl_setopt($ch, CURLOPT_URL, "http://localhost:8001/api.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data['request']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$testProgress)
		{
			$progressData = json_decode($data, true);
			if (isset($progressData['progress']))
			{
				$testProgress = $progressData['progress'];
				$completed = round($testProgress / 100 * 50);
				$testProgressBar = str_repeat('=', $completed) . str_repeat(' ', 50 - $completed);
				echo "\r[" . $testProgressBar . "] " . $testProgress . "%";
				flush();
			}
			return strlen($data);
		});
		$response = curl_exec($ch);

		if ($response === false)
		{
			echo "\rcurl error: " . curl_error($ch) . "\n";
			continue ;
		}
		if ($response == $data['expected_response'])
		{
			$passedTests++;
			echo "\r{$data['request']['request_type']} passed\n";
		}
		else
		{
				echo "\r\033[31mTest failed for request_type = {$data['request']['request_type']}.\n";
				echo "Expected: {$data['expected_response']}\n";
				echo "Got: $response";
				echo "\033[0m";
		}
		echo '\n';
		flush();
	}
}

$totalTests = count($testData);
$passedTests = 0;
$barLength = 50;
$progressBar = str_repeat(' ', $barLength);

echo "Running Tests...\n";
echo "[" . $progressBar . "] 0%";
flush();
$ch = curl_init();
progress();
curl_close($ch);
echo "\n\n\033[1;34mTest Summary:\033[0m\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests\n";
echo "Failed: " . ($totalTests - $passedTests) . "\n";
?>
