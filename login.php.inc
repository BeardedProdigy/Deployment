<?php

class loginDB
{
private $logindb;

public function __construct()
{
	$this->logindb = new mysqli("localhost","root","password","zipversions");

	if ($this->logindb->connect_errno != 0)
	{
		echo "Error connecting to database: ".$this->logindb->connect_error.PHP_EOL;
		exit(1);
	}
	echo "correctly connected to database".PHP_EOL;
}

public function validateLogin($username,$password)
{
	$un = $this->logindb->real_escape_string($username);
	$pw = $this->logindb->real_escape_string($password);
	$statement = "select * from login where username  = '$un'";
	$response = $this->logindb->query($statement);

	while ($row = $response->fetch_assoc())
	{
		echo "checking password for $username".PHP_EOL;
		if ($row["password"] == $pw)
		{
			echo "passwords match for $username".PHP_EOL;
			return 1;// password match
		}
		echo "passwords did not match for $username".PHP_EOL;
	}
	return 0;//no users matched username
}

public function validateSignup($username,$password)
{
	$un = $this->logindb->real_escape_string($username);
        $pw = $this->logindb->real_escape_string($password);
	$statement = "select * from login where username  = '$un'";
	$response = $this->logindb->query($statement);

	while ($row = $response->fetch_assoc())
	{
		echo "check username for $username".PHP_EOL;
		$row = $response->fetch_assoc();
		if ($row["username"] == $un)
		{
			echo "username exists".PHP_EOL;
			//echo "test1".PHP_EOL;
			return 1;
			//echo "1".PHP_EOL;
			exit();
			//echo "2".PHP_EOL;
		}
		else{
			$s = "INSERT INTO login (username, password) VALUES ('$username', '$pass			word')";
			$ss = "select * from login where username = '$un'";
			$response = $this->logindb->query($s);
			return 0;
			exit();

		}
	}
}
}
?>
