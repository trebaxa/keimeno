<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('IN_SIDE') or die('Access denied.');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * fill_temp()
 * Replace single template var with content
 * @param mixed $key
 * @param mixed $code
 * @param mixed $temp
 * @return string
 */
function fill_temp($key, $code, $temp) {
    $key = '{' . str_replace(array("{", "}"), "", $key) . '}';
    return str_replace($key, $code, $temp);
}

/**
 * get_template()
 * Returns template content by id and language id
 * @param mixed $id
 * @param integer $langid
 * @return string
 */
function get_template($id, $langid = 1) {
    global $kdb, $gbl_config;
    $template = $kdb->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . $id . "' AND lang_id='" . $langid . "'");
    if (intval($template['id']) == 0) {
        $template = $kdb->query_first("SELECT content FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . $id . "' AND use_all_lang=1");
    }
    return $template['content'];
}

/**
 * validate_subject()
 * checks if some bad inputs placed on form fields
 * @param mixed $input
 * @return boolean
 */
function validate_subject($input) {
    $result = true;
    $bad_inputs = array(
        "\r",
        "\n",
        "mime-version",
        "content-type",
        "cc:",
        "to:",
        "bcc:",
        "<a href=");
    foreach ($bad_inputs as $bad_input) {
        if (strpos(strtolower($input), strtolower($bad_input)) !== false) {
            $result = false;
            break;
        }
    }
    return $result;
}

/**
 * validate_email_input()
 * check if input is valid email address
 * @param mixed $email
 * @return boolean
 */
