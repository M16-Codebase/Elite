<?php
$stt = isset($_POST['state'])?$_POST['state']:NULL;
$id = isset($_POST['id'])?$_POST['id']:NULL;
$comiss = isset($_POST['comiss'])?$_POST['comiss']:NULL;
setArda($stt,$id,$comiss);
function setArda($newid,$id,$comis){
    $mysqli = new mysqli('localhost', 'eliteman', 'eliteman', 'elite3');
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	if($result = $mysqli->query("UPDATE `elite3`.`items` SET `is_arenda` = '$newid' ,`rent_price`='$comis' WHERE `id` = '$id'")){
		print_r("result: ".$result);
	}else{
		echo("eror: ".$mysqli->error);
	}
	$mysqli->close();
}
?>