<?php
	// Author: David Hughen
	// Date:   4/16/2015
	// This script saves the start time for a test
	
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
					  
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
	
	@$testId = $_POST['testId'];
	
	$timeStampQuery = "update test set start_time = curtime() where test_id = ?";
	
	$timeStampStatement = $database->prepare($timeStampQuery);
	
	$timeStampStatement->bind_param("s", $testId);
	$timeStampStatement->execute();
	$timeStampStatement->close();
	
	
?>