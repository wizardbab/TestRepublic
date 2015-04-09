<?php
	session_start();

    require("constants.php");

$id = $_POST['username']; // The id from login.html
$password = $_POST['password'];

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

// Put your query here. Not required to put it in a variable but it's way cleaner and easier.
// The question mark is for the bind_param below. Replace all variables with a question mark.
$query = "SELECT first_name, last_name from student WHERE student_id = ? and student_password = ?";

// The question mark is for the bind_param below. Replace all variables with a question mark.
$adminQuery = "SELECT admin_id, admin_password from admin WHERE admin_id = ? and admin_password = ?";

// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);

$query1 = "SELECT first_name, last_name from teacher WHERE teacher_id = ? and teacher_password = ?";
$tea = $database->prepare($query1);
if($tea == false)
{
	echo "fail!";
}

$adminStmt = $database->prepare($adminQuery);

// Bind the result to PHP variables. The number of results MUST match the number specified in the query above. Names don't have to be the same.
$stmt->bind_param("ss", $id, $password);
$stmt->bind_result($stu1, $stu2);
$tea->bind_param("ss", $id, $password);
$tea->bind_result($tea1, $tea2);
$adminStmt->bind_param("ss", $id, $password);
$adminStmt->bind_result($admin1, $admin2);

// ALWAYS use bind_param when you have a WHERE clause with $_GET and $_POST variables. This prevents SQL injection attacks.
// Otherwise someone could put "drop table users" in the get url and drop your table. This prevents that.
// First parameter is the number of parameters and their type. 1 s means 1 string. D means digit or decimal I believe.


// Execute the SQL
$stmt->execute();

$type = ""; // Determines whether a student or teacher or nothing

// Fetch all of the results one at a time. Access them from the variables in the bind_result

while($stmt->fetch())
{
   echo $stu1 . " - " . $stu2 . " - ";
}

$tea->execute();
while($tea->fetch())
{
   echo $tea1 . " - " . $tea2 . " - ";
}

$adminStmt->execute();
while($adminStmt->fetch())
{
   echo $admin1 . " - " . $admin2 . " - ";
}

// Post student id and go to student main page
if($stu1 != null)
{
	$_SESSION['username'] = $id;
	header('Location: studentMainPage.php');
}
// Post teacher id and go to teacher main page
else if($tea1 != null)
{
	$_SESSION['username'] = $id;
	header('Location: teacherMainPage.php');
}
// Post administrator id and go to administrator main page
else if($admin1 != null)
{
	$_SESSION['username'] = $id;
	header('Location: adminMainPage.php');
}
else
{
	 header('Location: login.html');
}

// Close the database connection
mysqli_close($database);
?>