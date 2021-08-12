<html>
<head>
    <title>Incidents</title>
    <link rel="stylesheet" href="style.css">
    <script>
        //validate and confirm incident entry
        function validateForm() {
            var x = document.forms["incidentForm"]["vehicle"].value; 
            var y = document.forms["incidentForm"]["people"].value;
            var z = document.forms["incidentForm"]["offence"].value;
            var a = document.forms["incidentForm"]["report"].value;
            var b = document.forms["incidentForm"]["date"].value;
            if ((x == "") || (y == "") || (z == "") || (a == "") || (b == "")) {
                alert("Please enter all the required fields");
                return false;
            } else{
                confirm("would you like to proceed with this entry");
            }
        }
    </script>
</head>
<body>
<button><a href="index.php?logout=true">Logout</a></button>
<button><a href="workspace.php">Main Page</a></button>
<button><a href="people.php">People</a></button>
<button><a href="vehicle.php">Vehicles</a></button>
<hr>

<?php
//continue session
session_start();

//check for validity of user
if(!isset($_SESSION['Role'])){
    header('location:index.php?lmsg=true');
    exit;
}

//load database connection file
require 'config.php';
?>

<h3>Please enter a new incident</h3>

<button><a href="incident.php?per=true">New Person</a></button>
<button><a href="incident.php?veh=true">New Vehicle</a></button><br/><br/>

<?php
//check for selection of person entry option
if ($_GET['per']!="") 
{
  //display person entry form   
  echo '<form method="post" name="newPerson">
        Name: <input type="text" name="nName">
        Address: <input type="text" name="nAddr">
        Licence: <input type="text" name="nLic">
        <input type="submit" value="Add Person">
  </form><br/>';
}

//check for selection of vehicle entry option
if ($_GET['veh']!="") 
{
  //display vehicle entry form  
  echo '<form method="post" name="newVehicle">
        Type: <input type="text" name="type">
        Colour: <input type="text" name="colour">
        Licence: <input type="text" name="vLicence">
        <select name="vpeople">
                <option disabled selected>-- Select owner --</option>';

        //display available owners as select list from database
        $sql = "SELECT * From People ORDER BY People_ID DESC";
        $lpeople = mysqli_query($conn, $sql); 
        while($data = mysqli_fetch_array($lpeople))
        {
            echo "<option value='". $data['People_ID'] ."'>" .$data['People_name'] ."</option>"; 
        }
        
   echo '</select>
        <input type="submit" value="Add Vehicle">
  </form><br/>';
}

