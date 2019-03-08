<?php

/**
 * @package    ktracker
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');

class ktracker_class extends ktracker_master_class
{

    var $KTRACKER = array();

    /**
     * ktracker_class::__construct()
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
     * ktracker_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        if ($this->smarty->getTemplateVars('KTRACKER') != null) {
            $this->KTRACKER = array_merge($this->smarty->getTemplateVars('KTRACKER'), $this->
                KTRACKER);
            $this->smarty->clearAssign('KTRACKER');
        }
        $this->smarty->assign('KTRACKER', $this->KTRACKER);
    }


    /**
     * ktracker_class::autorun()
     * 
     * @return
     */
    function autorun()
    {
        if (isset($_GET['ktrack'])) {
            $compain = $this->load_compain($_GET['ktrack']);
            if ($compain['id'] > 0) {

                $ident = md5(session_id()); #md5(getenv('REMOTE_ADDR') . $_SERVER['HTTP_USER_AGENT']);
                $min = date('i') <= 15 ? 1 : $min;
                $min = (date('i') <= 30 && date('i') > 15) ? 2 : $min;
                $min = (date('i') <= 45 && date('i') > 30) ? 3 : $min;
                $min = (date('i') <= 59 && date('i') > 45) ? 4 : $min;
                if (!isset($_SESSION['ktrackerlog']['log'][$ident][date('ymdH') . $min])) {
                    $_SESSION['ktrackerlog']['log'][$ident][date('ymdH') . $min] = $ident;
                    $this->db->query("LOCK TABLES " . TBL_KTRACKER_LOG . " WRITE");
                    $LOG = $this->db->query_first("SELECT * FROM " . TBL_KTRACKER_LOG .
                        " WHERE kl_id=" . $compain['id'] . " AND kl_date='" . date('Y-m-d') . "'");
                    if ($LOG['kl_id'] > 0) {
                        $this->db->query("UPDATE " . TBL_KTRACKER_LOG .
                            " SET kl_count=kl_count+1 WHERE kl_date='" . date('Y-m-d') . "' AND kl_id=" . $compain['id']);
                    } else {
                        insert_table(TBL_KTRACKER_LOG, array(
                            'kl_count' => 1,
                            'kl_id' => $compain['id'],
                            'kl_date' => date('Y-m-d')));
                    }
                    $this->db->query("UNLOCK TABLES");
                }
            }
        }
    }

}

?>