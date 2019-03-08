<?php

/**
 * @package    features
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class features_class extends features_master_class
{

    var $FEATURES = array();

    /**
     * features_class::__construct()
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
     * features_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        if ($this->smarty->getTemplateVars('FEATURES') != null) {
            $this->FEATURES = array_merge($this->smarty->getTemplateVars('FEATURES'), $this->
                FEATURES);
            $this->smarty->clearAssign('FEATURES');
        }
        $this->smarty->assign('FEATURES', $this->FEATURES);
    }


    /**
     * features_class::load_features()
     * 
     * @param mixed $gid
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_features($gid, $PLUGIN_OPT)
    {
        $this->FEATURES['features'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FEATURES . " WHERE " . (($gid >
            0) ? " f_gid=" . (int)$gid : "1") . "  ORDER BY " . $PLUGIN_OPT['column'] . " " .
            $PLUGIN_OPT['sort']);
        while ($row = $this->db->fetch_array_names($result)) {
            $row[thumb] = thumbit_fe('/file_data/features/' . $row['f_image'], $PLUGIN_OPT['thumb_width'],
                $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type'], $PLUGIN_OPT['g_croppos']);
            $this->FEATURES['features'][] = $row;
        }
        return $this->FEATURES['features'];
    }

    /**
     * features_class::parse_feature_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_feature_inlay($params)
    {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_FEATUREINLAY_')) {
            preg_match_all("={TMPL_FEATUREINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATUREGROUPS .
                    " WHERE id=" . (int)$PLUGIN_OPT['feature_group_id']);
                $this->load_features((int)$PLUGIN_OPT['feature_group_id'], $PLUGIN_OPT);
                $FEATUREOBJ['table'] = $this->FEATURES['features'];
                $this->smarty->assign('TMPL_FEATUREINLAY_' . $cont_matrix_id, $FEATUREOBJ);
                if ($PLUGIN_OPT['tpl_name'] != "") {
                    $html = str_replace($tpl_tag[0][$key],
                        '<% assign var=feature value=$TMPL_FEATUREINLAY_' . $cont_matrix_id .
                        ' %><% include file="' . $PLUGIN_OPT['tpl_name'] . '.tpl" %>', $html);
                } else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
            }
        }
        $params['html'] = $html;
        return $params;
    }

}

?>