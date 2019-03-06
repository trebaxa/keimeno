<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      09.02.2016
 * @version    1.0.4.0
 */

$version = '1.0.4.0';

# Password change
if (keimeno_class::get_config_value('hash_secret') == "") {
    $hash_secret = md5(uniqid());
    $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $hash_secret . "' WHERE config_name='hash_secret'");
}
else {
    $hash_secret = keimeno_class::get_config_value('hash_secret');
}

$result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE pass_backup='' AND passwort<>''");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_CUST . " SET pass_backup='" . $row['passwort'] . "' WHERE kid=" . $row['kid']);
    $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . password_hash($row['passwort'] . $hash_secret, PASSWORD_BCRYPT, array("cost" => 10)) .
        "' WHERE kid=" . $row['kid']);
}

$result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE mi_pass_backup=''");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_ADMINS . " SET mi_pass_backup='" . $row['passwort'] . "' WHERE id=" . $row['id']);
    $this->db->query("UPDATE " . TBL_CMS_ADMINS . " SET passwort='" . password_hash($row['passwort'] . $hash_secret, PASSWORD_BCRYPT, array("cost" => 10)) .
        "' WHERE id=" . $row['id']);
}


#remove forum from standards
global $HTA_CLASS_CMS;
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=58");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=59");
include_once (CMS_ROOT . 'admin/inc/htaedit.class.php');
$HTA = new htaedit_class();
$HTA->buildHTACCESS();

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES .
    " SET description='Forum Start',layout_group=1,tpl_name='forum_start',block_name='',c_type='T',gbl_template=1,modident='forum' WHERE id=40");
$this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET linkname='Forum-Start',t_tpl_name='forum_start' WHERE tid=40");

# obsolete config key
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_flickr'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_fbwp'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_wiziq'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_twitter'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_wilinku'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_forum'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_webcam'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_onlinesheet'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_otimer'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_vimeo'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_youtube'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_sellform'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_onlinerequests'");

$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='forum' WHERE gid=35");
#$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET gid='0' WHERE modident='forum'");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=35");

$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='memindex' WHERE gid=54");
#$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET gid='0' WHERE modident='forum'");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=54");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='memindex' WHERE config_name='login_mode'");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=6");


$this->db->query("UPDATE " . TBL_CMS_CUST . " SET anrede_sign=geschlecht");

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE modident='forum'");

$FLEX = new flextemp_master_class();
$flex_arr = $FLEX->load_flx_tpls();
foreach ($flex_arr as $row) {
    $this->execSQL("ALTER TABLE `" . $row['f_table'] . "` ADD `ds_settings` BLOB NOT NULL");
}

@unlink(CMS_ROOT.'sitemap.php');
self::delete_dir_with_subdirs(CMS_ROOT . 'js/tiny_mce/');
self::delete_dir_with_subdirs(CMS_ROOT . 'js/tiny_mce4012/');
# finalize
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;
