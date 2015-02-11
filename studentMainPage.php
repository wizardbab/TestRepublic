<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Simple Sidebar - Start Bootstrap Template</title>

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

// Class, etc, to display on studentMainPage
$tableQuery = "select class_id, c_update, update_date from student
join enrollment using (student_id)
join class using (class_id)
where student_id = ?";

// The @ is for ignoring PHP errors. Replace "database_down()" with whatever you want to happen when an error happens.
@ $database->select_db(DATABASENAME);

// The statement variable holds your query      
$stmt = $database->prepare($query);
$topRightStatement = $database->prepare($topRightQuery);
$table = $database->prepare($tableQuery);

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
				<a href="#menu-toggle" class="navbar-brand" id="menu-toggle"><img src="images/logo2.png" alt="Our Logo" height="30" width="30">Test Republic</a>
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
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php 
																											    $topRightStatement->bind_param("s", $id);
																												$topRightStatement->bind_result($first_name, $last_name);
																												$topRightStatement->execute();
																												while($topRightStatement->fetch())
																												{
																													echo $first_name . " " . $last_name;
																												}
																												$topRightStatement->close(); ?>
																												<b class="caret"></b></a>
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
                            <a href="login2.html"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
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
                    <a href="#">Summary</a>
                </li>
                <li class="sidebar-brand">
                        Select a class:
                </li>
                <?php $stmt->bind_param("s", $id);
				$stmt->bind_result($clid, $clde);
				$stmt->execute();
				while($stmt->fetch())
				{
					echo '<li><a href="#">'.$clid.'<br />'.$clde.'</a></li>';
				}
				$stmt->close();
				?>
                <li class="sidebar-brand">
                        Recent Updates:
                </li>
			<div class="recent_updates">
                <li>
                    2/5/15 - CS 130-2 Test # 2 was posted.
                </li>
                <li>
                    2/5/15 - HI 101-3 Midterm was graded.
                </li>
			</div>
			
            </ul>
        </div>
		
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="warning_box">
							<h2 style="color:#FF0000"> Warning(s): </h2>
							<p class="warning_msg"> 2/5/15 - EN 121-5 Midterm Exam will be expired in 1 day!</p>
						
						</div>
						<table>
							<tr>
								<th>Class</th>
								<th>Update</th>
								<th>Date</th>
							</tr>
							<?php $table->bind_param("s", $id);
							$table->bind_result($clid, $update, $date);
							$table->execute();
							while($table->fetch())
							{
								echo '<tr><td>'.$clid.'</td><td>'.$update.'</td><td>'.$date.'</td></tr>';
							}
							$table->close();
							?>
							<tr></tr>
						</table>
                        <p>This template has a responsive menu toggling system. The menu will appear collapsed on smaller screens, and will appear non-collapsed on larger screens. When toggled using the button below, the menu will appear/disappear. On small screens, the page content will be pushed off canvas.</p>
                        <p>Make sure to keep all page content within the 23232.</p>

							
                    </div>
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
