<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      09.02.2016
 * @version    1.0.3.8
 */

$version = '1.0.3.8';

# updates
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/js/ace119/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/js/ace119-sik/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/fb/');

@unlink(CMS_ROOT . 'admin/footer.php');
@unlink(CMS_ROOT . 'admin/inc/uploadify.inc.php');
@unlink(CMS_ROOT . 'siauto.php');
@unlink(CMS_ROOT . 'admin/change.php');
@unlink(CMS_ROOT . 'admin/log_error.log');
@unlink(CMS_ROOT . 'admin/log_successfull.log');

# import javascript

$js_path = CMS_ROOT . 'file_data/tpljs/';
if (is_dir($js_path)) {
    if ($handle = opendir($js_path)) {
        while (false !== ($file = readdir($handle))) {
            if (is_file($js_path . $file) && file_exists($js_path . $file)) {
                $tpl_name = str_replace(array('.js', 'js_'), '', $file);
                $tpl = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE tpl_name='" . $tpl_name . "' AND t_java=''");
                if (isset($tpl['id']) && $tpl['id'] > 0) {
                    $text = file_get_contents($js_path . $file);
                    update_table(TBL_CMS_TEMPLATES, 'id', $tpl['id'], array('t_java' => $this->db->real_escape_string($text)));
                }
            }
        }
    }
}

$tpl_rep = array('<div id="savedresult"></div>' => '');
$this->replaceInTemplates($tpl_rep);

function change_phpini($dir, $ext, $rep) {
    global $found;
    if (!is_dir($dir))
        return;

    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file == 'php.ini') {
                $c = file_get_contents($dir . $file);
                $found = false;
                foreach ($rep as $from => $to) {
                    if (strstr($c, $from)) {
                        $c = str_ireplace($from, $to, $c);
                        $found = true;
                        #  echo $dir . $file . ' = ' . $from . '<br>';
                    }
                }
                if ($found == true) {
                    file_put_contents($dir . $file, $c);
                }
            }
        }
    }
}

$dirs = array();

if ($handle = opendir(CMS_ROOT . 'includes/modules/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_dir(CMS_ROOT . 'includes/modules/' . $file)) {
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/';
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/admin/';
        }
    }
}


$rep = array(
    'ioncube_loader_lin_5.4.so' => 'ioncube_loader_lin_5.6.so',
    '5-54STABLE' => '5-56STABLE',
    );
foreach ($dirs as $dir) {
    change_phpini($dir, 'php.ini', $rep);
}


function change_cms_php($src, $replace) {
    $dh = opendir($src);
    if (!is_dir($src))
        return;
    while (($file = readdir($dh)) !== false) {

        if (is_file($src . '/' . $file) && file_exists($src . '/' . $file) && $file != 'change.php' && strstr($file, '.php')) {
            $content = file_get_contents($src . '/' . $file);
            $found = false;
            foreach ($replace as $key => $value) {
                if (strstr($content, $key)) {
                    $found = true;
                    break;
                }
            }
            if ($found == true) {
                #   echo $src . '/' . $file . '<br>';
                $content = strtr($content, $replace);
                file_put_contents($src . '/' . $file, $content);
            }


        }
    }
}
$dirs = array();
$dh = opendir(CMS_ROOT . 'includes/modules/');
while (($file = readdir($dh)) !== false) {
    if (is_dir(CMS_ROOT . 'includes/modules/' . $file) && $file != '.' && $file != '..') {
        $cx = CMS_ROOT . 'includes/modules/' . $file . '/config.xml';
        if (file_exists($cx)) {
            $xml_modul = simplexml_load_file($cx);
            if (isset($xml_modul->module->settings->private) && strtolower($xml_modul->module->settings->private) == 'true') {
                $dirs[] = CMS_ROOT . 'includes/modules/' . $file;
                if (is_dir(CMS_ROOT . 'includes/modules/' . $file . '/admin'))
                    $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/admin';
                if (is_dir(CMS_ROOT . 'includes/modules/' . $file . '/setup'))
                    $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/setup';
            }
        }
    }
}

