<?PHP

$version = '1.0.3.1';

$tpl_rep = array(
    '{TMPL_SHOPPROTECT}' => '<% $document_protection %>',
    '{FLAGS}' => '<% include file="flagtable.tpl" %>',
    '<span style="font-size:8pt">erzeugt in: <% $sidegentime %>sek</span>' => '',
    'erzeugt in: <% $sidegentime %>sek' => '',
    '<input type="hidden" name="page" value="10001">' => ' <input name="page" type="hidden" value="10001"><input type="hidden" name="cmd" value="fulltextsearch">',
    '<input type="hidden" name="page" value="50">' => '<input value="50" type="hidden" name="page" ><input type="hidden" name="cmd" value="indexsearch">',
    '<input type="hidden" name="aktion" value="dosend">' => '<input type="hidden" name="cmd" value="crossdomain_send">',
    ' src="<% $PATH_CMS %>default_js.js"' => ' src="<% $PATH_CMS %>js/default_js.js"',
    'TMPL_GBL_CONTENT' => 'TMPL_SPOT_1',
    '<% include file=$PAGEOBJ.header_tpl %>' => '');
$this->replaceInTemplates($tpl_rep);


$fname = CMS_ROOT . 'layout.css';
$fc = file_get_contents($fname);
if (!strstr($fc, '.cmsbtnhref')) {
    $fc .= '
.global_black {
display:none;
position:fixed;
width:100%;
height:100%;
opacity:0.3;
background-color:#000;
}

#closeicon-divframe {
margin:-20px -30px 0 0;
}

.divframe {
width:400px;
border:3px solid #959595;
color:#000;
background-color:#EFEFEF;
-moz-border-radius:10px;
-webkit-border-radius:10px;
-khtml-border-radius:10px;
border-radius:10px;
position:absolute;
left:50%;
top:50%;
margin-left:0;
text-align:left;
display:none;
z-index:1600;
padding:6px 12px;
}

.cmsbtnhref {
background: linear-gradient(to bottom, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 100%) repeat scroll 0 0 #209DC2;
border-color: #41627C;
color: #FFFFFF;
padding: 0 16px;
text-shadow: 0 -1px 0 #1F425E;
font-size:16px;
-moz-border-radius:3px;
-webkit-border-radius:3px;
-khtml-border-radius:3px;
border-radius:3px;
padding:6px 10px 6px 10px; 
}';
    file_put_contents($fname, $fc);
}

@unlink(CMS_ROOT . 'admin/guestbook.php');
@unlink(CMS_ROOT . "includes/sendpost.class.php");
@unlink(CMS_ROOT . "includes/engine.php");

# include update to smarty 3
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE content LIKE '%.tpl %>%'");
while ($row = $this->db->fetch_array_names($result)) {
    $c = str_replace('.tpl %>', '.tpl"%>', $row['content']);
    $c = str_replace('.tpl%>', '.tpl"%>', $c);
    $c = str_replace('<% include file=', '<% include file="', $c);
    $c = str_replace('<% include file=""', '<% include file="', $c);
    $c = str_replace('include file="$', 'include file=$', $c);

    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string($c) . "' WHERE id=" . $row['id']);
}

# defun update to smarty 3
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE content LIKE '% defun %'");
while ($row = $this->db->fetch_array_names($result)) {
    $i = 0;
    preg_match_all("=defun(.*)/defun=siU", $row['content'], $tpl_tag);
    foreach ($tpl_tag[0] as $key => $defun_html) {
        $i++;
        $name = 'recur' . $row['tid'] . $i;
        $org_defun = $defun_html;
        if (ereg('list=([[:graph:]]+)', $defun_html, $found)) {
            $php_tree = trim(str_replace(array('%>', '<%'), '', $found[1]));
        }
        $defun_html = str_replace('list=' . $php_tree, '', $defun_html);
        $defun_html = str_replace('list=', 'items=', $defun_html);
        $defun_html = str_replace('from=$list', 'from=$items', $defun_html);
        $defun_html = str_replace('/defun', '/function%>', $defun_html);
        $defun_html = str_replace('<%fun', '<%call', $defun_html);
        $defun_html = str_replace('<% fun', '<%call', $defun_html);
        $defun_html = str_replace('defun ', 'function ', $defun_html);
        if (ereg('name="([[:graph:]]+)"', $defun_html, $found)) {
            $old_name = trim(str_replace(array('%>', '<%'), '', $found[1]));
        }
        $defun_html = str_replace($old_name, $name, $defun_html);
        $defun_html .= '<% call name=' . $name . ' items=' . $php_tree . ' ';
        $row['content'] = str_replace($org_defun, $defun_html, $row['content']);
        #echo ($row['content']).'<hr>';
    }
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string($row['content']) . "' WHERE id=" . $row['id']);
}

