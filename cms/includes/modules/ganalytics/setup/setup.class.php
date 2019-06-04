<?php

class ganalytics_setup_class extends modules_class {


    /**
     * ganalytics_setup_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * ganalytics_setup_class::install()
     * 
     * @return void
     */
    function install() {
        $sql = array("INSERT INTO `" . TBL_CMS_PREFIX .
                "gblconfig` (`config_name`, `config_value`, `config_desc`, `isnumeric`, `del_spaces`, `gid`, `is_schalter`, `is_text`, `morder`, `is_time`, `is_font`, `is_list`, `is_lang`, `max`, `is_password`, `help`, `is_lang_fe`, `is_countryiso`, `is_land`, `is_jqueryver`, `is_curr`, `is_timezone`, `is_rediapi`, `is_modernizr`, `modident`, `is_gbltplmodname`, `is_mail`) VALUES
('ga_anonymize_ip', '1', 'Anonymize IP', 0, 0, 0, 1, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', ''),
('ga_forcessl', '1', 'SSL Übertragung erzwingen', 0, 0, 0, 1, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', ''),
('ga_active', '1', 'Google Analytics aktivieren', 0, 0, 0, 1, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', ''),
('ga_link_attribution', '1', 'Link Attribution übetragen', 0, 0, 0, 1, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', ''),
('ga_ua_ident', 'UA-', 'Google Analytics ID', 0, 1, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', ''),
('ga_aw_ident', 'AW-', 'AdWords ID', 0, 1, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', ''),
('ga_send_page_view', '1', 'PageView', 0, 0, 0, 1, 0, 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 'ganalytics', '', '');");
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    /**
     * ganalytics_setup_class::uninstall()
     * 
     * @return void
     */
    function uninstall() {
        $sql = array("DELETE FROM `" . TBL_CMS_PREFIX . "gblconfig` WHERE modident='ganalytics'");
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    /**
     * ganalytics_setup_class::update()
     * 
     * @return void
     */
    function update() {
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    /**
     * ganalytics_setup_class::autoupdate()
     * 
     * @return void
     */
    function autoupdate() {
        $sql = array();
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }
}
