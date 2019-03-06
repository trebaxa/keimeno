<?PHP

$version = '1.0.3.0';
$this->db->query("DELETE FROM " . TBL_CMS_MAIL_RECIP_MATRIX . " WHERE rm_mid=1");
$result = $this->db->query("SELECT * FROM " . TBL_CMS_MAILTEMP . "  WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("INSERT INTO " . TBL_CMS_MAIL_RECIP_MATRIX . " SET rm_emid=" . $row['id'] . ", rm_mid=1");
}



$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='wlu_quoterecep_mail'");

$result = $this->db->query("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE b64_done=0");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_DOWNCENTER . " SET description='" . $this->db->real_escape_string(base64_decode($row['description'])) . "',title='" .
        $this->db->real_escape_string(base64_decode($row['title'])) . "',b64_done=1 WHERE id=" . $row['id']);
}

$result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE b64_done=0");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("UPDATE " . TBL_CMS_PIN_CONTENT . " SET introduction='" . $this->db->real_escape_string(base64_decode($row['introduction'])) . "',content='" .
        $this->db->real_escape_string(base64_decode($row['content'])) . "',b64_done=1 WHERE id=" . $row['id']);
}

@unlink(CMS_ROOT . 'admin/pinwand.php');
@unlink(CMS_ROOT . 'admin/tpl/pinwand.tpl');
@unlink(CMS_ROOT . 'includes/pinwand.class.php');
@unlink(CMS_ROOT . 'admin/os_fields.class.php');
@unlink(CMS_ROOT . 'admin/news.php');
@unlink(CMS_ROOT . 'admin/pinwand.php');
@unlink(CMS_ROOT . 'admin/email_template.php');
@unlink(CMS_ROOT . 'admin/os_fields.php');
@unlink(CMS_ROOT . 'includes/os.inc.php');
@unlink(CMS_ROOT . 'wrapper.php');
@unlink(CMS_ROOT . 'admin/doccenter.php');
@unlink(CMS_ROOT . 'admin/inc/news.inc.php');
@unlink(CMS_ROOT . 'admin/inc/news.class.php');
@unlink(CMS_ROOT . 'admin/inc/doccenter.class.php');
@unlink(CMS_ROOT . 'includes/request.exec.inc.php');
@unlink(CMS_ROOT . 'includes/pdfconvert.inc.php');

$this->delDirWithSubDirs(CMS_ROOT . 'blog');

$this->db->query("UPDATE " . TBL_CMS_CUSTPERM . " SET module='tcblog',page_id=0 WHERE page_id=680"); #pinwand -> blog
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='tcblog' WHERE id=670 OR id=650 OR id=660"); #pinwand -> blog
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Blog Detail' WHERE id=670"); #pinwand -> blog
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Blog Table' WHERE id=660"); #pinwand -> blog
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Blog Item Editor' WHERE id=650"); #pinwand -> blog
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='TC Blog' WHERE id=680"); #pinwand -> blog
$this->db->query("UPDATE " . TBL_CMS_PIN_GROUPS . " SET groupname='Standard Blog' WHERE id=1"); #pinwand -> blog

$this->db->query("DELETE FROM " . TBL_CMS_TEMPLATES . " WHERE id=600");
$this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=600");

if (get_data_count(TBL_CMS_CUSTTOGROUP, 'kid', "gid=1100") == 0) {
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE 1");
    while ($row = $this->db->fetch_array_names($result)) {
        $this->db->query("INSERT INTO " . TBL_CMS_CUSTTOGROUP . " SET gid=1100,kid=" . $row['kid'] . "");
    }
}

@unlink(CMS_ROOT . 'includes/module.class.php');
@unlink(CMS_ROOT . 'includes/module.class.php_');
@unlink(CMS_ROOT . 'includes/sitemap.class.php');
@unlink(CMS_ROOT . 'includes/gallery.class.php');
@unlink(CMS_ROOT . 'includes/gallery.class.php_');

@unlink(CMS_ROOT . 'test.html');
#@unlink(CMS_ROOT . 'btn.php');
@unlink(CMS_ROOT . 'filesearchhover.js');


