<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      09.02.2016
 * @version    1.0.4.1
 */

$version = '1.0.4.1';
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='rediapi' WHERE tpl_name='fe_rediapi-produkte'");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='articles' WHERE tpl_name='articles_tree'");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='articles' WHERE tpl_name='article_latest_items'");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='articles' WHERE tpl_name='article'");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET modident='articles' WHERE tpl_name='article_tr'");

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE modident='articles'");
$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin='0' WHERE modident='webcam'");

$TPL = new template_class();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='webcam' OR modident='articles'");
while ($row = $this->db->fetch_array_names($result)) {
    $TPL->delete_template($row['id']);
}

self::delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/articles/');
self::delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/webcam/');

$LOG = new log_class();


$this->db->query("DROP TABLE IF EXISTS `" . TBL_CMS_PREFIX . "art_content`, `" . TBL_CMS_PREFIX . "art_files`, `" . TBL_CMS_PREFIX . "art_index`, `" . TBL_CMS_PREFIX .
    "art_tree`, `" . TBL_CMS_PREFIX . "art_treecontent`, `" . TBL_CMS_PREFIX . "webcam_content`, `" . TBL_CMS_PREFIX . "webcam_dates`, `" . TBL_CMS_PREFIX .
    "webcam_dialog`, `" . TBL_CMS_PREFIX . "webcam_files`, `" . TBL_CMS_PREFIX . "webcam_shoutbox`, `" . TBL_CMS_PREFIX . "webcam_wl`, `" . TBL_CMS_PREFIX .
    "webcam_wll`");

$this->execSQL("RENAME TABLE " . TBL_CMS_PREFIX . "_guestbook TO " . TBL_CMS_PREFIX . "_testimonials");
/*
error_reporting(0);
$WEB = new websites_class();
$result = $this->db->query("SELECT id FROM " . TBL_CMS_TEMPCONTENT . " WHERE t_precontent='' AND lang_id=1");
while ($row = $this->db->fetch_array_names($result)) {
try {
$WEB->save_pre_compiled_content($row['id']);
}
catch (Exception $e) {
#  echo 'Exception abgefangen: ', $e->getMessage(), "\n";
$row = $this->real_escape($row);
$LOG->addLog('FAILURE', 'Smarty compile error. TID: ' . $row['tid'] . '|' . $row['linkname']);
}
}
*/

@unlink(CMS_ROOT . 'includes/modules/gallery/admin/tpl/gallery.multiupload.tpl');
if (!is_dir(FILE_ROOT))
    mkdir(FILE_ROOT, 0750);
# finalize
$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;
