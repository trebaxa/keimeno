<?PHP

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class mainadmin_class extends keimeno_class {

    var $ADMIN = array();
    var $content = "";

    /**
     * mainadmin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * mainadmin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->ADMIN['rules'] = $_SESSION['RULE'];
        $this->ADMIN['rules_json'] = json_encode($_SESSION['RULE']);
        $this->smarty->assign('ADMIN', $this->ADMIN);
    }

    /**
     * mainadmin_class::cmd_get_rules()
     * 
     * @return void
     */
    function cmd_get_rules() {
        echo json_encode($_SESSION['RULE']);
        $this->hard_exit();
    }

    /**
     * mainadmin_class::inc_tpl()
     * 
     * @param mixed $tpl
     * @return
     */
    function inc_tpl($tpl) {
        $this->content .= '<%include file="' . trim(strval($tpl)) . '.tpl"%>';
    }

    /**
     * mainadmin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save((array )$_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * mainadmin_class::set_top_menu()
     * 
     * @param mixed $menu
     * @return
     */
    function set_top_menu($menu) {
        $k = 0;
        if (is_array($menu)) {
            foreach ($menu as $label => $add) {
                $k++;
                $link = $_SERVER['PHP_SELF'] . '?' . $add . (($add != "") ? "&" : "") . (($_GET['epage'] != "") ? '&epage=' . $_GET['epage'] . '&' : '') . 'msid=' . md5($label);
                if (strstr($add, 'redirect=') == TRUE) {
                    $link = './' . str_replace('redirect=', '', $add);
                }
                $active = (isset($_GET['msid']) && $_GET['msid'] == md5($label) || (!isset($_GET['msid']) && $k == 1));
                $top_menu[] = array(
                    'link' => $link,
                    'label' => $label,
                    'active' => $active);
            }
        }
        $this->ADMIN['topmenu'] = $top_menu;
    }

    /**
     * mainadmin_class::validate_lang_call()
     * 
     * @param mixed $uselang
     * @return
     */
    function validate_lang_call($uselang) {
        if (isset($uselang) && (int)$uselang > 0) {
            if (!in_array($uselang, $_SESSION['admin_obj']['lang_id_matrix'])) {
                $this->msge('Illegal language call.');
                header('location: welcome.html');
                exit;
            }
        }

    }

    /**
     * mainadmin_class::set_admin_defaults()
     * 
     * @return
     */
    function set_admin_defaults() {
        $this->session_protect();
        $this->check_ssl();
        $this->check_setlogin();
        $this->check_redirect();
        $this->set_post_get_cmd();
        $this->set_smarty_defaults();
    }

    /**
     * mainadmin_class::session_protect()
     *     
     */
    public static function session_protect() {
        $fingerprint = md5($_SERVER['HTTP_USER_AGENT'] . self::get_config_value('cms_hash_password') . $_SERVER['REMOTE_ADDR']);
        if (isset($_SESSION['SESSION_PROTECTTION']) && $_SESSION['SESSION_PROTECTTION'] != $fingerprint && session_class::is_crawler() === false) {
            session_regenerate_id(true);
            @session_start();
            header('X-Session-Reinit: true');
            $_SESSION['SESSION_PROTECTTION'] = $fingerprint;
        }
        else {
            $_SESSION['SESSION_PROTECTTION'] = $fingerprint;
        }
    }


    /**
     * mainadmin_class::set_post_get_cmd()
     * 
     * @return
     */
    function set_post_get_cmd() {
        if (isset($_POST['aktion'])) {
            $_GET['aktion'] = $_REQUEST['aktion'] = ($_POST['aktion'] != "") ? ($_POST['aktion']) : ($_GET['aktion']);
        }
        if (isset($_REQUEST['cmd'])) {
            $_GET['aktion'] = $_REQUEST['aktion'] = $_POST['aktion'] = $_REQUEST['cmd'];
        }
        if (isset($_REQUEST['aktion'])) {
            $_GET['cmd'] = $_REQUEST['cmd'] = $_POST['cmd'] = $_REQUEST['aktion'];
        }
    }

    /**
     * mainadmin_class::set_smarty_defaults()
     * 
     * @return
     */
    function set_smarty_defaults() {
        $this->smarty->addTemplateDir(CMS_ROOT . 'admin/tpl');
        define('SMARTY_TEMPDIR', CMS_ROOT . 'smarty/templates/');
    }

    /**
     * mainadmin_class::check_setlogin()
     * 
     * @return
     */
    function check_setlogin() {
        if ((int)$_SESSION['admin_obj']['id'] == 0 && !strstr($_SERVER['PHP_SELF'], 'index.php')) {
            header("location: index.php");
            exit;
        }
    }

    /**
     * mainadmin_class::check_redirect()
     * 
     * @return
     */
    function check_redirect() {
        if (isset($_GET['redirect'])) {
            header('location: ' . $_GET['redirect']);
            exit;
        }
    }

    /**
     * mainadmin_class::check_ssl()
     * 
     * @return
     */
    function check_ssl() {
        if ($this->gbl_config['ssl_forcessl'] == 1) {
            if (!self::ssl_active()) {
                header('location:' . self::get_domain_url() . 'admin/' . basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING']);
                exit;
            }
        }
    }

    /**
     * mainadmin_class::init_lang()
     * 
     * @param string $templang
     * @return
     */
    function init_lang($templang = "") {
        if (isset($templang) && (int)$templang > 0) {
            $_SESSION['alang_id'] = (int)$templang;
        }
        if ((int)$_SESSION['alang_id'] == 0)
            $_SESSION['alang_id'] = $this->gbl_config['std_lang_admin_id'];
        if ((int)$_SESSION['alang_id'] == 0) {
            $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE approval=1 ORDER BY s_order LIMIT 1");
            $_SESSION['alang_id'] = $R['id'];
        }
        if ((int)$_SESSION['alang_id'] == 0) {
            $_SESSION['alang_id'] = 1;
            $this->db->query("UPDATE " . TBL_CMS_LANG_ADMIN . " SET approval=1 WHERE id=1");
        }

        # load languages
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE approval='1' ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['link'] = './welcome.html?templang=' . $row['id'];
            $admin_langs[] = $row;
        }
        $this->smarty->assign('admin_langs', $admin_langs);
        return $_SESSION['alang_id'];
    }

    /**
     * mainadmin_class::std_smarty_vars()
     * 
     * @return
     */
    function std_smarty_vars() {
        $ver_info = $ver_info_org = $this->db->query_first("SELECT * FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='VERSION' LIMIT 1");
        $vparts = explode('.', $ver_info_org['wert']);
        $last = array_pop($vparts);
        $ver_info = implode('.', $vparts) . '<small>R' . $last . '</small>';
        $this->ADMIN['vars'] = array(
            'DB_DATABASE' => DB_DATABASE,
            'PREFIX' => str_replace(array('_', 'tch'), '', TBL_CMS_PREFIX),
            'CMSVERSION' => $ver_info);
        $LNGOBJ = new language_class();
        $this->ADMIN['langselect'] = $LNGOBJ->build_lang_select();
        $this->ADMIN['lang_arr'] = (array )$LNGOBJ->lang_arr;
        $this->ADMIN['is32bit'] = self::is_32bit();
        $this->ADMIN['server']['bitversion'] = (self::is_32bit() == true) ? '32bit' : '64bit';
        self::allocate_memory($LNGOBJ);
    }

    /**
     * mainadmin_class::output()
     * 
     * @return
     */
    function output() {
        global $sidegenstartadmin;
        $this->std_smarty_vars();
        $this->parse_to_smarty();
        $crj_obj = new crj_class();
        $this->inc_tpl('footer');
        if (isset($_GET['debug']) && $_GET['debug'] == 0)
            unset($_SESSION['DEBUG']);
        $_SESSION['DEBUG'] = (isset($_GET['debug']) && $_GET['debug'] == 1) ? 1 : intval($_SESSION['DEBUG']);
        $this->smarty->assign('DEBUG', $_SESSION['DEBUG']);
        if (isset($_REQUEST['axcall'])) {
            $this->content = '<%include file="framew.topbar.tpl"%>' . $this->content;
        }
        $this->content = kf::translate_admin(smarty_compile($this->content));
        $crj_obj->cleanSMARTYCompileCache();

        if ($_SESSION['DEBUG'] == 1) {
            file_put_contents('./cache/ladezeiten.xls', $this->db->query_hist);
            $this->content = str_replace('</body>',
                '<div style="border:1px solid red;padding:10px;z-index:10000;background-color:#fff;opacity:0.9;position:fixed;top:85%;right:0;">Memory: ' . human_file_size(memory_get_usage
                ()) . '<br>SQL Queries: ' . $this->db->query_counter . '<br>Compiletime:' . number_format(self::get_micro_time() - $sidegenstartadmin, 4, ".", ".") . '
                <br>Ladezeiten: <a href="./cache/ladezeiten.xls">Load XLS</a>
        ' . $_SESSION['DEBUG_ADMIN_CONTENT'] . '</div></body>', $this->content);
        }

        $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
        header("Content-type: text/html; charset=UTF-8");
        #echo tidy_page($html);
        echo $this->content;
        $this->hard_exit();
    }

    /**
     * mainadmin_class::check_cms_install_count()
     * 
     * @return boolean
     */
    function check_cms_install_count() {
        if ($this->gbl_config['multi_db'] == 1) {
            return true;
        }
        $result = mysqli_query($this->db->link_id, "SHOW TABLES FROM " . DB_DATABASE);
        while ($row = mysqli_fetch_row($result)) {
            if (strstr($row[0], 'temp_matrix')) {
                $found++;
                if ($found > 1) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * mainadmin_class::cmd_logout()
     * 
     * @return
     */
    function cmd_logout() {
        $this->LOGCLASS->addLog('LOGGEDOUT', $_SESSION['admin_obj']['mitarbeiter_name'] . 'left admin area');
        $_SESSION = array();
        session_write_close();
        @session_destroy();
        session_regenerate_id(true);
        HEADER("location: " . PATH_CMS . "admin/index.php");
        exit;
    }

    /**
     * mainadmin_class::cmd_login()
     * 
     * @return
     */
    function cmd_login() {
        global $EMPLOYEE;
        $FORM = (array )$_POST['FORM'];
        $admin_obj = $this->db->query_first("SELECT M.*,G.allowed,G.id AS GROUPID FROM " . TBL_CMS_ADMINS . " M, " . TBL_CMS_ADMINGROUPS .
            " G WHERE G.id=M.gid AND M.mitarbeiter_name='" . $FORM['mitindent'] . "' LIMIT 1");

        if (verfriy_password($FORM['password'], $admin_obj['passwort']) === false) {
            $admin_obj = array();
        }
        if (count($FORM) > 2) {
            $this->LOGCLASS->addLog('ACCESS_DENIED', 'Backend login hacking ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hacking('Backend Login hacking');
            unset($admin_obj);
        }
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            unset($admin_obj);
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        }

        if ($admin_obj['id'] > 0 && $this->check_cms_install_count() == true) {
            $EMPLOYEE->load_employee($admin_obj['id']);
            $EMPLOYEE->set_login_session();
            $_SESSION['employee'] = $EMPLOYEE->employee_obj;
            $this->LOGCLASS->employee = $_SESSION['mitarbeiter_name'];
            $this->LOGCLASS->addLog('LOGGEDIN', 'enter admin area');
            kf::load_permissions(); // MENU PERMISSION
            $this->LOGCLASS->clean_log();
            unset($_SESSION['login_log']);
            header("Location: " . PATH_CMS . "admin/welcome.html");
            exit;
        }
        else {
            if (intval($_SESSION['login_log']['last_fail']) == 0)
                $_SESSION['login_log']['first_fail'] = time();
            $_SESSION['login_log']['last_fail'] = time();
            $_SESSION['login_log']['counter']++;
            if ($_SESSION['login_log']['counter'] >= 3) {
                $_SESSION['login_log']['next'] = time() + (10 * ($_SESSION['login_log']['counter'] - 3));
            }
            header("Location: " . PATH_CMS . "admin/index.php?failed=1");
            exit;
        }
    }

    /**
     * mainadmin_class::init_login()
     * 
     * @return
     */
    function init_login() {
        $smarty = $this->smarty;
        $crj_obj = new crj_class();
        if ($_SESSION['login_log']['next'] > time()) {
            $restart_in = $_SESSION['login_log']['next'] - time();
            $this->smarty->assign('restart_in', $restart_in);
        }

        $this->smarty->assign('runable', $this->check_cms_install_count());
        $crj_obj->folderClean(CMS_ROOT . 'admin/tpl/', '.tpl', 0, array('TEMP_'));
        $this->smarty->assign('login_log', $_SESSION['login_log']);
        include (CMS_ROOT . 'admin/inc/smarty.inc.php');
        $crj_obj->clean_admin();
        kf::echo_template('login.admin');
    }
    /**
     * mainadmin_class::cmd_main_search_page()
     * 
     * @return
     */
    function cmd_main_search_page() {
        $term = strval(trim($_GET['term']));
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . "  WHERE 
	description LIKE '%" . $term . "%' 
    OR id LIKE '" . $term . "%'
	" . (($_SESSION['admin_obj']['GROUPID'] == 1) ? '' : " AND gbl_template=0 ") . "
	ORDER BY gbl_template,description 
	LIMIT 30");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['c_type'] == 'T') {
                $label = '{LBL_CONTENTPAGE}';
            }
            if ($row['c_type'] == 'B') {
                $label = 'Inlay';
            }
            if ($row['gbl_template'] == 1) {
                $label = '{LBLA_GBLTEMP}';
            }
            if ($row['c_type'] == 'B') {
                $edit_icon = kf::gen_edit_icon($row['id'], '&epage=inlayadmin.inc&uselang=' . $this->gbl_config['std_lang_id'], 'edit', 'id', 'run.php');
                $edit_link = 'run.php?epage=inlayadmin.inc&aktion=edit&id=' . $row['id'] . '&uselang=' . $this->gbl_config['std_lang_id'];
            }
            if ($row['c_type'] == 'T' && $row['gbl_template'] == 0) {
                $edit_icon = kf::gen_edit_icon($row['id'], '&epage=websitemanager.inc&tl=' . $row['tl'], 'edit', 'id', 'run.php');
                $edit_link = 'run.php?epage=websitemanager.inc&aktion=edit&tl=' . $row['tl'] . '&id=' . $row['id'] . '&uselang=' . $this->gbl_config['std_lang_id'];
            }
            if ($row['c_type'] == 'T' && $row['gbl_template'] == 1) {
                $edit_icon = kf::gen_edit_icon($row['id'], '&epage=gbltemplates.inc&uselang=' . $this->gbl_config['std_lang_id'], 'edit', 'id', 'run.php');
                $edit_link = 'javascript:std_load_gbltpl(' . $row['id'] . ',' . $this->gbl_config['std_lang_id'] . ');';
            }
            $row['edit_link'] = $edit_link;
            $row['label'] = $label;
            $this->ADMIN['searchresult'][md5($label)]['label'] = $label;
            $this->ADMIN['searchresult'][md5($label)]['items'][] = $row;
        }
        # search now for customers
        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K WHERE (
	 LOWER(K.email_notpublic) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.kid) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.nachname) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.knownof) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.firma) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.vorname) LIKE LOWER('%" . $term . "%') COLLATE utf8_bin OR 
	 LOWER(K.email) LIKE LOWER('%" . $term . "%')  COLLATE utf8_bin ) 
	 ORDER BY K.nachname LIMIT 10");
        while ($row = $this->db->fetch_array_names($result)) {
            $label = 'Kunden';
            $row['edit_link'] = 'kreg.php?cmd=edit&kid=' . $row['kid'];
            $row['description'] = $row['vorname'] . ' ' . $row['nachname'];
            $this->ADMIN['searchresult'][md5($label)]['label'] = $label;
            $this->ADMIN['searchresult'][md5($label)]['items'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('main_search_page');
    }
    /**
     * mainadmin_class::cmd_ax_ksearch()
     * 
     * @return
     */
    function cmd_ax_ksearch() {
        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K WHERE (K.email_notpublic LIKE '%" . $_GET['term'] . "%' OR K.kid LIKE '%" . $_GET['term'] .
            "%' OR K.nachname LIKE '%" . $_GET['term'] . "%' OR K.knownof LIKE '%" . $_GET['term'] . "%' OR K.firma LIKE '%" . $_GET['term'] . "%' OR K.vorname LIKE '%" . $_GET['term'] .
            "%' OR K.email LIKE '%" . $_GET['term'] . "%' ) 
            GROUP BY K.kid 
            ORDER BY K." . $_GET['orderby'] . " " . $_GET['direc']);
        $content .= '<table class="table table-striped table-hover" ><thead><tr><th></th><th>Anrede</th>
	<th>Nachname</a></th>
	<th>Vorname</th>
	<th>Username</th>
	<th>Email</th>
	<th>Firma</th>
	<th>Ort</th>
	<th></th>
	</tr></thead>';
        while ($row = $this->db->fetch_array_names($result)) {
            $content .= '<tr>
		<td><a href="kreg.php?cmd=show_edit&kid=' . $row['kid'] . '">' . $row['kid'] . '</a></td>
		<td>' . $row['anrede'] . '</td>
		<td><a href="kreg.php?cmd=show_edit&kid=' . $row['kid'] . '">' . $row['nachname'] . '</a></td>
		<td>' . $row['vorname'] . '</td>
		<td>' . $row['username'] . '</td>
		<td>' . $row['email'] . '</td>
		<td>' . $row['firma'] . '</td>
		<td>' . $row['ort'] . '</td>
		<td align="right"><a href="' . $_POST['ax_php'] . '?setkid=' . $row['kid'] . '&id=' . $_GET['id'] . '&epage=' . $_GET['epage'] . '&aktion=' . $_GET['doaktion'] .
                '">ausw&auml;hlen</a></td>
		</tr>';
        }
        ECHORESULT(kf::translate_admin($content) . '</table>');
    }

    /**
     * mainadmin_class::autorun_admin()
     * 
     * @return void
     */
    function autorun_admin() {
        $params = array();
        exec_evt('OnAutorunAdmin', $params, $this);
    }

    /**
     * mainadmin_class::load_admin_menu()
     * 
     * @return
     */
    function load_admin_menu() {
        $system_menu = array();
        $NARR = new nestedArrClass($this->db);
        $NARR->label_column = 'mname';
        $NARR->label_id = 'id';
        $NARR->label_parent = 'parent';
        $NARR->create_result_and_array("SELECT * FROM " . TBL_CMS_MENU . " ORDER BY morder", 0, 0, -1);
        # Apps Sortierung
        $NARR->menu_array[0]['children'] = (array )$NARR->menu_array[0]['children'];
        $NARR->menu_array[0]['children'] = $this->sort_multi_array($NARR->menu_array[0]['children'], 'mname', SORT_ASC, SORT_REGULAR, 'morder');

        if (count($NARR->menu_array[0]['children']) > 30) {
            $class = 'four-col';
            $col_class = '3'; #col-md-4
        }
        elseif (count($NARR->menu_array[0]['children']) > 20) {
            $class = 'three-col';
            $col_class = '3'; #col-md-4
        }
        elseif (count($NARR->menu_array[0]['children']) > 10) {
            $class = 'two-col';
            $col_class = '6'; #col-md-6
        }
        else {
            $class = 'one-col';
            $col_class = '12'; #col-md-12
        }
        foreach ($NARR->menu_array as $key => $row) {
            if ($row['id'] == 95) {
                $system_menu = $NARR->menu_array[$key]['children'];
                unset($NARR->menu_array[$key]);
                break;
            }
        }
        $system_menu = $this->fast_array_admintrans($system_menu);
        $system_menu = $this->sort_multi_array($system_menu, 'mname', SORT_ASC, SORT_REGULAR);

        if (!kf::is_superadmin()) {
            self::remove_notallowed_menu_items($system_menu);
        }

        $this->smarty->assign('adminmenu_row_class', $class);
        $this->smarty->assign('app_menu', $NARR->menu_array[0]['children']);
        $this->smarty->assign('adminmenu_col_class', $col_class);
        unset($NARR->menu_array[0]);
        $this->smarty->assign('adminmenu', (array)$NARR->menu_array);
        $this->smarty->assign('system_menu', $system_menu);
        $this->smarty->assign('allowed_menu_items', $_SESSION['mids']);
        keimeno_class::allocate_memory($NARR);
    }

    /**
     * mainadmin_class::remove_notallowed_menu_items()
     * 
     * @param mixed $arr
     * @return void
     */
    private static function remove_notallowed_menu_items(&$arr) {
        foreach ($arr as $key => $row) {
            if (!in_array($row['id'], $_SESSION['mids'])) {
                unset($arr[$key]);
            }
            else {
                if (is_array($arr[$key]['children']) && count($arr[$key]['children'] > 0)) {
                    self::remove_notallowed_menu_items($arr[$key]['children']);
                }
            }
        }

    }

    /**
     * mainadmin_class::force_admin_www()
     * 
     * @return
     */
    public static function force_admin_www() {
        $url = self::get_http_protocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parsedUrl = parse_url($url);
        $host = explode('.', $parsedUrl['host']);
        $subdomains = array_slice($host, 0, count($host) - 2);
        if (!strstr($_SERVER['HTTP_HOST'], 'www.') && count($subdomains) == 0) {
            header('location: ' . ((self::ssl_active()) ? 'https' : 'http') . '://www.' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}
