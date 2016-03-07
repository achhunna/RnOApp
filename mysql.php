<?php
	//MySQL database connection file

	//MySQL database parameters
	public static $dbHost = "";
	public static $dbUser = "";
	public static $dbPassword = "";
	public static $dbName = "";

	$connection = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);
	if(!$connection)
    {
      echo 'Failed to connect:' . mysqli_connect_error();
    }
?>
