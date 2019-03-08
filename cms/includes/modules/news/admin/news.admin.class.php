<?php

/**
 * @package    news
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class news_admin_class extends modules_class {
    protected $NEWSADMIN = array();

    /**
     * news_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->NEWS_OBJ = new news_class();
    }

    /**
     * news_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('NEWSADMIN', $this->NEWSADMIN);
    }

    /**
     * news_admin_class::gen_selbox_groups()
     * 
     * @return
     */
    function gen_selbox_groups() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->NEWSADMIN['groups'][] = $row;
        }
    }

    /**
     * news_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('news');
        $this->NEWSADMIN['conf'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * news_admin_class::cmd_delicon()
     * 
     * @return
     */
    function cmd_delicon() {
        $this->del_icon($_GET['id']);
        $this->ej();
    }

    /**
     * news_admin_class::cmd_a_save()
     * 
     * @return
     */
    function cmd_a_save() {
        $this->NEWS_OBJ->save_news($_POST['FORM'], $_POST['FORM_CON'], $_POST['id'], $_POST['conid'], $_FILES);
        $this->rebuild_page_index();
        $this->msg('{LBLA_SAVED}');
        $this->ej('reload_news_item');
    }

    /**
     * news_admin_class::cmd_setnewkid()
     * 
     * @return
     */
    function cmd_setnewkid() {
        $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET n_kid=" . $_GET['setkid'] . " WHERE id=" . $_GET['id']);
        $this->TCR->redirect('cmd=edit&id=' . $_GET['id'] . '&epage=' . $_GET['epage']);
    }

    /**
     * news_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        #  $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$this->TCR->GET['ident'] . "' LIMIT 1");
        $this->set_approve((int)$_GET['value'], $this->TCR->GET['ident']);
        $this->ej();
    }

    /**
     * news_admin_class::cmd_list()
     * 
     * @return
     */
    function cmd_list() {
        $_SESSION['newsgroup_id'] = ($_GET['gid'] > 0) ? (int)$_GET['gid'] : $_SESSION['newsgroup_id'];
        $this->load_newslist($_GET['order'], $_GET['dc'], $_SESSION['newsgroup_id'], $this->gbl_config['std_lang_id']);
    }

    /**
     * news_admin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        global $LNGOBJ;
        $LNGOBJ->init_uselang();
        $this->langid = ($_GET['uselang'] == 0) ? 1 : (int)$_GET['uselang'];
        $this->load_news($_GET['id']);
        $this->smarty->assign('news_obj', $this->news);
        $this->smarty->assign('id', $_GET['id']);
        $this->smarty->assign('gid', $_SESSION['newsgroup_id']);
        $this->smarty->assign('uselang', $_GET['uselang']);
        $this->smarty->assign('langselect', $LNGOBJ->build_lang_select());
        $this->smarty->assign('groupselect', build_html_selectbox('FORM[group_id]', TBL_CMS_NEWSGROUPS, 'id', 'groupname', '', $this->news['group_id']));
    }

    /**
     * news_admin_class::cmd_reload_news_item()
     * 
     * @return
     */
    function cmd_reload_news_item() {
        $this->cmd_edit();
        $this->parse_to_smarty();
        echo json_encode($this->news);
        $this->hard_exit();
    }

    /**
     * news_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        $this->delete_news($_GET['ident']);
        $this->ej();
    }

    /**
     * news_admin_class::cmd_reload_news_attachments()
     * 
     * @return
     */
    function cmd_reload_news_attachments() {
        $this->cmd_edit();
        $this->parse_to_smarty();
        kf::echo_template('news.attachments');
    }


    /**
     * news_admin_class::cmd_delgroup()
     * 
     * @return
     */
    function cmd_delgroup() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSLIST . " WHERE group_id=" . $this->TCR->GET['ident']);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->delete_news($row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_NEWSGROUPS . " WHERE id=" . $this->TCR->GET['ident'] . " LIMIT 1");
        $this->ej();
    }

    /**
     * news_admin_class::del_icon()
     * 
     * @param mixed $id
     * @return
     */
    function del_icon($id) {
        $id = intval($id);
        $nfile = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSLIST . " WHERE id=" . $id);
        if (delete_file(CMS_ROOT . NEWS_PATH . $nfile['n_icon'])) {
            $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET n_icon='' WHERE id=" . $id);
            $this->LOGCLASS->addLog('DELETE', 'News iconfile' . $nfile['n_icon'] . ' from ID:' . $id);
            return true;
        }
        return false;
    }

    /**
     * news_admin_class::delete_news()
     * 
     * @param mixed $id
     * @return
     */
    function delete_news($id) {
        $id = intval($id);
        $this->remove_from_page_index('news', $id, 'news');
        $this->del_icon($id);
        $this->db->query("DELETE FROM " . TBL_CMS_NEWSLIST . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_NEWSCONTENT . " WHERE nid=" . $id);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSFILES . " WHERE f_nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            delete_file(CMS_ROOT . NEWS_PATH . $row['f_file']);
        }
        $this->LOGCLASS->addLog('DELETE', 'News [' . $id . '] deleted.');
    }

    /**
     * news_admin_class::set_approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function set_approve($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET approval='" . (($value == 1) ? 1 : 0) . "' WHERE id='" . intval($id) . "' LIMIT 1");
    }

    /**
     * news_admin_class::load_news()
     * 
     * @param mixed $id
     * @return
     */
    function load_news($id) {
        global $GRAPHIC_FUNC;
        $id = intval($id);
        if ($id <= 0)
            return false;
        $this->news = $this->db->query_first("SELECT *, A.id AS NID, C.id AS CID,K.*, AG.group_id AS GINDENT FROM 
 		(" . TBL_CMS_NEWSLIST . " A 	LEFT JOIN " . TBL_CMS_NEWSCONTENT . " C ON (C.nid=A.id AND C.lang_id=" . $this->langid . " )),
 		(" . TBL_CMS_NEWSLIST . " AAA	LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=AAA.mid)),
 		(" . TBL_CMS_NEWSLIST . " AA 	LEFT JOIN " . TBL_CMS_CUST . " K ON (AA.n_kid=K.kid)),
 		(" . TBL_CMS_NEWSLIST . " AG 	INNER JOIN " . TBL_CMS_NEWSGROUPS . " NG ON (NG.id=AG.group_id))
 		WHERE AA.id=A.id AND AAA.id=A.id AND AG.id=A.id
 		AND A.id=" . $id . " 
 		LIMIT 1");
        $this->news = $this->set_newslist_options($this->news);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSFILES . " WHERE f_nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['humanfilesize'] = human_file_size($row['f_size']);
            $row['uploadtime'] = date("d.m.Y H:i:s", $row['f_inserttime']);
            $row['thumbnail'] = '';
            $row['resu'] = '';
            if (file_exists(CMS_ROOT . NEWS_PATH . $row['f_file']) && ($row['f_ext'] == 'jpg' || $row['f_ext'] == 'gif' || $row['f_ext'] == 'png' || $row['f_ext'] == 'jpeg')) {
                list($width_px, $height_px) = getimagesize(CMS_ROOT . NEWS_PATH . $row['f_file']);
                $row['resu'] = $width_px . 'x' . $height_px;
            }
            if (ISADMIN == 1) {
                $row['icon_del'] = kf::gen_del_icon($row['id'], false, 'a_delfile');
                #, '', '&uselang=' . $this->langid . '&id=' . $id . '&epage=' . $_GET['epage'],                    'fileid'
                if ($row['resu'] != "")
                    $row['thumbnail'] = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../' . NEWS_PATH . $row['f_file'], 100, 90, 'admin/' . CACHE, true, 'resize');
            }
            $this->news['filelist'][] = $row;
        }

    }

    /**
     * news_admin_class::cmd_a_delfile()
     * 
     * @return
     */
    function cmd_a_delfile() {
        $this->NEWS_OBJ->del_afile($_GET['ident']);
        $this->ej();
    }


    /**
     * news_admin_class::cmd_a_new()
     * 
     * @return
     */
    function cmd_a_new() {
        list($id, $conid) = $this->NEWS_OBJ->gen_new_news($this->gbl_config['std_lang_id']);
        $this->msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=edit&id=' . $id);
        exit;
    }

    /**
     * news_admin_class::set_newslist_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_newslist_options($row) {
        $row['fck'] = create_html_editor('FORM_CON[content]', $row['content'], 500, 'Basic');
        $row['is_today'] = date('Y-m-d') == $row['ndate'];
        $row['relative_time'] = $this->relative_date($row['timeint']);
        $row['relative_time_class'] = $this->color_class($row['relative_time']);
        $row['ndate'] = (($row['ndate'] != '0000-00-00') ? my_date('d.m.Y', $row['ndate']) : '');
        $row['date'] = $row['ndate'];
        $row['date_print'] = date('d.m.Y', $row['timeint']);
        $row['n_lastchange'] = date('d.m.Y', $row['n_lastchange']);
        $row['implement'] = '{TMPL_NEWSINLAY_' . $row['NID'] . '}';
        $row['n_kid'] = ($row['n_kid'] == 0) ? -1 : $row['n_kid'];
        $row['group_ident'] = 'NIDENT' . $row['GINDENT'];
        $row['icon_edit'] = kf::gen_edit_icon($row['NID'], '&epage=' . $_GET['epage'], 'edit', 'id', $_SERVER['PHP_SELF']);
        $row['icon_del'] = kf::gen_del_icon($row['NID'], true, 'axdelete_item');
        #kf::gen_del_icon_reload($row['NID'], 'a_del', '{LBL_CONFIRM}', '&epage=' . $_GET['epage']);
        $row['icon_approve'] = kf::gen_approve_icon($row['NID'], $row['approval'], 'axapprove_item');
        $row['n_icon'] = ($row['n_icon'] != "") ? kf::gen_thumbnail('/' . NEWS_PATH . $row['n_icon'], $this->gbl_config['opt_boxthumb_width'], $this->gbl_config['opt_boxthumb_width']) :
            '';

        $row['link'] = $this->NEWS_OBJ->gen_detail_link($row);
        return $row;
    }

    /**
     * news_admin_class::load_newslist()
     * 
     * @param mixed $order
     * @param mixed $dc
     * @param mixed $gid
     * @param mixed $langid
     * @return
     */
    function load_newslist($order, $dc, $gid, $langid) {
        $this->newss_approved = array();
        $this->newss_notapproved = array();
        $this->newss = array();

        if ($dc == "")
            $dc = "DESC";
        if ($order == "")
            $order = "timeint";
        $flipped_dc = ($dc == "ASC") ? "DESC" : "ASC";
        $count_approved = 0;
        $result = $this->db->query("SELECT A.*,AC.*, A.id AS NID,K.*,COUNT(AF.id) AS AFCOUNT, A.group_id AS GINDENT FROM 
 		(" . TBL_CMS_NEWSLIST . " A 	 LEFT JOIN " . TBL_CMS_CUST . " K ON (A.n_kid=K.kid)),
 		(" . TBL_CMS_NEWSLIST . " AA 	 LEFT JOIN " . TBL_CMS_NEWSFILES . " AF ON (AA.id=AF.f_nid)),
 		(" . TBL_CMS_NEWSLIST . " AAA  LEFT JOIN " . TBL_CMS_NEWSCONTENT . " AC ON (AAA.id=AC.nid AND AC.lang_id=" . $langid . ")),
 		(" . TBL_CMS_NEWSLIST . " AAAA LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=AAAA.mid))
 		 WHERE A.group_id=" . intval($gid) . " 
 		 AND AA.id=A.id
 		 AND AAA.id=A.id
 		 AND AAAA.id=A.id
 		 GROUP BY A.id
 		 ORDER BY A." . $order . " " . $dc);
        $k = 0;
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_newslist_options($row);
            $k++;
            if ($k == 1)
                $first_object = $row;
            $this->newss[] = $row;

            if ($row['approval'] == 1) {
                $count_approved++;
                $this->newss_approved[] = $row;
            }
            else {
                $this->newss_notapproved[] = $row;
            }
        }

        $this->smarty->assign('newsgroup', $first_object);
        $this->NEWSADMIN['allnewslist'] = array(
            'table' => $this->newss,
            'newslist_approved' => $this->newss_approved,
            'newslist_notapproved' => $this->newss_notapproved,
            'count' => count($this->newss),
            'count_approved' => $count_approved,
            'flipped_dc' => $flipped_dc,
            );
    }

    /**
     * news_admin_class::cmd_newsgroups()
     * 
     * @return
     */
    function cmd_newsgroups() {
        $result = $this->db->query("SELECT *      FROM " . TBL_CMS_NEWSGROUPS . "      ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icon_edit'] = kf::gen_edit_icon($row['id'], '&epage=' . $_GET['epage'], 'edit', 'id', $_SERVER['PHP_SELF']);
            $row['icon_del'] = kf::gen_del_icon($row['id'], true, 'delgroup');
            $row['templselect'] = build_html_selectbox('FORM[' . $row['id'] . '][ng_tpl]', TBL_CMS_TEMPLATES, 'id', 'tpl_name',
                " WHERE modident='news' AND layout_group='1'", $row['ng_tpl']);
            $this->NEWSADMIN['ngroups'][] = $row;
        }
    }

    /**
     * news_admin_class::cmd_savegtab()
     * 
     * @return
     */
    function cmd_savegtab() {
        $FORM = $_POST['FORM'];
        foreach ($FORM as $id => $row) {
            update_table(TBL_CMS_NEWSGROUPS, 'id', $id, $row);
        }
        $this->hard_exit();
    }

    /**
     * news_admin_class::cmd_addgtab()
     * 
     * @return
     */
    function cmd_addgtab() {
        $FORM = $_POST['FORM'];
        $FORM['ng_tpl'] = 970;
        insert_table(TBL_CMS_NEWSGROUPS, $FORM);
        $this->TCR->redirect('epage=' . $_POST['epage'] . '&cmd=newsgroups');
        $this->msg('{LBLA_SAVED}');
        $this->hard_exit();
    }

    /**
     * news_admin_class::cmd_massdeletenews()
     * 
     * @return
     */
    function cmd_massdeletenews() {
        if (is_array($_POST['newsids']) && count($_POST['newsids']) > 0) {
            foreach ($_POST['newsids'] as $id) {
                $this->delete_news($id);
            }
        }
        $this->TCR->redirect('epage=' . $_POST['epage'] . '&cmd=list');
        $this->msg('{LBL_DELETED}');
        $this->hard_exit();
    }

    /**
     * news_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * news_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='news' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * news_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['groupid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSGROUPS . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_NEWSINLAY_' . (int)$cont_matrix_id . '}', 'tm_pluginfo' => $R['groupname']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * news_admin_class::cmd_ax_searchnews()
     * 
     * @return
     */
    function cmd_ax_searchnews() {
        $tab = "";
        $result = $this->db->query("SELECT *,K.id AS NID,G.id AS GID FROM 
	(" . TBL_CMS_NEWSLIST . " K 	LEFT JOIN " . TBL_CMS_NEWSCONTENT . " C ON (C.nid=K.id)),
	(" . TBL_CMS_NEWSLIST . " A 	LEFT JOIN " . TBL_CMS_NEWSGROUPS . " G ON (G.id=A.group_id))
	WHERE A.id=K.id AND (	
	K.id LIKE '%" . $_POST['setvalue'] . "%' OR
	K.n_author LIKE '%" . $_POST['setvalue'] . "%' OR
	C.title LIKE '%" . $_POST['setvalue'] . "%' OR
	C.introduction LIKE '%" . $_POST['setvalue'] . "%' OR
	K.n_kid LIKE '%" . $_POST['setvalue'] . "%')
	GROUP BY K.id
	ORDER BY C." . $_POST['orderby'] . " " . $_POST['direc']);
        while ($row = $this->db->fetch_array_names($result)) {
            $tab .= '<tr>
		<td>' . $row['AID'] . '</td>
		<td><a href="run.php?id=' . $row['NID'] . '&cmd=edit&epage=news.inc">' . $row['title'] . '</a></td>
		<td><a href="kreg.php?cmd=show_edit&kid=' . $row['a_kid'] . '">' . $row['a_kid'] . '</a></td>		
		<td><a href="run.php?epage=news.inc&cmd=list&gid=' . $row['GID'] . '">' . $row['groupname'] . '</a></td>
		<td>' . $row['n_author'] . '</td>
		<td>' . date('d.m.Y H:i:s', $row['timeint']) . '</td>
		<td>' . my_date('d.m.Y', $row['ndate']) . '</td>
		<td>' . date('d.m.Y H:i:s', $row['n_lastchange']) . '</td>
		<td class="text-right">' . kf::gen_edit_icon($row['NID'], '&epage=news.inc', 'edit', 'id', $_SERVER['PHP_SELF']) . '</td>
		</tr>';
        }
        if ($tab != "") {
            $tab = '{LBL_FOUND} ' . $this->db->num_rows($result) . '<br>
 <table class="table table-striped table-hover"  width="99%"><thead><tr>
  <th>ID</th>
	<th>{LBL_TITLE}</a></th>
	<th>Knr.</th>
	<th>Gruppe</th>
	<th>Author</th>
	<th>Eingestellt am</th>
	<th>Ver&ouml;ffentlicht f&uuml;r den</th>
	<th>Letzte &Auml;nderung</th>
	<th></th></tr></thead>' . $tab . '</table>';
        }
        else {
            $tab = '<div class="bg-info text-info">{LNL_NOSEARCHRESULT}</div>';
        }
        ECHORESULT(pure_translation(kf::translate_admin($tab), $_SESSION['alang_id']));
    }

    /**
     * news_admin_class::rebuild_page_index()
     * 
     * @return
     */
    function rebuild_page_index() {
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='news'");
        $result = $this->db->query("SELECT *,id as MID FROM " . TBL_CMS_NEWSLIST);
        while ($row = $this->db->fetch_array($result)) {
            $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($lang = $this->db->fetch_array_names($resultlang)) {
                $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSCONTENT . " WHERE lang_id=" . $lang['id'] . " AND nid='" . $row['id'] . "' LIMIT 1");
                $FORM = array_merge($row, (array )$FORM_CON);
                $link = '/news/' . $this->format_file_name($FORM['title']) . '.html';
                $query = array(
                    'cmd' => 'show',
                    'id' => $row['MID'],
                    'gid' => $FORM['group_id']);
                if ($FORM['title'] != "")
                    $this->connect_to_pageindex($link, $query, $row['id'], 'news', $lang['id']);
            }
        }
    }

    /**
     * news_admin_class::cmd_rebuildperma()
     * 
     * @return
     */
    function cmd_rebuildperma() {
        $this->rebuild_page_index();
        $this->ej();
    }

}

?>