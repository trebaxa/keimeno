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

class safeupload_admin_class extends safeupload_master_class {

    protected $SAFEUPLOAD = array();

    /**
     * safeupload_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * safeupload_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        #$this->db->query("DELETE FROM ".TBL_CMS_EXAMPLE." WHERE id='".$_GET['ident']."'");
        $this->ej();
    }

    /**
     * safeupload_admin_class::on_load_customer_files()
     * 
     * @param mixed $params
     * @return
     */
    function on_load_customer_files($params) {
        if ((int)$params['kid'] > 0) {
            $log_rows = array();
            $customer_file_root = memindex_master_class::get_path($params['kid']);
            if (file_exists($customer_file_root . 'file_download_log.csv')) {
                $table = fopen($customer_file_root . 'file_download_log.csv', 'r');
                while (($data = fgetcsv($table, 1000, ";")) !== FALSE) {
                    $log_rows[$data[0]] = $data;
                }
                fclose($table);
            }

            foreach ($params['files'] as $key => $file) {
                $hash = md5($file['file_root'] . $file['file']);
                if (isset($log_rows[$hash])) {
                    $params['files'][$key]['last_download'] = $log_rows[$hash][3];
                    $params['files'][$key]['last_download_ger'] = date('d.m.Y H:i', strtotime($log_rows[$hash][3]));
                }
                if ($hash == md5($file['file_root'] . 'file_download_log.csv')) {
                    unset($params['files'][$key]);
                }
            }
        }
        return $params;
    }
    
    /**
     * safeupload_admin_class::on_upload_customer_files()
     * 
     * @param mixed $params
     * @return
     */
    function on_upload_customer_files($params) {
        self::remove_file_log(md5($params['file']), $params['kid']);
        return $params;
    }

    /**
     * safeupload_admin_class::on_delete_customer_files()
     * 
     * @param mixed $params
     * @return
     */
    function on_delete_customer_files($params) {
        self::remove_file_log(md5($params['file']), $params['kid']);
        return $params;
    }

    /**
     * safeupload_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$_GET['ident'] . "' LIMIT 1");
        $this->ej();
    }


    /**
     * safeupload_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('SAFEUPLOAD', $this->SAFEUPLOAD);
    }


    /**
     * safeupload_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('safeupload');
        $this->SAFEUPLOAD['CONFIG'] = $CONFIG_OBJ->buildTable();
    }


    /**
     * safeupload_admin_class::load_homepage_integration()
     * 
     * @return
     */
    function load_homepage_integration($params) {
        $this->SAFEUPLOAD['upload_max_filesize'] = ini_get('upload_max_filesize');
        $this->SAFEUPLOAD['post_max_size'] = ini_get('post_max_size');
        $this->parse_to_smarty();
        return $this->load_templates_for_plugin_by_modident('safeupload', $params);
    }


    /**
     * safeupload_admin_class::save_homepage_integration()
     * 
     * @return
     */
    function save_homepage_integration($params) {
        $this->save_plugin_integration($params, 'safeupload');
        return $params;
    }
}
