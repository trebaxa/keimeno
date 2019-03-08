<?php

/**
 * @package    gblvars
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class gblvars_admin_class extends gblvars_master_class {

    protected $GBLVARS = array();

    /**
     * gblvars_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * gblvars_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('GBLVARS', $this->GBLVARS);
    }


    /**
     * gblvars_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * gblvars_admin_class::load_vars()
     * 
     * @return
     */
    function load_vars() {
        $this->GBLVARS['vars'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLVARS . " WHERE 1 ORDER BY var_desc");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['var_settings'] = unserialize($row['var_settings']);
            $row['icons'][] = kf::gen_del_icon($row['var_name'], true, 'axdelgblvars');
            $row['icons'][] = kf::gen_ax_edit_icon($row['var_name'], 'js-gblvar-editor', 'ax_edit');
            $row['smarty'] = '<%$gblvars.' . $row['var_name'] . '%>';
            $row['var_settings']['list'] = explode('|', $row['var_settings']['list']);
            $this->GBLVARS['vars'][$row['var_name']] = $row;
        }
    }

    /**
     * gblvars_admin_class::cmd_load_var_tree()
     * 
     * @return
     */
    function cmd_load_var_tree() {
        $this->load_vars();
        $this->parse_to_smarty();
        kf::echo_template('gblvars.tree');
    }

    /**
     * gblvars_admin_class::cmd_ax_create_gblvars()
     * 
     * @return
     */
    function cmd_ax_create_gblvars() {
        $FORM = (array )$_REQUEST['FORM'];
        $FORM['var_desc'] = strip_tags($FORM['var_desc']);
        $FORM['var_name'] = 'new_node';
        insert_table(TBL_CMS_GBLVARS, $FORM);
        ECHO json_encode(array('id' => $FORM['var_name']));
        $this->hard_exit();
    }

    /**
     * gblvars_admin_class::cmd_ax_edit()
     * 
     * @return
     */
    function cmd_ax_edit() {
        $this->GBLVARS['VAR'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_GBLVARS . " WHERE var_name='" . $_GET['id'] . "'");
        $this->GBLVARS['VAR']['var_settings'] = unserialize($this->GBLVARS['VAR']['var_settings']);
        $this->parse_to_smarty();
        kf::echo_template('gblvars.editor');
    }

    /**
     * gblvars_admin_class::repair_index()
     * 
     * @return
     */
    function repair_index() {
        $FORM['var_name'] = "";
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLVARS . " WHERE var_name='new_node' ORDER BY var_desc");
        while ($row = $this->db->fetch_array_names($result)) {
            $FORM = array('var_name' => str_replace('-', '_', $this->format_file_name($row['var_desc'])));
            while (get_data_count(TBL_CMS_GBLVARS, '*', "var_name='" . $FORM['var_name'] . "'") > 0) {
                $k++;
                $FORM = array('var_name' => str_replace('-', '_', $this->format_file_name($row['var_desc'])) . '_' . $k);
            }
            update_table(TBL_CMS_GBLVARS, 'var_name', 'new_node', $FORM);
        }
        return $FORM['var_name'];
    }

    /**
     * gblvars_admin_class::cmd_rename_gblvars()
     * 
     * @return
     */
    function cmd_rename_gblvars() {
        $FORM = $_REQUEST['FORM'];
        update_table(TBL_CMS_GBLVARS, 'var_name', $_GET['id'], $FORM);

        $FORM['var_name'] = $_GET['id'];
        $var_name = $this->repair_index();
        if ($var_name != "")
            $FORM['var_name'] = $var_name;


        ECHO json_encode(array('id' => $FORM['var_name'], 'label' => $FORM['var_desc']));
        $this->hard_exit();
    }

    /**
     * gblvars_admin_class::cmd_axdelgblvars()
     * 
     * @return
     */
    function cmd_axdelgblvars() {
        $this->db->query("DELETE FROM " . TBL_CMS_GBLVARS . " WHERE var_name='" . $_GET['ident'] . "'");
        $this->msg("{LBL_DELETED}");
        $this->ej('reload_gblvar_tree');
    }


    /**
     * gblvars_admin_class::cmd_ax_start()
     * 
     * @return
     */
    function cmd_ax_start() {
        $this->load_vars();
        $this->parse_to_smarty();
        kf::echo_template('gblvars');
    }

    /**
     * gblvars_admin_class::cmd_ax_load_vars()
     * 
     * @return
     */
    function cmd_ax_load_vars() {
        $this->load_vars();
        $this->parse_to_smarty();
        kf::echo_template('gblvars.table');
    }

    /**
     * gblvars_admin_class::cmd_save_table()
     * 
     * @return
     */
    function cmd_save_table() {
        foreach ($_POST['FORM'] as $var_name => $row) {
            update_table(TBL_CMS_GBLVARS, 'var_name', $var_name, $row);
        }
        $this->ej();
    }

    /**
     * gblvars_admin_class::cmd_save_var()
     * 
     * @return
     */
    function cmd_save_var() {
        $var_name = $_POST['var_name'];
        $FORM = (array )$_REQUEST['FORM'];
        $SET = (array )$_REQUEST['SETTING'];
        #save new settings
        $FORM['var_settings'] = serialize($this->arr_trim_striptags($SET));
        update_table(TBL_CMS_GBLVARS, 'var_name', $var_name, $FORM);
        $var_name_repaired = $this->repair_index();
        $var_name = ($var_name_repaired != "") ? $var_name_repaired : $var_name;
        $this->ej('set_gblvar_id', '"' . $var_name . '"');
    }

    /**
     * gblvars_admin_class::load_page_gblvars()
     * 
     * @param mixed $params
     * @return
     */
    function load_page_gblvars($params) {
        $this->load_vars();
        $this->GBLVARS['template'] = $params['template'];
        $saved_gblvars = array();
        if (!empty($params['template']['formcontent']['t_gblvars'])) {
            $saved_gblvars = $this->GBLVARS['gblvarset'] = unserialize($params['template']['formcontent']['t_gblvars']);
        }
        foreach ($saved_gblvars as $key => $value) {
            if (isset($this->GBLVARS['vars'][$key]))
                $this->GBLVARS['vars'][$key]['var_value'] = $value['var_value'];
        }
        $this->parse_to_smarty();
        return $params;
    }

    /**
     * gblvars_admin_class::cmd_save_page_settings()
     * 
     * @return
     */
    function cmd_save_page_settings() {
        $SET = (array )$_POST['FORM'];
        $FORM = array('t_gblvars' => serialize($SET));
        update_table(TBL_CMS_TEMPCONTENT, 'id', $_POST['tcid'], $FORM);
        $this->ej();
    }

}

?>