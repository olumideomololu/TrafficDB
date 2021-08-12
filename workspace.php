<html>
<head>
    <title>workspace</title>
    <link rel="stylesheet" href="style.css">
</head>
<h1></h1>
<body>
<button><a href="index.php?logout=true">Logout</a></button>
<button><a href="password.php">Change Password</a></button>
<?php
//continue session
session_start();

//check user for admin rights 
if(($_SESSION['Role']) == "admin"){	
    //display admin panel button
    echo "<button><a href='admin.php'>admin/fines</a></button>";
}
?>
<hr>
<button><a href="people.php">People</a></button>
<button><a href="vehicle.php">Vehicles</a></button>
<button><a href="incident.php">Incidents</a></button>

<?php 

//check for validity of user
if(!isset($_SESSION['Role'])){
    header('location:index.php?lmsg=true');
    exit;
}		



//display personalized greeting message
echo "<h1>Welcome ".$_SESSION['User_Name']."</h1>";

//logout procedure
if(isset($_GET['logout']) == true)
{
	session_destroy();
	header("location:index.php");
	exit;
}

?>
<p>Please use the buttons above to select your chosen activity.<br><br>
    This database can be used to search and make additions to the following:
    <ul>
        <li>People</li>
        <li>Vehicles</li>
        <li>Incidents</li>
    </ul>
    <br><hr>
</p>
</body>
</html>

