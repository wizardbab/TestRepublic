<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Initial Creation by Victor Jereza *woot woot* -->
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" href="images/newlogo.ico">

    <title>Pledge Page</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	 
	 <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	<!-- Custom TestTaking JavaScript -->
	<script src="js/testTaking.js"></script> 
	
	<!-- Custom CSS -->
    <link href="css/pledgePage.css" rel="stylesheet">
	
	<!-- custom alerts -->
	<script src="dist/sweetalert.min.js"></script>
	<link rel="stylesheet" type="text/css" href="dist/sweetalert.css">
	
</head>
<?php
session_start();
$id = $_SESSION['username'];

if($id == null)
    header('Location: login.html');
 
 // Include the constants used for the db connection
require("constants.php");

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
 
$classId = $_SESSION['classId'];
$testId = $_SESSION['testId'];

@ $database->select_db(DATABASENAME);

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Display the class id and description at top of the screen
$mainClassQuery = "select class_id, class_description from class where class_id = ?";

// Display the test name at the top of the page
$testNameQuery = "select test_name, pledge from test where test_id = ?";

$pledgeSignedQuery = "update test_list set graded = 0 where test_id = ? and student_id = ?";

$topRightStatement = $database->prepare($topRightQuery);
$mainClassStatement = $database->prepare($mainClassQuery);
$testNameStatement = $database->prepare($testNameQuery);
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
						<img src="images/newlogo.png" alt="Our Logo" height="45" width="45">
						<span class="TestRepublic">TEST REPUBLIC</span>
					</div>
				</a>
			</div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i>
					<?php // Added by David Hughen
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
							echo $first_name . " " . $last_name . ", ". $id." ";
						}
						$topRightStatement->close();?><b class="caret"></b></a>
						
                </li>
            </ul>
        </nav>
	</div>		
	
   <?php
		$mainClassStatement->bind_param("s", $classId);
		$mainClassStatement->bind_result($clid, $clde);
		$mainClassStatement->execute();
		while($mainClassStatement->fetch())
		{
			echo
			'<div class="row course_header">
				<div class="course_number col-lg-12">
					'.$clid.'
				</div>
				
				<div class="class_name">
					'.$clde.'
				</div>
			</div>';
			
		}
		$mainClassStatement->close();

	?>		
   
	<div class="container-fluid main_section">
	
		<?php
			$testNameStatement->bind_param("s", $testId);
			$testNameStatement->bind_result($tname, $pledge);
			$testNameStatement->execute();
			while($testNameStatement->fetch())
			{
				echo
					'<div class="row test_title">
						'.$tname.'
					</div>';
			}
			
			$testNameStatement->close();
		?>
		
		<div class="row test_pledge_section">
			<span class="test_pledge_txt">Test Pledge</span>
			<textarea class="form-control pledge_tb" disabled name="specificInstruction" rows="8"><?php echo $pledge; ?></textarea>
		</div>
	
		<div class="row sign_name_txt">
            Sign your name below:
			<input type="text" id="nameBox" class="form-control sign_name_tb" />
		</div>
	
		<div class="row student_name">
		<?php
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
					$topRightStatement->close();
	
		?>
		</div>
	
		<div class="row increase_margin">
            <button type="button" id="submitPledge" class="btn btn-primary btn-block submit_btn">Submit</button>
		</div>
	
	</div>
	
	<div id="proceedModal" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header modal_header_color">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title"><span class="glyphicon glyphicon-minus"></span>Proceed?</h4>
						</div>
						<div class="modal-body">
							<form name="shortAnswerForm" id="shortAnswerForm" action="testCreationPage.php" method="post">
								<div class="form-group">
									<div class="point_value_section">
										<label for="short_answer_point_value" class="control-label">Do you wish to proceed?&nbsp;</label>
									</div>
									<hr />
									<div class="question_section">
									</div>
									<div class="form-group">
										<input type="submit" value="Yes" class="form-control" id="short_answer_question">
									</div>
									<div class="form-group">
										<input type="submit" value="No" class="form-control" id="short_answer_question">
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal" id="SACancelBtn">Cancel</button>
							<button type="submit" class="btn btn-primary " id="SABtn" name="create" value="create" >Create Question</button>
						</div>
					</div>
				</div>
			</div>
		
   
	
	<script>
	function proceedFunction()
	{
		var x;
		 
		 swal({
			title: "You didn't enter your name",
			text: "Proceed with a score of zero?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes",
			cancelButtonText: "No, I'll sign it",
			closeOnConfirm: false,
			closeOnCancel: false
		}, 
		function (isConfirm) 
		{
			if (isConfirm) 
			{
                swal("OK", "You have earned a 0", "warning");
                window.location = "studentClassPage.php?classId=" + '<?php echo str_replace(" ", "%20", $classId); ?>';
			} 
			else 
			{
				swal("Cancelled", "Please re-enter your signature.", "error");
			}
		});
	}
	
	$(document).ready(function()
	{
		var studentId = '<?php echo $id; ?>';
        var testId = '<?php echo $testId; ?>';
		
		$("#submitPledge").click(function()
		{
			if($("#nameBox").val() == "")
			{
				proceedFunction();
			}	
			else if($("#nameBox").val() != '<?php echo $first_name . " " . $last_name; ?>')
			{
				swal("Error!","Please re-enter your digital signature", "error");
			}
			else
			{
                $.post("TestButtonScripts/pledgeButton.php",
                {
                    studentId:studentId,
                    testId:testId
                },
                function(data)
                {
                    swal("Success","<?php echo $tname;?> Submitted!", "success");
                    window.location = "studentClassPage.php?classId=" + '<?php echo str_replace(" ", "%20", $classId); ?>';
                });
			}
		});
	});
	</script>
	
    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
	

</body>

</html>
