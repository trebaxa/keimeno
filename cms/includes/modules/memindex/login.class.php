<?php

/**
 * @package    memindex
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');


class login_class extends keimeno_class {

    /**
     * login_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $CMSDATA;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * login_class::cmd_sendpass()
     * 
     * @return
     */
    function cmd_sendpass() {
        if (!validate_email_input($_REQUEST['email'])) {
            keimeno_class::msge('{ERR_EMAIL}');
        }
        if ($this->has_errors() == false) {
            $e_object = $this->try_load_user();
            if ($e_object['kid'] > 0) {
                $e_object['passwort'] = gen_sid(6);
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . $e_object['passwort'] . "' WHERE kid='" . $e_object['kid'] . "' LIMIT 1");
                send_mail_to(replacer(get_email_template(980), $e_object['kid']));
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . encrypt_password($e_object['passwort']) . "' WHERE kid='" . $e_object['kid'] . "' LIMIT 1");
                $this->LOGCLASS->addLog('SENDMAIL', 'new password send: "<a href="index.php?kwort=' . $_REQUEST['email'] . '">' . $_REQUEST['email'] . '</a>"');
                if ($_REQUEST['autosubmit'] == 1) {
                    echo $_GET['callback'] . '({"status" : "OK"})';
                    $this->hard_exit();
                }
                else {
                    HEADER("location: http://www." . FM_DOMAIN . PATH_CMS . "index.php?sid_id=" . session_id() . "&msg=" . base64_encode("{LBL_EMAILERHALTEN}."));
                    $this->hard_exit();
                }
            }
            else {
                $this->LOGCLASS->addLog('FORM_FAILURE', 'password not send (unknown email): "<a href="index.php?kwort=' . $_POST['email'] . '">' . $_POST['email'] . '</a>"');
                keimeno_class::msge('{LBL_ACCOUNTNOTFOUND}');
                $smarty->assign('loginform_err', $err_arr);
                if ($_REQUEST['autosubmit'] == 1) {
                    echo $_GET['callback'] . '({"status" : "FAILED"})';
                    $this->hard_exit();
                }
                if ($_REQUEST['ajaxform'] == 1) {
                    $this->ej('loginresult');
                }
            }
        }

    }

    /**
     * login_class::try_load_user()
     * 
     * @return
     */
    function try_load_user() {
        $e_object = array();
        if ($this->gbl_config['login_mode'] == 'PUBLIC_EMAIL') {
            $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.email='" . $_REQUEST['email'] . "' ");
        }
        else
            if ($this->gbl_config['login_mode'] == 'NONE_PUBLIC_EMAIL') {
                $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.email_notpublic='" . $_REQUEST['email'] .
                    "' ");
            }
            else
                if ($this->gbl_config['login_mode'] == 'USERNAME') {
                    $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.username='" . $_REQUEST['email'] . "' ");
                }
                else
                    if ($this->gbl_config['login_mode'] == 'KNR') {
                        $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.kid='" . $_REQUEST['email'] . "' ");
                    }
        return $e_object;
    }

    /**
     * login_class::try_load_user_by_password()
     * 
     * @return
     */
    function try_load_user_by_password() {
        $e_object = array();
        $e_object = $this->try_load_user();
        $e_object['login_msge'] = "";

        if (verfriy_password($_POST['pass'], $e_object['passwort']) === false && $e_object['kid'] > 0) {
            $e_object['login_msge'] = '{LBL_INVALID_PASSWORD}';
            $e_object['feedback'] = 'INVALID_PASSWORT';
        }
        if ($e_object['kid'] <= 0) {
            $e_object['login_msge'] = '{LBL_ACCOUNTNOTFOUND}';
            $e_object['feedback'] = 'ACCOUNT_NOTFOUND';
        }
        if ($e_object['sperren'] == 1 && $e_object['kid'] > 0) {
            $e_object['login_msge'] = '{MSG_ACCOUNTDEAKT}';
            $e_object['feedback'] = 'ACCOUNT_BLOCKED';
        }
        return $e_object;
    }


}
