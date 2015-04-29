<?php
class TransactionDetailsController extends AppController {

	var $name = 'TransactionDetails';
	var $uses = array('TransactionDetail', 'PurchaseOrderDetail');
	var $helpers = array('Html', 'Form');
	var $components = array('RequestHandler','File');

	function indexedit($tid) {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['TransactionDetail'] = $this->params['form'];

		//transaccion padre
		$this->recursive = 0;
		if($action == 'edit')	{
			//descontar cantidad por transaccionar.
			$transaction_det = $this->TransactionDetail->findById($this->data['TransactionDetail']['id']); //busco detalle transaccion
			$amount_trans_orig = $transaction_det['PurchaseOrderDetail']['amount_trans']; //cantidad a transaccionar original
			$amount = $this->data['TransactionDetail']['amount'];
			//si la cantidad es mayor a lo que queda por transaccionar
			if($amount > $amount_trans_orig) {
				$this->data['TransactionDetail']['amount'] = $amount_trans_orig;
				$this->data['TransactionDetail']['amount_trans'] = 0;
			} else { //si es menor o igual solo resto
				if($amount < 0) $this->data['TransactionDetail']['amount'] = 0;
				$this->data['TransactionDetail']['amount_trans'] = $amount_trans_orig - $amount;
			}
			//calculo valor
			$this->data['TransactionDetail']['value'] = $amount * $transaction_det['TransactionDetail']['price'];
			
			$this->TransactionDetail->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['TransactionDetail']['id']);
			//id transaction
			$this->data['TransactionDetail']['transaction_id'] = $tid;
			//id usuario creador
			$this->data['TransactionDetail']['user_id'] = $this->LdapAuth->user('id');
			$this->TransactionDetail->create();
			$this->TransactionDetail->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->TransactionDetail->del($this->data['TransactionDetail']['id']);
		}
	}
        
        function editAsset(){
            $this->layout = 'ajax';
            Configure::write('debug', 0);
            $this->autoRender = FALSE;
            $output['result'] = 'failure';
            
            if($this->RequestHandler->isAjax()) {
                $transdetid = $this->params['form']['transdetid'];
                $costcenter = $this->params['form']['costcenter'];
                if($costcenter != ''){
                    $data = array();
                    $data['TransactionDetail']['id'] = $transdetid;
                    $data['TransactionDetail']['responsability_center_id'] = $costcenter;
                    if($this->TransactionDetail->save($data)) $output['result'] = 'success';
                }else{
                    $output['message'] = 'Seleccione centro de costo';
                }	
            }
            echo json_encode($output);
        }
	
	function addAsset() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$output = 'failure';
                $this->autoRender = false;
		if($this->RequestHandler->isAjax()) {
			$transdetid = $this->params['form']['transdetid'];
			$id = $this->params['form']['id'];
			//guardo detalle de orden por producto               
                        if($id != '') {
				$data = array();
				$data['TransactionDetail']['id'] = $transdetid;
				$data['TransactionDetail']['asset_id'] = $id;
				if( $this->TransactionDetail->save($data) ) $output = 'success';
			}
		}
		echo $output;	
	}
        
        function addDctoCsv(){
            Configure::write('debug', 0);
            $this->autoRender = false;
            $output['result'] = 'failure';
            $file = $this->data['Document']['csv'];
            if($this->RequestHandler->isAjax()) {
                if($this->File->upload_csv($file,$this->params['form']['id'],'CSV')){
                    $output['result'] = 'success';
                }else{
                    $output['message'] = 'Error al intentar subir el archivo, intentelo nuevamente';
                }
            }
            echo json_encode($output);
        }
        
        function deleteDctoCsv($id){
            Configure::write('debug', 0);
            $this->autoRender = false;
            $output['result'] = 'failure';
            if($this->RequestHandler->isAjax()) {
                if($this->File->delete(WWW_ROOT.'csv','CSV_'.$id.'.csv')){
                    $output['result'] = 'success';
                }else{
                    $output['message'] = 'Error al intentar eliminar el archivo, intentelo nuevamente';
                }
            }
            echo json_encode($output);
        }
}
?>