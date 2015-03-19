<?php
   // Authors: Jake Stevens
	// Date Created: 3/18/15
	// Last Modified: 3/18/15
	// This php script handles the db stuff for publishing a test
	require("../constants.php");

	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
    
    $publishTestQuery = "insert into test_list(student_id, test_id)
        select student_id, ? from enrollment where class_id = ?";

    @$newTestId = $_POST['newTestId'];
    @$classId = $_POST['classId'];
    
    $publishTestStatement = $database->prepare($publishTestQuery);
    $publishTestStatement->bind_param("ss", $newTestId, $classId);
	$publishTestStatement->execute();
	$publishTestStatement->close();


?>