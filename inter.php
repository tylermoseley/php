<?php

include 'database.class.php';

// Connection Configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "plaut0mati0n");
define("DB_NAME", "allpds3data");

$database = new database();

//BEGIN TRANSACTION
$database->beginTransaction();

//SELECT STATEMENT WITH PLACEHOLDERS
$database->query('
SELECT CONCAT(C_CODE,DONOR_NO) AS DONOR_NO,
MIN(TET_DATE),
UNIT_NO
FROM TETANUS
WHERE UNIT_NO = "*******"
GROUP BY CONCAT(C_CODE,DONOR_NO)
');

//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$init_shot = $database->resultset();

//SELECT STATEMENT WITH PLACEHOLDERS
$database->query('
SELECT CONCAT(C_CODE,DONOR_NO) AS DONOR_NO,
MIN(TET_DATE),
UNIT_NO
FROM TETANUS
WHERE UNIT_NO != "*******"
GROUP BY CONCAT(C_CODE,DONOR_NO)
');

//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$init_unit = $database->resultset();


//ECHO RESULTS TO SCREEN
echo "<pre>";
print_r($rows);
echo "</pre>";

//ECHO ROW COUNT TO SCREEN
echo $database->rowCount();

?>