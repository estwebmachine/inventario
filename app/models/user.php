<?php
class User extends AppModel {

	var $name = 'User';
//        var $validate = array(
//            'rut' => array(
//                'rule' => 'isUnique'
//            )
//        );
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
                'Section' => array(
			'className' => 'CostCenter',
			'foreignKey' => 'section_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'CostCenter',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),'Unit' => array(
			'className' => 'CostCenter',
			'foreignKey' => 'unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),'Boss' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
            'PurchaseOrder' => array(
                'className' => 'PurchaseOrder',
                'foreignKey' => 'user_id',
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
            'Transaction' => array(
                    'className' => 'Transaction',
                    'foreignKey' => 'user_id',
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
            'InventoryAssetAllocation' => array(
                    'className' => 'InventoryAssetAllocation',
                    'foreignKey' => 'user_id',
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
            'InventoryAssetHistory' => array(
                    'className' => 'InventoryAssetHistory',
                    'foreignKey' => 'user_id',
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

	var $hasAndBelongsToMany = array(

	);

	function getList()
	{
		$user= $this->find('all', array('conditions'=>array('role'=>5) ) );
		foreach( $user as $row){
			$user_list[$row['User']['id']] = $row['User']['name'];
		}
		return $user_list;
	}
        
        /**
         * is_active_sap Web Services
         * @param type $rut Rut del funcionario
         * @return int -1:Error, 0:Usuario activo,1:Usuario desvinculado
         */
        function is_active_sap($rut){
            App::import('Vendor', 'nusoap/lib/nusoap');
            $environment = Configure::read('App.environment');
            $wsdl = Configure::read('Soap.wsdl');
            $user = Configure::read('Soap.user');
            $pass = Configure::read('Soap.pass');
            $client = new nusoap_client($wsdl[$environment]['PA'],true);
            $client->setCredentials($user, $pass, "basic");
            $err = $client->getError();
            if ($err) {
                return -1;
            }
            $proxy = $client->getProxy();
            $result=$proxy->ZhcmInfPa(array('Rut'=>$rut,'Apellidomat'=>'','Apellidopat'=>'','Funcionario'=>array(),'Idorganigrama'=>''));
            if ($client->fault) {
                    return -1;
            } else {
                    // Check for errors
                    $err = $client->getError();
                    if ($err) {
                            // Display the error
                            return -1;
                    } else {
                            // Display the result
                            if($result['VarS'] == 0){
                                if($result['Funcionario']['item']['Estatus'] == 0){//Confirmar descripcion de estados en SAP codigo desvinculado
                                    return 1;
                                }else{
                                    return 0;
                                }
                            }else{
                                return -1;
                            }
                    }
            }
        }
	
}
?>