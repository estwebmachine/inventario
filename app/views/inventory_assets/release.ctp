<script type="text/javascript">
$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/inventory_assets/indextable') . '?action=release'; ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id', 'Código', 'Descripción','Detalle', 'Serial', 'Precio original', 'Precio actual', 'Estado', '','Calidad','Vida útil','Valor residual', 'Nombres Responsable', 'Apellido P. Responsable', 'Apellido M. Responsable','Programa','Oficina','Piso', 'Dirección', 'Ciudad','Región', 'Creado', 'Modificado'],
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
			{name:'modified', index:'modified', hidden:true, search:false,sortable:false}
		],
		pager: '#pager',
		rowNum:10,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Asignación Bienes Inventario'); ?>',
		editurl: '<?php echo $html->url('/inventory_assets/indexedit') ?>',
		//height: 350,
		autowidth: true,
//		subGrid: true,
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
        //Generar excel
        jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"Exportar",
			onClickButton: function() {
				//verifico que haya al menos una fila seleccionada
				if(selectedRows.length == 0) {
				    $('#message-content').html('Seleccione algún bien');
                                    $('#dialog-message').dialog('open');
				} else {
				    exportExcel(selectedRows,$("#list"));
				}
			},
		position: "first", title:"Exportar a Excel", cursor: "pointer", id: 'export_button'}
	);
        //boton editar centro de costo
//        jQuery("#list").jqGrid('navButtonAdd',"#pager",
//                {caption:"Editar Centro de Costo", buttonicon:"ui-icon-pencil",
//                        onClickButton: function() {
//                            if(selectedRows.length == 0) {
//					//mensaje error
//					$('#message-content').html('Seleccione un Bien.');
//					$('#dialog-message').dialog('open');
//				} else {
//                                    $('#resp-center-edit').load('<?php echo $html->url('/ajax/selectopt/ResponsabilityCenter?firstopt=Seleccione%20Centro%20de%20Costo option'); ?>');
//                                    $("#dialog-edit-cc").dialog('open');
//				}
//                },
//                position: "first", title:"", cursor: "pointer", id: 'edditCC'});
            
        //boton desasignar
