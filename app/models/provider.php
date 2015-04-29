<?php
class Provider extends AppModel {

	var $name = 'Provider';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
            'PurchaseOrder' => array(
                    'className' => 'PurchaseOrder',
                    'foreignKey' => 'provider_id',
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

}
?>