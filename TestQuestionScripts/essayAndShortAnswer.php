<?php
	// Authors: David Hughen - Jake Stevens
	// Date Created: 3/6/15
	// Last Modified: 3/9/15 - modified question number query
	// This php script handles the db stuff for essay and short answer questions
	require("../constants.php");
	
	// Grab the values posted by testCreationPage.php
	@$pointValue = $_POST["pointValue"];
	@$question = $_POST["question"];
	@$answer = $_POST["answer"];
	@$testId = $_POST["testId"];
	@$questionType = $_POST["questionType"];
	
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);
	
	// Question id query
	$questionIdQuery  = "select max(question_id) from question";
	
	$answerIdQuery = "select max(answer_id) from answer";
	
	$insertQuestionQuery = "insert into question(question_id, test_id,
		question_type, question_value, question_text, question_no)
		values(?, ?, ?, ?, ?, ?)";
		
	$insertAnswerQuery = "insert into answer(answer_id, question_id, answer_text)
		values(?, ?, ?)";
		
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
	
	// Insert into question table after question is created
	$insertQuestionStatement = $database->prepare($insertQuestionQuery);
	$insertQuestionStatement->bind_param("ssssss", $newQuestionId, $testId, $questionType,
											  $pointValue, $question, $newQuestionNumber);
	$insertQuestionStatement->execute();
	$insertQuestionStatement->close();
	
	// Insert into answer table after question is created
	$insertAnswerStatement = $database->prepare($insertAnswerQuery);
	$insertAnswerStatement->bind_param("sss", $newAnswerId, $newQuestionId, $answer);
	$insertAnswerStatement->execute();
	$insertAnswerStatement->close();
?>