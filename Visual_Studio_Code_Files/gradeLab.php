<!-- Agile Experience Group 7 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
    // Establish the database connection
    include "helper.php";
    $db = connectToDatabase();
    if($db == NULL) { die("<p>Connection Error </p></body></html>\n"); }
?>

<head>
    <title>Grade Lab</title>
    <script src="labDetails.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="gradeLab.css"/>
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
            top:52px;
            right:10px;
         }
    </style>

    

    <!-- DELETE THIS ONCE DONE TESTING 
    <h2> <?php //echo "Selected LabID: " . $_POST['studentLab'] ?> </h2>
    <h2> <?php //echo "Selected studentID: " . $_POST['currentStudentID'] ?> </h2>
    <h2> <?php //echo "GraderID: " . $_POST['gradedBy']; ?> </h2> -->

    <?php

    // ensure we can get the graderID
    if (isset($_POST['gradedBy'])){
        $graderID = $_POST['gradedBy'];
    }else{
        die("<h2 style='color:red;'>ERROR: Could not fetch graderID </h2></body></html>\n");
    }

    // If we have a student and lab selected to grade
    if (isset($_POST['currentStudentID']) && isset($_POST['studentLab'])){

        $studentID = $_POST['currentStudentID'];
        $currentLabID = $_POST['studentLab'];

        // check to see if the student has a grade for this lab already
        $stmt13 = simpleQuery($db, "SELECT gradeID, assignedValue FROM Grade WHERE labID = $currentLabID AND studentID = $studentID");
        if ($stmt13 == NULL){
            $alreadyGraded = False;
        }else{
            $stmt13->bind_result($receivedGradeID, $receivedGradeValue);
            while($stmt13->fetch()){}

            // Somehow, $stmt13 == NULL is false even if no matches are found in the query,thus the additional check
            $numRows = $stmt13 -> num_rows;
            if ($numRows == 0){
                $alreadyGraded = False;
            }else{
                $alreadyGraded = True;
            }
        }

        // get the students name
        $stmt11 = simpleQuery($db, "SELECT fullName FROM Student WHERE idStudent = $studentID");
        if($stmt11 == NULL) { die("<p>SQL Query Error: Can't get student's name " . $stmt11->error . "</p></body></html>\n"); }
        $stmt11->bind_result($studentName);
        while($stmt11->fetch()){}

        // Prepare and execute query to fetch the noteSheetID and lab name for the chosen lab
        $stmt1 = simpleQuery($db, "SELECT noteSheetID, labName FROM Lab WHERE idLab = $currentLabID");
        if($stmt1 == NULL) { die("<p>SQL Query Error: " . $stmt1->error . "</p></body></html>\n"); }

        // Bind variables to the results in same order as simpleQuery
        $stmt1->bind_result($nsID, $lab);

        while ($stmt1->fetch()) {
            $noteSheetID = $nsID; //since query returns one value, this only happens once
            $currentLab = $lab;
        }

        // Get information for the lab
        $stmt8 = simpleQuery($db, "SELECT GradeType, sectionID, classID, maxScore FROM Lab WHERE idLab = $currentLabID");
        if($stmt8 == NULL) { die("<p>SQL Query Error: " . $stmt8->error . "</p></body></html>\n"); }
        $stmt8->bind_result($gradeType, $sectionID, $classID, $maxPoints);
        while($stmt8->fetch()){}

        // Query to get the name of the grade type
        $stmt12 = simpleQuery($db, "SELECT TypeOfGrade FROM GradeType WHERE ID=$gradeType");
        if($stmt12 == NULL) { die("<p>SQL Query Error: Unable to get grade type " . $stmt8->error . "</p></body></html>\n"); }
        $stmt12->bind_result($typeOfGrade);
        while($stmt12->fetch()){}
        
        // Display a back button to go back to SelectedStudent.php so that a new lab and/or student can be selected
        $searchMethod = $_POST['searchCriteria'];
        if ($searchMethod == 'professor' || $searchMethod == 'section'){
        ?>
            <form action="SelectedStudent.php" method="POST">
                <input type="submit" value="Return to Select Student" class="belowbelow">
                <input type="hidden" name="gradedBy" value = <?php echo $graderID; ?> />
                <?php
                    if ($searchMethod == 'section') { ?>
                        <input type="hidden" name="section" value= <?php echo $sectionID; ?> />
                        <input type="hidden" name="currentClass" value= <?php echo $classID; ?> /> <?php
                    } else{ ?>
                        <input type="hidden" name="classNSection" value= <?php echo $classID . "," . $sectionID; ?> />
                        <?php
                    } ?>
            </form>

            <style type="text/css">
                .belowbelow{
                    position: fixed;
                    top:92px;
                    right:10px;
                }
            </style>
        <?php
        }
        ?>

        <div class = "header">
            <?php
            echo '<div style="text-align:center; Color:white">'. "<h2> Grading Lab: " . $currentLab . "</h2>" . '</div>';
            ?>
        </div>
        
        <?php
        // If a new grade has been entered and submitted
        if (isset($_POST['submitted'])) {

            // get the max logID, used to generate the new logID
            $stmt10 = simpleQuery($db, "SELECT MAX(LogID) FROM Log");
            if($stmt10 == NULL) { die("<p>SQL Query Error: " . $stmt10->error . "</p></body></html>\n"); }
            $stmt10->bind_result($newLogID);
            while($stmt10->fetch()){}

            // Get the max gradeID, used to generate the new gradeID
            $stmt9 = simpleQuery($db, "SELECT MAX(gradeID) FROM Grade");
            if($stmt9 == NULL) { die("<p>SQL Query Error: " . $stmt9->error . "</p></body></html>\n"); }
            $stmt9->bind_result($newGradeID);
            while($stmt9->fetch()){}            

            $testGradeTypeQuery = false;
            $grade = $_POST['grade'];

            // Check to make sure the gradeType is specified and the value/grade is acceptable and not empty
            if($gradeType == 1 && ($grade >= 0 && $grade <= 100 ) && !(ctype_space($grade)) && $grade != null){                
                $testGradeTypeQuery = true;        
                $newGrade = "Grade submitted";
            }
            else if($gradeType == 2 && ($grade == 'Incomplete' || $grade == 'Complete') && !(ctype_space($grade)) && $grade != null){
                $newGradeID += 1;
                $testGradeTypeQuery = true;        
                $newGrade = "Grade submitted";
            }
            else if($gradeType == 3 && !(ctype_space($grade)) && $grade != null){
                $testGradeTypeQuery = true;
                //echo 'Grade by rubric not implemented yet.';
            }
            else{
                $testGradeTypeQuery = false;
                echo ("<h4 style='color:red;'>ERROR ADDING GRADE: incorrect grade and/or grade type </h4>");
            } // end if-else statements

            // If the grade was accepted, add it to the database
            if($testGradeTypeQuery == true){

                $newGradeID += 1;
                $newLogID+=1;
                $timeStamp = date("Y-m-d H:i:s");

                // Add or update the grade in the Grade table
                if ($alreadyGraded == TRUE){
                    $sqlUpdateGrade = "UPDATE Grade SET assignedValue = '$grade' WHERE gradeID = $receivedGradeID";
                    if (!simpleQuery($db, $sqlUpdateGrade)){
                        die("<p>SQL Query Error updating Grade: " . $receivedGradeID . "</p></body></html>\n");
                    }else{
                        echo '<div style="text-align:center; Color:red">'. "<h3>Grade updated to: " . $grade . "</h3>" . '</div>';
                        $newGradeID = $receivedGradeID;
                    }
                }else{
                    $sqlinsertGrade = "INSERT INTO Grade (gradeID, assignedValue, maxScore, labID, classID, sectionID, studentID, typeofGrade, graderID)
                    VALUES ('$newGradeID', '$grade', '$maxPoints', '$currentLabID', '$classID', '$sectionID', '$studentID', '$gradeType', '$graderID')";
                    if(!simpleQuery($db, $sqlinsertGrade)){
                        die('big boy fail </body></html>\n');
                    }else{
                        echo '<div style="text-align:center; Color:red">'. "<h3>Grade successfully added </h3>" . '</div>';
                    }
                }
                
                // Generate a new log for the grade and add to Log table
                $sqlinsertLog = "INSERT INTO Log (LogID, TimeStamp, gradeID, graderID, assignedValue) VALUES ('$newLogID', '$timeStamp', '$newGradeID', '$graderID', '$grade')";
                if(!simpleQuery($db, $sqlinsertLog)){
                    die('log Failed' . $newLogID);
                }
                $grade = "";
            ?>
            <script> 
                //alert("Grade Added Successfully"); 
            </script> 
            <?php
            } // end if($testGradeTypeQuery == true){
        } // end if (isset($_POST['submitted']))
        ?>
        <div class="text-holder left"><!-- this places the skills checklist on the left side of the page-->
            <table>
                <div id="look"><!-- start the grading form formatting -->
                    <tr>
                        <td class = 'gradeSkills'>
                            <!-- Create form to handle entering a grade -->
                            <form method ="POST" action="gradeLab.php" id="enterGrade">
                                <input type="hidden" name="submitted" value="true"/>
                                <input type="hidden" name = "studentLab" value="<?php echo $currentLabID; ?>">
                                <input type="hidden" name = "currentStudentID" value="<?php echo $studentID; ?>">  
                                <input type="hidden" name = "gradedBy" value ="<?php echo $graderID; ?>">                                
                                <fieldset>
                                    <legend><h4>Enter New Grade for: <?php echo $studentName; ?></h4></legend>                    
                                    <label><h4>Grade: <input type="text" name="grade" /></h4></label>
                                    <table class='labDesc'>
                                        <tr>
                                            <?php
                                            if ($alreadyGraded == True){
                                                echo "<td><h4> Student's Current Grade: </h4></td>";
                                                echo "<td>" . $receivedGradeValue . "</td>";
                                            }else{
                                                echo "<td><h4> Student's Current Grade: </h4></td>";
                                                echo "<td> Not Yet Graded </td>";
                                            }
                                            ?>
                                        </tr>
                                    <table class='labDesc'>                    
                                        <tr class='labDesc'>
                                            <td class='labDesc'> <h4> Max Lab Score: </h4> </td> 
                                            <td class='labDesc'> <?php echo $maxPoints . "\t"; ?> </td>
                                            <td class='labDesc'> </td>
                                            <td class='labDesc'> <h4> Lab Grade Type: </h4> </td> 
                                            <td class='labDesc'> <?php echo $typeOfGrade; ?> </td>                            
                                        </tr> 
                                    </table>
                                </fieldset>
                                <br />
                                <button onClick="refreshPage()"> Submit New Grade <br />
                                    <input type="hidden" name = "searchCriteria" value="<?php echo $searchMethod; ?>">
                                </button>             
                                <script>
                                    function refreshPage(){
                                        window.location.reload();
                                    } 
                                </script>
                            </form>
                    </tr>
                </div><!-- end the grading form formatting -->
            </table> <!-- close table for grade entry and skills list -->
        </div><!-- this places the skills checklist on the left side of the page-->

        <div class="text-holder right"><!-- this places the skills checklist on the right side of the page-->
            <div class = 'gradeSkills skills'>
                <h2>Skills Checklist</h2> <!--get the checkbox info from database-->
        
                <?php  
                // Prepare and execute query to get section and class IDs
                $stmt4 = simpleQuery($db, "SELECT sectionID, classID FROM Lab WHERE idLab = $currentLabID");
                if($stmt4 == NULL) { die("<p>SQL Query Error: " . $stmt4->error . "</p></body></html>\n"); }
        
                // Bind variables to the results in same order as simpleQuery
                $stmt4->bind_result($secID, $class);
        
                //clear the results of the query
                while ($stmt4->fetch()) {
                }
        
                // Prepare and execute query to get the skillsListID
                $stmt5 = simpleQuery($db, "SELECT skillsListID FROM Section WHERE sectionID = $secID AND classID = $class");
                if($stmt5 == NULL) { die("<p>SQL Query Error: " . $stmt5->error . "</p></body></html>\n"); }
        
                // Bind variables to the results in same order as simpleQuery
                $stmt5->bind_result($listID);
        
                //clear the results of the query
                while ($stmt5->fetch()) {
                }
                // Prepare and execute query to get skills from the skills list
                $stmt6 = simpleQuery($db, "SELECT description, isLearned, skillID  FROM Skill WHERE skillListID = $listID");
                if($stmt6 == NULL) { die("<p>SQL Query Error: " . $stmt6->error . "</p></body></html>\n"); }

                // Bind variables to the results in same order as simpleQuery
                $stmt6->bind_result($skills, $learned, $skillID);

                echo "<form method='POST' action='gradeLab.php' id='skillCheckboxes'>";

                // Display all the skills for the skill list, with skills already learned selected and disabled
                while ($stmt6->fetch()) {      
                    // As stored in the database, for isLearned attribute in Skill table, 0 = not learned, 1 = learned  
                    if ($learned == 0){
                        echo "<input type='checkbox' name='unLearnedSkills[]' value='" . $skillID . "'>
                                <label for='unLearnedSkills'> $skills </label> <br />";
                    } else{
                        echo "<input type='checkbox' name='learnedSkills[]' checked disabled>
                                <label for='learnedSkills'> $skills </label> <br />";
                    } // end if else
                } // end while loop
            ?>
                <br />
                <button>
                    <input type="hidden" name = "studentLab" value="<?php echo $currentLabID; ?>">
                    <input type="hidden" name = "currentStudentID" value="<?php echo $studentID; ?>"> 
                    <input type="hidden" name = "gradedBy" value ="<?php echo $graderID; ?>">
                    <input type="hidden" name = "searchCriteria" value="<?php echo $searchMethod; ?>">
                    Mark Skills as Learned <br />
                    (double click)      <!-- Need to double click in order to see the change on the web page -->
                </button>               <!-- clicking once only adds it to the DB, does not update the web page -->
                </form>
                
                <?php
                // For each skill that has been selected as learned that was not previously, update 
                //     the Database to mark this skill as learned. 
                foreach($_POST['unLearnedSkills'] as $skill){
                    $stmt7 = simpleQuery($db, "UPDATE Skill SET isLearned = 1 WHERE skillID = $skill");
                    if($stmt7 == NULL) { 
                        die("<p>SQL Query Error: " . $stmt7->error . "</p></body></html>\n"); 
                    }else{
                        $success = True;
                        // Reload the page to display the newly learned skills
                        //echo "<meta http-equiv='refresh' content='0'>";
                    }    
                } // end foreach loop
            ?>
            </div><!--grade skills -->
        </div><!-- this places the skills checklist on the right side of the page-->
            <?php

                // Prepare and execute query to get the notes for the note sheet
                $stmt2 = simpleQuery($db, "SELECT noteText FROM Note WHERE noteSheetID = $noteSheetID");
                if($stmt2 == NULL) { die("<p>SQL Query Error: " . $stmt2->error . "</p></body></html>\n"); }

                // Bind variables to the results in same order as simpleQuery
                $stmt2->bind_result($text);
                ?>
                
                <table class='noteDesc'>
                <th><h1>Lab Notes:</h1></th>

                <?php
                // Show all the notes for this lab (in a table)
                while ($stmt2->fetch()) {
                    echo "<tr class='noteDesc'> <td class='noteDesc'>" . $text . "</td></tr>"; 
                }
                
                $addNote = TRUE;
                // Ensure we can get the note text from the text area and it is not empty
                if(isset($_POST["newNote"]) && !(empty($_POST["newNote"])))
                {
                    $comment=$_POST["newNote"];
                }
                else{
                    $addNote = FALSE;
                } 

                // If we successfully got the note text to add and it is not all whitespace, add it to the database
                if ($addNote && !(ctype_space($comment))){
                    // Prepare and execute query 2
                    $stmt4 = simpleQuery($db, "SELECT MAX(noteID) FROM Note WHERE noteSheetID = $noteSheetID");
                    if($stmt4 == NULL) { die("<p>SQL Query Error: " . $stmt4->error . "</p></body></html>\n"); }

                    // Bind variables to the results
                    // $lastNote is the note with the largest noteID for the given note sheet and is used to generate the noteID for the new note by adding 1 to it
                    $stmt4->bind_result($lastNote);

                    // clear the results of the query - need to do in order to use $lastNote variable more than once
                    while ($stmt4->fetch()) {
                    }

                    $newNoteText = $comment;
                    $lastNote = $lastNote + 1;
                    
                    //insert new note into database
                    $sql = "INSERT INTO Note (noteID, noteText, graderID, noteSheetID)
                    VALUES ('$lastNote', '$newNoteText', '$graderID', '$noteSheetID')";
                    
                    // Run the query and display the new note if successfull
                    // NOTE: Adding a note, then reloading the page adds the note again. Can't figure out how to fix this
                    if ($db->query($sql) === TRUE && !(ctype_space($newNoteText))) {
                        echo "<tr class='noteDesc'> <td class='noteDesc'>" . $newNoteText . "</td></tr>"; 
                        $newNoteText = "";
                        $comment = "";                
                    } else {
                        echo "Error: " . $sql . "<br>" . $db->error;
                    }
                } // end if ($addNote && !(ctype_space($comment))){
                ?>
                </table>

                <div id='buttonsNText'>
                    <!-- Display button for adding a new note -->
                    <form action="gradeLab.php" name="noteform" id="noteform" method="POST">
                        <br />
                        <textarea rows="4" cols="60" id="comment" form="noteform" name = "newNote"></textarea>
                        <br />
                        <br />
                        <button>
                            <input type="hidden" name = "studentLab" value="<?php echo $currentLabID; ?>">
                            <input type="hidden" name = "currentStudentID" value="<?php echo $studentID; ?>">  
                            <input type="hidden" name = "gradedBy" value ="<?php echo $graderID; ?>">
                            <input type="hidden" name = "searchCriteria" value="<?php echo $searchMethod; ?>">
                            Click to Save New Note
                        </button>   
                    </form>

                    <!-- Display a button to delete note -->
                    <form action="deleteNote.php" method="POST" id="noteDeletion">
                        <br />
                        <button>
                            <input type="hidden" name = "sheetID" value="<?php echo $noteSheetID; ?>">            
                            <input type="hidden" name = "currentLabID" value="<?php echo $currentLabID; ?>">
                            <input type="hidden" name = "studentID" value="<?php echo $studentID; ?>">  
                            <input type="hidden" name = "grader" value ="<?php echo $graderID; ?>"> 
                            <input type="hidden" name = "searchBy" value="<?php echo $searchMethod; ?>">         
                            Click to Delete a Note
                        </button>            
                    </form>
                    <br />
                </div> <!-- close buttonsNText -->

                
                <br />

        <?php

        // Close the database connection
        $stmt12->close();
        $stmt11->close();
        $stmt10->close();
        $stmt9->close();
        $stmt8->close();
        $stmt7->close();
        $stmt6->close();
        $stmt5->close();
        $stmt4->close();
        $stmt3->close();
        $stmt2->close();
        $stmt1->close();
        $db->close();
    } // end if(isset($_POST['currentStudentID']) && isset($_POST['studentLab']))
    else{
        die("<h2 style='color:red;'>ERROR: No student and/or lab selected. Please go back 
            to search and select a lab/student to grade. </h2></body></html>\n");
    }
    ?>

</body>
</html>