<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Republic</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>
<body>

<?php
session_start();

// Include the constants used for the db connection
require("constants.php");

$id = $_SESSION['username'];

// Gets the class id appended to url from teacherMainPage.php
$classId = $_GET['classId'];

$_SESSION['classId'] = $classId;
$_SESSION['testId'] = null;

if($id == null)
    header('Location: login.html');
    
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

$query = "select class_id, class_description from teacher join class using(teacher_id) where teacher_id = ?";

$mainClassQuery = "select class_id, class_description from class where class_id = ?";

// Query for the number of students in the class
$studentCountQuery = "select count(distinct student_id) from enrollment
where class_id = ?";

// Query for the number of tests in the class
$testCountQuery = "select count(distinct test_id) from test_list
join test using(test_id)
where class_id = ?";

// Query to populate the first table on the screen
$firstTableQuery = "select test_name, avg(test_score/max_points*100), test_id, student_id, date_begin, date_end from test_list
right join test using(test_id)
where class_id = ?
group by(test_name)
order by(test_id)";

// Teacher first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from teacher where teacher_id = ?";

// Title bar for student list
$studentTitleQuery = "select test_name from test
join test_list using(test_id)
where class_id = ?
group by(test_id)";

// Student names for student list
$studentNamesQuery = "select first_name, last_name from student
join enrollment using(student_id)
where class_id = ? and student_id = ?";

// Test score for student list
$testScoreQuery = "select student_id, test_score/max_points*100, graded, test_id, test_name, date_taken from test_list
join test
using(test_id)
where student_id = ? and class_id = ?";

// Average score for student list
$averageQuery = "select sum(test_score)/sum(max_points)*100 from test_list
join test using(test_id)
where student_id = ? and class_id = ? and date_taken is not null";

// List of students for student list
$studentQuery = "select student_id from enrollment
where class_id = ?";

// List of tests for student list
$testQuery = "select test_id from test
where class_id = ?";

$queryStatement = $database->prepare($query);
$studentCountStatement = $database->prepare($studentCountQuery);
$mainClassStatement = $database->prepare($mainClassQuery);
$testCountStatement = $database->prepare($testCountQuery);
$studentTitleStatement = $database->prepare($studentTitleQuery);
$studentNamesStatement = $database->prepare($studentNamesQuery);
$studentStatement = $database->prepare($studentQuery);


?>


			<!-- Added by Victor -->
	<?php require("Nav.php");?>
	
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li>
                    <a href="teacherMainPage.php" id="student-summary">Main Page</a>
                </li>
                <li class="sidebar-brand">
                    Select an Assignment:
                </li>
                <?php 
				// Added by David Hughen
				// The code to fetch the student's classes and put them in the sidebar to the left
				$queryStatement->bind_param("s", $id);
				$queryStatement->bind_result($clid, $clde);
				$queryStatement->execute();
				while($queryStatement->fetch())
				{
					
					echo '<li><a href=teacherClassPage.php?classId=' . $cid = str_replace(" ", "%20", $clid) . '><b>' . $clid . '</b><div class=subject-name>' . $clde . '</div></a></li>';
				}
				$queryStatement->close();
				?>
			
            </ul>
        </div>
		
        <?php
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
		
		
        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
				<div class="row">
				
				</div>
			</div>
			
			<div class="container-fluid align-center">
                <div class="row">
				
					<div class="students_num_text">
						Number of Students:
						<span class="students_number"><?php 
						$studentCountStatement->bind_param("s", $classId);
						$studentCountStatement->bind_result($studentCount);
						$studentCountStatement->execute();
						while($studentCountStatement->fetch())
						{
							echo $studentCount;
						}
						$studentCountStatement->close();
						?></span>
					</div>
					
				<div class="row">
					<div class="student_list_text">
						Student List
					</div>
				</div>
				<div class="row">
					<table class="student_list table-hover">
					<tr class="student_list_header">
					<td>First Name</td>
					<td>Last Name</td>
					<?php
						
						// Get the test name on top of second table
						$studentTitleStatement->bind_param("s", $classId);
						$studentTitleStatement->bind_result($testName);
						$studentTitleStatement->execute();
						while($studentTitleStatement->fetch())
						{
							echo '<td>'.$testName.'</td>';
						}
						$studentTitleStatement->close();
					?>		
						<td>Average Grade</td>
						</tr>
						
					<?php
					   // Get the student names
						
						
						// Get the number of tests
						$testCountStatement->bind_param("s", $classId);
						$testCountStatement->bind_result($testCount);
						$testCountStatement->execute();
						$testCountStatement->fetch();
						$testCountStatement->close();
						
						
						
						$studentStatement->bind_param("s", $classId);
						$studentStatement->bind_result($studentId);
						$studentStatement->execute();
						$i = 0;
						while($studentStatement->fetch())
						{
							$studentArray[$i] = $studentId;
							$i++;
						}
						$studentStatement->close();
						
						// Loop through each student
						for($i = 0; $i < $studentCount; $i++)
						{
								
								$studentNamesStatement = $database->prepare($studentNamesQuery);
								$studentNamesStatement->bind_param("ss", $classId, $studentArray[$i]);
								$studentNamesStatement->bind_result($firstName, $lastName);
								$studentNamesStatement->execute();
								echo '<tr>';
							while($studentNamesStatement->fetch())
							{
								echo '<td>'.$firstName . '</td><td>' . $lastName . '</td>';
							}
							$studentNamesStatement->close();
								
									$testScoreStatement = $database->prepare($testScoreQuery);
									$testScoreStatement->bind_param("ss", $studentArray[$i], $classId);
									$testScoreStatement->bind_result($studentId, $testScore, $graded, $testId, $testName, $dateTaken);
									$testScoreStatement->execute();
									while($testScoreStatement->fetch())
									{
                                        // Determines whether test is graded or not
                                        // This will become a button link to grade the test
                                        if($graded == 0)
                                            $graded = '<form action="testGradingPage.php" method="post">
                                            <input type="hidden" value="'.$classId.'" name="classId" id="classId"/>
                                            <input type="hidden" value="'.$testId.'" name="testId" id="testId"/>
                                            <input type="hidden" value="'.$testName.'" name="testName" id="testName"/>
                                            <input type="hidden" value="'.$studentId.'" name="studentId" id="studentId"/>
                                            <input type="submit" value="Grade" class="btn btn-primary btn-block"/>
                                            </form>';
                                        else
                                            $graded = '(view)';
                                        if($dateTaken != null)
                                        {
                                            $testScore = number_format($testScore, 2);
                                            echo '<td>' . (float)$testScore.'% ' . $graded.'</td>';
                                        }
                                        else
                                            echo '<td>Not Taken</td>';
									}
									
									$testScoreStatement->close();
									
							$averageStatement = $database->prepare($averageQuery);
							$averageStatement->bind_param("ss", $studentArray[$i], $classId);
							$averageStatement->bind_result($averageScore);
							$averageStatement->execute();	
							while($averageStatement->fetch())
							{
                                $averageScore = number_format($averageScore, 2);
                                if($averageScore != 0)
                                    echo '<td>' . (float)$averageScore.'%'. '</td>';
                                else
                                    echo '<td>No Tests Taken</td>';
							}
							$averageStatement->close();
					
							echo '</tr>';
						} 
						
						
						
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
