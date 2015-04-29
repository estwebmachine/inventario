<?php
class Acta extends AppModel {
	var $name = 'Acta';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Assigned' => array(
			'className' => 'User',
			'foreignKey' => 'assigned_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Receive' => array(
			'className' => 'User',
			'foreignKey' => 'receive_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
        
        var $hasMany = array(
                'InventoryAssetAllocation' => array(
			'className' => 'InventoryAssetAllocation',
			'foreignKey' => 'acta_id',
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
        
        function lastFolio($is_ses) {
		$last = $this->find('first', array('recursive'=>-1,'conditions'=>array('Acta.is_ses'=>  $is_ses),'order' => array('Acta.id DESC')));
                $index = 0;
		if( !empty($last) ) $index = $is_ses==1?$last['Acta']['folio_ses']:$last['Acta']['folio_sss'];
		return $index;
	}
	
}
