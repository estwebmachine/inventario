<?php
class PageorderHelper extends AppHelper{
	var $helpers = array('Html', 'Form');
	
	function armarTablaItem( $id, $subtitle_id, $item_id, $assignment_id, $valor, $paymentOrderId )
	{
		$this->Subtitle=& ClassRegistry::init('Subtitle');
		$this->Item=& ClassRegistry::init('Item');
		$this->Assignment=& ClassRegistry::init('Assignment');
		$subtitleList = $this->Subtitle->getList();
		$itemList = $this->Item->getList($subtitle_id);
		$assignment = $this->Assignment->getList($item_id);
		echo '<tr id="filaItemAsig_'.$id.'" class="cantFilaItemAsig" rel='.$paymentOrderId.'>';
			echo '<td colspan="3">';
			echo '<input type="hidden" id="paydetailId_'.$id.'" name="data[PaymentOrderDetail]['.$id.'][id]" value='.$paymentOrderId.' />';
			echo '<select id="subtitles_'.$id.'" name="data[PaymentOrderDetail]['.$id.'][subtitle_id]" class="subtitles" rel="'.$id.'">';
				foreach($subtitleList as $i => $value){
					if( $i == $subtitle_id ){
						echo '<option value="'.$i.'" selected="selected">'.$value.'</option>';
					}
					else{
						echo '<option value="'.$i.'">'.$value.'</option>';						
					}							
				}

			echo '</select>';
			echo '<select id="items_'.$id.'" name="data[PaymentOrderDetail]['.$id.'][item_id]" class="items" rel="'.$id.'">';
				foreach( $itemList as $i => $value){
					if( $i == $item_id ){
						echo '<option value="'.$i.'" selected="selected">'.$value.'</option>';
					}
					else{
						echo '<option value="'.$i.'">'.$value.'</option>';
					}					
				}
			echo '</select>';
			echo '<select id="assignments_'.$id.'" name="data[PaymentOrderDetail]['.$id.'][assignment_id]" class="assignments" rel="'.$id.'">';
				foreach( $assignment as $i => $value){
					if( $i == $assignment_id ){
						echo '<option value="'.$i.'" selected="selected">'.$value.'</option>';
					}
					else{
						echo '<option value="'.$i.'">'.$value.'</option>';
					}					
				}
			echo '</select>';
			echo '</td>';
			echo '<td><input class="paymentOrderMount" id="filaItemAsigValue_'.$id.'" onkeyup="calculaTotal()" value="'.$valor.'" name="data[PaymentOrderDetail]['.$id.'][amount]" size="9" onkeypress="return permite(event, \'num\');" /></td>';
			echo '<td>'.$this->Html->image("icons/menos.png", array( "class"=>"eliminarFilaItemAsig", "rel"=>$id)).'</td>';
		echo '</tr>';
	}
	
