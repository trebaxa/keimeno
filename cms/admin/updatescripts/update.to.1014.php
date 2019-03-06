<?PHP
$version = '1.0.1.4';


# TEMPLATE UPDATE TO SMARTY
$tpl_rep = array(
	'<TMPL_START_TOPLEVEL>' 			=> '<% include "file=toplevel.tpl" %>',
	'<TMPL_END_TOPLEVEL>' 				=> '',
	'<div id="toplevel_menu"><ul>{TMPL_TOPLEVEL_MENU_HOR}</ul></div>' => '',
	'{TMPL_SHOPPROTECT}' 		=> '<% $document_protection %>');
$this->replaceInTemplates($tpl_rep);		
$tpl_rep = array(
	'{META_KEYWORDS}' => '<% $meta.keywords %>',
	'{FM_OWNER}' 			=> '<% $meta.owner %>',
	'{FM_NAME}' 			=> '<% $meta.company %>',
	'{META_TITLE}' 		=> '<% $meta.title %>',
	'{FM_DOMAIN}' 		=> '<% $meta.domain %>',
	'content="Global"'=> 'content="<% $meta.distribution %>"',	
	'{META_DESC}' 		=> '<% $meta.description %>',
	'iso-8859-1' 			=> 'UTF-8',
	'de,deutsch,german' => '<% $meta.contentlang %>',
	'content="3 days"' => 'content="<% $meta.revisit %>"',
	'INDEX, FOLLOW' => '<% $meta.robots %>',
	'{TMPL_GOOGLE_ANALYTICS}' => '<% $meta.robots %>'
);

$this->replaceInTemplates($tpl_rep, 1);

//setze Mitarbeit auf löschen die nicht zum System gehören
$this->db->query("UPDATE ".TBL_CMS_ADMINS . " SET del=0 WHERE id<>100 AND id<>1");	

// Oeffentliche Gruppe muss vorhanden sein
if (get_data_count(TBL_CMS_RGROUPS,'id','id=1000')==0) {
	  $this->db->query("INSERT INTO ".TBL_CMS_RGROUPS." SET id=1000, groupname='".utf8_encode('Öffentliche Gruppe')."',cms_approval=1");
}
	
# HTA Update 07.09.2009
#$HTA_CLASS_CMS = new hta_class($this->db,$this->gbl_config,TBL_CMS_HTA, PATH_CMS,TBL_CMS_GBLCONFIG);
#$HTA_CLASS_CMS->changeAll(',');
	#	die(x);
$this->db->query("UPDATE ".TBL_CMS_CONFIG." SET wert='".$version."'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE','CMS version has been updated to '.$version);
?>