<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class employee_class extends keimeno_class {

    var $lang_matrix = array();
    var $employee_obj = array();
    var $country_matrix = array();
    var $country_id_matrix = array();
    var $lang_id_matrix = array();
    var $countries = array();
    var $redirect_page = 'employee.inc';

    /**
     * employee_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * employee_class::set_redirect()
     * 
     * @param mixed $redirect_page
     * @return
     */
    function set_redirect($redirect_page) {
        $this->redirect_page = $redirect_page;
    }

    /**
     * employee_class::cmd_emp_delete()
     * 
     * @return
     */
    function cmd_emp_delete() {
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINS . " WHERE id=" . (int)$_GET['ident'] . " AND id>1 LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINMATRIX . " WHERE em_mid=" . (int)$_GET['ident'] . " AND em_mid>1");
        exec_evt('OnDeleteEmployee', array('mid' => (int)$_GET['id']));
        $this->ej();
    }

    /**
     * employee_class::cmd_emp_approve()
     * 
     * @return
     */
    function cmd_emp_approve() {
        $this->set_approve($_GET['value'], $_GET['ident']);
        #$this->msg('{LBLA_SAVED}');
        #$this->TCR->redirect('epage=' . $this->redirect_page);
        $this->hard_exit();
    }

    /**
     * employee_class::set_approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function set_approve($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_ADMINS . " SET approval='" . (int)$value . "' WHERE id=" . (int)$id . " LIMIT 1");
    }

    /**
     * employee_class::add_err()
     * 
     * @param mixed $txt
     * @return
     */
    function add_err($txt) {
        parent::add_err($txt);
    }

    /**
     * employee_class::cmd_upload_profil_img()
     * 
     * @return
     */
    function cmd_upload_profil_img() {
        $id = $_POST['id'];
        if ($_FILES['datei']['name'] != "") {
            if (!validate_upload_file($_FILES['datei'], TRUE)) {
                $this->msge($_SESSION['upload_msge']);
            }
        }
        $img = "";
        if ($_FILES['datei']['name'] != "") {
            $dir = CMS_ROOT . 'admin/images/employees/';
            if (!is_dir($dir))
                mkdir($dir, 0775);
            $f_name = $id . '_employee_profil.' . $this->get_ext($_FILES['datei']['name']);
            delete_file($dir . $f_name);
            if (move_uploaded_file($_FILES['datei']['tmp_name'], $dir . $f_name)) {
                $this->LOGCLASS->addLog('UPLOAD', 'employee profil foto ' . $f_name);
                update_table(TBL_CMS_ADMINS, 'id', $id, array('mi_profil_img' => $f_name));
                $img = "./images/employees/" . $f_name;
            }
        }
        $this->msg('{LBLA_SAVED}');
        $this->ej('set_emp_id', $id . ',"' . $img . '"');
    }

    /**
     * employee_class::cmd_delete_profil_img()
     * 
     * @return
     */
    function cmd_delete_profil_img() {
        $ADMIN = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . $_GET['id']);
        delete_file(CMS_ROOT . 'admin/images/employees/' . $ADMIN['mi_profil_img']);
        update_table(TBL_CMS_ADMINS, 'id', $_GET['id'], array('mi_profil_img' => ''));
        $this->hard_exit();
    }

    /**
     * employee_class::emp_saveemployee()
     * 
     * @return
     */
    function emp_saveemployee() {
        if ($_POST['id'] > 0) {
            if ($_POST['FORM']['passwort'] != "")
                $_POST['FORM']['passwort'] = encrypt_password($_POST['FORM']['passwort']);
            else
                unset($_POST['FORM']['passwort']);
            if (isset($_POST['FORM_NOTEMPTY'])) {
                foreach ((array )$_POST['FORM_NOTEMPTY'] as $key => $wert) {
                    if (strlen($wert) == 0) {
                        $this->msge('{LBL_PLEASEFILLOUT}...');
                        return;
                    }
                    $_POST['FORM'][$key] = $wert;
                }
            }
            update_table(TBL_CMS_ADMINS, 'id', $_POST['id'], $_POST['FORM']);
            $id = $_POST['id'];
        }
        else {
            if (get_data_count(TBL_CMS_ADMINS, 'id', "mitarbeiter_name='" . $_POST['FORM']['mitarbeiter_name'] . "'") > 0) {
                $this->msge('{LBL_EMPLOYEE} {LBL_ALREADYEXISTS}');
                return;
            }
            foreach ($_POST['FORM_NOTEMPTY'] as $key => $wert) {
                if (strlen($wert) == 0) {
                    $this->msge('{LBL_PLEASEFILLOUT}...');
                    return;
                }
                $_POST['FORM'][$key] = $wert;
            }
            if ($this->has_errors() == true) {
                return;
            }
            $_POST['FORM']['del'] = 0;
            $_POST['FORM']['passwort'] = encrypt_password($_POST['FORM']['passwort']);
            $id = insert_table(TBL_CMS_ADMINS, $_POST['FORM']);

            # set standard languages
            $EM = array(
                'em_mid' => $id,
                'em_relid' => 1,
                'em_type' => 'LNG');
            insert_table(TBL_CMS_ADMINMATRIX, $EM);
            $EM = array(
                'em_mid' => $id,
                'em_relid' => 2,
                'em_type' => 'LNG');
            insert_table(TBL_CMS_ADMINMATRIX, $EM);
        }


        $this->employee_id = $id;
        $this->set_country_responsibilities($this->employee_id);
    }

    /**
     * employee_class::cmd_emp_saveemployee()
     * 
     * @return
     */
    function cmd_emp_saveemployee() {
        $this->emp_saveemployee();
        if ($this->has_errors() == true) {
            $this->ej();
        }
        $this->msg('{LBLA_SAVED}');
        $this->ej('set_emp_id', $this->employee_id . ',"' . $img . '"');
    }

    /**
     * employee_class::get_employee_id()
     * 
     * @return
     */
    function get_employee_id() {
        return $this->employee_id;
    }

    /**
     * employee_class::load_lang_list()
     * 
     * @return
     */
    function load_lang_list() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $langs[] = $row;
        }
        $this->smarty->assign('emplanglist', $langs);
    }

    /**
     * employee_class::load_admin_groups()
     * 
     * @return
     */
    function load_admin_groups() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE 1 ORDER BY mgname");
        while ($row = $this->db->fetch_array_names($result)) {
            $gr[] = $row;
        }
        $this->smarty->assign('employeegroups', $gr);
    }

    /**
     * employee_class::load_employee_lang_matrix()
     * 
     * @param mixed $mid
     * @return
     */
    function load_employee_lang_matrix($mid) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINMATRIX . " WHERE em_type='LNG' AND em_mid=" . (int)$mid);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->lang_matrix[] = $row;
            $this->lang_id_matrix[] = $row['em_relid'];
        }
        return $this->lang_matrix;
    }

    /**
     * employee_class::load_employee_country_matrix()
     * 
     * @param mixed $mid
     * @return
     */
    function load_employee_country_matrix($mid) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINMATRIX . " WHERE em_type='COU' AND em_mid=" . (int)$mid);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->country_matrix[] = $row;
            $this->country_id_matrix[$row['em_relid']] = $row['em_relid'];
        }
        return $this->lang_matrix;
    }

    /**
     * employee_class::get_first_element()
     * 
     * @param mixed $arr
     * @return
     */
    function get_first_element($arr) {
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                return $value;
            }
        }
    }

    /**
     * employee_class::set_continent_region_header()
     * 
     * @return
     */
    function set_continent_region_header() {
        /*
        $result = $this->db->query("SELECT *, C.id AS COUNTRYID FROM ".TBL_CMS_ADMINMATRIX." M
        , ".TBL_CMS_LAND." C
        , ".TBL_CMS_LANDCONTINET." CO 
        , ".TBL_CMS_LANDREGIONS." R
        WHERE M.em_type='COU' AND M.em_mid=".(int)$mid . " 
        AND M.em_relid=C.id
        AND R.lr_continet_id=CO.id
        AND C.region_id=R.id
        ORDER BY C.land
        ");
        while($row = $this->db->fetch_array_names($result)){
        $row['country_rel'] = 'E';
        $this->countries[$row['lr_continet_id']][$row['region_id']][$row['em_relid']]=$row;
        $this->employee_obj['all_country_ids'][$row['em_relid']] = $row['em_relid'];
        $this->employee_obj['responsible_country_names'][$row['COUNTRYID']] = $row['land'];
        $this->employee_obj['responsible_countries'][$row['COUNTRYID']] = $row;
        }
        */
        if (is_array($this->employee_obj['countries'])) {
            foreach ($this->employee_obj['countries'] as $continetid => $regions) {
                $r = $this->get_first_element($regions);
                $c = $this->get_first_element($r);
                $this->employee_obj['countries'][$continetid]['lc_name'] = $c['lc_name'];
                foreach ($regions as $regionid => $country) {
                    $c = $this->get_first_element($country);
                    $this->employee_obj['countries'][$continetid][$regionid]['lr_name'] = $c['lr_name'];
                }
            }
        }
        #echoarr($this->countries);
        return $this->employee_obj['countries'];
    }

    /**
     * employee_class::reload_employee()
     * 
     * @param mixed $mid
     * @return
     */
    function reload_employee($mid) {
        $this->load_employee($mid);
        $this->set_login_session();
    }

    /**
     * employee_class::load_employee()
     * 
     * @param mixed $mid
     * @param string $smarty_joker
     * @return
     */
    function load_employee($mid, $smarty_joker = 'EMPLOYEE') {
        $mid = (int)$mid;
        $this->employee_obj = $this->db->query_first("SELECT M.*,G.allowed,G.id AS GROUPID, M.id AS MID FROM " . TBL_CMS_ADMINS . " M 
	LEFT JOIN " . TBL_CMS_ADMINGROUPS . " G ON (G.id=M.gid) 
	WHERE M.id='" . $mid . "' LIMIT 1");

        $this->employee_obj['all_country_ids'] = array();
        $this->employee_obj['lang_matrix'] = $this->load_employee_lang_matrix($mid);
        $this->employee_obj['lang_id_matrix'] = $this->lang_id_matrix;

        $this->employee_obj['country_matrix'] = $this->load_employee_country_matrix($mid);
        $this->employee_obj['country_id_matrix'] = $this->country_id_matrix;
        $this->employee_obj['mi_profil_img'] = ($this->employee_obj['mi_profil_img'] != "") ? '/admin/images/employees/' . $this->employee_obj['mi_profil_img'] :
            '/images/opt_member_nopic.jpg';
        $this->employee_obj['thumb'] = kf::gen_thumbnail($this->employee_obj['mi_profil_img'], 90, 90, 'crop');
        $this->load_responsible_countries($mid);
        $this->set_continent_region_header();

        unset($this->employee_obj['passwort']);
        $this->employee_obj['PERM'] = new perm_class();
        $this->employee_obj['PERM']->loadPermFromGroup($this->employee_obj['GROUPID']);
        $this->smarty->assign($smarty_joker, $this->employee_obj);
        return $this->employee_obj;
    }

    /**
     * employee_class::load_responsible_countries()
     * 
     * @param mixed $mid
     * @return
     */
    function load_responsible_countries($mid) {
        $this->employee_obj['all_country_ids'] = array();
        $this->employee_obj['responsible_countries'] = array();
        $this->employee_obj['responsible_countries_ids'] = array();
        $result = $this->db->query("SELECT *, C.id AS COUNTRYID FROM " . TBL_CMS_ADMIN_RESPCOUNTRIES . " M
	, " . TBL_CMS_LAND . " C
	, " . TBL_CMS_LANDCONTINET . " CO
	, " . TBL_CMS_LANDREGIONS . " R
	WHERE " . (($mid <> 100) ? " M.ec_eid=" . (int)$mid . " AND " : " M.ec_eid=1 AND ") . "  
	M.ec_countryid=C.id
	AND R.lr_continet_id=CO.id
	AND C.region_id=R.id
	GROUP BY COUNTRYID
	ORDER BY C.land
	");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['country_rel'] = $row['ec_type'];
            $this->employee_obj['countries'][$row['lr_continet_id']][$row['region_id']][$row['ec_countryid']] = $row;
            $this->employee_obj['responsible_countries_ids'][$row['ec_countryid']] = $row['ec_countryid'];
            $this->employee_obj['responsible_country_names'][$row['COUNTRYID']] = $row['land'];
            $this->employee_obj['responsible_countries'][$row['COUNTRYID']] = $row;
        }
        $this->employee_obj['responsible_countries'] = sort_db_result($this->employee_obj['responsible_countries'], 'land', SORT_ASC, SORT_STRING);
    }

    /**
     * employee_class::cmd_add_lang_matrix()
     * 
     * @return
     */
    function cmd_add_lang_matrix() {
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINMATRIX . " WHERE em_type='LNG' AND em_mid=" . (int)$_POST['employee_id']);
        if (is_array($_POST['lang_ids'])) {
            foreach ($_POST['lang_ids'] as $key => $id) {
                $EM = array(
                    'em_mid' => (int)$_POST['employee_id'],
                    'em_relid' => $id,
                    'em_type' => 'LNG');
                insert_table(TBL_CMS_ADMINMATRIX, $EM);
            }
        }
        $this->load_employee($_SESSION['mitarbeiter']);
        $this->set_login_session();
        $this->ej();
    }

    /**
     * employee_class::cmd_add_country_matrix()
     * 
     * @return
     */
    function cmd_add_country_matrix() {
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINMATRIX . " WHERE em_type='COU' AND em_mid=" . (int)$_POST['employee_id']);
        foreach ($_POST['country_ids'] as $key => $id) {
            $EM = array(
                'em_mid' => (int)$_POST['employee_id'],
                'em_relid' => $id,
                'em_type' => 'COU');
            insert_table(TBL_CMS_ADMINMATRIX, $EM);
        }
        $this->set_country_responsibilities((int)$_POST['employee_id']);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('continentid=' . $_POST['continentid'] . '&regionid=' . $_POST['regionid'] . '&epage=' . $this->redirect_page . '&cmd=countryrelated&id=' .
            $_POST['employee_id']);
        $this->hard_exit();
    }

    /**
     * employee_class::set_country_responsibilities()
     * 
     * @param mixed $empid
     * @param string $type
     * @return
     */
    function set_country_responsibilities($empid, $type = 'E') {
        $this->db->query("DELETE FROM " . TBL_CMS_ADMIN_RESPCOUNTRIES . " WHERE ec_eid=" . $empid);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINMATRIX . " WHERE em_type='COU' AND em_mid=" . (int)$empid);
        while ($row = $this->db->fetch_array_names($result)) {
            $EC_MARTIX = array(
                'ec_eid' => $empid,
                'ec_countryid' => $row['em_relid'],
                'ec_type' => $type);
            insert_table(TBL_CMS_ADMIN_RESPCOUNTRIES, $EC_MARTIX);
        }
    }

    /**
     * employee_class::set_login_session()
     * 
     * @return
     */
    function set_login_session() {
        $_SESSION['admin_obj'] = $this->employee_obj; #$mitarbeiter_obj;
        $_SESSION['mitarbeiter'] = $this->employee_obj['id']; #$mitarbeiter_obj['id'];
        $_SESSION['mgroups'] = $this->employee_obj['allowed']; #$mitarbeiter_obj['allowed'];
        $_SESSION['mitarbeiter_name'] = $this->employee_obj['mitarbeiter_name']; #$mitarbeiter_obj['mitarbeiter_name'];
        $_SESSION['mids'] = explode(';', $_SESSION['mgroups']);
    }

    /**
     * employee_class::load_employees()
     * 
     * @return
     */
    function load_employees() {
        $result = $this->db->query("SELECT *,M.id AS MID FROM " . TBL_CMS_ADMINS . " M
  	LEFT JOIN " . TBL_CMS_ADMINGROUPS . " G ON (G.id=M.gid) 
  	GROUP BY M.id 
  	ORDER BY mitarbeiter_name");
        while ($row = $this->db->fetch_array_names($result)) {
            if (ISADMIN == 1) {
                if ($row['MID'] != 100)
                    $row['icons'][] = kf::gen_edit_icon($row['MID']);
                if ($row['MID'] != 1)
                    $row['icons'][] = kf::gen_approve_icon($row['MID'], $row['approval'], 'emp_approve');
                if ($row['del'] == 0)
                    $row['icons'][] = kf::gen_del_icon($row['MID'], true, 'emp_delete');
                #  $row['icons'][] = '<a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=countryrelated&id=' . $row['MID'] .
                #      '"><i class="fa fa-globe" ><!----></i></a>';
                $row['mi_profil_img'] = ($row['mi_profil_img'] != "") ? '/admin/images/employees/' . $row['mi_profil_img'] : '/images/opt_member_nopic.jpg';
                $row['thumb'] = kf::gen_thumbnail($row['mi_profil_img'], 90, 90, 'crop');
            }
            $this->employees[$row['MID']] = $row;
        }
        $this->smarty->assign('employees', $this->employees);
    }

    /**
     * employee_class::update_admin_online_status()
     * 
     * @return
     */
    function update_admin_online_status() {
        $CLIENT = $liste = array();
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINLOG . " WHERE mid=" . (int)$this->employee_obj['MID'] . " OR zeit<'" . (time() - (60 * 3)) . "'");
        if ($this->employee_obj['MID'] > 0) {
            $CLIENT['zeit'] = time();
            $CLIENT['ip'] = getenv('REMOTE_ADDR');
            $CLIENT['browser'] = $_SERVER['HTTP_USER_AGENT'];
            $CLIENT['akt_page'] = $_SERVER['REQUEST_URI'];
            $CLIENT['phpfile'] = basename($_SERVER['PHP_SELF']);
            $CLIENT['mname'] = $_SESSION['mitarbeiter_name'];
            $CLIENT['mid'] = $_SESSION['mitarbeiter'];
            $CLIENT['getvars'] = ((isset($_REQUEST['id'])) ? $_REQUEST['id'] : "");
            $CLIENT = $this->real_escape($CLIENT);
            insert_table(TBL_CMS_ADMINLOG, $CLIENT);
            $liste = "";
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINLOG . " WHERE mid<>" . (int)$this->employee_obj['MID'] . " 
		      AND getvars='" . $CLIENT['getvars'] . "' 
		      AND phpfile='" . basename($_SERVER['PHP_SELF']) . "'");
            while ($row = $this->db->fetch_array_names($result)) {
                $liste[] = $row;
            }
        }
        $this->smarty->assign('other_employees_working', $liste);
        return (array )$liste;
    }


}
