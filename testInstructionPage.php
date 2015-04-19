

<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Initial Creation by Victor Jereza -->
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" href="images/newlogo.ico">

    <title>Test Instruction</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/testInstructionPage.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<?php

session_start();


// Include the constants used for the db connection
require("constants.php");

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);

// Gets the class id appended to url from teacherMainPage.php
$id = $_SESSION['username']; // Just a random variable gotten from the URL

@$classId = $_POST['classId'];
@$testId = $_POST['testId'];
@$testName = $_POST['testName'];

$_SESSION['classId'] = $classId;
$_SESSION['testId'] = $testId;
$_SESSION['testName'] = $testName;

if($id == null)
    header('Location: login.html');
 
@ $database->select_db(DATABASENAME);

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Display the class id and description at top of the screen
$mainClassQuery = "select class_id, class_description from class where class_id = ?";

// Display the test name at the top of the page
$testNameQuery = "select test_name, instruction, time_limit from test where test_id = ?";

$topRightStatement = $database->prepare($topRightQuery);
$mainClassStatement = $database->prepare($mainClassQuery);
$testNameStatement = $database->prepare($testNameQuery);


?>


<body class="container-fluid">
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
						<img src="images/newlogo.png" alt="Our Logo" height="45" width="45">
						<span class="TestRepublic" id="backToClass">Back to <?php echo $classId; ?></span>
					</div> 
				</a>
			</div>
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
								echo $first_name . " " . $last_name .", ".$id;
							}
							$topRightStatement->close();?><b class="caret"></b></a>
						
                    <ul class="dropdown-menu">
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            

            <!-- /.navbar-collapse -->
        </nav>
	</div>	
	
	<!-- Page Content -->
    
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
			$testNameStatement->bind_result($tname, $instruction, $timeLimit);
			$testNameStatement->execute();
			while($testNameStatement->fetch())
			{
				echo
					'<div class="row test_title">
						'.$tname.'
					</div>';
				
				$_SESSION['timeLimit'] = $timeLimit;
			}
			$testNameStatement->close();
		?>
		
		<div class="row test_instruction_section">
			<span class="test_instruction_txt">Test Instructions</span>
			<textarea class="form-control instruction_tb" disabled name="specificInstruction" rows="8"> <?php echo $instruction; ?> </textarea>
		</div>
	
		<div class="row time_limit">
            Time Limit: <span class="red_text"> <?php echo $timeLimit; ?> </span>
		</div>
	
		<div class="row additional_instruction">
			You may not log out during the test.
			<br />After you finish, you will be asked to sign the pledge if you are able to do so.
			<br />Click start to begin.
		</div>
	
		<div class="row start_btn">
			<form action="testPage.php" method="post">
				<input type="hidden" value="<?php echo $classId; ?>" name="classId" id="classId"/>
				<input type="hidden" value="<?php echo $testId; ?>" name="testId" id="testId"/>
				<input type="hidden" value="<?php echo $testName; ?>" name="testName" id="testName"/>
				<input type="submit" id="startTest" value="Start" class="btn btn-primary btn-block start_btn"/>
			</form>
          
		</div>
	
	</div>
	
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	<!-- Custom TestTaking JavaScript -->
	<script src="js/testTaking.js"></script> 
	
    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
	 
	 <script>
	 $("#backToClass").click(function()
		{
            window.location = "studentClassPage.php?classId=" + '<?php echo str_replace(" ", "%20", $classId); ?>';
      });
		
	  $("#startTest").click(function()
		{
			var testId = '<?php echo $testId; ?>';
			var studentId = '<?php echo $id; ?>';
			
			$.post("TestButtonScripts/startButton.php",
			{
				 testId:testId,
				 studentId:studentId
			},
			function(data)
			{
					
			});
			alert(testId);
      });
		
		
	 </script>
	

</body>

</html>
