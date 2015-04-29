<?php
class MClassesController extends AppController {

	var $name = 'MClasses';
	var $helpers = array('Html', 'Form');
	var $components = array('RequestHandler');

	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['MClass'] = $this->params['form'];

		if($action == 'edit')	{
			$this->MClass->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['MClass']['id']);
                        $this->data['MClass']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->MClass->create();
			$this->MClass->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->MClass->del($this->data['MClass']['id']);
		}
	}
        
        function classnamecheck() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
                $this->autoRender=false;
		$output['result'] = 'failure';

		if($this->RequestHandler->isAjax()) {
			$classname = $this->params['form']['classname'];
			$id = $this->params['form']['id'];
			$class = $this->MClass->findByName($classname);
			if(empty($class)) $output['result'] = 'success';
			else if($class['MClass']['id'] == $id) $output['result'] = 'success';
		}
		echo json_encode($output);
	}
}
?>