<?php
class ProvidersController extends AppController {

	var $name = 'Providers';
	var $helpers = array('Html', 'Form');
        var $components = array('RequestHandler');
        
	function index() {

	}

	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['Provider'] = $this->params['form'];

		if($action == 'edit')	{
			$this->Provider->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['Provider']['id']);
                        $this->data['Provider']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->Provider->create();
			$this->Provider->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->Provider->del($this->data['Provider']['id']);
		}
	}

	function search() {
		$this->layout = null;
		Configure::write('debug', 0);

		$page = $this->params['url']['page']; // get the requested page
		$limit = $this->params['url']['rows']; // get how many rows we want to have into the grid
		$sidx = $this->params['url']['sidx']; // get index row - i.e. user click to sort
		$sord = $this->params['url']['sord']; // get the direction
		if(!$sidx) $sidx =1;

		if(isset($this->params['url']['nm_mask']))
			$nm_mask = $this->params['url']['nm_mask'];
		else
			$nm_mask = "";
		if(isset($this->params['url']['rut_mask']))
			$rut_mask = $this->params['url']['rut_mask'];
		else
			$rut_mask = "";
		//construct where clause
		$where = "WHERE 1=1";
		if($nm_mask != '')
			$where .= " AND fantasyname LIKE '%$nm_mask%'";
		if($rut_mask != '')
			$where .= " AND rut LIKE '$rut_mask%'";

		$result = $this->Provider->query("SELECT COUNT(*) AS count FROM providers " . $where);

		$count = $result[0][0]['count'];

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		if ($limit<0) $limit = 0;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start<0) $start = 0;

		$SQL = "SELECT id, rut, fantasyname FROM providers " . $where . " ORDER BY $sidx $sord LIMIT $start , $limit";
		$result = $this->Provider->query($SQL);
		
		$this->set('result', $result);
		$this->set('page', $page);
		$this->set('total_pages', $total_pages);
		$this->set('count', $count);
	}
        
        function rutcheck() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
                $this->autoRender=false;
		$output['result'] = 'failure';

		if($this->RequestHandler->isAjax()) {
			$rut = $this->params['form']['rut'];
			$id = $this->params['form']['id'];
			$provider = $this->Provider->findByRut($rut);
			if(empty($provider)) $output['result'] = 'success';
			else if($provider['Provider']['id'] == $id) $output['result'] = 'success';
		}
		echo json_encode($output);
	}
}
?>