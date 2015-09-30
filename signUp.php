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

   <!-- Custom CSS -->
   <link href="css/signup.css" rel="stylesheet">

   <!-- Custom Fonts -->
   <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

   <!-- Form Validation Includes -->
   <link href="css/validation_page.css" rel="stylesheet" type="text/css">
   <script type="text/javascript" src="js/signUpValidation.js"></script>

   <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
    <!-- Parsley -->
    <script src="js/Parsley.js/dist/parsley.js"></script>
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

   <div id="wrapper">
      <form name="signUpForm" id="signUpForm" action="signUp.php" method="post">
         <div id="sidebar-wrapper">
            <a href="logout.php">
               <!-- Button with a link wrapped around it to go back to the login page -->
               <button class="btn btn-block btn-primary" type="button" id="signUpButton" onclick="window.location.href='logout.php'">
                  <i class="glyphicon glyphicon-log-in"></i>Back to Login
               </button>
            </a>
            <ul class="sidebar-nav">
               <li>
                  <a href="#" id="student-summary">Summary</a>
               </li>
               
               <li class="sidebar-brand" id="sidebar-classes"><!-- VIC AND ANDREA, I'D LIKE FOR THIS TO "SELECT A CLASS TO ADD:" (formatting needed) -->
                  Select a Class:
               </li>
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
                  while($classList->fetch())
                  {
                     echo '
                     <li>
                        <a href="#">
                           <div class="subject-name">' . $courseCounter . ". " . $clde . '</div>
                        </a>
                        <input type="checkbox" name="classes[]" class="sidebar_class" value="' . $clid . '" id="sidebar-element' . $courseCounter++ . '" />
                     </li>
                     ';
                  }
                  $classList->close(); 
                  ?>
            </ul>
         </div>

         <div class="container-fluid">
            <div class="row">

               <div id="signUpDiv">

                  <div class="sign_up_box" id="sign_up_box">
                     <div class="sign_up_text_area">
                        <img src="images/logo4.png" alt="Our Logo" height="80" width="80">
                        <span class="sign_up_text">&nbsp; Sign Up</span>
                     </div>
                     <h2>Please enter your information.</h2>
                     <br />
                     
                     <label class="survey_style">
                        <div class="row">
                           <div class="col-md-4">First Name:</div>
                           <div class="col-md-8"><input type="text" name="firstName" id="firstName" value="<?php print $firstName; ?>" required /></div>
                        </div>
                     </label><br />
                     <label class="survey_style">
                        <div class="row">
                           <div class="col-md-4">Last Name:</div>
                           <div class="col-md-8"><input type="text" name="lastName" id="lastName" value="<?php print $lastName; ?>" required /></div>
                        </div>
                     </label><br />
                     <label class="survey_style">
                        <div class="row">
                           <div class="col-md-4">&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email:</div>
                           <div class="col-md-8"><input type="email" name="email" id="email" required /></div>
                        </div>
                     </label><br />
                     <label class="survey_style">
                        <div class="row">
                           <div class="col-md-4">Password:</div>
                           <div class="col-md-8"><input title="Must contain at least one uppercase, one lowercase, one number, and be a minimum of 8 characters."type="password" name="password" id="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" /></div>
                        </div>
                     </label><br />
                     <!--<input class="btn btn-primary" type="submit" value="Create Account" data-toggle="modal" data-target="#sign_up_modal" data-title="Sign Up" id="create_acc_btn" />-->
                     <input class="btn btn-primary" type="submit" value="Create Account" id="create_acc_btn" />
                  </div>

                        <?php 
                        $success = 1;
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

                                       echo '<h1 margin-left: 50px;>' . $a . '</h1></br />';
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
                                       else
                                       {
                                         printf("It failed");
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
									
                                 echo '<h1>' . $newId . '</h1>';
								 $success = 1;
                                 $testIdArray = null;
								 
								 $_SESSION['username'] = $newId;
								 
								
								
								// echo'<script> window.location="login.php"; </script> ';
								
								
								// header('Location: studentMainPage.php');
								
								/*$result = preg_replace('#<div class="sign_up_box" id="sign_up_box">(.*?)</div>#', ' ', $incoming_data);*/
								
								/*echo "<script language=javascript>alert('SUP')</script>";*/
								
								/*echo '	<div class="sign_up_text_area_2">
											<img src="images/logo4.png" alt="Our Logo" height="50" width="50">
											<span class="sign_up_text_2">&nbsp; Sign Up</span>
										</div>';
								echo '<div class="congrats_text">Congratulations!</div>
										<div><h4>You have successfully created an account.<h4></div>
										<div class="name_section">'.ucfirst($firstName) . ' ' .ucfirst($lastName). ' ' .$id. '</div>';*/
								
                              }
                           }			
                        }
                        else
                        // do nothing
                        {
                           $success = 0;
                        }
                        ?>
                  </div>
            </div>
         </div>
      </form>
   </div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <script src="js/bootstrap.js"></script>

   <!-- Menu Toggle Script -->
   <script>
   $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
   });
   </script>


</body>
</html>
