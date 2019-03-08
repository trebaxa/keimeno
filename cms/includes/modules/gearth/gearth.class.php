<?php

/**
 * @package    gearth
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class gearth_class extends keimeno_class
{

    var $GEARTH = array();

    /**
     * gearth_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->
            gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * gearth_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('GEARTH', $this->GEARTH);
    }

    /**
     * gearth_class::cronjob()
     * 
     * @return
     */
    function cronjob()
    {

    }

}

?>