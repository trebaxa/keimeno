<?php

# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    psitemap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');

$PSITEMAP = new psitemap_class();
$PSITEMAP->TCR->interpreter();
$PSITEMAP->cmd_load_sitemap();
$PSITEMAP->parse_to_smarty();

?>