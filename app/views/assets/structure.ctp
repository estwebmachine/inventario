<script type="text/javascript">
var classid = '';
var subclassid = '';

function classnamecheck(value, colname) {
        //llamada ajax para verificar la no exitencia del nombre de usuario
        var result = '';
        $.ajax({
            url: '<?php echo $html->url('/m_classes/classnamecheck/') ?>',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: ({classname: value, id: classid}),
            success: function(response) {
                if (response.result == "success") {
                    result = [true, ""];
                }
                else if (response.result == "failure") {
                    result = [false, "La clase ya existe"];
                }
            }
        });
        return result;
    }
    
function subclassnamecheck(value, colname) {
        //llamada ajax para verificar la no exitencia del nombre de usuario
        var result = '';
        var classname = $('#m_class_id option:selected').val();
        $.ajax({
            url: '<?php echo $html->url('/sub_classes/subclassnamecheck/') ?>',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: ({subclassname: value, id: subclassid, classname:classname}),
            success: function(response) {
                if (response.result == "success") {
                    result = [true, ""];
                }
                else if (response.result == "failure") {
                    result = [false, "La sub clase ya existe"];
                }
            }
        });
        return result;
    }

$(document).ready(function(){
	//grupos
	var classGrid = function() {
		$("#class-list").jqGrid({
			url:'<?php echo $html->url('/m_classes/indextable') ?>',
			datatype: 'xml',
			mtype: 'GET',
			colNames:['Id', 'Nombre', 'Estado','Creada','Modificada'],
			colModel :[
				{name:'id', index:'id', editable:false, hidden:true},
				{name:'name', index:'name', editable:true, formoptions:{rowpos:2, elmprefix:"(*)"}, editrules: {custom: true, custom_func: classnamecheck, required: true}},
                                {name:'is_active', index:'is_active', editable:true,edittype:'select', formoptions:{rowpos:3},editoptions: {value: "<?php echo $jqgrid->selectOpt('is_active'); ?>", defaultValue: 'Habilitado'}, stype: 'select', searchoptions: {value: ":;<?php echo $jqgrid->selectOpt('is_active'); ?>"}},
				{name:'created', index:'created', hidden:true},
				{name:'modified', index:'modified', editable:false, search:true, searchoptions:{dataInit: 
					function(element){
						$(element).datepicker({
							dateFormat: 'dd/mm/yy',
							onSelect: function(dateText, inst){
								var sgrid = $("#class-list")[0];
								sgrid.triggerToolbar();
							}
						});
					}
				}}
			],
			pager: '#class-pager',
			rowNum:10,
			rowList:[10,20,30],
			sortname: 'id',
			sortorder: 'desc',
			viewrecords: true,
			caption: '<?php __('Definir Clases'); ?>',
			editurl: '<?php echo $html->url('/m_classes/indexedit') ?>',
			//height: 350,
			autowidth: true,
			onSelectRow: function(row_id) {
				classid = row_id;
			}
		});

		$("#class-list").jqGrid('navGrid', '#class-pager', {add:true, edit:true, del:<?php if($session->read('Auth.User.role') == 0) echo 'true'; else echo 'true'; ?>, search:false}, //options
			{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
			{modal: true, width:350, height:'auto', afterShowForm: function(formid){classid = '';}, reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
			{width:380, height:'auto', reloadAfterSubmit:false,msg:'¿Desea eliminar la clase seleccionada, esto eliminara las sub clases hijas?'}, // del options
			{} // search options
		);
		
		$("#class-list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
	}

	//familias
	var subclassGrid = function() {
		$("#subclass-list").jqGrid({
			url:'<?php echo $html->url('/sub_classes/indextable') ?>',
			datatype: 'xml',
			mtype: 'GET',
			colNames:['Id', 'Nombre', 'Clase', 'Vida útil', 'Valor residual','Estado','Creada', 'Modificada'],
			colModel :[
				{name:'id', index:'id', editable:false, hidden:true},
				{name:'name', index:'name', editable:true, formoptions:{rowpos:2, elmprefix:"(*)"}, editrules: {custom: true, custom_func: subclassnamecheck, required: true}},
				{name:'m_class_id', index:'m_class_id', editable:true, edittype:'select', formoptions:{rowpos:3}, editoptions:{dataUrl: webroot + '/ajax/selectopt/m_class'}},
				{name:'life', index:'life', editable:true, formoptions:{rowpos:4}},
                                {name:'residual_value', index:'residual_value', editable:true, formoptions:{rowpos:5}},
                                {name:'is_active', index:'is_active', editable:true,edittype:'select', formoptions:{rowpos:6},editoptions: {value: "<?php echo $jqgrid->selectOpt('is_active'); ?>", defaultValue: 'Habilitado'}, stype: 'select', searchoptions: {value: ":;<?php echo $jqgrid->selectOpt('is_active'); ?>"}},
                                {name:'created', index:'created', hidden:true},
				{name:'modified2', index:'modified2', editable:false, search:true, searchoptions:{dataInit: 
					function(element){
						$(element).datepicker({
							dateFormat: 'dd/mm/yy',
							onSelect: function(dateText, inst){
								var sgrid = $("#subclass-list")[0];
								sgrid.triggerToolbar();
							}
						});
					}
				}}
			],
			pager: '#subclass-pager',
			rowNum:10,
			rowList:[10,20,30],
			sortname: 'id',
			sortorder: 'desc',
			viewrecords: true,
			caption: '<?php __('Definir Genéricos'); ?>',
			editurl: '<?php echo $html->url('/sub_classes/indexedit') ?>',
			//height: 350,
			autowidth: true,
			onSelectRow: function(row_id) {
				subclassid = row_id;
			}
		});

		$("#subclass-list").jqGrid('navGrid', '#subclass-pager', {add:true, edit:true, del:<?php if($session->read('Auth.User.role') == 0) echo 'true'; else echo 'true'; ?>, search:false}, //options
			{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
			{modal: true, width:350, height:'auto', afterShowForm: function(formid){subclassid = '';}, reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
			{width:350, height:'auto', reloadAfterSubmit:false}, // del options
			{sopt:['eq']} // search options
		);
		
		$("#subclass-list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
	}
        
	//tabs
	var initialized = [false, false];
	$('#tabs').tabs({
		show: function(event, ui){
			if (ui.index == 0 && !initialized[0]) {
				classGrid();
			} else if (ui.index == 1 && !initialized[1]) {
				subclassGrid();
			}
			initialized[ ui.index ] = true;
		},
		select: function(event, ui) {
			if (ui.index == 1 && initialized[1]) {
				$('#m_class_id').load(webroot + '/ajax/selectopt/m_class option');
			}else if (ui.index == 2 && initialized[2]) {
				$('#sub_class_id').load(webroot + '/ajax/selectopt/sub_class option');
			}
		}
	});
});
</script>

<div id="tabs">
	<ul>
		<li><a href="#class"><?php __('Clases') ;?></a></li>
		<li><a href="#subclass"><?php __('Genéricos') ;?></a></li>
	</ul>
	<div id="class">
		<table id="class-list"></table>
		<div id="class-pager"></div>
	</div>
	<div id="subclass">
		<table id="subclass-list"></table>
		<div id="subclass-pager"></div>
	</div>
</div>