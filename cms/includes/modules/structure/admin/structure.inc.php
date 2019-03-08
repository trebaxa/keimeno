<?php

/**
 * @package    structure
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$STRUCTURE = new structure_admin_class();
$STRUCTURE->TCR->interpreter();
#$STRUCTURE->parse_to_smarty();
$STRUCTURE->add_tpl($ADMINOBJ->content, 'structure');

?>