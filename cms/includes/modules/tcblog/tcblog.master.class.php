<?php

/**
 * @package    tcblog
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


class tcblog_master_class extends modules_class {

    /**
     * tcblog_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * tcblog_master_class::get_first_group()
     * 
     * @return
     */
    function get_first_group() {
        $group = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_GROUPS . " ORDER BY groupname LIMIT 1");
        if (!isset($group['id'])) {
            $group = array('id' => 0);
        }
        return $group;
    }

    /**
     * tcblog_master_class::load_group()
     * 
     * @param mixed $id
     * @return
     */
    function load_group($id) {
        $group = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_GROUPS . " WHERE id=" . $id);
        return $group;
    }

    /**
     * tcblog_master_class::load_default_blog_page()
     * 
     * @return
     */
    function load_default_blog_page() {
        $pageindex = $this->db->query_first("SELECT * FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='tcblog' LIMIT 1");
        return (int)$pageindex['pi_page'];
    }

    /**
     * tcblog_master_class::get_init_group_id()
     * 
     * @return
     */
    function get_init_group_id() {
        $group = $this->get_first_group();
        return $group['id'];
    }

    /**
     * tcblog_master_class::gen_detail_link()
     * 
     * @param mixed $row
     * @param string $prefix_lng
     * @return
     */
    function gen_detail_link($row, $prefix_lng = "") {
        return $prefix_lng . '/blog/' . date('Y', $row['inserttime']) . '/' . date('m', $row['inserttime']) . '/' . date('d', $row['inserttime']) . '/' . $this->
            format_file_name($row['title']) . '.html';
    }

    /**
     * tcblog_master_class::load_comments()
     * 
     * @param mixed $id
     * @param integer $active
     * @return
     */
    function load_comments($id, $active = -1) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN_COMMENTS . " WHERE c_itemid=" . (int)$id . " 
            " . (($active > 0) ? " AND c_approved=1 " : "") . "
            ORDER BY c_time DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * tcblog_master_class::load_item()
     * 
     * @param mixed $id
     * @return
     */
    function load_item($id) {
        $item = $this->db->query_first("SELECT NL.id AS DID,K.*,NL.*,NC.*,NC.id AS CONID,NL.kid AS CUSTID
	FROM " . TBL_CMS_PIN . " NL	
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->GBL_LANGID . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1 AND NC.title!='' AND NL.id=" . (int)$id . "
	");
        $this->set_item_opt($item);
        return $item;
    }

    /**
     * tcblog_master_class::genThemeMenu()
     * 
     * @param integer $pingroup_id
     * @return void
     */
    function genThemeMenu($pingroup_id = 0) {
        $pageindex = $this->db->query_first("SELECT * FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='tcblog' LIMIT 1");
        $page_content = $this->dao->load_template_content_by_tid($pageindex['pi_page']);
        #    echoarr($page_content);

        $result = $this->db->query("SELECT *, T.id AS GID FROM
	" . TBL_CMS_PIN_GROUPS . " T
	LEFT JOIN " . TBL_CMS_PIN_GCON . " NC ON (T.id=NC.g_id AND NC.lang_id=" . $this->GBL_LANGID . ")
	INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=T.id " . $this->user_object['sql_groups'] . ")
	GROUP BY T.id ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $prefix_lng = ($_SESSION['GBL_LANGID'] == $this->gbl_config['std_lang_id']) ? '' : $_SESSION['GBL_LOCAL_ID'] . '/';
            $themes[] = array(
                'theme' => (($row['g_title'] != "") ? $row['g_title'] : $row['groupname']),
                'class' => (($row['GID'] == $pingroup_id) ? ' class="selected"' : ''),
                'active' => (($row['GID'] == $pingroup_id) ? true : false),
                'id' => $row['GID'],
                'link' => PATH_CMS . $prefix_lng . $page_content['t_htalinklabel'] . '.html?page=' . $_GET['page'] . '&pingid=' . $row['GID']);
        }
        $this->smarty->assign('themes', $themes);
        $this->smarty->assign('themes_count', count($themes));
        #  return (int)$pingroup_id;
    }

}
