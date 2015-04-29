<script type="text/javascript">
    $(document).ready(function(){
        $('#desde').datepicker();
        $("#hasta").datepicker();
        $('#excel').button({ icons: {primary:'ui-icon-document'} });
        
        $('.formsend').click(function(){
            format = $(this).attr('id');
            $('#reportform').attr('target', '_self');
            $('#reportform').attr('action', webroot+'/reports/generate/all/' + format).submit();
            return false;
	});
    });
</script>

<form id="reportform" method="post">
    <fieldset>
        <legend>Reporte Total de bienes del sistema</legend>
        <table>
            <tr>
                <td>Desde</td>
                <td><input type="text" id="desde" name="desde"/></td>
            </tr>
            <tr>
                <td>Hasta</td>
                <td><input type="text" id="hasta" name="hasta"/></td>
            </tr>
            <tr>
                <td>Precio (Menor o Igual que)</td>
                <td>
                    <input type="text" id="price" name="price"/>
                </td>
            </tr>
        </table>
        <br />
		<a href="#" class="formsend" id="excel" target="_self">Generar Excel</a>
    </fieldset>    
</form>