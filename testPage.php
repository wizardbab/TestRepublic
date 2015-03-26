<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Initial Creation by Victor Jereza *woot woot* -->
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Republic</title>

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
    <link href="css/TestPage.css" rel="stylesheet">
	
	
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
								 
$headerQuery = "SELECT class_id, test_name from test where test_id = ?";
								 

$queryStatement = $database->prepare($query);
$headerStatement = $database->prepare($headerQuery);
//require("Nav.php");

@$classId = $_POST['classId'];
@$testId = $_POST['testId'];
@$testName = $_POST['testName'];

$_SESSION['classId'] = $classId;
$_SESSION['testId'] = $testId;


?>
	
</head>

<body class="container-fluid">

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
						<span class="TestRepublic" id="backToClass">Test Republic</span>
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
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
	</div>

<?php
	
				/*$queryStatement->bind_param("s", $classId);
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
						
						print_r($essayArray);
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
        <div class="row increase_margin_top">
            <div class="col-lg-12">
                <h1 class="page-header">
					 <?php
						// Code to display class id and test name
					   $headerStatement->bind_param("s", $testId);
						$headerStatement->bind_result($clid, $tname);
						$headerStatement->execute();
						while($headerStatement->fetch())
						{
							echo $clid . '<small>' . $tname . '</small>';
							
						}
						$headerStatement->close();
						  ?>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group" id="accordion">
                    
					<!-- Multiple Choice /.panel -->
					<div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Multiple Choice Questions</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body">
                                <h4>1.<span class="mc_questions">1 + 1 = ?</span></h4>
									<div class="mc_answers">
										<div class="mc_choice">
											<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio" />
											<span class="mc_answer_lbl">11</span>
										</div>
										<div class="mc_choice">
											<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio" />
											<span class="mc_answer_lbl">2</span>
										</div>
										<div class="mc_choice">
											<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio" />
											<span class="mc_answer_lbl">14</span>
										</div>
										<div class="mc_choice">
											<input type="radio" name="mc_answer1" id="mc_answer1" value="multipleRadio1" class="multipleRadio" />
											<span class="mc_answer_lbl">87</span>
										</div>
									</div>
							</div>
							<div class="panel-body">
                                <h4>2.<span class="mc_questions">How do you say "Hello" in Indonesian?</span></h4>
									<div class="mc_answers">
										<div class="mc_choice">
											<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio" />
											<span class="mc_answer_lbl">Yo</span>
										</div>
										<div class="mc_choice">
											<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio" />
											<span class="mc_answer_lbl">Hello</span>
										</div>
										<div class="mc_choice">
											<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio" />
											<span class="mc_answer_lbl">Halo</span>
										</div>
										<div class="mc_choice">
											<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio" />
											<span class="mc_answer_lbl">Aloha</span>
										</div>
									</div>
							</div>
                        </div>
                    </div>
                    
					<!-- Matching /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Matching Questions</a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <div class="panel-body container">
                                <h4>Identify the following types as a dog or a cat:</h4>
								<div class="col-md-6">
									<div class="matching_div">
										10.<span class="matching_questions">Pomeranian</span>
										<input type="text" class="matching_answer_tb" />
									</div>
									<div class="matching_div">
										11.<span class="matching_questions">Persian</span>
										<input type="text" class="matching_answer_tb" />
									</div>
									<div class="matching_div">
										12.<span class="matching_questions">Labrador</span>	
										<input type="text" class="matching_answer_tb" />
									</div>
									<div class="matching_div">
										13.<span class="matching_questions">Siberian</span>
										<input type="text" class="matching_answer_tb" />
									</div>	
									<div class="matching_div">
										14.<span class="matching_questions">Husky</span>
										<input type="text" class="matching_answer_tb" />
									</div>			
								</div>
								<div class="col-md-6">
									<div class="matching_div">
										a.<span class="matching_questions">Dog</span>
									</div>
									<div class="matching_div">
										b.<span class="matching_questions">Cat</span>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    
					<!-- All that Apply /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">All That Apply Questions</a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse">
                            <div class="panel-body">
                                <h4>20.<span class="ata_questions">Choose all the colors that are in Power Rangers.</span></h4>
								<div class="ata_answers">
									<div class="ata_choice">
										<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb" />
										<span class="ata_answer_lbl">Pink</span>
									</div>
									<div class="ata_choice">
										<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb" />
										<span class="ata_answer_lbl">Red</span>
									</div>
									<div class="ata_choice">
										<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb" />
										<span class="ata_answer_lbl">Purple</span>
									</div>
									<div class="ata_choice">
										<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb" />
										<span class="ata_answer_lbl">Yellow</span>
									</div>
									<div class="ata_choice">
										<input type="checkbox" name="ata_answer1" id="ata_answer_cb1" class="ata_cb" />
										<span class="ata_answer_lbl">Blue</span>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    
					<!-- True/False /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">True/False Questions</a>
                            </h4>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="panel-body">
                                <h4>30.<span class="tf_questions">David is ugly.</span></h4>
								<div class="tf_answers">
									<div class="tf_choice">
										<input type="radio" name="tf_answer1" id="tf_answer1" value="multipleRadio1" class="multipleRadio">
										<span class="mc_answer_lbl">True</span>
									</div>
									<div class="tf_choice">
										<input type="radio" name="mc_answer2" id="mc_answer2" value="multipleRadio2" class="multipleRadio" />
										<span class="mc_answer_lbl">False</span>
									</div>
								</div>
							</div>
							<div class="panel-body">
								Etc.
							</div>
                        </div>
                    </div>
                    
					<!-- Short Answer /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">Short Answer</a>
                            </h4>
                        </div>
                        <div id="collapseFive" class="panel-collapse collapse">
							<div class="panel-body">
								<h4>100.<span class="sa_questions">What kind of music that are allowed in PCC?</span></h4>
								<div class="sa_answers">
									<input type="text" class="m_answer_letter form-control" id="ShortAnswer2" />
								</div>
							</div>
							<div class="panel-body">
								<h4>101.<span class="sa_questions">There are ___ dalmantions in Dalmantions 101.</span></h4>
								<div class="sa_answers">
									<input type="text" class="m_answer_letter form-control" id="ShortAnswer3" />
								</div>
							</div>
                        </div>
                    </div>
                    
					<!-- Essay /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">Essay Questions</a>
                            </h4>
                        </div>
                        <div id="collapseSix" class="panel-collapse collapse">
                            <div class="panel-body">
								<h4>120.<span class="essay_questions">Explain why CIS students need to take Systems Design class.</span></h4>
								<div class="essay_answers">
									<textarea class="form-control" id="EssayQuestion1" name="specificInstruction" rows="6"> </textarea>
								</div>
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
			<button type="button" class="btn btn-success btn-block" id="Submit">Submit</button>
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
