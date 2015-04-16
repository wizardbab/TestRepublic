<?php
	// Author: David Hughen
	// Date: 4/15/2015
	// This script allows us to delete a test
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
					  
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);

	@$testId = $_POST['testId'];
	
	$deleteTestQuery = "delete from test where test_id = ?";
	$deleteTestListQuery = "delete from test_list where test_id = ?";
	$deleteQuestionQuery = "delete from question where test_id = ?";
	
	$deleteTestStatement = $database->prepare($deleteTestQuery);
	$deleteTestListStatement = $database->prepare($deleteTestListQuery);
	$deleteQuestionStatement = $database->prepare($deleteQuestionQuery);
	
	$deleteTestStatement->bind_param("s", $testId);
	$deleteTestStatement->execute();
	$deleteTestStatement->close();
	
	$deleteTestListStatement->bind_param("s", $testId);
	$deleteTestListStatement->execute();
	$deleteTestListStatement->close();
	
	$deleteQuestionStatement->bind_param("s", $testId);
	$deleteQuestionStatement->execute();
	$deleteQuestionStatement->close();
?>