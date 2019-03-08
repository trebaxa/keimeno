<?php



/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

$NEWSLETTER_OBJ = new newsletter_class();
$NEWSLETTER_OBJ->TCR->interpreterfe();
$NEWSLETTER_OBJ->parse_to_smarty();

?>