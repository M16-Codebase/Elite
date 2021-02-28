<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

getSitemap();
function checkUrl($url){
	
}
function getSitemap(){
	$xmlstr = simplexml_load_file('https://m16-estate.ru/asset/uploads/crm/SiteDataEstate.xml');
	echo '<pre>';print_r($xmlstr); echo '</pre>';
	//$sitemap=array();
	//echo '<pre>';print_r($xmlstr->children()); echo '</pre>';
	//foreach($xmlstr->children() as $value){
		//echo '<pre>';echo $value; echo '</pre>';
	//}
	//echo '<pre>';print_r($sitemap); echo '</pre>';
}
?>