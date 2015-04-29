<?php
class ActasController extends AppController {

	var $name = 'Actas';
        var $uses = array('Acta', 'InventoryAssetAllocation','InventoryAsset','InventoryAssetHistory', 'User');
	var $components = array('RequestHandler');
        
//	function indexedit() {
//		$this->autoRender = false;
//		$action = $this->params['form']['oper'];
//		unset($this->params['form']['oper']);
//		$this->data['Region'] = $this->params['form'];
//
//		if($action == 'edit')	{
//			$this->Region->save($this->data, null, null);
//		}
//		else if($action == 'add') {
//			unset($this->data['Region']['id']);
//                        $this->data['Region']['is_ses']= $this->LdapAuth->user('is_ses');
//			$this->Region->create();
//			$this->Region->save($this->data, null, null);
//		}
//		else if($action == 'del') {
//			$this->Region->del($this->data['Region']['id']);
//		}
//	}
        
        function index(){
            
        }
        
        function returns(){
              $this->set('funcionarios', $this->User->find('all',  array('fields' => array('User.id', 'User.nombre'))));
        }
        
        function funcionarios() {

            $this->autoRender = false;
            $funcionarios = $this->User->find('all', array(
                'conditions' => array(
                    'User.role' => 4,
                    'User.is_ses' => $this->LdapAuth->user('is_ses'),
                    'User.is_active' => 1
                )
                    )
            );
            
            $resultadohtml = '<ul>';
            $fh = 0;
            foreach ($funcionarios as $f){
                $resultadohtml .= "<li><p><input class='check_funcionario' type='radio'   name ='recibe_sub".$fh."' value='".$f['User']['names']."' onclick='validate()' />".$f['User']['names']."</p></li>";
            
                $fh++;
            }
            $resultadohtml .="</ul>";
            
            echo ($resultadohtml);
    }

    function nulls($acta_id){
            $this->layout = 'ajax';
            $this->autoRender = false;
            Configure::write('debug', 0);
            $output['result'] = 'failure';
            if($this->RequestHandler->isAjax()) {
                $count = $this->InventoryAssetAllocation->find('count', array('conditions'=>array('InventoryAssetAllocation.acta_id'=>$acta_id)));
                $count_current = $this->InventoryAssetAllocation->find('count', array('conditions'=>array('InventoryAssetAllocation.acta_id'=>$acta_id, 'InventoryAssetAllocation.is_current'=>1)));
                if($count == $count_current){
                    $iaa = $this->InventoryAssetAllocation->find('all', array('conditions'=>array('InventoryAssetAllocation.acta_id'=>$acta_id)));
                    foreach ($iaa as $item) {
                        $this->log($item,'test');
                        $last_iaa = $this->InventoryAssetAllocation->find('first',array('conditions'=>array('InventoryAssetAllocation.inventory_asset_id'=>$item['InventoryAsset']['id'], 'InventoryAssetAllocation.is_current'=>0),'order'=>array('InventoryAssetAllocation.id DESC')));
                        if(!empty($last_iaa)){
                            $this->InventoryAssetAllocation->set_not_current($item['InventoryAsset']['id']);
                            $new_iaa['InventoryAssetAllocation'] = $last_iaa['InventoryAssetAllocation'];
                            unset($new_iaa['InventoryAssetAllocation']['id']);
                            unset($new_iaa['InventoryAssetAllocation']['created']);
                            unset($new_iaa['InventoryAssetAllocation']['modified']);
                            $new_iaa['InventoryAssetAllocation']['is_current'] = 1;
                            $this->InventoryAssetAllocation->create();
                            $this->InventoryAssetAllocation->save($new_iaa);
                            $this->InventoryAsset->id = $item['InventoryAsset']['id'];
                            if($last_iaa['InventoryAssetHistory']['type'] == 3){//si el bien anteriormente estaba liberado lo dejo con estado no asignado
                                $this->InventoryAsset->saveField('status', 0);
                            }else if($last_iaa['InventoryAssetHistory']['type'] == 1){//si el bien anteriormente estaba asignado lo dejo con estado asignado
                                $this->InventoryAsset->saveField('status', 1);
                            }
                            $this->InventoryAssetHistory->id = $item['InventoryAssetHistory']['id'];
                            $this->InventoryAssetHistory->saveField('comment',$item['InventoryAssetHistory']['comment'].'-Anulada');
                        }else{//NO tiene registro previo es anulada la asignacion actual y el bien es colocado como No asignado
                            $this->InventoryAssetAllocation->set_not_current($item['InventoryAsset']['id']);
                            $this->InventoryAsset->id = $item['InventoryAsset']['id'];
                            $this->InventoryAsset->saveField('status', 0);
                            $this->InventoryAssetHistory->id = $item['InventoryAssetHistory']['id'];
                            $this->InventoryAssetHistory->saveField('comment',$item['InventoryAssetHistory']['comment'].'-Anulada');
                        }
                    }
                    $this->Acta->id=$acta_id;
                    $this->Acta->saveField('status', 0);
                    $output['result'] = 'success';
                }else{
                    $output['message'] = 'No se puede anular ya que la información relacionada con algunos bienes no esta vigente';
                }
            }
            echo json_encode($output);
        }

}
