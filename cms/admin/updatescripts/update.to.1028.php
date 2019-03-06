<?PHP

$version = '1.0.2.8';
/*
if (mod_installed('mod_wilinku')) {
    $C = new country_class();
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_LAND . " WHERE region_id=6 ORDER BY id");
    while ($row = $this->db->fetch_array_names($result)) {
        $L = $this->db->query_first("SELECT * FROM " . TBL_CMS_LAND . " WHERE id<>" . $row['id'] . " AND  land='" . $this->db->real_escape_string($row['land']) . "'");
        if ($L['id'] > 0) {
            $this->db->query("UPDATE " . TBL_CMS_WLU_COUNTRY_TO_CAT . " SET cm_countryid=" . $L['id'] . " WHERE cm_countryid=" . $row['id']);
            $this->db->query("UPDATE " . TBL_CMS_WLU_VIDEO_TO_COUNTRY . " SET vc_countryid=" . $L['id'] . " WHERE vc_countryid=" . $row['id']);
        }

        $C->delete_country($row['id']);
    }
    unset($C);
}
*/
$this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=0 OR lang_id=0");

$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=960");
while ($row = $this->db->fetch_array_names($result)) {
    if (!strstr($row['content'], 'cms_token')) {
        $tpl_rep = array('<input type="hidden" name="aktion" value="<% $kreg_aktion %>">' =>
                '<input name="aktion" type="hidden" value="<% $kreg_aktion %>"><input type="hidden" name="token" value="<% $cms_token %>">');
        $this->replaceInTemplates($tpl_rep, 960);
    }
}

$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=6");
while ($row = $this->db->fetch_array_names($result)) {
    if (!strstr($row['content'], 'cms_token')) {
        $tpl_rep = array('<input type="hidden" name="sec_key" value="{FORM_PAGE}">' =>
                '<input name="sec_key" type="hidden" value="{FORM_PAGE}"><input type="hidden" name="token" value="<% $cms_token %>">');
        $this->replaceInTemplates($tpl_rep, 6);
    }
}

$tpl_rep = array(
    '<% $PATH_CMS %>js/jquery-1.6.1.min.js' => '<% $PATH_CMS %>js/<% $gbl_config.jquery_version_script %>',
    '<% $gbl_config.tree_sublevel_sym %>' => $this->gbl_config['tree_sublevel_sym'],
    '<%$gbl_config.tree_sublevel_sym%>' => $this->gbl_config['tree_sublevel_sym'],
    '<%$gbl_config.bread_break_sym%>' => $this->gbl_config['bread_break_sym'],
    '<%$gbl_config.bread_break_sym%>' => $this->gbl_config['bread_break_sym']);
$this->replaceInTemplates($tpl_rep);

$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='tree_sublevel_sym' OR config_name='bread_break_sym'");
$this->db->query("ALTER TABLE " . TBL_CMS_MAILTEMP . " AUTO_INCREMENT=10000 ");


$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>