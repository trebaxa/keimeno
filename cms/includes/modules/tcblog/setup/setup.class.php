<?php

class tcblog_setup_class extends modules_class {


    function __construct() {
        parent::__construct();
    }

    function install() {
        $sql = array("INSERT INTO `" . TBL_CMS_PIN_GROUPS . "` (`groupname`, `id`) VALUES ('Standard Blog', 1);", "INSERT INTO `" . TBL_CMS_PIN_PERM .
                "` (`perm_did`, `perm_group_id`) VALUES (1, 1000);
");
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