<?php
class InventoryAssetHistory extends AppModel {
	var $name = 'InventoryAssetHistory';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'InventoryAsset' => array(
                    'className' => 'InventoryAsset',
                    'foreignKey' => 'inventory_asset_id',
                    'conditions' => '',
                    'fields' => '',
                    'order' => ''
		),
		'InventoryAssetDisposal' => array(
			'className' => 'InventoryAssetDisposal',
			'foreignKey' => 'inventory_asset_disposal_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryAssetAllocation' => array(
			'className' => 'InventoryAssetAllocation',
			'foreignKey' => 'inventory_asset_allocation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'User' => array(
                        'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
        
        var $hasOne = array(
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'inventory_asset_history_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
