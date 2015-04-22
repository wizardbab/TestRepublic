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
date_default_timezone_set(timezone_name_from_abbr("CST"));

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

	
	 // session variable_exists, use that
	 $timeLimit = $_SESSION['timeLimit'];



// Student first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from student where student_id = ?";

// Class id and description query
$query = "select class_id, class_description from class where class_id = ?";

$summaryQuery = "select question_no, question_type, question_value, question_text, heading, heading_id, question_letter, question_id, answer_id
								 from question
                                 left join answer using(question_id)
								 where test_id = ? and student_id = ?";
								 
$headerQuery = "SELECT class_id, test_name from test where test_id = ?";

$multipleChoiceQuery = "select answer_text, answer_id from answer where question_id = ?";
$ataQuery = "select answer_text, answer_id from answer where question_id = ?";

$matchingQuery = "SELECT correct, answer_text, answer_id, a_heading_id
from answer
where a_heading_id = ?
group by(correct)";

$matchingHeadQuery = "select distinct heading_id, heading from question where heading_id is not null and test_id = ? and student_id = ?";

$trueFalseQuery = "select answer_id, answer_text from answer where question_id = ?";

$selectStartTime = "select start_time from test_list where test_id = ? and student_id = ?";


$selectStartStatement = $database->prepare($selectStartTime); 
$matchingHeadStatement = $database->prepare($matchingHeadQuery);
$queryStatement = $database->prepare($query);
$headerStatement = $database->prepare($headerQuery);
$multipleChoiceStatement = $database->prepare($multipleChoiceQuery);
$ataStatement = $database->prepare($ataQuery);
$matchingStatement = $database->prepare($matchingQuery);
$trueFalseStatement = $database->prepare($trueFalseQuery);
//require("Nav.php");

@$classId = $_POST['classId'];
@$testId = $_POST['testId'];
@$testName = $_POST['testName'];

$_SESSION['classId'] = $classId;
$_SESSION['testId'] = $testId;

$selectStartStatement->bind_param("ss", $testId, $id);
$selectStartStatement->bind_result($ctime);
$selectStartStatement->execute();
while($selectStartStatement->fetch())
{
	
}
$selectStartStatement->close();


	$secs = strtotime($timeLimit)-strtotime("00:00:00");
	$endTime = date("H:i:s",strtotime($ctime)+$secs);

	$currentTime = date('H:i:s', time());

	sscanf($currentTime, "%d:%d:%d", $hours, $minutes, $seconds);

	$timeSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

	sscanf($endTime, "%d:%d:%d", $hours, $minutes, $seconds);

	$endTimeSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

	$timeLeft = $endTimeSeconds - $timeSeconds;

    // extract hours
    $hours = floor($timeLeft / (60 * 60));
 
    // extract minutes
    $divisor_for_minutes = $timeLeft % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);
 
    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $timeLeft = ceil($divisor_for_seconds);
	 
	 $timeArray[] = $hours;
	 $timeArray[] = $minutes;
	 $timeArray[] = $timeLeft;
?>
	
</head>


