<?php
class InventoryAsset extends AppModel {
	var $name = 'InventoryAsset';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasOne = array(
		'InventoryAssetDisposal' => array(
			'className' => 'InventoryAssetDisposal',
			'foreignKey' => 'inventory_asset_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $belongsTo = array(
		'Asset' => array(
			'className' => 'Asset',
			'foreignKey' => 'asset_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'TransactionDetail' => array(
			'className' => 'TransactionDetail',
			'foreignKey' => 'transaction_detail_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
                'InventoryAssetAllocation' => array(
			'className' => 'InventoryAssetAllocation',
			'foreignKey' => 'inventory_asset_id',
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
		'InventoryAssetHistory' => array(
			'className' => 'InventoryAssetHistory',
			'foreignKey' => 'inventory_asset_id',
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
	);
	
	/**
	 * Procesa una transaccion e ingresa los productos a inventario asignando sus codigos
	 * y creando registro de ingreso en historial para cada uno de ellos
	 * 
	 * @param int $transid la id de la transacción a procesar 
	 */
	function processReception($transid, $is_ses, $user) {
		/**
		 * para cada uno de los detalles de la transacción:
		 * 1. identifico el producto (asset) del que se trata
		 * 2. busco el ultimo ingreso de este asset en tabla inventory_assets y rescato su codigo autoincrementable
		 * 3. le sumo 1 al codigo y lo asigno al inventory_asset que estoy creando
		 * 4. el estado del nuevo inventory_asset es 0: no asignado.
		 * 5. creo un historial de ingreso para cada uno.
		 */
		$data = array(); //guarda los datos a almacenar
		
		App::import('Model', 'Transaction'); 
		$this->Transaction = new Transaction;
		$this->Transaction->Behaviors->attach('Containable');
		
		$params = array(
			'conditions' => array(
				'Transaction.id' => $transid
			),
			'contain' => array(
				'TransactionDetail'=>array(
                                    'Asset'=>array(
                                        'SubClass'
                                    )
                                )
			)
		);
		$transaction = $this->Transaction->find('first', $params);
                //Validar archivo proveedor con detalle transaccion
//                $rows_data = array();
//		foreach($transaction['TransactionDetail'] as $detail) {
//                    $file_csv = WWW_ROOT . 'csv' . DS . 'CSV_' . $detail['id'] . '.csv';
//                    if(file_exists($file_csv)){
//                        $gestor = fopen($file_csv,'r');
//                        $filas = 0;
//                        while(($datos = fgetcsv($gestor,0,','))!==FALSE){
//                            $columnas = count($datos);
//                            if($columnas < 2 || $columnas >2)
//                                return array('error'=>$detail['id']);
//                            $filas ++;
//                            $rows_data[] = $datos;
//                        }
//                        fclose($gestor);
//                        if($filas > $detail['amount'] ){
//                            return array('error'=>$detail['id']);
//                        }
//                    }
//                }
		//ultimo indice de bienes de inventario
		$index = $this->lastAssetIndex($is_ses);
		//recorro los detalles
		foreach($transaction['TransactionDetail'] as $detail) {
                        $rows_data = array();
                        $file_csv = WWW_ROOT . 'csv' . DS . 'CSV_' . $detail['id'] . '.csv';
                        if(file_exists($file_csv)){
                            $gestor = fopen($file_csv,'r');
                            $filas = 0;
                            while(($datos = fgetcsv($gestor,0,','))!==FALSE){
                                $columnas = count($datos);
                                if($columnas < 2 || $columnas >2)
                                    return array('error'=>$detail['id']);
                                $filas ++;
                                $rows_data[] = $datos;
                            }
                            fclose($gestor);
                            if($filas > $detail['amount'] ){
                                return array('error'=>$detail['id']);
                            }
                        }
			$asset_id = $detail['asset_id'];
			//genero un bien de inventario distinto segun la cantidad de este detalle
			for($i = 0; $i < $detail['amount']; $i++) {
				$index++;
                                $serial_csv = NULL;
                                $detail_csv = NULL;
                                if(!empty($rows_data))
                                    list($serial_csv, $detail_csv) = $rows_data[$i];
				$data[] = array(
					'InventoryAsset' => array( //datos bien de inventario
						'asset_id' => $asset_id,
						'code' => $this->codeGenerate($index),
						'index_ses' => $index,
                                                'index_sss' => $index,
                                                'serial' => $serial_csv,
                                                'detail' => $detail_csv,
                                                'life'=>$detail['Asset']['SubClass']['life'],
                                                'residual_value'=>$detail['Asset']['SubClass']['residual_value'],
						'status' => 0, // 0: ingreso, 1: asignación, 2: baja
						'original_price' => $detail['price'],
                                                'transaction_detail_id' => $detail['id'],
                                                'is_ses' => $is_ses
					),
					'InventoryAssetHistory' => array( //datos historial de bien de inventario
						0 => array(
							'type' => 0,// 0: ingreso, 1: asignación, 2: baja
							'comment' => 'Ingreso de bien a inventario',
                                                        'user_id' =>$user
						) 
					)
				);
			}
			
		}                
		//guardo los bienes de inventario y sus historiales
		foreach($data as $d) $this->saveAll($d);
		return $data;
	}
	
        /**
	 * Procesa una transaccion masiva e ingresa los productos a inventario asignando sus codigos
	 * y creando registro de ingreso en historial para cada uno de ellos
	 * 
	 *
	 */
	function processMigration($asset_id, $amount,$is_ses,$user) {
		/**
		 * para cada uno de los detalles de la transacción:
		 * 1. identifico el producto (asset) del que se trata
		 * 2. busco el ultimo ingreso de este asset en tabla inventory_assets y rescato su codigo autoincrementable
		 * 3. le sumo 1 al codigo y lo asigno al inventory_asset que estoy creando
		 * 4. el estado del nuevo inventory_asset es 0: no asignado.
		 * 5. creo un historial de ingreso para cada uno.
		 */
		$data = array(); //guarda los datos a almacenar
		//ultimo indice de bienes de inventario
		$index = $this->lastAssetIndex($is_ses);
			
                //genero un bien de inventario distinto segun la cantidad de este detalle
                for($i = 0; $i < $amount; $i++) {
                        $index++;

                        $data[] = array(
                                'InventoryAsset' => array( //datos bien de inventario
                                        'asset_id' => $asset_id,
                                        'code' => $this->codeGenerate($index),
                                        'index_ses' => $index,
                                        'index_sss' => $index,
                                        'is_ses' => $is_ses,
                                        'status' => 0 // 0: ingreso, 1: asignación, 2: baja
                                ),
                                'InventoryAssetHistory' => array( //datos historial de bien de inventario
                                        0 => array(
                                                'type' => 0,// 0: ingreso, 1: asignación, 2: baja 3:liberacion
                                                'comment' => 'Ingreso de bien a inventario',
                                                'user_id' => $user
                                        ) 
                                )
                        );
                }
			
		
		
		//guardo los bienes de inventario y sus historiales
		foreach($data as $d) $this->saveAll($d);
		return $data;
	}
        
	function lastAssetIndex($is_ses) {
		$last = $this->find('first', array('recursive'=>-1,'conditions'=>array('InventoryAsset.is_ses'=>  $is_ses),'order' => array('InventoryAsset.id DESC')));
                $index = 0;
		if( !empty($last) ) $index = $is_ses==1?$last['InventoryAsset']['index_ses']:$last['InventoryAsset']['index_sss'];
		return $index;
	}
	
	function codeGenerate($index) {
		//$code = '61001000';
		$index = strval($index);
                $largo = Configure::read('lenght_code');              
		$nzeros = $largo - strlen($index);
		for($i = 0; $i < $nzeros; $i++) $code .= '0';
		$code .= $index;
		return $code;
	}

}
