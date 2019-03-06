<?PHP

$version = '1.0.2.9';

$result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLCONFIG . "  WHERE is_list != ''");
while ($row = $this->db->fetch_array_names($result)) {
    $row['is_list'] = str_replace(",", "|", $row['is_list']);
    $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET is_list='" . $row['is_list'] . "' WHERE config_name='" . $row['config_name'] . "'");
}

$this->db->query("DELETE FROM " . TBL_CMS_ADMIN_ROLEMATRIX . " WHERE am_roleid=1");
$result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINGROUPS . "  WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $arr = array('am_roleid' => 1, 'am_groupid' => $row['id']);
    insert_table(TBL_CMS_ADMIN_ROLEMATRIX, $arr);
}


$result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLCONFIG . "  WHERE is_list != ''");
while ($row = $this->db->fetch_array_names($result)) {
    $row['is_list'] = str_replace(",", "|", $row['is_list']);
    $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET is_list='" . $row['is_list'] . "' WHERE config_name='" . $row['config_name'] . "'");
}
$this->db->query("DELETE FROM " . TBL_CMS_APERMISSIONS . " WHERE p_name='exp_access_role'");

# final clean

global $CMSDATA;
$CR = new crj_class();
foreach ($CMSDATA->LANGS as $key => $rowl) {
    $CR->folderClean(CMS_ROOT . 'smarty/templates/' . $CMSDATA->LANGS[$rowl['id']]['local'] . '/', '.TPL_' . $CMSDATA->LANGS[$rowl['id']]['local'], 0, array('TEMP_'));

}
$CR->folderClean(CMS_ROOT . 'smarty/templates/', 'final clean', 0, array('TEMP_'));
unset($CR);

$tpl_rep = array('$PAGEOBJ.tagable' => '$PAGEOBJ.basis.tagable', );
$this->replaceInTemplates($tpl_rep);

include (CMS_ROOT . 'admin/inc/emailsman.class.php');
$EM = new emailman_class();
$EM->cmd_utf8_convert();
unset($EM);
  

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);


?>