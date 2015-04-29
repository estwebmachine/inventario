<script type="text/javascript">
function populateSubClass(e) {
	var thisval = $(e.target).val();
	$.get(webroot + '/ajax/selectopt/sub_class?firstopt=Seleccione%20Genérico&mClass.id=' + thisval, function(data) {
		var res = $(data).html();
		$("#sub_class_id").html(res);
	}); // end get
}

function resetClass() {
	$("#sub_class_id").html('<option value=" ">Seleccione Genérico</option>');
}

function initEditClass() {
	row = $("#list").jqGrid('getGridParam','selrow');
	row_data = $("#list").getRowData(row);
	class_name = row_data['m_class_id'].replace(/ /g, '%20');
	$('#sub_class_id').load(webroot + '/ajax/selectopt/sub_class?firstopt=Seleccione%20Genérico&MClass.name=' + class_name + ' option', function() { $(this).setSelected(row_data['sub_class_id']); });
}

$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/assets/indextable') ?>/',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Descripción', 'Genérico', 'Clase', 'Estado', 'Creada', 'Modificado'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true},
			{name:'name', index:'name', editable:true, formoptions:{rowpos:1, elmprefix:"(*)"}, editrules:{required:true}},
			{name:'sub_class_id', index:'sub_class_id', editable:true, edittype:'select', formoptions:{rowpos:3, elmprefix:"(*)"}, editrules:{required:true}, editoptions:{value: ' :Cargando...'}},
			{name:'m_class_id', index:'m_class_id', editable:true, edittype:'select', formoptions:{rowpos:2, elmprefix:"(*)"}, editrules:{required:true}, editoptions:{dataUrl: webroot + '/ajax/selectopt/m_class?firstopt=Seleccione%20Clase', dataEvents: [{type: 'change', fn: populateSubClass}]}},
			{name:'is_active', index:'is_active', editable:true, edittype:'select', formoptions:{rowpos:4}, editoptions:{value:"<?php echo $jqgrid->selectOpt('is_active');?>", defaultValue:'Habilitado'}, stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('is_active');?>"}, search:true},
			{name:'created', index:'created', hidden:true},
			{name:'modified', index:'modified', editable:false, search:true, searchoptions:{dataInit: 
				function(element){
					$(element).datepicker({
						dateFormat: 'dd/mm/yy',
						onSelect: function(dateText, inst){
							var sgrid = $("#list")[0];
							sgrid.triggerToolbar();
						}
					});
				}
			}}
		],
		pager: '#pager',
		rowNum:10,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Definir Bien de Inventario'); ?>',
		editurl: '<?php echo $html->url('/assets/indexedit') ?>',
		//height: '100%',
		autowidth: true
	});

	$("#list").jqGrid('navGrid', '#pager', {add:true, edit:true, del:<?php if($session->read('Auth.User.role') == 0) echo 'true'; else echo 'true'; ?>, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, afterShowForm: initEditClass, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, afterShowForm: resetClass, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
	
	$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
	
//	$("#list").remapColumns([0,1,2,5,4,6,7,3,8,9,10,11,12,13,14,15,16],true,false);
});
</script>
<table id="list"></table>
<div id="pager"></div>