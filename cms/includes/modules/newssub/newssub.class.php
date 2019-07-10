<?php

/**
 * @package    news
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class newssub_class extends modules_class {

    var $NEWSSUB = array();

    /**
     * newssub_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * newssub_class::parse_contact_newssub()
     * 
     * @param mixed $params
     * @return
     */
    function parse_newssub($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_NEWSSUBINLAY_')) {
            preg_match_all("={TMPL_NEWSSUBINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $this->smarty->assign('TMPL_NEWSSUB_' . $cont_matrix_id, $PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=newssub value=$TMPL_NEWSSUB_' . $cont_matrix_id . ' %> ', $html);
            }
            $this->parse_to_smarty();
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * newssub_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('NEWSSUB') != null) {
            $this->NEWSSUB = array_merge($this->smarty->getTemplateVars('NEWSSUB'), $this->NEWSSUB);
            $this->smarty->clearAssign('NEWSSUB');
        }
        $this->smarty->assign('NEWSSUB', $this->NEWSSUB);
    }

    /**
     * newssub_class::cmd_insert()
     * 
     * @return
     */
    function cmd_add_to_newsletter() {
        $FORM = (array )$_POST['FORM'];
        $FORM = self::arr_trim_striptags($FORM);

        # load plugin option if set
        $PLUGIN_OPT = array();
        if (isset($_POST['cont_matrix_id']) && (int)$_POST['cont_matrix_id'] > 0) {
            $PLUGIN_OPT = $this->load_plug_opt((int)$_POST['cont_matrix_id']);
        }

        # Hidden Email Feld
        if (isset($_POST['email']) && $_POST['email'] != "") {
            $this->msge("hacking.");
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hack('NewsSub formular, hacking over hidden field');
        }

        $tschapura = $FORM['tschapura'];
        if (!validate_email_input($tschapura)) {
            $this->msge("{LBL_EMAIL}");
        }
        if (!validate_subject($FORM['nachname'])) {
            $this->msge("{LBL_NACHNAME}");
        }
        if (!validate_subject($FORM['vorname'])) {
            $this->msge("{LBL_VORNAME}");
        }
        
        if ((int)$DSGVO['dsgvo-1'] != 1) {
            $this->msge("Datenschutz nicht akzeptiert}");
        }

        if ($FORM['nachname'] == '') {
            $this->msge("{LBL_NACHNAME}");
        }

        if ($FORM['vorname'] == '') {
            $this->msge("{LBL_VORNAME}");
        }
        if (get_data_count(TBL_CMS_CUST, "kid", "email='" . $FORM['tschapura'] . "'") > 0 && $FORM['tschapura'] != "") {
            $this->msge("{LBL_EMAIL} {LBL_ALREADY_EXISTS}");
        }

        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            $this->msge("invalid token.");
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        }

        if (count($_SESSION['err_msgs']) == 0) {
            $FORM['email'] = $FORM['tschapura'];
            unset($FORM['tschapura']);
            $kobj = array(
                'time_int' => time(),
                'email' => $data[0],
                'datum' => date('Y-m-d'),
                'geschlecht' => 'f',
                'ip' => REAL_IP,
                'mailactive' => 0,
                'passwort' => encrypt_password(rand(0, 100000)),
                'land' => 1,
                'anrede' => get_customer_salutation('f'),
                #    'rabatt_gruppe' => (int)$PLUGIN_OPT['groupid']
                );
            $FORM = array_merge($kobj, $FORM);
            $kid = insert_table(TBL_CMS_CUST, $FORM);
            if ($PLUGIN_OPT['groupid'] > 0) {
                insert_table(TBL_CMS_CUSTTOGROUP, array('kid' => $kid, 'gid' => (int)$PLUGIN_OPT['groupid']));
            }
            $CUST_OBJ_SQL = dao_class::get_data_first(TBL_CMS_CUST, array('kid' => $kid));

            $link = SSLSERVER . 'index.php?cmd=actnews&page=7&sec=' . $kid . '&hash=' . sha1($kid . $CUST_OBJ_SQL['passwort']);
            $ear = get_email_template(900);
            $mail = array(
                'link' => $link,
                'FORM' => $FORM,
                'domain' => $_SERVER['HTTP_HOST'],
                'content' => $PLUGIN_OPT['mailtext'] . PHP_EOL . PHP_EOL . (($ear['add_adress'] == 1) ? $this->gbl_config['email_absender'] : ''),
                'subject' => $PLUGIN_OPT['mailsubject']);

            $ear['content'] = $mail['content'];
            $ear['subject'] = $mail['subject'];

            send_mail_to(replacer($ear, $kid, array('mail' => $mail)));
            $this->msg($PLUGIN_OPT['okmsg']);
            HEADER("location: " . $_SERVER['PHP_SELF'] . "?page=" . $_POST['page'] . "&section=done");
            $this->hard_exit();
        }
    }

    /**
     * newssub_class::cmd_actnews()
     * 
     * @return void
     */
    function cmd_actnews() {
        if (isset($_GET['sec']) && isset($_GET['hash']) && $_GET['sec'] > 0) {
            $kid = (int)$_GET['sec'];
            $k_obj = dao_class::get_data_first(TBL_CMS_CUST, array('kid' => $kid));
            $hash = sha1($kid . $k_obj['passwort']);
            if ($hash == $_GET['hash']) {
                dao_class::update_table(TBL_CMS_CUST, array('mailactive' => 1, 'news_confirmed' => time()), array('kid' => $kid));
                $this->LOGCLASS->addLog('UPDATE', 'newsletter activation ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
                $this->msg('Newsletter aktiviert');
                HEADER('location:' . self::get_domain_url());
                exit;
            }
            else {
                $this->LOGCLASS->addLog('FAILURE', 'newsletter activation fails: ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
                firewall_class::report_hacking('newsletter activation by mail hacking');
                $this->msge('newsletter not activated');
                HEADER('location:' . self::get_domain_url());
                exit;
            }
        }
    }

    /**
     * newssub_class::cmd_remove()
     * 
     * @return
     */
    function cmd_remove() {
        $FORM = $_POST['FORM'];
        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            $this->msge("invalid token.");
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        }
        if (!validate_email_input($tschapura)) {
            $this->msge("{LBL_EMAIL}");
        }

        if (count($_SESSION['err_msgs']) == 0) {
            $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive=0 WHERE email='" . $FORM['tschapura'] . "' LIMIT 1");
            $this->msg("{LBL_SUCCESSFULLY} {LBL_UNSUBSCRIBED}");
            HEADER("location: " . $_SERVER['PHP_SELF'] . "?page=" . $_POST['page'] . "&section=done");
            exit;
        }

    }

}
