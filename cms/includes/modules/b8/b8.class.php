<?php

/**
 * @package    B8
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */
 
defined('IN_SIDE') or die('Access denied.');

class b8_class extends b8_master_class {

    var $B8 = array();

    /**
     * b8_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * b8_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('B8') != NULL) {
            $this->B8 = array_merge($this->smarty->getTemplateVars('B8'), $this->B8);
            $this->smarty->clearAssign('B8');
        }
        $this->smarty->assign('B8', $this->B8);
    }


    /**
     * b8_class::autorun()
     * 
     * @return
     */
    function autorun() {
        if (!$this->b8_is_ready || $this->gblconfig->b8_active != 1)
            return;
        $time_start = $this->microtimeFloat();
        $cracktrack = ((!defined('ISADMIN')) ? implode(' ', $_POST) . '&' : '') . implode(' ', $_GET);
        $cracktrack = stripslashes($cracktrack);
        $rating = $this->b8->classify($cracktrack);
        if ($rating >= $this->gblconfig->b8_ispam) {
            if ($this->LOGCLASS) {
                $this->LOGCLASS->addLog('B8 PROTECTOR', 'B8 Spam Filter: Hacking blocked');
            }
            firewall_class::report_hack('B8 Protector');
            if (!defined('ISADMIN')) {
                echo ('Hacking blocked.');
                $this->hard_exit();
            }
        }
        $time_taken = round($this->microtimeFloat() - $time_start, 5);
        if (!isset($_SESSION['b8_save_time']) || (int)$_SESSION['b8_save_time'] == 0) {
            file_put_contents(MODULE_ROOT . 'b8/b8/b8/timetake.txt', $time_taken);
            $_SESSION['b8_save_time'] = 1;
        }
    }

}

?>