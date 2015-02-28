<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Republic</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

<?php

session_start();

// Include the constants used for the db connection
require("constants.php");

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

// Query for the list of classes offered
$listClassQuery = "select class_description from class";





@ $database->select_db(DATABASENAME);


// Check to see if anything was entered, if not assign and empty string
$firstName = (isset($_POST['firstName']) ? $_POST['firstName'] : "");
$lastName  = (isset($_POST['lastName']) ? $_POST['lastName'] : "");
$email     = (isset($_POST['email']) ? $_POST['email'] : "");
$password  = (isset($_POST['password']) ? $_POST['password'] : "");

?>
		


	
					<div id="sidebar-wrapper">
					
					<a href="logout.php">
					<!-- Button with a link wrapped around it to go back to the login page -->
					<button class="btn btn-block btn-primary" type="button" id="signUpButton" onclick="signUp.php">
						<i class="glyphicon glyphicon-log-in"></i>Back to Login
					</button></a>
            <ul class="sidebar-nav">
				<li>
                    <a href="#" id="student-summary">Summary</a>
                </li>
                <li class="sidebar-brand"><!-- VIC AND ANDREA, I'D LIKE FOR THIS TO "SELECT A CLASS TO ADD:" (formatting needed) -->
                    Select a Class:
                </li>
               
				<?php 
				$courseCounter = 1;
				// List of classes for the user to select from...we'll need to keep track of what class is selected for the db
				if ($classList = $database->prepare($listClassQuery)) 
				{
					// nothing to bind
				}
				else {
					printf("Errormessage: %s\n", $database->error);
				}	
				$classList->bind_result($clde);
				$classList->execute();
				while($classList->fetch())
				{	
					echo '<li><a href="#"><div class="subject-name">' . $courseCounter++ . ". " . $clde . '</div></a></li>';
				}
				$classList->close(); 
				?>
            </ul>
				
				
        </div>
		  
<form name="signUpForm" id="signUpForm" action="signUp.php" method="post">
	<div id="signUpDiv">
		<h1>Welcome!</h1>
			<h2>Please enter your information.</h2>
			<label class="survey_style">First Name:
				<input type="text" name="firstName" id="firstName" />
			</label><br />
			<label class="survey_style">Last Name:
				<input type="text" name="lastName" id="lastName" />
			</label><br />
			<label class="survey_style">Email:
				<input type="text" name="email" id="email" />
			</label><br />
			<label class="survey_style">Password:
				<input type="text" name="password" id="password" />
			</label><br />
			<input class="myButton" type="submit" value="Create Account" />
			
	
			
		<table class="signUpTable">
					<tr><td><?php echo $firstName ?></td></tr>
					<tr><td><?php echo $lastName ?></td></tr>
					<tr><td><?php echo $email ?></td></tr>
					<tr><td><?php echo $password ?></td></tr>
					
		</table>
	</div>
</form>



    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>

</html>