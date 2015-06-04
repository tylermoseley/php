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
$test_codes_sql = '
SELECT P_LOC, P_TEST, E_TEST_COD
FROM COD_ANA
WHERE 1
';
//EXECUTE STATEMENT
$test_codes_query = mysqli_query($link,$test_codes_sql)
        or die ("Error in test_codes_sql");

//POPULATE ARRAY OF TEST CODES FOR FASTER CONVERSION
$T_CODE = array();
while($test_code = mysqli_fetch_array($test_codes_query, MYSQLI_ASSOC)) {
	$T_CODE[$test_code["P_TEST"]] = $test_code["E_TEST_COD"];
}
unset($test_codes);
echo "Codes Complete\n";

for ($i = 1; $i <= 2; $i++) {
	if ($i == 1) {
		$limit = 'LIMIT 1';
	} else {
		$limit = '';
	}

	//SELECT STATEMENT
	$units_sql = '
	SELECT
	IF(DATE_FORMAT(UNIT.DONOR_DATE,"%d%m%Y")="00000000","",DATE_FORMAT(UNIT.DONOR_DATE,"%d%m%Y")) AS DONOR_DATE,
	CONCAT(CCODNDDR.NDDR_CODE,RESULTS.UNIT_NO) AS UNIT_NO,
	CCODNDDR.NDDR_CODE,
        UNIT.DONOR_WT,
	UNIT.HEMATOCRIT,
	UNIT.BPSYS,
	UNIT.BPDIA,
	UNIT.PULSE,
	UNIT.TEMPER,
	UNIT.TL_PROTEIN,
	RESULTS.HBSAG,
	RESULTS.HIV12,
	RESULTS.HCV,
	RESULTS.NATHIV,
	RESULTS.NATHCV,
	RESULTS.NATHBV,
	RESULTS.NATHAV,
	RESULTS.PARVO,
	RESULTS.NATMPX
	FROM allpds3data.RESULTS
	JOIN allpds3data.CCODNDDR
	ON RESULTS.C_CODE=CCODNDDR.C_CODE
	RIGHT JOIN allpds3data.UNIT
	ON RESULTS.C_CODE=UNIT.C_CODE AND RESULTS.UNIT_NO=UNIT.UNIT_NO
	WHERE 1 
	' . $limit;

	//EXECUTE STATEMENT
	$units_query = mysqli_query($link,$units_sql)
		or die ("Error in units_sql");
	
	//FETCH ROWS TO LOAD INNODB BUFFER FOR TUNING
	while (($i == 1) AND $warm_up = mysqli_fetch_array($units_query, MYSQLI_ASSOC)) {
	//DO NOTHING
	}
}

//END TIME
date_default_timezone_set('America/New_York');
$endtime1 = microtime(true);
$elapsedtime1 = $endtime1 - $starttime;
echo "Unit SQL Time: " . gmdate("H:i:s", $elapsedtime1) . "\n";

//BUILD ARRAY OF TEST TYPES
$RESULS = array('DONOR_WT', 'HEMATOCRIT', 'BPSYS', 'BPDIA', 'PULSE', 'TEMPER', 'TL_PROTEIN', 'HBSAG', 'HIV12', 'HCV', 'NATHIV', 'NATHCV', 'NATHBV', 'NATHAV', 'PARVO', 'NATMPX');

//OPEN ASCII FILE IF NOT EXISTS, OVERRIDE IF EXISTS  
$resul_asc = fopen('ePro/resul.asc','w');

//DO WORK
$DATE = array();
$count1 = 0;
$unitcount = mysqli_num_rows($units_query);
while ($unit = mysqli_fetch_array($units_query, MYSQLI_ASSOC)) {
	foreach ($RESULS as $RESUL) {
		if ($unit[$RESUL] == "" || $unit[$RESUL] == 0) {
		//NO ENTRY
		} else {
			$line_string = 
			$unit["DONOR_DATE"] . "|" . 
			$unit["UNIT_NO"] . "|" . 
			$unit["NDDR_CODE"] . "|" . 
			$T_CODE[$RESUL] . "|" . 
			$unit[$RESUL] . "|BADGE|" .
			$unit["DONOR_DATE"] . "||" //MODIFY FOR RESULTS ENTRY ONCE RECIEVED DATE IS FOUND
			;
			$line_string .= "\n";
			fputs ($resul_asc,$line_string);	 
		}
	}
	$DATE[$unit["UNIT_NO"]] = $unit["DONOR_DATE"];
	$count1++;
	$per = round((($count1 / $unitcount) * 100),4);
	echo $per  . " %\r";
}
unset($units);
echo "\nUnits/Results Complete\n";

