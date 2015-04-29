<?php
$firma = Configure::read('User.authorizes');
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['User']['id'] . "'>";
	echo "<cell>" . $result[$i]['User']['id'] . "</cell>";
        echo "<cell>" . $result[$i]['User']['username'] . "</cell>";
        echo "<cell>" . '' . "</cell>";
        echo "<cell>" . $result[$i]['User']['rut'] . "</cell>";
	echo "<cell>" . $result[$i]['User']['names'] . "</cell>";
	echo "<cell>" . $result[$i]['User']['primary_last_name'] . "</cell>";
        echo "<cell>" . $result[$i]['User']['second_last_name'] . "</cell>";
        echo "<cell>" . $result[$i]['Section']['name'] . "</cell>";
        echo "<cell>" . $result[$i]['Department']['name'] . "</cell>";
        echo "<cell>" . $result[$i]['Unit']['name'] . "</cell>";
	echo "<cell>" . $result[$i]['Boss']['name'] . "</cell>";
        echo "<cell>" . $result[$i]['User']['email'] . "</cell>";
	echo "<cell>" . Configure::read('User.roles.' . $result[$i]['User']['role']) . "</cell>";
	echo "<cell>" . Configure::read('is_active.' . $result[$i]['User']['is_active']) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['User']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['User']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>