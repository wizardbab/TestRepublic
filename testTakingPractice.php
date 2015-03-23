<?php

@$array = $_POST['array'];


foreach($array as $a)
				{
					foreach($a as $key => $value)
					{
						echo $value ." ";
					} 
				} 

?>