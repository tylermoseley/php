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

$database = new database();

//SELECT STATEMENT
$database->query('
SELECT
CONCAT(CCODNDDR.NDDR_CODE,DONOR_NO) AS DONOR_NO,
REJDONR.REJ_REASON,
REJDONR.REJ_PERIOD,
COD_INTER.E_DEF_CODE AS DEF_CODE,
IF(DATE_FORMAT(REJ_DATE,"%d%m%Y")="00000000","",DATE_FORMAT(REJ_DATE,"%d%m%Y")) AS F_REJ_DATE,
REJ_DATE
FROM allpds3data.REJDONR
JOIN allpds3data.COD_INTER
ON CONCAT(COD_INTER.P_DEF_TEXT,COD_INTER.P_DEF_PER)=CONCAT(REJDONR.REJ_REASON,REJDONR.REJ_PERIOD)
JOIN allpds3data.CCODNDDR
ON CCODNDDR.C_CODE=REJDONR.C_CODE
WHERE 1
ORDER BY REJDONR.C_CODE
');
//EXECUTE STATEMENT AND SAVE TO MULTI_DIMENSIONAL ARRAY
$rejections = $database->resultset_assoc();

//OPEN .ASC FILE IF NOT EXISTS, OVERWRITE IF EXISTS  
$inter_asc = fopen('ePro/inter.asc','w');

//DO WORK
foreach ($rejections as $rejection) {
	//CALCULATE END OF REJECTION	
	if ($rejection["REJ_PERIOD"] == "VARIABLE" OR $rejection["REJ_PERIOD"] == "PERMANENT") {
		$dur = 99999;
		$typdur = "DAYS";
	} else {
		$len = strlen($rejection["REJ_PERIOD"]);
		$pos = strpos($rejection["REJ_PERIOD"]," ");
		$dur = substr($rejection["REJ_PERIOD"],0,$pos);
		$typdur = substr($rejection["REJ_PERIOD"],$pos+1,$len-$pos);
	}
	if ($typdur == "WEEKS") {
		$dur = $dur*7;
	} elseif ($typdur == "MONTHS") {
		$dur = round($dur*30.41666666666667,0);
	} elseIF ($typdur == "YEARS") {
		$dur = $dur*365;
	}
	$rej_date = new DateTime($rejection["REJ_DATE"]);
	$rej_date->add(new DateInterval('P' . $dur . 'D'));
	$enddef = $rej_date->format('dmY');
	
	//GENERATE STRING AND PRINT LINE TO FILE
	$line_string = 
	$rejection["DONOR_NO"] . "|" . 
	$rejection["DEF_CODE"] . "|" . 
	$rejection["F_REJ_DATE"] . "|" . 
	$enddef . "||"
	;
	$line_string .= "\n"; 
	fputs($inter_asc,$line_string); 
}

//CLOSE .ASC FILE
fclose($inter_asc);

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "\n";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
