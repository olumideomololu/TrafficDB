<html>
<head>
    <title>Password Change</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            //Validate password entry via comparison
            var x = document.forms["loginForm"]["newPassword"].value; 
            var y = document.forms["loginForm"]["newPassword2"].value;
            if ((x == "") || (y != x)) {
                alert("Please enter the same password twice");
                return false;
            }
        }
    </script>
</head>
<h1></h1>
<body>
<button><a href="index.php?logout=true">Logout</a></button>
<button><a href="workspace.php">Main Page</a></button>

<?php
//continue session
session_start();

//check for validity of user
if(!isset($_SESSION['Role'])){
    header('location:index.php?lmsg=true');
    exit;
}		

//display personalized greeting message
echo "<h1>Welcome ".$_SESSION['User_Name']."</h1>";
?>

<h1>Please enter your new password</h1>
<form name="loginForm" method="post" onsubmit="return validateForm()">
        password: <input type="password" name="newPassword"><br/><br/>
        confirm password: <input type="password" name="newPassword2"><br/><br/>
                <input type="submit" value="update">
</form>

<?php

//load database connection file
require 'config.php';

//check for entry of new password
if($_POST['newPassword']!=""){
    //assign form data to variable
    //prepare sql statement with variable
    $password = trim($_POST['newPassword']);
    $sql = "UPDATE Users SET Password = '".$password."' WHERE User_Name = '".$_SESSION["User_Name"]."';";

    //notify users of success or failure
    if (mysqli_query($conn, $sql)) {
        echo "<h1>password updated successfully</h1>";
    } else {
        echo "Error changing password: " . mysqli_error($conn);
    }
}   

//logout procedure
if(isset($_GET['logout']) == true){
	session_destroy();
	header("location:index.php");
	exit;
}

//close database connection
mysqli_close($conn);
?>

</body>
</html>