<?php

/**
 * @package    workshop
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$WORKSHOP = new workshop_admin_class();
$WORKSHOP->TCR->interpreter();
$WORKSHOP->parse_to_smarty();
$WORKSHOP->add_tpl($content, 'workshop');

?>