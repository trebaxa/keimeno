<?php
/**
 * @package    Keimeno
 * @author Harald Petrich::instagram
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2019-10-18
 */

defined( 'IN_SIDE' ) or die( 'Access denied.' );

class instagram_admin_class extends instagram_master_class {

    protected $INSTAGRAM = array();

    /**
     * instagram_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

 
   
    /**
     * instagram_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
         if ($this->smarty->getTemplateVars('INSTAGRAM') != NULL) {
            $this->INSTAGRAM = array_merge($this->smarty->getTemplateVars('INSTAGRAM'), $this->INSTAGRAM);
            $this->smarty->clearAssign('INSTAGRAM');
        }
        $this->smarty->assign('INSTAGRAM', $this->INSTAGRAM);
    }   

    

    /**
     * cmd_conf()
     * 
     * @return void
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('instagram');
        $this->INSTAGRAM['CONFIG'] = $CONFIG_OBJ->buildTable();
    }

  
    /**
     * instagram_admin_class::load_homepage_integration()
     * 
     * @return
     */
    function load_homepage_integration($params) {
        return $this->load_templates_for_plugin_by_modident('instagram',$params);       
    }


    /**
     * instagram_admin_class::save_homepage_integration()
     * 
     * @return
     */
    function save_homepage_integration($params) {
        $this->save_plugin_integration($params, 'instagram');        
    }
    
}
