<?php

/**
 * @package    calendar
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$_GET['uselang'] = ($_POST['uselang'] > 0) ? intval($_POST['uselang']) : intval($_GET['uselang']);
$_GET['uselang'] = ($_GET['uselang'] > 0) ? $_GET['uselang'] : $GBL_LANGID;
if ($_GET['calgid'] > 0)
    $_SESSION['calgroup_id'] = (int)$_GET['calgid'];
$_SESSION['seldate'] = ($_GET['seldate'] != "") ? $_GET['seldate'] : $_SESSION['seldate'];
$_SESSION['seldate'] = ($_SESSION['seldate'] == "") ? date('Y-m-d') : $_SESSION['seldate'];
if ($_GET['aktion'] == '')
    $_GET['aktion'] = 'showday';
#list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
#$sel_date = $d . '.' . $m . '.' . $Y;
#$locale = get_locale_of_visitor('lang');
#$cal = new tgcCalendar($d, $m, $Y, $locale);

/*
if (($_REQUEST['aktion'] == 'edit' || $_REQUEST['aktion'] == 'a_save' || $_REQUEST['aktion'] == 'a_delnews' || $_REQUEST['aktion'] == 'delicon' || $_REQUEST['aktion'] ==
    'a_approve' || $_REQUEST['aktion'] == 'a_delfile') && !CU_LOGGEDIN) {
    $TCMASTER->LOGCLASS->addLog('ILLEGAL', 'Calendar Modul, aktion=' . $_REQUEST['aktion']);
    header('location:' . PATH_CMS . 'index.html');
    exit;
}
*/

if ($_GET['uselang'] > 0) {
    $EVENT_OBJ = new event_class($_GET['uselang'], $_SESSION['seldate']);
}
else {
    $EVENT_OBJ = new event_class($GBL_LANGID, $_SESSION['seldate']);
}
$EVENT_OBJ->TCR->interpreter();

$EVENT_OBJ->load_item($_GET['id']);
if ($EVENT_OBJ->event['group_id'] > 0) {
    $_SESSION['calgroup_id'] = (int)$EVENT_OBJ->event['group_id'];
}
$_SESSION['calgroup_id'] = $EVENT_OBJ->build_allowed_group_select($_SESSION['calgroup_id'], $user_object);

