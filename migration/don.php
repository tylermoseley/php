<?php
//START TIME
date_default_timezone_set('America/New_York');
echo "Began at: ". date('m/d/Y h:i:sa') ."\n";
flush();
$starttime = microtime(true);

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

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)
    or die ("Could not connect to database, error: " . mysqli_error($link));


//SELECT STATEMENT
$units_sql = '
SELECT
CONCAT (CCODNDDR.NDDR_CODE, UNIT_NO) AS UNIT_NO,
CONCAT (CCODNDDR.NDDR_CODE, DONOR_NO) AS DONOR_NO,
CCODNDDR.NDDR_CODE,
TYP_PREL.E_P_TYP AS PHLEB_TYPE,
GLOBAL_GMML.MLS,
IF (DATE_FORMAT (DONOR_DATE, "%d%m%Y")="00000000","",DATE_FORMAT (DONOR_DATE, "%d%m%Y")) AS DON_DATE
FROM allpds3data.UNIT
JOIN allpds3data.CCODNDDR
ON UNIT.C_CODE=CCODNDDR.C_CODE
LEFT JOIN allpds3data.TYP_PREL
ON SUBSTR(UNIT.BOXING_KEY,7,2)=TYP_PREL.P_P_TYP
LEFT JOIN allpds3data.GLOBAL_GMML
ON UNIT.WT_BOTT=GLOBAL_GMML.GMS 
WHERE 1
ORDER BY UNIT.C_CODE, DONOR_DATE
';

//EXECUTE STATEMENT
$units_query = mysqli_query($link,$units_sql)
        or die ("Error in units_sql");

//OPEN ASCII FILE IF NOT EXISTS, OVERRIDE IF EXISTS  
$don_asc = fopen('ePro/don.asc','w');

//QUERY TIME
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Query time: " . gmdate("H:i:s", $elapsedtime) . "\n";

//DO WORK
$count = 0;
while ($don = mysqli_fetch_array($units_query, MYSQLI_ASSOC)) {
	$line_string = 
	$don["UNIT_NO"] . "|" .
	$don["DONOR_NO"] . "|" .
	$don["NDDR_CODE"] . "|" .
	$don["DON_DATE"] . "|" .
	$don["NDDR_CODE"] . "|||" . 
	$don["PHLEB_TYPE"] . "|||||||||" . 
	$don["MLS"] . "||||||" .
	"||||"
	;
	$line_string .= "\n";
	fputs ($don_asc,$line_string);
	$count++;
	echo $count . "\r";
}


//CLOSE .ASC FILE
fclose($don_asc);

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "\n Completed at: " . date('m/d/Y h:i:sa') . "\n";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
