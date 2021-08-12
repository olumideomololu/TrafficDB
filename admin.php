<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <script>
        //validate and confirm fine entry
        function validateForm() {
            var x = document.forms["newfine"]["incident"].value; 
            var y = document.forms["newfine"]["Amount"].value;
            var z = document.forms["newfine"]["Points"].value;
            if ((x == "") || (y == "") || (z == "")) {
                alert("Please enter all the required fields");
                return false;
            } else{
                confirm("would you like to proceed with this fine entry");
            }
        }
        //Validate password entry via comparison
        function validateForm2() {
            var x = document.forms["newUser"]["Password"].value; 
            var y = document.forms["newUser"]["Password2"].value;
            if ((x == "") || (y != x)) {
                alert("Please enter the same password twice");
                return false;
            }
        }
    </script>
</head>
<body>
<button><a href="index.php?logout=true">Logout</a></button>
<button><a href="workspace.php">Main Page</a></button>

<?php
//continue session
session_start();

//check user for admin rights
if(($_SESSION['Role'])!="admin"){
    //redirect to login if not admin user
    header('location:index.php?lmsg=true');
    exit;
}		
?>
<hr>
<button><a href="admin.php?usr=true">New User</a></button>
<button><a href="admin.php?fin=true">New Fine</a></button><br/><br/>

<?php
//load database connection file
require 'config.php';

//check for selection of user creation option
if ($_GET['usr']!="") 
{
  //display user creation form  
  echo '<form method="post" name="newUser" onsubmit="return validateForm2()">
        Name: <input type="text" name="nName"><br/><br/>
        Role: <select name="urole">
                <option disabled selected>-- Select role --</option>';

                //display user role options in select list based on roles in database
                $sql = "SELECT DISTINCT Role From Users";
                $lrole = mysqli_query($conn, $sql); 
                while($data = mysqli_fetch_array($lrole))
                {
                    echo "<option value='". $data['Role'] ."'>" .$data['Role'] ."</option>"; 
                }
        
   echo '</select><br/><br/>
        Password: <input type="password" name="Password"><br/><br/>
        Confirm Password: <input type="password" name="Password2"><br/><br/>
        <input type="submit" value="Add User">
  </form><br/>';
}

//check for selection of fine entry option
if ($_GET['fin']!="") 
{
   //display fine entry form 
  echo '<form method="post" name="newfine" onsubmit="return validateForm()">
        Incident: <select name="incident">
                <option disabled selected>-- Select incident --</option>';

        //display offences and associated individuals from database as select options        
        $sql = "SELECT People_name,Offence_description, Incident_Date, Incident_ID FROM Incident inner join People using (People_ID) inner join Offence using (Offence_ID) order by Incident_Date DESC;";
        $lfine = mysqli_query($conn, $sql); 
        while($data = mysqli_fetch_array($lfine))
        {
            echo "<option value='". $data['Incident_ID'] ."'>" .$data['People_name'] ."|".$data['Offence_description'] ." on ".$data['Incident_Date'] ."</option>"; 
        }
        
   echo '</select><br/><br/>
        Amount: <input type="text" name="Amount"><br/><br/>
        Points: <input type="text" name="Points"><br/><br/>
        <input type="submit" value="Add fine">
  </form><br/>';
}

//check for availability of user creation form data
if($_POST['nName']!="" && $_POST['urole']!="" && $_POST['Password']!=""){

    //assign form data to variables
    $name = trim($_POST['nName']);
    $urole = trim($_POST['urole']);
    $pass = trim($_POST['Password']);

    //prepare sql statement with variables
    $sql = "INSERT INTO Users (User_Name, Password, Role) VALUES ('".$name."','".$pass."','".$urole."');";

    //notify users of success or failure
    if (mysqli_query($conn, $sql)) {
        echo "<p>user ".$name." created successfully</p>";
    } else {
        echo "Error creating user: " . mysqli_error($conn);
    }
}

//check for availability of user creation form data
if($_POST['incident']!="" && $_POST['Amount']!="" && $_POST['Points']!=""){

    //assign form data to variables
    $incident = $_POST['incident'];
    $amount = (int)trim($_POST['Amount']);
    $points = (int)trim($_POST['Points']);

    //prepare sql statement with variables
    $sql = "INSERT INTO Fines (Fine_Amount, Fine_Points, Incident_ID) VALUES (".$amount.",".$points.",".$incident.");";

    //notify users of success or failure
    if (mysqli_query($conn, $sql)) {
        echo "<p>fine added successfully</p>";
    } else {
        echo "Error adding record: " . mysqli_error($conn);
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