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


<?php require("Nav.php"); ?>
	
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
				$trueFalseArray = array();
				$multipleChoiceArray = array();
				$matchingArray = array();
				$shortAnswerArray = array();
				$ataArray = array();
				
				
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
               /* Essay type question                                                                             */
               /***************************************************************************************************/
					if($qtype == "Essay")
					{
						
						array_push($essayArray, $qno, $qtype, $qvalue, $qtext);
						
						print_r($essayArray);
						echo '<br />';
						
					}		

					/***************************************************************************************************/
               /* True/False type question                                                                        */
               /***************************************************************************************************/
					else if($qtype == "True/False")
					{
						array_push($trueFalseArray, $qno, $qtype, $qvalue, $qtext);
						
					}
					
					/***************************************************************************************************/
               /* Multiple Choice type question                                                                   */
               /***************************************************************************************************/
					else if($qtype == "Multiple Choice")
					{
						array_push($multipleChoiceArray, $qno, $qtype, $qvalue, $qtext);
						
					}
					
					/***************************************************************************************************/
               /* Matching type question                                                                          */
               /***************************************************************************************************/
					else if($qtype == "Matching")
					{
						array_push($matchingArray, $qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter);
						
					}
					
					/***************************************************************************************************/
               /* Short Answer type question crapola                                                              */
               /***************************************************************************************************/
					else if($qtype == "Short Answer")
					{
						array_push($shortAnswerArray, $qno, $qtype, $qvalue, $qtext);
					}
					
					/***************************************************************************************************/
               /* All That Apply type question                                                                    */
               /***************************************************************************************************/
					else
					{
						array_push($ataArray, $qno, $qtype, $qvalue, $qtext);
						
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
                    
                    <?php
						   /***************************************************************************************************/
							/* Test each question type's array for data; if there's data we add that tab to our page           */
							/***************************************************************************************************/
							// Essay stuff
							if(is_array($essayArray))
							{
								
								
								
								echo '<div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">Essay Questions</a>
                            </h4>
                        </div>
							  <div id="collapseSix" class="panel-collapse collapse">
									<div class="panel-body">';
									
									for($i = 0; $i < count($essayArray); $i+=4)
									{
										echo'<h4>'.$essayArray[$i].'<span class="essay_questions">'.$essayArray[$i+3].'</span></h4><h4>Point Value: '.$essayArray[$i+2].'</h4>
											<div class="essay_answers">
												<textarea class="form-control" id="EssayQuestion1" name="specificInstruction" rows="6"> </textarea></div>';
			
									}
								echo'		
									</div>
							  </div>
							      </div>';
							}
							
							// True/False stuff
							if(is_array($trueFalseArray))
							{
								// echo accordian
							}
							
							// Multiple Choice stuff
							if(is_array($multipleChoiceArray))
							{
								// echo accordian
							}
							
							// Matching stuff
							if(is_array($matchingArray))
							{
								// echo accordian
							}
							
							// Short Answer stuff
							if(is_array($shortAnswerArray))
							{
								echo'<div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">Short Answer</a>
                            </h4>
                        </div>
                        <div id="collapseFive" class="panel-collapse collapse">';
								for($i = 0; $i < count($shortAnswerArray); $i+=4)
								{
									echo'<div class="panel-body">
										<h4>'.$shortAnswerArray[$i].'<span class="sa_questions"></span>'.$shortAnswerArray[$i+3].'</h4><h4>Point Value: '.$shortAnswerArray[$i+2].'</h4>
										<div class="sa_answers">
											<input type="text" class="m_answer_letter form-control" id="ShortAnswer2" />
										</div>
									</div>';
								}
									
								echo'
									</div>
								</div>';
							}
							
							// All That Apply stuff
							if(is_array($ataArray))
							{
								// echo accordian
							}
						  ?>
					<!-- Essay /.panel -->
                    
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
