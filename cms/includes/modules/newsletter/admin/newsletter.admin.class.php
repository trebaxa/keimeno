<?php

/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');


class newsletter_admin_class extends newsletter_master_class {
    /**
     * newsletter_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * newsletter_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        #$this->NEWSLETTER['not_finished_newsletter'] = get_data_count(TBL_CMS_CUST, 'kid', "mailsend=0") > 0;
        if ($this->smarty->getTemplateVars('NEWSLETTER') != NULL) {
            $this->NEWSLETTER = array_merge($this->smarty->getTemplateVars('NEWSLETTER'), $this->NEWSLETTER);
            $this->smarty->clearAssign('NEWSLETTER');
        }
        $this->smarty->assign('NEWSLETTER', $this->NEWSLETTER);
    }

    /**
     * newsletter_admin_class::cmd_save_group()
     * 
     * @return void
     */
    function cmd_save_group() {
        if ($_POST['id'] == 0) {
            insert_table(TBL_CMS_NEWSLETTERGROUPS, $_POST['FORM']);
        }
        else {
            update_table(TBL_CMS_NEWSLETTERGROUPS, 'id', $_POST['id'], $_POST['FORM']);
        }
        $this->ej('reload_list');
    }

    /**
     * newsletter_admin_class::cmd_load_lists()
     * 
     * @return void
     */
    function cmd_load_lists() {
        $this->NEWSLETTER['FORM'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSLETTERGROUPS . " WHERE id='" . (int)$_GET['id'] . "' LIMIT 1");
        $this->NEWSLETTER['ngroups'] = array();
        $result = $this->db->query("SELECT G.*,count(E.email) AS ECOUNT FROM
	" . TBL_CMS_NEWSLETTERGROUPS . " G LEFT JOIN " . TBL_CMS_NEWSLETTEREMAILS . " E ON (G.id=E.gid)
	WHERE 1
	GROUP BY G.id
	ORDER BY G.group_name");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['id'] > 1) {
                $row['icons']['del'] = kf::gen_del_icon($row['id'], true, 'group_delete');
                $row['icons']['edit'] = kf::gen_edit_icon($row['id'], '', 'group_edit');
            }
            $row['icons']['add'] = kf::gen_std_icon($row['id'], 'fa-plus', 'E-Mails hinzuf&uuml;gen', 'id', 'add_mails', '&gid=' . $row['id'], $_SERVER['PHP_SELF']);
            $row['icons']['list'] = kf::gen_std_icon($row['id'], 'fa-list-alt', 'Inhalt anzeigen', 'id', 'listmails', '', $_SERVER['PHP_SELF']);
            $this->NEWSLETTER['ngroups'][] = $row;
        }
    }

    /**
     * newsletter_admin_class::cmd_group_delete()
     * 
     * @return void
     */
    function cmd_group_delete() {
        if ($_GET['ident'] > 1) {
            $this->db->query("DELETE FROM " . TBL_CMS_NEWSLETTERGROUPS . " WHERE id=" . $_GET['ident'] . ' LIMIT 1');
            $this->db->query("DELETE FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=" . $_GET['ident'] . '');
        }
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_reload_list()
     * 
     * @return void
     */
    function cmd_reload_list() {
        $this->cmd_load_lists();
        $this->parse_to_smarty();
        kf::echo_template('newsletter.lists');
    }

    /**
     * newsletter_admin_class::cmd_group_edit()
     * 
     * @return void
     */
    function cmd_group_edit() {
        $this->NEWSLETTER['FORM'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSLETTERGROUPS . " WHERE id='" . (int)$_GET['id'] . "' LIMIT 1");
    }

    /**
     * newsletter_admin_class::cmd_add_mails()
     * 
     * @return void
     */
    function cmd_add_mails() {
        $this->NEWSLETTER['ngroups_select'] = build_html_selectbox("gid", TBL_CMS_NEWSLETTERGROUPS, "id", "group_name", '', $_GET['gid']);
        $_GET['sign'] = (($_GET['sign'] == "") ? ";" : $_GET['sign']);
    }

    /**
     * newsletter_admin_class::cmd_email_import()
     * 
     * @return
     */
    function cmd_email_import() {
        $local_fname = CMS_ROOT . 'admin/cache/' . self::format_file_name($_FILES['datei']['name']);
        if (!validate_upload_file($_FILES['datei'])) {
            $this->TCR->set_just_turn_back(true);
            $this->TCR->add_msge($_SESSION['upload_msge']);
            return;
        }
        move_uploaded_file($_FILES['datei']['tmp_name'], $local_fname);
        chmod($local_fname, 0755);
        $fcontent = file($local_fname);

        if (is_array($fcontent)) {
            $fcontent = self::arr_trim($fcontent);
            $emails = $emails_arr = array();
            $emails = ($_POST['pro_zeile'] == 1) ? $fcontent : explode($_POST['sign'], $fcontent);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=" . $_POST['gid']);
            while ($row = $this->db->fetch_array_names($result)) {
                $emails_arr[] = $row['email'];
            }
            $emails = array_unique($emails);

            if (count($emails) > 0 && is_array($emails)) {

                foreach ($emails as $email) {
                    $FORM = array();
                    $FORM['email'] = trim($email);
                    $FORM['add_time'] = time();
                    $FORM['gid'] = $_POST['gid'];
                    if (!in_array($FORM['email'], $emails_arr) && validate_email_input($FORM['email'])) {
                        insert_table(TBL_CMS_NEWSLETTEREMAILS, $FORM);
                        $addit++;
                    }
                }
            }
            if (file_exists($local_fname)) {
                @unlink($local_fname);
            }
            $this->TCR->set_just_turn_back(true);
            $this->TCR->add_msg($addit . '{LBLA_SAVED}');
        }
        else {
            $this->TCR->set_just_turn_back(true);
            $this->TCR->add_msg('Keine Emails in Datei');
        }
    }

    /**
     * newsletter_admin_class::cmd_email_import_man()
     * 
     * @return void
     */
    function cmd_email_import_man() {
        $emails = explode(PHP_EOL, $_POST['FORM']['emails']);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=" . $_POST['gid']);
        while ($row = $this->db->fetch_array_names($result)) {
            $emails_arr[] = $row['email'];
        }
        if (count($emails) > 0 && is_array($emails)) {
            foreach ($emails as $email) {
                $arr = array(
                    'email' => trim($email),
                    'add_time' => time(),
                    'gid' => $_POST['gid'],
                    );
                if (!in_array($arr['email'], $emails_arr) && validate_email_input($arr['email'])) {
                    insert_table(TBL_CMS_NEWSLETTEREMAILS, $arr);
                    $addit++;
                }
            }
        }
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_listmails()
     * 
     * @return void
     */
    function cmd_listmails() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=" . (int)$_GET['id'] . " ORDER BY email");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'delmail');
            $this->NEWSLETTER['emailliste'][] = $row;
        }
        $this->NEWSLETTER['group'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSLETTERGROUPS . " WHERE id='" . (int)$_GET['id'] . "' LIMIT 1");
    }

    /**
     * newsletter_admin_class::cmd_delmail()
     * 
     * @return void
     */
    function cmd_delmail() {
        dao_class::db_delete(TBL_CMS_NEWSLETTEREMAILS, array('id' => $_GET['ident']));
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_show_hist()
     * 
     * @return void
     */
    function cmd_show_hist() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_EMAILER . " ORDER BY e_timeint DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id']);
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'delete_newsletter');
            $row['e_date'] = my_date("d.M Y", $row['e_date']);
            $this->NEWSLETTER['newsletter_table'][] = $row;

        }
    }

    /**
     * newsletter_admin_class::cmd_a_deac_news()
     * 
     * @return void
     */
    function cmd_a_deac_news() {
        $_POST['emails'] = explode("\n", $_POST['emails']);
        if (count($_POST['emails']) > 0) {
            foreach ($_POST['emails'] as $email) {
                $email = trim($email);
                if (validate_email_input($email) === true) {
                    $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive='0' WHERE email='" . $email . "'");
                }
            }
        }
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_edit()
     * 
     * @return void
     */
    function cmd_edit() {
        $id = 0;
        if (isset($_REQUEST['id']))
            $id = (int)$_REQUEST['id'];
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $id . "' LIMIT 1");
        if (strlen($FORM['e_unsubscribe']) == 0) {
            $FORM['e_unsubscribe'] = 'Unsubscribe from this mailing';
        }
        if (strlen($FORM['e_anrede_m']) == 0) {
            $FORM['e_anrede_m'] = 'Sehr geehrter Herr';
        }
        if (strlen($FORM['e_anrede_w']) == 0) {
            $FORM['e_anrede_w'] = 'Sehr geehrte Frau';
        }

        $this->NEWSLETTER['htmleditor'] = create_html_editor('FORM[e_content]', ($FORM['e_content']), 600, 'Fullpage', 1000, true, 'newsletteditor', array('remove_script_host' =>
                'false'));
        $this->NEWSLETTER['newsedit'] = $FORM;
        $this->NEWSLETTER['attachments'] = (array )$this->load_attachments($FORM);
    }


    /**
     * newsletter_admin_class::cmd_a_save()
     * 
     * @return void
     */
    function cmd_a_save() {
        $FORM = (array )$_POST['FORM'];
        $FORM['e_html'] = intval($FORM['e_html']);
        $FORM['e_content'] = str_replace(' src=\"/', ' src=\"' . self::get_domain_url(), $FORM['e_content']);
        $FORM['e_content'] = str_replace(' href=\"/', ' href=\"' . self::get_domain_url(), $FORM['e_content']);
        #   echoarr($FORM);die;
        update_table(TBL_CMS_EMAILER, "id", $_POST['id'], $FORM);
        if ($_FILES['attfile']['name'] != "") {
            $f_name = $_FILES['attfile']['name'];
            if (file_exists(NEWS_FOLDER . $f_name)) {
                @unlink(NEWS_FOLDER . $f_name);
            }
            $_POST['target_file'] = $this->format_file_name($_POST['target_file']);
            if (!validate_upload_file($_FILES['attfile'])) {
                $this->msge($_SESSION['upload_msge']);
                header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&aktion=a_showpics');
                exit;
            }
            if (move_uploaded_file($_FILES['attfile']['tmp_name'], NEWS_FOLDER . $f_name)) {
                chmod(NEWS_FOLDER . $f_name, 0755);
                $msg .= "Attachment gespeichert. " . $f_name;
                $ATT_FORM = $this->db->query_first("SELECT attachments FROM " . TBL_CMS_EMAILER . " WHERE id=" . $_POST['id']);
                $ATT_FORM['attachments'] = unserialize($ATT_FORM['attachments']);
                $ATT_FORM['attachments'][] = $f_name;
                $ATT_FORM['attachments'] = serialize($ATT_FORM['attachments']);
                update_table(TBL_CMS_EMAILER, "id", $_POST['id'], $ATT_FORM);
            }
        }
        $this->msg('saved. ' . $msg);
        $this->ej('reloadfiles');
    }

    /**
     * newsletter_admin_class::cmd_reloadfiles()
     * 
     * @return void
     */
    function cmd_reloadfiles() {
        $E_OBJ = $this->db->query_first("SELECT E.*,G.groupname,G.id AS GID FROM " . TBL_CMS_EMAILER . " E, " . TBL_CMS_RGROUPS . " G WHERE G.id=E.groups AND E.id='" .
            $_GET['id'] . "' LIMIT 1");
        $this->NEWSLETTER['attachments'] = (array )$this->load_attachments($E_OBJ);
        $this->parse_to_smarty();
        kf::echo_template('newsletter.files');
    }

    /**
     * newsletter_admin_class::cmd_news_confirm()
     * 
     * @return void
     */
    function cmd_news_confirm() {
        $active_count = 0;
        $FORM = (array )$_POST['FORM'];
        $FORM['e_egroups'] = serialize(array());
        if (isset($_POST['GROUPS']) && is_array($_POST['GROUPS'])) {
            foreach ($_POST['GROUPS'] as $gid) {
                $active_count += dao_class::get_count(TBL_CMS_NEWSLETTEREMAILS, array('gid' => $gid));
            }
            $FORM['e_egroups'] = serialize($_POST['GROUPS']);
        }


        update_table(TBL_CMS_EMAILER, "id", $_POST['id'], $FORM);
        $E_OBJ = $this->db->query_first("SELECT E.*,G.groupname,G.id AS GID FROM " . TBL_CMS_EMAILER . " E, " . TBL_CMS_RGROUPS . " G WHERE G.id=E.groups AND E.id='" .
            $_POST['id'] . "' LIMIT 1");

        $result = $this->db->query("SELECT count(C.kid) FROM " . TBL_CMS_CUST . " C, " . TBL_CMS_CUSTTOGROUP .
            " G WHERE (C.email!='' OR email_notpublic!='') AND G.kid=C.kid AND G.gid=" . $E_OBJ['GID']);
        while ($row = $this->db->fetch_array($result)) {
            $active_count += $row[0];
        }


        $this->NEWSLETTER['attachments'] = (array )$this->load_attachments($E_OBJ);
        $this->NEWSLETTER['newsedit'] = $E_OBJ;
        $this->NEWSLETTER['previewlink'] = './newsletter/' . TMP_FILE . md5(session_id()) . $_POST['id'] . '.html?a=' . gen_sid(8);
        $this->NEWSLETTER['active_count'] = $active_count;
        $this->NEWSLETTER['format'] = ($E_OBJ['e_html'] ? "HTML Format" : "Text Format");
    }

    /**
     * newsletter_admin_class::cmd_show_send()
     * 
     * @return void
     */
    function cmd_show_send() {
        $E_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_POST['id'] . "' LIMIT 1");
        if (!strstr($E_OBJ['e_content'], '!!DISABLE_LETTER_LINK!!')) {
            $this->NEWSLETTER['errors'][] = '{LBLA_NODISABLELINK}<br>';
        }
        if (!strstr($E_OBJ['e_content'], '!!TRACKING_CODE!!')) {
            $this->NEWSLETTER['errors'][] = '{LBLA_NOTRACKINGCODE}<br>';
        }
        if ($E_OBJ['e_html'] == 1) {
            if (!strstr(strtolower($E_OBJ['e_content']), '<body>'))
                $this->NEWSLETTER['errors'][] = htmlspecialchars('<body>') . ' Tag {LBLA_NOFOUND} in {LBLA_CONTENT}.<br>';
            if (!strstr(strtolower($E_OBJ['e_content']), '<html'))
                $this->NEWSLETTER['errors'][] = htmlspecialchars('<html>') . ' Tag {LBLA_NOFOUND} in {LBLA_CONTENT}.<br>';
        }
        if (strlen($E_OBJ['e_subject']) == 0) {
            $this->NEWSLETTER['errors_critical'][] = '{LBLA_NOEMPTY} {LBLA_SUBJECT} {LBLA_SAVED}.<br>';
        }
        if (strlen($E_OBJ['e_content']) == 0) {
            $this->NEWSLETTER['errors_critical'][] = '{LBLA_NOEMPTY} {LBLA_CONTENT} {LBLA_SAVED}.<br>';
        }
        if (strlen($E_OBJ['e_anrede_m']) == 0) {
            $this->NEWSLETTER['errors_critical'][] = '{LBLA_NOEMPTY} {LBLA_SALUTATION_MALE} {LBLA_SAVED}.<br>';
        }
        if (strlen($E_OBJ['e_anrede_w']) == 0) {
            $this->NEWSLETTER['errors_critical'][] = '{LBLA_NOEMPTY} {LBLA_SALUTATION_FEMALE} {LBLA_SAVED}.<br>';
        }


        if ($err_critical == "") {

            $result = $this->db->query("SELECT G.*,COUNT(GC.kid) AS KCOUNT FROM " . TBL_CMS_RGROUPS . " G
  	LEFT JOIN " . TBL_CMS_CUSTTOGROUP . " GC ON (GC.gid=G.id) 
  	GROUP BY G.id 
  	ORDER BY G.groupname");
            while ($row = $this->db->fetch_array_names($result)) {
                $this->NEWSLETTER['groupopt'][] = '<option ' . (($_POST['FORM']['groups'] == $row['id']) ? "selected" : "") . 'value="' . $row['id'] . '">' . $row['groupname'] .
                    ' [' . $row['KCOUNT'] . ']</option>';
            }

        }

        $this->NEWSLETTER['newsedit'] = $E_OBJ;

        $this->NEWSLETTER['groups'] = $this->load_groups();
    }


    /**
     * newsletter_admin_class::cmd_members()
     * 
     * @return void
     */
    function cmd_members() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " ORDER BY nachname,vorname,firma,email");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['iocns'][] = kf::gen_approve_icon($row['kid'], $row['mailactive']);
            $this->NEWSLETTER['members'][] = $row;
        }
    }

    /**
     * newsletter_admin_class::cmd_a_tracking()
     * 
     * @return void
     */
    function cmd_a_tracking() {
        $E_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_GET['id'] . "' LIMIT 1");
        $tracked_emails = $no_feedback = array();
        if ($E_OBJ['tracking'] != "") {
            $tracked_emails = explode("!", $E_OBJ['tracking']);
        }
        $send_emails = explode("!", $E_OBJ['send_emails']);
        sort($tracked_emails);
        if (count($send_emails) > 0) {
            foreach ($send_emails as $key => $semail) {
                $all[$semail]['email'] = $semail;
                if (!in_array($semail, $tracked_emails)) {
                    $no_feedback[] = $semail;
                    $all[$semail]['readed'] = 0;
                }
                else {
                    $all[$semail]['readed']++;
                }

            }
        }
        $tracked_count_arr = array_count_values($tracked_emails);

        $this->NEWSLETTER['all_feedback'] = $all;
        array_count_values(array_merge($tracked_emails, $no_feedback));
        #  echoarr($this->NEWSLETTER['all_feedback']);
        $this->NEWSLETTER['ok_feedback'] = $tracked_count_arr;
        $this->NEWSLETTER['no_feedback'] = $no_feedback;
        $this->NEWSLETTER['ok_feedback_count'] = count($tracked_count_arr);
        $this->NEWSLETTER['all_feedback_count'] = count($this->NEWSLETTER['all_feedback']);
        $this->NEWSLETTER['no_feedback_count'] = count($no_feedback);
    }

    /**
     * newsletter_admin_class::cmd_a_delatt()
     * 
     * @return void
     */
    function cmd_a_delatt() {
        $file = NEWS_FOLDER . $_GET['ident'];
        if (file_exists($file)) {
            @unlink($file);
        }
        $FORM = array();
        $ATT_FORM = $this->db->query_first("SELECT attachments FROM " . TBL_CMS_EMAILER . " WHERE id=" . $_GET['id']);
        $afiles = unserialize($ATT_FORM['attachments']);
        $afiles = (array )$afiles;
        $FORM['attachments'] = array();
        if (count($afiles) > 0) {
            foreach ((array )$afiles as $key => $afile) {
                if ($afile != $file) {
                    $FORM['attachments'][] = $afile;
                }
            }
        }
        update_table(TBL_CMS_EMAILER, "id", $_GET['id'], $FORM);

        $this->msg('{LBL_DELETED}');
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_preview()
     * 
     * @return void
     */
    function cmd_preview() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_REQUEST['id'] . "' LIMIT 1");
        $dh = opendir(CMS_ROOT . 'admin/newsletter');
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..' && strstr($filename, ".html")) {
                if (file_exists(NEWS_FOLDER . $filename))
                    unlink(NEWS_FOLDER . $filename);
            }
        }
        $FORM['e_content'] = stripslashes(str_replace('\"', '"', utf8_decode($FORM['e_content'])));
        $preview = NEWS_FOLDER . TMP_FILE . md5(session_id()) . $_REQUEST['id'] . '.html';
        if ($FORM['e_html'] == 0) {
            $FORM['e_content'] = nl2br($FORM['e_content']);
        }
        file_put_contents($preview, $FORM['e_content']);
        $this->NEWSLETTER['preview_link'] = './newsletter/' . basename($preview) . '?a=' . gen_sid(8);
    }

    /**
     * newsletter_admin_class::cmd_send_test_email()
     * 
     * @return void
     */
    function cmd_send_test_email() {
        if (!validate_email_input($_POST['testemail'])) {
            $this->msge('Falsche Email: ' . $_POST['testemail']);
            $this->ej();
        }
        $E_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_POST['id'] . "' LIMIT 1");
        $K_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . FM_EMAIL . "'");
        $K_OBJ['nachname'] = 'TESTUSER';
        $SendSuccess = $this->sendNewsToEmail($_POST['testemail'], $this->gbl_config['news_senderemail'], $E_OBJ, $K_OBJ);
        $this->LOGCLASS->addLog('SENDMAIL', 'newsletter test email');
        $this->msg('Test Email an ' . $_POST['testemail'] . ' gesendet.');
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_xlsexport()
     * 
     * @return void
     */
    function cmd_xlsexport() {
        $E_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_GET['id'] . "' LIMIT 1");
        $send_mails = explode('!', $E_OBJ['send_emails']);
        $fname = CMS_ROOT . "admin/newsletter/newsletter_emails_" . $EOBJ['id'] . ".xls";
        $file = fopen($fname, "w");
        $subject = ereg_replace("[^[:space:]a-zA-Z0-9*_.-üäöAÖÜ&;,#]", "", strip_tags(utf8_decode($E_OBJ['e_subject'])));
        foreach ($send_mails as $email) {
            fwrite($file, $email . "\t" . date('d.m.Y') . "\t" . date('H:i:s') . "\t" . $subject . "\n");
        }
        fclose($file);
        $this->direct_download($fname);
    }

    /**
     * newsletter_admin_class::cmd_axapprove_item()
     * 
     * @return void
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $_GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive='" . $_GET['value'] . "' WHERE kid=" . (int)$id);
        $this->hard_exit();
    }

    /**
     * newsletter_admin_class::cmd_clone()
     * 
     * @return void
     */
    function cmd_clone() {
        $LETTER = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id=" . (int)$_GET['id']);
        unset($LETTER['id']);
        $LETTER['e_date'] = date('Y-m-d');
        $LETTER['e_timeint'] = time();
        $LETTER['e_sendcount'] = 0;
        $LETTER['tracking'] = '';
        $LETTER['attachments'] = '';
        $LETTER['send_emails'] = '';
        $LETTER['e_subject'] = 'CLONE ' . $LETTER['e_subject'];
        $id = insert_table(TBL_CMS_EMAILER, $this->real_escape($LETTER));
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('epage=' . $_GET['epage'] . '&aktion=edit&id=' . $id);
    }

    /**
     * newsletter_admin_class::cmd_add_letter()
     * 
     * @return void
     */
    function cmd_add_letter() {
        $FORM = (array )$_POST['FORM'];
        $FORM['e_html'] = intval($FORM['e_html']);
        $FORM['e_date'] = date('Y-m-d');
        $FORM['e_time'] = date('h:i:s');
        $FORM['e_timeint'] = time();
        $id = insert_table(TBL_CMS_EMAILER, $FORM);
        $this->LOGCLASS->addLog('INSERT', 'new newsletter [ID:' . $id . ']');

        if ($_FILES['attfile']['name'] != "") {
            $f_name = $_FILES['attfile']['name'];
            if (file_exists(NEWS_FOLDER . $f_name))
                unlink(NEWS_FOLDER . $f_name);
            $_POST['target_file'] = $this->format_file_name($_POST['target_file']);
            if (!validate_upload_file($_FILES['attfile'])) {
                $this->msge($_SESSION['upload_msge']);
                header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&id=' . $id . '&aktion=edit');
                $this->hard_exit();
            }
            if (move_uploaded_file($_FILES['attfile']['tmp_name'], NEWS_FOLDER . $f_name)) {
                chmod(NEWS_FOLDER . $f_name, 0755);
                $ATT_FORM = $this->db->query_first("SELECT attachments FROM " . TBL_CMS_EMAILER . " WHERE id=" . $id);
                $ATT_FORM['attachments'] = unserialize($ATT_FORM['attachments']);
                $ATT_FORM['attachments'][] = $f_name;
                $ATT_FORM['attachments'] = serialize($ATT_FORM['attachments']);
                update_table(TBL_CMS_EMAILER, "id", $id, $ATT_FORM);
            }
        }
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('epage=' . $_POST['epage'] . '&aktion=edit&id=' . $id);
    }

    /**
     * newsletter_admin_class::cmd_add_to_newsletter()
     * 
     * @return void
     */
    function cmd_add_to_newsletter() {
        $emails_arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=1");
        while ($row = $this->db->fetch_array_names($result)) {
            $emails_arr[] = $row['email'];
        }
        $IE = $_POST['FORM'];
        $IE['add_time'] = time();
        $IE['gid'] = $_POST['gid'];
        if (!in_array($IE['email'], $emails_arr) && validate_email_input($IE['email'])) {
            insert_table(TBL_CMS_NEWSLETTEREMAILS, $IE);
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }


    /**
     * newsletter_admin_class::cmd_delete_newsletter()
     * 
     * @return void
     */
    function cmd_delete_newsletter() {
        $id = (int)$_GET['ident'];
        $ATT_FORM = $this->db->query_first("SELECT attachments FROM " . TBL_CMS_EMAILER . " WHERE id=" . $id);
        $afiles = unserialize($ATT_FORM['attachments']);
        if (is_array($afiles) && count($afiles) > 0) {
            foreach ((array )$afiles as $key => $afile)
                if (file_exists($afile)) {
                    unlink(NEWS_FOLDER . $afile);
                }
        }
        $tmp_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $id . "' LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_EMAILER . " WHERE id='" . $id . "' LIMIT 1");
        $this->LOGCLASS->addLog('DELETE', 'newsletter ' . $tmp_obj['e_subject']);
        $this->ej();
    }

    /**
     * newsletter_admin_class::cmd_setframework()
     * 
     * @return void
     */
    function cmd_setframework() {
        $FORM = array();
        $FORM['e_content'] = $this->db->real_escape_string(get_template(560)); // Newsletter Framework
        if ($_GET['id'] > 0) {
            update_table(TBL_CMS_EMAILER, "id", $_GET['id'], $FORM);
        }
        else {
            $_GET['id'] = insert_table(TBL_CMS_EMAILER, $FORM);
        }
        $this->msg('{LBLA_SAVED}');
        $this->TCR->tb();
    }

    /**
     * newsletter_admin_class::cmd_a_reset()
     * 
     * @return void
     */
    function cmd_a_reset() {
        $dh = opendir('./newsletter');
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..' && strstr($filename, ".html")) {
                if (file_exists(NEWS_FOLDER . $filename))
                    unlink(NEWS_FOLDER . $filename);
            }
        }
        $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailsend=1");
        $this->LOGCLASS->addLog('RESET', 'newsletter status');
        $this->msg("Newsletter reseted.");
        $this->ej('remove_newswarn');
    }
}
