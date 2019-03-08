<?php


# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    contractform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


defined('IN_SIDE') or die('Access denied.');

$CONTACT = new contactform_class();
$CONTACT->init();
$CONTACT->TCR->interpreterfe();
$CONTACT->parse_to_smarty();
?>