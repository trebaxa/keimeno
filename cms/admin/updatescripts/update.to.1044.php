<?PHP

/**
 * @package    keimeno
 *
 * @copyright  Copyright (C) 2006 - 2018 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      11.05.2018
 * @version    1.0.4.4
 */

$version = '1.0.4.4';

$files = array(CMS_ROOT . 'captcha.php', CMS_ROOT . 'includes/modules/memindex/admin/tpl/kreg.form.regedit.tpl');
foreach ($files as $file) {
    @unlink($file);
}

$tpl_rep = array(
    '$gbl_config.captcha_active' => '$contact.cf_cpatcha',
    '/captcha.php' => '<%$PATH_CMS%>includes/modules/contactform/contact.captcha.php',
    '<% html_subbtn class="btn btn-primary" value="{LBL_UPLOAD}" %>' => '<button class="btn btn-primary" type="submit">{LBL_UPLOAD}</button>'
    );
$this->replaceInTemplates($tpl_rep);

dao_class::db_delete(TBL_CMS_GBLCONFIG, array('config_name' => 'captcha_active'));

$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='0' WHERE config_name='debug_mode'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='SSL',is_list='keine|SSL|TLS' WHERE config_name='smtp_encrypt'");


$fcontent = '<?php
define("TBL_CMS_PREFIX", \'' . TBL_CMS_PREFIX . '\');
define("DB_HOST", \'' . DB_HOST . '\');
define("DB_USER", \'' . DB_USER . '\');
define("DB_PASSWORD", \'' . DB_PASSWORD . '\');
define("DB_DATABASE", \'' . DB_DATABASE . '\');';
file_put_contents(CMS_ROOT . 'admin/db_connect.php', $fcontent);

$this->delete_dir_with_subdirs( CMS_ROOT . 'includes/lib/phpmailer-old');
$this->delete_dir_with_subdirs( CMS_ROOT . 'includes/lib/phpmailer');

# ERST TRUE SETZEN, WENN RELEASE FERTIG IST UND VERSION GESETZT WERDEN SOLL
$NO_FAILURE = TRUE;
#$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
#$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $VERSION);
