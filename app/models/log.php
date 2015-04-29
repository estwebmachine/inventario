<?php
class Log extends AppModel {
	var $name = 'Log';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
            'InventoryAssetHistory' => array(
                    'className' => 'InventoryAssetHistory',
                    'foreignKey' => 'inventory_asset_history_id',
                    'conditions' => '',
                    'fields' => '',
                    'order' => ''
            ),
	);
	
	function add($user_id, $type, $comment) {
		$data['Log'] = array('user_id' => $user_id, 'type' => $type, 'comment' => $comment);
		return $this->save($data);
	}
}
