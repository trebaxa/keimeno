<?PHP

$version = '1.0.2.3';
@DEFINE('TBL_CMS_WEBCAMS', TBL_CMS_PREFIX . 'webcam_dates');
@DEFINE('TBL_CMS_WEBCAMS_CONTENT', TBL_CMS_PREFIX . 'webcam_content');
@DEFINE('TBL_CMS_WEBCAMFILES', TBL_CMS_PREFIX . 'webcam_files');
@DEFINE('TBL_CMS_WEBCAM_WL', TBL_CMS_PREFIX . 'webcam_wl');
@DEFINE('TBL_CMS_WEBCAM_WLL', TBL_CMS_PREFIX . 'webcam_wll');
@DEFINE('TBL_CMS_WEBCAM_DIALOG', TBL_CMS_PREFIX . 'webcam_dialog');
@DEFINE('TBL_CMS_WEBCAM_SHOUT', TBL_CMS_PREFIX . 'webcam_shoutbox');

@DEFINE('ARTICLES_PATH', 'file_data/articles/');
@DEFINE('TBL_CMS_ARTICLES', TBL_CMS_PREFIX . 'art_index');
@DEFINE('TBL_CMS_ARTICLESCONTENT', TBL_CMS_PREFIX . 'art_content');
@DEFINE('TBL_CMS_ARTTREE', TBL_CMS_PREFIX . 'art_tree');
@DEFINE('TBL_CMS_ARTTREECONTENT', TBL_CMS_PREFIX . 'art_treecontent');
@DEFINE('TBL_CMS_ARTFILES', TBL_CMS_PREFIX . 'art_files');

$this->db->query("UPDATE " . TBL_CMS_WEBCAMS . " SET c_gm_place=place");

$result = $this->db->query("SELECT * FROM " . TBL_CMS_ARTFILES . " WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $fs = filesize(CMS_ROOT . ARTICLES_PATH . $row['f_file']);
    $this->db->query("UPDATE " . TBL_CMS_ARTFILES . " SET f_size='" . $fs . "'WHERE id=" . $row['id'] . " LIMIT 1");
}

$result = $this->db->query("SELECT * FROM " . TBL_CMS_ARTICLES . " WHERE a_icon LIKE '%jpeg%'");
while ($row = $this->db->fetch_array_names($result)) {
    $fs = CMS_ROOT . ARTICLES_PATH . $row['a_icon'];
    $RetVal = explode('.', $FILES['aicon']['name']);
    $file_extention = strtolower($RetVal[count($RetVal) - 1]);
    $new_file = str_replace('.jpeg', '.jpg', $fs);
    rename($fs, $new_file);
    $this->db->query("UPDATE " . TBL_CMS_ARTICLES . " SET a_icon='" . basename($new_file) . "'WHERE id=" . $row['id'] . " LIMIT 1");
}

delete_file(CMS_ROOT . 'admin/intmit.php');
delete_file(CMS_ROOT . 'admin/mit_groups.php');


$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='' WHERE config_name='tw_consumerkey'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='' WHERE config_name='tw_consumersecret'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='twitter' WHERE config_name='tw_screenname'");

if (get_data_count(TBL_CMS_ADMINMATRIX, 'id', "1") == 0) {
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE 1");
    while ($row = $this->db->fetch_array_names($result)) {
        $this->db->query("INSERT INTO " . TBL_CMS_ADMINMATRIX . " SET em_type='LNG',em_relid=1,em_mid=" . $row['id']);
        $this->db->query("INSERT INTO " . TBL_CMS_ADMINMATRIX . " SET em_type='LNG',em_relid=2,em_mid=" . $row['id']);
    }
}

$this->db->query("UPDATE " . TBL_CMS_RGROUPS . " SET groupname='{LBL_PUBLIC_GROUP}' WHERE id=1000");
$this->execSQL("INSERT INTO " . TBL_CMS_ADMIN_ROLES . " SET rl_name='{LBL_MASTER_ROLE}',id=1");

$this->execSQL("UPDATE " . TBL_CMS_ADMINMATRIX . " SET em_relid=em_countryid WHERE em_countryid>0");
$this->execSQL("UPDATE " . TBL_CMS_ADMINMATRIX . " SET em_relid=em_langid WHERE em_langid>0");
$this->execSQL("ALTER TABLE " . TBL_CMS_ADMINMATRIX . " DROP `em_langid`");
$this->execSQL("ALTER TABLE " . TBL_CMS_ADMINMATRIX . " DROP `em_countryid`");
#$SICRAWLER = new search_index_class();
#$SICRAWLER->clean_sites();
$this->execSQL("UPDATE " . TBL_CMS_ADMINMATRIX . " SET em_compid=1 WHERE em_compid=0 AND em_type='COMCOU'");
$this->execSQL("DELETE " . TBL_CMS_ADMINMATRIX . " WHERE em_type='COM'");

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>