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
DEFINE('TBL_RESRC_TABLES', TBL_CMS_PREFIX . 'resrc_tables');
DEFINE('TBL_RESRCDV', TBL_CMS_PREFIX . 'resrc_dv');
DEFINE('TBL_RESRC_CONTENT', TBL_CMS_PREFIX . 'resrc_content');
DEFINE('TBL_RESRCVARS', TBL_CMS_PREFIX . 'resrc_vars');
DEFINE('TBL_RESRCPL', TBL_CMS_PREFIX . 'resrc_tpl');


class resource_master_class extends modules_class {

    var $froot = "";
    var $file_root = "";
    var $forbidden_column_arr = array();
    var $nested = null;
    protected static $mobile_detect = null;

    /**
     * resource_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->froot = CMS_ROOT . 'file_data/resource/images/';
        $this->file_root = CMS_ROOT . 'file_data/resource/files/';
        $this->forbidden_column_arr = array(
            'ds_cid',
            'ds_settings',
            'ds_langid');
        $this->nested = new nestedArrClass();
        $this->nested->label_column = 'description';
        $this->nested->label_id = 'id';
        $this->nested->label_parent = 'parent';
        static::$mobile_detect = new Mobile_Detect();
    }

    /**
     * resource_master_class::get_optimal_size()
     * 
     * @param mixed $img_opt
     * @return
     */
    protected static function get_optimal_size($img_opt) {
        if (self::get_config_value('gra_mobile_detect') == 1) {
            $mobile_width = 375;
            $mobile_height = 667;
            $tablet_width = 768;
            $tablet_height = 667;
            if (static::$mobile_detect->isMobile()) {
                if ($img_opt['foto_width'] > $mobile_width) {
                    $img_opt['foto_width'] = $mobile_width;
                }
                if ($img_opt['foto_height'] > $mobile_height) {
                    $img_opt['foto_height'] = $mobile_height;
                }
            }
            elseif (static::$mobile_detect->isTablet()) {
                if ($img_opt['foto_width'] > $tablet_width) {
                    $img_opt['foto_width'] = $tablet_width;
                }
                if ($img_opt['foto_height'] > $tablet_height) {
                    $img_opt['foto_height'] = $tablet_height;
                }
            }
        }
        return $img_opt;
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
     * resource_master_class::load_tables_of_resrc()
     * 
     * @param mixed $id
     * @return
     */
    function load_tables_of_resrc($id, $table = "") {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_RESRC_TABLES . " F WHERE f_rid=" . (int)$id . " " . (($table != "") ? " AND f_table='" . $table . "'" : "") .
            " ORDER BY f_table");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[$row['f_table']] = $row;
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
     * resource_master_class::gen_paging_link()
     * 
     * @param mixed $start
     * @param string $toadd
     * @return
     */
    private static function gen_paging_link($start, $toadd = '') {
        return $_SERVER['SCRIPT_URL'] . '?start=' . $start . $toadd;
    }

    /**
     * resource_master_class::microtime_float()
     * 
     * @return
     */
    public static function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * resource_master_class::gen_paging()
     * 
     * @param mixed $ovStart
     * @param mixed $max_paging
     * @param mixed $total
     * @param string $toadd
     * @return
     */
    private static function gen_paging($ovStart, $max_paging, $total, $toadd = '') {
        $NUM_PREPAGES = 6;
        $newStartBack = $newStart = 0;
        $max_paging = ($max_paging <= 0) ? 10 : $max_paging;
        $start = (isset($ovStart)) ? abs((int)$ovStart) : 0;
        $total_pages = ceil($total / $max_paging);
        $akt_page = round($start / $max_paging) + 1;
        if ($total_pages > 0)
            $akt_pages = $akt_page . '/' . $total_pages;
        $start = ($start > $total) ? $total - $max_paging : $start;
        $next_pages_arr = $back_pages_arr = array();
        if ($start > 0)
            $newStartBack = ($start - $max_paging < 0) ? 0 : ($start - $max_paging);
        if ($start > 0) {
            for ($i = $NUM_PREPAGES - 1; $i >= 0; $i--) {
                if ($newStartBack - ($i * $max_paging) >= 0) {
                    $back_pages_arr[] = array('link' => self::gen_paging_link(($newStartBack - ($i * $max_paging)), $toadd), 'index' => ($akt_page - $i - 1));
                }
            }
        }
        if ($start + $max_paging < $total) {
            $newStart = $start + $max_paging;
            for ($i = 0; $i < $NUM_PREPAGES; $i++) {
                if ($newStart + ($i * $max_paging) < $total) {
                    $next_pages_arr[] = array('link' => self::gen_paging_link(($newStart + ($i * $max_paging)), $toadd), 'index' => ($akt_page + $i + 1));
                }
            }
        }
        #	die;
        $_paging['start'] = $start;
        $_paging['total_pages'] = $total_pages;
        $_paging['item_per_page'] = $max_paging;
        $_paging['startback'] = $newStartBack;
        $_paging['last_page'] = $total - $max_paging;
        $_paging['newstart'] = $newStart;
        $_paging['base_link'] = $_SERVER['SCRIPT_URL'] . '?' . $toadd;
        $_paging['back_pages'] = $back_pages_arr;
        $_paging['akt_page'] = $akt_page;
        $_paging['next_pages'] = $next_pages_arr;
        $_paging['backlink'] = self::gen_paging_link($newStartBack, $toadd);
        $_paging['nextlink'] = self::gen_paging_link($newStart, $toadd);
        $_paging['count_total'] = $total;
        return $_paging;
    }

    /**
     * resource_master_class::load_content_table_fe()
     * 
     * @param mixed $resrc_id
     * @return
     */
    function load_content_table_fe($resrc_id, $v_settings = array(), $filter = array()) {
        $arr = $cid = array();
        $paging_active = (int)$v_settings['resrc']['paging'];
        $start = (isset($_GET['start']) ? (int)$_GET['start'] : 0);
        if ($paging_active == 1) {
            $items_per_page = (int)$v_settings['resrc']['items_per_page'];
        }
        else {
            $items_per_page = (int)$v_settings['resrc']['quantity'];
        }

        if (isset($v_settings['resrc']['cid']) && count($v_settings['resrc']['cid']) > 0) {
            $cid = $v_settings['resrc']['cid'];
            if ((int)$cid[0] == 0 && count($cid) == 1) {
                $cid = array();
            }
        }
        $direc = (isset($v_settings['resrc']['direc']) && $v_settings['resrc']['direc'] == 'DESC') ? 'DESC' : 'ASC';
        $rand_sort = (isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] == -1) ? " RAND()" : " F.c_sort";


        # set filter
        $sql_rfilter = "";
        if (isset($filter) && count($filter) > 0 && isset($filter['resrcid']) && $resrc_id == (int)$filter['resrcid']) {
            $RFILTER = (array )$_GET['RFILTER'];
            $sql_rfilter = "AND F.id IN (SELECT F.id FROM tcms1_resrc_content F, tcms1_resrc_vars V WHERE c_ftid=" . (int)$resrc_id . " 
            AND F.id=V.v_cid AND v_vid=" . (int)$RFILTER['v_vid'] . " AND v_value=" . (int)$RFILTER['v_value'] . ")";
        }

