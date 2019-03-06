<?PHP

$version = '1.0.3.2';


$tpl_rep = array(
    '<% include file=$globl_tree_template %>' => '<%include file=$globl_tree_template %>{TMPL_SPOT_2}',
    '<% include file="newslist.tpl" %>' => '',
    '<% assign var=newslist value=$TMPL_NEWSGROUP_1 %>' => '{TMPL_NEWSINLAY_1}',
    '<% assign var=newslist value=$TMPL_NEWSGROUP_2 %>' => '{TMPL_NEWSINLAY_2}',
    '<% assign var=newslist value=$TMPL_NEWSGROUP_3 %>' => '{TMPL_NEWSINLAY_3}',
    '<% assign var=newslist value=$TMPL_NEWSGROUP_4 %>' => '{TMPL_NEWSINLAY_4}',
    '<% assign var=newslist value=$TMPL_NEWSGROUP_5 %>' => '{TMPL_NEWSINLAY_5}',
    '<% assign var=newslist value=$TMPL_NEWSGROUP_6 %>' => '{TMPL_NEWSINLAY_6}',
    '{TMPL_NEWSINLAY_1}' => '{TMPL_NEWSINLAY_1}',
    '{TMPL_NEWSINLAY_2}' => '{TMPL_NEWSINLAY_2}',
    '{TMPL_NEWSINLAY_3}' => '{TMPL_NEWSINLAY_3}',
    '{TMPL_NEWSINLAY_4}' => '{TMPL_NEWSINLAY_4}',
    '{TMPL_NEWSINLAY_5}' => '{TMPL_NEWSINLAY_5}',
    '{TMPL_NEWSINLAY_6}' => '{TMPL_NEWSINLAY_6}',
    '{TMPL_NEWSINLAY_7}' => '{TMPL_NEWSINLAY_7}',
    '{TMPL_NEWSINLAY_8}' => '{TMPL_NEWSINLAY_8}',
    '{TMPL_NEWSINLAY_9}' => '{TMPL_NEWSINLAY_9}',
    'language="JavaScript"' => '',
    '<meta name="owner" content="<% $meta.owner %>">' => '',
    '<meta name="copyright" content="<% $meta.copyright %>">' => '',
    '<meta name="identifier-url" content="http://<% $meta.domain %>/index.html">' => '',
    '<meta name="language" content="<% $meta.contentlang %>">' => '',
    '<meta name="Content-Language" content="<% $meta.contentlang %>"/>' => '',
    '<meta http-equiv="expires" content="0">' => '',
    '<meta http-equiv="content-type" content="text/html; charset=UTF-8">' => '',
    '<meta name="page-topic" content="<% $meta.title %>">' => '',
    '<meta name="audience" content="all,All,alle">' => '',
    '<meta name="searchtitle" content="<% $meta.title %>">' => '',
    '<meta name="Content-Language" content="<% $meta.contentlang %>">' => '',
    '<meta name="distribution" content="<% $meta.distribution %>">' => '',
    'border="0"' => '',
    '$toplevel.TOPID' => '$cmstoplevels.toplobj.TOPID',
    '>ajax_class.js' => '>cjs/ajax_class.js',
    'js/default_js.js' => 'cjs/keimeno.js',
    '<% $content_bread_crumbs %>' =>
        '<% foreach from=$PAGEOBJ.t_breadcrumb_arr item=bread %><li> &#x9B; <a title="<%$bread.label|hsc%>" href="<%$bread.link%>"><%$bread.label%></a></li><%/foreach%>');
$this->replaceInTemplates($tpl_rep);

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET gbl_template=1, tpl_name = 'landing_page'  WHERE id=990");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET `php` = 'modules/news/news.inc' WHERE id =860 LIMIT 1 ");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET `description` = 'Impressum' WHERE id =5 LIMIT 1 ");
$this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET `linkname` = '{LBL_IMPRESSUM}' WHERE tid =5");


@unlink(CMS_ROOT . 'includes/news.inc.php');
@unlink(CMS_ROOT . 'admin/gallery.class.php');
@unlink(CMS_ROOT . 'includes/webcams.inc.php');
@unlink(CMS_ROOT . 'includes/parser.class.php');
@unlink(CMS_ROOT . 'admin/tpl/oeditorsrc.tpl');
@unlink("./inc/menue.xml");
@unlink(CMS_ROOT . 'admin/blog.class.php');
@unlink(CMS_ROOT . 'admin/blog.php');
@unlink(CMS_ROOT . 'admin/lang_edit.php');
@unlink(CMS_ROOT . 'admin/config_edit.php');
@unlink(CMS_ROOT . 'admin/config.class.php');
@unlink(CMS_ROOT . 'admin/menu_edit.php');
@unlink(CMS_ROOT . 'admin/index_.php');
@unlink(CMS_ROOT . 'ajax_class.js');
@unlink(CMS_ROOT . 'layout.class.php');
@unlink(CMS_ROOT . 'layout.php');
@unlink(CMS_ROOT . 'admin/layout.php');
@unlink(CMS_ROOT . 'cal.js');
@unlink(CMS_ROOT . 'phpThumb.php');
@unlink(CMS_ROOT . 'document_root.php');
@unlink(CMS_ROOT . 'graphics.php');
@unlink(CMS_ROOT . 'btn.php');
@unlink(CMS_ROOT . 'includes/phpthumb.class.php');
@unlink(CMS_ROOT . 'includes/phpThumb.config.php');
@unlink(CMS_ROOT . 'includes/phpthumb.functions.php');
@unlink(CMS_ROOT . 'admin/tpl/ajax.deleteitem.tpl');

