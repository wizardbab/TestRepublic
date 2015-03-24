

<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Initial Creation by Victor Jereza -->
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Republic</title>

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
//$classId = $_SESSION['classId'];
//$testId = $_SESSION['testId'];

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

$_SESSION['classId'] = $classId;
$_SESSION['testId'] = $testId;

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
				//$questionArray = array(array("number" => 0, "type" => 0, "value" => 0, "text" => 0, "heading" => 0, "id" => 0, "letter" => 0));
			
				$questionArray = array();
				$essayArray = array();
				$summaryStatement = $database->prepare($summaryQuery);
				$summaryStatement->bind_param("s", $testId);
				$summaryStatement->bind_result($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter);
				$summaryStatement->execute();
				
				$essayCounter = 0;
				$trueFalseCounter = 0;
				$multipleChoiceCounter = 0;
				$matchingCounter = 0;
				$shortAnswerCounter = 0;
				$ataCounter = 0;
				
				while($summaryStatement->fetch())
				{
					// Add individual question to our total list of questions
					array_push($questionArray, array($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter));
					
					/***************************************************************************************************/
               /* Essay type question crapola                                                                     */
               /***************************************************************************************************/
					if($qtype == "Essay")
					{
						$essayCounter++;
						array_push($essayArray, $qno, $qtype, $qvalue, $qtext);
						foreach($essayArray as $e)
							echo $e . ' ';
						//print_r($essayArray);
						echo '<br />';
						
					}		

					/***************************************************************************************************/
               /* True/False type question crapola                                                                */
               /***************************************************************************************************/
					else if($qtype == "True/False")
					{
						$trueFalseCounter++;
						
					}
					
					/***************************************************************************************************/
               /* Multiple Choice type question crapola                                                           */
               /***************************************************************************************************/
					else if($qtype == "Multiple Choice")
					{
						$multipleChoiceCounter++;
						
					}
					
					/***************************************************************************************************/
               /* Matching type question crapola                                                                  */
               /***************************************************************************************************/
					else if($qtype == "Matching")
					{
						$matchingCounter++; // Probably have to be incremented more that this xD
						
					}
					
					/***************************************************************************************************/
               /* Short Answer type question crapola                                                              */
               /***************************************************************************************************/
					else if($qtype == "Short Answer")
					{
						$shortAnswerCounter++;
					}
					
					/***************************************************************************************************/
               /* All That Apply type question crapola                                                            */
               /***************************************************************************************************/
					else
					{
						$ataCounter++;
						
					}
					
				}
				$summaryStatement->close();
		/*	foreach($questionArray as $question)
				{
					foreach($question as $key => $value)
					{
						echo $value ." ";
					} 
					echo '<br />';
				} */
				?>	
   
   
	 <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">CS 306
                    <small>Test 1</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Multiple Choice Questions</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body">
                                <h3>Question 1: <small>1+1=?</small><h3>
								<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio">11</input>
								<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio">2</input>
								<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio">14</input>
								<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio">87</input>
							</div>
							<div class="panel-body">
								<h3>Question 2: <small>Andrea is ...</small><h3>
								<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio">Indo</input>
								<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio">Viet</input>
								<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio">An Alien</input>
								<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio">Short</input>
							</div>
							<div class="panel-body">
								<h3>Question 3: <small>Cats are awesome</small><h3>
								<input type="radio" name="mc_answer3" id="mc_answer3" value="multipleRadio3" class="multipleRadio">True</input>
								<input type="radio" name="mc_answer3" id="mc_answer3" value="multipleRadio3" class="multipleRadio">rreally true</input>
								<input type="radio" name="mc_answer3" id="mc_answer3" value="multipleRadio3" class="multipleRadio">very true</input>
								<input type="radio" name="mc_answer3" id="mc_answer3" value="multipleRadio3" class="multipleRadio">MEGA TRUE</input>
							</div>
                        </div>
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Matching Choice Questions</a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <div class="panel-body container">
                                <h3>Question 1: <small>Math Problems!</small><h3>
								<div class="col-md-6">
									<input type="text" name="mc_answer1" id="mc_answer1"class="multipleRadio">1+1=</input>
									<input type="text" name="mc_answer1" id="mc_answer1"class="multipleRadio">2+2=</input>
								</div>
								<div class="col-md-6">
									<h1> a. 2</h1>
									<h1> b. 2</h1>
									<h1> c. EPIC</h1>
									<h1> d. 2</h1>
								</div>
							</div>
                        </div>
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">All That Apply Questions</a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse">
                            <div class="panel-body">
                                <h3>Question 1: <small>Jake is...</small><h3>
								<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb">Ugly</input>
								<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb">a DB God</input>
								<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb">Fat</input>
								<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb">Assistant Project manager</input>
							</div>
							<div class="panel-body">
								<h3>Question 2: <small>Blue is...</small><h3>
								<input type="checkbox" name="ata_answer2" id="ata_answer_cb2" class="ata_cb">Ugly</input>
								<input type="checkbox" name="ata_answer2" id="ata_answer_cb2" class="ata_cb">our color</input>
								<input type="checkbox" name="ata_answer2" id="ata_answer_cb2" class="ata_cb">meow</input>
								<input type="checkbox" name="ata_answer2" id="ata_answer_cb2" class="ata_cb">Ugly</input>
							</div>
							<div class="panel-body">
								<h3>Question 3: <small>Death comes to...</small><h3>
								<input type="checkbox" name="ata_answer3" id="ata_answer_cb3" class="ata_cb">Infidels</input>
								<input type="checkbox" name="ata_answer3" id="ata_answer_cb3" class="ata_cb">Ugly people</input>
								<input type="checkbox" name="ata_answer3" id="ata_answer_cb3" class="ata_cb">Pretty People</input>
								<input type="checkbox" name="ata_answer3" id="ata_answer_cb3" class="ata_cb">Sick people</input>
							</div>
                        </div>
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">True/False Questions</a>
                            </h4>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="panel-body">
                                <h3>Question 1: <small>David is ugly</small><h3>
								<input type="radio" name="tf_answer1" id="tf_answer1" value="multipleRadio1" class="multipleRadio">True</input>
								<input type="radio" name="tf_answer1" id="tf_answer1" value="multipleRadio1" class="multipleRadio">False</input>
							</div>
							<div class="panel-body">
								<h3>Question 2: <small>Dr. Howell is a boss.</small><h3>
								<input type="radio" name="tf_answer2" id="tf_answer2" value="multipleRadio2" class="multipleRadio">True</input>
								<input type="radio" name="tf_answer2" id="tf_answer2" value="multipleRadio2" class="multipleRadio">False</input>
							</div>
							<div class="panel-body">
								<h3>Question 3: <small>Cats are awesome</small><h3>
								<input type="radio" name="tf_answer3" id="tf_answer3" value="multipleRadio3" class="multipleRadio">True</input>
								<input type="radio" name="tf_answer3" id="tf_answer3" value="multipleRadio3" class="multipleRadio">False</input>
							</div>
                        </div>
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">Short Answer</a>
                            </h4>
                        </div>
                        <div id="collapseFive" class="panel-collapse collapse">
							<div class="panel-body">
								<h2>Question 1: <small>E=MC?</small><h2>
								<input type="text" class="m_answer_letter form-control" id="ShortAnswer1" />
							</div>
							<div class="panel-body">
								<h2>Question 2: <small>What is the meaning of life</small><h2>
								<input type="text" class="m_answer_letter form-control" id="ShortAnswer2" />
							</div>
							<div class="panel-body">
								<h2>Question 3: <small>Who is the main hero in Ranger's Apprentice?</small><h2>
								<input type="text" class="m_answer_letter form-control" id="ShortAnswer3" />
							</div>
                        </div>
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">Essay Questions</a>
                            </h4>
                        </div>
                        <div id="collapseSix" class="panel-collapse collapse">
                            <div class="panel-body">
								<h2>Question 1: <small>Why Should Victor get a raise?</small><h2>
								<textarea class="form-control" id="EssayQuestion1" name="specificInstruction" rows="6"> </textarea>
							</div>
							<div class="panel-body">
								<h2>Question 2: <small>Who is your teacher?</small><h2>
								<textarea class="form-control" id="EssayQuestion2" name="specificInstruction" rows="6"> </textarea>
						   </div>
							<div class="panel-body">
								<h2>Question 3: <small>Which team rocks your socks off?</small><h2>
								<textarea class="form-control" id="EssayQuestion3" name="specificInstruction" rows="6"> </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-group -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
		<div class="row">
			<h4 id="pledgeHeader"> Do you accept the terms of agreement? </h4>
		</div>
		
		<div class="row">
			<div class="col-md-3">
				<input type="text" class="form-control" id="Pledge">
			</div>
			
			<div class="col-md-9">	
				<button type="button" class="btn btn-primary btn-block" id="Submit">Submit</button>
			</div>
		</div>
		
		
		
   </div>
   <!-- /. Container -->
   
	

    
		<script>
	$(document).ready(function()
	{
		$("#summaryRadio").click(function()
		{
			alert("clicked summary");
		  
		});
		
		
		
		$("#essayRadio").click(function()
		{
			alert("clicked essay");
			
			var essayArray = [];
			var i;
			
			<?php foreach($essayArray as $e){ ?>
				essayArray.push('<?php echo $e; ?>');
				
			<?php } ?>
			
		for(i = 0; i < 2; i++)
			{
				alert(essayArray[i]);
			} 
			alert("after function");
		  
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