//check for availability of person entry form data
if($_POST['nName']!="" && $_POST['nAddr']!="" && $_POST['nLic']!=""){

    //assign form data to variables
    $name = trim($_POST['nName']);
    $addr = trim($_POST['nAddr']);
    $nLic = trim($_POST['nLic']);

    //prepare sql statement with variables
    $sql = "INSERT INTO People(People_name,People_address,People_licence) VALUES ('".$name."','".$addr."','".$nLic."');";

    //notify users of success or failure
    if (mysqli_query($conn, $sql)) {
        echo "<p>Person added successfully</p>";
        header("location:incident.php");
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}

//check for availability of vehicle entry form data
if($_POST['type']!="" && $_POST['colour']!="" && $_POST['vLicence']!="" && $_POST['vpeople']!=""){

    //assign form data to variables
    $type = trim($_POST['type']);
    $colour = trim($_POST['colour']);
    $vLic = trim($_POST['vLicence']);
    $pId = $_POST['vpeople'];

    //prepare sql statement with variables
    $sql = "INSERT INTO Vehicle(Vehicle_type, Vehicle_colour, Vehicle_licence) VALUES ('".$type."','".$colour."','".$vLic."');";
    $sql2 = "INSERT INTO Ownership(People_ID, Vehicle_ID) VALUES((SELECT People_ID FROM People WHERE People_ID = ".$pId."),(SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '".$vLic."'));";

    //notify users of success or failure
    if (mysqli_query($conn, $sql)&&mysqli_query($conn, $sql2)) {
        echo "<p>Vehicle added successfully</p>";
        header("location:incident.php");
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}

?>

<form name="incidentForm" method="post" onsubmit="return validateForm()">
        
    <select name="vehicle">
            <option disabled selected>-- Select Vehicle --</option>
            <?php
                $sql = "SELECT * From Vehicle ORDER BY Vehicle_ID DESC";
                $lvehicles = mysqli_query($conn, $sql); 
                while($data = mysqli_fetch_array($lvehicles))
                {
                    echo "<option value='". $data['Vehicle_ID'] ."'>" .$data['Vehicle_licence'] ."</option>"; 
                }	
            ?>  
        </select>
    <select name="people">
            <option disabled selected>-- Select Owner --</option>
            <?php
                $sql = "SELECT * From People ORDER BY People_ID DESC";
                $lpeople = mysqli_query($conn, $sql); 
                while($data = mysqli_fetch_array($lpeople))
                {
                    echo "<option value='". $data['People_ID'] ."'>" .$data['People_name'] ."</option>"; 
                }	
            ?>  
        </select>
    <select name="offence">
            <option disabled selected>-- Select Offence--</option>
            <?php
                $sql = "SELECT * From Offence ORDER BY Offence_ID DESC";
                $loffence = mysqli_query($conn, $sql); 
                while($data = mysqli_fetch_array($loffence))
                {
                    echo "<option value='". $data['Offence_ID'] ."'>" .$data['Offence_description'] ."</option>"; 
                }	
            ?>  
        </select><br/><br/>
    Report: <input type="text" name="report" style="width:85%;"><br/><br/>
    Date: <input type="date" name="date"><br/><br/>
    <input type="submit" value="Add Incident">         
</form>

<?php

//check for availability of incident entry form data
if($_POST['vehicle']!="" && $_POST['people']!="" && $_POST['offence']!="" && $_POST['report']!="" && $_POST['date']!="" ){

    //assign form data to variables
    $vehicle = $_POST['vehicle'];
    $people = $_POST['people'];
    $offence = $_POST['offence'];
    $report = trim($_POST['report']);
    $date = $_POST['date'];

    //prepare sql statement with variables
    $sql = "INSERT INTO Incident(Vehicle_ID,People_ID,Offence_ID,Incident_Report,Incident_Date) VALUES (".$vehicle.",".$people.",".$offence.",'".$report."','".$date."');";

    //notify users of success or failure
    if (mysqli_query($conn, $sql)) {
        echo "<p>incident added successfully</p>";
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}

//prepare sql statement with variables
$sql = "SELECT Incident_ID,Vehicle_licence,People_name,Incident_Date,Incident_Report,Offence_description FROM Incident LEFT JOIN Vehicle USING (Vehicle_ID) LEFT JOIN People USING (People_ID) LEFT JOIN Offence USING (Offence_ID) ORDER BY Incident_ID DESC;";
       
//check database for incident data
$result = mysqli_query($conn, $sql); 
echo "<p> There are ".mysqli_num_rows($result)." Incidents in the database.</p>";

//display related data in table if avaiable
if (mysqli_num_rows($result) > 0) {
    echo "<table>"; 
    echo "<tr><th>ID</th><th>Vehicle</th><th>Name</th><th>Date</th><th>Report</th><th>Offence</th></tr>"; 

    while($row = mysqli_fetch_assoc($result)) 
    {
        echo "<tr>";
        echo "<td>".$row["Incident_ID"]."</td>";
        echo "<td>".$row["Vehicle_licence"]."</td>";
        echo "<td>".$row["People_name"]."</td>";
        echo "<td>".$row["Incident_Date"]."</td>";
        echo "<td>".$row["Incident_Report"]."</td>"; 
        echo "<td>".$row["Offence_description"]."&nbsp;&nbsp;";
        echo "</tr>";
    } 
    echo "</table>"; 
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