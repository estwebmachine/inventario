<script type="text/javascript">
$(document).ready(function(){
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
                    $('#allocaddress').html('<option value="">Seleccionar Dirección</option>');
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
                    $('#allocfloor').html('<option value="">Seleccionar Piso</option>');
		} else {
			//traigo contenido de select pisos
			var address_id = $('#allocaddress').val();
                        $('#allocfloor').load('<?php echo html_entity_decode($html->url("/ajax/selectopt/floor?firstopt=Seleccione%20Piso&label=number&address_id=")) ?>'+address_id+' option');
		}
                $('#allocfloor').trigger('change');
	});
	
        $('#allocfloor').live("change",function(){
		if( $(this).val() == '' ) {
                    $('#allocoffice').html('<option value="">Seleccionar Oficina</option>');
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
        
        $('#pdf').button({ icons: {primary:'ui-icon-document'} });
        
        $('#pdf').live('click', function(){
            $('#form_firma').submit();
        });
});
</script>

<fieldset>
    <legend>Hoja Mural</legend>
        <form id="form_firma" method="post" action="<?php echo $html->url('/reports/generate/hoja_mural/pdf') ?>" target="_blank">
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
                                <select id="allocoffice" name="office">
                                    <option value="">Seleccionar Oficina</option>
                                </select>
                            </td>
                    </tr>
                    <tr>
                    <td>Encargado de Inventario (S)</td>
                    <td><input name="sub" type="checkbox" value=" (S)"/></td>
                </tr>
                <tr>
                    <td>Encargado/a Dpto. Adquisiciones: </td>
                    <td><textarea rows="4" name="entrega"></textarea></td>
                </tr>
            </table>
        </form>
        <br />
        <a href="#" id="pdf">Generar PDF</a>
</fieldset>

<div id="dialog-message" title="Mensaje">
	<p>
		<span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
		<div id="message-content"></div>
	</p>
</div>
