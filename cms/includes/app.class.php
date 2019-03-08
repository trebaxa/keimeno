<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('IN_SIDE') or die('Access denied.');

class app_class extends keimeno_class {

    /**
     * app_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
    }


    /**
     * app_class::load_mods_to_array()
     * 
     * @return
     */
    public static function load_mods_to_array() {
        $allmods_arr = array();
        try {
            $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
            foreach ($xml_modules->modules->children() as $module) {
                $allmods_arr[strval($module->settings->id)] = self::load_settings($module->settings);
            }
        }
        catch (kException $e) {
            die($e->errorMessage());
        }
        return $allmods_arr;
    }


    /**
     * app_class::load_active_mods_to_array()
     * 
     * @return
     */
    public static function load_active_mods_to_array() {
        $allmods_arr = array();
        try {
            $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
            foreach ($xml_modules->modules->children() as $module) {
                if ($module->settings->active == 'true')
                    $allmods_arr[strval($module->settings->id)] = self::load_settings($module->settings);
            }
        }
        catch (kException $e) {
            die($e->errorMessage());
        }
        return $allmods_arr;
    }

    /**
     * app_class::load_constants()
     * 
     * @param mixed $xml_constant
     * @return
     */
    public static function load_constants($xml_constant) {
        if (strtolower($xml_constant->getName()) == 'constants') {
            foreach ($xml_constant->children() as $node) {
                switch ($node->getName()) {
                    case 'sqltab':
                        DEFINE(strval($node), TBL_CMS_PREFIX . $node->attributes()->value);
                        break;
                }
            }
        }
    }

    /**
     * app_class::load_admin_includes()
     * 
     * @param mixed $xml_includes
     * @param mixed $mod_id
     * @param string $submodpath
     * @return
     */
    public static function load_admin_includes($xml_includes, $mod_id, $submodpath = '') {
        if (strtolower($xml_includes->getName()) == 'admin_includes') {
            foreach ($xml_includes->children() as $node) {
                $mpath = ($submodpath != "") ? MODULE_ROOT . $submodpath : MODULE_ROOT . $mod_id . '/';
                $file1 = $mpath . 'admin/' . strval($node) . '.admin.' . $node->attributes()->type . '.php';
                $file2 = $mpath . strval($node);
                $file3 = (defined('SHOP_ROOT') ? strtr(strval($node), array('SHOP_ROOT' => SHOP_ROOT, 'CMS_ROOT' => CMS_ROOT)) : strtr(strval($node), array('SHOP_ROOT' => '',
                        'CMS_ROOT' => CMS_ROOT)));
                if (file_exists($file1) || file_exists($file2) || file_exists($file3)) {
                    switch ($node->attributes()->type) {
                        case 'class':
                            if (!class_exists($node->attributes()->classname)) {
                                include ($file1);
                            }
                            break;
                        case 'file':
                            include ($file2);
                            break;
                        case 'externfile':
                            include ($file3);
                            break;
                        case '':
                            include ($file1);
                            break;
                    }
                }
                else {
                    throw new kException('XML error: File "' . strval($node) . '" defined, but "' . basename($file1) . '" / "' . basename($file2) . '" not found.');

                }
            }
        }

    }

    /**
     * app_class::load_includes()
     * 
     * @param mixed $xml_includes
     * @param mixed $mod_id
     * @param string $submodpath
     * @return
     */
    public static function load_includes($xml_includes, $mod_id, $submodpath = '') {
        if (strtolower($xml_includes->getName()) == 'includes') {
            foreach ($xml_includes->children() as $node) {
                $mpath = ($submodpath != "") ? MODULE_ROOT . $submodpath : MODULE_ROOT . $mod_id . '/';
                $file1 = $mpath . strval($node) . '.' . $node->attributes()->type . '.php';
                $file2 = $mpath . strval($node);
                if (defined('SHOP_ROOT')) {
                    $file3 = strtr(strval($node), array('SHOP_ROOT' => SHOP_ROOT, 'CMS_ROOT' => CMS_ROOT));
                }
                else {
                    $file3 = strtr(strval($node), array('SHOP_ROOT' => "", 'CMS_ROOT' => CMS_ROOT));
                }
                if (file_exists($file1) || file_exists($file2) || file_exists($file3)) {
                    switch ($node->attributes()->type) {
                        case 'class':
                            if (!class_exists($node->attributes()->classname)) {
                                include ($file1);
                            }
                            break;
                        case 'file':
                            include ($file2);
                            break;
                        case 'externfile':
                            include ($file3);
                            break;
                        case '':
                            include ($file1);
                            break;
                    }
                }
                else {
                    throw new kException('XML error: File "' . strval($node) . '" defined, but "' . $file1 . '" / "' . $file2 . '" not found.');

                }
            }
        }
    }


