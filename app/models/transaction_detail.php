<?php
class TransactionDetail extends AppModel {

	var $name = 'TransactionDetail';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Transaction' => array(
			'className' => 'Transaction',
			'foreignKey' => 'transaction_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Asset' => array(
			'className' => 'Asset',
			'foreignKey' => 'asset_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PurchaseOrderDetail' => array(
			'className' => 'PurchaseOrderDetail',
			'foreignKey' => 'purchase_order_detail_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
        
        var $hasOne = array(
		'InventoryAsset' => array(
			'className' => 'InventoryAsset',
			'foreignKey' => 'transaction_detail_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
?>