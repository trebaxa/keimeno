<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::formsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-04-09
 */
 

$FORMSEND = new formsend_admin_class();
$FORMSEND->TCR->interpreter();
$FORMSEND->parse_to_smarty();
$FORMSEND->add_tpl($content,'formsend');
?>