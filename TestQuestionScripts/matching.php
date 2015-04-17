<?php
	// Authors: David Hughen - Jake Stevens
	// Date Created: 3/13/15
	// Last Modified: 3/13/15 - 3/16/15
	// This php script handles the db stuff for matching questions
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
	@$pointValue = $_POST["pointValue"];
	@$questions = $_POST["questions"];
	@$questionLetters = $_POST["questionLetters"];
	@$answers = $_POST["answers"];
	@$answerLetters = $_POST["answerLetters"];
	@$testId = $_POST["testId"];
	@$questionType = $_POST["questionType"];
	@$heading = $_POST["heading"];
    @$classId = $_POST["classId"];
	
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);
	
	$headingQuery = "select max(heading_id) from question";
	// Question id query
	$questionIdQuery  = "select max(question_id) from question";
	
	$answerIdQuery = "select max(answer_id) from answer";
	
	$getQuestionQuery = "select question_id from question where heading_id = ? && question_letter = ?";
	
	$insertQuestionQuery = "insert into question(question_id, student_id, test_id,
		question_type, question_value, question_text, question_letter, question_no, heading_id, heading)
		values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
	$insertAnswerQuery = "insert into answer(answer_id, question_id, answer_text, correct, a_heading_id)
		values(?, ?, ?, ?, ?)";
		
	$questionNumberQuery = "select max(question_no) from question where test_id = ?";
    
    $studentIdQuery = "select student_id from enrollment where class_id = ?";
    
    // Generate new question number
    $questionNumberStatement = $database->prepare($questionNumberQuery);
    $questionNumberStatement->bind_param("s", $testId);
    $questionNumberStatement->bind_result($qno);
    $questionNumberStatement->execute();
    while($questionNumberStatement->fetch())
    {
        $firstQuestionNumber = $qno + 1;
        $newQuestionNumber = $qno + 1;
    }
    $questionNumberStatement->close();
    
    $idArray = array();
	$studentIdStatement = $database->prepare($studentIdQuery);
    $studentIdStatement->bind_param("s", $classId);
	$studentIdStatement->bind_result($sid);
	$studentIdStatement->execute();
	while($studentIdStatement->fetch())
	{
		array_push($idArray, $sid);
	}
	$studentIdStatement->close();
	
	// assign a new heading id
	$headingStatement = $database->prepare($headingQuery);
	$headingStatement->bind_result($hid);
	$headingStatement->execute();
	while($headingStatement->fetch())
	{
		$newHeadingId = $hid + 1;
	}
	$headingStatement->close();
	
    array_push($idArray, 0);
    for($j = 0; $j < count($idArray); $j++)
    {
        $newQuestionNumber = $firstQuestionNumber;
        // assign a new question id
        $questionIdStatement = $database->prepare($questionIdQuery);
        $questionIdStatement->bind_result($qid);
        $questionIdStatement->execute();
        while($questionIdStatement->fetch())
        {
            $newQuestionId = $qid + 1;
        }
        $questionIdStatement->close();
        
        echo $newQuestionId;
        
        // assign a new answer id
        $answerIdStatement = $database->prepare($answerIdQuery);
        $answerIdStatement->bind_result($qid);
        $answerIdStatement->execute();
        while($answerIdStatement->fetch())
        {
            $newAnswerId = $qid + 1;
        }
        $answerIdStatement->close();
        
        // Insert into question table after question is created
        if(is_array($questions))
        {
            for($i = 0; $i < count($questions); $i++)
            {
                $insertQuestionStatement = $database->prepare($insertQuestionQuery);
                $insertQuestionStatement->bind_param("ssssssssss", $newQuestionId, $idArray[$j], $testId, $questionType,
                                                          $pointValue, $questions[$i], $questionLetters[$i], $newQuestionNumber, 
                                                          $newHeadingId, $heading);
                $insertQuestionStatement->execute();
                $insertQuestionStatement->close();
                
                $newQuestionId++;
                $newQuestionNumber++;
            }
        }
        
        // Insert into answer table after question is created
        if(is_array($answers))
        {
            for($i = 0; $i < count($answers); $i++)
            {
                $k = 0;
                while($answerLetters[$i] != $questionLetters[$k] and $k < count($questionLetters))
                {
                    $k++;
                }
                if($k >= count($questionLetters))
                {
                    $newQid = 0;
                }
                else
                {
                    $getQuestionStatement = $database->prepare($getQuestionQuery);
                    $getQuestionStatement->bind_param("ss", $newHeadingId, $questionLetters[$k]);
                    $getQuestionStatement->bind_result($qid);
                    $getQuestionStatement->execute();
                    while($getQuestionStatement->fetch())
                    {
                        $newQid = $qid;
                    }
                    $getQuestionStatement->close();
                }
                
                $insertAnswerStatement = $database->prepare($insertAnswerQuery);
                $insertAnswerStatement->bind_param("sssss", $newAnswerId, $newQid, $answers[$i], $answerLetters[$i], $newHeadingId);
                $insertAnswerStatement->execute();
                $insertAnswerStatement->close();
                
                
                $newAnswerId++;
            }
        }
    }
?>