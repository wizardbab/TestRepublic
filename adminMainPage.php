<!DOCTYPE html>
<html lang="en">

<head>

   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <meta name="author" content="">

   <title>Test Republic - Admin</title>

   <!-- Bootstrap Core CSS -->
   <link href="css/bootstrap.min.css" rel="stylesheet">

   <!-- Custom CSS -->
   <link href="css/simple-sidebar.css" rel="stylesheet">

	<!-- Custom Fonts -->
   <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

   <!-- Custom Validation -->
   <link href="css/validation.css" rel="stylesheet" type="text/css">
   <script type="text/javascript" src="js/validation.js"></script>

</head>
<body>

<?php

session_start();

// Include the constants used for the db connection
require("constants.php");

$id = $_SESSION['username'];

// Gets the class id appended to url from teacherMainPage.php
//$classId = $_GET['classId'];

//$_SESSION['classId'] = $classId;
//$_SESSION['testId'] = null;

if($id == null)
   header('Location: login.html');

// The database variable holds the connection so you can access it
$database = mysqli_connect(DATABASEADDRESS,DATABASEUSER,DATABASEPASS);
@ $database->select_db(DATABASENAME);

if (mysqli_connect_errno())
{
   echo "<h1>Connection error</h1>";
}

$adminId = "select admin_id from admin where admin_id = ?";

$mainTableQuery = "select class_id, class_description, teacher_id, first_name, last_name
                   from class
                   join teacher
                   using(teacher_id)";

$teacherListQuery = "select teacher_id, first_name, last_name, teacher_password, email
                     from teacher";

$selectTeacherQuery = "select teacher_id from teacher";

$maxTeacherQuery = "select max(teacher_id) from teacher";

$mainTableStatement = $database->prepare($mainTableQuery);
$adminStatement = $database->prepare($adminId);
$teacherListStatement = $database->prepare($teacherListQuery);
$selectTeacherStatement = $database->prepare($selectTeacherQuery);
$maxTeacherStatement = $database->prepare($maxTeacherQuery);
?>

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
               <span class="TestRepublic">Test Republic</span>
            </div>
         </a>
      </div>
      <!-- Top Menu Items -->
      <ul class="nav navbar-right top-nav">
         <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i>

            <?php
            $adminStatement->bind_param("s", $id);
            $adminStatement->bind_result($aid);
            $adminStatement->execute();
            while($adminStatement->fetch())
            {
            echo $aid;
            }
            $adminStatement->close();
            ?>&nbsp;<b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
               <li>
                  <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
               </li>
            </ul>
         </li>
      </ul>
   </nav>
</div>	