function validate_email_input($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


/**
 * validate_form_empty_smarty()
 * validate form if there empty fields
 * @param mixed $FORM_NOTEMPTY
 * @return array
 */
function validate_form_empty_smarty($FORM_NOTEMPTY) {
    if (count($FORM_NOTEMPTY) > 0) {
        foreach ($FORM_NOTEMPTY as $key => $value) {
            if ($value == '') {
                $err_arr = keimeno_class::add_smarty_err($err_arr, $key, '{LBL_MISSING}');
            }
        }
    }
    return $err_arr;
}


/**
 * GetExt()
 * returns file extention
 * @param mixed $Filename
 * @return
 */
function GetExt($Filename) {
    $RetVal = explode('.', $Filename);
    return $RetVal[count($RetVal) - 1];
}


/**
 * clean_cache_like()
 * clean keimeno file cache with filename filter
 * @param mixed $word
 * @return
 */
function clean_cache_like($word) {
    if (strpos($word, '.')) {
        $arr = explode('.', $word);
        $word = array_shift($arr);
    }
    $word = str_replace('_', '-', $word);
    $dh = opendir(CMS_ROOT . 'cache');
    while (false !== ($filename = readdir($dh))) {
        if ($filename != '.' && $filename != '..') {
            if (strstr($filename, $word) == TRUE) {
                @unlink(CMS_ROOT . 'cache/' . $filename);
            }
        }
    }
    $dh = opendir(CMS_ROOT . 'admin/cache');
    while (false !== ($filename = readdir($dh))) {
        if ($filename != '.' && $filename != '..') {
            if (strstr($filename, $word) == TRUE) {
                @unlink(CMS_ROOT . 'admin/cache/' . $filename);
            }
        }
    }
}

/**
 * delete_file()
 * deletes file
 * @param mixed $file
 * @param integer $withoutcache
 * @return
 */
function delete_file($file, $withoutcache = 0) {
    if (is_file($file) && $file != "") {
        if ($withoutcache == 0) {
            clean_cache_like(basename($file));
        }
        if (file_exists($file)) {
            @unlink($file);
        }
        clearstatcache();
        if (@file_exists($file)) {
            $filesys = eregi_replace("/", "\\", $file);
            @system("del $filesys");
            clearstatcache();
            if (@file_exists($file)) {
                @chmod($file, 0775);
                @unlink($file);
                @system("del $filesys");
            }
        }
        clearstatcache();
        return !file_exists($file);
    }
    else {
        return true;
    }
}

/**
 * format_name_string()
 * formats a person's name string
 * @param mixed $name
 * @return string
 */
function format_name_string($name) {
    $name = strtolower($name);
    $name = trim($name);
    $splitarray = split(" ", $name);
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
 * date_to_time()
 * Returns time. Input german formated date dd.mm.YYYY
 * @param mixed $date
 * @return number
 */
function date_to_time($date) { //dd.mm.YYYY
    $publictime = str_replace(".", "", $date);
    $year = substr($publictime, 4, 4);
    $month = substr($publictime, 2, 2);
    $day = substr($publictime, 0, 2);
    return mktime(0, 0, 0, $month, $day, $year);
}


/**
 * gen_sid()
 * Generate a random string with a minimun of length
 * @param integer $length
 * @return
 */
function gen_sid($length = 8) {
    $key = "";
    $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
    for ($i = 0; $i < $length; $i++) {
        srand((double)microtime() * 1000000);
        $key .= $pattern{rand(0, 35)};
    }
    return $key;
}

/**
 * gen_random_number()
 * Generates a string out of numbers with a minimum length
 * @param integer $length
 * @return string
 */
function gen_random_number($length = 8) {
    $randstr = '';
    srand((double)microtime() * 1000000);
    $chars = array(
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0');
    for ($rand = 0; $rand < $length; $rand++) {
        $random = rand(0, count($chars) - 1);
        $randstr .= $chars[$random];
    }
    return $randstr;
}

/**
 * validate_num_for_sql()
 * returns a mysql formated number
 * @param mixed $number
 * @return
 */
function validate_num_for_sql($number) {
    return keimeno_class::sql_num($number);
}


/**
 * smarty_compile()
 * Compiles html code with smarty engine
 * @param mixed $html
 * @param bool $dorand
 * @return string
 */
function smarty_compile($html, $dorand = TRUE) {
    global $smarty, $sidegenstart, $kdb;
    list($usec, $sec) = explode(" ", microtime());
    $mircotime_now = ((float)$usec + (float)$sec);
    $sidegentime = number_format($mircotime_now - $sidegenstart, 4, ".", ".");
    $smarty->assign('sidegentime', $sidegentime);
    $smarty->assign('db_sqlcount', $kdb->query_counter);
    $smarty->assign('db_sqlhist', $kdb->query_hist);
    return $smarty->fetch('string:' . $html);
}


/**
 * translate_language()
 * Translate html code frontend including smarty compilation
 * @param mixed $html
 * @param mixed $langid
 * @return
 */
function translate_language($html, $langid = 1) {
    $html = (!defined('ISADMIN')) ? main_class::compile_frontend($html, $langid) : smarty_compile($html);
    return pure_translation($html, $langid);
}


/**
 * pure_translation()
 * Translate frontend html code
 * @param mixed $html
 * @param mixed $langid
 * @return
 */
function pure_translation($html, $langid) {
    global $kdb, $gbl_config;
    if ($gbl_config['langtrans'] == 0)
        return $html;
    $lang_content = $kdb->query_first("SELECT LC.langarray as LCA,L.langarray as LNA FROM " . TBL_CMS_LANG . " L, " . TBL_CMS_LANG_CUST . " LC WHERE LC.id='$langid' AND L.id='$langid'");
    $langarr1 = unserialize($lang_content['LCA']);
    $langarr2 = unserialize($lang_content['LNA']);

    $langarr1 = (array )$langarr1;
    $langarr2 = (array )$langarr2;

    if (is_array($langarr2) && is_array($langarr1)) {
        $langarr1 = array_merge($langarr2, $langarr1);
    }

    $trans = array();
    $langarr1 = (array )$langarr1;
    foreach ($langarr1 as $key => $value) {
        $trans['{' . $key . '}'] = stripslashes($value);
    }
    unset($langarr1);
    unset($langarr2);

    if (is_array($trans)) {
        $html = strtr($html, $trans);
    }
    return $html;
}

/**
 * format_to_currency()
 * Formats number into currency format
 * @param mixed $eur
 * @param integer $admin
 * @return string
 */
function format_to_currency($eur, $admin = 0) {
    $eur = round(($eur * 1), 2);
    if ($eur == "") {
        $eur = "0.00";
    }
    $eur = sprintf("%01.2f", $eur);
    $eur = str_replace(".", ",", $eur);
    if ($admin == 1)
        return "$eur EUR";
    else
        return "$eur {SYM_EUR}";
}


/**
 * send_admin_mail()
 * Send mail template to admins
 * @param mixed $template_id
 * @param mixed $smarty_arr
 * @param mixed $att_files
 * @param string $absender
 * @return
 */
function send_admin_mail($template_id, $smarty_arr = array(), $att_files = array(), $absender = "") {
    global $kdb, $gbl_config, $GBL_LANGID, $smarty, $MODULE;
    $template_id = (int)$template_id;
    $smarty->force_compile = true; // wichtig fuer massen emails / abo etc
    if (is_array($smarty_arr) && count($smarty_arr) > 0) {
        foreach ($smarty_arr as $key => $value)
            $smarty->assign($key, $value);
    }

    $email_arr = get_email_template($template_id);
    $b = smarty_compile($email_arr['subject']);
    $t = smarty_compile($email_arr['content']);
    $b = utf8_decode(pure_translation($b, $GBL_LANGID));
    $t = utf8_decode(pure_translation($t, $GBL_LANGID));

    $absender = ($absender == "") ? $gbl_config['adr_service_email'] : $absender;
    $result = $kdb->query("SELECT A.* FROM " . TBL_CMS_ADMINS . " A, " . TBL_CMS_MAIL_RECIP_MATRIX . " M
            WHERE  M.rm_emid=" . $template_id . " AND M.rm_mid=A.id AND 
            A.email<>'' AND A.approval=1 AND A.id<>100");
    while ($row = $kdb->fetch_array_names($result)) {
        $msg = new Email($row['email'], $absender, "[INTERN] " . $b, null, $from_name);
        $msg->Cc = "";
        $msg->Bcc = "";
        $msg->TextOnly = true;
        $msg->Content = $t;
        if ($att_files != "" && count($att_files) > 0) {
            foreach ($att_files as $key => $afile)
                $msg->Attach($afile);
        }
        $SendSuccess = $msg->Send();
    }
}

/**
 * replacer()
 * Replaces some default vars in mail template
 * @param mixed $input_array
 * @param mixed $kid
 * @param mixed $smarty_arr
 * @return array
 */
function replacer($input_array, $kid, $smarty_arr = array()) {
    global $kdb, $mitarbeiter_email, $gbl_config, $GBL_LANGID, $MODULE, $smarty;
    $GBL_LANGID = (intval($GBL_LANGID) == 0) ? $gbl_config['std_lang_id'] : $GBL_LANGID;
    if ($_SESSION['ADMIN_AREA_ACTIVE'] == TRUE) {
        $GBL_LANGID = $gbl_config['std_lang_id'];
    }
    $kid = intval($kid);
    $userx_obj = new member_class($GBL_LANGID);
    $userx_obj->loadUser($kid);
    if (is_array($smarty_arr) && count($smarty_arr) > 0) {
        foreach ($smarty_arr as $key => $value)
            $smarty->assign($key, $value);
    }
    $smarty->force_compile = true; // wichtig fuer massen emails / abo etc
    $CUST_OBJ_SQL = dao_class::get_data_first(TBL_CMS_CUST, array('kid' => $kid));
    $smarty->assign('user', $CUST_OBJ_SQL);
    $CUST_OBJ_SQL['email'] = $userx_obj->tryto_get_sentto_email();
    $anrede = ($CUST_OBJ_SQL['geschlecht'] == "m") ? "{LBL_RECEPTION_MALE} " . $CUST_OBJ_SQL['anrede'] . " " . $CUST_OBJ_SQL['vorname'] . " " . $CUST_OBJ_SQL['nachname'] :
        "{LBL_RECEPTION_FEMALE} " . $CUST_OBJ_SQL['anrede'] . " " . $CUST_OBJ_SQL['vorname'] . " " . $CUST_OBJ_SQL['nachname'];
    $passwort = $CUST_OBJ_SQL['passwort'];
    foreach ($input_array as $key => $wert) {
        $key_arr = array();
        foreach ($gbl_config as $tempkey => $tempvalue) {
            if (strstr($tempkey, 'adr_'))
                $key_arr['!!TMPLDB_FM_' . strtoupper($tempkey) . '!!'] = $tempvalue;
        }
        $konto = $gbl_config['adr_firma'] . "\nKonto: " . $gbl_config['adr_konto'] . " \nBLZ: " . $gbl_config['adr_blz'] . "\nBank: " . $gbl_config['adr_bank'] . "\nIBAN: " .
            $gbl_config['adr_iban'] . "\nSWIFT/BIC-Code: " . $gbl_config['adr_swift'];
        $input_array[$key] = fill_array($key_arr, $input_array[$key]);
        $input_array[$key] = str_replace("!!ANREDE!!", $anrede, $input_array[$key]);
        $input_array[$key] = str_replace("!!BANK_ACCOUNT!!", $konto, $input_array[$key]);
        $input_array[$key] = str_replace("!!FIRMA_NAME!!", $gbl_config['adr_firma'], $input_array[$key]);
        $input_array[$key] = str_replace("!!PASSWORT!!", $CUST_OBJ_SQL['passwort'], $input_array[$key]);
        $input_array[$key] = str_replace("!!CMS_LINK!!", $gbl_config['opt_site_domain'], $input_array[$key]);
        $input_array[$key] = str_replace("!!DATE!!", date("d.m.Y"), $input_array[$key]);
        $input_array[$key] = str_replace("!!EMAIL!!", $CUST_OBJ_SQL['email'], $input_array[$key]);
        $input_array[$key] = str_replace("!!LOGINNAME!!", $CUST_OBJ_SQL[$userx_obj->get_login_field()], $input_array[$key]);
        $input_array[$key] = str_replace("!!LINK_TO_HOMEPAGE!!", "http://www." . FM_DOMAIN . PATH_CMS, $input_array[$key]);
        $input_array[$key] = str_replace("!!RESV_LINK!!", "http://www." . FM_DOMAIN . PATH_CMS . 'index.php?page=540&sec=' . $kid . '&akt=' . $_SESSION['N_OBJ']['DATEID'] .
            '&hash=' . sha1($kid . $_SESSION['N_OBJ']['DATEID']), $input_array[$key]);
        $input_array[$key] = str_replace("!!ACTIVATE_LINK!!", SSLSERVER . 'index.php?cmd=actpro&page=' . $gbl_config['mem_defaultpage'] . '&sec=' . $kid . '&hash=' .
            sha1($kid . $CUST_OBJ_SQL['passwort']), $input_array[$key]);
        $input_array[$key] = str_replace("!!REACTIVATE_LINK!!", SSLSERVER . 'index.php?cmd=actpro&page=' . $gbl_config['mem_defaultpage'] . '&sec=' . $kid . '&hash=' .
            sha1($kid . $CUST_OBJ_SQL['passwort']), $input_array[$key]);


        $input_array[$key] = pure_translation($input_array[$key], $GBL_LANGID);
    }

    if (strstr($input_array['content'], '<%') && strstr($input_array['content'], '%>')) {
        $input_array['content'] = smarty_compile($input_array['content'], true);
    }

    if (strstr($input_array['subject'], '<%') && strstr($input_array['subject'], '%>')) {
        $input_array['subject'] = smarty_compile($input_array['subject'], true);
    }

    $input_array['cu_name'] = $CUST_OBJ_SQL['nachname'];
    if ($CUST_OBJ_SQL['firma'] != "")
        $input_array['cu_name'] = $CUST_OBJ_SQL['firma'];
    $input_array['cu_email'] = $CUST_OBJ_SQL['email'];
    $input_array['order_obj'] = '';
    return $input_array;
}

/**
 * get_email_template()
 * Returns array of email template
 * @param mixed $id
 * @param integer $langid
 * @return array
 */
function get_email_template($id, $langid = -1) {
    global $kdb, $GBL_LANGID, $gbl_config;
    $GBL_LANGID = (intval($GBL_LANGID) == 0) ? $gbl_config['std_lang_id'] : $GBL_LANGID;
    $langid = (intval($langid) == -1) ? $GBL_LANGID : $langid;
    $langid = ($langid == 0) ? $gbl_config['std_lang_id'] : $langid;
    $temp_mail = $kdb->query_first("SELECT * FROM " . TBL_CMS_MAILTEMP . " ET 
			LEFT JOIN " . TBL_CMS_MAILTEMP_CONTENT . " EC ON (EC.email_id=ET.id AND EC.lang_id=" . $langid . ") 
			WHERE ET.id=$id 
			GROUP BY ET.id LIMIT 1");
    if ($temp_mail['email_id'] == 0) {
        $temp_mail = $kdb->query_first("SELECT * FROM " . TBL_CMS_MAILTEMP . " ET 
			LEFT JOIN " . TBL_CMS_MAILTEMP_CONTENT . " EC ON (EC.email_id=ET.id AND EC.lang_id=" . $gbl_config['std_lang_id'] . ") 
			WHERE ET.id=$id 
			GROUP BY ET.id LIMIT 1");
    }
    if (intval($temp_mail['approval']) == 0 || empty($temp_mail['content']))
        return array();

    return array(
        'content' => $temp_mail['content'] . "\n" . (($temp_mail['add_adress'] == 1) ? $gbl_config['email_absender'] : ''),
        'subject' => $temp_mail['email_subject'],
        'admin_copy' => $temp_mail['admin_copy'],
        'add_adress' => $temp_mail['add_adress'],
        'absender_email' => $temp_mail['t_email'],
        'mit_in_copy' => $temp_mail['mit_in_copy'],
        'email_id' => $id);
}


/**
 * send_easy_mail_to()
 * An easys way to send an email
 * @param mixed $email_to
 * @param mixed $email_content
 * @param mixed $email_subject
 * @param string $att_files
 * @param bool $textonly
 * @param mixed $from_email
 * @param string $from_name
 * @return
 */
function send_easy_mail_to($email_to, $email_content, $email_subject, $att_files = array(), $textonly = TRUE, $from_email = FM_EMAIL, $from_name = '', $reply_to =
    "") {
    $email_array['absender_email'] = $from_email;
    $email_array['cu_email'] = $email_to;
    $email_array['subject'] = $email_subject;
    $email_array['content'] = $email_content;
    send_mail_to($email_array, $att_files, $textonly, $from_email, $from_name, $reply_to);
}

/**
 * send_mail_to()
 * 
 * @param mixed $email_array
 * @param mixed $att_files
 * @param bool $textonly
 * @param string $from_email
 * @param string $from_name
 * @return
 */
function send_mail_to($email_array, $att_files = array(), $textonly = TRUE, $from_email = '', $from_name = '', $reply_to = "") {
    global $kdb, $TCMASTER, $gbl_config;
    $att_files = (array )$att_files;
    $status = array('status' => 'ok', 'msg' => '');

    if ($email_array['content'] == '' || $email_array['cu_email'] == '' || $email_array['subject'] == '') {
        return 0;
    }

    if ($email_array['absender_email'] == "")
        $email_array['absender_email'] = FM_EMAIL;
    $from_email = $email_array['absender_email'];
    if ($textonly == FALSE)
        $email_array['content'] = format_tpl_charset_to_ascii($email_array['content']); // wenn html dann mach XHTML
    else {
        $email_array['content'] = utf8_decode($email_array['content']);
        $email_array['subject'] = utf8_decode($email_array['subject']);
    }
    unset($msg);
    if ($from_name == "" && $gbl_config['adr_general_firmname'] != "") {
        $from_name = $TCMASTER->only_alphanums($gbl_config['adr_general_firmname']);
    }
    if ($from_name == "" && $gbl_config['adr_firma'] != "" && $gbl_config['adr_general_firmname'] == "") {
        $from_name = $TCMASTER->only_alphanums($gbl_config['adr_firma']);
    }
    $from_name = utf8_decode($from_name);

    #error_reporting(E_ALL);

    //Create a new PHPMailer instance
    try {
        $mail = new PHPMailer(true);
        if ($gbl_config['smtp_use'] == 1) {
            $mail->isSMTP();
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $mail->SMTPDebug = $gbl_config['smtp_debug_level'];
            $mail->Debugoutput = 'html';
            $mail->Host = $gbl_config['smtp_server'];
            //Set the SMTP port number - likely to be 25, 465 or 587
            $mail->Port = (int)$gbl_config['smtp_port'];
            $mail->SMTPAuth = true;
            $mail->Username = $gbl_config['smtp_user'];
            $mail->Password = $gbl_config['smtp_pass'];
            switch ($gbl_config['smtp_encrypt']) {
                case 'SSL':
                    $mail->SMTPSecure = 'ssl';
                    break;
                case 'TLS':
                    $mail->SMTPSecure = 'tls';
                    break;
            }
        }
        else {
            $mail->isSendmail();
        }

        $mail->setFrom($from_email, $from_name);
        if ($reply_to != "") {
            $mail->addReplyTo($reply_to, $reply_to);
        }
        else {
            $mail->addReplyTo($from_email, $from_name);
        }

        //Set who the message is to be sent to
        $mail->addAddress($email_array['cu_email'], $email_array['cu_email']);
        # set CC
        if ($cc_email != "")
            $mail->addCC($cc_email);
        //Set the subject line
        $mail->Subject = $email_array['subject'];

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        #$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));

        //Replace the plain text body with one created manually
        # $mail->AltBody = 'This is a plain-text message body';
        if ($textonly == true) {
            $mail->isHTML(false);
            $mail->Body = $email_array['content'];
        }
        else {
            $mail->isHTML(true);
            $mail->msgHTML($email_array['content']);
            $mail->AltBody = strip_tags($email_array['content']);
        }
        //Attach an image file
        if (is_array($att_files) && count($att_files) > 0) {
            foreach ($att_files as $key => $afile)
                if (is_file($afile)) {
                    $mail->addAttachment($afile);
                }
        }

        //send the message, check for errors
        if (!$mail->send()) {
            #echo "Mailer Error: " . $mail->ErrorInfo;        die('X');
            $status = array('status' => 'failed', 'msg' => $mail->ErrorInfo);
        }


        if ($email_array['mit_in_copy'] == 1) {
            $result = $kdb->query("SELECT A.* FROM " . TBL_CMS_ADMINS . " A, " . TBL_CMS_MAIL_RECIP_MATRIX . " M
            WHERE  M.rm_emid=" . $email_array['email_id'] . " AND M.rm_mid=A.id AND 
            A.email<>'' AND A.approval=1 AND A.id<>100");
            while ($row = $kdb->fetch_array_names($result)) {
                $mail->ClearAddresses();
                $mail->addAddress($row['email'], $row['email']);
                $mail->Subject = "[KOPIE] " . $email_array['subject'];
                $mail->send();
            }
        }
    }
    catch (Exception $e) {
        # echo 'Exception abgefangen: ', $e->getMessage(), "\n";
    }
    keimeno_class::allocate_memory($mail);
    return $status;
}


/**
 * build_options_for_selectbox()
 * Returns <option> string for selectbox
 * @param mixed $table
 * @param mixed $id
 * @param mixed $column
 * @param string $where
 * @param mixed $selid
 * @return
 */
function build_options_for_selectbox($table, $id, $column, $where = '', $selid) {
    global $kdb;
    $result = $kdb->query("SELECT DISTINCT $id,$column FROM $table $where");
    while ($row = $kdb->fetch_array($result)) {
        if ($selid == $row[$id]) {
            $sel = 'selected';
        }
        else {
            $sel = '';
        }
        $ret .= "<option value=\"" . $row[$id] . "\" $sel>" . $row[$column] . "</option>";
    }

    return $ret;
}


/**
 * build_options_for_selectbox_opt()
 * Build selectbox via sql result
 * @param mixed $table
 * @param mixed $id
 * @param mixed $column
 * @param string $where
 * @param mixed $selid
 * @param mixed $freeid
 * @param mixed $freevalue
 * @return string
 */
function build_options_for_selectbox_opt($table, $id, $column, $where = '', $selid, $freeid, $freevalue) {
    global $kdb;
    $result = $kdb->query("SELECT DISTINCT $id,$column FROM $table $where");
    $ret .= "<option value=\"" . $freeid . "\" $sel>" . $freevalue . "</option>";
    while ($row = $kdb->fetch_array($result)) {
        if ($selid == $row[$id]) {
            $sel = 'selected';
        }
        else {
            $sel = '';
        }
        if ($row[$column] != "")
            $ret .= "<option value=\"" . $row[$id] . "\" $sel>" . $row[$column] . "</option>";
    }

    return $ret;
}


/**
 * build_html_selectbox()
 * Return complete bootstrap selectbox html code
 * @param mixed $select_name
 * @param mixed $table
 * @param mixed $id
 * @param mixed $column
 * @param string $where
 * @param mixed $selid
 * @return string
 */
function build_html_selectbox($select_name, $table, $id, $column, $where = '', $selid) {
    return '<select class="form-control" name="' . $select_name . '" >' . build_options_for_selectbox($table, $id, $column, $where, $selid) . '</select>';
}


/**
 * update_table()
 * Simple update function to update a row in tale via sql
 * @param mixed $table
 * @param mixed $id_name
 * @param mixed $id_value
 * @param mixed $FORM
 * @param integer $admin
 * @return
 */
function update_table($table, $id_name, $id_value, $FORM, $admin = 0) {
    global $kdb;
    $sqlquery = "";
    if (is_array($FORM)) {
        $objekt = $kdb->query_first("SELECT * FROM $table WHERE $id_name='$id_value'");
        foreach ($FORM as $key => $wert) {
            if ($objekt[$key] != $wert) {
                if ($sqlquery)
                    $sqlquery .= ', ';
                $sqlquery .= "$key='$wert'";
            }
        }
        $sql = "UPDATE `" . $table . "` SET $sqlquery WHERE $id_name='$id_value'";
        if ($admin == 1)
            echo $sql;
        if ($sqlquery)
            $kdb->query($sql);
    }
}

/**
 * insert_table()
 * Insert function to insert a row into table via sql
 * @param mixed $table
 * @param mixed $FORM
 * @param string $admin
 * @return
 */
function insert_table($table, $FORM, $admin = '0') {
    global $kdb;
    $sqlquery = "";
    if (count($FORM) > 0) {
        foreach ($FORM as $key => $wert) {
            if ($sqlquery != "")
                $sqlquery .= ', ';
            $sqlquery .= "$key='$wert'";
        }
        $sql = "INSERT INTO `" . $table . "` SET $sqlquery";
        if ($admin == 1)
            echo $sql;
        if ($sqlquery) {
            $kdb->query($sql);
            return $kdb->insert_id();
        }
        else
            false;
    }
}

/**
 * replace_db_table()
 * Replace row in a table via sql
 * @param mixed $table
 * @param mixed $FORM
 * @param string $admin
 * @return
 */
function replace_db_table($table, $FORM, $admin = '0') {
    global $kdb;
    if (count($FORM) > 0) {
        foreach ($FORM as $key => $wert) {
            if ($sqlquery)
                $sqlquery .= ', ';
            $sqlquery .= "$key='$wert'";
        }
        $sql = "REPLACE INTO `" . $table . "` SET $sqlquery";
        if ($admin == 1)
            echo $sql;
        if ($sqlquery) {
            $kdb->query($sql);
            return $kdb->insert_id();
        }
        else
            false;
    }
}


/**
 * get_data_count()
 * Returns count of data via sql
 * @param mixed $table
 * @param mixed $column
 * @param mixed $where
 * @return number
 */
function get_data_count($table, $column, $where) {
    global $kdb;
    $result = $kdb->query("SELECT COUNT($column) FROM $table WHERE $where");
    while ($row = $kdb->fetch_array($result)) {
        Return $row[0];
    }
}


/**
 * format_root_to_path()
 * Formats root path
 * @param mixed $path
 * @return
 */
function format_root_to_path($path) {
    $path = str_replace('//', '/', $path);
    $path = str_replace('../', '', $path);
    $path = str_replace('./', '', $path);
    $path = str_replace('/admin/', '/', $path);
    return $path;
}

/**
 * thumbit_fe()
 * Creates thumbnail for frontend
 * @param mixed $src
 * @param mixed $width
 * @param mixed $height
 * @param string $th_type
 * @param string $crop_pos
 * @return string
 */
function thumbit_fe($src, $width, $height, $th_type = 'resize', $crop_pos = "center") {
    if (substr($src, 0, 1) != '.')
        $src = '.' . $src;
    if (file_exists($src)) {
        $cache_file = graphic_class::makeThumb($src, $width, $height, CACHE, true, $th_type, "", "", $crop_pos);
        return SSL_PATH_SYSTEM . PATH_CMS . CACHE . $cache_file;
    }
    else {
        return false;
    }
}

/**
 * gen_thumb_image()
 * 
 * @param mixed $src
 * @param mixed $width
 * @param mixed $height
 * @param string $th_type
 * @param string $crop_pos
 * @return
 */
function gen_thumb_image($src, $width, $height, $th_type = 'resize', $crop_pos = "center") {
    if ($src == "")
        $src = "./images/opt_no_pic.jpg";
    if (substr($src, 0, 1) != '.' && substr($src, 1, 1) != '/')
        $src = './' . $src;
    if (substr($src, 0, 1) != '.')
        $src = '.' . $src;
    if (substr($src, 1, 1) != '/')
        $src = './' . $src;
    $src = str_replace('.//', './', $src);
    if (!file_exists(format_root_to_path(CMS_ROOT . $src)) || is_dir(format_root_to_path(CMS_ROOT . $src)))
        $src = "./pro_bilder/no_pic.jpg";
    return thumbit_fe($src, $width, $height, $th_type, $crop_pos);
}

/**
 * gen_thumb_picture()
 * Creates thumbnail with creation options and returns complete img tag
 * @param mixed $picture
 * @param integer $w
 * @param integer $h
 * @param string $float
 * @param integer $border
 * @param string $link
 * @param string $alt
 * @param string $target
 * @param string $root
 * @param integer $hspace
 * @param integer $vspace
 * @return string
 */
function gen_thumb_picture($picture, $w = 50, $h = 50, $float = 'center', $border = 1, $link = '', $alt = '', $target = '_self', $root = '', $hspace = 10, $vspace =
    10) {
    global $portal_config;
    if ($picture == "")
        return "";
    if ($link != "") {
        if ($alt != "")
            $lalt = 'TITLE="' . $alt . '"';
        $lpart1 = '<a ' . $lalt . ' href="' . $link . '" target="' . $target . '">';
        $lpart2 = '</a>';
    }
    if ($root == "")
        $root = PICS_ROOT;
    $wh = 'w=' . $w . '&h=' . $h;
    $jpg = gen_thumb_image($root . $picture, $w, $h, $border);
    if ($w > 0)
        $wt = ' width="' . $w . '" ';
    if ($h > 0)
        $ht = ' height="' . $h . '" ';
    return $lpart1 . '<img ' . $wt . $ht . ' HSPACE=' . $hspace . ' VSPACE=' . $vspace . ' src="' . $jpg . '" >' . $lpart2;

}

/**
 * format_meta()
 * Returns meta formated string with maximum length
 * @param mixed $description
 * @param integer $length
 * @return string
 */
function format_meta($description, $length = 500) {
    global $gbl_config;
    $length = (int)$gbl_config['metadesc_count'];
    $description = keimeno_class::pure_text($description);
    return trim(substr($description, 0, $length));
}


/**
 * get_numbers_in_string()
 * Returns all numbers in a string
 * @param mixed $string
 * @return string
 */
function get_numbers_in_string($string) {
    global $arr_numbers;
    for ($i = 0; $i < strlen($string); $i++)
        if (is_numeric($string[$i]))
            $result .= $string[$i];
    return $result;
}

/**
 * human_file_size()
 * Return filesize in a readable way
 * @param mixed $size
 * @return string
 */
function human_file_size($size) {
    return keimeno_class::human_filesize($size);
}


/**
 * format_date_to_sql_date()
 * Formats a german dd.mm.YYYY date into sql date 
 * @param mixed $date
 * @return
 */
function format_date_to_sql_date($date) { // INPUT DD.MM.YYYY
    $part = explode(".", $date);
    return $part[2] . '-' . $part[1] . '-' . $part[0];
}

/**
 * upload_file()
 * Uploads a file on server
 * @param mixed $file_field_name
 * @param mixed $table
 * @param mixed $id
 * @param string $prefix
 * @param string $column
 * @param string $id_column
 * @param string $path
 * @return number
 */
function upload_file($file_field_name, $table, $id, $prefix = "ICO", $column = "bild", $id_column = "id", $path = '') {
    global $_FILES, $kdb;
    $done = 0;
    $root = CMS_ROOT;
    if ($path == '')
        $path = PICS_ROOT;
    $path = str_replace(array('./', '../'), '', $path);
    if (substr($path, 0, 1) == '/')
        $path = substr($path, 1, strlen($path));
    if ($id != "" && $_FILES[$file_field_name]['name'] != "") {
        $done = 1;
        $ext = trim(strrchr($_FILES[$file_field_name]['name'], '.'));
        $f_name = $prefix . "_" . date("YmdHis") . $ext;
        delete_file($root . $path . $f_name);
        if (!validate_upload_file($_FILES[$file_field_name])) {
            header('location: ' . $_SERVER['PHP_SELF'] . '?' . (($_REQUEST['eapge'] != "") ? 'epage=' . $_REQUEST['eapge'] . '&' : '') . 'aktion=&msge=' . base64_encode($_SESSION['upload_msge']));
            exit;
        }
        if (move_uploaded_file($_FILES[$file_field_name]['tmp_name'], $root . $path . $f_name)) {
            chmod($root . $path . $f_name, 0755);
            $pic_obj = $kdb->query_first("SELECT " . $column . " FROM " . $table . " WHERE " . $id_column . "='" . $id . "' LIMIT 1");
            delete_file($root . $path . $pic_obj[$column]);
            $kdb->query("UPDATE " . $table . " SET " . $column . "='" . $f_name . "' WHERE " . $id_column . "='" . $id . "' LIMIT 1");
        }
        else {
            $done = 2;
        }
    }
    else {
        $done = 3;
    }
    return $done;
}

/**
 * get_data_by_url()
 * Get meta data of URL.
 * @param mixed $url
 * @return array
 */
function get_data_by_url($url) {
    $result = false;

    $contents = get_content_by_url($url);

    if (isset($contents) && is_string($contents)) {
        $title = null;
        $metaTags = null;

        preg_match('/<title>([^>]*)<\/title>/si', $contents, $match);

        if (isset($match) && is_array($match) && count($match) > 0) {
            $title = strip_tags($match[1]);
        }

        preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);

        if (isset($match) && is_array($match) && count($match) == 3) {
            $originals = $match[0];
            $names = $match[1];
            $values = $match[2];

            if (count($originals) == count($names) && count($names) == count($values)) {
                $metaTags = array();

                for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
                    $metaTags[$names[$i]] = array('html' => htmlentities($originals[$i]), 'value' => $values[$i]);
                }
            }
        }
        $metaTags['publisheremail'] = $metaTags['publisher-email'];
        $result = array('title' => $title, 'metaTags' => $metaTags);
    }
    else
        $title = "SITE NOT FOUND";

    return $result;
}

/**
 * get_content_by_url()
 * Returns content of url
 * @param mixed $url
 * @param mixed $maximumRedirections
 * @param integer $currentRedirection
 * @return string
 */
function get_content_by_url($url, $maximumRedirections = null, $currentRedirection = 0) {
    $result = false;
    $contents = keimeno_class::curl_get_data($url);

    // Check if we need to go somewhere else
    if (isset($contents) && is_string($contents)) {
        preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);

        if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1) {
            if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections) {
                return get_content_by_url($match[1][0], $maximumRedirections, ++$currentRedirection);
            }

            $result = false;
        }
        else {
            $result = $contents;
        }
    }

    return $contents;
}


/**
 * gen_java_confirm()
 * Returns javascript confirm text for a link
 * @param mixed $text
 * @return string
 */
function gen_java_confirm($text) {
    return 'onClick="return confirm(\'' . $text . '\')"';
}

/**
 * gen_submit_btn()
 * Generates submit button for formular
 * @param mixed $value
 * @param string $confirm
 * @param string $title
 * @param string $alt
 * @return string
 */
function gen_submit_btn($value, $confirm = '', $title = '', $alt = '') {
    $rand_name = gen_sid(6);
    if ($confirm)
        $confirm = gen_java_confirm($confirm);
    else
        $confirm = '';
    return '<input ' . $confirm . ' title="' . $title . '" alt="' . $alt . '" type="submit" id="' . $rand_name . '" name="' . $rand_name .
        '" class="btn btn-primary" value="' . htmlspecialchars($value) . '">';
}


/**
 * get_customer_salutation()
 * Gets customer salutation
 * @param mixed $ident
 * @return string
 */
function get_customer_salutation($ident) {
    global $anrede_arr;
    return $anrede_arr[$ident];
}

/**
 * get_customer_sex()
 * Gets customer's sex. Input is ident like "m" or "w"
 * @param mixed $ident
 * @return string
 */
function get_customer_sex($ident) {
    global $anrede_index_arr;
    return $anrede_index_arr[$ident]; // m oder w
}

/**
 * validate_upload_file()
 * Checks uploaded file.
 * @param mixed $_FILE_ARR
 * @param bool $foreceimg
 * @param bool $sizelimit
 * @return boolean
 */
function validate_upload_file($_FILE_ARR, $foreceimg = false, $sizelimit = false) {
    global $gbl_config;
    unset($_SESSION['upload_msge']);


    if (isset($_FILE_ARR['error']) && $_FILE_ARR['error'] > 0) {
        if (isset($_FILE_ARR['error']) && $_FILE_ARR['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }
        switch ($_FILE_ARR['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $_SESSION['upload_msge'] .= 'Die hochgeladene Datei überschreitet die in der Anweisung upload_max_filesize in php.ini festgelegte Größe';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $_SESSION['upload_msge'] .= 'Die hochgeladene Datei überschreitet die in dem HTML Formular mittels der Anweisung MAX_FILE_SIZE angegebene maximale Dateigröße. ';
                break;
            case UPLOAD_ERR_PARTIAL:
                $_SESSION['upload_msge'] .= 'Die Datei wurde nur teilweise hochgeladen. ';
                break;
            case UPLOAD_ERR_NO_FILE:
                $_SESSION['upload_msge'] .= 'Es wurde keine Datei hochgeladen. ';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $_SESSION['upload_msge'] .= 'Fehlender temporärer Ordner. Eingeführt in PHP 5.0.3. ';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $_SESSION['upload_msge'] .= 'Speichern der Datei auf die Festplatte ist fehlgeschlagen. Eingeführt in PHP 5.1.0. ';
                break;
            case UPLOAD_ERR_EXTENSION:
                $_SESSION['upload_msge'] .=
                    'Eine PHP Erweiterung hat den Upload der Datei gestoppt. PHP bietet keine Möglichkeit an, um festzustellen welche Erweiterung das Hochladen der Datei gestoppt hat. Überprüfung aller geladenen Erweiterungen mittels phpinfo() könnte helfen. Eingeführt in PHP 5.2.0. ';
                break;
        }
    }

    if ($_FILE_ARR['name'] == "") {
        return true;
    }

    $sizebytes = $gbl_config['max_pic_size'] * 1000;


    $ext = strtolower(strrchr($_FILE_ARR['name'], '.'));
    $ret = true;
    if (in_array($ext, $_SESSION['forbidden_ext']) || !in_array($ext, $_SESSION['allowed_ext'])) {
        $ret = false;
        $_SESSION['upload_msge'] .= '[' . $_FILE_ARR['type'] . '] | ".' . keimeno_class::get_ext($_FILE_ARR['name']) . '" not allowed.';
    }
    if (($sizelimit == true) && ($_FILE_ARR['size'] > $sizebytes)) {
        $ret = false;
        $_SESSION['upload_msge'] .= 'Filesize to big: ' . human_file_size($_FILE_ARR['size']) . '. Allowed: ' . human_file_size($sizebytes);
    }
    if ($foreceimg == true) {
        $imgext = array(
            'jpg',
            'jpeg',
            'ico',
            'svg',
            'icon',
            'gif',
            'bmp',
            'png');
        if (!in_array(keimeno_class::get_ext($_FILE_ARR['name']), $imgext)) {
            # if ((!strstr($_FILE_ARR['type'], 'image') || strstr($_FILE_ARR['type'], '/bmp') || (!strstr($_FILE_ARR['type'], 'jpeg') && !strstr($_FILE_ARR['type'], 'pjpeg') &&
            #     !strstr($_FILE_ARR['type'], 'gif') && !strstr($_FILE_ARR['type'], 'icon'))) && ($ext != '.ico' && $ext != '.png')) {
            $ret = false;
            $_SESSION['upload_msge'] .= 'File is not an allowed image format: ' . keimeno_class::get_ext($_FILE_ARR['name']) . '. Allowed is:' . implode(', ', $imgext);
        }
    }
    return $ret;
}

/**
 * format_string_to_xls()
 * Formats string into xls compatible string
 * @param mixed $input
 * @return string
 */
function format_string_to_xls($input) {
    $input = str_replace('"' . "\t", '"' . "\\t", $input);
    $input = str_replace("\r\n", "\\r\\n", $input);
    $rep = array("|", ";");
    $input = str_replace($rep, "", $input);
    if (is_numeric($input))
        $input = str_replace('.', ',', $input);
    return $input;
}

/**
 * get_land_of_customer_cms()
 * Returns country of customer
 * @param mixed $mid
 * @return string
 */
function get_land_of_customer_cms($mid) {
    global $kdb;
    $result = $kdb->query_first("SELECT L.land FROM " . TBL_CMS_LAND . " L, " . TBL_CMS_KUNDEN . " M WHERE M.kid='$mid' AND L.id=M.land");
    return $result['land'];
}

/**
 * my_date()
 * Formats sql date into a variable date format
 * @param mixed $format
 * @param mixed $publictime
 * @param string $blank_return
 * @return string
 */
function my_date($format, $publictime, $blank_return = '') {
    if (strlen($publictime) < 10 || $publictime == '0000-00-00')
        return $blank_return;
    $publictime = str_replace("-", "", $publictime);
    $sec = (int)substr($publictime, 12, 2);
    $min = (int)substr($publictime, 10, 2);
    $hour = (int)substr($publictime, 8, 2);
    $day = substr($publictime, 6, 2);
    $month = substr($publictime, 4, 2);
    $year = substr($publictime, 0, 4);
    $datum = date($format, mktime($hour, $min, $sec, $month, $day, $year));
    $gestern = date($format, mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
    return $datum;
}

/**
 * explode_string_by_ident()
 * Nich explode and returns always an array. Also on empty input
 * @param mixed $fields
 * @param string $ident
 * @return
 */
function explode_string_by_ident($fields, $ident = ';') {
    $fields = trim($fields);
    if ($fields != "")
        return explode($ident, $fields);
    else
        return array();
}

/**
 * fill_array()
 * Replace function by array on string
 * @param mixed $key_arr
 * @param mixed $temp
 * @return string
 */
function fill_array($key_arr, $temp) {
    $keys = array();
    $values = array();
    if (count($key_arr) > 0) {
        foreach ($key_arr as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
    }
    return str_replace($keys, $values, $temp);
}

/**
 * get_locale_of_visitor()
 * Get local information by user browser client
 * @param mixed $typ
 * @return
 */
function get_locale_of_visitor($typ) {
    if ($_SERVER["HTTP_ACCEPT_LANGUAGE"][2] == ",") {
        $lang = $_SERVER["HTTP_ACCEPT_LANGUAGE"][0] . $_SERVER["HTTP_ACCEPT_LANGUAGE"][1];
    }
    else {
        $lang = $_SERVER["HTTP_ACCEPT_LANGUAGE"][0] . $_SERVER["HTTP_ACCEPT_LANGUAGE"][1] . $_SERVER["HTTP_ACCEPT_LANGUAGE"][2] . $_SERVER["HTTP_ACCEPT_LANGUAGE"][3] .
            $_SERVER["HTTP_ACCEPT_LANGUAGE"][4];
    }

    If ($lang == "af") {
        $strLCID = 1078; // Afrikaans
    }
    ElseIf ($lang == "sq") {
        $strLCID = 1052; // Albanian
    }
    ElseIf ($lang == "ar-sa") {
        $strLCID = 1025; // Arabic(Saudi Arabia)
    }
    ElseIf ($lang == "ar-iq") {
        $strLCID = 2049; // Arabic(Iraq)
    }
    ElseIf ($lang == "ar-eg") {
        $strLCID = 3073; // Arabic(Egypt)
    }
    ElseIf ($lang == "ar-ly") {
        $strLCID = 4097; // Arabic(Libya)
    }
    ElseIf ($lang == "ar-dz") {
        $strLCID = 5121; // Arabic(Algeria)
    }
    ElseIf ($lang == "ar-ma") {
        $strLCID = 6145; // Arabic(Morocco)
    }
    ElseIf ($lang == "ar-tn") {
        $strLCID = 7169; // Arabic(Tunisia)
    }
    ElseIf ($lang == "ar-om") {
        $strLCID = 8193; // Arabic(Oman)
    }
    ElseIf ($lang == "ar-ye") {
        $strLCID = 9217; // Arabic(Yemen)
    }
    ElseIf ($lang == "ar-sy") {
        $strLCID = 10241; // Arabic(Syria)
    }
    ElseIf ($lang == "ar-jo") {
        $strLCID = 11265; // Arabic(Jordan)
    }
    ElseIf ($lang == "ar-lb") {
        $strLCID = 12289; // Arabic(Lebanon)
    }
    ElseIf ($lang == "ar-kw") {
        $strLCID = 13313; // Arabic(Kuwait)
    }
    ElseIf ($lang == "ar-ae") {
        $strLCID = 14337; // Arabic(U.A.E.)
    }
    ElseIf ($lang == "ar-bh") {
        $strLCID = 15361; // Arabic(Bahrain)
    }
    ElseIf ($lang == "ar-qa") {
        $strLCID = 16385; // Arabic(Qatar)
    }
    ElseIf ($lang == "eu") {
        $strLCID = 1069; // Basque
    }
    ElseIf ($lang == "bg") {
        $strLCID = 1026; // Bulgarian
    }
    ElseIf ($lang == "be") {
        $strLCID = 1059; // Belarusian
    }
    ElseIf ($lang == "ca") {
        $strLCID = 1027; // Catalan
    }
    ElseIf ($lang == "zh-tw") {
        $strLCID = 1028; // Chinese(Taiwan)
    }
    ElseIf ($lang == "zh-cn") {
        $strLCID = 2052; // Chinese(PRC)
    }
    ElseIf ($lang == "zh-hk") {
        $strLCID = 3076; // Chinese(Hong Kong)
    }
    ElseIf ($lang == "zh-sg") {
        $strLCID = 4100; // Chinese(Singapore)
    }
    ElseIf ($lang == "hr") {
        $strLCID = 1050; // Croatian
    }
    ElseIf ($lang == "cs") {
        $strLCID = 1029; // Czech
    }
    ElseIf ($lang == "da") {
        $strLCID = 1030; // Danish
    }
    ElseIf ($lang == "nl") {
        $strLCID = 1043; // Dutch(Standard)
    }
    ElseIf ($lang == "nl-be") {
        $strLCID = 2067; // Dutch(Belgian)
    }
    ElseIf ($lang == "en") {
        $strLCID = 9; // English
    }
    ElseIf ($lang == "en-us") {
        $strLCID = 1033; // English(United States)
    }
    ElseIf ($lang == "en-gb") {
        $strLCID = 2057; // English(British)
    }
    ElseIf ($lang == "en-au") {
        $strLCID = 3081; // English(Australian)
    }
    ElseIf ($lang == "en-ca") {
        $strLCID = 4105; // English(Canadian)
    }
    ElseIf ($lang == "en-nz") {
        $strLCID = 5129; // English(New Zealand)
    }
    ElseIf ($lang == "en-ie") {
        $strLCID = 6153; // English(Ireland)
    }
    ElseIf ($lang == "en-za") {
        $strLCID = 7177; // English(South Africa)
    }
    ElseIf ($lang == "en-jm") {
        $strLCID = 8201; // English(Jamaica)
    }
    ElseIf ($lang == "en-ca") {
        $strLCID = 9225; // English(Caribbean)
    }
    ElseIf ($lang == "en-bz") {
        $strLCID = 10249; // English(Belize)
    }
    ElseIf ($lang == "en-tt") {
        $strLCID = 11273; // English(Trinidad)
    }
    ElseIf ($lang == "et") {
        $strLCID = 1061; // Estonian
    }
    ElseIf ($lang == "fo") {
        $strLCID = 1080; // Faeroese
    }
    ElseIf ($lang == "fa") {
        $strLCID = 1065; // Farsi
    }
    ElseIf ($lang == "fi") {
        $strLCID = 1035; // Finnish
    }
    ElseIf ($lang == "fr") {
        $strLCID = 1036; // French(Standard)
    }
    ElseIf ($lang == "fr-be") {
        $strLCID = 2060; // French(Belgian)
    }
    ElseIf ($lang == "fr-ca") {
        $strLCID = 3084; // French(Canadian)
    }
    ElseIf ($lang == "fr-ch") {
        $strLCID = 4108; // French(Swiss)
    }
    ElseIf ($lang == "fr-lu") {
        $strLCID = 5132; // French(Luxembourg)
    }
    ElseIf ($lang == "mk") {
        $strLCID = 1071; // Macedonian (FYROM)
    }
    ElseIf ($lang == "gd") {
        $strLCID = 1084; // Gaelic(Scots)
    }
    ElseIf ($lang == "de") {
        $strLCID = 1031; // German(Standard)
    }
    ElseIf ($lang == "de-de") {
        $strLCID = 1031; // German(Standard)
    }
    ElseIf ($lang == "de-ch") {
        $strLCID = 2055; // German(Swiss)
    }
    ElseIf ($lang == "de-at") {
        $strLCID = 3079; // German(Austrian)
    }
    ElseIf ($lang == "de-lu") {
        $strLCID = 4103; // German(Luxembourg)
    }
    ElseIf ($lang == "de-li") {
        $strLCID = 5127; // German(Liechtenstein)
    }
    ElseIf ($lang == "el") {
        $strLCID = 1032; // Greek
    }
    ElseIf ($lang == "he") {
        $strLCID = 1037; // Hebrew
    }
    ElseIf ($lang == "hi") {
        $strLCID = 1081; // Hindi
    }
    ElseIf ($lang == "hu") {
        $strLCID = 1038; // Hungarian
    }
    ElseIf ($lang == "is") {
        $strLCID = 1039; // Icelandic
    }
    ElseIf ($lang == "in") {
        $strLCID = 1057; // Indonesian
    }
    ElseIf ($lang == "it") {
        $strLCID = 1040; // Italian(Standard)
    }
    ElseIf ($lang == "it-ch") {
        $strLCID = 2064; // Italian(Swiss)
    }
    ElseIf ($lang == "ja") {
        $strLCID = 1041; // Japanese
    }
    ElseIf ($lang == "ko") {
        $strLCID = 1042; // Korean
    }
    ElseIf ($lang == "ko") {
        $strLCID = 2066; // Korean(Johab)
    }
    ElseIf ($lang == "lv") {
        $strLCID = 1062; // Latvian
    }
    ElseIf ($lang == "lt") {
        $strLCID = 1063; // Lithuanian
    }
    ElseIf ($lang == "ms") {
        $strLCID = 1086; // Malaysian
    }
    ElseIf ($lang == "mt") {
        $strLCID = 1082; // Maltese
    }
    ElseIf ($lang == "no") {
        $strLCID = 1044; // Norwegian(Bokmal)
    }
    ElseIf ($lang == "no") {
        $strLCID = 2068; // Norwegian(Nynorsk)
    }
    ElseIf ($lang == "pl") {
        $strLCID = 1045; // Polish
    }
    ElseIf ($lang == "pt-br") {
        $strLCID = 1046; // Portuguese(Brazil)
    }
    ElseIf ($lang == "pt") {
        $strLCID = 2070; // Portuguese(Portugal)
    }
    ElseIf ($lang == "rm") {
        $strLCID = 1047; // Rhaeto-Romanic
    }
    ElseIf ($lang == "ro") {
        $strLCID = 1048; // Romanian
    }
    ElseIf ($lang == "ro-mo") {
        $strLCID = 2072; // Romanian(Moldavia)
    }
    ElseIf ($lang == "ru") {
        $strLCID = 1049; // Russian
    }
    ElseIf ($lang == "ru-mo") {
        $strLCID = 2073; // Russian(Moldavia)
    }
    ElseIf ($lang == "sz") {
        $strLCID = 1083; // Sami(Lappish)
    }
    ElseIf ($lang == "sr") {
        $strLCID = 3098; // Serbian(Cyrillic)
    }
    ElseIf ($lang == "sr") {
        $strLCID = 2074; // Serbian(Latin)
    }
    ElseIf ($lang == "sk") {
        $strLCID = 1051; // Slovak
    }
    ElseIf ($lang == "sl") {
        $strLCID = 1060; // Slovenian
    }
    ElseIf ($lang == "sb") {
        $strLCID = 1070; // Sorbian
    }
    ElseIf ($lang == "es") {
        $strLCID = 1034; // Spanish(Spain - Traditional Sort)
    }
    ElseIf ($lang == "es-mx") {
        $strLCID = 2058; // Spanish(Mexican)
    }
    ElseIf ($lang == "es-gt") {
        $strLCID = 4106; // Spanish(Guatemala)
    }
    ElseIf ($lang == "es-cr") {
        $strLCID = 5130; // Spanish(Costa Rica)
    }
    ElseIf ($lang == "es-pa") {
        $strLCID = 6154; // Spanish(Panama)
    }
    ElseIf ($lang == "es-do") {
        $strLCID = 7178; // Spanish(Dominican Republic)
    }
    ElseIf ($lang == "es-ve") {
        $strLCID = 8202; // Spanish(Venezuela)
    }
    ElseIf ($lang == "es-co") {
        $strLCID = 9226; // Spanish(Colombia)
    }
    ElseIf ($lang == "es-pe") {
        $strLCID = 10250; // Spanish(Peru)
    }
    ElseIf ($lang == "es-ar") {
        $strLCID = 11274; // Spanish(Argentina)
    }
    ElseIf ($lang == "es-ec") {
        $strLCID = 12298; // Spanish(Ecuador)
    }
    ElseIf ($lang == "es-c") {
        $strLCID = 13322; // Spanish(Chile)
    }
    ElseIf ($lang == "es-uy") {
        $strLCID = 14346; // Spanish(Uruguay)
    }
    ElseIf ($lang == "es-py") {
        $strLCID = 15370; // Spanish(Paraguay)
    }
    ElseIf ($lang == "es-bo") {
        $strLCID = 16394; // Spanish(Bolivia)
    }
    ElseIf ($lang == "es-sv") {
        $strLCID = 17418; // Spanish(El Salvador)
    }
    ElseIf ($lang == "es-hn") {
        $strLCID = 18442; // Spanish(Honduras)
    }
    ElseIf ($lang == "es-ni") {
        $strLCID = 19466; // Spanish(Nicaragua)
    }
    ElseIf ($lang == "es-pr") {
        $strLCID = 20490; // Spanish(Puerto Rico)
    }
    ElseIf ($lang == "sx") {
        $strLCID = 1072; // Sutu
    }
    ElseIf ($lang == "sv") {
        $strLCID = 1053; // Swedish
    }
    ElseIf ($lang == "sv-fi") {
        $strLCID = 2077; // Swedish(Finland)
    }
    ElseIf ($lang == "th") {
        $strLCID = 1054; // Thai
    }
    ElseIf ($lang == "ts") {
        $strLCID = 1073; // Tsonga
    }
    ElseIf ($lang == "tn") {
        $strLCID = 1074; // Tswana
    }
    ElseIf ($lang == "tr") {
        $strLCID = 1055; // Turkish
    }
    ElseIf ($lang == "uk") {
        $strLCID = 1058; // Ukrainian
    }
    ElseIf ($lang == "ur") {
        $strLCID = 1056; // Urdu
    }
    ElseIf ($lang == "ve") {
        $strLCID = 1075; // Venda
    }
    ElseIf ($lang == "vi") {
        $strLCID = 1066; // Vietnamese
    }
    ElseIf ($lang == "xh") {
        $strLCID = 1076; // Xhosa
    }
    ElseIf ($lang == "ji") {
        $strLCID = 1085; // Yiddish
    }
    ElseIf ($lang == "zu") {
        $strLCID = 1077; // Zulu
    }
    Else {
        $strLCID = 2048; // default
    }
    if ($typ == "lcid") {
        $locale = $strLCID;
    }
    elseif ($typ == "lang") {
        $locale = str_replace('-', '_', $lang);
        if (!strstr('_', $locale))
            $locale .= '_' . strtoupper($locale);
    }
    else {
        $locale = 'syntax:</br>&nbsp;&nbsp;&nbsp;&nbsp;get_locale_of_visitor(typ)</br>&nbsp;&nbsp;&nbsp;&nbsp;typ="lcid"/"lang"';
    }
    return ($locale);
}


/**
 * create_html_editor()
 * 
 * @param string $textarea_name
 * @param string $value
 * @param integer $height
 * @param string $tset
 * @param integer $width
 * @param bool $fullPage
 * @param string $id
 * @param mixed $settings
 * @return
 */
function create_html_editor($textarea_name = '', $value = '', $height = 200, $tset = 'Fullpage', $width = 0, $fullPage = false, $id = '', $settings = array()) {
    global $kdb;
    $c = "";
    $id = (($id != "") ? $id : md5($textarea_name));
    #   $id = md5(time() . rand(0, 100000));
    $local = ($_SESSION['GBL_LOCAL_ID'] != "") ? strtolower($_SESSION['GBL_LOCAL_ID']) : 'de';
    # http://www.tinymce.com/wiki.php/FAQ

    $links = 'link_list: [';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE c_type='T' AND gbl_template=0 ORDER BY description");
    while ($row = $kdb->fetch_array_names($result)) {
        $links .= '{title: "' . str_replace('"', '\"', $row['description']) . '", value: "' . content_class::gen_url_template($row['id']) . '"},';
    }
    $links .= '],';

    $opt_str = "";
    $std_opt = array(
        'convert_newlines_to_brs' => 'true',
        'force_br_newlines' => 'true',
        'force_p_newlines' => 'false',
        'paste_data_images' => 'true',
        'convert_fonts_to_spans' => 'true',
        'remove_script_host' => 'true',
        'relative_urls' => 'false',
        'allow_script_urls' => 'true',
        'image_advtab' => 'true',
        'height' => '"' . $height . 'px"',
        'width' => '"100%"');
    $opt = array_merge($std_opt, $settings);

    foreach ($opt as $key => $val) {
        $opt_str .= $key . ':' . $val . ',' . PHP_EOL;
    }


    $general_Settings = 'selector : "#' . $id . '",
    ' . $opt_str . '
        document_base_url : "' . keimeno_class::get_domain_url() . '",
        ' . $links . '      
        extended_valid_elements : "header[id|name|class|style],footer[id|name|class|style],article[id|name|class|style],section[id|name|class|style],hgroup,nav[id|name|class|style],figure[id|name|class|style],aside[id|name|class|style],date[id|name|class|style],style,i[id|name|class|style],em",      
        
        plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager ' . (($fullPage == true) ? ' fullpage' : '') . '"
        ], 
        setup: function (editor) {
            editor.on(\'change\', function () {
                tinymce.triggerSave();
            });
        },
        filemanager_title:"Filemanager",
        external_filemanager_path:"/cjs/responsive_filemanager/filemanager/",
        external_plugins: { "filemanager" :  "/cjs/responsive_filemanager/filemanager/plugin.min.js"}
       
       ';

    if ($tset == 'Full' || $tset == '' || $tset == NULL) {
        $tset = 'Fullpage';
    }
    if ($tset == 'Basic') {
        $c .= '
  
tinymce.init({
    menubar: false,       
    toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | image link",    
   ' . $general_Settings . '
});
';
    }
    if ($tset == 'Basic2') {
        $c .= '
    
tinymce.init({
    menubar: false,  
    toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | image code link | removeformat",
' . $general_Settings . '
});
';
    }
    if ($tset == 'Simple') {
        $c .= '
   
tinymce.init({
    toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify",
' . $general_Settings . '
});
';
    }
    if ($tset == 'Fullpage') {
        $c .= '
    
tinymce.init({
     toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | print preview fullpage | forecolor code fullscreen | removeformat",
' . $general_Settings . '
});
';
    }

    return '<textarea id="' . $id . '" name="' . $textarea_name . '">' . ($value) . '</textarea>
        <script>        
        ' . $c . '
             sleep(100).then(() => {
                tinymce.execCommand("mceRemoveEditor", false,"' . $id . '");
                tinymce.execCommand("mceAddEditor", false, "' . $id . '");              
                /*tinymce.execCommand("mceFocus", false, "' . $id . '");*/                             
              });
             
        </script>
        ';
}

/**
 * echoarr()
 * This function is for debuging. Prints out an array in readable way.
 * @param mixed $arr
 * @return
 */
function echoarr($arr) {
    echo '<div style="margin-top:160px;z-index:999999999"><pre>' . print_r(keimeno_class::arr_hsc($arr), true) . '    </pre></div><br>';
}

/**
 * format_tpl_charset_to_ascii()
 * Formats charset to ascii format
 * @param mixed $tmpl
 * @return string
 */
function format_tpl_charset_to_ascii($tmpl) {
    if (!extension_loaded('tidy'))
        return $tmpl;
    $tidy = new tidy();
    $config = array(
        'output-xhtml' => TRUE,
        'wrap' => 200,
        'alt-text' => '',
        'input-encoding' => 'utf8',
        'preserve-entities' => true,
        'quote-ampersand' => false,
        'numeric-entities' => true,
        'doctype' => 'transitional');
    $tidy->ParseString($tmpl, $config);
    $tidy->CleanRepair();
    return $tidy;
}

/**
 * set_utf8_entities()
 * Set utf8 entities
 * @param mixed $html
 * @return string
 */
function set_utf8_entities($html) {
    // bereitet nicht UTF8 codierten Seiten für die UTF8 Formatierung vor
    #$replace = array('Â»'=>'&raquo;','Â«'=>'&laquo;','Â§'=>'&sect;','Â©'=>'&copy;','Â®'=>'&reg;');
    $replace = array(
        'Â»' => '&raquo;',
        'Â«' => '&laquo;',
        'Â§' => '&sect;',
        'Â©' => '&copy;',
        'Â®' => '&reg;',
        '&#8208;' => '-');
    $html = strtr($html, $replace);

    $suchen = array(
        '&mu;',
        '&agrave;',
        '&sect;',
        '&laquo;',
        '&raquo;',
        '&Auml;',
        '&Ouml;',
        '&Uuml;',
        '&auml;',
        '&uuml;',
        '&ouml;',
        '&szlig;',
        '&copy;',
        '&euro;');
    foreach ($suchen as $value) {
        $rep[$value] = html_entity_decode($value);
    }
    $html = strtr($html, $rep);

    #	                    $suchen=array('«','»','Ä','Ö','Ü','§','ä','ü','ö','ß','©','€','£','®');
    $suchen = array(
        '-',
        '•',
        'µ',
        'à',
        'á',
        'Ä',
        'Ö',
        'Ü',
        '§',
        '«',
        '»',
        'ä',
        'ü',
        'ö',
        'ß',
        '©',
        '€',
        '£',
        '®',
        'é',
        '’',
        'ó',
        'ñ',
        'ú',
        '‘');
    foreach ($suchen as $value) {
        $rep[$value] = utf8_encode($value);
    }
    $html = strtr($html, $rep);

    return $html;
}


/**
 * ECHORESULT()
 * Send html code browser. May usefull for ajax requests. Includes translation
 * @param mixed $html
 * @return
 */
function ECHORESULT($html) {
    global $gbl_config, $TCMASTER;
    header("Content-type: text/html; charset=UTF-8");
    echo tidy_page($html);
    $TCMASTER->hard_exit();
}

/**
 * ECHORESULTPUR()
 * Send html code browser. May usefull for ajax requests. Without translation
 * @param mixed $html
 * @return
 */
function ECHORESULTPUR($html) {
    global $gbl_config, $kdb, $TCMASTER;
    $kdb->disconnect();
    header("Content-type: text/html; charset=UTF-8");
    echo $html;
    $TCMASTER->hard_exit();
}

/**
 * ECHORESULTCOMPILEDFE()
 * Send html code browser. May usefull for ajax requests. Includes translation and smarty compilation
 * @param mixed $html
 * @return
 */
function ECHORESULTCOMPILEDFE($html) {
    global $smarty, $GBL_LANGID, $TCMASTER, $gbl_config, $user_object;
    $CORE = new main_class();
    $CORE->GBL_LANGID = (int)$GBL_LANGID;
    $CORE->set_user_obj($user_object);
    $CORE->set_smarty_defaults();
    ECHORESULTPUR(pure_translation(smarty_compile($html), $GBL_LANGID));
    $TCMASTER->hard_exit();
}

/**
 * echo_template_fe()
 * Send smarty compiled template to browser. Includes translation.
 * @param mixed $tpl
 * @return
 */
function echo_template_fe($tpl) {
    ECHORESULTCOMPILEDFE('<%include file="' . $tpl . '.tpl"%>');
}

/**
 * ECHOCOMPILEDTPL()
 * Send smarty compiled template by id to browser. Includes translation.
 * @param mixed $tid
 * @return
 */
function ECHOCOMPILEDTPL($tid) {
    global $GBL_LANGID;
    ECHORESULTCOMPILEDFE(get_template($tid, $GBL_LANGID));
}

/**
 * safe_implode()
 * Returns an imploded string, even if its fails
 * @param mixed $arr
 * @param mixed $ident
 * @return string
 */
function safe_implode($arr, $ident) {
    if (is_array($arr)) {
        return implode($ident, $arr);
    }
    else
        return "";
}


/**
 * sort_db_result()
 * Multi sort of an array. Easy sorting of an array of rows by column.
 * $data = sort_multi_array($data, 'last_name', SORT_ASC, SORT_STRING, 'first_name', SORT_ASC, SORT_STRING); 
 * @param mixed $data
 * @return
 */
function sort_db_result(array $data) {
    return keimeno_class::sort_multi_array($data);
}

/**
 * sqlresult_to_array()
 * Transforms sql result into array
 * @param mixed $result
 * @return array
 */
function sqlresult_to_array($result) {
    global $kdb;
    $arr = array();
    while ($row = $kdb->fetch_array_names($result)) {
        $arr[] = $row;
    }
    return $arr;
}

/**
 * be_in_ssl_area()
 * Check if visitor is in ssl area
 * @return boolean
 */
function be_in_ssl_area() {
    return ((isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON'));
}

/**
 * tidy_page()
 * If pfp extention tidy is activated, it tidyup the html code
 * @param mixed $html
 * @return string
 */
function tidy_page(&$html) {
    global $gbl_config;
    if (extension_loaded('tidy')) {
        if ($gbl_config['xhtml_compiler'] == 'XHTML' && !defined('ISADMIN')) {
            $tidy = new tidy();
            $config = array(
                'output-xhtml' => true,
                'wrap' => 0,
                'alt-text' => $gbl_config['tidy_alt'],
                'preserve-entities' => true,
                'quote-ampersand' => true,
                'numeric-entities' => true,
                'hide-comments' => $gbl_config['tidy_hidecomments'],
                'doctype' => 'transitional');
            $tidy->ParseString($html, $config, 'utf8');
            $result = $tidy->CleanRepair();
            $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL . $tidy->html();
            $html = str_replace('xml:lang="" lang=""', 'xml:lang="en-en" lang="en-en"', $html);
        }
        if ($gbl_config['xhtml_compiler'] == 'HTML5' || (defined('ISADMIN') && ISADMIN == 1)) {
            $tidy = new tidy();
            $config = array(
                "char-encoding" => "utf8",
                'output-html' => true,
                'alt-text' => $gbl_config['tidy_alt'],
                'new-empty-tags' => 'i',
                'new-inline-tags' => 'video,i,audio,canvas,ruby,rt,rp',
                'doctype' => '<!DOCTYPE HTML>',
                'indent-attributes' => false,
                'hide-comments' => $gbl_config['tidy_hidecomments'],
                'wrap' => 0,
                'word-2000' => 1,
                'new-blocklevel-tags' => 'address, header, footer, article, section, hgroup, nav, figure, aside, date, main, figcaption, time');
            $tidy->ParseString($html, $config, 'utf8');
            $result = $tidy->CleanRepair();
            $html = '<!DOCTYPE HTML>' . PHP_EOL . $tidy->html();
            $html = str_replace(array(
                '<html lang="en" xmlns="http://www.w3.org/1999/xhtml">',
                '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="" lang="" dir="ltr">',
                '<html xmlns="http://www.w3.org/1999/xhtml">',
                '<html lang="" dir="ltr">'), '<html>', $html);
            $html = str_replace(array('<head profile="http://dublincore.org/documents/dcq-html/">', ), '<head>', $html);
        }
    }

    if ($gbl_config['tidy_hidecomments'] == 1) {
        $html = str_replace('<!---->', '<!-- -->', $html);
        $html = preg_replace('/<!--[^\[](.|\s)*?-->/', '', $html);
    }

    if ($gbl_config['tidy_compress'] == 1) {
        $html = str_replace(array(
            "/*<![CDATA[*/",
            "/*]]>*/",
            "//]]>",
            "]]>",
            "//<![CDATA["), "", $html);

        if (!defined('ISADMIN')) {
            //  Removes single line '//' comments, treats blank characters
            $html = preg_replace('![ \t]*// .*[ \t]*[\r\n]!', '', $html);
            # standard compress
            $html = preg_replace("/\r?\n/m", "", $html);
            $html = preg_replace("/\t/m", "", $html);
            $html = preg_replace('/[\s]+/', ' ', $html);
        }
    }

    if ($gbl_config['xhtml_compiler'] == 'NONE') {

    }
    return $html;
}


/**
 * build_land_selectbox()
 * Creates an <option> tag string with list of countries
 * @param mixed $selected_id
 * @return string
 */
function build_land_selectbox($selected_id) {
    global $kdb, $GBL_LANGID;
    $land_trans = array();
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_LAND . " WHERE visible=1 ORDER BY land");
    while ($row = $kdb->fetch_array_names($result)) {
        $translation = explode(";", $row['transland']);
        $trans_arr = array();
        foreach ($translation as $value) {
            list($langid, $langvalue) = explode("|", $value);
            $trans_arr[$langid] = $langvalue;
        }
        $land_trans[$row['id']] = array(
            'id' => $row['id'],
            'label' => ((trim($trans_arr[$GBL_LANGID]) == "") ? $row['land'] : $trans_arr[$GBL_LANGID]),
            'selected' => (($selected_id == $row['id']) ? ' selected' : ''));
    }

    if (count($land_trans) > 0) {
        $lang_opt = "";
        $sort_arr = array();
        foreach ($land_trans AS $uniqid => $row) {
            foreach ($row AS $sortkey => $value) {
                $sort_arr[$sortkey][$uniqid] = $value;
            }
        }
        if (is_array($sort_arr['label']) && count($sort_arr['label']) > 0) {
            array_multisort($sort_arr['label'], SORT_ASC, SORT_REGULAR, $land_trans);
        }
        foreach ($land_trans as $lid => $lval) {
            $lang_opt .= '<option value="' . $lval['id'] . '"' . $lval['selected'] . '>' . $lval['label'] . '</option>';
        }
    }
    return $lang_opt;
}

/**
 * printMenge()
 * Returns german formated number
 * @param mixed $menge
 * @param integer $nachkommastellen
 * @param bool $cuttozero
 * @param bool $ganzzahl
 * @return
 */
function printMenge($menge, $nachkommastellen = 2, $cuttozero = TRUE, $ganzzahl = false) {
    $menge = str_replace(",", ".", $menge);
    if ($cuttozero === TRUE && ($menge * 1) == 0)
        return 0;
    if ($ganzzahl == TRUE) {
        $menge = keimeno_class::format_number($menge, 1);
    }
    else
        $menge = number_format($menge, 2, ',', '.');
    return $menge;
}

/**
 * is_module_installed()
 * Check if modul ist installed
 * @param mixed $mod_name
 * @return boolean
 */
function is_module_installed($mod_name) {
    global $gbl_config;
    return (isset($gbl_config[$mod_name]) && (int)$gbl_config[$mod_name] == 1) || empty($mod_name);
}

/**
 * global_script_loader()
 * Executes keimeno core events
 * @param mixed $fn
 * @param mixed $exec_class
 * @param string $params
 * @return
 */
function global_script_loader($fn, $exec_class = null, $params = "") {
    return exec_evt($fn, $params, $exec_class);
}

/**
 * exec_evt()
 * Executes keimeno core events
 * @param mixed $fn
 * @param string $params
 * @param mixed $exec_class
 * @return
 */
function exec_evt($fn, $params = "", $exec_class = null) {
    global $MODMASTER;
    $request_params = $params;
    if (!$MODMASTER) {
        $MODMASTER = new modules_class();
    }
    $params = $MODMASTER->execute_event($fn, $exec_class, $params);
    # keimeno_class::allocate_memory($M);
    return !is_array($params) ? $request_params : $params;
}

/**
 * pure_translation_in_shop()
 * Shop translation for redimero
 * @param mixed $html
 * @param mixed $country_code
 * @return
 */
function pure_translation_in_shop($html, $country_code) {
    global $kdb, $gbl_config, $lang_content, $smarty, $DATA, $gbl_config_shop;
    $country_code = ($country_code == "") ? 'de' : $country_code;
    $country_code = strtolower($country_code);
    $lang_content_shop[$country_code] = $kdb->query_first("SELECT langarray,id FROM " . TBL_SHOPLANG . " WHERE local='" . $country_code . "'");
    $LANG_CONTENT = $lang_content_shop[$country_code];
    if ($LANG_CONTENT) {
        $lang_sets = explode('!#!', $LANG_CONTENT['langarray']);
        foreach ($lang_sets as $langset) {
            $lang_row = explode('!:!', $langset);
            $lang_key = '{' . $lang_row[0] . '}';
            $html = str_replace($lang_key, $lang_row[1], $html);
            $smarty->assign($lang_key, $lang_row[1]);
        }
    }
    else {
        # $html = '<b>Error by loading the Language [' . $langid . '] ' . $_SERVER['PHP_SELF'] . ' ' . FM_DOMAIN . '. Please check the Datarow</b>';
    }
    return $html;
}

/**
 * gen_pdf_onfly()
 * Generates a pdf file out of html code and send pdf file directly to browser
 * @param mixed $html
 * @param string $filename
 * @return
 */
function gen_pdf_onfly($html, $filename = "") {
    include_once (CMS_ROOT . "includes/pdf.class.php");
    $pdf_class = new pdf_class();
    $pdf_class->HTML2PDFonfly($pdf_class->compile_to_pdf($html, $filename));
}

/**
 * get_micro_time()
 * Returns micro time
 * @return string
 */
function get_micro_time() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * include_protection()
 * Include protection function
 * @return
 */
function include_protection() {
    if (IN_SIDE != 1) {
        header('location:' . PATH_CMS . 'index.html');
        exit;
    }
}

/**
 * gen_page_link()
 * Generates standard cms page link
 * @param mixed $id
 * @param mixed $linkname
 * @param integer $lid
 * @param string $local_id
 * @return string
 */
function gen_page_link($id, $linkname, $lid = 1, $local_id = "") {
    global $HTA_CLASS_CMS, $gbl_config;
    $local_id = ($local_id == "") ? $_SESSION['GBL_LOCAL_ID'] : $local_id;
    if ($gbl_config['std_lang_id'] != $lid) {
        if ($id == 0) {
            return SSL_PATH_SYSTEM . PATH_CMS . $local_id . '/' . $linkname . '.html';
        }
        else {
            return SSL_PATH_SYSTEM . PATH_CMS . $local_id . '/' . $id . '.html';
        }
    }
    else {
        if ($id == 0) {
            #  return SSL_PATH_SYSTEM . PATH_CMS . $HTA_CLASS_CMS->genLink(997, array($linkname));
            return SSL_PATH_SYSTEM . PATH_CMS . $linkname . '.html';
        }
        return SSL_PATH_SYSTEM . PATH_CMS . $HTA_CLASS_CMS->genLink(29, array($linkname, $id));
    }
}


/**
 * encrypt_password()
 * Encrypts customers password
 * @param mixed $password
 * @return string
 */
function encrypt_password($password_clear_text) {
    return password_hash(md5($password_clear_text) . keimeno_class::get_config_value('hash_secret'), PASSWORD_BCRYPT, array("cost" => 10));
}

/**
 * verfriy_password()
 * Verify customer's password
 * @param mixed $password
 * @param mixed $password_hash
 * @return boolean
 */
function verfriy_password($password_clear_text, $password_hash) {
    return password_verify(md5($password_clear_text) . keimeno_class::get_config_value('hash_secret'), $password_hash);
}

/* PHP 7 compatibility fix */
if (!function_exists('ereg')) {
    function ereg($pattern, $subject, &$matches = []) {
        return preg_match('/' . $pattern . '/', $subject, $matches);
    }
}
if (!function_exists('eregi')) {
    function eregi($pattern, $subject, &$matches = []) {
        return preg_match('/' . $pattern . '/i', $subject, $matches);
    }
}
if (!function_exists('ereg_replace')) {
    function ereg_replace($pattern, $replacement, $string) {
        return preg_replace('/' . $pattern . '/', $replacement, $string);
    }
}
if (!function_exists('eregi_replace')) {
    function eregi_replace($pattern, $replacement, $string) {
        return preg_replace('/' . $pattern . '/i', $replacement, $string);
    }
}
if (!function_exists('split')) {
    function split($pattern, $subject, $limit = -1) {
        return preg_split('/' . $pattern . '/', $subject, $limit);
    }
}
if (!function_exists('spliti')) {
    function spliti($pattern, $subject, $limit = -1) {
        return preg_split('/' . $pattern . '/i', $subject, $limit);
    }
}

/* end fix */
