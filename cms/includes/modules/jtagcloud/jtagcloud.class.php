<?php

/**
 * @package    jtagcloud
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class jtagcloud_class extends modules_class {

    var $JTAGCLOUD = array();

    /**
     * jtagcloud_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * jtagcloud_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('JTAGCLOUD', $this->JTAGCLOUD);
    }

    /**
     * jtagcloud_class::parse_jtagcloud()
     * 
     * @param mixed $params
     * @return
     */
    function parse_jtagcloud($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_JTAGCLOUD_')) {
            preg_match_all("={TMPL_JTAGCLOUD_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_JTAGCLOUD_", "}");
                $cont_matrix_id = intval(strtolower(str_replace($rep, "", $wert)));               
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                foreach ((array )$PLUGIN_OPT['pages'] as $ikey => $value) {
                    list($PLUGIN_OPT['webpages'][$ikey]['link_url'], $PLUGIN_OPT['webpages'][$ikey]['link_label']) = explode('###', $value);
                }
                $this->smarty->assign('TMPL_JTAGCLOUD_' . $cont_matrix_id, $PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=jtagcloud value=$TMPL_JTAGCLOUD_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

}
