<?php

/**
 * @package    feedbacks
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */

DEFINE('TBL_CMS_TESTIMONIALS', TBL_CMS_PREFIX . 'testimonials');

class feedbacks_class extends modules_class {

    /**
     * feedbacks_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->FEEDB = array();
    }

    /**
     * feedbacks_class::load_feedbacks()
     * 
     * @return
     */
    function load_feedbacks() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TESTIMONIALS . " WHERE approval='1' ORDER BY time_int DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $rows[] = array(
                'fb_customer' => $row['kname'],
                'fb_date' => date("d.m.Y", $row['time_int']),
                'fb_text' => nl2br($row['feedback']));
        }
        $this->smarty->assign('feedback', (array )$rows);
    }

    /**
     * feedbacks_class::load_latest()
     * 
     * @return
     */
    function load_latest() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TESTIMONIALS . " WHERE approval='1' ORDER BY time_int DESC LIMIT 10");
        while ($row = $this->db->fetch_array_names($result)) {
            $rows[] = array(
                'fb_customer' => $row['kname'],
                'fb_date' => date("d.m.Y", $row['time_int']),
                'fb_text' => nl2br($row['feedback']));
        }
        $this->smarty->assign('feedbacklatest', (array )$rows);
    }


    /**
     * feedbacks_class::cmd_add_item()
     * 
     * @return
     */
    function cmd_add_item() {
        $FORM = $_POST['FORM'];
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $value) {
                $FORM[$key] = strip_tags($FORM[$key]);
            }
        }
        if (!validate_email_input($FORM['tschapura']))
            $this->msge("{ERR_MSSING_FIELD} {LBL_EMAIL}");
        if ($FORM['feedback'] == "")
            $this->msge("{ERR_MSSING_FIELD} {LBL_MESSAGE}");
        if ($FORM['kname'] == "")
            $this->msge("{ERR_MSSING_FIELD} {LBL_KNAME}");

        if ($this->gbl_config['capcha_active'] == 1) {
            if (isset($_SESSION['captcha_spam']) and $_POST["securecode"] == $_SESSION['captcha_spam']) {
                unset($_SESSION['captcha_spam']);
            }
            else
                $this->msge("{ERR_SECODE}");
        }

        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            $this->msge("invalid token.");
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        }

        if (count($_SESSION['err_msgs']) == 0) {
            $FORM['time_int'] = time();
            $FORM['email'] = $FORM['tschapura'];
            $FORM['ip'] = getenv('REMOTE_ADDR');
            unset($FORM['tschapura']);
            insert_table(TBL_CMS_TESTIMONIALS, $FORM);
            $inhalt = "Name: " . $FORM['kname'] . "\n\nText: " . $FORM['feedback'] . "\n";
            mail(FM_EMAIL, utf8_decode(pure_translation("New Feedback: " . $FORM['kname'], $GBL_LANGID)), utf8_decode($inhalt), "From: " . $FORM['email']);
            $this->msg("{LBL_GUESTOK}");
            HEADER("location: " . $_SERVER['PHP_SELF'] . "?page=" . $_POST['page'] . "&section=done");
            $this->hard_exit();
        }

    }

    /**
     * feedbacks_class::load_items()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_items($PLUGIN_OPT) {
        $PLUGIN_OPT['sort'] = ($PLUGIN_OPT['sort'] == 'ASC') ? 'ASC' : 'DESC';
        $PLUGIN_OPT['thb_width'] = ($PLUGIN_OPT['thb_width'] == '') ? '90' : $PLUGIN_OPT['thb_width'];
        $PLUGIN_OPT['thb_height'] = ($PLUGIN_OPT['thb_height'] == '') ? '90' : $PLUGIN_OPT['thb_height'];
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TESTIMONIALS . " G LEFT JOIN " . TBL_CMS_CUST . " CU ON (CU.kid=G.kid) 
        WHERE G.approval='1' 
        ORDER BY G.time_int 
        " . $PLUGIN_OPT['sort'] . " 
        LIMIT " . (int)$PLUGIN_OPT['limit']);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['fb_customer'] = $row['kname'];
            $row['fb_date'] = date("d.m.Y", $row['time_int']);
            $row['fb_text'] = nl2br($row['feedback']);
            $profil_img = ($row['img'] != "") ? './file_data/feedbacks/' . $row['img'] : './images/opt_member_nopic.jpg';
            $row['custthumb'] = thumbit_fe($profil_img, $PLUGIN_OPT['thb_width'], $PLUGIN_OPT['thb_height'], 'crop');
            $this->FEEDB['list'][] = $row;
        }
    }

    /**
     * feedbacks_class::parse_feedbacks()
     * 
     * @param mixed $params
     * @return
     */
    function parse_feedbacks($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_FEEDBACKS_')) {
            preg_match_all("={TMPL_FEEDBACKS_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {        
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->load_items($PLUGIN_OPT);
                $this->smarty->assign('TMPL_FEEDBACKS_' . $cont_matrix_id, $this->FEEDB['list']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=feedbacks value=$TMPL_FEEDBACKS_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }
}
