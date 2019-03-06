<?php

# Scripting by Trebaxa Company(R) 2010    					*

/**
 * @package    vim
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');
$VIMEO_OBJ = new vimeocms_class();
$VIDEOTHEK_OBJ = new videothek_class();
$VIMEO_OBJ->TCR->interpreterfe();
$VIMEO_OBJ->parse_to_smarty();

$VIDEOTHEK_OBJ->TCR->interpreterfe();
$VIDEOTHEK_OBJ->build_selectbox_arr();
$VIDEOTHEK_OBJ->parse_to_smarty();

?>