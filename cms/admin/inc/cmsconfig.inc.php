<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



$CONFIG_OBJ = new config_class();
$CONFIG_OBJ->TCR->interpreter();

$ADMINOBJ->content .= $CONFIG_OBJ->init();
$ADMINOBJ->inc_tpl('cmsconfig');
