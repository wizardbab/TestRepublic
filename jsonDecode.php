<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
</head>

<body>
<?php

	
	$someJSON ='[ {"Answer" : "woof"}, {"Answer" : "oink"}]';
	$someArray = json_decode($someJSON, true);
	echo $someArray[0]["Answer"];

	
	//$myFile = fopen("textFile.JSOn", "w");
	foreach ($someArray as $key => $value)
	{
		//fwrite($myFile, $someArray[0]["Answer"]);
	}
	//fclose($myFile);
	
	/*
	fwrite($myFile,"[{\"Question\": \"".$var1."\",");
	fwrite($myFile,"\"Answer\": \"".$var2."\"");
	fwrite($myFile,"}]");
	fclose($myFile);
	$someArray[0]["Answer"]
	*/
?>
</body>