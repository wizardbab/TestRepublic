<?php

	
	if($_POST["var1"])
	{
	$var1 = $_POST["var1"];
	$var2 = $_POST["var2"];
	

	
	}
	$myFile = fopen("textFile.txt", "w");
	fwrite($myFile,$var1);
	fwrite($myFile,$var2);
	fclose($myFile);
	
	
?>