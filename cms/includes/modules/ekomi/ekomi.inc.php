<?php



# This script is not freeware						     	*
/**
 * @package    ekomi
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$EKOMI = new ekomi_class();
$EKOMI->TCR->interpreter();
$EKOMI->parse_to_smarty();
?>