<script type="text/javascript">
    $(document).ready(function(){
        $('#desde').datepicker();
        $("#hasta").datepicker();
        $('#excel').button({ icons: {primary:'ui-icon-document'} });
        $('#clases').load(webroot + '/ajax/selectopt/m_class?firstopt=Seleccionar option');
        $('#clases').live('change', function(){
            var clase = $('#clases option:selected').val();
            $('#sub_clases').load(webroot + '/ajax/selectopt/sub_class?MClass.id='+clase+'&firstopt=Seleccionar option');
        });
        $('.formsend').click(function(){
            format = $(this).attr('id');
            $('#reportform').attr('target', '_self');
            $('#reportform').attr('action', webroot+'/reports/generate/contabilidad/' + format).submit();
	});
    });
</script>

<form id="reportform" method="post">
    <fieldset>
        <legend>Reporte de Contabilidad</legend>
        <table>
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
                <td>Nº Dcto (Factura o Guía de Despacho)</td>
                <td>
                    <input type="text" id="document_number" name="document_number"/>
                </td>
            </tr>
            <tr>
                <td>Nº Inventario</td>
                <td>
                    <input type="text" id="code" name="code"/>
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