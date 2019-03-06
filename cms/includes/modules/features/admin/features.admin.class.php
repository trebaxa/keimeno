<?php

/**
 * @package    features
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class features_admin_class extends features_master_class
{

    protected $FEATURES = array();

    /**
     * features_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        global $GRAPHIC_FUNC;
        parent::__construct();
        $this->GRAPHIC_FUNC = $GRAPHIC_FUNC;
        $this->TCR = new kcontrol_class($this);
        $this->load_groups();
        $this->load_features(0);
    }

    /**
     * features_admin_class::del_feat()
     * 
     * @param mixed $id
     * @return
     */
    function del_feat($id)
    {
        $FEAT = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATURES .
            " WHERE id=" . (int)$id);
        $this->db->query("DELETE FROM " . TBL_CMS_FEATURES . " WHERE id=" . (int)$id);
        if ($FEAT['f_image'] != "") {
            @unlink(CMS_ROOT . 'file_data/features/' . $FEAT['f_image']);
        }
    }

    /**
     * features_admin_class::cmd_del_feature()
     * 
     * @return
     */
    function cmd_del_feature()
    {
        $this->del_feat($_GET['ident']);
        $this->ej();
    }

    /**
     * features_admin_class::cmd_del_feature_group()
     * 
     * @return
     */
    function cmd_del_feature_group()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FEATURES . " WHERE f_gid=" .
            (int)$_GET['ident'] . " ORDER BY f_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->del_feat($row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_FEATUREGROUPS . " WHERE id=" . (int)$_GET['ident']);
        $this->ej();
    }


    /**
     * features_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item()
    {
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$_GET['ident'] . "' LIMIT 1");
        $this->hard_exit();
    }


    /**
     * features_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('FEATURES', $this->FEATURES);
    }


    /**
     * features_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf()
    {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * features_admin_class::load_groups()
     * 
     * @return
     */
    function load_groups()
    {
        $this->FEATURES['feature_groups'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FEATUREGROUPS .
            " WHERE 1 ORDER BY fg_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_feature_group');
            $this->FEATURES['feature_groups'][] = $row;
        }
    }


    /**
     * features_admin_class::load_features()
     * 
     * @param mixed $gid
     * @return
     */
    function load_features($gid)
    {
        $this->FEATURES['features'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FEATURES . " WHERE " . (($gid >
            0) ? " f_gid=" . (int)$gid : "1") . "  ORDER BY f_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_feature');
            $row[thumb] = kf::gen_thumbnail('/file_data/features/' . $row['f_image'], 100,
                75, 'crop', true);
            #PATH_CMS . 'admin/' . CACHE . $this->GRAPHIC_FUNC->makeThumb('../file_data/features/' . $row['f_image'], 100, 75, 'admin/' . CACHE, TRUE, 'crop');
            $this->FEATURES['features'][] = $row;
        }
        $this->load_groups();
        $CONFIG_OBJ = new config_class('FEATURES');
        # $this->FEATURES['config'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * features_admin_class::cmd_load_feature()
     * 
     * @return
     */
    function cmd_load_feature()
    {
        $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATURES .
            " WHERE id=" . (int)$_GET['id']);
        $FEATUREOBJ = $this->arr_trim($FEATUREOBJ);
        echo json_encode($FEATUREOBJ);
        $this->hard_exit();
    }

    /**
     * features_admin_class::cmd_load_feature_group()
     * 
     * @return
     */
    function cmd_load_feature_group()
    {
        $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATUREGROUPS .
            " WHERE id=" . (int)$_GET['id']);
        echo json_encode($this->arr_trimhsc($FEATUREOBJ));
        $this->hard_exit();
    }


    /**
     * features_admin_class::cmd_save_feature()
     * 
     * @return
     */
    function cmd_save_feature()
    {
        $FORM = (array )$_POST['FORM'];
        if ($_POST['id'] > 0) {
            $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATURES .
                " WHERE id=" . (int)$_POST['id']);
        }
        if ($_POST['id'] > 0) {
            update_table(TBL_CMS_FEATURES, 'id', $_POST['id'], $FORM);
            $id = $_POST['id'];
        } else {
            $id = insert_table(TBL_CMS_FEATURES, $FORM);
        }
        # img upload
        if (!is_dir(CMS_ROOT . 'file_data/features/'))
            mkdir(CMS_ROOT . 'file_data/features/', 0775);
        if ($_FILES['datei']['name'] != "") {
            if (validate_upload_file($_FILES['datei'])) {
                $RetVal = explode('.', $_FILES['datei']['name']);
                $file_extention = strtolower($RetVal[count($RetVal) - 1]);
                if ($file_extention == 'jpeg')
                    $file_extention = 'jpg';
                $new_file_name = CMS_ROOT . 'file_data/features/icon_' . (int)$id . '.' . $file_extention;
                move_uploaded_file($_FILES['datei']['tmp_name'], $new_file_name);
                clean_cache_like($new_file_name);
                chmod($new_file_name, 0755);
                $this->db->query("UPDATE " . TBL_CMS_FEATURES . " SET f_image='" . basename($new_file_name) .
                    "' WHERE id=" . (int)$id . " LIMIT 1");
            }
        }

        $this->ej('reload_features', $_POST['FORM']['f_gid']);
    }


    /**
     * features_admin_class::cmd_save_fgroup()
     * 
     * @return
     */
    function cmd_save_fgroup()
    {
        $FORM = (array )$_POST['FORM'];
        if ($_POST['id'] > 0) {
            $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATUREGROUPS .
                " WHERE id=" . (int)$_POST['id']);
        }
        if ($_POST['id'] > 0) {
            update_table(TBL_CMS_FEATUREGROUPS, 'id', $_POST['id'], $FORM);
            $id = $_POST['id'];
        } else {
            $id = insert_table(TBL_CMS_FEATUREGROUPS, $FORM);
            $this->ej('reload_page');
        }
        $this->ej('reload_features', $id);
    }

    /**
     * features_admin_class::cmd_reload_page()
     * 
     * @return
     */
    function cmd_reload_page()
    {
        $this->load_features();
        $this->load_groups();
        $this->parse_to_smarty();
        kf::echo_template('features.main');
    }

    /**
     * features_admin_class::cmd_reload_features()
     * 
     * @return
     */
    function cmd_reload_features()
    {
        $this->load_features($_GET['gid']);
        $this->parse_to_smarty();
        kf::echo_template('features.table');
    }

    /**
     * features_admin_class::load_plugin_fgroup_list()
     * 
     * @param mixed $params
     * @return
     */
    function load_plugin_fgroup_list($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FEATUREGROUPS .
            " ORDER BY fg_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * features_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE modident='features' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * features_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params)
    {
        $cont_matrix_id = (int)$params['id'];
        $group_id = $params['FORM']['feature_group_id'];
        $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATUREGROUPS .
            " WHERE id=" . (int)$group_id);
        $upt = array('tm_content' => '{TMPL_FEATUREINLAY_' . $cont_matrix_id . '}',
                'tm_pluginfo' => $FEATUREOBJ['fg_name']);
        $upt = $this->real_escape($upt);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $upt);
    }

    /**
     * features_admin_class::cmd_save_tab()
     * 
     * @return
     */
    function cmd_save_tab()
    {
        $sort = (array )$_POST['sort'];
        $data = keimeno_class::sort_multi_array($sort, 'f_order', SORT_ASC, SORT_NUMERIC);
        $k = 0;
        foreach ($data as $key => $row) {
            $k += 10;
            update_table(TBL_CMS_FEATURES, 'id', $row['id'], array('f_order' => $k));
        }
        $this->ej('reload_features', (int)$_POST['gid']);
    }

    /**
     * features_admin_class::cmd_edit_feature()
     * 
     * @return
     */
    function cmd_edit_feature()
    {
        $FEATUREOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_FEATURES .
            " WHERE id=" . (int)$_GET['id']);
        $this->FEATURES['feature'] = $this->arr_trim($FEATUREOBJ);
        $this->parse_to_smarty();
        kf::echo_template('feature.editor');
    }


}

?>