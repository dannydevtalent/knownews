<!DOCTYPE html>
<html>
<head> 
  <title>Log in</title>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
  
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <style type="text/css">
    .card{
      border: 1px solid rgba(0, 0, 0, 0.325);
      background-color: #dbdbdb;
    }
    .login-form .card {
      position: relative;
      overflow: hidden;
    }
    .login-form .card .shape {
      width: 300px;
      height: 300px;
      border-radius: 40px;
      display: block;
      position: absolute;
      top: 0;
      right: -150px;
      background: rgba(0,0,0,0.05);
      transform: rotate(45deg);
    }

    .login-form .card .card-header i {
       font-size: 54px;
       margin-bottom: 15px;
    }

    .login-form .card .card-header h2 {
       font-size: 25px;
       font-weight: 700;
    }

    .login-form .card .card-body {
      position: relative;
    }

    .login-form .card .card-body label {
      font-size: 13px;
    }

    .login-form .card .card-body input,
    .login-form .card .card-body textarea {
      border: 1px solid #aaa;
      border-radius: 0;
    }
    .login-form .card .card-body input:focus,
    .login-form .card .card-body textarea:focus {
      border: 1px solid #222;
    }

    .login-form .card .card-body .btn {
      background: #222;
      color: #fff;
      border-radius: 0;
    }
    .login-form .card .card-body .btn:hover {
      background: #555;
      color: #fff;
    }
    .login-form .card .card-body .custom-control label {
      font-size: 16px;
    }
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
  </style>
</head>
<body>
  <!-- Login Form -->
  <div class="loading-gif"></div>
<div class="login-form py-4" style="margin-top:5%;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <div class="card shadow-sm">
          <span class="shape"></span>
          <div class="card-header text-center bg-transparent">
            <img src="kp-icon-1024px.png" width="15%">
            <h2 style="margin-top: 10px">LOGIN (RSS FEED)</h2>
          </div>
          <div class="card-body py-4">
            <form action="updateurl.php" method="post" class="form_login">
              <div class="form-group">
                <label for="name">Username</label>
                <input type="text" class="form-control shadow-none username" name="username" placeholder="Username">
              </div>
              <div class="form-group">
                <label for="name">Password</label>
                 <input type="password" class="form-control shadow-none password" name="password" placeholder="Password">
              </div>
         
            </form>
            <div class="form-group" style="text-align: center;">
                <button class="btn btn-log-in">Log in</button>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /Login Form -->
</body>
</html>

<script type="text/javascript">
    $(document).ready(function(){
        $(".btn-log-in").click(function(){
          $(".loading-gif").css("display","block");
            var username = $(".username").val();
            var password = $(".password").val();
            jQuery.ajax({
              url:"ajax.php",
                data: { 
                   request:"user_login",
                   username: username,
                   password : password
                    },
                    type: 'post',
                    success: function(result) 
                    {
                      //$(".loading-gif").css("display","none");
                      $(".loading-gif").css("display","none");
                      if(result=="success"){
                         // alert("success");
                          $(".form_login")[0].submit();
                      }
                      else{
                          alert("The username or password is not correct");
                      }

                    }
            });
        })
    });
</script>
