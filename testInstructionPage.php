

<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Initial Creation by Victor Jereza -->
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Instruction</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/testInstructionPage.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

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
						<img src="images/logo4.png" alt="Our Logo" height="45" width="45">
						<span class="TestRepublic">Back to CS 555-2</span>
					</div>
				</a>
			</div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i>John Smith<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
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
	
	<!-- Page Content -->
    
	
	<div class="row course_header">
		<div class="course_number col-lg-12">
			CS 555-2
		</div>
				
		<div class="class_name">
			Server/Client Relationship
		</div>
	</div>
	
	<div class="container-fluid main_section">
	
		<div class="row test_title">
			Test 1 - Network Introduction
		</div>
		
		<div class="row test_instruction_section">
			<span class="test_instruction_txt">Test Instruction</span>
			<textarea class="form-control instruction_tb" name="specificInstruction" rows="8">Read all the questions carefully. If you miss one question, do not dare to come to class.</textarea>
		</div>
	
		<div class="row time_limit">
            Time Limit: <span class="red_text">00:50</span>
		</div>
	
		<div class="row additional_instruction">
			You may not log out during the test.
			<br />After you finish, you will be asked to sign the pledge if you are able to do so.
			<br />Click start to begin.
		</div>
	
		<div class="row start_btn">
            <button type="button" class="btn btn-primary btn-block">Start</button>
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
	

</body>

</html>
