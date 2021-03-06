<?php
/* SVN FILE: $Id: vendors.php 7 2010-04-22 20:05:27Z webmachine $ */
/**
 * DESCRIPCION_CORTA
 *
 * DESCRIPCION_LARGA
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
 * @subpackage   minju.app.webroot.js
 * @version      $Revision: 7 $
 * @modifiedby   $LastChangedBy: webmachine $
 * @lastmodified $Date: 2010-04-22 16:05:27 -0400 (jue, 22 abr 2010) $
 */
/**
 * Enter description here...
 */
if (isset($_GET['file'])) {
	$file = $_GET['file'];
	$pos = strpos($file, '..');
	if ($pos === false) {
		if (is_file('../../vendors/javascript/'.$file) && (preg_match('/(\/.+)\\.js/', $file))) {
			readfile('../../vendors/javascript/'.$file);
			return;
		}
	}
}
header('HTTP/1.1 404 Not Found');
?>