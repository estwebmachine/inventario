<script type="text/javascript">
    $(document).ready(function(){
        $('#desde').datepicker();
        $("#hasta").datepicker();
        $('#excel').button({ icons: {primary:'ui-icon-document'} });
        $('#clases').load(webroot + '/ajax/selectopt/m_class?firstopt=Seleccionar option');
        $('#tipo_de').html('<option value="">Seleccionar</option><?php echo $general->selectOpt('Transaction.type');?>');
        $('#clases').live('change', function(){
            var clase = $('#clases option:selected').val();
            $('#sub_clases').load(webroot + '/ajax/selectopt/sub_class?MClass.id='+clase+'&firstopt=Seleccionar option');
        });
        $('#tipo').live('change', function(){
            if($('#tipo option:selected').val() == '1'){
                $('#tipo_de').html('<option value="">Seleccionar</option><?php echo $general->selectOpt('Transaction.type');?>');
            }else if($('#tipo option:selected').val() == '2'){
                $('#tipo_de').html('<option value="">Seleccionar</option><?php echo $general->selectOpt('InventoryAssetDisposal.type');?>');
            }else{
                $('#tipo_de').html('Seleccionar');
            }
        });
        $('.formsend').click(function(){
            format = $(this).attr('id');
            $('#reportform').attr('target', '_self');
            if($('#tipo').val() == 1) $('#reportform').attr('action', webroot+'/reports/generate/alta/' + format).submit();
            else if($('#tipo').val() == 2) $('#reportform').attr('action', webroot+'/reports/generate/baja/' + format).submit();
            return false;
	});
    });
</script>

<form id="reportform" method="post">
    <fieldset>
        <legend>Reporte de Alta/Baja</legend>
        <table>
            <tr>
                <td width="40%">Tipo</td>
                <td width="60%">
                    <select id="tipo">
                        <option value="1">Alta</option>
                        <option value="2">Baja</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%">Tipo de Alta/Baja</td>
                <td width="60%">
                    <select id="tipo_de" name="tipo_de">
                        <option value=''>Seleccionar</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Clase</td>
                <td>
                    <select id="clases" name="clase">
                        
                    </select>
                </td>
            </tr>
            <tr>
                <td>Sub Clase</td>
                <td>
                    <select id="sub_clases" name="subclase">
                        <option value="">Seleccionar</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Precio (Menor o Igual que)</td>
                <td>
                    <input type="text" id="price" name="price"/>
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
        <br />
		<a href="#" class="formsend" id="excel" target="_self">Generar Excel</a>
    </fieldset>    
</form>