<?php

/**
 * @package    sellform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


DEFINE('TBL_CMS_SELFORMS', TBL_CMS_PREFIX . 'sellforms');
DEFINE('TBL_CMS_SELFORMS_MATRIX', TBL_CMS_PREFIX . 'sellforms_fp');

class sellform_class extends modules_class {

    var $SELLFORM = array();
    var $langid = 1;
    var $user_object = array();
    var $gbl_config_shop = array();
    var $val_not_empty = array(
        'strasse',
        'nachname',
        'vorname',
        'ort',
        'plz',
        'hausnr',
        'email',
        'passwort');

    /**
     * sellform_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = new graphic_class();
        $this->SELLFORM = array();
        if ($this->gbl_config['sf_rediapi'] != "" && class_exists('rediapi_class')) {
            $RC = new rediapi_class();
            $R = $RC->get_keypair($this->gbl_config['sf_rediapi']);
            $this->ws_config = new ws_clientconfig_class();
            $this->ws_config->set_api_id($R['r_apiid']);
            $this->ws_config->set_api_key($R['r_apikey']);
            $this->ws_config->set_location($R['r_serverurl']);
            $this->client = new ws_client();
            $this->client->connect($this->ws_config);
            $this->SELLFORM['invalidredi'] = $R['r_apiid'] == "";
        }
    }

    /**
     * sellform_class::set_shop_config()
     * 
     * @return
     */
    function set_shop_config() {
        $this->gbl_config_shop = $this->client->call('get_shopconfig', array());
    }

    /**
     * sellform_class::load_forms()
     * 
     * @return
     */
    function load_forms() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_SELFORMS . " ORDER BY fo_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_admin_opt($row);
            $this->SELLFORM['sellforms'][] = $row;
        }
    }

    /**
     * sellform_class::cmd_axapprove_sform_item()
     * 
     * @return
     */
    function cmd_axapprove_sform_item() {
        $parts = explode('-', $this->TCR->REQUEST['id']);
        $id = $parts[1];
        $this->set_approve($this->TCR->REQUEST['value'], $id);
        $this->hard_exit();
    }

    /**
     * sellform_class::set_approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function set_approve($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_SELFORMS . " SET fo_approved='" . (int)$value . "' WHERE id=" . (int)$id . " LIMIT 1");
    }

    /**
     * sellform_class::cmd_sform_delete()
     * 
     * @return
     */
    function cmd_sform_delete() {
        $id = (int)$this->TCR->GET['id'];
        $this->TCR->set_just_turn_back(true);
        $this->db->query("DELETE FROM " . TBL_CMS_SELFORMS . " WHERE id=" . $id . " LIMIT 1");
        $this->TCR->add_msg('{LBL_DELETED}');
    }

    /**
     * sellform_class::set_admin_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_admin_opt($row) {
        if (ISADMIN == 1) {
            $row['icons'][] = kf::gen_edit_icon($row['id'], '&section=editor', 'edit');
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['fo_approved'], 'axapprove_sform_item');
            $row['icons'][] = kf::gen_del_icon_reload($row['id'], 'sform_delete', '{LBLA_CONFIRM}', '');
        }
        $row['tpl_inlay'] = '{TMPL_SELLFORM_' . $row['id'] . '}';
        return $row;
    }

    /**
     * sellform_class::cmd_save_sform()
     * 
     * @return
     */
    function cmd_save_sform() {
        $id = (int)$this->TCR->POST['id'];
        $FORM = $this->TCR->POST['FORM'];
        if ($id == 0) {
            insert_table(TBL_CMS_SELFORMS, $FORM);
        }
        else {
            update_table(TBL_CMS_SELFORMS, 'id', $id, $FORM);
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * sellform_class::cmd_pid_delete()
     * 
     * @return
     */
    function cmd_pid_delete() {
        list($tmp, $fm_formid, $fm_pid) = explode('-', $this->TCR->GET['id']);
        $this->db->query("DELETE FROM " . TBL_CMS_SELFORMS_MATRIX . " WHERE fm_formid=" . (int)$fm_formid . " AND fm_pid=" . (int)$fm_pid);
        $this->hard_exit();
    }

    /**
     * sellform_class::set_admin_pro_opt()
     * 
     * @param mixed $row
     * @param mixed $id
     * @return
     */
    function set_admin_pro_opt($row, $id) {
        if (ISADMIN == 1) {
            $row['icons'][] = kf::gen_del_icon_ajax($id . '-' . $row['pid'], false, 'pid_delete');
        }
        $row['pname'] = ($row['pname'] == "") ? "deleted" : $row['pname'];
        $row['valid'] = ($row['pname'] == "") ? false : true;
        return $row;
    }

    /**
     * sellform_class::load_form()
     * 
     * @param mixed $id
     * @return
     */
    function load_form($id) {
        $id = (int)$id;
        if ($id > 0) {
            $SF = $this->db->query_first("SELECT * FROM " . TBL_CMS_SELFORMS . " WHERE id=" . $id);
        }
        $order = $tarife = array();
        $SF = $this->set_admin_opt($SF);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_SELFORMS_MATRIX . " M WHERE fm_formid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $pids[] = $row['fm_pid'];
            $order[$row['fm_pid']] = $row['fm_order'];
            $tarife[$row['fm_pid']] = $row['fm_tarifid'];
        }
        $products = $this->client->call('get_products_by_pids', array('pids' => $pids));
        foreach ($products as $key => $pro) {
            $pro['fm_order'] = $order[$pro['pid']];
            $pro['fm_tarifid'] = $tarife[$pro['pid']];
            $SF['products'][] = $this->set_admin_pro_opt($pro, $id);
        }
        $SF['products'] = $this->sort_multi_array($SF['products'], 'fm_order', SORT_ASC, SORT_NUMERIC, 'pname');
        $SF['abo_traife'] = $this->client->call('load_abo_tarife', array());

        return (array )$SF;
    }

    /**
     * sellform_class::cmd_save_order()
     * 
     * @return
     */
    function cmd_save_order() {
        $FORM = (array )$_POST['FORM'];
        foreach ($FORM as $pid => $row) {
            $this->db->query("UPDATE " . TBL_CMS_SELFORMS_MATRIX . " SET fm_order=" . (int)$row['fm_order'] . ",fm_tarifid=" . (int)$row['fm_tarifid'] . " WHERE fm_pid=" .
                $pid . " AND fm_formid=" . $_POST['id']);
        }
        $this->ej();
    }

    /**
     * sellform_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $id = (int)$this->TCR->GET['id'];
        $SF = $this->load_form($id);
        $this->SELLFORM['sform'] = (array )$SF;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='sellform' ORDER BY description");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->SELLFORM['templates'][] = $row;
        }
    }

    /**
     * sellform_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('SELLFORM') != NULL) {
            $this->SELLFORM = array_merge($this->smarty->getTemplateVars('SELLFORM'), $this->SELLFORM);
            $this->smarty->clearAssign('SELLFORM');
        }
        $this->SELLFORM['rediinstalled'] = TBL_CMS_REDIAPI != 'TBL_CMS_REDIAPI';
        $this->smarty->assign('SELLFORM', $this->SELLFORM);
    }

    /**
     * sellform_class::cmd_searchproducts()
     * 
     * @return
     */
    function cmd_searchproducts() {
        $this->SELLFORM['searchwords'] = $this->client->call('search_product', array('word' => $this->TCR->GET['word']));
        $this->parse_to_smarty();
        ECHORESULTCOMPILEDFE('<% include file="sellform.search.tpl" %>');
    }

    /**
     * sellform_class::cmd_add_product()
     * 
     * @return
     */
    function cmd_add_product() {
        $this->db->query("DELETE FROM " . TBL_CMS_SELFORMS_MATRIX . " WHERE fm_pid=" . $this->TCR->GET['pid'] . " AND fm_formid=" . $this->TCR->GET['id']);
        $FORM = array(
            'fm_pid' => $this->TCR->GET['pid'],
            'fm_formid' => $this->TCR->GET['id'],
            );
        insert_table(TBL_CMS_SELFORMS_MATRIX, $FORM);
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * sellform_class::set_zw_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_zw_opt($row) {
        $row['zw_logo'] = $this->grabimage($row['api_image']);
        $row[thumb] = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb($row['zw_logo'], $this->gbl_config['sf_zwthumb_width'], $this->gbl_config['sf_zwthumb_height'],
            CACHE, TRUE, 'resize');

        if ($this->gbl_config['sf_useorgimg'] == 1) {
            $row[thumb] = $this->gbl_config['shop_root'] . 'images/' . $row['zw_logo'];
        }
        return $row;
    }

    /**
     * sellform_class::load_zw()
     * 
     * @return
     */
    function load_zw() {
        if (TBL_CMS_REDIAPI == 'TBL_CMS_REDIAPI')
            return;
        if ($this->gbl_config['sf_rediapi'] == 0 || get_data_count(TBL_CMS_REDIAPI, '*', "id=" . $this->gbl_config['sf_rediapi']) == 0)
            return;


        $this->SELLFORM['zahlweisen'] = $this->client->call('get_payment_methods', array());
        foreach ((array )$this->SELLFORM['zahlweisen'] as $key => $zw) {
            $this->SELLFORM['zahlweisen'][$key] = $this->set_zw_opt($zw);
        }
        $this->SELLFORM['zahlweisen'] = $this->sort_multi_array($this->SELLFORM['zahlweisen'], 'admin_label', SORT_ASC, SORT_REGULAR);
        return $this->SELLFORM['zahlweisen'];
    }

    /**
     * sellform_class::grabimage()
     * 
     * @param mixed $url
     * @return
     */
    function grabimage($url) {
        $fname = CMS_ROOT . 'cache/' . basename($url);
        if ((strstr($url, '.jpg') || strstr($url, '.png') || strstr($url, '.gif')) && !file_exists($fname)) {
            file_put_contents($fname, $this->curl_get_data($url));
        }
        return $fname;
    }

    /**
     * sellform_class::set_pro_opt_fe()
     * 
     * @param mixed $row
     * @return
     */
    function set_pro_opt_fe($row) {
        $row['bild'] = $this->grabimage($row['api_image']);

        #$row['bild'] = ($row['bild'] == "") ? '.' . $this->gbl_config['shop_root'] . 'pro_bilder/no_pic.jpg' : '.' . $this->gbl_config['shop_root'] . 'pro_bilder/' . $row['bild'];
        if (!file_exists($row['bild']))
            $row['bild'] = '.' . $this->gbl_config['shop_root'] . 'pro_bilder/no_pic.jpg';
        $row[thumb] = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb($row['bild'], $this->gbl_config['sf_prothumb_width'], $this->gbl_config['sf_prothumb_height'], CACHE, TRUE,
            'resize');
        $row['thumb_middle'] = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb($row['bild'], $this->gbl_config['sf_pro_middlethumb_width'], $this->gbl_config['sf_pro_middlethumb_height'],
            CACHE, TRUE, 'resize');
        if ($this->gbl_config['sf_useorgimg'] == 1) {
            $row[thumb] = str_replace(CMS_ROOT, '', $row['bild']);
        }

        $row['vkbr_eur'] = format_to_currency($row['vk']);
        $row['vkbr_num'] = currency_format(($row['vk'] * 1), 2, true, false);
        return $row;

    }


    /**
     * sellform_class::build_anrede()
     * 
     * @param mixed $FORMCUST
     * @return
     */
    function build_anrede($FORMCUST) {
        global $anrede_arr;
        foreach ($anrede_arr as $key => $value) {
            $anrede_arr[$key] = pure_translation($value, $this->langid);
        }
        asort($anrede_arr);
        foreach ($anrede_arr as $key => $value)
            $anrede_select .= '<option ' . (($FORMCUST['geschlecht'] == $key) ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
        return array('anrede_select' => $anrede_select, 'anrede_arr' => $anrede_arr);
    }


    /**
     * sellform_class::buildOptNum()
     * 
     * @param mixed $from
     * @param mixed $to
     * @param mixed $what2select
     * @return
     */
    function buildOptNum($from, $to, $what2select) {
        for ($i = $from; $i <= $to; $i++) {
            $sel = "";
            $fi = $i;
            if (strlen($i) == 1)
                $fi = '0' . $i;
            if ($what2select == $fi)
                $sel = "selected";
            $result .= "<option value=\"" . $fi . "\" $sel>" . $fi . "</option>";
        }
        return $result;
    }


    /**
     * sellform_class::load_form_fe()
     * 
     * @param integer $id
     * @param integer $langid
     * @return
     */
    function load_form_fe($id = 1, $langid = 1) {
        $id = (int)$id;
        $order = $tarife = array();
        if ($id > 0) {
            $SF = $this->db->query_first("SELECT *,id AS FORMID FROM " . TBL_CMS_SELFORMS . " WHERE id=" . $id);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_SELFORMS_MATRIX . " M WHERE fm_formid=" . $id);
            while ($row = $this->db->fetch_array_names($result)) {
                $pids[] = $row['fm_pid'];
                $order[$row['fm_pid']] = $row['fm_order'];
                $tarife[$row['fm_pid']] = $row['fm_tarifid'];
            }


            $abo_tarife = $this->client->call('load_abo_tarife', array());
            $products = $this->client->call('get_products_by_pids', array('pids' => $pids));
            foreach ($products as $key => $row) {
                $row['fm_order'] = $order[$row['pid']];
                $row['fm_tarifid'] = $tarife[$row['pid']];
                $row['abo_tarif'] = $abo_tarife[$row['fm_tarifid']];
                $row['content'] = $this->client->call('get_product_description', array('pid' => $row['pid'], 'langid' => $langid));
                $row['content']['plaintext'] = strip_tags($row['content']['content']);
                $SF['products'][] = $this->set_pro_opt_fe($row);
            }
            $SF['products'] = $this->sort_multi_array($SF['products'], 'fm_order', SORT_ASC, SORT_NUMERIC, 'pname');
            $SF['template'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$SF['fo_tpl']);
            $SF['zahlweisen'] = $this->load_zw();
        }
        $this->SELLFORM['SF'] = (array )$SF;
        return (array )$SF;
    }


    /**
     * sellform_class::cmd_load_product()
     * 
     * @return
     */
    function cmd_load_product() {
        global $user_object;
        $this->load_form_fe((int)$_REQUEST['formid']);
        $pid = (int)$this->TCR->REQUEST['pid'];
        if ($pid > 0) {
            $PRODUCT = $this->client->call('load_product', array('pid' => $pid));
            $PRODUCT = $this->set_pro_opt_fe($PRODUCT);
            $PRODUCT['vk_pro_unit'] = $PRODUCT['vk'] / $PRODUCT['vpe_menge'];
            $PRODUCT['content'] = $this->client->call('get_product_description', array('pid' => $PRODUCT['pid'], 'langid' => (int)$this->langid));
            $PRODUCT['content']['plaintext'] = strip_tags($PRODUCT['content']['content']);
        }
        $this->SELLFORM['PRODUCT'] = (array )$PRODUCT;
        foreach ($this->SELLFORM['SF']['products'] as $key => $row) {
            if ($row['pid'] == $pid) {
                $tarif = $row['abo_tarif'];
                break;
            }
        }
        if ($this->TCR->REQUEST['FORMCUST']['land'] == 0)
            $this->TCR->REQUEST['FORMCUST']['land'] = 1;
        $formcust = (count($this->TCR->REQUEST['FORMCUST']) < 2) ? $user_object : $this->TCR->REQUEST['FORMCUST'];
        $PAY_OBJ = array(
            'ssl_server' => SSLSERVER,
            'page' => $page,
            'sid_id' => session_id(),
            'form' => $this->TCR->REQUEST['FORM'],
            'formcust' => $formcust,
            'tarif' => $tarif,
            'months_list' => $this->buildOptNum(1, 12, $_POST['gueltig_mon']),
            'year_list' => $this->buildOptNum(date("Y"), date("Y") + 6, $_POST['gueltig_jahr']),
            'totalsum' => $totalsum,
            'land_select' => build_land_selectbox($this->TCR->REQUEST['FORMCUST']['land']),
            'anredeform' => $this->build_anrede($this->TCR->REQUEST['FORMCUST']));
        $this->SELLFORM['PAY_OBJ'] = (array )$PAY_OBJ;
    }

    /**
     * sellform_class::cmd_sforder()
     * 
     * @return
     */
    function cmd_sforder() {
        $this->cmd_load_product();
        $FORM = (array )$this->TCR->REQUEST['FORM'];
        $FORMCUST = (array )$this->TCR->REQUEST['FORMCUST'];
        $this->SELLFORM['PAY_OBJ']['form'] = (array )$this->SELLFORM['PAY_OBJ']['form'];
        $this->SELLFORM['PAY_OBJ']['form'] = array_merge($this->SELLFORM['PAY_OBJ']['form'], $FORM);

        if ((int)$this->TCR->POST['novalidation'] == 0) {
            # validate data
            if ($_POST['agb'] != 1) {
                $this->TCR->set_fault_form(true);
                $kregform_err['agb'] = 'x';
            }
            if ($_POST['wr'] != 1) {
                $this->TCR->set_fault_form(true);
                $kregform_err['wr'] = 'x';
            }
            if ($FORM['zahlweise'] == 2 || $FORM['zahlweise'] == 9) {
                if ($FORM['kinhaber'] == "") {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['kinhaber'] = 'x';
                }
                if ($FORM['knummer'] == "") {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['knummer'] = 'x';
                }

            }

            if ($FORM['zahlweise'] == 2) {
                if ($FORM['kcardcode'] == "") {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['kcardcode'] = 'x';
                }
            }
            if ($FORM['zahlweise'] == 3) {
                if ($FORM['iban'] == "") {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['iban'] = 'x';
                }

                if ($FORM['bic'] == "") {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['bic'] = 'x';
                }

                if ($FORM['bank'] == "") {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['bank'] = 'x';
                }
            }


            if (is_array($FORMCUST) && isset($_POST['FORMCUST'])) {
                foreach ($this->val_not_empty as $key => $value) {
                    if (trim($FORMCUST[$value]) == "") {
                        $this->TCR->set_fault_form(true);
                        $kregform_err[$value] = 'x';
                    }
                }
                if (!validate_email_input($FORMCUST['email'])) {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['email'] = 'x';
                }
                if (strlen($FORMCUST['passwort']) < 4) {
                    $this->TCR->set_fault_form(true);
                    $kregform_err['passwort'] = 'x';
                }
            }
            $this->SELLFORM['PAY_OBJ']['fault_form'] = $this->TCR->fault_form;
            if ($this->TCR->fault_form == true) {
                $this->smarty->assign('kregform_err', $kregform_err);

                return false;
            }
        }

        if (CU_LOGGEDIN) {
            if ($FORM['zahlweise'] == 3) {
                $FORMCUST['bank'] = $FORM['bank'];
                $FORMCUST['iban'] = $FORM['iban'];
                $FORMCUST['bic'] = $FORM['bic'];
            }
        }

        $FORMCUST = $this->arr_trim_striptags($FORMCUST);

        if ((int)$_POST['createabo'] == 1) {
            $aboid = $this->create_abo($FORM, $FORMCUST);
        }
        else {
            # Bestellung erzeugen
            $oid = $this->create_invoice($FORM, $FORMCUST);
        }
        # ensure mandatref set
        $this->client->call('update_mandat_ref', array());
        if ($FORM['zahlweise'] == 18) {
            require_once ("paypal_lastschrift.class.php");
            if ($aboid > 0) {
                $oid = $this->client->call('gen_abo_invoice', array('aboid' => $aboid));
            }
            $paypalLastschrift = new paypal_lastschrift();
            $o_obj = $this->client->call('load_order', array('oid' => $oid));
            $paypalLastschrift->on_order($o_obj);
        }
        if ($FORM['zahlweise'] == 3 && $this->gbl_config['sf_sepapdfsend'] == 1) {
            include_once (CMS_ROOT . "includes/pdf.class.php");
            $o_obj = $this->client->call('load_order', array('oid' => $oid));
            if ($aboid > 0)
                $aboobj = $this->client->call('load_abo', array('id' => $aboid));
            else
                $aboid = array();
            $this->set_shop_config();
            $pdf_class = new pdf_class();
            # reload because auf changeing customer options
            $CUSTOMER = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . $this->user_object['kid'] . "'");
            $this->smarty->assign('customer', $CUSTOMER);
            $this->smarty->assign('order', $o_obj);
            $this->smarty->assign('abo', $aboobj);
            $this->smarty->assign('gbl_config', $this->gbl_config);
            $this->smarty->assign('gbl_config_shop', $this->gbl_config_shop);
            $att_files[] = $pdf_class->createPDFFile(smarty_compile(get_template($this->gbl_config['sf_sepatpl'])), 'SEPA_Formular');
            $sepa_act_link = 'http://www.' . FM_DOMAIN . PATH_CMS . 'index.php?page=' . $_REQUEST['page'] . '&aboid=' . (int)$aboid . '&section=sepaval&cmd=sepaval&kid=' .
                $this->user_object['kid'] . '&hash=' . md5($this->user_object['kid'] . $this->gblconfig->cms_hash_password);
            $smarty_arr = array(
                'order' => $o_obj,
                'abo' => $aboobj,
                'customer' => $this->user_object,
                'sepa_act_link' => $sepa_act_link);
            $email_arr = replacer(get_email_template($this->gbl_config['sf_pdfmail']), $this->user_object['kid'], $smarty_arr);
            send_easy_mail_to($this->user_object['email'], $email_arr['content'], $email_arr['subject'], $att_files);
        }
        return true;
    }

    /**
     * sellform_class::cmd_sepaval()
     * 
     * @return
     */
    function cmd_sepaval() {
        $hash = md5($_GET['kid'] . $this->gblconfig->cms_hash_password);
        $aboid = (int)$_GET['aboid'];
        $this->SELLFORM['sepaok'] = $hash == $_GET['hash'];
        if ($this->SELLFORM['sepaok'] == true) {
            $info = array(
                'SEPA Confirm:' => 'true',
                'IP:' => REAL_IP,
                'Datum/Zeit:' => date('d.m.Y H:i:s'),
                'Knr:' => $this->user_object['kid'],
                'Nachname:' => $this->user_object['nachname'],
                'Vorname:' => $this->user_object['vorname'],
                'Email:' => $this->user_object['email'],
                'Abo ID:' => $_GET['aboid'],
                'Email verified:' => 'true',
                'Hash:' => $_GET['hash'] . '=' . $hash,
                );
            foreach ($info as $key => $val) {
                $content .= $this->min_len($key, 19) . $val . "\n";
            }

            send_easy_mail_to($this->user_object['email'], $content, 'SEPA Confirm ' . $this->user_object['kid'] . ' ' . $this->user_object['nachname']);
            if ($aboid > 0) {
                $this->client->call('update_abo', array('id' => $aboid, 'ABO' => array('abo_active' => 1)));
            }
        }
    }

    /**
     * sellform_class::cmd_order_done()
     * 
     * @param mixed $params
     * @return
     */
    function cmd_order_done($params = null) {
        require_once ("paypal_lastschrift.class.php");
        $paypalLastschrift = new Paypal_lastschrift();
        $paypalLastschrift->on_order_done();
    }

    /**
     * sellform_class::load_customer()
     * 
     * @param mixed $kid
     * @return
     */
    function load_customer($kid) {
        return $this->db->query_first("SELECT *,L.id AS LID FROM " . TBL_CMS_CUST . " K, " . TBL_CMS_LAND . " L WHERE L.id=K.land AND K.kid=" . (int)$kid);
    }

    /*
    function add_customer($KOBJ) {
    $K = $this->db->query_first("SELECT * FROM " . TBL_KUNDEN . " WHERE email='" . $KOBJ['email'] . "'");
    if ($K['kid'] == 0) {
    $KOBJ['passwort'] = md5($KOBJ['passwort']);
    $kid = insert_table(TBL_KUNDEN, $KOBJ);
    }
    else {
    $kid = $K['kid'];
    }
    return $kid;
    }*/
    /**
     * sellform_class::create_abo()
     * 
     * @param mixed $FORM
     * @param mixed $FORMCUST
     * @return
     */
    function create_abo($FORM, $FORMCUST) {
        global $DATA;
        $PRODUCT = $this->SELLFORM['PRODUCT'];
        # Kunden anlegen bzw laden
        if (CU_LOGGEDIN) {
            update_table(TBL_CMS_CUST, 'kid', $this->user_object['kid'], $FORMCUST);
            $this->load_customer($this->user_object['kid']);
            $kid = $this->user_object['kid']; #$this->client->call('register_customer', array('customer' => $KOBJ));
        }
        else {
            $kid = $this->client->call('register_customer', array('customer' => $FORMCUST));
            $KOBJ = $this->client->call('load_customer', array('kid' => $kid));
        }
        $FORM['abo_active'] = 0;

        $aboid = $this->client->call('create_abo', array(
            'abo' => $FORM,
            'kid' => $kid,
            'pid' => (int)$_POST['pid'],
            'kobj' => $FORMCUST));
        $this->TCR->redirecto = SSLSERVER . $_SERVER['PHP_SELF'] . '?cmd=orderinfo&section=orderfine&aboid=' . $aboid . '&kid=' . $kid . '&page=' . $this->TCR->POST['page'] .
            '&hash=' . sha1($aboid . $kid . $FORM['zahlweise']);
        return $aboid;
    }

    /**
     * sellform_class::create_invoice()
     * 
     * @param mixed $FORM
     * @param mixed $FORMCUST
     * @return
     */
    function create_invoice($FORM, $FORMCUST) {
        global $DATA;

        $PRODUCT = $this->SELLFORM['PRODUCT'];
        if (CU_LOGGEDIN) {
            update_table(TBL_CMS_CUST, 'kid', $this->user_object['kid'], $FORMCUST);
            $KOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$this->user_object['kid']);
            $kid = $KOBJ['kid'];
            #$KOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$this->user_object['kid']);
            #$this->load_customer($this->user_object['kid']);
            #$kid = $this->client->call('register_customer', array('customer' => $KOBJ));
        }
        else {
            $kid = $this->client->call('register_customer', array('customer' => $FORMCUST));
            $KOBJ = $this->client->call('load_customer', array('kid' => $kid));
        }

        $product_list = array();
        $PRO = array(
            'menge' => 1,
            'pname' => $PRODUCT['pname'],
            'price' => $PRODUCT['vk_pro_unit'],
            'vpe_id' => $PRODUCT['eid'],
            'bmerk' => '',
            'sub_title' => '');
        $product_list[$PRODUCT['pid']] = $PRO;
        $ordersettings = array(
            'rabatt' => 0,
            'post_brutto' => 0,
            'gewicht' => 0,
            'ngeb' => 0,
            'kid' => $kid,
            'datum' => date("d.m.Y"),
            'lieferdatum' => date("d.m.Y"),
            'zahlweise' => $FORM['zahlweise'],
            'datum' => date('d.m.Y'),
            're_template' => 'invoice_tpl_1');

        $oid = $this->client->call('create_invoice', array(
            'customer' => $KOBJ,
            'products' => $product_list,
            'ordersettings' => $ordersettings));
        $this->client->call('update_invoice', array('order' => $FORM, 'oid' => $oid));

        $this->TCR->redirecto = SSLSERVER . $_SERVER['PHP_SELF'] . '?cmd=orderinfo&section=orderfine&oid=' . $oid . '&kid=' . $kid . '&page=' . $this->TCR->POST['page'] .
            '&hash=' . sha1($oid . $kid . $FORM['zahlweise']);

        $this->connect_to_wiziq($oid, $product_list);
        return $oid;
    }

    /**
     * sellform_class::connect_to_wiziq()
     * 
     * @param mixed $oid
     * @param mixed $product_list
     * @return
     */
    function connect_to_wiziq($oid, $product_list) {
        $oid = (int)$oid;
        if ($oid > 0 && $this->gbl_config['mod_wiziq'] == 1 && class_exists('wiziq_class')) {
            $fields = array(
                'wq_pid',
                'wq_date',
                'wq_time_start',
                'wq_tid');
            foreach ($product_list as $pid => $P) {
                $PRO = $this->client->call('load_product', array('pid' => $pid));
                $FORM_OI = array();
                foreach ($fields as $v)
                    $FORM_OI[$v] = $PRO[$v];
                $OI = $this->client->call('get_ordercontent_by_pid', array('oid' => $oid, 'pid' => $pid));
                $this->client->call('update_ordercontent', array('id' => $OI['id'], 'FORM' => $FORM_OI));
            }
            $o_obj = $this->client->call('load_order', array('oid' => $oid));
            $WIZIQ = new wiziq_class();
            $WIZIQ->connect_order_to_wiziq($o_obj['oid'], $o_obj['kid'], $o_obj['datum']);
            unset($WIZIQ);
        }
    }

    /**
     * sellform_class::cmd_orderinfo()
     * 
     * @return
     */
    function cmd_orderinfo() {
        if ($this->TCR->GET['oid'] > 0) {
            $this->SELLFORM['ORDER'] = $this->client->call('load_order', array('oid' => (int)$this->TCR->GET['oid']));
        }
        if (CU_LOGGEDIN) {
            $this->SELLFORM['KOBJ'] = $this->load_customer((int)$this->user_object['kid']);
            $kid = (int)$this->user_object['kid'];
        }
        else {
            $this->SELLFORM['KOBJ'] = $this->client->call('load_customer', array('kid' => (int)$this->TCR->GET['kid']));
            $kid = (int)$this->TCR->GET['kid'];
        }
        if ($this->TCR->GET['aboid'] > 0) {
            $this->SELLFORM['ABO'] = $this->client->call('load_abo', array('id' => (int)$this->TCR->GET['aboid']));
        }

        $this->set_paypal_vars();
        $this->set_postfinance_vars();
        $params = array(
            'oid' => (int)$this->TCR->GET['oid'],
            'kid' => $kid,
            'aboid' => (int)$this->TCR->GET['aboid'],
            );
        exec_evt('on_sellform_order_finish', $params);
    }

    /**
     * sellform_class::clean_string()
     * 
     * @param mixed $clean_string
     * @return
     */
    function clean_string($clean_string) {

    }

    /**
     * sellform_class::set_postfinance_vars()
     * 
     * @return
     */
    function set_postfinance_vars() {
        $this->set_shop_config();
        function postfi_str_format($t) {
            while (strstr($t, '  '))
                $t = str_replace('  ', ' ', $t);
            $replace = array(
                'ä' => 'ae',
                'ü' => 'ue',
                'ö' => 'oe',
                'Ä' => 'Ae',
                'Ü' => 'Ue',
                'Ö' => 'Oe',
                'ß' => 'ss');
            $t = trim(strtr($t, $replace));
            return $t;
        }
        $DATA = new data_class($this->SELLFORM['KOBJ'], NULL);
        $o_obj = $this->SELLFORM['ORDER'];
        $postfi = array(
            "PSPID" => $this->gbl_config_shop['postfi_pspid'],
            "ORDERID" => (int)$o_obj['oid'],
            "LANGUAGE" => 'de_DE',
            "AMOUNT" => round($o_obj['brutto'], 2) * 100, // VORGABE VON POSTFI
            "EMAIL" => $this->SELLFORM['KOBJ']['email'],
            "OWNERADDRESS" => $this->SELLFORM['KOBJ']['strasse'],
            "OWNERCTY" => $this->SELLFORM['KOBJ']['land'],
            "OWNERTOWN" => $this->SELLFORM['KOBJ']['ort'],
            "COM" => FM_DOMAIN . ' Bestellung ' . (int)$o_obj['oid'],
            "LOGO" => 'https://sslsites.de/' . FM_DOMAIN . PATH_SHOP . 'images/postfilogo.jpg',
            "HOMEURL" => 'http://www.' . FM_DOMAIN,
            "PARAMPLUS" => 's_sid=' . session_id(),
            "ACCEPTURL" => 'http://www.' . FM_DOMAIN . '?cmd=orderinfo&section=zwfeedback&oid=' . (int)$o_obj['oid'] . '&kid=' . $this->SELLFORM['KOBJ']['kid'] .
                '&page=10137&pfstatus=1', #$DATA->OWNPAGES['links']['URLEXTERNSSL993'], // genehmigt
            "DECLINEURL" => 'http://www.' . FM_DOMAIN . '?cmd=orderinfo&section=zwfeedback&oid=' . (int)$o_obj['oid'] . '&kid=' . $this->SELLFORM['KOBJ']['kid'] .
                '&page=10137&pfstatus=2', #$DATA->OWNPAGES['links']['URLEXTERNSSL992'], //max. Anzahl Versuche
            "EXCEPTIONURL" => 'http://www.' . FM_DOMAIN . '?cmd=orderinfo&section=zwfeedback&oid=' . (int)$o_obj['oid'] . '&kid=' . $this->SELLFORM['KOBJ']['kid'] .
                '&page=10137&pfstatus=3', #$DATA->OWNPAGES['links']['URLEXTERNSSL991'], //unsicher
            "CANCELURL" => 'http://www.' . FM_DOMAIN . '?cmd=orderinfo&section=zwfeedback&oid=' . (int)$o_obj['oid'] . '&kid=' . $this->SELLFORM['KOBJ']['kid'] .
                '&page=10137&pfstatus=4', #$DATA->OWNPAGES['links']['URLEXTERNSSL990'], //annulliert
            "USERID" => $this->SELLFORM['KOBJ']['kid'],
            "CURRENCY" => $DATA->CURRENCY_DEFAULT['letter_code']);

        ksort($postfi);
        $postfi['OWNERADDRESS'] = $this->only_alphanums(postfi_str_format($postfi['OWNERADDRESS']));
        $postfi['OWNERCTY'] = $this->only_alphanums(postfi_str_format($postfi['OWNERCTY']));
        $postfi['OWNERCTY'] = $this->only_alphanums(postfi_str_format($postfi['OWNERCTY']));
        $postfi['OWNERTOWN'] = $this->only_alphanums(postfi_str_format($postfi['OWNERTOWN']));

        foreach ($postfi as $key => $v) {
            if ($v != "") {
                $sha_string .= $key . '=' . $v . $this->gbl_config_shop['postfi_sha1pass'];
            }
        }
        $hash = strtoupper(hash($this->gbl_config_shop['postfi_psshatype'], $sha_string));
        $postfi['SHASIGN'] = $hash;
        $apilink = ($this->gbl_config_shop['postfi_sandbox'] == 1) ? 'https://e-payment.postfinance.ch/ncol/test/orderstandard_utf8.asp' :
            'https://e-payment.postfinance.ch/ncol/prod/orderstandard.asp';
        $this->smarty->assign('postfi', $postfi);
        $this->smarty->assign('apilink', $apilink);

    }

    /**
     * sellform_class::set_paypal_vars()
     * 
     * @return
     */
    function set_paypal_vars() {
        global $GBL_LANGID;
        $this->set_shop_config();
        $DATA = new data_class($this->SELLFORM['KOBJ'], NULL);
        $this->smarty->assign('currency', $CURRENCY_ACTIVE);
        $this->smarty->assign('currency_count', count($CURRENCY_ACTIVE));
        $this->smarty->assign('currency_active', $_SESSION['active_curr']);
        $this->smarty->assign('currency_lettercode', $DATA->CURRENCY_DEFAULT['letter_code']);
        $this->smarty->assign('currency_kurs', $DATA->CURRENCY_DEFAULT['kurs']);
        $o_obj = $this->SELLFORM['ORDER'];
        $PAYPAL_POST = array(
            "PAYPAL_URL" => "https://www.paypal.com/cgi-bin/webscr",
            "business" => $this->gbl_config_shop['adr_email_paypal'],
            "cmd" => "_xclick",
            "currency_code" => $DATA->CURRENCY_DEFAULT['letter_code'],
            "mc_currency" => $DATA->CURRENCY_DEFAULT['letter_code'],
            "residence_country" => $this->SELLFORM['KOBJ']['country_code_2'],
            "cancel_return" => $this->gbl_config_shop['opt_site_domain'],
            "image_url" => SSLSERVER . substr(PATH_SHOP, 1, strlen(PATH_SHOP) - 1) . 'images/pplogo.jpg',
            "return" => $this->gbl_config_shop['opt_site_domain'] . 'index.php?page=order&done=1',
            "rm" => 2,
            "no_note" => 1,
            "item_name" => pure_translation('{LBL_YOURORDER} ', $GBL_LANGID) . $this->gbl_config['adr_firma'],
            "amount",
            str_replace(',', '.', printMenge($o_obj['brutto'])),
            "shipping" => 0,
            "quantity" => 1,
            "item_number" => $o_obj['oid'],
            "invoice" => $o_obj['oid'],
            "custom" => $o_obj['oid'],
            "notify_url" => $this->gbl_config_shop['opt_site_domain'] . 'index.php?page=paypal',
            #"undefined_quantity" => "",
            "edit_quantity" => "",
            "first_name" => $this->SELLFORM['KOBJ']['vorname'],
            "last_name" => $this->SELLFORM['KOBJ']['nachname'],
            "adress1" => $this->SELLFORM['KOBJ']['strasse'] . ' ' . $this->SELLFORM['KOBJ']['hausnr'],
            "zip" => $this->SELLFORM['KOBJ']['plz'],
            "city" => $this->SELLFORM['KOBJ']['ort'],
            "email" => $this->SELLFORM['KOBJ']['email'],
            "post_method" => "fso",
            "curl_location" => "/usr/local/bin/curl");
        foreach ($PAYPAL_POST as $key => $value)
            $PAYPAL_POST[$key] = utf8_decode($value);

        $this->SELLFORM['PAYPAL'] = (array )$PAYPAL_POST;
    }

    /**
     * sellform_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * sellform_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->MODIDENT = 'sellform';
        $this->SELLFORM['CONFIG'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * sellform_class::preparse()
     * 
     * @param mixed $params
     * @return
     */
    function preparse($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_SELLFORM_')) {
            preg_match_all("={TMPL_SELLFORM_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $form_id) {
                $SF_FORM = $this->load_form_fe($form_id);
                $this->smarty->assign('TMPL_SELLFORM_' . $form_id, $SF_FORM);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=sellforminlay value=$TMPL_SELLFORM_' . $form_id . ' %><% include file="' . $SF_FORM['template']['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * sellform_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['sellformid'];
        $result = $this->db->query_first("SELECT * FROM " . TBL_CMS_SELFORMS . " WHERE id=" . $id);
        $upt = array('tm_content' => '{TMPL_SELLFORM_' . (int)$id . '}', 'tm_pluginfo' => $result['fo_name']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * sellform_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_SELFORMS . " ORDER BY fo_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }
}

?>