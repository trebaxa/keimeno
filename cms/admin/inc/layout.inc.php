<?php




/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



include (CMS_ROOT . 'admin/inc/layout.class.php');

$LAY = new layout_class();
$LAY->TCR->interpreter();
$LAY->gen_img_list();

$menu = array("CSS File Editor" => "section=stylelive", "CMS Bilder" => "section=showpics");

$ADMINOBJ->set_top_menu($menu);

$LAY->parse_to_smarty();
$LAY->add_tpl($ADMINOBJ->content, 'layout');

?>