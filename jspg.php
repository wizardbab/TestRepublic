
<?php
	$array = json_decode($_POST["array"], true);
	//$var1 = $_POST["var1"];
	//$var2 = $_POST["var2"];

	
	$myFile = fopen("textFile.JSON", "w");
	
	fwrite($myfile,$array[1]["Answer"]);
	fclose($myFile);
	/*
	fwrite($myFile,"[{\"Question\": \"".$var1."\",");
	fwrite($myFile,"\"Answer\": \"".$var2."\"");
	fwrite($myFile,"}]");
	fclose($myFile);*/
?>