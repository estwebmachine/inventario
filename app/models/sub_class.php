<?php
class SubClass extends AppModel {

	var $name = 'SubClass';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'MClass' => array(
			'className' => 'MClass',
			'foreignKey' => 'm_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Asset' => array(
			'className' => 'Asset',
			'foreignKey' => 'sub_class_id',
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

}
?>