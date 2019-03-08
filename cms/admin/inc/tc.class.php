<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



class tc_class {
    var $db = NULL;
    var $gbl_config = array();
    protected static $config = array();
    var $smarty = NULL;
    var $NESTED_ARR = NULL;
    var $GBLPAGE = array();
    var $CORE = NULL;

    protected $MODIDENT = "";

    /**
     * keimeno_class::keimeno_class()
     * 
     * @return
     */
    function __construct() {
        global $kdb, $gbl_config, $smarty, $gbl_config_shop, $shop_tables, $cms_tables, $LOGCLASS, $CORE;
        $this->db = $kdb;
        $this->smarty = $smarty;
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
     * 
     * @return string
     */
    public function get_config_value($key) {
        return static::$config[$key];
    }

    /**
     * keimeno_class::recurse_copy()
     * 
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
     * 
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
     * 
     * @param mixed $ip
     * @return
     */
    public static function anonymizing_ip($ip) {
        return preg_replace('#(?:\.\d+){1}$#', '.0', $ip);
    }

    /**
     * keimeno_class::no_errors()
     * 
     * @return
     */
    public function no_errors() {
        return count($this->GBLPAGE['err']) == 0;
    }

    /**
     * keimeno_class::add_err()
     * 
     * @param mixed $text
     * @return
     */
    public function add_err($text) {
        $this->GBLPAGE['err'][] = $text;
    }

    /**
     * keimeno_class::add_access_err()
     * 
     * @param mixed $key
     * @param mixed $status
     * @return
     */
    public function add_access_err($key, $status) {
        $this->GBLPAGE['access'][$key] = $status;
    }

    /**
     * keimeno_class::add_trailing_slash()
     * 
     * @param mixed $str
     * @param bool $first
     * @return
     */
    public function add_trailing_slash($str, $first = false) {
        $str .= (substr($str, -1) == '/' ? '' : '/');
        if ($first == true && substr($str, 0, 1) != '/') {
            $str = '/' . $str;
        }
        return (string )$str;
    }

    /**
     * keimeno_class::msge()
     * 
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
     * 
     * @param mixed $msg
     * @return
     */
    public static function msg($msg) {
        $_SESSION['ok_msgs'][] = trim($msg);
    }

    /**
     * keimeno_class::allocate_memory()
     * 
     * @param mixed $obj
     * @return
     */
    public function allocate_memory(&$obj) {
        $obj = "";
        unset($obj);
    }

    /**
     * keimeno_class::currency_format()
     * 
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
     * 
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
     * 
     * @param mixed $arr
     * @return
     */
    protected function fast_array_admintrans(&$arr) {
        return json_decode(kf::translate_admin(json_encode($arr)), true);
    }

    /**
     * keimeno_class::msg_trans_fe()
     * 
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
     * 
     * @param string $java_call_func
     * @param string $jsparams
     * @return
     */
    public function ej($java_call_func = '', $jsparams = '') {
        $this->echo_json_fb($java_call_func, $jsparams);
    }

    /**
     * keimeno_class::echo_json_fb()
     * 
     * @param string $java_call_func
     * @param string $jsparams
     * @return
     */
    public function echo_json_fb($java_call_func = '', $jsparams = '') {
        if (count($_SESSION['ok_msgs']) == 0)
            $this->msg('saved');
        if (ISADMIN == 1) {
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
        ECHO json_encode($arr);
        $this->hard_exit();
    }

    /**
     * keimeno_class::has_errors()
     * 
     * @return
     */
    public function has_errors() {
        return count($_SESSION['err_msgs']) > 0;
    }

    /**
     * keimeno_class::add_smarty_err()
     * 
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
     * 
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
     * 
     * @param mixed $FORM
     * @return
     */
    public function strip_html($FORM) {
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $value) {
                $FORM[$key] = strip_tags($FORM[$key]);
            }
        }
        return $FORM;
    }

    /**
     * keimeno_class::get_error_count()
     * 
     * @return
     */
    public function get_error_count() {
        return count($this->GBLPAGE['err']);
    }

    /**
     * keimeno_class::break_to_newline()
     * 
     * @param mixed $text
     * @return
     */
    public static function break_to_newline($text) {
        $text = preg_replace('/<br\\\\s*?\\/?/i', "\\n", $text);
        return str_replace("<br />", "", $text);
    }

