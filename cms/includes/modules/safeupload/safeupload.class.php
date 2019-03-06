<?php

/**
 * @package    Keimeno::safeupload
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2017-08-23
 */

defined('IN_SIDE') or die('Access denied.');

class safeupload_class extends safeupload_master_class {

    var $SAFEUPLOAD = array();

    /**
     * safeupload_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->SAFEUPLOAD['upload_max_filesize'] = ini_get('upload_max_filesize');
        $this->SAFEUPLOAD['post_max_size'] = ini_get('post_max_size');
    }

    /**
     * safeupload_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('SAFEUPLOAD') != NULL) {
            $this->SAFEUPLOAD = array_merge($this->smarty->getTemplateVars('SAFEUPLOAD'), $this->SAFEUPLOAD);
            $this->smarty->clearAssign('SAFEUPLOAD');
        }
        $this->smarty->assign('SAFEUPLOAD', $this->SAFEUPLOAD);
    }


    /**
     * safeupload_class::parse_safeupload()
     * 
     * @return
     */
    function parse_safeupload($params) {
        $this->parse_to_smarty();
        $params = $this->parse_plugin_template($params, 'SAFEUPLOAD');
        return $params;
    }

    /**
     * safeupload_class::cmd_su_file_upload()
     * 
     * @return void
     */
    function cmd_su_file_upload() {
        $kid = (int)$this->user_object['kid'];
        if ($kid > 0) {
            $customer_file_root = memindex_master_class::get_path($kid);
            $msge = memindex_master_class::validate_file($_FILES, 'datei');
            if ($msge != "") {
                echo json_encode(array('status' => 'failed', 'filename' => $_FILES['datei']['name'] . $msge));
                $this->hard_exit();

            }
            $email_msg = 'Kunde [' . $kid . '], ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'] . PHP_EOL;

            $att_files = array();
            $newfilename = $this->unique_filename($customer_file_root, $this->gbl_config['mem_file_cuprefix'] . $_FILES['datei']['name']);
            if (move_uploaded_file($_FILES['datei']['tmp_name'], $customer_file_root . $newfilename)) {
                chmod($customer_file_root . $newfilename, 0755);
                $att_files[] = $customer_file_root . $newfilename;
                $email_msg .= 'Datei:' . "\t" . basename($newfilename) . PHP_EOL;
                $this->LOGCLASS->addLog('UPLOAD', 'File upload Kunde ' . $kid . ', ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'] . ': ' . basename($newfilename));
            }
            else {
                echo json_encode(array('status' => 'failed', 'filename' => 'Datei eventuell größer ' . ini_get('post_max_size')));
                $this->hard_exit();
            }


            $email_msg .= PHP_EOL;

