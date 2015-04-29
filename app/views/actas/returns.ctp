<script type="text/javascript">
    $(document).ready(function(){
        $("#list").jqGrid({
            url:'<?php echo $html->url('/actas/indextable?type=0'); ?>',
            datatype:'xml',
            mtype:'GET',
            colNames:['Id', 'Folio', 'Tipo', 'Estado','Asignador','Nombres', 'Apell. paterno', 'Apell. materno', 'Fecha asignación'],
            colModel:[
                {name:'id', index:'id', hidden:true},
                {name:'folio', index:'folio'},
                {name:'type', index:'type', search:true, stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('Actas.type'); ?>"}},
                {name:'status', index:'status', search:true, stype:'select', searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('Actas.status'); ?>"}},
                {name:'assigned_id', index:'assigned_id'},
                {name:'names', index:'names'},
                {name:'primary_last_name', index:'primary_last_name'},
                {name:'second_last_name', index:'second_last_name'},
                {name:'created', index:'created', search:true, searchoptions:{dataInit:function(element){$(element).datepicker({dateFormat:'dd/mm/yy', onSelect:function(dataText, inst){var sgrid = $("#list")[0];sgrid.triggerToolbar();}});}}},
            ],
            pager:'#pager',
            rowNum:10,
            rowList:[10, 20, 30],
            sortorder:'desc',
            sortname:'id',
            viewrecords:true,
            caption:'Actas de devolución',
            autowidth:true,
            subGrid:true,
            subGridRowExpanded: function(subgrid_id, row_id) {
                var subgrid_table_id, pager_id;
                subgrid_table_id = subgrid_id+"_t";
                pager_id = "p_"+subgrid_table_id;
                $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                $("#"+subgrid_table_id).jqGrid({
                    url:'<?php echo html_entity_decode($html->url('/inventory_assets/indextable?action=acta_devolucion&acta_id=')); ?>' + row_id,
                    datatype: "xml",
                    colNames:['Id', 'Código', 'Descripción','Detalle', 'Serial', 'Precio original', 'Precio actual', 'Estado', '','Calidad','Vida útil','Valor residual', 'Nombres Responsable', 'Apellido P. Responsable', 'Apellido M. Responsable','','Oficina','Piso', 'Dirección', 'Ciudad','Región', 'Creado', 'Modificado'],
                    colModel :[
			{name:'id', index:'id', editable:false, hidden:true, search:false},
                        {name:'code', index:'code', search:true, width:250},
			{name:'Asset.name', index:'Asset.name', search:true, hidden: false},
                        {name:'detail', index:'detail', search:false, hidden: true},
			{name:'serial', index:'serial', search:true, hidden: false},
			{name:'original_price', index:'original_price', search:false, hidden: true},
			{name:'current_price', index:'current_price', search:false, hidden: true},
			{name:'status', index:'status', stype:'select', hidden:true,searchoptions:{value:": ;<?php echo $jqgrid->selectOpt('InventoryAsset.status');?>"}},
                        {name:'is_depreciate', index:'is_depreciate', search:false, hidden: true},
                        {name:'situation', index:'situation', search:false, hidden: false},
                        {name:'life', index:'life', search:false, hidden: true},
			{name:'residual_value', index:'residual_value',hidden:true},
                        {name:'User.names', index:'User.names', search:true, hidden: true,sortable:false},
                        {name:'User.primary_last_name', index:'User.primary_last_name', search:true, hidden: true,sortable:false},
                        {name:'User.second_last_name', index:'User.second_last_name', search:true, hidden: true,sortable:false},
                        {name:'program_id', index:'program_id', search:true, hidden: true,sortable:false},
			{name:'Office.number', index:'Office.number', search:true, hidden: true,sortable:false},
			{name:'Floor.number', index:'Floor.number', search:true, hidden: true,sortable:false},
			{name:'Address.name', index:'Address.name', search:true, hidden: true,sortable:false},
                        {name:'City.name', index:'City.name', search:true, hidden: true,sortable:false},
			{name:'Region.name', index:'Region.name', search:true, hidden: true,sortable:false},
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
                    rowNum:20,
                    pager: pager_id,
                    sortname: 'id',
                    sortorder: "asc",
                    autowidth: true,
                    gridComplete: function() {
                        row_data = $("#list").getRowData(row_id);
                        if (row_data['status'] == 'Nula') {
                            //esconder boton borrar y editar
                            $('#anular_acta_'+row_id).hide();
                        } else {
                            //mostrar boton borrar y editar
                            $('#anular_acta_'+row_id).show();
                        }
                    }
                });
                $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false, add:false, del:false, search:false});
                $("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,{caption:"Imprimir Acta", buttonicon:"ui-icon-print",
                    onClickButton: function() {
                        $('#acta_id').val(row_id);
                        $('#dialog-acta').dialog('open');
                    },
                    position: "last", title:"", cursor: "pointer", id: 'print_acta_' + row_id});
                $("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,{caption:"Anular Acta", buttonicon:"ui-icon-close",
                    onClickButton: function() {
                        $.ajax({
                            url:'<?php echo $html->url('/actas/nulls')?>/'+row_id,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response){
                                if (response.result == "success") {
                                $('#list').trigger('reloadGrid');
                                }
                                else if (response.result == "failure") {
                                    $('#message-content').html(response.message);
                                    $('#dialog-message').dialog('open');
                                }
                            }
                        });
                    },
                    position: "last", title:"", cursor: "pointer", id: 'anular_acta_' + row_id});
            }
            
        });
        
        $("#list").jqGrid('navGrid', '#pager', {edit:false, add:false, del:false, search:false});
        
         $("#dialog-acta").dialog({
            autoOpen: false,
            height: 200,
            width: 520,
            modal: true,
            buttons: {
                'Imprimir': function(){
                   var acta_id = $('#acta_id').val();
                   var sub = $('#encargado_s').val();
                   var entrega = $('#entrega').val();
                   $('#form_acta').submit();
                   $(this).dialog('close');
                },
                'Cancelar': function() {
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
        
        $.ajax({
            url:'<?php echo $html->url('/actas/indextable?type=0'); ?>',
            datatype:'xml',
            
        });
        
        $.ajax({
            type: "POST",
            url: "<?php echo Router::url(array('controller'=>'actas','action'=>'funcionarios'),true); ?>",
					 				
	    success: function(response){
			$("#div_f").html(response);
	    }
	});
    });
  
</script>
<table id="list"></table>
<div id="pager"></div>

<div id="dialog-acta" title="Parametrizar Firma">
	
        <form id="form_acta" method="post" action="<?php echo $html->url('/reports/generate/actas/pdf') ?>" target="_blank">
            <input type="hidden" name="acta_id" id="acta_id" value="" />
            <input type="hidden" name="type" id="type" value="0" />
            <table>
                <tr>
                    <td>Encargado de Inventario (S)</td>
                    <td><input name="sub" type="checkbox" value=" (S)"/></td>
                </tr>
                <tr>
                    <td>Recibe: </td>
                    <td><textarea rows="4" name="entrega" id="text_recibe"></textarea></td>
                </tr>
            </table>
        </form>
</div>
<div id="dialog-message" title="Mensaje">
    <p>
        <span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
        <div id="message-content"></div>
    </p>
</div>