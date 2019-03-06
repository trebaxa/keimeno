<?PHP
$version = '1.0.2.4';

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
file_put_contents(CMS_ROOT . '.htaccess', $hta);
}

$hta = file_get_contents(CMS_ROOT . '.htaccess');
if (!strstr($hta,'block illegal calls')) {
	$hta.='
# block illegal calls
<Files *.tpl.php>
    deny from all
</Files>

<Files *.tpl>
    deny from all
</Files>

<Files *.inc.php>
    deny from all
</Files>

	';
file_put_contents(CMS_ROOT . '.htaccess', $hta);
}
$this->execSQL("UPDATE ".TBL_CMS_PREFIX."blog_config SET value=true WHERE name='dbNames'");

$result = $this->db->query("SELECT * FROM ".TBL_CMS_ADMINS." WHERE 1");
while($row = $this->db->fetch_array_names($result)){
	if (get_data_count(TBL_CMS_ADMINMATRIX,'id',"em_type='LNG' AND em_relid=1 AND em_mid=" . $row['id'])==0) $this->db->query("INSERT INTO ".TBL_CMS_ADMINMATRIX." SET em_type='LNG',em_relid=1,em_mid=" . $row['id']);
	if (get_data_count(TBL_CMS_ADMINMATRIX,'id',"em_type='LNG' AND em_relid=2 AND em_mid=" . $row['id'])==0)$this->db->query("INSERT INTO ".TBL_CMS_ADMINMATRIX." SET em_type='LNG',em_relid=2,em_mid=" . $row['id']);
}

if (get_data_count(TBL_CMS_TEMPLATES,'id',"is_startsite=1")==0) $this->db->query("UPDATE ".TBL_CMS_TEMPLATES." SET is_startsite=1 WHERE id=7");

$this->execSQL("INSERT INTO ".TBL_CMS_CUSTGROUPS." (id,groupname,cms_approval) VALUES (1000,'".'{LA_PUBLICGROUP}'."',1)");
$this->execSQL("INSERT INTO ".TBL_CMS_CUSTGROUPS." (id,groupname,cms_approval) VALUES (1100,'".'{LA_MEMBERGROUP}'."',1)");
$this->execSQL("UPDATE ".TBL_CMS_CUSTGROUPS." SET groupname='{LA_MEMBERGROUP}' WHERE id=1100");
$this->execSQL("UPDATE ".TBL_CMS_CUSTGROUPS." SET groupname='{LA_PUBLICGROUP}' WHERE id=1000");

$this->execSQL("UPDATE ".TBL_CMS_LANG_ADMIN." SET local='de' WHERE id=1");
$this->execSQL("UPDATE ".TBL_CMS_LANG_ADMIN." SET local='en',approval=0 WHERE id=2");

$result = $this->db->query("SELECT * FROM ".TBL_CMS_LAND." WHERE 1 ORDER BY land");
while($row = $this->db->fetch_array_names($result)){
	$country_ids[] = $row['id'];
}

$W = new employee_class();
$result = $this->db->query("SELECT * FROM ".TBL_CMS_ADMINS." WHERE 1");
while($row = $this->db->fetch_array_names($result)){
	$W->set_country_responsibilities($row['id']);
}
global $gbl_config;
if ($gbl_config['mod_wilinku']==1) {
$this->db->query("UPDATE ".TBL_CMS_WLU_VA." set yt_stock='YT' WHERE yt_stock=''");
$this->db->query("UPDATE ".TBL_CMS_WLU_VA." SET yt_videosrcname='youTube' WHERE yt_isadhoc=0 ");
$this->db->query("DELETE FROM ".TBL_CMS_WLU_VCMATRIX." WHERE vc_countryid=0 ");

$W = new wlu_collector_class();
$W->cat_breadcrumb_update(0);
$result = $this->db->query("SELECT * FROM ".TBL_CMS_WLU_VPQUERY." WHERE 1");
while($row = $this->db->fetch_array_names($result)){
	$W->update_cat_count_by_query($row['id']);
}



$W = new wlu_employee_class();
$result = $this->db->query("SELECT * FROM ".TBL_CMS_ADMINS." WHERE 1");
while($row = $this->db->fetch_array_names($result)){
	$W->set_country_responsibilities($row['id']);
}

$this->db->query("UPDATE ".TBL_CMS_WLU_VA." SET yt_jw_videourl=yt_watchpageurl WHERE 1");
$W = new wlu_collector_class();
$result = $this->db->query("SELECT * FROM ".TBL_CMS_WLU_VA." WHERE 1");
while($row = $this->db->fetch_array_names($result)){
	$R['yt_jw_videourl'] = $W->VI->vimeourl2videourl($row['yt_videoid']);
	$this->db->query("UPDATE ".TBL_CMS_WLU_VA." SET yt_jw_videourl='".$R['yt_jw_videourl']."' WHERE yt_videoid='".$row['yt_videoid']."'");
}

} else {

 $cfg=array("wlu_vm_consumerkey"=>"","wlu_vm_secret"=>"");
 foreach ($cfg as $key => $wert) {
  $this->execSQL("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='".$wert."' WHERE config_name='".$key."' LIMIT 1"); 
 }

}


$tpl_rep = array(
	'<% include file=dcmetatags.tpl %>"' => '<% include file="dcmetatags.tpl" %>
	<script language="JavaScript" type="text/javascript" src="<% $PATH_CMS %>js/jquery-1.5.2.js"></script>
	'
	);
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);	
$this->replaceInTemplates($tpl_rep);	

$this->db->query("UPDATE ".TBL_CMS_CONFIG." SET wert='".$version."'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE','CMS version has been updated to '.$version);
?>