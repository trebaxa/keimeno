<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('IN_SIDE') or die('Access denied.');

class main_class extends keimeno_class {

    var $content = "";
    var $MODULE = array();
    var $PAGE = 7;
    var $GBL_LANGID = 1;
    protected $CMD = "";
    protected $is_not_found_page = false;


    /**
     * main_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $CMSDATA;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->CMSDATA = $CMSDATA;
        $this->internactions = array();
    }

    /**
     * main_class::cmd_logout()
     * 
     * @return
     */
    function cmd_logout() {
        member_class::logout();
        self::msg('{MSG_ABGEMELDET}');
        HEADER("Location: " . self::get_domain_url() . "index.html");
        $this->hard_exit();
    }

    /**
     * main_class::cmd_stdlogout()
     * 
     * @return
     */
    function cmd_stdlogout() {
        member_class::logout();
        $this->msg('{MSG_ABGEMELDET}');
        HEADER("Location: " . self::get_domain_url() . "index.html");
        $this->hard_exit();
    }

    /**
     * main_class::cmd_loghack()
     * 
     * @return
     */
    function cmd_loghack() {
        firewall_class::report_hack('HTTP_INJECTION');
    }


    /**
     * main_class::set_user_obj()
     * 
     * @param mixed $user_object
     * @return
     */
    function set_user_obj($user_object) {
        $this->user = $user_object;
    }

    /**
     * main_class::protection_double_user()
     * 
     * @return
     */
    function protection_double_user() {
        if (CU_LOGGEDIN && $this->user['sessionid'] != session_id() && $this->gblconfig->ssl_forcessl == 1) {
            member_class::logout();
            $this->msg('{MSG_DOUBLEUSE}');
            firewall_class::report_hacking('Double use of account');
            HEADER("Location: " . self::get_domain_url() . "index.html");
            $this->hard_exit();
        }
    }

    /**
     * main_class::core_var_protection()
     * 
     * @return
     */
    function core_var_protection() {
        $protected_keys = array(
            'tl',
            'id',
            'clid',
            'gid',
            'id');
        foreach ($protected_keys as $key) {
            if (!empty($_GET[$key])) {
                $_GET[$key] = (int)$_GET[$key];
                $_REQUEST[$key] = (int)$_REQUEST[$key];
            }
            if (!empty($_POST[$key])) {
                $_POST[$key] = (int)$_POST[$key];
                $_REQUEST[$key] = (int)$_REQUEST[$key];
            }
        }
    }

    /**
     * main_class::force_www()
     * 
     * @return
     */
    function force_www() {
        $url = self::get_http_protocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parsedUrl = parse_url($url);
        $host = explode('.', $parsedUrl['host']);
        $subdomains = array_slice($host, 0, count($host) - 2);
        if (!strstr($_SERVER['HTTP_HOST'], 'www.') && count($subdomains) == 0) {
            $url = str_replace(self::get_http_protocol() . '://', self::get_http_protocol() . '://www.', $url);
            $this->redirect_301($url);
            $this->hard_exit();
        }
    }

