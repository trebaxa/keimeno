<?PHP

$version = '1.0.3.5';

$tpl_rep = array(
    '<% include file="webcontent_footer.tpl" %>' => '',
    '<% include file="webcontent_top.tpl" %>' => '',
    '<% include file="webcontent_top.tpl"%>' => '',
    '<%$pinitem.image%>' => '<%$pinitem.b_image%>',
    '<% $pinitem.detail_link %>' => '<% $SCRIPT_URI %>?cmd=load_blog_item&id=<% $pinitem.DID %>',
    '<%include file="webcontent_footer.tpl"%>' => '',
    '<% include file="webcontent_footer.tpl"%>' => '',
    '<% include file="tw_account.tpl" %>' => '',
    $this->db->real_escape_string('<% if ($kreg_aktion==\'insert\') %>') => '<% if ($CU_LOGGEDIN==false) %>',
    $this->db->real_escape_string('<% if ($aktion==\'kregdone\') %><% /if %>') => '',
    $this->db->real_escape_string('<% if ($kreg_aktion==\'update\') %>') => '<% if ($CU_LOGGEDIN==true) %>',
    '<input name="aktion" type="hidden" value="<% $kreg_aktion %>">' =>
        '<input name="cmd" type="hidden" value="<% if ($CU_LOGGEDIN==false) %>insert<%else%>update<%/if%>">',
    '<form name="aform" id="aform" action="<% $PHPSELF %>" method="post">' =>
        '<form id="aform" name="aform" action="<% $PHPSELF %>" method="post"><input type="hidden" name="valoaction" value="">',
    '<%include file="webcontent_top.tpl"%>' => '');
$this->replaceInTemplates($tpl_rep);

$t = new template_class();
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=320 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=330 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=4 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=990 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=9940 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=540 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=11 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=860 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Forum - Editor' WHERE tpl_name='forum_editor' LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Forum - Themes' WHERE tpl_name='forum_themes' LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Language selection' WHERE tpl_name='flagtable' LIMIT 1");

$t->delete_template(320);
$t->delete_template(330);
$t->delete_template(4);
$t->delete_template(990);
$t->delete_template(9940);

function change_saveconfig($dir) {
    if (!is_dir($dir))
        return;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && strstr($file, ".php")) {
                $c = file_get_contents($dir . $file);
                if (strstr($c, 'global $CONFIG_OBJ;')) {
                    $c = str_replace('global $CONFIG_OBJ;', '$CONFIG_OBJ = new config_class();', $c);
                    file_put_contents($dir . $file, $c);
                }
            }
        }
    }
}

change_saveconfig(CMS_ROOT . 'admin/');
change_saveconfig(CMS_ROOT . 'admin/inc/');
change_saveconfig(CMS_ROOT . 'includes/');

if ($handle = opendir(CMS_ROOT . 'includes/modules/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_dir(CMS_ROOT . 'includes/modules/' . $file)) {
            change_saveconfig(CMS_ROOT . 'includes/modules/' . $file . '/admin/');
            change_saveconfig(CMS_ROOT . 'includes/modules/' . $file . '/');
        }
    }
}
@unlink(CMS_ROOT . 'test.php');
@unlink(CMS_ROOT . 'db_zugriff.php');
@unlink(CMS_ROOT . 'admin/func_menue.php');
@unlink(CMS_ROOT . 'admin/server_menue.php');
@unlink(CMS_ROOT . 'admin/std_layout.tpl');
@unlink(CMS_ROOT . 'includes/kreg.php');
@unlink(CMS_ROOT . 'admin/ajax_engine.php');
@unlink(CMS_ROOT . 'admin/backup.class.php');
@unlink(CMS_ROOT . 'admin/inc/func_menue.php');
@unlink(CMS_ROOT . 'admin/inc/dbout.class.php');
@unlink(CMS_ROOT . 'admin/inc/autocrawl.class.php');
@unlink(CMS_ROOT . 'admin/inc/search_index.class.php');
@unlink(CMS_ROOT . 'admin/inc/searchindex.inc.php');
@unlink(CMS_ROOT . 'admin/tpl/searchindex.tpl');
@unlink(CMS_ROOT . 'admin/inc/news.inc.php');
@unlink(CMS_ROOT . 'admin/inc/server_menue.php');
@unlink(CMS_ROOT . 'admin/tpl/articles.table.admin.tpl');
@unlink(CMS_ROOT . 'admin/tpl/os_fields.table.admin.tpl');
@unlink(CMS_ROOT . 'admin/tpl/webcam.admin.tpl');
@unlink(CMS_ROOT . 'admin/tpl/webcam.editor.admin.tpl');
@unlink(CMS_ROOT . 'includes/modules/content/admin/tpl/website.titles.tpl');
@unlink(CMS_ROOT . 'admin/js/ColorPicker2.js');
@unlink(CMS_ROOT . 'admin/update.class.php');
@unlink(CMS_ROOT . 'admin/headera.php');
@unlink(CMS_ROOT . 'includes/pCache.class.php');
@unlink(CMS_ROOT . 'includes/pChart.class.php');
@unlink(CMS_ROOT . 'includes/pData.class.php');
@unlink(CMS_ROOT . 'includes/calendar.inc.php');
@unlink(CMS_ROOT . 'includes/graphics.inc.php');
@unlink(CMS_ROOT . 'includes/rating.class.php');
@unlink(CMS_ROOT . 'includes/smile.class.php');
@unlink(CMS_ROOT . 'includes/rss.inc.php');
@unlink(CMS_ROOT . 'admin/cms_olsi.php');
@unlink(CMS_ROOT . 'admin/js/jquery-1.6.1.min.js');
@unlink(CMS_ROOT . 'admin/js/jquery-1.8.3.min.js');
@unlink(CMS_ROOT . 'admin/js/phpThumb.config.php');
@unlink(CMS_ROOT . 'admin/js/ajax.js');

