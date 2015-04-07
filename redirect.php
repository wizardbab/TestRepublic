
<!DOCTYPE HTML>  
<html>
	<!-- Logout Page edited with timer and design by Victor Jereza -->
<head> 
    <meta name="author" content="Mongolian Horde" /> 
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

<?php
	// Code to logout of the current page whether the user
	// be student or teacher
	// Author: David Hughen
	// Date:   2/11/2015
	session_start();
	$id = $_SESSION['username'];
	
	// TIMER CODE SHOULD GO HERE SOMEWHERE
?>
<body> 
<div class="centralBox">
	<img class="centralLogo" src="images/newlogo.png" alt="Our Logo"/>
		<h1 align="center">Login with this id: <?php echo $id; ?> and your password</h1>
		<button type="button" class="btn btn-primary" onclick="Redirect()">Back to Login Page</button>
</div>


	    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
	<script>
		function Redirect()
		{
			window.location = "login.html";
		}
	</script>
	
</body> 
</html> 



