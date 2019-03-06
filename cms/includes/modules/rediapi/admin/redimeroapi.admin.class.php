<?php

# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    rediapi
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


DEFINE('TBL_CMS_REDIAPI', TBL_CMS_PREFIX . 'rediapi');

class rediapi_admin_class extends rediapi_master_class {

    var $REDIAPI = array();

    /**
     * rediapi_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * rediapi_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->REDIAPI['functions'] = $this->functions;
        $this->smarty->assign('REDIAPI', $this->REDIAPI);
    }

    /**
     * rediapi_admin_class::cmd_save_api_keys()
     * 
     * @return
     */
    function cmd_save_api_keys() {
        $id = (int)$_POST['id'];
        $FORM = (array )$_POST['FORM'];
        if ($id > 0) {
            update_table(TBL_CMS_REDIAPI, 'id', $id, $FORM);
        }
        else {
            $FORM['r_time'] = time();
            insert_table(TBL_CMS_REDIAPI, $FORM);
        }
        $this->TCR->set_just_turn_back(true);
        $this->msg('{LBLA_SAVED}');
    }

    /**
     * rediapi_admin_class::load_apis()
     * 
     * @return
     */
    function load_apis() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_REDIAPI . " WHERE 1 ORDER BY r_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id']);
            $row['icons'][] = kf::gen_del_icon($row['id'], false, 'delapi');
            $apis[] = $row;
        }
        $this->REDIAPI['apis'] = (array )$apis;
    }

    /**
     * rediapi_admin_class::cmd_load_axapis()
     * 
     * @return
     */
    function cmd_load_axapis() {
        $this->load_apis();
        $this->parse_to_smarty();
        kf::simple_output('<% include file="rediapi.table.tpl" %>');
    }

    /**
     * rediapi_admin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $this->REDIAPI['API'] = $this->load_api($_GET['id']);
    }


    /**
     * rediapi_admin_class::cmd_delapi()
     * 
     * @return
     */
    function cmd_delapi() {
        $this->db->query("DELETE FROM " . TBL_CMS_REDIAPI . " WHERE id=" . (int)$_GET['ident']);
        $this->ej();
    }

    /**
     * rediapi_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='rediapi' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * rediapi_admin_class::load_function_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_function_integration($params) {
        return $this->functions;
    }

    /**
     * rediapi_admin_class::load_api_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_api_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_REDIAPI . " WHERE 1 ORDER BY r_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * rediapi_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $upt = array('tm_content' => '{TMPL_REDIAPINLAY_' . $cont_matrix_id . '}', 'tm_pluginfo' => 'Redimero Anbindung');
        $upt = $this->real_escape($upt);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $upt);
    }

    /**
     * rediapi_admin_class::cmd_article_search()
     * 
     * @return void
     */
    function cmd_article_search() {
        $R = $this->get_keypair($_GET['api']);
        $this->ws_config = new ws_clientconfig_class();
        $this->ws_config->set_api_id($R['r_apiid']);
        $this->ws_config->set_api_key($R['r_apikey']);
        $this->ws_config->set_location($R['r_serverurl']);
        $this->client = new ws_client();
        $this->client->connect($this->ws_config);
        $this->REDIAPI['table'] = $this->client->call('get_specified', array('word' => $_GET['q']));
        $this->parse_to_smarty();
        kf::echo_template('rediapi.articles');
    }

}
