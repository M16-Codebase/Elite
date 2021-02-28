<?php
function ftMain(){
	
//Вспомогательная переменная - полный адрес страницы
$servurl=$_SERVER['HTTP_HOST'];

//Получение массива данных с комментариями
$shortname='top-100';
$YourPublicAPIKey='gZOJ4V0nyuMIOd0ZRtOCHI301tVn2ZC9NALI2saW53Fe95lPRiDm5QiSncyZ8v8d';
$json = json_decode(file_get_contents("https://disqus.com/api/3.0/forums/listThreads.json?forum=".$shortname."&api_key=".$YourPublicAPIKey),true);
$array = $json['response'];
$art_id = array();

$mysqli = new mysqli('localhost', 'top100', 'X0t1I7c9', 'top-100');
if ($mysqli->connect_error) {
	die('Ошибка подключения (' . $mysqli->connect_errno . ') '
			. $mysqli->connect_error);
}
$mysqli->query("SET NAMES 'utf8mb4'");

//
//
//INTERVIEWS
//
//


//[0]ID
//[1]LINK
//[2]DATE
//[3]COMMENT
//[4]VIEWS
//[5]TITLE
//[6]DESCRIPTION
//[7]FULL TEXT
//[8]IMG URL


if ($posts = $mysqli->query("SELECT `ID`,`post_date`,`post_name` FROM `wp_posts` WHERE `post_status`='publish' AND `post_name` LIKE '%art-%'")) {
	if (($posts->num_rows)>=1){
		$conut=0;
		$perm_id=0;
		while ($row = $posts->fetch_assoc()) {
				$perm_id=$row["ID"];
				$art_id[$conut][0]= $row["ID"];
				$cdate=explode(' ',$row["post_date"]);
				$cdate=explode('-',$cdate[0]);
				$cdate=$cdate[2].'.'.$cdate[1].'.'.$cdate[0];
				$art_id[$conut][2] = $cdate;
				$art_id[$conut][1] = "https://m16-elite.ru/top-100/articles/".$row["post_name"].'/';
				$articleUrl="https://m16-elite.ru/top-100/articles/".$row["post_name"].'/';
				$key = array_search($articleUrl, array_column($array, 'link'));
				$comments= $array[$key]['posts'];
				$art_id[$conut][3]= $comments;
				$meta = $mysqli->query("SELECT `period`,`count` FROM `wp_post_views` WHERE `id`='".$row["ID"]."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
							if($row["period"]=='total'){
								$art_id[$conut][4]= $row["count"];
							}
					}
				}
				$meta->close();
				
				$meta = $mysqli->query("SELECT `meta_key`,`meta_value` FROM `wp_postmeta` WHERE `post_id`='".$perm_id."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
						if($row["meta_key"]=='card_title'){
							$art_id[$conut][5] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_description'){
							$art_id[$conut][6] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_fulltext'){
							$art_id[$conut][7] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_imgurl'){
							$art_id[$conut][8] =$row["meta_value"];
						}
					}
				}
				$conut++;
				$meta->close();
			}
	}
}
$posts->close();
$post_id=array();
if ($posts = $mysqli->query("SELECT `ID`,`post_date`,`post_name` FROM `wp_posts` WHERE `post_status`='publish' AND `post_name` LIKE '%inter-%'")) {
	if (($posts->num_rows)>=1){
		$conut=0;
		$perm_id=0;
		while ($row = $posts->fetch_assoc()) {
				$perm_id=$row["ID"];
				$post_id[$conut][0]= $row["ID"];
				$cdate=explode(' ',$row["post_date"]);
				$cdate=explode('-',$cdate[0]);
				$cdate=$cdate[2].'.'.$cdate[1].'.'.$cdate[0];
				$post_id[$conut][2] = $cdate;
				$post_id[$conut][1] = "https://m16-elite.ru/top-100/interviews/".$row["post_name"].'/';
				$articleUrl="https://m16-elite.ru/top-100/interviews/".$row["post_name"].'/';
				$key = array_search($articleUrl, array_column($array, 'link'));
				$comments= $array[$key]['posts'];
				$post_id[$conut][3]= $comments;
				$meta = $mysqli->query("SELECT `period`,`count` FROM `wp_post_views` WHERE `id`='".$row["ID"]."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
							if($row["period"]=='total'){
								$post_id[$conut][4]= $row["count"];
							}
					}
				}
				$meta->close();
				
				$meta = $mysqli->query("SELECT `meta_key`,`meta_value` FROM `wp_postmeta` WHERE `post_id`='".$perm_id."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
						if($row["meta_key"]=='card_title'){
							$post_id[$conut][5] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_description'){
							$post_id[$conut][6] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_fulltext'){
							$post_id[$conut][7] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_imgurl'){
							$post_id[$conut][8] =$row["meta_value"];
						}
					}
				}
				$conut++;
				$meta->close();
			}
	}
}
$posts->close();

