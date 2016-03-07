<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=100" >
	<title>App</title>
<?php
	require('mysql.php'); //mysql database connections
	require('default.php'); //all default variables and functions
	require('scripts.php'); //all default scripts and css
	include('auth.php'); //LDAP authentication
?>	
</head>
<body>
<table>
	<tr class="ver"><td><img src="images/rno.png" /></td><td><br /><br />v2.0</td></tr>
</table><!--
 <center>
<img src='http://www.11points.com/images/animatedgifs/underconstruction.gif' /><br /><br />
<i>Be right back!</i>
</center>-->
 <?php
	
	$userDn = $_SESSION["userDn"];
	$userFirstname = $_SESSION["userFirstname"];
	$userLastname = $_SESSION["userLastname"];
	
	//check userDn exists in user
	if(checkConnect($connection,'user','alias',$userDn) != 1){
		mysqli_query($connection,"INSERT INTO user (alias, firstName, lastName) VALUES  ('$userDn','$userFirstname','$userLastname')");
		echo "<p>Welcome ".$userFirstname."! You have been added to <i>R&O App</i>.</p>";
	}else{
		echo "<p>Welcome back, <b>".$userFirstname."</b>. Here are your R&O items.</p>";
	}
	//option for forecast scenarios
	echo "<p><form method='post'>";
	echo "Select: ";
	echo "<select name='userOption' id='userOption'>";
	echo "<option value=''>All</option>";
	echo "<option value='".$userDn."' selected>".$userDn."</option>";
	echo "</select>";
	echo "&nbsp;<select name='fcstOption' id='fcstOption'>";
	for($i=0; $i<count($fcstArray); $i++){
		if($fcstArray[$i] == $currFcst){
			echo "<option value='".$fcstArray[$i]."' selected>".$fcstArray[$i]."</option>";
		}else{
			echo "<option value='".$fcstArray[$i]."' >".$fcstArray[$i]."</option>";
		}
	}
	echo "</select>&nbsp;";
	echo "&nbsp;<select name='fiscalOption' id='fiscalOption'>";
	for($i=0; $i<count($fiscal); $i++){
		if($fcst[$i] == $currFiscal){
			echo "<option value='".$fiscal[$i]."' selected>".$fiscal[$i]."</option>";
		}else{
			echo "<option value='".$fiscal[$i]."' >".$fiscal[$i]."</option>";
		}
	}
	echo "</select>&nbsp;";
	echo "<input type='text' id='projectSearch' value='Search project' />";
	echo "</form></p>";

	//header output
	echo "<table id='outputTable'>";
	displayTable($userDn,$currFcst,"*");
	echo "</table><br /><br />";

?> 

 </body>
 <div class="footer">Site maintained by <a href="mailto:amali@ea.com">Achhunna Mali</a>. All Rights Reserved.</div>
</html> 