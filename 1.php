<?php
	
	
	function update_news($news_url,$flag){
		$servername='localhost';
		$username='root';
		$password='passPASS1!';
		$dbname = "news";
		$conn=mysqli_connect($servername,$username,$password,"$dbname");
		if(!$conn){
			 die('Could not Connect MySql Server:' .mysql_error());
		}

		$url1=$news_url;
		//  Initiate curl
		$xmlfile = file_get_contents($url1);


		
		$news1_string = simplexml_load_string($xmlfile);


		$news1_array = json_decode( json_encode($news1_string) , 1);

		if($flag=="2"){
			$news1_item_array = $news1_array['item'];
		}
		else if($flag=="1"){
			$news1_item_array = $news1_array['channel']['item'];
		}
		

		for($i =0; $i<sizeof($news1_item_array); $i++){
			$item_title = $news1_item_array[$i]['title'];
			$item_title = str_replace("'","",$item_title);

			$item_link  = $news1_item_array[$i]['link'];

			$item_json = json_encode($news1_item_array[$i]);
			$item_json = str_replace("'","",$item_json);


			$check_sql = "SELECT id FROM update_news WHERE search_title='$item_title' AND search_link='$item_link'";
			$search_result = mysqli_query($conn, $check_sql);

			if (mysqli_num_rows($search_result) == 0) {
				$today = date("Y/m/d");
				$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
			     VALUES ('$item_json','$today','1','$item_title','$item_link')";
			 
			     if (mysqli_query($conn, $insert_sql)) {
			     	// echo "<br>";
			         echo "New record has been added successfully !";
			     	
			     } else {
			     	echo "<br>";
			        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
			     }
			}  
		}

		mysqli_close($conn);
	}

	update_news("http://search.nejm.org/search?cnt=20&start_month=7&start_year=2009&w=*&restrict=doctype%3Aarticle&srt=0&isort=date&ts=rss&af=topic:5","2");

	 update_news("https://www.news-medical.net/tag/feed/Dermatology.aspx","1");
	 update_news("https://www.healio.com/sws/feed/news/dermatology","1");
	 update_news("https://www.dermatologyadvisor.com/feed/","1");

	 
?>
<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
	<title>Football</title>
</head>
<body style="width: 50%; margin: 0 auto">
	
</body>
</html>

