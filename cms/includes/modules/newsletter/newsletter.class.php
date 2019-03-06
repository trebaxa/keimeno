<?php

/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TMP_FILE', 'news_preview_');
DEFINE('NEWS_FOLDER', CMS_ROOT . 'admin/newsletter/');
DEFINE('TBL_CMS_NEWSLETTERGROUPS', TBL_CMS_PREFIX . 'email_groups');
DEFINE('TBL_CMS_NEWSLETTEREMAILS', TBL_CMS_PREFIX . 'news_emails');


class newsletter_class extends keimeno_class {

    var $NEWSLETTER = array();

    /**
     * newsletter_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        if (!is_dir(NEWS_FOLDER))
            mkdir(NEWS_FOLDER, 0755);
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * newsletter_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->NEWSLETTER['not_finished_newsletter'] = get_data_count(TBL_CMS_CUST, 'kid', "mailsend=0") > 0;
        $this->smarty->assign('NEWSLETTER', $this->NEWSLETTER);
    }

    /**
     * newsletter_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive='" . $this->TCR->GET['value'] . "' WHERE kid=" . (int)$id);
        $this->hard_exit();
    }

    /**
     * newsletter_class::cmd_clone()
     * 
     * @return
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
     * newsletter_class::cmd_add_letter()
     * 
     * @return
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
     * newsletter_class::cmd_a_deac_news()
     * 
     * @return
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
        $this->msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * newsletter_class::cmd_add_to_newsletter()
     * 
     * @return
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
     * newsletter_class::cmd_group_delete()
     * 
     * @return
     */
    function cmd_group_delete() {
        if ($_GET['id'] > 1) {
            $this->db->query("DELETE FROM " . TBL_CMS_NEWSLETTERGROUPS . " WHERE id=" . $_GET['id'] . ' LIMIT 1');
            $this->db->query("DELETE FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=" . $_GET['id'] . '');
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBL_DELETED}');
    }

    /**
     * newsletter_class::cmd_group_edit()
     * 
     * @return
     */
    function cmd_group_edit() {
        $this->NEWSLETTER['FORM'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSLETTERGROUPS . " WHERE id='" . (int)$_GET['id'] . "' LIMIT 1");
    }

