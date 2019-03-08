<?php
/**
 * @package    rediconn
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
 
defined('IN_SIDE') or die('Access denied.');

class rediconn_class extends rediconn_master_class {

    var $REDICONN = array();

    /**
     * rediconn_class::__construct()
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
     * rediconn_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('REDICONN') != NULL) {
            $this->REDICONN = array_merge($this->smarty->getTemplateVars('REDICONN'), $this->REDICONN);
            $this->smarty->clearAssign('REDICONN');
        }
        $this->smarty->assign('REDICONN', $this->REDICONN);
    }

    /**
     * rediconn_class::on_delete_customer()
     * 
     * Delete customer
     * @return
     */
    public function on_delete_customer($params) {
        if (SHOP_EXISTS && $this->gbl_config['use_shop_for_customer'] == 1) {
            $kid = $params['kid'];
            if (get_data_count(TBL_ABO, 'id', "kid=" . $kid) == 0) {
                $this->db->query("DELETE FROM " . TBL_CMS_CUST . " WHERE kid=" . $kid . " LIMIT 1");
                $this->db->query("DELETE FROM " . TBL_CUNOTICE . " WHERE n_kid=" . $kid);
                $CUSTPROTO->delete_complete_protokoll($kid);
                include_once (SHOP_ROOT . 'admin/inc/notice.class.php');
                $NOT_OBJ = new notice_class($kid);
                $NOT_OBJ->delete_all_documents($kid);
                unset($NOT_OBJ);
            }
        }
        return $params;
    }


    /**
     * rediconn_class::on_core_startup()
     * 
     * @return
     */
    function on_core_startup() {
        if (file_exists(CMS_ROOT . $this->gbl_config['shop_root'] . '/admin/db_connect.php') && $this->gbl_config['redi_active'] == 1) {
            define('SHOP_EXISTS', true);
            $tmp = str_replace('//', '/', CMS_ROOT . $this->gbl_config['shop_root']);
            define('SHOP_ROOT', $tmp);
        }
        else {
            define('SHOP_EXISTS', false);
            define('SHOP_ROOT', CMS_ROOT);
        }

        if (SHOP_EXISTS && $this->gbl_config['redi_active'] == 1) {
            $shop_connect_file = file(CMS_ROOT . $this->gbl_config['shop_root'] . '/admin/db_connect.php');
            foreach ($shop_connect_file as $line) {
                if (strpos($line, 'TBL_PREFIX')) {
                    $posleft = strpos($line, "TBL_PREFIX") + strlen("TBL_PREFIX");
                    $posright = strpos($line, ");") - 1;
                    $SHOP_TAB_PREFIX = substr($line, $posleft, ($posright - $posleft) + 1);
                    $rep = array(
                        " ",
                        "'",
                        ",");
                    $SHOP_TAB_PREFIX = trim(str_replace($rep, "", $SHOP_TAB_PREFIX));
                    define('TBL_PREFIX', $SHOP_TAB_PREFIX);
                    break;
                }
            }
            $shop_tab_names = str_replace('//', '/', CMS_ROOT . $this->gbl_config['shop_root'] . '/includes/tab_names.php');
            if (file_exists($shop_tab_names))
                include_once ($shop_tab_names);
            $result = $this->db->query("SELECT * FROM " . TBL_GBLCONFIG . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $this->gbl_config_shop[$row['config_name']] = $row['config_value'];
            }
            define('PATH_SHOP', $this->gbl_config['shop_root']);
            $this->gbl_config_shop['opt_site_domain'] = "http://" . $_SERVER['HTTP_HOST'] . $this->gbl_config['shop_root'];
        }

        if (SHOP_EXISTS && $this->gbl_config['use_shop_for_customer'] == 1 && $this->gbl_config['redi_active'] == 1) {
            define('SHOP_CUST_USE', true);
            define('TBL_CMS_CUST', TBL_KUNDEN);
            #define('TBL_CMS_CUSTGROUPS', TBL_CMS_RGROUPS);
            define('TBL_CMS_CUSTGROUPS', TBL_CMS_RGROUPS);
            $shop_link = $this->gbl_config['opt_site_domain'] . $this->gbl_config['shop_root'];
            $shop_link = str_replace('//', '/', $shop_link);
            $shop_link = str_replace('http:/w', 'http://w', $shop_link);
            define('SHOP_LINK', $shop_link);
            unset($shop_link);

        }
        else {
            define('SHOP_CUST_USE', false);
            define('TBL_CMS_CUST', TBL_CMS_KUNDEN);
            define('TBL_CMS_CUSTGROUPS', TBL_CMS_RGROUPS);
        }
        if (SHOP_EXISTS === TRUE) {
            if (file_exists(SHOP_ROOT . 'includes/cmsshopsharedclasses.inc.php')) {
                $LANGS_APPROVED = $LANGSFE;
                $smarty = $this->smarty;
               /* include (SHOP_ROOT . 'admin/inc/tc.class.php');
                include (SHOP_ROOT . 'admin/inc/tcrequest.class.php');
                include (SHOP_ROOT . 'includes/cmsshopsharedfunctions.inc.php');
                include (SHOP_ROOT . 'includes/cmsshopsharedclasses.inc.php');
                */
            }
        }

    }

}
