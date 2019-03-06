<?PHP
$version = '1.0.1.7';

# TEMPLATE UPDATE TO SMARTY
$tpl_rep = array(
	'$CMS_PATH' => '$PATH_CMS',
	'{TMPL_PHPSELF}'=>'<% $PHPSELF %>',
	'{FLAGS}'=>'<% include file="flagtable.tpl" %>',
	' src="js' => ' src="<%$PATH_CMS%>js'
	);
$this->replaceInTemplates($tpl_rep);	

$tpl_rep = array(
	'{TMPL_GBL_CONTENT}' => '
	<% include file="webcontent_top.tpl"%>
{TMPL_GBL_CONTENT}
<% include file="webcontent_footer.tpl"%>'
	);
$this->replaceInTemplates($tpl_rep);	

$this->execSQL("INSERT INTO ".TBL_CMS_CUSTGROUPS." (id,groupname,cms_approval) VALUES (1000,'".utf8_encode('Öffentliche Gruppe')."',1)");
$this->execSQL("INSERT INTO ".TBL_CMS_CUSTGROUPS." (id,groupname,cms_approval) VALUES (1100,'".utf8_encode('Mitglieder/Kunden')."',1)");
delete_file(CMS_ROOT . 'admin/calendar.php');
$this->convert_64_to_utf8(TBL_CMS_CALENDAR_CONTENT, 'content', 'id');	
$this->db->query("UPDATE ".TBL_CMS_CONFIG." SET wert='".$version."'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE','CMS version has been updated to '.$version);
?>