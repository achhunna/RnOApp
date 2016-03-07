<?php

	//Initiate all variables and php functions

	require('mysql.php'); //mysql database connections
	session_start();

	//initiate variables
	$editSymbol = "&#10000";
	$deleteSymbol = "&#10005";
	$submitSymbol = "&#10003";
	$dbTable = "RnO";

	//current fiscal and upcoming forecast
	$currFiscal = "FY16";
	$fiscal = array(
		0 => "FY16",
		1 => "FY17",
		2 => "FY18"
		);
	$currFcst = "FY16MarFcst";
	$fcstArray = array(
		0 => "FY16OctFcst",
		1 => "FY16JanFcst",
		2 => "FY16MarFcst"
		);
	$currQtr = 3;
	
	//create arrays
	$accounts = array(
		0 => "Advertising",
		1 => "Agency Temps",
		2 => "Central Fund",
		3 => "Comms",
		4 => "Contracted Services",
		5 => "Dev Expenses",
		6 => "Facilities",
		7 => "Marketing",
		8 => "Non-Cap",
		9 => "Salary",
		10 => "T&E",
		11 => "Training",
		);
	$fields = array(
		0 => "fcst_",
		1 => "fiscal_",
		2 => "title_",
		3 => "bu_",
		4 => "dept_",
		5 => "account_",
		6 => "notes_",
		7 => "q1_",
		8 => "q2_",
		9 => "q3_",
		10 => "q4_",
		11 => "targetchange_",
		);
	$count = 1;

	//php methods
	function checkConnect($connection, $table, $field, $value){
		$query = "SELECT * FROM ".$table." WHERE ".$field."='$value'";
		$result = mysqli_query($connection,$query);
		return $result->num_rows!=0;
	}
	function getIpAddresses() {
		$ipAddresses = array();
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$ipAddresses['proxy'] = isset($_SERVER["HTTP_CLIENT_IP"]) ? $_SERVER["HTTP_CLIENT_IP"] : $_SERVER["REMOTE_ADDR"];
			$ipAddresses['user'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else{
			$ipAddresses['user'] = isset($_SERVER["HTTP_CLIENT_IP"]) ? $_SERVER["HTTP_CLIENT_IP"] : $_SERVER["REMOTE_ADDR"];
		}
		return $ipAddresses;
    }
	
	function removeComma($number){
		$randId = (int)str_replace(',', '', $number);
		return $randId;
	}
	
	function displayTable($userSelect, $fcst){
		//display query
		global $connection, $count, $userDn, $editSymbol, $deleteSymbol, $submitSymbol, $fields, $accounts, $fiscal, $currQtr, $currFcst, $dbTable;
		//subtotal variables declaration
		$q1Sum = $q2Sum = $q3Sum = $q4Sum = $fySum = 0;

		$result = mysqli_query($connection,"SELECT * FROM ".$dbTable." WHERE user LIKE '%".$userSelect."%' AND fcst='$fcst'");
		$outputCount = mysqli_num_rows($result);
		//output form
		echo "<tr>";
		echo "<th>ID</th>";
		echo "<th style='width: 35px'>USER</th>";
		echo "<th style='width: 85px;'>FORECAST</th>";
		echo "<th style='width: 40px;'>FISCAL</th>";
		echo "<th style='width: 150px;'>TITLE</th>";
		echo "<th style='width: 35px;'>BU</th>";
		echo "<th style='width: 35px;'>DEPT</th>";
		echo "<th style='width: 100px;'>ACCOUNT</th>";
		echo "<th style='width: 200px;'>NOTES</th>";
		for($i=1; $i<5; $i++){
			echo "<th style='width: 60px;'>Q".$i."</th>";
		}
		echo "<th style='width: 60px;'>FY</th>";
		echo "<th style='width: 50px;'>TARGET?</th>";
		echo "<th style='width: 80px;'>ACTION</th>";
		echo "</tr>";
		while($row = mysqli_fetch_array($result))
		{  			
			echo "<tr id='tr_".$count."' name='rows'>";
			echo "<form action='' method='post'>";
			echo "<td>".$count."</td>";
			echo "<td><input type='text' name='user' id='user_".$count."' value ='".$row['user']."' style='width: 35px' readonly /></td>";
			echo "<td><input type='text' name='fcst' id='fcst_".$count."' value='".$row['fcst']."' style='width: 85px;' class='left' readonly /></td>";
			echo "<td><select name='fiscal' id='fiscal_".$count."' style='width: 55px;' class='left' disabled>";
			for($i=0; $i<3; $i++){
				if($fiscal[$i] == $row['fiscal']){
					echo "<option value='".$fiscal[$i]."' selected >".$fiscal[$i]."</option>";
				}else{
					echo "<option value='".$fiscal[$i]."'>".$fiscal[$i]."</option>";
				}
			}
			echo"</select></td>";
			echo "<td><input type='text' name='title' id='title_".$count."' value='".$row['title']."' style='width: 150px;' class='left' readonly /></td>";	
			echo "<td><input type='text' name='bu' id='bu_".$count."' value='".$row['bu']."' style='width: 35px;' maxlength='4' readonly /></td>";
			echo "<td><input type='text' name='dept' id='dept_".$count."' value='".$row['dept']."' style='width: 35px;' maxlength='4' readonly /></td>";
			echo "<td><select name='account' id='account_".$count."' style='width: 100px;' disabled>";
			for($i=0; $i<12; $i++){
				if($accounts[$i] == $row['account']){
					echo "<option value='".$accounts[$i]."' selected >".$accounts[$i]."</option>";
				}else{
					echo "<option value='".$accounts[$i]."'>".$accounts[$i]."</option>";
				}
			}
			echo"</select></td>";
			echo "<td><input type='text' name='notes' id='notes_".$count."' value='".$row['notes']."' style='width: 200px;' readonly/></td>";
			for($i=1; $i<5; $i++){
				echo "<td><input type='text' name='q".$i."' id='q".$i."_".$count."' value='".number_format($row['q'.$i])."' style='width: 60px;' class='right' onchange='calcFullyear(".$count.")' readonly /></td>";
			}
			echo "<td><input type='text' name='fy' id='fy_".$count."' value='".number_format($row['fy'])."' style='width: 60px;' class='right bold' readonly /></td>";
			echo "<td><input type='text' name='targetchange' id='targetchange_".$count."' value='".$row['targetchange']."' class='center' maxlength='1' style='width: 50px; text-transform: uppercase;' readonly /></td>";
			echo "<td class='button'><input type='button' name='editButton' id='editButton_".$count."' value='".$editSymbol."' onclick='edit(".$count.",".json_encode($fields).",".$outputCount.")'/>&nbsp;<input type='button' name='deleteButton' id='deleteButton_".$count."' value='".$deleteSymbol."' />&nbsp;<input name='submitButton' id='submitButton_".$count."' type='button' value='".$submitSymbol."' disabled />";
			
			echo "<input type='hidden' name='idKey' id='idKey_".$count."' value=".$row['id']." /></td>";

			echo "</form>";
			//subtotal calc
			$q1Sum += $row['q1'];
			$q2Sum += $row['q2'];
			$q3Sum += $row['q3'];
			$q4Sum += $row['q4'];
			$fySum += $row['fy'];
			$count++;
			echo "</tr>";
		}
		
		//selection form
		if($fcst == $currFcst){
			echo "<form name='inputForm' action='' method='post'>";
			echo "<tr><td>".$count."</td>";
			echo "<td><input type='text' name='user' id='user_".$count."' value ='".$userDn."' style='width: 35px' readonly /></td>";
			echo "<td><input type='text' name='fcst' id='fcst_".$count."' value='".$fcst."' style='width: 85px;' class='left select' readonly /></td>";
			echo "<td><select name='fiscal' id='fiscal_".$count."' style='width: 55px;' class='left select'>";
			for($i=0; $i<3; $i++){
				echo "<option value='".$fiscal[$i]."'>".$fiscal[$i]."</option>";
			}
			echo"</select></td>";
			echo "<td><input type='text' name='title' id='title_".$count."' value='' style='width: 150px;'/></td>";
			echo "<td><input type='text' name='bu' id='bu_".$count."' value='' style='width: 35px;' maxlength='4'/></td>";
			echo "<td><input type='text' name='dept' id='dept_".$count."' value='' style='width: 35px;' maxlength='4'/></td>";
			echo "<td><select name='account' id='account_".$count."' style='width: 100px;'>";
			for($i=0; $i<12; $i++){
				echo "<option value='".$accounts[$i]."'>".$accounts[$i]."</option>";
			}
			echo"</select></td>";
			echo "<td><input type='text' name='notes' id='notes_".$count."' value='' style='width: 200px;'/></td>";

			for($i=1; $i<5; $i++){
				echo "<td><input type='text' name='q".$i."' id='q".$i."_".$count."' value='0' size='5' class='right' onclick ='if(this.value == \"0\") {this.value=\"\"}' onblur='this.value=!this.value?0:this.value;' onchange='calcFullyear(".$count.")'/></td>";
			}
			echo "<td><input type='text' name='fy' id='fy_".$count."' value='' size='6' class='right bold select' readonly /></td>";
			echo "<td><input type='text' name='targetchange' id='targetchange_".$count."' value='' class='center' maxlength='1' style='width: 50px; text-transform: uppercase;' /></td>";
			echo "<td class='button'>&nbsp;<input name='submitButton' id='submitButton_".$count."' type='button' value='".$submitSymbol."' class='big'/><input type='hidden' name='idKey' id='idKey_".$count."' value='' /></td></tr>";
			echo "</form>";
		}
		echo "<tr>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td class='bold blue'>SUBTOTAL:</td>";
		echo "<td class='right bold blue' id='q1Total'>".number_format($q1Sum)."</td>";
		echo "<td class='right bold blue' id='q2Total'>".number_format($q2Sum)."</td>";
		echo "<td class='right bold blue' id='q3Total'>".number_format($q3Sum)."</td>";
		echo "<td class='right bold blue' id='q4Total'>".number_format($q4Sum)."</td>";		
		echo "<td class='right bold blue' id='fyTotal'>".number_format($fySum)."</td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
		echo "<script> jqueryFunctions(); </script>"; //run jQuery functions
	}
?>