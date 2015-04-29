<script type="text/javascript">
    var rowid = '';
    function usernamecheck(value, colname) {
        //llamada ajax para verificar la no exitencia del nombre de usuario
        var result = '';
        $.ajax({
            url: '<?php echo $html->url('/users/usernamecheck/') ?>',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: ({username: value, id: rowid}),
            success: function(response) {
                if (response.result == "success") {
                    result = [true, ""];
                }
                else if (response.result == "failure") {
                    result = [false, "El nombre de usuario ya existe"];
                }
            }
        });
        return result;
    }

    function rutcheck(value, colname){
        var result = [false, 'Rut inválido'];
        if(value.match(/^[1-9]{1}[0-9]{6,}-{1}[1-9K]{1}$/)){
            result=[true,""];
        }
        return result;
    }


    $(document).ready(function() {
    
        function populateDepto(e) {
                var thisval = $(e.target).val();
                $.get(webroot + '/ajax/selectopt/cost_center/?firstopt=Seleccione Departamento&level=2&parent_id=' + thisval, function(data) {
                        var res = $(data).html();
                        $("#department_id").html(res);
                }); // end get
        }

        function populateUnit(e) {
                var thisval = $(e.target).val();
                $.get(webroot + '/ajax/selectopt/cost_center/?firstopt=Seleccione Unidad&level=3&parent_id=' + thisval, function(data) {
                        var res = $(data).html();
                        $("#unit_id").html(res);
                }); // end get
        }

        function resetCostCenter() {
                $("#department_id").html('<option value="">Seleccione Departamento</option>');
                $("#unit_id").html('<option value="">Seleccione Unidad</option>');
        }

        function initEditCostCenter() {
                row = $("#list").jqGrid('getGridParam','selrow');
                row_data = $("#list").getRowData(row);
                section_name = row_data['section_id'].replace(/ /g, '%20');
                $('#department_id').load(webroot + '/ajax/selectopt/cost_center?firstopt=Seleccione%20Departamento&level=2&parent.name=' + section_name + ' option', function() { $(this).setSelected(row_data['department_id']); });

                department_name = row_data['department_id'].replace(/ /g, '%20');
                $('#unit_id').load(webroot + '/ajax/selectopt/cost_center?firstopt=Seleccione%20Unidad&parent.name=' + department_name + ' option', function() { $(this).setSelected(row_data['unit_id']); });
        }
        
        $("#list").jqGrid({
            url: '<?php echo $html->url('/users/indextable') ?>',
            datatype: 'xml',
            mtype: 'GET',
            colNames: ['Id', 'Username', 'Password', 'Rut', 'Nombres', 'Apellido Paterno', 'Apellido Materno', 'Sección', 'Departamento', 'Unidad', 'Responsable', 'Email', 'Perfil', 'Estado', 'Creado', 'Modificado'],
            colModel: [
                {name: 'id', index: 'id', editable: false, hidden: true},
		{name:'username', index:'username', editable:true, formoptions:{rowpos:5, elmprefix:"(*)"}, editrules:{custom:true, custom_func:usernamecheck, required:true}},
                {name:'password', index:'password', editable:true, hidden:true, edittype:'password', formoptions:{rowpos:6, elmprefix:""}, editrules:{edithidden:true, required: false}},                
		//{name: 'username', index: 'username', hidden:true,editable: false, formoptions: {rowpos: 5, elmprefix: "(*)"}, editrules: {custom: true, custom_func: usernamecheck, required: true}},
                //{name: 'password', index: 'password', editable: false, hidden: true, edittype: 'password', formoptions: {rowpos: 6, elmprefix: ""}, editrules: {edithidden: true, required: false}},
                {name: 'rut', index: 'rut', editable: true, formoptions: {rowpos: 1, elmprefix: "(*)",elmsuffix: " Ej: 1111111-K"}, editrules: {custom: true, custom_func: rutcheck, required: true}},
                {name: 'names', index: 'names', editable: true, formoptions: {rowpos: 2, elmprefix: "(*)"}, editrules: {required: true},search:true},
                {name: 'primary_last_name', index: 'primary_last_name', editable: true, formoptions: {rowpos: 3, elmprefix: "(*)"}, editrules: {required: true}},
                {name: 'second_last_name', index: 'second_last_name', editable: true, formoptions: {rowpos: 4, elmprefix: "(*)"}, editrules: {required: true}},
                {name: 'section_id', index: 'section_id', editable: false, hidden: true, edittype: 'select', formoptions: {rowpos: 10}, editrules: {edithidden: true}, editoptions: {dataUrl: webroot + '/ajax/selectopt/cost_center?level=1&firstopt=Seleccione%20Sección', dataEvents: [{type: 'change', fn: populateDepto}]}},
                {name: 'department_id', index: 'department_id', editable: false, hidden: true, edittype: 'select', formoptions: {rowpos: 11}, editrules: {edithidden: true}, editoptions:{value: ' :Cargando...', dataEvents: [{type: 'change', fn: populateUnit}]}},
                {name: 'unit_id', index: 'unit_id', editable: false, hidden: true, edittype: 'select', formoptions: {rowpos: 12}, editrules: {edithidden: true}, editoptions:{value: ' :Cargando...'}},
                {name: 'user_id', index: 'user_id', editable: false,hidden:true},
                {name: 'email', index: 'email', editable: true, formoptions: {rowpos: 7},editrules: {required: true}},
                {name: 'role', index: 'role', editable: true, edittype: 'select', formoptions: {rowpos: 8}, editoptions: {value: "0:Administrador de Sistema;1:Ejecutivo;3:Contabilidad", defaultValue: 'Administrador de Sistema'}, stype: 'select', searchoptions: {value: ":;0:Administrador de Sistema;1:Ejecutivo;3:Contabilidad"}},
                {name: 'is_active', index: 'is_active', editable: true, edittype: 'select', formoptions: {rowpos: 9}, editoptions: {value: "<?php echo $jqgrid->selectOpt('is_active'); ?>", defaultValue: 'Habilitado'}, stype: 'select', searchoptions: {value: ":;<?php echo $jqgrid->selectOpt('is_active'); ?>"}},
                {name: 'created', index: 'created', hidden: true},
                {name: 'modified', index: 'modified', editable: false, search: true, searchoptions: {dataInit:
                                function(element) {
                                    $(element).datepicker({
                                        dateFormat: 'dd/mm/yy',
                                        onSelect: function(dateText, inst) {
                                            var sgrid = $("#list")[0];
                                            sgrid.triggerToolbar();
                                        }
                                    });
                                }
                    }}
            ],
            pager: '#pager',
            rowNum: 10,
            rowList: [10, 20, 30],
            sortname: 'id',
            sortorder: 'desc',
            viewrecords: true,
            caption: '<?php __('Definir Usuarios'); ?>',
            editurl: '<?php echo $html->url('/users/indexedit') ?>',
            //height: 350,
            autowidth: true,
            onSelectRow: function(row_id) {
                rowid = row_id;
            }
        });

        $("#list").jqGrid('navGrid', '#pager', {del: true, search: false}, //options
        {modal: true, width: 500, height: 'auto', reloadAfterSubmit: true, closeAfterEdit: true, afterShowForm: initEditCostCenter, bottominfo: "Campos marcados con (*) son obligatorios"}, // edit options
        {modal: true, width: 500, height: 'auto', beforeShowForm:function(){rowid = '';},afterShowForm: resetCostCenter, reloadAfterSubmit: true, closeAfterAdd: true, bottominfo: "Campos marcados con (*) son obligatorios"}, // add options
        {width: 350, height: 'auto', reloadAfterSubmit: false}, // del options
        {sopt: ['eq']} // search options
        );

        $("#list").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
    });
</script>
<table id="list"></table>
<div id="pager"></div> 
