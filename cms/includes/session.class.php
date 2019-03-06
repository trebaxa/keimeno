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
     * session_class::start_session()
     * 
     */
    public static function start_session() {
        @ini_set("session.gc_maxlifetime", 3600);
        $SESSION_NAME = "";
        if (self::is_crawler() !== false) {
            $SESSION_NAME = 'crawler';
        }

        if (defined('ISADMIN') == true && ISADMIN == 1) {
            $SESSION_NAME .= 'admin';
        }
        


        if (isset($_REQUEST['sid_id'])) {
            $sid_id = $_REQUEST['sid_id'];
            $clean_sid = preg_replace('/[^A-Za-z0-9,-]/', '', $sid_id);
            if ($sid_id != "" && $_REQUEST['sid_id'] == $clean_sid && session_class::is_crawler() === false) {
                session_id($sid_id);
            }
        }

        if (session_class::is_crawler() == true && file_exists(static::$cms_root . 'cache/crawler_sid.txt')) {
            $crawler_sid = trim(file_get_contents(static::$cms_root . 'cache/crawler_sid.txt'));
            session_id($crawler_sid);
        }

        if ($SESSION_NAME != "") {
            session_name($SESSION_NAME);
        }
        @session_start();
        if (session_class::is_crawler() === true) {
            file_put_contents(static::$cms_root . 'cache/crawler_sid.txt', session_id());
        }

        @ini_set('session.referer_check', '');
        @ini_set("session.use_cookies", "0");

    }


}
