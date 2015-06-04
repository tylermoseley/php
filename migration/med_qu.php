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
$q_codes_sql = '
SELECT P_VITALS, COD_QUE
FROM MQ_VITALS
WHERE 1
';
//EXECUTE STATEMENT 

$q_codes_query = mysqli_query($link,$q_codes_sql)
        or die ("Error in q_codes_sql");

//POPULATE ARRAY OF TEST CODES FOR FASTER CONVERSION
$Q_CODE = array();
while($qu_code = mysqli_fetch_array($q_codes_query, MYSQLI_ASSOC)) {
	$Q_CODE[$qu_code["P_VITALS"]] = $qu_code["COD_QUE"];
}

echo "\nQ_Codes Complete\n";


//SELECT STATEMENT
$units_sql = '
SELECT
CONCAT(CCODNDDR.NDDR_CODE,UNIT.UNIT_NO) AS UNIT_NO,
IF(DATE_FORMAT(UNIT.DONOR_DATE,"%d%m%Y")="00000000","",DATE_FORMAT(UNIT.DONOR_DATE,"%d%m%Y")) AS DONOR_DATE,
CCODNDDR.NDDR_CODE,
UNIT.DONOR_WT,
UNIT.HEMATOCRIT,
UNIT.BPSYS,
UNIT.BPDIA,
UNIT.PULSE,
UNIT.TEMPER,
UNIT.TL_PROTEIN
FROM allpds3data.UNIT
JOIN allpds3data.CCODNDDR
ON UNIT.C_CODE=CCODNDDR.C_CODE
WHERE 1
';
//EXECUTE STATEMENT
$units_query = mysqli_query($link,$units_sql)
        or die ("Error in units_sql");

//BUILD ARRAY OF TEST TYPES
$RESULS = array('DONOR_WT', 'HEMATOCRIT', 'BPSYS', 'BPDIA', 'PULSE', 'TEMPER', 'TL_PROTEIN');

//OPEN ASCII FILE IF NOT EXISTS, OVERRIDE IF EXISTS  
$med_qu_asc = fopen('ePro/med_qu.asc','w');

$count1 = 0;
$unitcount = mysqli_num_rows($units_query);
while ($unit = mysqli_fetch_array($units_query, MYSQLI_ASSOC)) {
	foreach ($RESULS as $RESUL) {
		if ($unit[$RESUL] != "" || $unit[$RESUL] != 0) {
			$line_string = 
			$unit["UNIT_NO"] . "|" . 
			$unit["DONOR_DATE"] . "|" . 
			$unit["NDDR_CODE"] . "|" . 
			$Q_CODE[$RESUL] . "|" . 
			$unit[$RESUL] . "||"
			;
			$line_string .= "\n";
			fputs ($med_qu_asc,$line_string);	 
		}
	}
	$count1++;
	$per = round((($count1 / $unitcount) * 100),4);
	echo $count1 . "   ------   " . $per  . " %\r";
	flush();
}

//CLOSE .ASC FILE
fclose($med_qu_asc);

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "\n";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
