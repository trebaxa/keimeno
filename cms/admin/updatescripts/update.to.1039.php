<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      09.02.2016
 * @version    1.0.3.8
 */

$version = '1.0.3.9';

$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/file_data/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/file_server/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/uploadify/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/tabmenu/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/multiupload/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/formval/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/autocomplete_jq/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'js/ResponsiveFilemanager/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/fonts/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/bootstrap-3.2.0-dist/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'admin/font-awesome-4.5.0/');
$this->delete_dir_with_subdirs(CMS_ROOT . 'js/tiny_mce/');

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " set modident='event' WHERE modident='calendar'");

if (!class_exists('moduleman_class'))
    require_once (CMS_ROOT . 'admin/inc/modulman.class.php');
$MOD = new moduleman_class();
$MOD->uninstall_mod('calendar');
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/calendar/');

$this->execSQL("DELETE FROM `" . TBL_CMS_HTA . "` WHERE id=34");

#$this->db->query("UPDATE ".TBL_CMS_TEMPLATES." SET module_id='content' WHERE module_id='psitemap' ");


$this->delete_dir_with_subdirs(CMS_ROOT . 'js/css/');
@unlink(CMS_ROOT . 'admin/change.php');
@unlink(CMS_ROOT . 'admin/change.php-');
@unlink(CMS_ROOT . 'admin/css/login.css');
@unlink(CMS_ROOT . 'admin/tablist.php');
@unlink(CMS_ROOT . 'admin/log.class.php');
@unlink(CMS_ROOT . 'admin/hta.class.php');
@unlink(CMS_ROOT . 'admin/downcenter.php');

$this->remove_adminmenu_id(87);
$this->remove_adminmenu_id(86);
$this->remove_adminmenu_id(141);

update_table(TBL_CMS_MENU, 'id', 79, array('parent' => '50'));
update_table(TBL_CMS_MENU, 'id', 80, array('parent' => '50'));


$admin_images = array(
    'page_delete.png',
    'images.png',
    'ico_doc.gif',
    'twitter.png',
    'logo-cms-keimeno-small.png',
    'page_white_edit.png',
    'application_add.png',
    'flickr.png',
    'chart_bar.png',
    'additem.png',
    'shield.png',
    'block_customer.png',
    'folderoff.gif',
    'opt_clone.gif',
    'old_msg.gif',
    'keimeno_logo_adminlogin.jpg',
    'close.png',
    'comment.png',
    'key.png',
    'ico_docn.png',
    'add.png',
    'opt_edit.gif',
    'opt_plus.gif',
    'page_movedown.png',
    'small_icon_16x16.jpg',
    'keimeno_adminicon.ico',
    'eye.png',
    'page_visible.png',
    'keimeno_logo_admin_30.png',
    'blockno_customer.png',
    'money_euro_somepayed.png',
    'application_view_detail.png',
    'page_reimport.png',
    'ico_doc_open.png',
    'control_repeat.png',
    'lock_unlock.png',
    'lock.png',
    'opt_frage_gross.gif',
    'apple-touch-icon-72x72-precomposed.png',
    'notmissed.png',
    'application_view_list.png',
    'keimeno_logo_adminlogin.png',
    'page_view.png',
    'axloader.gif',
    'opt_search.gif',
    'axloader-blue.gif',
    'keimeno-login.png',
    'arrow_right.png',
    'new_invoice.png',
    'folder.gif',
    'opt_up.gif',
    'infosmall.png',
    'money_ok.png',
    'doc_excel_csv.png',
    'products.png',
    'photo.png',
    'apple-touch-icon.png',
    'icon_printer.png',
    'opt_loader.gif',
    'keimeno_logo_admin_60.png',
    'article.png',
    'doc_pdf.png',
    'arrow_up.png',
    'ico_docn.gif',
    'tower.png',
    'languages.png',
    'page_moveup.png',
    'globe4.png',
    'opt_down.gif',
    'attach.png',
    'watermark.png',
    'additem3.png',
    'apple-touch-icon-114x114-precomposed.png',
    'gal_defekt.jpg',
    'mail_pdf.png',
    'comment_empty.png',
    'bookmark_document.png',
    'missed.png',
    'keimeno-logo-landscape.png',
    'apple-touch-icon-precomposed.png',
    'disk.png',
    'apple-touch-icon-144x144-precomposed.png',
    'money_euro_not_payed.png',
    'ico_doc.png',
    'arrow_down.png',
    'employees',
    'page_notvisible.png',
    'countryrelated.png',
    'grouppolicy.png',
    'folder_doc.png',
    'chart.png',
    'ico_edit.gif',
    'employee.png',
    'apple-touch-icon-57x57-precomposed.png',
    'opt_import.gif',
    'clone.png',
    'logo-cms-keimeno.png',
    'comment_red.png',
    'mail_stat.png',
    'small_icon_11x11.jpg',
    'folder_open.png');

if ($dh = opendir(CMS_ROOT . 'admin/images/')) {
    while (($file = readdir($dh)) !== false) {
        $ifile = CMS_ROOT . 'admin/images/' . $file;
        if (is_file($ifile) && file_exists($ifile) && !is_dir($ifile)) {
            if (!in_array($file, $admin_images)) {
                @unlink($ifile);
            }
        }
    }
    closedir($dh);
}

$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='mod_firewall'");

if (keimeno_class::get_config_value('hash_secret') == "") {
    $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . md5(uniqid()) . "' WHERE config_name='hash_secret'");
}

# finalize
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;
