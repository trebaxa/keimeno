<?php

/**
 * @package    gblvars
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
 
defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_CMS_GBLVARS', TBL_CMS_PREFIX . 'gblvars');

class gblvars_class extends gblvars_master_class {

    var $GBLVARS = array();

    /**
     * gblvars_class::__construct()
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
     * gblvars_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('GBLVARS') != NULL) {
            $this->GBLVARS = array_merge($this->smarty->getTemplateVars('GBLVARS'), $this->GBLVARS);
            $this->smarty->clearAssign('GBLVARS');
        }
        $this->smarty->assign('GBLVARS', $this->GBLVARS);
    }


    /**
     * gblvars_class::page_load_frontend()
     * 
     * @param mixed $params
     * @return
     */
    function page_load_frontend($params) {
        $gblvars = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLVARS . " WHERE 1 ORDER BY var_name,var_desc");
        while ($row = $this->db->fetch_array_names($result)) {
            $gblvars[$row['var_name']] = $row['var_value'];
        }
        if (isset($_REQUEST['page'])) {
            $page = $_REQUEST['page'];
            $TPL = $this->db->query_first("SELECT TC.* FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " T WHERE TC.tid=T.id AND TC.tid='" . PAGEID .
                "' AND TC.lang_id=" . $this->GBL_LANGID . " LIMIT 1");
            $saved_gblvars = unserialize($TPL['t_gblvars']);

            $saved_gblvars = (array )$saved_gblvars;
            foreach ($saved_gblvars as $key => $value) {
                $gblvars[$key] = $value['var_value'];
            }
        }
        $this->smarty->assign('gblvars', $gblvars);
        return $params;
    }

}

?>