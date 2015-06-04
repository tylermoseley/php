<?php
$input = 'ABJ'; 

$dy = ord(substr($input,1,1));
if (($dy >= 65) AND ($dy <= 90)) {
	$day = sprintf('%02d', ($dy - 64));
} elseif (($dy >= 48) AND ($dy <= 52)) {
	$day = sprintf('%02d', ($dy - 21));
}
echo $day . "-";

$mon = sprintf('%02d', (ord(substr($input,0,1))) - 64);
echo $mon . "-";

$yr = ord(substr($input,2,1));
if (($yr >= 65) AND ($yr <= 90)) {
	$year = $yr + 1941;
} elseif  (($yr >= 48) AND ($yr <= 58)) {
	$year = $yr + 1984;
}
echo $year;




echo "\n";
?>
