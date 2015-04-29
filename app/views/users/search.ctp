<?php //echo $result; ?>
<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['users']['id'] . "'>";
	echo "<cell>" . $result[$i]['users']['id'] . "</cell>";
        echo "<cell>" . $result[$i]['users']['rut'] . "</cell>";
        echo "<cell>" . $result[$i]['users']['names'] . "</cell>";
        echo "<cell>" . $result[$i]['users']['primary_last_name'] . "</cell>";
        echo "<cell>" . $result[$i]['users']['second_last_name'] . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>