$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/optionmenu');
$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/mktree');
$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/jcolorpicker');
$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/menue');
$this->delDirWithSubDirs(CMS_ROOT . 'admin/js/fancybox');
$this->delDirWithSubDirs(CMS_ROOT . 'admin/font-awesome-4.0.3');


include_once (CMS_ROOT . 'admin/inc/layout.class.php');
if (get_data_count(TBL_CMS_LAYOUTFILES, '*', "l_file='layout.css'") == 0) {
    $FORM = array('l_file' => 'layout.css');
    insert_table(TBL_CMS_LAYOUTFILES, $FORM);
}

$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='show_intro_page' LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . FM_EMAIL . "' WHERE config_name='cf_ccemail'");


$this->repl_hta_link(41);
$this->repl_hta_link(38);
$this->repl_hta_link(39);
$this->repl_hta_link(40);
$this->repl_hta_link(42);
$this->repl_hta_link(63);
$this->repl_hta_link(60);
$this->repl_hta_link(33);

$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=49");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=57");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=48");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=47");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=30");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=31");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=45");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=46");

$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=55");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=54");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=56");
$this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=50");


$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0 WHERE id=480 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=8 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=6 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=5 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=7 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=770 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=9930 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE id=22 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET description='Verkaufsformular Standard' WHERE id=9750 LIMIT 1");

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0',gbl_template=1,modident='downloadcenter' WHERE id=980 LIMIT 1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET php='modules/memindex/memindex.inc' WHERE id=770 LIMIT 1");
$this->execSQL("ALTER TABLE " . TBL_CMS_TEMPLATES . " DROP `tagable` ");
$this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET t_htalinklabel='' WHERE tid=950 LIMIT 1");

# CONFIG umschreiben
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='gallery' WHERE gid=30");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=30");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='ekomi' WHERE gid=10000");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=10000");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='sellform' WHERE gid=46");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=46");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='calendar' WHERE gid=31");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=31");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='webcam' WHERE gid=32");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=32");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='tcblog' WHERE gid=51");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=51");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='tagcloud' WHERE gid=29");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=29");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='indexsearch' WHERE gid=34");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=34");


$this->execSQL("DROP TABLE " . TBL_CMS_CUSTOMFIELDS);
$this->execSQL("DROP TABLE " . TBL_CMS_CUSTOMFIELDSCONT);

$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='newsticker_count'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='gal_preview_count'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='adr_telkosten'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_show_comments'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_profilid'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_screenname'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_show_ilike'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_ilike_connecttofb'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='fb_socialnetwork'");
$this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=38");


if ($handle = opendir(CMS_ROOT . 'admin/inc/')) {
    while (false !== ($file = readdir($handle))) {
        if (strstr($file, 'menue_') && strstr($file, '.xml')) {
            @unlink(CMS_ROOT . 'admin/inc/' . $file);
        }
    }
}

function isadmin_change($dir) {
    if (!is_dir($dir))
        return;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && strstr($file, ".php")) {
                $c = file_get_contents($dir . $file);
                if (strstr($c, 'if (ISADMIN != 1)')) {
                    $c = str_replace('if (ISADMIN != 1)', "if (!defined('ISADMIN'))", $c);
                    file_put_contents($dir . $file, $c);
                }
            }
        }
    }
}

isadmin_change(CMS_ROOT . 'admin/');
isadmin_change(CMS_ROOT . 'admin/inc/');
isadmin_change(CMS_ROOT . 'includes/');