        $result = $this->db->query("SELECT F.*,F.id AS CID FROM " . TBL_RESRC_CONTENT . " F, " . TBL_RESRCVARS . " V WHERE 
            c_ftid=" . (int)$resrc_id . " 
            AND F.id=V.v_cid       
            " . ((isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] > 0) ? " AND V.v_vid=" . (int)$v_settings['resrc']['sort'] : "") . "
            " . ((count($cid) > 0) ? " AND F.id IN (" . implode(',', $v_settings['resrc']['cid']) . ")" : "") . "
            " . (($sql_rfilter != "") ? $sql_rfilter : "") . "
            GROUP BY F.id
            ORDER BY 
            " . ((isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] > 0) ? " v_value " . $direc : $rand_sort) . "
            " . (($paging_active == 0 && isset($v_settings['resrc']['quantity']) && (int)$v_settings['resrc']['quantity'] > 0) ? " LIMIT " . $start . "," . $items_per_page :
            "") . (($paging_active == 1) ? " LIMIT " . $start . "," . $items_per_page : ""));
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }

        # Count
        $total = 0;
        $result = $this->db->query("SELECT F.id AS CID FROM " . TBL_RESRC_CONTENT . " F, " . TBL_RESRCVARS . " V WHERE c_ftid=" . (int)$resrc_id . " 
        AND F.id=V.v_cid
        
        " . ((isset($v_settings['resrc']['sort']) && (int)$v_settings['resrc']['sort'] > 0) ? " AND V.v_vid=" . (int)$v_settings['resrc']['sort'] : "") . "
        " . ((count($cid) > 0) ? " AND F.id IN (" . implode(',', $v_settings['resrc']['cid']) . ")" : "") . "
        " . (($sql_rfilter != "") ? $sql_rfilter : "") . "
        GROUP BY F.id");
        while ($row = $this->db->fetch_array_names($result)) {
            $total++;
        }

        # paging
        $paging = self::gen_paging($start, $items_per_page, $total);
        $paging['paging_active'] = $paging_active;

        return array('dataset' => $arr, 'paging' => $paging);
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
    function delete_flexvar_value($v_cid, $vid, $langid = 1) {
        $this->db->query("DELETE FROM " . TBL_RESRCVARS . " WHERE v_langid=" . (int)$langid . " AND v_cid=" . (int)$v_cid . " AND v_vid='" . $vid . "'");
    }

    /**
     * resource_master_class::load_dataset_vars()
     * 
     * @param mixed $id
     * @return
     */
    function load_dataset_vars($id, $table) {
        $arr = array();
        #  $FLEX = $this->load_resrc($id);
        if ($table != "") {
            $result = $this->db->query("SHOW COLUMNS FROM " . TBL_CMS_PREFIX . $table);
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
    public static function load_dataset_vars_table($id, $table) {
        $id = (int)$id;
        $arr = array();
        if ($id > 0) {
            $result = dao_class::$cdb->query("SELECT * FROM " . TBL_RESRCDV . " WHERE v_ftid=" . $id . " AND v_con=0 " . (($table != "") ? " AND v_table='" . $table . "'" : "") .
                " ORDER BY v_order");
            while ($row = dao_class::$cdb->fetch_array_names($result)) {
                $row['v_opt'] = unserialize($row['v_opt']);
                if ($table != "") {
                    $arr[$row['v_col']] = $row;
                }
                else {
                    $arr[$row['v_table']][$row['v_col']] = $row;
                }
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
    function load_dataset($table, $cont_matrix_id = 0, $langid = 1, $db_filter = array()) {
        $arr = array();
        if ($table != "") {
            $sql_drfilter = "";
            if (isset($db_filter['columns'][$table]) && is_array($db_filter['columns'][$table])) {
             #   echoarr($db_filter['columns'][$table]);
                foreach ($db_filter['columns'][$table] as $key => $row) {
                    $sql_drfilter .= (($sql_drfilter != "") ? " OR " : "") . $row['col'] . "='" . $key . "'";
                }
            }

            $result = $this->db->query("SELECT D.* FROM " . TBL_CMS_PREFIX . $table . " D WHERE 1 
                " . (($cont_matrix_id > 0) ? " AND ds_cid=" . (int)$cont_matrix_id : "") . " 
                AND ds_langid=" . (int)$langid . "
                " . (($sql_drfilter != "") ? " AND (" . $sql_drfilter . ")" : "") . "
                ORDER BY ds_order");
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
    function load_dataset_for_plugin($table, $content_matrix_id, $gid = 0, $langid = 1) {
        $arr = array();
        if ($table != "") {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_PREFIX . $table . " WHERE 
                ds_cid=" . (int)$content_matrix_id . " 
                AND ds_langid=" . $langid . "
                " . (($gid > 0) ? " AND ds_group=" . (int)$gid : "") . " 
                ORDER BY ds_order");
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
    function load_flexvars_table($resrc_id, $table = "", $varset = false) {
        $resrc_id = (int)$resrc_id;
        $arr = array();
        if ($resrc_id > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_RESRCDV . " WHERE v_ftid=" . $resrc_id . " 
            AND v_con=1 " . (($table != "") ? " AND v_table='" . $table . "'" : "") . "
            " . (($varset == true) ? " AND v_table='' " : "") . "  
            ORDER BY v_order");
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
    function load_flexvars_for_plugin($content_matrix_id, $langid = 1) {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_langid=" . (int)$langid . " AND v_cid=" . (int)$content_matrix_id);
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
