<?php
	// Authors: David Hughen - Jake Stevens
	// Date Created: 3/6/15
	// Last Modified: 3/6/15
	// This php script handles the db stuff for essay and short answer questions
	require("../constants.php");
	
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);
	
	// Question id query
	$questionIdQuery  = "select max(question_id) from question";
	
	// assign a new question id
	$questionIdStatement = $database->prepare($questionIdQuery);
	$questionIdStatement->bind_result($qid);
	$questionIdStatement->execute();
	while($questionIdStatement->fetch())
	{
		$newQuestionId = $qid + 1;
	}
	$questionIdStatement->close();
	
	@$question = $_POST["question"];
	@$answer = $_POST["answer"];
	@$testId = $_POST["testId"];
	@$questionType = $_POST["questionType"];
	
	echo $question;
	echo $answer;
	echo $testId;
	echo $questionType;
	echo $newQuestionId;
	
?>