$this->remove_adminmenu_id(132); # Handbuch
$this->remove_adminmenu_id(105); # System Template
$this->remove_adminmenu_id(66); # Toplevel
$this->remove_adminmenu_id(68); # Inlay
$this->remove_adminmenu_id(99); # XML Sitemaps
$this->remove_adminmenu_id(67);
$this->remove_adminmenu_id(97);
$this->remove_adminmenu_id(108); # site craawler
$this->remove_adminmenu_id(94); # content manager
$this->db->query("UPDATE " . TBL_CMS_MENU . " SET parent=95 WHERE parent=94 ");

# Template Vorlagen für immer aktivieren
include_once (CMS_ROOT . 'admin/inc/modulman.class.php');

$fname = CMS_ROOT . 'includes/modules/tplvars/config.xml';
$xml_modul = simplexml_load_file($fname);
$xml_modul->module->settings->active = 'true';
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml_modul->asXML());
$dom->save($fname);

app_class::generate_all_module_xml();


$dir = CMS_ROOT . 'includes/modules/';
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_dir($dir . $file) && file_exists($dir . $file . '/config.xml')) {
            $xml_module = simplexml_load_file($dir . $file . '/config.xml');
            $desc = strval($xml_module->module->settings->description);
            $this->db->query("UPDATE " . TBL_CMS_MENU . " SET description='" . $desc . "' WHERE mod_ident='" . strval($xml_module->module->settings->id) . "'");
        }
    }
}

if ($this->gbl_config['mod_wilinku'] == 0 && !strstr($_SERVER['HTTP_HOST'], "wilinku.com")) {
    $this->db->query("DELETE FROM " . TBL_CMS_APERMISSIONS . " WHERE p_mod='mod_wilinku' ");
    $this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name LIKE 'wlu_%'");
    $this->db->query("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id=47 OR id=43 OR id=39 OR id=37 OR id=49 OR id=50 OR id=44");
    $this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE gid=47 OR gid=43 OR gid=39 OR gid=37 OR gid=49 OR gid=50 OR gid=44");
}

$dir = CMS_ROOT . 'admin/tpl/';
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_file($dir . $file) && strstr($file, ".tpl") && strstr($file, "mail_")) {
            @unlink($dir . $file);
        }
    }
}


$this->transform_langtable_to_array();


function change($dir, $ext, $rep) {
    global $found;
    if (!is_dir($dir))
        return;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && $file != 'cms.php' && strstr($file, "." . $ext)) {
                $c = file_get_contents($dir . $file);
                $found = false;

                foreach ($rep as $from => $to) {
                    if (strstr($c, $from)) {
                        $c = str_ireplace($from, $to, $c);
                        $found = true;
                    }
                }
                if ($found) {
                    file_put_contents($dir . $file, $c);
                    # echo $dir . $file . '<br>';
                }
            }
        }
    }
}

$dirs = array(CMS_ROOT . 'includes/modules/');
if ($handle = opendir(CMS_ROOT . 'includes/modules/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_dir(CMS_ROOT . 'includes/modules/' . $file)) {
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/';
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/admin/';
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/admin/tpl/';
        }
    }
}

$rep = array(
    'insertTable' => 'insert_table',
    'insert_table' => 'insert_table',
    'get_column_count_of_db_table' => 'get_data_count',
    'gen_thumb_pictureSrc' => 'gen_thumb_image',
    'get_value_of_column_from_db_table' => 'get_value_from_table',
    'include_protection();' => "defined('IN_SIDE') or die('Access denied.');",
    'get_tamplate' => 'get_template');
foreach ($dirs as $dir) {
    change($dir, 'php', $rep);
}

$rep = array(
    'tab_std' => 'table table-striped table-hover',
    'class="<%cycle values="row1,row2"%>"' => '',
    'border="1"' => '',
    'class="stripebox"' => 'class="btn-group"',
    'class="infobox"' => 'class="bg-info text-info"',
    'class="okbox"' => 'class="bg-success text-success"',
    'class="faultbox"' => 'class="bg-danger text-danger"',
    'width="100%"' => '',
    'class="<%$sclass%>"' => '',
    'class="sub_btn"' => 'class="btn btn-primary"');
foreach ($dirs as $dir) {
    change($dir, 'tpl', $rep);
}

if (class_exists('tcblog_admin_class')) {
    $obj = new tcblog_admin_class();
    $obj->rebuild_page_index();
}

if (class_exists('news_admin_class')) {
    $obj = new news_admin_class();
    $obj->rebuild_page_index();
}

if (class_exists('articles_class')) {
    $obj = new articles_class();
    $obj->rebuild_page_index();
}

if (class_exists('gallery_class')) {
    $obj = new gallery_class();
    $obj->rebuild_page_index();
}

if (class_exists('tagcloud_admin_class')) {
    $obj = new tagcloud_admin_class();
    $obj->set_perma_link();
}

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);

?>