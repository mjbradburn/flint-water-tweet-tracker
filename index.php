<?php
require_once('TwitterAPIExchange.php');
require_once('Geocode.php');
/** Set access tokens **/
$settings = array (
	'oauth_access_token'=>"363822513-CPj3YUXqvSiUjd6819PRqGmafXKiNVXyG6R69j3j",
	'oauth_access_token_secret'=>"8u3Iwa24EsoxynyJrsklgOOx0GJbNYrcnWe9lXIZviwiy",
	'consumer_key'=>"O6ltnFO2colJblrbNRewDPfNq",
	'consumer_secret'=>"QKFj889PAgPKoHbUUUTzd5lCFqHCknudBRsTu0JPBQPSB5rCaO"
	);

$url = "https://api.twitter.com/1.1/search/tweets.json";
$requestMethod = "GET";
$getfield = '?q=%23FlintWaterCrisis&src=tyah&count=100&result_type=recent';
$twitter = new TwitterAPIExchange($settings);

$string = json_decode($twitter->setGetField($getfield)
	->buildOauth($url,$requestMethod)
	->performRequest(),$assoc = TRUE);

if (file_exists('coordinates.json')){
 	$saved_coords = json_decode(file_get_contents('coordinates.json'),true);
} else {
	$saved_coords['flint'] = array('lat'=>43.01,'long'=>83.69);
}

foreach($string["statuses"] as $statuses){

	$location = strtolower($statuses['user']['location']);
	//check to see if we already have that location OR get it.
	if($location && !array_key_exists ( $location , $saved_coords )){
		$coordinates = geocode($location);
		$saved_coords[$location] = array('lat'=>$coordinates['lat'],'long'=>$coordinates['long']);
	}

	echo "Created at: " .$statuses['created_at']."<br />" ;
	echo "Location: " .$location. "<br />" ;
	
	if($location){
/*		echo "Latitude: ".$coordinates['lat']."<br />";
		echo "Longitude: ".$coordinates['long']."<br />";*/
		echo "Latitude: ".$saved_coords[$location]['lat']."<br />";
		echo "Longitude: ".$saved_coords[$location]['long']."<br />";
	}

	echo "Followers: " .$statuses['user']['followers_count']."<br /><hr />" ;

	/*echo "Time and Date of Tweet: ".$items['created_at']."<br />";
	echo "Tweet: ". $items['text']."<br />";
	echo "Tweeted by: ". $items['user']['name']."<br />";
	echo "Screen name: ". $items['user']['screen_name']."<br />";
	echo "Followers: ". $items['user']['followers_count']."<br />";
	echo "Friends: ". $items['user']['friends_count']."<br />";
	echo "Listed: ". $items['user']['listed_count']."<br />";
	echo "Location:". $items['user']['location']."<br /><hr />";*/
}
$num_saved = count($saved_coords);
echo "Number of saved locations: ".$num_saved."<br />";

file_put_contents('coordinates.json',json_encode($saved_coords));
/*echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();*/

if($string["errors"][0]["message"] != ""){
	echo "<h3>Sorry, there was a problem.</h3>
	<p>Twitter returned the following error message:</p>
	<p><em>".$string[errors][0]["message"]."</em></p>";exit();
}

echo "<pre>";
//print_r($string);
echo "done";
echo "</pre>";



?>