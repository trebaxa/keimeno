<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

include (SYSTEM_ROOT . 'admin/inc/nestedArray.class.php');
include (SYSTEM_ROOT . 'includes/htmlcrawl.class.php');
include (SYSTEM_ROOT . 'admin/inc/tcrequest.class.php');
include (SYSTEM_ROOT . 'admin/inc/log.class.php');


class keimeno_class {
    var $db = null;
    var $gbl_config = array();
    protected static $config = array();
    var $smarty = null;
    var $NESTED_ARR = null;
    var $GBLPAGE = array();
    var $CORE = null;
    var $dao = null;

    protected $MODIDENT = "";
    protected static $exclude_filter = array();
    protected static $include_filter = array();
    protected static $umlaute = array(
        "ä" => 'ae',
        'ö' => 'oe',
        'ü' => 'ue',
        'ß' => 'ss',
        ',' => '-');

    /**
     * keimeno_class::keimeno_class()
     * Constructor of class
     * @return
     */
    function __construct() {
        global $kdb, $gbl_config, $smarty, $gbl_config_shop, $shop_tables, $cms_tables, $LOGCLASS, $CORE, $dao;
        $this->db = $kdb;
        $this->smarty = $smarty;
        $this->dao = $dao;
        $this->gbl_config = $gbl_config;
        static::$config = $gbl_config;
        $this->gblconfig = (object)$gbl_config;
        $this->gbl_config_shop = $gbl_config_shop;
        $this->shop_tables = (array )$shop_tables;
        $this->cms_tables = (array )$cms_tables;
        $this->GBLPAGE['err'] = array();
        $this->GBLPAGE['access'] = array();
        $this->NESTED_ARR = new nestedArrClass();
        $this->NESTED_ARR->db = $this->db;
        $this->LOGCLASS = $LOGCLASS;
        if (is_object($CORE))
            $this->CORE = $CORE;
        if (defined('ISADMIN') && ISADMIN == 1) {
            global $EPAGES;
            if (is_array($EPAGES) && array_key_exists($_GET['epage'], $EPAGES) && is_array($EPAGES[$_GET['epage']]) && isset($_GET['epage']) && array_key_exists('id', $EPAGES[$_GET['epage']]))
                $this->MODIDENT = $EPAGES[$_GET['epage']]['id'];
        }
    }

    /**
     * keimeno_class::get_config_value()
     * get value by key from global config
     * @param string $key
     * @return string
     */
    public static function get_config_value($key) {
        return static::$config[$key];
    }

    /**
     * keimeno_class::get_config()
     * 
     * @return
     */
    public static function get_config() {
        return static::$config;
    }

    /**
     * keimeno_class::set_config()
     * 
     * @param mixed $gbl_config
     * @return void
     */
    public static function set_config($gbl_config) {
        static::$config = $gbl_config;
    }

