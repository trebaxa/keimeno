<?PHP

$version = '1.0.1.9';
$tpl_rep = array(
    'src="<%$PATH_CMS%>' => 'src="' . PATH_CMS,
    'src="<% $PATH_CMS %>' => 'src="' . PATH_CMS,
    ' $news.date ' => ' $news.ndate ',
    '$customer.kid == 0' => '$customer.kid <= 0',
    '$customer.kid==0' => '$customer.kid<=0',
    '$news.date' => '$news.ndate',
    '<% $news_obj.date %>' => '<% $news_obj.ndate %>');
$this->replaceInTemplatesOnlyCustomers($tpl_rep);

$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='de' WHERE id=1 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='en' WHERE id=2 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='es' WHERE id=4 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='it' WHERE id=6 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='jp' WHERE id=7 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='cn' WHERE id=3 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='fr' WHERE id=5 LIMIT 1");

$this->db->query("ALTER TABLE " . TBL_CMS_CUSTPERM . "  DROP PRIMARY KEY,   ADD PRIMARY KEY(page_id,group_id,module)");

@unlink(CMS_ROOT . 'admin/inc/webcam.class.php');
@unlink(CMS_ROOT . 'admin/inc/webcam.inc.php');
@unlink(CMS_ROOT . 'includes/webcam.inc.php');

@unlink(CMS_ROOT . 'admin/inc/articles.class.php');
@unlink(CMS_ROOT . 'admin/inc/articles.inc.php');
@unlink(CMS_ROOT . 'includes/articles.inc.php');
@unlink(CMS_ROOT . 'includes/articles.init.inc.php');

@unlink(CMS_ROOT . 'admin/inc/event.class.php');
@unlink(CMS_ROOT . 'admin/inc/calendar.inc.php');
@unlink(CMS_ROOT . 'includes/events.inc.php');


$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET use_all_lang=0 WHERE tid=" . $row['id'] . " LIMIT 1");
}

$this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailsend=1");

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>