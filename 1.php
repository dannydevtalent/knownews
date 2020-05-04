<?php
	require_once 'vendor/autoload.php';

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); 

	$spaceId= "yx2a49crvee2";
	$environmentId = "development";
	$contentTypeId = "article";
	use Contentful\Management\Client;

	$client = new Client('CFPAT-um6p7SkC571k7OlGhPs9X6IHGKqUi_S5KHr9oNP9XzM');
	$environment = $client->getEnvironmentProxy('yx2a49crvee2', 'master');
	$contentType = $client->getContentType($spaceId, $environmentId, $contentTypeId);
	$environmentProxy = $client->getEnvironmentProxy($spaceId, $environmentId);
	$contentType = $environmentProxy->getContentType($contentTypeId);

	use Contentful\Core\Api\Exception;
	use Contentful\Management\Resource\Entry;


	function sendMessage($title){
	    $content = array(
	        "en" => $title
	        );

	    $fields = array(
	        'app_id' => "59d1fdb1-1d5a-4049-81e6-61e1d918975e",
	        'included_segments' => array('All'),
	        'data' => array("foo" => "bar"),
	        'large_icon' =>"kp-icon-1024px.png",
	        'contents' => $content
	    );

	    $fields = json_encode($fields);
	    print("\nJSON sent:\n");
	    print($fields);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
	                                               'Authorization: Basic Y2ZmMzI4ZTItZTU3Yy00NTcxLTg2ODctODljMjhiMDBjMWQ3'));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

	    $response = curl_exec($ch);
	    curl_close($ch);

	    return $response;
	}


	$counter = 0;
	// $table = '
	//     <h2> News daily update report</h2>
	//     <html> 
	//     <head>
	//     <title>Report</title>
	//     </head>
	//     <body>
	//     <table style="text-align: center">
	//         <thead>
	//             <th>ID</th>
	//             <th>News title</th>
	//             <th>News description</th>
	//         </thead>
	//         <tbody>';


	
		$servername='us-cdbr-iron-east-01.cleardb.net';
		$username='bd92f737073375';
		$password='b1572f0c';
		$dbname = "heroku_0324816153f4386";
		$conn=mysqli_connect($servername,$username,$password,"$dbname");
		if(!$conn){
			 die('Could not Connect MySql Server:' .mysql_error());
		}


		$urls_query = "SELECT * FROM feedurl";
		if ($result = mysqli_query($conn, $urls_query)) {
		  // Fetch one and one row
		  $rows=[];
		  while ($row = mysqli_fetch_row($result)) {
		   	$rows[] = $row;
		  }
		  echo json_encode($rows);
		  
		}

	

		for($j= 0; $j<sizeof($rows); $j++)
		{
			$url1 = $rows[$j][1];
			//  Initiate curl
			echo $url1;
			$xmlfile = file_get_contents($url1);
			$news1_string = simplexml_load_string($xmlfile);
			$news1_array = json_decode( json_encode($news1_string) , 1);
			if(isset($news1_array['channel']['item'])){
				$news1_item_array = $news1_array['channel']['item'];
			}
			else if(isset($news1_array['item'])){
				$news1_item_array = $news1_array['item'];
			}
			if(isset($news1_array['channel']['items'])){
				$news1_item_array = $news1_array['channel']['items'];
			}
			else if(isset($news1_array['items'])){
				$news1_item_array = $news1_array['items'];
			}


			for($i =0; $i<sizeof($news1_item_array); $i++){
				$item_title = $news1_item_array[$i]['title'];
				$item_title = str_replace("'","",$item_title);

				$item_link  = $news1_item_array[$i]['link'];
				$item_desc  = $news1_item_array[$i]['description'];

				$item_json = json_encode($news1_item_array[$i]);
				$item_json = str_replace("'","",$item_json);

				$check_sql = "SELECT id FROM update_news WHERE search_title='$item_title' AND search_link='$item_link'";
				$search_result = mysqli_query($conn, $check_sql);

				if (mysqli_num_rows($search_result) == 0) {
					$entry = new Entry('article');
					$entry->setField('title', 'en-US', $item_title);
					$entry->setField('description', 'en-US', $item_desc);
					$entry->setField('link', 'en-US', $item_link);
					$entry->setField('mainFeed', 'en-US', true);
					try {
					     $environmentProxy->create($entry);
					    $counter++;
					     $entry_id = $entry->getId();
					     $entry1 = $environmentProxy->getEntry($entry_id);

						 $entry1->publish();
					    echo $counter." Publish success<br>";
					    echo $item_title." title<br>";
						// $table.='<tr><td>'.$counter.'</td><td>'.$item_title.'</td><td>'.$item_desc.'</td></tr>';

						$today = date("Y/m/d");
						$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
					     VALUES ('$item_json','$today','1','$item_title','$item_link')";
					     if (mysqli_query($conn, $insert_sql)) {			     	
					     } else {
					     	echo "<br>";
					        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
					     }


					     sendMessage($item_title);

					} catch (Exception $exception) {
					    echo $exception->getMessage();
					}
					
				}  
			}	
		}









