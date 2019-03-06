<?PHP

$version = '1.0.3.4';

$this->execSQL("ALTER TABLE " . TBL_CMS_TOPLEVEL . " DROP `tree_type` ");

@unlink(CMS_ROOT . 'includes/hta.inc.php');
@unlink(CMS_ROOT . 'admin/kml.class.php');
@unlink(CMS_ROOT . 'admin/kml.inc.php');
@unlink(CMS_ROOT . 'admin/tpl/kml.tpl');
@unlink(CMS_ROOT . 'admin/os_fields.php');
@unlink(CMS_ROOT . 'admin/inc/tc.class.php_');
@unlink(CMS_ROOT . 'admin/layout.class.php');
@unlink(CMS_ROOT . 'admin/rgroup_edit.php');
@unlink(CMS_ROOT . 'admin/installtidy.php');
@unlink(CMS_ROOT . 'admin/own_pages.php');
@unlink(CMS_ROOT . 'admin/interpreter.inc.php');
@unlink(CMS_ROOT . 'admin/png.class.php');
@unlink(CMS_ROOT . 'admin/log_exec.php');
@unlink(CMS_ROOT . 'admin/png.ctrl.php');
@unlink(CMS_ROOT . 'admin/webexplorer.php');
@unlink(CMS_ROOT . 'admin/statistik.php');
@unlink(CMS_ROOT . 'admin/utf8_reconvert.php');
@unlink(CMS_ROOT . 'admin/upload.php');
@unlink(CMS_ROOT . 'admin/small_tasks.php');
@unlink(CMS_ROOT . 'includes/statistik.php');
@unlink(CMS_ROOT . 'admin/tpl/newsletter.tpl');
@unlink(CMS_ROOT . 'images/LINKS_ICO_20090402131439.jpg');
@unlink(CMS_ROOT . 'images/E_ICO_3003_6.jpg');
@unlink(CMS_ROOT . 'images/E_ICO_3003_10.jpg');
@unlink(CMS_ROOT . "includes/security.inc.php");
@unlink(CMS_ROOT . 'admin/inc/server_menue.php');
@unlink(CMS_ROOT . 'admin/inc/func_menue.php');


$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/ace');
$this->delDirWithSubDirs(CMS_ROOT . 'ckeditor');
$this->delDirWithSubDirs(CMS_ROOT . 'html_cache');

copy(CMS_ROOT . 'php.ini', CMS_ROOT . 'includes/modules/fbwp/php.ini');


$tpl_rep = array('<meta name="publisher" content="<% $meta.publisher %>">' => '');
$this->replaceInTemplates($tpl_rep);

$this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content = REPLACE(content, '{TMPL_GALINLAY_1}', '{TMPL_GALLERY_1}') WHERE tid=1 OR tid=9670 OR tid=990");
$this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content = REPLACE(content, '{TMPL_GALINLAY_2}', '{TMPL_GALLERY_2}') WHERE tid=1 OR tid=9670 OR tid=990");
$this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content = REPLACE(content, '{TMPL_GALINLAY_3}', '{TMPL_GALLERY_3}') WHERE tid=1 OR tid=9670 OR tid=990");


$folder = CMS_ROOT . 'includes/modules/';
$dh = opendir($folder);
while (false !== ($filename = readdir($dh))) {
    if (is_dir($folder . $filename)) {
        @unlink($folder . $filename . '/init.inc.php_');
    }
}

if ($this->gbl_config['mod_wilinku'] == 0 && !strstr($_SERVER['HTTP_HOST'], "wilinku.com")) {
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_MENU . " WHERE parent=112");
    while ($row = $this->db->fetch_array_names($result)) {
        $this->remove_adminmenu_id($row['id']);
    }
    $this->remove_adminmenu_id(112);
}

$arr = array(
    73,
    75,
    77,
    81,
    88,
    84,
    90,
    91,
    92,
    100,
    102,
    103,
    138,
    104,
    106,
    109,
    111,
    114,
    125,
    126,
    13);
