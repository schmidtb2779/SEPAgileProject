<!-- Agile Experience Group 7 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Delete Note</title>
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

    <?php
    $labID = $_POST['currentLabID'];
    $studentID = $_POST['studentID'];

    ?>

    <h1> Select a Note to Delete: </h1>

    <?php
        include "helper.php";

        $db = connectToDatabase();
        if($db == NULL) { die("<p>Connection Error </p></body></html>\n"); }

        if (isset($_POST['grader'])){
            $graderID = $_POST['grader'];
        }else{
            die("<h2 style='color:red;'>ERROR: Unable to get graderID. </h2></body></html>\n");
        }

        // Ensure we got the noteSheetID passed in from the gradeLab.php page
        if (isSet($_POST["sheetID"])){
            $noteSheetID = $_POST["sheetID"];
            echo "<br />";
                // Prepare and execute query to get all the noteTexts for the noteSheet
                $stmt2 = simpleQuery($db, "SELECT noteText FROM Note WHERE noteSheetID = $noteSheetID");
                if($stmt2 == NULL) { die("<p>SQL Query Error: " . $stmt2->error . "</p></body></html>\n"); }

                // Bind variables to the results
                $stmt2->bind_result($text);

                $numRows = $stmt2 -> num_rows;

                if ($numRows > 10){
                    $numRows = 10;
                }                

                // Create a form to hold a drop-down menu with the notes
                echo "<form action='deleteNoteConfirmation.php' method ='POST' id='deleteNoteConfirmation'>";
                echo "<select id='notes' name='notes' size=" . $numRows . ">";

                if ($numRows == 0){
                    echo "<option value='noNotes'> No notes to display for selected lab </option>";
                }else {
                    // Display all the notes for this lab
                    while ($stmt2->fetch()) {
                        echo "<option value='" . $text . "'>" . $text . "</option>";
                    }
                } // enf if-else
                echo "</select>";
            ?>
            
            <br /> <br />
            <!-- Create Delete and Back buttons -->
            <script> var noteSheetIDNum = "<?php echo $noteSheetID ?>"; </script>
            <button> 
                <input type="hidden" name="confirmDelete" value="<?php echo $noteSheetID;?>">
                <input type="hidden" name="currentLab" value="<?php echo $labID;?>">
                <input type="hidden" name="currentStudent" value="<?php echo $studentID;?>">
                <input type="hidden" name = "deletedBy" value ="<?php echo $graderID; ?>">  
                <input type="hidden" name = "search" value = "<?php echo $_POST['searchBy'];?>">
                Delete Note </button>
            </form>

            <form action="gradeLab.php" method="POST" id="noteform2">
                <br />
                <button> 
                    <input type="hidden" name="studentLab" value="<?php echo $labID;?>">
                    <input type="hidden" name="currentStudentID" value="<?php echo $studentID;?>">
                    <input type="hidden" name = "gradedBy" value ="<?php echo $graderID; ?>">
                    <input type="hidden" name = "searchCriteria" value = "<?php echo $_POST['searchBy']; ?>">
                    Back to Grade Lab 
                </button>
            </form>
            <?php
        } else{
            die("<h2 style='color:red;'>ERROR: No lab selected to delete notes from. </h2></body></html>\n");
        }

        // Close the database connection
        $stmt2->close();
        $db->close();    
        ?>        
</body>
</html>