	function linkGenerales( $rol, $user_id, $status )
	{
		if( $rol == 7){
			$is_minju = 0;
		}
		else{
			$is_minju = 1;
		}
		$url = $_SERVER['HTTP_HOST'];
		$this->PaymentDocument=& ClassRegistry::init('PaymentDocument');
		$this->PaymentOrder=& ClassRegistry::init('PaymentOrder');
		$ingresadas = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.status' => 0)));
		$coordinador = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.is_minju'=>$is_minju, 'PaymentDocument.status' =>2)));
		$coorRechaza = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.coordinador_id'=>$user_id, 'PaymentDocument.status' =>8)));
		$oPAnalisis = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.user_id' => $user_id,'PaymentDocument.status' => 3)));
		$doReasignadas = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.user_id' => $user_id,'PaymentDocument.status' => 10)));
		$payOrderPend = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.user_id' => $user_id,'PaymentDocument.status' => 4)));	
		$payOrderAnuladasCor = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.coordinador_id' => $user_id,'PaymentDocument.status' => 9)));
		$payOrderAprobar = $this->PaymentOrder->find('count', array('conditions'=>array('PaymentOrder.coordinador_id' => $user_id,'PaymentOrder.status' => 6)));
		$terminadas = $this->PaymentOrder->find('count', array('conditions'=>array('PaymentOrder.status'=>7)));
		$anuladas = $this->PaymentDocument->find('count',array('conditions'=>array('PaymentDocument.status'=>9)));
		$asigCoor = $this->PaymentDocument->find('count',array('conditions'=>array('PaymentDocument.status '=> array(2))));
		$opAsigCoor = $this->PaymentDocument->find('count',array('conditions'=>array('PaymentDocument.coordinador_id' => $user_id, 'PaymentDocument.status'=>array( 3, 4, 10))));
		$aprobadas = $this->PaymentOrder->find('count',array('conditions'=>array('PaymentOrder.user_id' => $user_id, 'PaymentOrder.status'=>6)));
		$aprobadasCor = $this->PaymentOrder->find('count',array('conditions'=>array('PaymentOrder.coordinador_id' => $user_id, 'PaymentOrder.status'=>7)));
		$payOrderAnuladasAna = $this->PaymentDocument->find('count', array('conditions'=>array('PaymentDocument.user_id' => $user_id,'PaymentDocument.status' =>8)));
		
		$linkIngresadas = "<li>".$this->Html->link('Documentos ingresados ('.$ingresadas.')', '/payment_documents/start')."</li>";
		$linkAsignadas = "<li>".$this->Html->link('Documentos por Asignar ('.$coordinador.')', '/payment_documents/asigdocumentpage')."</li>";
		$linkOpAnalisis = "<li>".$this->Html->link('Documentos Asignados  ('.$oPAnalisis.')', '/payment_documents/anadocumentpage')."</li>";
		$linkCorRechaza = "<li>".$this->Html->link('Documentos rechazados por analista ('.$coorRechaza.')', '/payment_documents/rechazadocumentpage')."</li>";
		$linkReasignadas = "<li>".$this->Html->link('Documentos re asignados ('.$doReasignadas.')', '/payment_documents/reasignadodocumentpage')."</li>";
		$linkOpPend = "<li>".$this->Html->link('Orden de Pago en Proceso ('.$payOrderPend.')', '/payment_orders/ordenpagoentramite')."</li>";
		$linkTerminadas = "<li>".$this->Html->link('Ordenes de pago aprobadas ('.$terminadas.')', '/payment_documents/end')."</li>";
		$linkAnuladas = "<li>".$this->Html->link('Documentos Anulados ('.$anuladas.')', '/payment_documents/badend')."</li>";
		$linkOpAproadas = "<li>".$this->Html->link('Pagos aprobados por Analista ('.$payOrderAprobar.')', '/payment_orders/approve')."</li>";
		$linkOpAsignadas = "<li>".$this->Html->link('Ordenes de pago Asignadas ('.$opAsigCoor.')', '/payment_documents/viewcorassig')."</li>";
		$linkViewStatus = "<li>".$this->Html->link('Documentos enviados a coordinador ('.$asigCoor.')', '/payment_documents/viewassig')."</li>";
		$linkOpAprobadaAnalista = "<li>".$this->Html->link('Ordenes de pago enviadas ('.$aprobadas.')', '/payment_orders/approveanalist')."</li>";
		$linkOpAprobadaCoordinador = "<li>".$this->Html->link('Pagos aprobados por Coordinador ('.$aprobadasCor.')', '/payment_orders/approveancoordinador')."</li>";
		$linkDocAnuladoPorCordinador = "<li>".$this->Html->link('Documentos anulados por Coordinador ('.$payOrderAnuladasCor.')', '/payment_documents/nullcoordinador')."</li>";
		$linkDocAnuladoPorAnalista = "<li>".$this->Html->link('Documentos Rechazados ('.$payOrderAnuladasAna.')', '/payment_documents/rechazaanalista')."</li>";
		
		$link = '<table class="grid-links">';
		$link .= '<tr>';
		$link .= '<td>';
		$link .='<ul>';
				switch( $rol ){
					case 6:
						$link .= $linkIngresadas;
						$link .= $linkTerminadas;
						$link .= $linkAnuladas;
						$link .= $linkViewStatus;
						break;
					case 7:
					case 8:
					case 11:
						$link .= '<li style="list-style: none;">Coordinador</li>';
						$link .= $linkAsignadas;
                        $link .= $linkOpAsignadas;
						$link .= $linkOpAproadas;
                        $link .= $linkOpAprobadaCoordinador;                  						
						$link .= $linkDocAnuladoPorCordinador;	
						$link .= $linkCorRechaza;
						
						$link .= '</ul></td><td><ul>';
						
						$link .= '<li style="list-style: none;">Analista</li>';
						$link .= $linkOpAnalisis;
						$link .= $linkReasignadas;
						$link .= $linkDocAnuladoPorAnalista;
						$link .= $linkOpPend;
						$link .= $linkOpAprobadaAnalista;
						break;
					case 5:
						$link .= $linkOpAnalisis;
						$link .= $linkReasignadas;
						$link .= $linkDocAnuladoPorAnalista;
						$link .= $linkOpPend;
						$link .= $linkOpAprobadaAnalista;
						
						break;
				}
		$link .= '</ul>';
		$link .= '</td>';
		$link .= '</tr>';
		$link .= '</table>';
			
			return $link;
	}

