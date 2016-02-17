<?php
$con = mysqli_connect("localhost","newuser","password","tweetmap");
if (!$con){ 
	echo "Error: Unable to connect".PHP_EOL;
	echo "Debugging error:".mysqli_connect_errno() . PHP_EOL;
	echo "Debugging error:".mysqli_connect_error().PHP_EOL;
	exit;
}

$query = "SELECT * FROM tweets";
$result = mysqli_query($con,$query);
if (!$result){
	header("HTTP/1.1 500 Internal Server Error");				
} else {
	$rows = array();
	while($r = mysqli_fetch_assoc($result)){
		$rows[] = $r;
	}
}
mysqli_close($con);
print json_encode($rows);
?>
