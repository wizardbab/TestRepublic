

<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Initial Creation by Victor Jereza -->
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Page Template</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/TestPage.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	 
	 <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	<!-- Custom TestTaking JavaScript -->
	<script src="js/testTaking.js"></script> 
	 
</head>

<body class="container-fluid">

<?php
session_start();

// Include the constants used for the db connection
require("constants.php");

$id = $_SESSION['username'];

if($id == null)
    header('Location: login.html');
 
 // The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Class id and description query
$query = "select class_id, class_description from class where class_id = ?";

$summaryQuery = "select question_no, question_type, question_value, question_text, heading, heading_id, question_letter
								 from question
								 where test_id = ?";
								 

$queryStatement = $database->prepare($query);
//require("Nav.php");

@$classId = $_POST['classId'];
@$testId = $_POST['testId'];
@$testName = $_POST['testName'];

?>
<?php
	
			/*	$queryStatement->bind_param("s", $classId);
				$queryStatement->bind_result($clid, $clde);
				$queryStatement->execute();
				while($queryStatement->fetch())
				{
					
					echo '<div class="row course_header">
								<div class="course_number col-lg-12">
									'.$clid.'
								</div><div class="class_name">
									'.$clde.' ' .$testName.'
								</div>
							</div>';
				}
				$queryStatement->close(); */
				$questionArray = array(array("number" => 0, "type" => 0, "value" => 0, "text" => 0, "heading" => 0, "id" => 0, "letter" => 0));
			
				$summaryStatement = $database->prepare($summaryQuery);
				$summaryStatement->bind_param("s", $testId);
				$summaryStatement->bind_result($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter);
				$summaryStatement->execute();
				$i = 0;
				while($summaryStatement->fetch())
				{
					
					//$questionArray = array("number" => $qno, "type" => $qtype, "value" => $qvalue, "text" => $qtext, "heading" => $heading, "id" => $hid, "letter" => $qletter);
					array_push($questionArray, array($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter));
					
					
				}
				$summaryStatement->close();
			foreach($questionArray as $question)
				{
					foreach($question as $key => $value)
					{
						echo $value ." ";
					} 
					echo '<br />';
				} 
				?>	
	
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
				<li class="sidebar-brand">
                    Question Types:
                </li>
				<li>
					<div class="radio">
						<label><input type="radio" name="optradio">Essay</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label><input type="radio" name="optradio">Fill in the Blank</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label><input type="radio" name="optradio">T/F</label>
					</div>
				</li>
				<li>
					<div class="radio disabled">
						<label><input type="radio" name="optradio" disabled>Multiple Choice</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label><input type="radio" name="optradio">Matching</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label><input type="radio" name="optradio">Short Answer</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label><input type="radio" name="optradio">All that Apply</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label><input type="radio" id="summaryRadio" name="optradio">Summary</label>
					</div>
				</li>
            </ul>
        </div>
		  
	<script>
	$(document).ready(function()
	{
		$("#summaryRadio").click(function()
		{
			
		});
	});
	</script>
	
		
        <!-- /#sidebar-wrapper -->
		
		<div class="row" id="test_name">
			Test #1
		</div>
		
		<div class="row">
			<div class="col-lg-12" id="question_header">
				<span id="question_text">Question</span>
				<a href="#"><span class="glyphicon glyphicon-chevron-left"></span></a>
				<input type="text" name="question_number" id="question_number" value="1" />
				<a href="#"><span class="glyphicon glyphicon-chevron-right"></span></a>
			</div>
		</div>
		
		
        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
				<div class="row">
					<div class="col-lg-12" id="question_type_text">
						<!-- don't touch this -->
						Essay
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12" id="QuestionBox">
						<h3 id="question" onload="loadFirstQuestion()"> </h3>
						<div class="form-group">
						  <textarea class="form-control" rows="10" id="AnswerBox"></textarea>
						</div>	
					</div>
					<button type="button" class="btn btn-default" id="prevBtn"><span class="glyphicon glyphicon-chevron-left"></span>previous</button>
					<button type="button" class="btn btn-default" id="nxtBtn"><span class="glyphicon glyphicon-chevron-right"></span>next</button>
				</div>
				
				<!-- Essay template 
				<div class="row">
					<div class="col-lg-12">
						<h1> Essay Question: </h1>
						<h3> What is the meaning of life?</h3>
						<div class="form-group">
						  <textarea class="form-control" rows="10" id="comment"></textarea>
						</div>
					<ul class="pager">
						<li class="previous"> <a href="#"><span class="glyphicon glyphicon-chevron-left"></span> previous </a></li>
						<li class="next"> <a href="#"><span class="glyphicon glyphicon-chevron-right"></span> next </a></li>
					</ul>
					</div>
				</div> -->
				
				
				<!-- /Essay template -->
				
				<!-- Multiple Choice template 
				<div class="row">
					<div class="col-lg-12">
						<h1> Multiple Choice: </h1>
						<h3> Which is not true? </h3>
						<div class="form-group">
							<form role="form">
								<div class="checkbox">
								  <label><input type="checkbox" value="">true</label>
								</div>
								<div class="checkbox">
								  <label><input type="checkbox" value="">false</label>
								</div>
								<div class="checkbox">
								  <label><input type="checkbox" value="">true</label>
								</div>
							</form>
						</div>
					<ul class="pager">
						<li class="previous"> <a href="#"><span class="glyphicon glyphicon-chevron-left"></span> previous </a></li>
						<li class="next"> <a href="#"><span class="glyphicon glyphicon-chevron-right"></span> next </a></li>
					</ul>
					</div>
				</div>
				 /Multiple Choice Template -->
				
				<!-- Short Answer 
				<div class="row">
                    <div class="col-lg-12">
						<h1> Short Answer: </h1>
						<h3> I am feeling... </h3>
                    </div>
                </div>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
						  <textarea class="form-control" rows="1" id="comment"></textarea>
						</div>
					<ul class="pager">
						<li class="previous"> <a href="#"><span class="glyphicon glyphicon-chevron-left"></span> previous </a></li>
						<li class="next"> <a href="#"><span class="glyphicon glyphicon-chevron-right"></span> next </a></li>
					</ul>
					</div>
				</div> -->
				
				
				<!-- /short Answer -->
				
				<!-- T/F template 
				<div class="row">
					<div class="col-lg-12">
						<h1> True/False: </h1>
						<h3> The Mongolian Horde is a beast team. </h3>
						<div class="form-group">
							<form role="form">
								<div class="radio">
									<label><input type="radio" name="optradio">True</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="optradio">False</label>
								</div>
							</form>
						</div>
					<ul class="pager">
						<li class="previous"> <a href="#"><span class="glyphicon glyphicon-chevron-left"></span> previous </a></li>
						<li class="next"> <a href="#"><span class="glyphicon glyphicon-chevron-right"></span> next </a></li>
					</ul>
					</div>
				</div>
				 /T/F Template -->
				
				
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->
	

    
	
    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
	

</body>

</html>
