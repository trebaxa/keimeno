<?PHP

$version = '1.0.1.3';

function utf8fixit($updobj) {
    $ver_info = $updobj->db->query_first("SELECT * FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='VERSION' LIMIT 1");
    $local_version = str_replace(".", "", $ver_info['wert']);
    if ($local_version <= '1013')
        return;
    //BASE64 Felder
    $updobj->utf8fix(array(
        TBL_CMS_TEMPCONTENT => array('linkname', 'content'),
        TBL_CMS_MAILTEMP_CONTENT => array('content', 'email_subject'),
        TBL_CMS_NEWSCONTENT => array('content')), 'id', TRUE);

    // Normale Felder mit ID
    $updobj->utf8fix(array(
        TBL_CMS_TEMPLATES => array('description'),
        TBL_CMS_NEWSGROUPS => array('groupname'),
        TBL_CMS_TEMPCONTENT => array(
            'content_plain',
            'meta_desc',
            'meta_keywords'),
        TBL_CMS_MAILTEMP => array(
            'inhalt',
            'title',
            'betreff'),
        TBL_CMS_LANG => array('langarray'),
        TBL_CMS_LANG_ADMIN => array('langarray'),
        TBL_CMS_LANG_CUST => array('langarray'),
        TBL_CMS_RGROUPS => array('groupname'),
        TBL_CMS_COLLECTION => array('col_name'),
        TBL_CMS_GALPICS => array(
            'pic_desc',
            'pic_title',
            'fotoquelle'),
        TBL_CMS_CONFGROUPS => array('catgroup'),
        TBL_CMS_NEWSCONTENT => array('introduction', 'title'),
        TBL_CMS_GUESTBOOK => array('kname', 'feedback'),
        TBL_CMS_TABLIST_CONTENT => array('title'),
        TBL_CMS_TABLIST_GROUP => array('groupname'),
        TBL_CMS_CUSTOMFIELDSCONT => array('content'),
        TBL_CMS_CALENDAR_CONTENT => array('introduction'),
        TBL_CMS_CALENDAR_GCON => array('g_title', 'g_content'),
        TBL_CMS_ADMINGROUPS => array('mgname'),
        TBL_CMS_TOPLEVEL => array('description'),
        TBL_CMS_TPLCON => array('level_name'),
        TBL_CMS_EMAILER => array(
            'e_subject',
            'e_content',
            'e_unsubscribe'),
        TBL_CMS_TABLIST => array('tab_name')));
    $updobj->utf8fix(array(TBL_CMS_GBLCONFIG => array('config_value', 'config_desc')), 'config_name');
    $updobj->utf8fix(array(TBL_CMS_CUST => array(
            'nachname',
            'vorname',
            'strasse',
            'ort',
            'firma',
            'bank',
            'firma_inhaber')), 'kid');
}

function changeKoll($updobj) {
    $ver_info = $updobj->db->query_first("SELECT * FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='VERSION' LIMIT 1");
    $local_version = str_replace(".", "", $ver_info['wert']);
    if ($local_version < '1013')
        return;

    $result = $updobj->db->query("SHOW TABLE STATUS FROM " . $updobj->db->database . ";");
    while ($row = mysqli_fetch_row($result)) {
        if (strstr($row[0], TBL_CMS_PREFIX)) {
            $tables[] = $row[0];
            if (strstr($row[14], 'latin1'))
                $updobj->execSQL("ALTER TABLE " . $row[0] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
            #echoARR($row);
        }
    }
    foreach ($tables as $key => $table_name) {
        $column_types = $updobj->GetAllColTYPEs($table_name);
        foreach ($column_types as $column_name => $column_TYPE) {
            if (substr_count($column_TYPE['TYPE'], 'varchar') || substr_count($column_TYPE['TYPE'], 'text')) {
                if (substr_count($column_TYPE['COLLATION'], 'latin1')) {
                    $updobj->execSQL(" ALTER TABLE " . $table_name . " CHANGE " . $column_name . " " . $column_name . " " . $column_TYPE['TYPE'] .
                        " CHARACTER SET utf8 COLLATE utf8_general_ci ");
                    #echoARR($column_TYPE);
                }
            }
        }
    }
}

changeKoll($this);
utf8fixit($this);
$this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE content='' AND linkname=''");
$result = $this->db->query("SELECT id,content FROM " . TBL_CMS_TEMPCONTENT . " WHERE content<>'' AND content_plain=''");
while ($row = $this->db->fetch_array_names($result)) {
    $words_plain = self::gen_plain_text($row['content']);
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content_plain='" . $this->db->real_escape_string($words_plain) . "' WHERE id=" . $row['id'] .
        " LIMIT 1");
}
#$result=$this->db->query("SELECT id,content FROM ".TBL_CMS_TEMPCONTENT . " WHERE meta_desc='' OR meta_keywords=''");
#while($row = $this->db->fetch_array_names($result)){
# $this->db->query("UPDATE ".TBL_CMS_TEMPCONTENT . " SET meta_desc='".$this->db->real_escape_string(formatMETA(base64_decode($row['content'])))."',meta_keywords='".$this->db->real_escape_string(genMetaKeywords($row['content']))."',content_plain='".$this->db->real_escape_string(genPlainTextContent($row['content']))."' WHERE id=".$row['id']." LIMIT 1");
#}
# Set Layout 1
if (get_data_count(TBL_CMS_LAYOUT, 'id', "1") == 0)
    $this->db->query("INSERT INTO " . TBL_CMS_LAYOUT . " SET layout_name='Standard Layout'");
