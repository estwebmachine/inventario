<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['SubClass']['id'] . "'>";
	echo "<cell>" . $result[$i]['SubClass']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['SubClass']['name'] . "</cell>";
	echo "<cell>" . $result[$i]['MClass']['name'] . "</cell>";
        echo "<cell>" . $result[$i]['SubClass']['life'] . "</cell>";
        echo "<cell>" . $result[$i]['SubClass']['residual_value'] . "</cell>";
        echo "<cell>" . Configure::read('is_active.'.$result[$i]['SubClass']['is_active']) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['SubClass']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['SubClass']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>