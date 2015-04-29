<script type="text/javascript">
$(document).ready(function(){
	$('#send').button({ icons: {primary:'ui-icon-document'} });

	$('#send').click(function(){
		$('#userform').submit();
		return false;
	});
});
</script>

<form action="<?php echo $this->base; ?>/users/passchange" method="post" target="_self" id="userform">
	<fieldset>
		<legend>Cambiar Contraseña</legend>
		<label for="data[User][password]">Contraseña:</label>
		<input type="text" name="data[User][password]" id="Password"><br />
		<label for="data[User][passwordconf]">Confirmar Contraseña:</label>
		<input type="text" name="data[User][passwordconf]" id="PasswordConf"><br /><br />
		<a href="#" class="formsend" id="send">Cambiar</a>
	</fieldset>
</form>