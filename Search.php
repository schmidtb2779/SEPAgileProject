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
    <title>Search</title>
</head>
<body>

    <h1>Search</h1>

    Enter Search Criteria: <br/><br/>
    <form action = "Search.php" method = "post">

    <textarea rows="2" cols="30" id="comment" form="searchForm"></textarea><br>
    <input type="radio" id="Student Name" name = "options" value = "Student Name"/>
    <label for="Student Name">Student Name</label><br>
    <input type="radio" id="Class Name" name = "options" value = "Class Name"/>
    <label for="Class Name">Class Name</label><br>
    <input type="radio" id="Professor Name" name = "options" value = "Professor Name"/>
    <label for="Professor Name">Professor Name</label><br>

    <br><br>

    <input type="submit" value = "Submit"/> <!-- To do: make this search the database -->
    </form>
    
    <?php
    $result = _$POST['options']
    if($result =="Student Name"){
        //Search database via student name
    }
    else if($result =="Class Name"){
        //Search database via class name
    }
    else if($result =="Professor Name"){
        //Search database via Professor Name
    }

</body>

</html>

<?php
// Close the database connection at the end
$stmt1->close();
$db->close();
?>