<?php

/**
 * @package    socialstream
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');

$SOCIALSTREAM = new socialstream_class();
$SOCIALSTREAM->TCR->interpreter();
$SOCIALSTREAM->parse_to_smarty();

?>