<?php
class GeneralHelper extends AppHelper {
	function sqlToDate($sql, $time = false) {
		$date_str = 'd/m/Y';
		if($time) $date_str .= ' H:i:s';
		return date($date_str, strtotime($sql));
	}
	
	/**
	 * Formatea un número como moneda
	 * 
	 * @access public
	 * @param int $num el numero a formatear
	 * @param array $opciones opciones para aplicar a {@link http://www.php.net/manual/en/function.number-format.php number_format}
	 * @return string el número formateado 
	 */
	function money($num, $options = NULL) {
		$default_options = array('before' => '$', 'decimals' => 0, 'dec_point' => ',', 'thousands_sep' => '.', 'negative' => '-');
		$options = ! $options? $default_options : array_merge($default_options, $options);
		extract($options);
		if ($num < 0) $before = $negative . $before;
		
		return $before . number_format(abs($num), $decimals, $dec_point, $thousands_sep);
	}
	
	/**
	 * Envuelve un texto en <![CDATA["texto"]]> para que sea ignorado por el parser XML
	 * y evitar problemas con caracteres extraños: caracteres como "<" y "&" son ilegales en elementos XML.
	 * 
	 * @param string $text el texto a envolver para ser ignorado por el parser
	 * @return string el texto escapado
	 */
	function cdata($text, $utf8_enc = FALSE) {
		$text = $utf8_enc? utf8_encode($text) : $text;
		return sprintf('<![CDATA[%s]]>', $text);
	}
	
	/**
	 * Escapa texto XML de caracteres extraños: caracteres como "<" y "&" son ilegales en elementos XML. 
	 * @param type string $string texto a escapar
	 * @param type $utf8_enc si codifica o no en UTF8
	 * @return string el texto escapado
	 */
	function xmlEscape($string, $utf8_enc = FALSE) {
		$string = $utf8_enc? utf8_encode($string) : $string;
		return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
	}
	
	/**
	 * Calcula el precio actual de un bien de acuerdo a una formula dada
	 * precio_original - (precio_original - valor_residual) * meses_desde_ingreso / vida_util
	 * 
	 * @param int $init_val precio original
	 * @param string $created la fecha en que fue ingresado el bien a inventario
	 * @param int $life la vida útil del bien (en meses), parámetro fijo
	 * @param int $res_val valor residual del bien, parámetro fijo
	 * @return int el precio actual del bien
	 */
	function assetPrice($init_val, $created, $life, $res_val,$depreciar = 1,$fecha_baja = NULL) {
            //ACTUALIZACION DE PRECIO DE UN BIEN DE INVENTARIO
            $mes_created = (int) date('m',strtotime($created));
            $anho_created = (int) date('Y', strtotime($created));
            $anho_para_ipc = $anho_created;
            if($mes_created > 6){//Pregunta si fue adquirido en el 2º semestre (Julio-Diciembre)
                $anho_para_ipc ++;//Si fue adquirido en el segundo semestre no se actualiza con el ipc de ese año
            }
            
             //IPC acumulado
            App::import("Model","Ipc");
            $model = new Ipc();
            $ipcs = $model->find('all',array(
                'conditions' => array(
                    'Ipc.date >=' => $anho_para_ipc
                )
            ));
            $ipc_acumulado = 1;
            foreach ($ipcs as $ipc) {
                $ipc_acumulado *= ($ipc['Ipc']['value']/100) + 1;
            }
            //Fin IPC acumulado
            
            $factor_ipc = $ipc_acumulado;//Factor de actualizacion
            
            $val_actualizado = $init_val * $factor_ipc; //Valor actualizado del bien
            //FIN ACTUALIZACION DE PRECIO DE UN BIEN DE INVENTARIO
            if($depreciar == 0)
                return round($val_actualizado);
            //DEPRECIACION
            if($mes_created < 7){//Pregunta si fue adquirido en el 1º semestre (Enero-Junio)
                $anho_created --;//Si fue adquirido en el primer semestre se deprecia ese año
            }
            if(empty($fecha_baja))
                $today = (int)date('Y');//año actual
            else
                $today = (int)date('Y', strtotime($fecha_baja));
            $diff = abs($today - $anho_created);//años desde su compra a la fecha
            //calculo el precio actual según formula
            $price = round($val_actualizado - (($val_actualizado - $res_val) * $diff / $life));
            return $price;
	}
        
        function vida($fecha_adquisicion){
            $ano_adquisicion = (int)date('Y', strtotime($fecha_adquisicion));
            $vida_restante = (int)date('Y') - $ano_adquisicion;
            $mes_adquisicion = (int)date('m', strtotime($fecha_adquisicion));
            if($mes_adquisicion <= 6)
                $vida_restante ++;
            return $vida_restante;
        }
        
        function selectOpt($opt) {
		if(!is_array($opt)) $opt = Configure::read($opt);
		$result = '';
		foreach($opt as $key => $value) $result .=  '<option value="'.$key . '">'.$value.'</option>';
		return $result;
	}
	
}
?>