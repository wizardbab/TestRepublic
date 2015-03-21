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
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/teacher-create-test.css" rel="stylesheet">
	
	   <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	
	<!-- Custom Test Creation JavaScript --> 
	<script src="js/testCreation.js"></script>
	
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	
</head>
<?php
session_start();

// Include the constants used for the db connection
require("constants.php");

// Gets the class id appended to url from teacherMainPage.php
$id = $_SESSION['username']; // Just a random variable gotten from the URL
$classId = $_SESSION['classId'];
$sessionTestId = $_SESSION['testId'];

//if(!is_null($_POST['testId']))
    $sessionTestId = $_POST['testId'];
    
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

global $newTestId;
global $multipleChoiceInputId;
global $multipleChoiceRadioId;

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
						<span class="TestRepublic" id="backToClass">Back to <?php echo $classId ?></span>
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
								<input type="text" id="dateBegin" name="dateBegin" value="<?php echo $startDate; ?>" />
							</label>
							
							<label class="time_lbl">Time:
								<input type="time" />
							</label>
							
							<label class="date_lbl">End Date:&nbsp;
								<input type="text" id="dateEnd" name="dateEnd" value="<?php echo $endDate; ?>" />
							</label>
							
							<label class="time_lbl">Time:
								<input type="time" />
							</label>
							
							<label class="time_limit_lbl">Time Limit:
								<input type="number" id="timeLimit" name="timeLimit" value="<?php echo $timeLimit; ?>" /> minutes
							</label>
							
							<label class="time_limit_lbl">Max Points:
								<input type="number" id="maxPoints" name="maxPoints" value="<?php echo $maxPoints; ?>" /> 
							</label>
							
							<br />
							
							<label class="instruction_lbl">Specific Instructions:</label>
							<br />

							<textarea class="form-control" id="specificInstruction" name="specificInstruction" rows="6"><?php echo $specificInstructions; ?></textarea>
							<p id="test" value="<?php echo $testName; ?>"> Foo </p> 

							<label class="pledge_lbl">Test Pledge:</label>

							<textarea class="form-control" id="testPledge" name="testPledge" rows="6"><?php echo $testPledge; ?></textarea>
						</form>
						<div class="row" id="upperButtons">
							<div class="col-md-6">
								<button type="button" class="btn btn-danger btn-block" id="cancelTestBtn">Cancel</button>
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
									<h4 class="modal-title">Short Answer</h4>
								</div>
								<div class="modal-body">
									<form name="shortAnswerForm" id="shortAnswerForm" action="testCreationPage.php" method="post">
										<div class="form-group">
											<div class="point_value_section">
												<label for="short_answer_point_value" class="control-label">Point Value:&nbsp;</label>
												<input type="text" id="short_answer_point_value">
											</div>
											<hr />
											<div class="question_section">
												<label for="short_answer_question" class="control-label">Question:</label>
												<input type="text" class="form-control" id="short_answer_question">
											</div>
											<label for="short_answer_answer" class="control-label">Answer:</label>
											<textarea type="text" class="form-control" id="short_answer_answer" rows="8"></textarea>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
									<h4 class="modal-title">Essay</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Point Value:</label>
											<input type="text" class="form-control" id="essay_point_value">
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="essay_question">
											<label for="recipient-name" class="control-label">Answer:</label>
											<textarea type="text" class="form-control" id="essay_answer" rows="8"> </textarea>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" id="EBtn" onclick="">Create Question</button>
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
									<h4 class="modal-title">True/False</h4>
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
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
									<h4 class="modal-title">Multiple Choice</h4>
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
										<div class="form-group">
											<label class="control-label">Answer:</label>
											<div class="row">
												<div class="col-md-1">
													<input type="radio" name="mc_answer0" id="mc_answer0" value="multipleRadio0" class="multipleRadio" />
												</div>
												<div class="col-md-11">
													<input type="text" class="form-control multipleTextboxes" id="multipleText0" name="multipleText0" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div id="MC_add_answers">
												<div class="row reduce_margin_top">
													<div class="col-md-1">
														<input type="radio" name="mc_answer0" id="mc_answer1" value="multipleRadio1" class="multipleRadio" />
													</div>
													<div class="col-md-11">
														<input type="text" class="form-control multipleTextboxes" id="multipleText1" name="multipleText1"/>
													</div>
												</div>
											</div>
										</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_MC">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
									<h4 class="modal-title">All that Apply</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="form-group">
											<label for="recipient-name" class="control-label">Point Value:</label>
											<input type="text" class="form-control" id="ata_point_value" />
											<label for="recipient-name" class="control-label">Question:</label>
											<input type="text" class="form-control" id="ata_question" />
										</div>
										<div class="form-group">
											<label for="recipient-name" class="control-label">Answer:</label>
											<br />
											<input type="checkbox" name="ata_answer" id="ata_answer_cb0" class="ata_cb" />
											<input type="text" id="ata_answer0" class="ata_tb" />
										</div>
										<div class="form-group" id="ATA_AddAns">
											<div class="ata_margin">
												<input type="checkbox" name="ata_answer" id="ata_answer_cb1" class="ata_cb"/>
												<input type="text" id="ata_answer1" class="ata_tb" />
											</div>
										</div>
									</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_ATA">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
									<h4 class="modal-title">Matching</h4>
								</div>
								<div class="modal-body">
									<form role="form">
										<div class="row">
											<div class="col-md-10" id="add_match_question">
												<div class="form-group">
													<label for="recipient-name" class="control-label">Section Heading:</label>
													<input type="text" class="form-control" id="m_heading" />
													<label for="recipient-name" class="control-label">Point Value (ea. question):</label>
													<input type="text" class="form-control" id="m_point_value" />
													<label for="recipient-name" class="control-label">Question:</label>
													<input type="text" class="m_question" id="match_question_tb0" />
												</div>
											</div>
											<div class="col-md-2" id="add_match_question_letter">
												<div class="form-group">
													<label for="recipient-name" class="control-label">Match:</label>
													<input type="text" class="m_question_letter" id="match_question_letter_tb0" />
												</div>
											</div>
										</div>
										
										<button type="button" class="btn btn-default" aria-hidden="true" id="add_match_question_btn">Add Item +</button>
										
										<div class="row">
											<div class="col-md-10" id="add_match_answer">
												<div class="form-group">
													<label for="recipient-name" class="control-label">Answer:</label>
													<input type="text" class="m_answer" id="match_answer_tb0" />
												</div>
											</div>
											<div class="col-md-2" id="add_match_answer_letter">
												<div class="form-group">
													<label for="recipient-name" class="control-label">Letter:</label>
													<input type="text" class="m_answer_letter" id="match_answer_letter_tb0" />
												</div>
											</div>
										</div>
										</form>
									<button type="button" class="btn btn-default" aria-hidden="true" id="add_match_answer_btn">Add Item +</button>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" id="MBtn" onclick="">Create Question</button>
								</div>
							</div>
						</div>
					</div>				
			</div>    				
		</div>	

	
			
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
            window.location = "teacherClassPage.php?classId=" + '<?php echo $classId ?>';
        });
		$("#backToClass").click(function()
		{
            window.location = "teacherClassPage.php?classId=" + '<?php echo $classId ?>';
        });
    });
    </script>
    
	<script>
	$(document).ready(function()
	{
		$("#SABtn").click(function()
		{
			var shortAnswerQuestion = $("#short_answer_question").val();
			var shortAnswerAnswer   = $("#short_answer_anwer").val();
			
			
			
		});
	});
	</script>
	
	<!-- Add matching JS -->
	<script>
		$(document).ready(function()
		{
			var c = 0;
			var d = 0;
			var a = 0;
			var b = 0;
			$("#add_match_answer_btn").click(function()
			{
				// Add the text box for a matching answer
				cloned = $('#match_answer_tb' + c );
				$("#match_answer_tb" + c).clone().attr('id', 'match_answer_tb'+(++c )).insertAfter(cloned);
			
				$("#match_answer_tb" + c ).text('match_answer_tb' + c );
				
				// Add the Letter box for the answer
				cloned = $('#match_answer_letter_tb' + d );
				$("#match_answer_letter_tb" + d).clone().attr('id', 'match_answer_letter_tb'+(++d )).insertAfter(cloned);
			
				$("#match_answer_letter_tb" + d ).text('match_answer_letter_tb' + d );
				
			});
			
			$("#add_match_question_btn").click(function()
			{
				// Add the text box for a matching question
				cloned = $('#match_question_tb' + a );
				$("#match_question_tb" + a).clone().attr('id', 'match_question_tb'+(++a )).insertAfter(cloned);
			
				$("#match_question_tb" + a ).text('match_question_tb' + a );
				
				// Add the Match box for the question
				cloned = $('#match_question_letter_tb' + b );
				$("#match_question_letter_tb" + b).clone().attr('id', 'match_question_letter_tb'+(++b )).insertAfter(cloned);
			
				$("#match_question_letter_tb" + b ).text('match_question_letter_tb' + b );
			});
		});
	</script>
	
		<!-- All that Apply JS -->
	<script>
	var ATACounter = 1;
		$(document).ready(function()
		{
			$("#add_ATA").click(function()
			{
				// adds text boxes to ata modal
				cloned = $('#ata_answer' + ATACounter);
				$("#ata_answer" + ATACounter).clone().attr('id', 'ata_answer'+(ATACounter+1)).insertAfter(cloned);
			
				$("#ata_answer" + ATACounter).text('ata_answer' + ATACounter);
				
				
				cloned = $('#ata_answer_cb' + ATACounter );
				$("#ata_answer_cb" + ATACounter).clone().attr('id', 'ata_answer_cb'+(ATACounter+1 )).insertAfter(cloned);
			
				$("#ata_answer_cb" + ATACounter ).text('ata_answer_cb' + ATACounter );
				ATACounter++;
				
			});
		});
	</script>
	
		<!-- Multiple Choice JS -->
		<!-- PROBLEM: in every append, how to generate a different value & id -->
		<!-- class add_margin_mc doesnt work! :'( -->
	<script>
	var MCCounter = 1;
	var cloned;
	
		$(document).ready(function(){
		
			$("#add_MC").click(function(){
			
				// adds radio buttons to mc modal
				cloned = $('#mc_answer' + MCCounter);
				$("#mc_answer" + MCCounter).clone().attr('id', 'mc_answer'+(MCCounter+1)).insertAfter(cloned);
				
				// adds text boxes to mc modal
				cloned = $('#multipleText' + MCCounter );
				$("#multipleText" + MCCounter).clone().attr('id', 'multipleText'+(MCCounter+1)).insertAfter(cloned);
		
				MCCounter++;
		});
	});
	</script>
	<!--
	<script>	
		$(document).ready(function(){
				$("#remove_MC").click(function(){
				MCArray.push('<input type="text" class="form-control" id="Question">');
				MCBtnArray.push(' <button type="button" class="btn btn-default" aria-hidden="true" id="remove_MC">remove item</button>');
			$("#MC_AddAns").append(MCArray[MCCounter]);
			$("#MC_AddAns").append(MCBtnArray[MCCounter]);
		});
	});
	</script>-->
	<script>	
		var testId = '<?php echo $newTestId; ?>';
			
		$(document).ready(function()
		{
			/***********************************************************/
			/* Short answer stuff                                      */
			/***********************************************************/
			$("#SABtn").click(function()
			{
				var pointValue = $("#short_anwer_point_value").val();
				var question = $("#short_answer_question").val();
				var answer = $("#short_answer_answer").val();
			
				$.post("TestQuestionScripts/essayAndShortAnswer.php",
				{
					pointValue:pointValue,
					question:question,
					answer:answer,
					testId:testId,
					questionType:"Short Answer"
				},
				function(data)
				{
					document.getElementById("test").innerHTML = data;
				});
				
				$("#testList").append('div class="list-group-item"> <h4 class="list-group-item-heading">Short Answer</h4> <p class="list-group-item-text">' + question + '</p></div>'
				);

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
					"answers[]":answerArray,
					"answerLetters[]":answerLetterArray,
					testId:testId,
					heading:heading
				},
				function(data)
				{
					document.getElementById("test").innerHTML = data;
				});
				
				$("#testList").append('<div class="list-group-item"> <h4 class="list-group-item-heading">Matching</h4> <p class="list-group-item-text">'+ heading + '</p></div>'
				);

			});

			/***********************************************************/
			/* Multiple choice stuff                                   */
			/***********************************************************/
			$("#MCBtn").click(function(){
				var pointValue = $("#mc_point_value").val();
				var question = $("#mc_question").val();
				var multipleChoiceArray = [];
				var multipleTextArray = [];
				
				<!-- check for multiple choice radios -->
				var i = 0;
				$('.multipleRadio').each(function() {
					
					// If true, assign
					if($(this).is(':checked'))
					{
						multipleChoiceArray[i] = 1;
					  	
					}
					// Else false, assign
					else
					{
						multipleChoiceArray[i] = 0;				
					}
					i++;		
				});
				
				// Get and store the possible answers from the multiple choice type
				for(i = 0; i <= MCCounter; i++)
				{
					multipleTextArray[i] = document.getElementById("multipleText" + i).value;
				}
				
				
				$.post("TestQuestionScripts/multipleChoiceTrueFalseAllThatApply.php",
				{
					pointValue:pointValue,
					questionType:"Multiple Choice",
					question:question,
					"parameters[]":multipleChoiceArray,
					"textBoxes[]":multipleTextArray,
					testId:testId
				},
				function(data)
				{
					document.getElementById("test").innerHTML = data;
				});
				
				$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">Multiple Choice</h4> <p class="list-group-item-text">' + question + '</p></a>'
				);
				
				// Resets MC Values
				for(MCCounter; MCCounter > 1; MCCounter--)
				{
					$('#mc_answer'+MCCounter).remove();
					$('#multipleText'+MCCounter).remove();
				}
				
			});
			
			/***********************************************************/
			/* All that apply stuff                                    */
			/***********************************************************/
			$("#ATABtn").click(function(){
				var pointValue = $("#ata_point_value").val();
				var question = $("#ata_question").val();
				var ataArray = [];
				var ataTextArray = [];
				
				<!-- check for all that apply checkboxes -->
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
				for(i = 0; i <= ATACounter; i++)
				{
					ataTextArray[i] = document.getElementById("ata_answer" + i).value;
				}
				
				
				$.post("TestQuestionScripts/multipleChoiceTrueFalseAllThatApply.php",
				{
					pointValue:pointValue,
					questionType:"All That Apply",
					question:question,
					"parameters[]":ataArray,
					"textBoxes[]":ataTextArray,
					testId:testId
				},
				function(data)
				{
					document.getElementById("test").innerHTML = data;
				});
				
				$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">All That Apply</h4> <p class="list-group-item-text">' + question + '</p></a>'
				);
				
				// Resets MC Values
				for(ATACounter; ATACounter > 1; ATACounter--)
				{
					$('#ata_answer_cb'+ATACounter).remove();
					$('#ata_answer'+ATACounter).remove();
				}
				
			});
			
			
			/***********************************************************/
			/* True/false stuff                                        */
			/***********************************************************/
			$("#TFBtn").click(function(){
				var pointValue = $("#tf_question_point_value").val();
				var question = $("#tf_question").val();
				var trueFalseArray = [];
				var answerText = ["true", "false"];
				
				<!-- check for true/false radios -->
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
					question:question,
					"parameters[]":trueFalseArray,
					"textBoxes[]":answerText,
					testId:testId
					
				},
				function(data)
				{
					document.getElementById("test").innerHTML = data;
				});
				
				$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">True/False</h4> <p class="list-group-item-text">' + question + '</p></a>'
				);
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
					testId:testId,
					questionType:"Essay"
				},
				function(data)
				{
					document.getElementById("test").innerHTML = data;
				});
				
				$("#testList").append('<a href="#" class="list-group-item"> <h4 class="list-group-item-heading">Essay</h4> <p class="list-group-item-text">' + question + '</p></a>'
				);
			
			});
		});
	</script>

</body>

</html>
