<?php  
class MailerComponent extends Object 
{      
    /** 
     * PHPMailer object. 
     *  
     * @access private 
     * @var object 
     */ 
     var $m;     
     
    /** 
     * Creates the PHPMailer object and sets default values. 
     * Must be called before working with the component! 
     * 
     * @access public 
     * @return void 
     */ 
    function init() 
    { 
        // Include the class file and create PHPMailer instance 
//        vendor('mailer/class.phpmailer');
        App::import('Vendor', 'PHPMailer', array('file' => 'mailer/class.phpmailer.php'));
        $this->m = new PHPMailer(); 
         
        // Set default PHPMailer variables (see PHPMailer API for more info) 
        $this->From = 'no-replay@desarrollosocial.gob.cl'; 
        $this->FromName ='Sistema Inventario'; 
        // set more PHPMailer vars, for smtp etc. 
     } 

    function __set($name, $value) 
    { 
        $this->m->{$name} = $value; 
    } 
     
    function __get($name) 
    { 
        if (isset($this->m->{$name})) { 
            return $this->m->{$name}; 
        } 
    } 
              
    function __call($method, $args) 
    { 
        if (method_exists($this->m, $method)) { 
            return call_user_func_array(array($this->m, $method), $args); 
        } 
    } 
}
?>