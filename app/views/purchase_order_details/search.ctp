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
	echo "<row id='" . $result[$i]['PurchaseOrderDetail']['id'] . "'>";
	echo "<cell>" . $result[$i]['PurchaseOrderDetail']['id'] . "</cell>";
	if(!empty($result[$i]['Asset']['name']))
            echo "<cell>" . $general->xmlEscape($result[$i]['Asset']['name']) . "</cell>";
        else
            echo "<cell>" . $general->xmlEscape($result[$i]['PurchaseOrderDetail']['description']) . "</cell>";
	echo "<cell>" . $result[$i]['PurchaseOrderDetail']['amount_trans'] . "</cell>";
	echo "<cell>" . $result[$i]['PurchaseOrderDetail']['amount'] . "</cell>";
	echo "</row>";
}
echo "</rows>";
?>