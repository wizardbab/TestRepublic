<?php

	// Include the constants used for the db connection
	require("constants.php");

	// The database variable holds the connection so you can access it
	$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@ $database->select_db(DATABASENAME);

	
	$insertTeacher = 'insert into teacher(teacher_id,first_name,last_name,teacher_password,email)
						values(?,?,?,?,?)';
	$insertClass = 'insert into class(class_id,teacher_id,class_description)
						values(?,?,?)';
	$updateClass = 'update class set teacher_id = ? where class_id = ?';

	
	
?>