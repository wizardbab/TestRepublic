<?PHP
	// Code to logout of the current page whether the user
	// be student or teacher
	// Author: David Hughen
	// Date:   2/11/2015
	session_start();
	session_destroy();
	header('Location: http://csweb/CS414/Team1/FrontEnd-master/login2.html');
?>