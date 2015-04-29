<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $uses = array('Region', 'User', 'InventoryAsset', 'InventoryAssetHistory','Transaction');
        var $components = array();

	function my_inventory_assets() { //Mis Bienes de Inventario
		
	}
        
        function code_asset(){
            Configure::write('debug', 0);
            $this->layout = 'ajax';
            $this->set('data', $this->params['form']['csvBuffer']);
            $this->set('filename','export');
        }
        
        function leaf_mural(){
            Configure::read('debug', 0);
            $regiones = $this->Region->find('list',array('conditions'=>array('Region.is_ses'=>  $this->LdapAuth->user('is_ses'))));
            $this->set('regiones', $regiones);
        }
        
        function inventory_movements(){
            
        }
        
        function all_data(){
            
        }
        
        function bitacora(){
            
        }
        
        function conta_report(){
            
        }
        
        function generate($type, $format){
            Configure::read('debug', 0);
            if (!$type or !$format) {
                $this->Session->setFlash('Disculpa, no existe este Reporte.', 'default', array('class' => 'error'));
                $this->redirect(array('controller' => 'users', 'action'=>'dashboard'));
            }
            $data = array();
            
            if($type == 'actas'){
                $acta_id = $this->params['form']['acta_id'];
                $tipo_acta = $this->params['form']['type'];
                $sub = $this->params['form']['sub'];
                $recibecaja = $this->params['form']['recibecaja'];
                $entrega=str_replace("\r","<br>",$this->params['form']['entrega']);
                App::import('Model', 'Acta');
                $this->Acta = new Acta();
                $this->Acta->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'Receive'=>array(
                        'Section',
                        'Department',
                        'Unit'
                    ),
                    'InventoryAssetAllocation'=>array(
                        'InventoryAsset'=>array(
                            'Asset'
                        )
                    )
                );
                $params['conditions'] = array(
                    'Acta.id' => $acta_id
                );
                $data = $this->Acta->find('first', $params);
                $this->layout = $format;
                $this->set('filename', 'acta');
                $this->set('sub', $sub);
                $this->set('entrega', $entrega);
                $this->set('recibecaja', $recibecaja);
                $this->set('tipo_acta', $tipo_acta);
            }else if($type == 'hoja_mural'){
                $office_id = $this->params['form']['office'];
                $sub = $this->params['form']['sub'];
                $entrega=str_replace("\r","<br>",$this->params['form']['entrega']);
                $this->User->InventoryAssetAllocation->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'User',
                    'Office'=>array('User'),
                    'InventoryAsset'=>array('Asset')
                );
                $params['conditions'] = array(
                    'InventoryAssetAllocation.office_id'=>$office_id,
                    'InventoryAssetAllocation.is_current'=>1
                );
                $aux = $this->User->InventoryAssetAllocation->find('all', $params);
                $data['office'] = $aux[0]['Office']['number'];
                $data['responsable'] = $aux[0]['Office']['User']['names'] . ' ' . $aux[0]['Office']['User']['primary_last_name'] . ' ' . $aux[0]['Office']['User']['second_last_name'];
                
                foreach ($aux as $item) {
                    $data['items'][$item['User']['id']][] = $item;
                }
               $this->layout = $format;
               $this->set('filename', 'hoja_mural');
               $this->set('sub', $sub);
                $this->set('entrega', $entrega);
            }else if($type == 'alta'){
                $subclase = $this->params['form']['subclase'];
                $clase = $this->params['form']['clase'];
                $tipo =  $this->params['form']['tipo_de'];
                $filter_price = $this->params['form']['price'];
                $params['conditions']['InventoryAsset.status !='] = 2;
                if($tipo !=''){
                    $t = $this->InventoryAsset->TransactionDetail->Transaction->find('list',array('recursive'=>-1,'fields'=>array('Transaction.id'),'conditions'=>array('Transaction.type'=>$tipo)));
                    $td = $this->InventoryAsset->TransactionDetail->find('list',array('recursive'=>-1, 'fields'=>array('TransactionDetail.id'),'conditions'=>array('TransactionDetail.transaction_id'=>$t)));
                    $params['conditions']['InventoryAsset.transaction_detail_id']=$td;
                }
                if(!empty($subclase))
                    $params['conditions']['Asset.sub_class_id'] = $subclase;
                else if(!empty($clase))
                    $params['conditions']['Asset.m_class_id'] = $clase;
                if(!empty($this->params['form']['desde'])){
                    $params['conditions']['InventoryAsset.created >='] = $this->dateToSql($this->params['form']['desde'],true,true);
                }
                if(!empty($this->params['form']['hasta'])){
                    $params['conditions']['DATE(InventoryAsset.created) <='] = $this->dateToSql($this->params['form']['hasta'],true,true);
                }
                $this->InventoryAsset->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'TransactionDetail'=>array('Transaction'=>array('PurchaseOrder')),
                    'Asset'=>array(
                        'MClass',
                        'SubClass'
                    )
                );                
                $data = $this->InventoryAsset->find('all',$params);
                $this->layout = $format;
                $this->set('filter_price',$filter_price);
                $this->set('filename', 'alta');
            }else if($type == 'baja'){
                $subclase = $this->params['form']['subclase'];
                $clase = $this->params['form']['clase'];
                $tipo =  $this->params['form']['tipo_de'];
                $filter_price = $this->params['form']['price'];
                $params['conditions']['InventoryAsset.status'] = 2;
                if($tipo !='')
                    $params['conditions']['InventoryAssetDisposal.type'] = $tipo;
                if(!empty($subclase))
                    $params['conditions']['Asset.sub_class_id'] = $subclase;
                else if(!empty($clase))
                    $params['conditions']['Asset.m_class_id'] = $clase;
                if(!empty($this->params['form']['desde'])){
                    $params['conditions']['InventoryAssetDisposal.created >='] = $this->dateToSql($this->params['form']['desde'],true,true);
                }
                if(!empty($this->params['form']['hasta'])){
                    $params['conditions']['DATE(InventoryAssetDisposal.created) <='] = $this->dateToSql($this->params['form']['hasta'],true,true);
                }
                $this->InventoryAsset->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'InventoryAssetDisposal',
                    'Asset'=>array(
                        'MClass',
                        'SubClass'
                    )
                );                

                $data = $this->InventoryAsset->find('all',$params);
                $this->layout = $format;
                $this->set('filename', 'baja');
                $this->set('filter_price',$filter_price);
            }else if($type=='all'){
                $this->InventoryAsset->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'TransactionDetail'=>array('Transaction'),
                    'InventoryAssetDisposal',
                    'InventoryAssetAllocation'=>array(
                        'conditions'=>array('InventoryAssetAllocation.is_current'=>1),
                        'Office',
                        'Floor',
                        'Address',
                        'City',
                        'Region'
                    ),
                    'Asset'=>array(
                        'MClass',
                        'SubClass'
                    )
                ); 
                $filter_price = $this->params['form']['price'];
                $params['conditions']['InventoryAsset.is_ses'] = 1;
                if(!empty($this->params['form']['desde'])){
                    $params['conditions']['InventoryAsset.created >='] = $this->dateToSql($this->params['form']['desde'],true,true);
                }
                if(!empty($this->params['form']['hasta'])){
                    $params['conditions']['DATE(InventoryAsset.created) <='] = $this->dateToSql($this->params['form']['hasta'],true,true);
                }
                $data = $this->InventoryAsset->find('all',$params);
                $this->layout = $format;
                $this->set('filename', 'dump');
                $this->set('filter_price',$filter_price);
            }else if($type == 'bitacora'){
                $this->InventoryAssetHistory->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'InventoryAsset'=>array(
                        'Asset',
                        'TransactionDetail'=>array('Transaction')
                    ),
                    'InventoryAssetDisposal',
                    'InventoryAssetAllocation'=>array(
                        'Office',
                        'Floor',
                        'Address',
                        'City',
                        'Region',
                        'User'
                    ),
                    'User'
                );
                $assets = $this->params['form']['assets'];
                if(!empty($assets)){
                    
                    //transformo assets en un arreglo para que pueda exportar la cantidad requerida de bienes
                    $bienes = explode(",", $assets);           
                    $params ['conditions']['InventoryAsset.id'] = $bienes;
    
                }
                $tipo_bitacora = $this->params['form']['tipo_bitacora'];
                if($tipo_bitacora != '*'){
                    $params['conditions']['InventoryAssetHistory.type'] = $tipo_bitacora;
                }
                $params['conditions']['InventoryAsset.is_ses'] = $this->LdapAuth->user('is_ses');
                if(!empty($this->params['form']['desde'])){
                    $params['conditions']['InventoryAssetHistory.created >='] = $this->dateToSql($this->params['form']['desde'],true,true);
                }
                if(!empty($this->params['form']['hasta'])){
                    $params['conditions']['DATE(InventoryAssetHistory.created) <='] = $this->dateToSql($this->params['form']['hasta'],true,true);
                }
                
                $data = $this->InventoryAssetHistory->find('all',$params);
                
               
               
               
               
                $this->layout = $format;
                $this->set('filename', 'bitacora');
                $this->set('tipo_bitacora', $tipo_bitacora);
            }else if($type == 'contabilidad'){
                $this->InventoryAsset->Behaviors->attach('Containable');
                $params['contain'] = array(
                    'Asset'=>array(
                        'MClass',
                        'SubClass'
                    )
                );
                $subclase = $this->params['form']['subclase'];
                $clase = $this->params['form']['clase'];
                $document_number = $this->params['form']['document_number'];
                $code = $this->params['form']['code'];
                $filter_price = $this->params['form']['price'];
                if(!empty($code))
                    $params['conditions']['InventoryAsset.code'] = $code;
                if(!empty($document_number)){
                    $t_id = $this->Transaction->find('list', array('fields'=>array('Transaction.id'),'recursive'=>-1,'conditions'=>array('Transaction.document_number'=>$document_number)));
                    $td_id = $this->Transaction->TransactionDetail->find('list', array('fields'=>array('TransactionDetail.id'),'recursive'=>-1,'conditions'=>array('TransactionDetail.transaction_id'=>$t_id)));
                    $params['conditions']['InventoryAsset.transaction_detail_id']=  array_values($td_id);
                    
                }
                if(!empty($subclase))
                    $params['conditions']['Asset.sub_class_id'] = $subclase;
                else if(!empty($clase))
                    $params['conditions']['Asset.m_class_id'] = $clase;
                if(!empty($this->params['form']['desde'])){
                    $params['conditions']['InventoryAsset.created >='] = $this->dateToSql($this->params['form']['desde'],true,true);
                }
                if(!empty($this->params['form']['hasta'])){
                    $params['conditions']['DATE(InventoryAsset.created) <='] = $this->dateToSql($this->params['form']['hasta'],true,true);
                }
                $params['conditions']['InventoryAsset.is_ses'] = $this->LdapAuth->user('is_ses');
                $params['conditions']['InventoryAsset.is_depreciate'] = 1;
                $data = $this->InventoryAsset->find('all',$params);
           
                $this->layout = $format;
                $this->set('filename', 'contabilidad');
                $this->set('filter_price',$filter_price);
            }
            $this->set('type', $type);
            $this->set('format', $format);
            $this->set('data', $data);
        }
	
}
?>