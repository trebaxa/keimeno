<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2019 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

DEFINE('IN_SIDE', 1);
DEFINE('NO_MODULES', 0);
DEFINE('IS_FRONTEND', 1);
DEFINE('CMS_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if (!ob_start("ob_gzhandler")) {
    ob_start();
}
require (CMS_ROOT . 'includes/hlock.class.php');
hlock::run(dirname(__FILE__));

list($usec, $sec) = explode(" ", microtime());
$sidegenstart = ((float)$usec + (float)$sec);

# start keimeno
include (CMS_ROOT . '/includes/system.corestartup.inc.php');

# excute engine
$CORE = new main_class();
$CORE->run($GBL_LANGID, $user_object, $MODULE);

if ($CORE->pageobj['php'] != "") {
    try {
        $inc_file = CMS_ROOT . 'includes/' . $CORE->pageobj['php'] . '.php';
        if (is_file($inc_file)) {
            require ($inc_file);
        }
        else {
            throw new kException('invalid include "' . htmlspecialchars(strip_tags($CORE->pageobj['php'])) . '"');
        }
    }
    catch (kException $e) {
        die($e->get_error_message());
    }
    $_SESSION['last_mod_exec'] = $CORE->pageobj['modident'];
}

# finish compile
$CORE->end($sidegenstart);
