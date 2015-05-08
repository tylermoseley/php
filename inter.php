<?php
//START TIME
date_default_timezone_set('America/New_York');
echo "Began at: ". date('m/d/Y h:i:sa') ."<\n>";
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

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "<\n>";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>