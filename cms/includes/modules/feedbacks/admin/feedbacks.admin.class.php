<?php

/**
 * @package    feedbacks
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


class feedbacks_admin_class extends keimeno_class {

    protected $FAQ = array();

    /**
     * feedbacks_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->img_root = CMS_ROOT . 'file_data/feedbacks/';
    }

    /**
     * feedbacks_admin_class::cmd_del_item()
     * 
     * @return
     */
    function cmd_del_item() {
        $this->db->query("DELETE FROM " . TBL_CMS_TESTIMONIALS . " WHERE id='" . $_GET['ident'] . "' LIMIT 1");
        $this->ej();
    }

    /**
     * feedbacks_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        $this->db->query("UPDATE " . TBL_CMS_TESTIMONIALS . " SET approval='" . $_GET['value'] . "' WHERE id='" . $_GET['ident'] . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * feedbacks_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('FEEDB', $this->FEEDB);
    }

    /**
     * feedbacks_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * feedbacks_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * feedbacks_admin_class::save_file()
     * 
     * @param mixed $id_value
     * @return
     */
    function save_file($id_value) {
        if ($_FILES['datei']['tmp_name'] != "") {
            if (!is_dir($this->img_root))
                mkdir($this->img_root, 0775);
            if (!validate_upload_file($_FILES['datei'])) {
                $this->msge($_SESSION['upload_msge']);
                return false;
            }
            if (!self::is_image($_FILES['datei']['tmp_name'])) {
                $this->msge('Keine Bilddatei');
                return false;
            }
            if ($_FILES['datei']['name'] != "" && $id_value > 0) {
                $target_file = $this->format_file_name($_FILES['datei']['name']);
                $target_file = $this->img_root . self::unique_filename($this->img_root, $target_file);
                $F = $this->db->query_first("SELECT * FROM " . TBL_CMS_TESTIMONIALS . " WHERE id=" . (int)$id_value);
                if ($F['img'] != "") {
                    @unlink($this->img_root . $F['img']);
                }
                move_uploaded_file($_FILES['datei']['tmp_name'], $target_file);
                chmod($target_file, 0755);
                update_table(TBL_CMS_TESTIMONIALS, "id", $id_value, array('img' => basename($target_file)));
            }
        }
        return true;
    }

    /**
     * feedbacks_admin_class::cmd_save_item()
     * 
     * @return
     */
    function cmd_save_item() {
        $FORM = (array )$_POST['FORM'];
        $FORM['time_int'] = date_to_time($FORM['time_int']);
        if ($FORM['kid'] > 0) {
            $CUST = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $FORM['kid']);
            $FORM['kname'] = $this->db->real_escape_string($CUST['vorname'] . ' ' . $CUST['nachname']);
            $FORM['email'] = ($CUST['email'] != "") ? $this->db->real_escape_string($CUST['email']) : $FORM['email'];
        }
        update_table(TBL_CMS_TESTIMONIALS, 'id', $_POST['id'], $FORM);
        $this->save_file($_POST['id']);
        $this->ej('load_feed_img');
    }

    /**
     * feedbacks_admin_class::cmd_additem()
     * 
     * @return
     */
    function cmd_additem() {
        $FORM = (array )$_POST['FORM'];
        $FORM['time_int'] = time();
        $id = insert_table(TBL_CMS_TESTIMONIALS, $FORM);
        $this->TCR->redirect('id=' . $id . '&cmd=edit&epage=' . $_POST['epage']);
        $this->hard_exit();
    }


    /**
     * feedbacks_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE layout_group=1 AND modident='feedbacks' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * feedbacks_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_FEEDBACKS_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * feedbacks_admin_class::cmd_searchcustomer()
     * 
     * @return
     */
    function cmd_searchcustomer() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE email LIKE '%" . $_GET['term'] . "%' 
      OR nachname LIKE '" . $_GET['term'] . "%'
      OR vorname LIKE '" . $_GET['term'] . "%'
      OR kid LIKE '" . $_GET['term'] . "%'
       ORDER BY nachname
       LIMIT 10");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->FEEDB['customers'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('feedbacks.custsearch');
    }

    /**
     * feedbacks_admin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_TESTIMONIALS . " WHERE id='" . (int)$_GET['id'] . "' LIMIT 1");
        $FORM['date_time'] = date("d.m.Y H:i:s", $FORM['time_int']);
        $FORM['date'] = date("d.m.Y", $FORM['time_int']);
        $FORM['editor'] = create_html_editor('FORM[feedback]', ($FORM['feedback']), 300, 'Basic');
        $this->FEEDB['form'] = $FORM;
    }

    /**
     * feedbacks_admin_class::cmd_load_feed_img()
     * 
     * @return
     */
    function cmd_load_feed_img() {
        $this->cmd_edit();
        if ($this->FEEDB['form']['img'] != "")
            echo '<img src="../file_data/feedbacks/' . $this->FEEDB['form']['img'] . '" class="img-responsive"/>';
        $this->hard_exit();
    }

    /**
     * feedbacks_admin_class::load_items()
     * 
     * @return
     */
    function load_items() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TESTIMONIALS . " ORDER BY time_int DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id']);
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['approval'], 'axapprove_item');
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_item');
            $row['datum'] = date("d.m.Y", $row['time_int']);
            $this->FEEDB['items'][] = $row;
        }
    }


}
