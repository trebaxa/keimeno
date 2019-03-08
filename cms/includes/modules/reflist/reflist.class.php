<?php

/**
 * @package    reflist
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_CMS_REFLINKS', TBL_CMS_PREFIX . 'reflinks');

class reflist_class extends reflist_master_class {

    var $REFLIST = array();

    /**
     * reflist_class::__construct()
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
     * reflist_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('REFLIST') != NULL) {
            $this->REFLIST = array_merge($this->smarty->getTemplateVars('REFLIST'), $this->REFLIST);
            $this->smarty->clearAssign('REFLIST');
        }
        $this->smarty->assign('REFLIST', $this->REFLIST);
    }


    /**
     * reflist_class::load_reflinks()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_reflinks($PLUGIN_OPT = array()) {
        $this->REFLIST['links'] = array();
        $col = ($PLUGIN_OPT['column'] != "") ? $PLUGIN_OPT['column'] : 'r_firma';
        $sort = ($PLUGIN_OPT['sort'] == "DESC") ? 'DESC' : 'ASC';
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_REFLINKS . " WHERE 1 ORDER BY " . $col . " " . $sort);
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['r_img'] != "") {
                $row['thumb'] = thumbit_fe('/file_data/reflist/' . $row['r_img'], $PLUGIN_OPT['thumb_width'], $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type']);
            }
            else {
                $row['thumb'] = thumbit_fe('/images/opt_member_nopic.jpg', $PLUGIN_OPT['thumb_width'], $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type']);
            }
            $this->REFLIST['links'][] = $row;
        }
    }

    /**
     * reflist_class::parse_ref_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_ref_inlay($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_REFLISTINLAY_')) {
            preg_match_all("={TMPL_REFLISTINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $this->load_reflinks($PLUGIN_OPT);
                $this->smarty->assign('TMPL_REFLISTINLAY_' . $cont_matrix_id, $this->REFLIST);
                if ($PLUGIN_OPT['tpl_name'] != "") {
                    $html = str_replace($tpl_tag[0][$key], '<% assign var=referenzen value=$TMPL_REFLISTINLAY_' . $cont_matrix_id . ' %><% include file="' . $PLUGIN_OPT['tpl_name'] .
                        '.tpl" %>', $html);
                }
                else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
            }
        }
        $params['html'] = $html;
        return $params;
    }

}

?>