// //function 1
	
		

// // //function 2

// 		$url1="https://www.news-medical.net/tag/feed/Dermatology.aspx";
// 		//  Initiate curl
// 		$xmlfile = file_get_contents($url1);
// 		$news1_string = simplexml_load_string($xmlfile);
// 		$news1_array = json_decode( json_encode($news1_string) , 1);
// 		$news1_item_array = $news1_array['channel']['item'];
// 		for($i =0; $i<sizeof($news1_item_array); $i++){
// 			$item_title = $news1_item_array[$i]['title'];
// 			$item_title = str_replace("'","",$item_title);

// 			$item_link  = $news1_item_array[$i]['link'];
// 			$item_desc  = $news1_item_array[$i]['description'];

// 			$item_json = json_encode($news1_item_array[$i]);
// 			$item_json = str_replace("'","",$item_json);

// 			$check_sql = "SELECT id FROM update_news WHERE search_title='$item_title' AND search_link='$item_link'";
// 			$search_result = mysqli_query($conn, $check_sql);

// 			if (mysqli_num_rows($search_result) == 0) {
				

// 				$entry = new Entry('article');

// 				$entry->setField('title', 'en-US', $item_title);
// 				$entry->setField('description', 'en-US', $item_desc);
// 				$entry->setField('link', 'en-US', $item_link);
// 				$entry->setField('mainFeed', 'en-US', true);
// 				// Let's call the API to persist the entry
// 				try {
// 				    $environmentProxy->create($entry);
// 				 	$counter++;
// 				    $entry_id = $entry->getId();
// 				    $entry1 = $environmentProxy->getEntry($entry_id);

// 					$entry1->publish();
// 				    echo $counter." publish success<br>";
// 				    echo $item_title." title<br>";

// 				    $today = date("Y/m/d");
// 					$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
// 				     VALUES ('$item_json','$today','1','$item_title','$item_link')";
// 				     if (mysqli_query($conn, $insert_sql)) {
// 				     } else {
// 				     	echo "<br>";
// 				        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
// 				     }

// 				     sendMessage($item_title);

// 				} catch (Exception $exception) {
// 				    echo $exception->getMessage();
// 				}





				
// 			}  
// 		}

// // 		// mysqli_close($conn);
	
// // //function 3

// 		$url1="https://www.healio.com/sws/feed/news/dermatology";
// 		//  Initiate curl
// 		$xmlfile = file_get_contents($url1);
// 		$news1_string = simplexml_load_string($xmlfile);
// 		$news1_array = json_decode( json_encode($news1_string) , 1);
// 		$news1_item_array = $news1_array['channel']['item'];
		
	
		

// 		for($i =0; $i<sizeof($news1_item_array); $i++){
// 			$item_title = $news1_item_array[$i]['title'];
// 			$item_title = str_replace("'","",$item_title);