$mysqli->close();
//echo'<pre>';print_r($post_ids);echo'</pre>';
//exit;
$data=array();
$data[0]=$post_id;
$data[1]=$art_id;
return $data;
}
function ftInter(){
	
//Вспомогательная переменная - полный адрес страницы
$servurl=$_SERVER['HTTP_HOST'];

//Получение массива данных с комментариями
$shortname='top-100';
$YourPublicAPIKey='gZOJ4V0nyuMIOd0ZRtOCHI301tVn2ZC9NALI2saW53Fe95lPRiDm5QiSncyZ8v8d';
$json = json_decode(file_get_contents("https://disqus.com/api/3.0/forums/listThreads.json?forum=".$shortname."&api_key=".$YourPublicAPIKey),true);
$array = $json['response'];
$post_id = array();

$mysqli = new mysqli('localhost', 'top100', 'X0t1I7c9', 'top-100');
if ($mysqli->connect_error) {
	die('Ошибка подключения (' . $mysqli->connect_errno . ') '
			. $mysqli->connect_error);
}
$mysqli->query("SET NAMES 'utf8mb4'");

//
//
//INTERVIEWS
//
//


//[0]ID
//[1]LINK
//[2]DATE
//[3]COMMENT
//[4]VIEWS
//[5]TITLE
//[6]DESCRIPTION
//[7]FULL TEXT
//[8]IMG URL


if ($posts = $mysqli->query("SELECT `ID`,`post_date`,`post_name` FROM `wp_posts` WHERE `post_status`='publish' AND `post_name` LIKE '%inter-%'")) {
	if (($posts->num_rows)>=1){
		$conut=0;
		$perm_id=0;
		while ($row = $posts->fetch_assoc()) {
			if($row["ID"]!=get_the_ID()){
				$perm_id=$row["ID"];
				$post_id[$conut][0]= $row["ID"];
				$cdate=explode(' ',$row["post_date"]);
				$cdate=explode('-',$cdate[0]);
				$cdate=$cdate[2].'.'.$cdate[1].'.'.$cdate[0];
				$post_id[$conut][2] = $cdate;
				$post_id[$conut][1] = "https://m16-elite.ru/top-100/interviews/".$row["post_name"].'/';
				$articleUrl="https://m16-elite.ru/top-100/interviews/".$row["post_name"].'/';
				$key = array_search($articleUrl, array_column($array, 'link'));
				$comments= $array[$key]['posts'];
				$post_id[$conut][3]= $comments;
				$meta = $mysqli->query("SELECT `period`,`count` FROM `wp_post_views` WHERE `id`='".$row["ID"]."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
							if($row["period"]=='total'){
								$post_id[$conut][4]= $row["count"];
							}
					}
				}
				$meta->close();
				
				$meta = $mysqli->query("SELECT `meta_key`,`meta_value` FROM `wp_postmeta` WHERE `post_id`='".$perm_id."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
						if($row["meta_key"]=='card_title'){
							$post_id[$conut][5] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_description'){
							$post_id[$conut][6] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_fulltext'){
							$post_id[$conut][7] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_imgurl'){
							$post_id[$conut][8] =$row["meta_value"];
						}
					}
				}
				$conut++;
				$meta->close();
			}else{
				$perm_id=$row["ID"];
				$meta = $mysqli->query("SELECT `meta_key`,`meta_value` FROM `wp_postmeta` WHERE `post_id`='".$perm_id."'");
					while ($row = $meta->fetch_assoc()) {
						if($row["meta_key"]=='card_fulltext'){
							$mmd =$row["meta_value"];
						}
					}
				$meta->close();
			}
		}
	}
}
$posts->close();
$mysqli->close();

$post_ids=array();
$post_ids=array_chunk($post_id, 3);
//echo'<pre>';print_r($post_ids);echo'</pre>';
//exit;
$data=array();
$data[0]=$post_ids;
$data[1]=$mmd;
return $data;
}

