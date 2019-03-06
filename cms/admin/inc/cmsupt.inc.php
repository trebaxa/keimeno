<?php
/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

include (CMS_ROOT . 'admin/inc/update.class.php');
include (CMS_ROOT . 'admin/inc/db.backup.class.php');
include (CMS_ROOT . '/admin/inc/cmsupdt.class.php');
$backup_obj = new db_backup_class();
$backup_obj->TCR->interpreter();


$cmsupt = new cmsupt_class();

$menu = array(
    "Update" => "section=update&cmd=initupd",
    "{LBL_SYSTEMFILES}" => "section=showtools",
    "Backup CMS" => "cmd=initbackup&section=backup"    
    );
$ADMINOBJ->set_top_menu($menu);
$ADMINOBJ->inc_tpl('cmsupd');
$cmsupt->parse_to_smarty();
$backup_obj->parse_to_smarty();
include (CMS_ROOT . 'admin/inc/footer.inc.php');
