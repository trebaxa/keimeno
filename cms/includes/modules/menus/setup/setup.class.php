<?php

class menus_setup_class extends modules_class {


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
        $this->add_index_to_column(TBL_MMENUMATRIX, 'mm_id');
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    function autoupdate() {
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }
}

?>