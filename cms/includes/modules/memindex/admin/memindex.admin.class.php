<?php

/**
 * @package    memindex
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class memindex_admin_class extends memindex_master_class {

    /**
     * memindex_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

        if (!isset($_GET['orderby']))
            $_GET['orderby'] = "nachname";
        if ($_GET['direc'] == "")
            $_GET['direc'] = "ASC";
        else
            if ($_GET['direc'] == "ASC")
                $_GET['direc'] = "DESC";
            else
                $_GET['direc'] = "ASC";
        if (!isset($_SESSION['filter']['type']))
            $_SESSION['filter']['type'] = '-';
        if (isset($_GET['type']))
            $_SESSION['filter']['type'] = $_GET['type'];
        $E = new employee_class();
        $E->load_employees();
        unset($E);
    }

    /**
     * memindex_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * memindex_admin_class::cmd_xls_kundennewsakt()
     * 
     * @return void
     */
    function cmd_xls_kundennewsakt() {
        $fieldnames = "Nachname,Vorname,Strasse,Nr,PLZ,Ort,Telefon,E-Mail,Firma,Kundengruppe,Land";
        kf::make_xls_format("nachname,vorname,strasse,hausnr,plz,ort,tel,email,firma,rabatt_gruppe,L.land", $fieldnames, TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L",
            " L.id=K.land AND mailactive=1", 'K.kid', 'Kunden_Newsletter_Aktiv_' . $_SESSION['trebaxashop_jahr']);
    }

    /**
     * memindex_admin_class::cmd_xls_kundennewsnotakt()
     * 
     * @return void
     */
    function cmd_xls_kundennewsnotakt() {
        $fieldnames = "Nachname,Vorname,Strasse,Nr,PLZ,Ort,Telefon,E-Mail,Firma,Kundengruppe,Land";
        kf::make_xls_format("nachname,vorname,strasse,hausnr,plz,ort,tel,email,firma,rabatt_gruppe,L.land", $fieldnames, TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L",
            " L.id=K.land AND mailactive=0", 'K.kid', 'Kunden_Newsletter_Inaktiv_' . $_SESSION['trebaxashop_jahr']);
    }

    /**
     * memindex_admin_class::cmd_xls_kundenfirma()
     * 
     * @return void
     */
    function cmd_xls_kundenfirma() {
        $fieldnames = "Nachname,Vorname,Firma,Strasse,Nr,PLZ,Ort,Telefon,E-Mail,Kundengruppe,Land";
        kf::make_xls_format("nachname,vorname,firma,strasse,hausnr,plz,ort,tel,email,rabatt_gruppe,L.land", $fieldnames, TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L",
            " L.id=K.land AND geschlecht='f'", 'K.kid', 'Firmenkunden_' . $_SESSION['trebaxashop_jahr']);
    }

    /**
     * memindex_admin_class::cmd_xls_kunden()
     * 
     * @return void
     */
    function cmd_xls_kunden() {
        $fieldnames = "Nachname,Vorname,Strasse,PLZ,Ort,Telefon,E-Mail,Firma,Kundengruppe,Land";
        kf::make_xls_format("nachname,vorname,strasse,plz,ort,tel,email,firma,rabatt_gruppe,L.land", $fieldnames, TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L",
            " L.id=K.land", 'K.kid', 'Kunden_' . date('Y'));
    }

    /**
     * memindex_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        global $anrede_arr;
        $CONFIG_OBJ = new config_class('memindex');
        $this->MEMINDEX['config'] = $CONFIG_OBJ->buildTable();
        $this->MEMINDEX['settings']['filter'] = $_SESSION['filter'];
        foreach ($anrede_arr as $key => $value) {
            $anrede_arr[$key] = pure_translation($value, $this->gbl_config['std_lang']);
        }
        asort($anrede_arr);
        foreach ($anrede_arr as $key => $value) {
            $this->MEMINDEX['anrede_arr'] .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $this->MEMINDEX['landselect'] = build_html_selectbox("FORM[land]", TBL_CMS_LAND, "id", "land", "ORDER BY land", 1);
        $this->smarty->assign('MEMINDEX', $this->MEMINDEX);
    }

    /**
     * memindex_admin_class::cmd_rebuild_page_index()
     * 
     * @return
     */
    function cmd_rebuild_page_index() {
        $count = $this->rebuild_page_index();
        $this->msg($count . ' bearbeitet');
        $this->ej();
    }


    /**
     * memindex_admin_class::cmd_dragdropfile_user()
     * 
     * @return void
     */
    function cmd_dragdropfile_user() {
        $kid = (int)$_GET['kid'];
        if ($kid > 0) {
            if (isset($_GET['folder']) && $_GET['folder'] != "") {
                $customer_file_root = base64_decode($_GET['folder']);
            }
            else {
                $customer_file_root = $this->get_path($kid);
            }

            if (!is_dir($customer_file_root)) {
                $customer_file_root = $this->get_path($kid);
            }

            self::add_trailing_slash($customer_file_root);
            $msge = $this->validate_file($_FILES, 'datei');

            if ($msge != "") {
                echo json_encode(array('status' => 'failed', 'filename' => $_FILES['datei']['name'] . $msge));
                $this->hard_exit();
            }

            $newfilename = $this->unique_filename($customer_file_root, $this->gbl_config['mem_file_prefix'] . $_FILES['datei']['name']);
            if (move_uploaded_file($_FILES['datei']['tmp_name'], $customer_file_root . $newfilename)) {
                chmod($customer_file_root . $newfilename, 0755);
                $this->LOGCLASS->addLog('UPLOAD', 'File upload Kunde ' . $kid . ': ' . basename($newfilename));
                $params = array('file' => $customer_file_root . $newfilename, 'kid' => $kid);
                $params = exec_evt('OnUploadCustomerFiles', $params);
            }
            else {
                echo json_encode(array('status' => 'failed', 'filename' => 'Datei eventuell größer ' . ini_get('post_max_size')));
                $this->hard_exit();
            }
            echo json_encode(array('status' => 'ok', 'filename' => $customer_file_root . $_FILES['datei']['name']));
            $this->hard_exit();
        }
        else {
            echo json_encode(array('status' => 'failed', 'filename' => $_FILES['datei']['name']));
            $this->hard_exit();
        }
    }


    /**
     * memindex_admin_class::load_tree()
     * 
     * @param mixed $kid
     * @return
     */
    function load_tree($kid) {
        $customer_file_root = $this->get_path($kid);
        #$arr = scandir($customer_file_root);
        #$tree = $this->set_tree_opt($arr, $customer_file_root);
        $arr = array();
        self::read_dirs($customer_file_root, $arr);
        $this->MEMINDEX['tree'] = $arr;
        $this->MEMINDEX['root'] = $customer_file_root;
        $this->MEMINDEX['root_hash'] = base64_encode($customer_file_root);
        return $this->MEMINDEX['tree'];
    }

    /**
     * memindex_admin_class::cmd_show_docs()
     * 
     * @return void
     */
    function cmd_show_docs() {
        $this->cmd_show_edit();
        $kid = (int)$_GET['kid'];
        if ($kid > 0) {
            $this->load_tree($kid);
        }
        $this->parse_to_smarty();
        kf::echo_template('memindex.editor.docs');
    }

    /**
     * memindex_admin_class::cmd_add_folder()
     * 
     * @return void
     */
    function cmd_add_folder() {
        $kid = (int)$_REQUEST['kid'];
        if ($kid > 0) {
            $FORM = (array )$_REQUEST['FORM'];
            $new_dir = trim(self::format_file_name($FORM['dir']));
            if ($new_dir != "") {
                $FORM['parent'] = self::add_trailing_slash(base64_decode($FORM['parent']));
                mkdir($FORM['parent'] . $new_dir, 0755);
            }
        }
        ECHO json_encode(array('parent' => $FORM['parent']));
        $this->hard_exit();
    }

    /**
     * memindex_admin_class::cmd_rename_dir()
     * 
     * @return void
     */
    function cmd_rename_dir() {
        $kid = (int)$_REQUEST['kid'];
        $FORM = (array )$_REQUEST['FORM'];
        $FORM['dir'] = trim(self::format_file_name($FORM['dir']));
        if ($FORM['dir'] != "") {
            self::add_trailing_slash($FORM['dir']);
            if ($kid > 0) {
                $old_dir_name = base64_decode($_REQUEST['dir']);
                $dir_arr = explode('/', $old_dir_name);
                array_pop($dir_arr);
                array_pop($dir_arr);
                $new_dir = implode('/', $dir_arr) . '/';
                rename($old_dir_name, $new_dir . $FORM['dir']);
            }
        }
        ECHO json_encode(array(
            'old_dir' => $old_dir_name,
            'id' => md5($new_dir . $FORM['dir']),
            'kid' => $kid,
            'new_dir' => $new_dir . $FORM['dir'],
            'hash' => base64_encode($new_dir . $FORM['dir'])));
        $this->hard_exit();
    }

    function cmd_del_folder() {
        $kid = (int)$_REQUEST['kid'];
        $folder = self::add_trailing_slash(base64_decode($_GET['folder']));
        $result = array(
            'folder' => $folder,
            'msge' => '',
            'kid' => $kid,
            'hash' => base64_encode($folder));
        if ($kid > 0 && is_dir($folder) && strpos($folder, DIRECTORY_SEPARATOR . self::add_trailing_slash($kid)) > 0) {
            self::delete_dir_with_subdirs($folder);
        }
        else {
            $result['msge'] = 'failed';
        }
        ECHO json_encode($result);
        $this->hard_exit();
    }

    /**
     * memindex_admin_class::cmd_load_folder_tree()
     * 
     * @return void
     */
    function cmd_load_folder_tree() {
        $kid = (int)$_GET['kid'];
        $this->load_tree($kid);
        $this->parse_to_smarty();
        kf::echo_template('memindex.editor.docs.tree');

    }

    /**
     * memindex_admin_class::load_files()
     * 
     * @param mixed $kid
     * @return
     */
    function admin_load_files($kid, $cdir = "") {
        $this->MEMINDEX['files'] = array();
        if ($cdir == "") {
            $cdir = $this->get_path($kid);
            if (!is_dir($cdir))
                return;
        }
        self::add_trailing_slash($cdir);
        if (is_dir($cdir)) {
            $dir = opendir($cdir);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    $hash = md5($cdir . $file . $this->gbl_config['hash_secret']);
                    if ($file != 'file_download_log.csv' && is_file($cdir . $file)) {
                        $this->MEMINDEX['files'][] = array(
                            'file' => $file,
                            'hash' => $hash,
                            'file_root' => $cdir,
                            'filehash' => base64_encode($cdir . $file),
                            'date' => date("d.m.Y H:i", filemtime($cdir . $file)),
                            'fdate_ger' => date("d.m.Y", filemtime($cdir . $file)),
                            'ftime' => filemtime($cdir . $file),
                            'size' => self::human_filesize(filesize($cdir . $file)),
                            'icons' => kf::gen_del_icon($hash, false, 'delfile', '', '&kid=' . $kid . '&file=' . base64_encode($cdir . $file)));
                    }
                }
            }
            closedir($dir);
        }
        return $this->MEMINDEX['files'];
    }


    /**
     * memindex_admin_class::reload_customer_files()
     * 
     * @return void
     */
    function cmd_reload_customer_files() {
        $kid = (int)$_GET['kid'];
        if (isset($_GET['folder']) && $_GET['folder'] != "") {
            $folder = base64_decode($_GET['folder']);
        }
        else {
            $folder = $this->get_path($kid);
        }

        $this->admin_load_files($kid, $folder);

        $params = array('files' => $this->MEMINDEX['files'], 'kid' => $kid);
        $params = exec_evt('OnLoadCustomerFiles', $params);
        $this->MEMINDEX['files'] = $params['files'];
        $this->MEMINDEX['folder'] = self::add_trailing_slash(str_replace(array(
            FILE_ROOT,
            'memindex',
            $kid . '/'), '', $folder), true);
        $this->parse_to_smarty();
        kf::echo_template('kreg.files.table');
    }

    /**
     * memindex_admin_class::cmd_delfile()
     * 
     * @return void
     */
    function cmd_delfile() {
        $kid = (int)$_GET['kid'];
        $gethash = $_GET['ident'];
        $file_to_del = base64_decode($_GET['file']);
        $hash = md5($file_to_del . $this->gbl_config['hash_secret']);
        if (is_file($file_to_del) && $hash == $gethash) {
            @unlink($file_to_del);
            $this->LOGCLASS->addLog('DELETE', 'Datei entfernt von KNR ' . $kid . ': ' . basename($file_to_del));
            $params = array('file' => $customer_file_root . $file, 'kid' => $kid);
            $params = exec_evt('OnDeleteCustomerFiles', $params);
        }
        $this->ej('deleted');
    }


    /**
     * memindex_admin_class::cmd_user_file_download()
     * 
     * @return void
     */
    function cmd_user_file_download() {
        if ($_GET['kid'] > 0) {
            $arr = self::load_files($_GET['kid']);
            foreach ($arr as $key => $row) {
                if ($row['hash'] == $_GET['hash']) {
                    self::direct_download($row['file_to_root']);
                }
            }
            firewall_class::report_hacking('Invalid secure file download ' . $_GET['kid']);
        }
        else {
            firewall_class::report_hacking('Invalid secure file download. User not logged in. ');
        }
    }


    # if ($_GET['aktion'] == "a_sedit" || $_GET['aktion'] == "" || $_GET['aktion'] == "show_edit" ) {
    function cmd_show_edit() {
        global $anrede_arr, $MODULE;
        $FORM = $this->db->query_first("SELECT K.* , L.land as COUNTRY FROM " . TBL_CMS_CUST . " K LEFT JOIN " . TBL_CMS_LAND . " L ON L.id=K.land WHERE K.kid=" . (int)
            $_GET['kid']);
        $MITARBEITER = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . intval($FORM['mit_id']));
        $POBJ = array(
            'day' => date("d"),
            'month' => date("m"),
            'year' => date("Y"),
            'date' => date("Y-m-d"),
            'delcusticon' => kf::gen_del_icon_reload($FORM['kid'], 'deletecust', '{LBL_CONFIRM}'),
            'custadress' => customer_class::format_customer_address($FORM),
            'custemailto' => '<a title="Email senden" href="mailto:' . $FORM['email'] . '">' . $FORM['email'] . '</a>',
            'desceditor' => create_html_editor('FORM[description]', $FORM['description'], 200, 'Basic'),
            'custformatedname' => customer_class::get_customer_name_from_customer_obj($FORM),
            'mailtemps' => build_options_for_selectbox(TBL_CMS_MAILTEMP, 'id', 'title', 'ORDER BY title', $_POST['emt']),
            'targetgroup' => build_options_for_selectbox(TBL_CMS_CUSTGROUPS, 'id', 'groupname', 'ORDER BY groupname', $FORM['kundengruppe']),
            'mailsendbtn' => kf::gen_admin_sub_btn('{LBL_SELECT}')); #echoarr($POBJ);die;
        $FORM['datum'] = my_date('d.m.Y', $FORM['datum']);
        $FORM['birthday'] = my_date('d.m.Y', $FORM['birthday']);
        unset($anrede_select);
        foreach ($anrede_arr as $key => $value)
            $anrede_arr[$key] = pure_translation($value, $gbl_config['std_lang']);
        asort($anrede_arr);
        foreach ($anrede_arr as $key => $value) {
            $FORM['anrede_arr'] .= '<option ' . (($key == $FORM['anrede_sign']) ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
        }
        $FORM['land'] = ($FORM['land'] == "") ? 1 : $FORM['land'];
        $FORM['landselect'] = build_html_selectbox("FORM[land]", TBL_CMS_LAND, "id", "land", "ORDER BY land", $FORM['land']);
        if ($FORM['res_gruppe'] > 0) {
            $group_obj = $this->db->query_first("SELECT * FROM " . TBL_RES_GROUPS . " WHERE id=" . (int)$FORM['res_gruppe']);
        }

        foreach ($MODULE as $mod) {
            if (is_dir($mod['epage_dir'] . $mod['id'] . '/admin/tpl')) {
                if ($dh = opendir($mod['epage_dir'] . $mod['id'] . '/admin/tpl')) {
                    while (($file = readdir($dh)) !== false) {
                        if (strstr($file, '.tpl') && strstr($file, 'kreg.modul.'))
                            $POBJ['modincs'][] = $file;
                    }
                    closedir($dh);
                }
            }
        }


        $all_membergroups_in_coll = array();
        if (defined('TBL_CMS_COLLECTION')) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION);
            while ($row = $this->db->fetch_array_names($result)) {
                $row['group_ids'] = $group_ids = explode_string_by_ident($row['col_groups']);
                if (count($group_ids) > 0) {
                    foreach ($group_ids as $gid) {
                        $G_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_RGROUPS . " WHERE id=" . (int)$gid);
                        $COL_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUSTCOLGROUPS . " WHERE kid=" . $_REQUEST['kid'] . " AND col_id=" . $row['id']);
                        $col_group_ids = explode_string_by_ident($COL_OBJ['groups']);
                        $arr = array(
                            'gid' => $gid,
                            'col_group_ids' => $col_group_ids,
                            'G_OBJ' => $G_OBJ);
                        $row['groups'][] = $arr;
                    }
                }
                $POBJ['collection'][] = $row;
            }

            $result2 = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION . " ");
            while ($row2 = $this->db->fetch_array_names($result2)) {
                $group_ids = explode_string_by_ident($row2['col_groups']);
                $all_membergroups_in_coll = array_merge($all_membergroups_in_coll, $group_ids);
            }
        }
        $cust_groups_active = array();
        $CUSTGROUPS = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid=" . (int)$FORM['kid']);
        while ($row = $this->db->fetch_array_names($CUSTGROUPS)) {
            $cust_groups_active[] = $row['gid'];
        }
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_RGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $POBJ['rgroups'][] = $row;
        }
        $POBJ['cust_groups_active'] = $cust_groups_active;
        $FORM['picture'] = ($FORM['picture'] != "") ? '../images/members/' . $FORM['picture'] : '../images/opt_member_nopic.jpg';
        $FORM['foto_exists'] = true;

        $FORM['country'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_LAND . " WHERE id=" . $FORM['land']);

        # custom fields
        require (CMS_ROOT . 'admin/inc/cfield.class.php');
        $CF = new cfield_class();
        $FORM['customer_fields'] = $CF->load_cf_fields_for_customer($FORM['kid']);

        $this->smarty->assign('CUSTOMER', $FORM);
        $this->smarty->assign('MITARBEITER', $MITARBEITER);
        $this->smarty->assign('POBJ', $POBJ);
    }

    /**
     * memindex_admin_class::cmd_edit_cust()
     * 
     * @return void
     */
    function cmd_edit_cust() {
        $this->cmd_show_edit();
        $this->parse_to_smarty();
        kf::echo_template('memindex.editor.data');
    }


    /**
     * memindex_admin_class::cmd_a_simport()
     * 
     * @return void
     */
    function cmd_a_simport() {
        $POBJ['targetgroup'] = build_options_for_selectbox(TBL_CMS_CUSTGROUPS, 'id', 'groupname', 'ORDER BY groupname', $_POST['FORM']['kundengruppe']);
        $POBJ['mitselect'] = build_options_for_selectbox(TBL_CMS_ADMINS, 'id', 'mitarbeiter_name', 'ORDER BY mitarbeiter_name', $_POST['FORM']['mit_id']);
        $POBJ['CSV_IMPORT'] = $_SESSION['CSV_IMPORT'];
        $this->smarty->assign('POBJ', $POBJ);
    }

    /**
     * memindex_admin_class::cmd_a_sendpassword()
     * 
     * @return void
     */
    function cmd_a_sendpassword() {
        /* $customer = $this->dao->load_customer($_GET['kid']);
        $customer['passwort'] = gen_sid(8);
        $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . $customer['passwort'] . "' WHERE kid='" . $customer['kid'] . "' LIMIT 1");
        send_mail_to(replacer(get_email_template(980), $customer['kid']));
        $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . encrypt_password($customer['passwort']) . "' WHERE kid='" . $customer['kid'] . "' LIMIT 1");
        */
        $customer = dao_class::load_customer($_GET['kid']);
        memindex_master_class::send_newpassword_link($customer, (int)$this->gbl_config['mem_login_page']);
        self::msg("{LBL_EMAILERHALTEN}: " . $customer['email']);
        $this->ej();
    }

    /**
     * memindex_admin_class::cmd_sendaktlink()
     * 
     * @return void
     */
    function cmd_sendaktlink() {
        $customer = $this->dao->load_customer($_GET['kid']);
        send_mail_to(replacer(get_email_template(940), $customer['kid']));
        self::msg("{LBL_EMAILERHALTEN}: " . $customer['email']);
        $this->ej();
    }

    /**
     * memindex_admin_class::cmd_senf_fileinfo_email()
     * 
     * @return void
     */
    function cmd_senf_fileinfo_email() {
        $customer = $this->dao->load_customer($_GET['kid']);
        $this->admin_load_files($_GET['kid']);
        $this->MEMINDEX['files'] = self::sort_multi_array($this->MEMINDEX['files'], 'ftime', SORT_DESC, SORT_NUMERIC);
        if (count($this->MEMINDEX['files']) > 0) {
            $new = 1;
            $olddate = $this->MEMINDEX['files'][0]['fdate_ger'];
            foreach ($this->MEMINDEX['files'] as $key => $file) {
                $this->MEMINDEX['files'][$key]['new'] = $new;
                if ($olddate != $file['fdate_ger']) {
                    $new = 0;
                }
                $olddate = $file['fdate_ger'];
            }
        }
        $this->parse_to_smarty();
        send_mail_to(replacer(get_email_template(1010), $customer['kid']));
        self::msg("{LBL_EMAILERHALTEN}: " . $customer['email']);
        $this->ej();
    }

    /**
     * memindex_admin_class::cmd_savegroupkid()
     * 
     * @return void
     */
    function cmd_savegroupkid() {
        $user_obj = new member_class();
        $user_obj->setKid($_POST['kid']);
        $user_obj->setMemGroups($_POST['MEMBERGROUPS'], $_POST['MEMBERGROUPSCOL'], true, true);
        self::msg('{LBLA_SAVED}');
        $this->ej();
    }
    /**
     * memindex_admin_class::cmd_ax_search()
     * 
     * @return void
     */
    function cmd_ax_search() {
        $orderby = ($_GET['orderby'] == "") ? 'nachname' : $_GET['orderby'];
        if ($_GET['type'] == 'a_sall') {
            $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K  WHERE K.kid>0 GROUP BY K.kid ORDER BY K.nachname");
        }
        else
            if ($_GET['type'] == 'showinactive') {
                $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K  WHERE sperren=1 GROUP BY K.kid ORDER BY K.nachname");
            }
            else
                if ($_GET['type'] == 'notindexed') {
                    $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K  WHERE cms_isindex=0 GROUP BY K.kid ORDER BY K.nachname");
                }
                else
                    if ($_GET['type'] == 'membersince') {
                        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K  WHERE 1 GROUP BY K.kid ORDER BY K.datum DESC LIMIT 500");
                    }
                    else
                        if ($_GET['type'] == 'nonewsletter') {
                            $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K  WHERE mailactive=0 GROUP BY K.kid ORDER BY K.nachname");
                        }
                        else
                            if ($_GET['type'] == 'notmember') {
                                $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K  WHERE K.kid not in (select G.kid from " . TBL_CMS_CUSTTOGROUP .
                                    " G WHERE G.gid=1100) group by K.kid, K.nachname");
                            }
                            else {
                                $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K WHERE (
	 LOWER(K.email_notpublic) LIKE LOWER('%" . $_GET['sword'] . "%') COLLATE utf8_bin OR 
	 LOWER(K.kid) LIKE LOWER('%" . $_GET['sword'] . "%') COLLATE utf8_bin OR 
	 LOWER(K.nachname) LIKE LOWER('%" . $_GET['sword'] . "%') COLLATE utf8_bin OR 
	 LOWER(K.knownof) LIKE LOWER('%" . $_GET['sword'] . "%') COLLATE utf8_bin OR 
	 LOWER(K.firma) LIKE LOWER('%" . $_GET['sword'] . "%') COLLATE utf8_bin OR 
	 LOWER(K.vorname) LIKE LOWER('%" . $_GET['sword'] . "%') COLLATE utf8_bin OR 
	 LOWER(K.email) LIKE LOWER('%" . $_GET['sword'] . "%')  COLLATE utf8_bin ) 
	 ORDER BY K." . $orderby . " " . $_GET['direc'] . "
     LIMIT 10");
                            }


                            while ($row = $this->db->fetch_array_names($result)) {
                                if ($row['mailactive'] == '1')
                                    $nactive = 'aktiv';
                                else
                                    $nactive = '-';
                                if ($this->gbl_config['login_mode'] == 'PUBLIC_EMAIL') {
                                    $email = $row['email'];
                                }
                                else
                                    if ($this->gbl_config['login_mode'] == 'NONE_PUBLIC_EMAIL') {
                                        $email = $row['email_notpublic'];
                                    }
                                    else
                                        if ($this->gbl_config['login_mode'] == 'KNR') {
                                            $email = $row['kid'];
                                        }
                                        else
                                            if ($this->gbl_config['login_mode'] == 'USERNAME') {
                                                $email = $row['username'];
                                            }
                                $row['icons'][] = kf::gen_edit_icon($row['kid'], '', 'show_edit', 'kid', 'kreg.php');
                                $row['icons'][] = kf::gen_del_icon($row['kid'], true, 'axdelete_customer', 'kreg.php');
                                $row['datum'] = my_date('d.m.Y', $row['datum']);
                                $row['land'] = get_land_of_customer_cms($row['kid']);
                                $row['general_name'] = $row['anrede'] . ' ' . $row['vorname'] . ' ' . $row['nachname'] . (($row['username'] != "") ? ', ' . $row['username'] : '');
                                $CUST['table'][] = $row;
                            }

        $this->smarty->assign('CUST', $CUST);
        kf::output('<% include file="kreg.table.tpl" %>');
    }

}
