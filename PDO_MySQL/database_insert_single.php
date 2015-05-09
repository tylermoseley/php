<?php

include 'database.class.php';

// Connection Configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "tap");
define("DB_NAME", "example");

$database = new database();

//INSERT STATEMENT WITH PLACEHOLDERS
$database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
//BIND DATA TO PLACEHOLDERS
$database->bind(':fname', 'John');
$database->bind(':lname', 'Smith');
$database->bind(':age', '24');
$database->bind(':gender', 'male');
//EXECUTE STATEMENT
$database->execute();
//ECHO lastInsertId TO VERIFY EXECUTION
echo $database->lastInsertId();
?>