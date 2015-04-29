<script type="text/javascript">

    $(document).ready(function() {
        $("#list").jqGrid({
            url: '<?php echo $html->url('/purchase_orders/indextable'); ?>',
            datatype: 'xml',
            mtype: 'GET',
            colNames: ['Id', 'Número', 'Nombre', 'Fecha', 'Descripción', 'Comentario anulación','Estado', 'Nombre Proveedor', 'Rut Proveedor', 'Usuario Ingreso', 'Creada', 'Modificada'],
            colModel: [
                {name: 'id', index: 'id', hidden: true,search: false},
                {name: 'order_number', index: 'order_number'},
                {name: 'name', index: 'name'},
                {name: 'date', index: 'date', search: true, searchoptions: {dataInit:
                function(element) {
                    $(element).datepicker({
                        dateFormat: 'dd/mm/yy',
                        onSelect: function(dateText, inst) {
                            var sgrid = $("#list")[0];
                            sgrid.triggerToolbar();
                        }
                    });
                }}
                },
                {name: 'description', index: 'description', sortable: false, search: false},
                {name: 'comment', index: 'comment', sortable: false, search: false},
                {name: 'status', index: 'status', stype: 'select', searchoptions: {value: ": ;<?php echo $jqgrid->selectOpt('PurchaseOrder.status'); ?>"}},
                {name: 'provider_name', index: 'provider_name', search: true},
                {name: 'provider_rut', index: 'provider_rut', search: true},
                {name: 'user_id', index: 'user_id', search: true},
                {name: 'created', index: 'created', hidden: true},
                {name: 'modified', index: 'modified', search: true, searchoptions: {dataInit:
                function(element) {
                    $(element).datepicker({
                        dateFormat: 'dd/mm/yy',
                        onSelect: function(dateText, inst) {
                            var sgrid = $("#list")[0];
                            sgrid.triggerToolbar();
                        }
                    });
                }}
                }
            ],
            pager: '#pager',
            rowNum: 10,
            rowList: [10, 20, 30],
            sortname: 'id',
            sortorder: 'desc',
            viewrecords: true,
            caption: '<?php __('Ordenes de Compras y Otros'); ?>',
            editurl: '<?php echo $html->url('/purchase_orders/indexedit') ?>',
            //height: 350,
            autowidth: true,
            subGrid: true,
            onSelectRow: function(row_id) {
                row_data = $("#list").getRowData(row_id);
                if (row_data['status'] == 'Sin Recepcionar' || row_data['status'] == 'Recepcionada' || row_data['status'] == 'Nula') {
                    //esconder boton borrar y editar
                    $('#del_list').hide();
                    $('#edit_list').hide();
                } else {
                    //mostrar boton borrar y editar
                    $('#del_list').show();
                    $('#edit_list').show();
                }
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
                subgrid_table_id = subgrid_id + "_t";
                pager_id = "p_" + subgrid_table_id;
                $("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
                jQuery("#" + subgrid_table_id).jqGrid({
                    url: '<?php echo $html->url('/purchase_order_details/indextable?purchase_order_id=') ?>' + row_id,
                    datatype: "xml",
                    colNames: ['Id','Descripción', 'Cantidad', 'Cantidad Restante','Moneda', 'Precio','Descuento', 'Valor', 'Creado', 'Modificado'],
                    colModel: [
//                        {name: 'purchase_order_id', index: 'purchase_order_id', editable: false, hidden: true},
                        {name: 'id', index: 'id', editable: false, hidden: true},
//                        {name: 'asset_id', index: 'asset_id', editable: false, width: 400, hidden: true},
                        {name: 'description', index: 'description'},
                        {name: 'amount', index: 'amount', align: 'center',editable:true, editoptions:{
                                dataInit: function(element) {
                                    $(element).on('focus', function(){
                                        if(element.value == '$0'){
                                            element.value = '';
                                        }
                                    }),
                                    $(element).keypress(function(evt) {
                                        var charCode = (evt.which) ? evt.which : event.keyCode;

                                        if ((charCode < 48 || charCode > 57)) {
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    }) 
                               }                            
                            }},
                        {name: 'amount_trans', index: 'amount_trans', align: 'center',hidden:true},
                        {name: 'currency',index:'currency',align: 'center',width: 100, editable: true, edittype:"select", editoptions:{value:"CLP:CLP;USD:USD;UF:UF"}},
                        {name:'price',index:'price',editable:true, align: 'center', edittype:"text", width:150,editoptions:{
                                dataInit: function(element) {
                                    $(element).on('focus', function(){
                                        if(element.value == '$0'){
                                            element.value = '';
                                        }
                                    }),
                                     $(element).keypress(function(evt) {
                                        var charCode = (evt.which) ? evt.which : event.keyCode;

                                        if ((charCode < 48 || charCode > 57)) {
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    })
                                }
                            }},
                        {name: 'discount', index: 'discount', align: 'center'},
                        {name: 'value', index: 'value', align: 'center'},
                        
                        {name: 'created', index: 'created', hidden: true},
                        {name: 'modified', index: 'modified', hidden:true}
                    ],
                    rowNum: 10,
                    rowList: [10, 20, 30],
                    pager: pager_id,
                    sortname: 'id',
                    sortorder: "asc",
                    height: '100%',
                    autowidth: true,
                    cellEdit: true,
                    cellsubmit: 'remote',
                    gridComplete: function() {
//                        $('#' + pager_id + '_right').remove();
                    },
                    afterSaveCell: function(id, name, val, iRow, iCol) {
                        //calculo campo valor, no se devuelve por ajax.
                      
                        var rowId = $("#" + subgrid_table_id).getRowData(id);
                    
                        if (rowId['amount'] != '' && rowId['price'] != '')
                            $("#" + subgrid_table_id).setRowData(id, {value: rowId['amount'] * rowId['price']});
                    },
                    cellurl: '<?php echo $html->url('/purchase_order_details/indexedit/') ?>' + row_id + '/',
                    editurl: '<?php echo $html->url('/purchase_order_details/indexedit/') ?>' + row_id + '/'
                });
                jQuery("#" + subgrid_table_id).jqGrid('navGrid', "#" + pager_id, {edit: false, add: false, del: true, search: false},
                {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
                {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"},
                {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
                {}
                );

                //boton imprimir
//                jQuery("#" + subgrid_table_id).jqGrid('navButtonAdd', "#" + pager_id,
//                        {caption: "Imprimir", buttonicon: "ui-icon-print",
//                            onClickButton: function() {
//                                window.open('<?php echo $html->url('/purchase_orders/view_pdf/'); ?>' + row_id + '/');
//                            },
//                            position: "last", title: "", cursor: "pointer", id: 'pdfprint' + row_id});

                //boton enviar
                jQuery("#" + subgrid_table_id).jqGrid('navButtonAdd', "#" + pager_id,
                        {caption: "Enviar", buttonicon: "ui-icon-check",
                            onClickButton: function() {
                                //enviar transaccion										
                                $.ajax({
                                    url: '<?php echo $html->url('/purchase_orders/close/') ?>' + row_id,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.result == "success") {
                                            //sacar botones
                                            $('#sendButton' + row_id).remove();
                                            $('#nullButton' + row_id).remove();
                                            $('#addAssets' + row_id).remove();
                                            $('#add_list_' + row_id + '_t').remove();
                                            $('#del_list_' + row_id + '_t').remove();
                                            //evitar inline edit
                                            $("#" + subgrid_table_id).setGridParam({cellEdit: false});
                                            //cambiar como muestra el estado
                                            $("#list").setRowData(row_id, {status: 'Sin Recepcionar'});
                                            //esconder boton borrar y editar de grid padre
                                            $('#del_list').hide();
                                            $('#edit_list').hide();
                                        }
                                        else if (response.result == "failure") {
                                            $('#message-content').html(response.message);
                                            $('#dialog-message').dialog('open');
                                        }
                                    }
                                });
                            },
                            position: "last", title: "", cursor: "pointer", id: 'sendButton' + row_id});

                //boton anular
                jQuery("#" + subgrid_table_id).jqGrid('navButtonAdd', "#" + pager_id,
                        {caption: "Anular", buttonicon: "ui-icon-circle-close",
                            onClickButton: function() {
                                //abrir dialogo comentario anulacion
                                //paso row_id y subgrid_table_id
                                $('#row_id_null').val(row_id);
                                $('#subgrid_table_id_null').val(subgrid_table_id);
                                $('#dialog-null').dialog('open');
                            },
                            position: "last", title: "", cursor: "pointer", id: 'nullButton' + row_id});

                //boton agregar bienes
                jQuery("#" + subgrid_table_id).jqGrid('navButtonAdd', "#" + pager_id,
                        {caption: "Agregar Bienes", buttonicon: "ui-icon-plus",
                            onClickButton: function() {
                                //paso id bodega
                                row_data = $("#list").getRowData(row_id);
                                //paso id padre
                                $('#purchase_id').val(row_id);
                                //recargar grid
                                gridReload();
                                $('#dialog-form').dialog('open');
                            },
                            position: "last", title: "", cursor: "pointer", id: 'addAssets' + row_id});

                //sacar botones al cargar si esta enviada la transaccion
                row_data = $("#list").getRowData(row_id);
                if (row_data['status'] == 'Sin Recepcionar' || row_data['status'] == 'Recepcionada' || row_data['status'] == 'Nula') {
                    //evitar inline edit
                    $("#" + subgrid_table_id).setGridParam({cellEdit: false});
                    //revisar presencia de boton anular
                    if (row_data['status'] != 'Sin Recepcionar')
                        $('#nullButton' + row_id).remove();
                    $('#sendButton' + row_id).remove();
                    $('#addAssets' + row_id).remove();
                    $('#add_list_' + row_id + '_t').remove();
                    $('#del_list_' + row_id + '_t').remove();
                }
            },
            subGridRowColapsed: function(subgrid_id, row_id) {

            }
        });

        $("#list").jqGrid('navGrid', '#pager', {edit: false, add: false, del: false, search: false}, //options
        {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
        {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
        {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
        {sopt: ['eq']} // search options
        );

        $("#list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

        //boton editar orden
        
         jQuery("#list").jqGrid('navButtonAdd',"#pager",
         {caption:"", buttonicon:"ui-icon-pencil",
         onClickButton: function() {
         //indico que se trata de edicion
         $('#order-oper').val('edit');
         $('#dialog-order').dialog( "option", "title", 'Editar Orden' );
         //verifico que haya una fila seleccionada
         rowsel= $("#list").jqGrid('getGridParam','selrow');
         if(rowsel == null) {
         //mensaje error
         $('#message-content').html('Seleccione una Orden.');
         $('#dialog-message').dialog('open');
         } else {
         // paso los datos de la fila seleccionada al formulario
         row_data = $("#list").getRowData(rowsel);
         $('#order-id').val(rowsel);
         $('#order-number').val(row_data['order_number']);
         $('#order-name').val(row_data['name']);
         $('#order-date').val(row_data['date']);
         $('#order-comment').val(row_data['description']);
         $('#rut_search_prov').val(row_data['provider_rut']);
         //presiono buscar
         gridReloadprov();
         
         $('#dialog-order').dialog('open');
         }
         },
         position: "first", title:"Editar Orden", cursor: "pointer", id: 'edit_list'}
         );
         

        //boton agregar orden
        jQuery("#list").jqGrid('navButtonAdd', "#pager",
                {caption: "Agregar OC-XML", buttonicon: "ui-icon-plus",
                    onClickButton: function() {
                        /* NUEVO DIALOGO */
                        $('#new-dialog-order').dialog('open');
                    },
                    position: "last", title: "Agregar Orden", cursor: "pointer", id: 'add_list'}
        );
        
         jQuery("#list").jqGrid('navButtonAdd', "#pager",
                {caption: "Agregar OC", buttonicon: "ui-icon-plus",
                    onClickButton: function() {
                        // DIALOGO ANTIGUO
                         //indico que se trata de agregar
                         $('#order-oper').val('add');
                         $('#dialog-order').dialog( "option", "title", 'Agregar Orden' );
                         //muestro listado de bodegas
                         
                         //reseteo campos
                         $('#order-id').val('');
                         $('#order-number').val('');
                         $('#order-date').val('');
                         $('#order-comment').val('');
                         $('#rut_search_prov').val('');
                         //presiono buscar
                         gridReloadprov();
                         
                         $('#dialog-order').dialog('open');
                    },
                    position: "last", title: "Agregar Orden Manualmente", cursor: "pointer", id: 'add_list2'}
        );

        //boton agregar contrato
//        jQuery("#list").jqGrid('navButtonAdd', "#pager",
//                {caption: "Agregar Otros", buttonicon: "ui-icon-plus",
//                    onClickButton: function() {
//                        //indico que se trata de agregar
//                        $('#order-oper').val('add');
//                        $('#dialog-order').dialog("option", "title", 'Agregar Otros');
//                        //muestro listado de bodegas
//                        //$('#order-warehouse').show();
//
//                        //reseteo campos
//                        //$('#order-id').val('');
//                        $('#order-number').val('');
//                        $('#order-date').val('');
//                        $('#order-comment').val('');
//                        $('#rut_search_prov').val('');
//                        //presiono buscar
//                        gridReloadprov();
//                        $('#dialog-order').dialog('open');
//                    },
//                    position: "last", title: "Agregar Orden", cursor: "pointer", id: 'add_list'}
//        );

        //boton imprimir grilla
//        jQuery("#list").jqGrid('navButtonAdd', "#pager", {caption: "Imprimir", buttonicon: "ui-icon-print",
//            onClickButton: function() {
//                var grid = $("#list");
//                var ids = grid.jqGrid('getDataIDs');
//                url = webroot + '/purchase_orders/grid_print/ordenes_compra/' + ids.join(',');
//                window.open(url);
//            },
//            position: "last", title: "", cursor: "pointer", id: 'pdfprint'}
//        );

        //MODALS
        // modal agregar y editar contrato

        $('#order-date').datepicker({dateFormat: 'dd/mm/yy'});
        //$('#warehouse-cont').load(webroot + '/ajax/userwarehouses/', function() {$('#warehouse-cont select').attr('id', 'order-warehouse');});
        $("#dialog-order").dialog({
            autoOpen: false,
            height: 550,
            width: 400,
            modal: true,
            buttons: {
                'Guardar': function() {
                    ordernumber = $('#order-number').attr('value');
                    ordername = $('#order-name').val();
                    orderdate = $('#order-date').attr('value');
                    provselected = $("#providers").jqGrid('getGridParam', 'selrow');
                    orderid = $('#order-id').val();
                    orderoper = $('#order-oper').val();
                    ordercomment = $('#order-comment').val();
                    //envio ajax

                    $.ajax({
                        url: '<?php echo $html->url('/purchase_orders/indexedit/') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: ({id:orderid,oper: orderoper, order_number: ordernumber, name: ordername,date: orderdate, provider_id: provselected, description: ordercomment}),
                        success: function(response) {
                            if (response.result == "success") {
                                //cierro dialogo							
                                $("#dialog-order").dialog('close');
                                //expando subgrid si estoy agregando
                                //if(response.oper == 'add') $('#expandSubGrid').val(response.id);
                                if (response.oper == 'edit')
                                    $('#expandSubGrid').val('');
                                //actualizo grid
                                $('#list').trigger('reloadGrid');
                                gridReloadprov();
                            }
                            else if (response.result == "failure") {
                                $('#message-content').html(response.message);
                                $('#dialog-message').dialog('open');
                            }
                        }
                    });
                },
                'Cancelar': function() {
                    $(this).dialog('close');
                    gridReload();
                }
            }
        });


        // nuevo modal agregar orden (desde xml)
        $("#new-dialog-order").dialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            buttons: {
                'Guardar': function() {
                    $('#addorderform').submit();
                },
                'Cancelar': function() {
                    $(this).dialog('close');
                }
            }
        });

        
         // modal agregar bienes
         $("#dialog-form").dialog({
            autoOpen: false,
            height: 550,
            width: 500,
            modal: true,
            buttons: {
                'Agregar': function(){
                    idsselected = $("#bigset").jqGrid('getGridParam', 'selarrrow');
                    purchaseid = $("#purchase_id").val();
                    //envio ajax
                    $.post('<?php echo $html->url('/purchase_orders/addasset/') ?>',
                    { ids: idsselected, pid: purchaseid},
                                        function(msg) {
                                            if (msg == 'success') {
                                                //cierro dialogo
                                                $("#dialog-form").dialog('close');
                                                //actualizo grid
                                                purchaseid = $("#purchase_id").val();
                                                $('#list_' + purchaseid + '_t').trigger('reloadGrid');
                                                gridReload();
                                            }
                                        }
                                        );

                                    },
                                    'Cancelar': function() {
                                        $(this).dialog('close');
                                        gridReload();
                                    }
                                }
                            });
         
        //modal comentario anulacion
        $("#dialog-null").dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                'Anular': function() {
                    //enviar transaccion
                    //obtengo row_id y subgrid_table_id
                    var row_id = $('#row_id_null').val();
                    var subgrid_table_id = $('#subgrid_table_id_null').val();
                    commentnull = $('#comment-null').val();
                    $.ajax({
                        url: '<?php echo $html->url('/purchase_orders/null/') ?>' + row_id,
                        type: 'POST',
                        dataType: 'json',
                        data: ({comment: commentnull}),
                        success: function(response) {
                            if (response.result == "success") {
                                //cierro dialogo
                                $("#dialog-null").dialog('close');
                                //sacar botones
                                $('#sendButton' + row_id).remove();
                                $('#nullButton' + row_id).remove();
                                $('#addAssets' + row_id).remove();
                                $('#add_list_' + row_id + '_t').remove();
                                $('#del_list_' + row_id + '_t').remove();
                                //evitar inline edit
                                $("#" + subgrid_table_id).setGridParam({cellEdit: false});
                                //cambiar como muestra el estado
                                $("#list").setRowData(row_id, {status: 'Nula'});
                                //cambiar como muestra comentario
                                $("#list").setRowData(row_id, {comment: commentnull});
                                //esconder boton borrar y editar de grid padre
                                $('#del_list').hide();
                                $('#edit_list').hide();
                            }
                            else if (response.result == "failure") {
                                $('#message-content').html(response.message);
                                $('#dialog-message').dialog('open');
                            }
                        }
                    });
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

    });
</script>
<table id="list"></table>
<div id="pager"></div>


<script type="text/javascript">
    
     //assets
     var timeoutHnd;
     var flAuto = false;
     
     function gridReload(){
     var nm_mask = $("#name_search").val();
     $("#bigset").jqGrid('setGridParam',{url: "<?php echo $html->url('/assets/search') . "?nm_mask="; ?>"+nm_mask,page:1}).trigger("reloadGrid");
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
     
//providers
    var timeoutHndprov;
    var flAutoprov = false;

    function gridReloadprov() {
        var nm_mask = $("#name_search_prov").val();
        var rut_mask = $("#rut_search_prov").val();
        $("#providers").jqGrid('setGridParam', {url: "<?php echo $html->url('/providers/search') . "/?nm_mask="; ?>" + nm_mask + "&rut_mask=" + rut_mask, page: 1}).trigger("reloadGrid");
    }

    function doSearchprov(ev) {
        if (!flAutoprov)
            return;
//	var elem = ev.target||ev.srcElement;
        if (timeoutHndprov)
            clearTimeout(timeoutHndprov)
        timeoutHndprov = setTimeout(gridReloadprov, 500)
    }

    function enableAutosubmitprov(state) {
        flAutoprov = state;
        $("#submitButtonprov").attr("disabled", state);
    }

    $(document).ready(function() {
        //grid de productos
        
         $("#bigset").jqGrid({
            url:'<?php echo $html->url('/assets/search') ?>',
            datatype: "xml",
            height: 270,
            colNames:['Id','Nombre'],
            colModel:[
            {name:'id',index:'id', width:30, hidden:true},
            {name:'name',index:'name', width:350}
//            {name:'code',index:'code', width:80}
            ],
            rowNum:50,
            //rowList:[10,20,30],
            mtype: "GET",
            pager: $('#pagerb'),
            sortname: 'name',
            viewrecords: true,
            sortorder: "asc",
            multiselect: true
         });
         
        //grid de proveedores
        $("#providers").jqGrid({
            url: '<?php echo $html->url('/providers/search') ?>',
            datatype: "xml",
            height: 200,
            colNames: ['Id', 'Nombre', 'Rut'],
            colModel: [
                {name: 'id', index: 'id', width: 30, hidden: true},
                {name: 'fantasyname', index: 'fantasyname', width: 250},
                {name: 'rut', index: 'rut', width: 100}
            ],
            rowNum: 8,
            //rowList:[10,20,30],
            mtype: "GET",
            pager: $('#providers-pager'),
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            loadComplete: function() {
                //si es edicion
                if ($('#order-oper').val() == 'edit') {
                    //busco la id primera fila
                    selid = $('#providers tr:first').attr('id');
                    //la selecciono
                    $(this).setSelection(selid);
                }
            }
        });

    });
</script>

<div id="new-dialog-order" title="Agregar Orden">
    <?php echo $form->create(null, array('type' => 'file', 'id' => 'addorderform', 'url' => array('action' => 'load'))); ?>
    <table>
        <tr>
            <td width="30%">Archivo XML</td>
            <td width="70%">
                <input type="file" name="data[File][]" />
            </td>
        </tr>
        <tr>
            <td width="30%">Observación</td>
            <td width="70%">
                <textarea name="data[PurchaseOrder][description]" rows="5" cols="30"></textarea>
            </td>
        </tr>
    </table>
</form>
</div>

<div id="dialog-order" title="Agregar Otros">
    <input type="hidden" id="order-oper" value="" />
    <input type="hidden" id="order-id" value="" />
    <table>
        <tr>
            <td style="vertical-align: text-top;">
                Fecha <br /><input type="text" id="order-date" readonly="readonly" /><br />
                Número <br /><input type="text" id="order-number" /><br />
                Nombre <br /><input type="text" id="order-name" /><br />
            </td>
            <td style="vertical-align: text-top;">
                Descripción <br /><textarea id="order-comment" rows="6"></textarea>
            </td>
        </tr>
    </table>

    <div>Proveedor:</div>
    <div>		
        Rut<br />
        <input type="text" id="rut_search_prov" onkeydown="doSearchprov(arguments[0] || event)" />
        <input type="checkbox" id="autosearch_prov" onclick="enableAutosubmitprov(this.checked)" /> Búsqueda automática <br />
    </div>
    <div>
        Nombre<br />
        <input type="text" id="name_search_prov" onkeydown="doSearchprov(arguments[0] || event)" />
        <button onclick="gridReloadprov()" id="submitButtonprov" style="margin-left:30px;">Buscar</button>
    </div>

    <br />
    <table id="providers"></table>
    <div id="providers-pager"></div>
</div>

<div id="dialog-form" title="Agregar Bienes">
        <input type="hidden" id="purchase_id" value="" />
        <div class="h">Buscar Por:</div>
        <div>
                Nombre<br />
                <input type="text" id="name_search" onkeydown="doSearch(arguments[0]||event)" />
                <button onclick="gridReload()" id="submitButton" style="margin-left:30px;">Buscar</button>
        </div>
        <div>		
                <input type="checkbox" id="autosearch" onclick="enableAutosubmit(this.checked)" /> Búsqueda automática <br />
        </div>
        

        <br />
        <table id="bigset"></table>
        <div id="pagerb"></div>
</div>


<div id="dialog-null" title="Comentario">
    <input type="hidden" id="row_id_null" value="" />
    <input type="hidden" id="subgrid_table_id_null" value="" />
    <div>
        <textarea id="comment-null"></textarea>
    </div>
</div>


<div id="dialog-message" title="Mensaje">
    <p>
        <span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
        <div id="message-content"></div>
    </p>
</div>

<input type="hidden" id="expandSubGrid" value="" />