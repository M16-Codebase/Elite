<?php
global $rpc;
$rpc=getArda($item_id);
function getArda($id){
    $mysqli = new mysqli('localhost', 'eliteman', 'eliteman', 'elite3');
    if ($mysqli->connect_errno) {
		echo("Connect failed: $mysqli->connect_error\n");
		die();
	}
	if($result = $mysqli->query("SELECT `rent_price` FROM `elite3`.`items` WHERE `id` = '$id'")){
		$row = $result->fetch_array(MYSQLI_NUM);
		//echo 'pesos'.$row[0];
		$rpc=$row[0];
	}else{
		echo("eror: ".$mysqli->error);
	}
	$mysqli->close();
	return $rpc;
}
?>