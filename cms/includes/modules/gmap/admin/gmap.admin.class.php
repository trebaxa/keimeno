<?php

/**
 * @package    gmap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class gmap_admin_class extends gmap_master_class {

    protected $GMAP = array();

    /**
     * gmap_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * gmap_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('GMAP', $this->GMAP);
    }

    /**
     * gmap_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='gmap' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * gmap_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $upt = array('tm_content' => '{TMPL_GMAPINLAY_' . (int)$cont_matrix_id . '}', 'tm_pluginfo' => 'Google Maps');
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }


}

?>