$fname = CMS_ROOT . 'php.ini';
$fc = file_get_contents($fname);
if (!strstr($fc, 'date.timezone')) {
    $fc = str_replace('[PHP]', '[PHP]
date.timezone="Europe/Berlin"
', $fc);
    file_put_contents($fname, $fc);
}

$fname = CMS_ROOT . 'admin/php.ini';
$fc = file_get_contents($fname);
if (!strstr($fc, 'date.timezone')) {
    $fc = str_replace('[PHP]', '[PHP]
date.timezone="Europe/Berlin"
', $fc);
    file_put_contents($fname, $fc);
}

function replaceInEMAILS($tpl_rep, $db) {
    $result = $db->query("SELECT * FROM " . TBL_CMS_MAILTEMP_CONTENT);
    while ($row = $db->fetch_array_names($result)) {
        $html = $row['content'];
        $found = false;
        foreach ($tpl_rep as $suchwort => $ersetzewort) {
            if (strstr(strtolower($html), strtolower($suchwort))) {
                $html = str_ireplace($suchwort, $ersetzewort, $html, $rep_count);
                $found = true;
            }
        }
        if ($found == TRUE)
            $db->query("UPDATE " . TBL_CMS_MAILTEMP_CONTENT . " SET content='" . $db->real_escape_string($html) . "' WHERE id='" . $row['id'] . "' LIMIT 1");
    }
}

$tpl_rep = array(
    '<% $PHPMSG %>' => '<% include file="feedback_messages.tpl" %>',
    'member.secure_email_link' => 'member.email',
    '{TMPL_CRYPTEMAIL}' => '<% mailto address="' . FM_EMAIL . '" encode="hex" %>',
    '{TMPL_ANREDESELECT}' => '<% $kregform.salutselect %>',
    '{TMPL_KNOWNOF}' => '<select name="FORM[knownof]"><% $kregform.knowofselect %></select>',
    '{TMPL_LAND_SELECT}' => '<select name="FORM[land]" size="-1"><% $kregform.countryselect %></select>',
    '{TMPL_GOOGLE_ANALYTICS}' => $this->db->real_escape_string($this->gbl_config['google_analytics']),
    '{TMPL_INFO}' => '<% $cmsinfo %>',
    '{TMPL_CRYPTKONTO}' => PN_KONTO,
    'type=news&aktion=pdfconvert' => 'cmd=print_as_pdf');
$this->replaceInTemplates($tpl_rep);

$tpl_rep = array(
    '{FM_FAX}' => '<% $gbl_config.adr_fax %>',
    '{FM_TELEFON}' => '<% $gbl_config.adr_telefon %>',
    '{FM_EMAIL}' => '<% $gbl_config.adr_service_email  %>',
    '{FM_ORT}' => '<% $gbl_config.adr_town %>',
    '{FM_UST}' => '<% $gbl_config.adr_ust %>',
    '{PATH_CMS}' => '<% $PATH_CMS %>',
    '{FM_FON_COST}' => '<% $gbl_config.adr_telkosten %>',
    '{FM_NAME}' => '<% $gbl_config.adr_firma %>',
    '{FM_OWNER}' => '<% $gbl_config.adr_forename %> <% $gbl_config.adr_surename %>',
    '{FM_STRASSE}' => '<% $gbl_config.adr_street %>',
    '{FM_PLZ}' => '<% $gbl_config.adr_plz %>',
    '{FM_DOMAIN}' => '<% $gbl_config.opt_domain %>',
    '{TMPL_PHPSELF}' => '<% $PHPSELF %>',
    '{TMPL_PHPGETPAGE}' => '<% $page %>',
    '{TMPL_SID}' => '<% $GET.sid_id %>',
    '{TMPL_SES_SID}' => '<% $session_id %>',
    '{FORM_GETPAGE}' => '<% $page %>',
    '{FORM_PAGE}' => '<% $PHPSELF %>?page=<% $page %>',
    '{TMPL_GOOGLEKNR}' => '<% $gbl_config.google_kontonummer %>',
    '{YEAR}' => '<% $smarty.now|date_format:"%Y" %>',
    '{META_TITLE}' => '<% $meta.title %>',
    '{META_DESC}' => '<% $meta.description %>',
    '{TMPL_PAGENAME}' => '<% $REQUEST_URI %>',
    '{META_KEYWORDS}' => '<% $meta.keywords %>',
    '{CMS_VERSION}' => '<% $KEIMENO_VERSION %>');
$tpl_rep = $this->real_escape($tpl_rep);
$this->replaceInTemplates($tpl_rep);
replaceInEMAILS($tpl_rep, $this->db);

$tpl_rep = array(
    '{PN_KONTO}' => "<% $gbl_config.adr_firma %>\nKonto: <% $ $gbl_config.adr_konto %>\nBLZ: <% $gbl_config.adr_blz %>\nBank: <% $gbl_config.adr_bank %>\nIBAN: <% $gbl_config.adr_iban %>\nSWIFT/BIC-Code: <% $gbl_config.adr_swift %>",
    '{TMPL_CRYPTKONTO}' => "<% $gbl_config.adr_firma %>\nKonto: <% $ $gbl_config.adr_konto %>\nBLZ: <% $gbl_config.adr_blz %>\nBank: <% $gbl_config.adr_bank %>\nIBAN: <% $gbl_config.adr_iban %>\nSWIFT/BIC-Code: <% $gbl_config.adr_swift %>",
    '{GOOGLE_MAPS}' => '<iframe marginwidth="0" marginheight="0" src="http://www.trebaxa.com/gmgen.php?address=<% $gbl_config.google_maps %>" frameborder="0" width="500" scrolling="no" height="400"></iframe>',
    '<% $GOOGLE_MAPS %>' =>
        '<iframe marginwidth="0" marginheight="0" src="http://www.trebaxa.com/gmgen.php?address=<% $gbl_config.google_maps %>" frameborder="0" width="500" scrolling="no" height="400"></iframe>');
$tpl_rep = $this->real_escape($tpl_rep);
$this->replaceInTemplates($tpl_rep);

$key_arr = array();
foreach ($this->gbl_config as $key => $value) {
    $key_arr['{TMPL_CFG_' . strtoupper($key) . '}'] = '<% $gbl_config.' . $key . ' %>';
    $key_arr['{TMPL_' . strtoupper($key) . '_CFG}'] = '<% $gbl_config.' . $key . ' %>';
}
$this->replaceInTemplates($key_arr);
replaceInEMAILS($key_arr, $this->db);

function printBtn_UPDATE($text, $fontsize, $btnsize) {
    global $gbl_config;
    $text = str_replace(" ", "%20", $text);
    $btn = "btn_klein.gif";
    if (file_exists("../btn.php")) {
        $filename = "../btn.php";
    }
    else {
        $filename = SSL_PATH_SYSTEM . "/btn.php";
    }
    Return "<img alt=\"$text\" src=\"$filename?text=$text&img=$btn&fontsize=$fontsize&fontcolor=" . str_replace('#', '', $gbl_config['btn_font_color']) . "\" border=0>";
}

function printBtnSrc_UPDATE($text, $fontsize, $btnsize) {
    global $gbl_config;
    $text = str_replace(" ", "%20", $text);
    if ($btnsize == 0 || $btnsize == "") {
        $btn = "btn_klein.gif";
    }
    else {
        $btn = "btn_gross.gif";
    }
    if (file_exists("../btn.php")) {
        $filename = "../btn.php";
    }
    else {
        $filename = SSL_PATH_SYSTEM . "/btn.php";
    }
    Return "alt=\"$text\" src=\"$filename?text=$text&img=$btn&fontsize=$fontsize&fontcolor=" . str_replace('#', '', $gbl_config['btn_font_color']) . "\"";
}

$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $html = $row['content'];
    preg_match_all("={TMPL_BTNSRC_(.*)}=siU", $html, $btn_tag);
    foreach ($btn_tag[0] as $key => $wert) {
        $html = str_replace($btn_tag[0][$key], printBtnSrc_UPDATE(str_replace("TMPL_BTNSRC_", "", $wert), 10, 0), $html);
    }
    preg_match_all("={TMPL_BTN_(.*)}=siU", $html, $btn_tag);
    foreach ($btn_tag[0] as $key => $wert) {
        $html = str_replace($btn_tag[0][$key], printBtn_UPDATE(str_replace("TMPL_BTN_", "", $wert), 10, 0), $html);
    }
    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string($html) . "' WHERE id=" . $row['id']);
}


