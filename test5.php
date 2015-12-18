<?php
//WORKING-ALLOWS EDITOR TO SELECT OBJECT, CLASS AND CHANGE ROLE. 
//ACTIVITY IS INSERTED into activity table.
//This Test5.php is the function 1 code cleaned up from test4.php
//From test6.php forward I begin working on function 2: 
//Allow editor to add groups, add classes to groups and add objects to a class.

//start the session
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "user_access";

//Connect to db and show list of forms as hyperlinks with descriptions
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
     }

$sql =  "SELECT object_id, form, form_description FROM object";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
     echo "<table><tr><th>Form</th><th>Description</th></tr>";
     // output data of each row
     while($row = $result->fetch_assoc()) {
       //Form as hyperlink
echo "<tr><td>"."<a href=http://localhost:8888/test5.php?id=".$row["object_id"].">" .$row["form"]. "</a></td><td>" . $row["form_description"]. "</td></tr>"; 

     }
     echo "</table>";
} else {
     echo "Form: 0 results";
}

$conn->close();


//Connect to db and show table of objects as hyperlinks
$conn2 = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn2->connect_error) {
     die("Connection failed: " . $conn2->connect_error);
     }

//Get parameter id from url and save in a variable
$obj_id = htmlspecialchars($_GET["id"]); 
//Save ojbect id as a session variable
$_SESSION['ses_obj']=htmlspecialchars($_GET["id"]);
$ob=$_SESSION['ses_obj'];

$sql =  "Select 
view_user_select_role.`class_id`,
view_user_select_role.class_name
from view_user_select_role
Where
view_user_select_role.`object_id`= $obj_id"; 

$result = $conn2->query($sql);


if ($result->num_rows > 0) {
     echo "<br/><table><tr><th>Class</th></tr>";
     // output data of each row
     while($row = $result->fetch_assoc()) {

echo "<tr><td>"."<a href=http://localhost:8888/test5.php?id=".$obj_id."&cid=".$row["class_id"].">" .$row["class_name"]. "</a></td></tr>"; //." "."<a href='group_class.php'>group_class</a></td></tr>"; //<td>" . $row["form_description"]. "</td>

     }
     echo "</table>";
} else {
     echo "Class: 0 results";
}

$conn2->close();

//Connects to db, show current role and allow selection of new role
$conn3 = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn3->connect_error) {
     die("Connection failed: " . $conn3->connect_error);
     }

$cla_id = htmlspecialchars($_GET["cid"]);

    $_SESSION['ses_cla']=htmlspecialchars($_GET["cid"]);
	$cl=$_SESSION['ses_cla'];

$sql =  "Select 
view_user_select_role.`role_id`,
view_user_select_role.role,
view_user_select_role.role_description
from view_user_select_role
Where 
view_user_select_role.`object_id`= $obj_id 
And 
view_user_select_role.`class_id`= $cla_id"; 

$result = $conn3->query($sql);


if ($result->num_rows > 0) {
     echo "<table><tr><th>Current Role</th></tr>";
     // output data of each row
     while($row = $result->fetch_assoc()) {

echo "<tr><td>".$row["role"]. "</a></td></tr>"; 
   
echo"<br/>";
echo "</table>";
   // Form for retriving editor's role selection
 echo"<form action='test5.php' method='get'>
    
<select name='roles'".">"
    ."<option value=".$row["role_id"]."selected>".$row["role_description"]."</option>".
    "<option value= 1 >Manipulate</option>
    <option value= 2 >Query</option>
    <option value= 3 >No Access</option> 
    <option value= 4 >Empty</option>   
  </select>
  <br>";

//Role Before
$bro = $row["role_id"];
  
  "</form>";  

     }
     echo "</table>";
     
} else {
     echo "<br/><br/> Role: 0 results";
}

$conn3->close();

?>

<html>

<!--Submit button-->
<br/>
<input type="submit" value="Submit Role" name="submit">

<?php  
// For Submit Role button 
echo"<form action='test5.php' method='get'>";
if(isset($_GET['submit'])){ 

$ro = ($_GET['roles']);
$cl2 = ($_GET['cla']);
$ob2 = ($_GET['obj']);

$bro2 = ($_GET['brole']);

"</form>";

//Connect to db and update role 
// Create connection
$conn4 = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn4->connect_error) {
     die("Connection failed: " . $conn4->connect_error);
     }

$sql = "UPDATE class_object_role 
        SET role_id=$ro 
        WHERE `object_id`=$ob2 AND class_id=$cl2"; 

if ($conn4->query($sql) === TRUE) {
    echo "Role record updated successfully.";
} else {
    echo "Error updating role record: " . $conn4->error;
}

$conn4->close();


//INSERTING Values INTO ACTIVITY TABLE
// Create connection
$conn5 = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn5->connect_error) {
     die("Connection failed: " . $conn5->connect_error);
     }

$sql1 =  "SELECT class_object_role_id FROM class_object_role
      WHERE `object_id`=$ob2 AND class_id=$cl2";
     $result = $conn5->query($sql1);


if ($result->num_rows > 0) {

     while($row = $result->fetch_assoc()) {
        $cor_id = $row["class_object_role_id"];
}
  
} else {
     echo "<br/><br/> class_object_role: 0 results";
}



$sql2 = "INSERT INTO activity (role_before,role_after,editor_id,class_object_role_id,activity_type,group_id,group_class_id)
VALUES ($bro2,$ro,3,$cor_id,'Change Role',0,0)";

//REMEMBER TO USE SHAUNTELL USER SESSION ID INSTEAD OF EDITOR-ID=1
if ($conn5->query($sql2) === TRUE) {
    echo "<br/>Activity record updated successfully.";
} else {
    echo "<br/> Error updating Activity record: " . $conn5->error;
}

$conn5->close();

 } //isset
?>


<input type="hidden" name="obj" value="<?php echo $obj_id;?>" />
<input type="hidden" name="cla" value="<?php echo $cla_id;?>" />
<input type="hidden" name="sobj" value="<?php echo $ro;?>" />
<input type="hidden" name="brole" value="<?php echo $bro;?>" /> 

</html>
