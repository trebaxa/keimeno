<?php

/**
 * @package    sitemap
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');
DEFINE('TBL_CMS_SITEMAP', TBL_CMS_PREFIX . 'sitemap');

class xmlsm_class extends modules_class {

    var $XMLSM = array();
    protected $MAP_INI = null;

    /**
     * xmlsm_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->MAP_INI = new site_mapini_class(TBL_CMS_SITEMAP, TBL_CMS_LANG);
    }

    /**
     * xmlsm_class::init()
     * 
     * @return
     */
    function init() {
        $this->XMLSM['MAPINI'] = $this->MAP_INI->MAP->configTable();
        $this->sync();
    }

    /**
     * xmlsm_class::cmd_delsitemap()
     * 
     * @return
     */
    function cmd_delsitemap() {
        $this->MAP_INI->MAP->delXMLFile($this->TCR->GET['file']);
        $this->TCR->set_just_turn_back(true);
        $this->TCR->reset_cmd('');
        $this->TCR->add_msg('{LBL_DELETED}');
    }

    /**
     * xmlsm_class::cmd_a_savesmconf()
     * 
     * @return
     */
    function cmd_a_savesmconf() {
        $this->MAP_INI->MAP->saveSMConf($this->TCR->POST['FORM']);
        $this->TCR->msg('{LBLA_SAVED}');
        $this->hard_exit();
    }

    /**
     * xmlsm_class::cmd_a_smapprove()
     * 
     * @return
     */
    function cmd_a_smapprove() {
        $this->db->query("UPDATE " . TBL_CMS_SITEMAP . " SET sm_active='" . intval($this->TCR->GET['value']) . "' WHERE id=" . $this->TCR->GET['ident'] . " LIMIT 1");
        $this->ej();
    }

    /**
     * xmlsm_class::cronjob()
     * 
     * @return void
     */
    function cronjob($params, $exec_class) {
        $start = self::get_micro_time();
        $this->MAP_INI->buildUrlTable();
        $sidegentime = number_format(self::get_micro_time() - $start, 4, ".", ".");
        $exec_class->feedback .= '<li>XML Sitemap (' . $sidegentime . ' sek)</li>';
        return $params;
    }

    /**
     * xmlsm_class::cmd_a_googlemaps()
     * 
     * @return
     */
    function cmd_a_googlemaps() {
        foreach ($_POST['FORM'] as $key => $row) {
            update_table(TBL_CMS_SITEMAP, 'id', $key, $row);
        }
        $result_lang = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANG . " WHERE id=" . $this->TCR->POST['sm_lang'] . " AND approval=1 ORDER BY post_lang");
        # if ($this->TCR->POST['sm_lang'] > 0)
        #     $MAP_INI = new site_mapini_class('../sitemap_' . $result_lang['local'] . '.xml', TBL_CMS_SITEMAP, TBL_CMS_LANG);
        #  else
        #      $MAP_INI = new site_mapini_class('../sitemap.xml', TBL_CMS_SITEMAP, TBL_CMS_LANG);
        $this->MAP_INI->buildUrlTable($this->TCR->POST['sm_lang']);
        $this->ej();
    }

    /**
     * xmlsm_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }


    /**
     * xmlsm_class::cmd_sendsmxml()
     * 
     * @return
     */
    function cmd_sendsmxml() {
        $this->MAP_INI->MAP->sendXMLFile($this->TCR->GET['id'], $this->TCR->GET['fname']);
    }


    /**
     * xmlsm_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('XMLSM', $this->XMLSM);
    }

    /**
     * xmlsm_class::sync()
     * 
     * @return
     */
    function sync() {
        $allmods = app_class::load_mods_to_array();
        foreach ($allmods as $modident => $mod) {
            $M = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='" . $modident . "'");
            if ($M['id'] == 0) {
                $arr = array(
                    'sm_title' => $mod['module_name'],
                    'sm_changefreq' => 'weekly',
                    'sm_priority' => 0.9,
                    'sm_active' => 1,
                    'sm_ident' => $modident,
                    );
                if ($mod['xmlsitemap'] == 1) {
                    insert_table(TBL_CMS_SITEMAP, $arr);
                }
            }
        }
    }
}

?>