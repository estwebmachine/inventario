<?php
/* SVN FILE: $Id: bootstrap.php 407 2012-09-25 21:57:26Z caacuna $ */
/**
 * DESCRIPCION_CORTA
 *
 * DESCRIPCION_LARGA
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
 * @version      $Revision: 407 $
 * @modifiedby   $LastChangedBy: caacuna $
 * @lastmodified $Date: 2012-09-25 18:57:26 -0300 (mar, 25 sep 2012) $
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
//EOF


$in_production = !in_array( $_SERVER['HTTP_HOST'], array('localhost', 'localhost:8080', 'dev.webmachine.cl') );
define('IN_PRODUCTION', $in_production);
?>