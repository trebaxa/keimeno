<?php

class aws_setup_class extends modules_class {


    function __construct() {
        parent::__construct();
    }

    function install() {
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    function uninstall() {
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    function update() {
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }
}

?>