<?php


# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    downloadcenter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


defined('IN_SIDE') or die('Access denied.');

$DOWNC = new downc_class();
$DOWNC->cmd_show_downloads();
$DOWNC->TCR->interpreterfe();

?>