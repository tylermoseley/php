<?php
$ip = getHostByName(getHostName());
if ($ip == '10.1.6.233') {
    echo "true";
} else {
    echo "false";
}
?>