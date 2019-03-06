<?php

/**
 * @package    tw
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


include (CMS_ROOT . 'admin/inc/module.class.php');
$MODULE_OBJ = new module_class(MODULE_ROOT . 'tw/');
$MODULE_OBJ->TCR->interpreter();

$TWOBJ = new twitter_admin_class();


if (ISSET($_POST['FORM']['twstatus'])) {
    $TWOBJ->format_post_txt($_POST['FORM']['twstatus'], $TWO);
}

$TWOBJ->TCR->interpreter();

$_SESSION['tw_consumerkey'] = $gbl_config['tw_consumerkey']; #wichtig fuer callback
$_SESSION['tw_consumersecret'] = $gbl_config['tw_consumersecret'];


$ADMINOBJ->set_top_menu(array('Twitter' => 'section=start', #  "Style&Files" => 'section=modstylefiles',
        "{LA_MODCONFIGURATION}" => 'section=conf&cmd=conf'));

if ($gbl_config['tw_screenname'] != "") {
    $TWOBJ->get_status();
}


$TWOBJ->parse_to_smarty();

$smarty->assign('TWO', $TWO);
$ADMINOBJ->inc_tpl('twitter');

?>