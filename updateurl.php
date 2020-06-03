<?php
	if(empty($_POST['username'])){
		header("Location: login.php"); /* Redirect browser */
  		exit();
	}
?>
 
<!DOCTYPE html>
<html>
<head>
	<title>Knowpro RSS Feed</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link rel="icon" href="kp-icon-1024px.png" type="image/icon type">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/5.0.7/sweetalert2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/5.0.7/sweetalert2.min.css">
    <style type="text/css">
    	@keyframes loading-gif {
		  0% {
		    transform: rotate(0deg);
		  }
		  100% {
		    transform: rotate(360deg);
		  }
		}
		.loading-gif 
		{
		  display: none;
		  position: fixed;
		  z-index: 1000;
		  top: 0;
		  left: 0;
		  width: 100%;
		  height: 100%;
		  background: rgba( 0, 0, 0, 0.5);
		}
		.loading-gif:after 
		{
		  top: 50%;
		  left: 50%;
		  position: fixed;
		  content: " ";
		  display: block;
		  width: 46px;
		  height: 46px;
		  border-radius: 50%;
		  border: 5px solid #fcf;
		  border-color: #fcf transparent #fcf transparent;
		  animation: loading-gif 1.2s linear infinite;
		}
		#alert{
			display: none;
		    position: absolute;
		    bottom: 20px;
		    right: 50%;
		    padding: 16px;
		    background: rgba(26, 153, 65, 0.6);
		    border-radius: 20px;
		    color: white;
		}

    </style>
</head>
<body style="width: 80%; margin: 0 auto;">
	<div class="loading-gif"></div>
	<button class="btn btn-primary btn-log-out" style="float: right; margin-top: 5%">Log out</button>
	<button data-toggle="modal" data-target="#add_dialog" class="btn btn-success" style="float: right; margin-top: 5%; margin-right:5%">Add</button>
	<button data-toggle="modal" data-target="#change_password_dialog" class="btn btn-primary" style="float: right; margin-top: 5%; margin-right:5%">Change password</button>
	<table class="table">
		<thead>
			<tr>
				<th>No</th>
				<th>RSS Feed</th>
				<th width="20%">Action</th>
			</tr>
		</thead>
		<tbody class="tbody">
			

		</tbody>
	</table>
	<div id="alert" >Success</div>
</body>
</html>

<div id="add_dialog" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" style="margin: 0 auto;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Url</h4>
      </div>
      <div class="modal-body">
      	<input class="add_url_input form-control" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-add" >Add</button>
        
      </div>
    </div>

  </div>
</div>

<div id="edit_dialog" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" style="margin: 0 auto;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Url</h4>
      </div>
      <div class="modal-body">
      	<input class="edit_url_input form-control" >
      	<input class="edit_url_id" type="hidden">
      	<input class="edit_url_count" type="hidden">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-edit-save" >Save</button>
       
      </div>
    </div>

  </div>
</div>

