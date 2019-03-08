<?php


# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    yt
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */




class ytbibio_class extends keimeno_class {


    /**
     * ytbibio_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();

    }

    /*  protected function add_include_path($path) {
    foreach (func_get_args() AS $path) {
    if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir')) {
    trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
    continue;
    }

    $paths = explode(PATH_SEPARATOR, get_include_path());

    if (array_search($path, $paths) === false)
    array_push($paths, $path);

    set_include_path(implode(PATH_SEPARATOR, $paths));
    }
    }*/

    /**
     * ytbibio_class::load_youtube_bibio()
     * 
     * @return
     */
    function load_youtube_bibio() {
        # $this->add_include_path(CMS_ROOT . 'includes/modules/yt');
        # include (CMS_ROOT . 'includes/modules/yt/Zend/Loader.php');
        # Zend_Loader::loadClass('Zend_Gdata_YouTube');
        include (CMS_ROOT . 'includes/modules/yt/vendor/autoload.php');
    }
}

?>