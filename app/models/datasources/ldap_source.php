<?php
 
class LdapSource extends DataSource {
 
    protected $_schema = array(
        'ldap_users' => array(
            'uid' => array(
                'type' => 'string',
                'null' => false,
                'key' => 'primary',
                'length' => 255
            )
        )
    );
 
    public function __construct($config) {
        parent::__construct($config);
 
        $this->connected = false;
        $this->connection = ldap_connect($config['host']);
 
        if ($this->connection !== false) {
            $this->connected = true;
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        }
 
        return $this->connected;
    }
 
    public function listSources() {
        return array('ldap_users');
    }
 
    public function read($model, $queryData = array()) {
 
//        $result = ldap_search($this->connection, $this->config['base_dn'],
//            sprintf('uid=%s', $queryData['conditions']['username']), array('uid'));
// 
//        if (ldap_count_entries($this->connection, $result)) {
//            $entries = ldap_get_entries($this->connection, $result);
 
//            if (@ldap_bind($this->connection, $entries[0]['dn'], $queryData['conditions']['password'])) {
//                $record = array($model->alias => array('uid' => $entries[0]['uid'][0]));
//                return array($record);
//            }
//        }
 
        if (@ldap_bind($this->connection, sprintf('uid=%s', $queryData['conditions']['username']).','.$this->config['base_dn'], $queryData['conditions']['password'])) {
                $record = array($model->alias => array('uid' => $queryData['conditions']['username']));
                return array($record);
        }
            
        return false;
    }
 
    public function describe($model) {
        return $this->_schema['ldap_users'];
    }
 
}
 
?>