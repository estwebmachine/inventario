<script type="text/javascript">
$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/transactions/indextable'); ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Fecha', 'Id Orden de Compra', 'N° Orden de Compra', /*'Rut Proveedor',*/'Usuario', 'Subtítulos','Tipo de Alta','Tipo de Dcto.', 'N° Dcto.', 'Observación','Fecha Dcto.', 'Estado', 'Creada', 'Modificada','PDF Dcto.'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true, search:false},
			{name:'date', index:'date', editable:true, formoptions:{rowpos:2, elmprefix:"(*)"}, editrules:{required:true}, editoptions:{size: 10, maxlengh: 10, dataInit: function(element) {
				$(element).datepicker({dateFormat: 'dd/mm/yy'})
				}},
				searchoptions:{dataInit:
					function(element){
						$(element).datepicker({
							dateFormat: 'dd/mm/yy',
							onSelect: function(dateText, inst){
								var sgrid = $("#list")[0];
								sgrid.triggerToolbar();
							}
						});
					}}
				},
			{name:'purchase_order_id', index:'purchase_order_id', hidden:true},
			{name:'PurchaseOrder.order_number', index:'PurchaseOrder.order_number', search:true},
//                        {name:'Provider.rut', index:'Provider.rut', search:false},
			{name:'user_id', index:'user_id', search:true},
                        {name:'subtitles', index:'subtitles', search:true},        
                        {name:'type', index:'type', search:true,stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('Transaction.type');?>"}},
			{name:'document_type', index:'document_type', stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('PurchaseOrder.document_types');?>"}, search:true},
			{name:'document_number', index:'document_number', search:true},
                        {name:'observation', index:'observation',search:false,editable:false},
			{name:'document_date', index:'document_date', editable:true, edittype:'text', formoptions:{rowpos:6, elmprefix:"(*)"}, editrules:{required:true}, editoptions:{size: 10, maxlengh: 10, dataInit: function(element) {
				$(element).datepicker({dateFormat: 'dd/mm/yy'})
				}},
				searchoptions:{dataInit:
					function(element){
						$(element).datepicker({
							dateFormat: 'dd/mm/yy',
							onSelect: function(dateText, inst){
								var sgrid = $("#list")[0];
								sgrid.triggerToolbar();
							}
						});
					}}
				},
			{name:'status', index:'status', stype:'select', searchoptions:{value:" : ;<?php echo $jqgrid->selectOpt('Transaction.status');?>"}},
			{name:'created', index:'created', hidden:true, search:false},
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
			}},
                        {name:'pdf', index:'pdf',hidden:false, editable:false, search:false,align:'center'},
                        
		],
		pager: '#pager',
		rowNum:10,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Alta Bienes Inventario'); ?>',
		editurl: '<?php echo $html->url('/transactions/indexedit') ?>',
		//height: 350,
		autowidth: true,
		subGrid: true,
		onSelectRow: function(row_id) {
			row_data = $("#list").getRowData(row_id);
			if(row_data['status'] == 'Enviada') {
				//esconder boton borrar y editar
				$('#del_list').hide();
				$('#edit_list').hide();
			} else {
				//mostrar boton borrar y editar
				$('#del_list').show();
				$('#edit_list').show();
			}
		},
		gridComplete: function() {			
			subgrid = $('#expandSubGrid').val();
			if(subgrid != '') $('#list').expandSubGridRow(subgrid);
                        var dataIds = $("#list").getDataIDs();
                        $.each(dataIds, function(index, value){
                            var url = $("#list").getRowData(value);
                            if(url['pdf'] != '')
                                $("#list").setCell(value, 'pdf', '<a href="' + wroot + 'pdf/' +url['pdf'] + '" target="_blank"><img src="'+wroot+'img/icons/pdf.png" /></a>','','',true);
                        });
		},
		subGridRowExpanded: function(subgrid_id, row_id) {
			// we pass two parameters
			// subgrid_id is a id of the div tag created whitin a table data
			// the id of this elemenet is a combination of the "sg_" + id of the row
			// the row_id is the id of the row
			// If we wan to pass additinal parameters to the url we can use
			// a method getRowData(row_id) - which returns associative array in type name-value
			// here we can easy construct the flowing
			var subgrid_table_id, pager_id;
			subgrid_table_id = subgrid_id+"_t";
			pager_id = "p_"+subgrid_table_id;
			$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
			jQuery("#"+subgrid_table_id).jqGrid({
				url:'<?php echo $html->url('/transaction_details/indextable/?transaction_id=') ?>' + row_id,
				datatype: "xml",
				colNames: ['Transacción', 'Id', 'Bien', 'Nombre desde OC', 'Usuario', 'Cantidad', 'Restan', 'Total', 'Precio', 'Valor','Creado','Modificado','Dcto. Proveedor'],
				colModel: [
					{name:'transaction_id', index:'transaction_id', editable:false, hidden:true},
					{name:'id', index:'id', editable:false, hidden:true},
					{name:'asset_id', index:'asset_id', editable:false,classes: 'ui-state-hover'},
					{name:'purchase_order_detail_name', index:'purchase_order_detail_name', editable:false},
					{name:'user_id', index:'user_id', editable:false, hidden:true},
					{name:'amount', index:'amount', align:'center', editable:true, edittype:'text', classes: 'ui-state-hover'},
					{name:'amount_trans', index:'amount_trans', align:'center', editable:false},
					{name:'total', index:'total', align:'center', editable:false},
					{name:'price', index:'price', editable:false, hidden:true, edittype:'text'},
					{name:'value', index:'value', editable:false, hidden:true},
					{name:'created', index:'created', hidden:true},
					{name:'modified', index:'modified', hidden:true},
                                        {name:'dcto_proveedor', index:'dcto_proveedor', align:'center', editable:false, classes: 'ui-state-hover'}
				],
				rowNum:20,
				pager: pager_id,
				sortname: 'id',
				sortorder: "asc",
				autowidth: true,
				cellEdit: true,
				cellsubmit: 'remote',
				afterSaveCell: function (id,name,val,iRow,iCol){
					//transid = $("#trans_id").attr('value');
					$('#list_' + row_id + '_t').trigger('reloadGrid');
				},
				onCellSelect: function(rowid, iCol, cellcontent, e) {
					row_data = $("#list").getRowData(row_id);
                                        row_data2 = $('#list_' + row_id + '_t').getRowData(rowid);
					//si se trata de la columna asset_id y la transaccion no ha sido enviada
					if(iCol == 2 && row_data['status'] == 'No Enviada' && row_data2['purchase_order_detail_name'] != row_data2['asset_id']) {
						//paso id del detalle
						$('#transaction_detail_id').val(rowid);
						//paso id padre
						$('#transaction_id').val(row_id);
						//recargar grid
						assetGridReload();
						$('#dialog-asset-select').dialog('open');
					}
//                                        if(iCol == 12 && row_data['status'] == 'No Enviada') {
//						//paso id padre
//						$('#trans-det-id-csv').val(rowid);
//						//recargar grid
//						$('#dialog-csv').dialog('open');
//                                                $('#list_' + row_id + '_t').trigger('reloadGrid');
//					}
				},
                                gridComplete: function(){
                                    var dataIds = $('#list_' + row_id + '_t').getDataIDs();
                                    var row_data3 = $("#list").getRowData(row_id);
                                    $.each(dataIds, function(index, value){
                                        var url = $('#list_' + row_id + '_t').getRowData(value);
                                        $('#trans-det-id-csv').val(value);
                                        $('#trans-id-csv').val(row_id);
                                        if(url['dcto_proveedor'] != ''){
                                            if(row_data3['status'] == 'No Enviada'){
                                                $('#list_' + row_id + '_t').setCell(value, 'dcto_proveedor', '<a href="' + wroot + 'csv/' +url['dcto_proveedor'] + '" target="_blank"><img src="'+wroot+'img/icons/csv_icon.gif" /></a>-<a id="delete" href="#">Eliminar</a>','','',true);
                                            }else{
                                                $('#list_' + row_id + '_t').setCell(value, 'dcto_proveedor', '<a href="' + wroot + 'csv/' +url['dcto_proveedor'] + '" target="_blank"><img src="'+wroot+'img/icons/csv_icon.gif" /></a>','','',true);
                                            }
                                        }else{
                                            if(row_data3['status'] == 'No Enviada'){
                                                $('#list_' + row_id + '_t').setCell(value,'dcto_proveedor','<a id="add" href="#">Agregar</a>','','',true);
                                            }
                                        }
                                    });
    
                                },
				cellurl: '<?php echo $html->url('/transaction_details/indexedit/') ?>' + row_id + '/',
				editurl: '<?php echo $html->url('/transaction_details/indexedit/') ?>' + row_id + '/'
			});
			jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false, add:false, del:true, search:false},
				{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
				{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"},
				{width:350, height:'auto', reloadAfterSubmit:false}, // del options
				{}
			);
			//boton enviar
			jQuery("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,
				{caption:"Enviar", buttonicon:"ui-icon-check",
					onClickButton: function() {
										//enviar transaccion										
										$.ajax({
											url: '<?php echo $html->url('/transactions/close/'); ?>' + row_id,
											type: 'POST',
											dataType: 'json',
											success: function(response) {
												if(response.result == "success") {
													//sacar botones
                                                                                                        $('#edditCC' + row_id).remove();
													$('#sendButton' + row_id).remove();
													$('#addAssets' + row_id).remove();
													$('#add_list_' + row_id + '_t').remove();
													$('#edit_list_' + row_id + '_t').remove();
													$('#del_list_' + row_id + '_t').remove();
													//evitar inline edit
													$("#"+subgrid_table_id).setGridParam({cellEdit:false});
													//cambiar como muestra el estado
													$("#list").setRowData(row_id, {status: 'Enviada'});
													//esconder boton borrar y editar de grid padre
													$('#del_list').hide();
													$('#edit_list').hide();
												}
												else if(response.result == "failure") {
													$('#message-content').html(response.message);
													$('#dialog-message').dialog('open');
												}else if(response.result == 'file_error'){
                                                                                                    $('#message-content').html(response.message);
                                                                                                    $('#dialog-message').dialog('open');
                                                                                                    $("#"+subgrid_table_id).setSelection(response.row);
                                                                                                }
											}
										});
									},
				position: "last", title:"", cursor: "pointer", id: 'sendButton' + row_id});
			
			//boton agregar bienes desde orden de compra
			jQuery("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,
				{caption:"Agregar Items OC", buttonicon:"ui-icon-plus",
					onClickButton: function() {
										//paso numero de orden padre
										row_data = $("#list").getRowData(row_id);
										$('#order_search').val(row_data['purchase_order_id']);
										//paso id padre
										$('#trans_id').val(row_id);
//                                                                                $('#resp-center').load('<?php echo $html->url('/ajax/selectopt/ResponsabilityCenter?firstopt=Seleccione%20Centro%20de%20Costo option'); ?>');
										//recargar grid
										gridReload();
										$('#dialog-form').dialog('open');
									},
				position: "last", title:"", cursor: "pointer", id: 'addAssets' + row_id});
			
//                        //boton editar centro de costo
//			jQuery("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,
//				{caption:"Editar Centro de Costo", buttonicon:"ui-icon-pencil",
//					onClickButton: function() {
//										//obntengo id transaction detail 
//                                                                                $('#trans_id_edit').val(row_id);
//										tranDet_id = $("#"+subgrid_table_id).getGridParam('selrow');
//                                                                                var validate = false;
//                                                                                if(tranDet_id != null){
//                                                                                    validate = true;
//                                                                                }
//                                                                                if(validate){
//                                                                                //paso id de trans detail
//                                                                                    $('#trans_detail_id').val(tranDet_id);
//                                                                                    //cargo la lista de centros de costo
////                                                                                    $('#resp-center-edit').load('<?php echo $html->url('/ajax/selectopt/ResponsabilityCenter?firstopt=Seleccione%20Centro%20de%20Costo option'); ?>');
//                                                                                    $('#dialog-edit-cc').dialog('open');
//                                                                                }else{
//                                                                                    $('#message-content').html('Seleccione celda de centro de costo');
//                                                                                    $('#dialog-message').dialog('open');
//                                                                                }
//									},
//				position: "last", title:"", cursor: "pointer", id: 'edditCC' + row_id});
			//sacar botones al cargar si esta enviada la transaccion
			row_data = $("#list").getRowData(row_id);
			if(row_data['status'] == 'Enviada') {
				//evitar inline edit
				$("#"+subgrid_table_id).setGridParam({cellEdit:false});
                                $('#edditCC' + row_id).remove();
				$('#sendButton' + row_id).remove();
				$('#addAssets' + row_id).remove();
				$('#add_list_' + row_id + '_t').remove();
				$('#edit_list_' + row_id + '_t').remove();
				$('#del_list_' + row_id + '_t').remove();
			}
		},
		subGridRowColapsed: function(subgrid_id, row_id) {
			// this function is called before removing the data
			//var subgrid_table_id;
			//subgrid_table_id = subgrid_id+"_t";
			//jQuery("#"+subgrid_table_id).remove();
		}
	});

	$("#list").jqGrid('navGrid', '#pager', {edit:false, add:false, del:true, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
	
	$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});

	//boton editar transaccion
	jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"", buttonicon:"ui-icon-pencil",
			onClickButton: function() {
				//indico que se trata de edicion
				$('#trans-oper').val('edit');
				$('#dialog-trans').dialog( "option", "title", 'Editar Transacción' );
				//verifico que haya una fila seleccionada
				rowsel= $("#list").jqGrid('getGridParam','selrow');
				if(rowsel == null) {
					//mensaje error
					$('#message-content').html('Seleccione una Transacción.');
					$('#dialog-message').dialog('open');
				} else {
					// paso los datos de la fila seleccionada al formulario
					row_data = $("#list").getRowData(rowsel);
					$('#trans-id').val(rowsel);
					$('#trans-date').val(row_data['date']);
					//seleccionar tipo documento
					$('#document-type').setSelected(row_data['document_type']);
					$('#document-number').val(row_data['document_number']);
                                        $('#trans-observation').val(row_data['observation']);
                                        $('#trans-type').setSelected(row_data['type']);
                                        $('#trans-subtitles').setSelected(row_data['subtitles']);
					$('#document-date').val(row_data['document_date']);
					$('#num_search_ord').val(row_data['PurchaseOrder.order_number']);
					
					//presiono buscar
					gridReloadord();
					
					$('#dialog-trans').dialog('open');
				}
			},
		position: "first", title:"Editar Recepción", cursor: "pointer", id: 'edit_list'}
	);
	
	//boton agregar transaccion
	jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"", buttonicon:"ui-icon-plus",
			onClickButton: function() {
				//indico que se trata de agregar
				$('#trans-oper').val('add');
				$('#dialog-trans').dialog( "option", "title", 'Agregar Transacción' );
//                                $('#resp-center').load('<?php echo $html->url('/ajax/selectopt/ResponsabilityCenter?firstopt=Seleccione%20Centro%20de%20Costo option'); ?>');
				//reseteo campos
				$('#trans-id').val('');
				$('#trans-date').val('');
				$('#document-type').val('');
                                $('#trans-type').val('');
                                $('#trans-subtitles').val('');
				$('#document-number').val('');
				$('#document-date').val('');
                                $('#trans-observation').val('');
				$('#num_search_ord').val('');
                                $('#DocumentPdf').val('');
				//presiono buscar
				gridReloadord();
				
				$('#dialog-trans').dialog('open');
			},
		position: "first", title:"Agregar Recepción", cursor: "pointer", id: 'add_list'}
	);
        
        $('#add').live('click', function(){
            var tran = $('#trans-id-csv').val();
            $('#dialog-csv').dialog('open');
            $('#list_' + tran + '_t').trigger('reloadGrid');
        });
        
        $('#delete').live('click', function(){
            var tran_det = $('#trans-det-id-csv').val();
            var tran_id = $('#trans-id-csv').val();
            $.ajax({
                url:'<?php echo $html->url('/transaction_details/deleteDctoCsv/') ?>'+tran_det,
                type: 'POST',
                async:true,
                dataType: 'json',
                success: function(response){
                    if(response.result == "success") {							
                        $("#dialog-csv").dialog('close');
                        $('#list_' + tran_id + '_t').trigger('reloadGrid');
                    }
                    else if(response.result == "failure") {
                        $('#message-content').html(response.message);
                        $('#dialog-message').dialog('open');
                    }						
                }
            });
        });

	// MODALS
	// modal agregar y editar transaccion
	$('#trans-date').datepicker({dateFormat: 'dd/mm/yy'});
	$('#document-date').datepicker({dateFormat: 'dd/mm/yy'});
		
	$("#dialog-trans").dialog({
		autoOpen: false,
		height: 550,
		width: 520,
		modal: true,
		buttons: {
			'Guardar': function(){
				transdate = $('#trans-date').val();
				documenttype = $('#document-type').val();
				documentnumber = $('#document-number').val();
				documentdate = $('#document-date').val();
				ordselected = $("#orders").jqGrid('getGridParam','selrow');
				transid = $('#trans-id').val();
				transoper = $('#trans-oper').val();
                                subtitles = $('#trans-subtitles').val();
                                type = $('#trans-type').val();
                                transobservation = $('#trans-observation').val();
                                //responsabilitycenter = $('#resp-center').val();
                                var options = {
                                    url: '<?php echo $html->url('/transactions/indexedit/') ?>',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: ({ observation:transobservation,subtitles:subtitles,type:type,oper: transoper,id: transid, date: transdate, document_type: documenttype, document_number: documentnumber, document_date: documentdate, purchase_order_id: ordselected}),
                                    success: function(response){
                                        if(response.result == "success") {							
                                            $("#dialog-trans").dialog('close');
                                            //expando subgrid si estoy agregando
                                            if(response.oper == 'add')$('#expandSubGrid').val(response.id);
                                            if(response.oper == 'edit')$('#expandSubGrid').val('');
                                            //actualizo grid							
                                            $('#list').trigger('reloadGrid');							
                                            gridReloadord();
                                        }
                                        else if(response.result == "failure") {
                                            $('#message-content').html(response.message);
                                            $('#dialog-message').dialog('open');
                                        }						
                                    }
                                };
                                
                                $("#t_form").ajaxSubmit(options);
                                
								
			},
			'Cancelar': function(){
				$(this).dialog('close');
				gridReload();
			}
		}
	});
        //agrega archivo del proveedor para cargar informacion de los bienes
        $("#dialog-csv").dialog({
		autoOpen: false,
		height: 200,
		width: 520,
		modal: true,
		buttons: {
			'Guardar': function(){
				var transidcsv = $('#trans-det-id-csv').val();
                                var tran = $('#trans-id-csv').val();
                                var options = {
                                    url: '<?php echo $html->url('/transaction_details/addDctoCsv/') ?>',
                                    type: 'POST',
                                    async:false,
                                    dataType: 'json',
                                    data: ({ id: transidcsv}),
                                    success: function(response){
                                        if(response.result == "success") {							
                                            $("#dialog-csv").dialog('close');
                                            $('#list_' + tran + '_t').trigger('reloadGrid');
                                        }
                                        else if(response.result == "failure") {
                                            $('#message-content').html(response.message);
                                            $('#dialog-message').dialog('open');
                                        }						
                                    }
                                };
                                
                                $("#csv_form").ajaxSubmit(options);
                                
								
			},
			'Cancelar': function(){
				$(this).dialog('close');
				gridReload();
			}
		}
	});
	
	// modal agregar bienes desde OC
	$("#dialog-form").dialog({
		autoOpen: false,
		height: 550,
		width: 560,
		modal: true,
		buttons: {
			'Agregar': function(){
				idsselected = $("#bigset").jqGrid('getGridParam', 'selarrrow');
				transid = $("#trans_id").attr('value');
//                                responsabilitycenter = $('#resp-center').val();
				//envio ajax
				$.post('<?php echo $html->url('/transactions/addasset/') ?>',
					{ ids: idsselected, tid: transid},
					function(msg){
						if(msg.result == 'success') {
							//cierro dialogo
							$("#dialog-form").dialog('close');
							//actualizo grid
							transid = $("#trans_id").val();
							$('#list_' + transid + '_t').trigger('reloadGrid');
							gridReload();
						}else{
                                                    $('#message-content').html(msg.message);
                                                    $('#dialog-message').dialog('open');
                                                }
					},'json'
				);
				
			},
			'Cancelar': function(){
				$(this).dialog('close');
				gridReload();
			}
		}
	});
	
	// modal seleccionar bien para detalle transacción
	$("#dialog-asset-select").dialog({
            autoOpen: false,
            height: 550,
            width: 400,
            modal: true,
            buttons: {
                'Seleccionar': function(){
                    idselected = $("#assets-set").jqGrid('getGridParam', 'selrow');
                    transactiondetid = $("#transaction_detail_id").attr('value');
                    //envio ajax
                    $.post('<?php echo $html->url('/transaction_details/addasset/') ?>',
                        { id: idselected, transdetid: transactiondetid },
                        function(msg){
                                if(msg == 'success') {
                                    //cierro dialogo
                                    $("#dialog-asset-select").dialog('close');
                                    //actualizo grid
                                    transactionid = $("#transaction_id").attr('value');
                                    $('#list_' + transactionid + '_t').trigger('reloadGrid');
                                    assetGridReload();
                                }
                        }
                    );
                },
                'Cancelar': function(){
                        $(this).dialog('close');
                        gridReload();
                }
            }
	});
