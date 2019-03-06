<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class language_class extends keimeno_class {

    var $options = array();
    var $lng_loaded = array();

    /**
     * language_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * language_class::set_lng_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_lng_options(&$row) {
        $row['thumb'] = kf::gen_thumbnail('/images/' . $row['bild'], 20, 20, 0);
        $row['thumbtrue'] = ($row['bild'] != "");
        $row['icons'][] = ($row['id'] > 1) ? kf::gen_del_icon($row['id'], true, 'lng_delete') : '';
        $row['icons'][] = kf::gen_edit_icon($row['id']);
        $row['icons'][] = (($this->options['sql_table'] != TBL_CMS_LANG_CUST) ? kf::gen_approve_icon($row['id'], $row['approval'], 'lng_approve') : "");
    }

    /**
     * language_class::load_iso_table()
     * 
     * @return
     */
    function load_iso_table() {
        $handle = fopen(CMS_ROOT . 'admin/tpl/lngpacks/lng_iso_639-1.csv', "r");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $this->iso_list[strtolower($data[1])] = array('lname' => $data[0], 'localid' => strtolower($data[1]));
        }
        fclose($handle);
        $this->iso_list = sort_db_result($this->iso_list, 'lname', SORT_ASC, SORT_STRING);
        return $this->iso_list;
    }

    /**
     * language_class::load_langs()
     * 
     * @return
     */
    function load_langs() {
        $result = $this->db->query("SELECT * FROM " . $this->options['sql_table'] . " ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_lng_options($row);
            $this->languages[] = $row;
            if (!is_dir(CMS_ROOT . 'smarty/templates_c/' . $row['local']))
                mkdir(CMS_ROOT . 'smarty/templates_c/' . $row['local'], 0777);
        }
        $this->load_iso_table();
    }

    /**
     * language_class::load_lang()
     * 
     * @param mixed $id
     * @return
     */
    function load_lang($id) {
        $this->lng_loaded = $this->db->query_first("SELECT * FROM " . $this->options['sql_table'] . " WHERE id=" . (int)$id . " LIMIT 1");
        $this->set_lng_options($this->lng_loaded);
        return $this->lng_loaded;
    }

    /**
     * language_class::cmd_lng_savelang()
     * 
     * @return
     */
    function cmd_lng_savelang() {
        $id = (int)$_POST['id'];
        $new = ($id == 0);
        $basedon = (int)$_POST['basedon'];
        $FORM = $_POST['FORM'];
        $FORM['local'] = strtolower(trim(strip_tags($_POST['FORM']['local'])));
        $FORM['post_lang'] = trim(strip_tags($_POST['FORM']['post_lang']));
        foreach ($_POST['FORM'] as $key => $wert) {
            if (strlen($wert) == 0) {
                $this->msge('{LBL_PLEASEFILLOUT}...');
                $this->ej();
            }
        }
        $LN = $this->db->query_first("SELECT * FROM " . $this->options['sql_table'] . " WHERE id=" . $basedon);
        if ($id == 0) {
            if (get_data_count($this->options['sql_table'], 'id', "local='" . trim($_POST['FORM']['local']) . "'") > 0) {
                $this->add_err('Local ID already exists.');
            }
            if ($this->get_error_count() > 0) {
                $this->ej();
            }
            $FORM['langarray'] = $this->db->real_escape_string($LN['langarray']);
            $id = insert_table($this->options['sql_table'], $FORM);
            if ($this->options['type'] == 'no') {
                $FORM['id'] = $id;
                $LN = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANG_CUST . " WHERE id=" . $basedon);
                $FORM['langarray'] = $this->db->real_escape_string($LN['langarray']);
                insert_table(TBL_CMS_LANG_CUST, $FORM);
            }
            if ($this->options['type'] == 'no') {
                exec_evt('OnAddLanguage', array('basedon' => $basedon, 'id' => $id));
            }
        }
        else {
            update_table($this->options['sql_table'], 'id', $id, $FORM);
            if ($this->options['type'] == 'no') {
                update_table(TBL_CMS_LANG_CUST, 'id', $id, $FORM);
            }
        }
        // UPLOAD FLAG
        if ($_FILES['attfile']['name'] != "") {
            $ext = strtolower(strrchr($_FILES['attfile']['name'], '.'));
            $f_name = CMS_ROOT . 'images/lng_' . $FORM['local'] . $ext;
            if (!validate_upload_file($_FILES['attfile'], TRUE)) {
                $this->mage($_SESSION['upload_msge']);
                $this->ej();
            }
            if (!move_uploaded_file($_FILES['attfile']['tmp_name'], $f_name)) {
                $this->mage(basename($f_name));
                $this->ej();

            }
            chmod($f_name, 0755);
            $this->db->query("UPDATE " . $this->options['sql_table'] . " SET bild='" . basename($f_name) . "' WHERE id='" . $id . "' LIMIT 1");
            if ($this->options['type'] == 'no') {
                $this->db->query("UPDATE " . TBL_CMS_LANG_CUST . " SET bild='" . basename($f_name) . "' WHERE id='" . $id . "' LIMIT 1");
            }
        }
        $this->load_lang($id);
        $this->ej('set_lng_id', $id . ',"' . $this->lng_loaded['bild'] . '"');
    }

    /**
     * language_class::cmd_lng_delete()
     * 
     * @return
     */
    function cmd_lng_delete() {
        global $MODULE;
        if ((int)$_GET['ident'] > 1) {
            $id = (int)$_GET['ident'];
            $lang_obj = $this->db->query_first("SELECT * FROM " . $this->options['sql_table'] . " WHERE id=" . $id . " AND id>1");
            delete_file(PICS_WEB_ADMIN . $lang_obj['bild']);
            $this->db->query("DELETE FROM " . $this->options['sql_table'] . " WHERE id=" . $id . " AND id>1 LIMIT 1");
            if ($this->options['type'] == 'no') {
                $this->db->query("DELETE FROM " . TBL_CMS_LANG_CUST . " WHERE id=" . $id . " AND id>1 LIMIT 1");
                exec_evt('OnDeleteLanguage', array('id' => $id));
            }
            $this->ej();
        }
        else {
            $this->msge('{LBL_NOTPOSSIBLE}');
            $this->ej();
        }
    }

    /**
     * language_class::cmd_lng_approve()
     * 
     * @return
     */
    function cmd_lng_approve() {
        $this->set_approve($_GET['value'], $_GET['ident']);
        $this->hard_exit();
    }

    /**
     * language_class::set_approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function set_approve($value, $id) {
        $this->db->query("UPDATE " . $this->options['sql_table'] . " SET approval='" . (int)$value . "' WHERE id=" . (int)$id . " LIMIT 1");
        if ($this->options['sql_table'] == TBL_CMS_LANG)
            $this->db->query("UPDATE " . TBL_CMS_LANG_CUST . " SET approval='" . (int)$value . "' WHERE id=" . (int)$id . " LIMIT 1");
        if ($value == 1) {
            include_once (CMS_ROOT . 'admin/inc/update.class.php');
            $upt_obj = new upt_class();
            $upt_obj->db_zugriff = $this->db;
            $upt_obj->rewriteSmartyTPL();
        }
    }

    /**
     * language_class::cmd_lng_savetable()
     * 
     * @return
     */
    function cmd_lng_savetable() {
        $FORM = $_POST['FORM'];
        $FORM = $this->sort_multi_array($FORM, 's_order', SORT_ASC, SORT_NUMERIC);
        foreach ($FORM as $key => $row) {
            $k += 10;
            $id = $row['id'];
            $row['s_order'] = $k;
            unset($row['id']);
            update_table($this->options['sql_table'], 'id', $id, $row);
        }
        $this->ej();
    }


    /**
     * language_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->lng_obj = array(
            'iso_list' => $this->iso_list,
            'languages' => $this->languages,
            'options' => $this->options,
            'lng_loaded' => $this->lng_loaded);
        $this->smarty->assign('lng_obj', $this->lng_obj);
    }

    /**
     * language_class::get_first_valid_lang()
     * 
     * @return
     */
    function get_first_valid_lang() {
        $RES = 0;
        if (isset($_SESSION['admin_obj']['lang_id_matrix'])) {
            if (in_array($this->gblconfig->std_lang_id, $_SESSION['admin_obj']['lang_id_matrix'])) {
                return $this->gblconfig->std_lang_id;
            }
            $R = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
            while ($row = $this->db->fetch_array_names($R)) {
                if (in_array($row['id'], $_SESSION['admin_obj']['lang_id_matrix'])) {
                    $RES = (int)$row['id'];
                    break;
                }
            }
        }
        return (int)$RES;
    }

    /**
     * language_class::init_uselang()
     * 
     * @return
     */
    function init_uselang() {
        $_GET['uselang'] = (!isset($_REQUEST['uselang'])) ? $this->get_first_valid_lang() : (int)$_REQUEST['uselang'];
    }

    /**
     * language_class::build_lang_select()
     * 
     * @param string $toadd
     * @return
     */
    function build_lang_select($toadd = "") {
        global $TCMASTER;
        $this->init_uselang();
        $TCMASTER->add_access_err('language', false);
        $ret = "";
        $this->lang_arr = array();
        if (is_array($_SESSION['admin_obj']['lang_id_matrix']) && count($_SESSION['admin_obj']['lang_id_matrix']) > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
            while ($row = $this->db->fetch_array_names($result)) {
                if (in_array($row['id'], $_SESSION['admin_obj']['lang_id_matrix'])) {
                    if ($_GET['uselang'] == $row['id'])
                        $sellang = $row;
                    $mod = $this->parse_query($_SERVER['PHP_SELF'] . '?' . $toadd);
                    $query = array_merge($mod, array(
                        'epage' => ((isset($_GET['epage'])) ? $_GET['epage'] : ""),
                        'section' => ((isset($_GET['section'])) ? $_GET['section'] : ""),
                        'uselang' => $row['id'],
                        'id' => ((isset($_GET['id'])) ? $_GET['id'] : ""),
                        'aktion' => ((isset($_GET['aktion'])) ? $_GET['aktion'] : ""),
                        'tl' => ((isset($_GET['tl'])) ? $_GET['tl'] : "")));
                    $q = array();
                    foreach ($query as $key => $value) {
                        if ($value != "" && $key != "") {
                            $q[$key] = $value;
                        }
                    }
                    unset($query);
                    $url = $_SERVER['PHP_SELF'] . '?' . http_build_query($q);
                    $ret .= '<option ' . ((isset($_GET['uselang']) && $_GET['uselang'] == $row['id']) ? 'selected' : '') . ' value="' . $url . '">' . $row['post_lang'] .
                        '</option>';
                    $this->lang_arr[] = array(
                        'url' => $url,
                        'uselang' => $_GET['uselang'],
                        'label' => $row['post_lang'],
                        'id' => $row['id']);
                    $TCMASTER->add_access_err('language', true);
                }
            }
            #   $this->add_access_err('language', ($this->db->num_rows($result) > 0));

            # $img = "";
            # if (!empty($sellang['bild']))
            #     $img = '<img title="' . $sellang['post_lang'] . '" alt="' . $sellang['post_lang'] . '" src="' .  kf::gen_thumbnail('/images/' . $sellang['bild'], 20, 20) .
            #         '" >';
            return '<select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">' . $ret . '</select>';
        }
        else {
            $TCMASTER->add_access_err('language', false);
            return '';
        }
    }

    /**
     * language_class::build_lang_select_smarty()
     * 
     * @param mixed $langid
     * @param string $toadd
     * @return
     */
    function build_lang_select_smarty($langid, $toadd = "") {
        $this->init_uselang();
        if (is_array($_SESSION['admin_obj']['lang_id_matrix']) && count($_SESSION['admin_obj']['lang_id_matrix']) > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
            while ($row = $this->db->fetch_array_names($result)) {
                if (in_array($row['id'], $_SESSION['admin_obj']['lang_id_matrix'])) {
                    $mod = $this->parse_query($_SERVER['PHP_SELF'] . '?' . $toadd);
                    $query = array_merge($mod, array(
                        'epage' => $_GET['epage'],
                        'uselang' => $row['id'],
                        'id' => $_GET['id'],
                        'aktion' => $_GET['aktion'],
                        'tl' => $_GET['tl']));
                    $row['url'] = $this->modify_url($_SERVER['PHP_SELF'], $query);
                    $row['selected'] = (($langid == $row['id']) ? 'selected' : '');
                    if (!empty($row['bild']))
                        $row['icon'] = '<img title="' . $row['post_lang'] . '" alt="' . $row['post_lang'] . '" src="' . kf::gen_thumbnail('/images/' . $row['bild'], 20, 20, 0) . '" >';
                    $lang_arr[$row['id']] = $row;
                }
            }

            return $lang_arr;
        }
    }

}
