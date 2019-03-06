<?php

/**
 * @package    gmap
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */*


$GMAP = new gmap_admin_class();
$GMAP->TCR->interpreter();
$GMAP->parse_to_smarty();
$GMAP->add_tpl($content,'gmap');
?>