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
		<?php __('Ministerio de Justicia - Sistema de Recursos'); ?>
	</title>
	<?php echo $html->meta('icon'); ?>
	<?php
		echo $html->css( array( 'style', 'paymentOrder' ) );
		echo $html->css($user_theme . '/jquery-ui-1.8.6.custom');
		echo $html->css('ui.jqgrid');

		$webroot_string = "var webroot = '" . $this->base . "';";
		$wroot_string = "var wroot = '" . $this->webroot . "';";
		$jsession = $session->read('Jsession');
		$jsession = (is_array($jsession))? $jsession : array();
		$jsession = 'var jsession_data = ' . json_encode($jsession) . ';';
		$theme = 'var theme = "' . $user_theme . '";';
		echo $javascript->codeBlock($webroot_string . $wroot_string . $jsession . $theme);
		echo $javascript->link(array('jquery-1.4.2.min', 'jquery-ui-1.8.6.custom.min', 'jquery.ui.datepicker-es', 'jquery.layout.min', 'i18n/grid.locale-sp', 'jquery.jqGrid.min', 'general', 'themeswitchertool'));
		echo $javascript->link('layout-default');
		$customjs = strtolower( $this->name . '_' . $this->action );
		if(file_exists( JS . $customjs . '.js' )) echo $javascript->link($customjs);
		echo $scripts_for_layout;
	?>
</head>
<body>
<?php echo $content_for_layout; ?>
