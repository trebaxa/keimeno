<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

class kf extends keimeno_class {

    /**
     * kf::__construct()
     *
     * @return
     */
    function __construct() {
        parent::__construct();
    }


    /**
     * kf::gen_approve_icon()
     * Generate approval icon for backend; controlled with ajax
     *
     * @param mixed $id
     * @param mixed $value
     * @param string $acmd
     * @param string $toadd
     * @return string
     */
    public static function gen_approve_icon($id, $value, $acmd = 'axapprove_item', $toadd = '') {
        if ($value == 1) {
            return '<a class="btn btn-secondary axapprove" href="javascript:void(0);" title="{LBLA_APPROVED}" id="axapprove-' . $id . '" data-ident="' . $id .
                '" data-value="0" data-cmd="' . $acmd . '" data-toadd="' . $toadd . '" data-epage="' . $_REQUEST['epage'] . '" data-phpself="' . $_SERVER['PHP_SELF'] .
                '"  ><i class="fa fa-circle fa-green"><!----></i></a>';
        }
        else {
            return '<a class="btn btn-secondary axapprove" href="javascript:void(0);" title="{LBLA_NOTAPPROVED}" id="axapprove-' . $id . '" data-ident="' . $id .
                '" data-value="1" data-cmd="' . $acmd . '" data-toadd="' . $toadd . '" data-epage="' . $_REQUEST['epage'] . '" data-phpself="' . $_SERVER['PHP_SELF'] .
                '" ><i class="fa fa-circle fa-red"><!----></i></a>';
        }
    }

    /**
     * kf::gen_edit_icon()
     * Returns edit icon link.
     * @param mixed $id
     * @param string $toadd
     * @param string $a
     * @param string $idc
     * @param string $siteurl
     * @return string
     */
    public static function gen_edit_icon($id, $toadd = '', $a = 'edit', $idc = 'id', $siteurl = '') {
        if ($siteurl == "")
            $siteurl = $_SERVER['PHP_SELF'];
        return '<a class="btn btn-secondary ajax-link" title="{LBLA_EDIT}" href="' . $siteurl . '?' . (($_GET['epage'] != "") ? 'epage=' . $_GET['epage'] . '&' : '') . '' .
            $idc . '=' . $id . '&aktion=' . $a . '&cmd=' . $a . $toadd . '"><i class="far fa-edit"></i></a>';
    }

    /**
     * kf::gen_ax_edit_icon()
     * (desperate function)
     * @param mixed $id
     * @param string $target
     * @param string $a
     * @param string $toadd
     * @param string $idc
     * @param string $siteurl
     * @return string
     */
    public static function gen_ax_edit_icon($id, $target = '', $a = 'edit', $toadd = '', $idc = 'id', $siteurl = '') {
        if ($siteurl == "")
            $siteurl = $_SERVER['PHP_SELF'];
        $target = ($target != "") ? $target : 'admincontent';
        return '<a class="btn btn-secondary" title="{LBLA_EDIT}" href="javascript:void(0);" onClick="simple_load(\'' . $target . '\',\'' . $siteurl . '?' . (($_GET['epage'] !=
            "") ? 'epage=' . $_GET['epage'] . '&' : '') . '' . $idc . '=' . $id . '&aktion=' . $a . '&cmd=' . $a . $toadd . '\')"><i class="far fa-edit"></i></a>';
    }

    /**
     * kf::gen_plus_icon()
     * Generates plus icon. If you want to add something
     * @param mixed $id
     * @param string $toadd
     * @param string $a
     * @param string $idc
     * @param string $siteurl
     * @return string
     */
    public static function gen_plus_icon($id, $toadd = '', $a = 'edit', $idc = 'id', $siteurl = '') {
        if ($siteurl == "")
            $siteurl = $_SERVER['PHP_SELF'];
        return '<a class="btn btn-secondary" title="{LBLA_PLUS}" href="' . $siteurl . '?' . (($_GET['epage'] != "") ? 'epage=' . $_GET['epage'] . '&' : '') . '' . $idc .
            '=' . $id . '&aktion=' . $a . $toadd . '"><i class="fa fa-plus"></i></a>';
    }

