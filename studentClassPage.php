<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" href="images/newlogo.ico">

    <title>Test Republic</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/studentClassPage.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

<?php
// Php connections added by David Hughen 2/11/15
// After Andrea Setiawan made modification to the student's html file
session_start();

// Include the constants used for the db connection
require("constants.php");

$id = $_SESSION['username']; // Just a random variable gotten from the URL

if($id == null)
    header('Location: login.html');

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

// Class id and description query
$query = "select class_id, class_description from enrollment join class using (class_id) where student_id = ?";

$mainClassQuery = "select class_id, class_description from class where class_id = ?";

$averageQuery = "select sum(points_earned) / sum(question_value) * 100
from question
join test using(test_id)
join test_list using(test_id, student_id)
where student_id = ? and class_id = ? and graded = 1";

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Display any tests that will expire within 7 days
$warningQuery = "select class_id, datediff(date_end, sysdate()) as days_left from enrollment
join class using (class_id)
join test using(class_id)
where student_id = ? and datediff(date_end, sysdate()) < 7 and datediff(date_end, sysdate()) > 0";

// Class, etc, to display on studentMainPage
$tableQuery = "select test_id, test_name, sum(points_earned)/sum(question_value)*100, date_begin, date_end, date_taken, graded from test
join test_list using(test_id)
join question using(test_id, student_id)
where student_id = ? and class_id = ?
group by(test_id)";
$_SESSION['classId'] = null;

$_SESSION['testId'] = null;

// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);
$mainClassStatement = $database->prepare($mainClassQuery);
$topRightStatement = $database->prepare($topRightQuery);
$table = $database->prepare($tableQuery);
$warningstmt = $database->prepare($warningQuery);
$averageStatement = $database->prepare($averageQuery);
global $class_id;

?>
		<!-- Added by Victor -->
	<?php require("Nav.php");?>
	
    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li>
                    <a href="studentMainPage.php" id="student-summary">Main Page</a>
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
				while($stmt->fetch())
				{
					
               // Modified by En Yang Pang
               // Gets the class id to display in the url correctly
					echo '<li><a href=studentClassPage.php?classId='.str_replace(" ", "%20", $clid).'><b>'.$clid.'</b><div class=subject-name>'.$clde.'</div></a></li>';
				}
				$stmt->close();
				?>
            </ul>
        </div>
		  
		  <?php
		  // This is excellent program practice xD - By David Hughen
		   $class_id = $_GET['classId'];
			$classId = str_replace("%20", " ", $class_id);
			$mainClassStatement->bind_param("s", $classId);
			$mainClassStatement->bind_result($clid, $clde);
			$mainClassStatement->execute();
			while($mainClassStatement->fetch())
			{
				echo '<div class="course_header">
	
			<div class="course_number">'
				. $clid .
			'</div>
			
			<div class="class_name">'
				. $clde . 
			'</div>
			</div>'; 
			}
			$mainClassStatement->close();
		?>
		
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
                <div class="row">
					
					<!-- our code starts here :) -->
                    <?php
                        $averageStatement->bind_param("ss", $id, $clid);
                        $averageStatement->bind_result($count);
                        $averageStatement->execute();
                        while($averageStatement->fetch())
                        {
                            echo '<br /><br /><br /><br /><br /><br /><br /><br /><br />';
                            if($count == null)
                                echo 'Grade: No assignments graded';
                            else
                            {
                                $count = number_format($count, 2);
                                echo 'Class Grade: ' . (float)$count . '%';
                            }
                        }
                        $averageStatement->close();
                    ?>
					<table class="class_table">
					
						<colgroup>
							<col class="list_test" />
							<col class="status" />
							<col class="date_frame" />
							<col class="option" />
						</colgroup>
						
						<thead>
						<tr>
							
							<th>List of Tests</th>
							<th>Grade</th>
							<th>Date Frame</th>
							<th>Option</th>
						</tr>
						</thead>
						
						<tbody>
						<?php 
						// Gets the current time formatted like MySql
						$time = time();
						$currentTime = date("Y-m-d", $time);
							
							// Code modified by En Yang Pang to display test list, status, and date frame
							// inside the table in the middle of the page
                            $class = $_GET['classId'];
							$classId = $class;
							$table->bind_param("ss", $id, $classId);
							$table->bind_result($test_id, $test_list, $status, $date_begin, $date_end, $date_taken, $graded);
							$table->execute();
							while($table->fetch())
							{
								echo '<tr><td>'.$test_list.'</td>';
                                if($date_taken != null)
								{
                                    if($graded != 1)
                                        echo '<td>Grade Pending</td>';
                                    else
                                    {
                                        $status = number_format($status, 2);
                                        echo'<td>'.(float)$status.'%</td>';
                                    }
                                }
                                else
                                    echo '<td>Not Taken</td>';

								echo'<td>'.$date_begin.' - '.$date_end.'</td>';
										if($date_taken != null)
										{
                                            if($graded != 1)
                                                echo '<td>Grade Pending</td>';
                                            else
                                                echo '<td><form action="testViewing.php" method="post">
															<input type="hidden" value="'.$class.'" name="classId" id="classId"/>
															<input type="hidden" value="'.$test_id.'" name="testId" id="testId"/>
															<input type="hidden" value="'.$test_list.'" name="testName" id="testName"/>
															<input type="hidden" value="'.$id.'" name="studentId" id="studentId"/>
															<input type="submit" value="View Test" class="btn btn-primary"/></form></td>';
										}
										else if($currentTime >= $date_begin and $currentTime <= $date_end)
										{
											echo '<td><form action="testInstructionPage.php" method="post">
															<input type="hidden" value="'.$class.'" name="classId" id="classId"/>
															<input type="hidden" value="'.$test_id.'" name="testId" id="testId"/>
															<input type="hidden" value="'.$test_list.'" name="testName" id="testName"/>
															<input type="submit" value="Take Test" class="btn btn-primary"/></form></td>';
										}
										else
										{
											echo '<td><button type="button" class="btn btn-danger" disabled>Unavailable</button></td>';
										}
										echo '</tr>';
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