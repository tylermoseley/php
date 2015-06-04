<?php

$address = "4736 Fronteir Rd";
$city = "Pace";
$state = "FL";

$zip = get_zip_from_address($address, $city, $state);
//print_r($zip);

function get_zip_from_address($addr_in,$city_in, $state_in) {
	$address_string = $addr_in . " " . $city_in . " " . $state_in;	
	$url = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . urlencode($address_string);
	//echo $url . "\n";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$c_response = curl_exec($ch);
	//print_r($c_response);
	curl_close($ch);
	$results = json_decode($c_response, true);	
	
	//print_r($results);
	
	$zip = $results['results'][0]['address_components'][7]['short_name'];
	if (!empty($results)) {
		$results = "empty_zip";
	} else {
		//
	}
	//return $zip;
	echo $zip . "\n";
}

?>
