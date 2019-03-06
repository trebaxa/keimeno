<?php

/**
 * @package    socialstream
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$SOCIALSTREAM = new socialstream_admin_class();
$SOCIALSTREAM->TCR->interpreter();
$SOCIALSTREAM->parse_to_smarty();
$SOCIALSTREAM->add_tpl($content, 'socialstream');

?>