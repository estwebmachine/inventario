<?php
class TestController extends AppController {

	var $name = 'Test';
	var $uses = array('InventoryAsset','Actas','User');

        function beforeFilter() {
		parent::beforeFilter();
		$this->LdapAuth->allow('update');
	}
        
	function index($model) {
            $model = Inflector::camelize($model);
            App::import('Model',$model);
            $this->$model = new $model();
            $this->autoRender = false;
            Configure::write('debug', 0);
            echo $model;
            print_r($this->$model->find('all',array('recursive'=>1)));
	}
        
        function byCode($code){
            $this->autoRender = false;
            Configure::write('debug', 0);
            App::import('Model','InventoryAsset');
            $this->InventoryAsset = new InventoryAsset();
            print_r($this->InventoryAsset->findByCode($code));
        }
        
        function update(){
            $this->autoRender = false;
            $data = array(
                'rut' => '9999999-p',
                'names' => 'PRUEBA USER2'
            );
            $this->User->create();
            $this->User->save($data);
            if(empty($this->User->id)){
                $user = $this->User->findByRut($data2['rut']);
                $user_id = $user['User']['id'];
                $this->User->id = $user_id;
                $this->User->save($data2);
            }
        }

}
?>