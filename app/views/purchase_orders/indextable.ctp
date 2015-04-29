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
	echo "<cell>" . $result[$i]['PurchaseOrder']['name'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['PurchaseOrder']['date']) . "</cell>";
        echo "<cell>" . $general->xmlEscape($result[$i]['PurchaseOrder']['description']) . "</cell>";
         echo "<cell>" . $general->xmlEscape($result[$i]['PurchaseOrder']['comment']) . "</cell>";
        echo "<cell>" . Configure::read('PurchaseOrder.status.' . $result[$i]['PurchaseOrder']['status']) . "</cell>";
	echo "<cell>" . $general->xmlEscape($result[$i]['Provider']['fantasyname']) . "</cell>";
        echo "<cell>" . $result[$i]['Provider']['rut'] . "</cell>";
        echo "<cell>" . $result[$i]['User']['names'] . ' ' . $result[$i]['User']['primary_last_name'] . ' ' . $result[$i]['User']['second_last_name'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['PurchaseOrder']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['PurchaseOrder']['modified'], true) . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>