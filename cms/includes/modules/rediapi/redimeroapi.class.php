<?php

# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    rediapi
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


DEFINE('TBL_CMS_REDIAPI', TBL_CMS_PREFIX . 'rediapi');

class rediapi_class extends rediapi_master_class {

    var $REDIAPI = array();

    /**
     * rediapi_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * rediapi_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('REDIAPI', $this->REDIAPI);
    }

    /**
     * rediapi_class::exec_rediapi_request()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function exec_rediapi_request($PLUGIN_OPT = array()) {
        $table = array();
        $R = $this->get_keypair($PLUGIN_OPT['api_id']);
        $this->ws_config = new ws_clientconfig_class();
        $this->ws_config->set_api_id($R['r_apiid']);
        $this->ws_config->set_api_key($R['r_apikey']);
        $this->ws_config->set_location($R['r_serverurl']);
        $this->client = new ws_client();
        $this->client->connect($this->ws_config);
        $table = array();
        if ($PLUGIN_OPT['func_name'] != 'get_specified') {
            $table = $this->client->call($PLUGIN_OPT['func_name'], array());
            foreach ($table as $key => $row) {
                $table[$key] = $this->set_article_opt($row, $PLUGIN_OPT, $R);
            }
        }
        else {
            $PLUGIN_OPT['awelements'] = (array )$PLUGIN_OPT['awelements'];
            if (count($PLUGIN_OPT['awelements']) > 0) {
                $params_cid = $params_pid = array();
                foreach ((array )$PLUGIN_OPT['awelements'] as $key => $row) {
                    if ($row['type'] == 'CAT') {
                        $params_cid['cids'][] = $row['id'];
                    }
                    if ($row['type'] == 'PRO') {
                        $params_pid['pids'][] = $row['id'];
                    }
                }
                $articles = $this->client->call('get_products_by_pids', $params_pid);
                $categories = $this->client->call('get_categories_by_cids', $params_cid);
                foreach ($articles as $key => $row) {
                    foreach ($PLUGIN_OPT['awelements'] as $ele) {
                        if ($ele['type'] == 'PRO' && $ele['id'] == $row['pid']) {
                            $row['order'] = $ele['order'];
                            break;
                        }
                    }
                    $table[] = $this->set_article_opt($row, $PLUGIN_OPT, $R);
                }

                foreach ($categories as $key => $row) {
                    foreach ($PLUGIN_OPT['awelements'] as $ele) {
                        if ($ele['type'] == 'CAT' && $ele['id'] == $row['cid']) {
                            $row['order'] = $ele['order'];
                            break;
                        }
                    }
                    $table[] = $this->set_cat_opt($row, $PLUGIN_OPT, $R);
                }

            }
        }
        $PLUGIN_OPT['sort_type'] = ($PLUGIN_OPT['sort_type'] == "") ? 'SORT_REGULAR' : $PLUGIN_OPT['sort_type'];
        $PLUGIN_OPT['sort'] = ($PLUGIN_OPT['sort'] == "") ? 'SORT_ASC' : $PLUGIN_OPT['sort'];
        $table = self::sort_multi_array($table, $PLUGIN_OPT['column'], constant($PLUGIN_OPT['sort']), constant($PLUGIN_OPT['sort_type']));

        return $table;
    }

    /**
     * rediapi_class::set_cat_opt()
     * 
     * @param mixed $row
     * @param mixed $PLUGIN_OPT
     * @param mixed $api
     * @return
     */
    function set_cat_opt($row, $PLUGIN_OPT, $api) {
        $img_url = str_replace(array('ws/server.php'), '', $api['r_serverurl']);
        $found = false;
        if ($row['foto'] != "") {
            $local_file = CMS_ROOT . 'cache/' . $row['foto'];
            if (!is_file($local_file)) {
                $this->curl_get_data_to_file($img_url . 'pro_bilder/' . $row['foto'], $local_file);
                $found = true;
            }
            else {
                $found = true;
            }
            if ($found == true) {
                $row['thumb'] = thumbit_fe('/cache/' . $row['foto'], $PLUGIN_OPT['thumb_width'], $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type']);
            }
            else {
                $row['thumb'] = thumbit_fe('/images/opt_member_nopic.jpg', $PLUGIN_OPT['thumb_width'], $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type']);
            }
        }
        $row['type'] = 'CAT';
        $row['order'] = (!isset($row['order'])) ? 0 : $row['order'];
        return $row;
    }

    /**
     * rediapi_class::set_article_opt()
     * 
     * @param mixed $row
     * @param mixed $PLUGIN_OPT
     * @param mixed $api
     * @return
     */
    function set_article_opt($row, $PLUGIN_OPT, $api) {
        $img_url = str_replace(array('ws/server.php'), '', $api['r_serverurl']);
        $found = false;
        if ($row['bild'] != "") {
            $local_file = CMS_ROOT . 'cache/' . $row['bild'];
            if (!is_file($local_file)) {
                $this->curl_get_data_to_file($img_url . 'pro_bilder/' . $row['bild'], $local_file);
                $found = true;
            }
            else {
                $found = true;
            }
            if ($found == true) {
                $row['thumb'] = thumbit_fe('/cache/' . $row['bild'], $PLUGIN_OPT['thumb_width'], $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type']);
            }
            else {
                $row['thumb'] = thumbit_fe('/images/opt_member_nopic.jpg', $PLUGIN_OPT['thumb_width'], $PLUGIN_OPT['thumb_height'], $PLUGIN_OPT['thumb_type']);
            }
        }
        $row['vk_eur'] = $this->currency_format($row['vk']);
        $row['type'] = 'PRO';
        $row['order'] = (!isset($row['order'])) ? 0 : $row['order'];
        return $row;
    }

    /**
     * rediapi_class::parse_rediapi_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_rediapi_inlay($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_REDIAPINLAY_')) {
            preg_match_all("={TMPL_REDIAPINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $this->smarty->assign('TMPL_REDIAPINLAY_' . $cont_matrix_id, $this->exec_rediapi_request($PLUGIN_OPT));
                if ($PLUGIN_OPT['tpl_name'] != "") {
                    $html = str_replace($tpl_tag[0][$key], '<% assign var=rediapitable value=$TMPL_REDIAPINLAY_' . $cont_matrix_id . ' %><% include file="' . $PLUGIN_OPT['tpl_name'] .
                        '.tpl" %>', $html);
                }
                else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
            }
        }
        $params['html'] = $html;
        return $params;
    }


}
