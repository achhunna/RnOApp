<?php
	require('mysql.php');
	include('default.php');
	$userDn = $_SESSION["userDn"];
	$action = $_POST["action"];

	if($action == "updateDisplay"){
		$userSelect = $_POST["userOption"];
		$fcst = $_POST["fcstOption"];
		displayTable($userSelect, $fcst);
	}
	else if ($action == "post"){
		for($i=1; $i<5; $i++){
			${"q".$i."_rm"} = removeComma($_POST["q".$i]);
		}
		$fy_rm = removeComma($_POST["fy"]);
		$notes_apostrophe = str_replace("'","&#39;",$_POST["notes"]);
		$query = "INSERT INTO ".$dbTable." (id, user, fcst, fiscal, title, bu, dept, account, notes, q1, q2, q3, q4, fy, targetchange, timestamp) VALUES ('$_POST[idKey]','$_POST[user]','$_POST[fcst]','$_POST[fiscal]','$_POST[title]','$_POST[bu]','$_POST[dept]','$_POST[account]','$notes_apostrophe','$_POST[q1]','$_POST[q2]','$_POST[q3]','$_POST[q4]','$_POST[fy]','$_POST[targetchange]',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE title = '$_POST[title]', bu = '$_POST[bu]', dept = '$_POST[dept]', account = '$_POST[account]', notes = '$notes_apostrophe', q1 = '$q1_rm', q2 = '$q2_rm', q3 = '$q3_rm', q4 = '$q4_rm', fy = '$fy_rm', targetchange = '$_POST[targetchange]', timestamp = CURRENT_TIMESTAMP;";
		mysqli_query($connection, $query) or die(mysqli_error($connection));
	}
	else if ($action == "delete"){
		$query = "DELETE FROM ".$dbTable." WHERE id=".$_POST['idKey'];
		mysqli_query($connection, $query) or die(mysqli_error($connection));
	}
?>