    /**
     * app_class::load_all_mods()
     * 
     * @param bool $exclude_cores
     * @return
     */
    public static function load_all_mods($exclude_cores = false) {
        $arr = array();
        $dh = opendir(MODULE_ROOT);
        while (false !== ($module_loader_dir = readdir($dh))) {
            if ($module_loader_dir != '.' && $module_loader_dir != '..' && $module_loader_dir != '' && is_dir(MODULE_ROOT . $module_loader_dir)) {
                $fname = MODULE_ROOT . $module_loader_dir . '/config.xml';
                if (file_exists($fname)) {
                    $xml_modul = simplexml_load_file($fname);
                    $mod = array('settings' => (array )$xml_modul->module->settings);
                    $mod['configfile'] = $fname;
                    $mod['module_name'] = kf::translate_admin($mod['settings']['module_name']);
                    $mod['current_version'] = (string )$xml_modul->module->settings->version;
                    $mod['current_version_num'] = str_replace('.', '', $mod['current_version']);
                    $mod['active'] = ($xml_modul->module->settings->active == 'false') ? false : true;
                    if ($exclude_cores == false || ($exclude_cores == true && $xml_modul->module->settings->iscore != 'true')) {
                        $arr[strval($xml_modul->module->settings->id)] = self::set_opt($mod);
                    }
                }
            }
        }
        return $arr;
    }


    /**
     * app_class::set_mod_active_status()
     * 
     * @return
     */
    public static function set_mod_active_status($modid, $status) {
        $fname = CMS_ROOT . 'includes/modules/' . $modid . '/config.xml';
        if (file_exists($fname)) {
            $xml_modul = simplexml_load_file($fname);
            $xml_modul->module->settings->active = ($status == true) ? 'true' : 'false';
            self::save_config($xml_modul, $fname);
        }
    }

    /**
     * app_class::save_config()
     * 
     * @param mixed $xml_modul
     * @param mixed $fname
     * @param bool $gen
     * @return
     */
    public static function save_config($xml_modul, $fname, $gen = true) {
        self::save_xml($xml_modul, $fname);
        if ($gen == true)
            self::generate_all_module_xml();
    }

    /**
     * app_class::generate_all_module_xml()
     * 
     * @return
     */
    public static function generate_all_module_xml() {
        $xml = new SimpleXMLElement('<config><modules></modules></config>');
        $dh = opendir(MODULE_ROOT);
        while (false !== ($module_loader_dir = readdir($dh))) {
            if ($module_loader_dir != '.' && $module_loader_dir != '..' && $module_loader_dir != '' && is_dir(MODULE_ROOT . $module_loader_dir)) {
                $dirs[] = MODULE_ROOT . $module_loader_dir;
                if (is_dir(MODULE_ROOT . $module_loader_dir . '/mods')) {
                    $dhm = opendir(MODULE_ROOT . $module_loader_dir . '/mods');
                    while (false !== ($module_loader_dirm = readdir($dhm))) {
                        if ($module_loader_dirm != '.' && $module_loader_dirm != '..' && file_exists(MODULE_ROOT . $module_loader_dir . '/mods/' . $module_loader_dirm . '/config.xml')) {
                            $dirs[] = MODULE_ROOT . $module_loader_dir . '/mods/' . $module_loader_dirm;
                        }
                    }
                }
            }
        }
        sort($dirs);
        foreach ($dirs as $dir) {
            $fname = $dir . '/config.xml';
            if (file_exists($fname)) {
                $xml_modul = simplexml_load_file($fname);
                if (strtolower($xml_modul->module->settings->active) == 'true') {
                    $domdict = dom_import_simplexml($xml->modules);
                    $domcat = dom_import_simplexml($xml_modul->module);
                    $domcat = $domdict->ownerDocument->importNode($domcat, TRUE);
                    $domdict->appendChild($domcat);
                }
            }
        }
        app_class::save_xml($xml, CMS_ROOT . 'includes/modules/config_all_modules.xml');
    }


