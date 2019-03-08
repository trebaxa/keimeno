<?php

/**
 * @package    flickr
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


defined('IN_SIDE') or die('Access denied.');

$FLICKR = new flickr_class();
$FLICKR->TCR->interpreter();
$FLICKR->parse_to_smarty();

?>