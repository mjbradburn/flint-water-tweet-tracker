<?php
include 'header.php';
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
$getfield = '?q=%23#flintwatercrisis&src=tyah&result_type=recent&count=100';
$twitter = new TwitterAPIExchange($settings);

$string = json_decode($twitter->setGetField($getfield)
	->buildOauth($url,$requestMethod)
	->performRequest(),$assoc = TRUE);

if (file_exists('coordinates.json')){
 	$saved_coords = json_decode(file_get_contents('coordinates.json'),true);
} else {
	$saved_coords['flint'] = array('lat'=>43.01,'long'=>83.69);
}
$count = 0;
foreach($string["statuses"] as $statuses){
	$count++;
	$location = strtolower($statuses['user']['location']);
	//check to see if we already have that location OR get it.
	if($location && !array_key_exists ( $location , $saved_coords )){
		$coordinates = geocode($location);
		$saved_coords[$location] = array('lat'=>$coordinates['lat'],'long'=>$coordinates['long']);
	}
	$id = $statuses['id'];
	echo "Tweet_ID: ".$id."<br />";
	$username = "@".$statuses['user']['screen_name'];
	echo "Handle: ".$username."<br />";
	//convert date to mysql datetime format
	$twitter_date = $statuses['created_at'];
	$format = 'D M d H:i:s \+\0\0\0\0 Y';
	$new_format_date = DateTime::createFromFormat($format,$twitter_date);
	$created_at = $new_format_date->format('Y-m-d H:i:s');
	
	echo "Created at: " .$created_at."<br />" ;
	echo "Location: " .$location. "<br />" ;
	
	if($location){
		$lat = $saved_coords[$location]['lat'];
		$long = $saved_coords[$location]['long'];
		echo "Latitude: ".$lat."<br />";
		echo "Longitude: ".$long."<br />";
	}

	$followers = $statuses['user']['followers_count'];
	$retweets = $statuses['retweet_count'];
	echo "Followers: " .$followers."<br />" ;
	echo "Retweets: " .$retweets."<br />";

	$text = str_replace("'","\\'",$statuses['text']);
	//echo "<script type='text/javascript'>alert('$text');</script>";
	echo "Text: " .$text."<br /><hr />";
	//$text = mysql_real_escape_string($text);
	//echo "<script type='text/javascript'>alert('$text');</script>";
	//insert into database
	$con = mysqli_connect("localhost","newuser","password","tweetmap");
	if (!$con){ 
		echo "Error: Unable to connect".PHP_EOL;
		echo "Debugging error:".mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error:".mysqli_connect_error().PHP_EOL;
		exit;
	}

	if($lat){
		// $text = "".mysql_real_escape_string($text);
		$query = "INSERT INTO tweets (`id`,`handle`,`created_at`,`latitude`,`longitude`,`followers`,`retweets`,`text`)
		VALUES ('$id','$username','$created_at',$lat,$long,$followers,$retweets,'$text')";
		if (mysqli_query($con,$query)){
			echo "insert success";
		} else {
			echo "insert fail ". mysqli_error($con);
		}
	}
	mysqli_close($con);

}
$num_saved = count($saved_coords);
echo "Tweets found: ".$count."<br />";
echo "Number of saved locations: ".$num_saved."<br />";

file_put_contents('coordinates.json',json_encode($saved_coords));

if($string["errors"][0]["message"] != ""){
	echo "<h3>Sorry, there was a problem.</h3>
	<p>Twitter returned the following error message:</p>
	<p><em>".$string[errors][0]["message"]."</em></p>";exit();
}

echo "<pre>";
print_r(json_encode($string));
echo "</pre>";

?>