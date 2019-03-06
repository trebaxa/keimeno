<?php

class ws_internal extends tc_class{
    

    public $shop_root;
    
    protected function __construct() {
        parent::__construct();
        $shop_root = str_replace("/ws/classes", "/", str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))));
        $this->shop_root = $shop_root;

    }

    protected function sql_to_array($result) {
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return (array )$arr;
    }


}

?>