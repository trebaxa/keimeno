<?php

/**
 * @package    onlinesheet
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class osfield_class extends osfields_master_class {

    var $field_table = array();
    var $sheet_table = array();
    var $sheet_obj = array();
    var $os_data = array();
    var $OSFORM = array();
    var $CUST = array();
    var $archives = array();
    var $archive = array();
    var $langid = 1;
    var $html = "";
    var $fieldtypes = array(
        'TEXT',
        'PASS',
        'LIST',
        'CHECK');
    var $cust_cols = array(
        'nachname' => '{CFG_SURENAME}',
        'vorname' => '{CFG_FORENAME}',
        'plz' => '{CFG_PLZ}',
        'strasse' => '{CFG_STREET}',
        'ort' => '{CFG_CITY}',
        'email' => 'Email',
        'mailactive' => 'Newsletter active',
        '' => '{LBL_OSNOCONN}',
        'tel' => '{CFG_TELEFON}');
    var $form_template = "";
    var $FORM_ERR = false;
    var $auftragsnr = 0;
    var $user_object = array();
    var $loggedin = false;


    /**
     * osfield_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->form_template = get_template(440);
        ksort($this->cust_cols);
        foreach ($this->cust_cols as $key => $value) {
            $ccols[] = array('column' => $key, 'value' => $value);
        }
        $this->smarty->assign('cust_cols', $ccols);
        $this->user_object = $user_object;
        $this->langid = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->loggedin = $this->user_object['kid'] > 0;
    }

    /**
     * osfield_class::cmd_setautoincre()
     * 
     * @return
     */
    function cmd_setautoincre() {
        $this->db->query(" ALTER TABLE " . TBL_CMS_OSARCHIVE . " AUTO_INCREMENT =" . (int)$_POST['FORM']['startauto']);
        $this->hard_exit();
    }


    /**
     * osfield_class::cmd_a_delarc()
     * 
     * @return
     */
    function cmd_a_delarc() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->delete_archive($id);
        $this->hard_exit();
    }


    /**
     * osfield_class::cmd_addfield()
     * 
     * @return
     */
    function cmd_addfield() {
        $this->add_field($_GET['sheetid']);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }


    /**
     * osfield_class::add_field()
     * 
     * @param mixed $id
     * @return
     */
    function add_field($id) {
        $id = intval($id);
        $FORM = array();
        $FORM['f_sheetid'] = $id;
        $FORM['f_name'] = ' NEU/NEW';
        $id = insert_table(TBL_CMS_OSFIELDS, $FORM);
        return $id;
    }

    /**
     * osfield_class::transform_to_inputfields()
     * 
     * @param mixed $row
     * @param mixed $OSFORM
     * @return
     */
    function transform_to_inputfields($row, $OSFORM) {
        if ($row['f_type'] == 'TEXT' && $row['f_isemail'] == 0) {
            return '<input type="text" class="form-control" ' . (($row['f_required'] == 1) ? 'required=""' : '') . ' ' . (($row['f_autoc'] == 0) ? 'autocomplete="OFF"' : '') .
                ' maxlength="' . intval($row['f_len']) . '"  name="OSFORM[' . $row['ident'] . ']" value="' . htmlspecialchars($OSFORM[$row['ident']]) . '" />';
        }
        if ($row['f_type'] == 'TEXT' && $row['f_isemail'] == 1) {
            return '<input type="email" class="form-control" ' . (($row['f_required'] == 1) ? 'required=""' : '') . ' ' . (($row['f_autoc'] == 0) ? 'autocomplete="OFF"' :
                '') . ' maxlength="' . intval($row['f_len']) . '"  name="OSFORM[' . $row['ident'] . ']" value="' . htmlspecialchars($OSFORM[$row['ident']]) . '" />';
        }
        if ($row['f_type'] == 'CHECK') {
            return '<input type="checkbox" ' . (($row['f_required'] == 1) ? 'required=""' : '') . ' ' . (($OSFORM[$row['ident']] == 1) ? 'checked' : '') . ' name="OSFORM[' .
                $row['ident'] . ']" value="1" />';
        }
        if ($row['f_type'] == 'LIST') {
            if (is_array($row['select_options']) && count($row['select_options']) > 0) {
                $sel = '<select class="form-control" name="OSFORM[' . $row['ident'] . ']">';
                foreach ($row['select_options'] as $value) {
                    $sel .= '<option ' . (($value == $OSFORM[$row['ident']]) ? 'selected' : '') . ' value="' . htmlspecialchars($value) . '">' . $value . '</option>';
                }
                $sel .= '</select>';
                return $sel;
            }
            else
                return '';
        }
    }

    /**
     * osfield_class::validate_form()
     * 
     * @param mixed $OSFORM
     * @return
     */
    function validate_form($OSFORM) {
        if (count($OSFORM) == 0)
            return false;
        $OS_ERR = array();
        foreach ($this->all_fields_of_sheet as $key => $row) {
            if (array_key_exists($row['ident'], $OSFORM) || $row['f_type'] == 'CHECK') {
                if ($row['f_force'] == 1 && $OSFORM[$row['ident']] == "") {
                    $OS_ERR[$row['ident']] = '<span class="' . $row['f_layoutclass'] . '">' . $row['f_errmsg'] . '</span>';
                }
                if ($row['f_force'] == 1 && $OSFORM[$row['ident']] == 0 && $row['f_type'] == 'CHECK') {
                    $OS_ERR[$row['ident']] = '<span class="' . $row['f_layoutclass'] . '">' . $row['f_errmsg'] . '</span>';
                }
                if ($row['f_isemail'] == 1 && !validate_email_input($OSFORM[$row['ident']])) {
                    $OS_ERR[$row['ident']] = '<span class="' . $row['f_layoutclass'] . '">' . $row['f_errmsg'] . '</span>';
                }
                if ($row['f_isemail'] == 1 && validate_email_input($OSFORM[$row['ident']])) {
                    $this->send_to_email = $OSFORM[$row['ident']];
                }
            }
        }
        $this->smarty->assign('OS_ERR', $OS_ERR);
        $this->FORM_ERR = count($OS_ERR) > 0;
    }

    /**
     * osfield_class::parse_fields_to_smarty()
     * 
     * @param mixed $OSFORM
     * @return
     */
    function parse_fields_to_smarty($OSFORM) {
        foreach ($this->all_fields_of_sheet as $key => $row) {
            $os_fields[$this->genAscciiJoker($row['FID']) . '_FIELD'] = $this->transform_to_inputfields($row, $OSFORM);
        }
        $this->smarty->assign('os_fields', $os_fields);
    }


    /**
     * osfield_class::get_sheet_ident_from_html()
     * 
     * @param mixed $html
     * @return
     */
    function get_sheet_ident_from_html($html) {
        $this->load_all_sheets();
        if (strstr($html, '{OS_')) {
            preg_match_all("={OS_(.*)_SHEET}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $wert = str_replace(array('{', '}'), '', $wert);
                $sheet_id = intval($this->sheet_table[$wert]['SID']);
                return $sheet_id;
            }
        }

    }


    /**
     * osfield_class::convert_joker_to_printout()
     * 
     * @param mixed $OSFORM
     * @return
     */
    function convert_joker_to_printout($OSFORM) {
        $os_fields = array();
        foreach ($this->all_fields_of_sheet as $key => $row) {
            if ($row['f_type'] == 'CHECK') {
                $OSFORM[$row['ident']] = ($OSFORM[$row['ident']] == 1) ? '{LBL_YES}' : '{LBL_NO}';
            }
            $os_fields[$this->genAscciiJoker($row['FID']) . '_FIELD'] = $OSFORM[$row['ident']];
        }
        $this->smarty->assign('os_fields', $os_fields);
    }


    /**
     * osfield_class::delete_archive()
     * 
     * @param mixed $id
     * @return
     */
    function delete_archive($id) {
        $this->db->query("DELETE FROM " . TBL_CMS_OSARCHIVE . " WHERE id=" . intval($id));
    }


    /**
     * osfield_class::process_orders()
     * 
     * @param mixed $order
     * @param mixed $dc
     * @return
     */
    function process_orders($order, $dc) {
        if ($dc == "")
            $dc = "DESC";
        if ($order == "")
            $order = "a_time";
        $flipped_dc = ($dc == "ASC") ? "DESC" : "ASC";
        $result = $this->db->query("SELECT K.*,A.*,A.id AS AID,S.id AS SID,S.* FROM 
		(" . TBL_CMS_OSARCHIVE . " A LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=A.a_kid)),
		(" . TBL_CMS_OSARCHIVE . " AB LEFT JOIN " . TBL_CMS_OSSHEETS . " S ON (S.id=AB.a_sheetid))
		WHERE 1 
		GROUP BY A.id
		ORDER BY A." . $order . " " . $dc);
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_archive_options($row);
            $this->archives[] = $row;
        }
        $this->smarty->assign('archives', array(
            'table' => $this->archives,
            'count' => count($this->archives),
            'flipped_dc' => $flipped_dc,
            ));
    }


    /**
     * osfield_class::load_all_sheets()
     * 
     * @return
     */
    function load_all_sheets() {
        $this->os_data = array();
        $result = $this->db->query("SELECT *,S.id AS SID FROM " . TBL_CMS_OSSHEETS . " S	
		LEFT JOIN " . TBL_CMS_OSSHEETSLANG . " FL ON (FL.t_sid=S.id AND FL.t_langid=" . $this->langid . ")
		WHERE 1
		GROUP BY S.id 
		ORDER BY S.s_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_sheet_options($row);
            $this->sheet_table[$row['ident']] = $row;
            $this->load_all_fields_of_sheet($row['SID']);

        }
        $this->scount = count($this->sheet_table);
        $this->smarty->assign('os_data', $this->os_data);
    }


    /**
     * osfield_class::load_fields_table_from_sheet()
     * 
     * @param mixed $id
     * @return
     */
    function load_fields_table_from_sheet($id) {
        $this->sheetid = intval($id);
        $result = $this->db->query("SELECT *,F.f_sheetid AS SID, F.id as FID FROM " . TBL_CMS_OSFIELDS . " F  
		LEFT JOIN " . TBL_CMS_OSFIELDSLANG . " FL ON (FL.t_fieldid=F.id AND FL.t_langid=" . $this->langid . ")
		WHERE F.f_sheetid=" . $this->sheetid . " 
		GROUP BY F.id 
		ORDER BY F.f_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_field_options($row);
            $this->field_table[] = $row;
        }
        $this->ccount = count($this->field_table);
    }

    /**
     * osfield_class::archive_sheet()
     * 
     * @param mixed $id
     * @return
     */
    function archive_sheet($id) {
        if ($this->sheet_obj['s_dbsave'] == 0)
            return 0;
        $archive = array('a_content' => $this->db->real_escape_string($this->html));
        $this->auftragsnr = $id;
        update_table(TBL_CMS_OSARCHIVE, 'id', $id, $archive);
        return $this->auftragsnr;
    }

    /**
     * osfield_class::gen_archive_id()
     * 
     * @return
     */
    function gen_archive_id() {
        $now = time();
        $archive = array(
            'a_time' => $now,
            'a_content' => '',
            'a_activatekey' => sha1($now . $this->gbl_config['cms_hash_password']),
            'a_email' => $this->CUST['email'],
            'a_date' => date('Y-m-d'),
            'a_kid' => $this->CUST['kid']);
        $this->auftragsnr = insert_table(TBL_CMS_OSARCHIVE, $archive);
        return $this->auftragsnr;
    }

    /**
     * osfield_class::cmd_confirm()
     * 
     * @return
     */
    function cmd_confirm() {
        $order = $this->load_order($_GET['an']);
        $hash = sha1($order['a_time'] . $this->gbl_config['cms_hash_password']);
        if ($hash == $_GET['n']) {
            update_table(TBL_CMS_OSARCHIVE, 'id', $id, array('a_approved' => 1));
            $this->msg('Auftrag angenommen');
        }
        else {
            $this->msge('Invaild Order');
        }
        $this->TCR->redirect('page=' . START_PAGE);
    }

    /**
     * osfield_class::gen_sheet_for_save()
     * 
     * @param mixed $id
     * @param mixed $page_content
     * @param mixed $auftragid
     * @return
     */
    function gen_sheet_for_save($id, $page_content, $auftragid) {
        $this->sheetid = intval($id);
        $this->load_sheet($this->sheetid);
        $html = $this->sheet_obj['t_content'] . $this->sheet_obj['t_signtext'];
        $this->convert_joker_to_printout($this->OSFORM);
        $pdf_template = str_replace('{TPL_OSHEET_CONTENT}', $html, $page_content);
        # $pdf_template = str_replace('{TPL_OSHEET_CONTENT}', $html . $this->sheet_obj['t_signtext'], get_template(9700));
        $this->sheet_obj['AID'] = $auftragid;
        $this->smarty->assign('OSHEET', $this->sheet_obj);
        $this->html = translate_language($pdf_template, $this->langid);
    }


    /**
     * osfield_class::register_customer()
     * 
     * @return
     */
    function register_customer() {
        if ($this->sheet_obj['s_custregister'] == 0 && $this->loggedin == false)
            return 0;

        $this->CUST = array();
        if ($this->loggedin == true) {
            $this->CUST['kid'] = $this->user_object;
        }
        else {
            foreach ($this->all_fields_of_sheet as $key => $row) {
                if ($row['f_column'] != "" && strlen($row['f_column']) > 2) {
                    if ($this->OSFORM[$row['ident']] != "")
                        $this->CUST[$row['f_column']] = $this->OSFORM[$row['ident']];
                }
            }

            if (count($this->CUST) > 0) {
                $tu = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email<>'' AND email='" . $this->CUST['email'] . "'");
                if (intval($tu['kid']) == 0) {
                    $this->CUST['kid'] = insert_table(TBL_CMS_CUST, $this->CUST);
                }
                else {
                    $this->CUST['kid'] = $tu['kid'];
                }
            }
        }
        $this->load_customer($this->CUST['kid']);
        return $this->CUST['kid'];
    }

    /**
     * osfield_class::cmd_send_os_sheet()
     * 
     * @return
     */
    function cmd_send_os_sheet() {
        if ($this->FORM_ERR == false) {
            $this->OSFORM = $_POST['OSFORM'];
            $this->load_all_fields_of_sheet($_POST['sheetid']);
            $this->load_sheet($_POST['sheetid']);
            $kid = $this->register_customer();
            $id = $this->gen_archive_id();
            $this->gen_sheet_for_save($_POST['sheetid'], get_template(9700), $id);
            $id = $this->archive_sheet($id);
            $this->process_order($id);
            $_SESSION['ossheet']['hash'] = $hash = sha1($this->gbl_config['cms_hash_password'] . $kid . $id);
            $this->TCR->redirect('page=' . $_POST['page'] . '&sheetid=' . $_POST['sheetid'] . '&an=' . $id . '&cmd=osdone&kid=' . $kid . '&hash=' . $hash);
        }
    }

    /**
     * osfield_class::cmd_osdone()
     * 
     * @return
     */
    function cmd_osdone() {
        if ((int)$_GET['an'] > 0 && $_SESSION['ossheet']['hash'] == $_GET['hash']) {
            $this->process_order($_GET['an']);
            $this->smarty->assign('order_obj', $this->archive);
        }
    }

    /**
     * osfield_class::parse_to_html()
     * 
     * @param mixed $html
     * @param mixed $OSFORM
     * @param integer $sheetid
     * @return
     */
    function parse_to_html($html, $OSFORM = array(), $sheetid = 0) {
        $sheetid = (int)$sheetid;
        if ($sheetid == 0) {
            $html = preg_replace("/({OS_\/?)(\w+)([^>]*_SHEET})/e", "", $html);
        }
        else {
            $this->load_sheet($sheetid);
            $html = str_replace($this->sheet_obj['joker'], $this->os_data[$this->sheet_obj['ident']], $html);
            $this->parse_fields_to_smarty($OSFORM); // alle Felder von allen Sheets
            $this->smarty->assign('os_obj', $this->sheet_obj);
            $this->validate_form($OSFORM); // alle Felder vom bestimmten Field
        }
        return $html;
    }

    /**
     * osfield_class::parse_ossheet_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_ossheet_inlay($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_OSSHEET_')) {
            preg_match_all("={TMPL_OSSHEET_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $sheetid = $PLUGIN_OPT['os_id'];
                $SHEET = $this->load_sheet($sheetid);
                $this->smarty->assign('TMPL_OSSHEET_' . $cont_matrix_id, $SHEET);
                if ($sheetid > 0) {
                    $html = str_replace($tpl_tag[0][$key], '<% assign var=sheet value=$TMPL_OSSHEET_' . $cont_matrix_id . ' %>' . $SHEET['joker'], $html);
                    $html = $this->parse_to_html($html, $_POST['OSFORM'], $sheetid);
                }
                else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
            }
        }
        $params['html'] = $html;
        return $params;
    }


} // CLASS


?>