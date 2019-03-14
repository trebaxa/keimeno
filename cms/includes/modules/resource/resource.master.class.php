<?PHP

/**
 * @package    resource
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_RESRC', TBL_CMS_PREFIX . 'resrc');
DEFINE('TBL_RESRCDV', TBL_CMS_PREFIX . 'resrc_dv');
DEFINE('TBL_RESRC_CONTENT', TBL_CMS_PREFIX . 'resrc_content');
DEFINE('TBL_RESRCVARS', TBL_CMS_PREFIX . 'resrc_vars');
DEFINE('TBL_RESRCPL', TBL_CMS_PREFIX . 'resrc_tpl');


class resource_master_class extends modules_class {

    var $froot = "";
    var $file_root = "";
    var $forbidden_column_arr = array();
    var $nested = null;

    /**
     * resource_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->froot = CMS_ROOT . 'file_data/resource/images/';
        $this->file_root = CMS_ROOT . 'file_data/resource/files/';
        $this->forbidden_column_arr = array('ds_cid', 'ds_settings');
        $this->nested = new nestedArrClass();
        $this->nested->label_column = 'description';
        $this->nested->label_id = 'id';
        $this->nested->label_parent = 'parent';
    }

    /**
     * resource_master_class::load_flx_tpls()
     * 
     * @return
     */
    function load_flx_tpls() {
        $arr = array();
        $result = $this->db->query("SELECT *,F.id AS FID FROM " . TBL_RESRC . " F WHERE 1 ORDER BY f_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * resource_master_class::load_vars_by_vcid()
     * sss
     * @return
     */
    function load_content_table($ftid) {
        $arr = array();
        $k = 0;
        $result = $this->db->query("SELECT *,F.id AS CID FROM " . TBL_RESRC_CONTENT . " F WHERE c_ftid=" . (int)$ftid . " ORDER BY c_sort");
        while ($row = $this->db->fetch_array_names($result)) {
            $k += 10;
            $row['c_sort'] = $k;
            $row['link'] = self::gen_resr_link($row['CID']);
            $row['link_resrc'] = dao_class::get_data_first(TBL_CMS_PAGEINDEX, array(
                'pi_local' => 'de',
                'pi_modident' => 'resource',
                'pi_relatedid' => $row['id']));
            $row['icons'][] = kf::gen_del_icon($row['CID'], true, 'del_content');
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * resource_master_class::load_content_table_fe()
     * 
     * @param mixed $ftid
     * @return
     */
    function load_content_table_fe($ftid, $v_settings = array()) {
        $arr = array();
        $cid = array();
        if (isset($v_settings['resrc']['cid']) && count($v_settings['resrc']['cid']) > 0) {
            $cid = implode(',', $v_settings['resrc']['cid']);
            if ((int)$cid[0] == 0) {
                $cid = array();
            }
        }
        $direc = (isset($v_settings['resrc']['direc']) && $v_settings['resrc']['direc'] == 'DESC') ? 'DESC' : 'ASC';
        $rand_sort = (isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] == -1) ? " RAND()" : " F.c_sort";
        $result = $this->db->query("SELECT F.*,F.id AS CID FROM " . TBL_RESRC_CONTENT . " F, " . TBL_RESRCVARS . " V WHERE c_ftid=" . (int)$ftid . " 
        AND F.id=V.v_cid 
        " . ((isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] > 0) ? " AND V.v_vid=" . (int)$v_settings['resrc']['sort'] : "") . "
        " . ((count($cid) > 0) ? " AND F.id IN (" . $cid . ")" : "") . "
        GROUP BY F.id
        ORDER BY 
        " . ((isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] > 0) ? " v_value " . $direc : $rand_sort) . "
        " . ((isset($v_settings['resrc']['quantity']) && (int)$v_settings['resrc']['quantity'] > 0) ? " LIMIT 0," . (int)$v_settings['resrc']['quantity'] : ""));
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * resource_master_class::load_content_table()
     * 
     * @param mixed $id
     * @return
     */
    function load_content($id) {
        $FLEX = $this->db->query_first("SELECT *,F.id AS CID FROM " . TBL_RESRC_CONTENT . " F WHERE id=" . (int)$id);
        return $FLEX;
    }

    /**
     * resource_master_class::load_resrc()
     * 
     * @param mixed $id
     * @return
     */
    function load_resrc($id) {
        $FLEX = $this->db->query_first("SELECT *,F.id AS FID FROM " . TBL_RESRC . " F WHERE id=" . (int)$id);
        return $FLEX;
    }

    /**
     * resource_master_class::delete_flexvar_value()
     * 
     * @param mixed $content_matrix_id
     * @param mixed $vid
     * @return
     */
    function delete_flexvar_value($v_cid, $vid) {
        $this->db->query("DELETE FROM " . TBL_RESRCVARS . " WHERE v_cid=" . (int)$v_cid . " AND v_vid='" . $vid . "'");
    }

    /**
     * resource_master_class::load_dataset_vars()
     * 
     * @param mixed $id
     * @return
     */
    function load_dataset_vars($id) {
        $arr = array();
        $FLEX = $this->load_resrc($id);
        if ($FLEX['f_table'] != "") {
            $result = $this->db->query("SHOW COLUMNS FROM " . $FLEX['f_table']);
            while ($row = $this->db->fetch_array_names($result)) {
                $arr[] = $row;
            }
        }
        return $arr;
    }

    /**
     * resource_master_class::load_dataset_vars_table()
     * 
     * @param mixed $id
     * @param integer $gid
     * @return
     */
    function load_dataset_vars_table($id, $gid = 0) {
        $id = (int)$id;
        $arr = array();
        if ($id > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_RESRCDV . " WHERE v_ftid=" . $id . " AND v_con=0 " . (($gid > 0) ? " AND v_gid=" . $gid : "") .
                " ORDER BY v_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['v_opt'] = unserialize($row['v_opt']);
                $arr[$row['v_col']] = $row;
            }
        }
        return $arr;
    }

    /**
     * resource_master_class::load_dataset()
     * 
     * @param mixed $table
     * @param integer $cont_matrix_id
     * @return
     */
    function load_dataset($table, $cont_matrix_id = 0) {
        $arr = array();
        if ($table != "") {
            $result = $this->db->query("SELECT D.* FROM " . $table . " D WHERE 1 " . (($cont_matrix_id > 0) ? " AND ds_cid=" . (int)$cont_matrix_id : "") .
                " ORDER BY ds_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['ds_settings'] = (!empty($row['ds_settings'])) ? unserialize($row['ds_settings']) : array();
                $arr[] = $row;
            }
        }

        return $arr;
    }

    /**
     * resource_master_class::load_groups()
     * 
     * @param mixed $fid
     * @return
     */
    function load_groups($fid) {
        $arr = array();
        if ($fid > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_FLXGROUPS . " WHERE g_ftid=" . (int)$fid . " ORDER BY g_name");
            while ($row = $this->db->fetch_array_names($result)) {
                $arr[] = $row;
            }
        }
        return $arr;
    }

    /**
     * resource_master_class::load_group()
     * 
     * @param mixed $gid
     * @return
     */
    function load_group($gid) {
        return $this->db->query_first("SELECT * FROM " . TBL_FLXGROUPS . " WHERE id=" . (int)$gid);
    }


    /**
     * resource_master_class::load_dataset_for_plugin()
     * 
     * @param mixed $table
     * @param mixed $content_matrix_id
     * @param integer $gid
     * @return
     */
    function load_dataset_for_plugin($table, $content_matrix_id, $gid = 0) {
        $arr = array();
        if ($table != "") {
            $result = $this->db->query("SELECT * FROM " . $table . " WHERE ds_cid=" . (int)$content_matrix_id . " " . (($gid > 0) ? " AND ds_group=" . (int)$gid : "") .
                " ORDER BY ds_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['ds_settings'] = (!empty($row['ds_settings'])) ? unserialize($row['ds_settings']) : array();
                $arr[] = $row;
            }
        }
        return $arr;
    }


    /**
     * resource_master_class::get_img_options()
     * 
     * @param mixed $flexvars_table
     * @param mixed $v_varname
     * @return
     */
    function get_img_options(array $flexvars_table, $v_varname) {
        foreach ($flexvars_table as $key => $row) {
            if ($row['v_varname'] == $v_varname) {
                return $row['v_opt'];
            }
        }
        return array();
    }

    /**
     * resource_master_class::load_flexvars_table()
     * 
     * @param mixed $id
     * @param integer $gid
     * @return
     */
    function load_flexvars_table($resrc_id, $gid = 0) {
        $resrc_id = (int)$resrc_id;
        $arr = array();
        if ($resrc_id > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_RESRCDV . " WHERE v_ftid=" . $resrc_id . " AND v_con=1 " . (($gid > 0) ? " AND v_gid=" . (int)$gid : "") .
                " ORDER BY v_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['v_opt'] = unserialize($row['v_opt']);
                $arr[$row['id']] = $row;
            }
        }
        return $arr;
    }

    /**
     * resource_master_class::load_flexvars_for_plugin()
     * 
     * @param mixed $content_matrix_id
     * @return
     */
    function load_flexvars_for_plugin($content_matrix_id) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_cid=" . (int)$content_matrix_id);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['v_settings'] = (!empty($row['v_settings'])) ? unserialize($row['v_settings']) : array();
            $arr[$row['v_vid']] = $row;
        }
        return $arr;
    }

    /**
     * resource_master_class::load_flexvar()
     * 
     * @param mixed $id
     * @return
     */
    function load_flexvar($id) {
        $flxvar = $this->db->query_first("SELECT * FROM " . TBL_RESRCDV . " WHERE id=" . $id);
        $flxvar['v_opt'] = unserialize($flxvar['v_opt']);
        return $flxvar;
    }

    /**
     * resource_master_class::load_tpl_table()
     * 
     * @param mixed $id
     * @return
     */
    function load_tpl_table($id) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_RESRCPL . " WHERE t_ftid=" . $id . " ORDER BY t_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * resource_master_class::load_html_tpl()
     * 
     * @param mixed $id
     * @return
     */
    function load_html_tpl($id) {
        $FLEX = $this->db->query_first("SELECT * FROM " . TBL_RESRCPL . " WHERE id=" . $id);
        return $FLEX;
    }

    /**
     * resource_master_class::gen_dsvar_name()
     * 
     * @param mixed $vname
     * @return
     */
    public static function gen_dsvar_name($vname) {
        $vname = $org_name = strtolower('dv_' . self::remove_white_space(self::only_alphanums($vname)));
        $k = 0;
        while (get_data_count(TBL_RESRCDV, '*', "v_varname='" . $vname . "'") > 0) {
            $k++;
            $vname = $org_name . '_' . $k;
        }
        return $vname;
    }

    /**
     * resource_master_class::gen_var_name()
     * 
     * @param mixed $vname
     * @return
     */
    public static function gen_var_name($vname) {
        $vname = $org_name = strtolower('fv_' . self::remove_white_space(self::only_alphanums($vname)));
        $k = 0;
        while (get_data_count(TBL_RESRCDV, '*', "v_varname='" . $vname . "'") > 0) {
            $k++;
            $vname = $org_name . '_' . $k;
        }
        return $vname;
    }

    /**
     * resource_master_class::gen_group_name()
     * 
     * @param mixed $vname
     * @return
     */
    public static function gen_group_name($vname) {
        $vname = $org_name = strtolower(self::remove_white_space(self::only_alphanums($vname)));
        $k = 0;
        while (get_data_count(TBL_FLXGROUPS, '*', "g_ident='" . $vname . "'") > 0) {
            $k++;
            $vname = $org_name . '_' . $k;
        }
        return $vname;
    }

    /**
     * resource_master_class::get_smarty_flexvar()
     * 
     * @param mixed $vname
     * @return
     */
    public static function get_smarty_flexvar($vname) {
        return str_replace(TBL_CMS_PREFIX . 'flxt_ds_', 'flxt.', $vname);
    }

    /**
     * resource_master_class::html_editor_transform_content()
     * 
     * @param mixed $value
     * @return
     */
    public static function html_editor_transform_content($value) {
        # fix wysing editor url link format
        if (!is_array($value) && strstr((string )$value, '/{URL_TPL_')) {
            $value = str_replace('/{URL_TPL_', '{URL_TPL_', $value);
        }

        # transform path
        $value = str_replace(array(
            'http://' . FM_DOMAIN . PATH_CMS,
            'http://www.' . FM_DOMAIN . PATH_CMS,
            'https://' . FM_DOMAIN . PATH_CMS,
            'https://www.' . FM_DOMAIN . PATH_CMS), '/', $value);
        return $value;
    }

    /**
     * resource_master_class::gen_resr_link()
     * 
     * @param mixed $id
     * @return
     */
    public static function gen_resr_link($id) {
        return '{URL_RESRC_' . (int)$id . '}';
    }

    /**
     * resource_master_class::gen_link_label()
     * 
     * @param mixed $row
     * @return
     */
    public static function gen_link_label($row) {
        #$row['nachname'] = (isset($row['nachname']) ? $row['nachname'] : "");
        #$label = (isset($row['vorname']) && $row['vorname'] != "") ? $row['vorname'] . ' ' . $row['nachname'] : $row['nachname'];
        #$label = (isset($row['firma']) && $row['firma'] != "") ? $row['firma'] . ' ' . $label : $label;
        return $row['c_label'];
    }

    /**
     * resource_master_class::rebuild_page_index()
     * 
     * @param integer $kid
     * @return
     */
    function rebuild_page_index($content_id = 0) {
        $k = 0;
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='resource' " . (($content_id > 0) ? " AND pi_relatedid='" . $content_id . "'" : ""));
        $result = $this->db->query("SELECT C.*,R.f_name,R.id AS FID FROM " . TBL_RESRC_CONTENT . " C, " . TBL_RESRC . " R WHERE R.id=C.c_ftid " . (($content_id > 0) ?
            " AND C.id='" . $content_id . "'" : ""));
        while ($row = $this->db->fetch_array($result)) {
            $k++;
            $label = self::gen_link_label($row);
            $link = '/' . self::format_file_name($row['f_name']) . '/' . self::format_file_name($label) . '.html';
            $query = array('cmd' => 'show_resource', 'id' => $row['id']);


            $TPL = dao_class::get_data_first(TBL_RESRCPL, array('t_ftid' => $row['FID'], 't_use' => '1'));

            $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($lang = $this->db->fetch_array_names($resultlang)) {
                if ($label != "")
                    $this->connect_to_pageindex($link, $query, $row['id'], 'resource', $lang['id'], 0, $TPL['t_pageid']);
            }
        }
        return $k;
    }

}
