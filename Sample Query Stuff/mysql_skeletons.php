<?php
   // Import the constants file. This imports DATABASEADDRESS, DATABASEUSER, DATABASEPASS, and DATABASENAME
   requires("constants.php");
   // Import the functions file. This importants the database_down() function
   requires("functions.php");
   
   // Get a variable using a GET request (in the URL ex: http://www.google.com/search?q=hello_world)
   // The isset function checks to see if the variable you're trying to get is there. If it's not and you try to access it
   // you will encounter an error, so it's best to handle it this way.
   if (isset($_GET['q']))
   {
      // Do whatever you need to do here if q is set.
   }
   else
   {
      // Do whatever you need to do here if q isn't set.
   }

   // Get a variable using a POST request (these are hidden from the user and are used for secure things like passwords and credit card numbers)
   // Once again, the isset function is used since we cannot guarantee that the data is being sent to the page
   if (isset($_POST['password']))
   {
      // Do whatever you need to do here if the password variable is being sent correctly
   }
   else
   {
      // Do whatever you need to do here if the password variable is not being sent correctly
   }

   // Declare our database connection variable
   $database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

   // Check to ensure that there is no error connecting to the database. If there is, go to the database_down() function
   if (mysqli_connect_errno())
   {
      database_down();
   }
     
   // Select the database that we want to use. If it's not available,go to the database_down() function
   // The @ before any statement is for error suppression. This prevents the page from dumping error information to the user if it
   // encounters an error. We would rather show them an elegant message that they can understand through the database_down function
   @ $database->select_db(DATABASENAME) or database_down();
   
   // We store our query into a string in case we need to concatenate more on later (for complex queries)
   // We will make all MySQL keywords in all caps since it is easier to identify them that way.
   // The ? is called a prepared statement. It prevents the user from purposefully entering bad data into the system and potentially
   // messing up our database. For example, if someone typed "1; DROP TABLE table_name;" they could potentially drop our table if we had
   // used the following without the prepared statement: 
   //    $query = "SELECT column1, column2, column3 FROM table_name WHERE column1 =".$_GET['q']." ORDER BY column1";
   // The prepared statement goes with and requires the upcoming bind_param function. You can have as many prepared statements as you like in a query.
   $query = "SELECT column1, column2, column3 FROM table_name WHERE column1 = ? ORDER BY column1";

   // The prepare function just sets the query that we just declared so that we can execute it later with the execute function
   $statement = $database->prepare($query);

   // The bind_param function works with the previous prepared statement. (The ?)
   // It takes two or more parameters. The first parameter is always a string. It contains either s's or d's or a combination of both.
   // s stands for string and d stands for decimal
   // In order from left to right, it specifies what type the parameters you are binding are. For example, below we only have one variable binded and it is a string.
   $statement->bind_param("s", $_GET['q']);

   // Here is how a bind_param with multiple variables would look:
   //    $statement->bind_param("ssds", $_GET['q'], $_GET['whatever'], $_GET['random_digit_or_whatever'], $_GET['blah']);

   // The bind_result function puts database rows that we are selecting into php variables. It puts them into the variable one at a time
   // and moves to the next row every time the fetch function is called.
   // We will use the word "fetched" before our variable name to easily differentiate fetched variables from other declared variables.
   $statement->bind_result($fetched_column_1, $fetched_column_2, $fetched_column_3);

   // The execute function sends and executes our query with MySQL on our database. However, if we are doing a SELECT statement, we still need to
   // individually fetch the rows.
   $statement->execute();  

   // The echo function injects whatever is echoed directly into the HTML. I am echoing the beginning of a table here so that we can format our
   // data neatly into a table.
   // The table tag makes a table. The tr tag makes a table row. The th tag makes a table heading. The td tag makes a table cell.
   echo "<table><tr><th>Column 1</th><th>Column 2</th><th>Column 3</th></tr>";

   // This loop goes through each individually fetched row starting at the beginning (We are ordering them by column1)
   while($statement->fetch())
   {
      // You can do any data processing here. All data will be put into our variables that we declared earlier. ($fetched_column_1, $fetched_column_2, $fetched_column_3)
      // Each fetch will signify a row of the table since it signifies a row of the database
      // The period is the concatenation operator in PHP.
      echo "<tr><td>".$fetched_column_1."</td><td>".$fetched_column_2."</td><td>".$fetched_column_3."</td></tr>";
   }

   // Since we are done looping, close our table tag.
   echo "</table>";

   // We are done accessing the database so it's time to close it.
   mysqli_close($database);
?>