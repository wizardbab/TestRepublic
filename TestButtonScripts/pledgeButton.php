<?php
    // Authors: David Hughen
	// Date Created: 4/18/2015
	// This php script handles the db stuff for assigning 
	// zeros when a student doesn't sign his pledge
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
					  
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
	
	@$studentId = $_POST["studentId"];
	@$testId = $_POST["testId"];
	
	$pledgeQuery = "update test_list
					set test_score = 0, 
						graded = 1 
					where student_id = ? and test_id = ?";
	
	$pledgeStatement = $database->prepare($pledgeQuery);
	
	$pledgeStatement->bind_param("ss", $studentId, $testId);
	$pledgeStatement->execute();
	$pledgeStatement->close();
?>