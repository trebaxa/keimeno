<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::docsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2017-08-24
 */
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

class docsend_class extends docsend_master_class {
    
    var $DOCSEND = array();

    /**
     * __construct()
     * 
     * @return void
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
         if ($this->smarty->getTemplateVars('DOCSEND') != NULL) {
            $this->DOCSEND = array_merge($this->smarty->getTemplateVars('DOCSEND'), $this->DOCSEND);
            $this->smarty->clearAssign('DOCSEND');
        }
        $this->smarty->assign('DOCSEND', $this->DOCSEND);
    }   
    
    
    /**
     * cronjob()
     * 
     * @return void
     */
    function cronjob() {
        
    }
    
    /**
     * parse_docsend()
     * 
     * @param mixed $params
     * @return
     */
    function parse_docsend($params) {
        $params = $this->parse_plugin_template($params, 'DOCSEND');
        return $params;
    }

}