for ($i = 1; $i <= 2; $i++) {
	if ($i == 1) {
		$limit = 'LIMIT 1';
	} else {
		$limit = '';
	}
	
	//SELECT STATEMENT
	$hots_sql = "
	SELECT 
	IF(DATE_FORMAT(HOT.DONOR_DATE,'%d%m%Y')='00000000','',DATE_FORMAT(HOT.DONOR_DATE,'%d%m%Y')) AS DONOR_DATE,
	CONCAT(CCODNDDR.NDDR_CODE,HOT.UNIT_NO) AS UNIT_NO,
	CCODNDDR.NDDR_CODE,
	HOT1,
	CONF1RESLT,
	CONF1DATE,
	HOT2,
	CONF2RESLT,
	CONF2DATE,
	HOT3,
	CONF3RESLT,
	CONF3DATE,
	HOT4, 
	CONF4RESLT,
	CONF4DATE,
	HOT5,
	CONF5RESLT,
	CONF5DATE
	FROM allpds3data.HOT
	JOIN allpds3data.CCODNDDR
	ON HOT.C_CODE=CCODNDDR.C_CODE
	WHERE (HOT1 IN ('A','B','C','D') AND CONF1RESLT <> '') OR 
		(HOT2 IN ('A','B','C','D') AND CONF2RESLT <> '') OR
	    (HOT3 IN ('A','B','C','D') AND CONF3RESLT <> '') OR 
	    (HOT4 IN ('A','B','C','D') AND CONF4RESLT <> '') OR 
	    (HOT5 IN ('A','B','C','D') AND CONF5RESLT <> '')
	" . $limit;

	//EXECUTE STATEMENT
	$hots_query = mysqli_query($link,$hots_sql)
		or die ("Error in hots_sql");

	//FETCH ROWS TO LOAD INNODB BUFFER FOR TUNING
	while (($i == 1) AND $warm_up = mysqli_fetch_array($hots_query, MYSQLI_ASSOC)) {
	//DO NOTHING
	}
}
//DO WORK
$RESULTS = array('A', 'B', 'C', 'D');
$HOTS = range(1,5);
while ($hot = mysqli_fetch_array($hots_query, MYSQLI_ASSOC)) {
	foreach ($HOTS as $HOT) {
		$reslt = 'CONF' . $HOT . 'RESLT';
		if ($hot[$reslt] != "") {
			$ht = "HOT" . $HOT;
			$line_string = 
			$hot["DONOR_DATE"] . "|" . 
			$hot["UNIT_NO"] . "|" . 
			$hot["NDDR_CODE"] . "|" . 
			$T_CODE[$hot[$ht]] . "|" . //ADD CONVERSION TO EPRO RESULTS 
			$hot["$reslt"] . "|BADGE|" .
			$hot["DONOR_DATE"]
			;
			$line_string .= "\n";
			fputs ($resul_asc,$line_string); 
			 
		}
	}
}

for ($i = 1; $i <= 2; $i++) {
	if ($i == 1) {
		$limit = 'LIMIT 1';
	} else {
		$limit = '';
	}
	
	//SELECT STATEMENT
	$resloth_sql = "
	SELECT
	CONCAT(CCODNDDR.NDDR_CODE,RESLOTH.SAMP_NO) AS SAMP_NO,
	CCODNDDR.NDDR_CODE,
	RESLOTH.ATYP_RESL,
	RESLOTH.RPR_RESL,
	RESLOTH.TL_PROTEIN
	FROM allpds3data.RESLOTH
	JOIN allpds3data.CCODNDDR
	ON RESLOTH.C_CODE=CCODNDDR.C_CODE
	WHERE 1
	" . $limit;

	//EXECUTE STATEMENT
	$resloth_query = mysqli_query($link,$resloth_sql)
		or die ("Error in resloth_sql");

	//FETCH ROWS TO LOAD INNODB BUFFER FOR TUNING
	while (($i == 1) AND $warm_up = mysqli_fetch_array($resloth_query, MYSQLI_ASSOC)) {
	//DO NOTHING
	}
}

//BUILD ARRAY OF TEST TYPES
$RESULS = array('ATYP_RESL','RPR_RESL','TL_PROTEIN');

