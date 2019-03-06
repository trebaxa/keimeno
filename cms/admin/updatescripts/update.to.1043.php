<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2018 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      11.05.2018
 * @version    1.0.4.3
 */

$version = '1.0.4.3';

$files = array(
    CMS_ROOT . 'admin/tpl/kreg.addcustomer.tpl',
    CMS_ROOT . 'admin/tpl/kreg.files.table.tpl',
    CMS_ROOT . 'admin/tpl/kreg.files.upload.tpl',
    CMS_ROOT . 'admin/tpl/kreg.form.regedit.tpl',
    CMS_ROOT . 'admin/tpl/kreg.foto.tpl',
    CMS_ROOT . 'admin/tpl/kreg.main.tpl',
    CMS_ROOT . 'admin/tpl/kreg.table.tpl',
    CMS_ROOT . 'includes/modules/flickr/Phlickr/Tests/Offline/sample_gallery_for_tests.zip',
    CMS_ROOT . 'admin/update.class.php',
    CMS_ROOT . 'aws/sdk-1.4.2/_samples/video.mp4');

foreach ($files as $file) {
    @unlink($file);
}

$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET is_list='groupname|id|g_createdate|g_order' WHERE config_name='gal_album_sort'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='g_order' WHERE config_name='gal_album_sort' AND config_value='manuell'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET gid='10' WHERE config_name='cms_hash_password'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='1' WHERE config_name='debug_mode'");

dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'modernizr'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'jquery_version_script'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'google_kontonummer'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'google_maps'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'ssl_proxy'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'ssl_certified'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'opt_shop_root'));

dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'cal_gm_enable'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'cal_gm_height'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'cal_gm_zoom'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'cal_gm_width'));

$this->execSQL("ALTER TABLE `" . TBL_CMS_TEMPLATES . "` DROP `ssl_active`");
$this->execSQL("ALTER TABLE `" . TBL_CMS_TEMPLATES . "` DROP `ssl_active`");
$this->execSQL("ALTER TABLE `" . TBL_CMS_PREFIX . "cal_dates` DROP `c_gm_place`");
$this->execSQL("ALTER TABLE `" . TBL_CMS_PREFIX . "flxtpl_vars` CHANGE `v_value` `v_value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$this->execSQL("ALTER TABLE `" . TBL_CMS_PREFIX . "temp_matrix` CHANGE `tm_content` `tm_content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");


dao_class::update_table(TBL_CMS_TEMPLATES, array('layout_group' => '1'), array('id' => 280));
dao_class::update_table(TBL_CMS_TEMPLATES, array('layout_group' => '1'), array('id' => 750));

dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Neueste Events'), array('id' => 280));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Sorted View '), array('id' => 70));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Editor'), array('id' => 310));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Inlay'), array('id' => 80));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Detail'), array('id' => 710));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Jahr'), array('id' => 730));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Monate'), array('id' => 720));
dao_class::update_table(TBL_CMS_TEMPLATES, array('description' => 'Kalendar - Table'), array('id' => 740));


#$this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET g_order='' WHERE approval=0");
self::delete_dir_with_subdirs(CMS_ROOT . 'phpmodule');
self::delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/webcam');

$tpl_rep = array(
    'E-Mail (E-Mail)' => 'E-Mail (<%$gbl_config.adr_service_email%>)',
    'Telefax (Faxnummer)' => 'Telefax (<%$gbl_config.adr_fax%>)',
    ' type="text/javascript"' => '',
    'gbl_config.adr_ort' => 'gbl_config.adr_town',
    '<% $gbl_config.jquery_version_script %>' => 'jquery-1.11.1.min.js',
    '<%$gbl_config.jquery_version_script%>' => 'jquery-1.11.1.min.js',
    '<% foreach from=$js_files item=jsfile%><script src="<%$jsfile%>"></script><%/foreach%>' => '',
    "$(\'.js-disclaimer-check\').prop(\'checked\')==true" => "$(\'.js-disclaimer-check:checked\').length==3");

$this->replaceInTemplates($tpl_rep);
$this->delete_dir_with_subdirs('../../includes/');
#exec('cd ..;cd ..; rm -rf ./includes');
$log = new log_class();
$log->clean_log();

$T = new template_class();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE description LIKE 'WiZiQ%'");
while ($row = $this->db->fetch_array_names($result)) {
    $T->delete_template($row['id'], true);
}


# ERST TRUE SETZEN, WENN RELEASE FERTIG IST UND VERSION GESETZT WERDEN SOLL
$NO_FAILURE = TRUE;
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $VERSION);
