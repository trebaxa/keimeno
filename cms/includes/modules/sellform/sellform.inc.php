<?php

# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    sellform
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


defined('IN_SIDE') or die('Access denied.');

$SELLFORM_OBJ = new sellform_class();
$SELLFORM_OBJ->langid = $GBL_LANGID;
$SELLFORM_OBJ->user_object = $user_object;
$SELLFORM_OBJ->gbl_config_shop = $gbl_config_shop;
$SELLFORM_OBJ->TCR->interpreterfe();
$SELLFORM_OBJ->load_form_fe((int)$SELLFORM_OBJ->TCR->REQUEST['formid']);
$SELLFORM_OBJ->parse_to_smarty();

?>