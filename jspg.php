<?php

	@$value = $_POST['parameters'];
	
	if(is_array($value))
	{
		foreach($value as $i)
			echo $i . " ";
	}

									
?>
		
		

