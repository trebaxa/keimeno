<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


if (!defined("TBL_CMS_CUSTCOLGROUPS"))
    DEFINE('TBL_CMS_CUSTCOLGROUPS', TBL_CMS_PREFIX . 'custcolgroups');
if (!defined("TBL_CMS_COLLECTION"))
    DEFINE('TBL_CMS_COLLECTION', TBL_CMS_PREFIX . 'cust_collect');

class customer_class extends keimeno_class {

    /**
     * customer_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * customer_class::cmd_axdelete_customer()
     * 
     * @return
     */
    function cmd_axdelete_customer() {
        $this->delete_customer($_GET['ident']);
        $this->ej();
    }

    /**
     * customer_class::cmd_deletecust()
     * 
     * @return
     */
    function cmd_deletecust() {
        $this->delete_customer($_GET['id']);
        keimeno_class::msg("{LBL_DELETED}.");
        HEADER("location:" . $_SERVER['PHP_SELF']);
        $this->hard_exit();
    }

    /**
     * customer_class::delete_customer()
     * 
     * @param mixed $kid
     * @return
     */
    function delete_customer($kid) {
        global $CUSTPROTO;
        $kid = (int)$kid;
        $params = array('kid' => $kid);
        exec_evt('OnDeleteCustomer', $params, $this);
        if (!class_exists('member_class')) {
            include_once (CMS_ROOT . 'includes/modules/memindex/memindex.php');
        }
        $MEM = new member_class();
        $MEM->delete_customer($kid);
    }

    /**
     * customer_class::load_custable()
     * 
     * @return
     */
    function load_custable() {
        $valid = array(
            'nachname',
            'vorname',
            'ort',
            'email',
            'kid',
            'mailactive',
            'sperren',
            'datum',
            'plz',
            'L.land',
            'UMSATZ_SQL',
            'email_notpublic',
            'firma');
        if ($this->TCR->GET['dc'] == "")
            $this->TCR->GET['dc'] = $this->gbl_config['admin_custstdsortdirec'];
        $dc = ($this->TCR->GET['dc'] == "ASC") ? 'ASC' : 'DESC';
        $start = ($this->TCR->GET['start'] == 0) ? 0 : (int)$this->TCR->GET['start'];
        $column = ($this->TCR->GET['col'] == "") ? $this->gbl_config['admin_custstdsort'] : $this->TCR->GET['col'];
        if (!in_array($column, $valid))
            die('access denied');
        $result = $this->db->query("SELECT K.*,L.land AS COUNTRYNAME FROM " . TBL_CMS_CUST . " K, " . TBL_CMS_LAND . " L 	
	 WHERE L.id=K.land
	 ORDER BY " . $column . " " . $dc . " LIMIT " . $start . ", 50");
        while ($row = $this->db->fetch_array_names($result)) {
            #   $row['icons'][] = kf::gen_edit_icon($row['kid'], '', 'edit', 'kid', 'kreg.php');
            $row['icons'][] = kf::gen_del_icon($row['kid'], true, 'axdelete_customer');
            $row['datum_ger'] = my_date('d.m.Y', $row['datum']);
            $row['land'] = get_land_of_customer_cms($row['kid']);
            $this->CUST['table'][] = $row;
        }
        #echoarr($this->CUST);die;
        $count = get_data_count(TBL_CMS_CUST, 'kid', '1');
        $flipped_dc = ($this->TCR->GET['dc'] == "DESC") ? "ASC" : "DESC";
        $_paging = $this->genPaging($start, 50, $count, '&col=' . $column . '&dc=' . $dc);
        $_paging['count_pro_page'] = 50;
        $_paging['flipped_dc'] = $flipped_dc;
        $this->smarty->assign('paging', $_paging);
    }


    /**
     * customer_class::gen_customer_paging_link_admin()
     * 
     * @param mixed $start
     * @param string $toadd
     * @return
     */
    function gen_customer_paging_link_admin($start, $toadd = '') {
        return $_SERVER['PHP_SELF'] . '?start=' . $start . $toadd;
    }

    /**
     * customer_class::genPaging()
     * 
     * @param mixed $ovStart
     * @param mixed $max_paging
     * @param mixed $total
     * @param string $toadd
     * @return
     */
    function genPaging($ovStart, $max_paging, $total, $toadd = '') {
        define('NUM_PREPAGES', 6);
        $start = (isset($ovStart)) ? abs((int)$ovStart) : 0;
        $total_pages = ceil($total / $max_paging);
        $akt_page = round($start / $max_paging) + 1;
        if ($total_pages > 0)
            $akt_pages = $akt_page . '/' . $total_pages;
        $start = ($start > $total) ? $total - $max_paging : $start;
        $next_pages_arr = $back_pages_arr = array();
        if ($start > 0)
            $newStartBack = ($start - $max_paging < 0) ? 0 : ($start - $max_paging);
        if ($start > 0) {
            for ($i = NUM_PREPAGES - 1; $i >= 0; $i--) {
                if ($newStartBack - ($i * $max_paging) >= 0) {
                    $back_pages_arr[] = array(
                        'link' => $_SERVER['PHP_SELF'] . '?start=' . ($newStartBack - ($i * $max_paging)),
                        'linkadmin' => $this->gen_customer_paging_link_admin(($newStartBack - ($i * $max_paging)), $toadd),
                        'index' => ($akt_page - $i - 1));
                }
            }
        }
        if ($start + $max_paging < $total) {
            $newStart = $start + $max_paging;
            for ($i = 0; $i < NUM_PREPAGES; $i++) {
                if ($newStart + ($i * $max_paging) < $total) {
                    $next_pages_arr[] = array(
                        'link' => '', #genCustPicAlbumLinkPaging($get_cid,$this->gallery_obj['groupname'],($newStart+($i*$max_paging))),
                        'linkadmin' => $this->gen_customer_paging_link_admin(($newStart + ($i * $max_paging)), $toadd),
                        'index' => ($akt_page + $i + 1));
                }
            }
        }
        $_paging['start'] = $start;
        $_paging['total_pages'] = $total_pages;
        $_paging['startback'] = $newStartBack;
        $_paging['newstart'] = $newStart;
        $_paging['base_link'] = '';
        $_paging['base_link_admin'] = $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . $toadd;
        $_paging['back_pages'] = $back_pages_arr;
        $_paging['akt_page'] = $akt_page;
        $_paging['next_pages'] = $next_pages_arr;
        $_paging['backlink'] = $this->gen_customer_paging_link_admin($newStartBack, $toadd);
        $_paging['nextlink'] = $this->gen_customer_paging_link_admin($newStart, $toadd);
        $_paging['product_count_total'] = $total;
        $_paging['count_total'] = $total;
        return $_paging;
    }

    /**
     * customer_class::update_hausnummer()
     * 
     * @param mixed $kid
     * @return
     */
    function update_hausnummer($kid) {
        $K = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE hausnr='' && kid=" . (int)$kid);
        if ($K['kid'] > 0) {
            $hausnummer = explode(' ', $K['strasse']);
            $hausnr = substr($hausnummer[count($hausnummer) - 1], 0, 5);
            $str = str_replace($hausnr, '', $K['strasse']);
            $this->db->query("UPDATE " . TBL_CMS_CUST . " SET strasse='" . $this->db->real_escape_string(trim($str)) . "',hausnr='" . $this->db->real_escape_string($hausnr) .
                "' WHERE kid=" . $K['kid']);
        }
    }

    /**
     * customer_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('CUST', $this->CUST);
    }


    /**
     * customer_class::cmd_load_cust_json()
     * 
     * @return
     */
    function cmd_load_cust_json() {
        $CUST = $this->load_customer($_GET['kid']);
        $CUST['picture'] = ($CUST['picture'] != "") ? '../images/members/' . $CUST['picture'] : '../images/opt_member_nopic.jpg';
        echo json_encode($CUST);
        $this->hard_exit();
    }

    /**
     * customer_class::load_customer()
     * 
     * @param mixed $kid
     * @return
     */
    function load_customer($kid) {
        return $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . "  WHERE kid=" . (int)$kid);
    }

