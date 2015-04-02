<?php
	// This php script handles the student choices for essay and short answer questions
	// Author: David Hughen
	// Date: 3/31/2015
	
	require("../constants.php");
	
	@$essayIds = $_POST['essayIds'];
	@$essayChoices = $_POST['essayChoices'];
	@$shortAnswerIds = $_POST['shortAnswerIds'];
	@$shortAnswerChoices = $_POST['shortAnswerChoices'];
	
	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
	
	$updateEssayQuery = "update question
							set student_answer = ?
							where question_id = ?";
							
	$updateShortAnswerQuery = "update question
											set student_answer = ?
											where question_id = ?";
							
	$updateEssayStatement = $database->prepare($updateEssayQuery);
	
	// Enter the student's answers for essay questions
	for($i = 0; $i < count($essayIds); $i++)
	{
		$updateEssayStatement->bind_param("ss", $essayChoices[$i], $essayIds[$i]);
		$updateEssayStatement->execute();	
	}	
		$updateEssayStatement->close();
		
	$updateShortAnswerStatement = $database->prepare($updateShortAnswerQuery);
	
	// Enter the student's answers for short answer questions
	for($i = 0; $i < count($shortAnswerIds); $i++)
	{
		$updateShortAnswerStatement->bind_param("ss", $shortAnswerChoices[$i], $shortAnswerIds[$i]);
		$updateShortAnswerStatement->execute();	
	}	
		$updateShortAnswerStatement->close();
?>