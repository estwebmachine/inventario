<div id="login">
<?php
	echo $html->link($html->image('login.jpg', array('alt'=> __('Ministerio de Justicia - Sistema de Bodega', true), 'border'=>"0")), '/', array('target'=>'_self'), null, false);
	echo $form->create('User', array('action' => 'login'));
	echo $form->input('username', array('label' => __('Nombre de Usuario', true)) );
	echo $form->input('password', array('label' => __('Contraseña', true)) );
	echo $form->end(__('Ingresar', true));
?>
</div>