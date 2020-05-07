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

?>

<head>
    <title>Search for Lab to Grade</title>
    <link rel="stylesheet" type="text/css" href="formatLabSearch.css"/>
</head>
<body>

    <!-- log out button located in top right corner-->
    <form action="index.html">
        <input type="submit" value="Log Out" class="topcorner">
    </form>
    
    <style type="text/css">
        .topcorner{
            position:fixed;
            top:10px;
            right:10px;
            font-size: 20px;
         }
    </style>

    <?php
	if($_POST["name"]) // && $_POST["grader"}]
	{
        $name=$_POST["name"];
        //$graderID = $_POST["grader];
    }
    ?>
    <div class = "header">
        <h1 class = "headerText">Welcome <?= $name ?></h1>
    </div>

    <!--Figured out how to align horizontal but wasn't able to align vertically so this is a way around that for now-->
    <div class = "invisibleBackground"></div>

    <div class = "align-center roundBorder">
        <h2> Search for Lab to Grade </h2>

        Enter Search Criteria: <br/><br/>
    
        <form action = "labSearch.php" method = "POST">

        <textarea rows="1" cols="30" id="comment" name="comment"></textarea><br /><br />
        
        <input type="radio" id="Student Name" name = "options" value = "Student Name" checked/>
        <label for="Student Name">Student Name</label><br>
        <input type="radio" id="Class Name" name = "options" value = "Class Name"/>
        <label for="Class Name">Class Name</label><br>
        <input type="radio" id="Professor Name" name = "options" value = "Professor Name"/>
        <label for="Professor Name">Professor Name</label><br>

        <br>

        <input type="submit" value = "Submit" id="submitSearch"/> <!-- To do: make this search the database -->
        </form>

        <?php
        $comments= $_POST['comment'];
        $result = $_POST['options'];
        // Hard-coding graderID since we are not using logins
        $graderID = 1;
        
        if($result =="Student Name"){

            // Query to get the student's ID
            $stmt = simpleQuery($db, "SELECT idStudent FROM Student WHERE Student.fullName = '$comments'");
            if($stmt == NULL) { die("<p>No Results Found matching student's name: " . $stmt->error . "</p></body></html>\n"); }

            $stmt->bind_result($student);

            // clear the results of the query
            while ($stmt->fetch()){
            }

            $stmt2 = simpleQuery($db, "SELECT Lab.labName, Lab.idLab
                FROM Lab
                INNER JOIN EnrollsIn ON Lab.classID=EnrollsIn.classID
                INNER JOIN Student ON EnrollsIn.studentID=Student.idStudent
                WHERE Student.fullName = '$comments';");
            if($stmt2 == NULL) { die("<p>No Results Found for stmt2: " . $stmt2->error . "</p></body></html>\n"); }

            // Bind variables to the results in same order as simpleQuery
            $stmt2->bind_result($LabName2, $LabIdLab2); 
            ?>

            <!-- Dislay lab results -->
            </br>
            <label for="Lab Results">The labs for the student are:</label></br>        
            <form action='gradeLab.php' method='POST' id='chooseStudentLab'>
                <?php 

                while($stmt2->fetch()){
                    echo "<label> <input type='radio' name='studentLab' value='" . $LabIdLab2 . "'/>";
                    echo " " . $LabName2 . "</label> <br />"; 
                }
                ?>
            
                <!-- select lab to grade -->   
                <br />        
                <input type="hidden" name="currentStudentID" value="<?php echo $student; ?>"/>
                <input type="hidden" name="gradedBy" value="<?php echo $graderID; ?>"/>      
                <input type="hidden" name="searchCriteria" value="student">
                <input type="submit" value = "Grade Selected Lab" id="labGrade"/> <!-- To do: make this search the database -->
            </form>
            <?php          
        }
        else if($result =="Class Name"){ 
            // Finds the sections based off of class name
            $stmt4 = simpleQuery($db, "SELECT Section.sectionID, Section.classID
            FROM Section
            INNER JOIN Class ON Section.classID=Class.idClass
            WHERE Class.name ='$comments';");
            if($stmt4 == NULL) { die("<p>SQL Query Error: " . $stmt4->error . "</p></body></html>\n"); }

            // Bind variables to the results in same order as simpleQuery
            $stmt4->bind_result($sectionID4, $sectionClassID4); 
            // show list of sections to choose from
            ?>
            <form action='SelectedStudent.php' method='POST' id='chooseStudentLab'>
            <br />
                <?php 
                
                echo "Sections for: " . $comments . "<br />";
                
                while($stmt4->fetch()){
                    echo "<label> <input type='radio' name='section' value='" . $sectionID4 . "'/>";
                    echo "Section " . $sectionID4 . "</label> <br />"; 
                }
                ?>
            
                <!-- select lab to grade -->   
                <br />        
                <input type="submit" value = "Grade Selected Section" id="classGrade"/> <!-- To do: make this search the database -->
                <input type="hidden" name="currentClass" value="<?php echo $sectionClassID4; ?>"/>
                <input type="hidden" name="gradedBy" value="<?php echo $graderID; ?>"/>  
            </form>
            <?php
            $selectedSection = $_POST['section'];
        }
        else if($result =="Professor Name"){
            //Search database via Professor Name to get professorID
            $stmt5 = simpleQuery($db, "SELECT professorID FROM Professor WHERE profName = '$comments'");
            if($stmt5 == NULL) { die("<p>No Results Found matching professor name: " . $stmt5->error . "</p></body></html>\n"); }
            $stmt5->bind_result($profID);
            while($stmt5->fetch()){}

            // Query to get the sections the professor teaches
            $stmt6 = simpleQuery($db, "SELECT sectionID, classID FROM Section WHERE professorID='$profID'");
            if($stmt6 == NULL) { die("<p>No sectiond Found matching professor id: " . $stmt6->error . "</p></body></html>\n"); }
            $stmt6->bind_result($section, $classID);

            ?>
            <form action="SelectedStudent.php" method="POST"> <br />
            <?php

            echo "The classes/sections for " . $comments . " are: <br />";

            while($stmt6->fetch()){
                // Query to get the class name
                $stmt7 = simpleQuery($db, "SELECT name FROM Class WHERE idClass='$classID'");
                if($stmt7 == NULL) { die("<p>No class Found matching class id: " . $stmt7->error . "</p></body></html>\n"); }
                $stmt7->bind_result($className);
                while($stmt7->fetch()){}

                echo "<label> <input type='radio' name='classNSection' value='" . $classID . "," . $section . "'/>";
                echo $className . " Section: " . $section . "</label> <br />";
            }
            ?>        
                <!-- select lab to grade -->   
                <br />        
                <input type="submit" value = "Grade Selected Section" id="labGrade"/> <!-- To do: make this search the database -->
                <input type="hidden" name="currentClass" value="<?php echo $sectionClassID4; ?>"/>
                <input type="hidden" name="gradedBy" value="<?php echo $graderID; ?>"/>  
            </form>
            <?php
        }
        // Close the database connection at the end
        $stmt->close();
        $stmt1->close();
        $stmt2->close();
        $stmt3->close();
        $stmt4->close();
        $stmt5->close();
        $stmt6->close();
        $db->close();
        ?>
    </div>
</body>
</html>
