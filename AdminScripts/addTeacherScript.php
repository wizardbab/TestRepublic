<?php
// This script allows the admin to add a teacher
// Author: David Hughen
// Date:   4/3/15

require("../constants.php");

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

$maxId = "select max(teacher_id) from teacher";

$insertTeacher = "insert into teacher(teacher_id, first_name, last_name, email, teacher_password)
					values(?, ?, ?, ?, ?);";
					

@$firstName = $_POST['firstName'];
@$lastName = $_POST['lastName'];
@$email = $_POST['email'];
@$password = $_POST['password'];

echo $firstName . '<br />' .
	 $lastName . '<br />' .
	 $email . '<br />' .
	 $password . '<br />' ;


	// Assign a new teacher id
	$maxIdStatement = $database->prepare($maxId);
	$maxIdStatement->bind_result($tid);
	$maxIdStatement->execute();
	while($maxIdStatement->fetch())
	{
		$newTeacherId = $tid + 1;
	}
	$maxIdStatement->close();
	
	// Insert new teacher
	$insertTeacherStatement = $database->prepare($insertTeacher);
	$insertTeacherStatement->bind_param("sssss", $newTeacherId, $firstName, $lastName, $email, $password);
	$insertTeacherStatement->execute();
	$insertTeacherStatement->close();
	
	


?>