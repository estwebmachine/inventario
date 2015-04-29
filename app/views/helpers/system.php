<?php
class SystemHelper extends AppHelper {
	var $helpers = array('Session');
	
	/**
	 * Chequea los roles del usuario actual
	 * 
	 * @param mixed $roles el o los roles que deseo checkear, int para un solo rol, aaray para varios roles
	 * @param string $condition 'AND', 'OR' si debe tener todos los roles especificados o solo alguno.
	 * @return boolean el resultado de la revisión de roles 
	 */
	function check_roles($roles, $condition = 'OR') {
		if(!$this->Session->check('Auth.User')) return false;
		//si estoy preguntando por un solo rol lo convierto a arreglo
		if(!is_array($roles)) $roles = array($roles);
		//elimino los valores no númericos de los roles por los que pregunto
		$roles = array_filter($roles, 'ctype_digit');
		$current_roles = array($this->Session->read('Auth.User.role'));
		//comparo
		if($condition == 'OR') {
			foreach($current_roles as $role) {
				if(in_array($role, $roles)) return true;
			}
			return false;
		} elseif($condition == 'AND') {
			foreach($current_roles as $role) {
				if(!in_array($role, $roles)) return false;
			}
			return true;
		} else {
			return false;
		}

	}
}
?>
