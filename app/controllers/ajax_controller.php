<?php
class AjaxController extends AppController {

	var $name = 'Ajax';
	var $uses = array('City','Address','Region', 'Floor','Office');

	function selectOpt($model) {
                $this->autoRender = false;
		Configure::write('debug', 0);
		$model = Inflector::camelize($model);
		$output = '';
		$label = 'name';
		if(isset($this->params['url']['label'])) {
			$label = $this->params['url']['label'];
			unset($this->params['url']['label']);
		}
		if(isset($this->params['url']['firstopt'])) {
			$firstopt = $this->params['url']['firstopt'];
			unset($this->params['url']['firstopt']);
		}
		if (App::import('Model', $model)) {
			$this->$model = new $model();
                        $params = array('recursive' => 0, 'fields' => array($model . '.id', $model . '.' . $label));
			//condiciones
			unset($this->params['url']['url']);
			if(count($this->params['url']) > 0) {
				foreach($this->params['url'] as $url_param => $value) {
					if(strpos($url_param, '_') === false)	$params['conditions'][$model . '.' . $url_param] = $value;
					else {
						$split = explode('_', $url_param);
						$thismodel = Inflector::camelize($split[0]);
						$url_param = $split[1];
						$params['conditions'][$thismodel . '.' . $url_param] = $value;
					}
				}
			}
                        if(array_key_exists('is_ses', $this->$model->_schema))
                            $params['conditions'][$model.'.is_ses'] = $this->LdapAuth->user('is_ses');//filtra por SES o SSS
                        if(array_key_exists('is_active', $this->$model->_schema))
                            $params['conditions'][$model.'.is_active'] = 1;//filtra que esten habilitados
			$data = $this->$model->find('all', $params);
			$output .= '<select>';
			if(isset($firstopt)) $output .= '<option value="">' . $firstopt . '</option>';
                        foreach($data as $row) {
                            $output .= '<option value="' . $row[$model]['id'] . '">' . $row[$model][$label] . '</option>';
                        }
			$output .= '</select>';
		}
                echo $output;
	}
        
        function getParentIdIL($index_cat, $id){
            $this->autoRender = false;
            Configure::write('debug', 0);
            $ids = array();
            if($index_cat > 3){
                $ids['floor'] = $this->Office->field('floor_id','Office.id = ' . $id);
            }else{
                $ids['floor'] = $id;
            }

            if($index_cat > 2){
                $ids['address'] = $this->Floor->field('address_id','Floor.id = ' . $ids['floor']);                
            }else{
                $ids['address'] = $id;
            }

            if($index_cat > 1){
                $ids['city'] = $this->Address->field('city_id','Address.id = ' . $ids['address']);
            }else{
                $ids['city'] = $id;
            }

            if($index_cat > 0){
                $ids['region'] = $this->City->field('region_id','City.id = ' . $ids['city']);
            }
            return json_encode($ids);
        }

}
?>