<?php
/* SVN FILE: $Id: app_controller.php 541 2013-10-08 16:44:54Z javier.jara $ */
/**
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
 * @subpackage   minju.app
 * @version      $Revision: 541 $
 * @modifiedby   $LastChangedBy: javier.jara $
 * @lastmodified $Date: 2013-10-08 13:44:54 -0300 (mar 08 de oct de 2013) $
 */
class AppController extends Controller {
	var $helpers = array('Html', 'Session', 'Form', 'Javascript', 'Xml', 'Jqgrid', 'General', 'System');
	var $components = array('LdapAuth', 'Mailer');
        var $uses = array('PurchaseOrder', 'PurchaseOrderDetail');
	function beforeFilter() {
		Configure::load('mds_config');
                $sso = Configure::read('SSO');
                $environment = Configure::read('App.environment');
                $this->LdapAuth->autoRedirect = false;
		$this->LdapAuth->authorize = 'controller';
		$this->LdapAuth->loginError = __('Ingreso fallido. Nombre de usuario o password inválido.', true);
		$this->LdapAuth->authError = __('No está autorizado para ingresar a esa área.', true);
		$this->LdapAuth->loginRedirect = array('controller' => 'users', 'action' => 'dashboard'); 
		$this->LdapAuth->logoutRedirect = array('controller' => 'users', 'action' => 'login'); 
               // $this->LdapAuth->logoutRedirect = $sso[$environment].'/libre/salir.aspx'; 
		if($this->LdapAuth->user()) $this->set('menu', $this->_createMenu());
		$this->set('version', $this->version());
		$this->set('user_theme', $this->user_theme());
		//$this->xml();
	}

	/**
	 * Recupera la versión actual
	 * 
	 * @return string la versión de la aplicación
	 */
	function version() {
		$conf_release = Configure::read('App.version.release');
		$conf_build = Configure::read('App.version.build');
		$release_cycle = 100; //número de revisiones que componen una versión

		if ( !in_array(shell_exec('svnversion'), array('', 'exported')) ) {
			$status = shell_exec('svnversion ' . dirname(__FILE__));
			$build = end( preg_split('/:/', $status) );
			$release = ceil( $build / $release_cycle );
			$modified = strpos($build, 'M');
			$build = $build % $release_cycle;
			if ($modified !== false) $build .= 'M';
			
		} else {
			$build_file = new File(WWW_ROOT . '/revision.txt', true);
			$build = $build_file->read();
			$release = $conf_release;
			$build_file->close();
		}
		if (!$build) {
			$build = $conf_build;
			$release = $conf_release;
		}
		return sprintf('V%s.%s', $release, $build);
	}
	
	function user_theme() {
		$default_theme = Configure::read('App.default_theme');
		App::import('Model', 'User');
		$this->User = new User();
		$tmp_recursive = $this->User->recursive;
		$this->User->recursive = -2;
		$this->User->recursive = $tmp_recursive;
		$user = $this->User->findById($this->LdapAuth->user('id'));		
		$user_theme = $user['User']['theme'];
		if($user_theme != '') return $user_theme;
		return $default_theme;
	}

	function isAuthorized() {
		return $this->perms();
	}

	function perms($controller = null, $action = null) {
		if(!$this->LdapAuth->user()) return false;
		$role = $this->LdapAuth->user('role');
		if(!$controller) $controller = $this->params['controller'];
		if(!$action) $action = strtolower($this->action);
		$user_perms = Configure::read('User.perms.' . $role);
		if($user_perms == '*') return true;
		else if(array_key_exists($controller, $user_perms)) {
			if($user_perms[$controller] == '*') return true;
			else if( in_array($action, $user_perms[$controller]) ) return true;
		}		
		return false;
	}

	function _createMenu() {
		$menu = Configure::read('User.menu');
		//filtro por permisos
		foreach($menu as $section => $subsections) {
			foreach ($subsections as $title => $link) {
				if( !$this->perms($link['controller'], $link['action']) ) unset($menu[$section][$title]);
			}
			if( empty($menu[$section]) ) unset($menu[$section]);
		}
		return $menu;
	}

