<?php //print_r($result); ?>
<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['PurchaseOrder']['id'] . "'>";
	echo "<cell>" . $result[$i]['PurchaseOrder']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['PurchaseOrder']['order_number'] . "</cell>";
	echo "<cell>" . $result[$i]['Warehouse']['name'] . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>