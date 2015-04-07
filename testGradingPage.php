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

$summaryQuery = "select question_no, question_type, question_value, question_text, heading, heading_id, question_letter, question_id, answer_id, student_answer, student_selection, correct
								 from question
                                 join answer using(question_id)
								 where test_id = ?";
								 
$headerQuery = "SELECT class_id, test_name from test where test_id = ?";

$multipleChoiceQuery = "select answer_text, answer_id, student_selection, correct from answer where question_id = ?";
$ataQuery = "select answer_text, answer_id, student_selection, correct from answer where question_id = ?";

$matchingQuery = "SELECT question_letter, answer_text, answer_id, student_selection
from answer
join question using(question_id)
where heading_id = ?
order by(question_letter)";

$matchingCorrectQuery = "SELECT correct
from answer
join question using(question_id)
where heading_id = ?";

$matchingHeadQuery = "select distinct heading_id, heading from question where heading_id is not null and test_id = ?";

$trueFalseQuery = "select answer_id, answer_text, student_selection, correct from answer where question_id = ?";
								 
$matchingHeadStatement = $database->prepare($matchingHeadQuery);
$queryStatement = $database->prepare($query);
$headerStatement = $database->prepare($headerQuery);
$multipleChoiceStatement = $database->prepare($multipleChoiceQuery);
$ataStatement = $database->prepare($ataQuery);
$trueFalseStatement = $database->prepare($trueFalseQuery);
$matchingStatement = $database->prepare($matchingQuery);
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
				$summaryStatement->bind_result($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid, $studentAnswer, $studentSelection, $correct);
				
				
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
					array_push($questionArray, array($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid, $studentAnswer, $studentSelection));
					
					
					/***************************************************************************************************/
               /* Essay type question                                                                             */
               /***************************************************************************************************/
					if($qtype == "Essay")
					{
						
						array_push($essayArray, $qno, $qtype, $qvalue, $qtext, $qid, $studentAnswer);
						
					}		

					/***************************************************************************************************/
               /* True/False type question                                                                        */
               /***************************************************************************************************/
					else if($qtype == "True/False")
					{
						array_push($trueFalseArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid, $studentSelection);
						
					}
					
					/***************************************************************************************************/
               /* Multiple Choice type question                                                                   */
               /***************************************************************************************************/
					else if($qtype == "Multiple Choice")
					{
						array_push($multipleChoiceArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid, $studentSelection);
						
					}
					
					/***************************************************************************************************/
               /* Matching type question                                                                          */
               /***************************************************************************************************/
					else if($qtype == "Matching")
					{
						array_push($matchingArray, $qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid, $studentSelection, $correct);
						
					}
					
					/***************************************************************************************************/
               /* Short Answer type question crapola                                                              */
               /***************************************************************************************************/
					else if($qtype == "Short Answer")
					{
						array_push($shortAnswerArray, $qno, $qtype, $qvalue, $qtext, $qid, $studentAnswer);
					}
					
					/***************************************************************************************************/
               /* All That Apply type question                                                                    */
               /***************************************************************************************************/
					else
					{
						array_push($ataArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid, $studentSelection);	
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
					
                    
					<!-- Matching /.panel -->
				  
								  
								  
						
				  </div>
                    
					<!-- All that Apply /.panel -->
                    
                    
					<!-- True/False /.panel -->
                    
						  
						  
						  
                    
					<!-- Short Answer /.panel -->
                    
                    <?php
                        $essayCounter = 0;
                        $trueFalseCounter = 0;
                        $multipleChoiceCounter = 0;
                        $matchingCounter = 0;
                        $shortAnswerCounter = 0;
                        $allThatApplyCounter = 0;
						   /***************************************************************************************************/
							/* Test each question type's array for data; if there's data we add that tab to our page           */
							/***************************************************************************************************/
							// Essay stuff
							if(count($essayArray) > 0)
							{
								
								echo '<div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">Essay Questions</a>
                            </h4>
                        </div>
							  <div id="collapseSix" class="panel-collapse collapse">
									<div class="panel-body">';
									
									for($i = 0; $i < count($essayArray); $i+=6)
									{
										echo'<h4>'.$essayArray[$i].'<span class="essay_questions">'.$essayArray[$i+3].'</span></h4><h4>Point Value: '.$essayArray[$i+2].'</h4>
											<div class="essay_answers">
												<textarea class="form-control" id="EssayQuestion'.$essayArray[$i+4].'" name="specificInstruction" rows="6">'.$essayArray[$i+5].'</textarea></div>';
                                        $essayCounter++;
									}
								echo'		
									</div>
							  </div>
							      </div>';
							}
                            
							// True/False stuff
							if(count($trueFalseArray) > 0)
							{
                                $oldQuestion = 0;
								echo'<div class="panel panel-default">
									<div class="panel-heading" id="panel-color">
										 <h4 class="panel-title">
											  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">True/False Questions</a>
										 </h4>
									</div>
									<div id="collapseFour" class="panel-collapse collapse">';
									for($i = 0; $i < count($trueFalseArray); $i+=7)
									{
                                    if($oldQuestion != $trueFalseArray[$i])
                                        {
                                        $oldQuestion = $trueFalseArray[$i];
                                            echo'<div class="panel-body">
                                                  <h4>'.$trueFalseArray[$i].'<span class="tf_questions">'.$trueFalseArray[$i+3].'</span></h4><h4>Point Value: '.$trueFalseArray[$i+2].'</h4>
                                                    <div class="tf_answers" id="trueFalse'.$trueFalseCounter.'">';
                                                    $trueFalseStatement->bind_param("s", $trueFalseArray[$i+4]);
													$trueFalseStatement->bind_result($answer_id, $answer_text, $stuSelection, $correct);
													$trueFalseStatement->execute();
													while($trueFalseStatement->fetch())
													{
                                                        $checked = ($stuSelection == 1)?'checked':'';
                                                        echo'<div class="tf_choice">
                                                            <input type="radio" name="tf_answer1'.$trueFalseCounter.'" id="tf_answer'.$answer_id.'" ' .$checked . ' value="multipleRadio1" class="multipleRadio">
                                                            <span class="mc_answer_lbl">'.$answer_text.'</span>';
                                                        if($correct == 1)
                                                        {
                                                            echo "   --   Correct Answer";
                                                        }
                                                        echo'</div>';
                                                        
                                                    }
                                                   echo' </div>
                                            </div>';
                                            $trueFalseCounter++;
                                        }
									}
									$trueFalseStatement->close();
							echo '</div>
							  </div>';
							}
							
							// Multiple Choice stuff
							if(count($multipleChoiceArray) > 0)
							{
                                $oldQuestion = 0;
								echo'
								<div class="panel panel-default">
									<div class="panel-heading" id="panel-color">
										<h4 class="panel-title">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Multiple Choice Questions</a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse">';
									for($i = 0; $i < count($multipleChoiceArray); $i+=7)
									{	
                                        if($oldQuestion != $multipleChoiceArray[$i+4])
                                        {
                                            echo'	<div class="panel-body" >
												<h4>'.$multipleChoiceArray[$i].'<span class="mc_questions">'.$multipleChoiceArray[$i+3].'</span></h4><h4>Point Value: '.$multipleChoiceArray[$i+2].'</h4>
													<div class="mc_answers" >';
                                                    $oldQuestion = $multipleChoiceArray[$i+4];
													$multipleChoiceStatement->bind_param("s", $multipleChoiceArray[$i+4]);
													$multipleChoiceStatement->bind_result($atext, $mcAnswerId, $stuSelection, $correct);
													$multipleChoiceStatement->execute();
													while($multipleChoiceStatement->fetch())
													{
                                                        $checked = ($stuSelection == 1)?'checked':'';
														echo '<div class="mc_choice" >
															<input type="radio" name="mc_answer1'.$multipleChoiceCounter.'" id="mc_answer'.$mcAnswerId.'" value="multipleRadio1" class="multipleRadio" '.$checked.'/>
															<span class="mc_answer_lbl">'.$atext.'</span>';
                                                            if($correct == 1)
                                                            {
                                                                echo "   --   Correct Answer";
                                                            }
                                                        echo '</div>';
													}	
											echo'	</div>
											</div>';
                                            $multipleChoiceCounter++;
                                        }
									}
									$multipleChoiceStatement->close();
								echo'
									</div>
								</div>';
							}
							
							// Matching stuff
                            $headingCounter = 4;
							if(count($matchingArray) > 0)
							{
								echo'
								<div class="panel panel-default">
									<div class="panel-heading" id="panel-color">
										 <h4 class="panel-title">
											  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Matching Questions</a>
										 </h4>
									</div>
									<div id="collapseTwo" class="panel-collapse collapse">
										 <div class="panel-body container">';
                                        $matchingHeadStatement->bind_param("s", $testId);
										$matchingHeadStatement->bind_result($hid, $heading);
										$matchingHeadStatement->execute();
										while($matchingHeadStatement->fetch())
                                        {
                                            // This is full of crap
                                            $headingArray[] = $heading;
                                            $headingIdArray[] = $hid;
                                        }
                                        $matchingHeadStatement->close();
                                        $j = 0;
                                        for($k = 0; $k < count($headingArray); $k++)
                                        {
                                            echo'<h4>'.$headingArray[$k].'</h4>';
                                            
                                            $matchingStatement->bind_param("s", $headingIdArray[$k]);
                                            $matchingStatement->bind_result($qletter, $atext, $aid, $stuSelection);
                                            $matchingStatement->execute();
                                            while($matchingStatement->fetch())
                                            {
                                                $matchingAnswer[] = $qletter;
                                                $matchingAnswer[] = $atext;
                                                $matchingAnswer[] = $stuSelection;
                                            }
                                            
                                            echo count($matchingAnswer);
                                            for($i = 0; $i < count($matchingAnswer); $i+=3)
                                            {	
                                            echo'	<div class="col-md-6">
                                                    <div class="matching_div">'
                                                    .$matchingArray[$j].'<span class="matching_questions">'.$matchingArray[$j+3].'</span>
                                                        <input type="text" class="matching_answer_tb" value="'.$matchingArray[$j+9].'" id="matching'.$matchingArray[$j+8].'"/>';
                                                    echo '__ Correct -- '. $matchingArray[$j+10];
                                                    echo'</div>';
                                                echo'</div>';
                                                
                                                echo'<div class="col-md-6">';
                                                        echo'<div class="matching_div">
                                                            '.$matchingAnswer[$i].'.<span class="matching_questions">'.$matchingAnswer[$i+1].'</span>
                                                        </div>
                                                        <br />';
                                                        
                                                $matchingCounter++;	
                                                $j+=11;
                                            echo'</div>';
                                            }
                                            $matchingAnswer = null;
                                        }       
                                        $matchingStatement->close();
								echo'
									</div>
						</div>  </div>';
                                $headingCounter += 11;
							}
							
							// Short Answer stuff
							if(count($shortAnswerArray))
							{
								echo'<div class="panel panel-default">
                        <div class="panel-heading" id="panel-color">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">Short Answer</a>
                            </h4>
                        </div>
                        <div id="collapseFive" class="panel-collapse collapse">';
								for($i = 0; $i < count($shortAnswerArray); $i+=6)
								{
									echo'<div class="panel-body">
										<h4>'.$shortAnswerArray[$i].'<span class="sa_questions"></span>'.$shortAnswerArray[$i+3].'</h4><h4>Point Value: '.$shortAnswerArray[$i+2].'</h4>
										<div class="sa_answers">
											<input type="text" class="m_answer_letter form-control" id="ShortAnswer'.$shortAnswerArray[$i+4].'" value="'.$shortAnswerArray[$i+5].'" />
										</div>
									</div>';
                                    $shortAnswerCounter++;
								}
									
								echo'
									</div>
								</div>';
							}
							
							// All That Apply stuff
							if(count($ataArray) > 0)
							{
                                $oldQuestion = 0;
								echo'
								<div class="panel panel-default">
									<div class="panel-heading" id="panel-color">
										 <h4 class="panel-title">
											  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">All That Apply Questions</a>
										 </h4>
									</div>
									<div id="collapseThree" class="panel-collapse collapse">';
									for($i = 0; $i < count($ataArray); $i+=7)
									{
                                        if($oldQuestion != $ataArray[$i+4])
                                        {
                                            $oldQuestion = $ataArray[$i+4];
                                            $ataStatement->bind_param("s", $ataArray[$i+4]);
                                            $ataStatement->bind_result($atext, $aid, $stuSelection, $correct);
                                            $ataStatement->execute();
                                             echo'<div class="panel-body">
                                                  <h4>'.$ataArray[$i].'<span class="ata_questions">'.$ataArray[$i+3].'</span></h4><h4>Point Value: '.$ataArray[$i+2].'</h4>
                                                  <div class="ata_answers">';
                                                        while($ataStatement->fetch())
                                                        {
                                                        $checked = ($stuSelection == 1)?'checked':'';
                                                        echo'
                                                            <div class="ata_choice">
                                                                <input type="checkbox" name="ata_answer1" id="ata_answer_cb'.$aid.'" class="ata_cb" '.$checked.'/>
                                                                <span class="ata_answer_lbl">'.$atext.'</span>';
                                                            if($correct == 1)
                                                            {
                                                                echo "   --   Correct Answer";
                                                            }
                                                            echo '</div>';
                                                        }
                                            
                                            echo'</div>';
                                            echo'</div>';
                                        }
									}
									$ataStatement->close();
									
								echo'</div>  </div>';
							}
						  ?>
					<!-- Essay /.panel -->
                    
              
                <!-- /.panel-group -->
          
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
		<div class="row">
			<button type="button" class="btn btn-success btn-block" id="Submit">Submit</button>
			<button type="button" class="btn btn-success btn-block" id="Save">Save</button>
		</div>
				
   </div>
   <!-- /. Container -->
	
    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
	

</body>

</html>