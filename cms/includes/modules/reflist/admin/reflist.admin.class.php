<?php

/**
 * @package    reflist
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


defined('IN_SIDE') or die('Access denied.');

class reflist_admin_class extends reflist_master_class {

    protected $REFLIST = array();

    /**
     * reflist_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * reflist_admin_class::cmd_del_ref()
     * 
     * @return
     */
    function cmd_del_ref() {
        $REFLINK = $this->db->query_first("SELECT * FROM " . TBL_CMS_REFLINKS . " WHERE id=" . (int)$_GET['ident']);
        @unlink(CMS_ROOT . 'file_data/reflist/' . $REFLINK['r_img']);
        $this->db->query("DELETE FROM " . TBL_CMS_REFLINKS . " WHERE id=" . (int)$_GET['ident']);
        $this->ej();
    }

    /**
     * reflist_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * reflist_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('REFLIST', $this->REFLIST);
    }


    /**
     * reflist_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * reflist_admin_class::load_reflinks()
     * 
     * @return
     */
    function load_reflinks() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_REFLINKS . " WHERE 1 ORDER BY r_firma");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['r_img'] != "") {
                $row[thumb] = kf::gen_thumbnail('/file_data/reflist/' . $row['r_img'], 160, 90, 'crop');
            }
            else {
                $row[thumb] = kf::gen_thumbnail('/images/opt_member_nopic.jpg', 160, 90, 'crop');
            }
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_ref');
            $this->REFLIST['links'][] = $row;
        }
        $CONFIG_OBJ = new config_class('reflist');
        $this->REFLIST['config'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * reflist_admin_class::cmd_load_reflink()
     * 
     * @return
     */
    function cmd_load_reflink() {
        $REFLINK = $this->db->query_first("SELECT * FROM " . TBL_CMS_REFLINKS . " WHERE id=" . (int)$_GET['id']);
        echo json_encode($this->arr_trimhsc($REFLINK));
        $this->hard_exit();
    }

    /**
     * reflist_admin_class::cmd_refresh_all_scr()
     * 
     * @return
     */
    function cmd_refresh_all_scr() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_REFLINKS . " WHERE 1 ORDER BY r_firma");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['r_url'] != "") {
                $filename = CMS_ROOT . 'file_data/reflist/' . $this->unique_filename(CMS_ROOT . 'file_data/reflist/', $this->get_domain_name_of_url($row['r_url']) . '.jpg');
                $command = sprintf('%s --width 1300 --height 1000 --redirect-delay 10000 --no-stop-slow-scripts --load-error-handling ignore --enable-javascript --javascript-delay 1000 %s %s',
                    'wkhtmltoimage', escapeshellarg($row['r_url']), $filename);
                $output = array();
                exec($command, $output);
                $FORM['r_img'] = basename($filename);
                update_table(TBL_CMS_REFLINKS, 'id', $row['id'], $FORM);
            }
        }
        $this->ej();
    }

    /**
     * reflist_admin_class::cmd_save_reflink()
     * 
     * @return
     */
    function cmd_save_reflink() {
        $FORM = (array )$_POST['FORM'];
        if (!is_dir(CMS_ROOT . 'file_data/reflist/'))
            mkdir(CMS_ROOT . 'file_data/reflist/', 0775);
        if ($_POST['id'] > 0) {
            $REFLINK = $this->db->query_first("SELECT * FROM " . TBL_CMS_REFLINKS . " WHERE id=" . (int)$_POST['id']);
            if ($REFLINK['r_img'] != "") {
                @unlink(CMS_ROOT . 'file_data/reflist/' . $REFLINK['r_img']);
                $FORM['r_img'] = '';
            }
        }
        if ($_POST['FORM']['r_url'] != "") {
            $filename = CMS_ROOT . 'file_data/reflist/' . $this->unique_filename(CMS_ROOT . 'file_data/reflist/', $this->get_domain_name_of_url($_POST['FORM']['r_url']) .
                '.jpg');
            $command = sprintf('%s --width 1300 --height 1000 --no-stop-slow-scripts --enable-javascript --javascript-delay 1000 %s %s', 'wkhtmltoimage', escapeshellarg($_POST['FORM']['r_url']),
                $filename);
            $output = array();
            exec($command, $output);
            if (file_exists($filename))
                $FORM['r_img'] = basename($filename);
            else
                $FORM['r_img'] = "";
        }

        if ($_POST['id'] > 0) {
            update_table(TBL_CMS_REFLINKS, 'id', $_POST['id'], $FORM);
        }
        else {
            insert_table(TBL_CMS_REFLINKS, $FORM);
        }
        $this->ej('reload_reflinks');
    }

    /**
     * reflist_admin_class::cmd_reload_reflinks()
     * 
     * @return
     */
    function cmd_reload_reflinks() {
        $this->load_reflinks();
        $this->parse_to_smarty();
        kf::echo_template('reflist.table');
    }

    /**
     * reflist_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='reflist' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * reflist_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $upt = array('tm_content' => '{TMPL_REFLISTINLAY_' . $cont_matrix_id . '}', 'tm_pluginfo' => 'Referenzen');
        $upt = $this->real_escape($upt);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $upt);
    }


}

?>