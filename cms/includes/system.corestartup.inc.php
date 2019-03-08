<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


if (defined('CMS_ROOT') == false) {
    define('CMS_ROOT', str_replace(DIRECTORY_SEPARATOR . "includes", "", dirname(__FILE__) . DIRECTORY_SEPARATOR));
}
define('SYSTEM_ROOT', CMS_ROOT);

require (CMS_ROOT . 'includes/lib/phpmailer-607/src/Exception.php');
require (CMS_ROOT . 'includes/lib/phpmailer-607/src/PHPMailer.php');
require (CMS_ROOT . 'includes/lib/phpmailer-607/src/SMTP.php');
require (CMS_ROOT . 'includes/error.handler.class.php');
require (CMS_ROOT . 'includes/session.class.php');
require (CMS_ROOT . 'includes/defaults.inc.php');
require (CMS_ROOT . 'includes/db.class.php');
require (CMS_ROOT . 'includes/settings.php');
require (CMS_ROOT . 'admin/inc/keimeno.class.php');
require (CMS_ROOT . 'includes/dao.class.php');
require (CMS_ROOT . 'admin/inc/tc.class.php');
require (CMS_ROOT . "admin/inc/config.class.php");
require (CMS_ROOT . 'includes/app.class.php');
require (CMS_ROOT . 'includes/lib/mobile-detect/mobiledetect.class.php');

// init DAO
$dao = new dao_class();
dao_class::set_db($kdb);


// define start page
$S = dao_class::get_data_first(TBL_CMS_TEMPLATES, array('is_startsite' => 1));
define('START_PAGE', (int)$S['id']);
unset($S);

// Load config
$CONFIG_OBJ = new config_class();
$gbl_config = $CONFIG_OBJ->load();
keimeno_class::set_config($gbl_config);

// get IP
$REAL_IP = (keimeno_class::get_config_value('log_use_ip') == 1) ? keimeno_class::anonymizing_ip(keimeno_class::get_my_ip()) : keimeno_class::get_my_ip();
define('REAL_IP', $REAL_IP);


// File Root einstellen
if (strlen(PATH_CMS) > 1) {
    $arr = explode('/', str_replace(PATH_CMS, '', CMS_ROOT));
}
else {
    $arr = explode('/', CMS_ROOT);
    array_pop($arr);
}
array_pop($arr);
define('FILE_ROOT', implode('/', $arr) . '/' . keimeno_class::get_config_value('data_path') . '/');
define('KSERVER', FILE_ROOT . 'file_server/');

(keimeno_class::get_config_value('debug_mode') == 1) ? keimeno_class::set_debug(true) : keimeno_class::set_debug(false);

// SSL PROXY ODER ZERTIFIKAT
#$SSLSERVER = 'https://www.' . keimeno_class::get_config_value('opt_domain') . PATH_CMS;
define('SSLSERVER', keimeno_class::get_domain_url());

$GBL_LANGID = 1;

# SET LANGUAGE
$lng_code_is_set = isset($_REQUEST['lngcode']) && $_REQUEST['lngcode'] != "" && strlen($_REQUEST['lngcode']) == 2;

if (defined('IS_FRONTEND')) {
    if ($lng_code_is_set == true) {
        $lngcode = strtolower(substr($_REQUEST['lngcode'], 0, 2));
        $LNG = $kdb->query_first("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 AND local='" . $lngcode . "'");
        $GBL_LANGID = $_SESSION['GBL_LANGID'] = $LNG['id'];
    }
    else {
        #  if ( !isset($_SESSION['GBL_LANGID']) ||(int)$_SESSION['GBL_LANGID'] == 0)
        if (!isset($_REQUEST['axcall'])) {
            $_SESSION['GBL_LANGID'] = $GBL_LANGID = keimeno_class::get_config_value('std_lang_id');
        }
        else {
            $GBL_LANGID = $_SESSION['GBL_LANGID'];
        }
    }
}

if (intval($GBL_LANGID) == 0) {
    $GBL_LANGID = $_SESSION['GBL_LANGID'];
}

if (defined('ISADMIN') && ISADMIN == 1) {
    if ($GBL_LANGID == 0)
        $GBL_LANGID = keimeno_class::get_config_value('std_lang_admin_id');
}
else {
    if ($GBL_LANGID == 0)
        $GBL_LANGID = keimeno_class::get_config_value('std_lang_id');
}
$_SESSION['GBL_LANGID'] = $GBL_LANGID;


# Sprache
$LANGS = $LANGSFE = array();
$result = $kdb->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
while ($row = $kdb->fetch_array_names($result)) {
    $LANGSFE[$row['id']] = $row;
}


if (defined('ISADMIN') && ISADMIN == 1) {
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE approval=1 ORDER BY s_order");
    while ($row = $kdb->fetch_array_names($result)) {
        $LANGS[$row['id']] = $row;
    }
}
else {
    $LANGS = $LANGSFE;
}

$_SESSION['GBL_LOCAL_ID'] = $GBL_LOCAL_ID = $LANGS[$GBL_LANGID]['local'];

