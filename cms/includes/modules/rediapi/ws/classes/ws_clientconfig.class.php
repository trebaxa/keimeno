<?php

class ws_clientconfig_class {

    protected $_api_appid = '2F46F729AA0334BDAF8CE47FB97C0272';
    protected $_api_key = '24DBA5049F61A2D3E70FB52C3362D52F89B72DDF';
    protected $_location = 'http://dev.trebaxa.com/shop/ws/server.php';

    function api_appid() {
        return $this->_api_appid;
    }

    function api_key() {
        return $this->_api_key;
    }

    function location() {
        return $this->_location;
    }

    function set_api_key($key) {
        $this->_api_key = $key;
    }

    function set_api_id($id) {
        $this->_api_appid = $id;
    }

    function set_location($location) {
        $this->_location = $location;
    }


}

?>