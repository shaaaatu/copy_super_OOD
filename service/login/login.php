<?php
if (!($request_data))
{
	$this->t->log('There is no request data');
	$this->output = 'Please enter your email/username and password';
	return ;
}
if (!($request_data->email || $request_data->username))
{
	$this->t->log('There is no email or username');
	$this->output = 'Please enter your email or username';
	return ;
}
if (!$request_data->password)
{
	$this->t->log('There is no password');
	$this->output = 'Please enter your password';
	return ;
}

$sql = "SELECT * FROM users WHERE email='$request_data->email' OR name='$request_data->username'";
$result = $this->db->query($sql);
if (!$result)
{
	$this->t->log('SQL failed');
	return ;
}
$row = $result->fetch_assoc();
if (empty($row))
{
	$this->t->log('email or username is not correct');
	$this->output('email or username is not correct');
	return ;
}
$hash = $row['password'];
if (!password_verify($request_data->password, $hash))
{
	$this->t->log('Password is not correct');
	$this->output = 'Password is not correct';
	return ;
}
$token = bin2hex(random_bytes(32));
$sql = "INSERT INTO logins
	(token, logindatetime, lastused, valid, users_id, users_uuid, users_permission)
	VALUES (?, NOW(), NOW(), 1, ?, ?, ?);
";
$stmt = $this->db->prepare($sql);
$stmt->execute([$token, $row['id'], $row['uuid'], $row['permission']]);
$this->output = 'Login successfuly!!!';
?>
