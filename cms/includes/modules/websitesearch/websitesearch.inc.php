<?php

/**
 * @package    websitesearch
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

if (IN_SIDE != 1) {
    header('location:' . PATH_CMS . 'index.html');
    exit;
}

$WEBSITESEARCH = new websitesearch_class();
$WEBSITESEARCH->TCR->interpreter();
$WEBSITESEARCH->parse_to_smarty();

?>