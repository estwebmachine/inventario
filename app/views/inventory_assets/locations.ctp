<script type="text/javascript">

    $(document).ready(function() {

        var regionsGrid = function() {
            $("#regions-list").jqGrid({
                url: '<?php echo $html->url('/regions/indextable') ?>',
                datatype: 'xml',
                mtype: 'GET',
                colNames: ['Id', 'Nombre', 'Creado', 'Modificado'],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'name', index: 'name',editable:true,formoptions: {rowpos: 1, elmprefix: "(*)"}, editrules: {required: true}},
                    {name: 'created', index: 'created', hidden: true},
                    {name: 'region_modified', index: 'region_modified', search: true, searchoptions: {dataInit:
                                    function(element) {
                                        $(element).datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            onSelect: function(dateText, inst) {
                                                var sgrid = $("#regions-list")[0];
                                                sgrid.triggerToolbar();
                                            }
                                        });
                                    }
                        }}
                ],
                pager: '#regions-pager',
                rowNum: 10,
                rowList: [10, 20, 30],
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                caption: '<?php __('Definir Regiones'); ?>',
                editurl: '<?php echo $html->url('/regions/indexedit') ?>',
                //height: 350,
                autowidth: true
            });

            $("#regions-list").jqGrid('navGrid', '#regions-pager', {add: true, edit: true, del:<?php if ($session->read('Auth.User.role') == 0) echo 'true';
else echo 'false'; ?>, search: false}, //options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
            {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
            {sopt: ['eq']} // search options
            );

            $("#regions-list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
        }

        //ciudades
        var citiesGrid = function() {
            $("#cities-list").jqGrid({
                url: '<?php echo $html->url('/cities/indextable') ?>',
                datatype: 'xml',
                mtype: 'GET',
                colNames: ['Id', 'Ciudad', 'Región', 'Creado', 'Modificado'],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'name', index: 'name'},
                    {name: 'region_id', index: 'region_id'},
                    {name: 'created', index: 'created', hidden: true},
                    {name: 'city_modified', index: 'city_modified', search: true, searchoptions: {dataInit:
                                    function(element) {
                                        $(element).datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            onSelect: function(dateText, inst) {
                                                var sgrid = $("#cities-list")[0];
                                                sgrid.triggerToolbar();
                                            }
                                        });
                                    }
                        }}
                ],
                pager: '#cities-pager',
                rowNum: 10,
                rowList: [10, 20, 30],
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                caption: '<?php __('Definir Ciudades'); ?>',
                editurl: '<?php echo $html->url('/cities/indexedit') ?>',
                //height: 350,
                autowidth: true
            });

            $("#cities-list").jqGrid('navGrid', '#cities-pager', {add: false, edit: false, del:<?php if ($session->read('Auth.User.role') == 0) echo 'true';
else echo 'false'; ?>, search: false}, //options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
            {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
            {sopt: ['eq']} // search options
            );

            $("#cities-list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

            $("#cities-list").jqGrid(
                    'navButtonAdd',
                    '#cities-pager',
                    {caption: "",
                        buttonicon: "ui-icon-pencil",
                        onClickButton: function() {
                            var selrow_floor = $("#cities-list").jqGrid('getGridParam', 'selrow');
                            if (selrow_floor != null) {
                                $.ajax({
                                    url: '<?php echo $html->url('/ajax/getparentidil/1'); ?>' + '/' + selrow_floor,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(response) {
                                        var row_data = $("#cities-list").jqGrid('getRowData', selrow_floor);
                                        $("#city_name").val(row_data['name']);
                                        $('#region_id_ac').load(webroot + '/ajax/selectopt/region option', function() {
                                            $(this).val(response.region);
                                        });
                                        $("#dialog-add-city").dialog("open");
                                        $("#oper_city").val('edit');
                                        $("#city_id_dialog").val(selrow_floor);
                                    }
                                });

                            } else {
                                $("#message-content").text("Seleccione registro");
                                $("#dialog-message").dialog("open");
                            }
                        },
                        position: "first"});

            $("#cities-list").jqGrid(
                    'navButtonAdd',
                    '#cities-pager',
                    {caption: "",
                        buttonicon: "ui-icon-plus",
                        onClickButton: function() {
                            $('#region_id_ac').load(webroot + '/ajax/selectopt/region?firstopt=Seleccionar option');
                            $("#city_name").val('');
                            $("#dialog-add-city").dialog("open");
                            $("#oper_city").val('add');
                        },
                        position: "first"});
        }

        //Modal agregar
        $("#dialog-add-city").dialog({
            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,
            buttons: {
                Guardar: function() {
                    var operacity = $("#oper_city").val();
                    var acityid = $("#city_id_dialog").val();
                    var seremi_id = $("#region_id_ac").val();
                    var acity_name = $("#city_name").val();
                    var validate = true;
                    var msg = "";
                    if (seremi_id == '') {
                        validate = false;
                        msg = "Seleccionar Región";
                    } else if (acity_name == '') {
                        validate = false;
                        msg = "Ingrese Ciudad";
                    }
                    if (validate == true) {
                        $.ajax({
                            url: '<?php echo $html->url('/cities/indexedit'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {id: acityid, oper: operacity, region_id: seremi_id, name: acity_name},
                            success: function(response) {
                                if (response.result == 'success') {
                                    $("#dialog-add-city").dialog('close');
                                    //Ocultar todos los campos de formulario modal
                                    $("#cities-list").trigger('reloadGrid');
                                } else {
                                    $("#message-content").text(response.msg);
                                    $("#dialog-message").dialog("open");
                                }
                            }
                        });
                    } else {
                        $("#message-content").text(msg);
                        $("#dialog-message").dialog("open");
                    }
                },
                Cancelar: function() {
                    $(this).dialog("close");
                }
            }
        });

        //direcciones
        var addressesGrid = function() {
            $("#addresses-list").jqGrid({
                url: '<?php echo $html->url('/addresses/indextable') ?>',
                datatype: 'xml',
                mtype: 'GET',
                colNames: ['Id', 'Dirección', 'Ciudad', 'Creado', 'Modificado'],
                colModel: [
                    {name: 'id', index: 'id',  hidden: true},
                    {name: 'name', index: 'name'},
                    {name: 'city_id', index: 'city_id'},
                    {name: 'created', index: 'created', hidden: true},
                    {name: 'address_modified', index: 'address_modified', editable: false, search: true, searchoptions: {dataInit:
                                    function(element) {
                                        $(element).datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            onSelect: function(dateText, inst) {
                                                var sgrid = $("#addresses-list")[0];
                                                sgrid.triggerToolbar();
                                            }
                                        });
                                    }
                        }}
                ],
                pager: '#addresses-pager',
                rowNum: 10,
                rowList: [10, 20, 30],
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                caption: '<?php __('Definir Direcciones'); ?>',
                editurl: '<?php echo $html->url('/addresses/indexedit') ?>',
                //height: 350,
                autowidth: true
            });

            $("#addresses-list").jqGrid('navGrid', '#addresses-pager', {add: false, edit: false, del:<?php if ($session->read('Auth.User.role') == 0) echo 'true';
else echo 'false'; ?>, search: false}, //options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
            {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
            {sopt: ['eq']} // search options
            );

            $("#addresses-list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

            $("#addresses-list").jqGrid(
                    'navButtonAdd',
                    '#addresses-pager',
                    {caption: "",
                        buttonicon: "ui-icon-pencil",
                        onClickButton: function() {
                            var selrow_address = $("#addresses-list").jqGrid('getGridParam', 'selrow');
                            if (selrow_address != null) {
                                $.ajax({
                                    url: '<?php echo $html->url('/ajax/getparentidil/2'); ?>' + '/' + selrow_address,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(response) {
                                        var row_data = $("#addresses-list").jqGrid('getRowData', selrow_address);
                                        $("#address_name").val(row_data['name']);
                                        $('#region_id_ad').load(webroot + '/ajax/selectopt/region option', function() {
                                            $(this).val(response.region);
                                        });
                                        $("#city_id_ad").load(webroot + '/ajax/selectopt/city?region_id=' + response.region + ' option', function() {
                                            $(this).val(response.city);
                                        });
                                        $("#dialog-add-address").dialog("open");
                                        $("#oper_address").val('edit');
                                        $("#address_id_dialog").val(selrow_address);
                                    }
                                });

                            } else {
                                $("#message-content").text("Seleccione registro");
                                $("#dialog-message").dialog("open");
                            }
                        },
                        position: "first"});

            $("#addresses-list").jqGrid(
                    'navButtonAdd',
                    '#addresses-pager',
                    {caption: "",
                        buttonicon: "ui-icon-plus",
                        onClickButton: function() {
                            $('#region_id_ad').load(webroot + '/ajax/selectopt/region?firstopt=Seleccionar option');
                            $("#city_id_ad").load(webroot + '/ajax/selectopt/city?firstopt=Seleccionar&region_id=0 option');
                            $("#address_name").val('');
                            $("#dialog-add-address").dialog("open");
                            $("#oper_address").val('add');
                        },
                        position: "first"});
        }

        //Modal agregar direccion
        $("#dialog-add-address").dialog({
            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,
            buttons: {
                Guardar: function() {
                    var operaddress = $("#oper_address").val();
                    var addressid = $("#address_id_dialog").val();
                    var acity_id = $("#city_id_ad").val();
                    var address_name = $("#address_name").val();
                    var validate = true;
                    var msg = "";
                    if ($("#region_id_ad").val() == '') {
                        validate = false;
                        msg = "Seleccione Región";
                    } else if (acity_id == '') {
                        validate = false;
                        msg = "Seleccione Ciudad";
                    } else if (address_name == '') {
                        validate = false;
                        msg = "Ingrese Dirección";
                    }

                    if (validate) {
                        $.ajax({
                            url: '<?php echo $html->url('/addresses/indexedit'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {id: addressid, oper: operaddress, city_id: acity_id, name: address_name},
                            success: function(response) {
                                if (response.result == 'success') {
                                    $("#dialog-add-address").dialog('close');
                                    //Ocultar todos los campos de formulario modal
                                    $("#addresses-list").trigger('reloadGrid');
                                } else {
                                    $("#message-content").text(response.msg);
                                    $("#dialog-message").dialog("open");
                                }
                            }
                        });
                    } else {
                        $("#message-content").text(msg);
                        $("#dialog-message").dialog("open");
                    }
                },
                Cancelar: function() {
                    $(this).dialog("close");
                    //Ocultar todos los campos de formulario modal
                }
            }
        });

        $("#region_id_ad").live('change', function() {
            var seremi_id = $(this).val();
            if (seremi_id == '' || seremi_id == null) {
                $("#city_id_ad").html('<option value>Seleccionar</option>');
                $("#city_id_ad").trigger('change');
            } else {
                $("#city_id_ad").load(webroot + '/ajax/selectopt/city?region_id=' + $("#region_id_ad").val() + '&firstopt=Seleccionar option', function() {
                    $("#city_id_ad").trigger('change');
                });
            }
        });

        //pisos
        var floorsGrid = function() {
            $("#floors-list").jqGrid({
                url: '<?php echo $html->url('/floors/indextable') ?>',
                datatype: 'xml',
                mtype: 'GET',
                colNames: ['Id', 'Piso', 'Dirección', 'Creado', 'Modificado'],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'number', index: 'number'},
                    {name: 'address_id', index: 'address_id'},
                    {name: 'created', index: 'created', hidden: true},
                    {name: 'floor_modified', index: 'floor_modified', search: true, searchoptions: {dataInit:
                                    function(element) {
                                        $(element).datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            onSelect: function(dateText, inst) {
                                                var sgrid = $("#floors-list")[0];
                                                sgrid.triggerToolbar();
                                            }
                                        });
                                    }
                        }}
                ],
                pager: '#floors-pager',
                rowNum: 10,
                rowList: [10, 20, 30],
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                caption: '<?php __('Definir Pisos'); ?>',
                editurl: '<?php echo $html->url('/floors/indexedit') ?>',
                //height: 350,
                autowidth: true
            });

            $("#floors-list").jqGrid('navGrid', '#floors-pager', {add: false, edit: false, del:<?php if ($session->read('Auth.User.role') == 0) echo 'true';
else echo 'false'; ?>, search: false}, //options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
            {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
            {sopt: ['eq']} // search options
            );

            $("#floors-list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

            $("#floors-list").jqGrid(
                    'navButtonAdd',
                    '#floors-pager',
                    {caption: "",
                        buttonicon: "ui-icon-pencil",
                        onClickButton: function() {
                            var selrow_floor = $("#floors-list").jqGrid('getGridParam', 'selrow');
                            if (selrow_floor != null) {
                                $.ajax({
                                    url: '<?php echo $html->url('/ajax/getparentidil/3'); ?>' + '/' + selrow_floor,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(response) {
                                        var row_data = $("#floors-list").jqGrid('getRowData', selrow_floor);
                                        $("#floor_name").val(row_data['number']);
                                        $('#region_id').load(webroot + '/ajax/selectopt/region option', function() {
                                            $(this).val(response.region);
                                        });
                                        $("#city_id").load(webroot + '/ajax/selectopt/city?region_id=' + response.region + ' option', function() {
                                            $(this).val(response.city);
                                        });
                                        $('#address_id').load(webroot + '/ajax/selectopt/address?city_id=' + response.city + ' option', function() {
                                            $(this).val(response.address);
                                        });
                                        $("#dialog-add-floor").dialog("open");
                                        $("#oper_floor").val('edit');
                                        $("#floor_id_dialog").val(selrow_floor);
                                    }
                                });

                            } else {
                                $("#message-content").text("Seleccione registro");
                                $("#dialog-message").dialog("open");
                            }
                        },
                        position: "first"});

            $("#floors-list").jqGrid(
                    'navButtonAdd',
                    '#floors-pager',
                    {caption: "",
                        buttonicon: "ui-icon-plus",
                        onClickButton: function() {
                            $('#region_id').load(webroot + '/ajax/selectopt/region?firstopt=Seleccionar option');
                            $('#city_id').load(webroot + '/ajax/selectopt/city?firstopt=Seleccionar&region_id=0 option');
                            $('#address_id').load(webroot + '/ajax/selectopt/address?firstopt=Seleccionar&city_id=0 option');
                            $("#floor_name").val('');
                            $("#dialog-add-floor").dialog("open");
                            $("#oper_floor").val('add');
                        },
                        position: "first"});
        }



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

        //Modal agregar piso
        $("#dialog-add-floor").dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            modal: true,
            buttons: {
                Guardar: function() {
                    var operfloor = $("#oper_floor").val();
                    var floorid = $("#floor_id_dialog").val();
                    var address_id = $("#address_id").val();
                    var floor_name = $("#floor_name").val();
                    var validate = true;
                    var msg = "";

                    if ($("#seremi_id").val() == '') {
                        validate = false;
                        msg = "Seleccione Región";
                    } else if ($("#acity_id").val() == '') {
                        validate = false;
                        msg = "Seleccione Ciudad";
                    } else if (address_id == '') {
                        validate = false;
                        msg = "Seleccione Dirección";
                    } else if (floor_name == '') {
                        validate = false;
                        msg = "Ingrese Piso";
                    }

                    if (validate) {
                        $.ajax({
                            url: '<?php echo $html->url('/floors/indexedit'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {id: floorid, oper: operfloor, address_id: address_id, number: floor_name},
                            success: function(response) {
                                if (response.result == 'success') {
                                    $("#dialog-add-floor").dialog('close');
                                    //Ocultar todos los campos de formulario modal
                                    $("#floors-list").trigger('reloadGrid');
                                } else {
                                    $("#message-content").text(response.msg);
                                    $("#dialog-message").dialog("open");
                                }
                            }
                        });
                    } else {
                        $("#message-content").text(msg);
                        $("#dialog-message").dialog("open");
                    }
                },
                Cancelar: function() {
                    $(this).dialog("close");
                    //Ocultar todos los campos de formulario modal
                }
            }
        });
        
        var officesGrid = function() {
            $("#offices-list").jqGrid({
                url: '<?php echo $html->url('/offices/indextable') ?>',
                datatype: 'xml',
                mtype: 'GET',
                colNames: ['Id', 'Número', 'Responsable', 'Piso', 'Creado', 'Modificado'],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'number', index: 'number'},
                    {name: 'user_id', index: 'user_id'},
                    {name: 'floor_id', index: 'floor_id'},
                    {name: 'created', index: 'created', hidden: true},
                    {name: 'offices_modified', index: 'offices_modified', search: true, searchoptions: {dataInit:
                                    function(element) {
                                        $(element).datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            onSelect: function(dateText, inst) {
                                                var sgrid = $("#offices-list")[0];
                                                sgrid.triggerToolbar();
                                            }
                                        });
                                    }
                        }}
                ],
                pager: '#offices-pager',
                rowNum: 10,
                rowList: [10, 20, 30],
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                caption: '<?php __('Definir Oficinas'); ?>',
                editurl: '<?php echo $html->url('/offices/indexedit') ?>',
                //height: 350,
                autowidth: true
            });

            $("#offices-list").jqGrid('navGrid', '#offices-pager', {add: false, edit: false, del:<?php if ($session->read('Auth.User.role') == 0) echo 'true';
else echo 'false'; ?>, search: false}, //options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
            {modal: true, width: 350, height: 'auto', reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
            {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
            {sopt: ['eq']} // search options
            );

            $("#offices-list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

            $("#offices-list").jqGrid(
                    'navButtonAdd',
                    '#offices-pager',
                    {caption: "",
                        buttonicon: "ui-icon-pencil",
                        onClickButton: function() {
                            var selrow_room = $("#offices-list").jqGrid('getGridParam', 'selrow');
                            if (selrow_room != null) {
                                $.ajax({
                                    url: '<?php echo $html->url('/ajax/getparentidil/4'); ?>' + '/' + selrow_room,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(response) {
                                        var row_data = $("#offices-list").jqGrid('getRowData', selrow_room);
                                        $("#office_name").val(row_data['number']);
                                        $('#region_id_ro').load(webroot + '/ajax/selectopt/region option', function() {
                                            $(this).val(response.region);
                                        });
                                        $("#city_id_ro").load(webroot + '/ajax/selectopt/city?region_id=' + response.region + ' option', function() {
                                            $(this).val(response.city);
                                        });
                                        $('#address_id_ro').load(webroot + '/ajax/selectopt/address?city_id=' + response.city + ' option', function() {
                                            $(this).val(response.address);
                                        });
                                        $('#floor_id_ro').load(webroot + '/ajax/selectopt/floor?address_id=' + response.address + '&label=number option', function() {
                                            $(this).val(response.floor);
                                        });
                                        $("#name_search_resp").val(row_data['user_id']);
                                        gridReloadresp();

                                        $("#dialog-add-office").dialog("open");
                                        $("#oper_office").val('edit');
                                        $("#office_id_dialog").val(selrow_room);
                                    }
                                });

                            } else {
                                $("#message-content").text("Seleccione registro");
                                $("#dialog-message").dialog("open");
                            }
                        },
                        position: "first"});

            $("#offices-list").jqGrid(
                    'navButtonAdd',
                    '#offices-pager',
                    {caption: "",
                        buttonicon: "ui-icon-plus",
                        onClickButton: function() {
                            $("#name_search_resp").val('');
                            $('#region_id_ro').load(webroot + '/ajax/selectopt/region?firstopt=Seleccionar option');
                            $('#city_id_ro').load(webroot + '/ajax/selectopt/city?region_id=0&firstopt=Seleccionar option');
                            $('#address_id_ro').load(webroot + '/ajax/selectopt/address?city_id=0&firstopt=Seleccionar option');
                            $('#floor_id_ro').load(webroot + '/ajax/selectopt/floor?address_id=0&firstopt=Seleccionar option');
                            $("#office_name").val('');
                            $("#dialog-add-office").dialog("open");
                            $("#oper_office").val('add');
                        },
                        position: "first"});
        }

        //Modal agregar room
        $("#dialog-add-office").dialog({
            autoOpen: false,
            height: 500,
            width: 500,
            modal: true,
            buttons: {
                Guardar: function() {
                    var operroom = $("#oper_office").val();
                    var roomid = $("#office_id_dialog").val();
                    var floor_id = $("#floor_id_ro").val();
                    var room_name = $("#office_name").val();
                    var user = $("#resp").jqGrid('getGridParam', 'selrow');
                    if (user == null) {
                        user = '';
                    }
                    var validate = true;
                    var msg = "";

                    if ($("#region_id_ro").val() == '') {
                        validate = false;
                        msg = "Seleccione Región";
                    } else if ($("#city_id_ro").val() == '') {
                        validate = false;
                        msg = "Seleccione Ciudad";
                    } else if ($("#address_id_ro").val() == '') {
                        validate = false;
                        msg = "Seleccione Dirección";
                    } else if (floor_id == '') {
                        validate = false;
                        msg = "Seleccione Piso";
                    } else if (room_name == '') {
                        validate = false;
                        msg = "Ingrese Oficina";
                    } else if (user == '') {
                        validate = false;
                        msg = "Seleccione Responsable";
                    }
                    

                    if (validate) {
                        $.ajax({
                            url: '<?php echo $html->url('/offices/indexedit'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {id: roomid, user_id: user, oper: operroom, floor_id: floor_id, number: room_name},
                            success: function(response) {
                                if (response.result == 'success') {
                                    $("#dialog-add-office").dialog('close');
                                    $("#resp").jqGrid('resetSelection');
                                    $("#offices-list").trigger('reloadGrid');
                                } else {
                                    $("#message-content").text(response.msg);
                                    $("#dialog-message").dialog("open");
                                }
                            }
                        });
                    } else {
                        $("#message-content").text(msg);
                        $("#dialog-message").dialog("open");
                    }
                },
                Cancelar: function() {
                    $(this).dialog("close");
                    $("#resp").jqGrid('resetSelection');
                }
            }
        });

        //Al seleccionar urs se actualiza listado de ciudadaes
        $("#region_id_ro").change(function() {
            var seremi_id = $(this).val();
            if (seremi_id == '') {
                $("#city_id_ro").html('<option value>Seleccionar</option>');
                $("#city_id_ro").trigger('change');
            } else {
                $("#city_id_ro").load(webroot + '/ajax/selectopt/city?region_id=' + $("#region_id_ro").val() + '&firstopt=Seleccionar option', function() {
                    $("#city_id_ro").trigger('change');
                });
            }

        });

        $("#city_id_ro").live('change', function() {
            var acity_id = $(this).val();
            if (acity_id == '' || acity_id == null) {
                $("#address_id_ro").html('<option value>Seleccionar</option>');
                $("#address_id_ro").trigger('change');
            } else {
                $("#address_id_ro").load(webroot + '/ajax/selectopt/address?city_id=' + $("#city_id_ro").val() + '&firstopt=Seleccionar option', function() {
                    $("#address_id_ro").trigger('change');
                });
            }
        });

        $("#address_id_ro").live('change', function() {
            var address_id = $(this).val();
            if (address_id == '' || address_id == null) {
                $("#floor_id_ro").html('<option value>Seleccionar</option>');
                $("#floor_id_ro").trigger('change');
            } else {
                $("#floor_id_ro").load(webroot + '/ajax/selectopt/floor?address_id=' + $("#address_id_ro").val() + '&label=number&firstopt=Seleccionar option', function() {
                    $("#floor_row_ro").show();
                    $("#floor_id_ro").trigger('change');
                });
            }
        });


        //tabs
        var initialized = [false, false, false, false, false];
        $('#tabs').tabs({
            show: function(event, ui) {
                if (ui.index == 0 && !initialized[0]) {
                    regionsGrid();
                } else if (ui.index == 1 && !initialized[1]) {
                    citiesGrid();
                } else if (ui.index == 2 && !initialized[2]) {
                    addressesGrid();
                } else if (ui.index == 3 && !initialized[3]) {
                    floorsGrid();
                } else if (ui.index == 4 && !initialized[4]) {
                    officesGrid();
                }
                initialized[ ui.index ] = true;
            },
            select: function(event, ui) {
//                if (ui.index == 3) {
//                    $('#region_id').load(webroot + '/ajax/selectopt/region?firstopt=Seleccionar option');
//                }
            }
        });

        //Al seleccionar urs se actualiza listado de ciudadaes
        $("#region_id").change(function() {
            var region_id = $(this).val();
            if (region_id == '') {
                $("#city_id").html('<option value>Seleccionar</option>');
                $("#city_id").trigger('change');
            } else {
                $("#city_id").load(webroot + '/ajax/selectopt/city?region_id=' + $("#region_id").val() + '&firstopt=Seleccionar option', function() {
                    $("#city_row").show();
                    $("#city_id").trigger('change');
                });
            }

        });

        $("#city_id").live('change', function() {
            var city_id = $(this).val();
            if (city_id == '' || city_id == null) {
                $("#address_id").html('<option value>Seleccionar</option>');
                $("#address_id").trigger('change');
            } else {
                $("#address_id").load(webroot + '/ajax/selectopt/address?city_id=' + $("#city_id").val() + '&firstopt=Seleccionar option', function() {
                    $("#address_row").show();
                    $("#address_id").trigger('change');
                });
            }
        });

        $("#resp").jqGrid({
            url: '<?php echo $html->url('/users/search/2') ?>',
            datatype: "xml",
            height: 200,
            colNames: ['Id', 'Rut','Nombres', 'Apellido Paterno', 'Apellido Materno'],
            colModel: [
                {name: 'id', index: 'id', width: 30, hidden: true},
                {name:'rut', index:'rut'},
                {name: 'names', index: 'names', width: 150},
                {name: 'primary_last_name', index: 'primary_last_name', width: 150},
                {name: 'second_last_name', index: 'secpnd_last_name', width: 150}
            ],
            rowNum: 8,
            //rowList:[10,20,30],
            mtype: "GET",
            pager: $('#resp-pager'),
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
        });

    });
