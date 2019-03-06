<?php

class b8_setup_class extends modules_class {


    function __construct() {
        parent::__construct();
    }

    function install() {
        $sql = array("INSERT INTO `" . TBL_CMS_PREFIX . 'b8_wordlist' . "` (`token`, `count_ham`) VALUES ('b8*dbversion', '3');", "INSERT INTO `" . TBL_CMS_PREFIX .
                'b8_wordlist' . "` (`token`, `count_ham`, `count_spam`) VALUES ('b8*texts', '0', '0');");
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
        $sql = array("INSERT INTO `" . TBL_CMS_PREFIX . 'b8_wordlist' . "` (`token`, `count_ham`) VALUES ('b8*dbversion', '3');", "INSERT INTO `" . TBL_CMS_PREFIX .
                'b8_wordlist' . "` (`token`, `count_ham`, `count_spam`) VALUES ('b8*texts', '0', '0');");
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    function autoupdate() {
        $sql = array("INSERT INTO `" . TBL_CMS_PREFIX . 'b8_wordlist' . "` (`token`, `count_ham`) VALUES ('b8*dbversion', '3');", "INSERT INTO `" . TBL_CMS_PREFIX .
                'b8_wordlist' . "` (`token`, `count_ham`, `count_spam`) VALUES ('b8*texts', '0', '0');");
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }
}

?>