<?php

/**
 * @package    workshop
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class workshop_class extends workshop_master_class {

    var $WORKSHOP = array();

    /**
     * workshop_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object, $user_obj;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->user_obj = $user_obj;
    }

    /**
     * workshop_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('WORKSHOP') != NULL) {
            $this->WORKSHOP = array_merge($this->smarty->getTemplateVars('WORKSHOP'), $this->WORKSHOP);
            $this->smarty->clearAssign('WORKSHOP');
        }
        $this->smarty->assign('WORKSHOP', $this->WORKSHOP);
    }

    /**
     * workshop_class::load_workshops()
     * 
     * @param mixed $city
     * @return
     */
    function load_workshops($city) {
        $this->WORKSHOP['workshops'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_WS_WORKSHOPS . " WHERE ws_city=" . (int)$city . " ORDER BY ws_date");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_workshop_opt($row);
            $this->WORKSHOP['workshops'][] = $row;
        }
        $this->WORKSHOP['city'] = $this->db->query_first("SELECT * FROM " . TBL_WS_CITIES . " WHERE id=" . (int)$city);
    }

    /**
     * workshop_class::load_cities()
     * 
     * @return
     */
    function load_cities() {
        $this->WORKSHOP['cities'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_WS_CITIES . " ORDER BY c_city");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['count'] = get_data_count(TBL_WS_WORKSHOPS, '*', "ws_city='" . $row['id'] . "'");
            $this->WORKSHOP['cities'][] = $row;
        }
    }

    /**
     * workshop_class::autorun()
     * 
     * @return
     */
    function autorun() {
        $this->load_cities();
        $this->parse_to_smarty();
    }

    /**
     * workshop_class::cmd_load_workshops()
     * 
     * @return
     */
    function cmd_load_workshops() {
        $this->load_workshops($_GET['city']);
    }

    /**
     * workshop_class::cmd_load_workshop()
     * 
     * @return
     */
    function cmd_load_workshop() {
        $this->load_workshop($_GET['id']);
    }

    /**
     * workshop_class::cmd_book_now()
     * 
     * @return
     */
    function cmd_book_now() {
        $id = (int)$_GET['id'];
        $_SESSION['workshop']['selected_ws_id'] = $id;
        if ($this->WORKSHOP['land'] == "")
            $this->WORKSHOP['land'] = $this->gbl_config['default_country'];
        $this->WORKSHOP['land_select'] = build_land_selectbox($this->WORKSHOP['land']);
    }

    /**
     * workshop_class::cmd_register_customer()
     * 
     * @return
     */
    function cmd_register_customer() {
        $FORM_NOTEMPTY = (array )$_POST['FORM_NOTEMPTY'];
        $FORM = (array )$_POST['FORM'];

        $str_arr = array(
            'strasse',
            'ort',
            'bank',
            'nachname',
            'vorname');
        foreach ($str_arr as $key) {
            if ($FORM[$key] != "")
                $FORM[$key] = format_name_string($FORM[$key]);
            if ($FORM_NOTEMPTY[$key] != "")
                $FORM_NOTEMPTY[$key] = format_name_string($FORM_NOTEMPTY[$key]);
        }

        if (count($FORM_NOTEMPTY) > 0) {
            foreach ($FORM_NOTEMPTY as $key => $value) {
                if ($value == '') {
                    $this->msge($key . '{LBL_MISSING}');
                }
                $FORM[$key] = $value;
            }
        }
        $FORM['email'] = strtolower($FORM['email']);
        if (!empty($FORM['geschlecht'])) {
            $FORM['anrede'] = get_customer_salutation($FORM['geschlecht']);
            $FORM['geschlecht'] = get_customer_sex($FORM['geschlecht']);
        }
        $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . $FORM['email'] . "'");
        # Schon vorhanden?
        if ($k_obj['kid'] > 0 && (validate_email_input($FORM['email']) || validate_email_input($FORM['email_notpublic']))) {
            #   keimeno_class::msge('{ERR_EMAIL_VORHANDEN}');
        }
        if (!validate_email_input($FORM['email'])) {
            keimeno_class::msge('{ERR_EMAIL}');
        }
        # Vorname und Nachname
        if (strtolower($FORM['vorname']) == strtolower($FORM['nachname'])) {
            keimeno_class::msge('{ERR_NAMEEQUAL}');
        }
        if ($this->has_errors() == false) {
            $FORM['monat'] = date("m");
            $FORM['jahr'] = date("Y");
            $FORM['tag'] = date("d");
            $FORM['datum'] = date('Y-m-d');
            $FORM['is_cms'] = 1;
            $FORM['ip'] = REAL_IP;
            $kid = insert_table(TBL_CMS_CUST, $FORM);
            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . $kid . "'");
            $this->LOGCLASS->addLog('SENDMAIL', 'send registration mail: ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
            send_mail_to(replacer(get_email_template(990), $k_obj['kid'])); // Template "Registrierung"
            $k_obj['passwort'] = md5($k_obj['passwort']);
            $k_obj['sessionid'] = session_id();
            $this->real_escape($k_obj);
            update_table(TBL_CMS_CUST, 'kid', $k_obj['kid'], $k_obj);
            $this->user_obj->setKid($kid);
            # $this->user_obj->setMemGroups($_POST['GROUPS'], $_POST['MEMBERGROUPSCOL'], false, true);
            $this->user_obj->addMemberToGroup(1100);
            $this->LOGCLASS->addLog('INSERT', 'new registration ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
            $_SESSION['workshop']['kid'] = $k_obj['kid'];
            $this->connect_customer_with_workshop($k_obj['kid'], $_SESSION['workshop']['selected_ws_id']);
            keimeno_class::msg('{LBL_REGOK}');
            $this->ej('load_paypal');
        }
        else {
            $this->ej();
        }
    }

    /**
     * workshop_class::cmd_load_paypal()
     * 
     * @return
     */
    function cmd_load_paypal() {
        $this->load_workshop($_SESSION['workshop']['selected_ws_id']);
        $this->WORKSHOP['customer'] = $K_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K WHERE K.kid=" . (int)$_SESSION['workshop']['kid']);
        $PAYPAL_POST = array(
            "PAYPAL_URL" => "https://www." . (($this->gbl_config['ws_paypal_ipn_sandbox'] == 1) ? "sandbox." : "") . "paypal.com/cgi-bin/webscr",
            "business" => $this->gbl_config['adr_email_paypal'],
            "cmd" => "_xclick",
            "currency_code" => 'EUR',
            "mc_currency" => 'EUR',
            "residence_country" => $this->WORKSHOP['customer']['country_code_2'],
            "cancel_return" => $this->gbl_config['opt_site_domain'],
            "image_url" => SSLSERVER . 'images/logo.jpg',
            "return" => $this->gbl_config['opt_site_domain'] . 'index.php?page=' . $_REQUEST['page'] . '&cmd=payment_done',
            "rm" => 2,
            "cbt" => "{LBL_PAYPAL_NEXT}",
            "no_note" => 1,
            "item_name" => 'Workshop Buchung ' . $this->WORKSHOP['ws']['ws_title'],
            "amount" => sprintf("%01.2f", $this->WORKSHOP['ws']['ws_price_br']),
            "shipping" => 0,
            "quantity" => 1,
            "item_number" => $this->WORKSHOP['ws']['id'],
            "invoice" => date('Ymd') . '' . $this->WORKSHOP['ws']['id'],
            "custom" => $this->WORKSHOP['ws']['id'],
            "notify_url" => $this->gbl_config['opt_site_domain'] . 'index.php?page=paypal',
            #"undefined_quantity" => "",
            "edit_quantity" => "",
            "first_name" => $this->WORKSHOP['customer']['vorname'],
            "last_name" => $this->WORKSHOP['customer']['nachname'],
            "adress1" => $this->WORKSHOP['customer']['strasse'],
            "zip" => $this->WORKSHOP['customer']['plz'],
            "city" => $this->WORKSHOP['customer']['ort'],
            "email" => $this->WORKSHOP['customer']['email'],
            "post_method" => "fso",
            "curl_location" => "/usr/local/bin/curl");

        $this->smarty->assign('PAYPAL_POST', $PAYPAL_POST);
        kf::echo_template_fe('ws_paypal');
    }

}

?>