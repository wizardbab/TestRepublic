<?php
	// Authors: David Hughen - Jake Stevens
	// Date Created: 3/13/15
	// Last Modified: 3/13/15 - 
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
	
	echo $pointValue;
	
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);
	
	// Question id query
	$questionIdQuery  = "select max(question_id) from question";
	
	$answerIdQuery = "select max(answer_id) from answer";
	
	$insertQuestionQuery = "insert into question(question_id, test_id,
		question_type, question_value, question_text, question_letter, question_no, heading)
		values(?, ?, ?, ?, ?, ?, ?, ?)";
		
	$insertAnswerQuery = "insert into answer(answer_id, question_id, answer_text, correct)
		values(?, ?, ?, ?)";
		
	$questionNumberQuery = "select max(question_no) from question where test_id = ?";
	
	// assign a new question id
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
	$answerIdStatement->bind_result($qid);
	$answerIdStatement->execute();
	while($answerIdStatement->fetch())
	{
		$newAnswerId = $qid + 1;
	}
	$answerIdStatement->close();
	
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
	
	echo $testId;
	
	// Insert into question table after question is created
	if(is_array($questions))
	{
		for($i = 0; $i < count($questions); $i++)
		{
			$insertQuestionStatement = $database->prepare($insertQuestionQuery);
			$insertQuestionStatement->bind_param("ssssssss", $newQuestionId, $testId, $questionType,
													  $pointValue, $questions[$i], $questionLetters[$i], $newQuestionNumber, $heading);
			$insertQuestionStatement->execute();
			$insertQuestionStatement->close();
		}
	}
	
	// Insert into answer table after question is created
	if(is_array($answers))
	{
		for($i = 0; $i < count($questions); $i++)
		{
			$insertAnswerStatement = $database->prepare($insertAnswerQuery);
			$insertAnswerStatement->bind_param("ssss", $newAnswerId, $newQuestionId, $answers[$i], $answerLetters[$i]);
			$insertAnswerStatement->execute();
			$insertAnswerStatement->close();
		}
	}
?>