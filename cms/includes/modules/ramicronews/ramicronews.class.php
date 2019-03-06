<?php

/**
 * @package    ramicronews
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
defined('IN_SIDE') or die('Access denied.');

class ramicronews_class extends ramicronews_master_class {

    var $RAMICRONEWS = array();

    /**
     * ramicronews_class::__construct()
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
     * ramicronews_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('RAMICRONEWS') != NULL) {
            $this->RAMICRONEWS = array_merge($this->smarty->getTemplateVars('RAMICRONEWS'), $this->RAMICRONEWS);
            $this->smarty->clearAssign('RAMICRONEWS');
        }
        $this->smarty->assign('RAMICRONEWS', $this->RAMICRONEWS);
    }

    /**
     * ramicronews_class::cmd_load_news()
     * 
     * @return
     */
    function cmd_load_news() {
        $id = (int)$_GET['id'];
        $this->RAMICRONEWS['newsdetail'] = $this->load_news_by_id($id);
        $this->parse_to_smarty();
        echo_template_fe('ramicronews_ra-micro-news-detail');
    }

    /**
     * ramicronews_class::parse_ramicronews()
     * 
     * @param mixed $params
     * @return
     */
    function parse_ramicronews($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_RAMICRONEWS_')) {
            preg_match_all("={TMPL_RAMICRONEWS_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->RAMICRONEWS['news'] = $this->load_items($PLUGIN_OPT);

                # $this->smarty->assign('TMPL_RAMICRONEWS_' . $cont_matrix_id, $items);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=RAMICRONEWS value=$TMPL_RAMICRONEWS_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
                $params['PLUGIN_OPT'] = $PLUGIN_OPT;
                $this->smarty->assign('TMPL_RAMICRONEWS_' . $cont_matrix_id, $this->RAMICRONEWS);
            }
        }
        $params['html'] = $html;

        return $params;
    }

}
