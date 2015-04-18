<?php
   // Authors: Jake Stevens
	// Date Created: 3/31/15
	// Last Modified: 3/31/15
	// This php script handles the db stuff for submitting T/F, MC, Matching, and ATA questions
	require("../constants.php");
    
    $database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
    
    $updateQuestionQuery1 = "update answer set student_selection = ? where answer_id = ?";
    $updateQuestionQuery2 = "update answer set student_selection = ? where answer_id = ?";
    $updateQuestionQuery3 = "update answer set student_selection = ? where answer_id = ?";
    $updateQuestionQuery4 = "update question set student_answer = ? where question_id = ?";
    
    @$multipleChoiceArray = $_POST['multipleChoiceArray'];
    @$multipleChoiceAnswerArray = $_POST['multipleChoiceAnswerArray'];
    @$trueFalseArray = $_POST['trueFalseArray'];
    @$trueFalseAnswerArray = $_POST['trueFalseAnswerArray'];
    @$ataArray = $_POST['ataArray'];
    @$ataAnswerArray = $_POST['ataAnswerArray'];
    @$matchingArray = $_POST['matchingArray'];
    @$matchingAnswerArray = $_POST['matchingAnswerArray'];
    
    
    $updateQuestionStatement1 = $database->prepare($updateQuestionQuery1);
    $updateQuestionStatement2 = $database->prepare($updateQuestionQuery2);
    $updateQuestionStatement3 = $database->prepare($updateQuestionQuery3);
    $updateQuestionStatement4 = $database->prepare($updateQuestionQuery4);
    
    for($i = 0; $i < count($multipleChoiceArray); $i++)
    {
        $updateQuestionStatement1->bind_param("ss", $multipleChoiceAnswerArray[$i], $multipleChoiceArray[$i]);
        $updateQuestionStatement1->execute();
    }
    $updateQuestionStatement1->close();
    
    for($i = 0; $i < count($trueFalseArray); $i++)
    {
        $updateQuestionStatement2->bind_param("ss", $trueFalseAnswerArray[$i], $trueFalseArray[$i]);
        $updateQuestionStatement2->execute();
    }
    $updateQuestionStatement2->close();
    
    for($i = 0; $i < count($ataArray); $i++)
    {
        $updateQuestionStatement3->bind_param("ss", $ataAnswerArray[$i], $ataArray[$i]);
        $updateQuestionStatement3->execute();
    }
    $updateQuestionStatement3->close();
    
    for($i = 0; $i < count($matchingArray); $i++)
    {
        $updateQuestionStatement4->bind_param("ss", $matchingAnswerArray[$i], $matchingArray[$i]);
        $updateQuestionStatement4->execute();
    }
    $updateQuestionStatement4->close();
?>