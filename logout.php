
<!DOCTYPE HTML>  
<html>
	<!-- Logout Page edited with timer and design by Victor Jereza -->
<head> 
    <meta name="author" content="Mongolian Horde" /> 
	<meta http-equiv="Refresh" content="2.25; URL=login.html">
 	<link href="style.css" rel="stylesheet" type="text/css" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 


 <title>Thanks for using Test Republic!</title> 
	
	<!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/login.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	
</head> 
<body> 
<div class="centralBox">
	<img class="centralLogo" src="images/logo2.png" alt="Our Logo"/>
	<h1 align="center">Thank you for using Test Republic!</h1>
	<div class="progress">
		<div class="progress-bar progress-bar-striped active" role="progressbar"
			aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%" > Redirecting to Login Page..
		</div>
	</div>
</div>

<?php
	// Code to logout of the current page whether the user
	// be student or teacher
	// Author: David Hughen
	// Date:   2/11/2015
	session_start();
	session_destroy();
	
	// TIMER CODE SHOULD GO HERE SOMEWHERE
?>
	    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
	
</body> 
</html> 



