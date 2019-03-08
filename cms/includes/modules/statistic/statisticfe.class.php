<?php

# Scripting by Trebaxa Company(R) 2012   					*

/**
 * @package    statistic
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


if (IN_SIDE != 1) {
    header('location:/index.html');
    exit;
}

include (CMS_ROOT . 'includes/modules/statistic/crawlerdetect.class.php');

class stat_class extends keimeno_class {
    var $SPIDERS = array();
    var $no_null = false;
    var $who_is_online = array();
    var $CrawlerDetect = null;

    /**
     * stat_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        #http://www.robotstxt.org/db.html
        parent::__construct();
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->CrawlerDetect = new CrawlerDetect();
        $this->SPIDERS = CrawlerDetect::get_crawlers();


    }

    /**
     * stat_class::insert_table()
     * 
     * @param mixed $table
     * @param mixed $FORM
     * @return
     */
    function insert_table($table, $FORM) {
        $sqlquery = "";
        if (is_array($FORM) > 0) {
            foreach ($FORM as $key => $wert) {
                if ($sqlquery)
                    $sqlquery .= ', ';
                $sqlquery .= "$key='$wert'";
            }
            $sql = "INSERT INTO " . $table . " SET " . $sqlquery;
            if ($sqlquery)
                $this->db->query($sql);
        }
    }

    /**
     * stat_class::get_data_count()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param string $where
     * @return
     */
    function get_data_count($table, $column, $where = '1') {
        $result = $this->db->query("SELECT COUNT($column) FROM $table WHERE $where");
        while ($row = $this->db->fetch_array($result)) {
            Return $row[0];
        }
    }

    /**
     * stat_class::saveStat()
     * 
     * @param mixed $addarr
     * @return
     */
    function saveStat($addarr = array()) {
        # TOTAL HITS
        $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value=config_value+1 WHERE config_name='gesamthits'");

        # REFERER LOG
        if (isset($_SERVER['HTTP_REFERER'])) {
            $ref = parse_url($_SERVER['HTTP_REFERER']);
            if (strpos($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]) == 0) {
                $rep = array(
                    'www',
                    'http',
                    ':',
                    '//');
                $ref['host'] = str_replace($rep, "", $ref['host']);
                if ($this->get_data_count(TBL_CMS_REFLOG, 'user_domain', "referer='" . $this->db->real_escape_string($_SERVER['HTTP_REFERER']) . "'") == 0) {
                    $FORM = array(
                        'referer' => $this->db->real_escape_string($_SERVER['HTTP_REFERER']),
                        'user_domain' => $ref['host'],
                        'user_count' => 1,
                        'lasthit' => time());
                    insert_table(TBL_CMS_REFLOG, $FORM);
                }
                else {
                    $this->db->query("UPDATE " . TBL_CMS_REFLOG . " SET lasthit='" . time() . "',user_count=user_count+1 WHERE user_domain='" . $this->db->real_escape_string($_SERVER['HTTP_REFERER']) .
                        "'");
                }
            }
            $this->db->query("DELETE FROM " . TBL_CMS_REFLOG . " WHERE (referer='') OR (user_count<=3 AND lasthit<'" . (time() - (60 * 60 * 24 * 30)) . "')");
        }


        # ONLINE STATUS
        $this->db->query("DELETE FROM " . TBL_CMS_NOWON . " WHERE ip='" . getenv('REMOTE_ADDR') . "' 
            OR zeit<'" . (time() - (60 * $this->gbl_config['nowon_time'])) . "'
            " . (($addarr['username'] != "") ? " OR username='" . $addarr['username'] . "'" : "") . "
        ");
        $CLIENT['zeit'] = time();
        $CLIENT['ip'] = getenv('REMOTE_ADDR');
        $CLIENT['browser'] = $_SERVER['HTTP_USER_AGENT'];
        $CLIENT['akt_page'] = $_SERVER['REQUEST_URI'];
        if (count($addarr) > 0) {
            foreach ($addarr as $key => $value) {
                $CLIENT[$key] = $value;
            }
        }
        foreach ($CLIENT as $key => $wert)
            $CLIENT[$key] = $this->db->real_escape_string($CLIENT[$key]);
        $this->insert_table(TBL_CMS_NOWON, $CLIENT);


        // SPIDERCHECK
        $is_spider = $this->CrawlerDetect->isCrawler($_SERVER['HTTP_USER_AGENT']);
        if ($is_spider == true) {
            $BOTNAME = $this->db->real_escape_string(self::gen_plain_text($this->CrawlerDetect->getMatches()));
            if ($this->db->query_first("SELECT id FROM " . TBL_CMS_SPIDER . " WHERE searchengine='" . $BOTNAME . "'")) {
                $this->db->query("UPDATE " . TBL_CMS_SPIDER . " SET anzahl=anzahl+1, lasthit='" . time() . "' WHERE searchengine='" . $BOTNAME . "'");
            }
            else {
                $this->db->query("INSERT INTO " . TBL_CMS_SPIDER . " SET searchengine='" . $BOTNAME . "', anzahl='1', lasthit='" . time() . "'");
            }
        }


        // Visitor Counter
        $ident = md5(session_id()); #md5(getenv('REMOTE_ADDR') . $_SERVER['HTTP_USER_AGENT']);
        $min = 0;
        $min = date('i') <= 15 ? 1 : $min;
        $min = (date('i') <= 30 && date('i') > 15) ? 2 : $min;
        $min = (date('i') <= 45 && date('i') > 30) ? 3 : $min;
        $min = (date('i') <= 59 && date('i') > 45) ? 4 : $min;
        if (!isset($_SESSION['visitor_log'][date('Y-m-d')][$ident]['viewcounter'])) {
            $_SESSION['visitor_log'][date('Y-m-d')]=array();
            $_SESSION['visitor_log'][date('Y-m-d')][$ident]['viewcounter'] = 0;
        }
        $_SESSION['visitor_log'][date('Y-m-d')][$ident]['viewcounter']++;
        if ((!isset($_SESSION['visitor_log']['log'][$ident]) || !isset($_SESSION['visitor_log']['log'][$ident][date('ymdH') . $min])) && $is_spider == false && !
            defined('ISADMIN') && $_SESSION['visitor_log'][date('Y-m-d')][$ident]['viewcounter'] <= 50) {
            $_SESSION['visitor_log']['log'][$ident][date('ymdH') . $min] = $ident;
            $this->db->query("LOCK TABLES " . TBL_CMS_VISITORS . " WRITE");
            if (get_data_count(TBL_CMS_VISITORS, '*', "cs_date='" . date('Y-m-d') . "'") == 0) {
                $arr = array(
                    'cs_hits' => 1,
                    'cs_date' => date('Y-m-d'),
                    'cs_last_client' => $this->db->real_escape_string($_SERVER['HTTP_USER_AGENT']));
                insert_table(TBL_CMS_VISITORS, $arr);
            }
            else {
                $this->db->query("UPDATE " . TBL_CMS_VISITORS . " SET cs_hits=cs_hits+1,cs_last_client='" . $this->db->real_escape_string($_SERVER['HTTP_USER_AGENT']) .
                    "' WHERE cs_date='" . date('Y-m-d') . "'");
            }
            $this->db->query("UNLOCK TABLES");
        }

        if (!isset($_SESSION['browser_stat']) || $_SESSION['browser_stat'] == false) {
            # Browser Stat
            $browser = $this->get_client_browser(); #get_browser(null, true);
            $browser = $this->real_escape($browser);
            if ($browser['browser'] != 'Unknown') {
                # Browser
                $SQLB = $this->db->query_first("SELECT id FROM " . TBL_CMS_BROWSERLOG . " WHERE b_browser='" . $browser['browser'] . "' AND b_type='B'");
                $arr = array(
                    'b_browser' => $browser['browser'],
                    'b_result' => serialize($browser),
                    'b_type' => 'B',
                    'b_count' => 1);
                if ((int)$SQLB['id'] == 0) {
                    insert_table(TBL_CMS_BROWSERLOG, $arr);
                }
                else {
                    $this->db->query("UPDATE " . TBL_CMS_BROWSERLOG . " SET b_count=b_count+1 WHERE id=" . $SQLB['id']);
                }

                # Betriebssystem
                $SQLB = $this->db->query_first("SELECT id FROM " . TBL_CMS_BROWSERLOG . " WHERE b_system='" . $browser['platform'] . "' AND b_type='S'");
                $arr = array(
                    'b_system' => $browser['platform'],
                    'b_result' => serialize($browser),
                    'b_type' => 'S',
                    'b_count' => 1);
                if ((int)$SQLB['id'] == 0) {
                    insert_table(TBL_CMS_BROWSERLOG, $arr);
                }
                else {
                    $this->db->query("UPDATE " . TBL_CMS_BROWSERLOG . " SET b_count=b_count+1 WHERE id=" . $SQLB['id']);
                }

                # Browser Version
                $SQLB = $this->db->query_first("SELECT id FROM " . TBL_CMS_BROWSERLOG . " WHERE b_browserv='" . $browser['browser'] . " " . $browser['version'] .
                    "' AND b_type='BV'");
                $arr = array(
                    'b_browserv' => $browser['browser'] . " " . $browser['version'],
                    'b_result' => serialize($browser),
                    'b_type' => 'BV',
                    'b_count' => 1);
                if ((int)$SQLB['id'] == 0) {
                    insert_table(TBL_CMS_BROWSERLOG, $arr);
                }
                else {
                    $this->db->query("UPDATE " . TBL_CMS_BROWSERLOG . " SET b_count=b_count+1 WHERE id=" . $SQLB['id']);
                }

                # Mobile Analyse
                if ($this->is_mobile()) {
                    preg_match_all("=\((.*)\)=siU", $browser['userAgent'], $btn_tag);
                    if ($btn_tag[1][0] != "") {
                        $device = explode(';', $btn_tag[1][0]);
                        $device = trim($device[1]);
                    }

                    $SQLB = $this->db->query_first("SELECT id FROM " . TBL_CMS_BROWSERLOG . " WHERE b_mobilesystem='" . $device . "' AND b_type='MS'");

                    $arr = array(
                        'b_mobilesystem' => $device,
                        'b_result' => serialize($browser),
                        'b_type' => 'MS',
                        'b_count' => 1);
                    if ((int)$SQLB['id'] == 0) {
                        insert_table(TBL_CMS_BROWSERLOG, $arr);
                    }
                    else {
                        $this->db->query("UPDATE " . TBL_CMS_BROWSERLOG . " SET b_count=b_count+1 WHERE id=" . $SQLB['id']);
                    }
                }
            }
            $_SESSION['browser_stat'] = true;
        }

    }

    /**
     * stat_class::is_mobile()
     * 
     * @return
     */
    function is_mobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    /**
     * stat_class::get_client_browser()
     * 
     * @return
     */
    function get_client_browser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
            if (preg_match('/samsung/i', $u_agent)) {
                $platform .= ' Samsung';
            }
            if (preg_match('/android/i', $u_agent)) {
                $platform .= ' Android';
            }
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
            if (preg_match('/NT 6.2/i', $u_agent)) {
                $platform .= ' 8';
            }
            elseif (preg_match('/NT 6.3/i', $u_agent)) {
                $platform .= ' 8.1';
            }
            elseif (preg_match('/NT 6.1/i', $u_agent)) {
                $platform .= ' 7';
            }
            elseif (preg_match('/NT 6.0/i', $u_agent)) {
                $platform .= ' Vista';
            }
            elseif (preg_match('/NT 5.1/i', $u_agent)) {
                $platform .= ' XP';
            }
            elseif (preg_match('/NT 5.0/i', $u_agent)) {
                $platform .= ' 2000';
            }
            if (preg_match('/WOW64/i', $u_agent) || preg_match('/x64/i', $u_agent)) {
                $platform .= ' (x64)';
            }
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array(
            'Version',
            $ub,
            'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            }
            else {
                $version = $matches['version'][1];
            }
        }
        else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'browser' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern);
    }

    /**
     * stat_class::count_who_is_online()
     * 
     * @return
     */
    function count_who_is_online() {
        return count($this->who_is_online);
    }

    /**
     * stat_class::genWhoOnlineTab()
     * 
     * @return
     */
    function genWhoOnlineTab() {
        $this->db->query("DELETE FROM " . TBL_CMS_NOWON . " WHERE zeit<'" . (time() - (60 * $this->gbl_config['nowon_time'])) . "'");
        $this->STATOBJ['now'] = date("H:i:s");
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NOWON . " ORDER BY zeit DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['date'] = date("d.m.Y", $row['zeit']);
            $row['time'] = date("H:i:s", $row['zeit']);
            $row['itsme'] = $row['ip'] == getenv('REMOTE_ADDR');
            $this->who_is_online[] = $row;
        }
    }

    /**
     * stat_class::autorun()
     * 
     * @return
     */
    function autorun() {
        $this->saveStat(array('username' => (isset($this->user_object['username']) ? $this->user_object['username'] : ""), 'realname' => (isset($this->user_object['vorname']) ?
                $this->user_object['vorname'] : "") . ' ' . (isset($this->user_object['nachname']) ? $this->user_object['nachname'] : "")));
        $this->genWhoOnlineTab();
        $this->smarty->assign('whoisonline', array('wiolist' => $this->who_is_online, 'wiocount' => count($this->who_is_online)));
    }


}

?>