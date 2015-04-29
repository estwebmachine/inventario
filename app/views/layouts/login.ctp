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
		echo $html->css(array('style'));
		echo $javascript->link(array('jquery-1.4.2.min', 'jquery-ui-1.8.custom.min', 'jquery.layout.min'));
		echo $javascript->link('layout-login');
		$customjs = strtolower( $this->name . '_' . $this->action );
		if(file_exists( JS . $customjs . '.js' )) echo $javascript->link($customjs);
		echo $scripts_for_layout;
	?>
</head>
<body>	
	<div id="header" class="ui-layout-north">
		<h1><?php echo $html->image('top_app_logo.png', array('alt' => __('MDS', true)) ); ?><?php echo $html->image('top_app_title.png', array('alt' => __('Sistema de Recursos Físicos', true)) ); ?></h1>
		<div id="version"><?php echo $version; ?></div>
	</div>
	<div id="content-login" class="ui-layout-center">

		<?php
			$session->flash();
			$session->flash('auth');
		?>

		<?php echo $content_for_layout; ?>

	</div>
	<div id="footer" class="ui-layout-south">
		<?php echo $html->link(
				$html->image('powered.png', array('alt'=> __("WebMachine", true), 'border'=>"0")),
				'http://www.webmachine.cl/',
				array('target'=>'_blank'), null, false
			);
		?>
		<?php echo $cakeDebug; ?>
	</div>
</body>
</html>