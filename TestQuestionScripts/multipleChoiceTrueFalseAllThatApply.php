<?php
	// Authors: David Hughen - Jake Stevens
	// Date Created: 3/9/15
	// Last Modified: 3/9/15
	// This php script handles the db stuff for multiple choice, all that apply, and true/false questions
	require("../constants.php");

	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);
	
	// Question id query
	$questionIdQuery  = "select max(question_id) from question";
	
	$answerIdQuery = "select max(answer_id) from answer";
	
	$insertQuestionQuery = "insert into question(question_id, student_id, test_id,
		question_type, question_value, question_text, question_no)
		values(?, ?, ?, ?, ?, ?, ?)";
		
	$insertAnswerQuery = "insert into answer(answer_id, question_id, answer_text, correct)
	values(?, ?, ?, ?)";
		
	$questionNumberQuery = "select max(question_no) from question where test_id = ?";
    
    $studentIdQuery = "select student_id from enrollment where class_id = ?";
	
	// Grab the values posted by testCreationPage.php
	@$pointValue = $_POST["pointValue"];
	@$question = $_POST["question"];
    @$classId = $_POST["classId"];
	@$answer = $_POST["answer"];
	@$testId = $_POST["testId"];
	@$questionType = $_POST["questionType"];
	@$correct = $_POST["correct"]; // boolean value
	@$textBoxAnswers = $_POST["textBoxes"];
	@$parameters = $_POST["parameters"]; // an array
	/*if(is_array($parameters))
	{
		foreach($parameters as $i)
			echo $i . " ";
		
	}
	echo $pointValue;
	echo $question; */
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
	
    // Generate new question number
    $questionNumberStatement = $database->prepare($questionNumberQuery);
    $questionNumberStatement->bind_param("s", $testId);
    $questionNumberStatement->bind_result($qno);
    $questionNumberStatement->execute();
    while($questionNumberStatement->fetch())
    {
        $newQuestionNumber = $qno + 1;
    }
    $questionNumberStatement->close();

	echo $newQuestionNumber;
	
    if(count($idArray) == 0)
        array_push($idArray, 0);
	// assign a new question id
    for($k = 0; $k < count($idArray); $k++)
    {
        $questionIdStatement = $database->prepare($questionIdQuery);
        $questionIdStatement->bind_result($qid);
        $questionIdStatement->execute();
        while($questionIdStatement->fetch())
        {
            $newQuestionId = $qid + 1;
        }
        $questionIdStatement->close();
        
        // assign a new answer id
        $answerIdStatement = $database->prepare($answerIdQuery);
        $answerIdStatement->bind_result($aid);
        $answerIdStatement->execute();
        while($answerIdStatement->fetch())
        {
            $newAnswerId = $aid + 1;
        }
        $answerIdStatement->close();
        
        // Insert into question table after question is created
        $insertQuestionStatement = $database->prepare($insertQuestionQuery);
        $insertQuestionStatement->bind_param("sssssss", $newQuestionId, $idArray[$k], $testId, $questionType,
                                                  $pointValue, $question, $newQuestionNumber);
        $insertQuestionStatement->execute();
        $insertQuestionStatement->close();
        if((is_array($parameters)) and (is_array($textBoxAnswers)))
        {
            for($i = 0; $i < count($parameters); $i++)
            {
                // Insert into answer table after question is created
                $insertAnswerStatement = $database->prepare($insertAnswerQuery);
                $insertAnswerStatement->bind_param("ssss", $newAnswerId, $newQuestionId, $textBoxAnswers[$i], $parameters[$i]);
                $insertAnswerStatement->execute();
                $insertAnswerStatement->close();
                
                $newAnswerId++;
            }
        }
    }
?>