    /**
     * kf::gen_eye_icon()
     * Generates an view icon with an eye.
     * @param mixed $link
     * @param string $target
     * @param string $class
     * @return string
     */
    public static function gen_eye_icon($link, $target = '_self', $class = "") {
        return '<a class="btn btn-secondary" ' . (($class != "") ? " class=\"" . $class . "\"" : "") . ' target="' . $target . '" title="{LBL_VIEW}" href="' . $link .
            '"><i class="fa fa-eye" ><!----></i></a>';
    }

    /**
     * kf::gen_chart_icon()
     * Generates a chart icon.
     * @param mixed $id
     * @param string $toadd
     * @return string
     */
    public static function gen_chart_icon($id, $toadd = '') {
        return '<a class="btn btn-secondary" title="{LBLA_STATISTIC}" href="' . $_SERVER['PHP_SELF'] . '?' . (($_GET['epage'] != "") ? 'epage=' . $_GET['epage'] . '&' :
            '') . 'id=' . $id . '&aktion=a_tracking' . $toadd . '"><i class="fa fa-bar-chart-o"><!----></i></a>';
    }

    /**
     * kf::gen_del_img_tagADMIN()
     * (desperate function)
     * @param mixed $id
     * @return string
     */
    public static function gen_del_img_tagADMIN($id) {
        return '<a class="btn btn-secondary" title="{LBLA_DELETE}" href="' . $_SERVER['PHP_SELF'] . '?' . (($_GET['epage'] != "") ? 'epage=' . $_GET['epage'] . '&' : '') .
            'id=' . $id . '&aktion=a_delconfirm"><i class="fa fa-trash"><!----></i></a>';
    }

    /**
     * kf::gen_clone_icon()
     * Generates clone icon.
     * @param mixed $id
     * @return string
     */
    public static function gen_clone_icon($id) {
        return '<a class="btn btn-secondary" title="{LBLA_CLONE}" href="' . $_SERVER['PHP_SELF'] . '?' . (($_GET['epage'] != "") ? 'epage=' . $_GET['epage'] . '&' : '') .
            'id=' . $id . '&cmd=a_klonen"><i class="fa fa-files-o"><!----></i></a>';
    }

    /**
     * kf::gen_std_icon()
     * Generates a standard icon link. You overhand the icon itself. Based on font awesome
     * @param mixed $id
     * @param mixed $icon
     * @param mixed $alt_tag
     * @param string $id_name
     * @param string $aktion
     * @param string $toadd
     * @param mixed $php_script
     * @return string
     */
    public static function gen_std_icon($id, $icon, $alt_tag, $id_name = 'id', $aktion = '', $toadd = '', $php_script) {
        return '<a class="btn btn-secondary" title="' . $alt_tag . '" href="' . $php_script . '?' . (($_GET['epage'] != "") ? 'epage=' . $_GET['epage'] . '&' : '') . '' .
            $id_name . '=' . $id . '&cmd=' . $aktion . $toadd . '"><i class="fa ' . $icon . '" ><!----></i></a>';
    }

    /**
     * kf::gen_del_icon_reload()
     * Generates a delete icon, which needs a page reload. Not ajax based
     * @param mixed $id
     * @param string $akt
     * @param string $confirm
     * @param string $toadd
     * @param string $idkey
     * @return string
     */
    public static function gen_del_icon_reload($id, $akt = 'a_del', $confirm = '{LBL_CONFIRM}', $toadd = '', $idkey = 'id') {
        return '<a class="btn btn-danger"' . gen_java_confirm($confirm) . ' title="{LBL_DELETE}" href="' . $_SERVER['PHP_SELF'] . '?' . (($_GET['epage'] != "") ?
            'epage=' . $_GET['epage'] . '&' : '') . $toadd . '&cmd=' . $akt . '&aktion=' . $akt . '&' . $idkey . '=' . $id . '"><i class="fa fa-trash"><!----></i></a>';
    }


