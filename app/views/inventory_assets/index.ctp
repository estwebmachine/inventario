<script type="text/javascript">
flag_ingresar = true;
    $(document).ready(function(){
       $('#asset_cant').keyup(function() {
        this.value = this.value.replace(/[^0-9]/g,''); 
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
                        {name:'detail', index:'detail', search:false, editable:true},
                        {name:'serial', index:'serial', search:true,editable:true},
			{name:'original_price', index:'original_price', search:false,editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'price',index:'price',search:false,editable:false},
			{name:'status', index:'status',stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('InventoryAsset.status');?>"}},
                        {name:'is_depreciate', index:'is_depreciate', editable:true,edittype:'checkbox',editoptions: {value: "Sí:No"},stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('InventoryAsset.is_depreciate');?>"}},
                        {name:'situation', index:'situation', editable:true,search:false, hidden: false,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'life', index:'life',editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
                        {name:'residual_value', index:'residual_value',editable:true,formoptions: { elmprefix: "(*)"}, editrules: {required: true}},
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
		rowNum:10,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Mantenedor Bienes'); ?>',
		editurl: '<?php echo $html->url('/inventory_assets/indexedit') ?>',
		//height: 350,
		autowidth: true,
		subGrid: false,
		multiselect: false,
		onSelectRow: function(row_id, status) {
		   updateSelRows([row_id], status);
		   
			row_data = $(this).getRowData(row_id);
			if(row_data['status'] == 'Dado de Baja') {
				//esconder boton asignar
				$('#allocate_button').hide();
			} else {
				//mostrar boton asignar
				$('#allocate_button').show();
			}
		},
		onSelectAll: function(aRowids, status) {
			updateSelRows(aRowids, status);
		},
		gridComplete: function() {
			//selecciono filas
			$(this).selectRows();
		}
	});

	$("#list").jqGrid('navGrid', '#pager', {edit:true, add:false, del:false, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
	
	$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});

	//boton asignar
        <?php if($session->read('Auth.User.role')!=3): ?>
	jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"Ingresar Bienes", buttonicon:"ui-icon-plus",
			onClickButton: function() {
                            $("#dialog-enter").dialog('open');
			},
		position: "first", title:"Asignar Bien", cursor: "pointer", id: 'enter_button'}
	);
        <?php endif; ?>
        
        //modal mensaje
	$("#dialog-message").dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Ok: function() {
				$(this).dialog('close');
			}
		}
	});
        
        $("#dialog-enter").dialog({
		autoOpen: false,
		height: 600,
		width: 400,
		modal: true,
		buttons: {
			'Ingresar': function(){
				var asset_id = $('#assets-set').jqGrid('getGridParam', 'selrow');
                                var amount = $('#asset_cant').val();
                                var validate = true;
                                var msg = "";
                                if(asset_id == null){
                                    validate = false;
                                    msg="Seleccione un Bien de la lista";
                                }
                                if(amount == ''){
                                    validate = false;
                                    msg="Ingrese Cantidad";
                                }
                                if(validate){
                                    //envio ajax
//                                    flag_ingresar = false;
                                    msg = "Espere, por favor !!!";
                                    $('#message-content').html(msg);
                                    $('#dialog-message').dialog('open');
                                    $.ajax({
                                            url: '<?php echo $html->url('/inventory_assets/migration_ajax/') ?>',
                                            type: 'POST',
                                            dataType: 'json',
                                            async:false,
                                            data: ({ asset_id: asset_id, amount: amount}),
                                            success: function(response){
//                                                    flag_ingresar = true;
                                                    if(response.result == "success") {
                                                            //cierro dialogo							
                                                            $("#dialog-enter").dialog('close');

                                                            //actualizo grid		
                                                            $('#list').trigger('reloadGrid');
                                                            $('#message-content').html('');
                                                            $('#dialog-message').dialog('close');
                                                    }
                                                    else if(response.result == "failure") {
                                                            $('#message-content').html(response.msg);
                                                            $('#dialog-message').dialog('open');
                                                            
                                                    }						
                                            }
                                    });
                                }else{
                                    $('#message-content').html(msg);
                                    $('#dialog-message').dialog('open');
                                }
			},
			'Cancelar': function(){
				$(this).dialog('close');
			}
		}
	});
        
        $("#assets-set").jqGrid({
		url:'<?php echo $html->url('/assets/search'); ?>',
		datatype: "xml",
		height: 270,
		colNames:['Id','Nombre', 'Código'],
		colModel:[
			{name:'id',index:'id', width:30, hidden:true},
			{name:'name',index:'name', width:350},
			{name:'code',index:'code', width:80, hidden:true}
		],
		rowNum:10,
		//rowList:[10,20,30],
		mtype: "GET",
		pager: $('#assets-pagerb'),
		sortname: 'name',
		viewrecords: true,
		sortorder: "asc",
		multiselect: false
	});
    });
    
    //assets
var assetTimeoutHnd;
var assetFlAuto = false;
function assetGridReload(){
	var nm_mask = $("#asset_name_search").val();
	var inventory_mask = $("#is_inventory").val();
	//var wh_mask = $("#warehouse_search").val();
	$("#assets-set").jqGrid('setGridParam',{url: "<?php echo $html->url('/assets/search') . "/?nm_mask="; ?>"+nm_mask+"&inventory_mask="+inventory_mask,page:1}).trigger("reloadGrid");
}

function assetDoSearch(ev){
	if(!assetFlAuto)
		return;
//	var elem = ev.target||ev.srcElement;
	if(assetTimeoutHnd)
		clearTimeout(assetTimeoutHnd)
	assetTimeoutHnd = setTimeout(assetGridReload,500)
}

function assetEnableAutosubmit(state){
	 assetFlAuto = state;
	$("#assetSubmitButton").attr("disabled",state);
}
</script>

    <table id="list"></table>
    <div id="pager"></div>
        
    <div id="dialog-enter" title="Ingreso de Bienes">
        <table>
            <tbody>
                <tr>
                    <td width="30%">Cantidad</td>
                    <td width="70%"><input type="text" id="asset_cant"/></td>
                </tr>
            </tbody>
        </table>
        <fieldset>
            <legend>Seleccionar Bien</legend>
        <div class="h">Buscar Por:</div>
	<div>		
		<input type="checkbox" id="asset_autosearch" onclick="assetEnableAutosubmit(this.checked)" /> Búsqueda automática <br />
	</div>
	<div>
		Nombre<br />
		<input type="text" id="asset_name_search" onkeydown="assetDoSearch(arguments[0]||event)" />
		<button onclick="assetGridReload()" id="assetSubmitButton" style="margin-left:30px;">Buscar</button>
	</div>

	<br />
	<table id="assets-set"></table>
	<div id="assets-pagerb"></div>
        </fieldset>
    </div>
    <div id="dialog-message" title="Mensaje">
	<p>
		<span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
		<div id="message-content"></div>
	</p>
</div>