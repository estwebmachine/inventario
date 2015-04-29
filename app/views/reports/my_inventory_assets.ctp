<script type="text/javascript">
$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/inventory_assets/indextable') . '?action=my_inventory_assets'; ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Código', 'Descripción','Detalle', 'Serial','Precio Original', 'Precio Actual','Estado', 'Calidad','Vida útil','Valor residual','','', '','','','','','','Creado', 'Modificado'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true, search:false},
                        {name:'code', index:'code', search:true, hidden: false},
			{name:'Asset.name', index:'Asset.name', search:true, hidden: false, editable:false},
                        {name:'detail', index:'detail', search:false, editable:true},
                        {name:'serial', index:'serial', search:true,editable:true},
			{name:'original_price', index:'original_price', search:false,hidden:true,editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'price',index:'price',search:false,editable:false,hidden:true},
			{name:'status', index:'status', stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('InventoryAsset.status');?>"}},
                        {name:'situation', index:'situation', search:false, hidden: true},
                        {name:'life', index:'life',hidden:true,editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'residual_value', index:'residual_value',hidden:true,editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'names', index:'names', editable:false, hidden:true, search:false},
                        {name:'last_name', index:'last_name', editable:false, hidden:true, search:false},
                        {name:'s_last_name', index:'s_last_name', editable:false, hidden:true, search:false},
                        {name:'program_id', index:'program_id', search:true, hidden: true,sortable:false},
                        {name:'of', index:'of', editable:false, hidden:true, search:false},
                        {name:'floor', index:'id', editable:false, hidden:true, search:false},
                        {name:'add', index:'id', editable:false, hidden:true, search:false},
                        {name:'city', index:'id', editable:false, hidden:true, search:false},
                        {name:'reg', index:'id', editable:false, hidden:true, search:false},
			{name:'created', index:'created'},
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
		caption: '<?php __('Mis Bienes de Inventario'); ?>',
		editurl: '<?php echo $html->url('/inventory_assets/indexedit') ?>',
		//height: 350,
		autowidth: true,
//		subGrid: true,
		onSelectRow: function(row_id) {

		},
		gridComplete: function() {			
			subgrid = $('#expandSubGrid').val();
			if(subgrid != '') $('#list').expandSubGridRow(subgrid);
		},
//		subGridRowExpanded: function(subgrid_id, row_id) {
//			// we pass two parameters
//			// subgrid_id is a id of the div tag created whitin a table data
//			// the id of this elemenet is a combination of the "sg_" + id of the row
//			// the row_id is the id of the row
//			// If we wan to pass additinal parameters to the url we can use
//			// a method getRowData(row_id) - which returns associative array in type name-value
//			// here we can easy construct the flowing
//			var subgrid_table_id, pager_id;
//			subgrid_table_id = subgrid_id+"_t";
//			pager_id = "p_"+subgrid_table_id;
//			$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
//			jQuery("#"+subgrid_table_id).jqGrid({
//				url:'<?php echo $html->url('/inventory_asset_histories/indextable/?inventory_asset_id=') ?>' + row_id,
//				datatype: "xml",
//				colNames: ['Bien Inventario', 'Id','Tipo', 'Comentario', 'Creado','Modificado'],
//				colModel: [
//					{name:'inventory_asset_id', index:'inventory_asset_id', editable:false, hidden:true},
//					{name:'id', index:'id', editable:false, hidden:true},
//					{name:'type', index:'type', editable:false, hidden:false},
//					{name:'comment', index:'comment', editable:false, hidden:false},
//					{name:'created', index:'created', hidden:false},
//					{name:'modified', index:'modified', hidden:true}
//				],
//				rowNum:20,
//				pager: pager_id,
//				sortname: 'id',
//				sortorder: "asc",
//				autowidth: true,
//				cellEdit: false,
//				cellsubmit: 'remote',
//				afterSaveCell: function (id,name,val,iRow,iCol){
//
//				},
//				onCellSelect: function(rowid, iCol, cellcontent, e) {
//					
//				},
//				cellurl: '<?php echo $html->url('/inventory_asset_histories/indexedit/') ?>' + row_id + '/',
//				editurl: '<?php echo $html->url('/inventory_asset_histories/indexedit/') ?>' + row_id + '/'
//			});
//			jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false, add:false, del:false, search:false},
//				{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
//				{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"},
//				{width:350, height:'auto', reloadAfterSubmit:false}, // del options
//				{}
//			);
//
//		},
//		subGridRowColapsed: function(subgrid_id, row_id) {
//			// this function is called before removing the data
//			//var subgrid_table_id;
//			//subgrid_table_id = subgrid_id+"_t";
//			//jQuery("#"+subgrid_table_id).remove();
//		}
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
<table id="list"></table>
<div id="pager"></div>