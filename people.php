<html>
<head>
    <title>People</title>
    <script>
        function validateForm() 
        {
            //double Search parameter check
            var x = document.forms["loginForm"]["pname"].value; 
            var y = document.forms["loginForm"]["plicence"].value;
            if ((x != "") && (y != "")) {
                alert("please use only one search parameter");
                return false;
            }
        }
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<h1></h1>
<body>
<button><a href="index.php?logout=true">Logout</a></button>
<button><a href="workspace.php">Main Page</a></button>
<button><a href="vehicle.php">Vehicles</a></button>
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
<button><a href="people.php?per=true">New Person</a></button><br/><br/>

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
        header("location:people.php");
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}
?>

<h3>Please search for an individual using either their name or licence number</h3>
<form name="loginForm" method="post" onsubmit="return validateForm()">
        Name: <input type="text" name="pname">&nbsp;
        Licence: <input type="text" name="plicence">&nbsp;
                <input type="submit" value="Search">
</form>

<?php
//check for supply of search queries
if($_POST['pname']!=""||$_POST['plicence']!=""){
    
    //initialize data and query variables
    $name = "";
    $sql = "";

    //assign values to variables based on supplied data
    if($_POST['pname']!=""){
        $name = trim($_POST['pname']);
        $sql = "SELECT * FROM People WHERE People_name LIKE '%".$name."%' ORDER BY People_ID DESC;";
    }
    if($_POST['plicence']!=""){
        $lic = trim($_POST['plicence']);
        $sql = "SELECT * FROM People WHERE People_licence LIKE '%".$lic."%' ORDER BY People_ID DESC;";
    }

    //check database for individual data
    $result = mysqli_query($conn, $sql); 
    echo "<p>".mysqli_num_rows($result)." search result(s).</p>";

    //display related data in table if avaiable
    if (mysqli_num_rows($result) > 0) {
        echo "<table>"; 
        echo "<tr><th>ID</th><th>Name</th><th>Licence</th><th>Address</th></tr>"; 

        while($row = mysqli_fetch_assoc($result)) 
        {
            echo "<tr>";
            echo "<td>".$row["People_ID"]."</td>";
            echo "<td>".$row["People_name"]."</td>";
            echo "<td>".$row["People_licence"]."</td>"; 
            echo "<td>".$row["People_address"]."&nbsp;&nbsp;";    
            echo "</tr>";
        } 
        echo "</table>"; 
    } else{
        //error message
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