<?php

/**
 * @package    faq
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */

DEFINE('TBL_CMS_FAQGROUPS', TBL_CMS_PREFIX . 'faq_groups');
DEFINE('TBL_CMS_FAQITEMS', TBL_CMS_PREFIX . 'faq_items');

class faq_class extends modules_class
{

    var $FAQ = array();

    /**
     * faq_class::__construct()
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
     * faq_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('FAQ', $this->FAQ);
    }

    /**
     * faq_class::cmd_load_items()
     * 
     * @return
     */
    function cmd_load_items()
    {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE modident='faq' AND gbl_template=1 LIMIT 1");
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQITEMS .
            " WHERE faq_gid=" . (int)$_GET['gid'] . " ORDER BY faq_order,faq_question");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->FAQ['faqitems'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template_fe($TPL['tpl_name']);
    }

    /**
     * faq_class::load_groups()
     * 
     * @param integer $groupid
     * @return
     */
    function load_groups($groupid = 0)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQGROUPS . " WHERE 1 " .
            (($groupid > 0) ? " AND id=" . $groupid : "") . " ORDER BY g_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->FAQ['groups'][] = $row;
        }
    }

    /**
     * faq_class::load_items()
     * 
     * @param integer $groupid
     * @return
     */
    function load_items($groupid = 0)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQITEMS . " WHERE 1 " . (($groupid >
            0) ? " AND faq_gid=" . $groupid : "") . " ORDER BY faq_order,faq_question");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->FAQ['faqitems'][$row['id']] = $row;
        }
    }

    /**
     * faq_class::parse_faq()
     * 
     * @param mixed $params
     * @return
     */
    function parse_faq($params)
    {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_FAQ_')) {
            preg_match_all("={TMPL_FAQ_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES .
                    " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->load_groups($PLUGIN_OPT['groupid']);
                $this->load_items($PLUGIN_OPT['groupid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=FAQ value=$TMPL_FAQ_' . $cont_matrix_id .
                    ' %><% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
                $params['PLUGIN_OPT'] = $PLUGIN_OPT;
                $params['faq_items'] = $this->FAQ['faqitems'];
                $params = exec_evt('load_faq_items', $params);
                $this->FAQ['faqitems'] = $params['faq_items'];
                $this->smarty->assign('TMPL_FAQ_' . $cont_matrix_id, $this->FAQ);
            }
        }
        $params['html'] = $html;

        return $params;
    }

}

?>