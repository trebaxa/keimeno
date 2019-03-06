<?php

/**
 * @package    tw
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class twitter_admin_class extends twitter_master_class {

    protected $TW = array();

    /**
     * twitter_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = new graphic_class();
    }


    /**
     * twitter_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('TW', $this->TWITTER);
    }

    /**
     * twitter_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * twitter_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        $this->TWITTER['CONFIG'] = $CONFIG_OBJ->buildTable(36, 36);
    }

    /**
     * twitter_admin_class::format_post_txt()
     * 
     * @param mixed $str
     * @param mixed $TWO
     * @return
     */
    function format_post_txt($str, &$TWO) {
        $org_txt = $txt = str_replace('#BR#', "\n", $str);
        $rows = explode("\n", $txt);
        if (is_array($rows)) {
            foreach ($rows as $key => $v)
                $rows[$key] = trim($v);
            $txt = implode("\n", $rows);
        }
        if (strlen($txt) > 140) {
            $txt = substr($txt, 0, 140);
        }
        $TWO['org_txt'] = nl2br($org_txt);
        $TWO['txt'] = $txt;
    }

    /**
     * twitter_admin_class::cmd_tw_connectsss()
     * 
     * @return
     */
    function cmd_tw_connectsss() {
        $connection = new TwitterOAuth($this->gbl_config['tw_consumerkey'], $this->gbl_config['tw_consumersecret']);
        $this->twconnection = $connection;
        $request_token = $this->twconnection->getRequestToken(OAUTH_CALLBACK);
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        $_SESSION['commingfrom'] = $_SERVER['HTTP_REFERER'];
        switch ($this->twconnection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                session_write_close();
                $url = $this->twconnection->getAuthorizeURL($token);
                header('Location: ' . $url);
                exit;
            default:
                /* Show notification if something went wrong. */
                return array(
                    'status' => true,
                    'msg' => '',
                    'msge' => 'Could not connect to Twitter. Refresh the page or try again later. [' . $this->twconnection->http_code . ']');
        }
    }

    /**
     * twitter_admin_class::php_self()
     * 
     * @param bool $dropqs
     * @return
     */
    function php_self($dropqs = true) {
        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $protocol = 'https';
        }
        elseif (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')) {
            $protocol = 'https';
        }

        $url = sprintf('%s://%s%s', $protocol, $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);

        $parts = parse_url($url);

        $port = $_SERVER['SERVER_PORT'];
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = @$parts['path'];
        $qs = @$parts['query'];
        $qs = str_replace('request_token', 'access_token', $qs);
        $port or $port = ($scheme == 'https') ? '443' : '80';

        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        $url = "$scheme://$host$path";
        if (!$dropqs)
            return "{$url}?{$qs}";
        else
            return $url;
    }

    /**
     * twitter_admin_class::cmd_request_token()
     * 
     * @return
     */
    function cmd_request_token() {
        $tmhOAuth = new tmhOAuth($this->config);
        $code = $tmhOAuth->apponly_request(array(
            'without_bearer' => true,
            'method' => 'POST',
            'url' => $tmhOAuth->url('oauth/request_token', ''),
            #'params' => array('oauth_callback' => $this->php_self(false), ),
            'params' => array('oauth_callback' => $this->php_self(false), ),
            ));

        if ($code != 200) {
            die("There was an error communicating with Twitter. {$tmhOAuth->response['response']}");
            return;
        }

        // store the params into the session so they are there when we come back after the redirect
        $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);

        // check the callback has been confirmed
        if ($_SESSION['oauth']['oauth_callback_confirmed'] !== 'true') {
            die('The callback was not confirmed by Twitter so we cannot continue.');
        }
        else {
            $url = $tmhOAuth->url('oauth/authorize', '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
            header('location:' . $url);
            $this->hard_exit();
        }
    }

    /**
     * twitter_admin_class::uri_params()
     * 
     * @return
     */
    function uri_params() {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $params = array();
        foreach (explode('&', $url['query']) as $p) {
            list($k, $v) = explode('=', $p);
            $params[$k] = $v;
        }
        return $params;
    }

    /**
     * twitter_admin_class::cmd_access_token()
     * 
     * @return
     */
    function cmd_access_token() {
        $tmhOAuth = new tmhOAuth($this->config);
        $params = $this->uri_params();
        if ($params['oauth_token'] !== $_SESSION['oauth']['oauth_token']) {
            die('The oauth token you started with doesn\'t match the one you\'ve been redirected with. do you have multiple tabs open?');
            unset($_SESSION['oauth']);
            #session_unset();
            return;
        }

        if (!isset($params['oauth_verifier'])) {
            die('The oauth verifier is missing so we cannot continue. did you deny the appliction access?');
            unset($_SESSION['oauth']);
            #   session_unset();
            return;
        }

        // update with the temporary token and secret
        $tmhOAuth->reconfigure(array_merge($tmhOAuth->config, array(
            'token' => $_SESSION['oauth']['oauth_token'],
            'secret' => $_SESSION['oauth']['oauth_token_secret'],
            )));

        $code = $tmhOAuth->user_request(array(
            'method' => 'POST',
            'url' => $tmhOAuth->url('oauth/access_token', ''),
            'params' => array('oauth_verifier' => trim($params['oauth_verifier']), )));

        if ($code == 200) {
            $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);
            $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $oauth_creds['oauth_token'] . "' WHERE config_name='tw_authcode'");
            $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $oauth_creds['oauth_token_secret'] . "' WHERE config_name='tw_oauth_token_secret'");

        }
        $this->msg('Done');
        header('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&section=start');
        $this->hard_exit();
    }

    /**
     * twitter_admin_class::get_status()
     * 
     * @return
     */
    function get_status() {
        $this->TWITTER['twuser'] = $this->get_user_info();
        $this->TWITTER['timeline'] = $this->get_user_timeline($this->TWITTER['twuser']['id']);
    }

    /**
     * twitter_admin_class::cmd_send_tw_msg()
     * 
     * @return
     */
    function cmd_send_tw_msg() {
        $tmhOAuth = new tmhOAuth($this->config);
        $code = $this->post_status_txt($tmhOAuth, $_POST['FORM']['status']);
        if ($code == 200) {
            $this->msg('Done');
        }
        else {
            $this->msge('Failed: ' . $code);
        }
        $this->echo_json_fb();
    }
}

?>