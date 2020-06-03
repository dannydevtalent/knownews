<?php

$servername='us-cdbr-iron-east-01.cleardb.net';
$username='bd92f737073375';
$password='b1572f0c';
$dbname = "heroku_0324816153f4386";
$conn=mysqli_connect($servername,$username,$password,"$dbname");
if(!$conn){
	 die('Could not Connect MySql Server:' .mysql_error());
}

$request = $_POST['request'];
    							
if($request=="add_feed_url")
{
	$url = $_POST['url'];

	$insert_sql = "INSERT INTO feedurl (feed_url) VALUES ('$url')";
	 if (mysqli_query($conn, $insert_sql)) {	
	 	$get_id = "SELECT id FROM feedurl WHERE feed_url='$url'";
		if ($result = mysqli_query($conn, $get_id)) {
		  // Fetch one and one row
		 
		  while ($row = mysqli_fetch_row($result)) {
		   	print_r($row[0]);
		  }		    
		} 	
	 
	 } else {
	 	echo "<br>";
	    echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
	 }
}

if($request=="change_password"){
	$password = $_POST['password'];
	$update_sql = "UPDATE user SET password = '$password'";
	if (mysqli_query($conn, $update_sql)) {	
	 	echo "success";
	 
	 } else {
	 	echo "<br>";
	    echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
	 }
}

if($request=="update_feed_url")
{
	$url = $_POST['url'];
	$id  = $_POST['id'];

	$update_sql = "UPDATE feedurl SET feed_url = '$url' WHERE id='$id'";
	 if (mysqli_query($conn, $update_sql)) {	
	 	echo "success";
	 
	 } else {
	 	echo "<br>";
	    echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
	 }
}

if($request=="remove")
{
	$id  = $_POST['id'];
	$remove_sql = "DELETE FROM feedurl WHERE id='$id'";
	 if (mysqli_query($conn, $remove_sql)) {	
	 	echo "success";
	 
	 } else {
	 	echo "<br>";
	    echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
	 }
}





if($request=="user_login"){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$login_query = "SELECT * FROM user WHERE username = '$username' and password = '$password'";
	$search_result = mysqli_query($conn, $login_query);
	if (mysqli_num_rows($search_result) > 0) {
		echo "success";
	}
	else{
		echo "fail";
	}

}

if($request=="get_url"){
	$urls_query = "SELECT * FROM feedurl";
	if ($result = mysqli_query($conn, $urls_query)) {
	  // Fetch one and one row
	  $rows=[];
	  while ($row = mysqli_fetch_row($result)) {
	   	$rows[] = $row;
	  }
	  echo json_encode($rows);
	  
	}
}

	
?>
