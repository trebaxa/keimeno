<?php
/**
 * @package    rediconn
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

class rediconn_admin_class extends rediconn_master_class {

    protected $REDICONN = array();

    /**
     * rediconn_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->cmd_conf();
    }

   

    /**
     * rediconn_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('REDICONN', $this->REDICONN);
    }


    /**
     * rediconn_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('rediconn');
        $this->REDICONN['CONFIG'] = $CONFIG_OBJ->buildTable();
    }


}

?>