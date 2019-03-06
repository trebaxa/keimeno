<?php

/**
 * @package    ramicronews
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class ramicronews_admin_class extends ramicronews_master_class {

    protected $RAMICRONEWS = array();


    /**
     * ramicronews_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

    }


    /**
     * ramicronews_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $CONFIG_OBJ = new config_class('ramicronews');
        $this->RAMICRONEWS['CONFIG'] = $CONFIG_OBJ->buildTable();
        $this->RAMICRONEWS['news'] = $this->load_items();
        $this->smarty->assign('RAMICRONEWS', $this->RAMICRONEWS);
    }

    /**
     * ramicronews_admin_class::cmd_load_news()
     * 
     * @return
     */
    function cmd_load_news() {
        $id = (int)$_GET['id'];
        $this->RAMICRONEWS['newsdetail'] = $this->load_news_by_id($id);
        $this->parse_to_smarty();
        kf::echo_template('ramicronews.detail');
    }


    /**
     * ramicronews_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE layout_group=1 AND modident='ramicronews' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * ramicronews_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_RAMICRONEWS_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }


}

?>