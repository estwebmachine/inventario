<?php
class FloorsController extends AppController {

	var $name = 'Floors';

	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['Floor'] = $this->params['form'];
                $output['result'] ='failure';
                
		if($action == 'edit')	{
			if($this->Floor->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido editar el registro';
                        }
		}
		else if($action == 'add') {
			unset($this->data['Floor']['id']);
                        $this->data['Floor']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->Floor->create();
			if($this->Floor->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido agregar el registro';
                        }
		}
		else if($action == 'del') {
			$this->Floor->del($this->data['Floor']['id']);
		}
                return json_encode($output);
	}
	
}
