<?php

/**
 * @package    features
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$FEATURES = new features_admin_class();
$FEATURES->TCR->interpreter();
$FEATURES->parse_to_smarty();
$FEATURES->add_tpl($content, 'features');
?>