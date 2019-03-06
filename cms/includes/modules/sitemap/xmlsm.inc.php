<?php

# Scripting by Trebaxa Company(R) 2010    					*

/**
 * @package    sitemap
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

# Or visit our homepage at www.trebaxa.com				    *


defined('IN_SIDE') or die('Access denied.');
$XMLSM = new xmlsm_class();
$XMLSM->TCR->interpreterfe();
$XMLSM->parse_to_smarty();

?>