if (!file_exists('std_layout.tpl')) {
    $this->import_single_layout('std_layout');
}
# TEMPLATE UPDATE TO SMARTY
/*
'src="/' => 'src="<% $PATH_CMS %>',   		
'href="/' => 'href="<% $PATH_CMS %>',   		
*/
$tpl_rep = array(
    './js/' => '<% $PATH_CMS %>js/',
    './images/' => '<% $PATH_CMS %>images/',
    '"captcha.php"' => '"<% $PATH_CMS %>captcha.php"',
    '"layout.css"' => '"<% $PATH_CMS %>layout.css"',
    '<% PATH_CMS %>' => '<% $PATH_CMS %>',
    'images/icon.ico' => 'favicon.ico',
    '"index.php' => '"<% $PATH_CMS %>index.php');
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE admin=1 AND gbl_template=1");
while ($row = $this->db->fetch_array_names($result)) {
    $tpl_rep[self::format_file_name($row['description']) . '.tpl'] = $row['tpl_name'] . '.tpl';
}
$result = $this->db->query("SELECT C.* FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C WHERE C.tid=T.id");
while ($row = $this->db->fetch_array_names($result)) {
    $html = base64_decode($row['content']);
    $found = false;
    foreach ($tpl_rep as $suchwort => $ersetzewort) {
        $suchwort = stripslashes($suchwort);
        if (strstr(strtolower($html), strtolower($suchwort))) {
            $html = str_ireplace($suchwort, stripslashes($ersetzewort), $html, $rep_count);
            $found = true;
        }
    }
    if ($found == TRUE)
        $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string(base64_encode($html)) . "' WHERE id='" . $row['id'] .
            "' LIMIT 1");
}
if (get_data_count(TBL_CMS_PERMISSIONS, 'perm_tid', '1') == 0) {
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE c_type='T'"); // AND C.content LIKE '%".base64_encode($inlay_obj['block_name'])."%'");
    while ($row = $this->db->fetch_array_names($result)) {
        $this->db->query("INSERT INTO " . TBL_CMS_PERMISSIONS . " SET perm_tid=" . $row['id'] . ", perm_group_id=1000");
    }
}
#CMSSet XML setzen
#global $SMILE;
include_once ('../includes/crjob.class.php');
$crj_obj = new crj_class();
#$crj_obj->db = $this->db;
#$crj_obj->gbl_config = $this->gbl_config;
#$crj_obj->smile = $SMILE;
$crj_obj->genCMSSetXml();
# Admin Template Content anlegen der fehlt. SEHR WICHTIG
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " T WHERE T.admin=1");
while ($row = $this->db->fetch_array_names($result)) {
    if (get_data_count(TBL_CMS_TEMPCONTENT, 'id', "tid=" . $row['id']) == 0) {
        $this->import_single_template($row['id']);
    }
}
# SMARTY Templates anlegen
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT .
    " TC WHERE T.id=TC.tid AND T.admin=1 AND T.gbl_template=1"); // AND C.content LIKE '%".base64_encode($inlay_obj['block_name'])."%'");
while ($row = $this->db->fetch_array_names($result)) {
    $filename = "../smarty/templates/" . $this->formatLink($row['description']) . ".tpl";
    delete_file($filename);
    $filename = "../smarty/templates/" . $row['tpl_name'] . ".tpl";
    $fp = fopen($filename, "w+");
    fwrite($fp, base64_decode($row['content']));
    fclose($fp);
}
# FAVICON 13.05.2009
if (file_exists('../images/icon.ico')) {
    copy('../images/icon.ico', '../favicon.ico');
}

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>