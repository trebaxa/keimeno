<?php



/**
 * @package    gearth
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


require_once (CMS_ROOT . 'admin/inc/kml.class.php');
$KML = new kml_class(CMS_ROOT, PATH_CMS);

if (!empty($_REQUEST['aktion'])) {
    $KML->interpreter($_REQUEST['aktion']);
    $KML->msg('{LBL_DONE}');
    header('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
    exit;
}


$ADMINOBJ->inc_tpl('kml');

?>