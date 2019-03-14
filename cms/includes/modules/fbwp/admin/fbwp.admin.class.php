<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('IN_SIDE') or die('Access denied.');


class fbwpadmin_class extends fbwp_master_class {

    var $FBWP = array();

    /**
     * fbwpadmin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * fbwpadmin_class::cmd_delpage()
     * 
     * @return void
     */
    function cmd_delpage() {
        list($tmp, $id) = explode('-', $_GET['id']);
        $this->db->query("DELETE FROM " . TBL_CMS_FBWPCONTENT . " WHERE id<>1 AND id=" . $id);
        $this->hard_exit();
    }

    /**
     * fbwpadmin_class::load_wp()
     * 
     * @param mixed $id
     * @return void
     */
    function load_wp($id) {
        // https://developers.facebook.com/docs/php/FacebookRedirectLoginHelper/5.0.0
        $this->FBWP['WP'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE id=" . (int)$id);
        $this->FBWP['WP']['delicon'] = kf::gen_del_icon_ajax($id, true, 'delpage');
        if ($this->FBWP['WP']['fb_appid'] != "" && $this->FBWP['WP']['fb_secret'] != "") {
            $this->facebook = new Facebook\Facebook(array(
                'app_id' => trim($this->FBWP['WP']['fb_appid']),
                'app_secret' => trim($this->FBWP['WP']['fb_secret']),
                'default_graph_version' => DEFAULT_GRAPH_VERSION));

            $helper = $this->facebook->getRedirectLoginHelper();
            
            $permissions = ['email', 'public_profile']; // optional
            $redirect_url = self::get_redirect_url($id);
            #&sid_id=' . session_id()
            $this->FBWP['loginUrl'] = $helper->getLoginUrl($redirect_url, $permissions);
            $this->FBWP['redirect_url'] = $redirect_url;

        }
    }


    /**
     * fbwpadmin_class::cmd_getpermatoken()
     * 
     * @return void
     */
    function cmd_getpermatoken() {
        # https://ericplayground.com/2015/06/22/how-to-create-a-permanent-facebook-page-access-token/
        # https://developers.facebook.com/tools/debug/accesstoken
        $fbwpid = (int)$_GET['fbwpid'];
        $this->FBWP['WP'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE id=" . $fbwpid);
        $url_long_live = "https://graph.facebook.com/v2.2/oauth/access_token?grant_type=fb_exchange_token&client_id=" . $this->FBWP['WP']['fb_appid'] .
            "&client_secret=" . $this->FBWP['WP']['fb_secret'] . "&fb_exchange_token=" . $this->FBWP['WP']['fb_token'];
        #$long_live_token = str_replace('access_token=', '', self::curl_exec_script($url_long_live));
        #if (strpos($long_live_token, '&') > 0)
        #    $long_live_token = substr($long_live_token, 0, strpos($long_live_token, '&'));
        $long_live_token_json = self::curl_exec_script($url_long_live);
        $long_live_token_arr = json_decode($long_live_token_json, true);
        $long_live_token = $long_live_token_arr['access_token'];
        $json_me_obj = json_decode(self::curl_exec_script('https://graph.facebook.com/v2.2/me?access_token=' . $long_live_token));

        if ($json_me_obj->id != "") {
            $permanet = self::curl_exec_script('https://graph.facebook.com/v2.2/' . $json_me_obj->id . '/accounts?access_token=' . $long_live_token);

            $permanet = json_decode($permanet, true);
            if (isset($permanet['data'])) {
                foreach ($permanet['data'] as $key => $app) {
                    if ($app['id'] == $this->gbl_config['fbwp_pageid']) {
                        $FORM = array('fb_token' => (string )$app['access_token']);
                        update_table(TBL_CMS_FBWPCONTENT, 'id', $fbwpid, $FORM);
                        echo '<div class="alert alert-success">Erfolgreich</div>';
                    }
                }
            }
            else {
                echo '<div class="alert alert-warning">Already permanent</div>';
            }
        }
        else {
            echo '<div class="alert alert-danger">LongLive Token:' . $long_live_token;
            echoarr($json_me_obj);
            echo '</div>';
        }
        $this->hard_exit();
    }

    /**
     * fbwpadmin_class::init()
     * 
     * @return void
     */
    function init() {
        $CONFIG_OBJ = new config_class();
        $id = ($_REQUEST['id'] > 0) ? (int)$_REQUEST['id'] : 1;
        $this->load_wp($id);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE 1 ORDER BY fb_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->FBWP['sites'][] = $row;
        }
        $this->FBWP['CONFTAB'] = $CONFIG_OBJ->buildTable(42, 42);
    }

    /**
     * fbwpadmin_class::cmd_set_token_fb()
     * 
     * @return void
     */
    function cmd_set_token_fb() {
        $accessToken = $this->get_token((int)$_GET['fbwpid']);
        if (isset($accessToken)) {
            // Logged in!
            $FORM = array('fb_token' => (string )$accessToken);
            update_table(TBL_CMS_FBWPCONTENT, 'id', $_GET['fbwpid'], $FORM);
            $this->load_wp($_GET['fbwpid']);
        }
        if (isset($_GET['error_message']) && $_GET['error_message'] != "") {
            $this->msge($_GET['error_message']);
        }
        else {
            $this->msg('done');
        }

    }

    /**
     * fbwpadmin_class::cmd_savewp()
     * 
     * @return void
     */
    function cmd_savewp() {
        update_table(TBL_CMS_FBWPCONTENT, 'id', $_POST['id'], $this->TCR->POST['FORM']);
        $this->hard_exit();
    }

    /**
     * fbwpadmin_class::cmd_save_config()
     * 
     * @return void
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * fbwpadmin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        $this->smarty->assign('FBWP', $this->FBWP);
    }

    /**
     * fbwpadmin_class::cmd_addpage()
     * 
     * @return void
     */
    function cmd_addpage() {
        $FORM = $_POST['FORM'];
        if ($FORM['fb_title'] == "") {
            $this->msge('Titel bitte ausfüllen.');
            $this->echo_json_fb();
        }
        else {
            $id = insert_table(TBL_CMS_FBWPCONTENT, $FORM);
            $this->echo_json_fb('loadpage', $id);
        }
        $this->hard_exit();
    }

    /**
     * fbwpadmin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE layout_group=1 AND modident='fbwp' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * fbwpadmin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_FACEBOOKGROUP_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * fbwpadmin_class::save_homepage_integration_fbrating()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration_fbrating($params) {
        $this->set_ident_to_cm($params['FORM']['tplid'], $params['id'], 'FACEBOOKRATING');
        return $params;
    }

    /**
     * fbwpadmin_class::save_homepage_integration_fbevents()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration_fbevents($params) {
        $this->set_ident_to_cm($params['FORM']['tplid'], $params['id'], 'FACEBOOKEVENTS');
        return $params;
    }

    /**
     * fbwpadmin_class::load_plugin_page_list()
     * 
     * @param mixed $params
     * @return
     */
    function load_plugin_page_list($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " ORDER BY fb_title");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * fbwpadmin_class::cmd_update_group_stream()
     * 
     * @return void
     */
    function cmd_update_group_stream() {
        $this->sync_group($_GET['id']);
        $this->parse_to_smarty();
        kf::echo_template('fbwp.grouplist');
    }


}
