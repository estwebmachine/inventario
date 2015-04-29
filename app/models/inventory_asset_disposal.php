<?php
class InventoryAssetDisposal extends AppModel {
	var $name = 'InventoryAssetDisposal';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasOne = array(
		'InventoryAssetHistory' => array(
			'className' => 'InventoryAssetHistory',
			'foreignKey' => 'inventory_asset_disposal_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $belongsTo = array(
		'InventoryAsset' => array(
			'className' => 'InventoryAsset',
			'foreignKey' => 'inventory_asset_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
