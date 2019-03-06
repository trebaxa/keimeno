<?php

/**
 * @package    faq
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


class faq_admin_class extends modules_class
{

    protected $FAQ = array();

    /**
     * faq_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * faq_admin_class::cmd_delitem()
     * 
     * @return
     */
    function cmd_delitem()
    {
        $id = $this->TCR->GET['ident'];
        $this->db->query("DELETE FROM " . TBL_CMS_FAQITEMS . " WHERE id=" . $id);
        $this->ej();
    }

    /**
     * faq_admin_class::cmd_axapprove_item()
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
     * faq_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('FAQ', $this->FAQ);
    }

    /**
     * faq_admin_class::cmd_save_config()
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
     * faq_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf()
    {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * faq_admin_class::load_groups()
     * 
     * @return
     */
    function load_groups()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQGROUPS .
            " WHERE 1 ORDER BY g_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'delgroup');
            $this->FAQ['groups'][] = $row;
        }
    }

    /**
     * faq_admin_class::cmd_load_items()
     * 
     * @return
     */
    function cmd_load_items()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQITEMS .
            " WHERE faq_gid=" . (int)$_GET['gid'] . " ORDER BY faq_order,faq_question");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], false, 'delitem');
            $this->FAQ['faqitems'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('faq.items');
    }

    /**
     * faq_admin_class::cmd_add_group()
     * 
     * @return
     */
    function cmd_add_group()
    {
        $_POST['FORM']['g_name'] = ($_POST['FORM']['g_name'] == "") ? 'Neue Gruppe' : $_POST['FORM']['g_name'];
        insert_table(TBL_CMS_FAQGROUPS, $_POST['FORM']);
        $this->TCR->set_just_turn_back();
    }

    /**
     * faq_admin_class::cmd_save_item()
     * 
     * @return
     */
    function cmd_save_item()
    {
        $FORM = (array )$_POST['FORM'];
        #  $FORM = $this->arr_trimsthsc($FORM);
        if ($_POST['id'] == 0) {
            $FORM['faq_time'] = time();
            insert_table(TBL_CMS_FAQITEMS, $FORM);
        } else {
            update_table(TBL_CMS_FAQITEMS, 'id', $_POST['id'], $_POST['FORM']);
        }
        $this->echo_json_fb('reload_faq_items');
    }

    /**
     * faq_admin_class::cmd_delgroup()
     * 
     * @return
     */
    function cmd_delgroup()
    {
        $id = $this->TCR->GET['ident'];
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQITEMS .
            " WHERE faq_gid=" . (int)$id . " ORDER BY faq_order,faq_question");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->db->query("DELETE FROM " . TBL_CMS_FAQITEMS . " WHERE id=" . $row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_FAQGROUPS . " WHERE id=" . $id);
        $this->ej();
    }

    /**
     * faq_admin_class::cmd_save_groups()
     * 
     * @return
     */
    function cmd_save_groups()
    {
        foreach ((array )$_POST['FORM'] as $key => $row) {
            update_table(TBL_CMS_FAQGROUPS, 'id', $key, $row);
        }
        $this->hard_exit();
    }

    /**
     * faq_admin_class::cmd_getitem()
     * 
     * @return
     */
    function cmd_getitem()
    {
        $ITEM = $this->db->query_first("SELECT * FROM " . TBL_CMS_FAQITEMS .
            " WHERE id=" . (int)$_GET['id']);
        # $ITEM = $this->arr_trimhsc($ITEM);
        echo json_encode($ITEM);
        $this->hard_exit();
    }

    /**
     * faq_admin_class::cmd_save_items()
     * 
     * @return
     */
    function cmd_save_items()
    {
        $FORM = (array )$_POST['FORM'];
        $FORM = $this->sort_multi_array($FORM, 'faq_order', SORT_ASC, SORT_NUMERIC);
        foreach ($FORM as $key => $row) {
            $k += 10;
            $row['faq_order'] = $k;
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_CMS_FAQITEMS, 'id', $id, $row);
        }
        $this->echo_json_fb('reload_faq_items');
    }

    /**
     * faq_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE layout_group=1 AND modident='faq' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * faq_admin_class::load_groups_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_groups_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FAQGROUPS .
            " WHERE 1 ORDER BY g_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * faq_admin_class::save_homepage_integration()
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
        $upt = array('tm_content' => '{TMPL_FAQ_' . $cont_matrix_id . '}', 'tm_pluginfo' =>
                $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }


}

?>