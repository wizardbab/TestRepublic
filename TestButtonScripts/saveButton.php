<?php
   // Authors: David Hughen - Jake Stevens
	// Date Created: 3/13/15
	// Last Modified: 3/13/15 - 3/16/15
	// This php script handles the db stuff for saving tests
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
					  
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
	
	// Save a test
	$saveQuery = "update test set date_begin = ?, date_end = ?, time_limit = ?, max_points = ?,
					  test_name = ?, pledge = ?, instruction = ?, saved = 1, class_id = ?, teacher_id = ?
					  where test_id = ?";
	
	@$testName = $_POST['testName'];
	@$dateBegin = $_POST['dateBegin'];
	@$dateEnd = $_POST['dateEnd'];
	@$timeLimit = $_POST['timeLimit'];
	@$specificInstruction = $_POST['specificInstruction'];
	@$testPledge = $_POST['testPledge'];
	@$newTestId = $_POST['newTestId'];
	@$maxPoints = $_POST['maxPoints'];
    @$classId = $_POST['classId'];
    @$teacherId = $_POST['teacherId'];
	
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
    
    //$timeLimit = intval($timeLimit * 100);
	
    $newDateBegin = null;
    $newDateEnd = null;
    
    $d_date = str_replace("-", "/", $_POST['dateBegin']);
	if(strtotime($d_date))
    {
		$newDateBegin = date("Y-m-d", strtotime($d_date));
    }
	$d_date = str_replace("-", "/", $_POST['dateEnd']);
	if(strtotime($d_date))
    {
		$newDateEnd = date("Y-m-d", strtotime($d_date));
    }
	
	$saveStatement = $database->prepare($saveQuery);
	$saveStatement->bind_param("ssssssssss", $newDateBegin, $newDateEnd, $timeLimit, $maxPoints, $testName, $testPledge, $specificInstruction, $classId, $teacherId, $newTestId);
	$saveStatement->execute();
	$saveStatement->close();
	
?>