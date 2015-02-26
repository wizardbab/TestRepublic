
<!-- 2/21 - Modals added by Victor Jereza -->
<!-- 2/23 - Added lots of JavaScript -->

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
    <link href="css/teacher-create-test.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	
</head>
<?php
session_start();

// Include the constants used for the db connection
require("constants.php");

// Gets the class id appended to url from teacherMainPage.php
$id = $_SESSION['username']; // Just a random variable gotten from the URL
$classId = $_SESSION['classId'];

if($id == null)
    header('Location: login.html');
    
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

// Teacher first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from teacher where teacher_id = ?";

// Class ID and description at the top of the page
$mainClassQuery = "select class_id, class_description from class where class_id = ?";

$mainClassStatement = $database->prepare($mainClassQuery);
?>
<body>
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
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i></i><?php // Added by David Hughen
																												// to display student's name in top right corner
																								if ($topRightStatement = $database->prepare($topRightQuery)) 
                                                                                                {
                                                                                                    $topRightStatement->bind_param("s", $id);
                                                                        
                                                                                                }
                                                                                                else {
                                                                                                    printf("Errormessage: %s\n", $database->error);
                                                                                                }							
                                                                                                $topRightStatement->bind_result($first_name, $last_name);
                                                                                                $topRightStatement->execute();
                                                                                                while($topRightStatement->fetch())
                                                                                                {
                                                                                                    echo $first_name . " " . $last_name;
                                                                                                }
                                                                                                $topRightStatement->close();?><b class="caret"></b></a>
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
                            <a href="#"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->

            <!-- /.navbar-collapse -->
        </nav>
	</div>	
	
        <!-- /#sidebar-wrapper -->
        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
                <div class="row">
					<div class="col-lg-12" id="course_section">
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
					</div>
				</div>
				
				
				<div class="row" id="test_section">
				
					<div class="col-lg-4" id="test_information">
				
						<div class="test-info-text">
							Test Information
						</div>
					
						<label class="blocklabel">Test Name:
							<input type="text" placeholder="Test #1" />
						</label>
						
						<label class="date_lbl">Start Date:
							<input type="date" />
						</label>
						
						<label class="time_lbl">Time:
							<input type="time" />
						</label>
						
						<label class="date_lbl">End Date:&nbsp;
							<input type="date" />
						</label>
						
						<label class="time_lbl">Time:
							<input type="time" />
						</label>
						
						<label class="time_limit_lbl">Time Limit:
							<input type="number" /> minutes
						</label>
						
						<br />
						
						<label class="instruction_lbl">Specific Instruction:</label>
						<br />
						<textarea class="form-control" rows="6">Don't cheat!</textarea>
						
						
						<label class="pledge_lbl">Test Pledge:</label>

						<textarea class="form-control" rows="6"></textarea>
						
						<button type="button" class="btn btn-danger btn-block" id="cancelTestBtn">Cancel</button>
						<button type="button" class="btn btn-primary btn-block" id="testCreate">Create Test</button>
					</div>
					
					<div class="col-lg-8" id="create_questions">
						<div class="create-questions-text">
							Create Questions
						</div>
					
						<div id="button_sections">
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#MCModal" data-title="MultipleChoice">
								<span class="glyphicon glyphicon-record"></span> Multiple Choice
							</button>

							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#TFModal" data-title="TrueFalse">
								<span class="glyphicon glyphicon-ok"></span> True/False 
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#ATAModal" data-title="AllThatApply">
								<span class="glyphicon glyphicon-check"></span> All that Apply
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#MatchModal" data-title="Matching" >
								<span class="glyphicon glyphicon-th-large"></span> Matching
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#SAModal" data-title="ShortAnswer">
								<span class="glyphicon glyphicon-minus"></span> Short Answer
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#EssayModal" data-title="Essay">
								<span class="glyphicon glyphicon-pencil"></span> Essay
							</button>
						</div>
						
						<div class="container-fluid">
							<div class="list-group" id ="testList">
							</div>
						</div>
					</div>		
                </div>
				
				<!-- Short Answer Modal -->
					<div id="SAModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title">Short Answer</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="Question">
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal"id="SABtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- Essay Modal -->
					<div id="EssayModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title">Essay</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="Question">
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal"id="EBtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- T/F Modal-->
					<div id="TFModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title">True/False</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										
										<div class="form-group">
											<div class="radio">
												<label><input type="radio" name="optradio">True</label>
											</div>
											<div class="radio">
												<label><input type="radio" name="optradio">False</label>
											</div>
										</div>
										
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
											<button type="button" class="btn btn-primary" data-dismiss="modal" id="TFBtn">Create Test</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
				<!-- Multiple Choice Modal-->
					<div id="MCModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title">Multiple Choice</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										<div class="form-group">
											<label for="recipient-name" class="control-label">Answer:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										<div class="form-group" id="MC_AddAns">
											<label for="message-text" class="control-label">Additional answers: </label>
											<input type="text" class="form-control" id="Question">
										</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_MC">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal"id="MCBtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- All that Apply Modal-->
					<div id="ATAModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title">All that Apply</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										<div class="form-group">
											<label for="recipient-name" class="control-label">Answer:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										<div class="form-group" id="ATA_AddAns">
											<label for="message-text" class="control-label">Additional answers: </label>
											<input type="text" class="form-control" id="Question">
										</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_ATA">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal"id="ATABtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- Matching Modal-->
					<div id="MatchModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title">Matching</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										<div class="form-group">
											<label for="recipient-name" class="control-label">Answer:</label>
											<input type="text" class="form-control" id="Question">
										</div>
										<div class="form-group" id="MatchAddAns">
											<label for="message-text" class="control-label">Additional answers: </label>
											<input type="text" class="form-control" id="Question">
										</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_match">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal"id="MBtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>				
			</div>    				
				
				
					
					
					
					
					  

	
				</div>   
				
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

	<!-- Custom Test Creation JavaScript --> 
	<script src="js/testCreation.js"></script>
	
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
	
	<!-- Add matching JS -->
	<script>
		$(document).ready(function(){
				$("#add_match").click(function(){
			$("#MatchAddAns").append('<input type="text" class="form-control" id="Question">'
			);
		});
	});
	</script>
	
		<!-- All that Apply JS -->
	<script>
		$(document).ready(function(){
				$("#add_ATA").click(function(){
			$("#ATA_AddAns").append('<input type="text" class="form-control" id="Question">'
			);
		});
	});
	</script>
	
		<!-- Multiple Choice JS -->
	<script>
		$(document).ready(function(){
				$("#add_MC").click(function(){
			$("#MC_AddAns").append('<input type="text" class="form-control" id="Question"> <button type="button" class="btn btn-default" aria-hidden="true" id="add_ATA">remove item</button>'
			);
		});
	});
	</script>
	
	<script>	
		var counter = 0;
		
			$(document).ready(function(){
				$("#SABtn").click(function(){
			$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">Short Answer</h4> <p class="list-group-item-text">List Group Item Text</p></a>'
			);
			counter++;
		});
		
					$("#MBtn").click(function(){
			$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">Matching</h4> <p class="list-group-item-text">List Group Item Text</p></a>'
			);
			counter++;
		});
		
					$("#MCBtn").click(function(){
			$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">Multiple Choice</h4> <p class="list-group-item-text">List Group Item Text</p></a>'
			);
			counter++;
		});
		
					$("#ATABtn").click(function(){
			$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">All That Apply</h4> <p class="list-group-item-text">List Group Item Text</p></a>'
			);
			counter++;
		});
		
					$("#TFBtn").click(function(){
			$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">True/False</h4> <p class="list-group-item-text">List Group Item Text</p></a>'
			);
			counter++;
		});
		
					$("#EBtn").click(function(){
			$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">Essay</h4> <p class="list-group-item-text">List Group Item Text</p></a>'
			);
			counter++;
		});
	});
	</script>

</body>

</html>
