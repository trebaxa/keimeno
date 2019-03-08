<?php

/**
 * @package    psitemap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class psitemap_class extends modules_class {

    /**
     * psitemap_class::__construct()
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
     * psitemap_class::cmd_load_sitemap()
     * 
     * @return
     */
    function cmd_load_sitemap() {
        $this->load_sitemap();
    }

    /**
     * psitemap_class::load_sitemap()
     * 
     * @return
     */
    function load_sitemap() {
        $sql = "SELECT T.id, T.parent, TC.linkname,T.url_redirect,T.url_redirect_target 
            FROM 
            " . TBL_CMS_TEMPLATES . " T INNER JOIN " . TBL_CMS_PERMISSIONS . " P ON (P.perm_tid=T.id " . $this->user_object['sql_groups'] . "),
            " . TBL_CMS_TEMPLATES . " TT LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.tid=TT.id AND TC.lang_id=" . $this->GBL_LANGID . ")    
            WHERE T.parent=0 AND T.approval='1' AND T.c_type='T' AND TT.id=T.id    
            GROUP BY T.id 
            ORDER BY T.morder";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $tree_ids[] = $row['id'];
        }
        $nodes = new cms_tree_class();
        $nodes->db = $this->db;
        $sql = "SELECT T.description,T.id, T.parent, TC.linkname,T.url_redirect,T.url_redirect_target,TC.t_icon,TC.t_htalinklabel,T.modident FROM 
				" . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.tid=T.id AND TC.lang_id=" . $this->GBL_LANGID . ") 
				WHERE T.gbl_template=0 AND T.approval='1' AND T.c_type='T'		
				GROUP BY T.id 
				ORDER BY T.morder";
        $result = $this->db->query($sql);
        $data_menu = array();
        while ($row = $this->db->fetch_array_names($result)) {
            $data_menu[] = $row;
        }

        $nodes->build_core_cms_tree($data_menu, $tree_ids);
        $this->PSITEMAP['menu_arr'] = $nodes->menu_array;
        $this->PSITEMAP = exec_evt('psitemap', $this->PSITEMAP, $this);
    }

    /**
     * psitemap_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('PSITEMAP', $this->PSITEMAP);
    }

    /**
     * psitemap_class::parse_sitemap()
     * 
     * @param mixed $params
     * @return
     */
    function parse_sitemap($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_SITEMAPINLAY_')) {
            $this->load_sitemap();
            $this->parse_to_smarty();
            preg_match_all("={TMPL_SITEMAPINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $this->smarty->assign('TMPL_SITEMAP_' . $cont_matrix_id, $PLUGIN_OPT);
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$PLUGIN_OPT['tplid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=sitemap value=$TMPL_SITEMAP_' . $cont_matrix_id . ' %>                
                <% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

}

?>