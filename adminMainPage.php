<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Republic - Admin</title>

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
//$classId = $_GET['classId'];

//$_SESSION['classId'] = $classId;
//$_SESSION['testId'] = null;

if($id == null)
    header('Location: login.html');
    
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

$adminId = "select admin_id from admin where admin_id = ?";

$mainTableQuery = "select class_id, class_description, teacher_id, first_name, last_name
						from class
						join teacher
						using(teacher_id)";
						
$teacherListQuery = "select teacher_id, first_name, last_name, teacher_password, email
						from teacher";
						
$mainTableStatement = $database->prepare($mainTableQuery);
$adminStatement = $database->prepare($adminId);
$teacherListStatement = $database->prepare($teacherListQuery);



?>

<div id="wrapper2">
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
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i>
				<b class="caret"></b></a>
						<?php
						$adminStatement->bind_param("s", $id);
						$adminStatement->bind_result($aid);
						$adminStatement->execute();
						while($adminStatement->fetch())
						{
							echo $aid;
						}
						$adminStatement->close();
						?>
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
        </nav>
	</div>	
	
    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li>
                    <a href="#" id="student-summary">Main Page</a>
                </li>
                <li class="sidebar-brand">
                    Admin Tools
                </li>
				<li>
					<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#TModal" data-title="MultipleChoice">
						<span class="glyphicon glyphicon-record"></span> Add Teacher
					</button>
				</li>
				<li>
					<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#CModal" data-title="MultipleChoice">
						<span class="glyphicon glyphicon-record"></span> Add Class
					</button>
				</li>
            </ul>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
				<div class="row">
				
				</div>
			</div>
			
			<div class="container-fluid align-center">
                <div class="row">
				

				</div>
				<div class="row">
					<div class="teacher_list_text">
						Teacher List
					</div>
				</div>
				<div class="row">
					<table class="test_list table-hover">
						<colgroup>
							<col class="test_name" />
							<col class="start_date" />
							<col class="ending_date" />
							<col class="test_average" />
							<col class="view_button_col" />
						</colgroup>
					
						<thead>
						<tr>
							<th>Test Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Average</th>
							<th>View Test</th>
						</tr>
						</thead>
						
						<tbody>
							<?php
								$teacherListStatement->bind_result($tid, $fname, $lname, $password, $email);
								$teacherListStatement->execute();

								while($teacherListStatement->fetch())
								{
								echo '<tr><td>'. $tid .'</td><td>'. $fname . '</td><td>' .$lname . '</td><td>' .$password. '</td><td>' . $email. '</td></tr>';
								
								}
								$teacherListStatement->close();
							?>

						</tbody>
					</table>
				</div>
				<div class="row">
					<div class="student_list_text">
						Class List
					</div>
				</div>
				<div class="row">
					<table class="test_list table-hover">
					<thead>
						<tr>
						<th>Class ID</th>
						<th>Class Desc.</th>
						<th>Teacher ID</th>
						<th>First Name</th>
						<th>Last Name</th>
						</tr>
					</thead>
					
					<tbody>
						<?php
							$mainTableStatement->bind_result($clid, $clde, $tid, $fname, $lname);
							$mainTableStatement->execute();
							while($mainTableStatement->fetch())
							{
							echo '<tr><td>' . $clid .'</td><td>'. $clde . '</td><td>' .$tid . '</td><td>' .$fname. '</td><td>' . $lname. '</td></tr>';
							
							}
							$mainTableStatement->close();
						?>
					
					
					</tbody>


						
					</table>
                </div>
            </div>
			
				<!--Teacher Creation Modal -->
				<div id="TModal" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal_header_color">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h4 class="modal-title">New Teacher</h4>
							</div>
							<div class="modal-body">
								<form name="TeacherForm" id="TeacherForm" method="post">
									<div class="form-group">
										<div class="point_value_section">
											<label for="short_answer_point_value" class="control-label">ID#:&nbsp;</label>
										</div>
										<hr />
										<div class="form-group">
											<label for="first_name" class="control-label">First Name:</label>
											<input type="text" class="form-control" id="short_answer_question">
										</div>
										<div class="form-group">
											<label for="last_name" class="control-label">Last Name:</label>
											<input type="text" class="form-control" id="short_answer_question">
										</div>
										<div class="form-group">
											<label for="email" class="control-label">Email:</label>
											<input type="text" class="form-control" id="short_answer_question">
										</div>
										<div class="form-group">
											<label for="email" class="control-label">Password:</label>
											<input type="text" class="form-control" id="short_answer_question">
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary " data-dismiss="modal" id="SABtn" name="create" value="create" >Create Teacher</button>
							</div>
						</div>
					</div>
				</div>
				
				<!--Class Creation Modal -->
				<div id="CModal" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header modal_header_color">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h4 class="modal-title">New Class</h4>
							</div>
							<div class="modal-body">
								<form name="shortAnswerForm" id="shortAnswerForm" action="testCreationPage.php" method="post">
										<div class="form-group">
											<label for="short_answer_question" class="control-label">Class Id:</label>
											<input type="text" class="form-control" id="short_answer_question">
										</div>
										<div class="form-group">
											<label for="short_answer_answer" class="control-label">Class Des:</label>
											<input type="text" class="form-control" id="short_answer_question">
										</div>
										<div class="form-group">
											<label for="short_answer_answer" class="control-label">Teacher ID:</label>
											<input type="number"  id="short_answer_question">
										</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary " data-dismiss="modal" id="CBtn" name="create" value="create" >Create Class</button>
							</div>
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