	function indextable() {
		$this->layout = null;
		Configure::write('debug', 0);
		$model = Inflector::singularize($this->name);
//		App::import('Model',$model);
		$params = array();
		$sparams = array();
		// obtener la página solicitada
		$page = $this->params['url']['page'];
		// obtener cuantas filas queremos tener en el grid - parámetro rowNum en el grid
		$limit = $this->params['url']['rows'];
		// obtener la fila index - i.e. usuario clickea para ordenar. En primera instancia el parámetro sortname -
		// luego de eso el index de colModel
		$sidx = $this->params['url']['sidx'];
		// dirección de ordenamiento - la primera vez sortorder
		$sord = $this->params['url']['sord'];
		//filtros para busqueda multiple
		$filters = array();
		if($this->params['url']['filters']) $filters = json_decode($this->params['url']['filters'], true);

		$search = $this->params['url']['_search'];
                                    
		// si no pasamos la primera vez un index usar la primera columna com index
		if(!$sidx) $sidx = 'id';

		//condiciones de listado
		
		unset($this->params['url']['url'], $this->params['url']['searchField'], $this->params['url']['searchString'], $this->params['url']['searchOper']);
		if(count($this->params['url']) > 0) {
			foreach($this->params['url'] as $url_param => $value) {
				if(in_array($url_param, array('_search', 'nd', 'rows', 'page', 'sidx', 'sord', 'filters', 'action','acta_id'))) continue;
				/**
				 * parametros especiales, si el parametro comienza en @ entonces se almacena en un arreglo $sparams
				 */
				if(strpos($url_param, '@') === 0) {
					$url_param = str_replace('@', '', $url_param);
					$sparams[$url_param] = $value;
					continue;
				}
				if(strpos($url_param, '__') === false)	$params['conditions'][$model . '.' . $url_param] = $value;
				else {
					$split = explode('__', $url_param);
					$thismodel = Inflector::camelize($split[0]);
					$url_param = $split[1];
					$params['conditions'][$thismodel . '.' . $url_param] = $value;
				}
			}
		}
		
		//busqueda sin filtros multiples
		if($search == 'true' and empty($filters)) {
			$searchField = $this->params['url']['searchField'];
			$searchString = $this->params['url']['searchString'];
			$searchOper = $this->params['url']['searchOper'];
			
			$key = $model . '.' . $searchField;
			
			if($model == 'PurchaseOrder') {
				$params['conditions']['PurchaseOrder.user_id'] = $this->LdapAuth->user('id');
				switch($searchField) {
					case 'provider_id':
						$key = 'Provider.rut';
						break;
					case 'date':
						$this->dateToSql($searchString);
						break;
				}
			} elseif($model == 'Transaction') {
				$params['conditions']['Transaction.user_id'] = $this->LdapAuth->user('id');
				switch($searchField) {
					case 'date':
						$this->dateToSql($searchString);
						break;
					case 'purchase_order_id':
						$key = 'PurchaseOrder.order_number';
						break;
					case 'document_date':
						$this->dateToSql($searchString);
						break;
				}
			} 			
			if($searchOper == 'eq') {				
				$params['conditions'][$key] = $searchString;
			}
		} else if($search == 'true' and !empty($filters)) { //busqueda con filtros multiples
			//asigno como key de cada regla el nombre del campo de busqueda
			$rules = array();
			foreach($filters['rules'] as $rule) {
				$field = $rule['field'];
				unset($rule['field']);
				$rules[$field] = $rule;
			}
			$conditions = array();
			
			if($model == 'PurchaseOrder') {
				if(isset($rules['order_number'])) $conditions['PurchaseOrder.order_number LIKE'] = $rules['order_number']['data'] . '%';
				if(isset($rules['provider_rut'])) $conditions['Provider.rut LIKE'] = $rules['provider_rut']['data'] . '%';
				if(isset($rules['provider_name'])) $conditions['Provider.fantasyname LIKE'] = $rules['provider_name']['data'] . '%';
				if(isset($rules['date'])) $conditions['PurchaseOrder.date LIKE'] = $this->dateToSql($rules['date']['data'], false, true) . '%';
                                if(isset($rules['user_id'])){ $conditions['OR']['User.names LIKE'] = '%' . $rules['user_id']['data'] . '%'; $conditions['OR']['User.primary_last_name LIKE'] = '%' . $rules['user_id']['data'] . '%';$conditions['OR']['User.second_last_name LIKE'] = '%' . $rules['user_id']['data'] . '%';}
//                                if(isset($rules['user_id'])){ $user_explode = explode(' ', trim($rules['user_id']['data']));$user_join = implode('|',$user_explode);$conditions['OR']['User.names REGEXP'] = $user_join; $conditions['OR']['User.primary_last_name REGEXP'] = $user_join;$conditions['OR']['User.second_last_name REGEXP'] =  $user_join;}
                                if(isset($rules['status']) and $rules['status']['data'] != ' ') $conditions['PurchaseOrder.status LIKE'] = $rules['status']['data'] . '%';
				if(isset($rules['modified'])) $conditions['PurchaseOrder.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'Transaction') {
				if(isset($rules['date'])) $conditions['Transaction.date LIKE'] = $this->dateToSql($rules['date']['data'], false, true) . '%';
				if(isset($rules['warehouse_id'])) $conditions['Warehouse.name LIKE'] = $rules['warehouse_id']['data'] . '%';
				if(isset($rules['PurchaseOrder.order_number'])) $conditions['PurchaseOrder.order_number LIKE'] = $rules['PurchaseOrder.order_number']['data'] . '%';
//                                if(isset($rules['PurchaseOrder.Provider.rut'])) $conditions['PurchaseOrder.Provider.rut LIKE'] = $rules['PurchaseOrder.Provider.rut']['data'] . '%';
				if(isset($rules['user_id'])) $conditions['User.name LIKE'] = $rules['user_id']['data'] . '%';
if(isset($rules['type']) and $rules['type']['data'] != ' ') $conditions['Transaction.type'] = $rules['type']['data'];				
if(isset($rules['document_type']) and $rules['document_type']['data'] != ' ') $conditions['Transaction.document_type LIKE'] = $rules['document_type']['data'] . '%';
				if(isset($rules['document_number'])) $conditions['Transaction.document_number LIKE'] = $rules['document_number']['data'] . '%';
				if(isset($rules['document_date'])) $conditions['Transaction.document_date LIKE'] = $this->dateToSql($rules['document_date']['data'], false, true) . '%';
				if(isset($rules['status']) and $rules['status']['data'] != ' ') $conditions['Transaction.status LIKE'] = $rules['status']['data'] . '%';
				if(isset($rules['modified'])) $conditions['Transaction.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'Asset') {
				if(isset($rules['name'])) $conditions['Asset.name LIKE'] = '%'.$rules['name']['data'] . '%';
				if(isset($rules['m_class_id'])) $conditions['MClass.name LIKE'] = '%'.$rules['m_class_id']['data'] . '%';
				if(isset($rules['sub_class_id'])) $conditions['SubClass.name LIKE'] = '%'.$rules['sub_class_id']['data'] . '%';
				if(isset($rules['is_active'])) $conditions['Asset.is_active'] = $rules['is_active']['data'] . '%';
				if(isset($rules['modified'])) $conditions['Asset.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'MClass') {
				if(isset($rules['name'])) $conditions['MClass.name LIKE'] = '%'.$rules['name']['data'] . '%';
                                if(isset($rules['is_active'])) $conditions['MClass.is_active LIKE'] = $rules['is_active']['data'] . '%';
				if(isset($rules['modified'])) $conditions['MClass.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'SubClass') {
				if(isset($rules['name'])) $conditions['SubClass.name LIKE'] = '%'.$rules['name']['data'] . '%';
				if(isset($rules['m_class_id'])) $conditions['MClass.name LIKE'] = '%'.$rules['m_class_id']['data'] . '%';
                                if(isset($rules['is_active'])) $conditions['SubClass.is_active'] = $rules['is_active']['data'] . '%';
				if(isset($rules['modified2'])) $conditions['SubClass.modified LIKE'] = $this->dateToSql($rules['modified2']['data'], false, true) . '%';
			}
			if($model == 'CostCenter') {
				if(isset($rules['name'])) $conditions['CostCenter.name LIKE'] = '%'.$rules['name']['data'] . '%';
                                if(isset($rules['code'])) $conditions['CostCenter.code LIKE'] = $rules['code']['data'] . '%';
				if(isset($rules['level'])) $conditions['CostCenter.level LIKE'] = $rules['level']['data'] . '%';
				if(isset($rules['parent_id'])) $conditions['Parent.name LIKE'] = '%'.$rules['parent_id']['data']. '%';
                                if(isset($rules['is_active'])) $conditions['CostCenter.is_active'] = $rules['is_active']['data'] . '%';
                                if(isset($rules['modified'])) $conditions['CostCenter.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'Position') {
				if(isset($rules['name'])) $conditions['Position.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['modified'])) $conditions['Position.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'Provider') {
				if(isset($rules['rut'])) $conditions['Provider.rut LIKE'] = $rules['rut']['data'] . '%';
				if(isset($rules['socialreason'])) $conditions['Provider.socialreason LIKE'] = '%'.$rules['socialreason']['data'] . '%';
				if(isset($rules['fantasyname'])) $conditions['Provider.fantasyname LIKE'] = '%'.$rules['fantasyname']['data'] . '%';
				if(isset($rules['contact_name'])) $conditions['Provider.contact_name LIKE'] = '%'.$rules['contact_name']['data'] . '%';
				if(isset($rules['contact_email'])) $conditions['Provider.contact_email LIKE'] = $rules['contact_email']['data'] . '%';
				if(isset($rules['observation'])) $conditions['Provider.observation LIKE'] = '%'.$rules['observation']['data'] . '%';
				if(isset($rules['is_active'])) $conditions['Provider.is_active LIKE'] = $rules['is_active']['data'] . '%';
				if(isset($rules['modified'])) $conditions['Provider.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
				if(isset($rules['contact_phone'])) $conditions['Provider.contact_phone LIKE'] = $rules['contact_phone']['data'] . '%';
			}
			if($model == 'Region') {
				if(isset($rules['name'])) $conditions['Region.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['modified'])) $conditions['Region.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'City') {
				if(isset($rules['name'])) $conditions['City.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['region_id'])) $conditions['Region.name LIKE'] = $rules['region_id']['data'] . '%';
				if(isset($rules['modified'])) $conditions['City.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'Commune') {
				if(isset($rules['name'])) $conditions['Commune.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['city_id'])) $conditions['City.name LIKE'] = $rules['city_id']['data'] . '%';
				if(isset($rules['modified'])) $conditions['Commune.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
			}
			if($model == 'User') {
                                if(isset($rules['rut'])) $conditions['User.rut LIKE'] = $rules['rut']['data'] . '%';
				if(isset($rules['names'])) $conditions['User.names LIKE'] = '%'.$rules['names']['data'] . '%';
                                if(isset($rules['primary_last_name'])) $conditions['User.primary_last_name LIKE'] = '%'.$rules['primary_last_name']['data'] . '%';
                                if(isset($rules['second_last_name'])) $conditions['User.second_last_name LIKE'] = '%'.$rules['second_last_name']['data'] . '%';
				if(isset($rules['username'])) $conditions['User.username LIKE'] = '%'.$rules['username']['data'] . '%';
				if(isset($rules['email'])) $conditions['User.email LIKE'] = $rules['email']['data'] . '%';
				if(isset($rules['role']) and $rules['role']['data'] != ' ') $conditions['User.role LIKE'] = $rules['role']['data'];
                                if(isset($rules['section_id'])) $conditions['Section.name LIKE'] = '%'.$rules['section_id']['data'] . '%';
                                if(isset($rules['department_id'])) $conditions['Department.name LIKE'] = '%'.$rules['department_id']['data'] . '%';
                                if(isset($rules['unit_id'])) $conditions['Unit.name LIKE'] = '%'.$rules['unit_id']['data'] . '%';
				if(isset($rules['is_active']) and $rules['is_active']['data'] != ' ') $conditions['User.is_active LIKE'] = $rules['is_active']['data'] . '%';
				if(isset($rules['modified'])) $conditions['User.modified LIKE'] = $this->dateToSql($rules['modified']['data'], false, true) . '%';
				//if(isset($rules['authorizes']) and $rules['authorizes']['data'] != ' ') $conditions['User.authorizes LIKE'] = $rules['authorizes']['data'] . '%';
			}
			if($model == 'InventoryAsset') {
//				if(isset($rules['Asset.name'])) $conditions[] = "MATCH (Asset.name) AGAINST ('" .$rules['Asset.name']['data']. "')";
                                if(isset($rules['Asset.name'])) $conditions['Asset.name LIKE'] = '%' .$rules['Asset.name']['data']. '%';
                                if(isset($rules['serial'])) $conditions['serial LIKE'] = '%'.$rules['serial']['data'] . '%';
                                if(isset($rules['life'])) $conditions['life LIKE'] = $rules['life']['data'] . '%';
                                if(isset($rules['residual_value'])) $conditions['residual_value LIKE'] = $rules['residual_value']['data'] . '%';
				if(isset($rules['code']) and $rules['code']['data'] != ' ') $conditions['InventoryAsset.code LIKE'] = '%'.$rules['code']['data'] . '%';
				if(isset($rules['status']) and $rules['status']['data'] != ' ') $conditions['InventoryAsset.status LIKE'] = $rules['status']['data'] . '%';
                                if(isset($rules['is_depreciate']) and $rules['is_depreciate']['data'] != ' ') $conditions['InventoryAsset.is_depreciate LIKE'] = $rules['is_depreciate']['data'] . '%';
				$iassets = array();
                                if(isset($rules['User.names'])){
//                                    $conditions['User.name LIKE'] =  '%'.$rules['User.name']['data'] . '%';
                                    $users = $this->User->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('User.names LIKE' => '%'.$rules['User.names']['data'] . '%')));
                                    if(!empty($users)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.user_id' => $users,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                    
                                }
                                if(isset($rules['User.primary_last_name'])){
//                                    $conditions['User.name LIKE'] =  '%'.$rules['User.name']['data'] . '%';
                                    $users = $this->User->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('User.primary_last_name LIKE' => '%'.$rules['User.primary_last_name']['data'] . '%')));
                                    if(!empty($users)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.user_id' => $users,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                    
                                }
                                if(isset($rules['User.second_last_name'])){
//                                    $conditions['User.name LIKE'] =  '%'.$rules['User.name']['data'] . '%';
                                    $users = $this->User->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('User.second_last_name LIKE' => '%'.$rules['User.second_last_name']['data'] . '%')));
                                    if(!empty($users)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.user_id' => $users,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                    
                                }
				if(isset($rules['Address.name'])){
//                                    $conditions['Address.name LIKE'] = '%'.$rules['Address.name']['data'] . '%';
//                                    App::import('Model','Address');
                                    $address = $this->InventoryAssetAllocation->Address->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('Address.name LIKE' => '%'.$rules['Address.name']['data'] . '%')));
                                    if(!empty($address)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id','InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.address_id' => $address,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
                                if(isset($rules['City.name'])){
//                                    $conditions['Acity.name LIKE'] = '%'.$rules['Acity.name']['data'] . '%';
                                    $city = $this->InventoryAssetAllocation->City->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('City.name LIKE' => '%'.$rules['City.name']['data'] . '%')));
                                    if(!empty($city)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.city_id' => $city,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
				if(isset($rules['Floor.number'])){
//                                    $conditions['Floor.number LIKE'] = '%'.$rules['Floor.number']['data'] . '%';
                                        $floor = $this->InventoryAssetAllocation->Floor->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('Floor.number LIKE' => '%'.$rules['Floor.number']['data'] . '%')));
                                    if(!empty($floor)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.floor_id' => $floor,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
				if(isset($rules['Office.number'])){
//                                    $conditions['Room.name LIKE'] = '%'.$rules['Room.name']['data'] . '%';
                                    $office = $this->InventoryAssetAllocation->Office->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('Office.number LIKE' => '%'.$rules['Office.number']['data'] . '%')));
                                    if(!empty($office)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.office_id' => $office,'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                        
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
                                if(isset($rules['program_id'])){
                                    $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.program_id' => $rules['program_id']['data'],'InventoryAssetAllocation.is_current'=>1)));
                                    $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                    if(empty($iassets))
                                        $iassets = $ia;
                                    $cond = array_intersect_key($ia, $iassets);
                                      
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
				if(isset($rules['Region.name'])){
//                                    $conditions['Seremi.name LIKE'] =  '%'.$rules['Seremi.name']['data'] . '%';
                                    $region = $this->InventoryAssetAllocation->Region->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('Region.name LIKE' => '%'.$rules['Region.name']['data'] . '%')));
                                    if(!empty($region)){
                                        $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id','InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.region_id' => $region, 'InventoryAssetAllocation.is_current'=>1)));
                                        $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                        if(empty($iassets))
                                            $iassets = $ia;
                                        $cond = array_intersect_key($ia, $iassets);
                                    }
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
				if(isset($rules['created'])){
                                    $allocations = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.created LIKE' => $this->dateToSql($rules['created']['data'],false,true).'%','InventoryAssetAllocation.is_current'=>1)));
                                    $ia = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations)));
                                    if(empty($iassets))
                                        $iassets = $ia;
                                    $cond = array_intersect_key($ia, $iassets);
                                    $conditions['InventoryAsset.id'] = $cond;
                                }
			}
			if($model == 'Floor') {
				if(isset($rules['number'])) $conditions['Floor.number LIKE'] = $rules['number']['data'] . '%';
                                if(isset($rules['address_id'])) $conditions['Address.name LIKE'] = $rules['address_id']['data'] . '%';
				if(isset($rules['floor_modified'])) $conditions['Floor.modified LIKE'] = $this->dateToSql($rules['floor_modified']['data'], false, true) . '%';
			}
			if($model == 'Office') {
				if(isset($rules['number'])) $conditions['Office.number LIKE'] = $rules['number']['data'] . '%';
                                if(isset($rules['user_id'])) $conditions['User.rut LIKE'] = $rules['user_id']['data'] . '%';
				if(isset($rules['floor_id'])) $conditions['Floor.number LIKE'] = $rules['floor_id']['data'] . '%';
				if(isset($rules['offices_modified'])) $conditions['Office.modified LIKE'] = $this->dateToSql($rules['offices_modified']['data'], false, true) . '%';
			}
                        if($model == 'Address') {
				if(isset($rules['name'])) $conditions['Address.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['city_id'])) $conditions['City.name LIKE'] = $rules['city_id']['data'] . '%';
				if(isset($rules['address_modified'])) $conditions['Address.modified LIKE'] = $this->dateToSql($rules['address_modified']['data'], false, true) . '%';
			}
                        if($model == 'City') {
				if(isset($rules['name'])) $conditions['City.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['region_id'])) $conditions['Region.name LIKE'] = $rules['region_id']['data'] . '%';
				if(isset($rules['city_modified'])) $conditions['City.modified LIKE'] = $this->dateToSql($rules['city_modified']['data'], false, true) . '%';
			}
			if($model == 'Region') {
				if(isset($rules['name'])) $conditions['Region.name LIKE'] = $rules['name']['data'] . '%';
				if(isset($rules['region_modified'])) $conditions['Region.modified LIKE'] = $this->dateToSql($rules['region_modified']['data'], false, true) . '%';
			}
                        if($model == 'Log') {
				if(isset($rules['created'])) $conditions['Log.created LIKE'] = $this->dateToSql($rules['created']['data'], false, true) . '%';
			}
			$params['conditions'] = (isset($params['conditions']))? array_merge($conditions, $params['conditions']) : $conditions;
		}
		
		//CASOS ESPECIALES, AJUSTAR CONDICIONES
                if($model == 'Asset') {
			$this->Asset->Behaviors->attach('Containable');
			$params['contain'] = array(
				'MClass',
				'SubClass'
			);
		}else if($model == 'User') {
			$this->User->Behaviors->attach('Containable');
			$params['contain'] = array(
				'Section',
				'Department',
				'Unit',
                                'Boss'
			);
                        $params['conditions'][]=array(
                            'NOT'=>array(
                                'User.role'=>array(2,4)
                            )
                        );
		} else if($model == 'PurchaseOrder') {
			$this->PurchaseOrder->Behaviors->attach('Containable');
			$params['contain'] = array(
                                'User',
                                'Provider'
			);				
		} 
                else if($model == 'PurchaseOrderDetail') {
			$this->PurchaseOrder->Behaviors->attach('Containable');
			$params['contain'] = array(
                                'Asset',
                                'PurchaseOrder'
			);				
		}else if($model == 'TransactionDetail') {
			$this->TransactionDetail->Behaviors->attach('Containable');
			$params['contain'] = array(
                                'Transaction',
                                'PurchaseOrderDetail',
                                'User',
                                'Asset'
			);			
		}elseif($model == 'Transaction') {
			$this->Transaction->Behaviors->attach('Containable');
			$params['contain'] = array(
				'PurchaseOrder'=>array('Provider'),
                                'User'
			);
		} elseif($model == 'InventoryAsset') {
			$this->$model->Behaviors->attach('Containable');
			$params['contain'] = array(
				'Asset',
				'InventoryAssetDisposal',
				'InventoryAssetAllocation' => array(
                                    'conditions'=>array('InventoryAssetAllocation.is_current'=>1),
					'User',
					'Office',
					'Floor',
					'Address',
                                        'City',
					'Region'
				),
				'InventoryAssetHistory',
			);
			//condiciones por accion (vista)
			if($this->params['url']['action'] == 'release') { //si es alta solo muestro los bienes de inventario ingresados y asignados
				$params['conditions']['InventoryAsset.status'] = array( 0, 1 );
//                                $allocations2 = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.is_current'=>1)));
//                                $ia2 = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations2)));
//                                $params['conditions']['InventoryAsset.id'] = $ia2;
			} else if($this->params['url']['action'] == 'terminate') { //si es baja solo muestro los bienes de inventario ingresados y asignados
				$params['conditions']['InventoryAsset.status'] = array( 0, 1, 2 );
			} else if($this->params['url']['action'] == 'my_inventory_assets') { //si son mis bienes, muestro solo mis bienes
				$params['conditions']['InventoryAsset.status'] = 1;//solo los con estado asignado
				$allocations2 = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.user_id' => $this->LdapAuth->user('id'),'InventoryAssetAllocation.is_current'=>1)));
                                $ia2 = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations2)));
                                $params['conditions']['InventoryAsset.id'] = $ia2;
			} else if($this->params['url']['action'] == 'acta_entrega') { 
//				$params['conditions']['InventoryAsset.status'] = 1;//solo los con estado asignado
				$allocations2 = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.acta_id' => $this->params['url']['acta_id'])));
                                $ia2 = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations2)));
                                $params['conditions']['InventoryAsset.id'] = $ia2;
			}else if($this->params['url']['action'] == 'acta_devolucion') { 
//				$params['conditions']['InventoryAsset.status'] = 0;
				$allocations2 = $this->InventoryAssetAllocation->find('list',array('recursive'=>-1,'fields'=>array('InventoryAssetAllocation.inventory_asset_id'),'conditions'=>array('InventoryAssetAllocation.acta_id' => $this->params['url']['acta_id'])));
                                $ia2 = $this->InventoryAsset->find('list',array('recursive'=>-1,'fields'=>array('id'),'conditions'=>array('InventoryAsset.id' => $allocations2)));
                                $params['conditions']['InventoryAsset.id'] = $ia2;
			}
			
		}
                if(array_key_exists('is_ses', $this->$model->_schema))
                    $params['conditions'][$model.'.is_ses'] = $this->LdapAuth->user('is_ses');//Filtra los resultados por SES o SSS
		// PAGINACION
		//calcular el número de filas para la consulta, necesario para paginar el resultado
		$count = $this->$model->find('count', $params);
		
		// calcular el total de páginas para la consulta
		$total_pages = ($count > 0 and $limit > 0)? ceil($count/$limit) : 0 ;

		// si por alguna razon la página solicitada es mayor al total
		// establecer la página solicitada como el total
		if ($page > $total_pages) $page = $total_pages;

		// calcular la posición de inicio de las filas
		$start = $limit*$page - $limit;

		// si por alguna razón la posición inicial es negativa establecerla como 0
		// el caso tipico es el del usuario que tipea 0 como página solicitada
		if($start < 0) $start = 0;

		// la consulta de datos para el grid
		$limit_range = $start . "," . $limit;
		$sort_range = $sidx . " " . $sord;

		//parametros busqueda
		$ord = (strpos($sort_range, '.') === false)? $model . '.' . $sort_range : $sort_range ;
		$params_paginate = array(
			'order' => $ord,
			'limit' => $limit,
			'page' => $page
		);
		$params = array_merge($params, $params_paginate);
		//RESULTADO
		$result = $this->$model->find('all', $params);;
		$this->set('result', $result);
		$this->set('page', $page);
		$this->set('total_pages', $total_pages);
		$this->set('count', $count);
                //$this->log($result,'test');
	}
	
	function _notification_email($data) {
            $this->Mailer->init();
            $this->Mailer->IsSMTP();
            $this->Mailer->Host     = "aspmx.l.google.com";
            $this->Mailer->AddAddress('admin@mds.cl'); //Modificar
            $this->Mailer->Subject = 'Alerta Inexistencia de Funcionario'; 
            // Set PHPMailer vars and call PHPMailer methods (see PHPMailer API for more info) 

            // Set mail body 
            $this->set('data', $data);
            ob_start(); 
            echo $this->render(NULL, 'email/html/send');
            $this->Mailer->Body = ob_get_contents();
            ob_end_clean();
            $this->layout = 'ajax';
            $this->output = null;
            $this->Mailer->IsHTML(true);
            // Send mail                             
            if ($this->Mailer->send()) { 
                $this->log('Mail was sent successfully.','debug'); 
             } else { 
                 $this->log('There was a problem sending mail: '.$this->Mailer->ErrorInfo,'debug'); 
            }
	}
        
	function dateToSql(&$date, $time = true, $return = false) {
		$date_str = 'Y-m-d';
		if($time) $date_str .= ' H:i:s';
		
		$date = explode('/', $date);
		$date = $date[2] . '-' . $date[1] . '-' . $date[0];
		$date = date($date_str, strtotime($date));
		if($return) return $date;
	}
	
	function sqlToDate($sql, $time = false) {
		if($sql == '') return $sql;
		$date_str = 'd/m/Y';
		if($time) $date_str .= ' H:i:s';
		return date($date_str, strtotime($sql));
	}
	
}
?>
