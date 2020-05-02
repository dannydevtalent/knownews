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


//function 1
	
		$url1="http://search.nejm.org/search?cnt=20&start_month=7&start_year=2009&w=*&restrict=doctype%3Aarticle&srt=0&isort=date&ts=rss&af=topic:5";
		//  Initiate curl
		$xmlfile = file_get_contents($url1);
		$news1_string = simplexml_load_string($xmlfile);
		$news1_array = json_decode( json_encode($news1_string) , 1);
		$news1_item_array = $news1_array['item'];
		// else if($flag=="1"){
		// 	$news1_item_array = $news1_array['channel']['item'];
		// }
	
		

		 for($i =0; $i<sizeof($news1_item_array); $i++){
		// for($i =0; $i<2; $i++){
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
				// Let's call the API to persist the entry
				try {
				     $environmentProxy->create($entry);
				    $counter++;
				    $entry_id = $entry->getId();
				    $entry1 = $environmentProxy->getEntry($entry_id);

					$entry1->publish();
				    echo $counter." publish success<br>";
					// $table.='<tr><td>'.$counter.'</td><td>'.$item_title.'</td><td>'.$item_desc.'</td></tr>';

				} catch (Exception $exception) {
				    echo $exception->getMessage();
				}





				$today = date("Y/m/d");
				$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
			     VALUES ('$item_json','$today','1','$item_title','$item_link')";
			 
			     if (mysqli_query($conn, $insert_sql)) {
			     	

			     	
			     	
			     } else {
			     	echo "<br>";
			        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
			     }
			}  
		}

//function 2

		$url1="https://www.news-medical.net/tag/feed/Dermatology.aspx";
		//  Initiate curl
		$xmlfile = file_get_contents($url1);
		$news1_string = simplexml_load_string($xmlfile);
		$news1_array = json_decode( json_encode($news1_string) , 1);
		$news1_item_array = $news1_array['channel']['item'];
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
				// Let's call the API to persist the entry
				try {
				    $environmentProxy->create($entry);
				 	$counter++;
				    $entry_id = $entry->getId();
				    $entry1 = $environmentProxy->getEntry($entry_id);

					$entry1->publish();
				    echo $counter." publish success<br>";
				} catch (Exception $exception) {
				    echo $exception->getMessage();
				}





				$today = date("Y/m/d");
				$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
			     VALUES ('$item_json','$today','1','$item_title','$item_link')";
			 
			     if (mysqli_query($conn, $insert_sql)) {
			     	

			     	
			     	
			     } else {
			     	echo "<br>";
			        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
			     }
			}  
		}

		// mysqli_close($conn);
	
//function 3

		$url1="https://www.healio.com/sws/feed/news/dermatology";
		//  Initiate curl
		$xmlfile = file_get_contents($url1);
		$news1_string = simplexml_load_string($xmlfile);
		$news1_array = json_decode( json_encode($news1_string) , 1);
		$news1_item_array = $news1_array['channel']['item'];
		
	
		

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
				// Let's call the API to persist the entry
				try {
				    $environmentProxy->create($entry);
				    $counter++;
				    $entry_id = $entry->getId();
				    $entry1 = $environmentProxy->getEntry($entry_id);

					$entry1->publish();
				    echo $counter." publish success<br>";
				} catch (Exception $exception) {
				    echo $exception->getMessage();
				}





				$today = date("Y/m/d");
				$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
			     VALUES ('$item_json','$today','1','$item_title','$item_link')";
			 
			     if (mysqli_query($conn, $insert_sql)) {
			     	

			     	
			     	
			     } else {
			     	echo "<br>";
			        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
			     }
			}  
		}

		// mysqli_close($conn);


//function 4

		$url1="https://www.dermatologyadvisor.com/feed/";
		//  Initiate curl
		$xmlfile = file_get_contents($url1);
		$news1_string = simplexml_load_string($xmlfile);
		$news1_array = json_decode( json_encode($news1_string) , 1);
		$news1_item_array = $news1_array['channel']['item'];
		
	
		

		for($i =0; $i<sizeof($news1_item_array); $i++){
			$item_title = $news1_item_array[$i]['title'];
			$item_title_check = str_replace("'","",$item_title);

			$item_link  = $news1_item_array[$i]['link'];
			$item_desc  = $news1_item_array[$i]['description'];

			$item_json = json_encode($news1_item_array[$i]);
			$item_json = str_replace("'","",$item_json);

			

			$check_sql = "SELECT id FROM update_news WHERE search_title='$item_title_check' AND search_link='$item_link'";
			$search_result = mysqli_query($conn, $check_sql);

			if (mysqli_num_rows($search_result) == 0) {
				

				$entry = new Entry('article');

				$entry->setField('title', 'en-US', $item_title);
				$entry->setField('description', 'en-US', $item_desc);
				$entry->setField('link', 'en-US', $item_link);
				$entry->setField('mainFeed', 'en-US', true);
				// Let's call the API to persist the entry
				try {
				    $environmentProxy->create($entry);
				   
				   	$counter++;
				    $entry_id = $entry->getId();
				    $entry1 = $environmentProxy->getEntry($entry_id);

					$entry1->publish();
				    echo $counter." publish success<br>";
				} catch (Exception $exception) {
				    echo $exception->getMessage();
				}





				$today = date("Y/m/d");
				$insert_sql = "INSERT INTO update_news (item,update_date,status,search_title,search_link)
			     VALUES ('$item_json','$today','1','$item_title','$item_link')";
			 
			     if (mysqli_query($conn, $insert_sql)) {
			     	

			     	
			     	
			     } else {
			     	echo "<br>";
			        echo "Error: " . $insert_sql . ":-" . mysqli_error($conn);
			     }
			}  
		}

		mysqli_close($conn);

		// $table.= '
		// 	</tbody>
		// 	</table>
		// 	</body>
		// 	</html>
		// ';

		// echo $table;
		
		//  if($counter==0){
	 //        $table = '<h2> News daily update report</h2><br>
	 //                <h3> There is no report today</h3>';
	 //    }

	 //    $to = "lujin0406@outlook.com";
		// //$to = "lujin0406@outlook.com";
		// $subject = "HTML email";

		// $headers = "MIME-Version: 1.0" . "\r\n"; 
		// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
		 
		// $headers .= 'From: sticsbackoffice < sticsbackoffice@iotops.net>' . "\r\n"; 
		// $headers .= 'Bcc: sticsbackoffice@iotops.net' . "\r\n"; 

		// mail($to,$subject,$table,$headers);

	
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