    /**
     * keimeno_class::send_image_to_browser()
     * Sends image to browser
     * @param string $file filename including path to file
     * @return
     */
    public static function send_image_to_browser($file) {
        if (file_exists($file) && is_file($file)) {
            $types = array(
                'gif' => 'image/gif',
                'png' => 'image/png',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg');
            $ext = strtolower(self::get_ext($file));
            if (array_key_exists($ext, $types)) {
                header('Content-type: ' . $types[$ext]);
                readfile($file);
                return;
            }

        }
    }

    /**
     * keimeno_class::color_class()
     * (desperate function)
     * @param mixed $relative_date
     * @return
     */
    public static function color_class($relative_date) {
        if ($relative_date == '{LBL_TODAY}') {
            return 'redimportant';
        }
        if ($relative_date == '{LBL_YESTERDAY}') {
            return 'bold';
        }
    }

    /**
     * keimeno_class::human_date()
     * 
     * @param mixed $ttime
     * @return
     */
    public static function human_date($ttime) {
        if ((time() - $ttime) <= 15 * 60) { // 15min old
            $humantime = '{LBL_15MINOLD}';
        }
        else
            if ((time() - $ttime) <= 2 * 60 * 60) { // 2hours old
                $humantime = '{LBL_2HOLD}';
            }
            else
                if ((time() - $ttime) <= 1 * 24 * 60 * 60) { // 24h old
                    $humantime = '{LBL_1DOLD}';
                }
                else
                    if ((time() - $ttime) <= 2 * 24 * 60 * 60) { // 2d old
                        $humantime = '{LBL_2DOLD}';
                    }
                    else {
                        $humantime = date('d.m.Y H:i', $ttime);
                    }
                    return $humantime;
    }

    /**
     * keimeno_class::relative_date()
     * Returns a readable date format
     * @param mixed $time
     * @return string
     */
    public static function relative_date($time) {
        $today = strtotime(date('M j, Y'));
        $reldays = ($time - $today) / 86400;
        if ($reldays >= 0 && $reldays < 1) {
            return '{LBL_TODAY}';
        }
        else
            if ($reldays >= 1 && $reldays < 2) {
                return 'Tomorrow';
            }
            else
                if ($reldays >= -1 && $reldays < 0) {
                    return '{LBL_YESTERDAY}';
                }
        if (abs($reldays) < 7) {
            if ($reldays > 0) {
                $reldays = floor($reldays);
                return 'In ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
            }
            else {
                $reldays = abs(floor($reldays));
                return $reldays . ' ' . ($reldays != 1 ? '{LBL_DAYS}' : '{LBL_DAY}') . ' {LBL_AGO}';
            }
        }
        if (abs($reldays) < 182) {
            return date('l, j F', $time ? $time : time());
        }
        else {
            return date('l, j F, Y', $time ? $time : time());
        }
    }

    /**
     * keimeno_class::recurse_copy()
     * recursive copy of directory
     * @param mixed $src
     * @param mixed $dst
     * @return
     */
    public function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        if (!is_dir($dst) || !is_dir($src))
            return;
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    file_put_contents($dst . '/' . $file, file_get_contents($src . '/' . $file));
                }
            }
        }
        closedir($dir);
    }

    /**
     * keimeno_class::add_object()
     * add class object to object
     * @param mixed $obj
     * @return
     */
    public function add_object($obj) {
        if (is_object($obj)) {
            $class_name = get_class($obj);
            $this->$class_name = $obj;
        }
    }

    /**
     * keimeno_class::anonymizing_ip()
     * Anonymize the an ip address
     * @param string $ip
     * @return
     */
    public static function anonymizing_ip($ip) {
        if (strpos($ip, ".") == true) {
            return preg_replace('#(?:\.\d+){1}$#', '.0', $ip);
        }
        else {
            return preg_replace('~[0-9]*:[0-9]+$~', 'XXXX:XXXX', $ip);
        }
    }

    /**
     * keimeno_class::no_errors()
     * check if an error has been placed
     * @return boolean
     */
    public function no_errors() {
        return count($this->GBLPAGE['err']) == 0;
    }

    /**
     * keimeno_class::add_err()
     * add an error via script
     * @param mixed $text
     * @return
     */
    public function add_err($text) {
        $this->GBLPAGE['err'][] = $text;
    }

    /**
     * keimeno_class::add_access_err()
     * add access error
     * @param mixed $key
     * @param mixed $status
     * @return
     */
    public function add_access_err($key, $status) {
        $this->GBLPAGE['access'][$key] = $status;
    }

    /**
     * keimeno_class::add_trailing_slash()
     * adds trailingslah to path
     * @param mixed $str
     * @param bool $first
     * @return
     */
    public static function add_trailing_slash(&$str, $first = false) {
        $str .= (substr($str, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
        if ($first == true && substr($str, 0, 1) != DIRECTORY_SEPARATOR) {
            $str = DIRECTORY_SEPARATOR . $str;
        }
        return (string )$str;
    }

    /**
     * keimeno_class::msge()
     * adds error message. Standard us for formulars etc. useable in backend and frontend
     * @param mixed $msge
     * @return
     */
    public static function msge($msge) {
        $msge = trim($msge);
        if ($msge != "")
            $_SESSION['err_msgs'][] = $msge;
    }

    /**
     * keimeno_class::msg()
     * adds success message. Standard us for formulars etc. useable in backend and frontend
     * @param mixed $msg
     * @return
     */
    public static function msg($msg) {
        $_SESSION['ok_msgs'][] = trim($msg);
    }

    /**
     * keimeno_class::allocate_memory()
     * release memory of defined variables in php
     * @param mixed $obj
     * @return
     */
    public static function allocate_memory(&$obj) {
        $obj = "";
        unset($obj);
    }

    /**
     * keimeno_class::currency_format()
     * format number to a currency format
     * @param mixed $amount
     * @param integer $precision
     * @param bool $use_commas
     * @param bool $parentheses_for_negative_amounts
     * @return
     */
    static function currency_format($amount, $precision = 2, $use_commas = true, $parentheses_for_negative_amounts = false) {
        $amount = (float)$amount;
        // Get rid of negative zero
        $zero = round(0, $precision);
        if (round($amount, $precision) == $zero) {
            $amount = $zero;
        }

        if ($use_commas) {
            if ($parentheses_for_negative_amounts && ($amount < 0)) {
                $amount = '(' . number_format(abs($amount), $precision, ',', '.') . ')';

            }
            else {
                $amount = number_format($amount, $precision, ',', '.');
                return $amount;
            }
        }
        else {
            if ($parentheses_for_negative_amounts && ($amount < 0)) {
                $amount = '(' . round(abs($amount), $precision) . ')';
            }
            else {
                #$amount = round($amount, $precision);
                $amount = number_format($amount, $precision, '.', ',');
            }
        }

        return $amount;
    }

    /**
     * keimeno_class::msg_trans()
     * translates messages 
     * @return
     */
    protected function msg_trans() {
        $ALANG_OBJ = new adminlang_class();
        foreach ((array )$_SESSION['err_msgs'] as $key => $value) {
            $_SESSION['err_msgs'][$key] = $ALANG_OBJ->translate_globalpack($value, $_SESSION['GBL_LOCAL_ID']);
        }
        foreach ((array )$_SESSION['ok_msgs'] as $key => $value) {
            $_SESSION['ok_msgs'][$key] = $ALANG_OBJ->translate_globalpack($value, $_SESSION['GBL_LOCAL_ID']);
        }
        $this->allocate_memory($ALANG_OBJ);
    }

    /**
     * keimeno_class::fast_array_admintrans()
     * translation for backend
     * @param mixed $arr
     * @return
     */
    public static function fast_array_admintrans(&$arr) {
        return json_decode(kf::translate_admin(json_encode((array )$arr)), true);
    }

    /**
     * keimeno_class::msg_trans_fe()
     * translation of messages for frontend
     * @return
     */
    protected function msg_trans_fe() {
        foreach ((array )$_SESSION['err_msgs'] as $key => $value) {
            $_SESSION['err_msgs'][$key] = pure_translation($_SESSION['err_msgs'][$key], $_SESSION['GBL_LANGID']);
        }
        foreach ((array )$_SESSION['ok_msgs'] as $key => $value) {
            $_SESSION['ok_msgs'][$key] = pure_translation($_SESSION['ok_msgs'][$key], $_SESSION['GBL_LANGID']);
        }
    }

    /**
     * keimeno_class::ej()
     * send json encoded feedback to browser. you overhand a javascript function
     * which should be called. The second parameter represents parameters of javascript funktion.
     * This function is realy important if you send an ajax json request. Just put class="jsonform" in the <form> tag
     * @param string $java_call_func
     * @param string $jsparams
     * @return
     */
    public function ej($java_call_func = '', $jsparams = '') {
        $this->echo_json_fb($java_call_func, $jsparams);
    }

    /**
     * keimeno_class::echo_json_fb()
     * send json encoded feedback fpr formulars which has been submited via jsonform (class="jsonform")
     * @param string $java_call_func
     * @param string $jsparams
     * @return
     */
    public function echo_json_fb($java_call_func = '', $jsparams = '') {
        if (is_array($_SESSION['ok_msgs']) && count($_SESSION['ok_msgs']) == 0)
            $this->msg('saved');
        if (defined('ISADMIN') && ISADMIN == 1) {
            $this->msg_trans();
        }
        else {
            $this->msg_trans_fe();
        }
        $arr = array(
            'msg' => implode('<br>', (array )$_SESSION['ok_msgs']),
            'msge' => implode('<br>', (array )$_SESSION['err_msgs']),
            'jsfunction' => $java_call_func,
            'jsparams' => $jsparams);
        unset($_SESSION['ok_msgs']);
        unset($_SESSION['err_msgs']);
        echo json_encode($arr);
        $this->hard_exit();
    }

    /**
     * keimeno_class::has_errors()
     * checks if an error accours
     * @return
     */
    public function has_errors() {
        $_SESSION['err_msgs'] = (array )$_SESSION['err_msgs'];
        return count($_SESSION['err_msgs']) > 0;
    }

    /**
     * keimeno_class::add_smarty_err()
     * a different way to report an error back to browser. (desperate function)
     * @param mixed $err_arr
     * @param mixed $field
     * @param mixed $err
     * @return
     */
    public function add_smarty_err(&$err_arr, $field, $err) {
        $err = str_replace(';', '', $err);
        $value = $err_arr[$field];
        $value .= ($value != "") ? ';' . $err : $err;
        $err_arr[$field] = $value;
        return $err_arr;
    }

    /**
     * keimeno_class::add_smarty_errors()
     * a different way to report an error back to browser. (desperate function)
     * @param mixed $err_arr
     * @param mixed $field
     * @param mixed $err
     * @return
     */
    public function add_smarty_errors(&$err_arr, $field, $err) {
        keimeno_class::add_smarty_err($err_arr, $field, $err);
    }

    /**
     * keimeno_class::strip_html()
     * removes html code from array values
     * @param mixed $arr
     * @return array
     */
    public function strip_html($arr) {
        if (count($arr) > 0) {
            foreach ($arr as $key => $value) {
                $arr[$key] = strip_tags($arr[$key]);
            }
        }
        return $arr;
    }

    /**
     * keimeno_class::get_error_count()
     * returns quantity of errors. (desperate function)
     * @return
     */
    public function get_error_count() {
        return count($this->GBLPAGE['err']);
    }

    /**
     * keimeno_class::break_to_newline()
     * converst <br> to text new line (br2nl)
     * @param mixed $text
     * @return string
     */
    public static function break_to_newline($text) {
        $text = preg_replace('/<br\\\\s*?\\/?/i', "\\n", $text);
        return str_replace("<br />", "", $text);
    }

    /**
     * keimeno_class::get_micro_time()
     * Return micro time
     * @return float
     */
    public static function get_micro_time() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * keimeno_class::gen_plain_text()
     * Returns plain text. UTF8 compatible
     * @param mixed $txt
     * @return string
     */
    public static function gen_plain_text($txt) { //utf8 rein und raus
        # $txt = preg_replace("/({\/?)(\w+)([^>]*})/e", "", $txt); # {...} entfernen
        $txt = preg_replace_callback("/({\/?)(\w+)([^>]*})/", function ($matches) {
            return ""; }
        , $txt);
        $txt = strip_tags(html_entity_decode($txt));
        $txt = preg_replace('/\[^\pL]/u', ' ', $txt); //UTF8 kompatibel, umlaute bleiben erhalten
        $txt = preg_replace('/[^\w\pL]/u', ' ', $txt); // entfernt nun hier sauber alle Satzzeichen
        $txt = preg_replace('/\s+/', ' ', $txt); //entfernt zeilenumbrueche, whitespace
        return trim($txt);
    }

    /**
     * keimeno_class::pure_text()
     * just an other function for getting plain text
     * @param mixed $html
     * @return string
     */
    public function pure_text($html) {
        # $html = preg_replace("/({\/?)(\w+)([^>]*})/e", "", $html); # {...} entfernen
        $html = preg_replace_callback("/({\/?)(\w+)([^>]*})/", function ($matches) {
            return ""; }
        , $html);
        $html = utf8_encode(strip_tags(html_entity_decode(utf8_decode($html))));
        $html = preg_replace('/\[^\pL]/u', ' ', $html); //UTF8 kompatibel, umlaute bleiben erhalten entfernt Satzzeichen
        $html = preg_replace('/\s+/', ' ', $html); # remove whitespace and line breaks
        return $html;
    }

    /**
     * keimeno_class::pure_text_iso()
     * Returns plain text in ISO Format. May usefull for XLS exported text
     * @param mixed $html
     * @return string
     */
    public function pure_text_iso($html) {
        return utf8_decode($this->pure_text($html));
    }

    /**
     * keimeno_class::csv_formated_iso()
     * Returns CSV compatible string
     * @param mixed $html
     * @return string
     */
    public function csv_formated_iso($html) {
        $html = strip_tags(html_entity_decode($html, ENT_COMPAT, 'UTF-8'));
        $html = trim(preg_replace('/\s+/', ' ', $html)); # remove whitespace and line breaks
        if (is_numeric($html))
            $html = str_replace('.', ',', $html);
        return utf8_decode($html);
    }

    /**
     * keimeno_class::gen_plain_text_iso()
     * Returns ISO formated plain text
     * @param mixed $txt
     * @return
     */
    public function gen_plain_text_iso($txt) {
        return utf8_decode($this->gen_plain_text($txt));
    }

    /**
     * keimeno_class::xml_xls_formated()
     * Returns string compatible with XML and XLS
     * @param mixed $str
     * @return
     */
    public function xml_xls_formated($str) {
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        $str = preg_replace('/&#?\w+;/', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str); //entfernet zeilenumbrueche, whitespace
        return utf8_decode($str);
    }

    /**
     * keimeno_class::format_file_name()
     * Turns a overhanded filename into a readable internet filename
     * @param mixed $str
     * @return
     */
    public static function format_file_name($str) {
        $str = mb_strtolower($str, 'UTF-8');
        $str = trim(strtr($str, static::$umlaute));
        $str = preg_replace('#[\/]+#', ' ', $str); // entfernt alle slashes /
        #$str = preg_replace('/[^0-9a-z?-????\`\~\!\$\%\^\*\; \,\.\_\-]/i', ' ',$str);
        $str = preg_replace('/[^0-9a-z\`\~\!\$\%\^\*\; \,\.\_\-]/i', ' ', $str);
        $str = trim($str);
        $str = preg_replace('/\s+/', '-', $str); //entfernt zeilenumbrueche, whitespace
        $str = preg_replace('/--/', '-', $str);
        while (strstr($str, '--'))
            $str = str_replace('--', '-', $str);
        if (substr($str, -1) == '-') {
            $str = rtrim($str, '-');
        }
        return $str;
    }


    /**
     * keimeno_class::gen_seo_name()
     * 
     * @param mixed $FORM
     * @param mixed $fname
     * @return
     */
    public static function gen_seo_name($arr, $fname) {
        $str = "";
        $arr = (array )$arr;
        foreach ($arr as $key => $value) {
            if (!is_array($value)) {
                $str .= $value . ' ';
            }
        }
        if ($str == "") {
            $str = $fname;
        }
        $str = strtr($str, static::$umlaute);
        $str = substr($str, 0, 30);
        $str = preg_replace("/[^a-zA-Z0-9\-]/", "", $str);
        return $str . '.' . self::get_ext($fname);
    }

    /**
     * keimeno_class::format_tpl_name()
     * Formats template name into storeable template name
     * @param mixed $str
     * @return
     */
    public function format_tpl_name($str) {
        $str = mb_strtolower($str, 'UTF-8');
        $str = strtr($str, static::$umlaute);
        $str = preg_replace('#[\/]+#', ' ', $str); // entfernt alle slashes /
        #$str = preg_replace('/[^0-9a-z?-????\`\~\!\$\%\^\*\; \,\.\_\-]/i', ' ',$str);
        $str = preg_replace('/[^0-9a-z\`\~\!\$\%\^\*\; \,\.\_\-]/i', ' ', $str);
        $str = preg_replace('/\s+/', '-', $str); //entfernt zeilenumbrueche, whitespace
        $str = preg_replace('/--/', '-', $str);
        while (strstr($str, '--'))
            $str = str_replace('--', '-', $str);
        return $str;
    }

    /**
     * keimeno_class::gen_meta_description()
     * Generates metadescription from overhanded text
     * @param mixed $html
     * @return string
     */
    public static function gen_meta_description($html) { //UTF8 rein und raus
        $del_tags = array(
            'script',
            'style',
            'head');
        foreach ($del_tags as $tag) {
            $html = preg_replace('#<' . $tag . '(.*?)>(.*?)</' . $tag . '>#is', '', $html);
        }
        $html = strip_tags($html);
        #$html = preg_replace("/({\/?)(\w+)([^>]*})/e", "", $html); # {...} entfernen
        $html = preg_replace_callback("/({\/?)(\w+)([^>]*})/", function ($m) {
            return ""; }
        , $html); # {...} entfernen
        $html = str_replace(array('<%', '%>'), '', $html);
        $html = preg_replace('/\s+/', ' ', $html); # remove whitespace and line breaks
        $meta_description = substr(html_entity_decode($html, ENT_COMPAT, 'UTF-8'), 0, static::$config['metadesc_count']);
        return trim($meta_description);
    }

    /**
     * keimeno_class::gen_meta_keywords()
     * Generates usefull keywords from html code
     * @param mixed $html
     * @param string $delimiter
     * @return string
     */
    public static function gen_meta_keywords($html, $delimiter = ',') {
        $htmlcrawl = new htmlcrawl_class($html);
        $htmlcrawl->gen_meta_keywords_se($meta_keywords, $delimiter, true);
        return $meta_keywords;
    }

    /**
     * keimeno_class::get_local_path()
     * 
     * @return void
     */
    public static function get_local_path() {
        if (be_in_ssl_area() === true) {
            $PATH_CMS_LOCAL = (static::$config['std_lang_id'] != $_SESSION['GBL_LANGID']) ? SSL_PATH_SYSTEM . '/' . $_SESSION['GBL_LOCAL_ID'] . '/' : SSL_PATH_SYSTEM .
                PATH_CMS;
        }
        else {
            $PATH_CMS_LOCAL = (static::$config['std_lang_id'] != $_SESSION['GBL_LANGID']) ? '/' . $_SESSION['GBL_LOCAL_ID'] . '/' : PATH_CMS;
        }
        return $PATH_CMS_LOCAL;
    }

    /**
     * keimeno_class::only_alphanums()
     * Returns string which contains only numbers or alphabetical keys
     * @param mixed $string
     * @return
     */
    public static function only_alphanums($string) {
        $string = preg_replace("/[^0-9a-zA-Z ]/", "", strval($string));
        return $string;
    }

    /**
     * keimeno_class::only_numbers()
     * 
     * @param mixed $string
     * @return
     */
    public static function only_numbers($string) {
        $string = preg_replace("/[^0-9]/", "", strval($string));
        return $string;
    }

    /**
     * keimeno_class::explode_string_by_ident()
     * Explode a string by ident and returns an clean array
     * @param mixed $ident
     * @param mixed $txtstr
     * @return array
     */
    public function explode_string_by_ident($ident, $txtstr) {
        if ($txtstr != "")
            return explode($ident, $txtstr);
        else
            return array();
    }


    /**
     * keimeno_class::parse_query()
     * Parse the url query into array
     * @param mixed $url
     * @return array
     */
    public function parse_query($url) {
        $var = parse_url($url, PHP_URL_QUERY);
        $var = html_entity_decode($var);
        $var = explode('&', $var);
        $arr = array();

        foreach ($var as $val) {
            $x = explode('=', $val);
            $arr[$x[0]] = ((isset($x[1])) ? $x[1] : "");
        }
        unset($val, $x, $var);
        return $arr;
    }

    /**
     * keimeno_class::modify_url()
     * add a key / value pair into url
     * @param mixed $url
     * @param mixed $mod
     * @return string
     */
    public function modify_url($url, $mod) {
        #  $url = urlencode($url);
        $url_arr = parse_url($url);
        $query = array();
        if (isset($url_arr['query']))
            $query = explode("&", $url_arr['query']);
        if (!isset($url_arr['query'])) {
            $queryStart = "?";
        }
        else {
            $queryStart = "&";
        }
        // modify/delete data
        foreach ($query as $q) {
            list($key, $value) = explode("=", $q);
            if (array_key_exists($key, $mod)) {
                if ($mod[$key]) {
                    $url = preg_replace('/' . $key . '=' . $value . '/', $key . '=' . $mod[$key], $url);
                }
                else {
                    $url = preg_replace('/&?' . $key . '=' . $value . '/', '', $url);
                }
            }
        }
        // add new data
        $k = 0;
        foreach ($mod as $key => $value) {
            if ($value && !preg_match('/&' . $key . '=/', $url) && !preg_match('/\?' . $key . '=/', $url)) {
                $k++;
                if ($k == 1)
                    $url .= $queryStart . $key . '=' . $value;
                else
                    $url .= '&' . $key . '=' . $value;
            }
        }
        return $url;
    }

    /**
     * keimeno_class::get_domain_name_of_url()
     * Get the domain of url
     * @param mixed $url
     * @return string
     */
    public function get_domain_name_of_url($url) {
        $parts = explode('.', $url);
        return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
    }

    /**
     * keimeno_class::get_domain_name()
     * Returns domain name of local host
     * @return
     */
    public function get_domain_name() {
        $parts = explode('.', $_SERVER["HTTP_HOST"]);
        return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
    }

    /**
     * keimeno_class::get_domain_name_pure()
     * Returns only domain name
     * @return
     */
    public function get_domain_name_pure() {
        $parts = explode('.', $_SERVER["HTTP_HOST"]);
        return $parts[count($parts) - 2];
    }

    /**
     * keimeno_class::parse_xmlfile_to_array()
     * Parse an xml file into an nested array
     * @param string $file
     * @param string $xml
     * @return array
     */
    public static function parse_xmlfile_to_array($file = '', $xml = '') {
        $xml_parser = xml_parser_create();
        if ($file != '') {
            $data = file_get_contents($file);
        }

        if ($xml != '') {
            $data = $xml;
        }

        xml_parse_into_struct($xml_parser, $data, $vals, $index);
        xml_parser_free($xml_parser);

        $params = array();
        $level = array();
        foreach ($vals as $xml_elem) {
            if ($xml_elem['type'] == 'open') {
                if (array_key_exists('attributes', $xml_elem)) {
                    list($level[$xml_elem['level']], $extra) = array_values($xml_elem['attributes']);
                }
                else {
                    $level[$xml_elem['level']] = $xml_elem['tag'];
                }
            }
            if ($xml_elem['type'] == 'complete') {
                $start_level = 1;
                $php_stmt = '$params';
                while ($start_level < $xml_elem['level']) {
                    $php_stmt .= '[$level[' . $start_level . ']]';
                    $start_level++;
                }
                $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
                eval($php_stmt);
            }
        }
        return $params;
    }


    /**
     * keimeno_class::get_part_of_array()
     * Returns a part of an array
     * @param mixed $list
     * @param mixed $from
     * @param mixed $limit
     * @return array
     */
    public function get_part_of_array($list, $from, $limit) {
        if ($limit == 0)
            return array();
        $temp_list = array_chunk($list, $limit, true);
        return $temp_list[floor($from / $limit)];
    }

    /**
     * keimeno_class::delete_dir_with_subdirs()
     * Delete a directory with all its subdirectories
     * @param mixed $dir
     * @return
     */
    public static function delete_dir_with_subdirs($dir) {
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir . $file))
                        @unlink($dir . $file);
                    else
                        self::delete_dir_with_subdirs($dir . $file);
                }
            }
            closedir($openDir);
            @rmdir($dir);
        }
    }

    /**
     * keimeno_class::table_header_sorting()
     * desperate function
     * @param mixed $sort_arr
     * @param mixed $default_col
     * @return
     */
    public function table_header_sorting(&$sort_arr, $default_col) {
        //sorting
        $key = md5($_SERVER['PHP_SELF'] . $_REQUEST['epage'] . $_REQUEST['page'] . $_REQUEST['aktion']);
        $FILTER = array(
            'st' => $_REQUEST['st'],
            'sc' => $_REQUEST['sc'],
            'sd' => $_REQUEST['sd']);
        if (isset($_REQUEST['sd'])) {
            $_SESSION['SFILTER'][$key] = $FILTER;
        }
        else {
            $FILTER = $_SESSION['SFILTER'][$key];
        }
        $sd = ($FILTER['sd'] == 'ASC') ? 'SORT_ASC' : 'SORT_DESC';
        $st = ($FILTER['st'] == 'NUM') ? 'SORT_NUMERIC' : 'SORT_STRING';
        $sc = ($FILTER['sc'] == "") ? $default_col : $FILTER['sc'];
        $sort_arr = sort_multi_array($sort_arr, $sc, constant($sd), constant($st));
        $FILTER['sd'] = ($FILTER['sd'] == 'ASC') ? 'DESC' : 'ASC';
        unset($FILTER['st']);
        unset($FILTER['sc']);
        $this->smarty->assign('sfilter_query', http_build_query($FILTER));
    }

    /**
     * keimeno_class::array_utf8encode()
     * Encodes an array to utf8
     * @param mixed $arr
     * @return array
     */
    public static function array_utf8encode(&$arr) {
        if (is_array($arr)) {
            foreach ($arr as $key => $value)
                if (is_array($value)) {
                    $arr[$key] = self::array_utf8encode($value);
                }
                else
                    $arr[$key] = utf8_encode($value);
        }
        return $arr;
    }

    /**
     * keimeno_class::array_utf8decode()
     * Decodes an array to ISO
     * @param mixed $arr
     * @return array
     */
    public static function array_utf8decode(&$arr) {
        if (is_array($arr)) {
            foreach ($arr as $key => $value)
                if (is_array($value)) {
                    $arr[$key] = self::array_utf8decode($value);
                }
                else
                    $arr[$key] = utf8_decode($value);
        }
        return $arr;
    }

    /**
     * keimeno_class::get_ext()
     * Returns file extention
     * @param mixed $Filename
     * @return string
     */
    public static function get_ext($Filename) {
        if (strstr($Filename, '.')) {
            $RetVal = explode('.', $Filename);
            return strtolower($RetVal[count($RetVal) - 1]);
        }
        else
            return "";
    }

    /**
     * keimeno_class::change_file_ext()
     * Change file extention of file
     * @param mixed $file
     * @param mixed $ext
     * @return string
     */
    public static function change_file_ext($file, $ext) {
        $org_ext = self::get_ext($file);
        $file = str_replace('.' . $org_ext, '', $file);
        return $file . '.' . strtolower($ext);
    }

    /**
     * keimeno_class::kill_white_spaces()
     * Removes white space of string (desperate function)
     * @param mixed $str
     * @return string
     */
    public function kill_white_spaces($str) {
        return keimeno_class::remove_white_space($str);
    }

    /**
     * keimeno_class::remove_white_space()
     * Removes white space of string
     * @param mixed $str
     * @return string
     */
    public function remove_white_space($str) {
        $rep_arr = array(
            " ",
            "\n",
            "\t",
            "\r");
        return str_replace($rep_arr, "", $str);
    }

    /**
     * keimeno_class::kill_double_space()
     * Removes double space in string
     * @param mixed $str
     * @return string
     */
    public function kill_double_space($str) {
        while (strstr($str, '  '))
            $str = str_replace('  ', ' ', $str);
        return $str;
    }


    /**
     * keimeno_class::min_len()
     * Returns the string with a minimun length
     * @param mixed $text
     * @param mixed $min_len
     * @param string $fill
     * @return string
     */
    public static function min_len($text, $min_len, $fill = " ") {
        while (strlen($text) < $min_len)
            $text .= $fill;
        return $text;
    }

    /**
     * keimeno_class::get_all_column_types()
     * Returns array of all columns of a table
     * @param mixed $Table
     * @return array
     */
    public function get_all_column_types($Table) {
        $dbQuery = mysqli_query($this->db->link_id, "SHOW FULL COLUMNS FROM " . $Table);
        while ($dbRow = mysqli_fetch_assoc($dbQuery)) {
            foreach ($dbRow as $key => $wert)
                $EnumValues[$dbRow["Field"]][strtoupper($key)] = $wert;
        }
        return (array )$EnumValues;
    }


    /**
     * keimeno_class::add_index_to_column()
     * 
     * @param mixed $table
     * @param mixed $column
     * @return void
     */
    function add_index_to_column($table, $column) {
        mysqli_query($this->db->link_id, "ALTER TABLE " . $table . " DROP INDEX ( `" . $column . "`  )");
        mysqli_query($this->db->link_id, "ALTER TABLE " . $table . " ADD INDEX ( `" . $column . "`  )");
    }

    /**
     * keimeno_class::hard_exit()
     * disconnect database connection and stops excuting script. Hard exit of script.
     * @return
     */
    public function hard_exit() {
        $this->db->disconnect();
        die();
    }

    /**
     * keimeno_class::seconds_to_hms()
     * Transform seconds into HMS format
     * @param mixed $sec
     * @param bool $padHours
     * @return string
     */
    public function seconds_to_hms($sec, $strip_sec = false, $padHours = false) {
        $neg = $sec < 0;
        $sec = ($sec < 0) ? ($sec * -1) : $sec;
        $hms = "";
        $hours = intval(intval($sec) / 3600);
        $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" : $hours . ":";
        $minutes = intval(($sec / 60) % 60);
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":";
        $seconds = intval($sec % 60);
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
        if ($strip_sec == true) {
            $list = explode(':', $hms);
            array_pop($list);
            $hms = implode(':', $list);
        }
        return ($neg == true) ? '-' . $hms : $hms;
    }

    /**
     * keimeno_class::is_valid_url()
     * validates an url against if it is url
     * @param mixed $url
     * @return boolean
     */
    public function is_valid_url($url = null) {
        if ($url == null)
            return false;

        return (filter_var($url, FILTER_VALIDATE_URL));
    }

    /**
     * keimeno_class::trim_array()
     * trims an array
     * @param mixed $array
     * @return array
     */
    public static function trim_array(&$array) {
        foreach ((array)$array as $key => $v) {
            if (!is_array($v)) {
                $array[$key] = trim($v);
            }
            else {
                $array[$key] = self::trim_array($v);
            }
        }
        return (array )$array;
    }

    /**
     * keimeno_class::min_str_len()
     * Returns param-1 with a minimum length
     * @param mixed $str
     * @param mixed $len
     * @return
     */
    public function min_str_len($str, $len) {
        while (strlen($str) < $len)
            $str .= ' ';
        return $str;
    }

    /**
     * keimeno_class::curl_exec_script()
     * Excute remote script via curl and returns content
     * @param mixed $url
     * @return string
     */
    public static function curl_exec_script($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLINFO_NAMELOOKUP_TIME, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    /**
     * keimeno_class::http_build_query_for_curl()
     * Transform array into url for curl
     * @param mixed $arrays
     * @param mixed $new
     * @param mixed $prefix
     * @return
     */
    public static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null) {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }
        foreach ($arrays as $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (is_array($value) or is_object($value)) {
                self::http_build_query_for_curl($value, $new, $k);
            }
            else {
                $new[$k] = $value;
            }
        }
    }

    /**
     * keimeno_class::curl_get_data()
     * Load remote script/page and returns data. You can use for REST Client/Server application
     * @param mixed $url
     * @param mixed $vars
     * @return string
     */
    public static function curl_get_data($url, $vars = array()) {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        # curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');

        if (is_array($vars) && count($vars) > 0) {
            curl_setopt($ch, CURLOPT_POST, 1);
            #  self::http_build_query_for_curl($vars, $curl_vars);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * keimeno_class::curl_get_data_to_file()
     * Loads remote file directly into local file
     * @param mixed $url
     * @param mixed $local_file
     * @return boolean
     */
    public static function curl_get_data_to_file($url, $local_file) {
        $fp = fopen($local_file, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        #        echo $data;die; $failure = (strstr($data, '302 Found'));
        if ($data == false) {
            @unlink($local_file);
            return false;
        }
        if (filesize($local_file) < 10000) {
            if (strstr(file_get_contents($local_file), '302 Found')) {
                @unlink($local_file);
                return false;
            }
        }
        return true;
    }


    /**
     * keimeno_class::untar_archive()
     * 
     * @param mixed $tar_file_gz
     * @return void
     */
    public static function untar_archive($tar_file_gz, $target_root = "", $overwrite = true, $delete = true) {
        if (file_exists($tar_file_gz)) {
            try {
                $target_root = ($target_root == "") ? rtrim(CMS_ROOT . '/') : rtrim($target_root, '/');
                $tarfile = str_replace('.gz', '', $tar_file_gz);
                if (file_exists($tarfile)) {
                    chmod($tarfile, 0755);
                    @unlink($tarfile);
                }
                file_put_contents($tarfile, gzopen($tar_file_gz, r));
                #  $phar = new PharData($tar_file_gz);
                #  $phar->decompress();
                unset($phar);
                $phar_tar = new PharData($tarfile);
                $phar_tar->extractTo($target_root, null, $overwrite); // extract all files, and overwrite
                if ($delete == true) {
                    chmod($tarfile, 0755);
                    chmod($tar_file_gz, 0755);
                    @unlink($tarfile);
                    @unlink($tar_file_gz);
                }
                unset($phar_tar);
            }
            catch (Exception $e) {
                echo $e->getMessage();
                if ($delete == true) {
                    @unlink($tarfile);
                    @unlink($tar_file_gz);
                }
            }
        }
    }

    /**
     * keimeno_class::tar_archive()
     * 
     * @param mixed $destdir
     * @param mixed $tarfile
     * @param mixed $exclude
     * @return
     */
    public static function tar_archive($dir_to_tar, $tarfile, $exclude = array(), $filter = array()) {
        if (file_exists($tarfile)) {
            @unlink($tarfile);
        }
        if (file_exists($tarfile . '.gz')) {
            @unlink($tarfile . '.gz');
        }
        static::$exclude_filter = $exclude;
        static::$include_filter = $filter;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir_to_tar, FilesystemIterator::SKIP_DOTS));
        $filterIterator = new CallbackFilterIterator($iterator, function ($file) {
            if (count(static::$exclude_filter) > 0) {
                foreach (static::$exclude_filter as $value) {
                    if (strpos($file, $value) !== false) {
                        return false; }
                }
            }
            if (count(static::$include_filter) > 0) {
                foreach (static::$include_filter as $value) {
                    if (strpos($file, $value) == false) {
                        return false; }
                }
            }
            return true; }
        );

        $phar = new PharData($tarfile);
        $phar->buildFromIterator($filterIterator, $dir_to_tar);
        $phar->compress(Phar::GZ);
        @unlink($tarfile);
    }


    /**
     * keimeno_class::col_exists()
     * Check if column exists in table
     * @param mixed $table
     * @param mixed $col
     * @return
     */
    public function col_exists($table, $col) {
        $t = $this->get_all_column_types($table);
        return key_exists($col, $t);
    }

    /**
     * keimeno_class::file_get_contents()
     * PHP trick to still use file_get_content (desperate function)
     * @param mixed $file
     * @return string
     */
    public static function file_get_contents($file) {
        $arrContextOptions = array("ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                ));
        return file_get_contents($file, false, stream_context_create($arrContextOptions));
    }

    /**
     * keimeno_class::real_escape()
     * Realescap a string
     * @param mixed $arr
     * @return string
     */
    public static function real_escape(&$var) {
        if (is_string($var)) {
            return kdb::real_escape_string($var);
        }
        if (is_array($var)) {
            foreach ($var as $key => $wert)
                if (is_string($wert)) {
                    $var[$key] = kdb::real_escape_string($var[$key]);
                }
                elseif (is_array($wert)) {
                    $var[$key] = self::real_escape($wert);
                }
            return $var;
        }
        else {
            return $var;
        }
    }

    /**
     * keimeno_class::arr_trimsthsc()
     * Trims array and does htmlspecialchars 
     * @param mixed $arr
     * @return array
     */
    public function arr_trimsthsc($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim(htmlspecialchars(strip_tags($arr[$key])));
            }
            else {
                $arr[$key] = $this->arr_trimsthsc($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_trim()
     * Trims an array recursively
     * @param mixed $arr
     * @return array
     */
    public static function arr_trim($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim($arr[$key]);
            }
            else {
                $arr[$key] = self::arr_trim($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_trimhsc()
     * Trims an array recursively and does htmlspecialchars
     * @param mixed $arr
     * @return array
     */
    public static function arr_trimhsc($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim(htmlspecialchars($arr[$key]));
            }
            else {
                $arr[$key] = self::arr_trimhsc($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_trim_striptags()
     * Trims an array recursively and does strip_tags
     * @param mixed $arr
     * @return array
     */
    public static function arr_trim_striptags($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim(strip_tags($arr[$key]));
            }
            else {
                $arr[$key] = self::arr_trim_striptags($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_hsc()
     * 
     * @param mixed $arr
     * @return
     */
    public static function arr_hsc($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = htmlspecialchars($arr[$key]);
            }
            else {
                $arr[$key] = self::arr_hsc($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_stripslashes()
     * Trims an array recursively and does stripslashes
     * @param mixed $arr
     * @return array
     */
    public static function arr_stripslashes($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim(stripslashes($arr[$key]));
            }
            else {
                $arr[$key] = self::arr_stripslashes($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::is_needle_in_string()
     * Is needle in string?
     * @param mixed $haystack
     * @param mixed $needle
     * @return boolean
     */
    public function is_needle_in_string($haystack, $needle) {
        $pos = strpos($haystack, $needle);
        return ($pos !== false);
    }

    /**
     * keimeno_class::format_number()
     * Formats float into readable number format
     * @param mixed $number
     * @param integer $cents
     * @return number
     */
    public static function format_number($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
        if (is_numeric($number)) { // a number
            if (!$number) { // zero
                $money = ($cents == 2 ? '0.00' : '0'); // output zero
            }
            else { // value
                if (floor($number) == $number) { // whole number
                    $money = number_format($number, ($cents == 2 ? 2 : 0), ',', '.'); // format
                }
                else { // cents
                    $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2), ',', '.'); // format
                } // integer or decimal
            } // value
            return $money;
        } // numeric
    } // formatMoney

    /**
     * keimeno_class::number_format()
     * Formats float into readable number format
     * @param mixed $number
     * @param integer $nachkommastellen
     * @param bool $cuttozero
     * @param bool $ganzzahl
     * @return number
     */
    public static function number_format($number, $nachkommastellen = 2, $cuttozero = true, $ganzzahl = false) {
        $number = str_replace(",", ".", $number);
        if ($cuttozero === true && ($number * 1) == 0)
            return 0;
        if ($ganzzahl == true) {
            $number = self::format_number($number, 1);
        }
        else
            $number = number_format($number, $nachkommastellen, ',', '.');
        return $number;
    }

    /**
     * keimeno_class::sql_num()
     * Transform german number format into sql number format
     * @param mixed $number
     * @return number
     */
    public static function sql_num(&$number) {
        $num_float = (float)$number;
        if ((string )$num_float == (string )$number)
            return $num_float;
        $number = preg_replace("/[^0-9],.-/", "", $number);
        $number = floatval(str_replace(',', '.', str_replace('.', '', $number)));
        return $number;
    }

    /**
     * keimeno_class::validate_num_for_sql()
     * Transform german number format into sql number format 
     * (desperate function)
     * @param mixed $num
     * @return
     */
    public static function validate_num_for_sql($num) {
        $num = trim(str_replace(",", ".", $num));
        return ($num * 1);
    }


    /**
     * echo_excel_header()
     * Send excel header information to browser
     * @param mixed $filename
     * @param string $ext
     * @return
     */
    public static function echo_excel_header($filename, $ext = 'xls') {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        #header("Content-type: application-download");
        header("Content-Type: application/vnd.ms-excel");
        #   header("Content-Length: $size");
        header("Content-Disposition: attachment; filename=\"$filename." . $ext . "\"");
        header("Content-Transfer-Encoding: binary");
    }


    /**
     * keimeno_class::sort_multi_array()
     * Sorts an array by its columns
     * $data = sort_multi_array($data, 'last_name', SORT_ASC, SORT_STRING, 'first_name', SORT_ASC, SORT_STRING); 
     *
     * @param array $data Result of sql query as associative array
     * 
     * @param mixed $data
     * @return array $data - Sorted data
     */
    public static function sort_multi_array(array $data) {
        /*$name, $order, $mode*/
        $_argList = func_get_args();
        $_data = array_shift($_argList);
        if (empty($_data)) {
            return $_data;
        }
        $_max = count($_argList);
        $_params = array();
        $_cols = array();
        $_rules = array();
        for ($_i = 0; $_i < $_max; $_i += 3) {
            $_name = (string )$_argList[$_i];
            if (!in_array($_name, array_keys(current($_data)))) {
                continue;
            }
            if (!isset($_argList[($_i + 1)]) || is_string($_argList[($_i + 1)])) {
                $_order = SORT_ASC;
                $_mode = SORT_REGULAR;
                $_i -= 2;
            }
            else
                if (3 > $_argList[($_i + 1)]) {
                    $_order = SORT_ASC;
                    $_mode = $_argList[($_i + 1)];
                    $_i--;
                }
                else {
                    $_order = $_argList[($_i + 1)] == SORT_ASC ? SORT_ASC : SORT_DESC;
                    if (!isset($_argList[($_i + 2)]) || is_string($_argList[($_i + 2)])) {
                        $_mode = SORT_REGULAR;
                        $_i--;
                    }
                    else {
                        $_mode = $_argList[($_i + 2)];
                    }
                }
                $_mode = $_mode != SORT_NUMERIC ? $_argList[($_i + 2)] != SORT_STRING ? SORT_REGULAR : SORT_STRING : SORT_NUMERIC;
            $_rules[] = array(
                'name' => $_name,
                'order' => $_order,
                'mode' => $_mode);
        }
        foreach ($_data as $_k => $_row) {
            foreach ($_rules as $_rule) {
                if (!isset($_cols[$_rule['name']])) {
                    $_cols[$_rule['name']] = array();
                    $_params[] = &$_cols[$_rule['name']];
                    $_params[] = $_rule['order'];
                    $_params[] = $_rule['mode'];
                }
                $_cols[$_rule['name']][$_k] = $_row[$_rule['name']];
            }
        }
        $_params[] = &$_data;
        call_user_func_array('array_multisort', $_params);
        return $_data;
    }

    /**
     * keimeno_class::include_protection()
     * (desperate function)
     * @return
     */
    public function include_protection() {
        if (IN_SIDE != 1) {
            header('location:' . PATH_CMS . 'index.html');
            exit;
        }
    }

    /**
     * keimeno_class::redirect_301()
     * Redirects to specified url via 301 redirection
     * @param mixed $url
     * @return
     */
    public function redirect_301($url) {
        header("HTTP/1.1 301 Moved Permanently");
        header('location:' . $url, TRUE, 301);
        $this->hard_exit();
    }

    /**
     * keimeno_class::std_frontend_call()
     * (desperate function)
     * @param mixed $class_name
     * @return
     */
    public function std_frontend_call($class_name) {
        $class_obj = new $class_name();
        $class_obj->TCR->interpreterfe();
        if (method_exists($class_obj, 'parse_to_smarty'))
            $class_obj->parse_to_smarty();
    }

    /**
     * keimeno_class::add_tpl()
     * Returns smarty template integration include
     * @param mixed $content
     * @param mixed $tpl
     * @return string
     */
    public static function add_tpl(&$content, $tpl) {
        $content .= '<%include file="' . $tpl . '.tpl"%>';
    }

    /**
     * keimeno_class::date_to_sqldate()
     * Transform german date into sql date
     * @param mixed $date
     * @return date
     */
    public static function date_to_sqldate($date) {
        if (strpos($date, '.') > 0) {
            $part = explode(".", $date);
            $date = $part[2] . '-' . $part[1] . '-' . $part[0];
        }
        return $date;
    }

    /**
     * keimeno_class::is_bot()
     * Check if client is bot
     * @return boolean
     */
    public function is_bot() {
        $this->SEOBOTS = array(
            'AdsBot [Google]' => 'AdsBot-Google',
            'Alexa [Bot]' => 'ia_archiver',
            'Alta Vista [Bot]' => 'Scooter/',
            'Ask Jeeves [Bot]' => 'Ask Jeeves',
            'Baidu [Spider]' => 'Baiduspider+(',
            'Exabot [Bot]' => 'Exabot/',
            'FAST Enterprise [Crawler]' => 'FAST Enterprise Crawler',
            'FAST WebCrawler [Crawler]' => 'FAST-WebCrawler/',
            'Francis [Bot]' => 'http://www.neomo.de/',
            'Gigabot [Bot]' => 'Gigabot/',
            'Google Adsense [Bot]' => 'Mediapartners-Google',
            'Google Desktop' => 'Google Desktop',
            'Google Feedfetcher' => 'Feedfetcher-Google',
            'Google [Bot]' => 'Googlebot',
            'Bing [Bot]' => 'MSNBOT',
            'Heise IT-Markt [Crawler]' => 'heise-IT-Markt-Crawler',
            'Heritrix [Crawler]' => 'heritrix/1.',
            'IBM Research [Bot]' => 'ibm.com/cs/crawler',
            'ICCrawler - ICjobs' => 'ICCrawler - ICjobs',
            'ichiro [Crawler]' => 'ichiro/2',
            'Majestic-12 [Bot]' => 'MJ12bot/',
            'Metager [Bot]' => 'MetagerBot/',
            'MSN NewsBlogs' => 'msnbot-NewsBlogs/',
            'MSN [Bot]' => 'msnbot/',
            'MSNbot Media' => 'msnbot-media/',
            'NG-Search [Bot]' => 'NG-Search/',
            'Nutch [Bot]' => 'http://lucene.apache.org/nutch/',
            'Nutch/CVS [Bot]' => 'NutchCVS/',
            'OmniExplorer [Bot]' => 'OmniExplorer_Bot/',
            'Online link [Validator]' => 'online link validator',
            'psbot [Picsearch]' => 'psbot/0',
            'Seekport [Bot]' => 'Seekbot/',
            'Sensis [Crawler]' => 'Sensis Web Crawler',
            'SEO Crawler' => 'SEO search Crawler/',
            'Seoma [Crawler]' => 'Seoma [SEO Crawler]',
            'SEOSearch [Crawler]' => 'SEOsearch/',
            'Snappy [Bot]' => 'Snappy/1.1 ( http://www.urltrends.com/ )',
            'Steeler [Crawler]' => 'http://www.tkl.iis.u-tokyo.ac.jp/~crawler/',
            'Synoo [Bot]' => 'SynooBot/',
            'Telekom [Bot]' => 'crawleradmin.t-info@telekom.de',
            'TurnitinBot [Bot]' => 'TurnitinBot/',
            'Voyager [Bot]' => 'voyager/1.0',
            'W3 [Sitesearch]' => 'W3 SiteSearch Crawler',
            'W3C [Linkcheck]' => 'W3C-checklink/',
            'W3C [Validator]' => 'W3C_*Validator',
            'WiseNut [Bot]' => 'http://www.WISEnutbot.com',
            'YaCy [Bot]' => 'yacybot',
            'Yahoo MMCrawler [Bot]' => 'Yahoo-MMCrawler/',
            'Yahoo Slurp [Bot]' => 'Yahoo! DE Slurp',
            'Yahoo [Bot]' => 'Yahoo! Slurp',
            'YahooSeeker [Bot]' => 'YahooSeeker/',
            ); // SPIDERCHECK
        foreach ($this->SEOBOTS as $bot => $botstring) {
            if ($botstring) {
                if (substr_count(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($botstring)) > 0) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    /**
     * keimeno_class::hyperlink()
     * 
     * @param mixed $str
     * @param bool $newwindow
     * @return
     */
    public static function hyperlink($str, $newwindow = true) {
        $nw = ($newwindow == true) ? ' target="_blank"' : "";
        $find = array('`((?:https?|ftp)://\S+[[:alnum:]]/?)`si', '`((?<!//)(www\.\S+[[:alnum:]]/?))`si');
        $replace = array('<a href="$1"' . $nw . '>$1</a>', '<a href="http://$1"' . $nw . '>$1</a>');
        return preg_replace($find, $replace, $str);
    }

    /**
     * keimeno_class::convert_to_strings()
     * Convert values of array into string
     * @param mixed $arr
     * @return array
     */
    public static function convert_to_strings(&$arr) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = self::convert_to_strings($value);
            }
            else {
                $arr[$key] = strval($value);
            }
        }
        return $arr;
    }

    /**
     * keimeno_class::unique_filename()
     * Generate a unique filename in a directory
     * @param mixed $path
     * @param mixed $f_name
     * @return string $f_name - filename
     */
    public static function unique_filename($path, $f_name) {
        $f_name = self::format_file_name($f_name);
        $fileParts = pathinfo($f_name);
        if (strtolower($fileParts['extension']) == 'jpeg') {
            $f_name = str_replace('.jpeg', '.jpg', $f_name);
        }
        $org_name = $f_name;
        while (file_exists($path . $f_name)) {
            $ext = trim(strrchr($org_name, '.'));
            $new_name = str_replace($ext, '', $org_name);
            $k++;
            $f_name = $new_name . '_' . $k . $ext;
        }
        return $f_name;
    }

    /**
     * keimeno_class::add_time_to_date()
     * Adds specified time to a date.
     * @param mixed $sql_date
     * @param integer $add_year
     * @param integer $add_month
     * @param integer $add_day
     * @param string $outputformat
     * @return
     */
    public static function add_time_to_date($sql_date, $add_year = 0, $add_month = 0, $add_day = 0, $outputformat = 'Y-m-d') { //YYYY-MM-DD
        $publictime = str_replace("-", "", $sql_date);
        $sec = substr($publictime, 12, 2);
        $min = substr($publictime, 10, 2);
        $hour = substr($publictime, 8, 2);
        $day = substr($publictime, 6, 2);
        $month = substr($publictime, 4, 2);
        $year = substr($publictime, 0, 4);
        return date($outputformat, mktime(0, 0, 0, $month + $add_month, $day + $add_day, $year + $add_year));
    }


    /**
     * keimeno_class::set_debug()
     * Enables or disbale debug mode of Keimeno
     * @param bool $debug
     * @return
     */
    public static function set_debug($debug = true) {
        if ($debug == true) {
            ini_set('display_errors', 1);
            ini_set('track_errors', 1);
            ini_set('log_errors_max_len', '1024');
            # error_reporting(E_ALL & ~E_NOTICE);
            error_reporting(E_ERROR | E_PARSE);
            set_error_handler(array('core_error_handler', 'handleError'));
        }
        elseif ($debug == false) {
            #error_reporting(-1 & ~ E_NOTICE);
            error_reporting(0);
        }
        else {
            error_reporting(intval($debug));
        }
    }

    /**
     * keimeno_class::get_all_columns_of_table()
     * Returns array of all columns of an table
     * @param mixed $Table
     * @return array
     */
    public function get_all_columns_of_table($Table) {
        $EnumValues = array();
        $dbQuery = mysqli_query($this->db->link_id, "SHOW COLUMNS FROM " . $Table);
        if ($dbQuery !== false) {
            while ($dbRow = mysqli_fetch_assoc($dbQuery)) {
                foreach ($dbRow as $key => $wert)
                    $EnumValues[$dbRow["Field"]][strtoupper($key)] = $wert;
            }
        }
        return $EnumValues;
    }

    /**
     * keimeno_class::get_all_columns()
     * Returns array of all columns of an table (desperate function)
     * @param mixed $table_name
     * @return array
     */
    public function get_all_columns($table_name) {
        $column_types = self::get_all_column_types($table_name);
        foreach ($column_types as $column_name => $column_TYPE) {
            $arr[] = $column_name;
        }
        return (array )$arr;
    }

    /**
     * keimeno_class::direct_download()
     * Direct download of file to browser
     * @param mixed $file
     * @return
     */
    public static function direct_download($file) {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        }
        else {
            echo 'No file found:' . basename($file);
        }
        exit();
    }

    /**
     * keimeno_class::download_pdf()
     * 
     * @param mixed $file
     * @return void
     */
    public static function download_pdf($file) {
        $filename = basename($file);
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        header('Expires: 0');
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        readfile($file);
        exit();
    }

    /**
     * keimeno_class::create_nested_array()
     * Creates an nested array out of flat array like sql result
     * @param mixed $elements
     * @param integer $parentId
     * @param string $id
     * @param string $parentID
     * @return array
     */
    function create_nested_array(array $elements, $parentId = 0, $id = 'prodCatID', $parentID = 'parentID') {
        $branch = array();
        foreach ($elements as $element) {
            if ($element[$parentID] == $parentId) {
                $children = $this->create_nested_array($elements, $element[$id], $id, $parentID);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * keimeno_class::is_image_svg()
     * 
     * @param mixed $file
     * @return
     */
    public static function is_image_svg($file) {
        return self::get_ext($file) == 'svg' || 'image/svg+xml' === mime_content_type($file) || 'image/svg' === mime_content_type($file);
    }

    /**
     * keimeno_class::is_image()
     * Check if file is an image
     * @param mixed $file
     * @return boolean
     */
    public static function is_image($file) {
        if (is_file($file)) {
            if (self::is_image_svg($file) == true) {
                return true;
            }
            else {
                return getimagesize($file) ? true : false;
            }
        }
        else
            return false;
    }

    /**
     * keimeno_class::xml2array()
     * Transform XML content to array
     * @param mixed $contents
     * @param integer $get_attributes
     * @param string $priority
     * @return array
     */
    function xml2array($contents, $get_attributes = 1, $priority = 'tag') {
        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if (!$xml_values)
            return; //Hmm...

        //Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference

        //Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        foreach ($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if ($type == "open") { //The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];

                }
                else { //There was another element with the same tag name

                    if (isset($current[$tag][0])) { //If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else { //This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }

                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }

            }
            elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;

                }
                else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;

                    }
                    else { //If it is not an array...
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }

            }
            elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }

        return ($xml_array);
    }

    /**
     * keimeno_class::is_mobile_client()
     * Check if browser client is a mobile
     * @return boolean
     */
    public static function is_mobile_client() {
        $mobiles = 'iPhone|Android|webOS|BlackBerry|iPod|SMART-TV|Opera Mini|Opera Mobi|Silk|Mobile';
        $is_mobile = preg_match("/$mobiles/", $_SERVER['HTTP_USER_AGENT']) > 0;
        return (bool)$is_mobile;
    }

    /**
     * keimeno_class::ssl_active()
     * Check if SSL is active
     * @return boolean
     */
    public static function ssl_active() {
        return ((isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON'));
    }

    /**
     * keimeno_class::is_32bit()
     * 
     * @return
     */
    public static function is_32bit() {
        return PHP_INT_SIZE === 4;
    }

    /**
     * keimeno_class::return_bytes()
     * 
     * @param mixed $val
     * @return
     */
    public static function ini_get_return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'p':
                $val *= (1024 * 1024 * 1024 * 1024 * 1024);
                break;
            case 't':
                $val *= (1024 * 1024 * 1024 * 1024);
                break;
            case 'g':
                $val *= (1024 * 1024 * 1024);
                break;
            case 'm':
                $val *= (1024 * 1024);
                break;
            case 'k':
                $val *= 1024;
                break;
        }
        return $val;
    }

    /**
     * keimeno_class::get_maximum_file_uploadsize()
     * 
     * @return
     */
    public static function get_maximum_file_uploadsize() {
        return min(self::ini_get_return_bytes(ini_get('post_max_size')), self::ini_get_return_bytes(ini_get('upload_max_filesize')));
    }


    /**
     * keimeno_class::get_value_from_table()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param mixed $where
     * @return
     */
    public function get_value_from_table($table, $column, $where) {
        $result = $this->db->query_first("SELECT " . $column . " FROM " . $table . " WHERE " . $where);
        return $result[$column];
    }

    /**
     * keimeno_class::get_http_protocol()
     * 
     * @return
     */
    public static function get_http_protocol() {
        return (self::get_config_value('ssl_forcessl') == 1) ? 'https' : 'http';
    }

    /**
     * keimeno_class::get_domain_url()
     * 
     * @return
     */
    public static function get_domain_url() {
        $parsedUrl = parse_url(self::get_config_value('opt_domain'));
        $host = explode('.', $parsedUrl['path']);
        $subdomains = array_slice($host, 0, count($host) - 2);
        if (count($subdomains) == 0) {
            return self::get_http_protocol() . "://www." . self::get_config_value('opt_domain') . PATH_CMS;
        }
        else {
            return self::get_http_protocol() . "://" . self::get_config_value('opt_domain') . PATH_CMS;
        }
    }

    /**
     * keimeno_class::send_json()
     * 
     * @param mixed $arr
     * @return void
     */
    public static function send_json(array $arr) {
        echo json_encode($arr);
        exit();
    }

    /**
     * keimeno_class::human_filesize()
     * 
     * @param mixed $bytes
     * @param integer $decimals
     * @return
     */
    public static function human_filesize($bytes, $decimals = 2) {
        $size = array(
            'Bytes',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    /**
     * keimeno_class::seconds_to_human()
     * 
     * @param mixed $ss
     * @return
     */
    public static function seconds_to_human($ss) {
        $s = $ss % 60;
        $m = floor(($ss % 3600) / 60);
        $h = floor(($ss % 86400) / 3600);
        $d = floor(($ss % 2592000) / 86400);
        # $M = floor($ss / 2592000);
        return array(
            'months' => $M,
            'days' => $d,
            'hours' => $h,
            'min' => $m,
            'sec' => $s);
    }

    /**
     * keimeno_class::is_strong_password()
     * 
     * @param mixed $pw
     * @return
     */
    public static function is_strong_password($pw, $length = 6) {
        if (strlen($pw) < $length)
            return false;
        return preg_match("/[a-z]/", $pw) && preg_match("/[A-Z]/", $pw) && preg_match("/[0-9]/", $pw);
    }

    /**
     * keimeno_class::get_my_ip()
     * 
     * @return
     */
    public static function get_my_ip() {
        return (empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) : $_SERVER['HTTP_CLIENT_IP']);
    }

    /**
     * keimeno_class::xls_num()
     * 
     * @param mixed $number
     * @param integer $decimals
     * @return
     */
    public static function xls_num($number, $decimals = 2) {
        return number_format((float)$number, $decimals, ",", "");
    }

    /**
     * keimeno_class::file_upload_err_to_txt()
     * 
     * @param mixed $code
     * @return
     */
    public static function file_upload_err_to_txt($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }
}

class kException extends Exception {
    protected static $ke_header = '<html>
        <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        </head>
        <body>
           <div class="container"> 
            ';
    protected static $ke_footer = '</div></body></html>';

    /**
     * kException::get_error_message()
     * 
     * @return string
     */
    public function get_error_message() {
        //error message
        $errorMsg = static::$ke_header . '<div class="bg-danger text-danger"><table class="table"  >
            <tr><td width="110">Error on line</td><td>' . $this->getLine() . '</td></tr>
            <tr><td>File:</td><td>' . $this->getFile() . '</td></tr>
            <tr><td>Message:</td><td><b>' . $this->getMessage() . '</b></td></tr></table></div>' . static::$ke_footer;
        return $errorMsg;
    }
}
