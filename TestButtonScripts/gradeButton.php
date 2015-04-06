<?php
    // Authors: Jake Stevens
	// Date Created: 4/02/15
	// Last Modified: 4/02/15
	// This php script handles the db stuff for grading a test
	require("../constants.php");
    
    $database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
    
    $gradeQuery = "update test_list set graded = 1 where student_id = ? and test_id = ?";
    $updateQuestionQuery = "update question set points_earned = ? where question_id = ?";
    
    $gradeStatement = $database->prepare($gradeQuery);
    $updateQuestionStatement = $database->prepare($updateQuestionQuery);
    
    @$studentId = $_POST['studentId'];
    @$testId = $_POST['testId'];
    @$pointsEarnedArray = $_POST['pointsEarnedArray'];
    @$questionIdArray = $_POST['questionIdArray'];
    
    $gradeStatement->bind_param("ss", $studentId, $testId);
	$gradeStatement->execute();
	$gradeStatement->close();
    
    for($i = 0; $i < count($pointsEarnedArray); $i++)
    {
        $updateQuestionStatement->bind_param("ss", $pointsEarnedArray[$i], $questionIdArray[$i]);
        $updateQuestionStatement->execute();
    }
    $updateQuestionStatement->close();
    foreach($pointsEarnedArray as $k)
        echo $k;
        echo count($pointsEarnedArray);
?>