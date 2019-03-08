<?php

/**
 * @package    flickr
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$FLICKR = new flickr_admin_class();
$FLICKR->TCR->interpreter();

$menu = array("Flickr" => 'section=start', "{LA_MODCONFIGURATION}" =>
        "section=conf&cmd=conf");
$ADMINOBJ->set_top_menu($menu);

$FLICKR->parse_to_smarty();
$FLICKR->add_tpl($content, 'flickr');

?>