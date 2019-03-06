<?PHP

$version = '1.0.2.0';
$tpl_rep = array(
    '$gbl_config.adr_lastname' => '$gbl_config.adr_surename',
    "<% if ($mdate.date_to!='') %> - <% $mdate.date_to %><% /if %>" => '',
    'src="/default_js.js"' => 'src="<% $PATH_CMS %>default_js.js"',
    'src="default_js.js"' => 'src="<% $PATH_CMS %>default_js.js"',
    '{TMPL_PHPSELF}' => '<% $PHPSELF %>',
    '{TMPL_SSL_LOGIN}' => '<% $HTA_SSLLINKS.EC_URL %>',
    'src="<%$language.icon%>"' => 'src="<%$PATH_CMS%><%$language.icon%>"',
    'src="js' => 'src="<% $PATH_CMS %>js');
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);
$this->replaceInTemplates($tpl_rep);

$this->db->query("UPDATE " . TBL_CMS_SMDEFS . " SET sm_key='' WHERE id=1 AND sm_key='-' LIMIT 1");

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>