    /**
     * customer_class::cmd_del_img()
     * 
     * @return
     */
    function cmd_del_img() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $_GET['kid']);
        if (delete_file(CMS_ROOT . 'images/members/' . $FORM['picture']))
            $this->db->query("UPDATE " . TBL_CMS_CUST . " SET picture='' WHERE kid='" . $_GET['kid'] . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * get_customer_name_from_customer_obj()
     * 
     * @param mixed $k_obj
     * @return string
     */
    public static function get_customer_name_from_customer_obj($k_obj) {
        if ($k_obj['nachname'] && $k_obj['firma'] == "")
            return $k_obj['nachname'] . ", " . $k_obj['vorname'];
        if ($k_obj['firma'] != "")
            return '<span class="firma">' . $k_obj['firma'] . '</span></span class="inhaber">' . $k_obj['firma_inhaber'] . '</span>';
    }

    /**
     * customer_class::cmd_change_cust_foto()
     * 
     * @return
     */
    function cmd_change_cust_foto() {
        $ext_file = strtolower(strrchr($_FILES['datei']['name'], '.'));
        $ext_target = '.jpg';
        if ($ext_file != $ext_target) {
            keimeno_class::msge('Erlaubter Dateityp: ' . $ext_target);
            $this->ej();
        }
        if (!validate_upload_file($_FILES['datei'], TRUE)) {
            keimeno_class::msge($_SESSION['upload_msge']);
            $this->ej();
        }
        if (!is_dir(CMS_ROOT . 'images/members'))
            mkdir(CMS_ROOT . 'images/members', 0755);
        $target = CMS_ROOT . 'images/members/member_' . $_POST['kid'] . '.jpg';
        delete_file($target);
        move_uploaded_file($_FILES['datei']['tmp_name'], $target);
        chmod($target, 0755);
        $this->db->query("UPDATE " . TBL_CMS_CUST . " SET picture='" . basename($target) . "' WHERE kid='" . $_POST['kid'] . "' LIMIT 1");
        keimeno_class::msg($target . " {LBLA_SAVED}.");
        $this->ej('reload_item');
    }

    /**
     * format_customer_address()
     * 
     * @param mixed $k_obj
     * @return string
     */
    public static function format_customer_address($k_obj) {
        if ($k_obj['firma'] != "") {
            $adr = $k_obj['firma'] . '<br>' . $k_obj['firma_inhaber'] . '<br>';
        }
        if ($k_obj['nachname'] && $k_obj['firma_inhaber'] == "")
            $adr .= $k_obj['vorname'] . ' ' . $k_obj['nachname'] . '<br>';
        $adr .= $k_obj['strasse'] . '<br>' . $k_obj['plz'] . ' ' . $k_obj['ort'] . '<br>' . get_land_of_customer_cms($k_obj['kid']);
        if ($k_obj['str_nr'] != '')
            $adr .= '<br>Ust.Nr.' . $k_obj['str_nr'];
        return str_replace("<br><br>", "<br>", $adr);
    }

    /**
     * customer_class::validate_form_for_save()
     * 
     * @param mixed $FORM
     * @return
     */
    function validate_form_for_save($FORM) {
        $FORM['strasse'] = format_name_string($FORM['strasse']);
        $FORM['ort'] = format_name_string($FORM['ort']);
        $FORM['bank'] = format_name_string($FORM['bank']);
        $FORM['nachname'] = ($FORM['nachname']);
        $FORM['vorname'] = format_name_string($FORM['vorname']);
        $FORM['birthday'] = format_date_to_sql_date($FORM['birthday']);
        $FORM['email'] = trim(strtolower($FORM['email']));
        if (!empty($FORM['anrede_sign'])) {
            $FORM['anrede'] = get_customer_salutation($FORM['anrede_sign']);
            $FORM['geschlecht'] = get_customer_sex($FORM['anrede_sign']);
        }

        if ($FORM['nachname'] == '' && $FORM['username'] == '' && $FORM['firma_inhaber'] == '') {
            self::msge('Nachname/Benutzername');
        }


        if ($FORM['agb'] == '')
            $FORM['agb'] = 1;
        if ($FORM['is_firma'] == '')
            $FORM['is_firma'] = 0;
        return $FORM;
    }

    /**
     * customer_class::cmd_add_customer()
     * 
     * @return void
     */
    function cmd_add_customer() {
        $FORM = $_POST['FORM'];
        $FORM['tag'] = date('d');
        $FORM['monat'] = date('m');
        $FORM['jahr'] = date('Y');
        $FORM['datum'] = date('Y-m-d');
        $FORM = $this->validate_form_for_save($FORM);
        $FORM['passwort'] = gen_sid(8);
        if ($FORM['email'] != "") {
            $result = $this->db->query("SELECT email FROM " . TBL_CMS_CUST . " WHERE email='" . $FORM['email'] . "'");
            while ($row = $this->db->fetch_array($result)) {
                $email2 = $row[0];
            }
            if ($email2 != '')
                self::msge('[BR]' . "Email schon vorhanden");
            if (!ereg("^.+@.+\\..+$", $FORM['email'])) {
                self::msge('ung&uuml;ltige Email');
            }
        }
        if (self::has_errors() == false) {
            $FORM['passwort'] = encrypt_password($FORM['passwort']);
            $kid = insert_table(TBL_CMS_CUST, $FORM);
            $user_obj = new member_class();
            $user_obj->setKid($kid);
            $user_obj->setMemGroups(array(1100), array(), true, true);
            $params = array('kid' => $kid, 'FORM' => $FORM);
            $params = exec_evt('OnAddCustomerAdmin', $params);
            self::msg("Registrierung erfolgreich.");
            $this->ej('load_customer', $kid);
        }
        else {
            $this->ej();
        }

    }

    /**
     * customer_class::cmd_a_save()
     * 
     * @return void
     */
    function cmd_a_save() {
        $FORM = $_POST['FORM'];
        $FORM = $this->validate_form_for_save($FORM);
        if (self::has_errors() == false) {
            $this->db->query("UPDATE " . TBL_CMS_CUST . " SET cms_isindex=0,sperren=0,vip=0,mailactive=0 WHERE kid='" . $_POST['kid'] . "' LIMIT 1");
            if ($FORM['passwort'] != "")
                $FORM['passwort'] = encrypt_password($FORM['passwort']);
            else
                unset($FORM['passwort']);
            update_table(TBL_CMS_CUST, 'kid', $_POST['kid'], $FORM);
            exec_evt('OnSaveCustomerBackend', $FORM);
            $this->LOGCLASS->addLog('UPDATE', 'profil update ' . $FORM['nachname'] . ', ' . $FORM['kid']);
        }
        $this->ej();
    }

}
