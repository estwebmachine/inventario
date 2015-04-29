<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['Transaction']['id'] . "'>";
	echo "<cell>" . $result[$i]['Transaction']['id'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Transaction']['date']) . "</cell>";
	echo "<cell>" . $result[$i]['PurchaseOrder']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['PurchaseOrder']['order_number'] . "</cell>";
//        echo "<cell>" . $result[$i]['PurchaseOrder']['Provider']['rut'] . "</cell>";
	echo "<cell>" . $result[$i]['User']['names'] . ' ' . $result[$i]['User']['primary_last_name'] . ' ' . $result[$i]['User']['second_last_name'] ."</cell>";
        echo "<cell>" . $result[$i]['Transaction']['subtitles'] . "</cell>";
        echo "<cell>" . Configure::read('Transaction.type.'.$result[$i]['Transaction']['type']) . "</cell>";
	echo "<cell>" . Configure::read('PurchaseOrder.document_types.' . $result[$i]['Transaction']['document_type']) . "</cell>";
	echo "<cell>" . $result[$i]['Transaction']['document_number'] . "</cell>";
        echo "<cell>" . $result[$i]['Transaction']['observation'] . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Transaction']['document_date']) . "</cell>";
	echo "<cell>" . Configure::read('Transaction.status.' . $result[$i]['Transaction']['status']) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Transaction']['created'], true) . "</cell>";
	echo "<cell>" . $general->sqlToDate($result[$i]['Transaction']['modified'], true) . "</cell>";
        if(file_exists(WWW_ROOT . 'pdf'. DS .'DP_' . $result[$i]['Transaction']['id'] . '.pdf'))
        echo "<cell>" .'DP_' . $result[$i]['Transaction']['id'] . '.pdf' . "</cell>";
	else 
        echo "<cell></cell>";
        echo "</row>";
}
echo "</rows>";
?>