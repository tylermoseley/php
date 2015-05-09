<?php

include 'database.class.php';

// Connection Configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "tap");
define("DB_NAME", "example");

$database = new database();

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