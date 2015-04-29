/* SVN FILE: $Id: layout-default.js 79 2010-11-26 06:10:14Z caacuna $ */
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
* @version      $Revision: 79 $
* @modifiedby   $LastChangedBy: caacuna $
* @lastmodified $Date: 2010-11-26 03:10:14 -0300 (vie, 26 nov 2010) $
*/
var myLayout; // init global vars
//opciones globales jqgrid
jQuery.extend(jQuery.jgrid.defaults, { altRows:true, height:'100%' });

$(document).ready(function () {
	$('#ThemeRoller').themeswitcher({
		cookieName: '',
		initialText: 'Seleccione Tema',
		buttonPreText: 'Tema: ',
		//loadTheme: theme,
		closeOnSelect: false,
		onSelect: function(themeName) {
			//llamada ajax,  guarda el theme seleccionado
			$.post(webroot + '/users/themechange', {theme: themeName});
		}
	});
	
	$('#ThemeRoller .jquery-ui-themeswitcher-title').text('Tema: ' + theme);

	//mover debug info a footer
	$(".cake-debug").appendTo("#footer");
	$(".cake-stack-trace").appendTo("#footer");
	$(".cake-sql-log").appendTo("#footer");
	//layout
	var resize = function(dimension) {
		if(dimension == 'width') $("#list").setGridWidth($('#content').width());
		if(dimension == 'height') $("#list").setGridHeight($('#content').height());
	}

	var jsession = function(mode, data, def) {
		if(mode == 'write') $.post(webroot + '/users/jsession/write', data);
		else if(mode == 'read') {
			result = eval('jsession_data.' + data);
			if(result == 'true') result = true;
			else if(result == 'false') result = false;
			if(result == undefined) result = def;
			return result;
		}
	}

	var myOptions = {
		applyDefaultStyles: true,
		togglerTip_open: "Cerrar",
		togglerTip_closed: "Abrir",
		north__size: 95,
		north__resizable: false,
		north__initClosed: jsession('read', 'north__initClosed', false),
		north__onclose: function() {jsession('write', {'north__initClosed':true});},
		north__onopen: function() {jsession('write', {'north__initClosed':false});},
		south__maxSize: 150,
		south__resizable: false,
		south__initClosed: jsession('read', 'south__initClosed', true),
		south__onclose: function() {jsession('write', {'south__initClosed':true});},
		south__onopen: function() {jsession('write', {'south__initClosed':false});},
		west__size:	250,
		west__resizable: false,
		west__initClosed: jsession('read', 'west__initClosed', false),
		west__onclose: function() {resize('width');jsession('write', {'west__initClosed':true});},
		west__onopen: function() {resize('width');jsession('write', {'west__initClosed':false});}
	};
	myLayout = $('body').layout( myOptions );

	//top-menu
	$('#top-menu').buttonset();
	$('#top-menu a').each(function() { //asigna iconos
		$(this).button('option', 'icons', {primary: $(this).attr('rel')});
	});
	//left-menu
	$("#accordion").accordion({
		header: 'h3',
		collapsible: true,
		active: false,
		autoHeight: false
	});
	var accordion_selected = $('#accordion-selected').attr('rel');
	if(accordion_selected != undefined) $("#accordion").accordion( "activate", parseInt(accordion_selected) );
});