    /**
     * app_class::set_opt()
     * 
     * @param mixed $mod
     * @return
     */
    protected static function set_opt(&$mod) {
        $approved = ($mod['settings']['active'] == 'true') ? 1 : 0;
        if (!isset($mod['settings']['iscore']) || $mod['settings']['iscore'] != 'true')
            $mod['icons'][] = kf::gen_approve_icon($mod['settings']['id'], $approved, 'axapprove_mod');
        $mod['installable'] = false;
        $mod['uninstallable'] = false;
        $mod['setup'] = file_exists(CMS_ROOT . 'includes/modules/' . $mod['settings']['id'] . '/setup/setup.class.php');
        if ($mod['setup'] === true) {
            include_once (CMS_ROOT . 'includes/modules/' . $mod['settings']['id'] . '/setup/setup.class.php');
            $class_name = $mod['settings']['id'] . '_setup_class';
            $C = new $class_name();
            $mod['uninstallable'] = method_exists($C, 'uninstall');
            $mod['installable'] = method_exists($C, 'install');
            self::allocate_memory($C);
        }
        return $mod;
    }


    /**
     * app_class::save_xml()
     * 
     * @param mixed $xml
     * @param mixed $fname
     * @return void
     */
    public static function save_xml($xml, $fname) {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($fname);
    }

    /**
     * app_class::get_module_roots()
     * 
     * @return
     */
    public static function get_module_roots() {
        $dirs = array();
        $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
        foreach ($xml_modules->modules->children() as $module) {
            $dirs[strval($module->settings->id)] = MODULE_ROOT . strval($module->settings->id) . '/';
        }
        return (array )$dirs;
    }

    /**
     * app_class::set_vars()
     * 
     * @param mixed $value
     * @param mixed $key
     * @param mixed $module_id
     * @param mixed $xml_settings
     * @return
     */
    public static function set_vars($value, $key, $module_id, $xml_settings) {
        if ($value == 'true' || $value == 'false') {
            return ($value == 'true');
        }
        if ($value == 'MODULE_ROOT') {
            return MODULE_ROOT;
        }
        if ($key == 'epage') {
            return explode(',', trim($value));
        }
        if ($key == 'php') {
            if (isset($xml_settings->submodpath)) {
                $submodpath = (string )$xml_settings->submodpath;
                $submodpath .= (substr($submodpath, -1) == '/' ? '' : '/');
                return MODULE_DIR . $submodpath . $value;
            }
            else {
                return MODULE_DIR . $module_id . '/' . $value;
            }
        }
        return $value;
    }

    /**
     * app_class::load_settings()
     * 
     * @param mixed $xml_settings
     * @return
     */
    public static function load_settings($xml_settings) {
        if (strtolower($xml_settings->getName()) == 'settings') {
            $module_id = strval($xml_settings->id);
            foreach ((array )$xml_settings as $index => $node) {
                $settings[$index] = self::set_vars(strval($node), $index, $module_id, $xml_settings);
            }
            $settings['mod_ident'] = (isset($settings['mod_ident']) ? $settings['mod_ident'] : "");
            $settings['general_mod'] = ($settings['mod_ident'] == "");
            if ($settings['mod_ident'] == "")
                $settings['mod_ident'] = 'mod_' . $module_id;
            return $settings;
        }
        else {
            throw new kException('XML error: Block "settings" is missing.');
        }
    }

}
