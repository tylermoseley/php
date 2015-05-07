<?php

include 'database.class.php';

// Connection Configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "plaut0mati0n");
define("DB_NAME", "allpds3data");

$database = new database();

//SELECT STATEMENT WITH PLACEHOLDERS
$database->query('

');

//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$rows = $database->resultset();



//OPEN .ASC FILE IF NOT EXISTS, OVERWRITE IF EXISTS  
$inter_asc = fopen('ePro/donor_program.asc','w');

//ECHO RESULTS TO SCREEN
echo "<pre>";
print_r($rows);
echo "</pre>";

//ECHO ROW COUNT TO SCREEN
echo $database->rowCount();


//CLOSE .ASC FILE
fclose($inter_asc);

?>