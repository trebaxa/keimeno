<?php

/**
 * @package    content
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

include_once (CMS_ROOT . 'includes/tree.class.php');
include (CMS_ROOT . 'admin/inc/gbltemplates.class.php');

$LNGOBJ->init_uselang();

$WEBSITE_OBJ = new websites_class($CMSDATA);
$WEBSITE_OBJ->set_lang_id($_GET['uselang']);
$WEBSITE_OBJ->TCR->interpreter();


$GBLTPL_OBJ = new gbltpl_class($CMSDATA);
$GBLTPL_OBJ->set_lang_id($_GET['uselang']);
$GBLTPL_OBJ->TCR->interpreter();


$WEBSITE_OBJ->initmanager();

$INLAY_OBJ = new inlay_class();
$INLAY_OBJ->langid = $GBL_LANGID;
$INLAY_OBJ->DATA = $CMSDATA;
$INLAY_OBJ->init();
$INLAY_OBJ->TCR->interpreter();

$ADMINOBJ->inc_tpl('websitemanager');
$WEBSITE_OBJ->parse_to_smarty();

?>