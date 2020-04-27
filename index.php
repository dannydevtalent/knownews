<?php
	$url="http://goldrushaffiliates.co.za/global/feed/json/?language=eng&amp;timeZone=179&amp;filterData[start_ts]=172800&amp;filterData[sport][]=1";

	//  Initiate curl
	$ch = curl_init();  // Will return the response, if false it print the response
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // Set the url
	
	curl_setopt($ch, CURLOPT_URL,$url);   // Execute
	
	$result=curl_exec($ch);   // Closing
	
	curl_close($ch);

?>

<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<title>Football</title>
</head>
<body style="width: 50%; margin: 0 auto">
	<div id="date_range" style="margin-top: 5%">
		<button class="btn btn-success btn_all" value="all">All date</button>
		<button class="btn btn-success btn_oneday" value="1">Today</button>
		<button class="btn btn-success btn_twoday" value="2">Tomorrow</button>
	</div>
	<div id="competition_list"> </div>
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
		var competition_all = [];
		var html_string = "";
		var date = "1";
		var competition_json_result = <?php echo json_encode($result); ?>;   //Get the json result from PHP value
		var competition_result = JSON.parse(competition_json_result);	 	 //Get the object from json by using Json parse
		console.log(competition_result);
		var region = competition_result['sport'][1]['region'];     	
    	$.each( region, function( key, value ) {
		  	$.each( value['competition'], function( key1, value1 ) {
		  		if(value1['name']!="G-VALUE BET BOOSTS"){
		  			var first_game = value1['game'][Object.keys(value1['game'])[0]];
		  			
		  			if(first_game['team2_name']!=undefined){   //Check if team2_name exists.
		  				competition_all.push(value1); 			 // all date competition
		  			}	
		  		}
			});
		});  													//filter function (If exist team2_name)
	
		function get_list_show(date_range){
			for(var i=0; i<competition_all.length; i++){
				var count  = 0;
				var range = "";
				var range_timestamp = "";
				var tomorrow_timestamp= "";
				$.each( competition_all[i]['game'], function( key3, value3 ) {
					var today=new Date();
					
					if(date_range=="all"){
						count++;
					}
					else if(date_range=="1") {
						today.setDate(today.getDate()+(parseInt(date_range)) );
						range =today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + today.getDate() + ' 00:00:00';
						range_timestamp = Date.parse(range);
						var start_date_timestamp = Date.parse(value3['start_ts']);
						if(start_date_timestamp < range_timestamp){
							count++;
						}	

					}
					else if(date_range=="2"){
						today.setDate(today.getDate()+1);
						var tomorrow = today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + today.getDate() + ' 00:00:00';
						tomorrow_timestamp = Date.parse(tomorrow);
						today.setDate(today.getDate()+1);
						range = today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + today.getDate() + ' 00:00:00';
						range_timestamp = Date.parse(range);
						var start_date_timestamp = Date.parse(value3['start_ts']);
						if((start_date_timestamp < range_timestamp) && (start_date_timestamp > tomorrow_timestamp)){
							count++;
						}	
					}
				});
				if(count>0){
					console.log(count);
					var competition_name =  competition_all[i]['name'];
					html_string+= `<h2 style="color:#a645e3">`+competition_name+`</h2>`;
					html_string+= `<table class="table" style="font-size: 15px; margin-left: 20px">
									 <thead>
									 	<th style="width: 30%;">Date</th>
									 	<th style="width: 70%;">Event</th>
									 </thead>
									 <tbody>`;
					$.each( competition_all[i]['game'], function( key3, value3 ) {

						if(date_range=="all"){
							html_string+= `<tr>
											<td>`+value3['start_ts']+`</td>
											<td><a href="game.php?id=`+value3['id']+`&name=`+value3['team1_name']+` vs `+ value3['team2_name'] +`&date=`+value3['start_ts']+`">`+value3['team1_name']+` vs `+ value3['team2_name'] +`</a></td>
									  	</tr>`;
						}	
						else if(date_range=="1")
						{	
							var start_date_timestamp = Date.parse(value3['start_ts']);
							if(start_date_timestamp < range_timestamp){
								html_string+= `<tr>
									<td>`+value3['start_ts']+`</td>
									<td><a href="game.php?id=`+value3['id']+`&name=`+value3['team1_name']+` vs `+ value3['team2_name'] +`&date=`+value3['start_ts']+`">`+value3['team1_name']+` vs `+ value3['team2_name'] +`</a></td>
							  	</tr>`;
							}
						}
						else if(date_range=="2"){
							var start_date_timestamp = Date.parse(value3['start_ts']);
							if(start_date_timestamp < range_timestamp && start_date_timestamp > tomorrow_timestamp){
								html_string+= `<tr>
									<td>`+value3['start_ts']+`</td>
									<td><a href="game.php?id=`+value3['id']+`&name=`+value3['team1_name']+` vs `+ value3['team2_name'] +`&date=`+value3['start_ts']+`">`+value3['team1_name']+` vs `+ value3['team2_name'] +`</a></td>
							  	</tr>`;
							}
						}
					});

					html_string+=	`</tbody>
								   </table>`

				}
			}
			console.log(html_string);
			$("#competition_list").html(html_string);
		}
		get_list_show(date);
		$(".btn_all").click(function(){
			var value = $(this).val();
			$("#competition_list").html();
			html_string="";
			get_list_show(value);
		});
		$(".btn_oneday").click(function(){
			var value = $(this).val();
			$("#competition_list").html();
			html_string="";
			get_list_show(value);
		});
		$(".btn_twoday").click(function(){
			var value = $(this).val();
			$("#competition_list").html();
			html_string="";
			get_list_show(value);
		});
		$(".btn_threeday").click(function(){
			var value = $(this).val();
			$("#competition_list").html();
			html_string="";
			get_list_show(value);
		});
	});
</script>