//	//modal editar centro de costo
//        $("#dialog-edit-cc").dialog({
//            autoOpen: false,
//            height: 160,
//            width: 530,
//            modal: true,
//            buttons: {
//                'Aceptar': function(){
//                    var transactiondetid2 = $("#trans_detail_id").val();
////                    var costcenter = $('#resp-center-edit').val();
//                    var trans_id_edit = $('#trans_id_edit').val();
//                    //envio ajax
//                    $.post('<?php echo $html->url('/transaction_details/editasset/') ?>',
//                        {transdetid: transactiondetid2, costcenter:costcenter },
//                        function(output){
//                                if(output.result == 'success') {
//                                    //cierro dialogo
//                                    $("#dialog-edit-cc").dialog('close');
//                                    $("#list_"+trans_id_edit + '_t').trigger('reloadGrid');
//                                }else{
//                                    $('#message-content').html(output.message);
//                                    $('#dialog-message').dialog('open');
//                                }
//                        },'json'
//                    );
//                },
//                'Cancelar': function(){
//                        $("#dialog-edit-cc").dialog('close');
//                }
//            }
//	});
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

});
</script>
<table id="list"></table>
<div id="pager"></div>

<script type="text/javascript">
//assets
var timeoutHnd;
var flAuto = false;

function gridReload(){
	var orderid_mask = $("#order_search").val();
	var transid_mask = $("#trans_id").val();
	var nm_mask = $("#name_search").val();
	$("#bigset").jqGrid('setGridParam',{url: "<?php echo $html->url('/purchase_order_details/search') . "/?nm_mask="; ?>"+nm_mask+"&orderid_mask="+orderid_mask+"&transid_mask="+transid_mask,page:1}).trigger("reloadGrid");
}

