<?php
class TransactionsController extends AppController {

	var $name = 'Transactions';
	var $uses = array('Transaction', 'PurchaseOrderDetail', 'PurchaseOrder', 'InventoryAsset');
	var $components = array('RequestHandler', 'File');
	var $helpers = array('Html', 'Form');

	function index() {
		
	}

	function indexedit() {
		$this->autoRender = false;
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$output['result'] = 'failure';
               
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
                $file = $this->data['Document']['pdf'];
                unset($this->data['Document']);
		$this->data['Transaction'] = $this->params['form'];
		if($action == 'edit')	{
			//id transaccion
			$trans_id = $this->data['Transaction']['id'];
			//validar
			if($this->data['Transaction']['date'] == '') {
				$output['message'] = 'Ingrese Fecha.';
			} else if($this->data['Transaction']['document_type'] == '') {
				$output['message'] = 'Seleccione Tipo de Documento.';
			} else if($this->data['Transaction']['document_number'] == '') {
				$output['message'] = 'Ingrese Número Documento.';
			} else if($this->data['Transaction']['document_date'] == '') {
				$output['message'] = 'Ingrese Fecha Documento.';
			} else if($this->data['Transaction']['type'] == '') {
				$output['message'] = 'Seleccione Tipo de Alta.';
			}else if($this->data['Transaction']['subtitles'] == '') {
				$output['message'] = 'Seleccione Subtítulo.';
			}else if($this->data['Transaction']['purchase_order_id'] == 'null') {
				$output['message'] = 'Seleccione Número de Orden.';
			} else {
				//validar existencia de numero de documento
				$purchase_order = $this->PurchaseOrder->findById($this->data['Transaction']['purchase_order_id']);				
				$valid_document = true;
				$pretrans = $this->Transaction->findAllByDocumentNumber($this->data['Transaction']['document_number']);
				//si es del mismo tipo de documento, mismo numero documento y mismo proveedor entonces rechazo
				$document_type = $this->data['Transaction']['document_type'];
				$document_number = $this->data['Transaction']['document_number'];
				$provider_id = $purchase_order['PurchaseOrder']['provider_id'];
				foreach($pretrans as $pretran) {
					if($pretran['Transaction']['document_type'] == $document_type and $pretran['Transaction']['document_number'] == $document_number and $pretran['PurchaseOrder']['provider_id'] == $provider_id and $pretran['Transaction']['id'] != $trans_id) $valid_document = false;
				}
				
				if($valid_document) {
					//validar fecha recepcion posterior a fecha creacion orden de compra
					$purchase_order_date = $purchase_order['PurchaseOrder']['date'];
					$transaction_date = $this->data['Transaction']['date'];
					$this->dateToSql($transaction_date);
					if(strtotime($transaction_date) - strtotime($purchase_order_date) > 0) {
						$this->dateToSql($this->data['Transaction']['date']);
						$this->dateToSql($this->data['Transaction']['document_date']);
						if($this->Transaction->save($this->data, null, null)) {
							$output['result'] = 'success';
							$output['oper'] = $action;
                                                        $this->File->upload_pdf($file,$this->Transaction->id,'DP');
						}
					} else { $output['message'] = 'Seleccione fecha posterior a ' . $this->sqlToDate($purchase_order_date) . '.'; }
				} else {
					//ya existe transaccion con ese numero de documento
					$output['message'] = 'Número de documento ya existe, indique uno nuevo.';
				}
			}
		}
		else if($action == 'add') {
			unset($this->data['Transaction']['id']);
			//validar
			if($this->data['Transaction']['date'] == '') {
				$output['message'] = 'Ingrese Fecha.';
			} else if($this->data['Transaction']['document_type'] == '') {
				$output['message'] = 'Seleccione Tipo de Documento.';
			} else if($this->data['Transaction']['document_number'] == '') {
				$output['message'] = 'Ingrese Número Documento.';
			} else if($this->data['Transaction']['document_date'] == '') {
				$output['message'] = 'Ingrese Fecha Documento.';
			} else if($this->data['Transaction']['type'] == '') {
				$output['message'] = 'Seleccione Tipo de Alta.';
			}else if($this->data['Transaction']['subtitles'] == '') {
				$output['message'] = 'Seleccione Subtítulo.';
			}else if($this->data['Transaction']['purchase_order_id'] == 'null') {
				$output['message'] = 'Seleccione Número de Orden.';
			} else {
				//validar existencia de numero de documento
				$purchase_order = $this->PurchaseOrder->findById($this->data['Transaction']['purchase_order_id']);				
				$valid_document = true;
				$pretrans = $this->Transaction->findAllByDocumentNumber($this->data['Transaction']['document_number']);
				//si es del mismo tipo de documento, mismo numero documento y mismo proveedor entonces rechazo
				$document_type = $this->data['Transaction']['document_type'];
				$document_number = $this->data['Transaction']['document_number'];
				$provider_id = $purchase_order['PurchaseOrder']['provider_id'];
				foreach($pretrans as $pretran) {
					if($pretran['Transaction']['document_type'] == $document_type and $pretran['Transaction']['document_number'] == $document_number and $pretran['PurchaseOrder']['provider_id'] == $provider_id) $valid_document = false;
				}
				
				if($valid_document) {
					//validar fecha recepcion posterior o igual a fecha creacion orden de compra
					$purchase_order_date = $purchase_order['PurchaseOrder']['date'];
					$transaction_date = $this->data['Transaction']['date'];
					$this->dateToSql($transaction_date);
					if(strtotime($transaction_date) - strtotime($purchase_order_date) >= 0) {
						//id usuario creador
						$this->data['Transaction']['user_id'] = $this->LdapAuth->user('id');
                                                $this->data['Transaction']['is_ses'] = $this->LdapAuth->user('is_ses');
						//status
						$this->data['Transaction']['status'] = 0;
						//paso fechas a formato SQL
						$this->dateToSql($this->data['Transaction']['date']);
						$this->dateToSql($this->data['Transaction']['document_date']);
						$this->Transaction->create();
						if($this->Transaction->save($this->data, null, null)) {
							$output['result'] = 'success';
							$output['oper'] = $action;
							$output['id'] = $this->Transaction->id;
                                                        $this->File->upload_pdf($file,$this->Transaction->id,'DP');
						}
					} else { $output['message'] = 'Seleccione fecha posterior a ' . $this->sqlToDate($purchase_order_date) . '.'; }
				} else {
					//ya existe orden con ese numero
					$output['message'] = 'Número de documento ya existe, indique uno nuevo.';
				}
			}
		}
		else if($action == 'del') {
			if($this->Transaction->del($this->data['Transaction']['id'])){
                            $this->File->delete(WWW_ROOT . 'pdf', 'DP_' . $this->data['Transaction']['id'] . '.pdf');
                        }
		}
		echo json_encode($output);
	}