$rep = array(
    '$db_zugriff' => '$kdb',
    'new db_zugriff' => 'new kdb',
    'gen_thumbnail' => 'kf::gen_thumbnail',
    'gen_approve_icon' => 'kf::gen_approve_icon',
    'gen_edit_icon' => 'kf::gen_edit_icon',
    'gen_ax_edit_icon' => 'kf::gen_ax_edit_icon',
    'gen_plus_icon' => 'kf::gen_plus_icon',
    'gen_eye_icon' => 'kf::gen_eye_icon',
    'gen_chart_icon' => 'kf::gen_chart_icon',
    'gen_del_img_tagADMIN' => 'kf::gen_del_img_tag_for_admin',
    'gen_clone_icon' => 'kf::gen_clone_icon',
    'gen_std_icon' => 'kf::gen_std_icon',
    'gen_del_icon_reload' => 'kf::gen_del_icon_reload',
    'gen_del_icon_ajax' => 'kf::gen_del_icon_ajax',
    'gen_del_icon' => 'kf::gen_del_icon',
    'ECHORESULTCOMPILED' => 'kf::ECHORESULTCOMPILED',
    'translate_admin' => 'kf::translate_admin',
    'gen_meta_keywords' => 'kf::gen_meta_keywords',
    'gen_plain_text_content' => 'kf::gen_plain_text_content',
    # 'thumb' => 'kf::thumb',
    'gen_admin_sub_btn' => 'kf::gen_admin_sub_btn',
    'make_xls_format' => 'kf::make_xls_format',
    'get_all_column_types' => 'kf::get_all_column_types',
    'build_module_select' => 'kf::build_module_select',
    'break_to_newline' => 'kf::break_to_newline',
    'gen_inputtext_field' => 'kf::gen_inputtext_field',
    'convert_url_query' => 'kf::convert_url_query',
    'validate_module' => 'kf::validate_module',
    'get_remote_template' => 'kf::get_remote_template',
    'echo_template' => 'kf::echo_template',
    'smarty_html_compile' => 'kf::smarty_html_compile',
    'ECHORESULTPURADMIN' => 'kf::output',
    'TBL_ADMINLOG' => 'TBL_CMS_ADMINLOG',
    'tc_request_class' => 'kcontrol_class',
    'tcException' => 'kException',
    'MenuNodes' => 'cms_tree_class',
    'GetDBData' => 'load_data_by_sql',
    '->GetDBData' => '->load_data_by_sql',
    'CreateNestedArrayCleanCMS' => 'create_nested_array',
    '->CreateNestedArrayCleanCMS' => '->create_nested_array',
    'CreateNestedArrayCMSSmarty' => 'create_nested_array_to_smarty',
    '->CreateNestedArrayCMSSmarty' => '->create_nested_array_to_smarty',
    'setCMSCatItem' => 'set_tree_item_option',
    'CreateResult' => 'create_result_and_array',
    '->CreateResult' => '->create_result_and_array',
    'create_result_and_arrayByArray' => 'create_result_and_array_by_array',
    'parent::tc_class' => 'parent::__construct',
    'tc_class' => 'keimeno_class',
    'tc_request_class' => 'kcontrol_class');
foreach ($dirs as $dir) {
    change_cms_php($dir, $rep);
}


$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_ssl_admin'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_token'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fbwp_appsecret'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fbwp_appid'");


@unlink(CMS_ROOT . 'admin/admin_functions.php');
@unlink(CMS_ROOT . 'admin/access.php');
@unlink(CMS_ROOT . 'includes/smarty.inc.php');
@unlink(CMS_ROOT . 'includes/classcreate.inc.php');
@unlink(CMS_ROOT . 'includes/session.inc.php');
@unlink(CMS_ROOT . 'admin/file_manager.php');
@unlink(CMS_ROOT . 'admin/a-bild.jpg');
@unlink(CMS_ROOT . 'admin/cms.php');
@unlink(CMS_ROOT . 'admin/unpack.php');
@unlink(CMS_ROOT . 'includes/modules/calendar/calendar.inc.php');
@unlink(CMS_ROOT . 'includes/modules/content/content.init.inc.php');

$this->db->query("UPDATE " . TBL_CMS_MENU . " SET php='run.php?epage=cmsupt.inc&section=update&cmd=initupd' WHERE id=71");

$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='rediconn' WHERE gid=6");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=6");

if (!class_exists('moduleman_class'))
    require_once (CMS_ROOT . 'admin/inc/modulman.class.php');

try {
    #$MOD = new moduleman_class();
    #$MOD->uninstall_mod('calendar');
    app_class::set_mod_active_status('calendar', false);
}
catch (Exception $e) {
    // throw new kException($class_name . ' ' . $e->getMessage());
}
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/calendar/');

# finalize
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;
