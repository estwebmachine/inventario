<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['Floor']['id'] . "'>";
	echo "<cell>" . $result[$i]['Floor']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['Floor']['number'] . "</cell>";
        echo "<cell>" . $result[$i]['Address']['name'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Floor']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Floor']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>