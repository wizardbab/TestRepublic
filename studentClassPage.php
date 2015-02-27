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

// 'CSWEB.studentnet.int', 'team1_cs414', 'CS414t1', 'cs414_team_1')

$id = $_SESSION['username']; // Just a random variable gotten from the URL

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

// Class id and description query
$query = "select class_id, class_description from enrollment join class using (class_id) where student_id = ?";

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Display any tests that will expire within 7 days
$warningQuery = "select class_id, datediff(date_end, sysdate()) as days_left from enrollment
join class using (class_id)
join test using(class_id)
where student_id = ? and datediff(date_end, sysdate()) < 7 and datediff(date_end, sysdate()) > 0";

// Class, etc, to display on studentMainPage
$tableQuery = "select test_name, t_status, date_begin, date_end, date_taken from test
join test_list using(test_id)
where student_id = ? and class_id = ?";
// Get the class id for certain user
/*"select class_id, c_update, update_date from student
join enrollment using (student_id)
join class using (class_id)
where student_id = ?";*/


// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);
$topRightStatement = $database->prepare($topRightQuery);
$table = $database->prepare($tableQuery);
$warningstmt = $database->prepare($warningQuery);

?>
	<div id="wrapper2"
	 <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
				<a href="#menu-toggle" class="navbar-brand" id="menu-toggle">
					<div id="logo-area">
						<img src="images/logo4.png" alt="Our Logo" height="45" width="45">
						<span class="TestRepublic">Test Republic</span>
					</div>
				</a>
			</div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu alert-dropdown">
                        <li>
                            <a href="#">Alert Name <span class="label label-default">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-primary">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-success">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-info">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-warning">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-danger">Alert Badge</span></a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">View All</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php  // Added by David Hughen
																												// to display student's name in top right corner	
																											   $topRightStatement->bind_param("s", $id);
																												$topRightStatement->bind_result($first_name, $last_name);
																												$topRightStatement->execute();
																												while($topRightStatement->fetch())
																												{
																													echo $first_name . " " . $last_name;
																												}
																												$topRightStatement->close(); ?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->

            <!-- /.navbar-collapse -->
        </nav>
	</div>	
	
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
					echo '<li><a href=studentClassPage.php?class_id='.$class_id = str_replace(" ", "%20", $clid).'>'.$clid.'<div class=subject-name>'.$clde.'</div></a></li>';
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
                    <div class="col-lg-12">
                        <div class="warning_box">
							<p class="warning_msg"><?php
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
							<th>Status</th>
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
                     $class = $_GET['class_id'];
							$table->bind_param("ss", $id, $class);
                     //$table->bind_param("s", $id);
							$table->bind_result($test_list, $status, $date_begin, $date_end, $date_taken);
							$table->execute();
							while($table->fetch())
							{
								echo '<tr><td>'.$test_list.'</td>
									   <td>'.$status.'</td>
									   <td>'.$date_begin.' - '.$date_end.'</td>';
										if($date_taken != null)
										{
											echo '<td><button type="button" class="btn btn-primary">View Test</button></td>';
										}
										else if($currentTime >= $date_begin and $currentTime <= $date_end)
										{
											echo '<td><button type="button" class="btn btn-primary">Take Test</button></td>';
										}
										else
										{
											echo '<td><button type="button" class="btn btn-primary">Unavailable</button></td>';
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