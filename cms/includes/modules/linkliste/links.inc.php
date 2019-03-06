<?php


# Scripting by Trebaxa Company(R) 2010    									*

/**
 * @package    linkliste
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');

$LINKS_OBJ = new links_class();

$_GET['cid'] = (int)$_GET['cid'];
if ($_GET['cid'] == 0) {
    $_GET['cid'] = $LINKS_OBJ->get_first_cid_fe();
}

$LINKS_OBJ->load_links_fe($_GET['cid']);
$LINKS_OBJ->load_groups_fe();

?>