<body class="container-fluid" onload="myFunction()">
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
									echo $first_name . " " . $last_name . " " . $id;
								}
								$topRightStatement->close();?><b class="caret"></b></a>
						
                </li>
            </ul>
        </nav>



	
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
		echo'</div>';	
				//$questionArray = array(array("number" => 0, "type" => 0, "value" => 0, "text" => 0, "heading" => 0, "id" => 0, "letter" => 0));
			
				$questionArray = array();
				$essayArray = array();
				$trueFalseArray = array();
				$multipleChoiceArray = array();
				$matchingArray = array();
				$shortAnswerArray = array();
				$ataArray = array();
				
				
				$summaryStatement = $database->prepare($summaryQuery);
				$summaryStatement->bind_param("ss", $testId, $id);
				$summaryStatement->bind_result($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid);
				
				
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
					array_push($questionArray, array($qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid));
					
					
					/***************************************************************************************************/
               /* Essay type question                                                                             */
               /***************************************************************************************************/
					if($qtype == "Essay")
					{
						
						array_push($essayArray, $qno, $qtype, $qvalue, $qtext, $qid);
						
					}		

					/***************************************************************************************************/
               /* True/False type question                                                                        */
               /***************************************************************************************************/
					else if($qtype == "True/False")
					{
						array_push($trueFalseArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid);
						
					}
					
					/***************************************************************************************************/
               /* Multiple Choice type question                                                                   */
               /***************************************************************************************************/
					else if($qtype == "Multiple Choice")
					{
						array_push($multipleChoiceArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid);
						
					}
					
					/***************************************************************************************************/
               /* Matching type question                                                                          */
               /***************************************************************************************************/
					else if($qtype == "Matching")
					{
						array_push($matchingArray, $qno, $qtype, $qvalue, $qtext, $heading, $hid, $qletter, $qid, $aid);
					}
					
					/***************************************************************************************************/
               /* Short Answer type question crapola                                                              */
               /***************************************************************************************************/
					else if($qtype == "Short Answer")
					{
						array_push($shortAnswerArray, $qno, $qtype, $qvalue, $qtext, $qid);
					}
					
					/***************************************************************************************************/
               /* All That Apply type question                                                                    */
               /***************************************************************************************************/
					else
					{
						array_push($ataArray, $qno, $qtype, $qvalue, $qtext, $qid, $aid);	
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
						  <div id="test"></div>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group" id="accordion">
					 <span id="time_limit_txt">Time Limit:&nbsp;&nbsp;</span><span id="test"></span>
                    
					<!-- Multiple Choice /.panel -->
					
                    
					<!-- Matching /.panel -->
				  
								  
								  
						
				</div>
                    
					<!-- All that Apply /.panel -->
                    
                    
					<!-- True/False /.panel -->
                    
						  
						  
						  
                    
					<!-- Short Answer /.panel -->
                    
                    <?php
						  // Set the hours, minutes, and seconds into their own array
							
							$counter = 1;
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
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
	                                <div class="panel-heading" id="panel-color">
	                            	<h4 class="panel-title"></span>  Essay</h4>
	                            	</div>
                            	</a>
							  <div id="collapseSix" class="panel-collapse collapse">
									<div class="panel-body">';
									
									/*for($i = 0; $i < count($essayArray); $i+=5)
									{
										echo'<h4>'.$counter.'. <span class="essay_questions">'.$essayArray[$i+3].'</span></h4><h4>Point Value: '.$essayArray[$i+2].'</h4>
											<div class="essay_answers">
												<textarea class="form-control" id="EssayQuestion'.$essayArray[$i+4].'" name="specificInstruction" rows="6"> </textarea></div>';
                                        $counter++;
									}*/
									
									for($i = 0; $i < count($essayArray); $i+=5)
									{

										echo'<h4><p class="question_num make_inline">'.$counter.'. </p>'.'<p class="essay_questions make_inline">'.$essayArray[$i+3].' ('.$essayArray[$i+2].')'.'</p></h4>
										<h4></h4>
											<div class="essay_answers">
												<textarea class="form-control essay_textarea" id="EssayQuestion'.$essayArray[$i+4].'" name="specificInstruction" rows="5"></textarea>
											</div>';
												
                                        $counter++;
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
									for($i = 0; $i < count($trueFalseArray); $i+=6)
									{
                                    if($oldQuestion != $trueFalseArray[$i])
                                        {
                                        $oldQuestion = $trueFalseArray[$i];
                                            echo'<div class="panel-body">
													<h4><p class="question_num make_inline">'.$counter.'. </p>'.'<p class="tf_questions make_inline">'.$trueFalseArray[$i+3].' ('.$trueFalseArray[$i+2].')'.'</p></h4>
                                                    <div class="tf_answers" id="trueFalse'.$trueFalseCounter.'">';	
													
                                                    $trueFalseStatement->bind_param("s", $trueFalseArray[$i+4]);
													$trueFalseStatement->bind_result($answer_id, $answer_text);
													$trueFalseStatement->execute();
													while($trueFalseStatement->fetch())
													{
                                                        /*echo'<div class="tf_choice">
                                                            <label><input type="radio" name="tf_answer1'.$trueFalseCounter.'" id="tf_answer'.$answer_id.'" value="multipleRadio1" class="multipleRadio">

                                                            <span class="mc_answer_lbl">'.$answer_text.'</span></label>
                                                            </div>';*/
															
                                                        echo'<div class="tf_choice">
                                                            <input type="radio" name="tf_answer1'.$trueFalseCounter.'" id="tf_answer'.$answer_id.'" value="multipleRadio1" class="multipleRadio">
                                                            <span class="mc_answer_lbl">'.$answer_text.'</span>';

                                                        echo'</div>';
                                                    }
                                                   echo' </div>
                                            </div>';
                                            $counter++;
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
									for($i = 0; $i < count($multipleChoiceArray); $i+=6)
									{	
                                        if($oldQuestion != $multipleChoiceArray[$i+4])
                                        {
                                            echo'	<div class="panel-body" >
													<h4><p class="question_num make_inline">'.$counter.'. </p>'.'<p class="mc_questions make_inline">'.$multipleChoiceArray[$i+3].' ('.$multipleChoiceArray[$i+2].')'.'</p></h4>
													<div class="mc_answers" >';
													
                                                    $oldQuestion = $multipleChoiceArray[$i+4];
													$multipleChoiceStatement->bind_param("s", $multipleChoiceArray[$i+4]);
													$multipleChoiceStatement->bind_result($atext, $mcAnswerId);
													$multipleChoiceStatement->execute();
													while($multipleChoiceStatement->fetch())
													{
														echo '<div class="mc_choice" >
															<input type="radio" name="mc_answer1'.$multipleChoiceCounter.'" id="mc_answer'.$mcAnswerId.'" value="multipleRadio1" class="multipleRadio" /><label for="mc_answer'.$mcAnswerId.'">
															<span class="mc_answer_lbl">&nbsp;'.$atext.'</span></label>
                                                            </div>';
													}	
											echo'	</div>
											</div>';
                                            $counter++;
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
                                        $matchingHeadStatement->bind_param("ss", $testId, $id);
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
                                            $matchingStatement->bind_result($qletter, $atext, $aid, $newHeadingId);
                                            $matchingStatement->execute();
                                            while($matchingStatement->fetch())
                                            {
                                                $matchingAnswer[] = $qletter;
                                                $matchingAnswer[] = $atext;
                                                $matchingAnswer[] = $aid;
                                                $matchingAnswer[] = $newHeadingId;
                                            }
                                            //$oldMId = -1;
                                            
											
											//
											echo '<div class="m_question_section make_inline2">';
											for($j; $j < count($matchingArray);)
                                            {
												if($headingIdArray[$k] == $matchingArray[$j+5])
												{
													echo'	<div class="">
														<div class="question123">
														<p class="question_num make_inline">'
														.$counter.'. </p>'.'<p class="match_questions make_inline">'.$matchingArray[$j+3].'
															<input type="text" class="matching_answer_tb" id="matching'.$matchingArray[$j+7].'"/>';
														
														
														
														echo'</div></p>';
													echo'</div>';
													$counter++;
													$j+=9;
												}
											}
											echo '</div>';
											
											echo '<div class="m_answer_section make_inline2">';
											for($i = 0; $i < count($matchingAnswer); $i+=4)
                                            {
												if($matchingAnswer[$i+3] == $headingIdArray[$k])
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
									</div>
						</div>  </div>';
                                $headingCounter += 9;
							}
							
							// Short Answer stuff
							if(count($shortAnswerArray))
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
								for($i = 0; $i < count($shortAnswerArray); $i+=5)
								{
									/*echo'<div class="panel-body">
										<h4>'.$counter.'. <span class="sa_questions"></span>'.$shortAnswerArray[$i+3].'</h4><h4>Point Value: '.$shortAnswerArray[$i+2].'</h4>
										<div class="sa_answers">
											<input type="text" class="m_answer_letter form-control" id="ShortAnswer'.$shortAnswerArray[$i+4].'" />
										</div>
									</div>';
                                    $counter++;*/
									
									echo'<div class="panel-body">
										<h4><p class="question_num make_inline">'.$counter.'. '.'<p class="sa_questions make_inline">'.$shortAnswerArray[$i+3].' ('.$shortAnswerArray[$i+2].')'.'</p></h4>
                                        </div>
										<div class="sa_answers">
											<input type="text" class="form-control sa_answers_tb" id="ShortAnswer'.$shortAnswerArray[$i+4].'" />
							
										</div>';
                                    $counter++;
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
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
										<div class="panel-heading" id="panel-color">
											 <h4 class="panel-title">
												All That Apply
											 </h4>
										</div>
									</a>
									<div id="collapseThree" class="panel-collapse collapse">';
									/*for($i = 0; $i < count($ataArray); $i+=6)
									{
                                        if($oldQuestion != $ataArray[$i+4])
                                        {
                                            $oldQuestion = $ataArray[$i+4];
                                            $ataStatement->bind_param("s", $ataArray[$i+4]);
                                            $ataStatement->bind_result($atext, $aid);
                                            $ataStatement->execute();
                                             echo'<div class="panel-body">
                                                  <h4>'.$counter.'.     <span class="ata_questions">'.$ataArray[$i+3].'</span></h4><h4>Point Value: '.$ataArray[$i+2].'</h4>
                                                  <div class="ata_answers">';
                                                        while($ataStatement->fetch())
                                                        {
                                                        echo'
                                                            <div class="ata_choice">
                                                                <input type="checkbox" name="ata_answer1" id="ata_answer_cb'.$aid.'" class="ata_cb" /><label for="ata_answer_cb'.$aid.'">
                                                                <span class="ata_answer_lbl">'.$atext.'</span></label>
                                                            </div>';
                                                        }
                                            
                                            echo'</div>';
                                            echo'</div>';
                                        }
                                        $counter++;
									}*/
									
									for($i = 0; $i < count($ataArray); $i+=6)
									{
                                        if($oldQuestion != $ataArray[$i+4])
                                        {
                                            $oldQuestion = $ataArray[$i+4];
                                            $ataStatement->bind_param("s", $ataArray[$i+4]);
                                            $ataStatement->bind_result($atext, $aid);
                                            $ataStatement->execute();
                                             echo'<div class="panel-body">
                                                  <h4><p class="question_num make_inline">'.$counter.'. </p>'.'<p class="ata_questions make_inline">'.$ataArray[$i+3].' ('.$ataArray[$i+2].')</p></h4>
                                                  <div class="ata_answers">';
                                                        while($ataStatement->fetch())
                                                        {
                                                        echo'
                                                            <div class="ata_choice">
                                                                <input type="checkbox" name="ata_answer1" id="ata_answer_cb'.$aid.'" class="ata_cb"/>
                                                                <span class="ata_answer_lbl">'.$atext.'</span>';
                                                            echo '</div>';
                                                        }
                                            echo'</div>';
                                        }
									}
									$ataStatement->close();
									
								echo'</div>
                                </div>';
							}
						  ?>
					<!-- Essay /.panel -->
                    
              
                <!-- /.panel-group -->
          
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
		<div class="row">
			<button type="button" class="btn btn-success btn-block submit_btn" id="Submit">Submit</button>
		</div>
				
   </div>
   <!-- /. Container -->
   	<script>
	$(document).ready(function()
	{
		function submitTest()
		{
			 
			
		}
	});
	</script>
	

    
		<script>
	$(document).ready(function()
	{
        $("#Submit").click(function()
        {
			  var counter;
            var essayArray = [];
                <?php for($i = 0; $i < count($essayArray); $i+=5){ ?>
                    essayArray.push('<?php echo $essayArray[$i+4];?>');
                <?php } ?>
            var shortAnswerArray = [];
                <?php for($i = 0; $i < count($shortAnswerArray); $i+=5){ ?>
                    shortAnswerArray.push('<?php echo $shortAnswerArray[$i+4];?>');
                <?php } ?>
            var multipleChoiceArray = [];
                <?php for($i = 0; $i < count($multipleChoiceArray); $i+=6){ ?>
                    multipleChoiceArray.push('<?php echo $multipleChoiceArray[$i+5];?>');
                <?php } ?>
            var trueFalseArray = [];
                <?php for($i = 0; $i < count($trueFalseArray); $i+=6){ ?>
                    trueFalseArray.push('<?php echo $trueFalseArray[$i+5];?>');
                <?php } ?>
            var ataArray = [];
                <?php for($i = 0; $i < count($ataArray); $i+=6){ ?>
                    ataArray.push('<?php echo $ataArray[$i+5];?>');
                <?php } ?>
            var matchingArray = [];
                <?php for($i = 0; $i < count($matchingArray); $i+=9){ ?>
                    matchingArray.push('<?php echo $matchingArray[$i+7];?>');
                <?php } ?>
            var essayAnswerArray = [];
            var shortAnswerAnswerArray = [];
            var multipleChoiceAnswerArray = [];
            var trueFalseAnswerArray = [];
            var ataAnswerArray = [];
            var matchingAnswerArray = [];
			var i = 0;
            var id = '<?php echo $id; ?>';
            var testId = '<?php echo $testId; ?>';
            
            for(counter = 0; counter < essayArray.length; counter++)
            {
                essayAnswerArray[counter] = $("#EssayQuestion"+essayArray[counter]).val();
            }
            
            for(counter = 0; counter < shortAnswerArray.length; counter++)
            {
                shortAnswerAnswerArray[counter] = $("#ShortAnswer"+shortAnswerArray[counter]).val();
            }
		
				$.post("TestAnswerScripts/essayAndShortAnswer.php",
				{
					"essayIds[]":essayArray,
					"essayChoices[]":essayAnswerArray,
					"shortAnswerIds[]":shortAnswerArray,
					"shortAnswerChoices[]":shortAnswerAnswerArray
				},
				function(data)
				{
					
				});
				
            for(counter = 0; counter < multipleChoiceArray.length; counter++)
            {
                if ($('#mc_answer'+multipleChoiceArray[counter]).is('input:checked'))
                {
                    multipleChoiceAnswerArray[counter] = 1;
                }
                else
                {
                    multipleChoiceAnswerArray[counter] = 0;	
                }
            }
            for(counter = 0; counter < trueFalseArray.length; counter++)
            {
                if ($('#tf_answer'+trueFalseArray[counter]).is('input:checked'))
                {
                    trueFalseAnswerArray[counter] = 1;
                }
                else
                {
                    trueFalseAnswerArray[counter] = 0;	
                }
            }
            for(counter = 0; counter < ataArray.length; counter++)
            {
                if ($('#ata_answer_cb'+ataArray[counter]).is('input:checked'))
                {
                    ataAnswerArray[counter] = 1;
                }
                else
                {
                    ataAnswerArray[counter] = 0;	
                }
            }
            for(counter = 0; counter < matchingArray.length; counter++)
            {
                matchingAnswerArray[counter] = $("#matching"+matchingArray[counter]).val();
            }
            
            $.post("TestAnswerScripts/mcmatatf.php",
				{
					"multipleChoiceArray[]":multipleChoiceArray,
                    "multipleChoiceAnswerArray[]":multipleChoiceAnswerArray,
                    "trueFalseArray[]":trueFalseArray,
                    "trueFalseAnswerArray[]":trueFalseAnswerArray,
                    "ataArray[]":ataArray,
                    "ataAnswerArray[]":ataAnswerArray,
                    "matchingArray[]":matchingArray,
                    "matchingAnswerArray[]":matchingAnswerArray
				},
				function(data)
				{

				});
                
            $.post("TestAnswerScripts/submit.php",
            {
                id:id,
                testId:testId
            },
            function(data)
            {
            });
            window.location = "pledgePage.php";
			   
           
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
	 
	 <script>
	 
	 function changePage()
	 {
		window.location = "pledgePage.php";
	 }
	 
	
	 var timeLimit = '<?php echo $timeLimit; ?>';
	 
	 var hours = '<?php echo $timeArray[0]; ?>';
	 var minutes = '<?php echo $timeArray[1]; ?>';
	 var seconds = '<?php echo $timeArray[2]; ?>';
	 
	 var myVar;
	function myFunction()
	{
       myVar = setInterval(function(){ myTimer() }, 960);
	}

	function pad2(number)
	{
     return (number < 10 ? '0' : '') + number;
   
   }
	
	function myTimer() 
	{
		//var d = new Date();
		//var t = d.toLocaleTimeString();
		if(hours == 0 && minutes == 0 && seconds == 0)
		{
			clearInterval(myVar);
			
			var counter;
            var essayArray = [];
                <?php for($i = 0; $i < count($essayArray); $i+=5){ ?>
                    essayArray.push('<?php echo $essayArray[$i+4];?>');
                <?php } ?>
            var shortAnswerArray = [];
                <?php for($i = 0; $i < count($shortAnswerArray); $i+=5){ ?>
                    shortAnswerArray.push('<?php echo $shortAnswerArray[$i+4];?>');
                <?php } ?>
            var multipleChoiceArray = [];
                <?php for($i = 0; $i < count($multipleChoiceArray); $i+=6){ ?>
                    multipleChoiceArray.push('<?php echo $multipleChoiceArray[$i+5];?>');
                <?php } ?>
            var trueFalseArray = [];
                <?php for($i = 0; $i < count($trueFalseArray); $i+=6){ ?>
                    trueFalseArray.push('<?php echo $trueFalseArray[$i+5];?>');
                <?php } ?>
            var ataArray = [];
                <?php for($i = 0; $i < count($ataArray); $i+=6){ ?>
                    ataArray.push('<?php echo $ataArray[$i+5];?>');
                <?php } ?>
            var matchingArray = [];
                <?php for($i = 0; $i < count($matchingArray); $i+=9){ ?>
                    matchingArray.push('<?php echo $matchingArray[$i+7];?>');
                <?php } ?>
            var essayAnswerArray = [];
            var shortAnswerAnswerArray = [];
            var multipleChoiceAnswerArray = [];
            var trueFalseAnswerArray = [];
            var ataAnswerArray = [];
            var matchingAnswerArray = [];
			var i = 0;
            var id = '<?php echo $id; ?>';
            var testId = '<?php echo $testId; ?>';
            
            for(counter = 0; counter < essayArray.length; counter++)
            {
                essayAnswerArray[counter] = $("#EssayQuestion"+essayArray[counter]).val();
            }
            
            for(counter = 0; counter < shortAnswerArray.length; counter++)
            {
                shortAnswerAnswerArray[counter] = $("#ShortAnswer"+shortAnswerArray[counter]).val();
            }
		
				$.post("TestAnswerScripts/essayAndShortAnswer.php",
				{
					"essayIds[]":essayArray,
					"essayChoices[]":essayAnswerArray,
					"shortAnswerIds[]":shortAnswerArray,
					"shortAnswerChoices[]":shortAnswerAnswerArray
				},
				function(data)
				{
					
				});
				
            for(counter = 0; counter < multipleChoiceArray.length; counter++)
            {
                if ($('#mc_answer'+multipleChoiceArray[counter]).is(':checked'))
                {
                    multipleChoiceAnswerArray[counter] = 1;
                }
                else
                {
                    multipleChoiceAnswerArray[counter] = 0;	
                }
            }
            for(counter = 0; counter < trueFalseArray.length; counter++)
            {
                if ($('#tf_answer'+trueFalseArray[counter]).is(':checked'))
                {
                    trueFalseAnswerArray[counter] = 1;
                }
                else
                {
                    trueFalseAnswerArray[counter] = 0;	
                }
            }
            for(counter = 0; counter < ataArray.length; counter++)
            {
                if ($('#ata_answer_cb'+ataArray[counter]).is(':checked'))
                {
                    ataAnswerArray[counter] = 1;
                }
                else
                {
                    ataAnswerArray[counter] = 0;	
                }
            }
            for(counter = 0; counter < matchingArray.length; counter++)
            {
                matchingAnswerArray[counter] = $("#matching"+matchingArray[counter]).val();
            }
            
                $.post("TestAnswerScripts/mcmatatf.php",
				{
					"multipleChoiceArray[]":multipleChoiceArray,
                    "multipleChoiceAnswerArray[]":multipleChoiceAnswerArray,
                    "trueFalseArray[]":trueFalseArray,
                    "trueFalseAnswerArray[]":trueFalseAnswerArray,
                    "ataArray[]":ataArray,
                    "ataAnswerArray[]":ataAnswerArray,
                    "matchingArray[]":matchingArray,
                    "matchingAnswerArray[]":matchingAnswerArray
				},
				function(data)
				{

				});
                
            $.post("TestAnswerScripts/submit.php",
            {
                id:id,
                testId:testId
            },
            function(data)
            {
            });
            window.location="pledgePage.php";
				
		}
		else
		{
			if(seconds > 0)
			{
				seconds--;
				
			}
			if(seconds == 0)
			{
				
				if(minutes > 0)
				{
					minutes--;
					seconds = 59;
				}
				
			}
			if(minutes == 0)
			{
				if(hours > 0)
				{
					hours--;

					minutes = 59;
				}
			}
		}
		document.getElementById("test").innerHTML = pad2(hours) + ":" +  pad2(minutes) + ":" + pad2(seconds);
	}
	 
	 </script>
	

</body>

</html>
