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

class {IDENT}_class extends {IDENT}_master_class {
    
    var ${IDENTUPPER} = array();

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
         if ($this->smarty->getTemplateVars('{IDENTUPPER}') != NULL) {
            $this->{IDENTUPPER} = array_merge($this->smarty->getTemplateVars('{IDENTUPPER}'), $this->{IDENTUPPER});
            $this->smarty->clearAssign('{IDENTUPPER}');
        }
        $this->smarty->assign('{IDENTUPPER}', $this->{IDENTUPPER});
    }   
    
    
    /**
     * cronjob()
     * 
     * @return void
     */
    function cronjob() {
        
    }
    
    /**
     * parse_{IDENT}()
     * 
     * @param mixed $params
     * @return
     */
    function parse_{IDENT}($params) {
        $params = $this->parse_plugin_template($params, '{IDENTUPPER}');
        return $params;
    }

}
