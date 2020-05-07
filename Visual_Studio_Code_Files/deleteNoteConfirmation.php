<!-- Agile Experience Group 7 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Delete Note Confirmation</title>
    <script type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="deleteNote.css"/>

    <!-- for some reason the css delete note file wasn't letting me update it so I'm formatting in the same file for now-->
    <!-- this should eventually be moved to css file-->
    <style>
        body { 
        background: url(StoutImageFour.jpg) no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        }
        h1{
        font-size: 30px;
        color:white;
        }
        p{
        color:white;
        }
        #notes{
        width: auto;
        min-width: 400px;
        max-width: 1200px;
        background-color: #9cb2cc;
        font-size: 25px;
        overflow: auto;
        padding: 10px;
        }
    </style>
</head>

<body>

    <?php
        $labID = $_POST['currentLab'];
        $student = $_POST['currentStudent'];

        include "helper.php";

        $db = connectToDatabase();
        if($db == NULL) { die("<p>Connection Error </p></body></html>\n"); }

        if($db == NULL) { die("<p>Connection Error </p></body></html>\n"); }

        if (isset($_POST['deletedBy'])){
            $graderID = $_POST['deletedBy'];
        }else{
            die("<h2 style='color:red;'>ERROR: Unable to get graderID. </h2></body></html>\n");
        }

        // Make sure we have the NoteSheetID and that a Note was selected 
        if (isSet($_POST["confirmDelete"])){
            $noteSheetID = $_POST["confirmDelete"];
            if (isSet($_POST["notes"])){
                // The text of the note to be deleted
                $toDelete = $_POST["notes"];
    
                // Prepare and execute query to get noteID
                $stmt1 = simpleQuery($db, "SELECT noteID FROM Note WHERE noteText = '$toDelete'");
                if($stmt1 == NULL) { die("<p>SQL Query Error Finding Note: " .$stmt1 . $stmt1->error . "</p></body></html>\n"); }
    
                // Bind variables to the results in same order as simpleQuery
                $stmt1->bind_result($noteID);
    
                // Store the noteID for the note to be deleted
                while ($stmt1->fetch()) {
                    $toDeleteID = $noteID;
                }
                    
                // Delete the note from the database
                $stmt2 = simpleQuery($db, "DELETE FROM Note WHERE noteID='$toDeleteID'");
                if($stmt2 == NULL) { 
                    die("<p>SQL Query Error Finding NoteID to delete: " .$stmt2 . $stmt2->error . "</p></body></html>\n"); 
                }else{
                    //echo "Note with ID '" . $toDeleteID . "' successfully deleted. <br />";
                    //echo "Deleted Note Text: '" . $toDelete ."'";
                    echo "<p style='color:white;'>'" . $toDelete . "' successfully deleted.</p><br />";
                }
                ?>

                 <!-- create return buttons for after the note is deleted -->
                <form method="POST" action="deleteNote.php" id="noteDeletion2">
                    <br />
                    <button>
                        <input type="hidden" name = "sheetID" value="<?php echo $noteSheetID; ?>">
                        <input type="hidden" name = "currentLabID" value="<?php echo $labID; ?>">
                        <input type="hidden" name = "studentID" value="<?php echo $student; ?>">
                        <input type="hidden" name = "grader" value ="<?php echo $graderID; ?>">
                        <input type="hidden" name = "searchBy" value = "<?php echo $_POST['search'];?>">
                        Back to Delete Note                        
                    </button>
                </form>         
    
                <form action="gradeLab.php" method="POST" id="noteform3">
                    <br />
                    <button> 
                    <input type="hidden" name="studentLab" value="<?php echo $labID;?>">
                    <input type="hidden" name="currentStudentID" value="<?php echo $student;?>">
                    <input type="hidden" name = "gradedBy" value ="<?php echo $graderID; ?>">   
                    <input type="hidden" name = "searchCriteria" value = "<?php echo $_POST['search'];?>">                 
                    Back to Grade Lab </button>
                </form>
                
                <?php
            } else{
                echo "<p style='color:white;'> No note was selected to delete </p>";
                ?>
                <!-- Create a button to return to deleteNote page -->
                <form method="POST" action="deleteNote.php" id="noteDeletion2">
                    <br />
                    <button>
                        <input type="hidden" name = "sheetID" value="<?php echo $noteSheetID; ?>">
                        <input type="hidden" name = "currentLabID" value="<?php echo $labID; ?>">
                        <input type="hidden" name = "studentID" value="<?php echo $student; ?>"> 
                        <input type="hidden" name = "grader" value ="<?php echo $graderID; ?>">
                        <input type="hidden" name = "searchBy" value = "<?php echo $_POST['search'];?>">
                        Previous Page
                    </button>
                </form>
            <?php
            } 
        } else{
            echo die("<h2 style='color:red;'>ERROR: Unable to fetch noteSheetID</h2></body></html>\n");
        }
    
        // Close the database connection
        $stmt2->close();
        $stmt1->close();
        $db->close();
        ?>
</body>
</html>

