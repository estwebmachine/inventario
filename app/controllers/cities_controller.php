<?php
class CitiesController extends AppController {

	var $name = 'Cities';
        var $uses = "City";
        
	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['City'] = $this->params['form'];
                $output['result'] ='failure';
		if($action == 'edit')	{
			if($this->City->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido editar el registro';
                        }
		}
		else if($action == 'add') {
			unset($this->data['City']['id']);
                        $this->data['City']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->City->create();
			if($this->City->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido agregar el registro';
                        }
		}
		else if($action == 'del') {
			$this->City->del($this->data['City']['id']);
		}
                return json_encode($output);
	}
	
}
