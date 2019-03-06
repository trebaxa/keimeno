<?php

/**
 * @package    callback
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class callback_class extends callback_master_class
{

    var $CALLBACK = array();

    /**
     * callback_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->
            gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * callback_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        if ($this->smarty->getTemplateVars('CALLBACK') != null) {
            $this->CALLBACK = array_merge($this->smarty->getTemplateVars('CALLBACK'), $this->
                CALLBACK);
            $this->smarty->clearAssign('CALLBACK');
        }
        $this->smarty->assign('CALLBACK', $this->CALLBACK);
    }

    /**
     * callback_class::cmd_send_callback()
     * 
     * @return
     */
    function cmd_send_callback()
    {
        $FORM = (array )$_POST['FORM'];

        foreach ($FORM as $key => $value) {
            if (!is_array($FORM[$key]))
                $FORM[$key] = trim(strip_tags($FORM[$key]));
        }

        # Hidden Email Feld
        if (isset($_POST['email']) && $_POST['email'] != "") {
            $this->msge("hacking.");
            $contact_err['hacking'] = true;
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hack('Contact formular, hacking over hidden field');
        }

        # load plugin option if set
        $PLUGIN_OPT = array();
        if (isset($_POST['cont_matrix_id']) && $_POST['cont_matrix_id'] > 0) {
            $PLUGIN_OPT = $this->load_plug_opt((int)$_POST['cont_matrix_id']);
        }

        # send mail
        foreach ($FORM as $key => $value) {
            if (!is_array($FORM[$key])) {
                $email_msg .= strtoupper(str_replace("tschapura", "EMAIL", $key)) . ": " . $FORM[$key] .
                    "\n";
            } else {
                foreach ($FORM[$key] as $key_arr => $value_arr) {
                    $email_msg .= "\t" . strtoupper(str_replace("tschapura", "EMAIL", $key_arr)) .
                        ": " . trim(strip_tags($FORM[$key][$key_arr])) . "\n";
                }
            }
        }
        $this->smarty_arr = array('mail' => array('subject' => 'Callback ' . $FORM['telefon'],
                    'content' => date("d.m.Y H:i:s") . "\n" . $email_msg));
        $recipient_email = ($PLUGIN_OPT['email'] != "") ? $PLUGIN_OPT['email'] :
            FM_EMAIL;
        send_easy_mail_to($recipient_email, $this->smarty_arr['mail']['content'], $this->
            smarty_arr['mail']['subject'], $att_files, true, $tschapura);
        $this->msg('Nachricht gesendet.');
        $this->ej('callback_send');
    }


    # PLUGIN
    /**
     * callback_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE modident='callback' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * callback_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params)
    {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" .
            (int)$id);
        $upt = array(
            'tm_modident' => 'callback',
            'tm_content' => '<% assign var=cont_matrix_id value="' . $cont_matrix_id .
                '" %><%include file="' . $R['tpl_name'] . '.tpl"%>',
            'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

}

?>