<?php
class PurchaseOrderDetailsController extends AppController {

	var $name = 'PurchaseOrderDetails';
	var $uses = array('PurchaseOrder','PurchaseOrderDetail', 'Asset','Transaction');
	var $helpers = array('Html', 'Form');

	function indexedit($pid) {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['PurchaseOrderDetail'] = $this->params['form'];

		//Orden padre
		$this->recursive = 0;
		$order = $this->PurchaseOrderDetail->PurchaseOrder->findById($pid);

		if($action == 'edit')	{
			//calculo valor
			$order_det = $this->PurchaseOrderDetail->findById($this->data['PurchaseOrderDetail']['id']);
			$amount = $order_det['PurchaseOrderDetail']['amount'];
			$price = $order_det['PurchaseOrderDetail']['price'];
			if($this->data['PurchaseOrderDetail']['amount'] != '') {
				$this->data['PurchaseOrderDetail']['value'] = $this->data['PurchaseOrderDetail']['amount'] * $price;
				//cantidad por transaccionar = cantidad total
				$this->data['PurchaseOrderDetail']['amount_trans'] = $this->data['PurchaseOrderDetail']['amount'];
			}
			else if($this->data['PurchaseOrderDetail']['price'] != '') $this->data['PurchaseOrderDetail']['value'] = $this->data['PurchaseOrderDetail']['price'] * $amount;

			$this->PurchaseOrderDetail->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['PurchaseOrderDetail']['id']);
			//valor = 0
			$this->data['PurchaseOrderDetail']['value'] = 0;
			//cantidad por transaccionar = cantidad total
			$this->data['PurchaseOrderDetail']['amount_trans'] = $this->data['PurchaseOrderDetail']['amount'];
			//id order
			$this->data['PurchaseOrderDetail']['purchase_order_id'] = $pid;
			$this->PurchaseOrderDetail->create();
			$this->PurchaseOrderDetail->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->PurchaseOrderDetail->del($this->data['PurchaseOrderDetail']['id']);
		}
	}

	function search() {
		$this->layout = null;
		Configure::write('debug', 0);
		$model = 'PurchaseOrderDetail';

		$params = array();
		$page = $this->params['url']['page']; // get the requested page
		$limit = $this->params['url']['rows']; // get how many rows we want to have into the grid
		$sidx = $this->params['url']['sidx']; // get index row - i.e. user click to sort
		$sord = $this->params['url']['sord']; // get the direction
		if(!$sidx) $sidx =1;

		$orderid_mask = $this->params['url']['orderid_mask'];
		$transid_mask = $this->params['url']['transid_mask'];
		if(isset($this->params['url']['nm_mask']))
			$nm_mask = $this->params['url']['nm_mask'];
		else
			$nm_mask = "";
		//construct where clause
		$params['conditions'] = array();
		$params['conditions']['AND'] = array();
		$params['conditions']['AND']["PurchaseOrder.id"] = $orderid_mask;
		//solo deben ser detalles de ordenes con cantidades por transaccionar mayores a cero
		$params['conditions']['AND']["PurchaseOrderDetail.amount_trans >"] = 0;
		//si el detalle de orden ya se encuentra en la transaccion no mostrarlo (evitar agregar dos veces el mismo producto)
		$transaction = $this->Transaction->findById($transid_mask);
		
		foreach($transaction['TransactionDetail'] as $detail) {
			$params['conditions']['AND'][] = array('PurchaseOrderDetail.id !=' => $detail['purchase_order_detail_id']);			
		}
		
		if($nm_mask != '')
			$params['conditions']['AND']["Asset.name LIKE"] = $nm_mask . '%';
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
		
		//resultado
		$result = $this->$model->find('all', $params);
		$this->log($result,'debug');
		$this->set('result', $result);
		$this->set('page', $page);
		$this->set('total_pages', $total_pages);
		$this->set('count', $count);
	}

}
?>