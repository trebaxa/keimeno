<?php

/**
 * @package    contractform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


DEFINE('TBL_CMS_CONTACTS', TBL_CMS_PREFIX . 'contacts');

class contactform_class extends modules_class {
    var $CONTACTF = array();
    /**
     * contactform_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->CONTACTF = array();
    }

    /**
     * contactform_class::init()
     * 
     * @return
     */
    function init() {
        $this->CONTACTF['values'] = array();
        if (isset($_POST['FORM_NOTEMPTY']) && isset($_POST['FORM'])) {
            $this->CONTACTF['values'] = array_merge((array )$_POST['FORM_NOTEMPTY'], (array )$_POST['FORM']);
        }
    }

    /**
     * contactform_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('CONTACTF') != null) {
            $this->CONTACTF = array_merge($this->smarty->getTemplateVars('CONTACTF'), $this->CONTACTF);
            $this->smarty->clearAssign('CONTACTF');
        }
        $this->smarty->assign('CONTACTF', $this->CONTACTF);
    }


    /**
     * contactform_class::cmd_sendmsg()
     * 
     * @return
     */
    function cmd_sendmsg() {
        global $FIREWALL;
        $_SESSION['err_msgs'] = array();
        $FIREWALL->do_log('contact');

        # load plugin option if set
        $PLUGIN_OPT = array();
        if (isset($_POST['cont_matrix_id']) && $_POST['cont_matrix_id'] > 0) {
            $PLUGIN_OPT = $this->load_plug_opt((int)$_POST['cont_matrix_id']);
        }

        $FORM = (array )$_POST['FORM'];
        $FORM_NOTEMPTY = (array )$_POST['FORM_NOTEMPTY'];

        $FORM = self::arr_trim_striptags($FORM);
        $FORM_NOTEMPTY = self::arr_trim_striptags($FORM_NOTEMPTY);
        $recipient_email = ($PLUGIN_OPT['email'] != "") ? $PLUGIN_OPT['email'] : FM_EMAIL;
        $FORM_ALL = array_merge($FORM, $FORM_NOTEMPTY);

        // check not emtpy fields , required fields
        if (is_array($FORM_NOTEMPTY)) {
            foreach ($FORM_NOTEMPTY as $db_column => $sword) {
                $sword = trim($sword);
                if ($sword == "") {
                    if ($this->has_errors() == true) {
                        $this->msge('{LBL_LBLPLEASECHECKFIELD} ');
                    }
                    $this->msge(ucfirst($db_column));
                    $contact_err[$db_column] = true;
                }
                $FORM[$db_column] = ($sword);
            }
        }
        $visitor_email = $FORM['email'] = $FORM['tschapura'];
        unset($FORM['tschapura']);
        $tschapura = preg_replace("/(\n+|\r+|%0A|%0D)/i", '', $visitor_email);
        if ((int)$PLUGIN_OPT['cf_notschapura'] == 0) {
            if (!validate_email_input($tschapura)) {
                $this->msge("{LBL_LBLPLEASECHECKFIELD} {LBL_EMAIL}");
                $contact_err['email'] = true;
            }
        }
        else {
            if (!validate_email_input($tschapura)) {
                $tschapura = $recipient_email;
            }
        }

        $c_disclaimer_sign = array(
            'time' => time(),
            'email' => $tschapura,
            'ip' => self::anonymizing_ip(REAL_IP));

        $c_disclaimer_sign = serialize($c_disclaimer_sign);
        #if ($this->gbl_config['captcha_active'] == 1) {
        if ((int)$PLUGIN_OPT['cf_captcha'] == 1) {
            if (isset($_SESSION['captcha_spam']) and $_POST["securecode"] == $_SESSION['captcha_spam']) {
                unset($_SESSION['captcha_spam']);
            }
            else {
                $this->msge("{ERR_SECODE}");
                $contact_err['capcha'] = true;
            }
        }

        # Token
        /*if ($_POST['ajaxsubmit'] != 1 && (empty($_POST['token']) || $_POST['token'] != $_SESSION['token'])) {
        $this->msge("invalid token.");
        $contact_err['token'] = true;
        $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        firewall_class::report_hack('Contact formular, invalid token');
        }*/

        # Hidden Email Feld
        if (isset($_POST['email']) && $_POST['email'] != "") {
            $this->msge("hacking.");
            $contact_err['hacking'] = true;
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hack('Contact formular, hacking over hidden field');
        }

        # invalid USER AGENT
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 254) : '';
        if (strlen($user_agent) < 2) {
            $contact_err['hacking'] = true;
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . REAL_IP . ', ' . $user_agent);
            self::msge('invalid browser type');
            firewall_class::report_hack('invalid browser type');
        }

        #detect HTML
        foreach ($FORM_ALL as $key => $value) {
            if (strip_tags($value) != $value) {
                self::msge('HTML gefunden. Kein HTML erlaubt');
            }
        }

        # Forbidden Words
        if ($PLUGIN_OPT['cf_forbiddenwords'] != "") {
            $wormprotector = explode(',', trim($PLUGIN_OPT['cf_forbiddenwords']));
            $wormprotector = self::arr_trim($wormprotector);
            $cracktrack = implode(' ', $FORM) . implode(' ', $FORM_NOTEMPTY);
            $checkworm = str_ireplace($wormprotector, '*', $cracktrack);
            if ($cracktrack != $checkworm) {
                self::msge('verbotene Inhalte z.B. URL');
            }
        }

        if (count($_SESSION['err_msgs']) == 0) {
            $email_msg = "";
            ksort($FORM);
            foreach ($FORM as $key => $value) {
                if (!is_array($FORM[$key])) {
                    $email_msg .= strtoupper($key) . ": " . $FORM[$key] . "\n";
                }
                else {
                    foreach ($FORM[$key] as $key_arr => $value_arr) {
                        $email_msg .= "\t" . strtoupper($key_arr) . ": " . trim(strip_tags($FORM[$key][$key_arr])) . "\n";
                    }
                }
            }

            // File
            $att_files = array();
            if (isset($_FILES)) {
                if (!is_array($_FILES["datei"]["name"])) {
                    if (move_uploaded_file($_FILES["datei"]["tmp_name"], CMS_ROOT . CACHE . $_FILES["datei"]["name"])) {
                        $att_files[] = CMS_ROOT . CACHE . $_FILES["datei"]["name"];
                    }
                }

                // Multi Files
                if (is_array($_FILES["files"]['name'])) {
                    foreach ($_FILES["files"]['tmp_name'] as $key => $datei) {
                        if ($datei != "") {
                            if (move_uploaded_file($datei, CMS_ROOT . CACHE . $_FILES["files"]['name'][$key])) {
                                $att_files[] = CMS_ROOT . CACHE . $_FILES["files"]['name'][$key];
                            }
                        }
                    }
                }
            }

            $this->smarty_arr = array('mail' => array('subject' => pure_translation('{LBL_EMAIL_KONTAKT} ' . $FORM['nachname'], 1), 'content' => date("d.m.Y H:i:s") .
                        PHP_EOL . PHP_EOL . $email_msg));


            send_easy_mail_to($recipient_email, $this->smarty_arr['mail']['content'], $this->smarty_arr['mail']['subject'], $att_files, true, $tschapura, $tschapura);
            #general mail template
            send_admin_mail(900, $this->smarty_arr, $att_files, $tschapura);

            # disclaimer Mail
            if ((int)$PLUGIN_OPT['cf_send_we'] == 1) {
                $this->gbl_config['we_link'] = self::get_domain_url() . '?page=' . $_POST['page'] . '&cmd=disclaim_reject&mail=' . $tschapura . '&hash=' . sha1($tschapura . $this->
                    gbl_config['hash_secret']);
                #  $email_msg_disclaim = "Hallo,\n\nSie haben in die Verarbeitung Ihrer im Kontaktformular angegebenen Daten zum Zwecke der Bearbeitung Ihrer Anfrage eingewilligt. Diese Einwilligung können Sie jederzeit durch Klick auf den nachfolgenden Link \n" .
                #      $link . "\n, unter dem entsprechenden Link auf der Kontaktseite unserer Homepage, durch gesonderte E-Mail (" . FM_EMAIL . "), Telefax (" . $this->gbl_config['adr_fax'] .
                #      ") oder Brief an die " . $this->gbl_config['adr_firma'] . ", " . $this->gbl_config['adr_street'] . ", " . $this->gbl_config['adr_plz'] . " " . $this->
                #      gbl_config['adr_town'] . " widerrufen." . "\n\n\n" . $this->gbl_config['email_absender'];

                $email_msg_disclaim = smarty_compile($PLUGIN_OPT['cf_we_text']);
                $arr = array('mail' => array('subject' => 'Kontaktaufnahme ' . $this->gbl_config['adr_general_firmname'], 'content' => $email_msg_disclaim));
                send_easy_mail_to($tschapura, $arr['mail']['content'], $arr['mail']['subject'], array(), true, $recipient_email, $recipient_email);
            }

            # save to db
            if ((int)$PLUGIN_OPT['cf_save'] == 1) {
                $arr = array(
                    'c_time' => time(),
                    'c_sender' => $tschapura,
                    'c_cc' => $this->gblconfig->cf_ccemail,
                    'c_recipient' => $recipient_email,
                    'c_text' => $email_msg,
                    'c_disclaimer_sign' => $c_disclaimer_sign,
                    'c_subject' => $this->smarty_arr['mail']['subject']);
                insert_table(TBL_CMS_CONTACTS, $this->arr_trim_striptags($arr));
            }
            # send CC
            if ($this->gblconfig->cf_ccemail != "") {
                send_easy_mail_to($this->gblconfig->cf_ccemail, $this->smarty_arr['mail']['content'], 'CC COPY ' . $this->smarty_arr['mail']['subject'], $att_files, true, $tschapura);
            }

            # cleanup
            foreach ($att_files as $key => $filename) {
                delete_file($filename);
            }

            $this->LOGCLASS->addLog('SENDMAIL', 'contact mail ' . $FORM['nachname'] . ', ' . $FORM['vorname'] . ', ' . $tschapura);
            if ((int)$_POST['ajaxsubmit'] == 1) {
                $this->msg('Nachricht gesendet.');
                $this->ej('reset_contact_form');
            }
            else {
                HEADER("location: " . $_SERVER['PHP_SELF'] . "?page=" . $_POST['page'] . "&section=done");
                exit;
            }
        }
        else {
            $this->LOGCLASS->addLog('FORM_FAILURE', 'contact formular ' . implode(', ', $_SESSION['err_msgs']));
            if ((int)$_POST['ajaxsubmit'] == 1) {
                $this->ej();
            }
        }

        $this->smarty->assign('contact_err', $contact_err);
    }

    /**
     * contactform_class::parse_contact_form()
     * 
     * @param mixed $params
     * @return
     */
    function parse_contact_form($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_CONTACTINLAY_')) {
            preg_match_all("={TMPL_CONTACTINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $this->smarty->assign('TMPL_CONTACT_' . $cont_matrix_id, $PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=contact value=$TMPL_CONTACT_' . $cont_matrix_id . ' %> ', $html);
            }
            $this->parse_to_smarty();
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * contactform_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='contactform' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * contactform_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array(
            'tm_modident' => 'contactform',
            'tm_content' => '{TMPL_CONTACTINLAY_' . $cont_matrix_id . '}<% assign var=cont_matrix_id value="' . $cont_matrix_id . '" %><%include file="' . $R['tpl_name'] .
                '.tpl"%>',
            'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * contactform_class::cmd_disclaim_reject()
     * 
     * @return void
     */
    function cmd_disclaim_reject() {
        $mail = trim($_GET['mail']);
        $hash1 = sha1($mail . $this->gbl_config['hash_secret']);
        if ($hash1 != $_GET['hash']) {
            self::msge('Invalid hash');
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hack('Contact formular, hacking disclaimer field');
        }
        if (self::has_errors() == false) {
            dao_class::db_delete(TBL_CMS_CONTACTS, array('c_sender' => $mail));
            self::msg('Ihre Daten wurden entfernt.');
        }

        header('location:/index.html');
        $this->hard_exit();
    }

    /**
     * contactform_class::cmd_send_disclaim_reject()
     * 
     * @return void
     */
    function cmd_send_disclaim_reject() {
        $FORM = self::arr_trim_striptags($_POST['FORM']);
        $tschapura = preg_replace("/(\n+|\r+|%0A|%0D)/i", '', $FORM['tschapura']);
        if (!validate_email_input($tschapura)) {
            self::msge("{LBL_EMAIL}");
        }
        if (self::has_errors() == false) {
            $PLUGIN_OPT = $this->load_plug_opt((int)$_POST['cont_matrix_id']);
            $link = self::get_domain_url() . '?page=' . $_POST['page'] . '&cmd=disclaim_reject&mail=' . $tschapura . '&hash=' . sha1($tschapura . $this->gbl_config['hash_secret']);
            $email_msg = "Hallo,\n\nper Klick auf diesen Link wird Ihre Einwilligungserklärung vom Kontaktformular mit E-Mail \"" . $tschapura . "\" widerufen:\n" . $link .
                "\n\n\n" . $this->gbl_config['email_absender'];
            $email_msg = smarty_compile($email_msg);
            $arr = array('mail' => array('subject' => 'Einwilligungserklärung widerrufen', 'content' => $email_msg));
            $recipient_email = ($PLUGIN_OPT['email'] != "") ? $PLUGIN_OPT['email'] : FM_EMAIL;
            send_easy_mail_to($tschapura, $arr['mail']['content'], $arr['mail']['subject'], array(), true, $recipient_email, $recipient_email);
            # send_admin_mail(900, $arr, $att_files, $tschapura);
            self::msg('Sie haben eine E-Mail erhalten.');
        }
        $this->ej('reset_disclaim_form');
    }

}
