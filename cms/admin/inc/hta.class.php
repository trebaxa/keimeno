<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class hta_class extends htamaster_class {


    var $ssl_links = array();
    var $ssl_url = false;
    var $SSL_SITES_IDS = array();


    /**
     * hta_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }


    /**
     * hta_class::cleanHTALink()
     * 
     * @param mixed $name
     * @param mixed $set
     * @return
     */
    function cleanHTALink($name, $set) {
        $clean_arr = array(
            $set['hta_delimeter1'],
            $set['hta_delimeter2'],
            $set['hta_delimeter3'],
            $set['hta_delimeter4']);
        if ($set['hta_delimeter1'] != '/' && $set['hta_delimeter2'] != '/' && $set['hta_delimeter3'] != '/' && $set['hta_delimeter4'] != '/')
            $clean_arr[] = '/';
        $name = str_replace($clean_arr, "", $name);
        return $name;
    }

    /**
     * hta_class::genLink()
     * 
     * @param mixed $id
     * @param mixed $fillin
     * @return
     */
    function genLink($id, $fillin) {
        if (array_key_exists($id, $this->settings)) {
            $link = $this->settings[$id]['hta_prefix'];
            $k = 0;
            foreach ($fillin as $key => $value) {
                $k++;
                $link .= $this->settings[$id]['hta_delimeter' . $k] . $this->cleanHTALink($value, $this->settings[$id]);
            }
            return $this->formatLink($link . $this->settings[$id]['hta_fileext']);

        }
        return "";
    }

}
