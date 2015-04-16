<?php
// This script allows the admin to add a class
// Author: David Hughen
// Date:   4/3/15

require("../constants.php");

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

$insertClass = "insert into class(class_id, teacher_id, class_description)
					values(?, ?, ?)";
					
$classTeacherQuery = "select first_name,last_name from teacher where teacher_id = ?";
$classTeacherStatement = $database->prepare($classTeacherQuery);

@$classId = $_POST['classId'];
@$teacherId = $_POST['teacherId'];
@$classDescription = $_POST['classDescription'];

	
	// Insert new teacher
	$insertTeacherStatement = $database->prepare($insertClass);
	$insertTeacherStatement->bind_param("sss", $classId, $teacherId, $classDescription);
	$insertTeacherStatement->execute();
	$insertTeacherStatement->close();
	
	$classTeacherStatement->bind_param("s", $teacherId);
	$classTeacherStatement->bind_result($fname, $lname);
	$classTeacherStatement->execute();
	while($classTeacherStatement->fetch())
	{
		 
	}
	echo $fname." " .$lname;
?>