foreach ($arr as $key => $id) {
    $this->remove_adminmenu_id($id);
}

include_once (CMS_ROOT . 'admin/inc/modulman.class.php');
$M = new moduleman_class();
$xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
foreach ($xml_modules->modules->children() as $module) {
    if (strval($module->settings->active) == 'true') {
        $M->install_admin_menu(strval($module->settings->id));
    }
    else {
        $M->uninstall_admin_menu(strval($module->settings->id));
    }
}

$this->execSQL("ALTER TABLE " . TBL_CMS_MENU . " AUTO_INCREMENT = 1000");

# News Update Matrix
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_content LIKE '{TMPL_NEWSINLAY_%}'");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_TEMPMATRIX . " SET tm_content='{TMPL_NEWSINLAY_" . $row['id'] . "}' WHERE id=" . $row['id']);
}
DEFINE('TBL_CMS_GLGRCON', TBL_CMS_PREFIX . 'gal_gcontent');
DEFINE('TBL_CMS_GALCON', TBL_CMS_PREFIX . 'gal_content');
DEFINE('TBL_CMS_GALGROUP', TBL_CMS_PREFIX . 'gal_groups');
DEFINE('TBL_CMS_GALPICS', TBL_CMS_PREFIX . 'gal_pics');
# Gallery
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_content LIKE '{TMPL_GALINLAY_%}'");
while ($row = $this->db->fetch_array_names($result)) {
    $PLUGIN_OPT = unserialize($row['tm_plugform']);
    $GAL_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALGROUP . " WHERE id=" . (int)$PLUGIN_OPT['galleryid']);
    $PLUGIN_OPT['image_width'] = $GAL_OBJ['thumb_width'];
    $PLUGIN_OPT['image_height'] = $GAL_OBJ['thumb_height'];
    $PLUGIN_OPT['thumb_type'] = $GAL_OBJ['thumb_type'];
    $PLUGIN_OPT['g_croppos'] = $GAL_OBJ['g_croppos'];
    $PLUGIN_OPT['default_order'] = $GAL_OBJ['default_order'];
    $PLUGIN_OPT['default_direc'] = $GAL_OBJ['default_direc'];
    $PLUGIN_OPT['image_count'] = 99;
    $this->db->query("UPDATE " . TBL_CMS_TEMPMATRIX . " SET tm_plugform='" . serialize($PLUGIN_OPT) . "',tm_content='{TMPL_GALINLAY_" . $row['id'] . "}' WHERE id=" .
        $row['id']);
}

# pre compiled cache fuellen
$WS = new websites_class();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $WS->save_compiled_version($row['id']);
}
tc_class::allocate_memory($WS);

#cleanup
$langcheck = array(
    21,
    4,
    5,
    6,
    7,
    3);
foreach ($langcheck as $id) {
    if (get_data_count(TBL_CMS_LANG, '*', "id=" . $id) == 0) {
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . $id . " OR lang_id=0");
    }
}

if ($this->gbl_config['mod_wilinku'] == 0 && !strstr($_SERVER['HTTP_HOST'], "wilinku.com")) {
    $openDir = opendir(CMS_ROOT . 'admin/tpl');
    while ($file = readdir($openDir)) {
        if (strstr($file, 'wlu_')) {
            @unlink(CMS_ROOT . 'admin/tpl/' . $file);
        }
    }
    closedir($openDir);
}

file_put_contents(CMS_ROOT . 'images/axloader.gif', $this->curl_get_data('http://www.cms.trebaxa.com/images/axloader.gif'));


$fname = CMS_ROOT . 'layout.css';
$fc = file_get_contents($fname);
if (!strstr($fc, 'axloader')) {
    $fc .= '
.axloader {
    width:16px;
}
';
    file_put_contents($fname, $fc);
}


$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
?>