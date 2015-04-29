<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
	<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
	<strong>
	<?php if( $session->check('Auth.User') ) echo __('Bienvenido', true) . ' ' . $session->read('Auth.User.name') . ' (' . Configure::read('User.roles.' . $session->read('Auth.User.role')) . ')'; ?>	
	</strong>.</p>
</div>
<?php
	//echo '<pre>' . print_r($session->read('Auth.User'), true) . '</pre>';
?>

<br />
<!--

<script type="text/javascript">
$(document).ready(function(){
	$("#list").jqGrid({
		url:'<?php echo $html->url('/notifications/indextable?user_id=' . $session->read('Auth.User.id')); ?>',
		datatype: 'xml',
		mtype: 'GET',
		colNames:['Id','Tipo', 'Creada','Modificada'],
		colModel :[
			{name:'id', index:'id', hidden:true},
			{name:'type', index:'type', hidden:false},
			{name:'created', index:'created', hidden:false},
			{name:'modified', index:'modified', hidden:false}
		],
		pager: '#pager',
		rowNum:10,
		rowList:[10,20,30],
		sortname: 'id',
		sortorder: 'desc',
		viewrecords: true,
		caption: '<?php __('Notificaciones'); ?>',
		//height: 350,
		autowidth: true
	});

	$("#list").jqGrid('navGrid', '#pager', {edit:false, add:false, del:false, search:false}, //options
		{modal: true, width:'auto', height:'auto', reloadAfterSubmit:true, closeAfterEdit: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // edit options
		{modal: true, width:'auto', height:'auto', reloadAfterSubmit:true, closeAfterAdd: true, bottominfo:"Campos marcados con (*) son obligatorios"}, // add options
		{width:'auto', height:'auto', reloadAfterSubmit:false}, // del options
		{sopt:['eq']} // search options
	);
});
</script>
<table id="list"></table>
<div id="pager"></div>

-->