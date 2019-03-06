<?php

/**
 * @package    Keimeno::formsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-04-09
 */

defined('IN_SIDE') or die('Access denied.');

class formsend_class extends formsend_master_class {

    var $FORMSEND = array();

    /**
     * __construct()
     * 
     * @return void
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('FORMSEND') != NULL) {
            $this->FORMSEND = array_merge($this->smarty->getTemplateVars('FORMSEND'), $this->FORMSEND);
            $this->smarty->clearAssign('FORMSEND');
        }
        $this->smarty->assign('FORMSEND', $this->FORMSEND);
    }


    /**
     * cronjob()
     * 
     * @return void
     */
    function cronjob() {

    }

    /**
     * parse_formsend()
     * 
     * @param mixed $params
     * @return
     */
    function parse_formsend($params) {
        $params = $this->parse_plugin_template($params, 'FORMSEND');
        return $params;
    }

    /**
     * formsend_class::cmd_formsend()
     * 
     * @return void
     */
    function cmd_formsend() {
        $FORM = (array )$_POST['FORM'];
        if (!filter_var($FORM['email'], FILTER_VALIDATE_EMAIL)) {
            self::msge('E-Mail ist ungültig');
        }
        if (count($FORM) > 0 && self::has_errors() == false) {
            $this->smarty->assign('gbl_config', $this->gbl_config);
            $PLUGIN_OPT = $this->load_plug_opt((int)$_POST['cont_matrix_id']);
            $anschreiben = dao_class::get_template_content($PLUGIN_OPT['tplidletter']);
            $anschreiben = smarty_compile($anschreiben . "\n" . '<% $gbl_config.email_absender %>');
            $anschreiben = smarty_compile($anschreiben);
            $pdf_tpl = dao_class::get_template_content($PLUGIN_OPT['tplidpdf']);
            $this->smarty->assign('FORMSEND', $FORM);
            $pdf_tpl = smarty_compile($pdf_tpl);
            require_once (CMS_ROOT . 'includes/pdf.class.php');
            $pdf = new pdf_class();
            $att_files[] = $pdf->createPDFFile($pdf_tpl, CMS_ROOT . self::format_file_name($PLUGIN_OPT['title']));
            #self::direct_download($att_files[0]);
            $smarty_arr = array('mail' => array('subject' => $PLUGIN_OPT['subject'], 'content' => $anschreiben));
            $recipient_email = ($PLUGIN_OPT['email'] != "") ? $PLUGIN_OPT['email'] : FM_EMAIL;
            #          echo $pdf_tpl;
            send_easy_mail_to($FORM['email'], $smarty_arr['mail']['content'], $smarty_arr['mail']['subject'], $att_files, true, $recipient_email);
            # send_admin_mail(900, $smarty_arr, $att_files, FM_EMAIL); #general mail template
        }
        self::msg('Ihren Daten wurden gesendet.');
        $this->ej('close_formsend');
    }

}
