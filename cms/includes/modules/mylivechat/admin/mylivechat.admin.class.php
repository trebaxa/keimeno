<?php
/**
 * @package    Keimeno::mylivechat
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-06-11
 */

defined( 'IN_SIDE' ) or die( 'Access denied.' );

class mylivechat_admin_class extends mylivechat_master_class {

    protected $MYLIVECHAT = array();

    /**
     * mylivechat_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

        
    /**
     * mylivechat_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
         if ($this->smarty->getTemplateVars('MYLIVECHAT') != NULL) {
            $this->MYLIVECHAT = array_merge($this->smarty->getTemplateVars('MYLIVECHAT'), $this->MYLIVECHAT);
            $this->smarty->clearAssign('MYLIVECHAT');
        }
        $this->smarty->assign('MYLIVECHAT', $this->MYLIVECHAT);
    }   

  
    /**
     * mylivechat_admin_class::load_homepage_integration()
     * 
     * @return
     */
    function load_homepage_integration($params) {
        return $this->load_templates_for_plugin_by_modident('mylivechat',$params);       
    }


    /**
     * mylivechat_admin_class::save_homepage_integration()
     * 
     * @return
     */
    function save_homepage_integration($params) {
        $this->save_plugin_integration($params, 'mylivechat');        
    }
    
}
