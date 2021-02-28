<?php
$id = isset($_POST['id'])?$_POST['id']:NULL;
getArda($id);
function getArda($id){
    $mysqli = new mysqli('localhost', 'eliteman', 'eliteman', 'elite3');
    if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	if($result = $mysqli->query("SELECT `is_arenda`,`rent_price` FROM `elite3`.`items` WHERE `id` = '$id'")){
		$row = $result->fetch_array(MYSQLI_NUM);
		echo $row[0];
		echo '|';
		echo $row[1];	
	}else{
		echo("eror: ".$mysqli->error);
	}
	$mysqli->close();
}
?>