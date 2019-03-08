<?php

/**
 * @package    websitesearch
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$WEBSITESEARCH = new websitesearch_admin_class();
$WEBSITESEARCH->TCR->interpreter();
$WEBSITESEARCH->parse_to_smarty();
$WEBSITESEARCH->add_tpl($content, 'websitesearch');

?>