//        jQuery("#list").jqGrid('navButtonAdd',"#pager",
//		{caption:"Desasignar", buttonicon:"ui-icon-cancel",
//			onClickButton: function(){
//				//verifico que haya al menos una fila seleccionada
//				if(selectedRows.length == 0) {
//					//mensaje error
//					$('#message-content').html('Seleccione un Bien.');
//					$('#dialog-message').dialog('open');
//				} else {
//					$.ajax({
//                                            url: '<?php echo $html->url('/inventory_assets/deallocate/') ?>',
//                                            type: 'POST',
//                                            dataType: 'json',
//                                            data: ({ ids: selectedRows}),
//                                            success: function(response){
//                                                    if(response.result == "success") {
//                                                            //cierro dialogo							
//                                                            //limpio filas seleccionadas
//                                                            selectedRows = [];
//                                                            //actualizo grid		
//                                                            $('#list').trigger('reloadGrid');
//                                                    }
//                                                    else if(response.result == "failure") {
//                                                            $('#message-content').html(response.message);
//                                                            $('#dialog-message').dialog('open');
//                                                    }						
//                                            }
//                                        });
//				}
//			},
//		position: "first", title:"Desasignar Bien", cursor: "pointer", id: 'deallocate_button'}
//	);
//	
	//boton liberar
        jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"Liberar", buttonicon:"ui-icon-cancel",
			onClickButton: function() {
				$('#dialog-allocate').dialog( "option", "title", 'Liberar Bien' );
				//verifico que haya al menos una fila seleccionada
				if(selectedRows.length == 0) {
					//mensaje error
					$('#message-content').html('Seleccione un Bien.');
					$('#dialog-message').dialog('open');
				} else {
                                        $.ajax({
                                                url: '<?php echo $html->url('/inventory_assets/deallocate/') ?>',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: ({ ids: selectedRows}),
                                                success: function(response){
                                                    if(response.result == "success") {
                                                            //cierro dialogo							
                                                            //limpio filas seleccionadas
                                                            selectedRows = [];
                                                            //actualizo grid		
                                                            $('#list').trigger('reloadGrid');
                                                            //escondo y reseteo ambas secciones
                                                            $('#allocregion').val('').trigger('change');
                                                    }
                                                    else if(response.result == "failure") {
                                                            $('#message-content').html(response.message);
                                                            $('#dialog-message').dialog('open');
                                                    }						
                                                }
                                        });	
				}
			},
		position: "first", title:"Liberar Bien", cursor: "pointer", id: 'deallocate_button'}
	);
	//boton asignar
	jQuery("#list").jqGrid('navButtonAdd',"#pager",
		{caption:"Asignar", buttonicon:"ui-icon-person",
			onClickButton: function() {
				$('#dialog-allocate').dialog( "option", "title", 'Asignar Bien' );
				//verifico que haya al menos una fila seleccionada
				if(selectedRows.length == 0) {
					//mensaje error
					$('#message-content').html('Seleccione un Bien.');
					$('#dialog-message').dialog('open');
				} else {
					$('#dialog-allocate').dialog('open');
				}
			},
		position: "first", title:"Asignar Bien", cursor: "pointer", id: 'allocate_button'}
	);
        
        
        
	//MODALS
	//modal asignar
	$('#allocregion').change(function() {
		if( $(this).val() == '' ) {
                    $('#alloccity').html('<option value="">Seleccionar Ciudad</option>');
		} else {
                    var alloc_region_id = $('#allocregion').val();
                    $('#alloccity').load('<?php echo html_entity_decode($html->url("/ajax/selectopt/city?firstopt=Seleccione%20Ciudad&region_id=")) ?>'+alloc_region_id+' option');
		}
                $('#alloccity').trigger('change');
	});
	
	//al seleccionar ciudad mostrar direcciones
	$('#alloccity').live('change', function(){
		if( $(this).val() == '' ) {
                    $('#allocaddress').html('<option value="" selected>Seleccionar Dirección</option>');
		} else {
                    //traigo contenido de select ciudades
                    var city_id = $('#alloccity').val();
                    $('#allocaddress').load('<?php echo html_entity_decode($html->url("/ajax/selectopt/address?firstopt=Seleccione%20Dirección&city_id=")) ?>'+city_id+' option');
		}
                $('#allocaddress').trigger('change');
	});
	
	//al seleccionar direcciones muestro pisos
	$('#allocaddress').live("change",function(){
		if( $(this).val() == '' ) {
                    $('#allocfloor').html('<option value="" selected>Seleccionar Piso</option>');
		} else {
			//traigo contenido de select pisos
			var address_id = $('#allocaddress').val();
                        $('#allocfloor').load('<?php echo html_entity_decode($html->url("/ajax/selectopt/floor?firstopt=Seleccione%20Piso&label=number&address_id=")) ?>'+address_id+' option');
		}
                $('#allocfloor').trigger('change');
	});
	
        $('#allocfloor').live("change",function(){
		if( $(this).val() == '' ) {
                    $('#allocoffice').html('<option value="" selected>Seleccionar Oficina</option>');
		} else {
                    //traigo contenido de select pisos
                    var floor_id = $('#allocfloor').val();
                    $('#allocoffice').load('<?php echo html_entity_decode($html->url("/ajax/selectopt/office?firstopt=Seleccione%20Oficina&label=number&floor_id=")) ?>'+floor_id+' option');
		}
                $('#allocoffice').trigger('change');
	});
        
        
	$("#dialog-allocate").dialog({
		autoOpen: false,
		height: 600,
		width: 550,
		modal: true,
		buttons: {
			'Asignar': function(){
				region = $('#allocregion').val();
				city = $('#alloccity').val();
				address = $('#allocaddress').val();
				floor = $('#allocfloor').val();
				office = $('#allocoffice').val();
				user = $("#resp").jqGrid('getGridParam', 'selrow');
                                <?php if($session->read('Auth.User.is_ses')==0):?>
                                    prog = $("#allocprog").val();
                                <?php endif; ?>
                                if(user == null){
                                    user = '';
                                }
				//envio ajax
				$.ajax({
					url: '<?php echo $html->url('/inventory_assets/allocate/') ?>',
					type: 'POST',
					dataType: 'json',
					data: ({ ids: selectedRows, region_id: region, city_id: city, address_id: address, floor_id: floor, office_id: office, user_id: user<?php if($session->read('Auth.User.is_ses')==0):?>, program_id:prog<?php endif; ?>}),
					success: function(response){
						if(response.result == "success") {
							//cierro dialogo							
							$("#dialog-allocate").dialog('close');
							//limpio filas seleccionadas
							selectedRows = [];
							//actualizo grid		
							$('#list').trigger('reloadGrid');
							//escondo y reseteo ambas secciones
							$('#allocurs').val('').trigger('change');
						}
						else if(response.result == "failure") {
							$('#message-content').html(response.message);
							$('#dialog-message').dialog('open');
						}						
					}
				});				
			},
			'Cancelar': function(){
				$(this).dialog('close');
			}
		}
	});

        //modal editar centro de costo
