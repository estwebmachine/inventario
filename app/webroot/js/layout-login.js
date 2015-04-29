/* SVN FILE: $Id: layout-login.js 12 2010-04-29 12:30:00Z webmachine $ */
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
* @version      $Revision: 12 $
* @modifiedby   $LastChangedBy: webmachine $
* @lastmodified $Date: 2010-04-29 08:30:00 -0400 (jue, 29 abr 2010) $
*/
$(document).ready(function () {
	//mover debug info a footer
	$(".cake-debug").appendTo("#footer");
	$(".cake-stack-trace").appendTo("#footer");
	$(".cake-sql-log").appendTo("#footer");
	//layout
	var myOptions = {
		applyDefaultStyles: true,
		togglerTip_open: "Cerrar",
		togglerTip_closed: "Abrir",
		north__size: 95,
		north__resizable: false,
		north__closable: true,
		south__resizable: false,
		south__initClosed: true,
		south__maxSize: 150
	};
	$('body').layout( myOptions );
	//boton login
	$('input:submit').button();
});