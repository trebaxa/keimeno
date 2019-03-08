<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::formsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-04-09
 */

defined('IN_SIDE') or die('Access denied.');

class formsend_admin_class extends formsend_master_class {

    protected $FORMSEND = array();

    /**
     * formsend_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * formsend_admin_class::cmd_axdelete_item()
     * 
     * @return void
     */
    function cmd_axdelete_item() {
        #$this->db->query("DELETE FROM ".TBL_CMS_EXAMPLE." WHERE id='".$_GET['ident']."'");
        $this->ej();
    }

    /**
     * formsend_admin_class::cmd_axapprove_item()
     * 
     * @return void
     */
    function cmd_axapprove_item() {
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$_GET['ident'] . "' LIMIT 1");
        $this->ej();
    }


    /**
     * formsend_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('FORMSEND') != NULL) {
            $this->FORMSEND = array_merge($this->smarty->getTemplateVars('FORMSEND'), $this->FORMSEND);
            $this->smarty->clearAssign('FORMSEND');
        }
        $this->smarty->assign('FORMSEND', $this->FORMSEND);
    }


    /**
     * cmd_conf()
     * 
     * @return void
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('formsend');
        $this->FORMSEND['CONFIG'] = $CONFIG_OBJ->buildTable();
    }


    /**
     * formsend_admin_class::load_homepage_integration()
     * 
     * @return
     */
    function load_homepage_integration($params) {
        return $this->load_templates_for_plugin_by_modident('formsend', $params);
    }


    /**
     * formsend_admin_class::save_homepage_integration()
     * 
     * @return
     */
    function save_homepage_integration($params) {
        $this->save_plugin_integration($params, 'formsend');
    }

}
