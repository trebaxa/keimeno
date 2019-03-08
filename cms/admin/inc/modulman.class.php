<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class moduleman_class extends modules_class {

    protected $MODMAN = array();

    /**
     * moduleman_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->rest_server = $this->rest_server_k = 'http://www.keimeno.de/rest/rest.php';
    }

    /**
     * moduleman_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if (!isset($_SESSION['keideveloper'])) {
            $_SESSION['keideveloper'] = array();
        }
        $this->MODMAN['keideveloper'] = (array )$_SESSION['keideveloper'];
        $this->smarty->assign('MODMAN', $this->MODMAN);
    }


    /**
     * moduleman_class::load_mods()
     * 
     * @return
     */
    function load_mods() {
        $this->MODMAN['allmods_arr'] = app_class::load_all_mods();
        $this->MODMAN['allmods_arr'] = self::convert_to_strings($this->MODMAN['allmods_arr']);
        $this->MODMAN['allmods_arr'] = self::sort_multi_array($this->MODMAN['allmods_arr'], 'active', SORT_DESC, SORT_REGULAR, 'module_name');
        return $this->MODMAN['allmods_arr'];
    }

    /**
     * moduleman_class::cmd_load_pool()
     * 
     * @return
     */
    function cmd_load_pool() {
        $this->MODMAN['pool'] = json_decode($this->curl_get_data($this->rest_server_k . '?cmd=get_keimeno_ext'), true);
        foreach ((array )$this->MODMAN['pool'] as $key => $row) {
            $fname = MODULE_ROOT . $row['id'] . '/config.xml';
            if (file_exists($fname)) {
                $xml_modul = simplexml_load_file($fname);
                $this->MODMAN['pool'][$key]['settings'] = (array )$xml_modul->module->settings;
                $this->MODMAN['pool'][$key]['current_version'] = (string )$xml_modul->module->settings->version;
                $this->MODMAN['pool'][$key]['current_version_num'] = str_replace('.', '', $this->MODMAN['pool'][$key]['current_version']);
                $this->MODMAN['pool'][$key]['installed'] = true;
            }
            else {
                $this->MODMAN['pool'][$key]['installed'] = false;
            }
            $this->MODMAN['pool'][$key]['e_version_num'] = str_replace('.', '', $row['e_version']);
        }
    }

    /**
     * moduleman_class::compile_all_apps()
     * 
     * @return
     */
    function compile_all_apps() {
        app_class::generate_all_module_xml();
        $this->uninstall_complete_admin_menu();
        $this->install_admin_menu();
    }

    /**
     * moduleman_class::cmd_compile_all_apps()
     * 
     * @return
     */
    function cmd_compile_all_apps() {
        $this->compile_all_apps();
        $this->hard_exit();
    }


    /**
     * moduleman_class::gen_all_mod_xml()
     * 
     * @return
     */
    public static function gen_all_mod_xml() {
        app_class::generate_all_module_xml();
    }

    /**
     * moduleman_class::delete_menue_xmls()
     * 
     * @return
     */
    function delete_menue_xmls() {
        $dh = opendir(CMS_ROOT . 'admin/cache/');
        while (false !== ($filename = readdir($dh))) {
            if (strstr($filename, 'menue_') && strstr($filename, '.xml')) {
                @unlink(CMS_ROOT . "admin/cache/" . $filename);
            }
        }
    }


    /**
     * moduleman_class::cmd_uninstall_mod()
     * 
     * @return
     */
    function cmd_uninstall_mod() {
        $mod_id = $this->TCR->GET['mod_id'];
        $this->uninstall_mod($mod_id);
        $this->hard_exit();
    }

    /**
     * moduleman_class::autoupdate_of_installed_mods()
     * 
     * @param string $usemodid
     * @return
     */
    function autoupdate_of_installed_mods($usemodid = "") {
        $dh = opendir(MODULE_ROOT);
        while (false !== ($modid = readdir($dh))) {
            if ($modid != '.' && $modid != '..' && $modid != 'default_mod' && $modid != '' && is_dir(MODULE_ROOT . $modid)) {
                if ($usemodid == "" || $usemodid = $modid) {
                    $fname = MODULE_ROOT . $modid . '/setup/setup.class.php';
                    if (file_exists($fname) && is_file($fname)) {
                        require_once ($fname);
                        $modclassname = $modid . '_setup_class';
                        $MCLASS = new $modclassname();
                        if (method_exists($MCLASS, 'autoupdate')) {
                            $MCLASS->autoupdate();
                        }
                        $this->allocate_memory($MCLASS);
                    }
                }
            }
        }
    }


    /**
     * moduleman_class::cmd_activate_mod()
     * 
     * @return
     */
    function cmd_activate_mod() {
        $actives = (array )$_POST['MODS'];
        $allmods = (array )$_POST['allmods'];
        foreach ($allmods as $key => $id) {
            $fname = $this->MODMAN['allmods_arr'][$id]['configfile'];
            if (file_exists($fname)) {
                $this->autoupdate_of_installed_mods($id);
                app_class::set_mod_active_status($id, in_array($id, $actives));
            }
            if (in_array($id, $actives)) {
                $this->install_admin_menu($id);
            }
            else {
                $this->uninstall_admin_menu($id);
            }
        }
        app_class::generate_all_module_xml();
        $this->ej('reload_menu');
    }

    /**
     * moduleman_class::install_admin_menu()
     * 
     * @param string $modid
     * @return
     */
    function install_admin_menu($modid = "") {
        $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
        foreach ($xml_modules->modules->children() as $module) {
            $submenuid = 0;
            if (($modid == "" || $modid == strval($module->settings->id))) {
                if (get_data_count(TBL_CMS_MENU, '*', "mod_ident='" . strval($module->settings->id) . "'") == 0 && (isset($module->admin_menu) || isset($module->
                    admin_menu_items))) {
                    if (isset($module->admin_menu)) {
                        $INFO = $this->db->query_first("SELECT MAX(id) AS MAXI FROM " . TBL_CMS_MENU);
                        $add = ($INFO['MAXI'] < 10000) ? 10000 : 10;
                        $query = ($module->admin_menu->attributes()->query != "") ? '&' . str_replace('+', '&', strval($module->admin_menu->attributes()->query)) : "";
                        $php = ($module->admin_menu->attributes()->epage != "") ? 'run.php?epage=' . $module->admin_menu->attributes()->epage . '.inc&section=start' . $query :
                            'run.php?epage=' . $module->settings->id . '.inc&section=start' . $query;
                        $F = array(
                            'id' => $INFO['MAXI'] + $add,
                            'mname' => strval($module->settings->module_name),
                            'parent' => ($module->admin_menu->attributes()->parent == "") ? 96 : (int)$module->admin_menu->attributes()->parent,
                            'mod_ident' => strval($module->settings->id),
                            'description' => strval($module->settings->description),
                            'icon' => $module->admin_menu->attributes()->icon,
                            'morder' => (int)$module->admin_menu->attributes()->morder,
                            'is_core' => 0,
                            #'php' => 'run.php?epage=' . $module->settings->id . '.inc&section=start',
                            'php' => $php,
                            );
                        $submenuid = insert_table(TBL_CMS_MENU, $F);
                        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE 1");
                        while ($MC = $this->db->fetch_array_names($result)) {
                            $ids = explode(';', $MC['allowed']);
                            $ids[] = $submenuid;
                            $this->db->query("UPDATE " . TBL_CMS_ADMINGROUPS . " SET allowed='" . implode(';', array_unique($ids)) . "' WHERE id=" . $MC['id']);
                        }
                    }
                    if (isset($module->admin_menu_items)) {
                        foreach ($module->admin_menu_items->children() as $amenu) {
                            #get_data_count(TBL_CMS_MENU, '*', "mod_ident='" . strval($module->settings->id) . "'") == 0 &&
                            $INFO = $this->db->query_first("SELECT MAX(id) AS MAXI FROM " . TBL_CMS_MENU);
                            $add = ($INFO['MAXI'] < 10000) ? 10000 : 10;
                            $query = ($amenu->attributes()->query != "") ? '&' . str_replace('+', '&', strval($amenu->attributes()->query)) : "";
                            $php = ($amenu->attributes()->epage != "") ? 'run.php?epage=' . $amenu->attributes()->epage . '.inc&section=start' . $query : 'run.php?epage=' . $module->
                                settings->id . '.inc&section=start' . $query;
                            if ($amenu->attributes()->php != "") {
                                $php = $amenu->attributes()->php . '.php?' . $query;
                            }
                            $mname = ($amenu->attributes()->label != "") ? strval($amenu->attributes()->label) : strval($module->settings->module_name);
                            $parent = ($amenu->attributes()->parent == "") ? 121 : (int)$amenu->attributes()->parent;
                            $parent = ($submenuid > 0) ? $submenuid : $parent;
                            $F = array(
                                'id' => $INFO['MAXI'] + $add,
                                'mname' => $mname,
                                'parent' => $parent,
                                'mod_ident' => strval($module->settings->id),
                                'description' => strval($module->settings->description),
                                'is_core' => 0,
                                'icon' => $amenu->attributes()->icon,
                                'morder' => (int)$amenu->attributes()->morder,
                                'php' => $php,
                                );
                            $id = insert_table(TBL_CMS_MENU, $F);
                            $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE 1");
                            while ($MC = $this->db->fetch_array_names($result)) {
                                $ids = explode(';', $MC['allowed']);
                                $ids[] = $id;
                                $this->db->query("UPDATE " . TBL_CMS_ADMINGROUPS . " SET allowed='" . implode(';', array_unique($ids)) . "' WHERE id=" . $MC['id']);
                            }
                        }
                    }
                }
            }
        }
        $this->delete_menue_xmls();
        $EMP = new employee_class();
        $EMP->reload_employee($_SESSION['mitarbeiter']);
        $this->allocate_memory($EMP);
    }

    /**
     * moduleman_class::uninstall_admin_menu()
     * 
     * @param mixed $id
     * @return
     */
    function uninstall_admin_menu($id) {
        if ($id != "") {
            $G = $this->db->query_first("SELECT * FROM " . TBL_CMS_MENU . " WHERE mod_ident='" . $id . "' AND mod_ident!=''");
            $this->db->query("DELETE FROM " . TBL_CMS_MENU . " WHERE mod_ident='" . $id . "' AND mod_ident<>''");
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE 1");
            while ($MC = $this->db->fetch_array_names($result)) {
                $ids = explode(';', $MC['allowed']);
                sort($ids);
                $this->db->query("UPDATE " . TBL_CMS_ADMINGROUPS . " SET allowed='" . implode(';', array_diff($ids, array((int)$G['id']))) . "' WHERE id=" . $MC['id']);
            }
        }
        $this->delete_menue_xmls();
        $EMP = new employee_class();
        $EMP->reload_employee($_SESSION['mitarbeiter']);
        $this->allocate_memory($EMP);
    }

    /**
     * moduleman_class::uninstall_complete_admin_menu()
     * 
     * @return
     */
    function uninstall_complete_admin_menu() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_MENU . " WHERE mod_ident<>''");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->uninstall_admin_menu($row['mod_ident']);
        }
    }

    /**
     * moduleman_class::recurse_copy_mod()
     * 
     * @param mixed $src
     * @param mixed $dst
     * @param mixed $replace
     * @param bool $overwrite
     * @return
     */
    function recurse_copy_mod($src, $dst, $replace, $overwrite = true) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy_mod($src . '/' . $file, $dst . '/' . $file, $replace);
                }
                else {
                    $newfile = strtr($file, $replace);
                    if ($overwrite == true || !file_exists($dst . '/' . $newfile))
                        file_put_contents($dst . '/' . $newfile, strtr(file_get_contents($src . '/' . $file), $replace));
                }
            }
        }
        closedir($dir);
    }

    /**
     * moduleman_class::cmd_create_mod()
     * 
     * @return
     */
    function cmd_create_mod() {
        $FORM = $_POST['FORM'];
        $dir = MODULE_ROOT . strtolower($FORM['mod_ident']);
        if (is_dir($dir)) {
            echo '<span class="redspan">Mod existiert schon.</span>';
            $this->hard_exit();
        }
        $replace = array(
            '{IDENT}' => strtolower($FORM['mod_ident']),
            '{IDENTUPPER}' => strtoupper($FORM['mod_ident']),
            '{MODNAME}' => $FORM['mod_name'],
            '{MODVERSION}' => strtoupper($FORM['mod_version']),
            'ident.' => strtolower($FORM['mod_ident']) . '.',
            '{CREATEDATE}' => date('Y-m-d'),
            'default_config.xml' => 'config.xml');
        $this->recurse_copy_mod(MODULE_ROOT . 'default_mod', $dir, $replace);
        # $MODCLASS = new modules_class();
        app_class::generate_all_module_xml();
        $this->install_admin_menu($FORM['mod_ident']);
        echo '<span class="greenspan">Mod created.</span>';
        $this->hard_exit();
    }

    /**
     * moduleman_class::cmd_reloadmodtable()
     * 
     * @return
     */
    function cmd_reloadmodtable() {
        $this->load_mods();
        $this->parse_to_smarty();
        kf::echo_template('modman.modtable');
    }


    /**
     * moduleman_class::uninstall_mod()
     * 
     * @param mixed $mod_id
     * @return
     */
    function uninstall_mod($mod_id) {
        $this->uninstall_admin_menu($mod_id);
        $mod['setup'] = file_exists(CMS_ROOT . 'includes/modules/' . $mod_id . '/setup/setup.class.php');
        if ($mod['setup'] === true) {
            include_once (CMS_ROOT . 'includes/modules/' . $mod_id . '/setup/setup.class.php');
            $class_name = $mod_id . '_setup_class';
            try {
                $C = new $class_name();
                if (method_exists($C, 'uninstall')) {
                    $C->uninstall();
                }
                $this->allocate_memory($C);
            }
            catch (Exception $e) {
                // echo $e->getMessage();
            }
            $this->delete_menue_xmls();
            # remove tempaltes
            $T = new template_class();
            $result = $this->db->query("SELECT id FROM " . TBL_CMS_TEMPLATES . " WHERE modident='" . $mod_id . "' AND modident<>'' ORDER BY id");
            while ($row = $this->db->fetch_array_names($result)) {
                $T->delete_template($row['id']);
            }
            $this->allocate_memory($T);

        }
        # delete mod root
        $this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/' . $mod_id);
        app_class::generate_all_module_xml();
    }

    /**
     * moduleman_class::add_gblconfig()
     * 
     * @param mixed $modid
     * @return
     */
    function add_gblconfig($modid) {
        # add gbl_config
        if (file_exists(MODULE_ROOT . $modid . '/setup/gblconfig.xml')) {
            $contents = file_get_contents(MODULE_ROOT . $modid . '/setup/gblconfig.xml');
            $arr = $this->xml2array($contents);
            if (isset($arr['config']['item']) && is_array($arr['config']['item'])) {
                foreach ($arr['config']['item'] as $item) {
                    $sql = "";
                    foreach ($item as $column => $value) {
                        $sql .= (($sql != "") ? "," : "") . $column . "='" . $this->db->real_escape_string((string )$value) . "'";
                    }
                    $result = mysqli_query($this->db->link_id, "INSERT INTO " . TBL_CMS_GBLCONFIG . " SET " . $sql);
                }
            }
        }
    }


    /**
     * moduleman_class::cmd_reminstall()
     * 
     * @return
     */
    function cmd_reminstall() {
        $modid = (string )$_GET['modid'];
        $target = CMS_ROOT . 'includes/modules/' . $modid . '.tar.gz';
        $this->LOGCLASS->addLog('MODINSTALL', 'install mod started ' . $modid);
        # download modul
        $this->curl_exec_script($this->rest_server_k . '?cmd=count_donwload&modid=' . $modid);
        $this->curl_get_data_to_file('https://www.keimeno.de/file_data/extensions/' . $modid . '.tar.gz', $target);
        exec('tar -C ../includes/modules/ -xvvzf ../includes/modules/' . basename($target));
        unlink($target);

        # install templates
        $this->add_tempaltes($modid);

        # add gbl_config
        $this->add_gblconfig($modid);

        # run setup
        if (file_exists(MODULE_ROOT . $modid . '/setup/setup.class.php')) {
            require_once (MODULE_ROOT . $modid . '/setup/setup.class.php');
            $modclassname = $modid . '_setup_class';
            $MCLASS = new $modclassname();
            if (method_exists($MCLASS, 'install')) {
                $MCLASS->install();
            }
            $this->allocate_memory($MCLASS);
            $this->delete_menue_xmls();
        }

        app_class::generate_all_module_xml();
        $this->install_admin_menu($modid);
        $this->LOGCLASS->addLog('MODINSTALL', 'install mod finished ' . $modid);
        $this->parse_to_smarty();
        kf::echo_template('modman.instfeedback');
    }

    /**
     * moduleman_class::add_tempaltes()
     * 
     * @param mixed $modid
     * @return
     */
    function add_tempaltes($modid) {
        $last_ids = $allowed_for_insert_tpls = array();
        $err_log = "";

        # import templates
        $file = MODULE_ROOT . $modid . '/setup/templates.sql';
        if (file_exists($file)) {
            $all_lines = file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            foreach ($all_lines as $query) {
                if (substr($query, 0, 2) == "--") {
                    continue;
                }
                # get tpl_name
                preg_match_all("=tpl_name\='(.*)',=siU", $query, $presult);
                $tpl_name = $presult[1][0];
                if (get_data_count(TBL_CMS_TEMPLATES, 'tpl_name', "tpl_name='" . $tpl_name . "'") == 0 && (string )$tpl_name != "") {
                    $query = str_replace('!!TBL_PREFIX!!', TBL_CMS_PREFIX, $query);
                    $query_id = mysqli_query($this->db->link_id, $query);
                    if (!$query_id) {
                        $err_log .= "Invalid SQL: " . $query . "<br>" . $this->db->get_err();
                    }
                    $last_ids[] = mysqli_insert_id($this->db->link_id);
                    $allowed_for_insert_tpls[] = $tpl_name;
                }
            }


            # import template content
            $file = MODULE_ROOT . $modid . '/setup/temp_content.sql';
            if (file_exists($file)) {
                $all_lines = file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
                foreach ($all_lines as $query) {
                    if (substr($query, 0, 2) == "--") {
                        continue;
                    }
                    # get tpl_name
                    preg_match_all("=t_tpl_name\='(.*)'=siU", $query, $presult);
                    $t_tpl_name = $presult[1][0];
                    if (in_array($t_tpl_name, $allowed_for_insert_tpls)) {
                        $query = str_replace('!!TBL_PREFIX!!', TBL_CMS_PREFIX, $query);
                        $query_id = mysqli_query($this->db->link_id, $query);
                        if (!$query_id) {
                            $err_log .= "Invalid SQL: " . $query . "<br>" . $this->db->get_err();
                        }
                        #  $last_id = mysqli_insert_id($this->db->link_id);
                    }
                }
            }

            # connect content with templates
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 AND modident='" . $modid . "'");
            while ($row = $this->db->fetch_array_names($result)) {
                $restemp = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE t_tpl_name='" . $row['tpl_name'] . "'");
                while ($trow = $this->db->fetch_array_names($restemp)) {
                    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET tid='" . $row['id'] . "' WHERE id=" . $trow['id']);
                }
            }

            include_once (CMS_ROOT . 'admin/inc/gbltemplates.class.php');
            $GBLT = new gbltpl_class();
            foreach ((array )$last_ids as $tid) {
                $GBLT->rewrite_tpls_of_template($tid);
            }

            if ($err_log != "") {
                file_put_contents(MODULE_ROOT . $modid . '/setup/sql_error.log', $err_log);
            }
        }
    }

    /**
     * moduleman_class::update_templates()
     * 
     * @param mixed $modid
     * @return
     */
    function update_templates($modid) {
        $this->add_tempaltes($modid);

        # Datenbank bereinigen
        $result = $this->db->query("SELECT tpl_name FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 
            AND tpl_name!='' 
            AND modident='" . $modid . "' 
            GROUP BY tpl_name 
            HAVING COUNT(tpl_name)>1");
        while ($row = $this->db->fetch_array_names($result)) {
            $result2 = $this->db->query("SELECT id,tpl_name FROM " . TBL_CMS_TEMPLATES . " WHERE tpl_name='" . $row['tpl_name'] . "'");
            while ($row2 = $this->db->fetch_array_names($result2)) {
                $arr[] = $row2;
            }
        }
        $T = new template_class();
        $arr = $this->sort_multi_array($arr, 'id', SORT_ASC, SORT_NUMERIC);
        while (count($arr) > 1) {
            $tpl = array_pop($arr);
            $T->delete_template($tpl['id']);
        }
    }

    /**
     * moduleman_class::mod_is_active()
     * 
     * @return boolean
     */
    public static function mod_is_active($modid) {
        $fname = CMS_ROOT . 'includes/modules/' . $modid . '/config.xml';
        if (file_exists($fname)) {
            $xml_modul = simplexml_load_file($fname);
            return ((string )$xml_modul->module->settings->active == 'true') ? true : false;
        }
        else
            return false;
    }


    /**
     * moduleman_class::cmd_remupdate()
     * 
     * @return
     */
    function cmd_remupdate() {
        try {
            $modid = $_GET['modid'];
            $mod_is_active = self::mod_is_active($modid);
            $this->LOGCLASS->addLog('MODUPDATE', 'update mod started: ' . $modid);
            $target = CMS_ROOT . 'includes/modules/' . $modid . '.tar.gz';

            # backup
            $xml_modul = simplexml_load_file(CMS_ROOT . 'includes/modules/' . $modid . '/config.xml');
            $version = (string )$xml_modul->module->settings->version;
            $backup_path = CMS_ROOT . 'includes/modules/' . $modid . '/backups/backup_' . $version;
            if (!is_dir(CMS_ROOT . 'includes/modules/' . $modid . '/backups')) {
                mkdir(CMS_ROOT . 'includes/modules/' . $modid . '/backups', 0775);
            }
            if (!is_dir($backup_path)) {
                mkdir($backup_path, 0775);
            }
            if (is_dir($backup_path)) {
                exec("cd ..;cd includes;cd modules;cd " . $modid . ";tar cvfz ./backups/backup_" . $version . "/backup_" . date('Y-m-d_H_i_s') .
                    ".tar.gz ./ --exclude ./backups");
            }

            # save only settings like CSS files
            exec("tar cvfz modsik.tar.gz ../includes/modules/" . $modid . " --exclude=*.php --exclude=*.tpl --exclude=config.xml --exclude=*.sql");

            # download modul and extract
            $this->curl_exec_script($this->rest_server_k . '?cmd=count_donwload&modid=' . $modid);
            $this->curl_get_data_to_file('https://www.keimeno.de/file_data/extensions/' . $modid . '/' . $modid . '.tar.gz', $target);

            if (is_file($target)) {
                $cmd = 'tar --overwrite -C ../includes/modules/ -xvzf ../includes/modules/' . basename($target);
                exec($cmd);
                @unlink($target);
            }

            # restore settings and CSS files
            exec('tar -C ../ -xvvzf ./modsik.tar.gz');
            @unlink("./modsik.tar.gz");

            # add gbl_config
            $this->add_gblconfig($modid);

            # install templates
            $this->update_templates($modid);

            # run setup
            if (file_exists(MODULE_ROOT . $modid . '/setup/setup.class.php')) {
                require_once (MODULE_ROOT . $modid . '/setup/setup.class.php');
                $modclassname = $modid . '_setup_class';
                $MCLASS = new $modclassname();
                if (method_exists($MCLASS, 'update')) {
                    $MCLASS->update();
                }
                $this->allocate_memory($MCLASS);
                $this->delete_menue_xmls();
            }

            # activate mod after update or deactivate / restore state
            if ($mod_is_active == true) {
                app_class::set_mod_active_status($modid, true);
                $this->msg('aktiviert');
            }
            if ($mod_is_active == false) {
                app_class::set_mod_active_status($modid, false);
                $this->msg('deaktiviert');
            }

            app_class::generate_all_module_xml();
            $this->install_admin_menu($modid);
            $this->LOGCLASS->addLog('MODUPDATE', 'update mod finished: ' . $modid);
            $this->parse_to_smarty();
        }
        catch (Exception $e) {
            $this->msge($e->getMessage());
        }
        $this->msg('Update installiert');
        $this->ej();
    }

    /**
     * moduleman_class::cmd_kelogin()
     * 
     * @return
     */
    function cmd_kelogin() {
        $this->MODMAN['customer'] = json_decode($this->curl_get_data($this->rest_server_k . '?cmd=login&email=' . $_POST['email'] . '&pwd=' . $_POST['pwd']), true);
        if ($this->MODMAN['customer']['kid'] > 0) {
            $_SESSION['keideveloper'] = $this->MODMAN['customer'];
            $this->msg('Willkommen ' . $this->MODMAN['customer']['vorname']);
            $this->ej('startsappsel');
        }
        else {
            keimeno_class::allocate_memory($_SESSION['keideveloper']);
            $this->msge('Login failed');
        }
        $this->ej();
    }

    /**
     * moduleman_class::cmd_get_own_apps()
     * 
     * @return
     */
    function cmd_get_own_apps() {
        $dh = opendir(MODULE_ROOT);
        while (false !== ($module_loader_dir = readdir($dh))) {
            if ($module_loader_dir != '.' && $module_loader_dir != '..' && $module_loader_dir != '' && is_dir(MODULE_ROOT . $module_loader_dir)) {
                $fname = MODULE_ROOT . $module_loader_dir . '/config.xml';
                if (file_exists($fname)) {
                    $xml_modul = simplexml_load_file($fname);
                    if ($xml_modul->module->settings->developer_id == $_SESSION['keideveloper']['kid']) {
                        $mod = array('settings' => (array )$xml_modul->module->settings);
                        $mod['configfile'] = $fname;
                        $mod['module_name'] = kf::translate_admin($mod['settings']['module_name']);
                        $this->MODMAN['ownapps'][strval($xml_modul->module->settings->id)] = app_class::set_opt($mod);
                    }
                }
            }
        }
        $this->MODMAN['ownapps'] = $this->convert_to_strings($this->MODMAN['ownapps']);
        $this->MODMAN['ownapps'] = $this->sort_multi_array($this->MODMAN['ownapps'], 'module_name', SORT_ASC, SORT_STRING); # load app if needed
        if ($_GET['modid'] != "") {
            $this->MODMAN['modul'] = $this->load_app_settings($_GET['modid']);
        }
        $this->parse_to_smarty();
        kf::echo_template('modman.ownapps');
    }

    /**
     * moduleman_class::load_app_settings()
     * 
     * @param mixed $appid
     * @return
     */
    function load_app_settings($appid) {
        $xml_modul = simplexml_load_file(MODULE_ROOT . $appid . '/config.xml');
        $settings = (array )$xml_modul->module->settings;
        return $this->convert_to_strings($settings);
    }

    /**
     * moduleman_class::cmd_load_packer()
     * 
     * @return
     */
    function cmd_load_packer() {
        $this->MODMAN['modul'] = $this->load_app_settings($_GET['modident']);
        $this->parse_to_smarty();
        kf::echo_template('modman.packer');
    }

    /**
     * moduleman_class::cmd_packandsend()
     * 
     * @return
     */
    function cmd_packandsend() {
        $modid = $_POST['modident']; # validate dirs
        if (!is_dir(MODULE_ROOT . $modid . '/setup')) {
            mkdir(MODULE_ROOT . $modid . '/setup', 0775);
        }
        if (!is_dir(MODULE_ROOT . $modid . '/setup/tpl')) {
            mkdir(MODULE_ROOT . $modid . '/setup/tpl', 0775);
        }

        #ensure setup std files
        $settings = $this->load_app_settings($modid);
        $replace = array(
            '{IDENT}' => strtolower($modid),
            '{IDENTUPPER}' => strtoupper($modid),
            '{MODNAME}' => $settings['module_name'],
            '{MODVERSION}' => strtoupper($FORM['mod_version']),
            'ident.' => strtolower($modid) . '.',
            '{CREATEDATE}' => date('Y-m-d'),
            'default_config.xml' => 'config.xml');
        $this->recurse_copy_mod(MODULE_ROOT . 'default_mod/setup/', MODULE_ROOT . $modid . '/setup/', array(), false);
        # create template export
        $result = $this->db->query("SELECT TC.*, T.description,L.local FROM " . TBL_CMS_LANG . " L," . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT .
            " TC ON (TC.tid=T.id) 
                    WHERE gbl_template=1 AND L.id=TC.lang_id
                    AND modident='" . $modid . "'");
        while ($row = $this->db->fetch_array_names($result)) {
            $fname = MODULE_ROOT . $modid . '/setup/tpl/' . $this->format_file_name($row['description']) . '_' . $row['local'] . '.tpl';
            file_put_contents($fname, $row['content']);
        }

        # export gbl_config
        $xml = '<config>';
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLCONFIG . " WHERE modident='" . $modid . "'");
        while ($row = $this->db->fetch_array_names($result)) {
            $xml .= '<item>';
            foreach ($row as $key => $value) {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
            $xml .= '</item>';
        }
        $xml .= '</config>';
        $xmlobj = new SimpleXMLElement($xml);
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlobj->asXML());
        $dom->save(MODULE_ROOT . $modid . '/setup/gblconfig.xml'); # create tar
        exec("cd ..;cd includes;cd modules;tar cvfz ../../cache/" . $modid . ".tar.gz " . $modid); # notify trebaxa
        $email_content = "New App\n\nApp ID:\t" . $modid . "\nDeveloper:\t" . $_SESSION['keideveloper']['vorname'] . ' ' . $_SESSION['keideveloper']['nachname'] . "
        DEV ID:\t" . $_SESSION['keideveloper']['kid'] . "\n\nSend on:\t" . date('Y-m-s H:i:s') . "\n\nForm:\n\n";
        foreach ((array )$_POST['FORM'] as $key => $value) {
            $email_content .= $key . ":\t" . $value . "\n";
        }
        $email_content .= "Server Vars:\n";
        foreach ($_SERVER as $key => $value) {
            $email_content .= $key . ":\t" . $value . "\n";
        }
        $email_content .= "Developer:\n";
        foreach ($_SESSION['keideveloper'] as $key => $value) {
            $email_content .= $key . ":\t" . $value . "\n";
        }
        $att_files[] = CMS_ROOT . 'cache/' . $modid . ".tar.gz";
        send_easy_mail_to(FM_EMAIL, $email_content, 'New App Register ' . $modid . '|' . $_SESSION['keideveloper']['kid'], $att_files);
        $this->ej('load_kei_finish', "'" . $modid . "'");
    }

    /**
     * moduleman_class::cmd_reloadmenu()
     * 
     * @return
     */
    function cmd_reloadmenu() {
        $this->compile_all_apps();
        $MA = new mainadmin_class();
        $MA->load_admin_menu();
        kf::echo_template('adminmenu');
    }

}
