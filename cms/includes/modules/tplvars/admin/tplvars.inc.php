<?php

/**
 * @package    tplvars
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$TPLVARS = new tplvars_admin_class();
$TPLVARS->TCR->interpreter();
$TPLVARS->parse_to_smarty();
$TPLVARS->add_tpl($content, 'tplvars');

?>