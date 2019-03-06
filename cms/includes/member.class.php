<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('IN_SIDE') or die('Access denied.');

class member_class extends keimeno_class {
    var $user_obj = NULL;
    var $langid = 1;

    /**
     * member_class::__construct()
     * 
     * @param integer $langid
     * @return
     */
    function __construct($langid = 1) {
        parent::__construct();
        $this->langid = $langid;
    }

    /**
     * member_class::setKid()
     * 
     * @param mixed $kid
     * @return
     */
    function setKid($kid) {
        $this->kid = intval($kid);
    }

    /**
     * member_class::genCustomerLink()
     * 
     * @param mixed $id
     * @param mixed $nachname
     * @param integer $lid
     * @return
     */
    public static function genCustomerLink($label, $lid = 1) {
        #global $HTA_CLASS_CMS;
        #return SSL_PATH_SYSTEM . PATH_CMS . $HTA_CLASS_CMS->genLink(34, array($nachname, $id));
        $prefix_lng = ($_SESSION['GBL_LANGID'] == self::get_config_value('std_lang_id')) ? '' : '/' . $_SESSION['GBL_LOCAL_ID'];
        return $prefix_lng . '/' . self::get_config_value('mem_link_detail') . '/' . self::format_file_name($label) . '.html';
    }

    /**
     * member_class::gen_link_label()
     * 
     * @param array $row
     * @return string
     */
    public static function gen_link_label($row) {
        $row['nachname'] = (isset($row['nachname']) ? $row['nachname'] : "");
        $label = (isset($row['vorname']) && $row['vorname'] != "") ? $row['vorname'] . ' ' . $row['nachname'] : $row['nachname'];
        $label = (isset($row['firma']) && $row['firma'] != "") ? $row['firma'] . ' ' . $label : $label;
        return $label;
    }

    /**
     * member_class::update_customer()
     * 
     * @param mixed $FORM
     * @param mixed $kid
     * @return void
     */
    public function update_customer($FORM, $kid) {
        update_table(TBL_CMS_CUST, 'kid', (int)$kid, $FORM);
    }

    /**
     * member_class::genCustomerGroupLink()
     * 
     * @param mixed $id
     * @param mixed $groupname
     * @param integer $lid
     * @return
     */
    function genCustomerGroupLink($id, $groupname, $lid = 1) {
        global $HTA_CLASS_CMS;
        return SSL_PATH_SYSTEM . PATH_CMS . $HTA_CLASS_CMS->genLink(35, array($groupname, $id));
    }

    /**
     * member_class::genCustomerAlphaLink()
     * 
     * @param mixed $alpha
     * @return
     */
    function genCustomerAlphaLink($alpha) {
        global $HTA_CLASS_CMS;
        return SSL_PATH_SYSTEM . PATH_CMS . $HTA_CLASS_CMS->genLink(36, array($alpha));
    }

