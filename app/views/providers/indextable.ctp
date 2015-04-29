<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['Provider']['id'] . "'>";
	echo "<cell>" . $result[$i]['Provider']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['Provider']['rut'] . "</cell>";
	echo "<cell>" . $general->xmlEscape($result[$i]['Provider']['socialreason']) . "</cell>";
	echo "<cell>" . $general->xmlEscape($result[$i]['Provider']['fantasyname']) . "</cell>";
	echo "<cell>" . $general->xmlEscape($result[$i]['Provider']['address']) . "</cell>";
	echo "<cell>" . $result[$i]['Provider']['contact_name'] . "</cell>";
	echo "<cell>" . $result[$i]['Provider']['contact_phone'] . "</cell>";
	echo "<cell>" . $result[$i]['Provider']['contact_email'] . "</cell>";
        echo "<cell>" . $result[$i]['Provider']['observation'] . "</cell>";
        echo "<cell>" . Configure::read('is_active.'.$result[$i]['Provider']['is_active']) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Provider']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Provider']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>