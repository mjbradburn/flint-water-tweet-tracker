<?php
function geocode($address){
	echo "calling twitter";
	$address = urlencode($address);
	$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
	$resp_json = file_get_contents($url);
	$resp = json_decode($resp_json,true);

		if($resp['status']=='OK'){
			$lat = $resp['results'][0]['geometry']['location']['lat'];
			$long = $resp['results'][0]['geometry']['location']['lng'];
			if ($lat && $long){
				$coordinates['lat'] = $lat;
				$coordinates['long'] = $long;

				return $coordinates;
			} else {
		return false;
		}
	} else {
		return false;
	}
}

?>