    /**
     * main_class::referer_log()
     * 
     * @return
     */
    function referer_log() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $ref = parse_url($_SERVER['HTTP_REFERER']);
            if (get_data_count(TBL_CMS_REFLOG, 'user_domain', "user_domain='" . $ref['host'] . "'") == 0 && !strstr($ref['host'], FM_DOMAIN)) {
                $this->db->query("INSERT INTO " . TBL_CMS_REFLOG . " (user_domain,user_count) VALUES ('" . $ref['host'] . "',1)");
            }
            else {
                $this->db->query("UPDATE " . TBL_CMS_REFLOG . " SET user_count=user_count+1 WHERE user_domain='" . $ref['host'] . "'");
            }
        }
    }

    /**
     * main_class::set_metas()
     * 
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @return
     */
    function set_metas($title = "", $description = "", $keywords = "") {
        # used from modules to change metas by mod
        $arr = array();
        if ($title != "") {
            $arr['title'] = htmlspecialchars(ucfirst(trim(strip_tags($title))));
        }
        if ($description != "") {
            $arr['description'] = htmlspecialchars($this->gen_meta_description($description));
        }
        if ($keywords != "") {
            $arr['keywords'] = implode(',', $this->get_part_of_array(explode(',', $keywords), 0, (int)$this->gbl_config['metakey_count']));
        }
        if (count($arr) > 0) {
            $this->META_ARR = array_merge($this->META_ARR, $arr);
            $this->smarty->assign('meta', $this->META_ARR);
        }
    }

    /**
     * main_class::set_page_metas()
     * 
     * @param mixed $template
     * @param mixed $langid
     * @return
     */
    function set_page_metas($template, $langid) {
        $pre_content = "";
        if (trim($template['meta_keywords']) != "") {
            $meta_keywords = $template['meta_keywords'];
        }
        else {
            $pre_content = $template['t_precontent'];
            $meta_keywords = $this->gen_meta_keywords($pre_content);
            $meta_keywords = ($meta_keywords != "") ? $meta_keywords : $this->gbl_config['meta_keywords'];
        }
        if (trim($template['meta_desc']) == "") {
            if ($pre_content == "")
                $pre_content = $template['t_precontent'];
            $meta_description = $this->gen_meta_description($pre_content);
            $meta_description = (trim($meta_description) != "") ? $meta_description : $this->gbl_config['meta_desc'];
        }
        else {
            $meta_description = $template['meta_desc'];
        }

        $meta_title = (trim($template['meta_title']) != "") ? $template['meta_title'] : $template['linkname'];
        $meta_title = ($meta_title != "") ? $meta_title : $this->gbl_config['opt_site_title'];
        $meta_title = ucfirst($meta_title);

        $meta_keywords = implode(',', $this->get_part_of_array(explode(',', $meta_keywords), 0, (int)$this->gbl_config['metakey_count']));
        $meta_description = ((strlen($meta_description) > $this->gbl_config['metadesc_count']) ? substr($meta_description, 0, $this->gbl_config['metadesc_count']) : $meta_description);
        $this->META_ARR = array(
            'owner' => $this->gbl_config['meta_owner'],
            'company' => $this->gbl_config['adr_firma'],
            'copyright' => $this->gbl_config['meta_copyr'],
            'author' => $this->gbl_config['meta_author'],
            'publisher' => $this->gbl_config['meta_publisher'],
            'distribution' => $this->gbl_config['meta_distribution'],
            'domain' => $this->gbl_config['opt_domain'],
            'title' => htmlspecialchars($meta_title),
            'description' => htmlspecialchars($meta_description),
            'keywords' => htmlspecialchars($meta_keywords),
            'contentlang' => ($_SESSION['GBL_LOCAL_ID'] != "") ? $_SESSION['GBL_LOCAL_ID'] : 'de',
            'contributor' => $this->gbl_config['meta_contributor'],
            'uri' => $_SERVER['SCRIPT_URI'],
            'robots' => $this->gbl_config['meta_robots']);
        $this->smarty->assign('meta', $this->META_ARR);
    }

    /**
     * main_class::validate_page_include()
     * 
     * @param mixed $TOPLEVEL_OBJ
     * @return
     */
    function validate_page_include($TOPLEVEL_OBJ) {
        $page = "";
        if (isset($_REQUEST['page']))
            $page = $_REQUEST['page'];

        # loading 404 page
        if ((int)$page == 404 && strpos($_SERVER['REQUEST_URI'], '404.html')) {
            $TP = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE t_htalinklabel='404' AND lang_id=" . $this->GBL_LANGID);
            $page = (int)$TP['tid'];
            $this->is_not_found_page = true;
        }

        if (empty($page))
            $page = START_PAGE;
        # get content by label
        if (!is_numeric($page) && !empty($page)) {
            $page = preg_replace("/[^0-9a-zA-Z_-]/", "", $page);
            $TP = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE t_htalinklabel='" . trim($page) . "' AND lang_id=" . $this->GBL_LANGID);
            $page = (int)$TP['tid'];
        }
        else
            $page = (int)$page;

        # get content by static page index
        if ($page == 0) {
            #$request_query = $_SERVER['REQUEST_URI'];
            $request_query = strtok($_SERVER["REQUEST_URI"], '?');
            $lng_code_is_set = isset($_GET['lngcode']) && $_GET['lngcode'] != "" && strlen($_GET['lngcode']) == 2;
            if ($lng_code_is_set == true) {
                $request_query = str_replace('/' . $_GET['lngcode'] . '/', '/', $request_query);
            }

            $PAGE_INDEX = $this->db->query_first("SELECT * FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_dynamic=0 AND MD5(pi_link)='" . md5($request_query) . "'");
            if (!isset($PAGE_INDEX['pi_ident']) || $PAGE_INDEX['pi_ident'] == "") {
                # get content by dynamic page index
                $arr = explode('/', $request_query);
                array_pop($arr);
                $PAGE_INDEX = $this->db->query_first("SELECT * FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_dynamic=1 AND MD5(pi_link)='" . md5(keimeno_class::add_trailing_slash(implode
                    ('/', $arr), true)) . "'");
            }
            if ($PAGE_INDEX['pi_ident'] != "") {
                $query = unserialize($PAGE_INDEX['pi_query']);
                $_GET['page'] = $_POST['page'] = $page = (int)$PAGE_INDEX['pi_page'];
                foreach ($query as $key => $value) {
                    $_REQUEST[$key] = $_POST[$key] = $_GET[$key] = $value;
                }

            }
        }
        # if nothing could be loaded set 404 page
        if ($page == 0) {
            $this->is_not_found_page = true;
            # add to 404 redirect table
            $url = (!isset($_SERVER['SCRIPT_URI']) || (isset($_SERVER['SCRIPT_URI']) && $_SERVER['SCRIPT_URI'] == "")) ? self::get_http_protocol() . '://' . $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_URI'];
            $pnf_hash = md5($url);
            $PNF = $this->db->query_first("SELECT * FROM " . TBL_CMS_PAGENF . " WHERE pnf_hash='" . $pnf_hash . "'");
            if ($PNF['pnf_uri'] == '' || !isset($PNF['pnf_uri'])) {
                $arr = array(
                    'pnf_page' => $page,
                    'pnf_time' => time(),
                    'pnf_uri' => $url,
                    'pnf_hash' => $pnf_hash,
                    'pnf_user' => $_SERVER['HTTP_USER_AGENT'],
                    );
                if ($url != "")
                    insert_table(TBL_CMS_PAGENF, $arr);
            }
            else {
                $this->db->query("UPDATE " . TBL_CMS_PAGENF . " SET pnf_calls=pnf_calls+1 WHERE pnf_hash='" . $pnf_hash . "'");
                if ($PNF['pnf_url'] != "") {
                    $this->redirect_301($PNF['pnf_url']);
                }
            }

            $TP = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE t_htalinklabel='404' AND lang_id=" . $this->GBL_LANGID);
            $page = (int)$TP['tid'];
        }
        if ($page == 0)
            $page = START_PAGE;
        if (strpos($page, '://') !== false || strpos($page, '../') !== false) {
            $page = START_PAGE;
            firewall_class::report_hacking('Hacking by redirect parameter');
        }
        if ($page == START_PAGE && $TOPLEVEL_OBJ['first_page'] > 0 && $_GET['tl'] > 0)
            $page = $TOPLEVEL_OBJ['first_page'];
        if (isset($_GET['page']))
            $_GET['page'] = $page;
        if (isset($_POST['page']))
            $_POST['page'] = $page;

        return (int)$page;
    }

    /**
     * main_class::force_ssl()
     * 
     * @param mixed $page
     * @return
     */
    function force_ssl($page) {
        if (isset($_REQUEST['ajax']) || isset($_REQUEST['axcall']))
            return;
        if (be_in_ssl_area() === false && self::get_config_value('ssl_forcessl') == 1) {
            header("HTTP/1.1 301 Moved Permanently");
            header('location:' . substr(SSLSERVER, 0, -1) . $_SERVER['REQUEST_URI'], TRUE, 301);
            exit;
        }
    }

    /**
     * main_class::compile_inlays()
     * 
     * @param mixed $DATA
     * @return
     */
    function compile_inlays($DATA) {
        $I = new inlay_class();
        $I->langid = $this->GBL_LANGID;
        $I->DATA = $DATA;
        $this->content = $I->fillin_inlays($this->content);
        unset($I);
    }

    /**
     * main_class::fill_template()
     * 
     * @param mixed $key
     * @param mixed $code
     * @param mixed $temp
     * @return
     */
    function fill_template($key, $code, $temp) {
        $key = str_replace(array("{", "}"), "", $key);
        $key = '{' . $key . '}';
        return str_replace($key, $code, $temp);
    }


    /**
     * main_class::set_security_token()
     * 
     * @return
     */
    function set_security_token() {
        if (!isset($_REQUEST['axcall']) && $this->is_not_found_page == false) {
            $settoken = md5(uniqid(rand(), true));
            $_SESSION['token'] = $settoken;
        }
        $this->smarty->assign('cms_token', $_SESSION['token']);
    }

    /**
     * main_class::set_get_msg()
     * 
     * @return
     */
    function set_get_msg() {
        if (isset($_GET['msge']))
            $this->msge(base64_decode($_GET['msge']));
        if (isset($_GET['msg']))
            $this->msg(base64_decode($_GET['msg']));
    }

    /**
     * main_class::cmd_setid()
     * 
     * @return
     */
    function cmd_setid() {
        $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . intval($_GET['cms_id']) . "' WHERE config_name='cms_id' LIMIT 1");
        $crj_obj = new crj_class();
        $crj_obj->genCMSSetXml();
        echo 'DONE';
        $this->hard_exit();
    }


    /**
     * main_class::cmd_docronjob()
     * 
     * @return
     */
    function cmd_docronjob() {
        $crj_obj = new crj_class();
        $crj_obj->execCronJob();
    }

    /**
     * main_class::global_requests()
     * 
     * @return
     */
    function global_requests() {
        exec_evt('globalrequest', array(), $this);
    }

    /**
     * main_class::set_globalframework()
     * 
     * @param mixed $template
     * @param mixed $page
     * @param mixed $templ
     * @return
     */
    function set_globalframework(&$template, $page, $templ) {
        $templ['use_framework'] = ($templ['use_framework'] == 0) ? 1 : (int)$templ['use_framework'];
        $template['content_spots'] = (array )$template['content_spots'];
        if ((int)$this->gbl_config['opt_cms_offline'] == 1) {
            $gbl_template = get_template(9960, $this->GBL_LANGID);
        }
        else {
            $gbl_template = get_template($templ['use_framework'], $this->GBL_LANGID);
            if ($gbl_template == "")
                $gbl_template = get_template($templ['use_framework'], 1);
        }
        foreach ($template['content_spots'] as $spotid => $content) {
            $gbl_template = $this->fill_template('TMPL_SPOT_' . $spotid, $content, $gbl_template);
        }
        $this->content = $gbl_template;

        unset($template['content_spots']);
        $this->compile_inlays($this->content);
    }

    /**
     * main_class::val_modul_call()
     * 
     * @param mixed $templ
     * @return
     */
    function val_modul_call($templ) {
        if (!array_key_exists($templ['modident'], $this->MODULE) && $templ['modident'] != "") {
            $this->redirect_301(PATH_CMS . 'index.html');
        }
    }

    /**
     * main_class::replaceStandardShortCuts()
     * 
     * @param mixed $html
     * @return
     */
    function replaceStandardShortCuts(&$html) {
        $this->GBL_LANGID = (intval($this->GBL_LANGID) == 0) ? 1 : $this->GBL_LANGID;
        if (strstr($html, '{URL_TPL_')) {
            preg_match_all("={URL_TPL_(.*)}=siU", $html, $pages);
            $result = $this->db->query("SELECT DISTINCT T.*,TC.linkname, TC.t_htalinklabel FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " TC, " .
                TBL_CMS_TOPLEVEL . " TL WHERE (T.tl=TL.id AND TC.tid=T.id AND T.c_type='T') AND ((TC.lang_id=" . $this->GBL_LANGID .
                ") OR (TC.use_all_lang=1)) ORDER BY use_all_lang ASC,TL.id,T.morder");
            while ($row = $this->db->fetch_array_names($result)) {
                $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
                $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['id'];
                if (in_array($row['id'], $pages[1])) {
                    if ($row['url_redirect'] != "") {
                        $page_link = $row['url_redirect'];
                    }
                    else {
                        $page_link = gen_page_link($tid, $url_label, $this->GBL_LANGID);
                        if ($this->gblconfig->ssl_forcessl == 1) {
                            $page_link = 'https://www.' . FM_DOMAIN . $page_link;
                        }
                    }
                    $html = str_replace(content_class::gen_url_template($row['id']), $page_link, $html);
                }
            }
            if (strstr($html, '{URL_TPL_')) {
                # $html = preg_replace("/({URL_TPL_\/?)(\w+)([^>]*})/e", "", $html);
                $html = preg_replace_callback("/({URL_TPL_\/?)(\w+)([^>]*})/", function ($m) {
                    return ""; }
                , $html);
            }

        }
        return $html;
    }

    /**
     * main_class::compile_cfields()
     * 
     * @param mixed $html
     * @return
     */
    function compile_cfields($html) {
        global $GBL_LANGID;
        require_once (CMS_ROOT . 'admin/inc/cfield.class.php');
        $CFIELD = new cfield_class();
        return $CFIELD->replaceJoker($this->user, $GBL_LANGID, $html);
    }

    /**
     * main_class::detect_javascript_files()
     * 
     * @param mixed $html
     * @return
     */
    function detect_javascript_files($html) {
        $js_content = "";
        $js_files = array();
        $active_mods = app_class::load_active_mods_to_array();
        $result = $this->db->query("SELECT * FROM  " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 AND t_java!=''");
        while ($row = $this->db->fetch_array_names($result)) {
            if (isset($active_mods[$row['modident']])) {
                $js_content .= $row['t_java'] . PHP_EOL;
            }
        }
        $params = exec_evt('OnJavaCompile', array('js_content' => $js_content));
        $js_content = $params['js_content'];

        if (!empty($js_content)) {
            $js_content = smarty_compile($js_content);
            $cache_file = CMS_ROOT . 'cache/js_' . $this->GBL_LANGID . '_' . md5($js_content) . '.js';
            if (!file_exists($cache_file)) {
                file_put_contents($cache_file, '<?PHP ' . $js_content . '?><!--keimeno-->');
                $js_content = php_strip_whitespace($cache_file);
                $js_content = str_replace(array('<?PHP ', '?><!--keimeno-->'), "", $js_content);
                $js_content = translate_language($js_content, $this->GBL_LANGID);
                file_put_contents($cache_file, $js_content);
            }
            $js_files = array(PATH_CMS . 'cache/' . basename($cache_file));
            $html = preg_replace("#</body(.*?)>#is", '<script src="' . PATH_CMS . 'cache/' . basename($cache_file) . '"></script></body>', $html);
        }
        #        $this->smarty->assign('js_files', $js_files);
        return $html;
    }

    /**
     * send_content_to_browser()
     * 
     * @param mixed $content
     * @param bool $is_not_found_page
     * @return 
     */
    function send_content_to_browser($content, $is_not_found_page = false) {
        $content = tidy_page($content);
        $content = str_replace('</body>', '<!-- keimeno cms --></body>', $content);
        if ($is_not_found_page == true) {
            header('HTTP/1.0 404 Not Found');
        }
        header("Content-type: text/html; charset=UTF-8");
        header("Cache-Control: max-age=600");
        $params = exec_evt('OnOutput', array('html' => $content, 'langid' => $this->GBL_LANGID));
        $content = $params['html'];
        echo $content;
    }

    /**
     * main_class::output()
     * 
     * @param mixed $sidegenstart
     * @return
     */
    function output($sidegenstart) {
        $this->set_security_token();

        $this->content = translate_language($this->content, $this->GBL_LANGID);
        $this->content = $this->detect_javascript_files($this->content);
        $this->content = $this->compile_cfields($this->content);

        $this->send_content_to_browser($this->replaceStandardShortCuts($this->content), $this->is_not_found_page);

        if (!isset($_SESSION['DEBUG']))
            $_SESSION['DEBUG'] = 0;
        $_SESSION['DEBUG'] = (isset($_GET['debug']) && $_GET['debug'] == 1) ? 1 : intval($_SESSION['DEBUG']);
        $_SESSION['DEBUG'] = (isset($_GET['debug']) && $_GET['debug'] == 0) ? 0 : intval($_SESSION['DEBUG']);
        if ($_SESSION['DEBUG'] == 1) {
            file_put_contents(CMS_ROOT . 'cache/ladezeiten.xls', $this->db->query_hist);
            echo '<div style="position:fixed;bottom:10px;right:0;opacity:0.75;background:#fff;border:1px solid #cecece;padding:10px 30px;z-index:999;text-align:left">
            Memory used: ' . human_file_size(memory_get_usage()) . '<br> 
            Peak memory used: ' . human_file_size(memory_get_peak_usage()) . '<br>                       
            Compiler Time: ' . number_format(get_micro_time() - $sidegenstart, 4, ",", ".") . 'sek<br>
            Page: ' . $_REQUEST['page'] . '<br>
            SQL Coutner: ' . $this->db->query_counter . $_SESSION['DEBUG_ADMIN_CONTENT'] . '</div>';
        }
        $this->db->disconnect();
    }

    /**
     * main_class::set_lang_obj()
     * 
     * @param mixed $GBL_LANGID
     * @return
     */
    function set_lang_obj($GBL_LANGID) {
        $GBL_LANG_OBJ = array();
        foreach ($this->CMSDATA->LANGS as $key => $row) {
            if ($row['id'] == $GBL_LANGID) {
                $GBL_LANG_OBJ = $row;
                $GBL_LANG_OBJ['countrycode'] = ($GBL_LANG_OBJ['local'] == "") ? 'de' : $GBL_LANG_OBJ['local'];
                break;
            }
        }
        $this->smarty->assign('lang_obj', $GBL_LANG_OBJ);
    }

    /**
     * main_class::change_toplevel()
     * 
     * @param mixed $TOPLEVEL_OBJ
     * @param mixed $toplid
     * @param mixed $templ
     * @param mixed $ALL_TOPLEVELS
     * @return
     */
    function change_toplevel(&$TOPLEVEL_OBJ, $toplid, $templ, $ALL_TOPLEVELS) {
        $used_tl_for_page = array();
        if ($toplid <= 0 && PAGEID > 0 && $templ['gbl_template'] == 0) {
            foreach ($ALL_TOPLEVELS as $key => $TL) {
                $tl_pages = explode(';', $TL['trees']);
                if (in_array(PAGEID, $tl_pages)) {
                    $used_tl_for_page[] = $TL['TOPID'];
                }
            }

            if (!in_array($TOPLEVEL_OBJ['TOPID'], $used_tl_for_page) && is_array($used_tl_for_page) && count($used_tl_for_page) > 0) {
                $_SESSION['tl'] = $used_tl_for_page[0];
                $TOPLEVEL_OBJ = $ALL_TOPLEVELS[$used_tl_for_page[0]];
            }
        }
    }

    /**
     * main_class::set_cookie()
     * 
     * @return
     */
    function set_cookie() {
        if (isset($_GET['setcookie']) && $_GET['setcookie'] == 1) {
            member_class::set_login_cookie($this->user);
        }
    }

    /**
     * main_class::load_language_selection()
     * 
     * @return
     */
    function load_language_selection() {
        $arr = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval='1' ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = '/' . $row['local'] . '/';
        }
        $content_flags = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval='1' ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $tpl_content = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $this->PAGE . " AND lang_id=" . $row['id']);
            $url_label = ($tpl_content['t_htalinklabel'] == "") ? $tpl_content['linkname'] : $tpl_content['t_htalinklabel'];
            $tid = ($tpl_content['t_htalinklabel'] != "") ? 0 : $tpl_content['tid'];
            if (be_in_ssl_area() === true) {
                $PATH_CMS_LOCAL = ($this->gbl_config['std_lang_id'] != $row['id']) ? SSL_PATH_SYSTEM . '/' . $row['local'] . '/' : SSL_PATH_SYSTEM . PATH_CMS;
            }
            else {
                $PATH_CMS_LOCAL = ($this->gbl_config['std_lang_id'] != $row['id']) ? '/' . $row['local'] . '/' : PATH_CMS;
            }
            $PATH_LOCAL = rtrim($PATH_CMS_LOCAL, '/');
            #&& (!isset($_GET['lngcode']) || strlen($_GET['lngcode']) == 0)
            if (substr_count($_SERVER['SCRIPT_URI'], '/') > 3) {
                $row['link'] = $PATH_CMS_LOCAL . str_replace($arr, '', $_SERVER['SCRIPT_URL']);
                $row['link'] = str_replace('//', '/', $row['link']);
            }
            else {
                $row['link'] = gen_page_link($tid, $url_label, $row['id'], $row['local']);
            }
            $row['icon'] = SSL_PATH_SYSTEM . IMAGE_PATH . $row['bild'];
            unset($row['langarray']);
            $content_flags[] = $row;
        }

        #   echoarr($content_flags);        die();
        $this->smarty->assign('flags', $content_flags);
    }


    /**
     * main_class::load_topl_and_set_page()
     * 
     * @param mixed $page
     * @return
     */
    function load_topl_and_set_page() {
        global $HTA_CLASS_CMS;
        $tl = 0;
        if (isset($_GET['tl']))
            $tl = (int)$_GET['tl'];
        if ($tl > 1)
            $_SESSION['tl'] = $tl;
        if (!isset($_GET['page']))
            $_GET['page'] = 0;
        if (($tl <= 1 && (intval($_SESSION['tl'] == 0 || $_GET['page'] == START_PAGE))) || $tl == -1 || $_SERVER['SCRIPT_URI'] == self::get_http_protocol() . '://www.' .
            FM_DOMAIN . '/') {
            $_SESSION['tl'] = 1;
        }
        $ALL_TOPLEVELS = $page_toplevel = array();
        $result = $this->db->query("SELECT *,T.id AS TOPID 
	       FROM " . TBL_CMS_TOPLEVEL . " T 
	       LEFT JOIN " . TBL_CMS_TPLCON . " TC ON (TC.tid=T.id AND TC.lang_id=" . $this->GBL_LANGID . ")
	       WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['allowed_pages'] = array();
            if ($row['trees'] != "")
                $row['allowed_pages'] = explode(';', $row['trees']);
            $ALL_TOPLEVELS[$row['TOPID']] = $row;

        }

        $TOPLEVEL_OBJ = $ALL_TOPLEVELS[$_SESSION['tl']];
        //Festlegung des Einstiegspunktes
        $temp_class_obj = new content_class();
        $temp_class_obj->set_entry_point($TOPLEVEL_OBJ);
        unset($temp_class_obj);
        $this->PAGE = $page = $this->validate_page_include($TOPLEVEL_OBJ);

        foreach ($ALL_TOPLEVELS as $topid => $row) {
            if (in_array($page, $row['allowed_pages'])) {
                $page_toplevel[] = $row['TOPID'];
            }
        }
        # Wenn Seite einen anderen Toplevel hat, als aktuell gewählt
        if (!in_array($_SESSION['tl'], $page_toplevel) && count($page_toplevel) > 0) {
            $_SESSION['tl'] = array_pop($page_toplevel);
            $TOPLEVEL_OBJ = $ALL_TOPLEVELS[$_SESSION['tl']];
        }

        $CONTENT_OBJ = new content_class();
        list($this->templatecon, $page, $this->pageobj) = $CONTENT_OBJ->load_frontend_webpage($page, $this->GBL_LANGID, $TOPLEVEL_OBJ);
        $this->val_modul_call($this->pageobj);
        $this->change_toplevel($TOPLEVEL_OBJ, $tl, $this->pageobj, $ALL_TOPLEVELS);

        # Tree
        $this->smarty->assign('globl_tree_template', 'menu_tree.tpl');
        list($all_allowed_page_ids, $allowed_page_ids) = $this->load_cms_tree($TOPLEVEL_OBJ);

        # check if is illegal access by permissions
        if ((int)$page > 0 && !in_array($page, $all_allowed_page_ids)) {
            $this->LOGCLASS->addLog('HACKING', 'Access protected page/file, ' . self::anonymizing_ip(REAL_IP) . ', ' . $_SERVER['REQUEST_URI']);
          #  firewall_class::report_hack('Try access protected file or page');
            self::msge('No public access to page ' . $page . ' Check if page is viewable for public group.');
            $this->redirect_301('/index.html');
        }


        # Metas
        $this->set_page_metas($this->templatecon, $this->GBL_LANGID);
        $this->TCR->interpreterfe();

        # Load Language Selection
        $this->load_language_selection();

        # load Layout files
        include_once (CMS_ROOT . 'admin/inc/layout.class.php');
        $LAY = new layout_class();
        $LAY->load_css_files();
        $this->allocate_memory($LAY);
        $this->add_object($HTA_CLASS_CMS);
        $this->force_ssl(PAGEID);

        $this->set_globalframework($this->templatecon, PAGEID, $this->pageobj);
        $this->global_requests();
        if ($this->pageobj['tl'] > 1)
            $_SESSION['tl'] = $this->pageobj['tl'];
        $TOPLEVELCMS = new toplevelcms_class();
        $TOPLEVELCMS->load_toplevel($TOPLEVEL_OBJ, $page);

        # exec interpreter dynamic
        if (is_array($CONTENT_OBJ->exec_interpreter) && count($CONTENT_OBJ->exec_interpreter) > 0) {
            $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
            foreach ($CONTENT_OBJ->exec_interpreter as $key => $modident) {
                foreach ($xml_modules->modules->children() as $module) {
                    if (strval($module->settings->id) == $modident && isset($module->settings->stdclass)) {
                        $class_name = strval($module->settings->stdclass);
                        $C = new $class_name();
                        if (is_object($C->TCR)) {
                            $C->TCR->interpreterfe();
                        }
                        keimeno_class::allocate_memory($C);
                    }
                }
            }
        }
        keimeno_class::allocate_memory($CONTENT_OBJ);
    }


    /**
     * main_class::load_cms_tree()
     * 
     * @return array
     */
    protected function load_cms_tree($TOPLEVEL_OBJ) {
        $subtrees = $exclude = $allowed_page_ids = $all_allowed_page_ids = array();
        $nodes = new cms_tree_class();
        $nodes->db = $this->db;
        if ($_SESSION['tl'] > 0) {
            $subtrees = explode(";", $TOPLEVEL_OBJ['trees']);
            if ($TOPLEVEL_OBJ['show_parent_level'] != 1)
                $exclude[] = $TOPLEVEL_OBJ['first_page'];
        }

        $result = $this->db->query("SELECT T.id, T.approval, T.c_type FROM " . TBL_CMS_TEMPLATES . " T 
            INNER JOIN " . TBL_CMS_PERMISSIONS . " P ON (P.perm_tid=T.id " . $this->user['sql_groups'] . ") WHERE 1  
            GROUP BY T.id
            ORDER BY T.morder    
        ");
        while ($row = $this->db->fetch_array_names($result)) {
            if (in_array($row['id'], $subtrees) && $row['c_type'] == 'T' && $row['approval'] == 1) {
                $allowed_page_ids[] = $row['id'];
            }
            $all_allowed_page_ids[] = $row['id'];
        }
        $result = $this->db->query("SELECT T.description,T.id, T.parent, TC.linkname,T.url_redirect,T.url_redirect_target,T.t_class,T.t_attributes,TC.t_icon,TC.t_htalinklabel,T.tid_childs,TC.t_themedescription,
             TC.t_imgthemealt,TC.t_imgthemetitle,TC.theme_image
             FROM " . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.tid=T.id AND TC.lang_id=" . $this->GBL_LANGID . ") 
				WHERE T.gbl_template=0 AND T.approval='1' AND T.c_type='T'		
				GROUP BY T.id 
				ORDER BY T.morder");
        $data_menu = array();
        while ($row = $this->db->fetch_array_names($result)) {
            $data_menu[] = $row;
        }

        $nodes->build_core_cms_tree($data_menu, $allowed_page_ids);
        $this->smarty->assign('categorytree', $nodes->menu_array);
        $this->smarty->assign('tree_by_parent', $nodes->parent_flat_arr);
        $this->smarty->assign('categorytreeselected', $nodes->cleanMenuArr);
        $this->smarty->assign('active_node', $nodes->active_node);
        $this->smarty->assign('active_node_parent', $nodes->active_node_parent);
        $this->smarty->assign('exclude_cids', $exclude);
        #echoarr($exclude);
        #echoarr($nodes->active_node_parent);
        #echoarr($data_menu);
        #echoarr($nodes->cleanMenuArr);
        #echoArr($nodes->menu_array);
        #echoArr($exclude);
        return array($all_allowed_page_ids, $allowed_page_ids);
    }

    /**
     * main_class::ajax_content()
     * 
     * @return
     */
    function ajax_content() {
        if ((isset($_REQUEST['aktion']) && $_REQUEST['aktion'] == 'axpageload') || (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'axpageload') || (isset($_REQUEST['ajax']) &&
            $_REQUEST['ajax'] == 1)) {
            header("Content-type: text/html; charset=UTF-8");
            echo translate_language($this->replaceStandardShortCuts($this->templatecon['content']), $this->GBL_LANGID);
            $this->hard_exit();
        }
    }

    /**
     * main_class::ob_end_flush_all()
     * 
     * @return
     */
    protected static function ob_end_flush_all() {
        $level_count = ob_get_level();
        for ($i = 0; $i < $level_count; $i++)
            ob_end_flush();
    }

    /**
     * main_class::run()
     * 
     * @param mixed $GBL_LANGID
     * @param mixed $user_object
     * @param mixed $MODULE
     * @param mixed $page
     * @return
     */
    public function run($GBL_LANGID, $user_object, $MODULE) {
        $this->force_www();
        $this->GBL_LANGID = (int)$GBL_LANGID;
        $this->core_var_protection();
        $this->set_user_obj($user_object);
        $this->referer_log();
        $this->protection_double_user();
        $this->MODULE = $MODULE;
        $this->set_cookie();
        $this->load_topl_and_set_page();
        $_SESSION['last_mod_exec'] = "";
    }

    /**
     * main_class::end()
     * 
     * @param mixed $sidegenstart
     * @return
     */
    public function end($sidegenstart) {
        $this->set_lang_obj($this->GBL_LANGID);
        $this->set_get_msg();
        #  include (CMS_ROOT . 'includes/smarty.inc.php');
        $this->set_smarty_defaults();
        $this->ajax_content();
        $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
        $this->output($sidegenstart);
        self::ob_end_flush_all();
    }


    /**
     * main_class::compile_frontend()
     * 
     * @param mixed $html
     * @param mixed $langid
     * @return void
     */
    public static function compile_frontend($html, $langid) {
        $params = exec_evt('beforesmartycompile', array('html' => $html, 'langid' => $langid));
        $html = smarty_compile($params['html']);
        $params = exec_evt('aftersmartycompile', array('html' => $html, 'langid' => $langid));
        if ($params['html'] != $html) {
            $params['html'] = smarty_compile($params['html']);
        }

        return $params['html'];
    }


    /**
     * main_class::set_smarty_defaults()
     * 
     * @return
     */
    public function set_smarty_defaults() {
        $cmd = "";
        if (isset($_REQUEST['aktion'])) {
            $cmd = ($_REQUEST['aktion'] != "") ? $_REQUEST['aktion'] : $_REQUEST['cmd'];
        }
        if (isset($_REQUEST['cmd'])) {
            $cmd = $_REQUEST['cmd'];
        }

        $this->smarty->assign('aktion', $cmd);
        $this->smarty->assign('cmd', $cmd);
        if (isset($_REQUEST['section'])) {
            $this->smarty->assign('section', trim(substr($_REQUEST['section'], 0, 30)));
        }
        $this->smarty->assign('page', PAGEID);
        if (isset($_GET['id'])) {
            $this->smarty->assign('id', $_GET['id']);
        }

        $this->smarty->assign('randid', rand(0, 100000) . time());
        $this->smarty->assign('SCRIPT_URI', $_SERVER['SCRIPT_URI']);
        $this->smarty->assign('REQUEST_URI', $_SERVER['REQUEST_URI']);
        $this->smarty->assign('GBL_LANGID', $this->GBL_LANGID);
        $this->smarty->assign('lastPage', $_SESSION['lastPage']);
        $this->smarty->assign('GBL_LOCAL_ID', $_SESSION['GBL_LOCAL_ID']);
        $referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "";
        $this->smarty->assign('HTTP_REFERER', $referer);
        $this->smarty->assign('CU_LOGGEDIN', CU_LOGGEDIN);
        $this->smarty->assign('SSLSERVER', SSLSERVER);
        $this->smarty->assign('SERVERVARS', $_SERVER);
        $this->smarty->assign('cmsinfo', '<span class="keimenoident" id="' . sha1($_SERVER['HTTP_HOST']) .
            '"><a title="Keimeno CMS" href="https://www.keimeno.de" target="_blank">Keimeno CMS</a></span>');
        $this->smarty->assign('clid', ((isset($_GET['clid']) && $_GET['clid'] > 0) ? $_GET['clid'] : $this->GBL_LANGID));
        $this->smarty->assign('session_id', session_id());
        $this->smarty->assign('sid_id', session_id());
        $this->smarty->assign('SSL_PATH_SYSTEM', SSL_PATH_SYSTEM);
        $this->smarty->assign('sslactive', be_in_ssl_area());
        $this->smarty->assign('ISSTARTPAGE', ((isset($_GET['page']) && (int)$_GET['page'] == START_PAGE) || (isset($_GET['page']) && (int)$_GET['page'] == 0)));

        if (be_in_ssl_area() === true) {
            $this->smarty->assign('PATH_CMS', SSL_PATH_SYSTEM . PATH_CMS);
            $PATH_CMS_LOCAL = ($this->gbl_config['std_lang_id'] != $this->GBL_LANGID) ? SSL_PATH_SYSTEM . '/' . $_SESSION['GBL_LOCAL_ID'] . '/' : SSL_PATH_SYSTEM . PATH_CMS;
            $this->smarty->assign('PATH_CMS_LOCAL', $PATH_CMS_LOCAL);
            $this->smarty->assign('cms_url', 'https://www.' . $this->gbl_config['opt_domain'] . rtrim(PATH_CMS, '/'));
        }
        else {
            $this->smarty->assign('PATH_CMS', PATH_CMS);
            $PATH_CMS_LOCAL = ($this->gbl_config['std_lang_id'] != $this->GBL_LANGID) ? '/' . $_SESSION['GBL_LOCAL_ID'] . '/' : PATH_CMS;
            $this->smarty->assign('PATH_CMS_LOCAL', $PATH_CMS_LOCAL);
            $this->smarty->assign('cms_url', 'http://www.' . $this->gbl_config['opt_domain'] . rtrim(PATH_CMS, '/'));
        }
        $this->smarty->assign('PHPSELF', SSL_PATH_SYSTEM . rtrim($PATH_CMS_LOCAL, '/') . $_SERVER['PHP_SELF']);
        $this->smarty->assign('eurl', SSL_PATH_SYSTEM . rtrim($PATH_CMS_LOCAL, '/') . $_SERVER['PHP_SELF'] . '?page=' . ((isset($_REQUEST['page'])) ? $_REQUEST['page'] :
            "") . '&');
        if (be_in_ssl_area() === true) {
            $this->smarty->assign('baseurl', SSLSERVER . ((!preg_match("/.*\/$/", SSLSERVER)) ? "/" : ""));
        }
        else {
            $this->smarty->assign('baseurl', self::get_http_protocol() . '://www.' . $this->gbl_config['opt_domain'] . ((!preg_match("/.*\/$/", $this->gbl_config['opt_domain'])) ?
                "/" : ""));
        }
        $this->smarty->assign('THISURL', self::get_http_protocol() . '://www.' . $this->gbl_config['opt_domain'] . PATH_CMS . substr($_SERVER['REQUEST_URI'], 1));
        $this->smarty->assign('language_table', $this->CMSDATA->LANGS);
        $this->smarty->assign('uselang', ((isset($_GET['uselang'])) ? $_GET['uselang'] : ""));
        $this->smarty->assign('GET', $_GET);
        $this->smarty->assign('REQUEST', $_REQUEST);
        $this->smarty->assign('POST', $_POST);
        $this->smarty->assign('subbtn', gen_submit_btn('{LA_SAVE}'));
        $this->smarty->assign('customer', $this->user);
        $this->smarty->assign('err_msgs', $_SESSION['err_msgs']);
        $this->smarty->assign('ok_msgs', $_SESSION['ok_msgs']);
        $_SESSION['ok_msgs'] = array();
        $_SESSION['err_msgs'] = array();
        if ($this->gbl_config['shop_protection'] == 1) {
            $this->smarty->assign('document_protection',
                'onmouseover="return false" onkeypress="return true" ondragstart="return false" onselectstart="return false" oncontextmenu="return false"');
        }
        else {
            $this->smarty->assign('document_protection', '');
        }

        // detect if is mobile client
        $this->smarty->assign('mobiledevice', self::is_mobile_client() == true);
    }
}
