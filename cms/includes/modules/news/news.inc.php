<?php

/**
 * @package    news
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */
# Or visit our hompage at www.trebaxa.com									  *

defined('IN_SIDE') or die('Access denied.');


$_GET['uselang'] = ($_POST['uselang'] > 0) ? intval($_POST['uselang']) : intval($_GET['uselang']);
$_GET['uselang'] = ($_GET['uselang'] > 0) ? $_GET['uselang'] : $GBL_LANGID;
$_SESSION['ADMIN_AREA_ACTIVE'] = false;


if ($_GET['uselang'] > 0) {
    $NEWS_OBJ = new news_class($_GET['uselang']);
} else {
    $NEWS_OBJ = new news_class($GBL_LANGID);
}
$NEWS_OBJ->pageid = $_REQUEST['page'];

if ($_GET['id'] > 0) {
    $NEWS_OBJ->load_news($_GET['id']);
} else {
    $NEWS_OBJ->news['ndate'] = date('d.m.Y');
    $NEWS_OBJ->news['NID'] = 0;
    $NEWS_OBJ->news = $NEWS_OBJ->set_newslist_options($NEWS_OBJ->news);
}


if ($_GET['aktion'] != '' && $NEWS_OBJ->news['NID'] > 0 && $NEWS_OBJ->news['approval'] ==
    0 && $user_obj->user_obj['PERMOD']['newslist']['edit'] === false) {
    header('location:' . PATH_CMS . 'index.html');
    exit;
}

if (($_REQUEST['aktion'] == 'edit' || $_REQUEST['aktion'] == 'a_save' || $_REQUEST['aktion'] ==
    'a_delfile' || $_REQUEST['aktion'] == 'delicon' || $_REQUEST['aktion'] ==
    'a_approve') && !CU_LOGGEDIN) {
    $NEWS_OBJ->LOGCLASS->addLog('ILLEGAL', 'News Modul, aktion=' . $_REQUEST['aktion']);
    header('location:' . PATH_CMS . 'index.html');
    exit;
}

if ($_GET['aktion'] == 'edit') {
    $smarty->assign('groupselect', build_html_selectbox('FORM[group_id]',
        TBL_CMS_NEWSGROUPS, 'id', 'groupname', '', $NEWS_OBJ->news['group_id']));
}


if ($_POST['aktion'] == 'a_save') {
    $err_arr = array();
    $err_arr = validate_form_empty_smarty($_POST['FORM_CON']);
    $smarty->assign('form_err', $err_arr);
    if (count($err_arr) == 0) {
        if ($_POST['id'] == 0) {
            list($_POST['id'], $_POST['conid']) = $NEWS_OBJ->gen_new_news($GBL_LANGID);
            $NEWS_OBJ->set_kid($user_obj->user_obj['kid'], $_POST['id']);
            $_POST['FORM_CON']['nid'] = $_POST['id'];
        }

        if (($user_obj->user_obj['PERMOD']['newslist']['edit'] === true || $user_obj->
            user_obj['kid'] == $NEWS_OBJ->news['n_kid']) && $user_obj->user_obj['kid'] > 0) {
            $NEWS_OBJ->save_news($_POST['FORM'], $_POST['FORM_CON'], $_POST['id'], $_POST['conid'],
                $_FILES);
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
                '&aktion=edit&id=' . $NEWS_OBJ->news['NID'] . '&page=' . $page . '&msg=' .
                base64_encode('{LBLA_SAVED}'));
        } else {
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
                '&aktion=show&id=' . $_GET['id'] . '&page=' . $_GET['page'] . '&msge=' .
                base64_encode('{LBL_NOPERMISSIONS}'));
        }
        exit;
    } else {
        foreach ($_POST['FORM_CON'] as $key => $value)
            $NEWS_OBJ->news[$key] = $value;
        foreach ($_POST['FORM'] as $key => $value)
            $NEWS_OBJ->news[$key] = $value;
        $_GET['aktion'] = 'edit';
    }
}

if ($_GET['aktion'] == 'a_delfile') {
    if (($user_obj->user_obj['PERMOD']['newslist']['del'] === true || $user_obj->
        user_obj['kid'] == $NEWS_OBJ->news['n_kid']) && $user_obj->user_obj['kid'] > 0) {
        $NEWS_OBJ->del_afile((int)$_GET['fileid']);
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
            '&aktion=show&id=' . $_GET['id'] . '&page=' . $_GET['page'] . '&msg=' .
            base64_encode('{LBL_DELETED}'));
    } else {
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
            '&aktion=show&id=' . $_GET['id'] . '&page=' . $_GET['page'] . '&msge=' .
            base64_encode('{LBL_NOPERMISSIONS}'));
    }
    exit;
}

if ($_GET['aktion'] == 'delicon') {
    if (($user_obj->user_obj['PERMOD']['newslist']['del'] === true || $user_obj->
        user_obj['kid'] == $NEWS_OBJ->news['n_kid']) && $user_obj->user_obj['kid'] > 0) {
        $NEWS_OBJ->del_icon($_GET['id']);
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
            '&aktion=show&id=' . $_GET['id'] . '&page=' . $_GET['page'] . '&msg=' .
            base64_encode('{LBL_DELETED}'));
    } else {
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
            '&aktion=show&id=' . $_GET['id'] . '&page=' . $_GET['page'] . '&msge=' .
            base64_encode('{LBL_NOPERMISSIONS}'));
    }
    exit;
}

if ($_GET['aktion'] == 'a_approve' && $user_obj->user_obj['kid'] > 0) {
    if (($user_obj->user_obj['PERMOD']['newslist']['edit'] === true || $user_obj->
        user_obj['kid'] == $NEWS_OBJ->news['n_kid']) && $user_obj->user_obj['kid'] > 0) {
        $NEWS_OBJ->setApprove($_GET['value'], $_GET['id']);
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?aktion=' . (($_GET['orgaktion'] !=
            "") ? $_GET['orgaktion'] : 'show') . '&id=' . $_GET['id'] . '&page=' . $_GET['page'] .
            '&gid=' . $NEWS_OBJ->news['group_id'] . '&msg=' . base64_encode('{LBLA_SAVED}'));
    } else {
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_GET['uselang'] .
            '&aktion=show&id=' . $_GET['id'] . '&page=' . $_GET['page'] . '&msge=' .
            base64_encode('{LBL_NOPERMISSIONS}'));
    }
    exit;
}

$smarty->assign('news_obj', $NEWS_OBJ->news);

if ($_GET['aktion'] == 'shownewsdetail') {
    $CORE = new main_class();
    $CORE->set_smarty_defaults();
    $content = get_template(860, $GBL_LANGID);
    HEADER('Content-type: text/html; charset=UTF8'); #ISO-8859-1
    echo translate_language($content, $GBL_LANGID);
    exit;
}

?>