<?php

/**
 * @package    forum
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$FORUM_OBJ = new forum_class($_GET['page']);
$FORUM_OBJ->TCR->interpreterfe();
$FORUM_OBJ->init();

$FORUM_OBJ->parse_to_smarty();

?>