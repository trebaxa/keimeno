<?php

/**
 * @package    workshop
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class workshop_admin_class extends workshop_master_class {

    protected $WORKSHOP = array();

    /**
     * workshop_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->load_cities();
    }

    /**
     * workshop_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$_GET['ident'] . "' LIMIT 1");
        $this->hard_exit();
    }


    /**
     * workshop_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('WORKSHOP', $this->WORKSHOP);
    }


    /**
     * workshop_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('workshop');
        $this->WORKSHOP['CONFIG'] = $CONFIG_OBJ->buildTable();
        $this->parse_to_smarty();
        kf::echo_template('workshop.conf');
    }

    /**
     * workshop_admin_class::load_cities()
     * 
     * @return
     */
    function load_cities() {
        $this->WORKSHOP['cities'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_WS_CITIES . " ORDER BY c_city");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id'], '', 'editcity');
            $row['icons'][] = kf::gen_del_icon($row['id'], false, 'del_city');
            # $row['icons'][] = kf::gen_approve_icon($row['id'], $row['lc_approval'], 'axapprovegroup');
            $row['count'] = get_data_count(TBL_WS_WORKSHOPS, '*', "ws_city='" . $row['id'] . "'");
            $this->WORKSHOP['cities'][] = $row;
        }
    }

    /**
     * workshop_admin_class::cmd_editcity()
     * 
     * @return
     */
    function cmd_editcity() {
        $CITY = $this->db->query_first("SELECT * FROM " . TBL_WS_CITIES . " WHERE id=" . (int)$_GET['id']);
        $this->WORKSHOP['city'] = $CITY;
    }

    /**
     * workshop_admin_class::cmd_save_city()
     * 
     * @return
     */
    function cmd_save_city() {
        $FORM = (array )$_POST['FORM'];
        update_table(TBL_WS_CITIES, 'id', $_POST['id'], $FORM);

        # upload theme
        if (!is_dir($this->imgroot))
            mkdir($this->imgroot, 0755);
        if (is_dir($this->imgroot)) {
            $CITY = $this->load_city($_POST['id']);
            if ($_FILES['datei']['name'] != "") {
                if (!validate_upload_file($_FILES['datei'])) {
                    $this->msge($_SESSION['upload_msge']);
                    $this->ej();
                }
                @unlink($this->imgroot . $CITY['c_image']);
                $new_file_name = $this->format_file_name($_FILES['datei']['name']);
                $new_file_name = $this->unique_filename($this->imgroot, $new_file_name);
                move_uploaded_file($_FILES['datei']['tmp_name'], $this->imgroot . $new_file_name);
                chmod($this->imgroot . $new_file_name, 0755);
                $arr['c_image'] = $new_file_name;
                update_table(TBL_WS_CITIES, 'id', $_POST['id'], $arr);
            }
        }
        $this->ej('reload_cities');
    }

    /**
     * workshop_admin_class::delete_city_image()
     * 
     * @param mixed $id
     * @return
     */
    function delete_city_image($id) {
        $CITY = $this->load_city($id);
        @unlink($this->imgroot . $CITY['c_image']);
        $arr['c_image'] = '';
        update_table(TBL_WS_CITIES, 'id', $id, $arr);
    }

    /**
     * workshop_admin_class::cmd_ws_delete_city_image()
     * 
     * @return
     */
    function cmd_ws_delete_city_image() {
        $this->delete_city_image($_GET['id']);
        $this->hard_exit();
    }


    /**
     * workshop_admin_class::cmd_load_cities()
     * 
     * @return
     */
    function cmd_load_cities() {
        $this->load_cities();
        $this->parse_to_smarty();
        kf::echo_template('workshop.cities');
    }

    /**
     * workshop_admin_class::cmd_del_city()
     * 
     * @return
     */
    function cmd_del_city() {
        if (get_data_count(TBL_WS_WORKSHOPS, '*', "ws_city='" . $_GET['ident'] . "'") == 0) {
            $this->delete_city_image($_GET['ident']);
            $this->db->query("DELETE FROM " . TBL_WS_CITIES . " WHERE id=" . $_GET['ident']);
        }
        else {
            $this->msge('Beinhaltet noch Workshops');
            $this->ej();
        }
        $this->ej();
    }

    /**
     * workshop_admin_class::cmd_save_cities()
     * 
     * @return
     */
    function cmd_save_cities() {
        foreach ($_POST['FORM'] as $key => $row) {
            update_table(TBL_WS_CITIES, 'id', $key, $row);
        }
        $this->ej();
    }

    /**
     * workshop_admin_class::cmd_add_city()
     * 
     * @return
     */
    function cmd_add_city() {
        if (get_data_count(TBL_WS_CITIES, '*', "c_city='" . $_POST['FORM']['c_city'] . "'") == 0) {
            insert_table(TBL_WS_CITIES, $_POST['FORM']);
        }
        else {
            $this->msge('Stadt schon verhanden');
            $this->ej();
        }
        $this->ej('reload_cities');
    }


    /**
     * workshop_admin_class::cmd_reload_workshops()
     * 
     * @return
     */
    function cmd_reload_workshops() {
        $this->load_cities();
        $result = $this->db->query("SELECT * FROM " . TBL_WS_WORKSHOPS . " WHERE ws_city=" . (int)$_GET['city'] . " ORDER BY ws_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id'], '', 'editws');
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_workshop');
            # $row['icons'][] = kf::gen_approve_icon($row['id'], $row['lc_approval'], 'axapprovegroup');
            $row['cust_count'] = get_data_count(TBL_WS_BOOKINGS, '*', "wb_wid='" . $row['id'] . "'");
            $this->set_workshop_opt($row);
            $this->WORKSHOP['workshops'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('workshop.workshops');
    }

    /**
     * workshop_admin_class::cmd_add_workshop()
     * 
     * @return
     */
    function cmd_add_workshop() {
        insert_table(TBL_WS_WORKSHOPS, $_POST['FORM']);
        $this->ej('reload_workshops', $_POST['FORM']['ws_city']);
    }

    /**
     * workshop_admin_class::cmd_editws()
     * 
     * @return
     */
    function cmd_editws() {
        $this->WORKSHOP['ws'] = $this->load_workshop($_GET['id']);
        foreach ($this->WORKSHOP['ws']['bookings'] as $key => $row) {
            $this->WORKSHOP['ws']['bookings'][$key]['icons'][] = kf::gen_del_icon($row['id'], true, 'del_cust_work');
        }
    }

    /**
     * workshop_admin_class::delete_ws_theme_image()
     * 
     * @param mixed $id
     * @return
     */
    function delete_ws_theme_image($id) {
        $WS = $this->load_workshop($id);
        @unlink($this->imgroot . $WS['ws_theme']);
        $arr['ws_theme'] = '';
        update_table(TBL_WS_WORKSHOPS, 'id', $id, $arr);
    }

    /**
     * workshop_admin_class::cmd_ws_delete_theme_image()
     * 
     * @return
     */
    function cmd_ws_delete_theme_image() {
        $this->delete_ws_theme_image($_GET['id']);
        $this->hard_exit();
    }

    /**
     * workshop_admin_class::cmd_del_cust_work()
     * 
     * @return
     */
    function cmd_del_cust_work() {
        $this->db->query("DELETE FROM " . TBL_WS_BOOKINGS . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * workshop_admin_class::cmd_save_workshop()
     * 
     * @return
     */
    function cmd_save_workshop() {
        $FORM = (array )$_POST['FORM'];
        $FORM['ws_date'] = self::date_to_sqldate($FORM['ws_date']);
        $FORM['ws_price_br'] = self::validate_num_for_sql($FORM['ws_price_br']);
        $FORM['ws_datetime'] = strtotime($FORM['ws_date'] . ' ' . $FORM['ws_time']);
        $FORM['ws_datetime'] = strtotime($FORM['ws_date'] . ' ' . $FORM['ws_time_to']);
        update_table(TBL_WS_WORKSHOPS, 'id', $_POST['id'], $FORM);

        # upload theme
        if (!is_dir($this->imgroot))
            mkdir($this->imgroot, 0755);
        if (is_dir($this->imgroot)) {
            $WORKSHOP = $this->load_workshop($_POST['id']);
            if ($_FILES['datei']['name'] != "") {
                if (!validate_upload_file($_FILES['datei'])) {
                    $this->msge($_SESSION['upload_msge']);
                    $this->ej();
                }
                @unlink($this->imgroot . $WORKSHOP['ws_theme']);
                $new_file_name = $this->format_file_name($_FILES['datei']['name']);
                $new_file_name = $this->unique_filename($this->imgroot, $new_file_name);
                move_uploaded_file($_FILES['datei']['tmp_name'], $this->imgroot . $new_file_name);
                chmod($this->imgroot . $new_file_name, 0755);
                $arr['ws_theme'] = $new_file_name;
                update_table(TBL_WS_WORKSHOPS, 'id', $_POST['id'], $arr);
            }
        }
        $this->ej('reloadtheme');
    }

    /**
     * workshop_admin_class::cmd_reloadtheme()
     * 
     * @return
     */
    function cmd_reloadtheme() {
        $this->WORKSHOP['ws'] = $this->load_workshop($_GET['id']);
        $this->parse_to_smarty();
        kf::echo_template('workshop.theme');
    }

    /**
     * workshop_admin_class::cmd_save_workshoptable()
     * 
     * @return
     */
    function cmd_save_workshoptable() {
        $FORM = (array )$_POST['FORM'];
        foreach ($FORM as $key => $row) {
            update_table(TBL_WS_WORKSHOPS, 'id', $key, $row);
        }
        $this->ej();
    }

    /**
     * workshop_admin_class::cmd_add_image()
     * 
     * @return
     */
    function cmd_add_image() {
        if (!is_dir($this->imgroot))
            mkdir($this->imgroot, 0755);
        if (is_dir($this->imgroot)) {
            $WORKSHOP = $this->load_workshop($_POST['id']);
            if ($_FILES['datei']['name'] != "") {
                if (!validate_upload_file($_FILES['datei'])) {
                    $this->msge($_SESSION['upload_msge']);
                    $this->ej();
                }
                $new_file_name = $this->format_file_name($_FILES['datei']['name']);
                $new_file_name = $this->unique_filename($this->imgroot, $new_file_name);
                move_uploaded_file($_FILES['datei']['tmp_name'], $this->imgroot . $new_file_name);
                chmod($this->imgroot . $new_file_name, 0755);
                $WORKSHOP['images'][] = $new_file_name;
                $arr = array('ws_images' => serialize($WORKSHOP['images']));
                update_table(TBL_WS_WORKSHOPS, 'id', $_POST['id'], $arr);
            }
        }
        $this->ej('reload_images', $_POST['id']);
    }

    /**
     * workshop_admin_class::cmd_reload_images()
     * 
     * @return
     */
    function cmd_reload_images() {
        $WORKSHOP = $this->load_workshop($_GET['wsid']);
        $this->parse_to_smarty();
        kf::echo_template('workshop.images');
    }

    /**
     * workshop_admin_class::cmd_delete_img()
     * 
     * @return
     */
    function cmd_delete_img() {
        $WORKSHOP = $this->load_workshop($_GET['wsid']);
        foreach ($WORKSHOP['images'] as $key => $img) {
            if ($_GET['img'] == $img) {
                unset($WORKSHOP['images'][$key]);
                @unlink($this->imgroot . $img);
                $arr = array('ws_images' => serialize($WORKSHOP['images']));
                update_table(TBL_WS_WORKSHOPS, 'id', $_GET['wsid'], $arr);
                break;
            }
        }
        $this->cmd_reload_images();
    }


    /**
     * workshop_admin_class::cmd_del_workshop()
     * 
     * @return
     */
    function cmd_del_workshop() {
        $this->delete_ws_theme_image($_GET['ident']);
        $WORKSHOP = $this->load_workshop($_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_WS_WORKSHOPS . " WHERE id=" . $_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_WS_BOOKINGS . " WHERE wb_wid=" . $_GET['ident']);
        foreach ((array )$WORKSHOP['images'] as $key => $img) {
            @unlink($this->imgroot . $img);
        }
        $this->ej();
    }


}

?>