define('FM_NAME', keimeno_class::get_config_value('adr_firma'));
define('FM_EMAIL', keimeno_class::get_config_value('adr_service_email'));
define('FM_DOMAIN', keimeno_class::get_config_value('opt_domain'));
define('PATH_ADMIN', PATH_CMS . 'admin/');
define('PATH_SMARTY', CMS_ROOT . 'smarty/');
define('HOST', isset($_SERVER['HTTP_HOST']) === true ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_ADDR']) === true ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']));
define('MODULE_DIR', 'modules/');
define('MODULE_ROOT', CMS_ROOT . 'includes/' . MODULE_DIR);

// Wenn SSL Bereich, dann wird SSL_PATH definiert. Wichtig für header(location...) bei / delimeter

    define('SSL_PATH_SYSTEM', '');

include (CMS_ROOT . "includes/functions.inc.php");

# set time zone
date_default_timezone_set(keimeno_class::get_config_value('adr_timezone'));

# thumbnail class
# http://stefangabos.ro/php-libraries/zebra-image/
require (CMS_ROOT . "includes/lib/Zebra_Image-master/Zebra_Image.php");

# email sender class
require (CMS_ROOT . "includes/email.class.php");

# setup smarty integration
if (!defined('DIR_SEP'))
    include_once (CMS_ROOT . 'smarty/Smarty.class.php');

#https://www.smarty.net/docs/en/advanced.features.tpl
$smarty = new Smarty();
$my_security_policy = new Smarty_Security($smarty);
$my_security_policy->php_functions = array(
    'isset',
    'empty',
    'count',
    'sizeof',
    'in_array',
    'is_array',
    'time',
    'nl2br');
// remove PHP tags
$my_security_policy->php_handling = Smarty::PHP_REMOVE;
// allow everthing as modifier
$my_security_policy->php_modifiers = array();
if (!defined('ISADMIN')) {
    $smarty->addTemplateDir(CMS_ROOT . 'smarty/templates/' . $LANGS[$GBL_LANGID]['local']);
    $smarty->enableSecurity($my_security_policy);
}
$smarty->compile_dir = CMS_ROOT . 'smarty/templates_c/' . $LANGS[$GBL_LANGID]['local'];
$smarty->config_dir = CMS_ROOT . 'smarty/configs';
$smarty->cache_dir = CMS_ROOT . 'smarty/cache';
$smarty->left_delimiter = '<%';
$smarty->right_delimiter = '%>';
$smarty->auto_literal = false;
$smarty->error_reporting = E_ERROR | E_PARSE;
$smarty->muteExpectedErrors();

if (!defined('ISADMIN')) {
    $smarty->caching = (keimeno_class::get_config_value('smarty_cache') == 1);
    if ($smarty->caching == true) {
        if (keimeno_class::get_config_value('smarty_cache_lifetime') > 0) {
            $smarty->cache_lifetime = keimeno_class::get_config_value('smarty_cache_lifetime');
        }
    }
}
else
    $smarty->caching = false;
$smarty->assign('gbl_config', keimeno_class::get_config());


$C = get_defined_constants(true);
foreach ($C['user'] as $key => $value) {
    if (strstr($key, 'TBL_') && !strstr($key, 'TBL_CMS_')) {
        $shop_tables[$key] = $value;
    }
    if (strstr($key, 'TBL_CMS_')) {
        $cms_tables[$key] = $value;
    }
}


# standard includes & class creates
$LOGCLASS = new log_class();
$TCMASTER = new keimeno_class();
include (CMS_ROOT . 'includes/modules/modules.class.php');
$MODMASTER = new modules_class();

include (CMS_ROOT . "includes/main.class.php");
include (CMS_ROOT . 'admin/inc/firewall.class.php');
if (!defined('NO_FIREWALL')) {
    $FIREWALL = new firewall_class();
}
include (CMS_ROOT . 'admin/inc/employee.class.php');
include (CMS_ROOT . 'admin/inc/hta.master.class.php');
include (CMS_ROOT . 'admin/inc/hta.class.php');
$HTA_CLASS_CMS = new hta_class(TRUE);
$HTA_CLASS_CMS->load_sslsites_fe();
include (CMS_ROOT . 'includes/graphics.class.php');
$GRAPHIC_FUNC = new graphic_class();
include_once (CMS_ROOT . 'includes/member.class.php');
$user_obj = new member_class($GBL_LANGID);
include (CMS_ROOT . 'includes/toplevel.class.php');
include (CMS_ROOT . "includes/data.class.php");
include_once (CMS_ROOT . 'includes/crjob.class.php');
$crj_obj = new crj_class();
include (CMS_ROOT . "admin/inc/template.class.php");
include (CMS_ROOT . "includes/calendar.class.php");
include (CMS_ROOT . 'admin/inc/country.class.php');


include (CMS_ROOT . 'includes/module_loader.inc.php');
if (!defined('ISADMIN')) {
    if (isset($_GET['page']))
        $user_obj->set_permissions((int)$_GET['page']);
    $user_object = $user_obj->user_obj;
}
include_once (CMS_ROOT . 'includes/tree.class.php');
