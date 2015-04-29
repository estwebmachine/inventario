<?php
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$filename.".csv");
header("Pragma: no-cache");
header("Expires: 0");
echo $data; 
?>