//        $("#dialog-edit-cc").dialog({
//            autoOpen: false,
//            height: 160,
//            width: 530,
//            modal: true,
//            buttons: {
//                'Aceptar': function(){
//                    var costcenter = $('#resp-center-edit').val();
//                    $.ajax({
//                            url: '<?php echo $html->url('/inventory_assets/editAsset/') ?>',
//                            type: 'POST',
//                            dataType: 'json',
//                            data: ({ ids: selectedRows, costcenter: costcenter}),
//                            success: function(response){
//                                    if(response.result == "success") {
//                                            //cierro dialogo							
//                                            $("#dialog-edit-cc").dialog('close');
//                                            //limpio filas seleccionadas
//                                            selectedRows = [];
//                                            //actualizo grid		
//                                            $('#list').trigger('reloadGrid');
//                                    }
//                                    else if(response.result == "failure") {
//                                            $('#message-content').html(response.message);
//                                            $('#dialog-message').dialog('open');
//                                    }						
//                            }
//                    });			
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
    

//grid de responsables
        $("#resp").jqGrid({
            url: '<?php echo $html->url('/users/search') ?>',
            datatype: "xml",
            height: 200,
            colNames: ['Id', 'Rut', 'Nombre'],
            colModel: [
                {name: 'id', index: 'id', width: 30, hidden: true},
                {name: 'rut', index: 'rut', width: 250},
                {name: 'name', index: 'name', width: 250},
            ],
            rowNum: 8,
            //rowList:[10,20,30],
            mtype: "GET",
            pager: $('#resp-pager'),
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
        });

//        $('#search_code').hide();
//        
//        $('input:[name="type"]').live('change', function(){
//            if($(this).val() == 2){
//                $('#search_code').show();
//                $("#list").jqGrid('setGridParam', {datatype:'local',data:[{id:1,code:'000000000000777'}]}).trigger("reloadGrid");
//            }else{
//                $('#search_code').hide();
//            }
//        });
});
//responsables
    var timeoutHndresp;
    var flAutoresp = false;

    function gridReloadresp() {
        var nm_mask = $("#name_search_resp").val();
        $("#resp").jqGrid('setGridParam', {url: "<?php echo $html->url('/users/search') . "/?nm_mask="; ?>" + nm_mask , page: 1}).trigger("reloadGrid");
    }

    function doSearchresp(ev) {
        if (!flAutoresp)
            return;
//	var elem = ev.target||ev.srcElement;
        if (timeoutHndresp)
            clearTimeout(timeoutHndresp)
        timeoutHndresp = setTimeout(gridReloadresp, 500)
    }

    function enableAutosubmitresp(state) {
        flAutoresp = state;
        $("#submitButtonresp").attr("disabled", state);
    }
    
    function exportExcel(selectIds, grid){
        var keys=[], i=0, rows="";
        var ids=selectIds;  // Get All IDs
        var row=grid.getRowData(ids[0]);     // Get First row to get the labels

        rows = "Codigo,Nombre";

        rows=rows+"\n";   // Output header with end of line
        for(i=0;i<ids.length;i++) {
          row=grid.getRowData(ids[i]); // get each row
          rows=rows+'"'+row['code']+'",';
          rows=rows+'"'+row['Asset.name']+'"\n';
        }
//        rows=rows+"\n";  // end of line at the end

          document.forms[0].csvBuffer.value=rows;
          document.forms[0].method='POST';
          document.forms[0].action='<?php echo $html->url('/reports/code_asset/') ?>';  // send it to server which will open this contents in excel file
          document.forms[0].target='_self';
          document.forms[0].submit();
      }
