<?PHP

$version = '1.0.3.3';

if ($this->gbl_config['mod_wilinku'] == 0 && !strstr($_SERVER['HTTP_HOST'], "wilinku.com")) {
    $this->delDirWithSubDirs(CMS_ROOT . 'includes/modules/wilinku');
}

@unlink(CMS_ROOT . 'includes/modules/tcblog/mod.cronjob.inc.php');
@unlink(CMS_ROOT . 'includes/modules/tw/twcallback.php');
@unlink(CMS_ROOT . 'includes/modules/tw/twcallbackadmin.php');
@unlink(CMS_ROOT . 'includes/modules/tw/init.inc.php');
@unlink(CMS_ROOT . 'includes/login.php');
@unlink(CMS_ROOT . 'includes/hta.inc.php');
@unlink(CMS_ROOT . 'admin/cfield.class.php');
@unlink(CMS_ROOT . 'admin/cfields.php');
@unlink(CMS_ROOT . 'admin/theme.class.php');
@unlink(CMS_ROOT . 'admin/tpl/xmlsm.tpl');
@unlink(CMS_ROOT . 'includes/kreg.php_');
@unlink(CMS_ROOT . 'crypt_adr.php');

$this->delDirWithSubDirs(CMS_ROOT . 'includes/modules/xmlsitemap');
$this->delDirWithSubDirs(CMS_ROOT . 'includes/modules/tw/twitteroauth');

if (!strstr($_SERVER['HTTP_HOST'], "mentoria.tv")) {
    $this->delDirWithSubDirs(CMS_ROOT . 'includes/modules/wiziq');
}

$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/pgrfilemanager');
$this->delDirWithSubDirs(CMS_ROOT . 'js/tiny_mce');

$tpl_rep = array(
    '$pin_items' => '$BLOG.items',
    'name="aktion" value="login"' => 'name="cmd" value="login"',
    'value="login" name="aktion"' => 'name="cmd" value="login"',
    'name="aktion" value="sendpass"' => 'name="cmd" value="sendpass"',
    '$themeobj.' => '$PAGEOBJ.',
    '<% $PATH_CMS %>register.html' => '<%$HTA_CMSFIXLINKS.EB_URL%>',
    '/register.html' => '<%$HTA_CMSFIXLINKS.EB_URL%>',
    '<%$PATH_CMS%>login.html' => '<%$HTA_CMSFIXLINKS.EC_URL%>',
    '/login.html' => '<%$HTA_CMSFIXLINKS.EC_URL%>',
    '<%$PATH_CMS%>logout.html' => '<% $HTA_CMSFIXLINKS.ED_URL %>',
    '/logout.html' => '<%$HTA_CMSFIXLINKS.EC_URL%>',
    '{VALUE_plz}' => '<% $CONTACTF.values.plz|sthsc %>',
    '{VALUE_strasse}' => '<% $CONTACTF.values.nachname|sthsc %>',
    '{VALUE_ort}' => '<% $CONTACTF.values.ort|sthsc %>',
    '{VALUE_handy}' => '<% $CONTACTF.values.handy|sthsc %>',
    '{VALUE_email}' => '<% $CONTACTF.values.email|sthsc %>');
$this->replaceInTemplates($tpl_rep);

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET php='modules/memindex/memindex.inc' WHERE php='login'");

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET PHP='kreg', modident='' WHERE id=960");

include_once (CMS_ROOT . 'admin/inc/layout.class.php');
if (get_data_count(TBL_CMS_LAYOUTFILES, '*', "l_file='layout.css'") == 0) {
    $FORM = array('l_file' => 'layout.css');
    insert_table(TBL_CMS_LAYOUTFILES, $FORM);
}

$this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=0");

$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE t_htalinklabel=''");
while ($row = $this->db->fetch_array_names($result)) {
    $k = 0;
    $row['t_htalinklabel'] = preg_replace("/[^0-9a-zA-Z_-]/", "", $this->format_file_name($row['linkname']));
    if ($row['t_htalinklabel'] == "") {
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $row['tid']);
        $row['t_htalinklabel'] = preg_replace("/[^0-9a-zA-Z_-]/", "", $this->format_file_name($T['description']));
    }
    if ($row['t_htalinklabel'] == "") {
        $row['t_htalinklabel'] = 'Page';
    }
    $org_label = $row['t_htalinklabel'];
    while (get_data_count(TBL_CMS_TEMPCONTENT, "*", "t_htalinklabel='" . $row['t_htalinklabel'] . "'") > 0) {
        $k++;
        $row['t_htalinklabel'] = $org_label . '_' . $k;
    }
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET t_htalinklabel='" . $row['t_htalinklabel'] . "' WHERE id=" . $row['id']);
}

#htaccess
global $HTA_CLASS_CMS;
$HT = $this->db->query_first("SELECT * FROM " . TBL_CMS_HTA . " WHERE id=997");
$HT2 = $this->db->query_first("SELECT * FROM " . TBL_CMS_HTA . " WHERE id=29");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=997");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=29");
include_once (CMS_ROOT . 'admin/inc/htaedit.class.php');
$HTA = new htaedit_class();
$HTA->buildHTACCESS();
$htastr = "\n# rewrite fix 01-2014\nRewriteRule ^" . $HT['hta_prefix'] . "/(.*).html http://www." . FM_DOMAIN . "/$1.html [R=301,L]\nRewriteRule ^" . $HT2['hta_prefix'] .
    $HT2['hta_delimeter1'] . "(.*)" . $HT2['hta_delimeter2'] . "(.*).html http://www." . FM_DOMAIN . "/$1.html [R=301,L]\n";
if (!strstr($this->gbl_config['hta_specialtext_first'], "# rewrite fix 01-2014")) {
    $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $htastr . $this->db->real_escape_string($this->gbl_config['hta_specialtext_first']) .
        "'WHERE config_name='hta_specialtext_first' LIMIT 1");
}

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES .
    " SET description='Search Formular',tpl_name='website_searchform',block_name='',c_type='T',gbl_template=1,modident='websitesearch' WHERE id=10000");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES .
    " SET description='Search Formular (index.)',tpl_name='website_searchform_index',block_name='',c_type='T',gbl_template=1,modident='websitesearch' WHERE id=60");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES .
    " SET description='Search Result (index.)',tpl_name='indexsearch',block_name='',c_type='T',gbl_template=1,modident='websitesearch' WHERE id=50");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET block_name='',c_type='T',gbl_template=1,modident='websitesearch' WHERE id=10001");
DEFINE('TBL_CMS_GALGROUP', TBL_CMS_PREFIX . 'gal_groups');
$this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET g_createdate=" . time() . " WHERE g_createdate=0");


$fname = CMS_ROOT . 'layout.css';
$fc = file_get_contents($fname);
if (!strstr($fc, '#savedresult {')) {
    $fc .= '
#savedresult {
    width:300px;
    z-index:1000;
    height:auto;
    position:absolute;
    display:none;
}
';
    file_put_contents($fname, $fc);
}

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);

?>