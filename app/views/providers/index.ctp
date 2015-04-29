<script type="text/javascript">
    var id = '';
    function rutcheck(value, colname) {
        //llamada ajax para verificar la no exitencia del nombre de usuario
        var result = '';
        $.ajax({
            url: '<?php echo $html->url('/providers/rutcheck/') ?>',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: ({rut: value, id: id}),
            success: function(response) {
                if (response.result == "success") {
                    result = [true, ""];
                }
                else if (response.result == "failure") {
                    result = [false, "El proveedor ya existe"];
                }
            }
        });
        return result;
    }
$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/providers/indextable') ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id','Rut', 'Razón Social', 'Nombre Fantasia', 'Dirección', 'Contacto', 'Fono Contacto', 'Email Contacto', 'Observación', 'Estado','Creado', 'Modificado'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true},
			{name:'rut', index:'rut', editable:true, formoptions:{rowpos:1, elmprefix:"(*)"}, editrules: {custom: true, custom_func: rutcheck, required: true}},
			{name:'socialreason', index:'socialreason', editable:true, hidden:false, formoptions:{rowpos:2}, editrules:{edithidden:true}},
			{name:'fantasyname', index:'fantasyname', editable:true, formoptions:{rowpos:3, elmprefix:"(*)"}, editrules:{required:true}},
			{name:'address', index:'address', editable:true, formoptions:{rowpos:4}, search:false},
			{name:'contact_name', index:'contact_name', editable:true, formoptions:{rowpos:5}},
			{name:'contact_phone', index:'contact_phone', editable:true, formoptions:{rowpos:6}},
			{name:'contact_email', index:'contact_email', editable:true, formoptions:{rowpos:7}},
                        {name:'observation', index:'observation',editable:true,formoptions:{rowpos:8}},
			{name:'is_active', index:'is_active', editable:true, edittype:'select', formoptions:{rowpos:9}, editoptions:{value:"<?php echo $jqgrid->selectOpt('is_active');?>", defaultValue:'Habilitado'}, stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('is_active');?>"}, search:true},
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
		rowNum:15,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Definir Proveedores'); ?>',
		editurl: '<?php echo $html->url('/providers/indexedit') ?>',
		//height: 350,
		autowidth: true,
                onSelectRow: function(row_id) {
                    id = row_id;
                }
	});

	$("#list").jqGrid('navGrid', '#pager', {add:true, edit:true, del:<?php if($session->read('Auth.User.role') == 0) echo 'true'; else echo 'false'; ?>, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto',afterShowForm: function(formid){id = '';}, reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
	$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
	
});
</script>
<table id="list"></table>
<div id="pager"></div>