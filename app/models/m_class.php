<?php
class MClass extends AppModel {

	var $name = 'MClass';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
            'SubClass' => array(
                    'className' => 'SubClass',
                    'foreignKey' => 'm_class_id',
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
            'Asset' => array(
			'className' => 'Asset',
			'foreignKey' => 'm_class_id',
			'dependent' => true,
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

}
?>