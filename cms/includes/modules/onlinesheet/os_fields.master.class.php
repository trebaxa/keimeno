<?PHP

/**
 * @package    onlinesheet
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

class osfields_master_class extends modules_class {

    var $archive = array();
    var $all_fields_of_sheet = array();

    /**
     * osfields_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * osfields_master_class::load_customer()
     * 
     * @param mixed $kid
     * @return
     */
    function load_customer($kid) {
        $this->DB_USER = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . intval($kid));
    }

    /**
     * osfields_master_class::genAscciiJoker()
     * 
     * @param mixed $id
     * @return
     */
    function genAscciiJoker($id) {
        $abc = "";
        $zahl_arr = str_split($id);
        foreach ($zahl_arr as $zkey => $zahl)
            $abc .= chr($zahl + 65); // +65, um im ABC zu landen
        return $abc;
    }

    /**
     * osfields_master_class::convert_fieldjoker_to_smarty()
     * 
     * @param mixed $html
     * @return
     */
    function convert_fieldjoker_to_smarty($html) {
        foreach ($this->all_fields_of_sheet as $key => $row) {
            $html = str_replace($row['joker'], $row['smarty_joker'] . '<% $OS_ERR.' . $row['ident'] . ' %>', $html);
        }
        return $html;
    }


    /**
     * osfields_master_class::set_sheet_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_sheet_options($row) {
        if (ISADMIN == 1) {
            $row['icon_edit'] = kf::gen_edit_icon($row['SID'], '&epage=' . $_GET['epage'], 'edit', 'sheetid', $_SERVER['PHP_SELF']);
            $row['icon_del'] = $row['icons'][] = kf::gen_del_icon($row['SID'], true, 'a_del');
        }
        $row['ident'] = 'OS_' . $this->genAscciiJoker($row['SID']) . '_SHEET';
        $row['smarty_joker'] = htmlspecialchars('<% $os_data.' . $row['ident'] . ' %>');
        $row['joker'] = '{' . $row['ident'] . '}';
        $row['t_content'] = $this->convert_fieldjoker_to_smarty($row['t_content']);
        $this->os_data[$row['ident']] = str_replace('{TPL_FORM_TABLE}', $row['t_content'] . '<input type="hidden" name="sheetid" value="' . $row['SID'] . '" />', $this->
            form_template);
        return $row;
    }

    /**
     * osfields_master_class::del_field()
     * 
     * @param mixed $id
     * @return
     */
    function del_field($id) {
        $id = intval($id);
        $this->db->query("DELETE FROM " . TBL_CMS_OSFIELDS . " WHERE id=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_OSFIELDSLANG . " WHERE t_fieldid=" . $id);
    }


    /**
     * osfields_master_class::delete_sheet()
     * 
     * @param mixed $id
     * @return
     */
    function delete_sheet($id) {
        $id = intval($id);
        $this->load_all_fields_of_sheet($id);
        foreach ($this->all_fields_of_sheet as $key => $row) {
            $this->del_field($row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_OSSHEETSLANG . " WHERE t_sid=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_OSFIELDS . " WHERE f_sheetid=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_OSSHEETS . " WHERE id=" . $id);
    }


    /**
     * osfields_master_class::set_field_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_field_options($row) {
        if (ISADMIN == 1) {
            $row['icon_del'] = kf::gen_del_icon($row['FID'], false, 'a_delfield');
        }
        $asccii = $this->genAscciiJoker($row['FID']);
        $row['smarty_joker'] = '<% $os_fields.' . $asccii . '_FIELD %>';
        $row['joker'] = '{INPUT_' . $asccii . '_FIELD}';
        $row['ident'] = $asccii . '_FIELD';
        $row['select_options'] = explode(';', $row['f_list']);
        return $row;
    }

    /**
     * osfields_master_class::load_all_fields_of_sheet()
     * 
     * @param mixed $id
     * @return
     */
    function load_all_fields_of_sheet($id) {
        $this->all_fields_of_sheet = array();
        $result = $this->db->query("SELECT *,F.id as FID FROM " . TBL_CMS_OSFIELDS . " F 
  	LEFT JOIN " . TBL_CMS_OSFIELDSLANG . " FL ON (FL.t_fieldid=F.id AND FL.t_langid=" . $this->langid . ")
  	WHERE F.f_sheetid=" . intval($id) . " ORDER BY F.f_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_field_options($row);
            $this->all_fields_of_sheet[] = $row;
        }
    }

    /**
     * osfields_master_class::load_sheet()
     * 
     * @param mixed $id
     * @return
     */
    function load_sheet($id) {
        $this->os_data = array();
        $this->sheetid = intval($id);
        $S_OBJ = $this->db->query_first("SELECT *,S.id AS SID FROM " . TBL_CMS_OSSHEETS . " S 
	LEFT JOIN " . TBL_CMS_OSSHEETSLANG . " FL ON (FL.t_sid=S.id AND FL.t_langid=" . $this->langid . ")
	WHERE S.id=" . $this->sheetid . " ");
        $S_OBJ['fck'] = create_html_editor('FORMSHEETLANG[t_content]', $S_OBJ['t_content'], 300, 'Basic');
        $S_OBJ['fck_donemsg'] = create_html_editor('FORMSHEETLANG[t_donemsg]', $S_OBJ['t_donemsg'], 300, 'Basic');
        $S_OBJ['fck_signtext'] = create_html_editor('FORMSHEETLANG[t_signtext]', $S_OBJ['t_signtext'], 300, 'Basic');
        $S_OBJ['t_langid'] = ($S_OBJ['t_langid'] == 0) ? $this->langid : $S_OBJ['t_langid'];
        $this->sheet_obj = $this->set_sheet_options($S_OBJ);
        $this->load_all_fields_of_sheet($S_OBJ['SID']);
        return $this->sheet_obj;
    }

    /**
     * osfields_master_class::set_archive_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_archive_options($row) {
        $row['date'] = (($row['a_date'] != '0000-00-00') ? my_date('d.m.Y', $row['a_date']) : '');
        $row['date_print'] = date('d.m.Y H:i:s', $row['a_time']);
        $hash = sha1($this->gbl_config['cms_hash_password'] . $row['a_kid'] . $row['AID']);
        if (ISADMIN == 1) {
            #$row['icon_edit']			= kf::gen_edit_icon($row['AID'],'&epage='.$_GET['epage'],'showsheet','aid',$_SERVER['PHP_SELF']);
            $row['icon_pdfemail'] = '<a class="btn btn-default" href="' . $_SERVER['PHP_SELF'] . '?cmd=sendaspdf&hash=' . $hash . '&epage=' . $_GET['epage'] . '&an=' . $row['AID'] .
                '&kid=' . $row['a_kid'] . '" title="Antrag als PDF senden"><i class="fa fa-envelope-o"><!----></i></a>';
            $row['icon_pdf'] = '<a class="btn btn-default" href="' . $_SERVER['PHP_SELF'] . '?cmd=showpdf&hash=' . $hash . '&epage=' . $_GET['epage'] . '&an=' . $row['AID'] .
                '&kid=' . $row['a_kid'] . '" title="Antrag ansehen"><i class="fa fa-file-pdf-o"><!----></i></a>';
            $row['icon_del'] = kf::gen_del_icon_ajax($row['AID'], true, 'a_delarc');
        }
        $row['a_email'] = ($row['a_email'] == "") ? $row['email'] : $row['a_email'];
        $row['activate_link'] = 'http://www.' . FM_DOMAIN . PATH_CMS . 'includes/modules/onlinesheet/os.inc.php?cmd=confirm&an=' . $row['AID'] . '&n=' . $row['a_activatekey'];
        return $row;
    }

    /**
     * osfields_master_class::printout_pdf()
     * 
     * @param mixed $onfly
     * @param integer $kid
     * @return
     */
    function printout_pdf($onfly, $kid = 0) {
        $this->load_customer($kid);
        $html = utf8_decode($this->html);
        include_once (CMS_ROOT . "includes/pdf.class.php");
        $fname = CMS_ROOT . 'cache/request_' . $this->sheetid . '_' . md5(session_id() . time());
        $pdf_filename = basename($fname . '.pdf');
        $fname .= '.html';
        file_put_contents($fname, $html);
        $pdf_class = new pdf_class();
        $pdf_class->pdf_filename = $pdf_filename;
        $pdf_class->pdf_target_folder = CMS_ROOT . 'cache/';
        if ($onfly === true)
            $pdf_class->HTML2PDFonfly($fname);
        else
            $pdf_class->HTML2PDF($fname);
        $this->smarty->assign('pdf_cached_request_file', PATH_CMS . 'cache/' . $pdf_filename);
        $this->smarty->assign('order', $this->archive);
        if ($this->sheet_obj['s_sendpdf'] == 1) {
            send_mail_to(replacer(get_email_template(950), $this->DB_USER['kid']), array(CMS_ROOT . 'cache/' . $pdf_filename));
        }
    }

    /**
     * osfields_master_class::load_order()
     * 
     * @param mixed $auftragsnr
     * @return
     */
    function load_order($auftragsnr) {
        $archive = $this->db->query_first("SELECT *,A.id AS AID,S.id AS SID FROM 
		(" . TBL_CMS_OSARCHIVE . " A LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=A.a_kid)),
		(" . TBL_CMS_OSARCHIVE . " AB LEFT JOIN " . TBL_CMS_OSSHEETS . " S ON (S.id=AB.a_sheetid))
		WHERE A.id=" . $auftragsnr . "  AND AB.id=A.id       
		GROUP BY A.id");
        $archive = $this->set_archive_options($archive);
        return $archive;
    }

    /**
     * osfields_master_class::process_order()
     * 
     * @param mixed $id
     * @param integer $kid
     * @param integer $secure
     * @return
     */
    function process_order($id, $kid = 0, $secure = 0) {
        $this->auftragsnr = (int)$id;
        $this->archive = $this->db->query_first("SELECT *,A.id AS AID,S.id AS SID FROM 
		(" . TBL_CMS_OSARCHIVE . " A LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=A.a_kid)),
		(" . TBL_CMS_OSARCHIVE . " AB LEFT JOIN " . TBL_CMS_OSSHEETS . " S ON (S.id=AB.a_sheetid))
		WHERE A.id=" . $this->auftragsnr . "  AND AB.id=A.id
        " . (($secure == 1) ? " AND A.a_kid=" . (int)$kid : "") . "
		GROUP BY A.id");
        $this->archive = $this->set_archive_options($this->archive);
        $this->load_customer($this->archive['a_kid']);
        $this->load_sheet($this->archive['a_sheetid']);
        $this->html = $this->archive['a_content'];
        $this->printout_pdf(false, $this->archive['a_kid']);
    }

}

?>