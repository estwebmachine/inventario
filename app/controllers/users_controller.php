<?php
/* SVN FILE: $Id: users_controller.php 537 2013-09-30 17:13:26Z javier.jara $ */
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
 * @subpackage   minju.app.controllers
 * @version      $Revision: 537 $
 * @modifiedby   $LastChangedBy: javier.jara $
 * @lastmodified $Date: 2013-09-30 13:13:26 -0400 (lun 30 de sep de 2013) $
 */
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('RequestHandler');
        var $uses = array('User','Log');

	function beforeFilter() {
		parent::beforeFilter();
		$this->LdapAuth->allow('login', 'logout', 'jsession','devincular_funcionario');
	}

	/**
	 * Login Usuario
	 */
	function login() {
		if($this->LdapAuth->user()){
                	$this->Session->setFlash('Ya está logueado en el sistema');
                	$this->redirect(array('action' => 'dashboard'));  
             
             	}
            	if( !empty($this->data) && $id = $this->LdapAuth->user('id') ) {
                    	$this->Log->add($id, 0, 'Ingreso al Sistema');
                    	$this->redirect(array('action' => 'dashboard'));
            	}
		
           /* $this->autoRender = false;
            if( !empty($this->data) && $id = $this->LdapAuth->user('id') ) {
                    $this->Log->add($id, 0, 'Ingreso al Sistema');
                    $this->redirect(array('action' => 'dashboard'));
            }
            if($this->LdapAuth->user()) $this->redirect(array('action' => 'dashboard'));
            $sso = Configure::read('SSO');
            $environment = Configure::read('App.environment');
            $aid = Configure::read('App.aid');
            if(isset($this->params['url']['t'])){
                $t = $this->params['url']['t'];
                $result = $this->validar_token($t, $sso[$environment]);
                if(!empty($result)){
                    $data = $this->User->find('first',array('conditions'=>array('User.rut'=> $result)));
                    $this->LdapAuth->login($data);
                    $this->redirect(array('action' => 'dashboard'));
                }else{
                    $this->redirect($sso[$environment].'/error.aspx?id=4');
                }          
            }else{
                $this->redirect($sso[$environment].'?AID='.$aid);
            }*/
	}
        
        function validar_token($t,$url_base){
            App::import('Vendor', 'nusoap/lib/nusoap');
            $client = new nusoap_client($url_base.'/libre/login.asmx?wsdl',true);
            $err = $client->getError();
            if ($err) {
                
                return NULL;
            }
            $proxy = $client->getProxy();
            $result=$proxy->autorizar(array('token'=>$t));
            if ($client->fault) {
                    return NULL;
            } else {
                    // Check for errors
                    $err = $client->getError();
                    if ($err) {
                            // Display the error
                            return NULL;
                    } else {
                            // Display the result
                            if(isset($result['autorizarResult']['Estado'])){
                                if($result['autorizarResult']['Estado'] == 'OK'){//Confirmar descripcion de estados en SAP codigo desvinculado
                                    return $result['autorizarResult']['RUT'];
                                }else{
                                    return 0;
                                }
                            }else{
                                return NULL;
                            }
                    }
            }
        }

	/**
	 * Logout Usuario
	 */
	function logout() {
		$this->redirect($this->LdapAuth->logout());
	}

	/**
	 * Dashboard Usuario
	 */
	function dashboard() {
		if($this->LdapAuth->user('is_active') == 0) { //usuario deshabilitado
			$this->redirect($this->LdapAuth->logout());
		}
	}

	/**
	 * Listado de Usuarios
	 */
	function index() {
		
	}

	/**
	 * Edición de Usuarios
	 */
	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['User'] = $this->params['form'];

		if($action == 'edit')	{
			if($this->data['User']['password'] != '') {
				$this->data['User']['password'] = $this->LdapAuth->password($this->data['User']['password']);
			} else { unset($this->data['User']['password']); }
                        $this->User->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['User']['id']);
			$this->data['User']['password'] = $this->LdapAuth->password($this->data['User']['password']);
                        $this->data['User']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->User->create();
			$this->User->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->User->del($this->data['User']['id']);
		}
	}

	/**
	 * Permite la escritura de datos en session mediante ajax
	 *
	 * @param string $mode modo write
	 */
	function jsession($mode) {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$response = '';

		if($mode == 'write') {
			$data = $this->params['form'];
			$response = false;			
			foreach($data as $key => $value) $response = $this->Session->write('Jsession.' . $key, $value);
			$response = ($response)? 'success' : 'failure' ;
		}
		$this->set('response', $response);
	}

	function test() {
		$this->set('perms', $this->perms());
	}

	function passchange() {
		if(!empty($this->data)) {
			$newpass = $this->data['User']['password'];
			$passconf = $this->data['User']['passwordconf'];
			
			if ($newpass != $passconf) {
				$this->Session->setFlash('Las Contraseñas no coinciden.', 'default', array('class' => 'error'));
				$this->redirect(array('controller' => 'users', 'action'=>'passchange'));
			}
			
			//elimino la confirmacion de password
			unset($this->data['User']['passwordconf']);
			//asigno id de usuario
			$this->data['User']['id'] = $this->LdapAuth->user('id');
			//encripto el nuevo password
			$this->data['User']['password'] = $this->LdapAuth->password($this->data['User']['password']);
			//guardo el nuevo password
			if($this->User->save($this->data)) {
				$this->Session->setFlash('La contraseña ha sido modificada.', 'default', array('class' => 'success'));
				$this->redirect(array('controller' => 'users', 'action'=>'dashboard'));
			}
		}
	}
	
	function usernamecheck() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$output['result'] = 'failure';

		if($this->RequestHandler->isAjax()) {
			$username = $this->params['form']['username'];
			$id = $this->params['form']['id'];
			$user = $this->User->findByUsername($username);
                        $this->log($user,'debug');
			if(empty($user)) $output['result'] = 'success';
			else if($user['User']['id'] == $id) $output['result'] = 'success';
		}
		$this->set('output', $output);
	}
	
	function themechange() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$response = 'failure';

		$data = $this->params['form'];
		$this->User->id = $this->LdapAuth->user('id');
		if($this->User->saveField('theme', $data['theme'])) $response = 'success';

		$this->set('response', $response);		
	}
	
	function view_pdf() {
		$this->layout = 'pdf';
		Configure::write('debug', 0);
		$this->User->Behaviors->attach('Containable');

		$params = array();
		$params['contain'] = array(
			'Position',
			'CostCenter',
			'Warehouse'
		);
		
		$users = $this->User->find('all', $params);
		
		$this->set('users', $users);
		$this->render();
	}
	
	function inventory_responsibles() { //Definir responsables inventario
		
	}
        
        function search($role) {
		$this->layout = null;
		Configure::write('debug', 0);

		$page = $this->params['url']['page']; // get the requested page
		$limit = $this->params['url']['rows']; // get how many rows we want to have into the grid
		$sidx = $this->params['url']['sidx']; // get index row - i.e. user click to sort
		$sord = $this->params['url']['sord']; // get the direction
		if(!$sidx) $sidx =1;

		if(isset($this->params['url']['nm_mask']))
			$nm_mask = $this->params['url']['nm_mask'];
		else
			$nm_mask = "";
		//construct where clause
                
		$where = "WHERE 1=1 AND is_ses = ".$this->LdapAuth->user('is_ses')." AND is_active = 1 AND role NOT IN (0, 3, 1)";
                if(!empty($role)){
                    $where .= ' AND role = '.$role; 
                }
		if($nm_mask != '')
			$where .= " AND (names LIKE '%$nm_mask%' OR rut LIKE '$nm_mask%' OR primary_last_name LIKE '$nm_mask%' OR second_last_name LIKE '$nm_mask%')";
		
		$result = $this->User->query("SELECT COUNT(*) AS count FROM users " . $where);

		$count = $result[0][0]['count'];

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		if ($limit<0) $limit = 0;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start<0) $start = 0;

		$SQL = "SELECT id, names, primary_last_name, second_last_name, rut FROM users " . $where . " ORDER BY $sidx $sord LIMIT $start , $limit";
		$result = $this->User->query($SQL);
		
		$this->set('result', $result);
		$this->set('page', $page);
		$this->set('total_pages', $total_pages);
		$this->set('count', $count);
        }
        
        function desvincular_funcionario(){
            $this->autoRender = false;
            $users = $this->User->find('all',array('recursive'=>-1,'conditions'=>array('User.is_active'=>1, 'User.role'=>array(4,2))));
            foreach ($users as $item) {
                $rut = explode('-', $item['User']['rut']);
                $rut=$rut[0];
                $active = $this->User->is_active_sap($rut);//0:activo.1:desvinculado,-1:error
                if($active == 1){
                    $this->_notification_email($item);
//                    $this->User->id = $item['User']['id'];
//                    $this->User->saveField('is_active',0);
                           
                }
            }
        }
}
?>
