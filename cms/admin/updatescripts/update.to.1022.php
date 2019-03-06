<?PHP

$version = '1.0.2.2';

$tpl_rep = array(
    'src="<%$PATH_CMS%><%$language.icon%>"' => 'src="<%$language.icon%>"',
    '{TMPL_BREAD_CRUMB_TRAILS}' => '<% $content_bread_crumbs %>',
    '{TMPL_SUBLEVEL_MENU_VER}' => '<% $sublevel_vert %>',
    '{TMPL_GALLERY_MENU}' => '<% $gallery_obj.gallery_html_menu %>',
    '{TMPL_GAL_BREAD_CRUMB_TRAILS}' => '<% $gallery_obj.gallery_breadcrumbs %>',
    './images/' => '<%$PATH_CMS%>/images/',
    '<% $news.date %>' => '<% $news.ndate %>',
    '/js/' => '<% $PATH_CMS %>js/');
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);
$this->replaceInTemplates($tpl_rep);

delete_file(CMS_ROOT . 'admin/gal_edit.php');
delete_file(CMS_ROOT . 'admin/gal_group_edit.php');
delete_file(CMS_ROOT . 'admin/links_edit.php');
delete_file(CMS_ROOT . 'admin/otimer.class.php');
delete_file(CMS_ROOT . 'admin/otimer.php');
delete_file(CMS_ROOT . 'admin/template_edit.php');
delete_file(CMS_ROOT . 'includes/links_edit.php');
delete_file(CMS_ROOT . 'includes/otimer.php');
$this->execSQL("DROP TABLE " . TBL_CMS_PREFIX . "links_album");

#$this->installTidy();

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>