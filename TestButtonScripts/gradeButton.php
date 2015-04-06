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
    $pointsEarnedQuery = "update test_list set test_score = ? where test_id = ? and student_id = ?";
    $testPointsQuery = "select max_points, sum(question_value) from question where test_id = ? and student_id = ?";
    
    $gradeStatement = $database->prepare($gradeQuery);
    $updateQuestionStatement = $database->prepare($updateQuestionQuery);
    $pointsEarnedStatement = $database->prepare($pointsEarnedQuery);
    $testPointsStatement = $database->prepare($testPointsQuery);
    
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
    
    $testPointsStatement->bind_param("ss", $testId, $studentId);
    $testPointsStatement->bind_result($maxPoints, $sumPoints);
    $testPointsStatement->execute();
    $testPointsStatement->fetch();
    $testPointsStatement->close();
    
    $sum = 0;
    foreach($pointsEarnedArray as $k)
        $sum += $k;
        $sum = $sum / $sumPoints * $maxPoints;
    $pointsEarnedStatement->bind_param("sss", $sum, $testId, $studentId);
    $pointsEarnedStatement->execute();
    $pointsEarnedStatement->close();
?>