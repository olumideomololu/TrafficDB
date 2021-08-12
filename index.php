<html>
<head>
    <title></title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() 
        {
            //Login form validation
            var x = document.forms["loginForm"]["name"].value; 
            var y = document.forms["loginForm"]["password"].value;
            if ((x == "") || (y == "")) {
                alert("Name and password must be filled out");
                return false;
            }
        }
    </script>
</head>
<body style="padding: 100px 100px 100px 100px;">
<h2 style="text-align: center;">Traffic Violations Database</h2>
<form style="width: 35%" name="loginForm" method="POST" onsubmit="return validateForm()">
    <input style="width: 100%" placeholder="Name"type="text" name="name"><br/><br/>
    <input style="width: 100%" placeholder="Password" type="password" name="password"><br/><br/>
    <input style="width: 100%" type="submit" value="login">
</form>
<?php

/**
 * The following resources were utilised in the implementation of this project:
 * https://www.w3schools.com/
 * https://www.wdb24.com/simple-role-based-access-control-example-using-php-and-mysqli/
 */
//initiate session
session_start();
//load database connection file
require 'config.php';

//check for login form data
if($_POST['name']!="" && $_POST['password']!=""){
    //assign login form data to variables
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    //build sql query
    $sql = "SELECT * FROM Users WHERE User_Name = '".$name."' AND Password = '".$password."';";
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_num_rows($result);

    if($rows == 1)
    {
        //create user session with result of successful query
        $getUser = mysqli_fetch_assoc($result);
        unset($getUser['password']);
        $_SESSION = $getUser;

        //navigate to application Menu            
        header('location:workspace.php');
        exit;
    }
    else
    {
        //display error message if use credentials do not exist
        $errorMsg = "Wrong username or password";
        echo "<h1>".$errorMsg."</h1>";
    }

}
//close database connection
mysqli_close($conn);
?>    
</body>
</html>