<?php

	
	
	$shortAnswer = (isset($_POST['shortAnswer']) ? $_POST['shortAnswer'] : " ");
	
	$myFile = fopen("textFile.json", "w");
	while(!eof($myFile))
	{
		fwrite($myFile, $shortAnswer);
	}
	fclose($myFile);
	
	
	
	
	
?>