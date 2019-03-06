<?php

/**
 * @package    otimer
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


defined('IN_SIDE') or die('Access denied.');
include ('calendar.inc.php');
$OTIMER_OBJ = new otimer_class();

$_GET['seldate'] = ($_POST['seldate'] != "") ? $_POST['seldate'] : $_GET['seldate'];
$_GET['hour'] = ($_POST['hour'] != "") ? $_POST['hour'] : $_GET['hour'];
$_GET['min'] = ($_POST['min'] != "") ? $_POST['min'] : $_GET['min'];
$_GET['employeeid'] = ($_POST['employeeid'] != "") ? $_POST['employeeid'] : $_GET['employeeid'];
$_GET['id'] = ($_POST['id'] != "") ? $_POST['id'] : $_GET['id'];


$_SESSION['OT_APPOINT']['employeeid'] = intval($_GET['employeeid']);
$_SESSION['seldate'] = ($_GET['seldate'] != "") ? $_GET['seldate'] : $_SESSION['seldate'];
$_GET['dateid'] = ($_POST['dateid'] != "") ? $_POST['dateid'] : $_GET['dateid'];
$_SESSION['seldate'] = ($_SESSION['seldate'] == '') ? date('Y-m-d') : $_SESSION['seldate'];
$_GET['id'] = (int)$_GET['id'];
$_SESSION['seldate'] = date('Y-m-d', strtotime($_SESSION['seldate']));

list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
$m = (strlen($m) == 1) ? '0' . $m : $m;
$cal = new tgcCalendar($d, $m, $Y);
$OTIMER_OBJ->init($cal, $GBL_LANGID);
if ($_GET['otgid'] > 0)
    $_SESSION['otgid'] = $_GET['otgid'];
$_SESSION['otgid'] = intval($_SESSION['otgid']);
$THEME_OBJ = $OTIMER_OBJ->genThemeMenu($_SESSION['otgid']);
if ($_SESSION['otgid'] == 0)
    $_SESSION['otgid'] = $THEME_OBJ['first_theme_id'];
$_GET['clid'] = (($_GET['clid'] > 0) ? $_GET['clid'] : $GBL_LANGID);
$DATE_ARR = array();
$DATE_ARR = $OTIMER_OBJ->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgid'], TRUE, 'tday');

if ($_GET['aktion'] == 'showday' || $_GET['aktion'] == '') {
    if ($gbl_config['ot_allow_past'] == 0 && $_SESSION['seldate'] < date('Y-m-d')) {
        header('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&seldate=' . date('Y-m-d') . '&msg=' . base64_encode('Datumsbereich ab ' . date('d.m.Y') .
            ' g&uuml;ltig.'));
        exit;
    }
    $OTIMER_OBJ->genCalTableFrontend($_SESSION['otgid'], $_SESSION['seldate'], $cal);
}

$_GET['hour'] = ($_GET['hour'] != "") ? $_GET['hour'] : date('H');
$_GET['min'] = ($_GET['min'] != "") ? $_GET['min'] : date('i');

$THEME_OBJ['page'] = $_GET['page'];
$THEME_OBJ['aktion'] = $_GET['aktion'];
$THEME_OBJ['seldate'] = $_SESSION['seldate'];
$THEME_OBJ['seldatetime'] = $cal->convertDateTime2Array($_SESSION['seldate'] . ' ' . date('H:i:s'));
$THEME_OBJ['allprograms'] = $OTIMER_OBJ->progProEmployTextList;

#*********************************
# Verfuegbarkeit
#*********************************
if ($_GET['aktion'] == 'showemploytab') {
    if ((int)$_GET['employid'] == 0) {
        header('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&msge=' . base64_encode('Bitte w&auml;hlen Sie erst einen Mitarbeiter aus.'));
        exit;
    }
    $EMPLOYEE = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . (int)$_GET['employid']);
    $EMPLOYEE['programs'] = safe_implode($OTIMER_OBJ->progProEmployTextList[(int)$EMPLOYEE['id']], ',');
    $workingdays = array();
    $sql = "SELECT * FROM " . TBL_CMS_OTIMER_DAYOPT . " DOPT, " . TBL_CMS_OTIMER_DAYWORKTIME . " DW 
  	WHERE DOPT.id=DW.dt_dayid AND DW.dt_mid=" . (int)$EMPLOYEE['id'] . " AND day_date>='" . date('Y-m-d') . "' AND day_date<='" . date('Y-m-d', strtotime(date('Y-m-d') .
        ' + 60 days')) . "' 
  	ORDER BY day_date";
    # echo $sql;
    $result = $kdb->query($sql);
    while ($row = $kdb->fetch_array_names($result)) {
        $workingdays[] = $OTIMER_OBJ->setOTDateObj($row['day_date'] . ' 00:00:00', $_SESSION['otgid']);
    }
    $EMPLOYEE['workingdays'] = $workingdays;
    $THEME_OBJ['employee'] = $EMPLOYEE;
}

$smarty->assign('otimer', $THEME_OBJ);
$smarty->assign('mdates_day', $DATE_ARR['day']);
$smarty->assign('mdates_day_count', count($DATE_ARR['day']));
$smarty->assign('clock_table', $DATE_ARR['clock_table']);
$smarty->assign('OTDATE_OBJ', $OTIMER_OBJ->setOTDateObj($_SESSION['seldate'] . ' ' . $_GET['hour'] . ':' . $_GET['min'] . ':' . date('s'), $_SESSION['otgid'], TRUE));
$smarty->assign('group_id', $_SESSION['otgid']);

#*********************************
# ADD_NEW
#*********************************
if ($_GET['id'] == 0) {
    $PROG = $OTIMER_OBJ->loadProg($OTIMER_OBJ->progProEmployIdList[0]);
}
else
    if ($_GET['id'] > 0) {
        $PROG = $OTIMER_OBJ->loadProg($_GET['id']);
    }
    else
        if ($_POST['FORM']['prog_id'] > 0) {
            $PROG = $OTIMER_OBJ->loadProg($_POST['FORM']['prog_id']);
        }
$smarty->assign('jav_prog_select', $OTIMER_OBJ->buildProgrammSelectJavaFE($PROG));
$smarty->assign('jav_prog_select_allowed', $OTIMER_OBJ->buildProgrammSelectJavaAllowed($PROG));
$smarty->assign('PROG', $PROG);


#*********************************
# OTIMER ENDTIME CALC & VALIDATE AJAX
#*********************************
if ($_POST['aktion'] == "calcendtime") {
    /*
    $_POST['setvalue'] = STUNDEN
    $_POST['setvalue2'] = MINUTEN
    $_POST['setvalue3'] = MITARBEITER ID
    */
    $endtime_ger = $OTIMER_OBJ->calcendtime($_POST['setvalue'], $_POST['setvalue2'], $_POST['duration'], $_POST['seldate']);
    $endtime_us = $OTIMER_OBJ->calcendtime($_POST['setvalue'], $_POST['setvalue2'], $_POST['duration'], $_POST['seldate'], TRUE);
    list($datum_ger, $zeit_ger) = explode(' ', $endtime_ger);
    $appointment = array();
    $appointment = $OTIMER_OBJ->genTimeSpanObj($_POST['seldate'] . ' ' . $_POST['setvalue'] . ':' . $_POST['setvalue2'] . ':00', $endtime_us, 'DATE');

    //Ueberlappung mit einem anderen Termin
    $dbl_use = $OTIMER_OBJ->doubleUsed($appointment);
    $hf = $appointment['timeto']['time']['H'] . '<sup>' . $appointment['timeto']['time']['i'] . '</sup> {LBL_OCLOCK}' . (($dbl_use === TRUE) ?
        '<br><span class="otimportant">{LBL_OTDBLUSE}</span>' : '<br><div class="otokbox">{LBL_OTUSEOK}</div>');

    //Arbeitet der ausgewaehlte Mitarbeiter?
    $employee_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . intval($_POST['setvalue3']));
    $hfw = (($OTIMER_OBJ->inEmployeeWorkingTime($_POST['seldate'], $_POST['groupid'], $_POST['setvalue3'], $appointment) === FALSE) ?
        '<br><span class="otimportant">Ihre gew&auml;hlte Zeit von ' . $_POST['setvalue'] . ':' . $_POST['setvalue2'] . ' bis ' . $zeit_ger . ' Uhr 
  	 deckt sich nicht mit den Arbeitszeiten des Mitarbeiters "' . $employee_obj['mitarbeiter_name'] . '".</span>' : '');

    // Kann ausgewaehlter Mitarbeiter Programm durchfuehren?
    $err_mitnoprog = ((!in_array($employee_obj['id'], $PROG['employeeids'])) ? '<br><span class="otimportant">' . $employee_obj['mitarbeiter_name'] .
        ' f&uuml;hrt "' . $PROG['pr_title'] . '" nicht aus.</span>' : '');


    if ($hfw != "")
        $hf = "";
    if ($err_mitnoprog != "")
        $hf = "";
    if ($dbl_use === TRUE) {
        $time_arr = $OTIMER_OBJ->nextOptimalStartTime($appointment);
        if (is_array($time_arr)) {
            $feedback_optimal_time = '<br>{LBL_OTDIFFTIME} <a href="' . PATH_CMS . 'index.php?id=' . $_POST['id'] . '&page=' . $_GET['page'] . '&aktion=addnew&seldate=' . $_POST['seldate'] .
                '&employeeid=' . $employee_obj['id'] . '&hour=' . $time_arr['timefrom']['time']['H'] . '">' . $time_arr['timefrom']['time']['formatedtime'] . ' Uhr</a>';
        }
    }
    ECHORESULTPUR(pure_translation($hf . $hfw . $feedback_optimal_time . $err_mitnoprog, $GBL_LANGID));
}

