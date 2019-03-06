<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class cmsupt_class extends keimeno_class {

    protected $CUSTGROUPS = array();

    /**
     * cmsupt_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->TCR->interpreter();
        if (file_exists('../robots.txt'))
            $this->CMSUPDT['robot'] = trim(file_get_contents('../robots.txt'));

    }

    function cmd_rewritetpls() {
        include_once (CMS_ROOT . 'admin/inc/update.class.php');
        $updt = new upt_class();
        $updt->rewriteSmartyTPL();
        $this->ej();
    }

    /**
     * cmsupt_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('CMSUPDT', $this->CMSUPDT);
    }

    /**
     * cmsupt_class::cmd_save_robot()
     * 
     * @return
     */
    function cmd_save_robot() {
        $FORM = $_POST['FORM'];
        if ($FORM['robots'] == "") {
            if (file_exists('../robots.txt'))
                unlink('../robots.txt');
        }
        else {
            file_put_contents('../robots.txt', stripslashes($FORM['robots']));
        }
        $this->LOGCLASS->addLog('MODIFY', 'robot.txt changed');
        keimeno_class::msg("robots.txt gespeichert");
        $this->ej();
    }


    /**
     * cmsupt_class::version_format()
     * 
     * @param mixed $version
     * @return
     */
    function version_format($version) {
        $vparts = explode('.', $version);
        $last = array_pop($vparts);
        return implode('.', $vparts) . '<small>R' . $last . '</small>';
    }

    /**
     * cmsupt_class::cmd_initupd()
     * 
     * @return
     */
    function cmd_initupd() {
        $this->CMSUPDT['ver_info'] = $ver_info = $this->db->query_first("SELECT * FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='VERSION' LIMIT 1");
        $version = keimeno_class::curl_get_data(RESTSERVER . "?cmd=getversion");
        $this->CMSUPDT['needupd'] = str_replace(".", "", $ver_info['wert']) < str_replace(".", "", $version);
        $this->CMSUPDT['version'] = $this->version_format($version); # implode('.', $vparts) . '<small>R' . $last . '</small>';
        $this->CMSUPDT['local_version'] = $this->version_format($ver_info['wert']);
        # register keimeno
        $upt_obj = new upt_class();
        $upt_obj->register_keimeno();
    }

    /**
     * cmsupt_class::cmd_a_importback()
     * 
     * @return
     */
    function cmd_a_importback() {
        $backup_obj = new db_backup_class();
        $backup_obj->import($_GET['file']);
        keimeno_class::msg("{LBL_UPDATEDONE}");
        $this->TCR->set_just_turn_back();
    }

    /**
     * cmsupt_class::cmd_cleancache()
     * 
     * @return
     */
    function cmd_cleancache() {
        $k = 0;
        if ($handle = opendir(CMS_ROOT . 'cache/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    @unlink(CMS_ROOT . 'cache/' . $file);
                    $k++;
                }
            }
            closedir($handle);
        }
        if ($handle = opendir(CMS_ROOT . 'admin/cache/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    @unlink(CMS_ROOT . 'admin/cache/' . $file);
                    $k++;
                }
            }
            closedir($handle);
        }
        keimeno_class::msg("{LBL_UPDATED}. " . (int)$k . " {LBL_FILESDELETED}.");
        $this->ej();
    }

    /**
     * cmsupt_class::cmd_clearsmartycache()
     * 
     * @return void
     */
    function cmd_clearsmartycache() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $dir = CMS_ROOT . 'smarty/templates_c/' . $row['local'] . '/';
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        @unlink($dir . $file);
                        $k++;
                    }
                }
                closedir($handle);
            }
        }
        keimeno_class::msg("Done.");
        $this->ej();
    }

    /**
     * cmsupt_class::cmd_instlangadmin()
     * 
     * @return
     */
    function cmd_instlangadmin() {
        $upt_obj = new upt_class();
        $upt_obj->install_lang_admin();
        keimeno_class::msg("{LBL_UPDATEDONE}");
        $this->ej();
    }

    /**
     * cmsupt_class::cmd_langarray()
     * 
     * @return void
     */
    function cmd_langarray() {
        $upt_obj = new upt_class();
        $upt_obj->transform_langtable_to_array();
        die();
    }

    /**
     * cmsupt_class::cmd_test()
     * 
     * @return void
     */
    function cmd_test() {
        $upt_obj = new upt_class();
        $upt_obj->test();
        die();
    }

    /**
     * cmsupt_class::cmd_phpini()
     * 
     * @return void
     */
    function cmd_phpini() {
        $upt_obj = new upt_class();
        $upt_obj->complete_update();
        die();
    }

    /**
     * cmsupt_class::cmd_tidy()
     * 
     * @return void
     */
    function cmd_tidy() {
        $upt_obj = new upt_class();
        $upt_obj->installTidy();
        die();
    }

    /**
     * cmsupt_class::cmd_alng()
     * 
     * @return void
     */
    function cmd_alng() {
        $upt_obj = new upt_class();
        $upt_obj->import_missing_lngpacks();
        die(X);
    }

    /**
     * cmsupt_class::cmd_langcodes()
     * 
     * @return void
     */
    function cmd_langcodes() {
        $upt_obj = new upt_class();
        $upt_obj->init_lang_codes(true);
        die();
    }

    /**
     * cmsupt_class::cmd_updtsql()
     * 
     * @return void
     */
    function cmd_updtsql() {
        $upt_obj = new upt_class();
        $upt_obj->upt_generalsql();
        die();
    }

    /**
     * cmsupt_class::cmd_bc()
     * 
     * @return void
     */
    function cmd_bc() {
        $upt_obj = new upt_class();
        $upt_obj->backup_individuel_settings();
        die();
    }

    /**
     * cmsupt_class::cmd_repair()
     * 
     * @return void
     */
    function cmd_repair() {
        $upt_obj = new upt_class();
        $upt_obj->repair_db();
        self::msg('DONE');
        $this->ej();
    }

    /**
     * cmsupt_class::cmd_release_updt()
     * 
     * @return void
     */
    function cmd_release_updt() {
        $upt_obj = new upt_class();
        $upt_obj->repair_db();
        $upt_obj->import_missing_lngpacks();
        $upt_obj->upt_lang();
        $crj_obj = new crj_class();
        $crj_obj->genCMSSetXml();
        unset($crj_obj);
        $this->LOGCLASS->addLog('UPDATE', 'CMS Update finished');
        ECHO '<p class="bg-success text-success">DONE</p>';
        die();
    }

    /**
     * cmsupt_class::cmd_core_upt()
     * 
     * @return void
     */
    function cmd_core_upt() {
        /*  $fname = 'admin/update.class.txt';
        $fcontent = keimeno_class::curl_get_data(UPDATE_SERVER . $fname);
        $fname = CMS_ROOT . str_replace(".txt", ".php", $fname);
        file_put_contents($fname, $fcontent);
        chmod($fname, 0755);*/        
        $upt_obj = new upt_class();
        $upt_obj->validate_dirs();
        $upt_obj->complete_update();
        ECHO '<p class="bg-success text-success">DONE</p>';
        die();
    }

    /**
     * cmsupt_class::cmd_sql_upt()
     * 
     * @return void
     */
    function cmd_sql_upt() {        
        $upt_obj = new upt_class();
        $upt_obj->update_database();
        ECHO '<p class="bg-success text-success">DONE</p>';
        die();
    }
    
        /**
     * cmsupt_class::cmd_file_upt()
     * 
     * @return void
     */
    function cmd_file_upt() {
        $upt_obj = new upt_class();
        $upt_obj->file_update();
        ECHO '<p class="bg-success text-success">DONE</p>';
        die();
    }

}
