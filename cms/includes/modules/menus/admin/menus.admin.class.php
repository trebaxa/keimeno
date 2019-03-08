<?php

/**
 * @package    menus
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class menus_admin_class extends menus_master_class {

    protected $MENUS = array();

    /**
     * menus_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->nested->label_parent = 'mm_parent';
        $this->nested->label_id = 'id';
    }


    /**
     * menus_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('MENUS', $this->MENUS);
    }

    /**
     * menus_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * menus_admin_class::cmd_save_menu()
     * 
     * @return
     */
    function cmd_save_menu() {
        $FORM = (array )$_POST['FORM'];
        if ($_POST['id'] > 0) {
            update_table(TBL_MMENU, 'id', $_POST['id'], $FORM);
        }
        else {
            $id = insert_table(TBL_MMENU, $FORM);
            $this->ej('reload_menus');
        }
        $this->ej();
    }

    /**
     * menus_admin_class::set_menu_opt()
     * 
     * @param mixed $arr
     * @return
     */
    function set_menu_opt($arr) {
        foreach ($arr as $key => $row) {
            $arr[$key]['icons'][] = kf::gen_del_icon($row['id'], true, 'del_menu');
            $arr[$key]['icons'][] = kf::gen_edit_icon($row['id'], '', 'edit_menu');
        }
        return $arr;
    }

    /**
     * menus_admin_class::cmd_del_menu()
     * 
     * @return
     */
    function cmd_del_menu() {
        $this->db->query("DELETE FROM " . TBL_MMENU . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * menus_admin_class::cmd_edit_man_item()
     * 
     * @return void
     */
    function cmd_edit_man_item() {
        $this->MENUS['item'] = $this->load_item($_GET['id']);
        $this->load_standards($_GET['menuid'], $this->MENUS['item']['mm_parent']);
        $this->parse_to_smarty();
        kf::echo_template('menus.edit.medit');
    }

    /**
     * menus_admin_class::cmd_reload_menus()
     * 
     * @return
     */
    function cmd_reload_menus() {
        $this->MENUS['table'] = $this->set_menu_opt($this->load_menus());
        $this->parse_to_smarty();
        kf::echo_template('menus.table');

    }

    /**
     * menus_admin_class::cmd_save_menu_table()
     * 
     * @return
     */
    function cmd_save_menu_table() {
        $FORM = (array )$_POST['FORM'];
        foreach ($FORM as $key => $row) {
            update_table(TBL_MMENU, 'id', $key, $row);
        }
        $this->ej();
    }

    /**
     * menus_admin_class::menu_opt()
     * 
     * @param mixed $arr
     * @return
     */
    function menu_opt(&$arr) {
        foreach ($arr as $key => $row) {
            if (count($row['children']) > 0) {
                $this->menu_opt($row['children']);
            }
            else {
                $arr[$key]['icons'][] = kf::gen_del_icon($row['id'], false, 'del_nested_item');
            }
        }
    }

    function load_standards($id, $select_node_id = 0) {
        $this->MENUS['menu'] = $this->load_menu($id);
        # Load nested Menu
        $nested_menu = $this->nested->create_result_and_array_by_array($this->load_mmenu_matrix($id), 0, 0, -1);
        $this->menu_opt($nested_menu);
        $this->MENUS['nested_menu'] = $nested_menu;
        $this->MENUS['menu_selectox'] = $this->nested->output_as_selectbox($select_node_id);

        # Load original menu
        $this->nested->label_column = 'description';
        $this->nested->label_id = 'id';
        $this->nested->label_parent = 'parent';
        $nested_menu_org = $this->nested->create_result_and_array_by_array($this->load_org_menu(), 0, 0, -1);
        $this->MENUS['nested_menu_org'] = $nested_menu_org;
        $this->MENUS['menuorg_selectox'] = $this->nested->output_as_selectbox();
        $this->MENUS['example'] = file_get_contents(CMS_ROOT . 'includes/modules/menus/admin/tpl/menus.example.tpl');
    }

    /**
     * menus_admin_class::cmd_edit_menu()
     * 
     * @return
     */
    function cmd_edit_menu() {
        $this->load_standards($_GET['id']);
    }

    /**
     * menus_admin_class::cmd_add_item()
     * 
     * @return
     */
    function cmd_add_item() {
        $FORM = (array )$_POST['FORM'];
        $itemid = (isset($_POST['itemid'])) ? (int)$_POST['itemid'] : 0;
        if ($itemid > 0) {
            update_table(TBL_MMENUMATRIX, 'id', $itemid, $FORM);
        }
        else {
            insert_table(TBL_MMENUMATRIX, $FORM);
        }
        $this->ej('reload_mmtree');
    }

    /**
     * menus_admin_class::cmd_delete_item()
     * 
     * @return
     */
    function cmd_delete_item() {
        $this->db->query("DELETE FROM " . TBL_MMENUMATRIX . " WHERE id=" . $_GET['id']);
        $this->hard_exit();
    }

    /**
     * menus_admin_class::cmd_reload_mmtree()
     * 
     * @return
     */
    function cmd_reload_mmtree() {
        $this->cmd_edit_menu();
        $this->parse_to_smarty();
        kf::echo_template('menus.nestedtree');
    }

    /**
     * menus_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_MMENU . " ORDER BY m_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * menus_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = (int)$params['FORM']['menuid'];
        $R = $this->load_menu($id);
        $upt = array('tm_content' => '{TMPL_MMENU_' . (int)$cont_matrix_id . '}', 'tm_pluginfo' => $R['m_name']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * websites_class::cmd_ax_sort_menutree()
     * 
     * @return
     */
    function cmd_ax_sort_menutree() {
        $next_node_id = (int)$_GET['next_node_id'];
        $prev_node_id = (int)$_GET['prev_node_id'];
        $parent = (int)$_GET['parent'];

        $next_node = $this->db->query_first("SELECT * FROM " . TBL_MMENUMATRIX . " WHERE id=" . $next_node_id);
        $prev_node = $this->db->query_first("SELECT * FROM " . TBL_MMENUMATRIX . " WHERE id=" . $prev_node_id);
        #  $thisnode = $this->db->query_first("SELECT * FROM " . TBL_MMENUMATRIX . " WHERE id=" . $_GET['tid']);

        if ($next_node['id'] > 0) {
            $morder = $next_node['mm_order'] - 1;
        }
        else
            if ($prev_node['id'] > 0) {
                $morder = $prev_node['mm_order'] + 1;
            }
            else {
                $morder = 0;
            }

            update_table(TBL_MMENUMATRIX, 'id', $_GET['tid'], array('mm_parent' => $parent, 'mm_order' => $morder));
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_MMENUMATRIX . " WHERE mm_parent=" . $parent);
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        $arr = self::sort_multi_array($arr, 'mm_order', SORT_ASC, SORT_NUMERIC);
        $k = 0;
        foreach ($arr as $key => $row) {
            $k += 10;
            update_table(TBL_MMENUMATRIX, 'id', $row['id'], array('mm_order' => $k));
        }

        $this->hard_exit();
    }

}

?>