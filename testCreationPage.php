<!DOCTYPE html>

<!-- 2/21 - Modals added by Victor Jereza -->
<!-- 2/23 - Added lots of JavaScript -->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test Republic</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="css/teacher-create-test.css" rel="stylesheet" type="text/css" />
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	
	<!-- Custom Test Creation JavaScript --> 
	<script src="js/testCreation.js"></script>
	
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
    <script type="text/javascript" src="js/allThatApplyValidation.js"></script>
</head>
<?php
session_start();
// Include the constants used for the db connection
require("constants.php");
// Gets the class id appended to url from teacherMainPage.php
$id = $_SESSION['username']; // Just a random variable gotten from the URL
$classId = $_SESSION['classId'];
$sessionTestId = $_SESSION['testId'];
global $newTestId;
if(!is_null($_POST['testId']))
   @$sessionTestId = $_POST['testId'];
    
if($id == null)
    header('Location: login.html');
    
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);
// Teacher first and last name to display on top right of screen
$topRightQuery = "select first_name, last_name from teacher where teacher_id = ?";
// Class ID and description at the top of the page
$mainClassQuery = "select class_id, class_description from class where class_id = ?";
$mainClassStatement = $database->prepare($mainClassQuery);
// Generate a test id
$testIdQuery = "select a.test_id, a.saved, question_id from test as a
	left join test as b
    on (a.test_id < b.test_id)
    left join question as c on a.test_id = c.test_id
    where b.test_id is null";
// Create a test
$createTestQuery = "insert into test(test_id) values(?)";
				  
// Publish a test
$publishQuery = "insert into test_list(student_id, test_id)
					  select student_id, ? from enrollment where class_id = ?";
					  
$populateTestCrapQuery = "select test_name, date_begin, date_end, time_limit, instruction, pledge, max_points
									from test where test_id = ?";
                                    
// Old questions from Db
$oldQuestionsQuery = "select question_id, question_type, question_value, question_text, question_no,
                        answer_id, answer_text, correct, heading_id, heading from question
                        join answer using (question_id)
                        where test_id = ? group by(question_no)";
