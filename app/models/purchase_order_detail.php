<?php
class PurchaseOrderDetail extends AppModel {

	var $name = 'PurchaseOrderDetail';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	//var $hasOne = array('PurchaseOrder');
	var $belongsTo = array(
            'Asset' => array(
			'className' => 'Asset',
			'foreignKey' => 'asset_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
            'PurchaseOrder' => array(
			'className' => 'PurchaseOrder',
			'foreignKey' => 'purchase_order_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
            );
	var $hasMany = array('TransactionDetail');
	
	function valorOrdenCompra( $ordenCompraId )
	{
		$valorTotal = 0;
		$cuota = 0;
		$Orden = $this->find('all', array('conditions'=>array('PurchaseOrderDetail.purchase_order_id'=>$ordenCompraId) ) );
			foreach ( $Orden as $detalle){
				$valorTotal += $detalle['PurchaseOrderDetail']['amount'];
				$cuota = $detalle['PurchaseOrderDetail']['amount_trans'];
				
			}
		return $valorTotal."-".$cuota;
	}
}
?>