    /**
     * member_class::delete_customer()
     * 
     * @param mixed $kid
     * @return
     */
    function delete_customer($kid) {
        $kid = (int)$kid;
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $kid);
        if ($FORM['picture'] != "" && delete_file(CMS_ROOT . 'images/members/' . $FORM['picture']))
            $this->db->query("UPDATE " . TBL_CMS_CUST . " SET picture='' WHERE kid='" . $kid . "' LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_CUST . " WHERE kid='" . $kid . "' LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_CUSTCOLGROUPS . " WHERE kid=" . $kid);
        $this->db->query("DELETE FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid=" . $kid);
        exec_evt('OnDeleteCustomer', array('kid' => $kid));
        $this->LOGCLASS->addLog('DELETE', $FORM['kid'] . ' ' . $FORM['nachname'] . ' deleted');
    }


    /**
     * member_class::setOptions()
     * 
     * @param mixed $K_OBJ
     * @param bool $create_picture
     * @return
     */
    function setOptions($K_OBJ = array(), $create_picture = TRUE) {
        global $GRAPHIC_FUNC;
        if (count($K_OBJ) == 0) {
            $K_OBJ = $this->user_obj;
        }
        if ($create_picture === TRUE) {
            $width = $this->gbl_config['mem_thumb_x'];
            $height = $this->gbl_config['mem_thumb_y'];
            $profil_img = (isset($K_OBJ['picture']) && $K_OBJ['picture'] != "" && is_file(CMS_ROOT . 'images/members/' . $K_OBJ['picture'])) ? './images/members/' . $K_OBJ['picture'] :
                './images/opt_member_nopic.jpg';
            $K_OBJ['img'] = thumbit_fe($profil_img, $width, $height);
            $K_OBJ['img_crop'] = thumbit_fe($profil_img, $width, $width, 'crop');
            $K_OBJ['img_detail'] = thumbit_fe($profil_img, $this->gbl_config['mem_detail_width'], $this->gbl_config['mem_detail_height'], 'resize');
            #  echoarr($K_OBJ);die;
        }

        if (isset($K_OBJ['datum']))
            $K_OBJ['datum_ger'] = my_date('d.m.Y', $K_OBJ['datum']);
        if (isset($K_OBJ['birthday']))
            $K_OBJ['birthday'] = my_date('d.m.Y', $K_OBJ['birthday']);
        $label = self::gen_link_label($K_OBJ);
        $K_OBJ['link'] = $this->genCustomerLink($label);
        if ($this->gbl_config['customer_nametype'] == 'FIRSTNAME_LASTNAME') {
            $K_OBJ['username'] = $K_OBJ['vorname'] . ' ' . $K_OBJ['nachname'];
        }
        else
            if ($this->gbl_config['customer_nametype'] == 'FIRSTNAME') {
                $K_OBJ['username'] = $K_OBJ['vorname'];
            }
            else
                if ($this->gbl_config['customer_nametype'] == 'LASTNAME') {
                    $K_OBJ['username'] = $K_OBJ['nachname'];
                }

        return $K_OBJ;
    }

    /**
     * member_class::loadUser()
     * 
     * @param string $kid
     * @return
     */
    function loadUser($kid = '') {
        if (intval($kid) > 0)
            $this->setKid($kid);
        else
            $this->setKid(0);

        $this->user_obj = $this->db->query_first("SELECT L.zone,L.country_code_2,L.land AS COUNTRY, K.* FROM
	" . TBL_CMS_LAND . " L ,
	" . TBL_CMS_CUST . " K
	WHERE K.land=L.id AND K.kid=" . (int)$this->kid . "
	LIMIT 1");
        $perm_groups_sql = "";
        #$this->user_obj['sql_groups']='P.perm_group_id=1000'; // öffentliche Gruppe
        if (!isset($this->user_obj['sql_groups']))
            $this->user_obj['sql_groups'] = "";
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTTOGROUP . " G WHERE G.kid=" . $this->kid);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->user_obj['groups'][] = $row['gid'];
            $this->user_obj['sql_groups'] .= (($this->user_obj['sql_groups'] != "") ? ' OR ' : '') . 'P.perm_group_id=' . $row['gid'];
            $perm_groups_sql .= (($perm_groups_sql != "") ? ' OR ' : '') . 'group_id=' . $row['gid'];
        }
        // öffentliche Gruppe manuel hinzufügen
        $this->user_obj['groups'][] = 1000;
        $this->user_obj['sql_groups'] .= ((isset($this->user_obj['sql_groups']) && $this->user_obj['sql_groups'] != "") ? ' OR ' : '') . 'P.perm_group_id=' . 1000;
        $perm_groups_sql .= (($perm_groups_sql != "") ? ' OR ' : '') . 'group_id=' . 1000;

        // Permissions laden
        $this->user_obj['PERM'] = array();
        if ($perm_groups_sql != "") {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTPERM . " WHERE " . $perm_groups_sql);
            while ($row = $this->db->fetch_array_names($result)) {
                if ($row['module'] == '') {
                    $this->user_obj['ALLPERM'][$row['page_id']]['edit'] = $this->user_obj['ALLPERM'][$row['page_id']]['add'] = $this->user_obj['ALLPERM'][$row['page_id']]['del'] = FALSE;
                    if ($row['p_edit'] == 1)
                        $this->user_obj['ALLPERM'][$row['page_id']]['edit'] = TRUE;
                    if ($row['p_add'] == 1)
                        $this->user_obj['ALLPERM'][$row['page_id']]['add'] = TRUE;
                    if ($row['p_del'] == 1)
                        $this->user_obj['ALLPERM'][$row['page_id']]['del'] = TRUE;
                }
                else {
                    $this->user_obj['ALLPERM'][$row['module']]['edit'] = ($row['p_edit'] == 1);
                    $this->user_obj['ALLPERM'][$row['module']]['add'] = ($row['p_add'] == 1);
                    $this->user_obj['ALLPERM'][$row['module']]['del'] = ($row['p_del'] == 1);
                }
            }
        }
        if ($this->user_obj['sql_groups'] != "")
            $this->user_obj['sql_groups'] = ' AND (' . $this->user_obj['sql_groups'] . ')';
        else
            $this->user_obj['sql_groups'] = ' AND P.perm_group_id=0';
        if (!isset($this->user_obj['kid']) || isset($this->user_obj['kid']) && $this->user_obj['kid'] == 0)
            $this->user_obj['kid'] = -1;
        #$this->user_obj['kid'] = (!isset($this->user_obj['kid']) || (isset($this->user_obj['kid']) && $this->user_obj['kid'] == 0)) ? -1 : $this->user_obj['kid'];
        $params = array('user' => $this->user_obj);
        $params = exec_evt('OnCustomerLoadProfil', $params);
        $this->user_obj = $params['user'];
        return $this->user_obj;
    }

    /**
     * member_class::set_permissions()
     * 
     * @param mixed $page_id
     * @return
     */
    function set_permissions($page_id) {
        global $MODULE;
        $this->user_obj['PERM'] = array();
        if (isset($this->user_obj['ALLPERM']))
            $this->user_obj['PERM'] = $this->user_obj['ALLPERM'][intval($page_id)];
        if (count($MODULE) > 0) {
            foreach ($MODULE as $key => $row) {
                $this->user_obj['PERMOD'][$key] = (isset($this->user_obj['ALLPERM'][$key]) ? $this->user_obj['ALLPERM'][$key] : "");
            }
        }
    }

    /**
     * member_class::addMemberToGroup()
     * 
     * @param mixed $gid
     * @return
     */
    function addMemberToGroup($gid) {
        $this->db->query("DELETE FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid=" . $this->kid . " AND gid=" . intval($gid));
        $this->db->query("INSERT INTO " . TBL_CMS_CUSTTOGROUP . " SET gid=" . intval($gid) . ",kid=" . $this->kid . "");
    }

    /**
     * member_class::setMemGroups()
     * 
     * @param mixed $groups
     * @param mixed $memcol
     * @param mixed $save_groups
     * @param mixed $save_col
     * @return
     */
    function setMemGroups($groups = array(), $memcol = array(), $save_groups, $save_col) {
        // Füge KID in alle ausgewählte Gruppen
        if ($save_groups === TRUE) {
            $this->db->query("DELETE FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid=" . $this->kid);
            if (is_array($groups)) {
                #die('A');
                foreach ($groups as $key => $gid) {
                    $this->db->query("INSERT INTO " . TBL_CMS_CUSTTOGROUP . " SET gid='" . $gid . "',kid=" . $this->kid . "");
                }
            }
        }
        // Ermittle Kollekion und aktualisiere Zuordnung
        if ($save_col === TRUE) {
            $this->db->query("DELETE FROM " . TBL_CMS_CUSTCOLGROUPS . " WHERE kid=" . $this->kid);
            if (is_array($memcol)) {
                $def = array();
                foreach ($memcol as $key => $gid_colid) {
                    list($gid, $colid) = explode('_', $gid_colid);
                    $def[$colid] .= (($def[$colid] != "") ? ';' : '') . $gid;
                }
                foreach ($def as $colid => $sql) {
                    $this->db->query("INSERT INTO " . TBL_CMS_CUSTCOLGROUPS . " SET groups='" . $sql . "',kid=" . $this->kid . ",col_id=" . $colid);
                }
            }
        }


        /*$result = $this->db->query("SELECT * FROM ".TBL_CMS_COLLECTION." ");
        while($row = $this->db->fetch_array_names($result)) {
        $col_groups = explode_string_by_ident($row['col_groups']);
        $sql="";
        foreach ($memcol as $key => $gid_colid) {
        list($gid,$colid) = explode('_',$gid_colid);
        if (in_array($gid,$col_groups)) {
        $sql.=(($sql!="") ? ';' : '') . $gid;
        }
        }
        $this->db->query("INSERT INTO ".TBL_CMS_CUSTCOLGROUPS." SET groups='".$sql."',kid=".$this->kid.",col_id=" . $row['id']);
        }*/

    }

    /**
     * member_class::buildDefaultSelect()
     * 
     * @param mixed $anrede_arr
     * @param mixed $knownof
     * @param mixed $FORM
     * @return
     */
    function buildDefaultSelect($anrede_arr, $knownof, $FORM) {
        global $smarty, $GBL_LANGID;
        if ($FORM['land'] == "")
            $FORM['land'] = 1; // DEUTSCHLAND
        $knownof = explode(";", $this->gbl_config['opt_knownof']);
        foreach ($knownof as $kvalue) {
            $kopt .= '<option value="' . $kvalue . '">' . $kvalue . '</option>';
        }
        unset($anrede_select);
        foreach ($anrede_arr as $key => $value)
            $anrede_arr[$key] = pure_translation($value, $GBL_LANGID);
        asort($anrede_arr);
        foreach ($anrede_arr as $key => $value)
            $anrede_select .= '<option ' . ((pure_translation($FORM['anrede'], $GBL_LANGID) == $value) ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
        $smarty->assign('anredeselect', $anrede_select);
        $smarty->assign('knownof', $kopt);
        $smarty->assign('countrys', build_land_selectbox($FORM['land']));
    }

    /**
     * member_class::tryto_get_sentto_email()
     * 
     * @return
     */
    function tryto_get_sentto_email() {
        $field = $this->get_login_field();
        if (validate_email_input($this->user_obj[$field])) {
            return $this->user_obj[$field];
        }
        else
            if (validate_email_input($this->user_obj['email'])) {
                return $this->user_obj['email'];
            }
            else
                if (validate_email_input($this->user_obj['email_notpublic'])) {
                    return $this->user_obj['email_notpublic'];
                }
    }

    /**
     * member_class::get_login_field()
     * 
     * @return
     */
    function get_login_field() {
        if ($this->gbl_config['login_mode'] == 'PUBLIC_EMAIL') {
            $field = 'email';
        }
        else
            if ($this->gbl_config['login_mode'] == 'NONE_PUBLIC_EMAIL') {
                $field = 'email_notpublic';
            }
            else
                if ($this->gbl_config['login_mode'] == 'USERNAME') {
                    $field = 'username';
                }
                else
                    if ($this->gbl_config['login_mode'] == 'KNR') {
                        $field = 'kid';
                    }
        return $field;
    }


    /**
     * member_class::try_to_get_kid_by_cookie()
     * 
     * @return
     */
    function try_to_get_kid_by_cookie() {
        $kid = $decode = "";
        if (isset($_COOKIE[COOKIENAME]))
            $decode = $_COOKIE[COOKIENAME];
        if (strlen($decode) > 0)
            list($kid, $md5_kid, $passwort) = explode('-', $decode);
        if ($kid > 0) {
            $K = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$kid);
            if ($K['passwort'] == $passwort)
                return $kid;
        }
        return 0;
    }

    /**
     * member_class::init_user()
     * 
     * @return
     */
    function init_user() {
        if ($_SESSION['kid'] == 0) {
            $_SESSION['kid'] = $this->try_to_get_kid_by_cookie();
        }
        $_SESSION['kid'] = (int)$_SESSION['kid'];
        $_SESSION['user_object'] = $user_object = array();
        $this->loadUser($_SESSION['kid']);
        if (isset($_GET['page']))
            $this->set_permissions((int)$_GET['page']);
        if ($_SESSION['kid'] > 0) {
            define("CU_LOGGEDIN", TRUE);
            $_SESSION['user_object'] = $user_object = $this->user_obj;
        }
        else {
            define("CU_LOGGEDIN", FALSE);
            $_SESSION['user_object'] = $user_object = $this->user_obj;
            $user_object['sql_groups'] = ' AND P.perm_group_id=1000';
        }
        $user_object = $this->setOptions($user_object, true);
        return $user_object;
    }

    /**
     * member_class::logout()
     * 
     * @return
     */
    function logout() {
        $_SESSION = array();
        unset($_COOKIE[COOKIENAME]);
        setcookie(COOKIENAME, NULL, -1);
        session_write_close();
        @session_destroy();
        session_regenerate_id(true);
        session_start();
    }

    /**
     * member_class::login()
     * 
     * @param mixed $k_obj
     * @return
     */
    function login($k_obj) {
        $this->LOGCLASS->addLog('INFO', 'Customer logged in: "<a href="kreg.php?aktion=show_edit&kid=' . $k_obj['kid'] . '">' . $k_obj['kid'] . ', ' . $k_obj['nachname'] .
            '</a>"');
        $FROM = array();
        $FROM['sessionid'] = session_id();
        $FROM['ip'] = REAL_IP;
        $FROM['lastvisit'] = date('Y-m-d H:i:s');
        update_table(TBL_CMS_CUST, 'kid', $k_obj['kid'], $FROM);
    }

    /**
     * member_class::set_login_cookie()
     * 
     * @param mixed $K
     * @return
     */
    function set_login_cookie($K = array()) {
        $K = ($K['kid'] == 0) ? $this->user_obj : $K;
        $cookie_value = $K['kid'] . '-' . md5($K['kid']) . '-' . $K['passwort'];
        setcookie(COOKIENAME, $cookie_value, time() + 3600);
        /* verfällt in 1 Stunde */
    }
    /**
     * member_class::format_name_string()
     * 
     * @param mixed $n
     * @return
     */
    function format_name_string($n) {
        $n = strtolower($n);
        $n = trim($n);
        $splitarray = split(" ", $n);
        while (list($arg, $val) = each($splitarray)) {
            $temp = "";
            $splitarray2 = split("-", $val);
            while (list($arg2, $val2) = each($splitarray2)) {
                if ($temp != "")
                    $temp .= "-";
                $temp .= ucfirst($val2);
            }
            if ($result != "")
                $result .= " ";
            $result .= ucfirst($temp);
        }
        return $result;
    }


    /**
     * member_class::validate_save_kreg()
     * 
     * @param mixed $FORM
     * @param mixed $FORM_NOTEMPTY
     * @return
     */
    function validate_save_kreg($FORM, $FORM_NOTEMPTY) {
        if (count($FORM) > 0) {
            $str_arr = array(
                'strasse',
                'ort',
                'bank',
                'nachname',
                'vorname');
            foreach ($str_arr as $key) {
                if ($FORM[$key] != "")
                    $FORM[$key] = $this->format_name_string($FORM[$key]);
                if ($FORM_NOTEMPTY[$key] != "")
                    $FORM_NOTEMPTY[$key] = $this->format_name_string($FORM_NOTEMPTY[$key]);
            }
            if (count($FORM_NOTEMPTY) > 0) {
                foreach ($FORM_NOTEMPTY as $key => $value) {
                    if ($value == '') {
                        $this->add_smarty_errors($err_arr, $key, '{LBL_MISSING}');
                    }
                    $FORM[$key] = $value;
                }
            }
            if ($this->gbl_config['newsletter_disable_unreg'] == 0) {
                $FORM['mailactive'] = (int)$FORM['mailactive'];
            }
            else
                unset($FORM['mailactive']);
            $FORM['email'] = strtolower($FORM['email']);
            $FORM['birthday'] = format_date_to_sql_date($FORM['birthday']);
            if ($this->TCR->POST['passwort_free'] != 1) {
                if ($this->TCR->POST['cmd'] == "insert" && ($FORM['passwort'] == '' || strlen($FORM['passwort']) < 4)) {
                    $this->add_smarty_errors($err_arr, 'passwort', '{ERR_PASSWORT}');
                }
            }

            foreach ($FORM as $key => $wert) {
                if (validate_subject($wert) == false) {
                    $this->add_smarty_errors($err_arr, $key, '{ERR_INPUT}');
                    break;
                }
            }
            if (!empty($FORM['geschlecht'])) {
                $FORM['anrede'] = get_customer_salutation($FORM['geschlecht']);
                $FORM['geschlecht'] = get_customer_sex($FORM['geschlecht']);
            }
            $FORM['land'] = ($FORM['land'] == 0) ? 1 : $FORM['land'];


            # Vorname und Nachname
            if (strtolower($FORM['vorname']) == strtolower($FORM['nachname'])) {
                $this->add_smarty_errors($err_arr, 'nachname', '{ERR_NAMEEQUAL}');
            }
            # Token
            if (empty($this->TCR->POST['token']) || $this->TCR->POST['token'] != $_SESSION['token']) {
                $this->add_smarty_errors($err_arr, 'token', 'invalid token ' . $this->TCR->POST['token'] . ' != ' . $_SESSION['token']);
                $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            }

            $FORM = keimeno_class::trim_array($FORM);
        }
        return array(
            $err_arr,
            $FORM,
            $FORM_NOTEMPTY);
    }

    /**
     * member_class::update_profil()
     * 
     * @param mixed $FORM
     * @param mixed $user_object
     * @return
     */
    function update_profil($FORM, $user_object) {
        if ($FORM['passwort'] != "")
            $FORM['passwort'] = md5($FORM['passwort']);
        else
            unset($FORM['passwort']);
        if ($_FILES['datei']['name'] != "") {
            $ftarget = CMS_ROOT . 'images/members/member_' . $user_object['kid'] . '.jpg';
            move_uploaded_file($_FILES['datei']['tmp_name'], $ftarget);
            chmod($ftarget, 0755);
            $FORM['picture'] = basename($ftarget);
            list($width, $height, $type, $atrr) = getimagesize($ftarget);
            $this->LOGCLASS->addLog('UPLOAD', 'foto profil update ' . basename($ftarget) . ', ' . $user_object['nachname'] . ', ' . $user_object['kid'] . ',' . $_FILES['datei']['name'] .
                $width . 'x' . $height . ',' . $type);
        }
        update_table(TBL_CMS_CUST, 'kid', $user_object['kid'], $FORM);
        if (is_array($this->TCR->POST['GROUPS']))
            $user_obj->setMemGroups($this->TCR->POST['GROUPS'], $this->TCR->POST['MEMBERGROUPSCOL'], false, true);
        $_SESSION['msg'] = base64_encode("{MSG_PROFILGESPEICHERT}");
        $this->LOGCLASS->addLog('UPDATE', 'profil update ' . $user_object['nachname'] . ', ' . $user_object['kid']);
    }


}
