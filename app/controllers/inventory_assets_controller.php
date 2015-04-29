<?php

class InventoryAssetsController extends AppController {

    var $name = 'InventoryAssets';
    var $components = array('RequestHandler','File');
    var $uses = array('InventoryAsset', 'Region','Acta','InventoryAssetAllocation', 'User','InventoryAssetDisposal');

    function enter() { //Alta bienes inventario
    }

    function index() {
        
    }

    function locations() {
        
    }

    function terminate() { //Baja de bienes inventario
		
    }
        
    function release() { //Asignación de bienes inventario
        $regiones = $this->Region->find('list',array('conditions'=>array('Region.is_ses'=>  $this->LdapAuth->user('is_ses'))));
        $this->set('regiones', $regiones);
    }

    function allocate() {
        Configure::write('debug', 0);
        $this->autoRender = false;
        $output['result'] = 'failure';

        if ($this->RequestHandler->isAjax()) {
            //validar
            $validates = false;
            $lugar = $this->params['form']['lugar'];
            unset($this->params['form']['lugar']);
            if ($this->params['form']['region_id'] == '') {
                $output['message'] = 'Seleccione Región.';
            } else if ($this->params['form']['city_id'] == '') {
                $output['message'] = 'Seleccione Ciudad.';
            } else if ($this->params['form']['address_id'] == '') {
                $output['message'] = 'Seleccione Dirección.';
            } else if ($this->params['form']['floor_id'] == '') {
                $output['message'] = 'Seleccione Piso.';
            } else if ($this->params['form']['office_id'] == '') {
                $output['message'] = 'Seleccione Oficina.';
            } else if ($this->params['form']['user_id'] == '') {
                $output['message'] = 'Seleccione Responsable.';
            } else {
                $validates = true;
            }

            //si valida guardo
            if ($validates) {
                $output['message'] = '';
                $folio = $this->Acta->lastFolio($this->LdapAuth->user('is_ses'));
                $folio ++;
                
                 $last_acta = $this->Acta->find('first',array(
                      'recursive'=>-1,
                      'conditions'=>array(
                          'Acta.type' => 1,
                          'Acta.is_ses'=>  $this->LdapAuth->user('is_ses')),    
                      'order' => array(
                          'Acta.created' => 'desc'
                     )
                 )
                    
                   
                 );
                  
                $last_acta =  $last_acta['Acta']['number'] + 1;
                                
                $acta = array(
                    'Acta'=>array(
                        'type' => 1,//Acta asignacion
                        'status' => 1,//OK
                        'number' => $last_acta,
                        'folio_ses' => $folio,
                        'folio_sss' => $folio,
                        'is_ses' => $this->LdapAuth->user('is_ses'),
                        'assigned_id' => $this->LdapAuth->user('id'),
                        'receive_id' => $this->params['form']['user_id']
                    )
                );
                $this->Acta->save($acta);
                $acta_id = $this->Acta->id;
                $ids = $this->params['form']['ids'];
                unset($this->params['form']['ids']);
                //guardo el valor is_current (esta es la asignación actual)
                $this->params['form']['is_current'] = 1;
                $this->params['form']['is_ses'] = $this->LdapAuth->user('is_ses');
                $this->params['form']['acta_id'] = $acta_id;
                
                foreach ($ids as $id) {
                    $this->params['form']['inventory_asset_id'] = $id;
                    $this->data = array(
                        'InventoryAssetAllocation' => $this->params['form'],
                        'InventoryAssetHistory' => array(
                            'inventory_asset_id' => $id,
                            'type' => 1, // 0: ingreso, 1: asignación, 2: baja
                            'comment' => 'Asignación de bien',
                            'user_id' => $this->LdapAuth->user('id')                            
                        )
                        
                    );
                    
                    //actualizo el valor is_current de la asignación anterior a 0 (ya no es la asignación actual)
                    $this->InventoryAssetAllocation->set_not_current($id);
                    $this->InventoryAsset->id = $id;
                    $this->InventoryAsset->set('situation', 'Bueno');
                    $this->InventoryAsset->save();

                    if ($this->InventoryAssetAllocation->saveAll($this->data) and $this->InventoryAsset->saveField('status', 1)) {
                        $output['result'] = 'success';
                    }
                }
            }
        }
//
        echo json_encode($output);
    }
    
