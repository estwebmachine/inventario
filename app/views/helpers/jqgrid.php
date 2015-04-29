<?php
/* SVN FILE: $Id: jqgrid.php 8 2010-04-26 19:05:03Z webmachine $ */
/**
 * Jqgrid Helper
 *
 * Funciones para utilizar con jqGrid
 *
 * PHP versión 5
 *
 * WebMachine, Desarrollo Web <http://www.webmachine.cl/>
 * Copyright 2010-2012, WebMachine Ltda.
 * Dominica N° 165 - Recoleta
 * Santiago, Chile
 *
 * @filesource
 * @copyright    Copyright 2010-2012, WebMachine Ltda.
 * @link         http://www.webmachine.cl WebMachine
 * @package      minju
 * @subpackage   minju.app.views.helpers
 * @version      $Revision: 8 $
 * @modifiedby   $LastChangedBy: webmachine $
 * @lastmodified $Date: 2010-04-26 15:05:03 -0400 (lun, 26 abr 2010) $
 */
/**
 * Jqgrid Helper
 *
 * Funciones para utilizar con jqGrid
 *
 * @package    minju
 * @subpackage minju.app.views.helpers
 */
class JqgridHelper extends AppHelper {
	/**
	 * Transforma un array de opciones select a formato string para jqgrid
	 *
	 * @param mixed $opt string o array de opciones, si es string es leido desde configuracion
	 * @return string opciones como string formateado para jqgrid
	 */
	function selectOpt($opt) {
		if(!is_array($opt)) $opt = Configure::read($opt);
		$result = '';
		$last_item = end($opt);
		foreach($opt as $key => $value) $result .= ($opt[$key] == $last_item)? $key . ':' . $value : $key . ':' . $value . ';' ;
		return $result;
	}
}
?>