    /**
     * kf::gen_del_icon_ajax()
     * (desperate function)
     * @param mixed $id
     * @param bool $c
     * @param string $cmd
     * @param string $phpfile
     * @param string $toadd
     * @return string
     */
    public static function gen_del_icon_ajax($id, $c = true, $cmd = 'axdelete_item', $phpfile = '', $toadd = '') {
        $phpfile = ($phpfile == "") ? $_SERVER['PHP_SELF'] : $phpfile;
        return '<a class="btn btn-danger" title="{LBL_DELETE}" href="javascript:void(0);"><i rel="' . $cmd . (($c == TRUE) ? '|confirm' : '|') . '|' . $phpfile . '|' .
            $toadd . '" id="del-' . $id . '" class="fa fa-trash"><!----></i></a>';
    }

    /**
     * kf::gen_del_icon()
     * Generates delete icon link. Ajax based.
     * @param mixed $id
     * @param bool $c
     * @param string $cmd
     * @param string $phpfile
     * @param string $toadd
     * @param string $ctext
     * @return string
     */
    public static function gen_del_icon($id, $c = true, $cmd = 'axdelete_item', $phpfile = '', $toadd = '', $ctext = '{LBL_CONFIRM}') {
        $phpfile = ($phpfile == "") ? $_SERVER['PHP_SELF'] : $phpfile;
        $ctext = ($ctext == "") ? '{LBL_CONFIRM}' : $ctext;
        return '<a class="btn btn-danger deljson" data-cmd="' . $cmd . '" data-ctext="' . $ctext . '" data-epage="' . $_REQUEST['epage'] . '" data-confirm="' . (($c == TRUE) ?
            '1' : '0') . '" data-phpfile="' . $phpfile . '" data-toadd="' . $toadd . '" data-ident="' . $id .
            '" title="{LBL_DELETE}" href="javascript:void(0);"><i class="fa fa-trash"><!----></i></a>';
    }

    /**
     * kf::load_permissions()
     * Loads permission of logged in admin.
     *
     */
    public static function load_permissions() {
        global $kdb;
        unset($_SESSION['RULE']);
        $m_obj = $kdb->query_first("SELECT M.*,G.allowed,G.id AS GID FROM " . TBL_CMS_ADMINS . " M, " . TBL_CMS_ADMINGROUPS . " G WHERE G.id=M.gid AND M.id='" . intval
            ($_SESSION['mitarbeiter']) . "' LIMIT 1");
        $_SESSION['mgroups'] = $m_obj['allowed'];
        $_SESSION['mids'] = explode(';', $m_obj['allowed']);
        #  echoarr($_SESSION['mids']);
        $result = $kdb->query("SELECT * FROM " . TBL_CMS_MENU . " ORDER BY morder");
        while ($row = $kdb->fetch_array_names($result)) {
            $url_arr = parse_url(basename($row['php']));
            $query_params = self::convert_url_query($url_arr['query']);
            if (in_array($row['id'], explode(';', $m_obj['allowed']))) {
                $_SESSION['RULE']['allowed_php'][md5($query_params['epage'])] = $query_params['epage'];
            }
            $core_apps_pages = array(
                'websitemanager.inc',
                'tplmgr.inc',
                'tplvars.inc',
                'gblvars.inc',
                'gbltemplates.inc',
                'flextemp.inc',
                'resource.inc',
                'inlayadmin.inc',
                'welcome.inc');
            foreach ($core_apps_pages as $app_php_inc) {
                $_SESSION['RULE']['allowed_php'][md5($app_php_inc)] = $app_php_inc;
            }

        }
    }

    /**
     * kf::gen_thumbnail()
     * Generates thumbnail for backend use.
     * @param mixed $src
     * @param mixed $width
     * @param mixed $height
     * @param string $type
     * @param bool $usecache
     * @return string
     */
    public static function gen_thumbnail($src, $width, $height, $type = 'resize', $usecache = true) {
        global $GRAPHIC_FUNC;
        if (!file_exists('..' . $src) || !is_file('..' . $src))
            $src = '/images/gal_defekt.jpg';
        $src = str_replace('../', '/', $src);
        $imgsrc = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('..' . $src, $width, $height, 'admin/' . CACHE, $usecache, $type);
        return $imgsrc;
    }