#*********************************
# SPEICHERN IN SESSION
#*********************************
if ($_POST['otaktion'] == 'a_save') {
    $_GET['employeeid'] = (int)$_GET['employeeid'];
    if (count($_POST['FORM']) == 0)
        $_POST['FORM'] = $_SESSION['OT_APPOINT'];
    $FORM = $_POST['FORM'];
    $FORM['comments_cu'] = strip_tags($FORM['comments_cu']);
    $FORM['time_from'] = $FORM['ndate'] . ' ' . $_POST['hour'] . ':' . $_POST['min'] . ':00';
    $FORM['time_to'] = $OTIMER_OBJ->calcendtime($_POST['hour'], $_POST['min'], $PROG['pr_duration'], $FORM['ndate'], TRUE);
    $PROG = $OTIMER_OBJ->loadProg($FORM['prog_id']);
    $FORM['prog_title'] = $TCMASTER->real_escape($PROG['pr_admintitle']);
    $err_gbl_arr = array();
    //Validierung, ob es eine Doppelbuchung gibt
    list($Y, $m, $d) = explode('-', $FORM['ndate']);
    $OTIMER_OBJ->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgid'], TRUE, 'tday');
    $_SESSION['appointment'] = $OTIMER_OBJ->genTimeSpanObj($FORM['time_from'], $FORM['time_to'], 'DATE');
    $employee_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . intval($_GET['employeeid']));
    if ($OTIMER_OBJ->doubleUsed($_SESSION['appointment']) == TRUE) {
        $err_gbl_arr[] = '{LBL_OTDBLUSE}';
    }
    // Validierung der Arbeitszeiten des ausgewählten Mitarbeiters
    if ($OTIMER_OBJ->inEmployeeWorkingTime($FORM['ndate'], $_SESSION['otgid'], $_GET['employeeid'], $_SESSION['appointment']) === FALSE) {
        $err_gbl_arr[] = '<br>Gew&auml;hlte Zeit deckt sich nicht mit Arbeitszeiten des Mitarbeiters ' . $employee_obj['mitarbeiter_name'];
    }
    // Kann ausgewaehlter Mitarbeiter Programm durchfuehren?
    if (!in_array($_GET['employeeid'], $PROG['employeeids'])) {
        $err_gbl_arr[] = '<br>' . $employee_obj['mitarbeiter_name'] . ' f&uuml;hrt "' . $PROG['pr_title'] . '" nicht aus.';
    }
    $FORM['created_date'] = date('Y-m-d');
    $FORM['prog_employee'] = $PROG['mitarbeiter_name'];
    $FORM['mid'] = $_GET['employeeid'];
    $_SESSION['OT_APPOINT'] = $FORM;
    if (count($err_gbl_arr) == 0) {
        $_SESSION['OTOBJ']['FORM'] = $_SESSION['OT_APPOINT'];
        $_SESSION['OTOBJ']['OK'] = TRUE;
        HEADER('location:' . PATH_CMS . 'index.php?page=' . $_GET['page'] . '&aktion=kreg&id=' . $OTIMER_OBJ->prog_id . '&seldate=' . $FORM['ndate']);
        exit;
    }
    else {
        $_SESSION['OT_APPOINT']['comments_cu'] = stripslashes($_SESSION['OT_APPOINT']['comments_cu']);
        $_SESSION['OT_APPOINT']['employeeid'] = intval($_GET['employeeid']);
        $_SESSION['OTOBJ']['OK'] = FALSE;
        $OTIMER_OBJ->msge(implode('<br>', $err_gbl_arr));
    }
}
$smarty->assign('OTFORM', $_SESSION['OT_APPOINT']);
#*********************************
# SPEICHERN UND ABSCHLIESSEN
#*********************************
if ($_GET['aktion'] == 'kreg' || $_POST['aktion'] == 'kreg') {

    if (count($_POST['FORM']) == 0)
        $_POST['FORM'] = $_SESSION['kregform'];
    $FORM = $_POST['FORM'];
    $FORM_NOTEMPTY = $_POST['FORM_NOTEMPTY'];
    $user_obj->buildDefaultSelect($anrede_arr, $knownof, $FORM);


    if (count($_POST['FORM']) > 0) {
        //VALIDIERUNG
        $err_arr = $err_gbl_arr = array();
        if (!validate_email_input($FORM['email'])) {
            $err_arr = keimeno_class::add_smarty_err($err_arr, 'email', '{ERR_EMAIL}');
        }
        $str_arr = array(
            'strasse',
            'ort',
            'bank',
            'nachname',
            'vorname');
        foreach ($str_arr as $key) {
            if ($FORM[$key] != "")
                $FORM[$key] = format_name_string($FORM[$key]);
            if ($FORM_NOTEMPTY[$key] != "")
                $FORM_NOTEMPTY[$key] = format_name_string($FORM_NOTEMPTY[$key]);
        }
        if (count($FORM_NOTEMPTY) > 0) {
            foreach ($FORM_NOTEMPTY as $key => $value) {
                if ($value == '') {
                    $err_arr = keimeno_class::add_smarty_err($err_arr, $key, '{LBL_MISSING}');
                }
                $FORM[$key] = $value;
            }
        }
        if ($FORM['mailactive'] != 1)
            $FORM['mailactive'] = 0;
        $FORM['email'] = strtolower($FORM['email']);
        foreach ($FORM as $key => $wert) {
            if (validate_subject($wert) == false) {
                keimeno_class::msge('{ERR_INPUT}');
                break;
            }
        }
        if ($_SESSION['OTOBJ']['OK'] === FALSE) {
            keimeno_class::msge('Ung&uuml;ltiger Termin. Bitte gehen Sie einen Schritt zur&uuml; und wählen einen Termin aus.');
        }
        $k_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . $FORM['email'] . "'");
        // Kunde in Blacklist
        if ($k_obj['kid'] > 0 && get_data_count(TBL_CMS_OTIMER, 'id', "kid=" . (int)$k_obj['kid'] . " AND block_kid=1") > 0) {
            keimeno_class::msge('{LBL_OTBLOCKEDCUSTINFO}');
            $smarty->assign('cuinblacklist', true);
        }
        //SPEICHERN
        if (count($err_arr) == 0 && count($_SESSION['err_msgs']) == 0) {
            $FORM['monat'] = date("m");
            $FORM['jahr'] = date("Y");
            $FORM['tag'] = date("d");
            $FORM['datum'] = date('Y-m-d');
            $FORM['is_cms'] = 1;
            $FORM['anrede'] = get_customer_salutation($FORM['geschlecht']);
            $FORM['geschlecht'] = get_customer_sex($FORM['geschlecht']);
            if ($k_obj['email'] == "") {
                $k_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . insert_table(TBL_CMS_CUST, $FORM) . "'");
                $TCMASTER->LOGCLASS->addLog('INSERT', 'new registration ' . $k_obj['nachname'] . ', ' . $k_obj['kid']);
            }
            $_SESSION['OTOBJ']['FORM']['kid'] = $k_obj['kid'];
            $n_id = insert_table(TBL_CMS_OTIMER, $_SESSION['OTOBJ']['FORM']);
            $_SESSION['N_OBJ'] = $OTIMER_OBJ->loadAppointment($n_id);
            send_mail_to(replacer(get_email_template(970), $k_obj['kid']), "", TRUE); // Template "Registrierung"
            $TCMASTER->LOGCLASS->addLog('INSERT', 'new online timer item ' . $_SESSION['N_OBJ']['ndate'] . ', ' . $k_obj['nachname'] . ', ' . $k_obj['kid']);
            unset($_SESSION['OTOBJ']);
            unset($_SESSION['otgid']);
            unset($_SESSION['appointment']);
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&kregdone=1');
            exit;
        }
    }


    $_SESSION['kregform'] = $FORM;
    $smarty->assign('kregform', $_SESSION['kregform']);
    $smarty->assign('appointment', $_SESSION['appointment']);
    $smarty->assign('kregform_err', $err_arr);
    $smarty->assign('global_err', $err_gbl_arr);
}