    function deallocate2() {
        Configure::write('debug', 0);
        $this->autoRender = false;
        $output['result'] = 'failure';

        if ($this->RequestHandler->isAjax()) {
            //validar
            $output['message'] = 'No se han podido liberar los bienes, ya que algunos no estan asignados o no pertenecen al mismo funcionario';
            
            $ids = $this->params['form']['ids'];
            unset($this->params['form']['ids']);
            $user = $this->is_assigned_to_same_user($ids);//valida que todos los bienes seleccionados esten asignados al mismo usuario
            $aux = $this->User->findById($user);
            $params['conditions']['User.role'] = 2;//jefe
            if(!empty($aux['User']['unit_id'])){
                $params['conditions']['User.unit_id'] = $aux['User']['unit_id'];
            }else if(!empty($aux['User']['department_id'])){
                $params['conditions']['User.department_id'] = $aux['User']['department_id'];
            }else if(!empty($aux['User']['section_id'])){
                $params['conditions']['User.section_id'] = $aux['User']['section_id'];
            }
            $jefe = $this->User->find('first', $params);
            if(!empty($user) && !empty($jefe)){
                $folio = $this->Acta->lastFolio($this->LdapAuth->user('is_ses'));
                $folio ++;
                
                $last_acta = $this->Acta->find('first',array(
                      'recursive'=>-1,
                      'conditions'=>array(
                          'Acta.type' => 0,
                          'Acta.is_ses'=>  $this->LdapAuth->user('is_ses')),    
                      'order' => array(
                          'Acta.created' => 'desc'
                     )
                 )                                       
                 );
                  
                $last_acta =  $last_acta['Acta']['number'] + 1;
                $acta = array(
                    'Acta'=>array(
                        'type' => 0,//Acta asignacion
                        'status' => 1,//OK
                        'number' => $last_acta,
                        'folio_ses' => $folio,
                        'folio_sss' => $folio,
                        'is_ses' => $this->LdapAuth->user('is_ses'),
                        'assigned_id' => $this->LdapAuth->user('id'),
                        'receive_id' => $user
                    )
                );
                $this->Acta->save($acta);
                $acta_id = $this->Acta->id;
                $this->params['form']['is_current'] = 1;
                $this->params['form']['is_ses'] = $this->LdapAuth->user('is_ses');
                $this->params['form']['user_id'] = $jefe['User']['id'];
                $this->params['form']['acta_id'] = $acta_id;
                foreach ($ids as $id) {
                    $this->params['form']['inventory_asset_id'] = $id;
                    $this->data = array(
                        'InventoryAssetAllocation' => $this->params['form'],
                        'InventoryAssetHistory' => array(
                            'inventory_asset_id' => $id,
                            'type' => 3, // 0: ingreso, 1: asignación, 2: baja, 3:desasignacion
                            'comment' => 'Liberación de bien',
                            'user_id' => $this->LdapAuth->user('id')
                        )
                    );

                    //actualizo el valor is_current de la asignación anterior a 0 (ya no es la asignación actual)
                    $this->InventoryAssetAllocation->set_not_current($id);
                    $this->InventoryAsset->id = $id;

                    if ($this->InventoryAssetAllocation->saveAll($this->data) and $this->InventoryAsset->saveField('status', 0)) {
                        $output['result'] = 'success';
                    }
                }
            }
        }

        echo json_encode($output);
    }
    
    
     function deallocate() {
        Configure::write('debug', 0);
        $this->autoRender = false;
        $output['result'] = 'failure';

        if ($this->RequestHandler->isAjax()) {
            //validar
            $output['message'] = 'No se han podido liberar los bienes, ya que algunos no estan asignados o no pertenecen al mismo funcionario';
            
            $ids = $this->params['form']['ids'];
            unset($this->params['form']['ids']);
            $user = $this->is_assigned_to_same_user($ids);//valida que todos los bienes seleccionados esten asignados al mismo usuario
            $aux = $this->User->findById($user);
            $params['conditions']['User.role'] = 2;//jefe
            if(!empty($aux['User']['unit_id'])){
                $params['conditions']['User.unit_id'] = $aux['User']['unit_id'];
            }else if(!empty($aux['User']['department_id'])){
                $params['conditions']['User.department_id'] = $aux['User']['department_id'];
            }else if(!empty($aux['User']['section_id'])){
                $params['conditions']['User.section_id'] = $aux['User']['section_id'];
            }
            $jefe = $this->User->find('first', $params);
            if(!empty($user) && !empty($jefe)){
                $folio = $this->Acta->lastFolio($this->LdapAuth->user('is_ses'));
                $folio ++;
                $acta = array(
                    'Acta'=>array(
                        'type' => 0,//Acta asignacion
                        'status' => 1,//OK
                        'folio_ses' => $folio,
                        'folio_sss' => $folio,
                        'is_ses' => $this->LdapAuth->user('is_ses'),
                        'assigned_id' => $this->LdapAuth->user('id'),
                        'receive_id' => $user
                    )
                );
                $this->Acta->save($acta);
                $acta_id = $this->Acta->id;
                $this->params['form']['is_current'] = 1;
                $this->params['form']['is_ses'] = $this->LdapAuth->user('is_ses');
                $this->params['form']['user_id'] = $jefe['User']['id'];
                $this->params['form']['acta_id'] = $acta_id;
                foreach ($ids as $id) {
                    $this->params['form']['inventory_asset_id'] = $id;
                    $this->data = array(
                        'InventoryAssetAllocation' => $this->params['form'],
                        'InventoryAssetHistory' => array(
                            'inventory_asset_id' => $id,
                            'type' => 3, // 0: ingreso, 1: asignación, 2: baja, 3:desasignacion
                            'comment' => 'Liberación de bien',
                            'user_id' => $this->LdapAuth->user('id')
                        )
                    );

                    //actualizo el valor is_current de la asignación anterior a 0 (ya no es la asignación actual)
                    $this->InventoryAssetAllocation->set_not_current($id);
                    $this->InventoryAsset->id = $id;

                    if ($this->InventoryAssetAllocation->saveAll($this->data) and $this->InventoryAsset->saveField('status', 0)) {
                        $output['result'] = 'success';
                    }
                }
            }
        }

        echo json_encode($output);
    }
    /**
     * Devuelve true si los bienes esta asignados
     * @return boolean
     */
    function is_assigned_to_same_user($ids){
        Configure::write('debug', 0);
        $this->autoRender = false;
        $flag = true;
        $user_old = '';
        foreach ($ids as $id) {
            $ia = $this->InventoryAsset->findById($id);
            if($ia['InventoryAsset']['status'] == 1){//asignado
                $iaa = $this->InventoryAssetAllocation->find('first', array('conditions'=>array('InventoryAssetAllocation.is_current'=>1, 'InventoryAssetAllocation.inventory_asset_id'=>$id)));
                if($iaa['InventoryAssetAllocation']['user_id'] == $user_old || $flag){
                    $flag = false;
                    $user_old = $iaa['InventoryAssetAllocation']['user_id'];
                    
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        return $user_old;
    }

    function indexedit() {
        $this->autoRender = false;
        $action = $this->params['form']['oper'];
        unset($this->params['form']['oper']);
        if($this->params['form']['is_depreciate'] == 'Sí')
            $this->params['form']['is_depreciate'] = 1;
        else
            $this->params['form']['is_depreciate'] = 0;
        $this->data['InventoryAsset'] = $this->params['form'];

        if ($action == 'edit') {
            $this->InventoryAsset->save($this->data, null, null);
        }
//		else if($action == 'add') {
//			unset($this->data['Provider']['id']);
//                        $this->data['Provider']['is_ses'] = $this->LdapAuth->user('is_ses');
//			$this->InventoryAsset->create();
//			$this->InventoryAsset->save($this->data, null, null);
//		}
//		else if($action == 'del') {
//			$this->InventoryAsset->del($this->data['Provider']['id']);
//		}
    }

    function migration_ajax($asset_id = null, $cant = null) {
        $this->autoRender = false;
        Configure::write('debug', 0);
        $asset_id = $this->params['form']['asset_id'];
        $cant = $this->params['form']['amount'];
        if ($this->RequestHandler->isAjax()) {
            $output['result'] = "failure";
            $output['msg'] = "No se han podido cargar los bienes, intentelo nuevamente";
            if ($this->InventoryAsset->processMigration($asset_id, $cant, $this->LdapAuth->user('is_ses'),$this->LdapAuth->user('id'))) {
                $output['result'] = "success";
            }
            return json_encode($output);
        }
    }
    
    function dispose() {
            $this->layout = 'ajax';
            $this->autoRender = false;
            Configure::write('debug', 0);
            $output['result'] = 'failure';

            if($this->RequestHandler->isAjax()) {
                    //validar
                if($this->params['form']['type'] == '') {
                        $output['message'] = 'Seleccione tipo de Baja.';
                } else if($this->params['form']['comment'] == '') {
                        $output['message'] = 'Ingrese Comentario.';
                } else {
                        $ids = $this->params['form']['ids'];
                        unset( $this->params['form']['ids'] );
                        $file = $this->data['Document']['pdf'];
                        unset($this->data['Document']);

                        foreach($ids as $id) {
                                $this->params['form']['inventory_asset_id'] = $id;
                                $this->data = array(
                                        'InventoryAssetDisposal' => $this->params['form'],
                                        'InventoryAssetHistory' => array(
                                                'inventory_asset_id' => $id,
                                                'type' => 2,// 0: ingreso, 1: asignación, 2: baja
                                                'comment' => 'Baja de bien',
                                                'user_id' => $this->LdapAuth->user('id')
                                        )
                                );

                                //actualizo el valor is_current de la última asignación a 0 (ya no existe asignación actual)
                                $this->InventoryAssetAllocation->set_not_current($id);

                                $this->InventoryAsset->id = $id;
                                $this->InventoryAsset->saveField('situation', 'En Desuso');

                                if( $this->InventoryAssetDisposal->saveAll($this->data) and $this->InventoryAsset->saveField('status', 2) ) {
                                    
                                        $output['result'] = 'success';
                                        $this->File->upload_pdf($file,$this->InventoryAsset->id,'AD');
                                }
                        }

                }
            }

            echo json_encode($output);
	}

}
