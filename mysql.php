<?php
	//MySQL database connection file

	//MySQL database parameters
	public static $dbhost = "";
	public static $dbuser = "";
	public static $dbpassword = "";
	public static $dbname = "";
	
	$connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
	if(!$connection)
    {
      echo 'Failed to connect:' . mysqli_connect_error();
    }
?>
