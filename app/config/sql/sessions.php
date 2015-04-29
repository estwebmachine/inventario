<?php
/* SVN FILE: $Id: sessions.php 7 2010-04-22 20:05:27Z webmachine $ */
/*Sessions schema generated on: 2007-11-25 07:11:54 : 1196004714*/
/**
 * This is Sessions Schema file
 *
 * Use it to configure database for Sessions
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
 * @subpackage   minju.app.config.sql
 * @version      $Revision: 7 $
 * @modifiedby   $LastChangedBy: webmachine $
 * @lastmodified $Date: 2010-04-22 16:05:27 -0400 (jue, 22 abr 2010) $
 */
/*
 *
 * Using the Schema command line utility
 * cake schema run create Sessions
 *
 */
class SessionsSchema extends CakeSchema {

	var $name = 'Sessions';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $cake_sessions = array(
			'id' => array('type'=>'string', 'null' => false, 'key' => 'primary'),
			'data' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'expires' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);

}
?>