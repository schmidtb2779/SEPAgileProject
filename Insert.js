// Require MySQL module
var mysql = require('mysql')

// Create connection variable
var con = mysql.createConnection({
    host:"144.13.22.59:3306",
    user:"g7AppUser",
    password:"aug7",
    database:"G7AgileExperience"
})

// Connect to database
con.connect(function(err){
    if(err)throw err;
    console.log("Connected!");

    // Create SQL query
    var sql = "INSERT INTO class (idClass, name) VALUES ?";
    // Insert values
    var values = [
        ['1', 'TEST Class 1'],
        ['2', 'TEST Class 2'],
        ['3', 'TEST Class 3']
    ];

    // Execute query by using query method
    con.query(sql, function(err, result){
        if(err) throw err;
        console.log("Number of records inserted: " + result.affectedRows);
        //console.log(result);
    });
});