$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/js/editarea');


$arr = array('<% $ .adr_konto %>' => '<% $gbl_config.adr_konto %>', );
$this->replaceInTemplates($tpl_rep);
$this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=0");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET layout_group='1' WHERE layout_group!=''");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='tablist' WHERE id=620");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET is_framework=1,modident='frameworks',description='GLOBAL FRAMEWORK - Default' WHERE id=1");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET is_framework=1,modident='frameworks',description='GLOBAL FRAMEWORK - Default 2' WHERE id=9670");


if (!is_dir(CMS_ROOT . 'smarty/cache'))
    mkdir(CMS_ROOT . 'smarty/cache', 0755);


if (is_module_installed('mod_wilinku')) {
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_WLU_ADPRICE_QUOTES . " WHERE q_rf_numcountries=0 AND q_rf_countrybox!=''");
    while ($row = $this->db->fetch_array_names($result)) {
        $count = count(explode(',', $row['q_rf_countrybox']));
        $this->db->query("UPDATE " . TBL_CMS_WLU_ADPRICE_QUOTES . " SET q_rf_numcountries=" . (int)$count . " WHERE id=" . $row['id']);
    }
}


$fname = CMS_ROOT . 'layout.css';
$fc = file_get_contents($fname);
if (!strstr($fc, 'img { border: 0; }')) {
    $fc .= '
img { border: 0; }
';
    file_put_contents($fname, $fc);
}


$fname = CMS_ROOT . 'admin/.htaccess';
$fc = file_get_contents($fname);
if (!strstr($fc, '##-- gzip aktivieren')) {
    $fc = '
##-- gzip aktivieren
SetOutputFilter DEFLATE
AddOutputFilterByType DEFLATE text/css text/html text/plain text/xml text/js application/x-javascript application/javascript

# BEGIN headers
<IfModule mod_headers.c>
    <FilesMatch "\.(js|css|xml|gz)$">
        Header append Vary Accept-Encoding
    </FilesMatch>

    Header unset Last-Modified
</IfModule>
# END headers


# BEGIN Expire headers
<IfModule mod_expires.c>
    <FilesMatch "\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico)$">
        ExpiresActive On
        ExpiresDefault "access plus 10 years"
    </FilesMatch>
</IfModule>
# END Expire headers


# BEGIN Cache-Control Headers
<FilesMatch "\.(ico|jpeg|jpg|png|gif|swf|css)$">
    Header set Cache-Control "max-age=5184000, public"
</FilesMatch>
<FilesMatch "\.(js)$">
    Header set Cache-Control "max-age=5184000, private"
</FilesMatch>
<FilesMatch "\.(xhtml|html|htm|php)$">
    Header set Cache-Control "max-age=5184000, private, must-revalidate"
</FilesMatch>
# END Cache-Control Headers
' . $fc;
    file_put_contents($fname, $fc);
}
if (!class_exists('websites_class')) {
    include_once (CMS_ROOT . 'includes/modules/content/admin/websites.admin.class.php');
}
$W = new websites_class();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE 1");#tid_childs=''
while ($row = $this->db->fetch_array_names($result)) {
    $W->breadcrumb_update($row['id']);
}

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='memindex' WHERE id IN (820,700,920,950,960,830,9920,930,780,800,810,450)");

$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='google_analytics'");

#clean if not wilinku

if (!strstr($_SERVER['HTTP_HOST'], "wilinku.com")) {
    $result = mysql_list_tables($this->db->database, $this->db->link_id);
    while ($row = mysqli_fetch_row($result)) {
        if (strstr($row[0], TBL_CMS_PREFIX . 'wlu_'))
            $this->db->query('DROP TABLE ' . $row[0]);
    }
    $folder = CMS_ROOT . 'admin/tpl/';
    $dh = opendir($folder);
    while (false !== ($filename = readdir($dh))) {
        if (strstr($filename, 'wlu_') && strstr($filename, '.tpl')) {
            @unlink($folder . $filename);
        }
    }
}
if (!is_dir(CMS_ROOT . 'file_data/links'))
    mkdir(CMS_ROOT . 'file_data/links', 0775);
if (is_dir(CMS_ROOT . 'file_data/links_banners'))
    rename(CMS_ROOT . 'file_data/links_banners', CMS_ROOT . 'file_data/links');

$folder = CMS_ROOT . 'includes/modules/';
$dh = opendir($folder);
while (false !== ($filename = readdir($dh))) {
    if (is_dir($folder . $filename)) {
        @unlink($folder . $filename . '/init.inc.php_');
    }
}

@unlink(CMS_ROOT . 'admin/inc/websites.class.php');
@unlink(CMS_ROOT . 'admin/inc/websitemanager.inc.php');
@unlink(CMS_ROOT . 'template_edit.php');
@unlink(CMS_ROOT . 'settings.php');
@unlink(CMS_ROOT . 'includes/modules/sellform/preparse.inc.php');

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
?>