    /**
     * keimeno_class::get_micro_time()
     * 
     * @return
     */
    public static function get_micro_time() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * keimeno_class::gen_plain_text()
     * 
     * @param mixed $txt
     * @return
     */
    public static function gen_plain_text($txt) { //utf8 rein und raus
        $txt = preg_replace("/({\/?)(\w+)([^>]*})/e", "", $txt); # {...} entfernen
        $txt = strip_tags(html_entity_decode($txt));
        $txt = preg_replace('/\[^\pL]/u', ' ', $txt); //UTF8 kompatibel, umlaute bleiben erhalten
        $txt = preg_replace('/[^\w\pL]/u', ' ', $txt); // entfernt nun hier sauber alle Satzzeichen
        $txt = preg_replace('/\s+/', ' ', $txt); //entfernt zeilenumbrueche, whitespace
        return trim($txt);
    }

    /**
     * keimeno_class::pure_text()
     * 
     * @param mixed $html
     * @return
     */
    public function pure_text($html) {
        $html = preg_replace("/({\/?)(\w+)([^>]*})/e", "", $html); # {...} entfernen
        $html = utf8_encode(strip_tags(html_entity_decode(utf8_decode($html))));
        $html = preg_replace('/\[^\pL]/u', ' ', $html); //UTF8 kompatibel, umlaute bleiben erhalten entfernt Satzzeichen
        $html = preg_replace('/\s+/', ' ', $html); # remove whitespace and line breaks
        return $html;
    }

    /**
     * keimeno_class::pure_text_iso()
     * 
     * @param mixed $html
     * @return
     */
    public function pure_text_iso($html) {
        return utf8_decode($this->pure_text($html));
    }

    /**
     * keimeno_class::csv_formated_iso()
     * 
     * @param mixed $html
     * @return
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
     * 
     * @param mixed $txt
     * @return
     */
    public function gen_plain_text_iso($txt) {
        return utf8_decode($this->gen_plain_text($txt));
    }

