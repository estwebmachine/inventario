<?php
class PurchaseOrder extends AppModel {

	var $name = 'PurchaseOrder';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
            'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
            'Provider' => array(
			'className' => 'Provider',
			'foreignKey' => 'provider_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        );
	var $hasMany = array(
//            'Transaction',
            'PurchaseOrderDetail' => array(
                    'className' => 'PurchaseOrderDetail',
                    'foreignKey' => 'purchase_order_id',
                    'dependent' => true,
                    'conditions' => '',
                    'fields' => '',
                    'order' => '',
                    'limit' => '',
                    'offset' => '',
                    'exclusive' => '',
                    'finderQuery' => '',
                    'counterQuery' => ''
            ),
            
            );
	
	function updateStatus( $id, $status )
	{
		$query = "update purchase_orders set status = ".$status.", modified= '".date ("Y-m-d H:m:s")."' ";
		$query .= "where id = ".$id;
		$this->query( $query );	
	}
	

}
?>