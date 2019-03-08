<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


$LOG = new log_class();
$LOG->clean_log();

if ($_GET['aktion'] == 'log_download') {
    $content .= $LOG->genXLS();
}

if ($_GET['aktion'] == 'alogtab') {
    $content .= $LOG->genTable();
}

if ($_GET['aktion'] == 'cleanlog') {
    $LOG->clean_log();
}
unset($LOG);
