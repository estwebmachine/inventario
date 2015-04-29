<script type="text/javascript">
    
$(document).ready(function(){
    
        function initEditCostCenter() {
                row = $("#list").jqGrid('getGridParam','selrow');
                row_data = $("#list").getRowData(row);
                level = row_data['level'].replace(/ /g, '%20');
                level_id=0;
                if(level == 'Unidad')
                    level_id=2;
                else if(level == 'Departamento')
                    level_id = 1;
                $('#parent_id').load(webroot + '/ajax/selectopt/cost_center?firstopt=Seleccione%20Padre&level='+level_id+' option', function() { $(this).setSelected(row_data['parent_id']); });
        }
        
        function populateParent(e) {
                var thisval = $(e.target).val();
                $.get(webroot + '/ajax/selectopt/cost_center/?firstopt=Seleccione%20Padre&level='+(thisval-1), function(data) {
                    var res = $(data).html();
                    $("#parent_id").html(res);
                }); // end get
        }

        function resetCostCenter() {
                $("#parent_id").html('<option value="">Seleccione Padre</option>');
        }
	$("#list").jqGrid({
		url:'<?php echo $html->url('/cost_centers/indextable') ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Código','Nombre', 'Tipo', 'Padre', 'Estado','Creado', 'Modificado'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true},
                        {name:'code', index:'code', editable:true, formoptions:{rowpos:1, elmprefix:"(*)"}, editrules:{required:true},search:true},
			{name:'name', index:'name', editable:true, formoptions:{rowpos:2, elmprefix:"(*)"}, editrules:{required:true},search:true},
			{name:'level', index:'level', editable:true, edittype:'select', formoptions:{rowpos:3}, editoptions:{value:"<?php echo $jqgrid->selectOpt('level'); ?>", dataEvents: [{type: 'change', fn: populateParent}]},stype: 'select', searchoptions: {value: ": ;<?php echo $jqgrid->selectOpt('level'); ?>"}},
			{name:'parent_id', index:'parent_id', editable:true, edittype:'select', formoptions:{rowpos:4}, editoptions:{value:":Cargando ...;"}},
                        {name:'is_active', index:'is_active', editable:true, edittype:'select', formoptions:{rowpos:5}, editoptions:{value:"<?php echo $jqgrid->selectOpt('is_active');?>", defaultValue:'Habilitado'}, stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('is_active');?>"}, search:true},
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
		caption: '<?php __('Definir Centros de Costos'); ?>',
		editurl: '<?php echo $html->url('/cost_centers/indexedit') ?>',
		//height: 350,
		autowidth: true
	});

	$("#list").jqGrid('navGrid', '#pager', {add:true, edit:true, del:<?php if($session->read('Auth.User.role') == 0) echo 'true'; else echo 'false'; ?>, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true,beforeShowForm: function(form) { $('#tr_level', form).hide(); },afterShowForm: initEditCostCenter, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true,beforeShowForm: function(form) { $('#tr_level', form).show(); },afterShowForm: resetCostCenter, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
	
	$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
});
</script>
<table id="list"></table>
<div id="pager"></div>