<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class admingroup_class extends keimeno_class {

    var $admin_groups = array();
    var $html_tree = "";
    var $AGROUP = array();

    /**
     * admingroup_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * admingroup_class::cmd_delete_role()
     * 
     * @return
     */
    function cmd_delete_role() {
        if (get_data_count(TBL_CMS_ADMIN_ROLEMATRIX, '*', "am_roleid=" . (int)$_GET['ident']) > 0) {
            $this->msge('{LBL_ROLE} {LBL_INUSE}');
            $this->ej();
        }
        $this->db->query("DELETE FROM " . TBL_CMS_ADMIN_ROLES . " WHERE id=" . (int)$_GET['ident'] . " AND id>1 LIMIT 1");
        $this->ej();
    }

    /**
     * admingroup_class::cmd_delete_group()
     * 
     * @return
     */
    function cmd_delete_group() {
        if (get_data_count(TBL_CMS_ADMINS, 'id', "gid=" . (int)$_GET['ident']) > 0) {
            $this->msge('{LBL_GROUP} {LBL_INUSE}');
            $this->ej();

        }
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINGROUPS . " WHERE id=" . (int)$_GET['ident'] . " AND id>1 LIMIT 1");
        $this->msg('{LBL_DELETED}');
        $this->ej();
    }

    /**
     * admingroup_class::cmd_ag_savetable()
     * 
     * @return
     */
    function cmd_ag_savetable() {
        foreach ($_POST['FORM'] as $id => $row) {
            $row['ag_sort'] = (int)$row['ag_sort'];
            update_table(TBL_CMS_ADMINGROUPS, 'id', $id, $row);
        }
        $this->hard_exit();
    }

    /**
     * admingroup_class::cmd_save_role()
     * 
     * @return
     */
    function cmd_save_role() {
        $id = (int)$_POST['id'];
        if ($_POST['FORM']['rl_ident'] == "") {
            $_POST['FORM']['rl_ident'] = strtoupper(substr($_POST['FORM']['rl_name'], 0, 3));
        }
        $_POST['FORM']['rl_ident'] = strtoupper(strip_tags($_POST['FORM']['rl_ident']));
        foreach ($_POST['FORM'] as $key => $wert) {
            if (strlen($wert) == 0) {
                $this->add_err('{LBL_PLEASEFILLOUT}...');
                break;
            }
        }
        if ($this->get_error_count() > 0) {
            $this->TCR->set_fault_form(true);
            $this->TCR->reset_cmd('edit');
            $this->cmd_edit();
            return;

        }
        if ($id == 0) {
            $id = insert_table(TBL_CMS_ADMIN_ROLES, $_POST['FORM']);
            $this->TCR->add_msg('{LBLA_SAVED}');
            $this->TCR->redirect('cmd=roles&id=' . $id . '&epage=' . $_POST['epage']);
            $this->hard_exit();
        }
        else {
            update_table(TBL_CMS_ADMIN_ROLES, 'id', $id, $_POST['FORM']);
            $this->ej();
        }

    }

    /**
     * admingroup_class::cmd_save_group()
     * 
     * @return
     */
    function cmd_save_group() {
        $id = (int)$_POST['id'];
        foreach ($_POST['FORM'] as $key => $wert) {
            if (strlen($wert) == 0) {
                $this->add_err('{LBL_PLEASEFILLOUT}...');
                break;
            }
        }

        if ($id == 0) {
            if (get_data_count(TBL_CMS_ADMINGROUPS, 'id', "mgname='" . $_POST['FORM']['mgname'] . "'") > 0) {
                $this->add_err('Group already exists.');
            }
        }


        if ($this->get_error_count() > 0) {
            $this->TCR->set_fault_form(true);
            $this->TCR->reset_cmd('edit');
            $this->cmd_edit();
            return;
        }

        if ($id == 0) {
            $id = insert_table(TBL_CMS_ADMINGROUPS, $_POST['FORM']);
        }
        else {
            $menu = (array )$_POST['menue_id'];
            foreach ($menu as $key => $wert) {
                if ($_POST['FORM']['allowed'] != '')
                    $_POST['FORM']['allowed'] .= ';';
                $_POST['FORM']['allowed'] .= $wert;
            }
            update_table(TBL_CMS_ADMINGROUPS, 'id', $id, $_POST['FORM']);

        }

        # update roles matrix
        $this->db->query("DELETE FROM " . TBL_CMS_ADMIN_ROLEMATRIX . " WHERE am_groupid=" . $id);
        $rolesids = (array )$_POST['roleids'];
        foreach ($rolesids as $rid) {
            $arr = array('am_roleid' => $rid, 'am_groupid' => $id);
            insert_table(TBL_CMS_ADMIN_ROLEMATRIX, $arr);
        }

        kf::load_permissions();
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * admingroup_class::cmd_save_gpo()
     * 
     * @return
     */
    function cmd_save_gpo() {
        $this->perm_class->savePerms($_POST['id'], $_POST['FORM']);
        $this->ej();
    }

    /**
     * admingroup_class::load_roles_by_group()
     * 
     * @return
     */
    function load_roles_by_group($groupid) {
        $names = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMIN_ROLES . " G, " . TBL_CMS_ADMIN_ROLEMATRIX . " M 
		WHERE G.id=M.am_roleid AND M.am_groupid=" . $groupid . "
		ORDER BY G.rl_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $names[] = $row['rl_name'];
        }

        return implode(', ', $names);
    }

    /**
     * admingroup_class::load_groups()
     * 
     * @return
     */
    function load_groups() {
        $this->AGROUP['permgroups'] = $this->perm_class->load_perm_groups();
        $sql_direc = ($_REQUEST['direc'] == 'D') ? 'DESC' : 'ASC';
        $sql_col = ($_REQUEST['col'] == '') ? 'ag_sort' : trim($_REQUEST['col']);
        $result = $this->db->query("SELECT *,G.id AS GID FROM " . TBL_CMS_ADMINGROUPS . " G
		WHERE 1
		ORDER BY " . $sql_col . " " . $sql_direc);
        while ($row = $this->db->fetch_array_names($result)) {
            if ($_SERVER['SERVER_NAME'] == "www.cms.trebaxa.com") {
                $row['edit_possible'] = true;
            }
            else {
                $row['edit_possible'] = $row['GID'] > 1;
            }
            $row['role_names'] = $this->load_roles_by_group($row['GID']);
            $row['ag_sort'] = (int)$row['ag_sort'];
            $row['icons'][] = ($row['edit_possible'] === true) ? kf::gen_edit_icon($row['GID']) . kf::gen_del_icon($row['GID'], true, 'delete_group') .
                '<a class="btn btn-default" title="{LBL_GROUP_POLICIES}" href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=gpo&id=' . $row['GID'] .
                '"><i class="fa fa-shield"><!----></i></a>' : '';
            $this->admin_groups[] = $row;
        }
        $this->load_roles();
    }

    /**
     * admingroup_class::load_roles()
     * 
     * @return
     */
    function load_roles() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMIN_ROLES . " R WHERE 1 ORDER BY rl_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = ($row['id'] > 1) ? kf::gen_edit_icon($row['id'], '', 'roles') . kf::gen_del_icon($row['id'], true, 'delete_role') : '';
            #kf::gen_del_icon_reload($row['id'], 'delete_role', '{LBLA_CONFIRM}')
            $this->roles[] = $row;
        }
    }

    /**
     * admingroup_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $arr = array(
            'admin_groups' => $this->admin_groups,
            'admin_roles' => $this->roles,
            'loaded_group' => $this->loaded_group,
            'loaded_role' => $this->loaded_role);
        $this->AGROUP = array_merge($this->AGROUP, $arr);
        $this->smarty->assign('AGROUP', $this->AGROUP);
    }

    /**
     * admingroup_class::load_group()
     * 
     * @return
     */
    function load_group() {
        $this->loaded_group = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE id=" . (int)$_REQUEST['id'] . " LIMIT 1");
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMIN_ROLEMATRIX . "  WHERE am_groupid=" . (int)$_REQUEST['id']);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->AGROUP['roleids'][] = $row['am_roleid'];
        }
        $this->AGROUP['roleids'] = (array )$this->AGROUP['roleids'];
    }

    /**
     * admingroup_class::load_role()
     * 
     * @return
     */
    function load_role() {
        $this->loaded_role = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMIN_ROLES . " WHERE id=" . (int)$_REQUEST['id'] . " LIMIT 1");
    }


    /**
     * admingroup_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $this->perm_class->allowAllToGid(1); // WICHTIG! erzwingt alle Rechte für Administratoren
        if ($_REQUEST['id'] == 1) {
            $this->msge('Not allowed to modfiy administrator group.');
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=admin_groups.inc');
            $this->hard_exit();
        }
        $this->load_group();
        if ($_REQUEST['id'] > 0) {
            $MA = new mainadmin_class();
            $MA->load_admin_menu();
            $this->AGROUP['allowed'] = explode(';', $this->loaded_group['allowed']);
        }

        # load page access
        $TREE = new nestedArrClass();
        $TREE->db = $this->db;
        $TREE->label_id = 'id';
        $TREE->create_result_and_array("SELECT id, parent, description, approval FROM " . TBL_CMS_TEMPLATES . "  WHERE gbl_template=0 AND c_type='T' 
        ORDER BY parent,morder", 0, 0, -1);
        $this->AGROUP['websitetree'] = $TREE->menu_array;

        # Page access
        $page_access = new pageaccess_class();
        $this->AGROUP['page_noaccess'] = $page_access->load_page_access($_REQUEST['id']);
    }


    /**
     * admingroup_class::cmd_save_pageaccess()
     * 
     * @return
     */
    function cmd_save_pageaccess() {
        $FORM = (array )$_POST['FORM'];
        $this->db->query("DELETE FROM " . TBL_CMS_ADMIN_PAGEACCESS . " WHERE p_groupid=" . (int)$_POST['id']);
        foreach ($FORM as $key => $row) {
            $row['p_groupid'] = (int)$_POST['id'];
            $row['p_id'] = $key;
            insert_table(TBL_CMS_ADMIN_PAGEACCESS, $row);
        }
        $this->ej();
    }


    /**
     * admingroup_class::cmd_gpo()
     * 
     * @return
     */
    function cmd_gpo() {
        global $PERM;
        $this->load_group();
        $this->smarty->assign('permlist', $PERM->loadPermFromGroup($_GET['id']));
    }

    /**
     * admingroup_class::cmd_add_policy()
     * 
     * @return
     */
    function cmd_add_policy() {
        $FORM = (array )$_POST['FORM'];
        if ($_SERVER['SERVER_NAME'] == "www.dev.keimeno.de") {
            $FORM['p_core'] = 1;
            $FORM['p_groups'] = 1;
        }
        insert_table(TBL_CMS_APERMISSIONS, $FORM);
        $this->TCR->redirect('epage=' . $_REQUEST['epage']);
        $this->msg('{LBLA_SAVED}');
        $this->hard_exit();
    }

}
