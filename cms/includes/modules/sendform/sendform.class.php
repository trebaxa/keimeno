<?php


# Scripting by Trebaxa Company(R) 2013    					*

/**
 * @package    sendform
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */



defined( 'IN_SIDE' ) or die( 'Access denied.' );

class sendpost_class extends keimeno_class {

    var $host = '';
    var $referer = '';
    var $data_to_send = 'remoteexexc=1';
    var $langid;

    /**
     * sendpost_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->referer = $_SERVER['HTTP_REFERER'];
        $this->langid = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->data_to_send .= '&rm_langid=' . $this->langid;
    }

    /**
     * sendpost_class::cmd_crossdomain_send()
     * 
     * @return
     */
    function cmd_crossdomain_send() {
        $PAGE = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " T WHERE id=" . (int)$this->TCR->POST['page']);
        $this->host = $PAGE['sf_host'];
        $this->smarty->assign('sendform', $this->PostToHost($_POST['FORM']));
    }

    /**
     * sendpost_class::PostToHost()
     * 
     * @param mixed $FORM
     * @return
     */
    function PostToHost($FORM) {
        $target = $this->host;
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $value) {
                $this->data_to_send .= (($this->data_to_send != "") ? '&' : '') . 'FORM[' . $key . ']' . '=' . $value;
            }
        }
        $URL = parse_url($this->host);
        $this->data_to_send .= (($URL['query'] != "") ? '&' : '') . $URL['query'];

        $this->ch = curl_init($target);
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            # CURLOPT_USERAGENT      => "Trebaxa Poster",     // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_POST => 1, // i am sending post data
            CURLOPT_POSTFIELDS => $this->data_to_send, // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0, // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false, //
            CURLOPT_REFERER,
            $this->referer, //if server needs to think this post came from elsewhere
            CURLOPT_VERBOSE => 1 //
                );
        curl_setopt_array($this->ch, $options);
        $result = curl_exec($this->ch);
        $feeds = array();
        $feeds['REQUEST_OK'] = (strstr($result, '!!REQUEST_OK!!'));
        $feeds['CONTENT'] = str_ireplace('!!REQUEST_OK!!', '', $result);
        return $feeds;
    }


}
