<?php
class OfficesController extends AppController {

	var $name = 'Offices';
	
	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['Office'] = $this->params['form'];
                $output['result'] = 'failure';
		if($action == 'edit')	{
			if($this->Office->save($this->data, null, null)){
                            $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido editar el registro';
                        }
		}
		else if($action == 'add') {
			unset($this->data['Ofice']['id']);
                        $this->data['Office']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->Office->create();
			if($this->Office->save($this->data, null, null)){
                           $output['result']='success';
                        }else{
                            $output['result']='failure';
                            $output['msg'] = 'No se ha podido editar el registro';
                        }
		}
		else if($action == 'del') {
			$this->Office->del($this->data['Office']['id']);
		}
                return json_encode($output);
	}

}