app_class::generate_all_module_xml();

$fname = CMS_ROOT . 'layout.css';
$fc = file_get_contents($fname);
if (!strstr($fc, 'div.okbox')) {
    $fc .= 'div.okbox {
border:1px solid #090;
text-align:left;
width:99%;
background-color:#FFF;
font-size:10pt;
font-weight:bold;
color:#090;
padding:3px;
}

div.faultbox {
border:1px solid #FF2400;
text-align:left;
width:99%;
background-color:#FFF;
font-size:10pt;
font-weight:bold;
color:#C00;
padding:3px;
}';
    file_put_contents($fname, $fc);
}

$tpl_rep = array(
    '<!-- TMPL_CONTACT_START -->' => '<% if ($section=="") %>',
    '<!-- TMPL_CONTACT_END -->' => '<%/if%>',
    '<input type="hidden" name="btn" value="1">' => '',
    '<input type="hidden" name="token"' => '<input type="hidden" name="cmd" value="sendmsg"><input name="token" type="hidden"',
    "$aktion==" => "$section==");
$tpl_rep = $this->real_escape($tpl_rep);
$arr = array(
    'nachname',
    'vorname',
    'tschapura',
    'telefon',
    'nachricht');
foreach ($arr as $key) {
    $tpl_rep['{VALUE_' . $key . '}'] = '<% $CONTACTF.values.' . $key . '|sthsc %>';
}
$this->replaceInTemplates($tpl_rep, 6);


$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>