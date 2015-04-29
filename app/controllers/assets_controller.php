<?php
class AssetsController extends AppController {

	var $name = 'Assets';
	var $helpers = array('Html', 'Form');
        
	function index() {

	}

	function structure() {//Definir estructura de clases
		
	}
        
        function define(){ //Definir maestra de inventario
            
        }
	
	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['Asset'] = $this->params['form'];

		if($action == 'edit')	{
			$this->Asset->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['Asset']['id']);
			$this->data['Asset']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->Asset->create();
			$this->Asset->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->Asset->del($this->data['Asset']['id']);
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
//		if(isset($this->params['url']['rut_mask']))
//			$rut_mask = $this->params['url']['rut_mask'];
//		else
//			$rut_mask = "";
		//construct where clause
		$where = "WHERE 1=1 AND is_ses = ".$this->LdapAuth->user('is_ses');
		if($nm_mask != '')
			$where .= " AND name LIKE '%$nm_mask%'";
//		if($rut_mask != '')
//			$where .= " AND rut LIKE '$rut_mask%'";

		$result = $this->Asset->query("SELECT COUNT(*) AS count FROM assets " . $where);

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

		$SQL = "SELECT id, name FROM assets " . $where . " ORDER BY $sidx $sord LIMIT $start , $limit";
		$result = $this->Asset->query($SQL);
		
		$this->set('result', $result);
		$this->set('page', $page);
		$this->set('total_pages', $total_pages);
		$this->set('count', $count);
	}
	
	
}
?>