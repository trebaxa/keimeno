<?PHP

/**
 * @package    menus
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_MMENU', TBL_CMS_PREFIX . 'mmenu');
DEFINE('TBL_MMENUMATRIX', TBL_CMS_PREFIX . 'mmenu_matrix');

class menus_master_class extends modules_class {

    var $nested = null;

    /**
     * menus_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->nested = new nestedArrClass();
        $this->nested->label_column = 'description';
        $this->nested->label_id = 'mm_id';
        $this->nested->label_parent = 'mm_parent';
    }

    /**
     * menus_master_class::load_menus()
     * 
     * @return
     */
    function load_menus() {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_MMENU . " WHERE 1 ORDER BY m_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * menus_master_class::load_mmenu_matrix()
     * 
     * @param mixed $id
     * @return
     */
    function load_mmenu_matrix($id) {
        $arr = array();
        $result = $this->db->query("SELECT M.*,RM.description FROM " . TBL_MMENUMATRIX . " M LEFT JOIN " . TBL_CMS_TEMPLATES . " RM ON RM.id=M.mm_id 
        WHERE mm_mid=" . $id . " ORDER BY mm_parent,mm_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['description'] = ($row['description'] != "") ? $row['description'] : $row['mm_title'];
            $row['catlink'] = ($row['catlink'] != "") ? $row['catlink'] : $row['mm_url'];
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * menus_master_class::load_mmenu_matrix_fe()
     * 
     * @param mixed $id
     * @param integer $langid
     * @return
     */
    function load_mmenu_matrix_fe($id, $langid = 1) {
        global $user_object;
        $arr = array();
        $result = $this->db->query("SELECT P.perm_tid,M.*,T.id AS TID,T.description, TC.linkname,T.url_redirect,T.url_redirect_target,T.t_class,T.t_attributes,TC.t_icon,TC.t_htalinklabel,T.tid_childs,TC.t_themedescription,
             TC.t_imgthemealt,TC.t_imgthemetitle,TC.theme_image 
             FROM " . TBL_MMENUMATRIX . " M LEFT JOIN " . TBL_CMS_TEMPLATES . " T ON T.id=M.mm_id 
             LEFT JOIN " . TBL_CMS_PERMISSIONS . " P ON (P.perm_tid=T.id " . $user_object['sql_groups'] . ")
             LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON  TC.tid=M.mm_id AND TC.lang_id=" . (int)$langid . "
            WHERE 
             mm_mid=" . $id . " 
             GROUP BY M.id
             ORDER BY mm_parent,mm_order             
             ");

        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['perm_tid'] > 0 || ((int)$row['perm_tid'] == 0 && (int)$row['TID'] == 0)) {
                if ((int)$row['perm_tid'] == 0 && (int)$row['TID'] == 0) {
                    $row['linkname'] = $row['description'] = ($row['description'] != "") ? $row['description'] : $row['mm_title'];
                    $row['url_redirect'] = $row['catlink'] = ($row['catlink'] != "") ? $row['catlink'] : $row['mm_url'];
                    $row['mm_id'] = $row['id'];
                }
                $arr[] = $row;
            }
        }
        return $arr;
    }


    /**
     * menus_master_class::load_org_menu()
     * 
     * @return
     */
    function load_org_menu() {
        $arr = array();
        $result = $this->db->query("SELECT id,parent,description FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=0 ORDER BY description,parent,id");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[$row['id']] = $row;
        }
        return $arr;
    }

    /**
     * menus_master_class::load_menu()
     * 
     * @param mixed $id
     * @return
     */
    function load_menu($id) {
        return $this->db->query_first("SELECT * FROM " . TBL_MMENU . " WHERE id=" . (int)$id);
    }

    /**
     * menus_master_class::load_item()
     * 
     * @param mixed $id
     * @return
     */
    function load_item($id) {
        return $this->db->query_first("SELECT * FROM " . TBL_MMENUMATRIX . " WHERE id=" . (int)$id);
    }

}
