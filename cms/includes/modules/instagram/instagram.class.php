<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::instagram
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2019-10-18
 */

defined('IN_SIDE') or die('Access denied.');


class instagram_class extends instagram_master_class {

    var $INSTAGRAM = array();

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
        if ($this->smarty->getTemplateVars('INSTAGRAM') != NULL) {
            $this->INSTAGRAM = array_merge($this->smarty->getTemplateVars('INSTAGRAM'), $this->INSTAGRAM);
            $this->smarty->clearAssign('INSTAGRAM');
        }
        $this->smarty->assign('INSTAGRAM', $this->INSTAGRAM);
    }

    /**
     * parse_instagram()
     * 
     * @param mixed $params
     * @return
     */
    function parse_instagram($params) {
        #$params = $this->parse_plugin_template($params, 'INSTAGRAM');
        $html = $params['html'];
        $langid = $params['langid']; // parse group
        if (strstr($html, '{TMPL_INSTAGRAM_')) {
            preg_match_all("={TMPL_INSTAGRAM_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
              #  echoarr($tpl_tag);
                $this->INSTAGRAM['feed'] = $this->get_insta_stream($PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=instastream value=$TMPL_INSTAGRAM_' . $cont_matrix_id . ' %>
                <% assign var=plginopt value=$PLUG_OPT_' . $cont_matrix_id . ' %>
                <% assign var=cont_matrix_id value="' . $cont_matrix_id . '" %>
                <% include file="' . $PLUGIN_OPT['tpl_name'] . '.tpl" %>', $html);
                $params['PLUGIN_OPT'] = $PLUGIN_OPT;
                $this->smarty->assign('TMPL_INSTAGRAM_' . $cont_matrix_id, $this->INSTAGRAM);
            }
        }

        $params['html'] = $html;
        return $params;
    }

    /**
     * instagram_class::cmd_get_insta_story()
     * 
     * @return void
     */
    function cmd_get_insta_story() {
        $arr = $this->get_insta_stream();
        echoarr($arr);
        die;
    }

}
