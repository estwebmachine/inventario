<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
    $responsible = '-';
    $city = '-';
    $address = '-';
    $floor = '-';
    $office = '-';
    $region = '-';
    $program = '-';
    $created = $general->sqlToDate($result[$i]['InventoryAsset']['created'], true);
    $modified = $general->sqlToDate($result[$i]['InventoryAsset']['modified'], true);
//    $created = '-';
//    $modified = '-';
    if( !empty($result[$i]['InventoryAssetAllocation']) ) {
		$current_alloc = $result[$i]['InventoryAssetAllocation'][0];
		if( !empty($current_alloc['User']) ) $responsible = $current_alloc['User'];
		if( !empty($current_alloc['City'])) $city = $current_alloc['City']['name'];
                if( !empty($current_alloc['Address'])) $address = $current_alloc['Address']['name'];
                if( !empty($current_alloc['Floor']) ) $floor = $current_alloc['Floor']['number'];
		if( !empty($current_alloc['Office']) ) $office = $current_alloc['Office']['number'];
                $program = $current_alloc['program_id'];
		if( !empty($current_alloc['Region']) ) $region = $current_alloc['Region']['name'];
//                if( !empty($current_alloc['created']) ) $created = $general->sqlToDate($current_alloc['created'],true);
//		if( !empty($current_alloc['modified']) ) $modified = $general->sqlToDate($current_alloc['modified'],true);
    }
    echo "<row id='" . $result[$i]['InventoryAsset']['id'] . "'>";
    echo "<cell>" . $result[$i]['InventoryAsset']['id'] . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['code'] . "</cell>";
    echo "<cell>" . $result[$i]['Asset']['name'] . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['detail'] . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['serial'] . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['original_price'] . "</cell>";
    echo "<cell>" . $general->assetPrice($result[$i]['InventoryAsset']['original_price'],$result[$i]['InventoryAsset']['created'],$result[$i]['InventoryAsset']['life'],$result[$i]['InventoryAsset']['residual_value'],$result[$i]['InventoryAsset']['is_depreciate']) . "</cell>";
    echo "<cell>" . Configure::read('InventoryAsset.status.' . $result[$i]['InventoryAsset']['status']) . "</cell>";
    echo "<cell>" . Configure::read('InventoryAsset.is_depreciate.'.$result[$i]['InventoryAsset']['is_depreciate']) . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['situation'] . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['life'] . "</cell>";
    echo "<cell>" . $result[$i]['InventoryAsset']['residual_value'] . "</cell>";
    echo "<cell>" . $responsible['names'] . "</cell>";
    echo "<cell>" . $responsible['primary_last_name'] . "</cell>";
    echo "<cell>" . $responsible['second_last_name'] . "</cell>";
    echo "<cell>" . Configure::read('Programas.'.$program) . "</cell>";
    echo "<cell>" . $office . "</cell>";
    echo "<cell>" . $floor . "</cell>";
    echo "<cell>" . $address . "</cell>";
    echo "<cell>" . $city . "</cell>";
    echo "<cell>" . $region . "</cell>";
    echo "<cell>" . $created . "</cell>";
    echo "<cell>" . $modified . "</cell>";
    if(file_exists(WWW_ROOT . 'pdf'. DS .'AD_' . $result[$i]['InventoryAsset']['id'] . '.pdf'))
        echo "<cell>" .'AD_' . $result[$i]['InventoryAsset']['id'] . '.pdf' . "</cell>";
	else 
        echo "<cell></cell>";
    echo "</row>";
}
echo "</rows>";
?>