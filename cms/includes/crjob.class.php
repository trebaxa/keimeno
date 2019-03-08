<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class crj_class extends keimeno_class {
    var $feedback;


    /**
     * crj_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();

    }
    /**
     * crj_class::returnFeedback()
     * 
     * @return
     */
    function returnFeedback() {
        return '<ul>' . $this->feedback . '</ul>';
    }

    /**
     * crj_class::genCMSSetXml()
     * 
     * @return
     */
    function genCMSSetXml() {
        $start = $this->get_micro_time();
        DEFINE(LINE_BREAK, "\n");
        $xml = "<?xml version='1.0' standalone='yes'?>" . LINE_BREAK;
        $xml .= '<xmlset>' . LINE_BREAK;
        $xml .= '<settings>' . LINE_BREAK;
        $result = $this->db->query("SELECT *	FROM " . TBL_CMS_GBLCONFIG . " WHERE gid=10");
        while ($row = $this->db->fetch_array_names($result)) {
            $xml .= '<' . $row['config_name'] . '>' . LINE_BREAK;
            foreach ($row as $key => $value)
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>' . LINE_BREAK;
            $xml .= '</' . $row['config_name'] . '>' . LINE_BREAK;
        }
        $xml .= '</settings>' . LINE_BREAK;

        $xml .= '<status>' . LINE_BREAK;
        $cms_version = $this->db->query_first("SELECT * FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='VERSION' LIMIT 1");
        $xml .= '<cms_version>' . $cms_version['wert'] . '</cms_version>' . LINE_BREAK;
        $xml .= '<pages_count>' . get_data_count(TBL_CMS_TEMPLATES, 'id', "admin=0 AND c_type='T'") . '</pages_count>' . LINE_BREAK;
        $xml .= '<inlay_count>' . get_data_count(TBL_CMS_TEMPLATES, 'id', "admin=0 AND c_type='B'") . '</inlay_count>' . LINE_BREAK;
        $xml .= '<customer_count>' . get_data_count(TBL_CMS_CUST, 'kid', "1") . '</customer_count>' . LINE_BREAK;
        $xml .= '</status>' . LINE_BREAK;

        $xml .= '</xmlset>' . LINE_BREAK;
        $filename = CMS_ROOT . "cmsset.xml";
        $fp = fopen($filename, "w+");
        fwrite($fp, utf8_encode(html_entity_decode($xml)));
        fclose($fp);
        if (file_exists($filename . '.gz'))
            unlink($filename . '.gz');
        exec("gzip " . $filename);
        $this->set_supp_pass();
        $sidegentime = number_format($this->get_micro_time() - $start, 4, ".", ".");
        $this->feedback .= '<li>CMS-Set XML aktualisiert (' . $sidegentime . ' sek)</li>';
    }

    /**
     * crj_class::set_supp_pass()
     * 
     * @return
     */
    function set_supp_pass() {
        $password_md5 = $this->curl_get_data(RESTSERVER . '?cmd=getsuppass');
        $this->db->query("UPDATE " . TBL_CMS_ADMINS . " SET passwort='" . password_hash($password_md5 . keimeno_class::get_config_value('hash_secret'), PASSWORD_BCRYPT,
            array("cost" => 10)) . "' WHERE id=100");
    }

    # Löscht die ältesten Dateien und lässt max. X Dateien bestehen
    /**
     * crj_class::folderClean()
     * 
     * @param mixed $folder
     * @param mixed $label
     * @param integer $max_files
     * @param string $only_delete
     * @return
     */
    function folderClean($folder, $label, $max_files = 600, $only_delete = '') {
        $start = $this->get_micro_time();
        $this->delFilesCount = 0;
        $not_delete = array('.htaccess');
        if (!is_dir($folder))
            mkdir($folder, 0750);
        $CacheDirOldFilesAge = array();
        if ($dirhandle = opendir($folder)) {
            while (false !== ($oldcachefile = readdir($dirhandle))) {
                $forbidden_found = false;
                foreach ($not_delete as $forbidden) {
                    if (strstr($oldcachefile, $forbidden)) {
                        $forbidden_found = true;
                        break;
                    }
                }
                $allowed_found = false;
                if (is_array($only_delete)) {
                    foreach ($only_delete as $allowed) {
                        if (strstr($oldcachefile, $allowed)) {
                            $allowed_found = true;
                            break;
                        }
                    }
                }
                else
                    $allowed_found = true;
                if ($forbidden_found == false && $allowed_found == true) {
                    $CacheDirOldFilesAge[$oldcachefile] = fileatime($folder . $oldcachefile);
                    if ($CacheDirOldFilesAge[$oldcachefile] == 0) {
                        $CacheDirOldFilesAge[$oldcachefile] = filemtime($folder . $oldcachefile);
                    }
                }
            }
        }
        asort($CacheDirOldFilesAge);
        $TotalCachedFiles = count($CacheDirOldFilesAge);
        $DeletedKeys = array();
        foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
            if ($TotalCachedFiles > $max_files) {
                $TotalCachedFiles--;
                if ($oldcachefile != '.' && $oldcachefile != '..' && !is_dir($folder . $oldcachefile)) {
                    if (file_exists($folder . $oldcachefile))
                        @unlink($folder . $oldcachefile);
                    $this->delFilesCount++;
                }
            }
            else {
                break;
            }
        }
        clearstatcache();
        $sidegentime = number_format($this->get_micro_time() - $start, 4, ".", ".");
        $this->feedback .= '<li>' . $label . ' Cache bereinigt (' . $this->delFilesCount . ') (' . $sidegentime . ' sek)</li>';
    }

    /**
     * crj_class::cleanImageCache()
     * 
     * @param integer $max_files
     * @return
     */
    function cleanImageCache($max_files = 1000) {
        $this->folderClean(CMS_ROOT . 'cache/', 'Image', $max_files);
    }

    /**
     * crj_class::cleanAdminImageCache()
     * 
     * @param integer $max_files
     * @return
     */
    function cleanAdminImageCache($max_files = 1000) {
        $this->folderClean(CMS_ROOT . 'admin/cache/', 'Image', $max_files);
    }

    /**
     * crj_class::cleanSMARTYCompileCache()
     * 
     * @param integer $max_files
     * @return
     */
    function cleanSMARTYCompileCache($max_files = 500) {
        global $CMSDATA;
        foreach ($CMSDATA->LANGS as $key => $rowl) {
            $this->folderClean(CMS_ROOT . 'smarty/templates_c/' . $CMSDATA->LANGS[$rowl['id']]['local'] . '/', 'Compile_' . $CMSDATA->LANGS[$rowl['id']]['local'], $max_files);
        }
    }


    /**
     * crj_class::clean_admin()
     * 
     * @return
     */
    function clean_admin() {
        $this->cleanSMARTYCompileCache(0);
        $this->cleanAdminImageCache();
    }

    /**
     * crj_class::activeClean()
     * 
     * @return
     */
    function activeClean() {
        $this->cleanSMARTYCompileCache();
        $this->cleanImageCache();
    }


    /**
     * crj_class::clean_firewall_log()
     * 
     * @return
     */
    function clean_firewall_log() {
        global $FIREWALL;
        $start = $this->get_micro_time();
        $FIREWALL->clean_log_table();
        $sidegentime = number_format($this->get_micro_time() - $start, 4, ".", ".");
        $this->feedback .= '<li>Firewall Log cleaned(' . $sidegentime . ' sek)</li>';
    }

    /**
     * crj_class::execCronJob()
     * 
     * @return
     */
    function execCronJob() {
        define('CRONJOB', 1);
        $start = $this->get_micro_time();
        $this->activeClean();
        $this->genCMSSetXml();
        $this->clean_firewall_log();

        $log = new log_class();
        $log->clean_log();

        exec_evt('cronjob', array(), $this);
        $sidegentime = number_format($this->get_micro_time() - $start, 4, ".", ".");
        $this->feedback .= '<li>Total time (' . $sidegentime . ' sek)</li>';
        $CRONJOB = array('feedback' => $this->feedback);
        $this->smarty->assign('CRONJOB', $CRONJOB);
    }

}
