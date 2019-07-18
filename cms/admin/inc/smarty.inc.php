<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


$cmd = "";
if (isset($_REQUEST['aktion'])) {
    $cmd = ($_REQUEST['aktion'] != "") ? $_REQUEST['aktion'] : $_REQUEST['cmd'];
}
if (isset($_REQUEST['cmd'])) {
    $cmd = $_REQUEST['cmd'];
}

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;
$smarty->assign('cms_token', $_SESSION['token']);
$smarty->assign('GBLPAGE', $TCMASTER->GBLPAGE);
$smarty->assign('PHPSELF', $_SERVER['PHP_SELF']);
$smarty->assign('SSLSERVER', SSLSERVER);
$smarty->assign('SERVERVARS', $_SERVER);
$smarty->assign('btnsave', kf::gen_admin_sub_btn('{LA_SAVE}'));
$smarty->assign('subbtn', kf::gen_admin_sub_btn('{LA_SAVE}'));
$smarty->assign('nextbtn', (kf::gen_admin_sub_btn('{LBL_WEITER}')));
$smarty->assign('setbtn', kf::gen_admin_sub_btn('{LBL_SETBTN}'));
$smarty->assign('addbtn', kf::gen_admin_sub_btn('{LA_LBLADDBTN}'));
$smarty->assign('anzbtn', kf::gen_admin_sub_btn('{LBL_SHOWBTN}'));
$smarty->assign('sendbtn', kf::gen_admin_sub_btn('{LBL_SENDBTN}'));
$smarty->assign('execbtn', kf::gen_admin_sub_btn('{LBL_EXEC}', '{LBL_CONFIRM}'));
$smarty->assign('importbtn', kf::gen_admin_sub_btn('Import'));
$smarty->assign('deactivebtn', kf::gen_admin_sub_btn('Deaktivieren'));
$smarty->assign('austragenbtn', kf::gen_admin_sub_btn('austragen'));
$smarty->assign('replacebtn', kf::gen_admin_sub_btn('{LBLA_REPLACE}', '{LBL_REPLACECONFIRM}'));
$smarty->assign('searchbtn', kf::gen_admin_sub_btn('{LBLA_SEARCH}'));
$smarty->assign('btngo', kf::gen_admin_sub_btn('GO'));
$smarty->assign('btnsearch', kf::gen_admin_sub_btn('{LBL_SEARCH}'));
$smarty->assign('btnimport', kf::gen_admin_sub_btn('Import'));
$smarty->assign('project_domain', keimeno_class::get_domain_url());
$smarty->assign('aktion', $cmd);
$smarty->assign('session_id', session_id());
$smarty->assign('cmd', $cmd);
$smarty->assign('eurl', $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&');
$smarty->assign('THISURL', keimeno_class::get_domain_url() . substr($_SERVER['REQUEST_URI'], 1));
if (isset($_REQUEST['section'])) {
    $smarty->assign('section', trim(substr($_REQUEST['section'], 0, 30)));
}
$smarty->assign('GET', $_GET);
$smarty->assign('POST', $_POST);
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('is_keimeno_domain', strstr($_SERVER['SERVER_NAME'], "keimeno.de"));
$smarty->assign('cal_year_today', date('Y'));
$smarty->assign('time', time());
$smarty->assign('admin_obj', $_SESSION['admin_obj']);
$smarty->assign('alang_id', $_SESSION['alang_id']);
$smarty->assign('today', date('d.m.Y'));
$smarty->assign('cms_url', keimeno_class::get_http_protocol().'://www.' . $gbl_config['opt_domain'] . substr(PATH_CMS, -1));
$smarty->assign('loginbtn', kf::gen_admin_sub_btn('login'));
$smarty->assign('MYIP', REAL_IP);
$smarty->assign('FM_DOMAIN', FM_DOMAIN);
$smarty->assign('PATH_CMS', PATH_CMS);
$domain_arr = explode('.', $_SERVER['HTTP_HOST']);
$tld = array_pop($domain_arr);
$smarty->assign('domain', array_pop($domain_arr) . '.' . $tld);
$smarty->assign('PERM', $PERM->perm);
$smarty->assign('GBLEMP', $EMPLOYEE->employee_obj);
$smarty->assign('GBL_LOCAL_ID', $_SESSION['GBL_LOCAL_ID']);
$smarty->assign('err_msgs', $_SESSION['err_msgs']);
$smarty->assign('ok_msgs', $_SESSION['ok_msgs']);
$smarty->assign('mobiledevice', keimeno_class::is_mobile_client() == true);

$_SESSION['ok_msgs'] = array();
$_SESSION['err_msgs'] = array();
#echoarr($EMPLOYEE->employee_obj['responsible_countries']);
#echoarr($PERM->perm);


?>