<div id="change_password_dialog" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" style="margin: 0 auto;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Change password</h4>
      </div>
      <div class="modal-body">
      <!-- 	<div class="form-group">
      		<label style="width: 25%; float: left;">Current password: </label>
      		<input style="width: 70%;" class="current_password form-control" type="password">
      	</div> -->
      	<div class="form-group">
      		<label style="width: 25%; float: left;">Change password: </label>
      		<input style="width: 70%;" class="change_password form-control" type="password">
      	</div>

      	<div class="form-group">
      		<label style="width: 25%; float: left;">Confirm password: </label>
      		<input style="width: 70%;" class="confirm_password form-control" type="password">
      	</div>
      	

      	<input class="edit_url_id" type="hidden">
      	<input class="edit_url_count" type="hidden">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-change-password-save" >Save</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var url_list_string = "";
		var count = 0;
		
		var url_list_object = {};

		function validURL(str) {
		  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
		    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
		    '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
		    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
		    '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
		    '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
		  return !!pattern.test(str);
		}
		$(".loading-gif").css("display","block");
		jQuery.ajax({
	    	url:"ajax.php",
	        data: { 
	        	request:"get_url"
	            },
	            type: 'post',
	            success: function(result) 
	            {
	            	$(".loading-gif").css("display","none");
	            	console.log(result);
	            	if(result != []){
	            		var urls = JSON.parse(result);
	            		console.log(urls);
	            		for(var i=0; i<urls.length; i++){
	            			url_list_string += "<tr id='tr_"+urls[i][0]+"'>";
	            			url_list_string +="<td>"+(i+1)+"</td>";
	            			url_list_string+= "<td id='url_"+urls[i][0]+"'>"+urls[i][1]+"</td>";
	            			url_list_string+= "<td> <button data-toggle='modal' data-target='#edit_dialog' class='btn btn-primary btn-edit1' url_id='"+urls[i][0]+"' url_counter='"+i+"'>Edit</button>";
	            			url_list_string+="<button class='btn btn-danger btn-del' style='margin-left: 15px' url_id='"+urls[i][0]+"' url_counter='"+i+"'>Del</button></td></tr>";


	            			count++;
	            			
	            			url_list_object[urls[i][0]] = urls[i][1];

	            		}
	            		$(".tbody").append(url_list_string);
	            	}
	            }
			});


		$(".btn-add").click(function(){
			var add_url = $(".add_url_input").val();
			if(validURL(add_url)){
				$(".loading-gif").css("display","block");
				$("#add_dialog").modal('hide');
				jQuery.ajax({
		    	url:"ajax.php",
		        data: { 
		        	 request:"add_feed_url",
		        	 url: add_url
		            },
		            type: 'post',
		            success: function(result) 
		            {
		            	$(".loading-gif").css("display","none");
		            	$("#alert").html("Add success");
		            	$("#alert").fadeIn();
		            	setTimeout(function(){$("#alert").fadeOut(); }, 3000);
		            	
		            	
		            	var add_string = "<tr id='tr_"+result+"'><td>"+(count+1)+"</td><td id='url_"+result+"'>"+add_url+"</td><td><button data-toggle='modal' data-target='#edit_dialog' class='btn btn-primary btn-edit1' url_id='"+result+"' url_counter='"+(count+1)+"'>Edit</button><button class='btn btn-danger btn-del' style='margin-left: 15px' url_id='"+result+"' url_counter='"+(count+1)+"'>Del</button></td></tr>";
						$(".tbody").append(add_string);
						
						count++;
					

						url_list_object[result] = add_url;
		            	
		            }
				});
			}
			else{
				
				swal({
                  title: "",
                  text: "This url is not correct.",
                  type: "warning"
                });
			}
			
		});

		$(".btn-change-password-save").click(function(){
			// var current_password = $(".current_password").val();
			var change_password  = $(".change_password").val();
			var confirm_password = $(".confirm_password").val();
			if(change_password == confirm_password){
				$(".loading-gif").css("display","block");
				jQuery.ajax({
		    	url:"ajax.php",
		        data: { 
		        	 request:"change_password",
		        	 password: change_password
		            },
		            type: 'post',
		            success: function(result) 
		            {
		            	$(".loading-gif").css("display","none");
		            	console.log(result);
		            	if(result=="success"){
		            		swal("Success!", "Password has been changed!", "success");
		            	}

		            	$("#change_password_dialog").modal("hide");

		            }
				});
			}
			else
			{
				swal({
                  title: "Alert",
                  text: "Confirm password is not correct. Please check again",
                  type: "warning"
                });
				
			}
		});

		$('body').on('click','.btn-edit1',function(){
			console.log("ef");
			var url_count = $(this).attr("url_counter");
			var url_id = $(this).attr("url_id");
			$(".edit_url_input").val(url_list_object[url_id]);
			$(".edit_url_id").val(url_id);
			$(".edit_url_count").val(url_count);

		});

		$('body').on('click','.btn-del',function(){
			var url_count = $(this).attr("url_counter");
			var url_id = $(this).attr("url_id");
			console.log(url_id);
			
			swal({
              title: 'Are you sure?',
              text: "You will delete this url?",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes'
            }).then(function() {
            	$(".loading-gif").css("display","block");
                jQuery.ajax({
		    	url:"ajax.php",
		        data: { 
		        	 request:"remove",
		        	 id : url_id
		            },
		            type: 'post',
		            success: function(result) 
		            {
		            	console.log(result);
		            	$(".loading-gif").css("display","none");
		            	$("#alert").html("Remove success");
		            	$("#alert").fadeIn();
		            	setTimeout(function(){$("#alert").fadeOut(); }, 3000);
		            	$("#tr_"+url_id).remove();

		            }
				});
            })
           

		});

		$(".btn-edit-save").click(function(){
			var url_edit = $(".edit_url_input").val();
			if(validURL(url_edit)){
				$("#edit_dialog").modal('hide');

				$(".loading-gif").css("display","block");
				
				var url_id   = $(".edit_url_id").val();
				var url_count   = $(".edit_url_count").val();
				jQuery.ajax({
		    	url:"ajax.php",
		        data: { 
		        	 request:"update_feed_url",
		        	 url: url_edit,
		        	 id : url_id
		            },
		            type: 'post',
		            success: function(result) 
		            {
		            	$(".loading-gif").css("display","none");
		            	console.log(result);
		            	$("#alert").html("Edit success");
		            	$("#alert").fadeIn();
		            	setTimeout(function(){$("#alert").fadeOut(); }, 3000);
		            	url_list_object[url_id] = url_edit;
		            	$("#url_"+url_id).html(url_edit);

		            }
				});
			}
			else
			{
				alert("This url is not correct");
			}

			
		});

		$(".btn-log-out").click(function(){
			$(location).attr('href', 'login.php')
		});
	});
</script>