</script>
<!--<fieldset style="display: block;width: 300px;font-size: 14px;">
    <legend>Selección de Bienes</legend>
    <p>
        <input id="radio_1" type="radio" name="type" value="1" checked/>
        <label for="radio_1">Desde Sistema</label>
    </p>
    <p>
        <input id="radio_2" type="radio" name="type" value="2"/>
        <label for="radio_2">Desde Capturador de Datos</label>
    </p>
    <p id="search_code">
        <label for="codigo_id">Ingresar Código</label>
        <input id="codigo_id" type="text" id="codigo" />
    </p>
</fieldset>-->
<!--
<br/>
<br/>-->
<table id="list"></table>
<div id="pager"></div>

<div id="dialog-allocate" title="Asignar Bienes">
	<table>
		<tr>
			<td width="30%">Región</td>
			<td width="70%">
				<select id="allocregion" style="width: 200px">
					<option value="">Seleccionar Región</option>
					<?php foreach($regiones as $id => $name) echo '<option value="' . $id . '">' . $name . '</option>';?>
				</select>
			</td>
		</tr>
                <tr>
			<td width="30%">Ciudad</td>
			<td width="70%">
                            <select id="alloccity">
                                <option value="">Seleccionar Ciudad</option>
                            </select>
			</td>
		</tr>
                <tr>
			<td width="30%">Dirección</td>
			<td width="70%">
                            <select id="allocaddress">
                                <option value="">Seleccionar Direcci&oacute;n</option>
                            </select>
			</td>
		</tr>
                <tr>
			<td width="30%">Piso</td>
			<td width="70%">
                            <select id="allocfloor">
                                <option value="">Seleccionar Piso</option>
                            </select>
			</td>
		</tr>
                <tr>
			<td width="30%">Oficina</td>
			<td width="70%">
                            <select id="allocoffice">
                                <option value="">Seleccionar Oficina</option>
                            </select>
			</td>
		</tr>
                <?php if($session->read('Auth.User.is_ses') == 0): ?>
                <tr>
			<td width="30%">Programas</td>
			<td width="70%">
				<select id="allocprog">
					<option value="">Seleccione Tipo</option>
					<?php foreach(Configure::read('Programas') as $k => $v) echo '<option value=' . $k . '>' .$v . '</option>';?>
				</select>
			</td>
		</tr>
                <?php endif; ?>
	</table>
    <br />
    <div id='responsable'>		
        Responsable:<br />

        <div>
            Nombre o Rut<br />
            <input type="text" id="name_search_resp" onkeydown="doSearchresp(arguments[0] || event)" />
            <button onclick="gridReloadresp()" id="submitButtonresp" style="margin-left:30px;">Buscar</button> <br />
            <input type="checkbox" id="autosearch_resp" onclick="enableAutosubmitresp(this.checked)" /> Búsqueda automática <br />
        </div>

        <br />
        <table id="resp"></table>
        <div id="resp-pager"></div>
    </div>
</div>

<!--<div id="dialog-edit-cc" title="Editar Centro de Costo">
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
<form method="post">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>