<?php

/**
 * @package    memindex
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class memindex_master_class extends modules_class {


    /**
     * memindex_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }


    /**
     * news_admin_class::rebuild_page_index()
     * 
     * @return
     */
    function rebuild_page_index($kid = 0) {
        $k = 0;
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='memindex' " . (($kid > 0) ? " AND pi_relatedid='" . $kid . "'" : ""));
        $result = $this->db->query("SELECT kid,nachname,vorname,firma FROM " . TBL_CMS_CUST . (($kid > 0) ? " WHERE kid='" . $kid . "'" : ""));
        while ($row = $this->db->fetch_array($result)) {
            $k++;
            $label = member_class::gen_link_label($row);
            $link = '/' . $this->gblconfig->mem_link_detail . '/' . $this->format_file_name($label) . '.html';
            $query = array('cmd' => 'showcustomer', 'id' => $row['kid']);
            $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($lang = $this->db->fetch_array_names($resultlang)) {
                if ($label != "")
                    $this->connect_to_pageindex($link, $query, $row['kid'], 'memindex', $lang['id'], 0, self::get_config_value('mem_link_detail_pageid'));
            }
        }
        return $k;
    }

    /**
     * memindex_admin_class::get_path()
     * 
     * @param mixed $kid
     * @return void
     */
    public static function get_path($kid) {
        $customer_file_root = FILE_ROOT . 'memindex/' . (int)$kid . '/';
        if (!is_dir(FILE_ROOT))
            mkdir(FILE_ROOT, 0750);
        if (!is_dir(FILE_ROOT . 'memindex/'))
            mkdir(FILE_ROOT . 'memindex/', 0750);
        if (!is_dir($customer_file_root))
            mkdir($customer_file_root, 0750);
        return self::add_trailing_slash($customer_file_root);
    }

    /**
     * memindex_master_class::load_files()
     * 
     * @param mixed $kid
     * @return
     */
    public static function load_files($kid, $cdir = "") {
        $files = array();
        if ($cdir == "") {
            $cdir = self::get_path($kid);
            if (!is_dir($cdir))
                return;
        }
        self::add_trailing_slash($cdir);

        $dir = opendir($cdir);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..') && is_file($cdir . $file)) {
                $hash = md5($cdir . $file . self::get_config_value('hash_secret'));
                $files[] = array(
                    'file' => $file,
                    'file_root' => $cdir,
                    'file_to_root' => $cdir . $file,
                    'file_to_root_hash' => base64_encode( $cdir),
                    'hash' => $hash,
                    'sechash' => md5($file . $kid . self::get_config_value('hash_secret')),
                    'date' => date("d.m.Y H:i", filemtime($cdir . $file)),
                    'size' => filesize($cdir . $file),
                    'size_human' => self::human_filesize(filesize($cdir . $file)),
                    );
            }
        }
        closedir($dir);
        $files = self::sort_multi_array($files, 'file', SORT_ASC, SORT_STRING, 'size', SORT_DESC, SORT_NUMERIC);
        return $files;
    }

    /**
     * memindex_master_class::load_tree_frontend()
     * 
     * @param mixed $kid
     * @return
     */
    public static function load_tree_frontend($kid) {
        $customer_file_root = self::get_path($kid);
        $arr = array();
        self::read_dirs($customer_file_root, $arr);
        return $arr;
    }

    /**
     * memindex_master_class::read_dirs()
     * 
     * @param mixed $dir
     * @param mixed $arr
     * @return void
     */
    public static function read_dirs($dir, &$arr) {
        $index = 0;
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir . $file)) {
                    }
                    else {
                        $dir_r = $dir . $file;
                        $dir_r = self::add_trailing_slash($dir_r);
                        $arr[$index] = array(
                            'id' => md5($dir_r),
                            'folder' => $file,
                            'dir' => $dir_r,
                            'hash' => base64_encode($dir_r),
                            'children' => array());
                        self::read_dirs($dir_r, $arr[$index]['children']);
                    }
                }
                $index++;
            }
            closedir($openDir);
        }
    }

    /**
     * memindex_master_class::validate_file()
     * 
     * @param mixed $FILES
     * @param mixed $name
     * @return
     */
    public static function validate_file($FILES, $name) {
        $msge = "";
        if (!validate_upload_file($FILES[$name])) {
            $msge = $_SESSION['upload_msge'];
        }
        return $msge;
    }

    /**
     * memindex_master_class::send_newpassword_link()
     * 
     * @param mixed $customer
     * @param mixed $page
     * @return void
     */
    public static function send_newpassword_link($customer, $page) {
        $arr = array('mail' => array('passforgot_link' => self::gen_setpass_link($customer, $page)));
        send_mail_to(replacer(get_email_template(1000), $customer['kid'], $arr));
    }


    /**
     * memindex_master_class::gen_setpass_link()
     * 
     * @param mixed $customer
     * @param mixed $page
     * @return
     */
    public static function gen_setpass_link($customer, $page) {
        $hash = sha1(implode('|', $customer));
        return self::get_domain_url() . 'index.php?page=' . $page . '&cmd=show_setnewpass&kid=' . $customer['kid'] . '&hash=' . $hash;
    }
}
