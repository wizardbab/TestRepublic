<!DOCTYPE html>
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

   

   <!-- Custom Fonts -->
   <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

   <!-- Form Validation Includes -->
   <link href="css/validation_page.css" rel="stylesheet" type="text/css">
   <script type="text/javascript" src="js/validation.js"></script>

   <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	<!-- Multi Select -->
	<link href="css/multi-select.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="js/jquery.multi-select.js" type="text/javascript"></script>
    <script src="js/application.js" type="text/javascript"></script>
	
	<!-- Custom CSS -->
   <link href="css/signup2.css" rel="stylesheet">
   
   <script src="path/to/jquery.multi-select.js" type="text/javascript"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>

   <!-- Menu Toggle Script -->
   <script>
   $(document).ready(function()
	{
		
	
	});
	
	function redirect()
	{
		window.location = "redirect.php";
	}
	</script>
	
</head>

<body>
<?php
session_start();

$_SESSION['username'] = null;
// Include the constants used for the db connection
require("constants.php");
// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}
// Query for the list of classes offered
$listClassQuery = "select class_id, class_description from class";
// Query to assign a student an id
$newStudentIdQuery = "select max(student_id) from student"; // We add one to this with each insert
// Insert a student into the student table
$insertStudentQuery = "insert into student values(?, ?, ?, ?, ?)";
// Insert the classes a student is taking into enrollment
$insertEnrollmentQuery = "insert into enrollment values(?, ?)";
$insertTestQuery = "insert into test_list(student_id, test_id) values (?, ?)";
$selectTestIdQuery = "select test_id from test
join test_list using (test_id)
where class_id = ?
group by(test_id)";
@ $database->select_db(DATABASENAME);
// Check to see if anything was entered, if not assign an empty string
$firstName = (isset($_POST['firstName']) ? $_POST['firstName'] : "");
$lastName  = (isset($_POST['lastName']) ? $_POST['lastName'] : "");
$email     = (isset($_POST['email']) ? $_POST['email'] : "");
$password  = (isset($_POST['password']) ? $_POST['password'] : "");
$classes  = (isset($_POST['classes']) ? $_POST['classes'] : "");
global $id;
$success = false;


