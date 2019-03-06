<?PHP

/**
 * @package    ekomi
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

class ekomia_class extends ekomi_master_class {

    protected $ekomi = array();
    protected $client = null;

    /**
     * ekomia_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->client = new SoapClient("http://api.ekomi.de/v2/wsdl");
    }


    /**
     * ekomia_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('ekomi', $this->ekomi);
    }

    /**
     * ekomia_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('ekomi');
        $this->ekomi['conf'] = $CONFIG_OBJ->buildTable();
    }

    /*    function cmd_save_config() {
    $CONFIG_OBJ = new config_class();
    $CONFIG_OBJ->save($_POST['FORM']);
    $this->hard_exit();
    }
    */

    /**
     * ekomia_class::cmd_load_latest()
     * 
     * @return
     */
    function cmd_load_latest() {
        $this->ekomi['REVIEWS'] = $this->get_last_bewertungen();
        $this->ekomi['REVIEWS'] = $this->sort_multi_array($this->ekomi['REVIEWS'], 'create_time', SORT_DESC, SORT_NUMERIC);
    }


    /* function cronjob($params, $exec_class) {
    $start = $this->get_micro_time();
    file_put_contents(CMS_ROOT . 'cache/ekomi_bewertungen.csv', fopen('http://api.ekomi.de/get_feedback.php?interface_id=' . $this->gbl_config['ekomi_interface_id'] .
    '&interface_pw=' . $this->gbl_config['ekomi_interface_pw'] . '&version=cust-1.0.0&type=csv', 'r'));
    $sidegentime = number_format($this->get_micro_time() - $start, 4, ".", ".");
    $exec_class->feedback .= '<tr><td>eBay neue Produkte importiert</td><td>(' . $sidegentime . ' sek)</td></tr>';
    }
    */

    /**
     * ekomia_class::send_ekomi_mail()
     * 
     * @param mixed $params
     * @return
     */
    function send_ekomi_mail($params) {
        if ($this->gbl_config['ekomi_active'] == 1) {
            $ekomi_feedback_array = array('ekomi' => $this->put_order($params['ident']));
            send_mail_to(replacer($this->get_template(), $params['kid'], $ekomi_feedback_array));
        }
        return $params;
    }

    /**
     * ekomia_class::get_email_content()
     * 
     * @return
     */
    function get_email_content() {
        $settings = array(
            'auth' => $this->gbl_config['ekomi_interface_id'] . '|' . $this->gbl_config['ekomi_interface_pw'],
            'version' => 'cust-1.0.0',
            );
        $email_content = $this->client->__soapCall(strval('getSettings'), $settings);
        $email_content = unserialize($email_content);
        //echoarr($email_content['mail_plain']);
        //die;
        //return $email_content;
    }

    /**
     * ekomia_class::put_order()
     * 
     * @param mixed $oid
     * @return
     */
    function put_order($oid) {
        $order = array(
            'auth' => $this->gbl_config['ekomi_interface_id'] . '|' . $this->gbl_config['ekomi_interface_pw'],
            'version' => 'cust-1.0.0',
            'order_id' => $oid, //$oid,
            'product_ids' => '');
        $sendOrder = $this->client->__soapCall(strval('putOrder'), $order);
        $sendOrder = unserialize($sendOrder);
        $o_obj['ekomi_feedback_link'] = $sendOrder['link'];
        // update_table($this->shop_tables['TBL_ORDERS'], 'oid', $oid, $o_obj);
        return $sendOrder;
    }


    /**
     * ekomia_class::cmd_create_et()
     * 
     * @return
     */
    function cmd_create_et() {
        $FORM = $_POST['FORM'];
        $filename = $this->format_file_name($FORM['name']);
        file_put_contents(MODULE_ROOT . 'ekomi/admin/tpl_emails/' . $filename, '');
        $this->ej('reload_mail_tpls', $filename);
    }

    /**
     * ekomia_class::cmd_get_mail_tpls()
     * 
     * @return
     */
    function cmd_get_mail_tpls() {
        $arr = array();
        if ($handle = opendir(MODULE_ROOT . 'ekomi/admin/tpl_emails')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    $arr[] = array('key' => $file, 'file' => $file);
                }
            }
        }
        echo json_encode(array('mailtpls' => $arr));
        $this->hard_exit();
    }

    /**
     * ekomia_class::cmd_save_et()
     * 
     * @return
     */
    function cmd_save_et() {
        file_put_contents(MODULE_ROOT . 'ekomi/admin/tpl_emails/' . $_POST['filename'], ($_POST['mailarea']));
        $this->ej();
    }

    /**
     * ekomia_class::cmd_get_mail_con()
     * 
     * @return
     */
    function cmd_get_mail_con() {
        if (file_exists(MODULE_ROOT . 'ekomi/admin/tpl_emails/' . $_GET['ident'])) {
            echo json_encode(array('mailcontent' => stripcslashes(file_get_contents(MODULE_ROOT . 'ekomi/admin/tpl_emails/' . $_GET['ident']))));
        }
        else {
            echo json_encode(array('mailcontent' => ''));
        }

        $this->hard_exit();
    }

    /**
     * ekomia_class::get_template()
     * 
     * @return
     */
    function get_template() {
        $temp_mail = array('mailcontent' => (file_get_contents(MODULE_ROOT . 'ekomi/admin/tpl_emails/kunden.mail.link.tpl')));
        $temp_mail['add_adress'] = 0;
        $email_data = array();
        $email_data['content'] = $temp_mail['mailcontent'] . "\n" . (($temp_mail['add_adress'] == 1) ? $this->gbl_config['email_absender'] : '');
        $email_data['subject'] = $this->gbl_config['ekomi_email_subject'];
        $email_data['admin_copy'] = 1;
        return $email_data;
    }

    /**
     * ekomia_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_EKOMI_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * ekomia_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE layout_group=1 AND modident='ekomi' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

}

?>