	function close($id) {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$output['result'] = 'failure';
		$this->autoRender = false;
		if($this->RequestHandler->isAjax()) {
			$this->Transaction->recursive = 1;
			$transaction = $this->Transaction->read(null, $id);

			//si existen productos en la transaccion prosigo
			if(!empty($transaction['TransactionDetail'])) {
				//validar que campo cantidad sea no vacio para todos los productos, y que cada detalle tenga asignado un producto (asset_id)
				$empty_fields = false;
				$empty_assets = false;
				foreach($transaction['TransactionDetail'] as $detail) {
					if($detail['amount'] == '') {
						$empty_fields = true;
						$output['message'] = 'Complete todos los campos de cantidad.';
						break;
					} else if($detail['amount'] == '0') {
						$empty_fields = true;
						$output['message'] = 'Los campos de cantidad deben ser distintos de cero.';
						break;
					}
					if($detail['asset_id'] == '') {
						$empty_assets = true;
						$output['message'] = 'Seleccione los bienes para cada detalle.';
						break;
					}
				}
				
				if(!$empty_fields and !$empty_assets) {
					//actualizo status
					$this->Transaction->id = $id;
					
					
                                        $result = $this->InventoryAsset->processReception($id, $this->LdapAuth->user('is_ses'),$this->LdapAuth->user('id'));
                                        if(!isset($result['error'])){
                                        $this->Transaction->saveField('status', 1);
					//*** actualizar cantidades por transaccionar de detalles orden ***
					foreach($transaction['TransactionDetail'] as $detail) {
						//busco detalle transaccion
						$transaction_det = $this->Transaction->TransactionDetail->findById($detail['id']);
						//busco detalle orden
						$order_det['PurchaseOrderDetail'] = $transaction_det['PurchaseOrderDetail'];
						//actualizo cantidad por transaccionar						
						$amount_trans_orig = $order_det['PurchaseOrderDetail']['amount_trans']; //cantidad a transaccionar original
						$amount = $transaction_det['TransactionDetail']['amount'];
						
						
						$this->PurchaseOrderDetail->save($order_det);
						
						//si la cantidad es mayor a lo que queda por transaccionar
						if($amount > $amount_trans_orig) {
							//disminuyo cantidad a todo lo que queda
							$transaction_det['TransactionDetail']['amount'] = $amount_trans_orig;
							$detail['amount'] = $amount_trans_orig;
							$transaction_det['TransactionDetail']['amount_trans'] = 0;
							//guardo detalle transaccion con nueva cantidad
							$this->Transaction->TransactionDetail->save(array('TransactionDetail' => $transaction_det['TransactionDetail']));
							$order_det['PurchaseOrderDetail']['amount_trans'] = 0;
							//guardo detalle orden con nueva cantidad por transaccionar
							$this->PurchaseOrderDetail->save($order_det);
						} else { //si es menor o igual solo resto
							$order_det['PurchaseOrderDetail']['amount_trans'] = $amount_trans_orig - $amount;
							//guardo detalle orden con nueva cantidad por transaccionar
							$this->PurchaseOrderDetail->save($order_det);
						}						
					}
//                                        $result = $this->InventoryAsset->processReception($id, $this->LdapAuth->user('is_ses'));
                                        
                                            $output['result'] = 'success';

                                            //si la orden correspondiente a la transaccion fue completamente transaccionada asignarle estado "Recepcionada"
                                            $purchase_order_id = $transaction['Transaction']['purchase_order_id'];
                                            $purchase_order = $this->PurchaseOrder->findById($purchase_order_id);
                                            $total_trans = true;
                                            foreach($purchase_order['PurchaseOrderDetail'] as $detail) {
                                                    if($detail['amount_trans'] > 0) $total_trans = false;
                                            }
                                            //orden completamente transaccionada, asigno estado "Recepcionada" a la orden
                                            if($total_trans) {
                                                    $this->PurchaseOrder->id = $purchase_order_id;
                                                    $this->PurchaseOrder->saveField('status', 2);
                                            }
                                        }else{
                                            $output['result'] = 'file_error';
                                            $output['row'] = $result['error'];
                                            $output['message']='Error. La cantidad de registros en el archivo es mayor a la cantidad de bienes a ingresar o la cantidad de columnas no concuerda';
                                        }
				}
			} else { $output['message'] = 'No se han agregado Bienes.'; }
		}
		echo json_encode($output);
	}

