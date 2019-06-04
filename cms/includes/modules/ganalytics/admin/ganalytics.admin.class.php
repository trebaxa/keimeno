<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::ganalytics
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2019-06-04
 */

defined('IN_SIDE') or die('Access denied.');

class ganalytics_admin_class extends ganalytics_master_class {

    protected $GANALYTICS = array();

    /**
     * ganalytics_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $CONFIG_OBJ = new config_class('ganalytics');
        $this->GANALYTICS['CONFIG'] = $CONFIG_OBJ->buildTable();
    }


    /**
     * ganalytics_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('GANALYTICS') != NULL) {
            $this->GANALYTICS = array_merge($this->smarty->getTemplateVars('GANALYTICS'), $this->GANALYTICS);
            $this->smarty->clearAssign('GANALYTICS');
        }
        $this->smarty->assign('GANALYTICS', $this->GANALYTICS);
    }


}