    /**
     * kf::translate_admin()
     * Translage keimeno backend
     * @param mixed $html
     * @return string
     */
    public static function translate_admin($html) {

        if (count($_SESSION) > 0) {
            foreach ($_SESSION as $key => $wert) {
                if (strstr($key, '_')) {
                    list($index, $joker) = explode("_", $key);
                    if ($index == "CNT")
                        $html = fill_temp(strtoupper($key), $wert . '&nbsp;', $html);
                }
            }
        }

        # XML LANG TRANSLATE
        $ALANG_OBJ = new adminlang_class();
        $ALANG_OBJ->translate($html, $_SESSION['GBL_LOCAL_ID'], $_GET['epage']);
        unset($ALANG_OBJ);


        # MOD LANG TRANSLATE
        global $MODULE, $LANGS, $GBL_LANGID;
        $M = new modules_class();
        $M->load_admin_translation($GBL_LANGID, $LANGS, $MODULE);
        if (is_array($M->ADMIN_MOD_TRANS)) {
            foreach ($M->ADMIN_MOD_TRANS as $key => $value) {
                $html = str_replace('{' . $value['joker'] . '}', $value['value'], $html);
            }
        }
        unset($M);
        return $html;
    }

    /**
     * kf::gen_meta_keywords()
     * Generates meta keywords from html code
     * @param mixed $html_base64
     * @param integer $decodeit
     * @param string $delimiter
     * @param integer $langid
     * @return string
     */
    public static function gen_meta_keywords($html, $decodeit = 0, $delimiter = ',', $langid = 1) {
        $html = ($decodeit == 1) ? base64_decode($html) : $html;
        $html = pure_translation($html, $langid);
        $meta_keywords = keimeno_class::gen_meta_keywords($html, $delimiter);
        return $meta_keywords;
    }


    /**
     * kf::remove_doublewhitespace()
     *
     * @param mixed $s
     * @return
     */
    public static function remove_doublewhitespace($s = null) {
        return $ret = preg_replace('/([\s])\1+/', ' ', $s);
    }

    /**
     * kf::remove_whitespace()
     *
     * @param mixed $s
     * @return
     */
    public static function remove_whitespace($s = null) {
        return $ret = preg_replace('/[\s]+/', '', $s);
    }

    /**
     * kf::remove_whitespace_feed()
     *
     * @param mixed $s
     * @return
     */
    public static function remove_whitespace_feed($s = null) {
        return $ret = preg_replace('/[\t\n\r\0\x0B]/', '', $s);
    }

    /**
     * kf::remove_keimeno_vars()
     *
     * @param mixed $html
     * @return
     */
    public static function remove_keimeno_vars($html = null) {
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = trim(strip_tags($html));
        $html = preg_replace('#{LBL_(.*?)}#is', '', $html);
        $html = preg_replace('#{URL_(.*?)}#is', '', $html);
        # remove everything with {...}
        $html = preg_replace_callback("/({\/?)(\w+)([^>]*})/", function ($matches) {
            return ""; }
        , $html);
        return $html;
    }

    /**
     * kf::smart_clean()
     *
     * @param mixed $s
     * @return
     */
    public static function smart_clean($s = null) {
        return trim(self::remove_doublewhitespace(self::remove_whitespace_feed(self::remove_keimeno_vars($s))));
    }

    /**
     * kf::gen_plain_text_content()
     * Generates plain text
     * @param mixed $html
     * @param integer $langid
     * @return string
     */
    public static function gen_plain_text_content($html, $langid = 1) {
        return self::translate_admin(pure_translation(self::smart_clean($html), $langid));
    }

    /**
     * kf::format_meta()
     *
     * @param mixed $description
     * @param integer $length
     * @return
     */
    public static function format_meta($description) {
        $length = self::get_config_value('metadesc_count');
        $description = self::smart_clean($description);
        return trim(substr($description, 0, $length));
    }