function doSearch(ev){
	if(!flAuto)
		return;
//	var elem = ev.target||ev.srcElement;
	if(timeoutHnd)
		clearTimeout(timeoutHnd)
	timeoutHnd = setTimeout(gridReload,500)
}

function enableAutosubmit(state){
	flAuto = state;
	$("#submitButton").attr("disabled",state);
}

//assets
var assetTimeoutHnd;
var assetFlAuto = false;
function assetGridReload(){
	var nm_mask = $("#asset_name_search").val();
	var cd_mask = $("#asset_code_search").val();
	var inventory_mask = $("#is_inventory").val();
	//var wh_mask = $("#warehouse_search").val();
	$("#assets-set").jqGrid('setGridParam',{url: "<?php echo $html->url('/assets/search') . "/?nm_mask="; ?>"+nm_mask+"&cd_mask="+cd_mask+"&inventory_mask="+inventory_mask,page:1}).trigger("reloadGrid");
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

//purchase_orders
var timeoutHndord;
var flAutoord = false;

function gridReloadord(){
	var num_mask = $("#num_search_ord").val();
	$("#orders").jqGrid('setGridParam',{url: "<?php echo $html->url('/purchase_orders/search') . "/?num_mask="; ?>"+num_mask,page:1}).trigger("reloadGrid");
}

function doSearchord(ev){
	if(!flAutoord)
		return;
//	var elem = ev.target||ev.srcElement;
	if(timeoutHndord)
		clearTimeout(timeoutHndord)
	timeoutHndord = setTimeout(gridReloadord,500)
}

function enableAutosubmitord(state){
	flAutoord = state;
	$("#submitButtonord").attr("disabled",state);
}

$(document).ready(function(){
	//grid de productos (purchase_order_details)
	$("#bigset").jqGrid({
		url:'<?php echo $html->url('/purchase_order_details/search'); ?>',
		datatype: "xml",
		height: 270,
		colNames:['Id','Nombre', 'Restan', 'Total'],
		colModel:[
			{name:'id',index:'id', width:30, hidden:true},
			{name:'name',index:'name', width:330},
			{name:'amount_trans',index:'amount_trans', width:60},
			{name:'amount',index:'amount', width:40}
		],
		rowNum:50,
		//rowList:[10,20,30],
		mtype: "GET",
		pager: $('#pagerb'),
		sortname: 'id',
		viewrecords: true,
		sortorder: "asc",
		multiselect: true
	});
	
	//grid de productos para selección en detalle transacción
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
		rowNum:50,
		//rowList:[10,20,30],
		mtype: "GET",
		pager: $('#assets-pagerb'),
		sortname: 'name',
		viewrecords: true,
		sortorder: "asc",
		multiselect: false
	});

	//grid de ordenes de compra
	$("#orders").jqGrid({
		url:'<?php echo $html->url('/purchase_orders/search') ?>',
		datatype: "xml",
		height: 200,
		colNames:['Id','Número', 'Bodega'],
		colModel:[
			{name:'id',index:'id', width:30, hidden:true},
			{name:'order_number',index:'order_number', width:200},
			{name:'warehouse_id',index:'warehouse_id', width:150, hidden:true}
		],
		rowNum:8,
		//rowList:[10,20,30],
		mtype: "GET",
		pager: $('#orders-pager'),
		sortname: 'id',
		viewrecords: true,
		sortorder: "desc",
		loadComplete: function() {
			//si es edicion
			if($('#trans-oper').val() == 'edit') {
				//busco la id primera fila
				selid = $('#orders tr:first').attr('id');
				//la selecciono
				$(this).setSelection(selid);
			}
		}
	});

});
</script>

