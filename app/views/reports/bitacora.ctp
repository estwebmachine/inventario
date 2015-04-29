<script type="text/javascript">
    $(document).ready(function(){
        $('#desde').datepicker();
        $("#hasta").datepicker();
        $('#excel').button({ icons: {primary:'ui-icon-document'} });
        $('.formsend').click(function(){
            format = $(this).attr('id');
            $('#assets').val(selectedRows);
            $('#reportform').attr('target', '_self');
            $('#reportform').attr('action', webroot+'/reports/generate/bitacora/' + format).submit();
            return false;
	});
        
        $("#list").jqGrid({
		url:'<?php echo $html->url('/inventory_assets/indextable'); ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Código', 'Descripción','Detalle', 'Serial','Precio Original', 'Precio Actual','Estado', 'Activo','Calidad','Vida útil','Valor residual','','', '','','','','','','','Creado', 'Modificado'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true, search:false},
                        {name:'code', index:'code', search:true, hidden: false},
			{name:'Asset.name', index:'Asset.name', search:true, hidden: false, editable:false},
                        {name:'detail', index:'detail', hidden:true, search:false, editable:true},
                        {name:'serial', index:'serial', search:true,hidden:true},
			{name:'original_price', index:'original_price', hidden:true,search:false,editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'price',index:'price',search:false,editable:false,hidden:true},
			{name:'status', index:'status',stype:'select', hidden:true,searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('InventoryAsset.status');?>"}},
                        {name:'is_depreciate', index:'is_depreciate', hidden:true},
                        {name:'situation', index:'situation', editable:true,search:false, hidden: true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'life', index:'life',hidden:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'residual_value', index:'residual_value',hidden:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'names', index:'names', editable:false, hidden:true, search:false},
                        {name:'last_name', index:'last_name', editable:false, hidden:true, search:false},
                        {name:'s_last_name', index:'s_last_name', editable:false, hidden:true, search:false},
                        {name:'program_id', index:'program_id', search:true, hidden: true,sortable:false},
                        {name:'of', index:'of', editable:false, hidden:true, search:false},
                        {name:'floor', index:'id', editable:false, hidden:true, search:false},
                        {name:'add', index:'id', editable:false, hidden:true, search:false},
                        {name:'city', index:'id', editable:false, hidden:true, search:false},
                        {name:'reg', index:'id', editable:false, hidden:true, search:false},
			{name:'created', index:'created',hidden:true},
			{name:'modified', index:'modified', hidden:true,editable:false, search:true, searchoptions:{dataInit: 
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
		caption: '<?php __('Bienes'); ?>',
		editurl: '<?php echo $html->url('/inventory_assets/indexedit') ?>',
		//height: 350,
		autowidth: true,
		subGrid: false,
		multiselect: true,
		onSelectRow: function(row_id, status) {
		   updateSelRows([row_id], status);
		},
		onSelectAll: function(aRowids, status) {
			updateSelRows(aRowids, status);
		},
		gridComplete: function() {
			//selecciono filas
			$(this).selectRows();
		}
	});

	$("#list").jqGrid('navGrid', '#pager', {edit:false, add:false, del:false, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
        
        $("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
    });
</script>
<fieldset>
<form id="reportform" method="post">
    <input type="hidden" id="assets" name="assets" />
        <legend>Reporte de Alta/Baja</legend>
        <table>
            <tr>
                <td width="40%">Tipo</td>
                <td width="60%">
                    <select id="tipo" name="tipo_bitacora">
                        <option value="*">Todo</option>
                        <?php foreach (Configure::read('InventoryAssetHistory.type') as $index => $value): ?>
                        <option value="<?php echo $index; ?>"><?php echo $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Desde</td>
                <td><input type="text" id="desde" name="desde"/></td>
            </tr>
            <tr>
                <td>Hasta</td>
                <td><input type="text" id="hasta" name="hasta"/></td>
            </tr>
        </table>
</form>
<br>
<table id="list"></table>
<div id="pager"></div>
<br>
<a href="#" class="formsend" id="excel" target="_self">Generar Excel</a>
</fieldset>    