<?php

/**
 * @package    resource
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$RESOURCE = new resource_admin_class();
$RESOURCE->TCR->interpreter();
$RESOURCEC = new resource_content_class();
$RESOURCEC->TCR->interpreter();

$RESOURCE->parse_to_smarty();
$RESOURCE->add_tpl($content, 'resource');
