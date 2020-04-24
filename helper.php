<?php
abstract class DBDeets {
  const DB_NAME_G7 = 'G7AgileExperience';
  const DB_USER = 'g7AppUser';
  const DB_PW = 'aug7';
  const DB_SILENT_FAIL = FALSE;
}

function connectToDatabase($databaseName) {
  // Attempt to connect using the constants from above 
  $db = new mysqli('localhost', DBDeets::DB_USER, DBDeets::DB_PW, $databaseName);

  // Detect and report any errors (if SILENT_FAIL is not true)
  if ($db->connect_errno && !DBDeets::DB_SILENT_FAIL) {
    echo "<!-- FAILED DB CONNECT: ($db->connect_errno) $db->connect_error -->\n";
  }

  // Return the handle to the mysql connection (which may be in an error state)
  return $db;
}

function simpleQuery($db, $query) {
  // Prepare the query using the passed in db connection
  if(!($stmt = $db->prepare($query))) {
    if (!DBDeets::DB_SILENT_FAIL) {
      echo "<!-- FAILED QUERY PREPARE: ($db->errno) $db->error -->\n";
    }
    return null;
  }

  // Execute the prepared query
  if(!$stmt->execute()) {
    if (!DBDeets::DB_SILENT_FAIL) {
      echo "<!-- FAILED QUERY EXECUTE: check that database and statement are still open and valid -->\n";
    }
    return null;
  }

  // If it is a SELECT query, cache the results for quick access
  if(strpos($query, 'SELECT') !== false) {
    $stmt->store_result();
  }

  // return the statement object
  return $stmt;
}

function simpleQueryParam($db, $query, $ptype, &$param) {
  // Prepare the query
  if(!($stmt = $db->prepare($query))) {
    if (!DBDeets::DB_SILENT_FAIL) {
      echo "<!-- FAILED QUERY PREPARE: ($db->errno) $db->error -->\n";
    }
    return null;
  }

  // Bind input param
  if(!($stmt->bind_param($ptype, $param))) {
    if (!DBDeets::DB_SILENT_FAIL) {
      echo "<!-- FAILED BIND PARAM: Did you leave a ? in your query -->\n";
    }
    return null;
  }

  // Execute query
  if(!$stmt->execute()) {
    if (!DBDeets::DB_SILENT_FAIL) {
      echo "<!-- FAILED QUERY EXECUTE: check that database and statement are still open and valid -->\n";
    }
    return null;
  }

  // Store the results for SELECT queries
  if(strpos($query, 'SELECT') !== false) {
    $stmt->store_result();
  }

  // return the statement object
  return $stmt;
}
?>