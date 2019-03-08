<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class firewall_class extends keimeno_class {

    var $log = array();


    /**
     * firewall_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->load_settings();

        $this->IP = (empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) : $_SERVER['HTTP_CLIENT_IP']);
        $this->REF = (!empty($HTTP_SERVER_VARS['HTTP_REFERER'])) ? $HTTP_SERVER_VARS['HTTP_REFERER'] : ((!empty($HTTP_ENV_VARS['HTTP_REFERER'])) ? $HTTP_ENV_VARS['HTTP_REFERER'] :
            getenv('HTTP_REFERER'));
        $this->USER_AGENT = (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) ? $HTTP_SERVER_VARS['HTTP_USER_AGENT'] : ((!empty($HTTP_ENV_VARS['HTTP_USER_AGENT'])) ? $HTTP_ENV_VARS['HTTP_USER_AGENT'] :
            getenv('HTTP_USER_AGENT'));
        $this->HOST = (!empty($HTTP_SERVER_VARS['HTTP_HOST'])) ? $HTTP_SERVER_VARS['HTTP_HOST'] : ((!empty($HTTP_ENV_VARS['HTTP_HOST'])) ? $HTTP_ENV_VARS['HTTP_HOST'] :
            getenv('HTTP_HOST'));

        $rep = array(
            '/admin/',
            '/tadmin/',
            basename($_SERVER['PHP_SELF']));
        $repd = array(
            '/',
            '/',
            '');
        $this->SYSTEM_PATH = $_SERVER['DOCUMENT_ROOT'] . '/';
        $this->INC_PATH = $this->SYSTEM_PATH . 'includes/';
        $this->TCR = new kcontrol_class($this);
        $this->std_protection();
        $this->proofe();

    }

    /**
     * firewall_class::save_settings()
     * 
     * @param mixed $FORM
     * @return
     */
    function save_settings($FORM) {
        foreach ($FORM AS $id => $row) {
            $row['fw_active'] = (int)$row['fw_active'];
            update_table(TBL_CMS_FIREWALL, 'id', $id, $row);
        }
    }

    /**
     * firewall_class::set_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_options($row) {
        $row['fw_recalls'] = (int)$row['fw_recalls'];
        $row['fw_active'] = (int)$row['fw_active'];
        $row['fw_timespan'] = (int)$row['fw_timespan'];
        return $row;
    }

    /**
     * firewall_class::load_settings()
     * 
     * @return
     */
    function load_settings() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FIREWALL . " WHERE 1 ORDER BY id");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->settings[$row['id']] = $this->set_options($row);
        }
        $this->smarty->assign('FW_SETTINGS', $this->settings);
    }

    /**
     * firewall_class::cmd_a_delip()
     * 
     * @return
     */
    function cmd_a_delip() {
        $this->delete_ip($_GET['ident']);
        $this->ej();
    }

    /**
     * firewall_class::set_fw_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_fw_options($row) {
        $row['date_ger'] = date('d.m.Y H:i:s', $row['fw_time']);
        if (ISADMIN == 1)
            $row['icon_del'] = kf::gen_del_icon($row['id'], false, 'a_delip');
        return $row;
    }

    /**
     * firewall_class::clear_log()
     * 
     * @return
     */
    function clear_log() {
        $this->db->query("DELETE FROM " . TBL_CMS_FIREWALL_LOG);
        #$this->update_htaccess();
    }

    /**
     * firewall_class::cmd_clear()
     * 
     * @return
     */
    function cmd_clear() {
        $this->clean_log_table();
        $this->msg("{LBL_DELETED}");
        $this->TCR->set_just_turn_back();
    }

    /**
     * firewall_class::delete_ip()
     * 
     * @param mixed $id
     * @return
     */
    function delete_ip($id) {
        $this->db->query("DELETE FROM " . TBL_CMS_FIREWALL_LOG . " WHERE id=" . (int)$id);
        #$this->update_htaccess();
    }

    /**
     * firewall_class::delelte_ip_by_ip()
     * 
     * @param mixed $ip
     * @return
     */
    function delelte_ip_by_ip($ip) {
        $this->db->query("DELETE FROM " . TBL_CMS_FIREWALL_LOG . " WHERE fw_ip='" . $ip . "'");
        #$this->update_htaccess();
    }

    /**
     * firewall_class::load_log()
     * 
     * @return
     */
    function load_log() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FIREWALL_LOG . " WHERE 1 ORDER BY fw_time DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->log[$row['id']] = $this->set_fw_options($row);
        }
        $this->smarty->assign('FW_LOG', $this->log);
    }

    /**
     * firewall_class::cmd_load_blacklist()
     * 
     * @return
     */
    function cmd_load_blacklist() {
        $this->load_log();
        kf::echo_template('firewall.blacklist');
    }

    /**
     * firewall_class::stop_compile()
     * 
     * @return
     */
    function stop_compile() {
        if (!defined('ISADMIN')) {
            echo ('<html><head>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
            </head>
            <body>
            <section>
                <div class="container">
                 <div class="row">
                    <div class="col-md-12">
                         <div class="alert alert-danger text-center">
                            Your IP has been blocked.
                         </div>    
                       </div>
                     </div>           
                </div>
            </section>    
            </body>
            </html>');
            $this->hard_exit();
        }
    }

    /**
     * firewall_class::proofe()
     * 
     * @return
     */
    function proofe() {
        if ($this->gbl_config['fw_enable'] == 1) {
            $BLOCK = $this->db->query_first("SELECT * FROM " . TBL_CMS_FIREWALL_LOG . " WHERE fw_ip='" . $this->IP . "'");
            if ($BLOCK['id'] > 0) {
                $this->report_hacking('Blocked IP detected.');
                $this->stop_compile();
            }
        }
    }

    /**
     * firewall_class::get_conf()
     * 
     * @param mixed $id
     * @return
     */
    function get_conf($id) {
        return $this->settings[$id];
    }

    /**
     * firewall_class::validate_ip()
     * 
     * @param mixed $ip
     * @return
     */
    function validate_ip($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP) && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return FALSE;
        }
        return true;
    }

    /**
     * firewall_class::add_ip_to_blacklist()
     * 
     * @param mixed $FORM
     * @return
     */
    function add_ip_to_blacklist($FORM) {
        $FORM['fw_ip'] = trim($FORM['fw_ip']);
        if ($this->validate_ip($FORM['fw_ip']) === TRUE) {
            $BLOCKIP = array(
                'fw_id' => 'Blacklist',
                'fw_blacklist' => 1,
                'fw_time' => time());
            $FORM = array_merge($FORM, $BLOCKIP);
            $this->delelte_ip_by_ip($FORM['fw_ip']);
            insert_table(TBL_CMS_FIREWALL_LOG, $FORM);
            #$this->update_htaccess();
            return true;
        }
        return false;
    }

    /**
     * firewall_class::add_ip()
     * 
     * @param mixed $idname
     * @param mixed $counter
     * @return
     */
    function add_ip($idname, $counter) {
        $BLOCKIP = array(
            'fw_ip' => $this->IP,
            'fw_id' => $idname,
            'fw_calls' => $counter,
            'fw_script' => $_SERVER['REQUEST_URI'],
            'fw_time' => time());
        $this->delelte_ip_by_ip($BLOCKIP['fw_ip']);
        insert_table(TBL_CMS_FIREWALL_LOG, $BLOCKIP);
        #$this->update_htaccess();
    }

    /**
     * firewall_class::do_log()
     * 
     * @param mixed $id
     * @return
     */
    function do_log($id) {
        $CONF = $this->get_conf($id);
        if ($CONF['fw_active'] == 1) {
            $counter = 0;
            $now = time();
            $_SESSION['fwlog'][$id][$this->IP][] = $now;
            $time_from = $now - ($CONF['fw_timespan']);
            foreach ($_SESSION['fwlog'][$id][$this->IP] as $key => $ttime) {
                $diff = $ttime - $time_from;
                #	echo $ttime.'-'.$time_from.'='.$diff.';'.$now.'<br>';
                if ($diff > 0) {
                    $counter++;
                }
                else {
                    unset($_SESSION['fwlog'][$id][$this->IP]);
                    break;
                }
            }
            if ($counter >= $CONF['fw_recalls']) {
                $this->add_ip($id, $counter);
                $this->log_and_mail();
                unset($_SESSION['fwlog'][$id][$this->IP]);
            }
            $this->clean_log_table();
        }
    }

    /**
     * firewall_class::clean_log_table()
     * 
     * @return
     */
    function clean_log_table() {
        $this->db->query("DELETE FROM " . TBL_CMS_FIREWALL_LOG . " WHERE fw_blacklist=0 AND fw_time<" . (time() - $this->gbl_config['fw_deltime'] * 60 * 60 * 1000));
        #$this->update_htaccess();
    }

    /**
     * firewall_class::report_hack()
     * 
     * @param mixed $type_info
     * @return
     */
    public static function report_hack($type_info) {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 254) : '';
        $arr = array(
            'FORM[h_type]' => $type_info,
            'FORM[h_domain]' => $_SERVER['HTTP_HOST'],
            'FORM[h_ip]' => self::anonymizing_ip(REAL_IP),
            'FORM[h_url]' => base64_encode($_SERVER['PHP_SELF'] . '###' . $_SERVER['QUERY_STRING'] . '###' . http_build_query($_REQUEST)),
            'cmd' => 'log_hacking',
           # 'FORM_IP[b_iphash]' => md5(REAL_IP . $user_agent),
           # 'FORM_IP[b_ua]' => $user_agent,
           # 'FORM_IP[b_ip]' => self::anonymizing_ip(REAL_IP),
            );
        if (self::get_config_value('fw_aktivate_reporting') == 1) {
            self::curl_get_data('https://www.keimeno.de/report-hack.html', $arr);
        }
    }

    /**
     * firewall_class::report_hacking()
     * 
     * @param mixed $type_info
     * @return
     */
    function report_hacking($type_info) {
        $arr = array(
            'FORM[h_type]' => $type_info,
            'FORM[h_domain]' => $_SERVER['HTTP_HOST'],
            'FORM[h_ip]' => self::anonymizing_ip(REAL_IP),
            'FORM[h_url]' => base64_encode($_SERVER['PHP_SELF'] . '###' . $_SERVER['QUERY_STRING'] . '###' . http_build_query($_REQUEST)),
            );
        $loc = array();
        if (!is_array($_SESSION['FIREWALL_IPLOC'][REAL_IP])) {
            $loc = self::locate_ip_address(REAL_IP);
            $_SESSION['FIREWALL_IPLOC'][REAL_IP] = (array )$loc;
        }
        else {
            $loc = (array )$_SESSION['FIREWALL_IPLOC'][REAL_IP];
        }

        foreach ((array )$loc as $key => $value) {
            $arr['IPLOC[' . $key . ']'] = $value;
        }
        if (self::get_config_value('fw_aktivate_reporting') == 1) {
            self::curl_get_data('https://www.keimeno.de/hack-log.html?cmd=log_hacking', $arr);
        }
    }

    /**
     * firewall_class::log_and_mail()
     * 
     * @param string $text
     * @param string $type_info
     * @return
     */
    function log_and_mail($text = '', $type_info = '') {
        $this->report_hacking($type_info);
        if ($this->gblconfig->fw_sendmail == 1) {
            if (!class_exists('email'))
                include_once ($this->INC_PATH . 'email.class.php');
            unset($msg);
            $msg = new Email($this->gbl_config['adr_service_email'], $this->gbl_config['adr_service_email'], 'IP blocked: ' . $this->IP . ' | ' . $_SERVER['HTTP_HOST']);
            $msg->Cc = "";
            $msg->Bcc = "";
            $msg->TextOnly = TRUE;
            $msg->Content = "
	Sehr geehrte Damen und Herren,
	
	Dies ist eine automatische Email aus Ihrem " . $_SERVER['HTTP_HOST'] . " System. Es wurde ein illegaler Aufruf eines Scriptes registriert. 
	Dieser nicht authorisierte Aufruf wurde vom System erkannt und verhindert. Damit der Verursacher keinen weiteren Schaden mehr anrichten kann, wurde
	die IP Adresse vom System f¸r 24h ausgeschlossen.
	
	" . $type_info . "
	
	IP blocked: " . $this->IP . '
	Domain:' . $this->HOST . '
	Zeit: ' . date('d.m.Y H:i:s', time()) . '
	Referer: ' . $this->REF . '
	User-Agent: ' . $this->USER_AGENT . '
	Script: ' . $_SERVER['PHP_SELF'] . '
	' . $text;
            return $msg->Send();
        }
        return true;
    }

    /**
     * firewall_class::locate_ip_address()
     * 
     * @param mixed $ip
     * @return
     */
    public static function locate_ip_address($ip) {
        if (self::get_config_value('fw_apikey_infodb') == "") {
            return false;
        }
        return json_decode(self::curl_get_data("https://api.ipinfodb.com/v3/ip-city/?key=" . self::get_config_value('fw_apikey_infodb') . "&ip=" . $ip . "&format=json"), true);
    }


    /**
     * firewall_class::locate_ip_adress()
     * 
     * @param mixed $ip
     * @return
     */
    function locate_ip_adress($ip) {
        /*stdClass Object
        (
        [statusCode] => OK
        [statusMessage] => 
        [ipAddress] => 88.68.143.115
        [countryCode] => DE
        [countryName] => GERMANY
        [regionName] => HESSEN
        [cityName] => ESCHBORN
        [zipCode] => 65760
        [latitude] => 50.1433
        [longitude] => 8.57111
        [timeZone] => +02:00
        )
        */

        $ip_info = $this->locate_ip_address($ip);
        if ($ip_info !== false) {
            $this->loacte_str = "";
            if ($ip_info['cityName'] != "") {
                $this->loacte_str = $ip_info['zipCode'] . ',' . $ip_info['cityName'] . ',' . $ip_info['countryName'] . ',' . $ip_info['countryCode'];
            }
            $this->maps = $ip . '<iframe marginwidth="0" marginheight="0" src="https://www.trebaxa.com/gmgen.php?height=400&zoom=9&address=' . $this->loacte_str .
                '" frame  scrolling="no" style="height:400px;border:0px;"></iframe>';
            $this->GMAPSIP = array(
                'loacte_str' => $this->loacte_str,
                'iframe' => $this->maps,
                'iframe_url' => 'https://www.trebaxa.com/gmgen.php?zoom=9&width=100%&height=400&address=' . urlencode($this->loacte_str));
        }
        else {
            $this->GMAPSIP = array(
                'loacte_str' => "",
                'iframe' => '<p class="text-info">Please configure API Key of <a href="http://www.ipinfodb.com" target="_blank">http://www.ipinfodb.com</a></p>',
                'iframe_url' => '');
        }
        $this->smarty->assign('GMPASIP', $this->GMAPSIP);
    }

    /**
     * firewall_class::cmd_ax_location()
     * 
     * @return
     */
    function cmd_ax_location() {
        $this->locate_ip_adress($_POST['ip']);
        ECHO $this->GMAPSIP['iframe'];
        die;
    }

    // POST && GET Validation
    /**
     * firewall_class::secureFilter()
     * 
     * @param mixed $input
     * @param mixed $is_post_var
     * @return
     */
    function secureFilter($input, $is_post_var) {
        // Wenn es wahrscheinlich ein Textarea ist, dann Kontrolle ausschlieﬂen
        if ((strstr($input, "\n")) && $is_post_var === TRUE)
            return TRUE;
        $hack_arr = array(
            "SELECT * ",
            "CREATE TABLE ",
            "DELETE FROM",
            "MODIFY ",
            "TRUNCATE ",
            "DROP TABLE ",
            "ALTER TABLE ",
            "UNION SELECT");
        foreach ($hack_arr as $hack_str) {
            $hack_arr2[] = str_replace(' ', "%20", $hack_str);
        }
        $hack_arr = array_merge($hack_arr, $hack_arr2);
        foreach ($hack_arr as $hack_str) {
            if (stristr($input, $hack_str)) {
                return false;
                break;
            }
        }
        return TRUE;
    }


    /**
     * firewall_class::worm_protection()
     * 
     * @return
     */
    function worm_protection() {
        // WORM PROTECTOR
        # WICHTIG! Zummenfuehrung Suchanfragen. Request enth‰lt nicht die ajax POST Vars
        $cracktrack = self::get_query_string();
        $wormprotector = array(
            '–',
            '—',
            'µ',
            '∞',
            'wmsite.ru',
            "%20'1'=",
            'CONCACT',
            'ftp://',
            '.system',
            'HTTP_PHP',
            '&aim',
            'getenv%20',
            'new_password',
            '&icq',
            '/etc/password',
            '/etc/shadow',
            '/etc/groups',
            '/etc/gshadow',
            'HTTP_USER_AGENT',
            'HTTP_HOST',
            '/bin/ps',
            'wget%20',
            'uname\x20-a',
            '/usr/bin/id',
            '/bin/echo',
            '/bin/kill',
            '/bin/',
            '/chgrp',
            '/chown',
            '/usr/bin',
            'g\+\+',
            'bin/python',
            'bin/tclsh',
            'bin/nasm',
            'perl%20',
            'traceroute%20',
            'ping%20',
            '.pl ',
            '.pl%20',
            '/usr/X11R6/bin/xterm',
            'lsof%20',
            '/bin/mail',
            '.conf',
            'motd%20',
            'HTTP/1.',
            'config.php',
            #'cgi-',
            '.eml',
            'file\://',
            'window.open',
            '<SCRIPT>',
            'javascript\://',
            'img src',
            'img%20src',
            'ftp.exe',
            'xp_enumdsn',
            'xp_availablemedia',
            'xp_filelist',
            'xp_cmdshell',
            'nc.exe',
            '.htpasswd',
            #'servlet',
            '/etc/passwd',
            'wwwacl',
            '~root',
            '~ftp',
            '.history',
            'bash_history',
            '.bash_history',
            '~nobody',
            'server-info',
            'server-status',
            'reboot%20',
            'halt%20',
            'powerdown%20',
            '/home/ftp',
            '/home/www',
            'secure_site, ok',
            'chunked',
            'org.apache',
            '/servlet/con',
            '<script',
            '/robot.txt',
            '/perl',
            'mod_gzip_status',
            'db_mysql.inc',
            '+union+all+select+0x',
            '.inc.',
            '_default:k.htmlSerialize',
            '.done(function(a)',
            'k.htmlSerialize',
            'GRABBER_SQL_INJECTION',
            'GRABBER_SQL_STATEMENT',
            'getComputedStyle=1%27',
            '+SELECT+ALL+FROM+WHERE',
            'isDefaultPrevented=%22OR',
            'pageXOffset=%22OR+',
            'cookie_text=1%27+OR',
            'webkitMatchesSelector=OR+',
            'page=+');
        $checkworm = str_ireplace($wormprotector, '*', $cracktrack);
        if ($cracktrack != $checkworm) {
            if ($this->LOGCLASS) {
                $this->LOGCLASS->addLog('WORM_PROTECTOR', 'Hacking blocked');
            }
            $FORM = array('fw_ip' => $this->IP);
            $this->add_ip_to_blacklist($FORM);
            $this->log_and_mail('Hacking blocked [WORM]: ' . $cracktrack . "\nHacked: " . $checkworm, 'Illegaler Aufruf Type: WORM');
            $this->stop_compile();
        }
    }

    /**
     * firewall_class::get_query_string()
     * 
     * @return
     */
    public static function get_query_string() {
        return $_SERVER['QUERY_STRING'] . '&' . ((!defined('ISADMIN')) ? http_build_query($_POST) . '&' : '') . http_build_query($_GET);
    }

    /**
     * firewall_class::detect_injection()
     * 
     * @return void
     */
    function detect_injection() {
        /*SQL ERRORS: 	index.php	main	simple_search	/index.php?page=main&aktion=simple_search&cmd=simple_search&setvalue=molo&sort_col=pname+or+1=(%2f**%2fsElEcT+1+%2f**%2ffRoM(%2f**%2fsElEcT+count(*),%2f**%2fcOnCaT((%2f**%2fsElEcT(%2f**%2fsElEcT(%2f**%2fsElEcT+%2f**%2fdIsTiNcT+%2f**%2fcOnCaT(0x217e21,%2f**%2fcOlUmN_NaMe,0x217e21)+%2f**%2ffRoM+information_schema.%2f**%2fcOlUmNs+%2f**%2fwHeRe+%2f**%2ftAbLe_sChEmA=0x6462325f3535+and+%2f**%2ftAbLe_nAmE=0x31343233395f7473705f70726f64756b7465+%2f**%2flImIt+96,1))+%2f**%2ffRoM+information_schema.%2f**%2ftAbLeS+%2f**%2flImIt+0,1),floor(rand(0)*2))x+%2f**%2ffRoM+information_schema.%2f**%2ftAbLeS+%2f**%2fgRoUp%2f**%2fbY+x)a)+and+1=1&start=195&tpl=	Duplicate entry '!~!car_enginenr!~!1' for key 'group_key'*/
        $cracktrack = self::get_query_string();
        $wormprotector = array(
            '%2fdistinct',
            '%2fconcat',
            '%2fcolumn',
            '%2flimit',
            '%2ftable_name',
            '%2ffrom',
            "OR '1'='1'",
            "'+%2f",
            "+and+'0'='0",
            '%2funion',
            '%2fselect',
            ',0x393133353134353632392e39',
            'OR 1=1',
            'information_schema');
        $checkworm = str_ireplace($wormprotector, '*', $cracktrack);
        if ($cracktrack != $checkworm) {
            if ($this->LOGCLASS) {
                $this->LOGCLASS->addLog('DETECT_INJECT', 'SQL Injection blocked');
            }
            $FORM = array('fw_ip' => $this->IP);
            $this->add_ip_to_blacklist($FORM);
            $this->log_and_mail('Hacking blocked [SQLINJECTION]: ' . $cracktrack . "\nHacked: " . $checkworm, 'Illegaler Aufruf Type: SQLINJECTION');
            $this->stop_compile();
        }
    }

    /**
     * firewall_class::std_protection()
     * 
     * @return
     */
    function std_protection() {
        $this->worm_protection();
        $this->detect_injection();
        $this->do_log('general');
    }

    /**
     * firewall_class::update_htaccess()
     * 
     * @return
     */
    function update_htaccess() {
        return;
        if ($this->gbl_config['fw_htaccess'] == 1) {
            $this->load_log();
            $block_text = "\n\n#IP_BLOCK_BEGIN\norder deny,allow\n";
            foreach ($this->log as $id => $row) {
                if (!strstr($row['fw_ip'], ':'))
                    $block_text .= "deny from " . $row['fw_ip'] . "\n";
            }
            $block_text .= "allow from " . $this->IP . "\n#last change: " . date('Y-m-d H:i:s') . "\n#IP_BLOCK_END\n";

            $hta = file_get_contents($this->SYSTEM_PATH . '.htaccess');
            if (strstr($hta, "#IP_BLOCK_BEGIN")) {
                preg_match_all("=#IP_BLOCK_BEGIN(.*)#IP_BLOCK_END=siU", $hta, $treffer);
                foreach ($treffer[0] as $key => $wert)
                    $hta = str_replace($treffer[0][$key], "", $hta);
            }
            $hta .= $block_text;
            $hta = str_replace("\n\n\n", "\n", $hta);
            file_put_contents($this->SYSTEM_PATH . '.htaccess', $hta);
        }
    }

    /**
     * firewall_class::cmd_save()
     * 
     * @return
     */
    function cmd_save() {
        $this->save_settings($_POST['FORM']);
        $this->ej();
    }

    /**
     * firewall_class::cmd_addip()
     * 
     * @return
     */
    function cmd_addip() {
        $res = $this->add_ip_to_blacklist($_POST['FORM']);
        if ($res === TRUE) {
            $this->ej('fwrealodbl');
        }
        else {
            $this->msge("Invalid IP");
            $this->ej();
        }
    }


}