?>
      <form name="signUpForm" id="signUpForm" action="signUp2.php" onsubmit="return validate(this)" method="post">
         <div class="container-fluid">
            <div class="row">
				<div class="back_section">
					<a href="#" id="back_btn" onclick="window.location.href='login.html'"><span class="glyphicon glyphicon-circle-arrow-left"></span> Back to Log in</a>
				</div>
				<div class="sign_up_text_area">
					<img src="images/newlogo.png" alt="Our Logo" height="60" width="60">
					<span class="sign_up_text">&nbsp; Sign Up</span>
				</div>
			</div>
			<div class="row">
				<h2>Please enter your information.</h2>
			</div>
			
			<label class="survey_style">
                <div class="row">
                    <div class="col-md-4">First Name:</div>
                    <div class="col-md-8"><input type="text" name="firstName" id="firstName" value="" required /></div>
                </div>
            </label><br />
            <label class="survey_style">
                <div class="row">
                    <div class="col-md-4">Last Name:</div>
                    <div class="col-md-8"><input type="text" name="lastName" id="lastName" value="" required /></div>
                </div>
            </label><br />
            <label class="survey_style">
                <div class="row">
                    <div class="col-md-4">&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email:</div>
                    <div class="col-md-8"><input type="email" name="email" id="email" required/></div>
                </div>
            </label><br />
            <label class="survey_style">
                <div class="row">
                    <div class="col-md-4">Password:</div>
                    <div class="col-md-8"><input title="Must contain at least one uppercase, one lowercase, one number, and be a minimum of 8 characters." type="password" name="password" id="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" /></div>
                </div>
            </label>
			
			<div class="row">
				<div class="col-md-6 class_section">Select a Class</div>
				<div class="col-md-6 class_section">Your Classes</div>
			</div>
			
			<div class="row">
				<?php 
                  // List of classes for the user to select from...we'll need to keep track of what class is selected for the db
                  if ($classList = $database->prepare($listClassQuery)) 
                  {
                     // nothing to bind
                  }
                  else {
                     printf("Error message: %s\n", $database->error);
                  }	
                  $classList->bind_result($clid, $clde);
                  $classList->execute();
                  
                  $courseCounter = 1;
                  $classCounter = 1; 
                  $sidebarArray = array();
				  echo '<select multiple class="yo" id="aloha" name="classes[]">';
                  while($classList->fetch())
                  {
                     echo '
							<option value="'.$clid.'">'.$clid.' '.$clde.'</option>
                     ';

                  }
				echo '</select>';
                  $classList->close(); 
                  ?>
			</div>

			
			<div class="row">
				 <button class="btn btn-primary" type="submit" id="create_acc_btn"><span class="glyphicon glyphicon-user"></span> &nbsp;Create Account</button>
			</div>
			   <?php 
                        
                        // We have data; begin validation
                        if(is_array($classes))
                        {
                           // Does a preliminary check for email pattern
                           if(!preg_match('^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+^', $email))
                           {
                              echo "Invalid email ".$email;
                           }
                           // Valid email; validate password
                           else
                           {
                              // Does a preliminary check for required password pattern
                              if(!preg_match('^[[:alpha:]]+[[:digit:]]+^', $password))
                              {
                                 
                                 echo "Need more variety: ";
                              }
                              else
                                 if(!preg_match('^.{8,20}^', $password))
                                 {
                                    echo "Password needs to be between 8-16 characters";
                                 }
                                 
                                 // Valid email and password so we insert into db
                                 else
                                 {
                                    // Assign an id
                                    if ($idStatement = $database->prepare($newStudentIdQuery)) 
                                    {
                                       
                                    }
                                    else
                                    {
                                       printf("Error message: %s\n", $database->error);
                                    }	

                                    $idStatement->bind_result($sid);
                                    $idStatement->execute();
                                    while($idStatement->fetch())
                                    {	
                                       $newId = $sid + 1;
                                    }
                                    $idStatement->close();
                                    
                                    foreach($classes as $a)
                                    {
                                       $testCounter = 0;
                                       $testIdArray[] = null;

                                       //echo '<h1 margin-left: 50px;>' . $a . '</h1></br />';
                                       if($insertEnrollmentStatement = $database->prepare($insertEnrollmentQuery))
                                       {
                                          
                                       }
                                       else
                                       {
                                          printf("Error message: %s\n", $database->error);
                                       }
                                       $insertEnrollmentStatement->bind_param("ss", $newId, $a);
                                       $insertEnrollmentStatement->execute();
                                       $insertEnrollmentStatement->close();

                                       $testCount = count($testIdArray);
                                       for($i = 0; $i < $testCount; $i++)
                                       {
                                          $testIdArray[$i] = null;
                                       }

                                       // Select id for the test
                                       if($selectTestIdStatement = $database->prepare($selectTestIdQuery))
                                       {
                                          
                                       }
                                       else 
                                       {
                                          printf("Error message: %s\n", $database->error);
                                       }

                                       $selectTestIdStatement->bind_param("s", $a);
                                       $selectTestIdStatement->bind_result($tid);
                                       $selectTestIdStatement->execute();
                                       while($selectTestIdStatement->fetch())
                                       {	
                                          $testIdArray[$testCounter++] = $tid;
                                       }
                                       $selectTestIdStatement->close();

                                       if(is_array($testIdArray))
                                       {
                                          foreach($testIdArray as $t)
                                          {
                                             $insertTestStatement = $database->prepare($insertTestQuery);
                                             $insertTestStatement->bind_param("ss", $newId, $t);
                                             $insertTestStatement->execute();
                                             $insertTestStatement->close();
                                          }
                                       }
                                    }

                                    if ($insertStudentStatement = $database->prepare($insertStudentQuery))
                                    {
                                    
                                    }
                                    else{
                                       printf("Error message: %s\n", $database->error);
                                    }
                                    $insertStudentStatement->bind_param("sssss", $newId, $firstName, $lastName, $password, $email);
                                    $insertStudentStatement->execute();
                                    $insertStudentStatement->close();
									$password = null;
									
                                // echo '<h1>' . $newId . '</h1>';
								 $success = true;
                                 $testIdArray = null;
								 
								 $_SESSION['username'] = $newId;
								 
                                 //Insert questions and answers for each test in each class a student is enrolling in
                                $testIdQuery = "select test_id, student_id from test
                                join test_list using(test_id)
                                where class_id = ?
                                group by(test_id)";

                                $selectQuestionQuery = "select heading, heading_id, question_letter, question_no, question_text, question_type, question_value, test_id, answer_id, answer_text, correct
                                from answer
                                right join question using(question_id)
                                join test using(test_id)
                                where student_id = ? and test_id = ?";
                                
                                $insertQuestionQuery = "insert into question(student_id, question_id, heading, heading_id, question_letter, question_no, question_text, question_type, question_value, test_id)
                                                        values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                $insertAnswerQuery = "insert into answer(answer_id, question_id, answer_text, correct)
                                                      values(?, ?, ?, ?)";
                                $maxQuestionQuery = "select max(question_id) from question";
                                $maxAnswerQuery = "select max(answer_id) from answer";
                                $questionArray = array();
                                $testIdArray = array();
                                
                                for($i = 0; $i < count($classes); $i++)
                                {
                                    $testIdStatement = $database->prepare($testIdQuery);
                                    $testIdStatement->bind_param("s", $classes[$i]);
                                    $testIdStatement->bind_result($testId, $stuId);
                                    $testIdStatement->execute();
                                    while($testIdStatement->fetch())
                                    {
                                        $testIdArray[] = $testId;
                                    }
                                    $testIdStatement->close();
                                    
                                    $maxQuestionStatement = $database->prepare($maxQuestionQuery);
                                    $maxQuestionStatement->bind_result($newQId);
                                    $maxQuestionStatement->execute();
                                    $maxQuestionStatement->fetch();
                                    $maxQuestionStatement->close();
                                    
                                    $maxAnswerStatement = $database->prepare($maxAnswerQuery);
                                    $maxAnswerStatement->bind_result($newAId);
                                    $maxAnswerStatement->execute();
                                    $maxAnswerStatement->fetch();
                                    $newAId = $newAId + 1;
                                    $maxAnswerStatement->close();

                                    for($a = 0; $a < count($testIdArray); $a++)
                                    {
                                        $selectQuestionStatement = $database->prepare($selectQuestionQuery);
                                        $selectQuestionStatement->bind_param("ss", $stuId, $testIdArray[$a]);
                                        $selectQuestionStatement->bind_result($heading, $heading_id, $question_letter, $question_no, $question_text, $question_type, $question_value, $test_id, $answer_id, $answer_text, $correct);
                                        $selectQuestionStatement->execute();
                                        while($selectQuestionStatement->fetch())
                                        {
                                            $questionArray[] = $newQId;
                                            $questionArray[] = $heading;
                                            $questionArray[] = $heading_id;
                                            $questionArray[] = $question_letter;
                                            $questionArray[] = $question_no;
                                            $questionArray[] = $question_text;
                                            $questionArray[] = $question_type;
                                            $questionArray[] = $question_value;
                                            $questionArray[] = $test_id;
                                            $questionArray[] = $newAId;
                                            $questionArray[] = $answer_text;
                                            $questionArray[] = $correct;
                                            $newAId++;
                                        }
                                        $selectQuestionStatement->close();
                                        
                                        $oldQno = -1;
                                        $oldTest = -1;
                                        for($k = 0; $k < count($questionArray); $k += 12)
                                        {
                                            if($oldQno != $questionArray[$k+4] or $oldTest != $questionArray[$k+8])
                                            {
                                                $newQId++;
                                                $insertQuestionStatement = $database->prepare($insertQuestionQuery);
                                                $insertQuestionStatement->bind_param("ssssssssss", $newId, $newQId, $questionArray[$k+1], $questionArray[$k+2], $questionArray[$k+3], $questionArray[$k+4], $questionArray[$k+5], $questionArray[$k+6], $questionArray[$k+7], $questionArray[$k+8]);
                                                $insertQuestionStatement->execute();
                                                $insertQuestionStatement->close();
                                            }
                                            $insertAnswerStatement = $database->prepare($insertAnswerQuery);
                                            $insertAnswerStatement->bind_param("ssss", $questionArray[$k+9], $newQId, $questionArray[$k+10], $questionArray[$k+11]);
                                            $insertAnswerStatement->execute();
                                            $insertAnswerStatement->close();
                                            $oldQno = $questionArray[$k+4];
                                            $oldTest = $questionArray[$k+8];
                                        }
                                        $questionArray = array();
                                    }
                                    $testIdArray = array();
                                }
                                echo '<script type="text/javascript">';
								 echo 'redirect();';
								 echo '</script>';
                              }
                           }			
                        }
                        else
                        // do nothing
                        {
                           $success = false;
                        }
                        ?>
        </div>
      </form>
	
   <script>
   $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
   });
   </script>
	
</body>
</html>