    /**
     * newsletter_class::cmd_save_group()
     * 
     * @return
     */
    function cmd_save_group() {
        if ($_POST['id'] == 0) {
            insert_table(TBL_CMS_NEWSLETTERGROUPS, $_POST['FORM']);
        }
        else {
            update_table(TBL_CMS_NEWSLETTERGROUPS, 'id', $_POST['id'], $_POST['FORM']);
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * newsletter_class::cmd_listmails()
     * 
     * @return
     */
    function cmd_listmails() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTEREMAILS . " WHERE gid=" . $_GET['id'] . " ORDER BY email");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->NEWSLETTER['emailliste'][] = $row;
        }
    }

    /**
     * newsletter_class::cmd_add_mails()
     * 
     * @return
     */
    function cmd_add_mails() {
        $this->NEWSLETTER['ngroups_select'] = build_html_selectbox("gid", TBL_CMS_NEWSLETTERGROUPS, "id", "group_name", '', $_GET['gid']);
        $_GET['sign'] = (($_GET['sign'] == "") ? ";" : $_GET['sign']);
    }

    /**
     * newsletter_class::cmd_email_import()
     * 
     * @return
     */
    function cmd_email_import() {
        $local_fname = CMS_ROOT . 'admin/cache/' . $_FILES['datei']['name'];
        if (!validate_upload_file($_FILES['datei'])) {
            $this->TCR->set_just_turn_back(true);
            $this->TCR->add_msge($_SESSION['upload_msge']);
            return;
        }
        move_uploaded_file($_FILES['datei']['tmp_name'], $local_fname);
        chmod($local_fname, 0755);
        $fcontent = file($local_fname);
        if (is_array($fcontent)) {
            $emails = $emails_arr = array();
            $emails = ($_POST['pro_zeile'] == 1) ? $fcontent : explode($_POST['sign'], $fcontent);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTEREMAILS);
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
            if (file_exists($local_fname))
                unlink($local_fname);
            $this->TCR->set_just_turn_back(true);
            $this->TCR->add_msg('{LBLA_SAVED}');
        }
        else {
            $this->TCR->set_just_turn_back(true);
            $this->TCR->add_msg('Keine Emails in Datei');
        }
    }

    /**
     * newsletter_class::cmd_load_lists()
     * 
     * @return
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
                $row['icons']['del'] = kf::gen_del_icon_reload($row['id'], 'group_delete');
                $row['icons']['edit'] = kf::gen_edit_icon($row['id'], '', 'group_edit');
            }
            $row['icons']['add'] = kf::gen_std_icon($row['id'], 'fa-plus', 'Kunde hinzuf&uuml;gen', 'id', 'add_mails', '', $_SERVER['PHP_SELF']);
            $row['icons']['list'] = kf::gen_std_icon($row['id'], 'fa-list-alt', 'Inhalt anzeigen', 'id', 'listmails', '', $_SERVER['PHP_SELF']);
            $this->NEWSLETTER['ngroups'][] = $row;
        }
    }

    /**
     * newsletter_class::cmd_delete_newsletter()
     * 
     * @return
     */
    function cmd_delete_newsletter() {
        $id = (int)$this->TCR->GET['ident'];
        $ATT_FORM = $this->db->query_first("SELECT attachments FROM " . TBL_CMS_EMAILER . " WHERE id=" . $id);
        $afiles = unserialize($ATT_FORM['attachments']);
        if (count($afiles) > 0) {
            foreach ((array )$afiles as $key => $afile)
                if (file_exists($afile))
                    unlink(NEWS_FOLDER . $afile);
        }
        $tmp_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $id . "' LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_EMAILER . " WHERE id='" . $id . "' LIMIT 1");
        $this->LOGCLASS->addLog('DELETE', 'newsletter ' . $tmp_obj['e_subject']);
        $this->ej();
    }

    /**
     * newsletter_class::cmd_setframework()
     * 
     * @return
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
     * newsletter_class::cmd_a_reset()
     * 
     * @return
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

    /**
     * newsletter_class::cmd_show_hist()
     * 
     * @return
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
     * newsletter_class::replace_news()
     * 
     * @param mixed $content
     * @param mixed $user_obj
     * @param mixed $e_obj
     * @return
     */
    function replace_news($content, $user_obj, $e_obj) {
        $content = str_replace("!!EMAIL!!", $user_obj['email'], $content);
        if ($user_obj['geschlecht'] == 'm')
            $anrede = trim($e_obj['e_anrede_m']) . ' ' . $user_obj['nachname'];
        else
            $anrede = trim($e_obj['e_anrede_w']) . ' ' . $user_obj['nachname'];
        $user_obj['email'] = (!validate_email_input($user_obj['email_notpublic'])) ? $user_obj['email'] : $user_obj['email_notpublic'];
        $content = str_replace("!!CUSTOMER_LASTNAME!!", $user_obj['nachname'], $content);
        $content = str_replace("!!HELLO!!", $anrede, $content);
        if ($e_obj['e_html'] == 1) {
            $content = str_replace("[BR]", "<br>", $content);
            $content = str_replace("!!DISABLE_LETTER_LINK!!", '<a target="_blank" href="http://www.' . FM_DOMAIN . PATH_CMS .
                'includes/modules/newsletter/newsletter.inc.php?cmd=a_edisable&group=' . $user_obj['kid'] . '&n=' . md5($user_obj['email']) . '">' . $e_obj['e_unsubscribe'] .
                '</a>', $content);
            $content = str_replace("!!TRACKING_CODE!!", '<img style="display:none" width="0" height="0" src="http://www.' . FM_DOMAIN . PATH_CMS .
                'index.php?page=9910&cmd=recordn&id=' . $e_obj['id'] . '&n=' . base64_encode($user_obj['email']) . '" >', $content);
            $content = str_replace("</body>", "<!-- IP " . getenv('REMOTE_ADDR') . " --></body>", $content);
        }
        else {
            $content = str_replace("!!DISABLE_LETTER_LINK!!", 'http://www.' . FM_DOMAIN . PATH_CMS . 'index.php?page=9910&cmd=a_edisable&group=' . $user_obj['kid'] . '&n=' .
                md5($user_obj['email']), $content);
            $content = str_replace("[BR]", "\n", $content);
            $content = str_replace("!!TRACKING_CODE!!", '', $content);
        }
        foreach ($user_obj as $key => $value) {
            $content = str_replace("!!" . strtoupper($key) . "!!", $value, $content);
        }

        return $content;
    }

    /**
     * newsletter_class::cmd_xlsexport()
     * 
     * @return
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
     * newsletter_class::sendNewsToEmail()
     * 
     * @param mixed $sendto
     * @param mixed $sendfrom
     * @param mixed $EOBJ
     * @param mixed $CUSTOBJ
     * @return
     */
    function sendNewsToEmail($sendto, $sendfrom, $EOBJ, $CUSTOBJ) {
        $newsletter_content = $this->replace_news($EOBJ['e_content'], $CUSTOBJ, $EOBJ);
        $msg = new Email($sendto, $sendfrom, utf8_decode($EOBJ['e_subject']));
        $msg->Cc = "";
        $msg->Bcc = "";
        $msg->TextOnly = $EOBJ['e_html'] == 1 ? false : true;
        $msg->Content = utf8_decode($newsletter_content);
        $afiles = unserialize($EOBJ['attachments']);
        if (count($afiles) > 0) {
            foreach ((array )$afiles as $key => $afile) {
                if (file_exists(NEWS_FOLDER . $afile) && $afile != "" && is_file(NEWS_FOLDER . $afile)) {
                    $msg->Attach(NEWS_FOLDER . $afile);
                }
            }
        }
        if (validate_email_input($sendto) && $EOBJ['e_subject'] != "" && $EOBJ['e_content'] != "")
            $SendSuccess = $msg->Send();
        return $SendSuccess;
    }


    /**
     * newsletter_class::cmd_a_edisable()
     * 
     * @return
     */
    function cmd_a_edisable() {
        $_GET['id'] = intval($_GET['id']);
        if ($_GET['group'] > 0) {
            $cuobj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$_GET['group'] . " LIMIT 1");
            if (md5($cuobj['email']) == $_GET['n'] && $gbl_config['newsletter_disable_unreg'] == 0) {
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive=0 WHERE email=" . (int)$_GET['group'] . " LIMIT 1");
                keimeno_class::msg('{NEWSL_DEACT_VALID}');
                HEADER("Location: index.html");
            }
            else {
                keimeno_class::msge('{NEWSL_DEACT_INVALID}');
                HEADER("Location: index.html");
            }
        }
        $this->hard_exit();
    }


    /**
     * newsletter_class::cmd_recordn()
     * 
     * @return
     */
    function cmd_recordn() {
        $im = @imagecreatetruecolor(1, 1);
        imagesavealpha($im, true);
        imagealphablending($im, false);
        $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $transparent);
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
        $_GET['id'] = (int)$_GET['id'];
        if ($_GET['id'] > 0) {
            $track_obj = $this->db->query_first("SELECT tracking FROM " . TBL_CMS_EMAILER . " WHERE id=" . $_GET['id'] . " LIMIT 1");
            $email = base64_decode($_GET['n']);
            if (validate_email_input($email)) {
                if ($track_obj['tracking'] != "")
                    $track_obj['tracking'] .= '!';
                $track_obj['tracking'] .= $email;
                update_table(TBL_CMS_EMAILER, "id", $_GET['id'], $track_obj);
            }
        }
        $this->hard_exit();
    }

    /**
     * newsletter_class::cmd_send_test_email()
     * 
     * @return
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
     * newsletter_class::cmd_preview()
     * 
     * @return
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
     * newsletter_class::load_attachments()
     * 
     * @param mixed $FORM
     * @return
     */
    function load_attachments($FORM) {
        $afiles = unserialize($FORM['attachments']);
        $afiles = (array )$afiles;
        if (count($afiles) > 0) {
            foreach ((array )$afiles as $key => $afile) {
                if (file_exists(NEWS_FOLDER . $afile) && is_file(NEWS_FOLDER . $afile)) {
                    $attachments[] = array(
                        'afile' => $afile,
                        'relativefile' => './admin/newsletter/' . basename($afile),
                        'bafile' => basename($afile),
                        'fs' => human_file_size(filesize(NEWS_FOLDER . $afile)),
                        'delicon' => kf::gen_del_icon(urlencode(basename($afile)), false, 'a_delatt', '', '&id=' . $FORM['id']));
                }
            }
        }
        return (array )$attachments;
    }

    /**
     * newsletter_class::cmd_edit()
     * 
     * @return
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

        $this->NEWSLETTER['htmleditor'] = create_html_editor('FORM[e_content]', ($FORM['e_content']), 600, 'Fullpage', 1000, true);
        $this->NEWSLETTER['newsedit'] = $FORM;
        $this->NEWSLETTER['attachments'] = (array )$this->load_attachments($FORM);
    }

    /**
     * newsletter_class::cmd_a_delatt()
     * 
     * @return
     */
    function cmd_a_delatt() {
        $file = NEWS_FOLDER . $_GET['ident'];
        if (file_exists($file)) {
            @unlink($file);
        }
        $FORM = array();
        $ATT_FORM = $this->db->query_first("SELECT attachments FROM " . TBL_CMS_EMAILER . " WHERE id=" . $_GET['id']);
        $afiles = unserialize($ATT_FORM['attachments']);
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
     * newsletter_class::cmd_a_save()
     * 
     * @return
     */
    function cmd_a_save() {
        $FORM = (array )$_POST['FORM'];
        $FORM['e_html'] = intval($FORM['e_html']);
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

    function cmd_reloadfiles() {
        $E_OBJ = $this->db->query_first("SELECT E.*,G.groupname,G.id AS GID FROM " . TBL_CMS_EMAILER . " E, " . TBL_CMS_RGROUPS . " G WHERE G.id=E.groups AND E.id='" .
            $_GET['id'] . "' LIMIT 1");
        $this->NEWSLETTER['attachments'] = (array )$this->load_attachments($E_OBJ);
        $this->parse_to_smarty();
        kf::echo_template('newsletter.files');
    }

    /**
     * newsletter_class::cmd_news_confirm()
     * 
     * @return
     */
    function cmd_news_confirm() {
        update_table(TBL_CMS_EMAILER, "id", $_POST['id'], $_POST['FORM']);
        $E_OBJ = $this->db->query_first("SELECT E.*,G.groupname,G.id AS GID FROM " . TBL_CMS_EMAILER . " E, " . TBL_CMS_RGROUPS . " G WHERE G.id=E.groups AND E.id='" .
            $_POST['id'] . "' LIMIT 1");
        $result = $this->db->query("SELECT count(C.kid) FROM " . TBL_CMS_CUST . " C, " . TBL_CMS_CUSTTOGROUP .
            " G WHERE (C.email!='' OR email_notpublic!='') AND G.kid=C.kid AND G.gid=" . $E_OBJ['GID']);
        while ($row = $this->db->fetch_array($result)) {
            $active_count = $row[0];
        }

        $this->NEWSLETTER['attachments'] = (array )$this->load_attachments($E_OBJ);
        $this->NEWSLETTER['newsedit'] = $E_OBJ;
        $this->NEWSLETTER['previewlink'] = './newsletter/' . TMP_FILE . md5(session_id()) . $_POST['id'] . '.html?a=' . gen_sid(8);
        $this->NEWSLETTER['active_count'] = $active_count;
        $this->NEWSLETTER['format'] = ($E_OBJ['e_html'] ? "HTML Format" : "Text Format");
    }

    /**
     * newsletter_class::cmd_show_send()
     * 
     * @return
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
    }

    /**
     * newsletter_class::cmd_members()
     * 
     * @return
     */
    function cmd_members() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " ORDER BY nachname,vorname,firma,email");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['iocns'][] = kf::gen_approve_icon($row['kid'], $row['mailactive']);
            $this->NEWSLETTER['members'][] = $row;
        }
    }

    /**
     * newsletter_class::cmd_a_tracking()
     * 
     * @return
     */
    function cmd_a_tracking() {
        $E_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_GET['id'] . "' LIMIT 1");
        $emails = explode("!", $E_OBJ['tracking']);
        $send_emails = explode("!", $E_OBJ['send_emails']);
        sort($emails);
        if (count($send_emails) > 0) {
            foreach ($send_emails as $key => $semail) {
                if (!in_array($semail, $emails))
                    $no_feedback[] = $semail;
            }
        }
        $count_arr = array_count_values($emails);
        $this->NEWSLETTER['all_feedback'] = array_count_values(array_merge((array )$count_arr, (array )$no_feedback));
        $this->NEWSLETTER['ok_feedback'] = $count_arr;
        $this->NEWSLETTER['no_feedback'] = $no_feedback;
        $this->NEWSLETTER['ok_feedback_count'] = count($count_arr);
        $this->NEWSLETTER['all_feedback_count'] = count($this->NEWSLETTER['all_feedback']);
        $this->NEWSLETTER['no_feedback_count'] = count($no_feedback);
    }

    /**
     * newsletter_class::cmd_remoteadd()
     * 
     * @return
     */
    function cmd_remoteadd() {
        $vorlage = get_template(580);
        if ($_GET['type'] == 'news') {
            $N_OBJ = $this->db->query_first("SELECT * FROM
		" . TBL_CMS_NEWSLIST . " NL
		LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
		WHERE NL.id=" . $_GET['id']);
            $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSCONTENT . " WHERE lang_id=" . $_GET['uselang'] . " AND nid='" . $_GET['id'] . "' LIMIT 1");
            $vorlage = str_replace('{TMPL_INHALT}', $FORM_CON['content'], $vorlage);
        }
        else {
            die();
        }
        $NEWSLETTER['e_date'] = date('Y-m-d');
        $NEWSLETTER['e_time'] = date('h:i:s');
        $NEWSLETTER['e_timeint'] = time();
        $NEWSLETTER['e_anrede_m'] = 'Sehr geehrter Herr';
        $NEWSLETTER['e_anrede_w'] = 'Sehr geehrte Frau';
        $NEWSLETTER['e_subject'] = $FORM_CON['title'];
        $NEWSLETTER['e_content'] = $this->db->real_escape_string($vorlage);
        $NEWSLETTER['e_html'] = 1;
        $id = insert_table(TBL_CMS_EMAILER, $NEWSLETTER);
        $this->msg("{LBLA_SAVED}");
        HEADER("location:" . $_SERVER['PHP_SELF'] . "?epage=" . $_REQUEST['epage'] . "&cmd=edit&id=" . $id);
        exit;
    }

}