    /**
     * kf::thumb()
     * Generates thumbail and returns "src" for thumbail
     * @param mixed $src
     * @param mixed $width
     * @param mixed $height
     * @return string
     */
    public static function thumb($src, $width, $height) {
        $cache_file = './cache/thumb_' . $width . 'x' . $height . '_' . basename($src);
        if (!file_exists($cache_file)) {
            $G = new graphic_class();
            $cache_file = $G->resize_picture_to_size($src, $cache_file, (int)$width, (int)$height);
            unset($G);
        }
        return $cache_file;
    }


    /**
     * kf::gen_admin_sub_btn()
     * Returns admin submit button
     * @param mixed $value
     * @param string $confirm
     * @param string $title
     * @param string $alt
     * @return string
     */
    public static function gen_admin_sub_btn($value, $confirm = '', $title = '', $alt = '') {
        $rand_name = gen_sid(6);
        if ($confirm)
            $confirm = gen_java_confirm($confirm);
        else
            $confirm = '';
        return '<button onClick="toggle_off();" class="btn btn-primary" type="submit" id="' . $rand_name . '" ' . $confirm . ' title="' . $title . '">'.htmlspecialchars($value).'</button>';
        #return '<input onClick="toggle_off();" ' . $confirm . ' title="' . $title . '" type="submit" id="' . $rand_name . '" name="' . $rand_name .            '" class="btn btn-primary" value="' . htmlspecialchars($value) . '">';
    }

    /**
     * kf::make_xls_format()
     * Generates XLS file from SQL query and returns directly to browser
     * @param mixed $fieldlist
     * @param mixed $fieldnames
     * @param mixed $table
     * @param mixed $where
     * @param mixed $orderby
     * @param mixed $filename
     * @return string
     */
    public static function make_xls_format($fieldlist, $fieldnames, $table, $where, $orderby, $filename) {
        global $kdb;
        #header("Content-Type: application/vnd.ms-excel");
        #header("Content-Disposition: inline; filename=\"$filename.xls\"");
        keimeno_class::echo_excel_header($filename, 'xls');
        $fieldnames = explode(",", $fieldnames);
        foreach ($fieldnames as $key => $wert) {
            echo '"' . $wert . '"' . "\t";
        }
        echo "\r\n";
        $result = $kdb->query("SELECT " . $fieldlist . " FROM " . $table . " WHERE " . $where . " ORDER BY " . $orderby);
        $_FIELDS = explode(",", $fieldlist);
        while ($data = $kdb->fetch_array_names($result)) {
            foreach ($_FIELDS as $key) {
                if (strstr($key, ".")) {
                    $parts = explode(".", $key);
                    $key = $parts[1];
                }
                echo '"' . format_string_to_xls($data[$key]) . '"' . "\t";
            }
            echo "\r\n";
        }
        exit;
    }

    /**
     * kf::get_all_columns_from_table()
     * Returns an array of all colums of table
     * @param mixed $Table
     * @return array
     */
    public static function get_all_columns_from_table($Table) {
        global $TCMASTER;
        $dbQuery = mysqli_query($TCMASTER->link_id, "SHOW COLUMNS FROM " . $Table);
        while ($dbRow = mysqli_fetch_assoc($dbQuery)) {
            foreach ($dbRow as $key => $wert)
                $EnumValues[$dbRow["Field"]][strtoupper($key)] = $wert;
        }
        return $EnumValues;
    }

    /**
     * kf::build_module_select()
     * Build app selectbox
     * @param mixed $id
     * @return string
     */
    public static function build_module_select($id) {
        global $MODULE;
        unset($_SESSION['modul_selected']);
        foreach ($MODULE as $key => $value) {
            if ($value['is_content_page'] === TRUE) {
                $olist .= '<option ' . (($value['id'] == $id) ? 'selected' : '') . ' value="' . $value['id'] . '">' . $value['module_name'] . '</option>';
                if ($value['id'] == $id)
                    $_SESSION['modul_selected'] = $MODULE[$key];
            }
        }
        return '<select class="form-control custom-select" name="FORM_TEMPLATE[module_id]">' . $olist . '</select>';
    }