// 			$item_link  = $news1_item_array[$i]['link'];
// 			$item_desc  = $news1_item_array[$i]['description'];

// 			$item_json = json_encode($news1_item_array[$i]);
// 			$item_json = str_replace("'","",$item_json);

			

// 			$check_sql = "SELECT id FROM update_news WHERE search_title='$item_title' AND search_link='$item_link'";
// 			$search_result = mysqli_query($conn, $check_sql);

// 			if (mysqli_num_rows($search_result) == 0) {
				

// 				$entry = new Entry('article');

// 				$entry->setField('title', 'en-US', $item_title);
// 				$entry->setField('description', 'en-US', $item_desc);
// 				$entry->setField('link', 'en-US', $item_link);
// 				$entry->setField('mainFeed', 'en-US', true);
// 				// Let's call the API to persist the entry
// 				try {
// 				    $environmentProxy->create($entry);
// 				    $counter++;
// 				    $entry_id = $entry->getId();
// 				    $entry1 = $environmentProxy->getEntry($entry_id);

// 					$entry1->publish();
// 				    echo $counter." publish success<br>";
// 				    echo $item_title." title<br>";

// 				    $today = date("Y/m/d");
// 					$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
// 				     VALUES ('$item_json','$today','1','$item_title','$item_link')";
				 
// 				     if (mysqli_query($conn, $insert_sql)) {
				     	

				     	
				     	
// 				     } else {
// 				     	echo "<br>";
// 				        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
// 				     }

// 				     sendMessage($item_title);

// 				} catch (Exception $exception) {
// 				    echo $exception->getMessage();
// 				}





				
// 			}  
// 		}

// // 		// mysqli_close($conn);


// // //function 4

// 		$url1="https://www.dermatologyadvisor.com/feed/";
// 		//  Initiate curl
// 		$xmlfile = file_get_contents($url1);
// 		$news1_string = simplexml_load_string($xmlfile);
// 		$news1_array = json_decode( json_encode($news1_string) , 1);
// 		$news1_item_array = $news1_array['channel']['item'];
		
	
		

// 		for($i =0; $i<sizeof($news1_item_array); $i++){
// 			$item_title = $news1_item_array[$i]['title'];
// 			$item_title_check = str_replace("'","",$item_title);

// 			$item_link  = $news1_item_array[$i]['link'];
// 			$item_desc  = $news1_item_array[$i]['description'];

// 			$item_json = json_encode($news1_item_array[$i]);
// 			$item_json = str_replace("'","",$item_json);

			

// 			$check_sql = "SELECT id FROM update_news WHERE search_title='$item_title_check' AND search_link='$item_link'";
// 			$search_result = mysqli_query($conn, $check_sql);

// 			if (mysqli_num_rows($search_result) == 0) {
				

// 				$entry = new Entry('article');

// 				$entry->setField('title', 'en-US', $item_title);
// 				$entry->setField('description', 'en-US', $item_desc);
// 				$entry->setField('link', 'en-US', $item_link);
// 				$entry->setField('mainFeed', 'en-US', true);
// 				// Let's call the API to persist the entry
// 				try {
// 				    $environmentProxy->create($entry);
				   
// 				   	$counter++;
// 				    $entry_id = $entry->getId();
// 				    $entry1 = $environmentProxy->getEntry($entry_id);

// 					$entry1->publish();
// 				    echo $counter." publish success<br>";
// 				    echo $item_title." title<br>";

// 				    $today = date("Y/m/d");
// 					$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
// 				     VALUES ('$item_json','$today','1','$item_title','$item_link')";
				 
// 				     if (mysqli_query($conn, $insert_sql)) {
				     	

				     	
				     	
// 				     } else {
// 				     	echo "<br>";
// 				        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
// 				     }


// 				     sendMessage($item_title);
// 				} catch (Exception $exception) {
// 				    echo $exception->getMessage();
// 				}





				
// 			}  
// 		}

// 		mysqli_close($conn);


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
<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>
