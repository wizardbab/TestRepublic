<?php
   // Authors: David Hughen - Jake Stevens
	// Date Created: 3/13/15
	// Last Modified: 3/13/15 - 3/16/15
	// This php script handles the db stuff for matching questions
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
					  
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
	
	// Save a test
	$saveQuery = "update test set date_begin = ?, date_end = ?, time_limit = ?, max_points = ?,
					  test_name = ?, pledge = ?, instruction = ?, saved = 1
					  where test_id = ?";
	
	@$testName = $_POST['testName'];
	@$dateBegin = $_POST['dateBegin'];
	@$dateEnd = $_POST['dateEnd'];
	@$timeLimit = $_POST['timeLimit'];
	@$specificInstruction = $_POST['specificInstruction'];
	@$testPledge = $_POST['testPledge'];
	@$newTestId = $_POST['newTestId'];
	@$maxPoints = $_POST['maxPoints'];
	
	/*echo $testName . " ";
	echo $dateBegin . " ";
	echo $dateEnd . " ";
	echo $timeLimit . " ";
	echo $specificInstruction . " ";
	echo $testPledge . " ";
	echo $newTestId . " ";
	echo $maxPoints; */
	
	$dataArray = ["testName" => $testName,
					  "dateBegin" => $dateBegin, 
					  "dateBegin" => $dateEnd, 
					  "timeLimit" => $timeLimit, 
					  "specificInstruction" => $specificInstruction, 
					  "testPledge" => $testPledge, 
					  "maxPoints" => $maxPoints];
					  
	echo $dataArray["testName"];
	echo $dataArray["dateBegin"];
	echo $dataArray["dateBegin"];
	echo $dataArray["timeLimit"];
	echo $dataArray["specificInstruction"];
	echo $dataArray["testPledge"];
	echo $dataArray["maxPoints"];
	
	if(!is_null($dateBegin))
		@$dateBegin = date("Y-m-d", strtotime($dateBegin));
	
	if(!is_null($dateEnd))
		@$dateEnd = date("Y-m-d", strtotime($dateEnd));
	
	$saveStatement = $database->prepare($saveQuery);
	$saveStatement->bind_param("ssssssss", $dateBegin, $dateEnd, $timeLimit, $maxPoints, $testName, $testPledge, $specificInstruction, $newTestId);
	$saveStatement->execute();
	$saveStatement->close();
	
?>