<?PHP

$version = '1.0.1.5';

$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE hta_description=''");
global $HTA_CLASS_CMS;
$result = $this->db->query("SELECT * FROM " . TBL_CMS_HTA);
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_HTA . " SET hta_tmpllink='" . $HTA_CLASS_CMS->genAscciiJoker($row['id']) . "' WHERE id=" . $row['id']);
}
#$HTA_CLASS_CMS->buildHTACCESS();


$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>