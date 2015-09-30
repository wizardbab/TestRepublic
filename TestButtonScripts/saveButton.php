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
	if($_POST['timeLimit'] != null)
		@$timeLimit = $_POST['timeLimit'];
	else
		$timeLimit = null;
	@$specificInstruction = $_POST['specificInstruction'];
	@$testPledge = $_POST['testPledge'];
	@$newTestId = $_POST['newTestId'];
	if($_POST['maxPoints'] != null)
		@$maxPoints = $_POST['maxPoints'];
	else
		$maxPoints = null;
    @$classId = $_POST['classId'];
    @$teacherId = $_POST['teacherId'];

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
	
	/*$tl = filter_var($timeLimit), FILTER_SANITIZE_STRING);
	$ti = filter_var($newTestId), FILTER_SANITIZE_STRING);
	
		$query = "update test set time_limit = ".$tl." where test_id = ".$ti."";
   mysql_query($query);
	*/
	
	
?>