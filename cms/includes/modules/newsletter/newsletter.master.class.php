<?php

/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TMP_FILE', 'news_preview_');
DEFINE('NEWS_FOLDER', CMS_ROOT . 'admin/newsletter/');
DEFINE('TBL_CMS_NEWSLETTERGROUPS', TBL_CMS_PREFIX . 'email_groups');
DEFINE('TBL_CMS_NEWSLETTEREMAILS', TBL_CMS_PREFIX . 'news_emails');


class newsletter_master_class extends keimeno_class {
    var $NEWSLETTER = array();

    /**
     * newsletter_master_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        if (!is_dir(NEWS_FOLDER))
            mkdir(NEWS_FOLDER, 0755);
    }

    /**
     * newsletter_master_class::load_attachments()
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
     * newsletter_master_class::replace_news()
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
            $content = str_replace("!!DISABLE_LETTER_LINK!!", '<a target="_blank" href="' . self::get_domain_url() .
                'includes/modules/newsletter/newsletter.inc.php?cmd=a_edisable&group=' . $user_obj['kid'] . '&n=' . md5($user_obj['email']) . '">' . $e_obj['e_unsubscribe'] .
                '</a>', $content);
            $content = str_replace("!!TRACKING_CODE!!", '<img style="display:none" width="0" height="0" src="' . self::get_domain_url() .
                'index.php?page=9910&cmd=recordn&id=' . $e_obj['id'] . '&n=' . base64_encode($user_obj['email']) . '" >', $content);
            $content = str_replace("</body>", "<!-- IP " . getenv('REMOTE_ADDR') . " --></body>", $content);
        }
        else {
            $content = str_replace("!!DISABLE_LETTER_LINK!!", self::get_domain_url() . 'index.php?page=9910&cmd=a_edisable&group=' . $user_obj['kid'] . '&n=' . md5($user_obj['email']),
                $content);
            $content = str_replace("[BR]", "\n", $content);
            $content = str_replace("!!TRACKING_CODE!!", '', $content);
        }
        foreach ($user_obj as $key => $value) {
            $content = str_replace("!!" . strtoupper($key) . "!!", $value, $content);
        }

        return $content;
    }

    /**
     * newsletter_master_class::sendNewsToEmail()
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
     * newsletter_master_class::load_groups()
     * 
     * @return void
     */
    function load_groups() {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLETTERGROUPS . " ORDER BY group_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }
}
