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
SELECT CONCAT(CCODNDDR.NDDR_CODE,DONOR_NO) AS DONOR_NO,
LAST,
FIRST,
SEX,
IF(DATE_FORMAT(BIRTH_DATE,"%d%m%Y")="00000000","",DATE_FORMAT(BIRTH_DATE,"%d%m%Y")) AS BIRTH_DATE,
MIDDLE,
SSNO,
STREET1,
STREET2,
ZIP1,
CITY,
PHONE,
STATE,
EMAIL_ADDR,
IF(DATE_FORMAT(ENTRY_DATE,"%d%m%Y")="00000000","",DATE_FORMAT(ENTRY_DATE,"%d%m%Y")) AS ENTRY_DATE
FROM allpds3data.REGDONR
JOIN allpds3data.CCODNDDR
ON REGDONR.C_CODE=CCODNDDR.C_CODE
WHERE 1
');
//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$reg_donors = $database->resultset_both();


//OPEN .ASC FILE IF NOT EXISTS, OVERWRITE IF EXISTS  
$donneur_asc = fopen('donneur.asc','w');

//DO WORK
foreach ($reg_donors as $donor) {
    $line_string =
        $donor["DONOR_NO"] . "|" .
        $donor["LAST"] . "|" .
        soundex($donor["LAST"]) . "||" .
        $donor["FIRST"] . "|" .
        $donor["SEX"] . "|" .
        $donor["BIRTH_DATE"] . "|||||" .
        $donor["MIDDLE"] . "||||" .
        $donor["SSNO"] . "|||" .
        $donor["STREET1"] . "|" .
        $donor["STREET2"] . "|" .
        $donor["ZIP1"] . "|" .
        $donor["CITY"] . "||" .
        $donor["PHONE"] . "|" .
        $donor["STATE"] . "||||||||||||||||||" .
        "PHLEB_CODE" . "|||||||||||||||||||||||||||||" .
        $donor["EMAIL_ADDR"] . "|" .
        $donor["ENTRY_DATE"] . "||" .
    $line_string .= "\n";
    fputs($donneur_asc,$line_string);
}

//CLOSE .ASC FILE
fclose($donneur_asc);

?>