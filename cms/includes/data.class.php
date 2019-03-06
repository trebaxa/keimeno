<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class cms_data_class extends keimeno_class {
    var $langid = 1;
    var $user_object = array();
    var $user_obj = array();


    var $LASTCUSTOMERS = array();
    var $LANGS = array();

    function __construct($user_object, $langid, $user_obj) {
        parent::__construct();
        $this->user_object = $user_object;
        $this->langid = $langid;
        $this->user_obj = $user_obj;



    }

}
