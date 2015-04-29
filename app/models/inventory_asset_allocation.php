<?php
class InventoryAssetAllocation extends AppModel {
	var $name = 'InventoryAssetAllocation';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasOne = array(
		'InventoryAssetHistory' => array(
			'className' => 'InventoryAssetHistory',
			'foreignKey' => 'inventory_asset_allocation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $belongsTo = array(
                'Acta' => array(
			'className' => 'Acta',
			'foreignKey' => 'acta_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryAsset' => array(
			'className' => 'InventoryAsset',
			'foreignKey' => 'inventory_asset_id',
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
		),
		'Floor' => array(
			'className' => 'Floor',
			'foreignKey' => 'floor_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'address_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	/**
	 * Actualiza el valor is_current de la asignación anterior a 0 (ya no es la asignación actual)
	 * para un bien de inventario dado
	 * 
	 * @param int $inventory_asset_id la id del bien de inventario 
	 */
	function set_not_current($inventory_asset_id) {
		$result = false;
		$params = array(
			'recursive' => -1,
			'conditions' => array(
				'InventoryAssetAllocation.inventory_asset_id' => $inventory_asset_id,
				'InventoryAssetAllocation.is_current' => 1
			)
		);
		$old_allocation = $this->find('first', $params);
		if( !empty($old_allocation) ) {
			$old_allocation['InventoryAssetAllocation']['is_current'] = 0;
			$result = $this->save($old_allocation);
		}
		return $result;
	}
	
}
