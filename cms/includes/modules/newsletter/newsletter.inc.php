<?php

/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

if (!class_exists("newsletter_class")) {
    $dis_link = '/index.php?page=9910&cmd=a_edisable&group=' . $_GET['group'] . '&n=' . $_GET['n'];
    header('location: ' . $dis_link);
    exit();
}

defined('IN_SIDE') or die('Access denied.');

$NEWSLETTER_OBJ = new newsletter_class();
$NEWSLETTER_OBJ->TCR->interpreterfe();
$NEWSLETTER_OBJ->parse_to_smarty();
