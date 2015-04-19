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
	
	<!-- custom alerts -->
	<script src="dist/sweetalert.min.js"></script>
	<link rel="stylesheet" type="text/css" href="dist/sweetalert.css">
	
	
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
$topRightQuery = "select first_name, last_name from teacher where teacher_id = ?";

// Class id and description query
$query = "select class_id, class_description from class where class_id = ?";

$summaryQuery = "select question_no, question_type, question_value, question_text, heading, heading_id, question_letter, question_id, answer_id, student_answer, student_selection, correct, points_earned
								 from question
                                 left join answer using(question_id)
								 where test_id = ? and student_id = ?";
								 
$headerQuery = "SELECT class_id, test_name from test where test_id = ?";

$multipleChoiceQuery = "select answer_text, answer_id, student_selection, correct from answer where question_id = ?";
$ataQuery = "select answer_text, answer_id, student_selection, correct from answer where question_id = ?";

$matchingQuery = "SELECT correct, answer_text, question_id, student_selection, a_heading_id
from answer
where a_heading_id = ?
group by(correct)";

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
@$studentId = $_POST['studentId'];

$_SESSION['classId'] = $classId;
$_SESSION['testId'] = $testId;
?>
	
</head>

<body class="container-fluid">


<?php //require("Nav.php"); ?>

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
                  <span class="TestRepublic" id="backToClass">Back to <?php echo $classId; ?></span>
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
												echo $first_name . " " . $last_name . ", ". $id;
											}
											$topRightStatement->close();?><b class="caret"></b></a>
						
                    <ul class="dropdown-menu">
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
	</div>	
	
