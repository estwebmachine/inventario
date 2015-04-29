<?php

class IpcsController extends AppController {

    var $name = 'Ipcs';
    var $uses = "Ipc";

    function index() {
        
    }
    
    function checkAnho($anho){
        $this->autoRender = false;
        $anho = $this->params['form']['anho'];
        $id = $this->params['form']['id'];
        $valid = $this->Ipc->find('first',array('conditions'=>array('Ipc.date'=> $anho)));
        $output['result'] = 'failure';
        if(empty($valid)) $output['result'] = 'success';
        else if($valid['Ipc']['id'] == $id) $output['result'] = 'success';
        return json_encode($output);
    }

    function indexedit() {
        $this->autoRender = false;
        $action = $this->params['form']['oper'];
        unset($this->params['form']['oper']);
        $this->data['Ipc'] = $this->params['form'];
        $output['result'] = 'failure';
        if ($action == 'edit') {
            if ($this->Ipc->save($this->data, null, null)) {
                $output['result'] = 'success';
            } else {
                $output['result'] = 'failure';
                $output['msg'] = 'No se ha podido editar el registro';
            }
        } else if ($action == 'add') {
            unset($this->data['Ipc']['id']);
            $this->Ipc->create();
            if ($this->Ipc->save($this->data, null, null)) {
                $output['result'] = 'success';
            } else {
                $output['result'] = 'failure';
                $output['msg'] = 'No se ha podido agregar el registro';
            }
        } else if ($action == 'del') {
            $this->Ipc->del($this->data['Ipc']['id']);
        }
        return json_encode($output);
    }

}
