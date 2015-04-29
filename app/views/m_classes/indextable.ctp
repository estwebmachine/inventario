<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['MClass']['id'] . "'>";
	echo "<cell>" . $result[$i]['MClass']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['MClass']['name'] . "</cell>";
        echo "<cell>" . Configure::read('is_active.'.$result[$i]['MClass']['is_active']) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['MClass']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['MClass']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>