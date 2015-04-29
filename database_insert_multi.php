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
?>