            # send mail
            $PLUGIN_OPT = array();
            if (isset($_GET['cont_matrix_id']) && $_GET['cont_matrix_id'] > 0) {
                $PLUGIN_OPT = $this->load_plug_opt((int)$_GET['cont_matrix_id']);
                if ($PLUGIN_OPT['send_mail'] == 1) {
                    $smarty_arr = array('mail' => array(
                            'subject' => pure_translation('Datei Upload: ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'], 1),
                            'content' => date("d.m.Y H:i:s") . "\n" . $email_msg,
                            ));
                    $recipient_email = ($PLUGIN_OPT['email'] != "") ? $PLUGIN_OPT['email'] : FM_EMAIL;
                    if ($PLUGIN_OPT['send_mail_attach'] != 1) {
                        $att_files = array();
                    }
                    send_easy_mail_to($recipient_email, $smarty_arr['mail']['content'], $smarty_arr['mail']['subject'], $att_files, true, $recipient_email, $recipient_email);
                }
            }
            echo json_encode(array('status' => 'ok', 'filename' => $_FILES['datei']['name']));
            $this->hard_exit();
        }
        else {
            firewall_class::report_hack('SafeUpload - Illegal upload try "' . $_FILES['datei']['name'] . '"');
            echo json_encode(array('status' => 'failed', 'filename' => $_FILES['datei']['name'] . $msge));
            $this->hard_exit();
        }
    }

    /**
     * safeupload_class::cmd_load_customer_files()
     * 
     * @return void
     */
    function cmd_load_customer_files() {
        $kid = (int)$this->user_object['kid'];
        if (isset($_GET['folder']) && $_GET['folder'] != "") {
            $folder = base64_decode($_GET['folder']);
        }
        else {
            $folder = memindex_master_class::get_path($kid);
        }

        if (!is_dir($folder)) {
            die('hacking');
        }
        $this->load_su_files($folder);
        $this->parse_to_smarty();
        echo_template_fe('mem_file_list');
    }

    /**
     * safeupload_class::load_su_files()
     * 
     * @return
     */
    function load_su_files($cdir = "") {
        $kid = (int)$this->user_object['kid'];
        $MEMINDEX = array();
        if ($cdir == "") {
            $cdir = memindex_master_class::get_path($kid);
            if (!is_dir($cdir))
                return;
        }
        self::add_trailing_slash($cdir);
        $MEMINDEX['tree'] = memindex_master_class::load_tree_frontend($kid);

        $this->SAFEUPLOAD['files'] = array();
        # $customer_file_root = memindex_master_class::get_path($kid);
        if (!is_dir($cdir))
            return;
        $dir = opendir($cdir);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..') && is_file($cdir . $file)) {
                $hash = md5($cdir . $file . self::get_config_value('hash_secret'));
                if ($hash != md5($cdir . 'file_download_log.csv' . self::get_config_value('hash_secret'))) {
                    $this->SAFEUPLOAD['files'][] = array(
                        'file' => $file,
                        'file_root' => $cdir,
                        'file_to_root' => $cdir . $file,
                        'file_to_root_hash' => base64_encode($cdir),
                        'hash' => $hash,
                        'sechash' => md5($file . $kid . self::get_config_value('hash_secret')),
                        'date' => date("d.m.Y H:i", filemtime($cdir . $file)),
                        'size' => self::human_filesize(filesize($cdir . $file)),
                        'size_bytes' => filesize($cdir . $file),
                        );
                }
            }
        }
        closedir($dir);
        $MEMINDEX['myfiles'] = $this->SAFEUPLOAD['files'] = self::sort_multi_array($this->SAFEUPLOAD['files'], 'file', SORT_ASC, SORT_STRING, 'size_bytes', SORT_DESC,
            SORT_NUMERIC);
        $this->smarty->assign('MEMINDEX', $MEMINDEX);
        return $this->SAFEUPLOAD['files'];
    }

    /**
     * safeupload_class::cmd_reload_customer_files()
     * 
     * @return
     */
    function cmd_reload_customer_files() {
        $kid = (int)$this->user_object['kid'];
        $this->load_su_files();
        if (isset($_GET['cont_matrix_id']) && $_GET['cont_matrix_id'] > 0) {
            $PLUGIN_OPT = $this->load_plug_opt((int)$_GET['cont_matrix_id']);
            $template = $this->dao->load_template($PLUGIN_OPT['tplid']);
        }
        $this->parse_to_smarty();
        echo_template_fe($template['tpl_name']);
    }

    /**
     * safeupload_class::cmd_user_file_delete()
     * 
     * @return void
     */
    function cmd_user_file_delete() {
        $kid = (int)$this->user_object['kid'];
        if ($kid > 0) {
            $arr = memindex_master_class::load_files($kid);
            foreach ($arr as $key => $row) {
                if ($row['hash'] == $_GET['hash']) {
                    if (file_exists($row['file_to_root'])) {
                        @unlink($row['file_to_root']);
                    }
                    $this->LOGCLASS->addLog('DELETE', $kid . ', ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'] . ' Datei entfernt: ' . basename($row['file_to_root']));
                    self::msg('Datei entfernt');
                    $this->ej('reload_customer_files');
                }
            }
        }
        firewall_class::report_hacking('Try to delete file. Wrong hash.');
        self::msg('failed');
        $this->ej();
    }


    /**
     * safeupload_class::cmd_user_safe_file_download()
     * 
     * @return void
     */
    function cmd_user_safe_file_download() {
        $kid = (int)$this->user_object['kid'];
        $folder = memindex_master_class::get_path($kid);
        if (isset($_GET['folder'])) {
            $folder = base64_decode($_GET['folder']);
        }
        if (!is_dir($folder)) {
            firewall_class::report_hacking('Invalid secure file download ' . $this->user_object['kid']);
            die('hacking');
        }
        if ($kid > 0) {
            $arr = memindex_master_class::load_files($kid, $folder);
            foreach ($arr as $key => $row) {
                if ($row['hash'] == $_GET['hash']) {
                    if (isset($_GET['cmid'])) {
                        $PLUGIN_OPT = $this->load_plug_opt((int)$_GET['cmid']);
                        if ($PLUGIN_OPT['send_download_mail'] == 1) {
                            $email_msg = 'Kunde [' . $kid . '], ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'] . PHP_EOL;
                            $email_msg .= 'Datei:' . "\t" . basename($row['file_to_root']) . PHP_EOL . PHP_EOL;
                            $smarty_arr = array('mail' => array(
                                    'subject' => pure_translation('Datei Download: ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'], 1),
                                    'content' => date("d.m.Y H:i:s") . "\n" . $email_msg,
                                    ));
                            $recipient_email = ($PLUGIN_OPT['email'] != "") ? $PLUGIN_OPT['email'] : FM_EMAIL;
                            send_easy_mail_to($recipient_email, $smarty_arr['mail']['content'], $smarty_arr['mail']['subject'], $att_files, true, $recipient_email, $recipient_email);
                        }

                        # Log Download
                        self::remove_file_log(md5($row['file_to_root']), $kid);
                        $customer_file_root = memindex_master_class::get_path($kid);
                        $fp = fopen($customer_file_root . 'file_download_log.csv', 'a+');
                        $fields = array(
                            md5($row['file_to_root']),
                            $row['file_to_root'],
                            basename($row['file_to_root']),
                            date('Y-m-d H:i:s'));
                        fputcsv($fp, $fields, ";");
                        fclose($fp);
                    }
                    $this->LOGCLASS->addLog('DOWNLOAD', $kid . ', ' . $this->user_object['vorname'] . ' ' . $this->user_object['nachname'] . ' Datei download: ' . basename($row['file_to_root']));

                    self::direct_download($row['file_to_root']);
                }
            }
            firewall_class::report_hacking('Invalid secure file download ' . $kid);
        }
        else {
            firewall_class::report_hacking('Invalid secure file download. User not logged in. ');
        }
    }

}
