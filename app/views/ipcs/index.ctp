<script>
    var rowid = '';
    function checkAnho(value){
        var result = '';
	$.ajax({
		url: '<?php echo $html->url('/ipcs/checkAnho/') ?>',
		type: 'POST',
                data:({anho:value,id:rowid}),
		dataType: 'json',
		async: false,
		success: function(response) {
			if(response.result == "success") {
                            result = [true,""];
			}
			else if(response.result == "failure") {
                            result = [false,"El año ya ha sido ingresado"];
			}
		}
	});
	return result;
    }
    $(document).ready(function(){
            $("#list").jqGrid({
			url:'<?php echo $html->url('/ipcs/indextable') ?>',
			datatype: 'xml',
			mtype: 'GET',
			colNames:['Id','Valor IPC', 'Año','Creado', 'Modificado'],
			colModel :[
				{name:'id', index:'id', editable:false, hidden:true},
				{name:'value', index:'value', editable:true, formoptions:{rowpos:1, elmprefix:"(*)"}, editrules:{required:true}},
				{name:'date', index:'date', formatoptions: { rowpos:2, elprefix:"(*)" }, editable: true,edittype:'text',editrules:{custom:true,custom_func:checkAnho} },
                                {name:'created', index:'created', hidden:true},
				{name:'modified', index:'modified', editable:false, search:true, searchoptions:{dataInit: 
					function(element){
						$(element).datepicker({
							dateFormat: 'dd/mm/yy',
							onSelect: function(dateText, inst){
								var sgrid = $("#acities-list")[0];
								sgrid.triggerToolbar();
							}
						});
					}
				}}
			],
			pager: '#pager',
			rowNum:10,
			rowList:[10,20,30],
			sortname: 'id',
			sortorder: 'desc',
			viewrecords: true,
			caption: '<?php __('Mantenedor IPC'); ?>',
			editurl: '<?php echo $html->url('/ipcs/indexedit') ?>',
			//height: 350,
			autowidth: true,
                        onSelectRow: function(row_id) {
			rowid = row_id;
                    }
		});

		$("#list").jqGrid('navGrid', '#pager', {add:true, edit:true, del:<?php if($session->read('Auth.User.role') == 0) echo 'true'; else echo 'false'; ?>, search:false}, //options
			{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
			{modal: true, width:350, height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
			{width:350, height:'auto', reloadAfterSubmit:false}, // del options
			{sopt:['eq']} // search options
		);

		$("#list").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
    });
</script>
<table id="list"></table>
<div id="pager"></div>