# Smarty 3 Install
$this->delDirWithSubDirs(CMS_ROOT . 'smarty');
$list = array('smarty.tar.gz');
$this->mass_tar_overwrite($list);

# deinstalliere alten blog
$result = $this->db->query("SHOW TABLE STATUS FROM " . $this->db->database . ";");
while ($row = mysqli_fetch_row($result)) {
    if (strstr($row[0], TBL_CMS_PREFIX) && strstr($row[0], '_blog_')) {
        $this->execSQL("DROP TABLE " . $row[0]);
    }
}

#repair admin include
$tplroot = CMS_ROOT . 'admin/tpl/';
$dh = opendir($tplroot);
while (false !== ($filename = readdir($dh))) {
    if ($filename != '.' && $filename != '..' && is_file($tplroot . $filename) && strstr($filename, '.tpl')) {
        $save = false;
        $html = file_get_contents($tplroot . $filename);
        $c = str_replace('.tpl %>', '.tpl"%>', $html);
        $c = str_replace('.tpl%>', '.tpl"%>', $c);
        $c = str_replace('<% include file=', '<% include file="', $c);
        $c = str_replace('<% include file=""', '<% include file="', $c);
        $c = str_replace('include file="$', 'include file=$', $c);
        $c = str_replace('<% include file="divframe.top.tpl" %>', '', $c);
        if (strpos($c, 'file="')) {
            $save = true;
        }
        if (strpos($c, 'defun')) {
            preg_match_all("=defun(.*)/defun=siU", $c, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $defun_html) {
                $i++;
                $name = 'recur' . $row['tid'] . $i;
                $org_defun = $defun_html;
                if (ereg('list=([[:graph:]]+)', $defun_html, $found)) {
                    $php_tree = trim(str_replace(array('%>', '<%'), '', $found[1]));
                }
                $defun_html = str_replace('list=' . $php_tree, '', $defun_html);
                $defun_html = str_replace('list=', 'items=', $defun_html);
                $defun_html = str_replace('from=$list', 'from=$items', $defun_html);
                $defun_html = str_replace('/defun', '/function%>', $defun_html);
                $defun_html = str_replace('<%fun', '<%call', $defun_html);
                $defun_html = str_replace('<% fun', '<%call', $defun_html);
                $defun_html = str_replace('defun ', 'function ', $defun_html);
                if (ereg('name="([[:graph:]]+)"', $defun_html, $found)) {
                    $old_name = trim(str_replace(array('%>', '<%'), '', $found[1]));
                }
                $defun_html = str_replace($old_name, $name, $defun_html);
                $defun_html .= '<% call name=' . $name . ' items=' . $php_tree . ' ';
                $c = str_replace($org_defun, $defun_html, $c);
                $save = true;
                #echo ($row['content']).'<hr>';
            }
        }

        if ($save == true)
            file_put_contents($tplroot . $filename, $c);
    }
}

#fixes
$this->execSQL("UPDATE " . TBL_CMS_TEMPLATES . " SET admin = '0' WHERE id =3 LIMIT 1 ;");
$this->execSQL("UPDATE " . TBL_CMS_TEMPLATES . " SET module_id = 'contactform',modident = 'contactform' WHERE id=6 LIMIT 1;");
$this->execSQL("UPDATE " . TBL_CMS_TEMPLATES . " SET modident = 'wiziq' WHERE id =10060 LIMIT 1 ;");

#migrate content
$this->db->query("TRUNCATE TABLE " . TBL_CMS_TEMPMATRIX);
$this->db->query("OPTIMIZE TABLE " . TBL_CMS_TEMPMATRIX);
$level_result = $this->db->query("SELECT TC.* FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES .
    " T WHERE T.c_type='T' AND T.id=TC.tid AND T.gbl_template=0");
