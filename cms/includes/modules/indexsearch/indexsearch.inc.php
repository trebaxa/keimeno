<?php

/**
 * @package    indexsearch
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$INDEXSEARCH = new indexsearch_class();
$INDEXSEARCH->TCR->interpreter();
$INDEXSEARCH->parse_to_smarty();

?>