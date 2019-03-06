<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      09.02.2016
 * @version    1.0.4.2
 */

$version = '1.0.4.2';

$files = array(
    CMS_ROOT . 'admin/tpl/kreg.addcustomer.tpl',
    CMS_ROOT . 'admin/tpl/kreg.files.table.tpl',
    CMS_ROOT . 'admin/tpl/kreg.files.upload.tpl',
    CMS_ROOT . 'admin/tpl/kreg.form.regedit.tpl',
    CMS_ROOT . 'admin/tpl/kreg.foto.tpl',
    CMS_ROOT . 'admin/tpl/kreg.main.tpl',
    CMS_ROOT . 'admin/tpl/kreg.table.tpl',
    CMS_ROOT . 'includes/modules/flickr/Phlickr/Tests/Offline/sample_gallery_for_tests.zip',
    CMS_ROOT . 'aws/sdk-1.4.2/_samples/video.mp4');

foreach ($files as $file) {
    @unlink($file);
}

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET xml_sitemap=0 WHERE approval=0");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET is_list='groupname|id|g_createdate|g_order' WHERE config_name='gal_album_sort'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='g_order' WHERE config_name='gal_album_sort' AND config_value='manuell'");

dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'modernizr'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'jquery_version_script'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'google_kontonummer'));
dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'google_maps'));


#dao_class::db_delete(TBL_CMS_MAILTEMP, array('id'=>980));

#$this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET g_order='' WHERE approval=0");
self::delete_dir_with_subdirs(CMS_ROOT . 'phpmodule');
self::delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/webcam');

$tpl_rep = array(
    'E-Mail (E-Mail)' => 'E-Mail (<%$gbl_config.adr_service_email%>)',
    'Telefax (Faxnummer)' => 'Telefax (<%$gbl_config.adr_fax%>)',
    'gbl_config.adr_ort' => 'gbl_config.adr_town',
    #"$('.js-disclaimer-check').prop('checked')==true" => "$('.js-disclaimer-check:checked').length==3"
    );

$this->replaceInTemplates($tpl_rep);
#exec('cd ..;cd ..; rm -rf ./includes');

# finalize
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;
