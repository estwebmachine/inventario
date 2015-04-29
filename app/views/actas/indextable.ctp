<?php
header("Content-type: text/xml;charset=utf-8");
echo $xml->header();
echo "<rows>";
echo "<page>" . $page . "</page>";
echo "<total>" . $total_pages . "</total>";
echo "<records>" . $count . "</records>";

// be sure to put text data in CDATA

for($i = 0; $i < sizeof($result); $i++){
    echo "<row id='" . $result[$i]['Acta']['id'] . "'>";
    echo "<cell>" . $result[$i]['Acta']['id'] . "</cell>";
    $folio = $result[$i]['Acta']['is_ses'] == 1?$result[$i]['Acta']['folio_ses']:$result[$i]['Acta']['folio_sss'];
    echo "<cell>" . $result[$i]['Acta']['number'] . "</cell>";
    echo "<cell>" . Configure::read('Acta.type.' . $result[$i]['Acta']['type']) . "</cell>";
    echo "<cell>" . Configure::read('Acta.status.' . $result[$i]['Acta']['status']) . "</cell>";
    echo "<cell>" . $result[$i]['Assigned']['names'] . ' ' . $result[$i]['Assigned']['primary_last_name'] . ' ' . $result[$i]['Assigned']['second_last_name'] ."</cell>";
    echo "<cell>" . $result[$i]['Receive']['names'] . "</cell>";
    echo "<cell>" . $result[$i]['Receive']['primary_last_name'] . "</cell>";
    echo "<cell>" . $result[$i]['Receive']['second_last_name'] . "</cell>";
    echo "<cell>" . $general->sqlToDate($result[$i]['Acta']['created'], true) . "</cell>";
    echo "</row>";
}
echo "</rows>";
?>