/*
#*********************************
# ICON DELETE
#*********************************
if ($_GET['aktion'] == 'delicon') {
if (($user_obj->user_obj['PERMOD']['calendar']['edit'] === true || $user_obj->
user_obj['kid'] == $EVENT_OBJ->event['c_kid']) && CU_LOGGEDIN) {
$EVENT_OBJ->del_icon($_GET['id']);
HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&msg=' .
base64_encode('{LBL_DELETED}'));
} else {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?aktion=&page=' . $_GET['page'] .
'&msge=' . base64_encode('{LBL_NOPERMISSIONS}'));
}
exit;
}

#*********************************
# ITEM DELETE
#*********************************
if ($_GET['aktion'] == 'a_delnews') {
if (($user_obj->user_obj['PERMOD']['calendar']['edit'] === true || $user_obj->
user_obj['kid'] == $EVENT_OBJ->event['c_kid']) && CU_LOGGEDIN) {
$EVENT_OBJ->delete_item($_GET['id']);
HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&msg=' .
base64_encode('{LBL_DELETED}'));
} else {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
'&aktion=&page=' . $_GET['page'] . '&msge=' . base64_encode('{LBL_NOPERMISSIONS}'));
}
exit;
}

#*********************************
# APPROVAL
#*********************************
if ($_GET['aktion'] == 'a_approve') {
if (($user_obj->user_obj['PERMOD']['calendar']['edit'] === true || $user_obj->
user_obj['kid'] == $EVENT_OBJ->event['c_kid']) && CU_LOGGEDIN) {
$EVENT_OBJ->setApprove($_GET['value'], $_GET['id']);
HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] .
'&aktion=&msg=' . base64_encode('{LBLA_SAVED}'));
} else {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
'&aktion=&page=' . $_GET['page'] . '&msge=' . base64_encode('{LBL_NOPERMISSIONS}'));
}
exit;
}

#*********************************
# FILE DELETE
#*********************************
if ($_GET['aktion'] == 'a_delfile') {
if (($user_obj->user_obj['PERMOD']['calendar']['del'] === true || $user_obj->
user_obj['kid'] == $EVENT_OBJ->event['c_kid']) && CU_LOGGEDIN) {
if ($EVENT_OBJ->del_afile($_GET['fileid'])) {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] .
'&id=' . $_GET['id'] . '&aktion=edit&msg=' . base64_encode('{LBL_DELETED}'));
} else {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
'&page=' . $_GET['page'] . '&aktion=edit&id=' . $_GET['id'] . '&page=' . $_GET['page'] .
'&msge=' . base64_encode('{LBL_NOTDELETED}'));
}
} else {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
'&aktion=&page=' . $_GET['page'] . '&msge=' . base64_encode('{LBL_NOPERMISSIONS}'));
}
exit;
}

#*********************************
# SAVE
#*********************************
if ($_POST['aktion'] == 'a_save' && CU_LOGGEDIN) {
$err_arr = array();
$err_arr = validate_form_empty_smarty($_POST['FORM_CON']);
$smarty->assign('form_err', $err_arr);
if (count($err_arr) == 0) {
if ($user_obj->user_obj['PERMOD']['calendar']['edit'] === true || $user_obj->
user_obj['kid'] == $EVENT_OBJ->news['c_kid'] || ($_POST['id'] == 0 && $user_obj->
user_obj['PERMOD']['calendar']['add'] === true)) {
$EVENT_OBJ->save_item($_POST['FORM'], $_POST['FORM_CON'], $_POST['id'], $_POST['conid'],
$_FILES);
if ((int)$_POST['id'] == 0) {
$EVENT_OBJ->set_kid($user_obj->user_obj['kid'], $EVENT_OBJ->event['EID']);
}
}
if ($EVENT_OBJ->event['APPROVED'] == 1) {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
'&page=' . $_GET['page'] . '&aktion=edit&id=' . $EVENT_OBJ->event['EID'] .
'&msg=' . base64_encode('{LBLA_SAVED}'));
} else {
HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
'&page=' . $_GET['page'] . '&waitapp=1&msg=' . base64_encode('{LBLA_SAVED}'));
}
exit;
} else {
foreach ($_POST['FORM'] as $key => $value)
$EVENT_OBJ->event[$key] = $value;
$EVENT_OBJ->event['FORM_CON'] = $_POST['FORM_CON'];
$EVENT_OBJ->event = $EVENT_OBJ->set_item_options($EVENT_OBJ->event);
$_GET['aktion'] = 'edit';
}
}

#*********************************
# EDIT
#*********************************
if ($_GET['aktion'] == 'edit' && CU_LOGGEDIN) {
$EVENT_OBJ->event['group_id'] = ($EVENT_OBJ->event['group_id'] == 0) ? $_SESSION['calgroup_id'] :
$EVENT_OBJ->event['group_id'];
$editform = array(
'event' => $EVENT_OBJ->event,
'FORM_CON' => $EVENT_OBJ->event['FORM_CON'],
'uselang' => ($EVENT_OBJ->event['FORM_CON']['lang_id'] > 0) ? $EVENT_OBJ->event['FORM_CON']['lang_id'] :
$_GET['uselang'],
'id' => $EVENT_OBJ->event['EID'],
);
$smarty->assign('edit_form', $editform);
}*/

#*********************************
# LOAD COMPLETE CALENDAR YEAR
#*********************************
$CALOBJ = $EVENT_OBJ->build_year_tablelinks($_GET['page'], $_GET['aktion'], $_SESSION['seldate'], $_SESSION['calgroup_id'], $user_object, $_GET['id']);
foreach ($CALOBJ as $key => $value) {
    $smarty->assign($key, $value);
}

$EVENT_OBJ->parse_to_smarty();
