<?php
/**
 * @package    callback
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class callback_admin_class extends callback_master_class
{

    protected $CALLBACK = array();

    /**
     * callback_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * callback_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item()
    {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        # fillin delete function
        $this->hard_exit();
    }

    /**
     * callback_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item()
    {
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$_GET['ident'] . "' LIMIT 1");
        $this->hard_exit();
    }


    /**
     * callback_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('CALLBACK', $this->CALLBACK);
    }

    /**
     * callback_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config()
    {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * callback_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf()
    {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }


}

?>