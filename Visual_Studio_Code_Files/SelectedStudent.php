<!-- Agile Experience Group 7 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
// Establish the database connection
include "helper.php";
$db = connectToDatabase();
if($db == NULL) { die("<p>Connection Error</p></body></html>\n"); }

// Prepare and execute query 1
 $stmt1 = simpleQuery($db, "SELECT idStudent, fullName FROM Student");//CHANGE THIS LINE TO CHANGE QUERY
 if($stmt1 == NULL) { die("<p>SQL Query Error: " . $stmt1->error . "</p></body></html>\n"); }

// Bind variables to the results in same order as simpleQuery
$stmt1->bind_result($studentID, $studentName); //CHANGE THIS LINE TO RENAME QUERY RESULTS

//can add more statements and queries just label it stmt2, stmt3 and so on
?>

<head>
    <title>Select Student and Lab</title>
    <script src="viewLab.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="gradeLab.css"/>
</head>
<body id="selectStudent">
    <!-- log out button located in top right corner-->
    <form action="index.html">
        <input type="submit" value="Log Out" class="topcorner">
    </form>

    <style type="text/css">
        .topcorner{
            position:fixed;
            top:10px;
            right:10px;
         }
    </style>

    <!-- Return to Lab Search button is below the log out button-->
    <!-- This will have to be changed so it actually passes the grader id back to the search for lab page-->
    <form action="labSearch.php">
        <input type="submit" value="Return to Lab Search" class="belowtopcorner">
    </form>

    <style type="text/css">
        .belowtopcorner{
            position:fixed;
            top:50px;
            right:10px;
         }
    </style>
<?php  
// Results from searching by class
// Finds the roster of students based off of sectionID
if (isset($_POST['section']) && isset($_POST['currentClass'])){
    $section = $_POST['section'];
    $class = $_POST['currentClass'];
            // Query to get students enrolled in the section
            $stmt5 = simpleQuery($db, "SELECT studentID FROM EnrollsIn WHERE sectionID = '$section' AND classID = '$class';");
            if($stmt5 == NULL) { die("<p>No Results Found: " . $stmt5->error . "</p></body></html>\n"); }

            $stmt5->bind_result($studentID5); 

        ?>
            <form action='gradeLab.php' method='POST' id='chooseStudentLab'>
            <label for="Lab Results"><h4>Select a Student to Grade:</h4></label>
        <?php 

            // Display the students enrolled in the section
            while($stmt5->fetch()){             
                // Query to get the student's name   
                $stmt2 = simpleQuery($db, "SELECT fullName FROM Student WHERE idStudent = $studentID5");
                if($stmt2 == NULL) { die("<p>No students Found: " . $stmt2->error . "</p></body></html>\n"); }
                $stmt2->bind_result($studentName);
                while($stmt2->fetch()){}

                echo "<label> <input type='radio' name='currentStudentID' value='" . $studentID5 . "'/>";
                echo " " . $studentName . "</label> <br />"; 
            }

            // Query to find the labs for the selected section
            $stmt6 = simpleQuery($db, "SELECT Lab.labName, Lab.idLab
                FROM Lab
                WHERE sectionID = '$section' AND classID = '$class';");
            $stmt6->bind_result($labName6, $labIDlab6); 
            if($stmt6 == NULL) { die("<p>No Results Found: " . $stmt6->error . "</p></body></html>\n"); }
            ?>
    
            <label for="Lab Results"><h4>Select a Lab to Grade:</h4></label>

            <?php 
                // Display a list of the labs for the section
                while($stmt6->fetch()){
                    echo "<label> <input type='radio' name='studentLab' value='" . $labIDlab6 . "'/>";
                    echo " " . $labName6 . "</label> <br />"; 
                }
            ?>

                <br />        
                <input type="hidden" name="gradedBy" value="<?php echo $_POST['gradedBy']; ?>"/>   
                <input type="hidden" name="searchCriteria" value="section">
                <input type="submit" value = "Grade Selected Lab"/>                 
            </form>
        <?php
// Results from searching by professor
}else if(isset($_POST['classNSection'])){
    $classSection = $_POST['classNSection'];
    $selections = explode(",", $classSection);
    $selectedClass = $selections[0];
    $selectedSection = $selections[1];

    // Query to get students enrolled in the section
    $stmt5 = simpleQuery($db, "SELECT studentID FROM EnrollsIn WHERE sectionID = '$selectedSection' AND classID = '$selectedClass';");
    if($stmt5 == NULL) { die("<p>No Students Found for the section: " . $stmt5->error . "</p></body></html>\n"); }

    $stmt5->bind_result($studentID5); 

?>
    <form action='gradeLab.php' method='POST' id='chooseStudentLab'>
    <label for="Lab Results"><h4>Select a Student to Grade:</h4></label>
<?php 

    // Display the students enrolled in the section
    while($stmt5->fetch()){
         // Query to get the students names
        $stmt3 = simpleQuery($db, "SELECT fullName FROM Student WHERE idStudent = $studentID5");
        if($stmt3 == NULL) { die("<p>No students Found: " . $stmt3->error . "</p></body></html>\n"); }
        $stmt3->bind_result($studentName);
        while($stmt3->fetch()){}

        echo "<label> <input type='radio' name='currentStudentID' value='" . $studentID5 . "'/>";
        echo " " . $studentName . "</label> <br />"; 
    }

    // Query to find the labs for the selected section
    $stmt6 = simpleQuery($db, "SELECT Lab.labName, Lab.idLab
        FROM Lab
        WHERE sectionID = '$selectedSection' AND classID = '$selectedClass';");
    $stmt6->bind_result($labName6, $labIDlab6); 
    if($stmt6 == NULL) { die("<p>No Labs Found for the section: " . $stmt6->error . "</p></body></html>\n"); }
    ?>

    
    <label for="Lab Results"><h4>Select a Lab to Grade:</h4></label>

    <?php 
        // Display a list of the labs for the section
        while($stmt6->fetch()){
            echo "<label> <input type='radio' name='studentLab' value='" . $labIDlab6 . "'/>";
            echo " " . $labName6 . "</label> <br />"; 
        }
    ?>

        <br />        
        <input type="hidden" name="gradedBy" value="<?php echo $_POST['gradedBy']; ?>"/>  
        <input type="hidden" name="searchCriteria" value="professor"> 
        <input type="submit" value = "Grade Selected Lab"/>                 
    </form>
<?php
}else{
    echo "<h2 style='color:red;'>ERROR: No class/section selected.</h2>\n";
    ?>
    <form action=labSearch.php>
        <button> Back to Search </button>
    </form>
    <?php
}
$stmt1->close();
$stmt2->close();
$stmt3->close();
$stmt5->close();
$stmt6->close();
$db->close();
?>
</body>
</html>