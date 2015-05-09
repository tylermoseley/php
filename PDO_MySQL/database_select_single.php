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

?>