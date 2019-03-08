<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


/**
 * get_micro_time_mod()
 * 
 * @return
 */
function get_micro_time_mod() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

if (NO_MODULES != 1) {

    #  app_class::generate_all_module_xml();

    try {
        if (!file_exists(MODULE_ROOT . 'config_all_modules.xml')) {
            throw new kException('config_all_modules.xml is missing. Defect installation!');
        }

        $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
        if ($xml_modules->modules->children() !== null) {
            foreach ($xml_modules->modules->children() as $module) {
                $submodpath = strtolower($module->settings->submodpath);
                $MODULE[strval($module->settings->id)] = app_class::load_settings($module->settings);
                if ($submodpath != "") {
                    $MODULE[strval($module->settings->id)]['epage_dir'] .= $submodpath;
                }
                app_class::load_constants($module->constants);
                app_class::load_includes($module->includes, $module->settings->id, $submodpath);
                if (defined('ISADMIN') && ISADMIN == 1) {
                    app_class::load_admin_includes($module->admin_includes, $module->settings->id, $submodpath);
                    $mpath = ($module->settings->submodpath != "") ? MODULE_ROOT . $module->settings->submodpath : MODULE_ROOT . strval($module->settings->id) . '/';
                    if (is_dir($mpath . '/admin/tpl')) {
                        $smarty->addTemplateDir($mpath . '/admin/tpl');
                    }
                }
            }
        }
        else {
            throw new kException('config_all_modules.xml is empty. Defect installation!');
        }
    }
    catch (kException $e) {
        die($e->get_error_message());
    }

    exec_evt('OnCoreStartup');

    if (!defined('TBL_CMS_CUST')) {
        define('TBL_CMS_CUST', TBL_CMS_KUNDEN);
        define('TBL_CMS_CUSTGROUPS', TBL_CMS_RGROUPS);
    }

    $user_object = array();
    if (!defined('ISADMIN')) {
        $user_object = $user_obj->init_user();
    }
    $CMSDATA = new cms_data_class($user_object, $GBL_LANGID, $user_obj);
    $CMSDATA->LANGS = $LANGS;
    $CMSDATA->LANGSFE = $LANGSFE;

    if (!defined('ISADMIN')) {
        exec_evt('autorun');
    }

    if (defined('ISADMIN')) {
        exec_evt('autorunadmin');
    }

    if (!defined('ISADMIN') && ((isset($_GET['page']) && $_GET['page'] == START_PAGE) || (isset($_GET['page']) && $_GET['page'] == 0) || !isset($_GET['page']))) {
        exec_evt('startpage');
    }


}
$user_obj->user_obj = $user_object; // important
