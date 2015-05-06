<?php

include 'database.class.php';

// Connection Configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "tap");
define("DB_NAME", "allpds3data");

$database = new database();


//SELECT STATEMENT WITH PLACEHOLDERS
$database->query('
SELECT CONCAT(C_CODE,DONOR_NO) AS DONOR_NO,
MIN(TET_DATE) AS TET_DATE,
UNIT_NO
FROM TETANUS
WHERE 1
GROUP BY CONCAT(C_CODE,DONOR_NO)
');
//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$init_donor_shot = $database->resultset();


//SELECT STATEMENT WITH PLACEHOLDERS
$database->query('
SELECT CONCAT(C_CODE,DONOR_NO) AS DONOR_NO,
MIN(TET_DATE) AS TET_DATE,
UNIT_NO
FROM TETANUS
WHERE UNIT_NO != "*******"
GROUP BY CONCAT(C_CODE,DONOR_NO)
');
//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$init_unit = $database->resultset();


//OPEN .ASC FILE IF NOT EXISTS, OVERWRITE IF EXISTS  
$donor_program_asc = fopen('ePro/donor_program.asc','w');

//DO WORK
foreach ($init_donor_shot as $donor) {
    //ADD NDDR CONVERSION
    $line_string = $donor["DONOR_NO"] . "|004|1|1|1|||||||";
    if ($donor['UNIT_NO'] == "*******") {
        $line_string .= $donor['TET_DATE'] . "|||";
    } else {
        $line_string .= "|||";
    }
    foreach ($init_unit as $first_unit) {
        if ($first_unit["DONOR_NO"] == $donor["DONOR_NO"]) {
            $line_string .= $first_unit['TET_DATE'] . "|||";
        }
    }
    $line_string .= "\n";
    fputs($donor_program_asc,$line_string);
}

//CLOSE .ASC FILE
fclose($donor_program_asc);

?>