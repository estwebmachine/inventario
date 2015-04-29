<?php
class RegionsController extends AppController {

	var $name = 'Regions';
	
	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['Region'] = $this->params['form'];

		if($action == 'edit')	{
			$this->Region->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['Region']['id']);
                        $this->data['Region']['is_ses']= $this->LdapAuth->user('is_ses');
			$this->Region->create();
			$this->Region->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->Region->del($this->data['Region']['id']);
		}
	}

}
