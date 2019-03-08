<?php
/**
 * @package    Keimeno
 * @author Harald Petrich::{IDENT}
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    {MODVERSION}
 * @since      {CREATEDATE}
 */

defined( 'IN_SIDE' ) or die( 'Access denied.' );

class {IDENT}_admin_class extends {IDENT}_master_class {

    protected ${IDENTUPPER} = array();

    /**
     * {IDENT}_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * {IDENT}_admin_class::cmd_axdelete_item()
     * 
     * @return void
     */
    function cmd_axdelete_item() {
        #$this->db->query("DELETE FROM ".TBL_CMS_EXAMPLE." WHERE id='".$_GET['ident']."'");
        $this->ej();
    }

    /**
     * {IDENT}_admin_class::cmd_axapprove_item()
     * 
     * @return void
     */
    function cmd_axapprove_item() {        
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$_GET['ident'] . "' LIMIT 1");
        $this->ej();
    }
    
    
    /**
     * {IDENT}_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
         if ($this->smarty->getTemplateVars('{IDENTUPPER}') != NULL) {
            $this->{IDENTUPPER} = array_merge($this->smarty->getTemplateVars('{IDENTUPPER}'), $this->{IDENTUPPER});
            $this->smarty->clearAssign('{IDENTUPPER}');
        }
        $this->smarty->assign('{IDENTUPPER}', $this->{IDENTUPPER});
    }   

    

    /**
     * cmd_conf()
     * 
     * @return void
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('{IDENT}');
        $this->{IDENTUPPER}['CONFIG'] = $CONFIG_OBJ->buildTable();
    }

  
    /**
     * {IDENT}_admin_class::load_homepage_integration()
     * 
     * @return
     */
    function load_homepage_integration($params) {
        return $this->load_templates_for_plugin_by_modident('{IDENT}',$params);       
    }


    /**
     * {IDENT}_admin_class::save_homepage_integration()
     * 
     * @return
     */
    function save_homepage_integration($params) {
        $this->save_plugin_integration($params, '{IDENT}');        
    }
    
}