<?php

	
				$queryStatement->bind_param("s", $classId);
				$queryStatement->bind_result($clid, $clde);
				$queryStatement->execute();
				while($queryStatement->fetch())
				{
					
					echo '<div class="row course_header">
								<div class="course_number col-lg-12">
									'.$clid.'
								</div><div class="class_name">
									'.$clde.'
								</div>
							</div>';
				}
				$queryStatement->close(); 
				//$questionArray = array(array("number" => 0, "type" => 0, "value" => 0, "text" => 0, "heading" => 0, "id" => 0, "letter" => 0));
			
				$questionArray = array();
				$essayArray = array();
				$trueFalseArray = array();
				$multipleChoiceArray = array();
				$matchingArray = array();
				$shortAnswerArray = array();
				$ataArray = array();
				
				
				$summaryStatement = $database->prepare($summaryQuery);
				$summaryStatement->bind_param("ss", $testId, $studentId);
				$summaryStatement->bind_result($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid, $studentAnswer, $studentSelection, $correct, $pointsEarned);
				
				
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
					array_push($questionArray, array($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid, $studentAnswer, $studentSelection, $pointsEarned));
					
					
					/***************************************************************************************************/
               /* Essay type question                                                                             */
               /***************************************************************************************************/
					if($qtype == "Essay")
					{
						
						array_push($essayArray, $qno, $qtype, $qvalue, $qtext, $qid, $studentAnswer, $pointsEarned);
						
					}		

					/***************************************************************************************************/
               /* True/False type question                                                                        */
               /***************************************************************************************************/
					else if($qtype == "True/False")
					{
						array_push($trueFalseArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid, $studentSelection, $pointsEarned);
						
					}
					
					/***************************************************************************************************/
               /* Multiple Choice type question                                                                   */
               /***************************************************************************************************/
					else if($qtype == "Multiple Choice")
					{
						array_push($multipleChoiceArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid, $studentSelection, $pointsEarned);
						
					}
					
					/***************************************************************************************************/
               /* Matching type question                                                                          */
               /***************************************************************************************************/
					else if($qtype == "Matching")
					{
						array_push($matchingArray, $qno, $qtype, $qvalue, $qtext, $heading, $hid, $correct, $qid, $aid, $studentAnswer, $qletter, $pointsEarned);
						
					}
					
					/***************************************************************************************************/
               /* Short Answer type question crapola                                                              */
               /***************************************************************************************************/
					else if($qtype == "Short Answer")
					{
						array_push($shortAnswerArray, $qno, $qtype, $qvalue, $qtext, $qid, $studentAnswer, $pointsEarned);
					}
					
					/***************************************************************************************************/
               /* All That Apply type question                                                                    */
               /***************************************************************************************************/
					else
					{
						array_push($ataArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid, $studentSelection, $pointsEarned);	
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
							echo $tname;
							
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
                        $pointsEarned = 0;
						   /***************************************************************************************************/
							/* Test each question type's array for data; if there's data we add that tab to our page           */
							/***************************************************************************************************/
							// Essay stuff
							if(count($essayArray) > 0)
							{
								
								echo '<div class="panel panel-default">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
	                                <div class="panel-heading" id="panel-color">
	                            	<h4 class="panel-title"></span>  Essay</h4>
	                            	</div>
                            	</a>
							  <div id="collapseSix" class="panel-collapse collapse">
									<div class="panel-body">';
									
							
									for($i = 0; $i < count($essayArray); $i+=7)
									{

										echo'<h4><p class="question_num make_inline">'.$essayArray[$i].'.</p>'.'<p class="essay_questions make_inline">'.$essayArray[$i+3].' ('.$essayArray[$i+2].')'.'</p></h4>
										<h4></h4>
											<div class="essay_answers">
												<textarea class="form-control essay_textarea" disabled id="EssayQuestion'.$essayArray[$i+4].'" name="specificInstruction" rows="5">'.$essayArray[$i+5].'</textarea>
												<div class="answer_text">Answer:</div>
												<textarea class="form-control essay_textarea" rows="4">Put teacher answer here</textarea>
											</div>
												<div class="points_earned_section_essay"><span class="points_earned_txt">Points Earned</span><input type=text value="'.$essayArray[$i+6].'" class="essay_points_tb" id="EssayPoints'.$essayArray[$i+4].'" name="EssayPoints"/></div><br />';
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
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
										<div class="panel-heading" id="panel-color">
											 <h4 class="panel-title">
												True/False
											 </h4>
										</div>
									</a>
									<div id="collapseFour" class="panel-collapse collapse">';
									for($i = 0; $i < count($trueFalseArray); $i+=8)
									{
                                    if($oldQuestion != $trueFalseArray[$i])
                                        {
                                        $oldQuestion = $trueFalseArray[$i];
                                            echo'<div class="panel-body">
                                                  <h4><p class="question_num make_inline">'.$trueFalseArray[$i].'.</p>'.'<p class="tf_questions make_inline">'.$trueFalseArray[$i+3].' ('.$trueFalseArray[$i+2].')'.'</p></h4>
                                                    <div class="tf_answers" id="trueFalse'.$trueFalseCounter.'">';
                                                    $trueFalseStatement->bind_param("s", $trueFalseArray[$i+4]);
													$trueFalseStatement->bind_result($answer_id, $answer_text, $stuSelection, $correct);
													$trueFalseStatement->execute();
													while($trueFalseStatement->fetch())
													{
                                                        $checked = ($stuSelection == 1)?'checked':'';
                                                        echo'<div class="tf_choice">
                                                            <input type="radio" disabled="disabled" name="tf_answer1'.$trueFalseCounter.'" id="tf_answer'.$answer_id.'" ' .$checked . ' value="multipleRadio1" class="multipleRadio">
                                                            <span class="mc_answer_lbl">'.$answer_text.'</span>';
                                                        if($stuSelection == 1)
                                                        {
                                                            if($correct == $stuSelection)
															{
																echo " <img src='images/sign.png' />";
															}
                                                            else
															{
																echo " <img src='images/cross.jpg' />";
															}
                                                        }
                                                        if($correct == 1 and $stuSelection != 1)
                                                        {
                                                        	echo " <img src='images/sign.png' />";
                                                        }
                                                        echo'</div>';
                                                        
                                                    }
                                                   echo' </div>';
                                                   echo'<div class="points_earned_section"><span class="points_earned_txt">Points Earned</span><input type=text value="'.$trueFalseArray[$i+7].'" class="matching_answer_tb" id="TFPoints'.$trueFalseArray[$i+4].'" name="TFPoints"/></div>
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
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
										<div class="panel-heading" id="panel-color">
											<h4 class="panel-title">
												Multiple Choice
											</h4>
										</div>
									</a>
									<div id="collapseOne" class="panel-collapse collapse">';
									for($i = 0; $i < count($multipleChoiceArray); $i+=8)
									{	
                                        if($oldQuestion != $multipleChoiceArray[$i+4])
                                        {
                                           echo'	<div class="panel-body" >

												<h4><p class="question_num make_inline">'.$multipleChoiceArray[$i].'.</p>'.'<p class="mc_questions make_inline">'.$multipleChoiceArray[$i+3].' ('.$multipleChoiceArray[$i+2].')'.'</p></h4>
													<div class="mc_answers" >';
                                                    $oldQuestion = $multipleChoiceArray[$i+4];
													$multipleChoiceStatement->bind_param("s", $multipleChoiceArray[$i+4]);
													$multipleChoiceStatement->bind_result($atext, $mcAnswerId, $stuSelection, $correct);
													$multipleChoiceStatement->execute();
													while($multipleChoiceStatement->fetch())
													{
                                                        $checked = ($stuSelection == 1)?'checked':'';
														echo '<div class="mc_choice" >
															<input type="radio" disabled="disabled" name="mc_answer1'.$multipleChoiceCounter.'" id="mc_answer'.$mcAnswerId.'" value="multipleRadio1" class="multipleRadio" '.$checked.'/>
															<span class="mc_answer_lbl">'.$atext.'</span>';

                                                        if($stuSelection == 1)
                                                        {
                                                            if($correct == $stuSelection)
															{
                                                                $pointsEarned = $multipleChoiceArray[$i+2];
																echo " <img src='images/sign.png' />";
															}
                                                            else
															{
                                                                $pointsEarned = 0;
																echo " <img src='images/cross.jpg' />";
															}
                                                        }
                                                        if($correct == 1 and $stuSelection != 1)
                                                        {
                                                        	echo " <img src='images/sign.png' />";
                                                        }    

                                                        echo '</div>';
													}	
											echo'	</div><div class="points_earned_section"><span class="points_earned_txt">Points Earned</span><input type=text value="'.$multipleChoiceArray[$i+7].'" class="mc_points_tb" id="MCPoints'.$multipleChoiceArray[$i+4].'" name="MCPoints"/></div>
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
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
										<div class="panel-heading" id="panel-color">
											 <h4 class="panel-title">
												Matching
											 </h4>
										</div>
									</a>
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
                                            $matchingStatement->bind_result($qletter, $atext, $questionId, $stuSelection, $newHeadingId);
                                            $matchingStatement->execute();
                                            while($matchingStatement->fetch())
                                            {
                                                $matchingAnswer[] = $qletter;
                                                $matchingAnswer[] = $atext;
                                                $matchingAnswer[] = $questionId;
                                                $matchingAnswer[] = $stuSelection;
                                                $matchingAnswer[] = $newHeadingId;
                                            }
                                            

											echo '<div class="m_question_section make_inline2">';
                                            for($j; $j < count($matchingArray);)
                                            {
												if($headingIdArray[$k] == $matchingArray[$j+5])
												{
													echo'	<div class="">
														<div class="question123">
														<p class="question_num make_inline">'
														.$matchingArray[$j].'.</p>'.'<p class="match_questions make_inline">'.$matchingArray[$j+3].'
															<input type="text" disabled class="matching_answer_tb" value="'.$matchingArray[$j+9].'" id="matching'.$matchingArray[$j+8].'"/>';
														if($matchingArray[$j+10] == $matchingArray[$j+9])
														{
															$pointsEarned = $matchingArray[$j+2];
															echo '<img src="images/sign.png" />';
														}
														else
														{
															$pointsEarned = 0;
															echo '<img src="images/cross.jpg" />';
															echo '&nbsp;'.$matchingArray[$j+10].'';
														}
														
														
														echo'<div class="points_earned_section add_margin_top"><span class="points_earned_txt">Points Earned</span><input type=text value="'.$matchingArray[$j+11].'" class="match_points_tb" id="MPoints'.$matchingArray[$j+7].'" name="TFPoints"/></div></div></p>';
													echo'</div>';
													$j+=12;
												}
											}
											echo '</div>';
											
											echo '<div class="m_answer_section make_inline2">';
											for($i = 0; $i < count($matchingAnswer); $i+=5)
                                            {
												if($matchingAnswer[$i+4] == $headingIdArray[$k])
												{
												 
													echo'<div class="testcrap">';
															echo'<div class="matching_div">
																<p class="question_num make_inline">'.$matchingAnswer[$i].'.</p><p class="match_questions make_inline"><span class="matching_questions">'.$matchingAnswer[$i+1].'</span></p>
															</div>
															<br />';
															
													$matchingCounter++;
													echo '</div>';
												}
                                            }
											echo '</div>';
											
											
                                            $matchingAnswer = null;
                                        }


                                        $matchingStatement->close();
								echo'
									</div></div>
						  </div>';
                                $headingCounter += 12;
							}
							
							// Short Answer stuff
							if(count($shortAnswerArray) > 0)
							{
								echo'<div class="panel panel-default">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
	                        <div class="panel-heading" id="panel-color">
	                            <h4 class="panel-title">
	                                Short Answer
	                            </h4>
	                        </div>
	                    </a>
                        <div id="collapseFive" class="panel-collapse collapse">';
								/*for($i = 0; $i < count($shortAnswerArray); $i+=7)
								{
									echo'<div class="panel-body">
										<h4>'.$shortAnswerArray[$i].'<span class="sa_questions"></span>'.$shortAnswerArray[$i+3].'</h4><h4>Point Value: '.$shortAnswerArray[$i+2].'</h4>
                                        Points Earned<input type=text value="'.$shortAnswerArray[$i+6].'" class="matching_answer_tb" id="SAPoints'.$shortAnswerArray[$i+4].'" name="shortAnswerPoints"/></div>
										<div class="sa_answers">
											<input type="text" disabled class="m_answer_letter form-control" id="ShortAnswer'.$shortAnswerArray[$i+4].'" value="'.$shortAnswerArray[$i+5].'" />
										</div>';
                                    $shortAnswerCounter++;
								}*/
								
								for($i = 0; $i < count($shortAnswerArray); $i+=7)
								{
									echo'<div class="panel-body">
										<h4><p class="question_num make_inline">'.$shortAnswerArray[$i].'.'.'<p class="sa_questions make_inline">'.$shortAnswerArray[$i+3].' ('.$shortAnswerArray[$i+2].')'.'</p></h4>
                                        </div>
										<div class="sa_answers">
											<input type="text" disabled class="form-control sa_answers_tb" id="ShortAnswer'.$shortAnswerArray[$i+4].'" value="'.$shortAnswerArray[$i+5].'" />
											<div class="answer_text">Answer:</div>
											<input type="text" class="form-control sa_teacher_answers_tb" value="" />
										</div>
										<div class="points_earned_section"><span class="points_earned_txt">Points Earned<input type=text value="'.$shortAnswerArray[$i+6].'" class="sa_points_tb" id="SAPoints'.$shortAnswerArray[$i+4].'" name="shortAnswerPoints"/></div><br />';
                                    $shortAnswerCounter++;
								}
									
								echo'
									</div>
								</div>';
							}
							
							// All That Apply stuff
							if(count($ataArray) > 0)
							{
                                $pointsEarnedCounter = 0;
                                $pointsPossibleCounter = 0;
                                $oldQuestion = 0;
								echo'
								<div class="panel panel-default">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
										<div class="panel-heading" id="panel-color">
											 <h4 class="panel-title">
												All That Apply
											 </h4>
										</div>
									</a>
									<div id="collapseThree" class="panel-collapse collapse">';
									for($i = 0; $i < count($ataArray); $i+=8)
									{
                                        if($oldQuestion != $ataArray[$i+4])
                                        {
                                            $oldQuestion = $ataArray[$i+4];
                                            $ataStatement->bind_param("s", $ataArray[$i+4]);
                                            $ataStatement->bind_result($atext, $aid, $stuSelection, $correct);
                                            $ataStatement->execute();
                                             echo'<div class="panel-body">
												  <h4><p class="question_num make_inline">'.$ataArray[$i].'.</p>'.'<p class="ata_questions make_inline">'.$ataArray[$i+3].' ('.$ataArray[$i+2].')</p></h4>
                                                  <div class="ata_answers">';
												  
                                                        while($ataStatement->fetch())
                                                        {
                                                        $checked = ($stuSelection == 1)?'checked':'';
                                                        echo'
                                                            <div class="ata_choice">
                                                                <input type="checkbox" disabled="disabled" name="ata_answer1" id="ata_answer_cb'.$aid.'" class="ata_cb" '.$checked.'/>
                                                                <span class="ata_answer_lbl">'.$atext.'</span>';
                                                            if($stuSelection == 1)
                                                            {
                                                                if($correct == $stuSelection)
                                                                {
                                                                    echo " <img src='images/sign.png' />";
                                                                }
                                                                else
                                                                {
                                                                    echo " <img src='images/cross.jpg' />";
                                                                }
                                                            }
                                                            if($correct == 1 and $stuSelection != 1)
                                                            {
                                                                echo " <img src='images/sign.png' />";
                                                            }
                                                            echo '</div>';
                                                        }
                                            //echo 'Points Earned<input type=text value="'.$ataArray[$i+7].'" class="matching_answer_tb" id="ATAPoints'.$ataArray[$i+4].'" name="TFPoints"/>';
                                            
											
											echo'</div>';
											echo '<div class="points_earned_section"><span class="points_earned_txt">Points Earned</span><input type=text value="'.$ataArray[$i+7].'" class="ata_points_tb" id="ATAPoints'.$ataArray[$i+4].'" name="TFPoints"/>';
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
			<button type="button" class="btn btn-success btn-block submit_btn" id="Submit">Save</button>
		</div>
				
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
    <script>
    $(document).ready(function()
	{
			$("#backToClass").click(function()
			{
            window.location = "teacherClassPage.php?classId=" + '<?php echo $classId; ?>';
         });
			
        $("#Submit").click(function()
        {
			swal("Success","New grade saved!", "success");
            var testId = '<?php echo $testId; ?>';
            var studentId = '<?php echo $studentId; ?>';
            var oldId = 0;
            var questionIdArray = [];
            var pointsEarnedArray = [];
            <?php for($i = 0; $i < count($essayArray); $i+=7){ ?>
            if(oldId != '<?php echo $essayArray[$i+4]; ?>')
                questionIdArray.push('<?php echo $essayArray[$i+4];?>');
            oldId = '<?php echo $essayArray[$i+4]; ?>';
            <?php } ?>
            <?php for($i = 0; $i < count($shortAnswerArray); $i+=7){ ?>
            if(oldId != '<?php echo $shortAnswerArray[$i+4]; ?>')
                questionIdArray.push('<?php echo $shortAnswerArray[$i+4];?>');
            oldId = '<?php echo $shortAnswerArray[$i+4]; ?>';
            <?php } ?>
            <?php for($i = 0; $i < count($multipleChoiceArray); $i+=8){ ?>
            if(oldId != '<?php echo $multipleChoiceArray[$i+4]; ?>')
			{
                questionIdArray.push('<?php echo $multipleChoiceArray[$i+4];?>');
				alert('<?php echo $multipleChoiceArray[$i+4]; ?>');
			}
            oldId = '<?php echo $multipleChoiceArray[$i+4]; ?>';
            <?php } ?>
            <?php for($i = 0; $i < count($trueFalseArray); $i+=8){ ?>
            if(oldId != '<?php echo $trueFalseArray[$i+4]; ?>')
                questionIdArray.push('<?php echo $trueFalseArray[$i+4];?>');
            oldId = '<?php echo $trueFalseArray[$i+4]; ?>';
            <?php } ?>
            <?php for($i = 0; $i < count($ataArray); $i+=8){ ?>
            if(oldId != '<?php echo $ataArray[$i+4]; ?>')
                questionIdArray.push('<?php echo $ataArray[$i+4];?>');
            oldId = '<?php echo $ataArray[$i+4]; ?>';
            <?php } ?>
            <?php for($i = 0; $i < count($matchingArray); $i+=12){ ?>
            if(oldId != '<?php echo $matchingArray[$i+7]; ?>')
                questionIdArray.push('<?php echo $matchingArray[$i+7];?>');
            oldId = '<?php echo $matchingArray[$i+7]; ?>';
            <?php } ?>
            oldId = 0;
            <?php for($i = 0; $i < count($essayArray); $i+=7){ ?>
                    pointsEarnedArray.push($("#EssayPoints"+'<?php echo $essayArray[$i+4]; ?>').val());
            <?php } ?>
            <?php for($i = 0; $i < count($shortAnswerArray); $i+=7){ ?>
                    pointsEarnedArray.push($("#SAPoints"+'<?php echo $shortAnswerArray[$i+4]; ?>').val());
            <?php } ?>
            <?php for($i = 0; $i < count($multipleChoiceArray); $i+=8){ ?>
                if(oldId != '<?php echo $multipleChoiceArray[$i+4]; ?>')
				{
                    pointsEarnedArray.push($("#MCPoints"+'<?php echo $multipleChoiceArray[$i+4]; ?>').val());
					alert($("#MCPoints"+'<?php echo $multipleChoiceArray[$i+4]; ?>').val());
				}
				oldId = '<?php echo $multipleChoiceArray[$i+4]; ?>';
            <?php } ?>
            oldId = 0;
            <?php for($i = 0; $i < count($trueFalseArray); $i+=8){ ?>
                if(oldId != '<?php echo $trueFalseArray[$i+4]; ?>')
                    pointsEarnedArray.push($("#TFPoints"+'<?php echo $trueFalseArray[$i+4]; ?>').val());
            oldId = '<?php echo $trueFalseArray[$i+4]; ?>';    
            <?php } ?>
            <?php for($i = 0; $i < count($ataArray); $i+=8){ ?>
                if(oldId != '<?php echo $ataArray[$i+4]; ?>')
                    pointsEarnedArray.push($("#ATAPoints"+'<?php echo $ataArray[$i+4]; ?>').val());
                oldId = '<?php echo $ataArray[$i+4]; ?>';
            <?php } ?>
            <?php for($i = 0; $i < count($matchingArray); $i+=12){ ?>
                    pointsEarnedArray.push($("#MPoints"+'<?php echo $matchingArray[$i+7]; ?>').val());
                    //alert($("#MPoints"+'<?php echo $matchingArray[$i+7]; ?>').val());
            <?php } ?>
            
            $.post("TestButtonScripts/gradeButton.php",
            {
                "pointsEarnedArray[]":pointsEarnedArray,
                "questionIdArray[]":questionIdArray,
                testId:testId,
                studentId:studentId
            },
            function(data)
            {
				alert(data);
                window.location = "teacherClassPage.php?classId=" + '<?php echo $classId; ?>';
            });
        });
    });
    </script>
	

</body>

</html>
