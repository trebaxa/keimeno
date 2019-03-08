<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class emailman_class extends keimeno_class {

    var $EM = array();

    /**
     * emailman_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * emailman_class::cmd_utf8_convert()
     * 
     * @return
     */
    function cmd_utf8_convert() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_MAILTEMP_CONTENT . " WHERE converted=0");
        while ($row = $this->db->fetch_array_names($result)) {
            $A['content'] = $this->db->real_escape_string(base64_decode($row['content']));
            $A['email_subject'] = $this->db->real_escape_string(base64_decode($row['email_subject']));
            $A['converted'] = 1;
            update_table(TBL_CMS_MAILTEMP_CONTENT, 'id', $row['id'], $A);
        }
    }

    /**
     * emailman_class::load_mails_tpls()
     * 
     * @return
     */
    function load_mails_tpls() {
        $this->load_email_list();
        $mit_emails = "";
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE email<>'' AND mi_email_copy=1 AND approval=1 AND id<>100");
        while ($row = $this->db->fetch_array_names($result)) {
            $mit_emails .= (($mit_emails != "") ? ", " : '') . $row['email'];
        }
        if (!isset($_SESSION['emtpl']) || !isset($_SESSION['emtpl']['mod'])) {
            $_SESSION['emtpl'] = array('mod' => "");
        }
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_MAILTEMP . " WHERE module_id='" . $_SESSION['emtpl']['mod'] . "' ORDER BY title");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['mit_emails'] = $mit_emails;
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['approval'], 'approve_eml');
            $row['icons'][] = kf::gen_edit_icon($row['id']);
            $row['icons'][] = (($row['admin'] == 1) ? '' : kf::gen_del_icon($row['id'], true, 'axdelete_item'));
            $this->EM['elist'][] = $row;
        }
    }

    /**
     * emailman_class::load_email_tempaltes_by_app()
     * 
     * @param mixed $app
     * @return
     */
    function load_email_tempaltes_by_app($app) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_MAILTEMP . " WHERE module_id='" . $app . "' ORDER BY title");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * emailman_class::load_email_list()
     * 
     * @return
     */
    function load_email_list() {
        foreach ($this->gbl_config as $key => $value) {
            if (strstr($key, 'email') && validate_email_input($value)) {
                $this->EM['emails'][] = $value;
            }
            if (strstr($key, 'email')) {
                $list = explode(';', $value);
                if (is_array($list) && count($list) > 0) {
                    foreach ($list as $email) {
                        if (validate_email_input($email))
                            $this->EM['emails'][] = $email;
                    }
                }
            }
        }
        $this->EM['emails'] = array_unique($this->EM['emails']);
    }

    /**
     * emailman_class::load_recipient_matrix()
     * 
     * @param mixed $email_id
     * @return
     */
    function load_recipient_matrix($email_id) {
        $this->EM['selected_mid'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_MAIL_RECIP_MATRIX . " WHERE rm_emid=" . (int)$email_id);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->EM['selected_mid'][] = $row['rm_mid'];
        }
    }

    /**
     * emailman_class::cmd_save_recipient_matrix()
     * 
     * @return
     */
    function cmd_save_recipient_matrix() {
        $this->db->query("DELETE FROM " . TBL_CMS_MAIL_RECIP_MATRIX . " WHERE rm_emid=" . (int)$_POST['id']);
        $M = (array )$_POST['FORM'];
        foreach ($M as $rm_mid => $value) {
            if ($value == 1)
                $this->db->query("INSERT INTO " . TBL_CMS_MAIL_RECIP_MATRIX . " SET rm_emid=" . (int)$_POST['id'] . ", rm_mid=" . $rm_mid);
        }
        $this->ej();
    }

    /**
     * emailman_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        global $LNGOBJ;
        $uselang = ($_GET['uselang'] == 0) ? $this->gbl_config['std_lang_admin_id'] : (int)$_GET['uselang'];
        $_SESSION['CNT_TABBEDLANG'] = $LNGOBJ->build_lang_select();
        $template = $this->db->query_first("SELECT T.* FROM " . TBL_CMS_MAILTEMP . " T WHERE T.id='" . (int)$_GET['id'] . "' LIMIT 1");
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_MAILTEMP_CONTENT . " 
            WHERE email_id='" . (int)$_GET['id'] . "' 
            AND lang_id='" . $uselang . "' 
            LIMIT 1");
        $this->EM['mailtpl'] = $FORM;
        $this->EM['mailtemplate'] = $template;

        $this->load_recipient_matrix($_GET['id']);

        $EMP = new employee_class();
        $EMP->load_employees();
        unset($EMP);
    }


    /**
     * emailman_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if (isset($_SESSION['emtpl']))
            $this->EM['sess'] = $_SESSION['emtpl'];
        $this->EM['legende'] = "";
        foreach ($this->gbl_config as $key => $value) {
            #$this->EM['legende'] .= '!!TMPLDB_FM_' . strtoupper($key) . '!!<br>';
            if (strstr($key, 'adr_')) {
                $this->EM['legende'] .= '<tr><td><code>' . htmlspecialchars('<%$gbl_config.' . $key . '%>') . '</code></td><td>' . $value . '</td></tr>';
            }
        }
        $this->smarty->assign('EM', $this->EM);
    }

    /**
     * emailman_class::load_mod_filter()
     * 
     * @return
     */
    function load_mod_filter() {
        include_once (CMS_ROOT . 'admin/inc/modulman.class.php');
        $MODMAN = new moduleman_class();
        $this->EM['modlist'] = $MODMAN->load_mods();
        foreach ($this->EM['modlist'] as $key => $row) {
            $this->EM['modlist'][$key]['hastpls'] = get_data_count(TBL_CMS_MAILTEMP, '*', "module_id='" . $row['settings']['id'] . "'") > 0;
            if ($row['settings']['active'] == 'false') {
                unset($this->EM['modlist'][$key]);
            }

        }
        if (isset($this->TCR->GET['mod']) && $this->TCR->GET['mod'] != "") {
            $_SESSION['emtpl']['mod'] = strtolower($this->TCR->GET['mod']);
        }
        if (isset($this->TCR->GET['mod']) && $this->TCR->GET['mod'] == -1) {
            $_SESSION['emtpl']['mod'] = "";
        }
    }

    /**
     * emailman_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        $this->db->query("DELETE FROM " . TBL_CMS_MAILTEMP . " WHERE id=" . (int)$this->TCR->GET['ident'] . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_MAILTEMP_CONTENT . " WHERE email_id=" . (int)$this->TCR->GET['ident'] . " LIMIT 1");
        $this->ej();
    }

    /**
     * emailman_class::cmd_approve_eml()
     * 
     * @return
     */
    function cmd_approve_eml() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_MAILTEMP . " SET approval='" . $_GET['value'] . "' WHERE id='" . $id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * emailman_class::cmd_save_mail_template()
     * 
     * @return
     */
    function cmd_save_mail_template() {
        $FORM = (array )$_POST['FORM'];
        $FORM_TPL['module_id'] = $_POST['module_id'];
        update_table(TBL_CMS_MAILTEMP, 'id', $_POST['email_id'], $FORM_TPL);
        $T = $this->db->query_first("SELECT id FROM " . TBL_CMS_MAILTEMP_CONTENT . " WHERE lang_id=" . $_POST['lang_id'] . " AND email_id=" . (int)$_POST['email_id']);
        if ($T['id'] > 0) {
            update_table(TBL_CMS_MAILTEMP_CONTENT, 'id', $T['id'], $FORM);
        }
        else {
            $FORM['email_id'] = $_POST['email_id'];
            $FORM['lang_id'] = $_POST['lang_id'];
            insert_table(TBL_CMS_MAILTEMP_CONTENT, $FORM);
        }

        $this->ej();
    }

    /**
     * emailman_class::cmd_add()
     * 
     * @return
     */
    function cmd_add() {
        $FORM = (array )$_POST['FORM'];
        $id = insert_table(TBL_CMS_MAILTEMP, $FORM);
        $this->TCR->redirect("epage=" . $_POST['epage'] . "&id=" . $id . "&cmd=edit");
        $this->hard_exit();
    }

    /**
     * emailman_class::cmd_save_etpl_tab()
     * 
     * @return
     */
    function cmd_save_etpl_tab() {
        $SQL_ARR = array();
        if (count($_POST['FORM']) > 0) {
            foreach ($_POST['FORM'] as $key => $FORM_SET) {
                if (count($FORM_SET) > 0) {
                    foreach ($FORM_SET as $id => $value) {
                        $SQL_ARR[$id][$key] = $value;
                    }
                }
            }
        }

        if (count($SQL_ARR) > 0) {
            foreach ($SQL_ARR as $id => $SQL_SET) {
                $SQL_SET['add_adress'] = (int)$SQL_SET['add_adress'];
                $SQL_SET['admin_copy'] = (int)$SQL_SET['admin_copy'];
                $SQL_SET['mit_in_copy'] = (int)$SQL_SET['mit_in_copy'];
                update_table(TBL_CMS_MAILTEMP, 'id', $id, $SQL_SET);
            }
        }
        $this->ej();
    }
}

?>