<div id="wrapper">

   <!-- Sidebar -->
   <div id="sidebar-wrapper">
      <ul class="sidebar-nav">
         <li class="sidebar-brand admin_tools_text">
            Admin Tools
         </li>
         <li>
            <button type="button" class="btn btn-default btn-sm admin_button" data-toggle="modal" data-target="#TModal" data-title="MultipleChoice">
               <img src="images/add_user.png" class="admin_icon" /> Add Teacher
            </button>
         </li>
         <li>
            <button type="button" class="btn btn-default btn-sm admin_button" data-toggle="modal" data-target="#CModal" data-title="MultipleChoice">
               <img src="images/add.png" class="admin_icon" /> Add a Class
            </button>
         </li>
         <li>
            <button type="button" class="btn btn-default btn-sm admin_button" data-toggle="modal" data-target="#UpdateModal" data-title="MultipleChoice">
               <img src="images/update.png" class="admin_icon" /> Update a Class
            </button>
         </li>
      </ul>
   </div>

   <!-- Page Content -->
   <div id="page-content-wrapper">
      <!-- Keep page stuff under this div! -->
      <div class="container-fluid">
         <div class="row">
            <div class="back_section">
               <button type="button" id="back_btn" onclick="window.location.href='login.html'"><span class="glyphicon glyphicon-circle-arrow-left"></span> Back</button>
            </div>
         </div>
      </div>

      <div class="container-fluid align-center">
         <div class="row add_margin_top">
         
         </div>
         <div class="row">
            <div class="panel-group" id="accordion">
               <div class="panel panel-default">
                  <a class="accordion-toggle accordian_link" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                     <div class="panel-heading accordion_heading">
                        <h4 class="panel-title">
                           <span class="accordion_title">TEACHER LIST</span>
                        </h4>
                     </div>
                  </a>
                  <div id="collapseOne" class="panel-collapse collapse">
                     <div class="panel-body">
                        <table class="test_list table-hover">
                           <colgroup>
                              <col class="test_name" />
                              <col class="start_date" />
                              <col class="ending_date" />
                              <col class="test_average" />
                              <col class="view_button_col" />
                           </colgroup>
                           
                           <thead>
                              <tr>
                                 <th>Teacher ID</th>
                                 <th>First Name</th>
                                 <th>Last Name</th>
                                 <th>Password</th>
                                 <th>Email</th>
                              </tr>
                           </thead>

                           <tbody>
                           <?php
                              $teacherListStatement->bind_result($tid, $fname, $lname, $password, $email);
                              $teacherListStatement->execute();

                              while($teacherListStatement->fetch())
                              {
                                 echo '<tr><td>'. $tid .'</td><td>'. $fname . '</td><td>' .$lname . '</td><td>' .$password. '</td><td>' . $email. '</td></tr>';
                              }
                              $teacherListStatement->close();
                           ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
               
               <div class="panel panel-default">
                  <a class="accordion-toggle accordion_link" data-toggle="collapse" data-parent="#accordion" href="#collapsetwo">
                     <div class="panel-heading accordion_heading">
                        <h4 class="panel-title">
                           <span class="accordion_title">CLASS LIST</span>
                        </h4>
                     </div>
                  </a>
                  <div id="collapsetwo" class="panel-collapse collapse add_margin_bottom">
                     <table class="test_list table-hover">
                        <thead>
                           <tr>
                              <th>Class ID</th>
                              <th>Class Description</th>
                              <th>Teacher ID</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                           </tr>
                        </thead>

                        <tbody>
                        <?php
                           $mainTableStatement->bind_result($clid, $clde, $tid, $fname, $lname);
                           $mainTableStatement->execute();
                           while($mainTableStatement->fetch())
                           {
                           echo '<tr><td>' . $clid .'</td><td>'. $clde . '</td><td>' .$tid . '</td><td>' .$fname. '</td><td>' . $lname. '</td></tr>';
                           $classArray[] = $clid;
                           }
                           $mainTableStatement->close();
                        ?>
                        </tbody>

                     <?php
                     // Loop and store all valid teacher; if the entry by admin doesn't match we don't enter into db
                     $selectTeacherStatement->bind_result($teacher);
                     $selectTeacherStatement->execute();
                     while($selectTeacherStatement->fetch())
                     {
                        $teacherArray[] = $teacher;
                     }
                     $selectTeacherStatement->close();
                     
                     
                     $maxTeacherStatement->bind_result($tid);
                     $maxTeacherStatement->execute();
                     while($maxTeacherStatement->fetch())
                     {
                        $teacherId = $tid + 1;
                     }
                     $maxTeacherStatement->close();
                     ?>
                     </table>
                  </div>
               </div>
            </div>
         </div>

         <!--Teacher Creation Modal -->
         <div id="TModal" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header modal_header_color">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                     <h4 class="modal-title"><img src="images/add_user.png" class="admin_icon" /> New Teacher</h4>
                  </div>
                  <div class="modal-body">
                     <form name="TeacherForm" id="TeacherForm" method="post">
                        <div class="form-group">
                           <div class="point_value_section">
                              <label class="control-label">ID#:&nbsp;<?php echo $teacherId; ?></label>
                           </div>
                           <hr />
                           <div class="add_teacher_modal">
                              <div class="form-group form_elements">
                                 <label for="firstNameText" class="control-label">First Name:</label>
                                 <input type="text" class="textbox_style_add_teacher" id="firstNameText">
                              </div>
                              <div class="form-group form_elements">
                                 <label for="lastNameText" class="control-label">Last Name:</label>
                                 <input type="text" class="textbox_style_add_teacher" id="lastNameText">
                              </div>
                              <div class="form-group form_elements">
                                 <label for="emailText" class="control-label">Email:</label>
                                 <input type="text" class="textbox_style_add_teacher" id="emailText">
                              </div>
                              <div class="form-group form_elements">
                                 <label for="passwordText" class="control-label">Password:</label>
                                 <input type="text" class="textbox_style_add_teacher" id="passwordText">
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer bottom_modal">
                     <button type="button" class="btn btn-default" data-dismiss="modal"><img src="images/cancel.png" class="cancel_icon" /> Cancel</button>
                     <button type="submit" class="btn btn-primary " id="createTeacherButton" name="create" value="create" >Create Teacher</button>
                  </div>
               </div>
            </div>
         </div>

         <!--Class Creation Modal -->
         <div id="CModal" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header modal_header_color">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                     <h4 class="modal-title"><img src="images/add.png" class="admin_icon" /> New Class</h4>
                  </div>
                  <div class="modal-body add_class_modal">
                     <form name="shortAnswerForm" id="shortAnswerForm" action="testCreationPage.php" method="post">
                        <div class="form-group form_elements">
                           <label for="classIdText" class="control-label">Class ID:</label>
                           <input type="text" class="textbox_style_add_class" id="classIdText">
                        </div>
                        <div class="form-group form_elements">
                           <label for="classDescriptionText" class="control-label">Class Description:</label>
                           <input type="text" class="textbox_style_add_class" id="classDescriptionText">
                        </div>
                        <div class="form-group form_elements">
                           <label for="teacherIdText" class="control-label">Teacher ID:</label>
                           <input type="number" class="textbox_style_add_class" id="teacherIdText">
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer bottom_modal">
                     <button type="button" class="btn btn-default" data-dismiss="modal"><img src="images/cancel.png" class="cancel_icon" /> Cancel</button>
                     <button type="submit" class="btn btn-primary " id="createClassButton" name="create" value="create" >Create Class</button>
                  </div>
               </div>
            </div>
         </div>

         <!--Class Update Modal -->
         <div id="UpdateModal" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header modal_header_color">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                     <h4 class="modal-title"><img src="images/update.png" class="admin_icon" /> Update Class</h4>
                  </div>
                  <div class="modal-body class_update_modal">
                     <form name="shortAnswerForm" id="shortAnswerForm" action="testCreationPage.php" method="post">
                        <div class="form-group form_elements">
                           <label for="classIdUpdateText" class="control-label">Class ID:</label>
                           <input type="text" class="textbox_style_update_class" id="classIdUpdateText">
                        </div>
                        <div class="form-group form_elements">
                           <label for="teacherIdUpdateText" class="control-label">New Teacher ID:</label>
                           <input type="text" class="textbox_style_update_class" id="teacherIdUpdateText">
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer bottom_modal">
                     <button type="button" class="btn btn-default" data-dismiss="modal"><img src="images/cancel.png" class="cancel_icon" /> Cancel</button>
                     <button type="submit" class="btn btn-primary " id="updateClassButton" name="create" value="create" >Create Class</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- /#page-content-wrapper -->

   </div>
   <!-- /#wrapper -->

   <!-- jQuery -->
   <script src="js/jquery.js"></script>

   <!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>

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
      // Check if the teacher exists
      function inArray(array, id) 
      {	
         for(var i = 0; i < array.length; i++)
         {
            if(array[i] == id)
               return true;// We're good
         }
         return false;
      }

      $("#createTeacherButton").click(function()
      {
         var firstName = $("#firstNameText").val();
         var lastName = $("#lastNameText").val();
         var email = $("#emailText").val();
         var password = $("#passwordText").val();
         var emailRegex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
         var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/;

         if(firstName == "")
         {
            inlineMsg('firstNameText', 'This field cannot be empty', 3);
         }
         else if(lastName == "")
         {
            inlineMsg('lastNameText', 'This field cannot be empty', 3);
         }
         else if(email == "")
         {
            inlineMsg('emailText', 'This field cannot be empty', 3);
         }
         else if(!email.match(emailRegex))
         {
            inlineMsg('emailText', 'This email is invalid', 3);
         }
         else if(password == "")
         {
            inlineMsg('passwordText', 'This field cannot be empty', 3);
         }
         else if(!password.match(passwordRegex))
         {
            inlineMsg('passwordText', 'A password must contain at least one uppercase, one lowercase, one number, and be a minimum of eight characters long', 4);
         }
         else
         {
            $("#TModal").modal("hide");
            $.post("AdminScripts/addTeacherScript.php",
            {
               firstName:firstName,
               lastName:lastName,
               email:email,
               password:password
            },
            function()
            {
               alert("You have successfully added a new teacher!");
               
            });
         }
      }); 

      $("#createClassButton").click(function()
      {
         var teacherArray = [];
         <?php for($i = 0; $i < count($teacherArray); $i++){ ?>
         teacherArray.push('<?php echo $teacherArray[$i];?>');
         <?php } ?>
            
         var classId = $("#classIdText").val();
         var classDescription = $("#classDescriptionText").val();
         var teacherId = $("#teacherIdText").val();
         
         if(classId == "")
         {
            inlineMsg('classIdText', 'This field cannot be empty', 2);
         }
         else if(classDescription == "")
         {
            inlineMsg('classDescriptionText', 'This field cannot be empty', 2);
         }
         else if(teacherId == "")
         {
            inlineMsg('teacherIdText', 'This field cannot be empty', 2);
         }
         else
         {
            $("#CModal").modal("hide");
            $.post("AdminScripts/addClassScript.php",
            {
               classId:classId,
               classDescription:classDescription,
               teacherId:teacherId
            },
            function()
            {
               alert("You have successfully created a new class");
            });
         }
      }); 

      $("#updateClassButton").click(function()
      {
         var teacherArray = [];
         var classArray = [];
         var classUpdateId = $("#classIdUpdateText").val();
         var teacherUpdateId = $("#teacherIdUpdateText").val();
         
          <?php for($i = 0; $i < count($teacherArray); $i++){ ?>
                    teacherArray.push('<?php echo $teacherArray[$i];?>');
                <?php } ?>
            
         <?php for($i = 0; $i < count($classArray); $i++){ ?>
                    classArray.push('<?php echo $classArray[$i];?>');
                <?php } ?>
            
         if(classUpdateId == "")
         {
            inlineMsg('classIdUpdateText', 'This field cannot be empty', 2);
         }
         else if(teacherUpdateId == "")
         {
            inlineMsg('teacherIdUpdateText', 'This field cannot be empty', 2);
         }
         else
         {
            $("#UpdateModal").modal("hide");
            $.post("AdminScripts/updateClassScript.php",
            {
               classId:classId,
               teacherId:teacherId
            },
            function()
            {
               alert("You have successfully updated the class");
               
            });
         }
      });

   });
   </script>
</div>
</body>

</html>
