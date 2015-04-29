<?php
class SubClassesController extends AppController {

	var $name = 'SubClasses';
	var $helpers = array('Html', 'Form');
	var $components = array('RequestHandler');

	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['SubClass'] = $this->params['form'];

		if($action == 'edit')	{
			$this->SubClass->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['SubClass']['id']);
                        $this->data['SubClass']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->SubClass->create();
			$this->SubClass->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->SubClass->del($this->data['SubClass']['id']);
		}
	}
        
        function subclassnamecheck() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
                $this->autoRender=false;
		$output['result'] = 'failure';

		if($this->RequestHandler->isAjax()) {
			$classname = $this->params['form']['classname'];
                        $subclassname = $this->params['form']['subclassname'];
			$id = $this->params['form']['id'];
                        $subclass = $this->SubClass->find('first', array('conditions'=>array('SubClass.name'=>$subclassname, 'SubClass.m_class_id'=>$classname)));
			if(empty($subclass)) $output['result'] = 'success';
			else if($subclass['SubClass']['id'] == $id) $output['result'] = 'success';
		}
		echo json_encode($output);
	}
}
?>