    /**
     * keimeno_class::xml_xls_formated()
     * 
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
     * 
     * @param mixed $str
     * @return
     */
    public function format_file_name($str) {
        $str = strtolower($str);
        $rep = array(
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss');
        $str = strtr($str, $rep);
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
     * keimeno_class::format_tpl_name()
     * 
     * @param mixed $str
     * @return
     */
    public function format_tpl_name($str) {
        $str = strtolower($str);
        $rep = array(
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss');
        $str = strtr($str, $rep);
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
     * 
     * @param mixed $html
     * @return
     */
    public static function gen_meta_description($html) { //UTF8 rein und raus
        $del_tags = array(
            'script',
            'style',
            'head');
        foreach ($del_tags as $tag) {
            $html = preg_replace('#<' . $tag . '(.*?)>(.*?)</' . $tag . '>#is', '', $html);
            #$html = preg_replace('#<' . $tag . '\b.+?</' . $tag . '>#', '', $html);
        }
        $html = strip_tags($html);
        $html = preg_replace("/({\/?)(\w+)([^>]*})/e", "", $html); # {...} entfernen
        $html = str_replace(array('<%', '%>'), '', $html);
        $html = preg_replace('/\s+/', ' ', $html); # remove whitespace and line breaks
        $meta_description = substr(html_entity_decode($html, ENT_COMPAT, 'UTF-8'), 0, static::$config['metadesc_count']);
        return trim($meta_description);
    }

    /**
     * keimeno_class::gen_meta_keywords()
     * 
     * @param mixed $html
     * @param string $delimiter
     * @return
     */
    public static function gen_meta_keywords($html, $delimiter = ',') {
        $htmlcrawl = new htmlcrawl_class($html);
        $htmlcrawl->gen_meta_keywords_se($meta_keywords, $delimiter);
        return $meta_keywords;
    }

    /**
     * keimeno_class::only_alphanums()
     * 
     * @param mixed $string
     * @return
     */
    public static function only_alphanums($string) {
        $string = preg_replace("/[^0-9a-zA-Z ]/", "", strval($string));
        return $string;
    }

    /**
     * keimeno_class::explode_string_by_ident()
     * 
     * @param mixed $ident
     * @param mixed $txtstr
     * @return
     */
    public function explode_string_by_ident($ident, $txtstr) {
        if ($txtstr != "")
            return explode($ident, $txtstr);
        else
            return array();
    }


    /**
     * keimeno_class::parse_query()
     * 
     * @param mixed $url
     * @return
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
     * 
     * @param mixed $url
     * @param mixed $mod
     * @return
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
     * 
     * @param mixed $url
     * @return
     */
    public function get_domain_name_of_url($url) {
        $parts = explode('.', $url);
        return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
    }

    /**
     * keimeno_class::get_domain_name()
     * 
     * @return
     */
    public function get_domain_name() {
        $parts = explode('.', $_SERVER["HTTP_HOST"]);
        return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
    }

    /**
     * keimeno_class::get_domain_name_pure()
     * 
     * @return
     */
    public function get_domain_name_pure() {
        $parts = explode('.', $_SERVER["HTTP_HOST"]);
        return $parts[count($parts) - 2];
    }

    /**
     * keimeno_class::parse_xmlfile_to_array()
     * 
     * @param string $file
     * @param string $xml
     * @return
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
     * 
     * @param mixed $list
     * @param mixed $from
     * @param mixed $limit
     * @return
     */
    public function get_part_of_array($list, $from, $limit) {
        if ($limit == 0)
            return array();
        $temp_list = array_chunk($list, $limit, true);
        return $temp_list[floor($from / $limit)];
    }

    /**
     * keimeno_class::delete_dir_with_subdirs()
     * 
     * @param mixed $dir
     * @return
     */
    public function delete_dir_with_subdirs($dir) {
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir . $file))
                        @unlink($dir . $file);
                    else
                        $this->delete_dir_with_subdirs($dir . $file);
                }
            }
            closedir($openDir);
            @rmdir($dir);
        }
    }

    /**
     * keimeno_class::table_header_sorting()
     * 
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
     * 
     * @param mixed $arr
     * @return
     */
    public function array_utf8encode(&$arr) {
        if (is_array($arr)) {
            foreach ($arr as $key => $value)
                $arr[$key] = utf8_encode($value);
        }
    }

    /**
     * keimeno_class::array_utf8decode()
     * 
     * @param mixed $arr
     * @return
     */
    public function array_utf8decode(&$arr) {
        if (is_array($arr)) {
            foreach ($arr as $key => $value)
                $arr[$key] = utf8_decode($value);
        }
    }

    /**
     * keimeno_class::get_ext()
     * 
     * @param mixed $Filename
     * @return
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
     * 
     * @param mixed $file
     * @param mixed $ext
     * @return
     */
    public static function change_file_ext($file, $ext) {
        $org_ext = self::get_ext($file);
        $file = str_replace('.' . $org_ext, '', $file);
        return $file . '.' . strtolower($ext);
    }

    /**
     * keimeno_class::kill_white_spaces()
     * 
     * @param mixed $str
     * @return
     */
    public function kill_white_spaces($str) {
        return keimeno_class::remove_white_space($str);
    }

    /**
     * keimeno_class::remove_white_space()
     * 
     * @param mixed $str
     * @return
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
     * 
     * @param mixed $str
     * @return
     */
    public function kill_double_space($str) {
        while (strstr($str, '  '))
            $str = str_replace('  ', ' ', $str);
        return $str;
    }

    //Relative Date Function

    /**
     * keimeno_class::relative_date()
     * 
     * @param mixed $time
     * @return
     */
    public function relative_date($time) {
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
     * keimeno_class::color_class()
     * 
     * @param mixed $relative_date
     * @return
     */
    public function color_class($relative_date) {
        if ($relative_date == '{LBL_TODAY}') {
            return 'redimportant';
        }
        if ($relative_date == '{LBL_YESTERDAY}') {
            return 'bold';
        }
    }

    /**
     * keimeno_class::min_len()
     * 
     * @param mixed $text
     * @param mixed $min_len
     * @param string $fill
     * @return
     */
    public static function min_len($text, $min_len, $fill = " ") {
        while (strlen($text) < $min_len)
            $text .= $fill;
        return $text;
    }

    /**
     * keimeno_class::get_all_column_types()
     * 
     * @param mixed $Table
     * @return
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
     * keimeno_class::hard_exit()
     * 
     * @return
     */
    public function hard_exit() {
        $this->db->disconnect();
        die();
    }

    /**
     * keimeno_class::seconds_to_hms()
     * 
     * @param mixed $sec
     * @param bool $padHours
     * @return
     */
    public function seconds_to_hms($sec, $padHours = false) {
        $hms = "";
        // do the hours first: there are 3600 seconds in an hour, so if we divide
        // the total number of seconds by 3600 and throw away the remainder, we're
        // left with the number of hours in those seconds
        $hours = intval(intval($sec) / 3600);
        // add hours to $hms (with a leading 0 if asked for)
        $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" : $hours . ":";
        // dividing the total seconds by 60 will give us the number of minutes
        // in total, but we're interested in *minutes past the hour* and to get
        // this, we have to divide by 60 again and then use the remainder
        $minutes = intval(($sec / 60) % 60);
        // add minutes to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":";
        // seconds past the minute are found by dividing the total number of seconds
        // by 60 and using the remainder
        $seconds = intval($sec % 60);
        // add seconds to $hms (with a leading 0 if needed)
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
        return $hms;
    }

    /**
     * keimeno_class::is_valid_url()
     * 
     * @param mixed $url
     * @return
     */
    public function is_valid_url($url = NULL) {
        if ($url == NULL)
            return false;

        $protocol = '(http://|https://)';
        $allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';

        $regex = "^" . $protocol . // must include the protocol
            '(' . $allowed . '{1,63}\.)+' . // 1 or several sub domains with a max of 63 chars
            '[a-z]' . '{2,6}'; // followed by a TLD
        if (eregi($regex, $url) == true)
            return true;
        else
            return false;
    }

    /**
     * keimeno_class::trim_array()
     * 
     * @param mixed $array
     * @return
     */
    public static function trim_array(&$array) {
        foreach ($array as $key => $v)
            $array[$key] = trim($v);
        return (array )$array;
    }

    /**
     * keimeno_class::min_str_len()
     * 
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
     * 
     * @param mixed $url
     * @return
     */
    public static function curl_exec_script($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLINFO_NAMELOOKUP_TIME, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    /**
     * keimeno_class::http_build_query_for_curl()
     * 
     * @param mixed $arrays
     * @param mixed $new
     * @param mixed $prefix
     * @return
     */
    public static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null) {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }
        foreach ($arrays AS $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (is_array($value) OR is_object($value)) {
                self::http_build_query_for_curl($value, $new, $k);
            }
            else {
                $new[$k] = $value;
            }
        }
    }

    /**
     * keimeno_class::curl_get_data()
     * 
     * @param mixed $url
     * @param mixed $vars
     * @return
     */
    public function curl_get_data($url, $vars = array()) {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        # curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');

        if (is_array($vars) && count($vars) > 0) {
            curl_setopt($ch, CURLOPT_POST, 1);
            self::http_build_query_for_curl($vars, $curl_vars);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_vars);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * keimeno_class::curl_get_data_to_file()
     * 
     * @param mixed $url
     * @param mixed $local_file
     * @return
     */
    public function curl_get_data_to_file($url, $local_file) {
        $fp = fopen($local_file, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
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
     * keimeno_class::col_exists()
     * 
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
     * 
     * @param mixed $file
     * @return
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
     * 
     * @param mixed $arr
     * @return
     */
    public function real_escape($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = $this->db->real_escape_string($arr[$key]);
            }
            else {
                $arr[$key] = $this->real_escape($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_trimsthsc()
     * 
     * @param mixed $arr
     * @return
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
     * 
     * @param mixed $arr
     * @return
     */
    public function arr_trim($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim($arr[$key]);
            }
            else {
                $arr[$key] = $this->arr_trim($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_trimhsc()
     * 
     * @param mixed $arr
     * @return
     */
    public function arr_trimhsc($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim(htmlspecialchars($arr[$key]));
            }
            else {
                $arr[$key] = $this->arr_trimhsc($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_trim_striptags()
     * 
     * @param mixed $arr
     * @return
     */
    public function arr_trim_striptags($arr) {
        foreach ((array )$arr as $key => $wert)
            if (!is_array($wert)) {
                $arr[$key] = trim(strip_tags($arr[$key]));
            }
            else {
                $arr[$key] = $this->arr_trim_striptags($arr[$key]);
            }
            return $arr;
    }

    /**
     * keimeno_class::arr_stripslashes()
     * 
     * @param mixed $arr
     * @return
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
     * 
     * @param mixed $haystack
     * @param mixed $needle
     * @return
     */
    public function is_needle_in_string($haystack, $needle) {
        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * keimeno_class::format_number()
     * 
     * @param mixed $number
     * @param integer $cents
     * @return
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
     * 
     * @param mixed $menge
     * @param integer $nachkommastellen
     * @param bool $cuttozero
     * @param bool $ganzzahl
     * @return
     */
    public static function number_format($menge, $nachkommastellen = 2, $cuttozero = TRUE, $ganzzahl = false) {
        $menge = str_replace(",", ".", $menge);
        if ($cuttozero === TRUE && ($menge * 1) == 0)
            return 0;
        if ($ganzzahl == TRUE) {
            $menge = $this->format_number($menge, 1);
        }
        else
            $menge = number_format($menge, 2, ',', '.');
        return $menge;
    }

    /**
     * keimeno_class::sql_num()
     * 
     * @param mixed $number
     * @return
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
     * 
     * @param mixed $num
     * @return
     */
    public static function validate_num_for_sql($num) {
        $num = trim(str_replace(",", ".", $num));
        Return ($num * 1);
    }


    /**
     * keimeno_class::sort_multi_array()
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
     * 
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
     * 
     * @param mixed $url
     * @return
     */
    public function redirect_301($url) {
        header("HTTP/1.1 301 Moved Permanently");
        header('location:' . $url);
        $this->hard_exit();
    }

    /**
     * keimeno_class::std_frontend_call()
     * 
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
     * 
     * @param mixed $content
     * @param mixed $tpl
     * @return
     */
    public function add_tpl(&$content, $tpl) {
        $content .= '<%include file="' . $tpl . '.tpl"%>';
    }

    /**
     * keimeno_class::date_to_sqldate()
     * 
     * @param mixed $date
     * @return
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
     * 
     * @return
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
     * @param mixed $url
     * @param bool $newwindow
     * @return
     */
    public function hyperlink(&$url, $newwindow = true) {
        $nw = ($newwindow == true) ? "target=\"_blank\"" : "";
        $url = ereg_replace("[[:alpha:]]+://([-]*[.]?[[:alnum:]_/-?&%])*", "<a href=\"\\0\" " . $nw . ">\\0</a>", $url);
        $url = ereg_replace("[[:alpha:]]+:(([-]*[.]?[[:alnum:]_/-?&%])+@([-]*[.]?[[:alnum:]_/-?&%])*)", "<a href=\"\\0\" " . $nw . ">\\0</a>", $url);
        $url = ereg_replace("(^| )(www([-]*[.]?[[:alnum:]_/-?&%])*)", "\\1<a href=\"http://\\2\" " . $nw . ">\\2</a>", $url);
        return $url;
    }

    /**
     * keimeno_class::convert_to_strings()
     * 
     * @param mixed $arr
     * @return
     */
    public function convert_to_strings(&$arr) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = $this->convert_to_strings($value);
            }
            else {
                $arr[$key] = strval($value);
            }
        }
        return $arr;
    }

    /**
     * keimeno_class::unique_filename()
     * 
     * @param mixed $path
     * @param mixed $f_name
     * @return
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
     * 
     * @param mixed $sql_date
     * @param integer $add_year
     * @param integer $add_month
     * @param integer $add_day
     * @param string $outputformat
     * @return
     */
    public function add_time_to_date($sql_date, $add_year = 0, $add_month = 0, $add_day = 0, $outputformat = 'Y-m-d') { //YYYY-MM-DD
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
     * 
     * @param bool $debug
     * @return
     */
    static function set_debug($debug = true) {
        if ($debug === true) {
            ini_set('display_errors', 1);
            ini_set('track_errors', '1');
            ini_set('log_errors_max_len', '1024');
            error_reporting(E_ALL ^ E_NOTICE);
            set_error_handler(array('core_error_handler', 'handleError')); #   error_reporting(-1);
        }
        elseif ($debug === false) {
            error_reporting(0);
        }
        else {
            error_reporting(intval($debug));
        }
    }

    /**
     * keimeno_class::get_all_columns_of_table()
     * 
     * @param mixed $Table
     * @return
     */
    public function get_all_columns_of_table($Table) {
        $dbQuery = mysqli_query($this->db->link_id, "SHOW COLUMNS FROM " . $Table);
        while ($dbRow = mysqli_fetch_assoc($dbQuery)) {
            foreach ($dbRow as $key => $wert)
                $EnumValues[$dbRow["Field"]][strtoupper($key)] = $wert;
        }
        return $EnumValues;
    }

    /**
     * keimeno_class::get_all_columns()
     * 
     * @param mixed $table_name
     * @return
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
     * 
     * @param mixed $file
     * @return
     */
    public function direct_download($file) {
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
     * keimeno_class::create_nested_array()
     * 
     * @param mixed $elements
     * @param integer $parentId
     * @param string $id
     * @param string $parentID
     * @return
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
     * keimeno_class::is_image()
     * 
     * @param mixed $file
     * @return
     */
    public static function is_image($file) {
        return getimagesize($file) ? true : false;
    }

    /**
     * keimeno_class::xml2array()
     * 
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
     * 
     * @return boolean
     */
    public static function is_mobile_client() {
        $mobiles = 'iPhone|Android|webOS|BlackBerry|iPod|SMART-TV|Opera Mini|Opera Mobi|Silk|Mobile';
        $is_mobile = preg_match("/$mobiles/", $_SERVER['HTTP_USER_AGENT']) > 0;
        return (bool)$is_mobile;
    }

    /**
     * keimeno_class::ssl_active()
     * 
     * @return boolean
     */
    public static function ssl_active() {
        return ((isset($_SERVER['HTTP_X_FORWARDED_HOST']) && $_SERVER['HTTP_X_FORWARDED_HOST'] == SSL_PROXY) || (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) ==
            'ON'));
    }


}


?>