<?php
class AddressesController extends AppController {

	var $name = 'Addresses';
        var $uses = "Address";
        
	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['Address'] = $this->params['form'];
                $output['result'] ='failure';
                
		if($action == 'edit')	{
			if($this->Address->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido editar el registro';
                        }
		}
		else if($action == 'add') {
			unset($this->data['Address']['id']);
                        $this->data['Address']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->Address->create();
			if($this->Address->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido agregar el registro';
                        }
		}
		else if($action == 'del') {
			$this->Address->del($this->data['Address']['id']);
		}
                return json_encode($output);
	}
	
}
