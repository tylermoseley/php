<?php

$resource = fopen("test.asc", "r");
$i = -2;
while($char != "\n") {
	fseek($resource,$i,SEEK_END);
	$char = fgetc($resource);
	$i--;
}
$line = fgets($resource,10);

?>