<div id="dialog-trans" title="Agregar Transacción">
	<input type="hidden" id="trans-oper" value="" />
	<input type="hidden" id="trans-id" value="" />
	<table>
		<tr>
			<td width="30%">Fecha</td>
			<td width="70%"><input type="text" id="trans-date" readonly="readonly" /></td>
		</tr>
		
		<tr>
			<td width="30%">Tipo Dcto.</td>
			<td width="70%">
				<select id="document-type">
					<option value="">Seleccione Tipo</option>
					<?php foreach(Configure::read('PurchaseOrder.document_types') as $k => $v) echo '<option value=' . $k . '>' . $v . '</option>';?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%">N° Dcto.</td>
			<td width="70%"><input type="text" id="document-number" /></td>
		</tr>
		<tr>
			<td width="30%">Fecha Dcto.</td>
			<td width="70%"><input type="text" id="document-date" readonly="readonly" /></td>
		</tr>
                <tr>
			<td width="30%">Tipo de Alta</td>
			<td width="70%">
				<select id="trans-type">
					<option value="">Seleccione Tipo</option>
					<?php foreach(Configure::read('Transaction.type') as $k => $v) echo '<option value=' . $k . '>' . $v . '</option>';?>
				</select>
			</td>
		</tr>
                <tr>
			<td width="30%">Subtitulos</td>
			<td width="70%">
				<select id="trans-subtitles">
					<option value="">Seleccione Tipo</option>
					<?php foreach(Configure::read('Subtitulos') as $k => $v) echo '<option value=' . $k . '>' . $k.' - '.$v . '</option>';?>
				</select>
			</td>
		</tr>
                <tr>
			<td width="30%">Observación</td>
			<td width="70%"><textarea cols="35" rows="5" type="text" id="trans-observation"></textarea></td>
		</tr>
                <tr>
                
                    <td><?php echo $form->label('ArchivoPDF','Archivo PDF',array('for'=>'DocumentPdf')); ?> </td>
                    <td><form id="t_form" type="multipart/form-data"><?php echo $form->file('Document.pdf'); ?> </form></td>
                
                </tr>
	</table>

	<div>		
		Número Orden:<br />
		<input type="text" id="num_search_ord" onkeydown="doSearchord(arguments[0]||event)" />
		<button onclick="gridReloadord()" id="submitButtonord" style="margin-left:30px;">Buscar</button><br />
		<input type="checkbox" id="autosearch_ord" onclick="enableAutosubmitord(this.checked)" /> Búsqueda automática
	</div>

	<br />
	<table id="orders"></table>
	<div id="orders-pager"></div>
