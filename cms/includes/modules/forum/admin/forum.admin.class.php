<?PHP

/**
 * @package    forum
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class forum_admin_class extends forum_master_class {

    /**
     * forum_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->load_groups();
    }

    /**
     * forum_admin_class::cmd_delgroup()
     * 
     * @return void
     */
    function cmd_delgroup() {
        $this->delete_fgroup($_GET['ident']);
        $this->msg('{LBL_DELETED}');
        $this->ej();
    }

    /**
     * forum_admin_class::set_fg_opt()
     * 
     * @param mixed $fgroup
     * @return
     */
    function set_fg_opt(&$fgroup) {
        $fgroup['icons'][] = kf::gen_del_icon($fgroup['id'], true, 'delgroup');
        $fgroup['icons'][] = kf::gen_edit_icon($fgroup['id'], '&epage=' . $_GET['epage'], 'edit', 'id', $_SERVER['PHP_SELF']);
        $fgroup['icons'][] = kf::gen_approve_icon($fgroup['id'], $fgroup['fg_approved'], 'approvegroup');
        $fgroup['icons'][] = kf::gen_plus_icon(0, '&gid=' . $fgroup['id'] . '&epage=' . $_GET['epage'], 'editforum', 'id', $_SERVER['PHP_SELF']);
    }

    /**
     * forum_admin_class::cmd_editforum()
     * 
     * @return void
     */
    function cmd_editforum() {
        $this->load_forum($_GET['id']);
    }

    function cmd_delforum() {
        $this->del_forum($_GET['id']);
        $this->msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=fgroups&epage=' . $_GET['epage']);
        exit;
    }

    /**
     * forum_admin_class::set_forum_opt()
     * 
     * @param mixed $forum
     * @return
     */
    function set_forum_opt(&$forum) {
        $forum['icons'][] = kf::gen_del_icon_reload($forum['id'], 'delforum', '{LBL_CONFIRM}', '&epage=' . $_GET['epage']);
        $forum['icons'][] = kf::gen_edit_icon($forum['id'], '&epage=' . $_GET['epage'], 'editforum', 'id', $_SERVER['PHP_SELF']);
        $forum['icons'][] = kf::gen_approve_icon($forum['id'], $forum['fn_approved'], 'approveforum');
    }

    /**
     * forum_admin_class::load_forum()
     * 
     * @param mixed $id
     * @return
     */
    function load_forum($id) {
        $F = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMF . " WHERE id=" . (int)$id);
        $this->set_forum_opt($F);
        $this->smarty->assign('forum', $F);
    }

    /**
     * forum_admin_class::save_forum()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @return
     */
    function save_forum($FORM, $id) {
        $id = (int)$id;
        $GROUP = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " WHERE id=" . (int)$FORM['fn_gid']);
        if ($id > 0) {
            update_table(TBL_CMS_FORUMF, 'id', $id, $FORM);
        }
        else {
            $id = insert_table(TBL_CMS_FORUMF, $FORM);
            $this->add_forum_pageindex($GROUP['fg_name'], $FORM['fn_name'], $id);
        }

    }


    /**
     * forum_admin_class::cmd_save_forum()
     * 
     * @return void
     */
    function cmd_save_forum() {
        $this->save_forum($_POST['FORM'], $_POST['id']);
        $this->msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?aktion=fgroups&epage=' . $_GET['epage']);
        exit;
    }

    /**
     * forum_admin_class::load_forums()
     * 
     * @param mixed $gid
     * @return
     */
    function load_forums($gid) {
        $this->foren = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FORUMF . " WHERE fn_gid=" . (int)$gid . " ORDER BY fn_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_forum_opt($row);
            $this->foren[] = $row;
        }
        return $this->foren;
    }

    /**
     * forum_admin_class::load_groups()
     * 
     * @return
     */
    function load_groups() {
        $this->ftable = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " ORDER BY fg_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_fg_opt($row);
            $row['foren'] = $this->load_forums($row['id']);
            $this->ftable[] = $row;
        }
        $this->smarty->assign('ftable', $this->ftable);
        return $this->ftable;
    }

    /**
     * forum_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('FORUM', $this->FORUM);
    }

    /**
     * forum_admin_class::cmd_conf()
     * 
     * @return void
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('forum');
        $this->FORUM['conf'] = $CONFIG_OBJ->buildTable();
    }


    /**
     * forum_admin_class::cmd_savegroup()
     * 
     * @return void
     */
    function cmd_savegroup() {
        $this->save_fgroup($_POST['FORM'], $_POST['id']);
        $this->msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?aktion=fgroups&epage=' . $_GET['epage']);
        exit;
    }

    /**
     * forum_admin_class::load_group()
     * 
     * @param mixed $id
     * @return
     */
    function load_group($id) {
        $F = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " WHERE id=" . (int)$id);
        $this->set_fg_opt($F);
        $this->smarty->assign('fgroup', $F);
    }

    /**
     * forum_admin_class::cmd_edit()
     * 
     * @return void
     */
    function cmd_edit() {
        $this->load_group($_GET['id']);
    }

    /**
     * forum_admin_class::approve_forum()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve_forum($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_FORUMF . " SET fn_approved='" . (($value == 1) ? 1 : 0) . "' WHERE id='" . intval($id) . "' LIMIT 1");
    }

    /**
     * forum_admin_class::cmd_approveforum()
     * 
     * @return
     */
    function cmd_approveforum() {
        $this->approve_forum($_GET['value'], $_GET['ident']);
        $this->ej();
    }

    /**
     * forum_admin_class::approve_group()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve_group($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_FORUMGROUPS . " SET fg_approved='" . (($value == 1) ? 1 : 0) . "' WHERE id='" . intval($id) . "' LIMIT 1");
    }

    /**
     * forum_admin_class::cmd_approvegroup()
     * 
     * @return
     */
    function cmd_approvegroup() {
        $this->approve_group($_GET['value'], $_GET['ident']);
        $this->ej();
    }


    /**
     * forum_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='forum' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * forum_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $upt = array('tm_content' => '{TMPL_FORUMINLAY_' . (int)$cont_matrix_id . '}', 'tm_pluginfo' => 'Forum');
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
        $this->set_content_mode($cont_matrix_id, 'forum', 'modules/forum/forum.inc');
    }

    /**
     * forum_admin_class::cmd_rebuildpageindex()
     * 
     * @return void
     */
    function cmd_rebuildpageindex() {
        $k = $i = 0;
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='forum'");
        # Foren Index
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FORUMF . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $GROUP = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " WHERE id=" . $row['fn_gid']);
            $this->add_forum_pageindex($GROUP['fg_name'], $row['fn_name'], $row['id']);
            $k++;
        }

        # Themen
        $result = $this->db->query("SELECT T.id AS TID,fg_name,fn_name,t_name  
		FROM " . TBL_CMS_FORUMTHEMES . " T, " . TBL_CMS_FORUMF . " F, " . TBL_CMS_FORUMGROUPS . " G 		
		WHERE G.id=F.fn_gid AND T.t_fid=F.id
		GROUP BY T.id
		ORDER BY T.t_time DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->add_theme_pageindex($row['fg_name'], $row['fn_name'], $row['t_name'], $row['TID']);
            $i++;
        }
        $this->msg($k . ' Foren, ' . $i . ' Themen aktualisiert.');
        $this->ej();
    }

}
