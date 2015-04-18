<?php
    // Authors: Jake Stevens
	// Date Created: 4/17/15
	// Last Modified: 4/17/15
	// This php script handles the db stuff for submitting Matching questions
	require("../constants.php");

    $database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
	@$database->select_db(DATABASENAME);
    
    $update = "update question set";

?>