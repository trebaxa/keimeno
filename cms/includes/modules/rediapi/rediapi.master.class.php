<?php

/**
 * @package    rediapi
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


DEFINE('TBL_CMS_REDIAPI', TBL_CMS_PREFIX . 'rediapi');


class rediapi_master_class extends modules_class {
    var $functions = array(
        array('function' => 'get_newproducts', 'label' => 'Neue Artikel'),
        array('function' => 'get_specials', 'label' => 'Angebote'),
        array('function' => 'get_bestsellers', 'label' => 'Bestsellers'),
        array('function' => 'get_specified', 'label' => 'Ausgewählte Artikel und Warengruppen'));


    /**
     * rediapi_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * rediapi_master_class::load_api()
     * 
     * @param mixed $id
     * @return
     */
    function load_api($id) {
        $API = $this->db->query_first("SELECT * FROM " . TBL_CMS_REDIAPI . " WHERE id=" . (int)$id);
        return (array )$API;
    }

    /**
     * rediapi_master_class::get_keypair()
     * 
     * @param mixed $id
     * @return
     */
    function get_keypair($id) {
        $A = $this->load_api($id);
        return $A;
    }
}
