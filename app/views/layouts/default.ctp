<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
 _    _      _    ___  ___           _     _            
| |  | |    | |   |  \/  |          | |   (_)           
| |  | | ___| |__ | .  . | __ _  ___| |__  _ _ __   ___ 
| |/\| |/ _ \ '_ \| |\/| |/ _` |/ __| '_ \| | '_ \ / _ \
\  /\  /  __/ |_) | |  | | (_| | (__| | | | | | | |  __/
 \/  \/ \___|_.__/\_|  |_/\__,_|\___|_| |_|_|_| |_|\___|
 
 						Web Design.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('MDS - Sistema de Recursos Físicos'); ?>
	</title>
	<?php echo $html->meta('icon'); ?>
	<?php
		echo $html->css('style');
		echo $html->css($user_theme . '/jquery-ui-1.8.6.custom');
		echo $html->css('ui.jqgrid');

		$webroot_string = "var webroot = '" . $this->base . "';";
		$wroot_string = "var wroot = '" . $this->webroot . "';";
		$jsession = $session->read('Jsession');
		$jsession = (is_array($jsession))? $jsession : array();
		$jsession = 'var jsession_data = ' . json_encode($jsession) . ';';
		$theme = 'var theme = "' . $user_theme . '";';
		echo $javascript->codeBlock($webroot_string . $wroot_string . $jsession . $theme);
		echo $javascript->link(array('jquery-1.7.2.min', 'jquery.Rut', 'jquery.validate', 'Utilitarios', 'jquery.selectboxes', 'jquery-ui-1.8.6.custom.min', 'jquery.ui.datepicker-es', 'jquery.layout.min', 'i18n/grid.locale-sp', 'jquery.jqGrid.min', 'general', 'themeswitchertool', 'jquery.form'));
		
                echo $javascript->link('layout-default');
		$customjs = strtolower( $this->name . '_' . $this->action );
		if(file_exists( JS . $customjs . '.js' )) echo $javascript->link($customjs);
		echo $scripts_for_layout;
	?>
</head>
<body>	
	<div id="header" class="ui-layout-north">
		<h1><?php echo $html->image('top_app_logo.png', array('alt' => __('MDS', true)) ); ?><?php echo $html->image('top_app_title.png', array('alt' => __('Sistema de Recursos Físicos', true)) ); ?></h1>
		<?php 
                    if($session->check('Auth.User')){
                ?>
                  
		<div id="ThemeRoller"></div>
		<div id="top-menu">
			<?php
				echo $html->link(__('Inicio', true), '/', array('rel' => 'ui-icon-home'));
				if ($session->check('Auth.User')) echo $html->link(__('Salir', true), array('controller'=>'users', 'action'=>'logout'), array('rel' => 'ui-icon-key'));
			?>
		</div>
		<div id="current-user"><?php echo $session->read('Auth.User.names') . ' ' . $session->read('Auth.User.primary_last_name') . ' (' . Configure::read('User.roles.' . $session->read('Auth.User.role')) . ')'; ?></div>
		<div id="version"><?php echo $version; ?></div>
                <?php } ?>
	</div>
                <?php 
                    if($session->check('Auth.User')){
                ?>
	<div id="left-menu" class="ui-layout-west">
		<?php echo $this->element('left_menu'); ?>
	</div>
                <?php } ?>
	<div id="content" class="ui-layout-center">
                <?php  echo $this->element('top_navigator'); ?>
		<?php
			$session->flash();
                        echo $this->element('sql_dump');
			$session->flash('auth');
		?>

		<?php echo $content_for_layout; ?>
                <div class="consultas-esteban" style="padding: 50px; font-size: 20px; display: none;">
                    Para Consultas u Observaciones, llamar a <b>Esteban Lillo</b>, fono: <b>88212811</b> o vía email <b>Esteban.lillo@webmachine.cl</b>
                </div>
	</div>
	<div id="footer" class="ui-layout-south">
		<?php			
			echo $html->link(
				$html->image('powered.png', array('alt'=> __("WebMachine", true), 'border'=>"0")),
				'http://www.webmachine.cl/',
				array('target'=>'_blank'), null, false
			);
		?>
                <?php echo $this->element('sql_dump'); ?>
		<?php echo $cakeDebug; ?>
             
	</div>
</body>
</html>