<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/newlogo.ico">

    <title>Test Republic - Main Page</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>    
	

</head>

<body>   

<?php
// Php connections added by David Hughen 2/11/15
// After Andrea Setiawan made modification to the student's html file
session_start();

// Include the constants used for the db connection
require("constants.php");

// 'CSWEB.studentnet.int', 'team1_cs414', 'CS414t1', 'cs414_team_1')

$id = $_SESSION['username']; // Just a random variable gotten from the URL

if($id == null)
    header('Location: login.html');
    
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

//query for listing the classes the teacher teaches
/* select class_id from teacher
join class
using(teacher_id)
where teacher_id = ? */


// query for students who have not taken a test:

// Class id and description query
$query = "select class_id, class_description from teacher join class using(teacher_id) where teacher_id = ?";

// Teacher first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from teacher where teacher_id = ?";



$tableQuery = "select class_id, count(graded), date_taken from class
left join test using(class_id, teacher_id)
left join test_list using(test_id)
where teacher_id = ? and (graded != 1 or graded is null)
group by class_id";

$warningQuery = "select class_id, datediff(date_end, sysdate()) as days_left from enrollment
join class using (class_id)
join test using(class_id)
where student_id = ? and datediff(date_end, sysdate()) < 7 and datediff(date_end, sysdate()) > 0";

// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);
$topRightStatement = $database->prepare($topRightQuery);
$warningstmt = $database->prepare($warningQuery);
$table = $database->prepare($tableQuery);

?>
	<!-- Added by Victor, replaces the nav bar -->
	<?php require("Nav.php");?>
	
    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li>
                    <a href="#" id="student-summary">Main Page</a>
                </li>
                <li class="sidebar-brand">
                    Select a Class:
                </li>
               
				<?php 
				// Added by David Hughen
				// The code to fetch the student's classes and put them in the sidebar to the left
				$stmt->bind_param("s", $id);
				$stmt->bind_result($clid, $clde);
				$stmt->execute();
				$classId = str_replace(" ", "%20", $clid);
				while($stmt->fetch())
				{	
					echo '<li><a href=teacherClassPage.php?classId=' . $class_id = str_replace(" ", "%20", $clid) . '>' .'<b>'. $clid .'</b>'. '<div class=subject-name>' . $clde . '</div></a></li>';
				}
				$stmt->close();
				?>
            </ul>
        </div>
		
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
                <div class="row">
					<!-- our code starts here :) -->
					<table class="teacher_summary">
					
						<colgroup>
							<col class="classes" />
							<col class="recent_updates" />
							<col class="date" />
						</colgroup>
						
						<thead>
						<tr>
							<th>Classes</th>
							<th>Recent Updates</th>
						</tr>
						</thead>
						
						<tbody>
						<?php 
							// The query for the middle of the page
							$table->bind_param("s", $id);
							$table->bind_result($clid, $update, $date);
							$table->execute();
							while($table->fetch())
							{	
								echo '<tr><td><button type="button" class="course_button" onclick="location.href=\'teacherClassPage.php?classId='.$clid.'\'">'.$clid.'</button></td>
									  <td>'.$update.' test(s) to grade</td></tr>';
							}
							$table->close(); 
							?>			
					</table>
                </div>

            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>

</html>