while ($row = $this->db->fetch_array_names($level_result)) {
    $FORM = array(
        'tm_cid' => $row['id'],
        'tm_tid' => $row['tid'],
        'tm_order' => 100,
        'tm_content' => $row['content'],
        'tm_type' => (($row['no_fck'] == 1 || (strpos($row['content'], '<%') && strpos($row['content'], '%>')) || strpos($row['content'], 'file="') || strpos($row['content'],
            '$cmd') || strpos($row['content'], '$aktion') || strpos($row['content'], '$section')) ? 'C' : 'W'));
    $FORM = $this->real_escape($FORM);
    $id = insert_table(TBL_CMS_TEMPMATRIX, $FORM);
}

#migrate inlays
DEFINE('TBL_CMS_INLAYCONNECT', TBL_CMS_PREFIX . 'inlay_connect');

$result = $this->db->query("SELECT TH.description,T.lang_id,T.content, IC.*, T.id AS TMCID, TH.id AS TID 
        FROM " . TBL_CMS_TEMPCONTENT . " T, " . TBL_CMS_INLAYCONNECT . " IC, " . TBL_CMS_TEMPLATES . " TH 
        WHERE TH.id=T.tid AND T.tid=IC.i_iid 
        ORDER BY i_order");
while ($row = $this->db->fetch_array_names($result)) {
    $T = $this->db->query_first("SELECT T.description,C.*,C.id AS TMCID FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C 
            WHERE C.lang_id=" . $row['lang_id'] . " 
            AND C.tid=T.id 
            AND T.id=" . $row['i_tid']);
    $FORM = array(
        'tm_cid' => $T['TMCID'],
        'tm_tid' => $row['i_tid'],
        'tm_content' => '',
        'tm_type' => 'P',
        'tm_order' => $i,
        'tm_plugname' => 'Inlay',
        'tm_plugid' => 'html_inlay',
        'tm_pluginfo' => $row['description'],
        'tm_refid' => $row['TMCID']);
    $i++;
    $FORM = $this->real_escape($FORM);
    if ($row['i_pos'] == 2) { // unten
        $FORM['tm_order'] = $k;
        $k += 100;
    }
    if ($FORM['tm_cid'] > 0) {
        $id = insert_table(TBL_CMS_TEMPMATRIX, $FORM);
        $W = new websites_class(array());
        $W->resaved_sorted($T['TMCID'],1);
    }
}

$level_result = $this->db->query("SELECT TC.*,TC.id AS TMCID FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES .
    " T WHERE T.c_type='T' AND T.id=TC.tid AND TC.t_header_html<>''");
while ($row = $this->db->fetch_array_names($level_result)) {
    $FORM = array(
        'tm_cid' => $row['id'],
        'tm_tid' => $row['tid'],
        'tm_order' => 0,
        'tm_content' => $row['t_header_html'],
        'tm_type' => (($row['no_fck'] == 1 || (strpos($row['content'], '<%') && strpos($row['content'], '%>')) || strpos($row['content'], 'file="') || strpos($row['content'],
            '$cmd') || strpos($row['content'], '$aktion') || strpos($row['content'], '$section')) ? 'C' : 'W'));
    $FORM = $this->real_escape($FORM);
    if ($FORM['tm_cid'] > 0) {
        $id = insert_table(TBL_CMS_TEMPMATRIX, $FORM);
        $W = new websites_class(array());
        $W->resaved_sorted($T['TMCID'],1);
    }
}

$this->db->query("update " . TBL_CMS_TEMPLATES . " SET module_id='content' WHERE module_id='' OR module_id='0' OR module_id='content_page'");
$this->db->query("TRUNCATE TABLE " . TBL_CMS_INLAYCONNECT);
$this->execSQL("ALTER TABLE " . TBL_CMS_TEMPMATRIX . " ADD INDEX ( tm_tid )");
$this->execSQL("ALTER TABLE " . TBL_CMS_TEMPMATRIX . " ADD INDEX ( tm_cid )");
$this->execSQL("ALTER TABLE " . TBL_CMS_TEMPLATES .
    " CHANGE module_id module_id VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'content'");
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>