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

$_SESSION['username'] = $id;

// Class id and description query
$query = "select class_id, class_description from enrollment join class using (class_id) where student_id = ?";

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Class, etc, to display on studentMainPage
$tableQuery = "select class_id, c_update, update_date from student
join enrollment using (student_id)
join class using (class_id)
where student_id = ?";

// Display any tests that will expire within 7 days
$warningQuery = "select class_id, datediff(date_end, sysdate()) as days_left from enrollment
join class using (class_id)
join test using(class_id)
where student_id = ? and datediff(date_end, sysdate()) < 7 and datediff(date_end, sysdate()) > 0";

// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);
$topRightStatement = $database->prepare($topRightQuery);
$table = $database->prepare($tableQuery);
$warningstmt = $database->prepare($warningQuery);

?>

	<!-- Added by Victor -->
	<?php require("Nav.php");?>
	
    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li>
                    <a href="#" id="student-summary">Summary</a>
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
               echo '<li><a href=studentClassPage.php?class_id='.$class_id = str_replace(" ", "%20", $clid).'><b>'.$clid.'</b><div class=subject-name>'.$clde.'</div></a></li>';
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
					<h2 class="warning_sign_msg"> Warning(s): </h2>
                    <div class="col-md-12" id="warning_box1">
                        <div class="warning_box">
							<p class="warning_msg"> 
                                <?php
                                // Display warnings if a test has seven days or less to take
                                $warningstmt->bind_param("s", $id);
                                $warningstmt->bind_result($class_id, $days_left);
                                $warningstmt->execute();
                                while($warningstmt->fetch())
                                {
                                    echo $class_id . ' test will expire in ' . $days_left . ' day(s).';
                                    echo '<br />';
                                }
                                if($class_id == null)
                                    echo 'No warnings :)';
                                $warningstmt->close();
                            ?>
                                </p>
						</div>
                    </div>
					
					<!-- our code starts here :) -->
					<table class="student_summary">
					
						<colgroup>
							<col class="classes" />
							<col class="recent_updates" />
							<col class="date" />
						</colgroup>
						
						<thead>
						<tr>
							<th>Classes</th>
							<th>Recent Updates</th>
							<th>Date</th>
						</tr>
						</thead>
						
						<tbody>
						<?php 
							// Code added by David Hughen to display class id, update, and date
							// inside the table in the middle of the page
							$table->bind_param("s", $id);
							$table->bind_result($clid, $update, $date);
							$table->execute();
							while($table->fetch())
							{	
								echo '<tr><td><button type="button" class="course_button" onclick="location.href=\'studentClassPage.php?class_id='.str_replace(" ", "%20", $clid).'\'">'.$clid.'</button></td>
									  <td>'.$update.'</td>
									  <td>'.$date.'</td></tr>';
							}
							$table->close(); 
							?>	
							</tbody>
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