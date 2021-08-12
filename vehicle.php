<html>
<head>
    <title>Vehicles</title>
    <link rel="stylesheet" href="style.css">
</head>
<h1></h1>
<body>
<button><a href="index.php?logout=true">Logout</a></button>
<button><a href="workspace.php">Main Page</a></button>
<button><a href="people.php">people</a></button>
<button><a href="incident.php">Incidents</a></button>
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

<button><a href="vehicle.php?veh=true">New Vehicle</a></button>
<button><a href="vehicle.php?per=true">New Owner</a></button><br/><br/>

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
        header("location:vehicle.php");
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
        header("location:vehicle.php");
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}

?>

<h3>Please search a vehicle using its licence plate number</h3>
<form name="loginForm" method="post" onsubmit="return validateForm()">
        Licence: <input type="text" name="licence">&nbsp;
                <input type="submit" value="Search">
</form>

<?php

//check for entry of vehicle licence
if($_POST['licence']!=""){
    
    //assign form data to variable
    $lic = trim($_POST['licence']);

    //prepare sql statement with variables
    $sql = "SELECT Vehicle_ID,Vehicle_type,Vehicle_colour,Vehicle_licence,People_name FROM Vehicle LEFT JOIN Ownership USING (Vehicle_ID) LEFT JOIN People USING (People_ID) WHERE Vehicle_licence LIKE '%".$lic."%' ORDER BY Vehicle_ID DESC;";

    //check database for related data
    $result = mysqli_query($conn, $sql); 
    echo "<p>".mysqli_num_rows($result)." search result(s).</p>";

    //display related data in table if avaiable
    if (mysqli_num_rows($result) > 0) {
        echo "<table>"; 
        echo "<tr><th>ID</th><th>Type</th><th>Colour</th><th>Licence No</th><th>Owner</th></tr>"; 

        while($row = mysqli_fetch_assoc($result)) 
        {
            echo "<tr>";
            echo "<td>".$row["Vehicle_ID"]."</td>";
            echo "<td>".$row["Vehicle_type"]."</td>";
            echo "<td>".$row["Vehicle_colour"]."</td>";
            echo "<td>".$row["Vehicle_licence"]."</td>"; 
            echo "<td>".$row["People_name"]."&nbsp;&nbsp;";    
            echo "</tr>";
        } 
        echo "</table>"; 
    } else{
        //display error message if no related items are available
        echo "<p>No entries match this query</p>";
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