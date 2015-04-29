<script type="text/javascript">
$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/inventory_assets/indextable') . '?action=terminate'; ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Código', 'Descripción','Detalle', 'Serial', 'Precio original', 'Precio actual', 'Estado', '','Calidad','Vida útil','Valor residual', 'Nombres Responsable', 'Apellido P. Responsable', 'Apellido M. Responsable','Programa','Oficina','Piso', 'Dirección', 'Ciudad','Región', 'Creado', 'Modificado','Archivo PDF'],
		colModel :[
			{name:'id', index:'id', editable:false, hidden:true, search:false},
                        {name:'code', index:'code', search:true, width:250},
			{name:'Asset.name', index:'Asset.name', search:true, hidden: false},
                        {name:'detail', index:'detail', search:false, hidden: false},
			{name:'serial', index:'serial', search:true, hidden: false},
			{name:'original_price', index:'original_price', search:false, hidden: true},
			{name:'current_price', index:'current_price', search:false, hidden: true},
			{name:'status', index:'status', stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('InventoryAsset.status');?>"}},
                        {name:'is_depreciate', index:'is_depreciate', search:false, hidden: true},
                        {name:'situation', index:'situation', search:false, hidden: false},
                        {name:'life', index:'life', search:false, hidden: true},
			{name:'residual_value', index:'residual_value',hidden:true},
                        {name:'User.names', index:'User.names', search:true, hidden: false,sortable:false},
                        {name:'User.primary_last_name', index:'User.primary_last_name', search:true, hidden: false,sortable:false},
                        {name:'User.second_last_name', index:'User.second_last_name', search:true, hidden: false,sortable:false},
                        {name:'program_id', index:'program_id', search:true, hidden: <?php if($session->read('Auth.User.is_ses')==0)echo 'false';else echo 'true'; ?>,sortable:false,stype:'select',searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('Programas');?>"}},
			{name:'Office.number', index:'Office.number', search:true, hidden: false,sortable:false},
			{name:'Floor.number', index:'Floor.number', search:true, hidden: false,sortable:false},
			{name:'Address.name', index:'Address.name', search:true, hidden: false,sortable:false},
                        {name:'City.name', index:'City.name', search:true, hidden: false,sortable:false},
			{name:'Region.name', index:'Region.name', search:true, hidden: false,sortable:false},
			{name:'created', index:'created', editable:false,sortable:false, search:true, searchoptions:{dataInit: 
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
			{name:'modified', index:'modified', hidden:true, search:false,sortable:false},
                        {name:'pdf', index:'pdf',hidden:false, editable:false, search:false,align:'center'},
		],
		pager: '#pager',
		rowNum:10,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Baja Bienes Inventario'); ?>',
		editurl: '<?php echo $html->url('/inventory_assets/indexedit') ?>',
		//height: 350,
		autowidth: true,
		multiselect: true,
		onSelectRow: function(row_id, status) {
			updateSelRows([row_id], status);
			
			row_data = $("#list").getRowData(row_id);
			if(row_data['status'] == 'Dado de Baja') {
				//esconder boton dar de baja
				$('#dispose_button').hide();
			} else {
				//mostrar boton dar de baja
				$('#dispose_button').show();
			}
		},
		onSelectAll: function(aRowids, status) {
			updateSelRows(aRowids, status);
		},
		gridComplete: function() {
			//selecciono filas
			$(this).selectRows();
                        var dataIds = $("#list").getDataIDs();
                        $.each(dataIds, function(index, value){
                            var url = $("#list").getRowData(value);
                            if(url['pdf'] != '')
                                $("#list").setCell(value, 'pdf', '<a href="' + wroot + 'pdf/' +url['pdf'] + '" target="_blank"><img src="'+wroot+'img/icons/pdf.png" /></a>','','',true);
                        });
		}
	});

	$("#list").jqGrid('navGrid', '#pager', {edit:false, add:false, del:false, search:false}, //options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:350, height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
	
	$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : true});
	
	//boton dar de baja
	jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"Dar de Baja Bienes", buttonicon:"ui-icon-circle-arrow-s",
			onClickButton: function() {
				$('#dialog-disposal').dialog( "option", "title", 'Dar de Baja' );
				//verifico que haya al menos una fila seleccionada
				if(selectedRows.length == 0) {
					//mensaje error
					$('#message-content').html('Seleccione un Bien.');
					$('#dialog-message').dialog('open');
				} else {
					$('#dialog-disposal').dialog('open');
				}
			},
		position: "first", title:"Dar de Baja", cursor: "pointer", id: 'dispose_button'}
	);
	
	//MODALS
	//modal dar de baja
	$("#dialog-disposal").dialog({
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,
		buttons: {
			'Dar de Baja': function(){
				disptype = $('#disptype').val();
				dispcomment = $('#dispcomment').val();
				//envio ajax
				var options = {
					url: '<?php echo $html->url('/inventory_assets/dispose/') ?>',
					type: 'POST',
					dataType: 'json',
					data: ({ ids: selectedRows, type: disptype, comment: dispcomment}),
					success: function(response){
						if(response.result == "success") {
							//cierro dialogo							
							$("#dialog-disposal").dialog('close');
							//limpio filas seleccionadas
							selectedRows = [];
							//actualizo grid							
							$('#list').trigger('reloadGrid');
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
			}
		}
	});
	
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

<div id="dialog-disposal" title="Dar de Baja Bienes">
	<table>
		<tr>
			<td width="30%">Tipo Baja</td>
			<td width="70%">
				<select id="disptype">
					<option value="">Seleccione Tipo</option>
					<?php foreach(Configure::read('InventoryAssetDisposal.type') as $k => $v) echo '<option value="' . $k . '">' . $v . '</option>';?>
				</select>
			</td>
		</tr>
                <tr>
                
                    <td><?php echo $form->label('ArchivoPDF','Archivo PDF',array('for'=>'DocumentPdf')); ?> </td>
                    <td><form id="t_form" type="multipart/form-data"><?php echo $form->file('Document.pdf'); ?> </form></td>
                
                </tr>
		<tr>
			<td width="30%">Comentario</td>
			<td width="70%"><textarea id="dispcomment" rows="10" style="width: 100%;"></textarea></td>
		</tr>
	</table>
</div>

<div id="dialog-message" title="Mensaje">
	<p>
		<span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
		<div id="message-content"></div>
	</p>
</div>