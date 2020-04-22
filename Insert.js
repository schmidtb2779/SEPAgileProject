var mysql = require('mysql')

var con = mysql.createConnection({
    host:"144.13.22.59:3306",
    user:"G7_Admin",
    password:"aexpBrandon",
    database:"G7AgileExperience"
})

con.connect(function(err){
    if(err)throw err;
    console.log("Connected!");
    var sql = "INSERT INTO class (idClass, name) VALUES ('101', 'FAKE Class 1')";
    con.query(sql, function(err, result){
        if(err) throw err;
        console.log("1 record inserted");
        //console.log(result);
    });
});