</div>

<div id="dialog-csv" title="Agregar Archivo Proveedor">
	<input type="hidden" id="trans-det-id-csv" value="" />
        <input type="hidden" id="trans-id-csv" value="" />
	<table>
                <tr>
                
                    <td><?php echo $form->label('ArchivoCSV','Archivo CSV',array('for'=>'DocumentCsv')); ?> </td>
                    <td><form id="csv_form" type="multipart/form-data"><?php echo $form->file('Document.csv'); ?> </form></td>
                
                </tr>
	</table>
</div>

<div id="dialog-form" title="Agregar Items desde Orden de Compra">
	<input type="hidden" id="trans_id" value="" />
	<input type="hidden" id="order_search" value="" />
<!--        <table>
            <tr>
                <td width="30%">Centro de Costo</td>
                <td width="70%"><select id="resp-center"></select></td>
            </tr>
        </table>-->
	<div class="h">Buscar Por:</div>
	<div>
		Nombre<br />
		<input type="text" id="name_search" onkeydown="doSearch(arguments[0]||event)" />
		<button onclick="gridReload()" id="submitButton" style="margin-left:30px;">Buscar</button>
	</div>
        <div>		
		<input type="checkbox" id="asset_autosearch" onclick="enableAutosubmit(this.checked)" /> Búsqueda automática <br />
	</div>

	<br />
	<table id="bigset"></table>
	<div id="pagerb"></div>
</div>

<div id="dialog-asset-select" title="Seleccionar Bien para detalle transacción">
	<input type="hidden" id="transaction_id" value="" />
	<input type="hidden" id="transaction_detail_id" value="" />
	<div class="h">Buscar Por:</div>
	<div>		
		Código<br />
		<input type="text" id="asset_code_search" onkeydown="assetDoSearch(arguments[0]||event)" />
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
</div>

<!--<div id="dialog-edit-cc" title="Editar Centro de Costo">
	<input type="hidden" id="trans_detail_id" value="" />
        <input type="hidden" id="trans_id_edit" value="" />
        <table>
            <tr>
                <td width="40%">Centro de Costo</td>
                <td width="60%"><select id="resp-center-edit"></select></td>
            </tr>
        </table>
</div>-->

<div id="dialog-message" title="Mensaje">
	<p>
		<span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
		<div id="message-content"></div>
	</p>
</div>

<input type="hidden" id="expandSubGrid" value="" />