<?php

/**
 * @package    jtagcloud
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class jtagcloud_admin_class extends keimeno_class
{

    protected $JTAGCLOUD = array();

    /**
     * jtagcloud_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * jtagcloud_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item()
    {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        # fillin delete function
        $this->hard_exit();
    }

    /**
     * jtagcloud_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item()
    {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * jtagcloud_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('JTAGCLOUD', $this->JTAGCLOUD);
    }

    /**
     * jtagcloud_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config()
    {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * jtagcloud_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf()
    {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * jtagcloud_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE layout_group=1 AND modident='jtagcloud' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * jtagcloud_admin_class::load_webpages_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_webpages_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE c_type='T' AND approval=1 AND gbl_template=0 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row['description'];
            $row['urltpl'] = content_class::gen_url_template($row['id']);
            $row['ID'] = $row['urltpl'] . '###' . trim(strip_tags($row['description']));
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * jtagcloud_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params)
    {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" .
            (int)$id);
        $upt = array('tm_content' => '{TMPL_JTAGCLOUD_' . $cont_matrix_id . '}',
                'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }


}

?>