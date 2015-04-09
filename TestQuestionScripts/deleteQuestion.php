<?php
// Author: Jake Stevens
	// Date Created: 3/26/15
	// Last Modified: 3/26/15
	// This php script handles deleting questions
	require("../constants.php");
    
    // The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);
    
    $deleteQuestionQuery = "delete from question where question_no = ?";
    
    @$qno = $_POST["qno"];
    
	$deleteQuestionStatement = $database->prepare($deleteQuestionQuery);
	$deleteQuestionStatement->bind_param("s", $qno);
	$deleteQuestionStatement->execute();
	$deleteQuestionStatement->close();
?>