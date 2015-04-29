<?php
class PurchaseOrdersController extends AppController {

	var $name = 'PurchaseOrders';
	var $components = array('RequestHandler', 'Xml', 'File');
	var $helpers = array('Html', 'Form', 'Pageorder');
	var $uses = array('PurchaseOrder');

	function index() {

	}

	function indexedit() {
		$this->autoRender = false;
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$output['result'] = 'failure';
		
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['PurchaseOrder'] = $this->params['form'];

		if($action == 'edit') {
			//id orden
			$order_id = $this->data['PurchaseOrder']['id'];
			//revisar existencia de numero de orden
			if($this->data['PurchaseOrder']['order_number'] == '') {
				$output['message'] = 'Indique Número de Orden.';
			} else if($this->data['PurchaseOrder']['provider_id'] == 'null') {
				$output['message'] = 'Indique Proveedor.';
			} else {
				$preorder = $this->PurchaseOrder->findByOrderNumber($this->data['PurchaseOrder']['order_number']);
                                $this->log($preorder['PurchaseOrder']['id'],'debug');
                                $this->log($order_id,'debug');
                                
				if(empty($preorder) or $preorder['PurchaseOrder']['id'] == $order_id) {
					$this->dateToSql($this->data['PurchaseOrder']['date']);
					if($this->PurchaseOrder->save($this->data, null, null)) {
						$output['result'] = 'success';
						$output['oper'] = $action;
					}
				} else {
					//ya existe orden con ese numero
					$output['message'] = 'Número de orden ya existe, indique uno nuevo.';
				}
			}
		}
		else if($action == 'add') {
			unset($this->data['PurchaseOrder']['id']);
			//revisar existencia de numero de orden
			if($this->data['PurchaseOrder']['order_number'] == '') {
				$output['message'] = 'Indique Número de Orden.';
			} else if($this->data['PurchaseOrder']['date'] == '') {
				$output['message'] = 'Indique Fecha.';
			} else if($this->data['PurchaseOrder']['provider_id'] == 'null') {
				$output['message'] = 'Indique Proveedor.';
			} else {
				$preorder = $this->PurchaseOrder->findByOrderNumber($this->data['PurchaseOrder']['order_number']);
				if(empty($preorder)) {
					//id usuario creador
					$this->data['PurchaseOrder']['user_id'] = $this->LdapAuth->user('id');
                                        $this->data['PurchaseOrder']['is_ses'] = $this->LdapAuth->user('is_ses');
					//status
					$this->data['PurchaseOrder']['status'] = 0;
					$this->dateToSql($this->data['PurchaseOrder']['date']);
					$this->PurchaseOrder->create();
					if($this->PurchaseOrder->save($this->data, null, null)) {
						$output['result'] = 'success';
						$output['oper'] = $action;
						$output['id'] = $this->PurchaseOrder->id;
					};
				} else {
					//ya existe orden con ese numero
					$output['message'] = 'Número de orden ya existe, indique uno nuevo.';
				}
			}
		}
		else if($action == 'del') {
			$this->PurchaseOrder->del($this->data['PurchaseOrder']['id']);
		}
                echo json_encode($output);
                //$this->set('output', $output);
	}

