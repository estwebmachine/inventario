<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['Ipc']['id'] . "'>";
	echo "<cell>" . $result[$i]['Ipc']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['Ipc']['value'] . "</cell>";
        echo "<cell>" . $result[$i]['Ipc']['date'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Ipc']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Ipc']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>