<?php
// $lat = 42.33;
// $long = -83.04;

// error_reporting(~0);
// ini_set('display_errors',1);


function reverseGeocode($lat,$long){
	//echo "calling twitter";
	$query = "&lat=".$lat."&lon=".$long;
	//echo $query;
	
	$url = 
	"http://nominatim.openstreetmap.org/reverse?email=mjbradburn@yahoo.com&format=json".$query;
	//echo "url is: ".$url;
	//$address = urlencode($url);
	$resp_json = file_get_contents($url);
	//echo "resp_json: ".$resp_json;// = strip_tags($resp_json);
	//echo $resp_json;
	$resp = json_decode($resp_json,true);
	//echo "response: ".$resp;
	//echo $resp['status'];

	if($resp){
		//echo "status is ok";
		$state = $resp['address']['state'];

			//$long = $resp['results'][0]['geometry']['location']['lng'];
		if ($state){
			//$coordinates['lat'] = $lat;
			//$coordinates['long'] = $long;
			//echo "state: ".$state;
			return $state;
		} else {
			return false;
		}
	} else {
		// $error = json_last_error_msg();
		// echo $error;
		return false;
	}
}
//reverseGeocode($lat,$long);
?>