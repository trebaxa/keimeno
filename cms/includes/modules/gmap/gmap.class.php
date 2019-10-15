<?php

/**
 * @package    gmap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
defined('IN_SIDE') or die('Access denied.');

class gmap_class extends gmap_master_class {

    var $GMAP = array();

    /**
     * gmap_class::__construct()
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
     * gmap_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('GMAP') != NULL) {
            $this->GMAP = array_merge($this->smarty->getTemplateVars('GMAP'), $this->GMAP);
            $this->smarty->clearAssign('GMAP');
        }
        $this->smarty->assign('GMAP', $this->GMAP);
    }


    /**
     * gmap_class::parse_googlemaps()
     * 
     * @param mixed $params
     * @return
     */
    function parse_googlemaps($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_GMAPINLAY_')) {
            preg_match_all("={TMPL_GMAPINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $this->smarty->assign('TMPL_GMAP_' . $cont_matrix_id, $PLUGIN_OPT);
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$PLUGIN_OPT['tplid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=gmap value=$TMPL_GMAP_' . $cont_matrix_id . ' %>                
                <% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * gmap_class::on_output()
     * 
     * @param mixed $params
     * @return
     */
    function on_output($params) {
        $txt = '<script async defer src="https://maps.googleapis.com/maps/api/js?key=<%$gmap.gmapkey%>&callback=init_gmap_app" ></script>';
        if (!strstr($params['html'], 'maps.googleapis.com')) {
            $params['html'] = str_replace('</body>', smarty_compile($txt) . PHP_EOL . '</body>', $params['html']);
        }
        return $params;
    }

}
