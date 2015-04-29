<?php
	ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '9999');
        
        if($type == 'actas'){
       
            $texto_folio = $tipo_acta == 1?'Entrega':'Devolución';
            $texto_firma1 = $tipo_acta == 1?'Entrega':'Recibe';
            $texto_firma2 = $tipo_acta == 1?'Recibe':'Entrega';
            $numero = $data['Acta']['number'];
            $folio = $data['Acta']['is_ses'] == 1?$data['Acta']['folio_ses']:$data['Acta']['folio_sss'];
            $res = '
                <p style="text-align:justify">
                En virtud de la normativa vigente, correspondiente a la Administración de Bienes del Estado, el Departamento de Adquisiciones, hace entrega de los bienes que se individualizan a continuación:
                </p>
                <p style="text-align:justify">
                Cabe señalar, que al momento de la firma de esta acta de entrega, el funcionario declara conocer la Ley Orgánica de la Contraloría General de la República en u Título IV, sobre la Responsabilidad de los Funcionarios, Artículo 61, "Los funcionarios que tengan a su cargo bienes serán responsables de su uso, abuso, o empleo ilegal y de toda pérdida o deterioro de los mismo bienes que se refiere el artículo 1º", será responsable de éstos, en conformidad con las disposiciones legales y reglamentarias.
                </p>
                <table border="1" width="33%">
                    <tr>
                        <td>Folio Acta '.$texto_folio.'</td>
                        <td align="center">'.$numero.'</td>
                    </tr>
                </table>
                <p></p>
                <table border="1">
                    <tr>
                        <td>Asignado a:</td>
                        <td>Unidad</td>
                        <td>Nº Inventario</td>
                        <td>Descripción</td>
                        <td>Estado</td>
                        <td>Total</td>
                    </tr>';
            $receive = $data['Receive']['names'].' '.$data['Receive']['primary_last_name'].' '.$data['Receive']['second_last_name'];
            $unit = isset($data['Receive']['Unit']['name'])?$data['Receive']['Unit']['name']:'';
            $dpto = isset($data['Receive']['Department']['name'])?$data['Receive']['Department']['name']:'';
            $sec = isset($data['Receive']['Section']['name'])?$data['Receive']['Section']['name']:'';
            foreach ($data['InventoryAssetAllocation'] as $key => $value) {
                $receive = $receive;
                $res .= '<tr nobr="true">
                            <td>'.$receive.'</td>
                            <td>'.$sec.'-'.$dpto.'-'.$unit.'</td>
                            <td>'.$value['InventoryAsset']['code'].'</td>
                            <td>'.$value['InventoryAsset']['Asset']['name'].'</td>
                            <td>'.$value['InventoryAsset']['situation'].'</td>
                            <td>1</td>
                         </tr>';
            }
            $res .= '</table>
            <p>
            
            </p>
            <p>
            
            </p>
            <table>
                <tr>
                    <td align="center">_______________________</td>
                    <td align="center">_______________________</td>
                    <td align="center">_______________________</td>
                </tr>
                <tr>
                    <td align="center">Registro<br/>
                    Encargado de Inventario'. $sub .'</td>
                    <td align="center">'.$texto_firma1.'<br/>
                    '.$entrega.'</td>
                    <td align="center">'.$texto_firma2.'<br/>
                    '; if($tipo_acta == 1){$res.= $recibecaja; }else{$res.= $receive;} 
                    $res.= '</td>
                </tr>
            </table>';
               
               
            
            App::import('Vendor','mypdf');
		// create new PDF document
            $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $titulo = $tipo_acta == 1?'ENTREGA':'DEVOLUCIÓN';
            $pdf->SetSubtitle('ACTA DE '.$titulo.' DE BIENES');

            //set zoom, layout
            $pdf->SetDisplayMode('real', 'OneColumn');

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Ministerio Desarrollo Social');
            $pdf->SetTitle('Sistema de Inventario');
            $pdf->SetSubject('Sistema de Inventario');
            $pdf->SetKeywords('PDF');

            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            //set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            //set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


            // ---------------------------------------------------------

            // set font
            $pdf->SetFont('times', 'BI', 10);

            // add a page
            $pdf->AddPage();
            $html = $res;

            //$html = 'ok';
            $pdf->writeHTML($html, true, false, false, false, '');

            // ---------------------------------------------------------

            //Close and output PDF document
            $pdf->Output($filename, 'I');

        }elseif($type == 'hoja_mural'){
            $res = '<p></p>
                <table border="1" width="50%">
                    <tr>
                        <td>Responsable</td>
                        <td>'.$data['responsable'].'</td>
                    </tr>
                    <tr>
                        <td>Nº Oficina</td>
                        <td>'.$data['office'].'</td>
                    </tr>
                </table><p></p>';
            
            $res .='<table border="1">
                        <tr>
                            <td>ASIGNADO A:</td>
                            <td>Nº INVENTARIO</td>
                            <td>DESCRIPCIÓN</td>
                            <td>ESTADO</td>
                        </tr>';
            
            foreach ($data['items'] as $item) {
                foreach ($item as $i => $value) {
                    $asignado = '';
                    if($i == 0){
                        $asignado = '<td rowspan="'.count($item).'">'.$value['User']['names'].' '.$value['User']['primary_last_name'].'</td>';
                    }
                    $res .= '
                        <tr nobr="true">
                            '.$asignado.'
                            <td>'.$value['InventoryAsset']['code'].'</td>
                            <td>'.$value['InventoryAsset']['Asset']['name'].'</td>
                            <td>'.$value['InventoryAsset']['situation'].'</td>
                        </tr>';
                }
            }
            
            $res .= '</table>
                <p>
            
            </p>
            <p>
            
            </p>
            <table>
                <tr>
                    <td align="center">_______________________</td>
                    <td align="center">_______________________</td>
                    <td align="center">_______________________</td>
                </tr>
                <tr>
                    <td align="center">
                    Encargado de Inventario'. $sub .'</td>
                    <td align="center">
                    '.$entrega.'</td>
                    <td align="center">Recibido Conforme</td>
                </tr>
            </table>';
            
            App::import('Vendor','mypdf');
		// create new PDF document
            $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetSubtitle('HOJA MURAL');

            //set zoom, layout
            $pdf->SetDisplayMode('real', 'OneColumn');

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Ministerio Desarrollo Social');
            $pdf->SetTitle('Sistema de Inventario');
            $pdf->SetSubject('Sistema de Inventario');
            $pdf->SetKeywords('PDF');

            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            //set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            //set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


            // ---------------------------------------------------------

            // set font
            $pdf->SetFont('times', 'BI', 10);

            // add a page
            $pdf->AddPage();
            $html = $res;

            //$html = 'ok';
            $pdf->writeHTML($html, true, false, false, false, '');

            // ---------------------------------------------------------

            //Close and output PDF document
            $pdf->Output($filename, 'I');
        } else if($type == 'alta'){
            $res = '
                <table><tr><td>Reporte de Altas</td><td>'.  date('d/m/Y').'</td></tr></table>
                    <p></p>
                <table border="1">
                    <tr>
                        <td>Fecha Alta</td>
                        <td>Tipo Alta</td>
                        <td>Nº OC</td>
                        <td>Nº Dcto</td>
                        <td>Tipo Dcto</td>
                        <td>Sub Clase</td>
                        <td>Decripción</td>
                        <td>Código</td>
                        <td>Precio Adquisición</td>
                        <td>Precio Actual</td>
                        <td>Estado</td>
                    </tr>
                    ';
            foreach ($data  as $value) {
                $oc = '-';
                $fa = '-';
                $fa_type = '-';
                $type = 'Ingreso masivo';
                if(!empty($value['TransactionDetail']['Transaction'])){
                    $oc = $value['TransactionDetail']['Transaction']['PurchaseOrder']['order_number'];
                    $fa = $value['TransactionDetail']['Transaction']['document_number'];
                    $fa_type = Configure::read('PurchaseOrder.document_types.'.$value['TransactionDetail']['Transaction']['document_type']);
                    $type = Configure::read('Transaction.type.'.$value['TransactionDetail']['Transaction']['type']);
                }
                $price = $general->assetPrice($value['InventoryAsset']['original_price'],$value['InventoryAsset']['created'],$value['InventoryAsset']['life'],$value['InventoryAsset']['residual_value'],$value['InventoryAsset']['is_depreciate']);
                if(empty($filter_price) || $price <= $filter_price){
                    $res .= '
                        <tr>
                            <td>'.$general->sqlToDate($value['InventoryAsset']['created'], true).'</td>
                            <td>'.$type.'</td>
                            <td>'.$oc.'</td>
                            <td>'.$fa.'</td>
                            <td>'.$fa_type.'</td>
                            <td>'.$value['Asset']['SubClass']['name'].'</td>
                            <td>'.$value['Asset']['name'].'</td>
                            <td>="'.$value['InventoryAsset']['code'].'"</td>
                            <td>'.$value['InventoryAsset']['original_price'].'</td>
                            <td>'.$general->assetPrice($value['InventoryAsset']['original_price'],$value['InventoryAsset']['created'],$value['InventoryAsset']['life'],$value['InventoryAsset']['residual_value'],$value['InventoryAsset']['is_depreciate']).'</td>
                            <td>'.$value['InventoryAsset']['situation'].'</td>
                        </tr>
                        ';
                }
            }
            
            $res .= '</table>';
            echo utf8_decode($res);
        }else if($type == 'baja'){
            $res = '
                <table><tr><td>Reporte de Bajas</td><td>'.  date('d/m/Y').'</td></tr></table>
                    <p></p>
                <table border="1">
                    <tr>
                        <td>Fecha Baja</td>
                        <td>Tipo Baja</td>
                        <td>Sub Clase</td>
                        <td>Decripción</td>
                        <td>Código</td>
                        <td>Precio Adquisición</td>
                        <td>Valor Libro</td>
                        <td>Estado</td>
                        <td>Comentario</td>
                    </tr>
                    ';
            foreach ($data  as $value) {
                $price = $general->assetPrice($value['InventoryAsset']['original_price'],$value['InventoryAsset']['created'],$value['InventoryAsset']['life'],$value['InventoryAsset']['residual_value'],$value['InventoryAsset']['is_depreciate'],$value['InventoryAssetDisposal']['created']);
                if(empty($filter_price) || $price <= $filter_price){
                    $res .= '
                        <tr>
                            <td>'.$general->sqlToDate($value['InventoryAssetDisposal']['created'], true).'</td>
                            <td>'.Configure::read('InventoryAssetDisposal.type.'.$value['InventoryAssetDisposal']['type']).'</td>
                            <td>'.$value['Asset']['SubClass']['name'].'</td>
                            <td>'.$value['Asset']['name'].'</td>
                            <td>="'.$value['InventoryAsset']['code'].'"</td>
                            <td>'.$value['InventoryAsset']['original_price'].'</td>
                            <td>'.$general->assetPrice($value['InventoryAsset']['original_price'],$value['InventoryAsset']['created'],$value['InventoryAsset']['life'],$value['InventoryAsset']['residual_value'],$value['InventoryAsset']['is_depreciate'],$value['InventoryAssetDisposal']['created']).'</td>
                            <td>'.$value['InventoryAsset']['situation'].'</td>
                            <td>'.$value['InventoryAssetDisposal']['comment'].'</td>
                        </tr>
                        ';
                }
            }
            
            $res .= '</table>';
            echo utf8_decode($res);
        }else if($type == 'all'){
            $res = '
                <table><tr><td>Reporte total de bienes del sistema</td><td>'.date('d/m/Y').'</td></tr></table>
                <p></p>
                <table border="1">
                    <tr>
                        <td>Código</td>
                        <td>Descripción</td>
                        <td>Serial</td>
                        <td>Precio Adquisición</td>
                        <td>Precio Actual</td>
                        <td>Vida Útil Restante</td>
                        <td>Valor Residual</td>
                        <td>Estado</td>
                        <td>Oficina</td>
                        <td>Piso</td>
                        <td>Dirección</td>
                        <td>Ciudad</td>
                        <td>Región</td>
                        <td>Tipo Alta</td>
                        <td>Fecha Ingreso (Alta)</td>
                        <td>Fecha Asignación</td>
                        <td>Tipo Baja</td>
                        <td>Fecha Baja</td>
                    </tr>
            ';
            
            foreach ($data as $value) {
                $o = '-';
                $f = '-';
                $a = '-';
                $c = '-';
                $r = '-';
                $fa = '-';
                $fd = '-';
                $ta = '-';
                $td = '-';
                if(!empty($value['InventoryAssetAllocation'])){
                    $o = $value['InventoryAssetAllocation'][0]['Office']['number'];
                    $f = $value['InventoryAssetAllocation'][0]['Floor']['number'];
                    $a = $value['InventoryAssetAllocation'][0]['Address']['name'];
                    $c = $value['InventoryAssetAllocation'][0]['City']['name'];
                    $r = $value['InventoryAssetAllocation'][0]['Region']['name'];
                    $fa = $general->sqlToDate($value['InventoryAssetAllocation'][0]['created'], true);
                    $ta = Configure::read('Transaction.type.'.$value['TransactionDetail']['Transaction']['type']);
                }
                if(!empty($value['InventoryAssetDisposal']['id'])){
                    $fd = $general->sqlToDate($value['InventoryAssetDisposal']['created'], true);
                    $td  = Configure::read('InventoryAssetDisposal.type.'.$value['InventoryAssetDisposal']['type']);
                }
                $price = $general->assetPrice($value['InventoryAsset']['original_price'],$value['InventoryAsset']['created'],$value['InventoryAsset']['life'],$value['InventoryAsset']['residual_value'],$value['InventoryAsset']['is_depreciate']);
                if(empty($filter_price) || $price <= $filter_price){
                    $res .= '
                        <tr>
                            <td>="'.$value['InventoryAsset']['code'].'"</td>
                            <td>'.$value['Asset']['name'].'</td>
                            <td>="'.$value['InventoryAsset']['serial'].'"</td>
                            <td>'.$value['InventoryAsset']['original_price'].'</td>
                            <td>'.$general->assetPrice($value['InventoryAsset']['original_price'],$value['InventoryAsset']['created'],$value['InventoryAsset']['life'],$value['InventoryAsset']['residual_value'],$value['InventoryAsset']['is_depreciate']).'</td>
                            <td>'.$value['InventoryAsset']['life'].'</td>
                            <td>'.$value['InventoryAsset']['residual_value'].'</td>
                            <td>'.Configure::read('InventoryAsset.status.'.$value['InventoryAsset']['status']).'</td>
                            <td>'.$o.'</td>
                            <td>'.$f.'</td>
                            <td>'.$a.'</td>
                            <td>'.$c.'</td>
                            <td>'.$r.'</td>
                            <td>'.$ta.'</td>
                            <td>'.$general->sqlToDate($value['InventoryAsset']['created'], true).'</td>
                            <td>'.$fa.'</td>
                            <td>'.$td.'</td>
                            <td>'.$fd.'</td>
                        </tr>
                        ';
                }
            }
            
            $res .= '</table>';
            echo utf8_decode($res);
        }else if($type == 'bitacora'){
            $res = '<table><tr><td>Reporte histórico de movimiento de bienes</td><td>'.date('d/m/Y').'</td></tr></table>
                <p></p>
                <table border="1">
                    <tr>
                        <td>Código</td>
                        <td>Descripción</td>
                        <td>Usuario operador</td>
                        <td>Tipo Historial</td>
                        <td>Comentario</td>
                ';
            if($tipo_bitacora == 0 || $tipo_bitacora == '*'){//Alta
                $res .= '<td>Tipo Alta</td>
                         <td>Fecha Alta</td>';
            }if($tipo_bitacora == 1 || $tipo_bitacora == '*'){//Asignacion
                $res .= '<td>Asignado a:</td>
                         <td>Oficina</td>
                         <td>Piso</td>
                         <td>Dirección</td>
                         <td>Ciudad</td>
                         <td>Región</td>
                         <td>Fecha asignación</td>';
            }if($tipo_bitacora == 2 || $tipo_bitacora == '*'){//Baja
                $res .= '<td>Tipo Baja</td>
                         <td>Observación de baja</td>
                         <td>Fecha baja</td>';
            }if($tipo_bitacora == 3 || $tipo_bitacora == '*'){//Liberacion
                $res .= '<td>Responsable</td>
                         <td>Fecha liberación</td>';
            }
            
            $res .= '</tr>';
            
            foreach ($data as $item) {
                $res .= '<tr>';
                $res .= '    <td>="'.$item['InventoryAsset']['code'].'"</td>
                             <td>'.$item['InventoryAsset']['Asset']['name'].'</td>
                             <td>'.$item['User']['names']. ' '. $item['User']['primary_last_name'] . ' ' . $item['User']['second_last_name'] .'</td>
                             <td>'.Configure::read('InventoryAssetHistory.type.'.$item['InventoryAssetHistory']['type']).'</td>
                             <td>'.$item['InventoryAssetHistory']['comment'].'</td>';
                             
            if($tipo_bitacora == '0' || ($tipo_bitacora == '*' && $item['InventoryAssetHistory']['type'] == '0')){//Alta
                    $ta = 'Ingreso masivo';
                    if(!empty($item['InventoryAsset']['TransactionDetail']['Transaction']['type']))
                        $ta = Configure::read('Transaction.type.'.$item['InventoryAsset']['TransactionDetail']['Transaction']['type']);
                    $res .= '<td>'.$ta.'</td>
                         <td>'.$item['InventoryAsset']['created'].'</td>';
            }else{
                
            }if($tipo_bitacora == '1' || ($tipo_bitacora == '*' && $item['InventoryAssetHistory']['type'] == '1')){//Asignacion
             
            $res .= '<td>'.$item['InventoryAssetAllocation']['User']['names'].' '. $item['InventoryAssetAllocation']['User']['primary_last_name'] . ' ' . $item['InventoryAssetAllocation']['User']['second_last_name'] .'</td>
                         <td>'.$item['InventoryAssetAllocation']['Office']['number'].'</td>
                         <td>'.$item['InventoryAssetAllocation']['Floor']['number'].'</td>
                         <td>'.$item['InventoryAssetAllocation']['Address']['name'].'</td>
                         <td>'.$item['InventoryAssetAllocation']['City']['name'].'</td>
                         <td>'.$item['InventoryAssetAllocation']['Region']['name'].'</td>
                         <td>'.$item['InventoryAssetAllocation']['created'].'</td>';
            }else{
                
            }if($tipo_bitacora == '2' || ($tipo_bitacora == '*' && $item['InventoryAssetHistory']['type'] == '2')){//Baja
                $res .= '<td>'.Configure::read('InventoryAssetDisposal.type.'.$item['InventoryAssetDisposal']['type']).'</td>
                         <td>'.$item['InventoryAssetDisposal']['comment'].'</td>
                         <td>'.$item['InventoryAssetDisposal']['created'].'</td>';
            }else{
                
            }if($tipo_bitacora == '3' || ($tipo_bitacora == '*' && $item['InventoryAssetHistory']['type'] == '3')){//Liberacion
                $res .= '<td>'.$item['InventoryAssetAllocation']['User']['names'].' '. $item['InventoryAssetAllocation']['User']['primary_last_name'] . ' ' . $item['InventoryAssetAllocation']['User']['second_last_name'].'</td>
                         <td>'.$item['InventoryAssetAllocation']['created'].'</td>';
            }else{
               
            }
                $res .= '</tr>';
            }
            $res .= '</table>';
            echo utf8_decode($res);
        }
        
        if($type == 'contabilidad') {
            App::import("Model","Ipc");
            $model = new Ipc();
            $res = '';
            $res .='
                    <table border="1">
                        <thead>
                            <tr>
                                <td>CODIGO INVENTARIO</td>
                                <td>DESCRIPCION</td>
                                <td>CLASE</td>
                                <td>SUBCLASE</td>
                                <td>FECHA ALTA</td>
                                <td>VALOR UNIDAD</td>
                                <td>VIDA UTIL</td>
                                <td>VALOR RESIDUAL</td>
                                <td>AÑO EJERCICIO</td>
                                <td>INDICE ACTUALIZACION</td>
                                <td>VALOR UNIDAD ACTUALIZADO</td>
                                <td>DEPRECIACION ACUMULADA ACTUALIZADA</td>
                                <td>VIDA UTIL RESTANTE</td>
                                <td>DEPRECIACION EJERCICIO</td>
                                <td>DEPRECIACION ACUMULADA</td>
                                <td>VALOR LIBRO</td>
                            </tr>
                        </thead>
                        <tbody>
            ';
            $valor_unidad_actualizado=0;
            $depreciacion_acumulada=0;
            foreach ($data as $item) {
                $price = $general->assetPrice($item['InventoryAsset']['original_price'],$item['InventoryAsset']['created'],$item['InventoryAsset']['life'],$item['InventoryAsset']['residual_value'],$item['InventoryAsset']['is_depreciate']);
                if(empty($filter_price) || $price <= $filter_price){
                    $vida = $general->vida($item['InventoryAsset']['created']);
                    $ano_adquisicion = (int)date('Y', strtotime($item['InventoryAsset']['created']));
                    $mes_adquisicion = (int)date('m', strtotime($item['InventoryAsset']['created']));

                    if($mes_adquisicion > 6)
                        $ano_adquisicion ++;
                    for($i = 0; $i < $vida; $i++){
                        $res .= '<tr>
                                 <td>="'.$item['InventoryAsset']['code'].'"</td>
                                 <td>'.$item['Asset']['name'].'</td>
                                 <td>'.$item['Asset']['MClass']['name'].'</td>
                                 <td>'.$item['Asset']['SubClass']['name'].'</td>
                                 <td>'.$item['InventoryAsset']['created'].'</td>
                                 <td>'.number_format($item['InventoryAsset']['original_price'],0,',','').'</td>
                                 <td>'.$item['InventoryAsset']['life'].'</td>
                                 <td>'.$item['InventoryAsset']['residual_value'].'</td>
                                 <td>'.($ano_adquisicion + $i).'</td>';
                        /* indice */
                        $indice = $model->find('first',array(
                            'conditions' => array(
                                'Ipc.date' => $ano_adquisicion + $i
                                )
                        ));

                        if(empty($indice))
                            $indice = 0;
                        else
                            $indice = $indice['Ipc']['value'];
                        $res .= '<td>'.$indice.'%</td>';
                        /* fin indice */
                        /* valor unidad actualizada */
                        if($i == 0)
                            $valor_unidad = $item['InventoryAsset']['original_price'];
                        else
                            $valor_unidad = $valor_unidad_actualizado;
                        $valor_unidad_actualizado = $valor_unidad * (($indice/100) + 1);
                        $res .= '<td>'.number_format($valor_unidad_actualizado,0,',','').'</td>';
                        /* fin valor unidad actualizado */
                        /* depreciacion acumulada actualizada */
                        $depreciacion_acumulada_actualizada = 0;
                        if($i != 0){
                            $depreciacion_acumulada_actualizada = $depreciacion_acumulada * (($indice/100) + 1);
                        }
                        $res .= '<td>'.number_format($depreciacion_acumulada_actualizada,0,',','').'</td>';
                        /* fin depreciacion acumulada actualizada */
                        /* vida util restante */
                        $vida_util_restante = $item['InventoryAsset']['life'] - $i;
                        $res .= '<td>'.$vida_util_restante.'</td>';
                        /* fin vida util restante */
                        /* depreciacion ejercicio */
                        $depreciacion_ejercicio = ($valor_unidad_actualizado - $item['InventoryAsset']['residual_value'])/$item['InventoryAsset']['life'];
                        $res .= '<td>'.number_format($depreciacion_ejercicio,0,',','').'</td>';
                        /* fin depreciacion ejercicio */
                        /* depreciacion acumulada */
                        $depreciacion_acumulada = $depreciacion_acumulada_actualizada + $depreciacion_ejercicio;
                        $res .= '<td>'.number_format($depreciacion_acumulada,0,',','').'</td>';
                        /* fin depreciacion acumulada */
                        /* valor libro */
                        $valor_libro = $valor_unidad_actualizado - $depreciacion_acumulada;
                        $res .= '<td>'.number_format($valor_libro,0,',','').'</td></tr>';
                    }
                }
            }
		$res .= '</tbody></table>';
            if($format == 'pdf'){
                App::import('Vendor','mypdf');
		// create new PDF document
		$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetSubtitle('Informe de Contabilidad');

		//set zoom, layout
		$pdf->SetDisplayMode('real', 'OneColumn');

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Ministerio Desarrollo Social');
                $pdf->SetTitle('Sistema de Inventario');
                $pdf->SetSubject('Sistema de Inventario');
                $pdf->SetKeywords('PDF');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('times', 'BI', 10);

		// add a page
		$pdf->AddPage();
                $html = $res;
		
		//$html = 'ok';
		$pdf->writeHTML($html, true, false, false, false, '');

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('imprime.pdf', 'I');
            }else if($format == 'excel') {
		echo utf8_decode($res);
            }

		//============================================================+
		// END OF FILE                                                
		//============================================================+
	}
        
	
?>