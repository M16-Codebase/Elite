<?php
function essb_get_present_settings($position = '') {
	$result = array ();
	$result ["{$position}_activate"] = "true";
	$result ["{$position}_button_style"] = "button";
	$result ["{$position}_template"] = "28";
	$result ["{$position}_more_button_icon"] = "plus";
	$result ["{$position}_more_button_func"] = "1";
	$result ["{$position}_show_counter"] = "true";
	$result ["{$position}_counter_pos"] = "insidebefore";
	$result ["{$position}_total_counter_pos"] = "leftbig";
	$result ["{$position}_fullwidth_align"] = "left";
	$result ["{$position}_button_width"] = "auto";
	$result ["{$position}_networks"] = array ("facebook", "twitter", "google", "linkedin", "pinterest", "buffer", "love", "subscribe" );
	$result ["{$position}_nospace"] = "";
	
	$all_networks = essb_available_social_networks();
	
	$result ["{$position}_networks_order"] = array();
	foreach ($result["{$position}_networks"] as $network) {
		$data = isset($all_networks[$network]) ? $all_networks[$network] : array("name" => $network);
		$result["{$position}_networks_order"][] = $network."|".$data["name"]; 
	}
	
	foreach ($all_networks as $key => $data) {
		if (!in_array($key, $result["{$position}_networks"])) {
			$result["{$position}_networks_order"][] = $key."|".$data["name"];
		}
	}
	
	$result ["{$position}_name_change"] = "true";
	$result ["{$position}_facebook_name"] = "Shares";
	$result ["{$position}_twitter_name"] = "Tweets";
	$result ["{$position}_google_name"] = "+1s";
	$result ["{$position}_pinterest_name"] = "Pins";
	$result ["{$position}_buffer_name"] = "Shares";
	$result ["{$position}_linkedin_name"] = "Shares";
	$result ["{$position}_Love_name"] = "Loves";
	$result ["{$position}_subscribe_name"] = "Join our list";
	
	return $result;
}