	function close($id) {
		$this->layout = 'ajax';
                $this->autoRender = false;
		Configure::write('debug', 0);
		$output['result'] = 'failure';
		
		if($this->RequestHandler->isAjax()) {
			$this->PurchaseOrder->recursive = 1;
			$order = $this->PurchaseOrder->read(null, $id);

			//si existen productos en la orden prosigo
			if(!empty($order['PurchaseOrderDetail'])) {
				//validar que campos cantidad precio sean no vacios para todos los productos
				$empty_fields = false;
				foreach($order['PurchaseOrderDetail'] as $detail) {
					if($detail['amount'] == '' or $detail['price'] == '' or $detail['currency'] == '') {
						$empty_fields = true;
						$output['message'] = 'Complete todos los campos de cantidad, precio y moneda.';
						break;
					}
				}
				
				if(!$empty_fields) {				
					//actualizo status
					$this->PurchaseOrder->id = $id;
					$this->PurchaseOrder->saveField('status', 1);
					$output['result'] = 'success';
				}
			} else { $output['message'] = 'No se han agregado Bienes.'; }
		}
		echo json_encode($output);
	}
//
	function null($id) {
            $this->autoRender = false;
            $this->layout = 'ajax';
            Configure::write('debug', 0);
            $output['result'] = 'failure';

            if($this->RequestHandler->isAjax()) {
                    $order = $this->PurchaseOrder->read(null, $id);
                    //si hay transacciones enviadas no es posible anular
                    $isvoidable = true;
                    foreach($order['Transaction'] as $transaction) if($transaction['status'] == 1) $isvoidable = false;
                    if($isvoidable) {
                            //si se ingreso comentario
                            $comment = $this->params['form']['comment'];
                            if($comment != '') {
                                    //actualizo status a "Nula" y guardo comentario
                                    $data = array();
                                    $data['PurchaseOrder']['id'] = $id;
                                    $data['PurchaseOrder']['status'] = 3;
                                    $data['PurchaseOrder']['comment'] = $comment;

                                    if($this->PurchaseOrder->save($data)) $output['result'] = 'success';
                            } else { $output['message'] = 'Ingrese Comentario.'; }
                    } else { $output['message'] = 'No es posible anular, ya se ha recepcionado parte de la Orden.'; }
            }
            echo json_encode($output);
	}
//	
	function addAsset() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$output = 'failure';
		$this->autoRender=false;
		if($this->RequestHandler->isAjax()) {
			$pid = $this->params['form']['pid'];
			$ids = $this->params['form']['ids'];
			//guardo detalle de orden por producto
			if(!empty($ids)) {
				$data = array();
				foreach($ids as $id) {
					$data['PurchaseOrderDetail']['purchase_order_id'] = $pid;
					$data['PurchaseOrderDetail']['asset_id'] = $id;
					$this->PurchaseOrder->PurchaseOrderDetail->create();
					$this->PurchaseOrder->PurchaseOrderDetail->save($data);
				}
				//if($this->PurchaseOrder->PurchaseOrderDetail->saveAll($data)) $output = 'success';
				$output = 'success';
			}
		}
		$this->set('output', $output);
                echo $output;
	}
//
	function search() {
		Configure::write('debug', '0');
		$this->layout = null;
		
		$model = 'PurchaseOrder';

		$params = array();
		$page = $this->params['url']['page']; // get the requested page
		$limit = $this->params['url']['rows']; // get how many rows we want to have into the grid
		$sidx = $this->params['url']['sidx']; // get index row - i.e. user click to sort
		$sord = $this->params['url']['sord']; // get the direction
		if(!$sidx) $sidx =1;

		if(isset($this->params['url']['num_mask']))
			$num_mask = $this->params['url']['num_mask'];
		else
			$num_mask = "";
		
		//construct where clause
		$params['conditions'] = array();
		//solo ordenes de compras enviadas
		$params['conditions']["PurchaseOrder.status"] = 1;
                $params['conditions'][$model.'.is_ses'] = $this->LdapAuth->user('is_ses');//Filtra los resultados por SES o SSS
				
		if($num_mask != '')
			$params['conditions']["PurchaseOrder.order_number LIKE"] = $num_mask . '%';

		$count = $this->$model->find('count', $params);

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		if ($limit<0) $limit = 0;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start<0) $start = 0;
		
		
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
		//$this->log($params_paginate, 'debug');
		$params = array_merge($params, $params_paginate);
		
		
		//resultado
		$result = $this->$model->find('all', $params);
		
		$this->set('result', $result);
		$this->set('page', $page);
		$this->set('total_pages', $total_pages);
		$this->set('count', $count);
	}
	
	function view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Disculpa, no existe este archivo PDF.', 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		
		$this->layout = 'pdf';
		Configure::write('debug', 0);
		$this->PurchaseOrder->Behaviors->attach('Containable');

		$params = array();
		$params['conditions'] = array('PurchaseOrder.id' => $id);
		$params['contain'] = array(
			'User',
			'Warehouse',
			'Provider',
			'PurchaseOrderDetail' => array(
				'Asset'
			)
		);
		
		$order = $this->PurchaseOrder->find('first', $params);
		//controlar que la solicitud sea del usuario actual, siempre que no sea administrador de sistema
		if($this->LdapAuth->user('role') != 0 and $this->LdapAuth->user('id') != $order['PurchaseOrder']['user_id'] ) {
			//$this->Session->setFlash('No tiene permiso para ingresar a esta area.', 'default', array('class' => 'error'));
			//$this->redirect(array('action'=>'index'));
		}		
		$this->set('order', $order);
		$this->render();
	}
//	
	function load() {
		$upload_data = $this->File->upload($this->data['File'], 'xml', true);
              
		if(array_key_exists('urls', $upload_data)) {
                    $message = $this->Xml->processPurchaseOrder(null, $this->data['PurchaseOrder']['description']);
                    $this->Session->setFlash($message['text'], 'default', array('class' => $message['class']));
		} else {
                    $this->Session->setFlash('Orden de compra no pudo ser agregada. Intente nuevamente.', 'default', array('class' => 'error'));
		}
		
		//$this->log($this->data, 'debug');
		//$this->log($upload_data, 'debug');
		$this->redirect(array('action'=>'index'));
	}
}
?>