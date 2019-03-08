<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

# TIMEZONE >=PHP53
date_default_timezone_set('Europe/Berlin');
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);
@ini_set('magic_quotes_gpc', 1);


$arr = explode('/', $_SERVER['SCRIPT_NAME']);
array_pop($arr);
array_shift($arr);
$PATH_CMS = '/';
if (isset($arr[0])) {
    if ($arr[0] == 'admin' || $arr[0] == 'includes')
        $arr[0] = "";
    $PATH_CMS = '/' . $arr[0] . '/';
    $PATH_CMS = (empty($arr[0]) ? '/' : $PATH_CMS);
}
if (!defined('PATH_CMS')) {
    DEFINE('PATH_CMS', $PATH_CMS);
}

session_class::start_session();

@ini_set("url_rewriter.tags", "");
ignore_user_abort(TRUE);
