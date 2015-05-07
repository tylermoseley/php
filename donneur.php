<?php
//Timestamp for script run time
date_default_timezone_set('America/New_York');
echo "Began at: ". date('m/d/Y h:i:sa') ."\n";
$starttime = microtime(true);

//DB Connection variables
$db_host = 'localhost';
$db_uname = 'root';
$db_passwd = 'plaut0mati0n';
$db = 'allpds3data';
$link = mysqli_connect($db_host, $db_uname, $db_passwd, $db)
    or die ("Could not connect to database, error: " . mysqli_error($link));
    
 
$ccodes = "('AB','AU','AY','BG','BY','CD','CS','DF','DU','FD','FS','GA','GL','GN','HS','JB','JK','JV','LB','LC','LR','LU','NG','OL','PH','PL','SA','SS','ST','TA','TT','WF','WN')";    


$ccode_nddr = array(
	'AU' => '0885',
	'NG' => '0824',
	'AB' => '0454',
	'BG' => '0469',
	'BY' => '0904',
	'CD' => '0644',
	'DU' => '0455',
	'DF' => '0447',
	'FD' => '0646',
	'FS' => '0730',
	'GN' => '0775',
	'GA' => '0453',
	'GL' => '0837',
	'HS' => '0759',
	'JK' => '0777',
	'JV' => '0207',
	'JB' => '0835',
	'LC' => '0459',
	'PL' => '0449',
	'LR' => '0761',
	'LU' => '0739',
	'LB' => '0948',	
	'OL' => '0815',
	'PH' => '0108',	
	'SA' => '0982',
	'SS' => '0876',	
	'ST' => '0458',
	'TT' => '0635',
	'TA' => '0446',
	'CS' => '0448',
	'WF' => '0810',
	'WN' => '0639',
	'AY' => '0456',
);
    
$file = fopen("ePro/donneur.asc", 'w');    
//$headers = "NO_DONN|NOM_DONN|SDX_NOM_DONN|SFX_NOM_DONN|PRE_DONN|SEX_DONN|DTN_DONN|CPN_DONN|DPT_NAI|NOM_JF|SDX_NOM_JF|MIDDLE_NAME|NICKNAME|MOTHER_NAME|CORR_NAME|NO_SS|CAN_ID_NO|NO_CI|ADR_DONN1|SAD_DONN1|CP_DONN1|VILL_DONN1|SSA_DONN1|TEL_DONN1|COD_COUNTY1|PER_VAL1|ADR_DONN2|SAD_DONN2|CP_DONN2|VILL_DONN2|SSA_DONN2|TEL_DONN2|COD_COUNTY2|PER_VAL2|ENT_DONN|NB_DON_ANT|DATE_FIRST|LIEU_COL1|LIEU_COL2|LIEU_COL3|LIEU_COL4|COD_ASSO|TYP_PREL|CONVO|TPP|POIDS|TAILLE|COD_DIPL|TITRE_DONN|TEL_COMP|TEL_DIRECT|OCCUPATION|MBS|COD_ARCON|LANGUE_DONN|ETHNIE|TYP_POCH_USUEL|COD_CONVO1|COD_CONVO2|COD_CONVO3|FREQ_DES|COD_MED|TYP_DONN|PTP|PRE_DON|EDI_CARTE|JRS_NON|PER_NON1|PER_NON2|PER_NON3|TEL_MOBILE|EMAIL|DT_ENTRY|DEBIT_CARD_PACKAGE_ID|REC_ID\n";
//echo fputs($file,$headers);
$sql = "
    	SELECT C_CODE,
	DONOR_NO,
	LAST,
	FIRST,
	SEX,
	BIRTH_DATE,
	MIDDLE,
	SSNO,
	STREET1,
	STREET2,
	ZIP1,
	CITY,
	PHONE,
	STATE,
	EMAIL_ADDR,
	ENTRY_DATE
    	FROM allpds3data.`REGDONR`
    	WHERE 1
";    

$result = mysqli_query($link,$sql)
    or die ("Error in sql");

$tot_count = mysqli_num_rows($result);
$count = 0;
    
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$count++;
	$NO_DONN = $ccode_nddr[$row["C_CODE"]] . $row["DONOR_NO"];
	$SDX_NOM_DONN = soundex($row["LAST"]);
	if ($row["BIRTH_DATE"] == "0000-00-00") {
		$DTN_DONN = "";
} else {
		$bdate = date_create($row["BIRTH_DATE"]);
		$DTN_DONN = date_format($bdate, 'dmY');
	}
	if ($row["ENTRY_DATE"] == "0000-00-00") {
		$DT_ENTRY = "";
	} else {
		$edate = date_create($row["ENTRY_DATE"]);
		$DT_ENTRY = date_format($edate, 'dmY');
    	}
	
	$string = "$NO_DONN|{$row["LAST"]}|$SDX_NOM_DONN||{$row["FIRST"]}|{$row["SEX"]}|$DTN_DONN|||||{$row["MIDDLE"]}||||{$row["SSNO"]}|||{$row["STREET1"]}|{$row["STREET2"]}|{$row["ZIP1"]}|{$row["CITY"]}||{$row["PHONE"]}|{$row["STATE"]}|||||||||||||||||||||||||||||||||||||||||||||||{$row["EMAIL_ADDR"]}|$DT_ENTRY|||\n";
    	
	fputs($file,$string);
    	
	echo round($count / $tot_count * 100) . "%\r";
}

fclose($file);
    
//timestamp for end of script and elapsed execution time calculation.
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "\n";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