	function num2letras($num, $fem = true, $dec = true) {
		//if (strlen($num) > 14) die("El número introducido es demasiado grande");
		$matuni[2]  = "dos";
		$matuni[3]  = "tres";
		$matuni[4]  = "cuatro";
		$matuni[5]  = "cinco";
		$matuni[6]  = "seis";
		$matuni[7]  = "siete";
		$matuni[8]  = "ocho";
		$matuni[9]  = "nueve";
		$matuni[10] = "diez";
		$matuni[11] = "once";
		$matuni[12] = "doce";
		$matuni[13] = "trece";
		$matuni[14] = "catorce";
		$matuni[15] = "quince";
		$matuni[16] = "dieciseis";
		$matuni[17] = "diecisiete";
		$matuni[18] = "dieciocho";
		$matuni[19] = "diecinueve";
		$matuni[20] = "veinte";
		$matunisub[2] = "dos";
		$matunisub[3] = "tres";
		$matunisub[4] = "cuatro";
		$matunisub[5] = "quin";
		$matunisub[6] = "seis";
		$matunisub[7] = "sete";
		$matunisub[8] = "ocho";
		$matunisub[9] = "nove";
		$matdec[2] = "veint";
		$matdec[3] = "treinta";
		$matdec[4] = "cuarenta";
		$matdec[5] = "cincuenta";
		$matdec[6] = "sesenta";
		$matdec[7] = "setenta";
		$matdec[8] = "ochenta";
		$matdec[9] = "noventa";
		$matsub[3]  = 'mill';
		$matsub[5]  = 'bill';
		$matsub[7]  = 'mill';
		$matsub[9]  = 'trill';
		$matsub[11] = 'mill';
		$matsub[13] = 'bill';
		$matsub[15] = 'mill';
		$matmil[4]  = 'millones';
		$matmil[6]  = 'billones';
		$matmil[7]  = 'de billones';
		$matmil[8]  = 'millones de billones';
		$matmil[10] = 'trillones';
		$matmil[11] = 'de trillones';
		$matmil[12] = 'millones de trillones';
		$matmil[13] = 'de trillones';
		$matmil[14] = 'billones de trillones';
		$matmil[15] = 'de billones de trillones';
		$matmil[16] = 'millones de billones de trillones';
		$num = trim((string)@$num);
		if ($num[0] == '-') {
			$neg = 'menos ';
			$num = substr($num, 1);
		}else
		$neg = '';
		while ($num[0] == '0') $num = substr($num, 1);
		if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
		$zeros = true;
		$punt = false;
		$ent = '';
		$fra = '';
		for ($c = 0; $c < strlen($num); $c++) {
			$n = $num[$c];
			if (! (strpos(".,'''", $n) === false)) {
				if ($punt) break;
				else{
					$punt = true;
					continue;
				}
			}elseif (! (strpos('0123456789', $n) === false)) {
				if ($punt) {
					if ($n != '0') $zeros = false;
					$fra .= $n;
				}else
				$ent .= $n;
			}else
			break;
		}
		 
		$ent = '     ' . $ent;
		 
		if ($dec and $fra and ! $zeros) {
			$fin = ' coma';
			for ($n = 0; $n < strlen($fra); $n++) {
				if (($s = $fra[$n]) == '0')
				$fin .= ' cero';
				elseif ($s == '1')
				$fin .= $fem ? ' una' : ' un';
				else
				$fin .= ' ' . $matuni[$s];
			}
		}else
		$fin = '';
		if ((int)$ent === 0) return strtoupper('Cero ' . $fin);
		$tex = '';
		$sub = 0;
		$mils = 0;
		// $neutro = false; //COMENTEADO EL 11 09 09 PARA OP
		$neutro = true;
		 
		while ( ($num = substr($ent, -3)) != '   ') {
			 
			$ent = substr($ent, 0, -3);
			if (++$sub < 3 and $fem) {
				$matuni[1] = 'una';
				$subcent = 'as';
			}else{
				$matuni[1] = $neutro ? 'un' : 'uno';
				$subcent = 'os';
			}
			$t = '';
			$n2 = substr($num, 1);
			if ($n2 == '00') {
			}elseif ($n2 < 21)
			$t = ' ' . $matuni[(int)$n2];
			elseif ($n2 < 30) {
				$n3 = $num[2];
				if ($n3 != 0) $t = 'i' . $matuni[$n3];
				$n2 = $num[1];
				$t = ' ' . $matdec[$n2] . $t;
			}else{
				$n3 = $num[2];
				if ($n3 != 0) $t = ' y ' . $matuni[$n3];
				$n2 = $num[1];
				$t = ' ' . $matdec[$n2] . $t;
			}
			 
			$n = $num[0];
	
			if ($n == 1) {
				//$t = ' ciento' . $t; //comentado el 11 09 09 para OP
				/* ARGREGADO PARA OP 11 09 09 */
				if($num[1]==0 && $num[2]==0){
					$t = ' cien' . $t;
				}else{
					$t = ' ciento' . $t;
				}
				/* FIN AGREGADO */
			}elseif ($n == 5){
				$t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
			}elseif ($n != 0){
				$t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
			}
			 
			if ($sub == 1) {
			}elseif (! isset($matsub[$sub])) {
				if ($num == 1) {
					$t = ' mil';
				}elseif ($num > 1){
					$t .= ' mil';
				}
			}elseif ($num == 1) {
				$t .= ' ' . $matsub[$sub] . 'ón';
			}elseif ($num > 1){
				$t .= ' ' . $matsub[$sub] . 'ones';
			}
			if ($num == '000') $mils ++;
			elseif ($mils != 0) {
				if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
				$mils = 0;
			}
			$neutro = true;
			$tex = $t . $tex;
		}
		$tex = $neg . substr($tex, 1) . $fin;
		/* ADJUNTADO PARA OP 08-09-2009 */
		if($tex!=''){
			$tex.=" pesos";
		}
		/* FIN */
		return strtoupper($tex);
	}	
	
