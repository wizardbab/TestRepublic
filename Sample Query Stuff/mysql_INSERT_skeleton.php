<?php
   // Import the constants file. This imports DATABASEADDRESS, DATABASEUSER, DATABASEPASS, and DATABASENAME
   requires("constants.php");
   // Import the functions file. This importants the database_down() function
   requires("functions.php");
   
   // Declare our database connection variable
   $database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

   // Check to ensure that there is no error connecting to the database. If there is, go to the database_down() function
   if (mysqli_connect_errno())
   {
      database_down();
   }
     
   // Select the database that we want to use. If it's not available,go to the database_down() function
   @ $database->select_db(DATABASENAME) or database_down();
   
   // We store our query into a string in case we need to concatenate more on later (for complex queries)
   // Again, be sure to use prepared statements for any values that could potentially be edited by a user
   $query = "INSERT INTO table_name (column1, column2, column3) VALUES (?, ?, ?);";

   // The prepare function just sets the query that we just declared so that we can execute it later with the execute function
   $statement = $database->prepare($query);

   // The bind_param function works with the previous prepared statement in our query. (The ?)
   // Normally you should check to make sure there is actually data in $_GET 1, 2, and 3 but this is just a quick example
   $statement->bind_param("sss", $_GET['1'], $_GET['2'], $_GET['3']);

   // Note that there is no bind_result or fetch function in an INSERT statement

   // The execute function is all that is required since we are not fetching any rows from the database
   $statement->execute();  

   // We are done accessing the database so it's time to close it.
   mysqli_close($database);
?>