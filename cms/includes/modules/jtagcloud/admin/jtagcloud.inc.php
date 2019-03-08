<?php

/**
 * @package    jtagcloud
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$JTAGCLOUD = new jtagcloud_admin_class();
$JTAGCLOUD->TCR->interpreter();
$JTAGCLOUD->parse_to_smarty();
$JTAGCLOUD->add_tpl($content, 'jtagcloud');
?>