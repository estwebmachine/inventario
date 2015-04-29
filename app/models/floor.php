<?php
class Floor extends AppModel {
	var $name = 'Floor';
	var $displayField = 'number';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'InventoryAssetAllocation' => array(
			'className' => 'InventoryAssetAllocation',
			'foreignKey' => 'floor_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'floor_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
        
        var $belongsTo = array(
		'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'address_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
