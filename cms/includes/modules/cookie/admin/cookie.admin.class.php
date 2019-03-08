<?php

/**
 * @package    cookie
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class cookie_admin_class extends cookie_master_class {

    protected $COOKIE = array();

    /**
     * cookie_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $CONFIG_OBJ = new config_class('cookie');
        $this->COOKIE['CONFIG'] = $CONFIG_OBJ->buildTable();
    }


    /**
     * cookie_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('COOKIE', $this->COOKIE);
    }


}

?>