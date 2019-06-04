<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class session_class {

    protected static $cms_root = "";

    /**
     * session_class::__construct()
     * 
     * @return
     */
    function __construct() {
        static::$cms_root = str_replace('/includes', '', dirname(__FILE__) . '/');
    }

    /**
     * session_class::is_crawler()
     * 
     * @return boolean
     */
    public static function is_crawler() {
        $user_is_crawler = false;
        $crawlers = 'Bloglines subscriber|Dumbot|Sosoimagespider|QihooBot|FAST-WebCrawler|Superdownloads Spiderman|LinkWalker|msnbot|ASPSeek|WebAlta Crawler|Lycos|FeedFetcher-Google|Yahoo|YoudaoBot|AdsBot-Google|Googlebot|Scooter|Gigabot|Charlotte|eStyle|AcioRobot|GeonaBot|msnbot-media|Baidu|CocoCrawler|Google|Charlotte t|Yahoo! Slurp China|Sogou web spider|YodaoBot|MSRBOT|AbachoBOT|Sogou head spider|AltaVista|IDBot|Sosospider|Yahoo! Slurp|Java VM|DotBot|LiteFinder|Yeti|Rambler|Scrubby|Baiduspider|accoona';
        if (preg_match("/$crawlers/", $_SERVER['HTTP_USER_AGENT']) > 0) {
            $user_is_crawler = true;
        }
        return $user_is_crawler;
    }

    /**
     * session_class::prevent_hijacking()
     * 
     * @return
     */
    protected static function prevent_hijacking() {
        # if (!isset($_SESSION['ip_address']) || !isset($_SESSION['user_agent']))
        if (!isset($_SESSION['user_agent'])) {
            return false;
        }

        # if ($_SESSION['ip_address'] != $_SERVER['REMOTE_ADDR'])
        #    return false;

        if ($_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
            return false;
        }
        return true;
    }

    /**
     * session_class::init_session()
     * 
     * @return void
     */
    public static function init_session() {
        # $_SESSION = array('ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']);
        $_SESSION = array('user_agent' => $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * session_class::clear_session()
     * 
     * @return void
     */
    public static function clear_session() {
        $_SESSION = array();
        session_write_close();
        @session_destroy();
        session_regenerate_id(true);
        self::set_session_and_start();
        self::init_session();
    }

    /**
     * session_class::set_session_and_start()
     * 
     * @return void
     */
    public static function set_session_and_start() {
        $lifetime = 60 * 60 * 8;
        $opt = array(
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true);
        session_set_cookie_params($opt);
        @session_start();
    }

    /**
     * session_class::start_session()
     * 
     */
    public static function start_session() {
        static::$cms_root = str_replace('/includes', '', dirname(__FILE__) . '/');
        $SESSION_NAME = "";
        if (self::is_crawler() !== false) {
            $SESSION_NAME = 'crawler';
        }

        if (defined('ISADMIN') == true && ISADMIN == 1) {
            $SESSION_NAME .= 'admin';
        }

        if (session_class::is_crawler() == true && file_exists(static::$cms_root . 'cache/crawler_sid.txt')) {
            $crawler_sid = trim(file_get_contents(static::$cms_root . 'cache/crawler_sid.txt'));
            session_id($crawler_sid);
        }

        if ($SESSION_NAME != "") {
            session_name($SESSION_NAME);
        }


        self::set_session_and_start();

        if (!self::prevent_hijacking()) {
            self::init_session();
        }

        if (session_class::is_crawler() === true) {
            file_put_contents(static::$cms_root . 'cache/crawler_sid.txt', session_id());
        }

        @ini_set('session.referer_check', '');
        #@ini_set("session.use_cookies", "0");
        #echo '<pre>'.print_r(session_get_cookie_params(),true);die;
    }
}
