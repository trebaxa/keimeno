<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::ganalytics
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2019-06-04
 */
 

$GANALYTICS = new ganalytics_admin_class();
$GANALYTICS->TCR->interpreter();
$GANALYTICS->parse_to_smarty();
$GANALYTICS->add_tpl($content,'ganalytics');
?>