	function cambiaf_a_normal($fecha){
		ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
		$lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
		return $lafecha;
	}
	
	function formateo_rut($rut){
		return number_format( substr($rut, 0, -1), 0, "", ".") . '-' . substr($rut, -1);
	}

	function armarUnidad()
	{
		$this->Division=& ClassRegistry::init('Division');
		$selectOption = $this->Division->getList();
		$fila = '';
		$fila .= '<span class="etiqueta4"></span>';
		$fila .= '<span class="unidadDemandante">';
		$fila .= '<select><option value=""></option></select>';
		$fila .= '<select><option value="">Sel..</option></select>';
		$fila .= '<img src="'. $this->webroot.'img/icons/menos.png" alt="Quitar" />';
		$fila .= '</span>';
		return $fila;
	}
	
	function subdivision( $division )
	{
		$this->Subdivision=& ClassRegistry::init('Subdivision');
		$subDivision= $this->Subdivision->find('all', array('conditions'=>array('Subdivision.is_active'=>0, 'Subdivision.division_id'=> $division ), 'fields'=>array('Subdivision.id','Subdivision.number') ) );
		if( count($subDivision) < 10 ){
			foreach( $subDivision as $row){
				$subDivision_list[$row['Subdivision']['id']] = "0".$row['Subdivision']['number'];
			}
		}
		else{
			foreach( $subDivision as $row){
				$subDivision_list[$row['Subdivision']['id']] = "0".$row['Subdivision']['number'];
			}
		}
		
		return $subDivision_list;		
	}
}
?>