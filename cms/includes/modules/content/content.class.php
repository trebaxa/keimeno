<?php

/**
 * @package    content
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class content_class extends keimeno_class {

    var $CON_OBJ = array();
    var $exec_interpreter = array();

    /**
     * content_class::__construct()
     * 
     * @param integer $pageid
     * @return
     */
    function __construct($pageid = 0) {
        global $GRAPHIC_FUNC;
        parent::__construct();
        $this->GRAPHIC_FUNC = $GRAPHIC_FUNC;
    }

    /**
     * content_class::gen_url_template()
     * 
     * @param mixed $template_id
     * @return
     */
    static function gen_url_template($template_id) {
        return '{URL_TPL_' . $template_id . '}';
    }

    /**
     * content_class::gen_entry_point_list()
     * 
     * @param mixed $list
     * @param mixed $nodes
     * @param mixed $topl
     * @return
     */
    function gen_entry_point_list(&$list, &$nodes = null, $topl = array()) {
        $list = array();
        $valid = array();
        $level_result = $this->db->query("SELECT T.id FROM " . TBL_CMS_TEMPLATES . " T WHERE T.c_type='T' AND gbl_template=0 ORDER BY T.description");
        $tees_ids = explode(';', $topl['trees']);
        while ($rowl = $this->db->fetch_array_names($level_result)) {
            $childs = array();
            if (count($tees_ids) > 0) {
                $found = false;
                foreach ($tees_ids as $key => $id) {
                    $node = $nodes->getOneNode(intval($id), $nodes->menu_array);
                    if (is_array($node['children']))
                        $childs = explode(";", $nodes->buildFlatIdArray($node['children']));
                    if (in_array($rowl['id'], $childs)) {
                        $found = true;
                        break;
                    }
                }
            }
            if (in_array($rowl['id'], $tees_ids) || $found == true) {
                $valid[] = $rowl['id'];
            }
        }
        $level_result = $this->db->query("SELECT T.* FROM " . TBL_CMS_TEMPLATES . " T WHERE gbl_template=0 ORDER BY T.description");
        $list[] = array(
            'id' => 0,
            'label' => '- {LBL_NO_FIRSTPAGE} -',
            );

        while ($rowl = $this->db->fetch_array_names($level_result)) {
            if (in_array($rowl['id'], $valid)) {
                $list[] = array('id' => $rowl['id'], 'label' => $rowl['description']);
            }
        }
        return $list;
    }

    /**
     * content_class::set_entry_point()
     * 
     * @param mixed $TOPLEVEL_OBJ
     * @return
     */
    function set_entry_point(&$TOPLEVEL_OBJ) {
        $tl = 0;
        if (isset($_GET['tl']))
            $tl = (int)$_GET['tl'];
        if ($tl > 0 && $_GET['page'] != START_PAGE) {
            $_GET['page'] = $TOPLEVEL_OBJ['first_page'] > 0 ? $TOPLEVEL_OBJ['first_page'] : $_GET['page'];
        }
    }

    /**
     * content_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('CONTENT_OBJ', $this->CON_OBJ);
    }


    /**
     * content_class::delete_lang_content()
     * 
     * @param mixed $params
     * @return
     */
    function delete_lang_content($params) {
        $id = (int)$params['id'];
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . (int)$id . " AND lang_id>1");
        return $params;
    }

    /**
     * content_class::get_all_hotspots()
     * 
     * @param mixed $lang_id
     * @param mixed $framework_id
     * @return
     */
    function get_all_hotspots($lang_id, $framework_id) {
        if ((int)$this->gbl_config['opt_cms_offline'] == 1) {
            $gbl_template = get_template(9960, $lang_id);
        }
        else {
            $gbl_template = get_template($framework_id, $lang_id);
            if ($gbl_template == "")
                $gbl_template = get_template($framework_id, 1);
        }
        if (strstr($gbl_template, '{TMPL_SPOT_')) {
            preg_match_all("={TMPL_SPOT_(.*)}=siU", $gbl_template, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $spot) {
                $tmp = explode('_', $spot);
                $spot_num = (int)$tmp[count($tmp) - 1];
                $num_arr[] = $spot_num;
            }
        }
        return (array )$num_arr;
    }

    /**
     * content_class::build_parent_content_fe()
     * 
     * @param mixed $tid
     * @param mixed $tm_pos
     * @param mixed $html
     * @param mixed $lang_id
     * @return
     */
    function build_parent_content_fe($tid, $tm_pos, &$html, $lang_id) {
        $result = $this->db->query("SELECT T.parent,IC.*, IC.id AS TMID FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPMATRIX . " IC ," . TBL_CMS_TEMPCONTENT . " TC
                WHERE TC.id=IC.tm_cid 
                AND T.id=TC.tid 
                AND T.id=IC.tm_tid 
                AND IC.tm_approved=1
                AND T.id=" . (int)$tid . " 
                AND TC.lang_id=" . (int)$lang_id . " 
                AND IC.tm_pos=" . (int)$tm_pos . "
                ORDER BY tm_order");
        while ($row = $this->db->fetch_array_names($result)) {
            if (!empty($row['tm_modident'])) {
                $this->exec_interpreter[$row['tm_modident']] = $row['tm_modident'];
            }
            if ($row['tm_type'] != 'H') {
                if ($row['tm_refid'] == 0) {
                    $html .= $row['tm_content'];
                }
                else {
                    $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " T 
                        WHERE T.approval=1 AND TC.tid=T.id AND TC.id=" . $row['tm_refid']);
                    $html .= $T['content'];
                }
            }
            else {
                if ($row['tm_parent'] > 0) {
                    $this->build_parent_content_fe($row['tm_parent'], $tm_pos, $html, $lang_id);
                }
            }
        }
    }

    /**
     * content_class::build_content()
     * 
     * @param mixed $tm_cid
     * @param mixed $pos
     * @param mixed $lang_id
     * @param mixed $tid
     * @return
     */
    function build_content($tm_cid, $pos, $lang_id, $tid) {
        $content = "";
        $global_tpl = false;

        if ($this->gblconfig->templ_cache == 1 && ISADMIN != 1) {
            $cache_content = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATE_PRE . " WHERE tc_cid=" . $tm_cid . " AND tc_pos=" . $pos . " AND tc_tid=" . $tid);
            $this->exec_interpreter = unserialize($cache_content['tc_interpreter']);
            return array('content' => $cache_content['tc_content'], 'global_tpl' => $cache_content['tc_globaltpl'] == 1);
        }
        else {
            $result = $this->db->query("SELECT TM.*,T.parent FROM " . TBL_CMS_TEMPMATRIX . " TM , " . TBL_CMS_TEMPLATES . " T 
                WHERE T.id=TM.tm_tid 
                AND TM.tm_approved=1
                AND TM.tm_cid=" . (int)$tm_cid . " 
                AND TM.tm_pos=" . (int)$pos . "                
                ORDER BY tm_order");
            if ($this->db->num_rows($result) > 0) {
                while ($row = $this->db->fetch_array_names($result)) {
                    if (!empty($row['tm_modident'])) {
                        $this->exec_interpreter[$row['tm_modident']] = $row['tm_modident'];
                    }
                    if ($row['tm_refid'] == 0) {
                        if ($row['tm_type'] == 'H') {
                            $this->build_parent_content_fe($row['tm_parent'], $row['tm_pos'], $content, $lang_id);
                        }
                        else {
                            $content .= $row['tm_content'];
                        }
                    }
                    else {
                        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " T 
                        WHERE T.approval=1 AND TC.tid=T.id AND TC.id=" . $row['tm_refid']);
                        $content .= $T['content'];
                    }
                }
            }
            else {
                $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . $lang_id . " AND tid=" . (int)$tid);
                $content = $T['content'];
                $global_tpl = true;
            }

            return array('content' => $content, 'global_tpl' => $global_tpl);
        }

    }

    /**
     * content_class::redirect_if_not_exists()
     * 
     * @param mixed $page
     * @return
     */
    function redirect_if_not_exists($page) {
        if ((string )$page == "0")
            return;
        $redirect = PATH_CMS . 'index.html';
        $pnf_hash = md5($_SERVER['SCRIPT_URI']);
        $PNF = dao_class::get_data_first(TBL_CMS_PAGENF, array('pnf_hash' => $pnf_hash));
        if (isset($PNF['pnf_page']) && $PNF['pnf_page'] == 0 && count($PNF) <= 1) {
            $arr = array(
                'pnf_page' => $page,
                'pnf_time' => time(),
                'pnf_uri' => $_SERVER['SCRIPT_URI'],
                'pnf_hash' => $pnf_hash,
                'pnf_user' => $_SERVER['HTTP_USER_AGENT'],
                );
            insert_table(TBL_CMS_PAGENF, $arr);
        }
        else {
            if (isset($PNF['pnf_page']) && $PNF['pnf_url'] != "")
                $redirect = $PNF['pnf_url'];
        }
        $this->redirect_301($redirect);
    }


    /**
     * content_class::load_frontend_webpage()
     * 
     * @param mixed $page
     * @param mixed $lang_id
     * @param mixed $TOPLEVEL_OBJ
     * @return
     */
    function load_frontend_webpage($page, $lang_id, $TOPLEVEL_OBJ) {
        $page = (int)$page;

        $templ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $page);
        if ($templ['id'] == 0) {
            $this->redirect_if_not_exists($page);
        }
        else {

            $templ['use_framework'] = ($templ['use_framework'] == 0) ? 1 : (int)$templ['use_framework'];
            if ($templ['first_page'] > 0) {
                $page = $templ['first_page'];
                $templ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $page);
            }
            $template = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $page . " AND lang_id=" . $lang_id);
            if ($template['id'] == 0) {
                $template = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $page . " AND lang_id=1");
            }
            $template['t_breadcrumb_arr'] = unserialize($template['t_breadcrumb_arr']);

            # build content
            $template['content_spots'] = array();
            $spots = $this->get_all_hotspots($lang_id, $templ['use_framework']);

            $this->exec_interpreter = array();
            foreach ($spots as $key => $spotid) {
                $arr = $this->build_content($template['id'], $spotid, $lang_id, $template['tid']);
                if ($arr['global_tpl'] == false) {
                    $template['content_spots'][$spotid] = $arr['content'];
                }
                else {
                    $template['content_spots'][$spotid] = "";
                    if ($spotid == 1)
                        $template['content_spots'][1] = $arr['content'];
                }
            }

            $template['content'] = $template['content_spots'][1];
            $template['basis'] = $templ;

            # THEME IMAGE SET
            $template['theme_image_width'] = 0;
            $template['theme_image_height'] = 0;
            if (!empty($template['theme_image'])) {
                if (is_file(CMS_ROOT . 'file_data/themeimg/' . $template['theme_image'])) {
                    list($width, $height, $type, $attr) = getimagesize(CMS_ROOT . 'file_data/themeimg/' . $template['theme_image']);
                    $template['theme_image_width'] = $width;
                    $template['theme_image_height'] = $height;
                }

                $template['theme_image_url'] = self::get_domain_url() . 'file_data/themeimg/' . $template['theme_image'];
                if ($template['t_tiwidth'] > 0 && $template['t_tiheight'] > 0) {
                    $template['theme_image'] = thumbit_fe('./file_data/themeimg/' . $template['theme_image'], $template['t_tiwidth'], $template['t_tiheight'], 'crop', $template['t_ticroppos']);
                }
                else {
                    $template['theme_image'] = SSL_PATH_SYSTEM . PATH_CMS . 'file_data/themeimg/' . $template['theme_image'];
                }
            }

            # THEME IMAGE SET BY TOPLEVEL
            if ($template['theme_image'] == "" && $TOPLEVEL_OBJ['theme_image'] != "") {
                list($width, $height, $type, $attr) = getimagesize(CMS_ROOT . 'file_data/themeimg/' . $TOPLEVEL_OBJ['theme_image']);
                $template['theme_image_width'] = $width;
                $template['theme_image_height'] = $height;
                $template['theme_image_url'] = self::get_domain_url() . 'file_data/themeimg/' . $TOPLEVEL_OBJ['theme_image'];
                $template['theme_image'] = SSL_PATH_SYSTEM . PATH_CMS . 'file_data/themeimg/' . $TOPLEVEL_OBJ['theme_image'];
            }

            $this->smarty->assign('PAGEOBJ', $template);
            DEFINE('PAGEID', (int)$page);
            $params = array();
            $params = exec_evt('OnPageLoad', $params, $this);
            return array(
                $template,
                $page,
                $templ);
        }
    }


    /**
     * content_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='content' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $params = array_merge($params, $SM);
            $url['url'] = rtrim(self::get_domain_url(), '/');
            $url['frecvent'] = $params['sm_changefreq'];
            $url['priority'] = $params['sm_priority'];
            $params['urls'][] = $url;
            $sql_filter = array('approval' => 1);
            if ((int)$params['langid'] > 0) {
                $sql_filter['id'] = (int)$params['langid'];
            }
            $lang_arr = dao_class::get_data(TBL_CMS_LANG, $sql_filter);
            foreach ($lang_arr as $rowl) {

                $result = $this->db->query("SELECT C.* FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C 
                WHERE C.lang_id=" . $rowl['id'] . " AND c_type='T' AND C.linkname<>'' AND C.tid=T.id AND T.xml_sitemap=1");
                while ($row = $this->db->fetch_array_names($result)) {
                    $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
                    $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['tid'];

                    $url = array(
                        'url' => rtrim(self::get_domain_url(), '/') . gen_page_link($tid, $url_label, $rowl['id'], $rowl['local']),
                        'frecvent' => $params['sm_changefreq'],
                        'priority' => $params['sm_priority'],
                        );

                    if ($row['theme_image'] != "") {
                        $url['images'][] = array('loc' => self::get_domain_url() . 'file_data/themeimg/' . $row['theme_image'], 'title' => $row['linkname']);
                    }
                    $params['urls'][] = $url;
                }
            }
        }
        return (array )$params;
    }

}
