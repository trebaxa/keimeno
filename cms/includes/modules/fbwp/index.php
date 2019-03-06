<?php




/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


DEFINE('IN_SIDE', 1);
DEFINE('NO_MODULES', 0);
$root = str_replace('includes/modules/fbwp', '', dirname(__FILE__) . '/');
include ($root . 'includes/system.corestartup.inc.php');
require './fbwp.inc.php';

?>