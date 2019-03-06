<?PHP

$version = '1.0.2.5';

if (!file_exists(CMS_ROOT . 'admin/.htaccess')) {
    $hta = 'RewriteEngine On
Options +FollowSymLinks 

RewriteCond %{QUERY_STRING} http[:%] [NC]
RewriteRule .* /-http- [F,NC]
RewriteRule http: /-http- [F,NC]

RewriteRule ^welcome.html /admin/run.php?epage=welcome.inc&%{QUERY_STRING} [L] 
RewriteRule ^logout.html /admin/index.php?aktion=logout [L]
RewriteRule ^login.html /admin/index.php?&%{QUERY_STRING} [L] 
';
    file_put_contents(CMS_ROOT . 'admin/.htaccess', $hta);
}

$hta = file_get_contents(CMS_ROOT . 'admin/.htaccess');
if (!strstr($hta, 'RewriteRule ^welcome.html')) {
    $hta .= '
RewriteRule ^welcome.html /admin/run.php?epage=welcome.inc&%{QUERY_STRING} [L]
';
    file_put_contents(CMS_ROOT . 'admin/.htaccess', $hta);
}

$hta = file_get_contents(CMS_ROOT . 'layout.css');
if (!strstr($hta, '.facebook-like-button')) {
    $hta .= '
.facebook-like-button {
  padding-top: 1px;
  margin-right: 10px;
}

#share-content {
 width:100%;
 float:left;
}

.twitter-button {
  margin-right: -5px;
}

.float-left {
  float: left;
}

.float-right {
  float: right;
}
';
    file_put_contents(CMS_ROOT . 'layout.css', $hta);
}

@mkdir(CMS_ROOT . 'images/scr');
$tpl_rep = array('.img_moosrc' => '.img_src', );
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);
$this->replaceInTemplates($tpl_rep);
$result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " ORDER BY s_order");
while ($row = $this->db->fetch_array_names($result)) {
    if (!is_dir(CMS_ROOT . 'smarty/templates_c/' . $row['local']))
        mkdir(CMS_ROOT . 'smarty/templates_c/' . $row['local'], 0777);
}
$result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " ORDER BY s_order");
while ($row = $this->db->fetch_array_names($result)) {
    if (!is_dir(CMS_ROOT . 'smarty/templates_c/' . $row['local']))
        mkdir(CMS_ROOT . 'smarty/templates_c/' . $row['local'], 0777);
}

$this->db->query("UPDATE " . TBL_CMS_LANG_ADMIN . " SET local='de' WHERE local='' AND id=1");
$this->db->query("UPDATE " . TBL_CMS_LANG_ADMIN . " SET local='en' WHERE local='' AND id=2");

$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='de' WHERE local='' AND id=1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='en' WHERE local='' AND id=2");

if (!is_dir(CMS_ROOT . 'file_server/template'))
    mkdir(CMS_ROOT . 'file_server/template', 0755);


$ADD_INDEX = array(
    TBL_CMS_GALCON => 'pic_id',
    TBL_CMS_GLGRCON => 'g_id',
    TBL_CMS_TEMPCONTENT => 'tid',
    TBL_CMS_WEBCAMS_CONTENT => 'nid',
    TBL_CMS_WEBCAMS => 'mid',
    TBL_CMS_WEBCAMS => 'group_id',
    TBL_CMS_CALENDAR_CONTENT => 'g_id',
    TBL_CMS_CALENDAR => 'mid',
    TBL_CMS_CALENDAR => 'group_id',
    TBL_CMS_CALENDAR_CONTENT => 'nid',
    TBL_CMS_CALENDARFILES => 'f_nid',
    TBL_CMS_ARTTREECONTENT => 'ac_aid',
    TBL_CMS_ARTFILES => 'f_aid',
    TBL_CMS_ARTTREECONTENT => 'g_id',
    TBL_CMS_ARTFILES => 'a_group_id',
    TBL_CMS_CUSTGROUPS => 'kid',
    TBL_CMS_CUSTGROUPS => 'gid',
    TBL_CMS_FORUMTHREADS => 'f_tid',
    TBL_CMS_FORUMTHREADS => 'f_fid',
    TBL_CMS_FORUMTHEMES => 't_fid',
    TBL_CMS_FORUMFILES => 'f_threadid',
    TBL_CMS_FORUMTHREADS => 'f_kid',
    TBL_CMS_NEWSCONTENT => 'nid',
    TBL_CMS_NEWSFILES => 'f_nid',

    );
foreach ($ADD_INDEX as $table => $index)
    $this->execSQL("ALTER TABLE " . $table . " ADD INDEX ( " . $index . " )");


$result = $this->db->query("SELECT kid FROM " . TBL_CMS_CUSTTOGROUP);
$U = new member_class(1);
while ($row = $this->db->fetch_array_names($result)) {
    if (get_data_count(TBL_CMS_CUST, 'kid', "kid=" . $row['kid']) == 0) {
        $U->delete_customer($row['kid']);
    }
}
unset($U);

$result = $this->db->query("SELECT kid,COUNT(kid) FROM " . TBL_CMS_CUSTTOGROUP . " WHERE gid=1100 group by kid HAVING COUNT(kid)>1");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("DELETE FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid=" . $row['kid'] . " AND gid=1100");
    $this->db->query("INSERT INTO " . TBL_CMS_CUSTTOGROUP . " SET kid=" . $row['kid'] . ",gid=1100");
}


$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>