//responsables
    var timeoutHndresp;
    var flAutoresp = false;

    function gridReloadresp() {
        var nm_mask = $("#name_search_resp").val();
        $("#resp").jqGrid('setGridParam', {url: "<?php echo $html->url('/users/search') . "/?nm_mask="; ?>" + nm_mask, page: 1}).trigger("reloadGrid");
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

</script>

<div id="tabs">
    <ul>
        <li><a href="#regions"><?php __('Regiones'); ?></a></li>
        <li><a href="#cities"><?php __('Ciudades'); ?></a></li>
        <li><a href="#addresses"><?php __('Direcciones'); ?></a></li>
        <li><a href="#floors"><?php __('Pisos'); ?></a></li>
        <li><a href="#offices"><?php __('Oficinas'); ?></a></li>

    </ul>
    <div id="regions">
        <table id="regions-list"></table>
        <div id="regions-pager"></div>
    </div>

    <div id="floors">
        <table id="floors-list"></table>
        <div id="floors-pager"></div>
    </div>
    <div id="offices">
        <table id="offices-list"></table>
        <div id="offices-pager"></div>
    </div>

    <div id="cities">
        <table id="cities-list"></table>
        <div id="cities-pager"></div>
    </div>
    <div id="addresses">
        <table id="addresses-list"></table>
        <div id="addresses-pager"></div>
    </div>
</div>
<div id="dialog-add-floor" title="Agregar Registro">
    <input type="hidden" id="oper_floor" />
    <input type="hidden" id="floor_id_dialog" />
    <table>
        <tr>
            <td width="30%">Región</td>
            <td width="70%">
                <select id="region_id">

                </select>
            </td>
        </tr>
        <tr id="acity_row">
            <td width="30%">Ciudad</td>
            <td width="70%">
                <select id="city_id"> 
                </select>
            </td>
        </tr>
        <tr id="address_row">
            <td width="30%">Dirección</td>
            <td width="70%">
                <select id="address_id">

                </select>
            </td>
        </tr>
        <tr id="floor_row">
            <td width="30%">Piso</td>
            <td width="70%">
                <input type="text" id="floor_name" />
            </td>
        </tr>
    </table>
</div>

<div id="dialog-message" title="Mensaje">
    <p>
        <span class="ui-icon ui-icon-circle-close" style="float:left; margin:0 7px 50px 0;"></span>
    <div id="message-content"></div>
</p>
</div>

<div id="dialog-add-city" title="Agregar Registro">
    <input type="hidden" id="oper_city" />
    <input type="hidden" id="city_id_dialog" />
    <table>
        <tr>
            <td width="30%">Región</td>
            <td width="70%">
                <select id="region_id_ac">

                </select>
            </td>
        </tr>
        <tr id="city_row_ac">
            <td width="30%">Ciudad</td>
            <td width="70%">
                <input type="text" id="city_name" />
            </td>
        </tr>
    </table>
</div>

<div id="dialog-add-address" title="Agregar Registro">
    <input type="hidden" id="oper_address" />
    <input type="hidden" id="address_id_dialog" />
    <table>
        <tr>
            <td width="30%">Región</td>
            <td width="70%">
                <select id="region_id_ad">

                </select>
            </td>
        </tr>
        <tr id="city_row_ad">
            <td width="30%">Ciudad</td>
            <td width="70%">
                <select id="city_id_ad"> 
                </select>
            </td>
        </tr>
        <tr id="address_row_ad">
            <td width="30%">Dirección</td>
            <td width="70%">
                <input type="text" id="address_name" />
            </td>
        </tr>
    </table>
</div>

<div id="dialog-add-office" title="Agregar Registro">
    <input type="hidden" id="oper_office" />
    <input type="hidden" id="office_id_dialog" />
    <table>
        <tr>
            <td width="30%">Región</td>
            <td width="70%">
                <select id="region_id_ro">

                </select>
            </td>
        </tr>
        <tr id="city_row_ro">
            <td width="30%">Ciudad</td>
            <td width="70%">
                <select id="city_id_ro"> 
                </select>
            </td>
        </tr>
        <tr id="address_row_ro">
            <td width="30%">Dirección</td>
            <td width="70%">
                <select id="address_id_ro">

                </select>
            </td>
        </tr>
        <tr id="floor_row_ro">
            <td width="30%">Piso</td>
            <td width="70%">
                <select id="floor_id_ro">

                </select>
            </td>
        </tr>
        <tr id="office_row_ro">
            <td width="30%">Oficina</td>
            <td width="70%">
                <input type="text" id="office_name" />
            </td>
        </tr>
    </table>
    <div id='responsable'>		
        Responsable:<br />

        <div>
            Nombre<br />
            <input type="text" id="name_search_resp" onkeydown="doSearchresp(arguments[0] || event)" />
            <button onclick="gridReloadresp()" id="submitButtonresp" style="margin-left:30px;">Buscar</button> <br />
            <input type="checkbox" id="autosearch_resp" onclick="enableAutosubmitresp(this.checked)" /> Búsqueda automática <br />
        </div>

        <br />
        <table id="resp"></table>
        <div id="resp-pager"></div>
    </div>
</div>