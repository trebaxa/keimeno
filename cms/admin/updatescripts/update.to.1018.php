<?PHP

$version = '1.0.1.8';
global $GRAPHIC_FUNC;
$GALLERY = new gallery_class();
$GALLERY->delete_files_not_in_db();

$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET use_all_lang=0 WHERE tid=" . $row['id']);
}

$tpl_rep = array('$customer.kid==0' => '$customer.kid<=0');
$this->replaceInTemplates($tpl_rep);

$this->convert_64_to_utf8(TBL_CMS_TEMPCONTENT, 'linkname', 'id');
$this->convert_64_to_utf8(TBL_CMS_TEMPCONTENT, 'content', 'id');
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>