function ftArt(){
	
//Вспомогательная переменная - полный адрес страницы
$servurl=$_SERVER['HTTP_HOST'];

//Получение массива данных с комментариями
$shortname='top-100';
$YourPublicAPIKey='gZOJ4V0nyuMIOd0ZRtOCHI301tVn2ZC9NALI2saW53Fe95lPRiDm5QiSncyZ8v8d';
$json = json_decode(file_get_contents("https://disqus.com/api/3.0/forums/listThreads.json?forum=".$shortname."&api_key=".$YourPublicAPIKey),true);
$array = $json['response'];
$post_id = array();

$mysqli = new mysqli('localhost', 'top100', 'X0t1I7c9', 'top-100');
if ($mysqli->connect_error) {
	die('Ошибка подключения (' . $mysqli->connect_errno . ') '
			. $mysqli->connect_error);
}
$mysqli->query("SET NAMES 'utf8mb4'");

//
//
//INTERVIEWS
//
//


//[0]ID
//[1]LINK
//[2]DATE
//[3]COMMENT
//[4]VIEWS
//[5]TITLE
//[6]DESCRIPTION
//[7]FULL TEXT
//[8]IMG URL


if ($posts = $mysqli->query("SELECT `ID`,`post_date`,`post_name` FROM `wp_posts` WHERE `post_status`='publish' AND `post_name` LIKE '%art-%'")) {
	if (($posts->num_rows)>=1){
		$conut=0;
		$perm_id=0;
		while ($row = $posts->fetch_assoc()) {
			if($row["ID"]!=get_the_ID()){
				$perm_id=$row["ID"];
				$post_id[$conut][0]= $row["ID"];
				$cdate=explode(' ',$row["post_date"]);
				$cdate=explode('-',$cdate[0]);
				$cdate=$cdate[2].'.'.$cdate[1].'.'.$cdate[0];
				$post_id[$conut][2] = $cdate;
				$post_id[$conut][1] = "https://m16-elite.ru/top-100/articles/".$row["post_name"].'/';
				$articleUrl="https://m16-elite.ru/top-100/articles/".$row["post_name"].'/';
				$key = array_search($articleUrl, array_column($array, 'link'));
				$comments= $array[$key]['posts'];
				$post_id[$conut][3]= $comments;
				$meta = $mysqli->query("SELECT `period`,`count` FROM `wp_post_views` WHERE `id`='".$row["ID"]."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
							if($row["period"]=='total'){
								$post_id[$conut][4]= $row["count"];
							}
					}
				}
				$meta->close();
				
				$meta = $mysqli->query("SELECT `meta_key`,`meta_value` FROM `wp_postmeta` WHERE `post_id`='".$perm_id."'");
				if (($meta->num_rows)>=1){
					while ($row = $meta->fetch_assoc()) {
						if($row["meta_key"]=='card_title'){
							$post_id[$conut][5] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_description'){
							$post_id[$conut][6] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_fulltext'){
							$post_id[$conut][7] =$row["meta_value"];
						}
						if($row["meta_key"]=='card_imgurl'){
							$post_id[$conut][8] =$row["meta_value"];
						}
					}
				}
				$conut++;
				$meta->close();
			}else{
				$perm_id=$row["ID"];
				$meta = $mysqli->query("SELECT `meta_key`,`meta_value` FROM `wp_postmeta` WHERE `post_id`='".$perm_id."'");
					while ($row = $meta->fetch_assoc()) {
						if($row["meta_key"]=='card_fulltext'){
							$mmd =$row["meta_value"];
						}
					}
				$meta->close();
			}
		}
	}
}
$posts->close();
$mysqli->close();

$post_ids=array();
$post_ids=array_chunk($post_id, 3);
//echo'<pre>';print_r($post_ids);echo'</pre>';
//exit;
$data=array();
$data[0]=$post_ids;
$data[1]=$mmd;
return $data;
}
function breadCrGen(){
	$breadcrumbs='<div class="interview-path-links">';
$depth=explode('/',$_SERVER['REQUEST_URI']);
$depth = array_slice($depth, 1, -1);
foreach($depth as $value){
	if($value=='top-100'){ $breadcrumbs=$breadcrumbs.'<a href="https://m16-elite.ru/" class="interview-path-link">Главная</a><span style="color:#fff;margin:0 5px;">/</span><a href="https://m16-elite.ru/top-100/" class="interview-path-link">TOP-100-PRESS</a>';}
	if($value=='interviews'){ $breadcrumbs=$breadcrumbs.'<span style="color:#fff;margin:0 5px;">/</span><a  href="https://m16-elite.ru/top-100/interviews/" class="interview-path-link">Интервью</a>';}
	if($value=='articles'){ $breadcrumbs=$breadcrumbs.'<span style="color:#fff;margin:0 5px;">/</span><a  href="https://m16-elite.ru/top-100/articles/" class="interview-path-link">Статьи</a>';}
	if(strpos($_SERVER['REQUEST_URI'],'articles') & $value!='interviews' & $value!='articles' & $value!='top-100'){ $breadcrumbs=$breadcrumbs.'<span style="color:#fff;margin:0 5px;">/</span><a class="interview-path-link path-link-active">'.get_field('card_title').'</a>';}
	if(strpos($_SERVER['REQUEST_URI'],'interviews') & $value!='interviews' & $value!='articles' & $value!='top-100'){ $breadcrumbs=$breadcrumbs.'<span style="color:#fff;margin:0 5px;">/</span><a class="interview-path-link path-link-active">'.get_field('card_title').'</a>';}
}
$breadcrumbs=$breadcrumbs.'</div>';
return $breadcrumbs;
}
function init(){
	$mobileP=0;
	$data=array();
	$data[0]=breadCrGen();
	require_once('/var/www/estate/data/www/m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/assets/php/Mobile_Detect.php');
	$detect = new Mobile_Detect;
	if (strpos($_SERVER['HTTP_USER_AGENT'],'ndroid') || strpos($_SERVER['HTTP_USER_AGENT'],'IOS')){
		$mobileP=1;
	}
	$data[1]=$mobileP;
	return $data;
}
?>