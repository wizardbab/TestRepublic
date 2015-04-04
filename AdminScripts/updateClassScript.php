<?php
// This script allows the admin to assign a new teacher to a class
// Author: David Hughen
// Date:   4/3/15

require("../constants.php");

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

$updateClass = "update class
set teacher_id = ?
where class_id = ?";
					
@$classId = $_POST['classId'];
@$teacherId = $_POST['teacherId'];

	
	// Update the class to have a new teacher
	$updateClassStatement = $database->prepare($updateClass);
	$updateClassStatement->bind_param("ss", $teacherId, $classId);
	$updateClassStatement->execute();
	$updateClassStatement->close();
	
	echo$teacherId. " " . $classId;
	
	


?>