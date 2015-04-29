<?php
 
App::import('Component', 'Auth');
 
class LdapAuthComponent extends AuthComponent {
 
  
    function login($data){
        return parent::login($data);
    }
}
 
?>