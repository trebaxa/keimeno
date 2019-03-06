<?php


# Scripting by Trebaxa Company(R) 2011    									*

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



class country_class extends keimeno_class {

    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    function cmd_reg_save() {
        if (is_array($_POST['REGIS'])) {
            foreach ($_POST['REGIS'] as $id => $row) {
                $id = $row['id'];
                unset($row['id']);
                update_table(TBL_CMS_LANDREGIONS, 'id', $id, $row);
            }
        }
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('epage=countrymanager.inc&cmd=region');
        $this->hard_exit();
    }

    function reinstall_countries() {
        include_once (CMS_ROOT . 'admin/inc/update.class.php');
        $upt = new upt_class();
        $upt->reinstall_countries();
        unset($upt);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('epage=countrymanager.inc&cmd=region');
        $this->hard_exit();

    }

    function cmd_region_saveregion() {
        if ($_POST['id'] > 0) {
            update_table(TBL_CMS_LANDREGIONS, 'id', $_POST['id'], $_POST['FORM']);
        }
        else {
            insert_table(TBL_CMS_LANDREGIONS, $_POST['FORM']);
        }
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('epage=countrymanager.inc&cmd=region');
        $this->hard_exit();
    }

    function load_regions() {
        $result = $this->db->query("SELECT *, R.id AS RID FROM " . TBL_CMS_LANDREGIONS . " R 
		LEFT JOIN " . TBL_CMS_LANDCONTINET . " C ON (C.id=R.lr_continet_id)
		WHERE 1 
		GROUP BY R.id
		ORDER BY C.lc_name, R.lr_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['RID']);
            $row['icons'][] = kf::gen_del_icon($row['RID'], true, 'delete_region');
            #kf::gen_del_icon_reload($row['RID'], 'delete_region');
            $regions[] = $row;
        }
        $this->smarty->assign('regions', $regions);
        $this->load_continents();
    }

    function load_regions_by_continent($continentid) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANDREGIONS . " WHERE lr_continet_id=" . (int)$continentid . " ORDER BY lr_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id']);
            $row['icons'][] = kf::gen_del_icon($row['RID'], true, 'delete_region');
            $regions[] = $row;
        }
        $this->smarty->assign('regions_by_continent', $regions);
    }

    function load_region($id) {
        $REGION = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANDREGIONS . " WHERE id=" . (int)$id);
        $this->smarty->assign('regionobj', $REGION);
    }

    function load_continents() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANDCONTINET . " WHERE 1 ORDER BY lc_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $continents[] = $row;
        }
        $this->smarty->assign('continents', $continents);
    }

    function load_countries_by_region($regionid) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LAND . " WHERE region_id=" . (int)$regionid . " ORDER BY land");
        while ($row = $this->db->fetch_array_names($result)) {
            $lands[] = $row;
            $land_ids[] = $row['id'];
        }
        $this->smarty->assign('countries_by_region', $lands);
        $this->smarty->assign('countrids_by_region', $land_ids);
    }

    function cmd_save_land_table() {
        foreach ($_POST['FORM'] as $word_id => $land_arr) {
            $transland = "";
            foreach ($land_arr as $land_id => $word) {
                $transland .= ($transland != "") ? ';' : '';
                $transland .= $land_id . '|' . $word;
            }
            $this->db->query("UPDATE " . TBL_CMS_LAND . " SET transland='" . $this->db->real_escape_string($transland) . "' WHERE id=" . $word_id);
        }

        foreach ($_POST['LOPT'] as $id => $row) {
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_CMS_LAND, 'id', $id, $row);
        }
        $this->ej();
    }

    function delete_country($country_id) {
        $inuse = false;
        if ($this->gbl_config['mod_wilinku'] == 1) {
            $inuse = (get_data_count(TBL_CMS_WLU_COUNTRY_TO_CAT, 'cm_countryid', "cm_countryid=" . $country_id) > 0 || get_data_count(TBL_CMS_WLU_VIDEO_TO_COUNTRY, 'vc_countryid',
                "vc_countryid=" . $country_id) > 0);
        }
        if ($inuse == false) {
            $this->db->query("DELETE FROM " . TBL_CMS_LAND . " WHERE id=" . $country_id . " AND id<>1 LIMIT 1");
        }
        return $inuse;
    }

    function cmd_del_country() {
        $country_id = (int)$_GET['id'];
        $inuse = $this->delete_country($country_id);
        if ($inuse == false) {
            $this->msg('{LBL_DELETED}');
            $this->TCR->redirect('epage=countrymanager.inc');
            $this->hard_exit();
        }
        else {
            $this->msge('Country in use');
            $this->TCR->redirect('epage=countrymanager.inc');
            $this->hard_exit();
        }
    }

    function cmd_add_land() {
        insert_table(TBL_CMS_LAND, $_POST['FORM']);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->redirect('epage=countrymanager.inc');
        $this->hard_exit();
    }

    function cmd_delete_region() {
        if (get_data_count(TBL_CMS_LAND, 'id', "region_id=" . $_GET['ident']) > 0) {
            $this->msge('Countries included. Can not be deleted.');
            $this->ej();
        }
        else {
            $this->db->query("DELETE FROM " . TBL_CMS_LANDREGIONS . " WHERE id=" . $_GET['ident']);
            $this->ej();
        }
    }

}

?>