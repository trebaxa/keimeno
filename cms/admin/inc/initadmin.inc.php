<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


DEFINE('IN_SIDE', 1);
DEFINE('NO_MODULES', 0);
@DEFINE('ISADMIN', 1);
@DEFINE('ISCMS', 1);

if (file_exists('../install.php'))
    @unlink('../install.php');

require ("../includes/system.corestartup.inc.php");
$_SESSION['CNT_TABBEDMENU'] = $_SESSION['DEBUG_ADMIN_CONTENT'] = $_SESSION['CNT_TOPMENU'] = "";
$_SESSION['ADMIN_AREA_ACTIVE'] = TRUE;


include (CMS_ROOT . 'admin/inc/pageaccess.class.php');
include (CMS_ROOT . 'admin/inc/mainadmin.class.php');
include (CMS_ROOT . 'admin/inc/languageman.class.php');
include (CMS_ROOT . 'admin/inc/adminlang.class.php');
include (CMS_ROOT . 'admin/inc/customer.class.php');
include (CMS_ROOT . 'admin/inc/backup.class.php');
include (CMS_ROOT . "admin/inc/perm.class.php");
require (CMS_ROOT . "admin/inc/functions.class.php");
#require (CMS_ROOT . "admin/admin_functions.php");

$ADMINOBJ = new mainadmin_class();
$EMPLOYEE = new employee_class();
$LNGOBJ = new language_class();

$ADMINOBJ->set_admin_defaults();


# SET EMPLOYEE
$EMPLOYEE->load_employee((int)$_SESSION['mitarbeiter']);
$PERM = $EMPLOYEE->employee_obj['PERM'];

# VALIDATE ILLEGAL LANGUAGE CALL
if (isset($_REQUEST['uselang'])) {
    $ADMINOBJ->validate_lang_call($_REQUEST['uselang']);
}

# SET LANGUAGE
if (isset($_GET['templang'])) {
    $GBL_LANGID = $ADMINOBJ->init_lang($_GET['templang']);
}
else {
    $GBL_LANGID = $ADMINOBJ->init_lang();
}

if ($LANGS[$GBL_LANGID]['local'] == "")
    $LANGS[$GBL_LANGID]['local'] = 'de';
$_SESSION['GBL_LOCAL_ID'] = $GBL_LOCAL_ID = $LANGS[$GBL_LANGID]['local'];

$ADMINOBJ->TCR->interpreter();

if (!isset($_REQUEST['axcall'])) {
    list($usec, $sec) = explode(" ", microtime());
    $sidegenstartadmin = ((float)$usec + (float)$sec);

    $_GET['tmsid'] = (isset($_GET['tmsid'])) ? $_REQUEST['tmsid'] : $_SESSION['tmsid'];
    $_SESSION['tmsid'] = (isset($_GET['tmsid'])) ? $_GET['tmsid'] : $_SESSION['tmsid'];
    $_GET['toplevel'] = (isset($_GET['toplevel'])) ? $_REQUEST['toplevel'] : $_SESSION['toplevel'];
    $_SESSION['toplevel'] = (isset($_GET['toplevel'])) ? $_GET['toplevel'] : $_SESSION['toplevel'];

    $HEADER_PAGE = array(
        'current_year' => date('Y'),
        'employee' => $EMPLOYEE->employee_obj,
        'msg' => (isset($_GET['msg'])) ? str_replace("[BR]", "<br>", base64_decode($_GET['msg'])) : '',
        'msge' => (isset($_GET['msge'])) ? str_replace("[BR]", "<br>", base64_decode($_GET['msge'])) : '',
        );
    $smarty->assign('HEADER_PAGE', $HEADER_PAGE);
    $ADMINOBJ->load_admin_menu();
    $ADMINOBJ->autorun_admin();    
    $ADMINOBJ->inc_tpl('framew.header');
}
