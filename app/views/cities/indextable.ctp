<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['City']['id'] . "'>";
	echo "<cell>" . $result[$i]['City']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['City']['name'] . "</cell>";
        echo "<cell>" . $result[$i]['Region']['name'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['City']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['City']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>