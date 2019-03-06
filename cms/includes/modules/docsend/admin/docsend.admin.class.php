<?php

/**
 * @package    Keimeno::docsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2017-08-24
 */

defined('IN_SIDE') or die('Access denied.');


class docsend_admin_class extends docsend_master_class {

    protected $DOCSEND = array();
    protected $root = FILE_ROOT . 'docsend/';

    /**
     * docsend_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        if (!is_dir(FILE_ROOT))
            mkdir(FILE_ROOT, 0750);
        if (!is_dir($this->root))
            mkdir($this->root, 0750);
        $this->DOCSEND['upload_max_filesize'] = ini_get('upload_max_filesize');
        $this->DOCSEND['post_max_size'] = ini_get('post_max_size');
        include_once (CMS_ROOT . 'admin/inc/emailsman.class.php');
        $mail_manager = new emailman_class();
        $this->DOCSEND['mails'] = $mail_manager->load_email_tempaltes_by_app('docsend');
    }


    /**
     * docsend_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('DOCSEND') != NULL) {
            $this->DOCSEND = array_merge($this->smarty->getTemplateVars('DOCSEND'), $this->DOCSEND);
            $this->smarty->clearAssign('DOCSEND');
        }
        $this->smarty->assign('DOCSEND', $this->DOCSEND);
    }


    /**
     * cmd_conf()
     * 
     * @return void
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('docsend');
        $this->DOCSEND['CONFIG'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * docsend_admin_class::cmd_ds_file_upload()
     * 
     * @return void
     */
    function cmd_ds_file_upload() {
        $msge = memindex_master_class::validate_file($_FILES, 'datei');
        if ($msge != "") {
            echo json_encode(array('status' => 'failed', 'filename' => $_FILES['datei']['name'] . $msge));
            $this->hard_exit();
        }
        $newfilename = $this->unique_filename($this->root, $_FILES['datei']['name']);
        if (move_uploaded_file($_FILES['datei']['tmp_name'], $this->root . $newfilename)) {
            chmod($this->root . $newfilename, 0755);
            $this->LOGCLASS->addLog('UPLOAD', 'File upload Kunde ' . $kid . ': ' . basename($newfilename));
        }
        else {
            echo json_encode(array('status' => 'failed', 'filename' => 'Datei eventuell größer ' . ini_get('post_max_size')));
            $this->hard_exit();
        }

        echo json_encode(array('status' => 'ok', 'filename' => $_FILES['datei']['name']));
        $this->hard_exit();
    }

    /**
     * docsend_admin_class::load_files()
     * 
     * @return
     */
    function load_files() {
        $this->DOCSEND['files'] = array();
        if (!is_dir($this->root))
            return;
        $dir = opendir($this->root);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $hash = md5($this->root . $file);
                $this->DOCSEND['files'][] = array(
                    'file' => $file,
                    'hash' => $hash,
                    'file_root' => $this->root,
                    'file_to_root' => $this->root . $file,
                    'date' => date("d.m.Y H:i", filemtime($this->root . $file)),
                    'size' => self::human_filesize(filesize($this->root . $file)),
                    'size_bytes' => filesize($this->root . $file),
                    'icons' => array(kf::gen_del_icon($hash, true, 'del_ds_file')),
                    );
            }
        }
        closedir($dir);
        $this->DOCSEND['files'] = self::sort_multi_array($this->DOCSEND['files'], 'file', SORT_ASC, SORT_STRING, 'size_bytes', SORT_DESC, SORT_NUMERIC);
        return $this->DOCSEND['files'];
    }

    /**
     * docsend_admin_class::cmd_reload_docsend_files()
     * 
     * @return
     */
    function cmd_reload_docsend_files() {
        $this->load_files();
        $this->parse_to_smarty();
        kf::echo_template('docsend.files');
    }

    /**
     * docsend_admin_class::cmd_user_file_download()
     * 
     * @return void
     */
    function cmd_ds_file_download() {
        $arr = $this->load_files();
        foreach ($arr as $key => $row) {
            if ($row['hash'] == $_GET['hash']) {
                self::direct_download($row['file_to_root']);
            }
        }
        firewall_class::report_hacking('Invalid secure file download ');
    }

    /**
     * docsend_admin_class::cmd_del_ds_file()
     * 
     * @return void
     */
    function cmd_del_ds_file() {
        $arr = $this->load_files();
        foreach ($arr as $key => $row) {
            if ($row['hash'] == $_GET['ident']) {
                @unlink($row['file_to_root']);
            }
        }
        $this->ej();
    }

    /**
     * docsend_admin_class::cmd_preview()
     * 
     * @return void
     */
    function cmd_preview() {
        $files = $this->load_files();
        $file_ids = (array )$_POST['FORM']['files'];
        foreach ($files as $key => $file) {
            if (!in_array($file['hash'], $file_ids)) {
                unset($this->DOCSEND['files'][$key]);
            }
        }
        $this->DOCSEND['mail'] = get_email_template($_POST['FORM']['mailid']);
        $this->DOCSEND['mail']['content'] = smarty_compile($this->DOCSEND['mail']['content']);
        $cust = new customer_class();
        $this->DOCSEND['customer'] = $cust->load_customer($_POST['kid']);
        $this->parse_to_smarty();
        kf::echo_template('docsend.preview');
    }

    /**
     * docsend_admin_class::cmd_send_mail()
     * 
     * @return void
     */
    function cmd_send_mail() {
        $mail_arr = replacer(get_email_template($_POST['mailid']), $_POST['kid']);
        $mail_arr['subject'] = $_POST['FORM']['subject'];
        $mail_arr['content'] = $_POST['FORM']['content'];
        $files = $this->load_files();
        $file_ids = (array )$_POST['FILEIDS'];
        $att_files = array();
        foreach ($files as $key => $file) {
            if (in_array($file['hash'], $file_ids)) {
                $att_files[] = $file['file_to_root'];
            }
        }

        $mail_arr['cu_mail'] = 'service@trebaxa.com';

        send_mail_to($mail_arr, $att_files, TRUE, $mail_arr['absender_email'], $mail_arr['absender_email']);
        self::msg('Dateien an ' . $mail_arr['cu_mail'] . ' versendet');
        $this->parse_to_smarty();
        $this->ej('reload_docsend_files');
    }

    /**
     * docsend_admin_class::cmd_cusearch()
     * 
     * @return void
     */
    function cmd_cusearch() {
        $term = trim($_GET['term']);
        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K WHERE (
	 LOWER(K.email_notpublic) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.kid) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.nachname) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.knownof) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.firma) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.vorname) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.email) LIKE LOWER('%" . $term . "%')  COLLATE utf8_bin ) 
	 ORDER BY K.nachname,firma ASC LIMIT 10");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->DOCSEND['customers'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('docsend.customers');
    }

}
