<?php

/**
 * @package    Keimeno::mylivechat
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-06-11
 */

defined('IN_SIDE') or die('Access denied.');

class mylivechat_class extends mylivechat_master_class {

    var $MYLIVECHAT = array();

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
        if ($this->smarty->getTemplateVars('MYLIVECHAT') != NULL) {
            $this->MYLIVECHAT = array_merge($this->smarty->getTemplateVars('MYLIVECHAT'), $this->MYLIVECHAT);
            $this->smarty->clearAssign('MYLIVECHAT');
        }
        $this->smarty->assign('MYLIVECHAT', $this->MYLIVECHAT);
    }


    /**
     * cronjob()
     * 
     * @return void
     */
    function cronjob() {

    }

    /**
     * parse_mylivechat()
     * 
     * @param mixed $params
     * @return
     */
    function parse_mylivechat($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_MYLIVECHAT_')) {
            preg_match_all("={TMPL_MYLIVECHAT_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $PLUGIN_OPT['mylivechat_tpl'] = str_replace('add_chatinline();', '', $PLUGIN_OPT['mylivechat_tpl']);
                $this->smarty->assign('TMPL_MYLIVECHAT_' . $cont_matrix_id, $PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=mylivechat value=$TMPL_MYLIVECHAT_' . $cont_matrix_id . ' %>                
                ' . $PLUGIN_OPT['mylivechat_tpl'] . '
                <script>
                  function start_mylivechat() {  
                    if (document.getElementById("js-mylivechat-check").checked ) {
                        add_chatinline();
                        $("#js-mylivechat").html("Chat Anzeige wird geladen. Bitte warten...");
                        setTimeout(function(){ $("#js-mylivechat").fadeOut(); }, 3000);
                    } else {
                        alert("Bitte stimmen Sie unseren Datenschutzrichtlinie zu.");
                    }
                  }  
                </script>
                ', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

}
