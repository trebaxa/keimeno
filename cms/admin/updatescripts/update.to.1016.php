<?PHP
$version = '1.0.1.6';

$result = $this->db->query("SELECT * FROM ".TBL_CMS_NEWSGROUPS." ");
while($row = $this->db->fetch_array_names($result)){
	$tpl_rep['{TMPL_NEWSGROUP_'.intval($row['id']).'}'] =  	'<% assign var=newslist value=$TMPL_NEWSGROUP_'.$row['id'].' %>';
}
$this->replaceInTemplates($tpl_rep);

$tpl_rep=array();
$result = $this->db->query("SELECT * FROM ".TBL_CMS_NEWSGROUPS." ");
while($row = $this->db->fetch_array_names($result)){
	$tpl_rep['<% assign var=newslist value=$TMPL_NEWSGROUP_ %>'] =  	'<% assign var=newslist value=$TMPL_NEWSGROUP_'.$row['id'].' %>';
}
$this->replaceInTemplates($tpl_rep);

# TEMPLATE UPDATE TO SMARTY
$tpl_rep = array(
	'file="menu_tree.tpl"' 			=> 'file=$globl_tree_template',
	'$CMS_PATH' => '$PATH_CMS',
	'{TMPL_PHPSELF}'=>'<% $PHPSELF %>',
	'{FLAGS}'=>'<% include file="flagtable.tpl" %>'	
	);
$this->replaceInTemplates($tpl_rep);	

delete_file(CMS_ROOT . 'admin/newsman.php');


$this->convert_64_to_utf8(TBL_CMS_NEWSCONTENT, 'content', 'id');	
$this->db->query("UPDATE ".TBL_CMS_CONFIG." SET wert='".$version."'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE','CMS version has been updated to '.$version);
?>