    /**
     * kf::gen_inputtext_field()
     * (desperate function)
     * @param mixed $word
     * @return string
     */
    public static function gen_inputtext_field($word) {
        return ' type="text" class="form-control" value="' . htmlspecialchars($word) . '" size="' . ((strlen($word) > 26) ? strlen($word) + 3 : 26) . '" ';
    }

    /**
     * kf::break_to_newline()
     * Converts all <br> into "nl"
     * @param mixed $text
     * @return string
     */
    public static function break_to_newline($text) {
        $text = preg_replace('/<br\\\\s*?\\/?/i', "\\n", $text);
        return str_replace("<br />", "\n", $text);
    }

    /**
     * kf::convert_url_query()
     * Converts url query string into key value array
     * @param mixed $query
     * @return array
     */
    public static function convert_url_query($query) {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }


    /**
     * kf::validate_module()
     *
     * @param mixed $key
     *
     */
    public static function validate_module($key) {
        global $content, $gbl_config, $smarty, $kdb, $crj_obj;
        if (intval($gbl_config[$key]) == 0) {
            $content .= '<div class="bg-info text-info" style="margin-top:10px;">Dieses Modul k&ouml;nnen Sie in Ihrem Keimeno Tarif nicht nutzen. Wenden Sie sich an hallo@keimeno.de </div>';
            include (CMS_ROOT . 'admin/inc/footer.inc.php');
            exit;
        }
    }

    /**
     * kf::echo_template()
     * Direct output of template to browser. Userfull for ajax request like simple_load()
     * @param mixed $tpl
     *
     */
    public static function echo_template($tpl) {
        self::output('<% include file="' . $tpl . '.tpl" %>');
    }


    /**
     * kf::smarty_html_compile()
     * Compiles html with smarty engine
     * @param mixed $html
     * @return string
     */
    public static function smarty_html_compile($html) {
        global $smarty, $gbl_config, $PERM, $TCMASTER, $DATA, $kdb, $ADMINOBJ;
        include (CMS_ROOT . '/admin/inc/smarty.inc.php');
        $ADMINOBJ->std_smarty_vars();
        $ADMINOBJ->parse_to_smarty();
        return smarty_compile($html, true);
    }

    /**
     * kf::output()
     * Outputs content to browser
     * @param mixed $html
     * @param integer $langid
     *
     */
    public static function output($html, $langid = 0) {
        global $TCMASTER;
        $html = self::smarty_html_compile($html);
        header("Content-type: text/html; charset=UTF-8");
        echo self::translate_admin($html);
        $TCMASTER->hard_exit();
    }

    /**
     * kf::simple_output()
     * (desperate function)
     * @param mixed $html
     *
     */
    public static function simple_output($html) {
        global $smarty;
        include (CMS_ROOT . 'admin/inc/smarty.inc.php');
        header("Content-type: text/html; charset=UTF-8");
        ECHO self::translate_admin(smarty_compile($html));
        die;
    }

    /**
     * kf::get_value_from_config()
     * Get value from config
     * @param mixed $idstr
     * @return string
     */
    public function get_value_from_config($idstr) {
        $arr = $this->db->query_first("SELECT wert FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='" . $idstr . "'");
        return $arr['wert'];
    }

    /**
     * kf::set_value_into_config_by_idstr()
     * Writes a value into config
     * @param mixed $idstr
     * @param mixed $value
     * @return
     */
    public function set_value_into_config_by_idstr($idstr, $value) {
        $this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $value . "' WHERE ID_STR='" . $idstr . "'");
    }

    /**
     * kf::is_superadmin()
     *
     * @return
     */
    public static function is_superadmin() {
        return (int)$_SESSION['mitarbeiter'] == 1 || (int)$_SESSION['mitarbeiter'] == 100;
    }

}
