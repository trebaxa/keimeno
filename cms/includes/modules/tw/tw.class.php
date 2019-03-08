<?php

/**
 * @package    tw
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');

DEFINE('TW_ROOT', MODULE_ROOT . 'tw/');

require_once (TW_ROOT . 'twmaster.class.php');

class tw_class extends twitter_master_class {

    /**
     * tw_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_obj = $user_object;
        $access_token = $_SESSION['access_token'] = unserialize($this->user_obj['tw_authcode']);
        $this->usertwconfig = array(
            'consumer_key' => $this->user_obj['tw_consumerkey'],
            'consumer_secret' => $this->user_obj['tw_consumersecret'],
            'token' => $access_token['oauth_token'],
            'secret' => $access_token['oauth_token_secret'],
            'use_ssl' => true);
    }


    /**
     * tw_class::cmd_save_account()
     * 
     * @return
     */
    function cmd_save_account() {
        update_table(TBL_CMS_CUST, 'kid', $this->user_obj['kid'], $_POST['FORM']);
        $this->msg('{LBLA_SAVED}');
    }


    /**
     * tw_class::cmd_tw_post_status()
     * 
     * @return
     */
    function cmd_tw_post_status() {
        $status = $_REQUEST['status'];
        $tmhOAuth = new tmhOAuth($this->usertwconfig);
        if (ISSET($_POST['FORM']['twstatus']))
            $status = strip_tags(trim($_POST['FORM']['twstatus']));
        $code = $this->post_status_txt($tmhOAuth, $status);
        if ($code == 200) {
            $this->msg('Successfully updated Status');
        }
        else {
            $this->msge('Error connecting to Twitter: ' . $code);
        }
        if (isset($_POST['comingfrom'])) {
            header('location:' . $_POST['comingfrom']);
            $this->hard_exit();
        }
    }


    /**
     * tw_class::on_startpage()
     * 
     * @return
     */
    function on_startpage() {

    }

    /**
     * tw_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('TWITTER') != NULL) {
            $this->TWITTER = array_merge($this->smarty->getTemplateVars('TWITTER'), $this->TWITTER);
            $this->smarty->clearAssign('TWITTER');
        }
        $this->smarty->assign('TW', $this->TWITTER);
    }
}

?>