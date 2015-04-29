<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
	echo "<row id='" . $result[$i]['TransactionDetail']['id'] . "'>";
	echo "<cell>" . $result[$i]['TransactionDetail']['transaction_id'] . "</cell>";
	echo "<cell>" . $result[$i]['TransactionDetail']['id'] . "</cell>";
	echo "<cell>" . $result[$i]['Asset']['name'] . "</cell>";
	if(!empty($result[$i]['PurchaseOrderDetail']['asset_id']))
            echo "<cell>" . $general->xmlEscape($result[$i]['Asset']['name']) . "</cell>";
        else
            echo "<cell>" . $general->xmlEscape($result[$i]['PurchaseOrderDetail']['description']) . "</cell>";
	echo "<cell>" . $result[$i]['User']['names'] . "</cell>";
	echo "<cell>" . $result[$i]['TransactionDetail']['amount'] . "</cell>";
	echo "<cell>" . $result[$i]['TransactionDetail']['amount_trans'] . "</cell>";
	echo "<cell>" . $result[$i]['PurchaseOrderDetail']['amount'] . "</cell>";
	echo "<cell>" . $result[$i]['TransactionDetail']['price'] . "</cell>";
	echo "<cell>" . $result[$i]['TransactionDetail']['value'] . "</cell>";
	echo "<cell>" . $result[$i]['Transaction']['created'] . "</cell>";
	echo "<cell>" . $result[$i]['Transaction']['modified'] . "</cell>";
        if(file_exists(WWW_ROOT . 'csv'. DS .'CSV_' . $result[$i]['TransactionDetail']['id'] . '.csv'))
        echo "<cell>" .'CSV_' . $result[$i]['TransactionDetail']['id'] . '.csv' . "</cell>";
	else 
        echo "<cell></cell>";
	echo "</row>";
}
echo "</rows>";
?>