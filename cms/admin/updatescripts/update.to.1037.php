<?PHP

$version = '1.0.3.7';


$tpl_rep = array('$mobildevice' => '$mobiledevice');
$this->replaceInTemplates($tpl_rep);

#$this->remove_adminmenu_id(10231);
$this->execSQL("ALTER TABLE `" . TBL_CMS_TEMPCONTENT . "` ADD `t_lastchange` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");

$this->execSQL("ALTER TABLE `" . TBL_CMS_PAGENF . "` DROP PRIMARY KEY ");
$this->execSQL("DELETE FROM `" . TBL_CMS_PAGENF . "` WHERE pnf_hash=''");
$this->execSQL("ALTER TABLE `" . TBL_CMS_PAGENF . "` ADD PRIMARY KEY ( `pnf_hash` )");

$this->execSQL("ALTER TABLE `" . TBL_FLXTDV . "` CHANGE `v_col` `v_col` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");

$this->execSQL("ALTER TABLE `" . TBL_FLXTDV . "` DROP `v_value`");
$this->execSQL("DELETE FROM `" . TBL_CMS_HTA . "` WHERE hta_prefix=''");


$files = array(
    'gallery.class.php',
    'classcreate.inc.php',
    'class_db_zugriff.php',
    'admin/cms.php',
    'admin/a-bild.jpg',
    'mysqli.php',
    'SMARTY_TEMPDIRglobal_framework.tpl',
    'includes/otimer.inc',
    'includes/config.php',
    'includes/modules/content/admin/tpl/website.themegal.tpl');
foreach ($files as $file) {
    if (is_file(CMS_ROOT . $file))
        @unlink(CMS_ROOT . $file);
}
copy(CMS_ROOT . 'admin/images/gal_defekt.jpg', CMS_ROOT . 'images/gal_defekt.jpg');

$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/js/ace112/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/font-awesome-4.2.0/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/font-awesome-4.3.0/');


$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET is_list='groupname|id|g_createdate|manuell' WHERE config_name='gal_album_sort'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_socialstream'");


$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 AND modident<>''");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET t_tpl_name='" . $row['tpl_name'] . "' WHERE tid=" . $row['id']);
}

if (!strstr($_SERVER['HTTP_HOST'], 'trebaxa.com') && $this->gbl_config['fbwp_pageid'] == '224684924224240') {
    $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='' WHERE config_name='fb_token' 
        OR config_name='fbwp_pageid' 
        OR config_name='fbwp_appsecret' 
        OR config_name='fb_fanpagename'
        OR config_name='fbwp_appid'");
}

# fixing permission to global templates
$CUSTGROUP = array(1000, 1100); # Mitglieder, oeffentliche
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("DELETE FROM " . TBL_CMS_PERMISSIONS . " WHERE perm_tid=" . $row['id']);
    if (is_array($CUSTGROUP)) {
        foreach ($CUSTGROUP as $key => $group_id) {
            $this->db->query("INSERT INTO " . TBL_CMS_PERMISSIONS . " SET perm_tid=" . $row['id'] . ", perm_group_id=" . (int)$group_id);
        }
    }
}

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET approval=1 WHERE gbl_template=1");
#$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE description LIKE '%blog%'");
$this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=0");

if (!is_dir(FILE_ROOT))
    mkdir(FILE_ROOT, 0755);
if (!is_dir(FILE_ROOT . 'file_server'))
    mkdir(FILE_ROOT . 'file_server', 0755);
if (!is_dir(FILE_ROOT . DOWNCENTER))
    mkdir(FILE_ROOT . DOWNCENTER, 0755);
if (is_dir(CMS_ROOT . 'file_server/downloads/'))
    $this->recurse_copy(CMS_ROOT . 'file_server/downloads/', FILE_ROOT . 'downloadcenter/');

$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/fbwp/facebook-php-sdk-/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/fbwp/facebook-php-sdk-org/');

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE modident='fb' OR id=9790 OR id=9780");
try {
   # if (!class_exists('moduleman_class'))
   #     require_once (CMS_ROOT . 'admin/inc/modulman.class.php');
    #$MOD = new moduleman_class();
    #$MOD->uninstall_mod('fb');
    app_class::set_mod_active_status('fb', false);
}
catch (Exception $e) {
    // throw new kException($class_name . ' ' . $e->getMessage());
}
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/fb/');

#require_once (CMS_ROOT . 'admin/inc/template.class.php');
$TEMPL = new template_class();
$TEMPL->delete_template(9790);
$TEMPL->delete_template(9780);

$this->set_default_file_permissions();

$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/js/ace119/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/js/ace119-sik/');

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;

?>