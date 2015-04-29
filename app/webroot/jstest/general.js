jQuery.fn.setSelected = function (text) {
	$("#" + $(this).attr('id') + " option:contains('" + text + "')").filter(function(index){return $(this).text() === text}).attr('selected', 'selected');
	return $(this);
}

//guarda las filas seleccionadas
var selectedRows = [];

/**
 * Agrega o elimina filas seleccionadas
 */
function updateSelRows(row_ids, status) {
	if(status) { //si estoy seleccionando guardo en selectedRows
		for(i in row_ids) if( selectedRows.indexOf(row_ids[i]) == -1 ) selectedRows.push(row_ids[i]);
	} else { //si estoy deseleccionando lo elimino de selectedRows
		for(i in row_ids) selectedRows.splice(selectedRows.indexOf(row_ids[i]), 1);
	}	
}

/**
 * Selecciona filas en la grilla
 */
$.fn.selectRows = function() {
	//recorro selectedRows y voy seleccionado una a una solo si esta presente en la grilla
	for (i=0; i < selectedRows.length; i++) {
		is_empty = isEmpty( $(this).getRowData(selectedRows[i]) );
		if( !is_empty ) $(this).setSelection(selectedRows[i], false);
	}
};

/**
 * Reponde si un objeto es o no vacío
 */
function isEmpty(obj) {
	for(var prop in obj) {
		if(obj.hasOwnProperty(prop))
			return false;
	}
	return true;
}