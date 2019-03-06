<?php




/**
 * @package    onlinesheet
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class os_fields_admin_class extends osfields_master_class
{

    private $OSFIELD = array();
    var $langid = 1;

    /**
     * os_fields_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * os_fields_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('OSFIELD', $this->OSFIELD);
    }

    /**
     * os_fields_admin_class::cmd_msavesheet()
     * 
     * @return
     */
    function cmd_msavesheet()
    {
        $this->db->query("UPDATE " . TBL_CMS_OSSHEETS .
            " SET s_dbsave=0,s_sendpdf=0,s_custregister=0 WHERE 1");
        foreach ((array )$_POST['FORM'] as $key => $row) {
            update_table(TBL_CMS_OSSHEETS, 'id', $key, $row);
        }
        $this->ej();
    }

    /**
     * os_fields_admin_class::cmd_a_del()
     * 
     * @return
     */
    function cmd_a_del()
    {
        $this->delete_sheet($_GET['ident']);
        $this->ej();
    }

    /**
     * os_fields_admin_class::cmd_a_delfield()
     * 
     * @return
     */
    function cmd_a_delfield()
    {
        $this->del_field($_GET['ident']);
        $this->ej();
    }

    /**
     * os_fields_admin_class::saveSheet()
     * 
     * @param mixed $id
     * @param mixed $FORM
     * @return
     */
    function saveSheet($id, $FORM)
    {
        $id = intval($id);
        if ($id > 0) {
            update_table(TBL_CMS_OSSHEETS, 'id', $id, $FORM);
        } else {
            $id = insert_table(TBL_CMS_OSSHEETS, $FORM);
        }
        return $id;
    }

    /**
     * os_fields_admin_class::cmd_msave()
     * 
     * @return
     */
    function cmd_msave()
    {
        update_table(TBL_CMS_OSSHEETS, 'id', $_POST['sheetid'], (array )$_POST['FORMSHEET']);
        if ($_POST['sheetid'] > 0 && isset($_POST['FORMSHEETLANG']['t_content'])) {
            $this->db->query("DELETE FROM " . TBL_CMS_OSSHEETSLANG . " WHERE t_langid=" . $_POST['FORMSHEETLANG']['t_langid'] .
                " AND t_sid=" . $_POST['FORMSHEETLANG']['t_sid']);
            insert_table(TBL_CMS_OSSHEETSLANG, $_POST['FORMSHEETLANG']);
        }
        if ($_POST['sheetid'] > 0 && isset($_POST['FORMFIELDLANG']) && is_array($_POST['FORMFIELDLANG'])) {
            foreach ($_POST['FORMFIELDLANG'] as $column => $arr) {
                foreach ($arr as $fid => $value) {
                    $this->db->query("DELETE FROM " . TBL_CMS_OSFIELDSLANG . " WHERE t_langid=" . $_GET['uselang'] .
                        " AND t_fieldid=" . $fid);
                    $NEW = array();
                    $NEW['t_langid'] = $_GET['uselang'];
                    $NEW['t_fieldid'] = $fid;
                    $NEW[$column] = $value;
                    insert_table(TBL_CMS_OSFIELDSLANG, $NEW);
                }
            }
        }
        $_POST['sheetid'] = $this->saveSheet($_POST['sheetid'], $_POST['FORMSHEET']);
        $this->db->query("UPDATE " . TBL_CMS_OSFIELDS .
            " SET f_isemail=0,f_required=0,f_force=0,f_autoc=0 WHERE f_sheetid=" . $_POST['sheetid']);
        if (is_array($_POST['FORM'])) {
            foreach ((array )$_POST['FORM'] as $key => $row) {
                update_table(TBL_CMS_OSFIELDS, 'id', $key, $row);
            }
        }
        $this->ej();
    }

    /**
     * os_fields_admin_class::cmd_showpdf()
     * 
     * @return
     */
    function cmd_showpdf()
    {
        $hash = sha1($this->gbl_config['cms_hash_password'] . $_GET['kid'] . $_GET['an']);
        if ($hash != $_GET['hash'])
            die;
        $this->process_order($_GET['an'], $_GET['kid'], 1);
        $this->printout_pdf(true, $_GET['kid']);
    }

    /**
     * os_fields_admin_class::cmd_sendaspdf()
     * 
     * @return
     */
    function cmd_sendaspdf()
    {
        $this->process_order($_GET['an']);
        $this->sheet_obj['s_sendpdf'] == 1;
        $this->printout_pdf(false, $this->DB_USER['kid']);
        $this->msg('{LBL_SEND}');
        $this->TCR->tb();
    }

    /**
     * os_fields_admin_class::load_sheets_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_sheets_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_OSSHEETS .
            " WHERE 1 ORDER BY s_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * os_fields_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params)
    {
        $cont_matrix_id = (int)$params['id'];
        $sheet_id = $params['FORM']['os_id'];
        $SHEET = $this->db->query_first("SELECT * FROM " . TBL_CMS_OSSHEETS .
            " WHERE id=" . (int)$sheet_id);
        $SHEET = $this->set_sheet_options($SHEET);
        $upt = array('tm_content' => '{TMPL_OSSHEET_' . $cont_matrix_id . '}',
                'tm_pluginfo' => $SHEET['s_name']);
        $upt = $this->real_escape($upt);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $upt);
    }


}

?>