// These go with the form on the left of the page
$testName = (isset($_POST['testName']) ? $_POST['testName'] : "");
$startDate = (isset($_POST['startDate']) ? $_POST['startDate'] : "");
$endDate = (isset($_POST['endDate']) ? $_POST['endDate'] : "");
$timeLimit = (isset($_POST['timeLimit']) ? $_POST['timeLimit'] : "");
$specificInstructions = (isset($_POST['specificInstructions']) ? $_POST['specificInstructions'] : "");
$testPledge = (isset($_POST['testPledge']) ? $_POST['testPledge'] : "");
$maxPoints = (isset($_POST['maxPoints']) ? $_POST['maxPoints'] : "");
/*global $testName;
global $startDate;
global $endDate;
global $timeLimit;
global $specificInstructions;
global $testPledge;
global $maxPoints; */
$modalId = 0;
?>
<body>
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
												echo $first_name . " " . $last_name .", " . $id;
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
	
        <!-- /#sidebar-wrapper -->
        <!-- Page Content -->
        <div id="page-content-wrapper">
		<!-- Keep page stuff under this div! -->
            <div class="container-fluid">
                <div class="row">
					<div class="col-md-12" id="course_section">
						<?php
                        $mainClassStatement->bind_param("s", $classId);
                        $mainClassStatement->bind_result($clid, $clde);
                        $mainClassStatement->execute();
                        while($mainClassStatement->fetch())
                        {
                            echo '<div class="course_header">
                        <div class="course_number">'
                            . $clid .
                        '</div>
                        
                        <div class="class_name">'
                            . $clde . 
                        '</div>
                        </div>'; 
                        }
                        $mainClassStatement->close();
                        ?>
					</div>
				</div>
			<?php
				// New test id
				if($sessionTestId == "")
				{
					$testIdStatement = $database->prepare($testIdQuery);
					$testIdStatement->bind_result($tid, $saved, $questionId);
					$testIdStatement->execute();
					$testIdStatement->fetch();
                    // Create a session variable with the test id
                    if($saved == 0 and is_null($questionId))
                    {
                        $newTestId = $tid;
                        $_SESSION['testId'] = $newTestId;
                    }
                    else
                    {
                        $newTestId = $tid + 1;
                        $_SESSION['testId'] = $newTestId;
                    }
					$testIdStatement->close();
				}
				else
				{
					$newTestId = $sessionTestId;
				}
				$testCreateStatement = $database->prepare($createTestQuery);
				$testCreateStatement->bind_param("s", $newTestId);
                $testCreateStatement->execute();
				$testCreateStatement->close();
				
					$populateTestCrapStatement = $database->prepare($populateTestCrapQuery);
					$populateTestCrapStatement->bind_param("s", $newTestId);
					$populateTestCrapStatement->bind_result($bTestName, $bStartDate, $bEndDate, $bTimeLimit, $bSpecificInstructions, $bTestPledge, $bMaxPoints);
					$populateTestCrapStatement->execute();
					while($populateTestCrapStatement->fetch())
					{
						$testName = $bTestName;
	
						if(!is_null($bStartDate))
							$startDate = $bStartDate;
						
						if(!is_null($bEndDate))
							$endDate = $bEndDate;
						
						$timeLimit = $bTimeLimit;
						$specificInstructions = $bSpecificInstructions;
						$testPledge = $bTestPledge;
						$maxPoints = $bMaxPoints;
					}
					$populateTestCrapStatement->close();
				?>
				<div class="row" id="test_section">
				
					<div class="col-md-4" id="test_information">
				
						<div class="test-info-text">
							Test Information
						</div>
						<form name="test_form" id="test_form" method="post">
							<label class="blocklabel">Test Name:
								<input type="text" id="testName" name="testName" placeholder="Test #1" value="<?php echo $testName; ?>" />
							</label>
							
							<label class="date_lbl">Start Date:
								<input type="text" id="dateBegin" name="dateBegin" value="<?php echo $startDate; ?>" placeholder="mm/dd/yyyy" />
							</label>
							
							<label class="time_lbl">Time:
								<input type="time" placeholder="12:00 PM" />
							</label>
							
							<label class="date_lbl">End Date:&nbsp;
								<input type="text" id="dateEnd" name="dateEnd" value="<?php echo $endDate; ?>" placeholder="mm/dd/yyyy" />
							</label>
							
							<label class="time_lbl">Time:
								<input type="time" placeholder="12:00 PM" />
							</label>
							
							<label class="time_limit_lbl">Time Limit:
								<input type="number" id="timeLimit" name="timeLimit" value="<?php echo $timeLimit; ?>" /> minutes
							</label>
							
							<br />
							<label class="time_limit_lbl">Max Points:
								<input type="number" id="maxPoints" name="maxPoints" value="<?php echo $maxPoints; ?>" /> 
							</label>
							
							<br />
							
							<label class="instruction_lbl">Specific Instructions:</label>
							<br />

							<textarea class="form-control" id="specificInstruction" name="specificInstruction" rows="6"><?php echo $specificInstructions; ?></textarea>
							

							<label class="pledge_lbl">Test Pledge:</label>

							<textarea class="form-control" id="testPledge" name="testPledge" rows="6"><?php echo $testPledge; ?></textarea>
						</form>
						<div class="row" id="upperButtons">
							<div class="col-md-6">
								<button type="button" class="btn btn-danger btn-block" id="cancelTestBtn"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
							</div>
							
							<div class="col-md-6">	
								<button type="button" class="btn btn-primary btn-block" id="saveTestBtn">Save</button>
							</div>
						</div>
						
						<button type="button" class="btn btn-success btn-block" id="createTestBtn">Create and Publish</button>
					</div>
					
					<div class="col-md-8" id="create_questions">
						<div class="create-questions-text">
							Create Questions
						</div>
					
						<div id="button_sections">
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#MCModal" data-title="MultipleChoice">
								<span class="glyphicon glyphicon-record"></span> Multiple Choice
							</button>

							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#TFModal" data-title="TrueFalse">
								<span class="glyphicon glyphicon-ok"></span> True/False 
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#ATAModal" data-title="AllThatApply">
								<span class="glyphicon glyphicon-check"></span> All that Apply
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#MatchModal" data-title="Matching" >
								<span class="glyphicon glyphicon-th-large"></span> Matching
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#SAModal" data-title="ShortAnswer">
								<span class="glyphicon glyphicon-minus"></span> Short Answer
							</button>
							
							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#EssayModal" data-title="Essay">
								<span class="glyphicon glyphicon-pencil"></span> Essay
							</button>
						</div>
						
						<div class="container-fluid">
							<div class="list-group" id ="testList">
                                

                                <?php
                /***************************************************************************************************/
                /* Modal crap for Victor to mess with                                                              */
                /***************************************************************************************************/
                                    $oldId = 0;
                                    $counter = 0;
                                    $oldQuestionsStatement = $database->prepare($oldQuestionsQuery);
                                    $oldQuestionsStatement->bind_param("s", $newTestId);
                                    $oldQuestionsStatement->bind_result($qid, $qtype, $qvalue, $qtext, $qno, $aid, $atext, $correct, $heading_id, $heading);
                                    $oldQuestionsStatement->execute();
                                    while($oldQuestionsStatement->fetch())
                                    {
                                        // Checks to see if this is a new question, or just a new answer
                                        if($oldId != $qid)
                                        {
                                            $counter++;
                                            $modalId++;
                                            // Modals here will save changes rather than create questions
                                            if($qtype == "True/False")
                                            {
                                                // Echo True/False with info inside
                                                // This just puts the box thing on test page... not a modal
                                                echo '<a href="#" id="list_group'.$qno.'" class="list-group-item" data-toggle="modal" > <h4 class="list-group-item-heading">'.$counter. '. '.$qtype.'</h4> <p class="list-group-item-text">' . $qtext . '</p></a>';
                                                echo '<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'.$qno.'" onclick="removeQuestion('.$qno.')"><span class="glyphicon glyphicon-trash"></span></button>';
                                            }
                                            else if($qtype == "Multiple Choice")
                                            {
                                                // Echo multiple choice modal with info inside
                                                echo '<a href="#" id="list_group'.$qno.'" class="list-group-item" data-toggle="modal"> <h4 class="list-group-item-heading">'.$counter. '. '.$qtype.'</h4> <p class="list-group-item-text">' . $qtext . '</p></a>';
                                                echo '<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'.$qno.'" onclick="removeQuestion('.$qno.')"><span class="glyphicon glyphicon-trash"></span></button>';
                                            }
                                            else if($qtype == "All That Apply")
                                            {
                                                echo '<a href="#" id="list_group'.$qno.'" class="list-group-item" data-toggle="modal" > <h4 class="list-group-item-heading">'.$counter. '. '.$qtype.'</h4> <p class="list-group-item-text">' . $qtext . '</p></a>';
                                                echo '<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'.$qno.'" onclick="removeQuestion('.$qno.')"><span class="glyphicon glyphicon-trash"></span></button>';
                                                // Echo All that Apply modal with info inside
                                            }
                                            else if($qtype == "Matching")
                                            {
                                                echo '<a href="#" id="list_group'.$qno.'" class="list-group-item" data-toggle="modal" > <h4 class="list-group-item-heading">'.$counter. '. '.$qtype.'</h4> <p class="list-group-item-text">'.$qtext.'</p></a>';
                                                echo '<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'.$qno.'" onclick="removeQuestion('.$qno.')"><span class="glyphicon glyphicon-trash"></span></button>';
                                                // Echo Matching modal with info inside
                                            }
                                            else if($qtype == "Short Answer")
                                            {
                                                // Echo Short Answer Modal with info inside
												echo '<a href="#" id="list_group'.$qno.'" class="list-group-item" data-toggle="modal" data-target="#SAModal'.$modalId.'"> <h4 class="list-group-item-heading">'.$counter. '. '.$qtype.'</h4> <p class="list-group-item-text">' . $qtext . '</p></a>';
                                                echo '<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'.$qno.'" onclick="removeQuestion('.$qno.')"><span class="glyphicon glyphicon-trash"></span></button>';
                                            }
                                            else
                                            {
                                                // Echo Essay modal with info inside
												echo '<a href="#" id="list_group'.$qno.'" class="list-group-item" data-toggle="modal" data-target="#EssayModal'.$modalId.'"> <h4 class="list-group-item-heading">'.$counter. '. '.$qtype.'</h4> <p class="list-group-item-text">' . $qtext . '</p></a>';
												echo '<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'.$qno.'" onclick="removeQuestion('.$qno.')"><span class="glyphicon glyphicon-trash"></span></button>';				
                                            }
                                        }
                                        $oldId = $qid;
                                    }
                                    $oldQuestionsStatement->close();
                                ?>
							</div>
						</div>
					</div>		
                </div>
			
				<!-- Short Answer Modal -->
					<div id="SAModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header modal_header_color">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title"><span class="glyphicon glyphicon-minus"></span> Short Answer</h4>
								</div>
								<div class="modal-body">
									<form name="shortAnswerForm" id="shortAnswerForm" action="testCreationPage.php" method="post">
										<div class="form-group">
											<div class="point_value_section">
												<label for="short_answer_point_value" class="control-label">Point Value:&nbsp;</label>
												<input type="number" id="short_answer_point_value">
											</div>
											<hr />
											<div class="question_section">
											</div>
											<div class="form-group">
												<label for="short_answer_question" class="control-label">Question:</label>
												<input type="text" class="form-control" id="short_answer_question">
											</div>
											<div class="form-group">
												<label for="short_answer_answer" class="control-label">Answer:</label>
												<textarea type="text" class="form-control" id="short_answer_answer" rows="2"></textarea>
											</div>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="SACancelBtn">Cancel</button>
									<button type="submit" class="btn btn-primary " data-dismiss="modal" id="SABtn" name="create" value="create" >Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- Essay Modal -->
					<div id="EssayModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header modal_header_color">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title"><span class="glyphicon glyphicon-pencil"></span> Essay</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<div class="point_value_section">
												<label for="essay_point_value" class="control-label">Point Value:&nbsp;</label>
												<input type="number" id="essay_point_value">
											</div>
										</div>
										<hr />
										<div class="form-group">
											<label for="essay_question" class="control-label">Question:</label>
											<input type="text" class="form-control" id="essay_question">
										</div>
										<div class="form-group">
											<label for="essay_answer" class="control-label">Answer:</label>
											<textarea type="text" class="form-control" id="essay_answer" rows="8"> </textarea>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="ECancelBtn">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" id="EBtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- T/F Modal-->
					<div id="TFModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header modal_header_color">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title"><span class="glyphicon glyphicon-ok"></span> True/False</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<div class="point_value_section">
												<label for="tf_question_point_value" class="control-label">Point Value:&nbsp;</label>
												<input type="number" id="tf_question_point_value" />
											</div>
											<hr />
											<div class="question_section">
												<label for="tf_question" class="control-label">Question:</label>
												<input type="text" class="form-control" id="tf_question" />
											</div>
										</div>
										
										<div class="form-group">
											<div class="radio">
												<label><input type="radio" class="optradio" name="optradio" value="true" />True</label>
											</div>
											<div class="radio">
												<label><input type="radio" class="optradio" name="optradio" value="false" />False</label>
											</div>
										</div>
										
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal" id="TFCancelBtn">Cancel</button>
											<button type="button" class="btn btn-primary" data-dismiss="modal" id="TFBtn" onclick="">Create Question</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
				<!-- Multiple Choice Modal-->
					<div id="MCModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header modal_header_color">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title"><span class="glyphicon glyphicon-record"></span> Multiple Choice</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<div class="point_value_section">
												<label for="mc_point_value" class="control-label">Point Value:&nbsp;</label>
												<input type="number" id="mc_point_value" />
											</div>
											<hr />
											<div class="question_section">
												<label for="mc_question" class="control-label">Question:</label>
												<input type="text" class="form-control" id="mc_question" />
											</div>
										</div>
									<label class="control-label">Answer:</label>
									<div class="answers_section">
										<div class="form-group">
											<div class="row choices">
												<div class="col-md-1">
													<input type="radio" name="mc_answer" id="mc_answer0" value="multipleRadio0" class="multipleRadio" />
												</div>
												<div class="col-md-10">
													<input type="text" class="form-control multipleTextboxes" id="multipleText0" name="multipleText0" />
												</div>
											</div>
										</div>
										<div class="form-group">
												<div class="row reduce_margin_top choices">
													<div class="col-md-1" id="MC_answers">
														<input type="radio" name="mc_answer" id="mc_answer1" value="multipleRadio1" class="multipleRadio" />
													</div>
													<div class="col-md-10" id="MC_text_boxes">
														<input type="text" class="form-control multipleTextboxes" id="multipleText1" name="multipleText1"/>
													</div>
													<div class="col-md-1" id="MC_add_trash_btn">
													</div>
												</div>

										</div>
									</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_MC">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="MCCancelBtn">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" id="MCBtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- All that Apply Modal-->
					<div id="ATAModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header modal_header_color">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title"><span class="glyphicon glyphicon-check"></span> All that Apply</h4>
								</div>
								<div class="modal-body">
									<form role="form" onsubmit="return validate(this)">
										<div class="form-group">
											<div class="point_value_section">
												<label for="ata_point_value" class="control-label">Point Value:&nbsp;</label>
												<input type="number" id="ata_point_value" />
											</div>
										</div>
										<hr />
										<div class="form-group">
											<label for="ata_question" class="control-label">Question:</label>
											<input type="text" class="form-control" id="ata_question" />
										</div>
										<label class="control-label">Answer:</label>
										<div class="answers_section">
											<div class="form-group">
												<div class="row choices">
													<div class="col-md-1">
														<input type="checkbox" name="ata_answer" id="ata_answer_cb0" class="ata_cb" />
													</div>
													<div class="col-md-10">
														<input type="text" id="ata_answer0" class="ata_tb form-control" />
													</div>
												</div>
											</div>
											<div class="form-group">
												<div>
													<div class="row reduce_margin_top choices">
														<div class="col-md-1" id="ATA_cbs">
															<input type="checkbox" name="ata_answer" id="ata_answer_cb1" class="ata_cb" />
														</div>
														<div class="col-md-10" id="ATA_answers">
															<input type="text" id="ata_answer1" class="ata_tb form-control" />
														</div>
														<div class="col-md-1" id="ATA_add_trash_btn">
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_ATA">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="ATACancelBtn">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" id="ATABtn" onclick="">Create Question</button>
								</div>
							</div>
						</div>
					</div>
					
				<!-- Matching Modal-->
					<div id="MatchModal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header modal_header_color">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title"><span class="glyphicon glyphicon-th-large"></span> Matching</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="m_heading" class="control-label">Section Heading:</label>
											<input type="text" class="form-control" id="m_heading" />
										</div>
										<div class="form-group">
											<div class="point_value_section">		
												<label for="m_point_value" class="control-label">Point Value (ea. question):&nbsp;</label>
												<input type="number" id="m_point_value" />
											</div>
										</div>
										<hr />
										<div class="row reduce_margin_top">
											<div class="col-md-9">
												<div class="form-group">
													<label class="control-label test">Question:</label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label reduce_margin_top">Match:</label>
												</div>
											</div>
										</div>
										<div class="reduce_margin_bottom">
										</div>
										<div class="row">
											<div class="col-md-9" >
												<div class="form-group" id="add_match_question">
													<input type="text" class="m_question form-control" id="match_question_tb0" />
												</div>
											</div>
											<div class="col-md-2" >
												<div class="form-group" id="add_match_question_letter">
													<input type="text" class="m_question_letter form-control" id="match_question_letter_tb0" />
												</div>
											</div>
											<div class="col-md-1" id="add_match_question_trash_btn">
											</div>
										</div>
										
										<button type="button" class="btn btn-default" aria-hidden="true" id="add_match_question_btn">Add Item +</button>
										
										<div class="row">
											<div class="col-md-9">
												<div class="form-group">
													<label class="control-label">Answer:</label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label">Letter:</label>
												</div>
											</div>
										</div>
										<div class="reduce_margin_bottom">
										</div>
										<div class="row">
											<div class="col-md-9"> 
												<div class="form-group" id="add_match_answer">
													<input type="text" class="m_answer form-control" id="match_answer_tb0" />
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" id="add_match_answer_letter">
													<input type="text" class="m_answer_letter form-control" id="match_answer_letter_tb0" />
												</div>
											</div>
											<div class="col-md-1" id="add_match_answer_trash_btn">
											</div>
										</div>
										</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_match_answer_btn">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="MCancelBtn">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" id="MBtn">Create Question</button>
								</div>
							</div>
						</div>
					</div>				
			</div>    				
		</div>	
        
    <script>
    function removeQuestion(qno)
    {
        $('#list_group'+qno).remove();
        $('#remove_Question'+qno).remove();
        
        $.post("TestQuestionScripts/deleteQuestion.php",
			{
				qno:qno
			},
        function(data)
		{
        
		});
    }
    </script>
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
		var testName;
		var dateBegin;
		var dateEnd;
		var timeLimit;
		var specificInstruction;
		var testPledge;
		var newTestId = '<?php echo $newTestId; ?>';
		var maxPoints;
        var classId = '<?php echo $clid; ?>';
        var teacherId = '<?php echo $id; ?>';
		
		$("#saveTestBtn").click(function()
		{
			testName = $("#testName").val();
			dateBegin = $("#dateBegin").val();
			dateEnd = $("#dateEnd").val();
			timeLimit = $("#timeLimit").val();
			specificInstruction = $("#specificInstruction").val();
			testPledge = $("#testPledge").val();
			maxPoints = $("#maxPoints").val();
			alert("Test Saved");
			
			$.post("TestButtonScripts/saveButton.php",
			{
				testName:testName,
				dateBegin:dateBegin,
				dateEnd:dateEnd,
				timeLimit:timeLimit,
				specificInstruction:specificInstruction,
				testPledge:testPledge,
				newTestId:newTestId,
				maxPoints:maxPoints,
            classId:classId,
            teacherId:teacherId
			},
		function(data)
		{
			
		});
			
		});
	});
	</script>
	
    <script>
    $(document).ready(function()
	{
        var newTestId = '<?php echo $newTestId; ?>';
        var classId = '<?php echo $clid; ?>';
        
        $("#createTestBtn").click(function()
		{
			alert("Test published!");
			$.post("TestButtonScripts/createButton.php",
			{
                newTestId:newTestId,
                classId:classId
			},
        function(data)
        {
    
        });
        });
	});
    </script>
    
    <script>
    $(document).ready(function()
	{
        $("#cancelTestBtn").click(function()
		{
            window.location = "teacherClassPage.php?classId=" + '<?php echo $classId; ?>';
        });
		$("#backToClass").click(function()
		{
            window.location = "teacherClassPage.php?classId=" + '<?php echo $classId; ?>';
        });
    });
    </script>
	
	<!-- Add matching JS -->
	<script>
		var mQuestionCounter = 0;
		var mAnswerCounter = 0;
		var matchingQuestionArray = [0];
		var matchingAnswerArray = [0];
		$(document).ready(function()
		{
			$("#add_match_question_btn").click(function()
			{
				$("#add_match_question").append('<input type="text" class="m_question form-control" id="match_question_tb'+(mQuestionCounter+1)+'">');
				$("#add_match_question_letter").append('<input type="text" class="m_question_letter form-control" id="match_question_letter_tb'+(mQuestionCounter+1)+'">');
				$("#add_match_question_trash_btn").append('<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_match_question'+(mQuestionCounter+1)+'" onclick="removeMatchingQuestion('+(mQuestionCounter+1)+')"><span class="glyphicon glyphicon-trash"></span></button>');
				mQuestionCounter++;
                matchingQuestionArray.push(mQuestionCounter);
			});
			
			$("#add_match_answer_btn").click(function()
			{
                $("#add_match_answer").append('<input type="text" class="m_answer form-control" id="match_answer_tb'+(mAnswerCounter+1)+'">');
				$("#add_match_answer_letter").append('<input type="text" class="m_answer_letter form-control" id="match_answer_letter_tb'+(mAnswerCounter+1)+'">');
				$("#add_match_answer_trash_btn").append('<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_match_answer'+(mAnswerCounter+1)+'" onclick="removeMatchingAnswer('+(mAnswerCounter+1)+')"><span class="glyphicon glyphicon-trash"></span></button>');
                mAnswerCounter++;
				matchingAnswerArray.push(mAnswerCounter);
            });
			
			$("#MCancelBtn").click(function()
			{
				// Resets Matching Values
				for(mQuestionCounter; mQuestionCounter > 0; mQuestionCounter--)
				{
					$('#match_question_tb'+mQuestionCounter).remove();
					$('#match_question_letter_tb'+mQuestionCounter).remove();
					$('#remove_match_question'+mQuestionCounter).remove();
				}
				for(mAnswerCounter; mAnswerCounter > 0; mAnswerCounter--)
				{
					$('#match_answer_tb'+mAnswerCounter).remove();
					$('#match_answer_letter_tb'+mAnswerCounter).remove();
					$('#remove_match_answer'+mAnswerCounter).remove();
				}
					matchingQuestionArray = [0];
					matchingAnswerArray = [0];
					$('#match_question_tb0').val("");
					$('#match_question_letter_tb0').val("");
					$('#match_answer_tb0').val("");
					$('#match_answer_letter_tb0').val("");
					$('#m_heading').val("");
					$('#m_point_value').val("");		
			});
		});
		function removeMatchingQuestion(mQuestion)
		{
			$('#match_question_tb'+mQuestion).remove();
			$('#match_question_letter_tb'+mQuestion).remove();
			$('#remove_match_question'+mQuestion).remove();
			matchingQuestionArray.splice(mQuestion,1);
		}
		
		function removeMatchingAnswer(mAnswer)
		{
			$('#match_answer_tb'+mAnswer).remove();
			$('#match_answer_letter_tb'+mAnswer).remove();
			$('#remove_match_answer'+mAnswer).remove();
			matchingAnswerArray.splice(mAnswer,1);
		}
	</script>
	
	<!-- All that Apply JS -->
	<script>
	var ATACounter = 1;
	var testATAArray = [0,1];
		$(document).ready(function()
		{
			$("#add_ATA").click(function()
			{
				// adds radio buttons to ATA modal
				$("#ATA_cbs").append('<input type="checkbox" name="ata_answer" id="ata_answer_cb'+(ATACounter+1)+'" class="ata_cb" />');
				// adds text boxes to ATA modal
				$("#ATA_answers").append('<input type="text" id="ata_answer'+(ATACounter+1)+'" class="ata_tb form-control" />');
				// adds trash button to ATA modal
				$("#ATA_add_trash_btn").append('<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_ata'+(ATACounter+1)+'" onclick="removeATAQuestion('+(ATACounter+1)+')"><span class="glyphicon glyphicon-trash"></span></button>');
				ATACounter++;
				testATAArray.push(ATACounter);
		});
		
			$("#ATACancelBtn").click(function()
			{
				// Resets ATA Values
				for(ATACounter; ATACounter > 1; ATACounter--)
				{
					$('#ata_answer_cb'+ATACounter).remove();
					$('#ata_answer'+ATACounter).remove();
					$('#remove_ata'+ATACounter).remove();
				}
				$('input:checkbox').removeAttr('checked');;
				$('#ata_answer0').val("");
				$('#ata_answer1').val("");
				$('#ata_question').val("");
				$('#ata_point_value').val("");
				testATAArray = [0,1];
		});
	});
	function removeATAQuestion(questionNum)
	{
		testATAArray.splice(questionNum,1);
		$('#ata_answer_cb'+questionNum).remove();
		$('#ata_answer'+questionNum).remove();
		$('#remove_ata'+questionNum).remove();
	}
	</script>
	
	<!-- Multiple Choice JS -->
	<script >
	var MCCounter = 1;
	var cloned;
	var testMCArray = [0,1];
    var classId = '<?php echo $classId; ?>';
		$(document).ready(function(){
		
			$("#add_MC").click(function(){
				// adds radio buttons to mc modal
				$("#MC_answers").append('<input type="radio" name="mc_answer" id="mc_answer'+(MCCounter+1)+'" value="multipleRadio'+(MCCounter+1)+'" class="multipleRadio" />');
				// adds text boxes to mc modal
				$("#MC_text_boxes").append('<input type="text" class="form-control multipleTextboxes" id="multipleText'+(MCCounter+1)+'" name="multipleText'+(MCCounter+1)+'"/>');
				// adds trash button to mc modal
				$("#MC_add_trash_btn").append('<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_MC'+(MCCounter+1)+'" onclick="removeMCQuestion('+(MCCounter+1)+')"><span class="glyphicon glyphicon-trash"></span></button>');
				MCCounter++;
				testMCArray.push(MCCounter);
		});
			$("#MCCancelBtn").click(function(){
				// Resets MC Values
				for(MCCounter; MCCounter > 1; MCCounter--)
				{
					$('#mc_answer'+MCCounter).remove();
					$('#multipleText'+MCCounter).remove();
					$('#remove_MC'+MCCounter).remove();
				}
					$('#mc_answer0').val("");
					$('#multipleText0').val("");	
					$('#mc_answer1').val("");
					$('#multipleText1').val("");
					$('#mc_question').val("");
					$('#mc_point_value').val("");
					testMCArray = [0,1];
		});
	});
	
	function removeMCQuestion(questionNum)
	{
		testMCArray.splice(questionNum,1);
		$('#mc_answer'+questionNum).remove();
		$('#multipleText'+questionNum).remove();
		$('#remove_MC'+questionNum).remove();
	}
	</script>
	
	<script>	
		var testId = '<?php echo $newTestId; ?>';
		var counter = '<?php echo $counter; ?>';
		$(document).ready(function()
		{
			/***********************************************************/
			/* Short answer stuff                                      */
			/***********************************************************/
			$("#SABtn").click(function()
			{
				var pointValue = $("#short_answer_point_value").val();
				var question = $("#short_answer_question").val();
				var answer = $("#short_answer_answer").val();
			
				$.post("TestQuestionScripts/essayAndShortAnswer.php",
				{
					pointValue:pointValue,
					question:question,
                    classId:classId,
					answer:answer,
					testId:testId,
					questionType:"Short Answer"
				},
				function(data)
				{
					$("#testList").append('<div class="list-group-item" id="list_group'+data+'"> <h4 class="list-group-item-heading">SHORT ANSWER</h4> <br /><p class="list-group-item-text">' + question + ' (' + pointValue +')</p></div>'
                    );
                    $("#list_group"+data).append('<button type="button" class="btn btn-default btn-md q_trash_button" aria-hidden="true" id="remove_Question'+data+'" onclick="removeQuestion('+data+')"><span class="glyphicon glyphicon-trash"></span></button>');
				
                    $("#list_group"+data).append('<div><b>Answer</b>: ' + answer + '<img src="images/sign.png" /></div>');
				});
				$('#short_answer_question').val("");
				$('#short_answer_answer').val("");
				$('#short_answer_point_value').val("");
				
			});
			$("#SACancelBtn").click(function()
			{
				$('#short_answer_question').val("");
				$('#short_answer_answer').val("");
				$('#short_answer_point_value').val("");
				
			});
			/***********************************************************/
			/* Matching stuff                                          */
			/***********************************************************/
			$("#MBtn").click(function()
			{
				var pointValue = $("#m_point_value").val();
				var heading = $("#m_heading").val();
				
				var questionArray = [];
				var questionLetterArray = [];
				var answerArray = [];
				var answerLetterArray = [];
				
				var i = 0;
				// Loop and store questions
				$('.m_question').each(function() {
					questionArray[i] = $(this).val();
					i++;									
				});
				
				i = 0;
				// Loop and store question letters
				$('.m_question_letter').each(function() {
					questionLetterArray[i] = $(this).val();
					i++;					
				});
				
				i = 0;
				// Loop and store answers
				$('.m_answer').each(function() {
					answerArray[i] = $(this).val();
					i++;					
				});
				
				i = 0;
				// Loop and store answer letters
				$('.m_answer_letter').each(function() {
					answerLetterArray[i] = $(this).val();
					i++;	
				});
				
				$.post("TestQuestionScripts/matching.php",
				{
					pointValue:pointValue,
					questionType:"Matching",
					"questions[]":questionArray,
					"questionLetters[]":questionLetterArray,
                    classId:classId,
					"answers[]":answerArray,
					"answerLetters[]":answerLetterArray,
					testId:testId,
					heading:heading
				},
				function(data)
				{

					$("#testList").append('<div class="list-group-item" id="list_group'+data+'"> <h4 class="list-group-item-heading">'+(++counter)+'. Matching</h4> <p class="list-group-item-text">'+ heading + '</p></div>'
                    );
                    $("#testList").append('<button type="button" class="btn btn-default btn-md trash_button" aria-hidden="true" id="remove_Question'+data+'" onclick="removeQuestion('+data+')"><span class="glyphicon glyphicon-trash"></span></button>');
				});

				for(mQuestionCounter; mQuestionCounter > 0; mQuestionCounter--)
				{
					$('#match_question_tb'+mQuestionCounter).remove();
					$('#match_question_letter_tb'+mQuestionCounter).remove();
					$('#remove_match_question'+mQuestionCounter).remove();
				}
				for(mAnswerCounter; mAnswerCounter > 0; mAnswerCounter--)
				{
					$('#match_answer_tb'+mAnswerCounter).remove();
					$('#match_answer_letter_tb'+mAnswerCounter).remove();
					$('#remove_match_answer'+mAnswerCounter).remove();
				}
					matchingQuestionArray = [0];
					matchingAnswerArray = [0];
					$('#match_question_tb0').val("");
					$('#match_question_letter_tb0').val("");
					$('#match_answer_tb0').val("");
					$('#match_answer_letter_tb0').val("");
					$('#m_heading').val("");
					$('#m_point_value').val("");
			});
			/***********************************************************/
			/* Multiple choice stuff                                   */
			/***********************************************************/
			$("#MCBtn").click(function(){
				var pointValue = $("#mc_point_value").val();
				var question = $("#mc_question").val();
				var multipleChoiceArray = [];
				var multipleTextArray = [];
				// check for multiple choice radios		
				for(i = 0; i < testMCArray.length; i++)
				{
					if ($('#mc_answer'+(testMCArray[i])).is(':checked'))
					{
						multipleChoiceArray[i] = 1;
					}
					else
					{
						multipleChoiceArray[i] = 0;	
					}
				}
				
				
				// Get and store the possible answers from the multiple choice type 
				for(i = 0; i < testMCArray.length; i++)
				{
					multipleTextArray[i] = document.getElementById("multipleText" + testMCArray[i]).value;
				}
				
				$.post("TestQuestionScripts/multipleChoiceTrueFalseAllThatApply.php",
				{
					pointValue:pointValue,
                    classId:classId,
					questionType:"Multiple Choice",
					question:question,
					"parameters[]":multipleChoiceArray,
					"textBoxes[]":multipleTextArray,
					testId:testId
				},
				function(data)
				{
					$("#testList").append('<a href="#" id="list_group'+data+'" class="list-group-item"> <h4 class="list-group-item-heading">MULTIPLE CHOICE</h4> <br /><p class="list-group-item-text">' + question + ' (' + pointValue +')</p></a>'
                    );
					alert(data);
                    $("#list_group"+data).append('<button type="button" class="btn btn-default btn-md q_trash_button" aria-hidden="true" id="remove_Question'+data+'" onclick="removeQuestion('+data+')"><span class="glyphicon glyphicon-trash"></span></button>');
					alert(data);

					for (i = 0; i < multipleChoiceArray.length; i++)
					{
						if(multipleChoiceArray[i] == true)
						{
							$("#list_group"+data).append('<div><input type="radio" disabled checked="checked" /> ' + multipleTextArray[i] + ' <img src="images/sign.png" /></div>');
						}
						else
						{
							$("#list_group"+data).append('<div><input type="radio" disabled /> ' + multipleTextArray[i] + '</div>');
						}
					}
				});
				
				// Resets MC Values
				for(MCCounter; MCCounter > 1; MCCounter--)
				{
					$('#mc_answer'+MCCounter).remove();
					$('#multipleText'+MCCounter).remove();
					$('#remove_MC'+MCCounter).remove();
				}
					$('input[name="mc_answer"]').prop('checked', false);
					$('#mc_answer0').val("");
					$('#multipleText0').val("");	
					$('#mc_answer1').val("");
					$('#multipleText1').val("");
					$('#mc_question').val("");
					$('#mc_point_value').val("");
					testMCArray = [0,1];
            });
			
			/***********************************************************/
			/* All that apply stuff                                    */
			/***********************************************************/
			$("#ATABtn").click(function(){
				var pointValue = $("#ata_point_value").val();
				var question = $("#ata_question").val();
				var ataArray = [];
				var ataTextArray = [];
                var question_no = 0;
				//check for all that apply checkboxes
				var i = 0;
				$('.ata_cb').each(function() {
					
					// If true, assign
					if($(this).is(':checked'))
					{
						ataArray[i] = 1;
					  	
					}
					// Else false, assign
					else
					{
						ataArray[i] = 0;				
					}
					i++;		
				});
				// Get and store the possible answers from the multiple choice type
				for(i = 0; i < testATAArray.length; i++)
				{
					ataTextArray[i] = document.getElementById("ata_answer" + i).value;
				}
				
				$.post("TestQuestionScripts/multipleChoiceTrueFalseAllThatApply.php",
				{
					pointValue:pointValue,
                    classId:classId,
					questionType:"All That Apply",
					question:question,
					"parameters[]":ataArray,
					"textBoxes[]":ataTextArray,
					testId:testId
				},
				function(data)
				{
                    $("#testList").append('<a href="#" id="list_group'+data+'" class="list-group-item"> <h4 class="list-group-item-heading">ALL THAT APPLY</h4> <br /><p class="list-group-item-text">' + question + ' (' + pointValue +')</p></a>'
                    );
                    $("#list_group"+data).append('<button type="button" class="btn btn-default btn-md q_trash_button" aria-hidden="true" id="remove_Question'+data+'" onclick="removeQuestion('+data+')"><span class="glyphicon glyphicon-trash"></span></button>');
				
                    for (i = 0; i < ataArray.length; i++)
					{
						if(ataArray[i] == true)
						{
							$("#list_group"+data).append('<div><input type="checkbox" disabled checked="checked" /> ' + ataTextArray[i] + ' <img src="images/sign.png" /></div>');
						}
						else
						{
							$("#list_group"+data).append('<div ><input type="checkbox" disabled /> ' + ataTextArray[i] + '</div>');
						}
					}
				});
				// Resets ATA Values
				for(ATACounter; ATACounter > 1; ATACounter--)
				{
					$('#ata_answer_cb'+ATACounter).remove();
					$('#ata_answer'+ATACounter).remove();
					$('#remove_ata'+ATACounter).remove();
				}
				$('input:checkbox').removeAttr('checked');
				$('#ata_answer0').val("");
				$('#ata_answer1').val("");
				$('#ata_question').val("");
				$('#ata_point_value').val("");
				testATAArray = [0,1];
			});
			
			
			/***********************************************************/
			/* True/false stuff                                        */
			/***********************************************************/
			$("#TFBtn").click(function(){
				var pointValue = $("#tf_question_point_value").val();
				var question = $("#tf_question").val();
				var trueFalseArray = [];
				var answerText = ["true", "false"];
				
				//check for true/false radios 
				var i = 0;
				$('.optradio').each(function() {
						 		 
					if($(this).is(':checked'))
					{
						trueFalseArray[i] = 1;
					 
					}
					else
					{
						trueFalseArray[i] = 0;				
					}
					i++;		
				});
			
				$.post("TestQuestionScripts/multipleChoiceTrueFalseAllThatApply.php",
				{
					
					questionType:"True/False",
					pointValue:pointValue,
                    classId:classId,
					question:question,
					"parameters[]":trueFalseArray,
					"textBoxes[]":answerText,
					testId:testId
					
				},
				function(data)
				{
					$("#testList").append('<a href="#" id="list_group'+data+'" class="list-group-item"> <h4 class="list-group-item-heading">TRUE/FALSE</h4><br /> <p class="list-group-item-text">' + question + ' (' + pointValue + ')</p></a>'
                    );
                    $("#list_group"+data).append('<button type="button" class="btn btn-default btn-md q_trash_button" aria-hidden="true" id="remove_Question'+data+'" onclick="removeQuestion('+data+')"><span class="glyphicon glyphicon-trash"></span></button>');
				
                    for (i = 0; i < trueFalseArray.length; i++)
					{
						if(trueFalseArray[i] == true)
						{
							$("#list_group"+data).append('<div><input type="radio" disabled checked="checked" /> ' + answerText[i] + ' <img src="images/sign.png" /></div>');
						}
						else
						{
							$("#list_group"+data).append('<div><input type="radio" disabled /> ' + answerText[i] + '</div>');
						}
					}
				});
				$('#tf_question').val("");
				$('#tf_question_point_value').val("");
				$('input[name="optradio"]').prop('checked', false);
            });
			$("#TFCancelBtn").click(function(){	
				$('#tf_question').val("");
				$('#tf_question_point_value').val("");
				$('input[name="optradio"]').prop('checked', false);
			});
			
			/***********************************************************/
			/* Essay stuff                                             */
			/***********************************************************/
			$("#EBtn").click(function(){
					
				var pointValue = $("#essay_point_value").val();
				var question = $("#essay_question").val();
				var answer = $("#essay_answer").val();
				
				$.post("TestQuestionScripts/essayAndShortAnswer.php",
				{
					pointValue:pointValue,
					question:question,
					answer:answer,
                    classId:classId,
					testId:testId,
					questionType:"Essay"
				},
				function(data)
				{
					$("#testList").append('<a href="#" id="list_group'+data+'" class="list-group-item"> <h4 class="list-group-item-heading">ESSAY</h4> <br /><p class="list-group-item-text">' + question + ' (' + pointValue + ')</p></a>'
                    );
                    $("#list_group"+data).append('<button type="button" class="btn btn-default btn-md q_trash_button" aria-hidden="true" id="remove_Question'+data+'" onclick="removeQuestion('+data+')"><span class="glyphicon glyphicon-trash"></span></button>');
				
                    $("#list_group"+data).append('<div><b>Answer</b>: ' + answer + '<img src="images/sign.png" /></div>');
				});	
				$('#essay_answer').val("");
				$('#essay_question').val("");
				$('#essay_point_value').val("");
			});
			
			$("#ECancelBtn").click(function(){	
				$('#essay_answer').val("");
				$('#essay_question').val("");
				$('#essay_point_value').val("");
			});
		});
	</script>
</body>

</html>
