<?php

/**
 * @package    tw
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
# Or visit our hompage at www.trebaxa.com									  *

defined('IN_SIDE') or die('Access denied.');

# **************************
# ******* SECURE ***********
# **************************
$protected_actions = array(
    'save_account',
    'tw_post_message',
    'tw_post_status');
if (in_array($_REQUEST['aktion'], $protected_actions) && !CU_LOGGEDIN) {
    $TCMASTER->LOGCLASS->addLog('ILLEGAL', 'twitter call, aktion=' . $_REQUEST['aktion']);
    header('location:' . PATH_CMS . 'index.html');
    exit;
}

/*
$access_token = $_SESSION['access_token'] = unserialize($user_object['tw_authcode']);
$_SESSION['tw_consumerkey'] = $user_object['tw_consumerkey']; #wichtig fuer callback
$_SESSION['tw_consumersecret'] = $user_object['tw_consumersecret'];
$connection = new TwitterOAuth($user_object['tw_consumerkey'], $user_object['tw_consumersecret'], $access_token['oauth_token'], $access_token['oauth_token_secret']);
$TWIT_OBJ->twconnection = $connection;


$ir = $TWIT_OBJ->interpreter($_REQUEST['aktion']);


if ($ir['status'] === TRUE) {
$ir['redirect'] = (($ir['redirect'] != "") ? $ir['redirect'] : $_SERVER['PHP_SELF'] . '?page=' . $_GET['page']);
if (!empty($ir['msg'])) {
$ir['redirect'] = $TWIT_OBJ->modify_url($ir['redirect'], array('msg' => base64_encode($ir['msg'])));
HEADER('location:' . $ir['redirect']);
}
if (!empty($ir['msge'])) {
$ir['redirect'] = $TWIT_OBJ->modify_url($ir['redirect'], array('msge' => base64_encode($ir['msge'])));
HEADER('location:' . $ir['redirect']);
}
exit;
}*/
$TW['callback_link'] = OAUTH_CALLBACK;
$smarty->assign('TW', $TW);

?>