	function addAsset() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
                $this->autoRender = false;
		$output['result'] = 'failure';
		
		if($this->RequestHandler->isAjax()) {
			$tid = $this->params['form']['tid'];
			$ids = $this->params['form']['ids'];
			//transaccion padre
			$this->recursive = 0;
			//$transaction = $this->Transaction->findById($tid);
			//guardo detalle de transaccion por producto
                       if(!empty($ids)) {
				$data = array();
				foreach($ids as $id) {
					//id transaccion
					$data['TransactionDetail']['transaction_id'] = $tid;				
					//id detalle orden de compra
					$data['TransactionDetail']['purchase_order_detail_id'] = $id;
					//id usuario creador
					$data['TransactionDetail']['user_id'] = $this->LdapAuth->user('id');
					//recupero id producto (asset_id) a partir de id de detalle orden (purchase_order_detail_id)
					$p_order_det = $this->PurchaseOrderDetail->findById($id);
					$data['TransactionDetail']['asset_id'] = $p_order_det['PurchaseOrderDetail']['asset_id'];
					//recupero precio desde detalle orden
					$data['TransactionDetail']['price'] = $p_order_det['PurchaseOrderDetail']['price'];
					
					//guardo cantidad por transaccionar en detalle transaccion, desde detalle orden
					$data['TransactionDetail']['amount_trans'] = $p_order_det['PurchaseOrderDetail']['amount_trans'];
					
					$this->Transaction->TransactionDetail->create();
					$this->Transaction->TransactionDetail->save($data);
				}				
				$output['result'] = 'success';
			}else{
                             $output['message']='Seleccione bienes';
                        }
		}
		return json_encode($output);		
	}
}
?>