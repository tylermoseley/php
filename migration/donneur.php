<?php
//START TIME
date_default_timezone_set('America/New_York');
echo "Began at: ". date('m/d/Y h:i:sa') ."\n";
flush();
$starttime = microtime(true);

include 'database.class.php';

//CONNECTION VARIABLES
$ip = getHostByName(getHostName());
if ($ip == '10.2.1.102') {
    define("DB_HOST", "localhost");
    define("DB_USER", "root");
    define("DB_PASS", "plaut0mati0n");
} else {
    define("DB_HOST", "10.2.1.102");
    define("DB_USER", "remote");
    define("DB_PASS", "t1a2p3");
}
define("DB_NAME", "allpds3data");

//NEW CONNECTION OBJECT
$database = new database();

//SELECT STATEMENT
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
$reg_donors = $database->resultset_assoc();

//SELECT STATEMENT
$database->query("
    SELECT *
    FROM CP_VILL_DONN
    WHERE 1
    ");
//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$zip_list = $database->resultset_assoc();



//OPEN .ASC FILE IF NOT EXISTS, OVERWRITE IF EXISTS  
$donneur_asc = fopen('ePro/donneur.asc','w');

//DO WORK
foreach ($reg_donors as $donor) {
	$matches = array();	
	$count1 = 0;
	foreach ($zip_list as $zip_city) {
		if ($donor["ZIP1"] = "" AND $donor["CITY"] == "") {
			$finalzip = "99999";
			$finalcity = "blank";
		} elseif ($donor["ZIP1"] == "" AND $donor["CITY"] != "") {
			$finalzip = "99999";
			$finalcity = $donor["CITY"];	
		
		}
		//echo "|" . substr($zip_city["ZIP"],0,5) . "|" . $donor["ZIP1"] . "|\n";
		if (substr($zip_city["ZIP"],0,5) == $donor["ZIP1"]) {
			//echo $zip_city["ZIP"] . "|" . $zip_city["CITY"] . "|" . $donor["ZIP1"] . "|" . $donor["CITY"] . "|\n"; 
			$matches[$count1]["ZIP"] = $zip_city["ZIP"];
			$matches[$count1]["CITY"] = $zip_city["CITY"];
			$count1++;
		} else {
		//do nothing
		}
	}	
	//echo "......." . count($matches) . "\n";
	if (count($matches) == 1) {
		$finalzip = $matches[0]["ZIP"];
		$finalcity = $matches[0]["CITY"]; 
	} elseif (count($matches) == 0) {
		
	} else {
		$finalzip = "undetermined";
		$finalcity = "undetermined";
	}

    
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
        $finalzip . "|" .
        $finalcity . "||" .
        //$donor["ZIP1"] . "|" .
        //$donor["CITY"] . "||" .
        $donor["PHONE"] . "|" .
        $donor["STATE"] . "||||||||||||||||||" .
        "PHLEB_CODE" . "|||||||||||||||||||||||||||||" .
        $donor["EMAIL_ADDR"] . "|" .
        $donor["ENTRY_DATE"] . "||"
        ;
    $line_string .= "\n";
    fputs($donneur_asc,$line_string);
}

//CLOSE .ASC FILE
fclose($donneur_asc);

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "<\n>";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
