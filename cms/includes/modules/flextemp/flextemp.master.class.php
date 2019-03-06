<?PHP

/**
 * @package    flextemp
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_FLXT', TBL_CMS_PREFIX . 'flxt');
DEFINE('TBL_FLXTDV', TBL_CMS_PREFIX . 'flxtpl_dv');
DEFINE('TBL_FLXTPL', TBL_CMS_PREFIX . 'flxtpl_tpl');
DEFINE('TBL_FLXVARS', TBL_CMS_PREFIX . 'flxtpl_vars');
DEFINE('TBL_FLXGROUPS', TBL_CMS_PREFIX . 'flxtpl_groups');


class flextemp_master_class extends modules_class {

    var $froot = "";
    var $file_root = "";
    var $forbidden_column_arr = array();
    var $nested=null;

    /**
     * flextemp_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->froot = CMS_ROOT . 'file_data/flextemp/images/';
        $this->file_root = CMS_ROOT . 'file_data/flextemp/files/';
        $this->forbidden_column_arr = array('ds_cid', 'ds_settings');
        $this->nested = new nestedArrClass();
        $this->nested->label_column = 'description';
        $this->nested->label_id = 'id';
        $this->nested->label_parent = 'parent';
    }

    /**
     * flextemp_master_class::load_flx_tpls()
     * 
     * @return
     */
    function load_flx_tpls() {
        $arr = array();
        $result = $this->db->query("SELECT *,F.id AS FID FROM " . TBL_FLXT . " F WHERE 1 ORDER BY f_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * flextemp_master_class::load_flex_tpl()
     * 
     * @param mixed $id
     * @return
     */
    function load_flex_tpl($id) {
        $FLEX = $this->db->query_first("SELECT *,F.id AS FID FROM " . TBL_FLXT . " F WHERE id=" . (int)$id);
        return $FLEX;
    }

    /**
     * flextemp_master_class::delete_flexvar_value()
     * 
     * @param mixed $content_matrix_id
     * @param mixed $vid
     * @return
     */
    function delete_flexvar_value($content_matrix_id, $vid) {
        $this->db->query("DELETE FROM " . TBL_FLXVARS . " WHERE v_cid=" . $content_matrix_id . " AND v_vid='" . $vid . "'");
    }

    /**
     * flextemp_master_class::load_dataset_vars()
     * 
     * @param mixed $id
     * @return
     */
    function load_dataset_vars($id) {
        $arr = array();
        $FLEX = $this->load_flex_tpl($id);
        if ($FLEX['f_table'] != "") {
            $result = $this->db->query("SHOW COLUMNS FROM " . $FLEX['f_table']);
            while ($row = $this->db->fetch_array_names($result)) {
                $arr[] = $row;
            }
        }
        return $arr;
    }

    /**
     * flextemp_master_class::load_dataset_vars_table()
     * 
     * @param mixed $id
     * @param integer $gid
     * @return
     */
    function load_dataset_vars_table($id, $gid = 0) {
        $id = (int)$id;
        $arr = array();
        if ($id > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_FLXTDV . " WHERE v_ftid=" . $id . " AND v_con=0 " . (($gid > 0) ? " AND v_gid=" . $gid : "") .
                " ORDER BY v_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['v_opt'] = unserialize($row['v_opt']);
                $arr[$row['v_col']] = $row;
            }
        }
        return $arr;
    }

    /**
     * flextemp_master_class::load_dataset()
     * 
     * @param mixed $table
     * @param integer $cont_matrix_id
     * @return
     */
    function load_dataset($table, $cont_matrix_id = 0) {
        $arr = array();
        if ($table != "") {
            $result = $this->db->query("SELECT D.*,G.g_ident FROM " . $table . " D, " . TBL_FLXGROUPS . " G WHERE G.id=D.ds_group " . (($cont_matrix_id > 0) ?
                " AND ds_cid=" . (int)$cont_matrix_id : "") . " ORDER BY ds_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['ds_settings'] = (!empty($row['ds_settings'])) ? unserialize($row['ds_settings']) : array();
                $arr[] = $row;
            }
        }

        return $arr;
    }

    /**
     * flextemp_master_class::load_groups()
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
     * flextemp_master_class::load_group()
     * 
     * @param mixed $gid
     * @return
     */
    function load_group($gid) {
        return $this->db->query_first("SELECT * FROM " . TBL_FLXGROUPS . " WHERE id=" . (int)$gid);
    }


    /**
     * flextemp_master_class::load_dataset_for_plugin()
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
     * flextemp_master_class::get_img_options()
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
     * flextemp_master_class::load_flexvars_table()
     * 
     * @param mixed $id
     * @param integer $gid
     * @return
     */
    function load_flexvars_table($id, $gid = 0) {
        $id = (int)$id;
        $arr = array();
        if ($id > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_FLXTDV . " WHERE v_ftid=" . $id . " AND v_con=1 " . (($gid > 0) ? " AND v_gid=" . (int)$gid : "") .
                " ORDER BY v_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['v_opt'] = unserialize($row['v_opt']);
                $arr[$row['id']] = $row;
            }
        }
        return $arr;
    }

    /**
     * flextemp_master_class::load_flexvars_for_plugin()
     * 
     * @param mixed $content_matrix_id
     * @return
     */
    function load_flexvars_for_plugin($content_matrix_id) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_FLXVARS . " WHERE v_cid=" . (int)$content_matrix_id);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['v_settings'] = (!empty($row['v_settings'])) ? unserialize($row['v_settings']) : array();
            $arr[$row['v_vid']] = $row;
        }
        return $arr;
    }

    /**
     * flextemp_master_class::load_flexvar()
     * 
     * @param mixed $id
     * @return
     */
    function load_flexvar($id) {
        $flxvar = $this->db->query_first("SELECT * FROM " . TBL_FLXTDV . " WHERE id=" . $id);
        $flxvar['v_opt'] = unserialize($flxvar['v_opt']);
        return $flxvar;
    }

    /**
     * flextemp_master_class::load_tpl_table()
     * 
     * @param mixed $id
     * @return
     */
    function load_tpl_table($id) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_FLXTPL . " WHERE t_ftid=" . $id . " ORDER BY t_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * flextemp_master_class::load_html_tpl()
     * 
     * @param mixed $id
     * @return
     */
    function load_html_tpl($id) {
        $FLEX = $this->db->query_first("SELECT * FROM " . TBL_FLXTPL . " WHERE id=" . $id);
        return $FLEX;
    }

    /**
     * flextemp_master_class::gen_dsvar_name()
     * 
     * @param mixed $vname
     * @return
     */
    public static function gen_dsvar_name($vname) {
        $vname = $org_name = strtolower('dv_' . self::remove_white_space(self::only_alphanums($vname)));
        $k = 0;
        while (get_data_count(TBL_FLXTDV, '*', "v_varname='" . $vname . "'") > 0) {
            $k++;
            $vname = $org_name . '_' . $k;
        }
        return $vname;
    }

    /**
     * flextemp_master_class::gen_var_name()
     * 
     * @param mixed $vname
     * @return
     */
    public static function gen_var_name($vname) {
        $vname = $org_name = strtolower('fv_' . self::remove_white_space(self::only_alphanums($vname)));
        $k = 0;
        while (get_data_count(TBL_FLXTDV, '*', "v_varname='" . $vname . "'") > 0) {
            $k++;
            $vname = $org_name . '_' . $k;
        }
        return $vname;
    }

    /**
     * flextemp_master_class::gen_group_name()
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
     * flextemp_master_class::get_smarty_flexvar()
     * 
     * @param mixed $vname
     * @return
     */
    public static function get_smarty_flexvar($vname) {
        return str_replace(TBL_CMS_PREFIX . 'flxt_ds_', 'flxt.', $vname);
    }

    /**
     * flextemp_master_class::html_editor_transform_content()
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


}
