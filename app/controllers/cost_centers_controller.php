<?php
class CostCentersController extends AppController {

	var $name = 'CostCenters';
	var $helpers = array('Html', 'Form');
        var $uses = array('User', 'CostCenter');
        
        function beforeFilter() {
		parent::beforeFilter();
		$this->LdapAuth->allow('updateTable');
	}

	function index() {
		
	}

	function indexedit() {
		$this->autoRender = false;
		$action = $this->params['form']['oper'];
		unset($this->params['form']['oper']);
		$this->data['CostCenter'] = $this->params['form'];
                $this->log($this->data, 'debug');
		if($action == 'edit')	{
			$this->CostCenter->save($this->data, null, null);
		}
		else if($action == 'add') {
			unset($this->data['CostCenter']['id']);
                        $this->data['CostCenter']['is_ses'] = $this->LdapAuth->user('is_ses');
			$this->CostCenter->create();
			$this->CostCenter->save($this->data, null, null);
		}
		else if($action == 'del') {
			$this->CostCenter->del($this->data['CostCenter']['id']);
		}
	}
        
        function updateTable(){
            $this->autoRender = false;
            App::import('Vendor', 'nusoap/lib/nusoap');
            $environment = Configure::read('App.environment');
            $wsdl = Configure::read('Soap.wsdl');
            $user = Configure::read('Soap.user');
            $pass = Configure::read('Soap.pass');
            $client = new nusoap_client($wsdl[$environment]['OM'],true);
            $client->setCredentials($user, $pass, "basic");
            $client2 = new nusoap_client($wsdl[$environment]['PA'],true);
            $client2->setCredentials($user, $pass, "basic");
            $err = $client->getError();
            if ($err) {
                return -1;
            }
            $proxy = $client->getProxy();
            $result = $proxy->ZhcmInfOm(array('Idpadre'=>60001130,'UnidadOrganizacional'=>array()));//Obtiene unidades
            foreach ($result['Unidadorganizacional']['item'] as $item) {
                $data = array(
                    'code' => $item['Idunidad'],
                    'name'=>utf8_encode($item['Descripcion'])
                );
                $this->CostCenter->create();
                $this->CostCenter->save($data);
                $proxy2 = $client2->getProxy();
                $result2=$proxy2->ZhcmInfPa(array('Rut'=>-1,'Apellidomat'=>'','Apellidopat'=>'','Funcionario'=>array(),'Idorganigrama'=>$item['Idunidad']));
                //Funcionario
                if(isset($result2['Funcionario']['item'][0])){
                    foreach ($result2['Funcionario']['item'] as $item2) {
                        $role = 4;
                        if(!empty($item['Rutmanager'])){
                            if(utf8_encode($item['Rutmanager']) == $item2['Rut'].'-'.$item2['Dv']){
                                $role = 2;//Jefe
                            }
                        }
                        $unit_id = $this->CostCenter->findByCode($item2['Unidad']);
                        $unit_id = $unit_id['CostCenter']['id'];
                        $data2 = array(
                            'rut' => $item2['Rut'].'-'.$item2['Dv'],
                            'names' => utf8_encode($item2['Nombres']),
                            'primary_last_name' => utf8_encode($item2['Apellidopaterno']),
                            'second_last_name' => utf8_encode($item2['Apellidomaterno']),
                            'email' => utf8_encode($item2['Email']),
                            'role' => $role,
                            'unit_id' => $unit_id
                        );
                        $user = $this->User->find('first',array('recursive'=>-1,'conditions'=>array('User.rut'=>$data2['rut'])));
                        if(empty($user)){
                            $this->User->create();
                            $this->User->save($data2);
                        }else{
                            $data2['id'] = $user['User']['id'];
                            $this->User->save($data2);
                        }
//                        $this->log($user,'test');
                    }
                }elseif(isset($result2['Funcionario']['item'])){
                    $item2 = $result2['Funcionario']['item'];
                    $unit_id = $this->CostCenter->findByCode($item2['Unidad']);
                    $unit_id = $unit_id['CostCenter']['id'];
                    $data2 = array(
                            'rut' => $item2['Rut'].'-'.$item2['Dv'],
                            'names' => utf8_encode($item2['Nombres']),
                            'primary_last_name' => utf8_encode($item2['Apellidopaterno']),
                            'second_last_name' => utf8_encode($item2['Apellidomaterno']),
                            'email' => utf8_encode($item2['Email']),
                            'role' => $role,
                            'unit_id' => $unit_id
                        );
                    $user = $this->User->find('first',array('recursive'=>-1,'conditions'=>array('User.rut'=>$data2['rut'])));
                    if(empty($user)){
                        $this->User->create();
                        $this->User->save($data2);
                    }else{
                        $data2['id'] = $user['User']['id'];
                        $this->User->save($data2);
                    }
//                    $this->log($data2,'test');
                }
            }
        }
}
?>