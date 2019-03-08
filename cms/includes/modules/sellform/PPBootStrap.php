<?php

/**
 * @package    sellform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 *
 * Include this file in your application. This file sets up the required classloader based on 
 * whether you used composer or the custom installer.
 */

//
require_once 'Configuration.php';
/*
* @constant PP_CONFIG_PATH required if credentoal and configuration is to be used from a file
* Let the SDK know where the sdk_config.ini file resides.
*/
//define('PP_CONFIG_PATH', dirname(__FILE__));

/*
* use autoloader
*/
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require 'vendor/autoload.php';
    echo "test";
    die();
}
else {
    require 'PPAutoloader.php';

    PPAutoloader::register();
}
