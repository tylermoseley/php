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

$tables = array ('CCODNDDR' => array('C_CODE', 'NDDR_CODE'),
		'UNIT' => array('C_CODE', 'UNIT_NO', 'WT_BOTT', 'C_CODE`, `UNIT_NO'),
		'REGDONR' => array('C_CODE', 'DONOR_NO'),
		'TETANUS' => array('C_CODE', 'UNIT_NO', 'DONOR_NO'),
		'REJDONR' => array('C_CODE', 'REJ_REASON', 'REJ_PERIOD'),
		'COD_INTER' => array('P_DEF_TEXT', 'P_DEF_PER'),
		'RESULTS' => array('C_CODE', 'UNIT_NO', 'C_CODE`, `UNIT_NO'),
		'GLOBAL_GMML' => array('GMS'),
		'HOT' => array('C_CODE`, `UNIT_NO'),
		'SAMPLE' => array('C_CODE`, `SAMP_NO'),
		'RESLOTH' => array('C_CODE`, `SAMP_NO')
		);
$indexes = array();
while ($table = current($tables)) {
	$database->query('SHOW INDEX FROM ' . key($tables));

	$indexes = $database->resultset_assoc();

	$index = array("blank");
	if (empty($indexes)) { 
	} else {
		foreach ($indexes as $indexkey) {
			$index[] = $indexkey["Key_name"];
		}	
	}
	foreach ($table as $field) {
		if (in_array($field, $index)) {
			//DO NOTHING
		} else {
			$database->query('ALTER TABLE ' . key($tables) . ' ADD INDEX (`' . $field . '`)');
			$database->execute();
		}
	}
next($tables);
}

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "<\n>";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