//DO WORK
$DATE = array();
$count = 0;
$unitcount = mysqli_num_rows($resloth_query);
while ($resloth = mysqli_fetch_array($resloth_query, MYSQLI_ASSOC)) {
	foreach ($RESULS as $RESUL) {
		if ($resloth[$RESUL] == "" || $resloth[$RESUL] == 0) {
		//NO ENTRY
		} else {
			//CALCULATE DATE FROM CODE
			$dy = ord(substr($resloth["SAMP_NO"],6,1));
			if (($dy >= 65) AND ($dy <= 90)) {
				$day = sprintf('%02d', ($dy - 64));
			} elseif (($dy >= 48) AND ($dy <= 52)) {
				$day = sprintf('%02d', ($dy - 21));
			}
			$mon = sprintf('%02d', (ord(substr($resloth["SAMP_NO"],5,1))) - 64);
			$yr = ord(substr($resloth["SAMP_NO"],7,1));
			if (($yr >= 65) AND ($yr <= 90)) {
				$year = $yr + 1941;
			} elseif  (($yr >= 48) AND ($yr <= 58)) {
				$year = $yr + 1984;
			}
			$resloth_date = $day . $mon . $year;
 			//
			$line_string = 
			$resloth_date . "|" . 
			$resloth["SAMP_NO"] . "|" . 
			$resloth["NDDR_CODE"] . "|" . 
			$T_CODE[$RESUL] . "|" . 
			$resloth[$RESUL] . "|BADGE|" .
			$resloth_date . "||" //MODIFY FOR RESULTS ENTRY ONCE RECIEVED DATE IS FOUND
			;
			$line_string .= "\n";
			fputs ($resul_asc,$line_string);	 
		}
	}
	$count++;
	$per = round((($count1 / $unitcount) * 100),4);
	echo $per  . " %\r";
}
for ($i = 1; $i <= 2; $i++) {
	if ($i == 1) {
		$limit = 'LIMIT 1';
	} else {
		$limit = '';
	}


	//SELECT STATEMENT
	$sample_sql = "
	SELECT
	IF(DATE_FORMAT(SAMPLE.SAMP_DATE,'%d%m%Y')='00000000','',DATE_FORMAT(SAMPLE.SAMP_DATE,'%d%m%Y')) AS DONOR_DATE,
	CONCAT(CCODNDDR.NDDR_CODE,SAMPLE.SAMP_NO) AS SAMP_NO,
	CCODNDDR.NDDR_CODE,
	SAMPLE.SPE_RESLT,
	SAMPLE.RPR_RESLT
	FROM allpds3data.SAMPLE
	JOIN allpds3data.CCODNDDR
	ON SAMPLE.C_CODE=CCODNDDR.C_CODE
	WHERE 1
	";

	//EXECUTE STATEMENT
	$sample_query = mysqli_query($link,$sample_sql)
		or die ("Error in sample_sql");

	//FETCH ROWS TO LOAD INNODB BUFFER FOR TUNING
	while (($i == 1) AND $warm_up = mysqli_fetch_array($sample_query, MYSQLI_ASSOC)) {
	//DO NOTHING
	}
}

//BUILD ARRAY OF TEST TYPES
$RESULS = array('SPE_RESLT','RPR_RESLT');

//DO WORK
$DATE = array();
$count = 0;
$unitcount = mysqli_num_rows($sample_query);
while ($sample = mysqli_fetch_array($sample_query, MYSQLI_ASSOC)) {
	foreach ($RESULS as $RESUL) {
		if ($sample[$RESUL] == "" || $sample[$RESUL] == 0) {
		//NO ENTRY
		} else {
			//CALCULATE DATE FROM CODE
		//	$dy = ord(substr($resloth["SAMP_NO"],6,1));
		//	if (($dy >= 65) AND ($dy <= 90)) {
		//		$day = sprintf('%02d', ($dy - 64));
		//	} elseif (($dy >= 48) AND ($dy <= 52)) {
		//		$day = sprintf('%02d', ($dy - 21));
		//	}
		//	$mon = sprintf('%02d', (ord(substr($resloth["SAMP_NO"],5,1))) - 64);
		//	$yr = ord(substr($resloth["SAMP_NO"],7,1));
		//	if (($yr >= 65) AND ($yr <= 90)) {
		//		$year = $yr + 1941;
		//	} elseif  (($yr >= 48) AND ($yr <= 58)) {
		//		$year = $yr + 1984;
		//	}
		//	$resloth_date = $day . $mon . $year;
 			//
			$line_string = 
			$sample["DONOR_DATE"] . "|" . 
			$sample["SAMP_NO"] . "|" . 
			$sample["NDDR_CODE"] . "|" . 
			$T_CODE[$RESUL] . "|" . 
			$sample[$RESUL] . "|BADGE|" .
			$sample["DONOR_DATE"] . "||" //MODIFY FOR RESULTS ENTRY ONCE RECIEVED DATE IS FOUND
			;
			$line_string .= "\n";
			fputs ($resul_asc,$line_string);	 
		}
	}
	$count++;
	$per = round((($count1 / $unitcount) * 100),4);
	echo $per  . " %\r";
}

//CLOSE .ASC FILE
fclose($resul_asc);

//END TIME
date_default_timezone_set('America/New_York');
$endtime = microtime(true);
$elapsedtime = $endtime - $starttime;
echo "Completed at: " . date('m/d/Y h:i:sa') . "\n";
echo "Elapsed time: " . gmdate("H:i:s", $elapsedtime) . "\n";
?>
