<?php
    // Authors: Jake Stevens
	// Date Created: 4/07/15
	// Last Modified: 4/07/15
	// This php script handles inserting questions for registered students
	require("constants.php");
    
    $database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);


    $testIdQuery = "select test_id, student_id from test
    join test_list using(test_id)
    where class_id = ?
    group by(test_id)";

    $selectQuestionQuery = "select heading, heading_id, question_letter, question_no, question_text, question_type, question_value, test_id, answer_id, answer_text, correct
    from answer
    join question using(question_id)
    join test using(test_id)
    where student_id = ?";
    
    $insertQuestionQuery = "insert into question(heading, heading_id, question_id, question_letter, question_no, question_text, question_type, question_value, test_id)
                            values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertAnswerQuery = "insert into answer(answer_id, answer_text, correct)
                          values(?, ?, ?)";
    $maxQuestionQuery = "select max(question_id) from question";
    $maxAnswerQuery = "select max(answer_id) from answer";

    @$classIdArray = $_POST['classIdArray'];
    @$studentId = $_POST['studentId'];
    
    for($i = 0; $i < count($classIdArray); $i++)
    {
        $testIdStatement = $database->prepare($testIdQuery);
        $testIdStatement->bind_param("s", $classIdArray[$i]);
        $testIdStatement->bind_result($testId, $stuId);
        $testIdStatement->execute();
        $testIdStatement->fetch();
        $testIdStatement->close();
        
        $maxQuestionStatement = $database->prepare($maxQuestionQuery);
        $maxQuestionStatement->bind_result($newQId);
        $maxQuestionStatement->execute();
        $maxQuestionStatement->fetch();
        $newQId = $newQId + 1;
        $maxQuestionStatement->close();
        
        $maxAnswerStatement = $database->prepare($maxAnswerQuery);
        $maxAnswerStatement->bind_result($newAId);
        $maxAnswerStatement->execute();
        $maxAnswerStatement->fetch();
        $newAId = $newAId + 1;
        $maxAnswerStatement->close();

        $selectQuestionStatement = $database->prepare($selectQuestionQuery);
        $selectQuestionStatement->bind_param("s", $stuId);
        $selectQuestionStatement->bind_result($heading, $heading_id, $question_letter, $question_no, $question_text, $question_type, $question_value, $test_id, $answer_id, $answer_text, $correct);
        $selectQuestionStatement->execute();
        while($selectQuestionStatement->fetch())
        {
            questionArray[] = $heading;
            questionArray[] = $heading_id;
            questionArray[] = $newQId;
            questionArray[] = $question_letter;
            questionArray[] = $question_no;
            questionArray[] = $question_text;
            questionArray[] = $question_type;
            questionArray[] = $question_value;
            questionArray[] = $test_id;
            questionArray[] = $newAId;
            questionArray[] = $answer_text;
            questionArray[] = $correct;
            $newQId++;
            $newAId++;
        }
        $selectQuestionStatement->close();
        
        $oldQno = -1;
        for($k = 0; $k < count(questionArray); $k += 12)
        {
            if($oldQno != questionArray[$k+3])
            {
                $insertQuestionStatement = $database->prepare($insertQuestionQuery);
                $insertQuestionStatement->bind_param("sssssssss", questionArray[$k], questionArray[$k+1], questionArray[$k+2], questionArray[$k+3], questionArray[$k+4], questionArray[$k+5], questionArray[$k+6], questionArray[$k+7], questionArray[$k+8]);
                $insertQuestionStatement->execute();
                $insertQuestionStatement->close();
            }
            $insertAnswerStatement = $database->prepare($insertAnswerQuery);
            $insertAnswerStatement->bind_param("sss", questionArray[$k+9], questionArray[$k+10], questionArray[$k+11]);
            $insertAnswerStatement->execute();
            $insertAnswerStatement->close();
        }
    }
?>