#*********************************
# AKTIVIEREN
#*********************************
$smarty->assign('date_activated', ($_GET['date_activated'] == 1));
if ($_GET['hash'] == sha1($_GET['sec'] . $_GET['akt'])) {
    $k_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . intval($_GET['sec']) . "'");
    $N_OBJ = $OTIMER_OBJ->loadAppointment(intval($_GET['akt']));
    $appointment = $OTIMER_OBJ->genTimeSpanObj($N_OBJ['ndate'] . ' ' . $N_OBJ['starttime']['time']['time'], $N_OBJ['ndate'] . ' ' . $N_OBJ['endtime']['time']['time'],
        'DATE');

    //Ueberlappung mit einem anderen Termin
    $dbl_use = $OTIMER_OBJ->doubleUsed($appointment, $N_OBJ['DATEID']);
    if ($dbl_use == false) {
        if ($N_OBJ['kid'] == $k_obj['kid']) {
            $kdb->query("UPDATE " . TBL_CMS_OTIMER . " SET approval=1 WHERE id=" . intval($_GET['akt']));
            $TCMASTER->LOGCLASS->addLog('UPDATE', 'appointment aktivated by mail ID:' . $_GET['akt'] . ', KID: ' . $k_obj['kid'] . ',' . $k_obj['nachname']);
            header('location:' . PATH_CMS . 'index.php?page=' . $_GET['page'] . '&date_activated=1');
            exit;
        }

        else {
            header('location:' . PATH_CMS . 'index.html');
            exit;
        }
    }
    else {
        $TCMASTER->msge('Termin bereits vergeben. Buchung nicht möglich.');
        header('location:' . PATH_CMS . 'index.html');
        exit;
    }
}


$smarty->assign('kregdone', intval($_GET['kregdone']));
unset($DATE_ARR);
unset($OTIMER_OBJ);
unset($THEME_OBJ);
