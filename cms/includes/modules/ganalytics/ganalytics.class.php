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

class ganalytics_class extends ganalytics_master_class {

    var $GANALYTICS = array();

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
        if ($this->smarty->getTemplateVars('GANALYTICS') != NULL) {
            $this->GANALYTICS = array_merge($this->smarty->getTemplateVars('GANALYTICS'), $this->GANALYTICS);
            $this->smarty->clearAssign('GANALYTICS');
        }
        $this->smarty->assign('GANALYTICS', $this->GANALYTICS);
    }

    /**
     * ganalytics_class::OnOutput()
     * 
     * @param mixed $params
     * @return
     */
    function on_output($params) {
        $this->GANALYTICS['config'] = array(
            'anonymize_ip' => ($this->gbl_config['ga_anonymize_ip'] == 1) ? 'true' : 'false',
            'forcessl' => ($this->gbl_config['ga_forcessl'] == 1) ? 'true' : 'false',
            'send_page_view' => ($this->gbl_config['ga_send_page_view'] == 1) ? 'true' : 'false',
            'link_attribution' => ($this->gbl_config['ga_link_attribution'] == 1) ? 'true' : 'false',
            );
        $this->parse_to_smarty();
        $gcode = file_get_contents(CMS_ROOT . 'includes/modules/ganalytics/tpl/ganalytics.tpl');
        $gcode = str_replace(array("\n","\r","\t"),"", smarty_compile($gcode));
        $params['html'] = str_replace('</head>', $gcode . '</head>', $params['html']);
        return $params;
    }


}
