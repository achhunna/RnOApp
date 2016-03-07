<?php
//jQuery autocomplete query file

require('mysql.php'); //mysql database connections
			
$term = $_GET["term"];
$field = $_GET["field"];

$sql = "SELECT DISTINCT ".$field." FROM RnO WHERE ".$field." LIKE '%".$term."%' ORDER BY ".$field;		
$result = mysqli_query($connection,$sql) or die(mysqli_error());

$return_array = array();

if($result){
	while($row = mysqli_fetch_array($result)){
	$return_array[] = $row[$field]; 
	}
}
echo json_encode($return_array);

?>