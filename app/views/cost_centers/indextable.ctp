<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['CostCenter']['id'] . "'>";
	echo "<cell>" . $result[$i]['CostCenter']['id'] . "</cell>";
        echo "<cell>" . $result[$i]['CostCenter']['code'] . "</cell>";
	echo "<cell>" . $result[$i]['CostCenter']['name'] . "</cell>";
	echo "<cell>" . Configure::read('level.'.$result[$i]['CostCenter']['level']) . "</cell>";
        echo "<cell>" . $result[$i]['Parent']['name'] . "</cell>";
        echo "<cell>" . Configure::read('is_active.'.$result[$i]['CostCenter']['is_active']) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['CostCenter']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['CostCenter']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>