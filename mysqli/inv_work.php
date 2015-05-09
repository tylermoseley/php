<?php
$unitno = $_POST['unitno'];
$fp = fopen("test.csv", 'w');
echo $unitno;
echo fputs($fp, $unitno);
fclose($fp);
//GOTO(inv_reconcile.html);
?>