<?PHP

/**
 * @package    contractform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */

class contact_admin_class extends keimeno_class {

    /**
     * contact_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->conf();
    }

    /**
     * contact_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('CONTACT', $this->CONTACT);
    }

    /**
     * contact_admin_class::cmd_delcontact()
     * 
     * @return
     */
    function cmd_delcontact() {
        $this->db->query("DELETE FROM " . TBL_CMS_CONTACTS . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * contact_admin_class::conf()
     * 
     * @return
     */
    function conf() {
        $CONFIG_OBJ = new config_class('contactform');
        $this->CONTACT['conf'] = $CONFIG_OBJ->buildTable();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CONTACTS . " WHERE 1 ORDER BY c_time DESC,c_sender");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['date'] = date('d.m.Y H:i', $row['c_time']);
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'delcontact');
            $row['c_disclaimer_sign'] = unserialize($row['c_disclaimer_sign']);
            $this->CONTACT['items'][] = $row;
        }
    }

    /**
     * contact_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

}
