<?php
/* SVN FILE: $Id: routes.php 7 2010-04-22 20:05:27Z webmachine $ */
/**
 * DESCRIPCION_CORTA
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versión 5
 *
 * WebMachine, Desarrollo Web <http://www.webmachine.cl/>
 * Copyright 2010-2012, WebMachine Ltda.
 * Dominica N° 165 - Recoleta
 * Santiago, Chile
 *
 * @filesource
 * @copyright    Copyright 2010-2012, WebMachine Ltda.
 * @link         http://www.webmachine.cl WebMachine
 * @package      minju
 * @subpackage   minju.app.config
 * @version      $Revision: 7 $
 * @modifiedby   $LastChangedBy: webmachine $
 * @lastmodified $Date: 2010-04-22 16:05:27 -0400 (jue, 22 abr 2010) $
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
	//Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
/*
 * Mis rutas
 */
	Router::connect('/', array('controller' => 'users', 'action' => 'login'));
?>