<?php
//////////////////////////////////////////DATA CONNECTION///////////////////////////////////////
include 'database.class.php';

// Connection Configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "tap");
define("DB_NAME", "example");

$database = new database();

//////////////////////////////////////////INSERT SINGLE/////////////////////////////////////////
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

/////////////////////////////////////INSERT MULTI WITH TRANSACTION//////////////////////////////
//BEGIN TRANSACTION
$database->beginTransaction();
//INSERT STATEMENT WITH PLACEHOLDERS
$database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
//BIND DATA TO PLACEHOLDERS
$database->bind(':fname', 'Jenny');
$database->bind(':lname', 'Smith');
$database->bind(':age', '23');
$database->bind(':gender', 'female');
//EXECUTE STATEMENT
$database->execute();
//BIND 2ND DATA TO PLACEHOLDERS
$database->bind(':fname', 'Jilly');
$database->bind(':lname', 'Smith');
$database->bind(':age', '25');
$database->bind(':gender', 'female');
//EXECUTE STATEMENT
$database->execute();
//ECHO lastInsertId TO VERIFY EXECUTION
echo $database->lastInsertId();
//END TRANSACTION
$database->endTransaction();

//////////////////////////////////////////SELECT SINGLE/////////////////////////////////////////
//BEGIN TRANSACTION
$database->beginTransaction();
//SELECT STATEMENT WITH PLACEHOLDER
$database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE FName = :fname');
//BIND DATA TO PLACEHOLDER
$database->bind(':fname', 'Jenny');
//EXECUTE STATEMENT AND SAVE TO ARRAY
$row = $database->single();
//ECHO RESULTS TO SCREEN
echo "<pre>";
print_r($row);
echo "</pre>";

/////////////////////////////////SELECT MULTI WITH TRANSACTION AND ROW COUNT////////////////////////////////////
//BEGIN TRANSACTION
$database->beginTransaction();
//SELECT STATEMENT WITH PLACEHOLDERS
$database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname');
//BIND DATA TO PLACEHOLDERS
$database->bind(':lname', 'Smith');
//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$rows = $database->resultset();
//ECHO RESULTS TO SCREEN
echo "<pre>";
print_r($rows);
echo "</pre>";
//ECHO ROW COUNT TO SCREEN
echo $database->rowCount();
?>