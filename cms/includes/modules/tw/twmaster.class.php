<?php

/**
 * @package    tw
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

require (MODULE_ROOT . 'tw/lib/tmhOAuth.php');

class twitter_master_class extends keimeno_class {

    /**
     * twitter_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = new graphic_class();
        $this->config = array(
            'consumer_key' => $this->gbl_config['tw_consumerkey'],
            'consumer_secret' => $this->gbl_config['tw_consumersecret'],
            'token' => $this->gbl_config['tw_authcode'],
            'secret' => $this->gbl_config['tw_oauth_token_secret'],
            'use_ssl' => true);
    }

    /**
     * twitter_master_class::get_user_timeline()
     * 
     * @param mixed $userid
     * @return
     */
    function get_user_timeline($userid) {
        $tmhOAuth = new tmhOAuth($this->config);
        $code = $tmhOAuth->user_request(array('url' => $tmhOAuth->url('1.1/statuses/user_timeline'), 'params' => array('user_id' => $userid)));
        if ($code == 200) {
            $data = json_decode($tmhOAuth->response['response'], true);
            foreach ($data as $key => $row) {
                $data[$key]['twdate'] = date("d.m.Y \- H:i", strtotime($row['created_at']));
                $data[$key]['twcreatetime'] = strtotime($row['created_at']);
                $data[$key]['text'] = $this->hyperlink($data[$key]['text'], true);
            }
            return $data;
        }
    }

    /**
     * twitter_master_class::get_user_info()
     * 
     * @return
     */
    function get_user_info() {
        $tmhOAuth = new tmhOAuth($this->config);
        $code = $tmhOAuth->user_request(array('url' => $tmhOAuth->url('1.1/account/verify_credentials')));
        if ($code == 200) {
            $data = json_decode($tmhOAuth->response['response'], true);
            return $data;
        }
    }

    /**
     * twitter_master_class::post_status_txt()
     * 
     * @param mixed $tmhOAuth
     * @param mixed $txt
     * @return
     */
    function post_status_txt($tmhOAuth, $txt) {
        $txt = substr(strip_tags($txt), 0, 140);
        $code = $tmhOAuth->user_request(array(
            'method' => 'POST',
            'include_entities' => 'true',
            'url' => $tmhOAuth->url('1.1/statuses/update'),
            'params' => array('status' => $txt)));
        return $code;
    }

}

?>