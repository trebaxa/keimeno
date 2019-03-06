<?php

/**
 * @package    otimer
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */

$cal = new tgcCalendar();
$OTIMER_OBJ = new otimer_class();
$OTIMER_OBJ->init($cal, $_SESSION['alang_id']);


$OTIMER = new otimer_admin_class();
$OTIMER->TCR->interpreter();
list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
$sel_date = my_date('d.m.Y', $_SESSION['seldate']);

if ($_GET['aktion'] == 'setmissed') {
    $OTIMER_OBJ->set_missed_date($_GET['id'], $_GET['value']);
    $OTIMER_OBJ->msg('{LBLA_SAVED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
    exit;
}

if ($_GET['aktion'] == 'setblock') {
    $OTIMER_OBJ->set_blocked_customer_by_date($_GET['id'], $_GET['value']);
    $OTIMER_OBJ->msg('{LBLA_SAVED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
    exit;
}

if ($_GET['aktion'] == 'setblockcust') {
    $OTIMER_OBJ->set_blocked_customer($_GET['id'], $_GET['value']);
    $OTIMER_OBJ->msg('{LBLA_SAVED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=blacklist');
    exit;
}

#*********************************
# SAVE PROG DESCR
#*********************************
if ($_POST['aktion'] == "a_saveprog") {
    $_POST['FORM']['pr_duration'] = validate_num_for_sql($_POST['FORM']['pr_duration']);
    if (!is_array($_POST['employees']))
        $_POST['employees'] = array();
    $_POST['FORM']['pr_employees'] = implode(';', $_POST['employees']);

    foreach ($_POST['employees'] as $mid) {
        $employee_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . $mid);
        $_POST['FORM']['pr_employees_names'] .= (($_POST['FORM']['pr_employees_names'] != "") ? ',' : '') . $employee_obj['mitarbeiter_name'];
    }

    update_table(TBL_CMS_OTIMER_PROG, 'id', $_POST['tid'], $_POST['FORM']);

    foreach ($_POST['FORM_LANG'] as $lang_id => $arr) {
        $LANGT = array();
        $LANGT['pr_prog_id'] = $_POST['tid'];
        foreach ($arr as $bd_ident => $values) {
            foreach ($values as $td => $value) {
                $LANGT[$td] = $value;
            }
        }
        $kdb->query("DELETE FROM " . TBL_CMS_OTIMER_PROG_LANG . " WHERE pr_prog_id=" . $_POST['tid'] . " AND lang_id=" . $lang_id . " LIMIT 1");
        insert_table(TBL_CMS_OTIMER_PROG_LANG, $LANGT);
    }
    $OTIMER_OBJ->msg('{LBLA_SAVED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=otprograms_edit&id=' . $_POST['tid']);
    exit;
}


if ($_POST['aktion'] == 'a_repeatapp') {
    $N_OBJ = $OTIMER_OBJ->loadAppointment($_POST['dateid']);
    $KOBJ = $kdb->query_first("SELECT * FROM		" . TBL_CMS_CUST . " P 				WHERE P.kid=" . $N_OBJ['kid'] . " LIMIT 1");
    $KICKOUT = $kdb->query_first("SELECT * FROM	" . TBL_CMS_OTIMER . " WHERE id=" . $_POST['dateid'] . "		");
    $savedItems = $notSavedItems = 0;
    for ($i = 1; $i <= intval($_POST['timespan']); $i++) {
        $NEXT_N_OBJ = $N_OBJ;
        $NEXT_N_OBJ['created_date'] = $NEXT_N_OBJ['ndate'] = $cal->addTime2Date($N_OBJ['starttime']['date_us'], 0, 0, ($_POST['repdays'] * $i), 'Y-m-d');
        $NEXT_N_OBJ['time_from'] = $NEXT_N_OBJ['ndate'] . ' ' . $N_OBJ['starttime']['time']['time'];
        $NEXT_N_OBJ['time_to'] = $OTIMER_OBJ->calcendtime($N_OBJ['starttime']['time']['H'], $N_OBJ['starttime']['time']['i'], $N_OBJ['program_obj']['pr_duration'], $NEXT_N_OBJ['ndate'], TRUE);
        $NEXT_N_OBJ['starttime'] = $NEXT_N_OBJ['timefrom'] = $cal->convertDateTime2Array($NEXT_N_OBJ['time_from']);
        $NEXT_N_OBJ['endtime'] = $NEXT_N_OBJ['timeto'] = $cal->convertDateTime2Array($NEXT_N_OBJ['time_to']);
        $DATE_ARR = array();
        $DATE_ARR = $OTIMER_OBJ->buildDateArr_MonthDay($NEXT_N_OBJ['starttime']['date']['Y'], $NEXT_N_OBJ['starttime']['date']['m'], $NEXT_N_OBJ['starttime']['date']['d'],
            $_SESSION['otgroup_id'], false, 'tmonth');
        $dbuse = false;
        if ($OTIMER_OBJ->doubleUsed($NEXT_N_OBJ) == TRUE) {
            $msge_dbluse .= $NEXT_N_OBJ['starttime']['date_ger'] . '[BR]';
            $dbuse = true;
            $notSavedItems++;
        }
        else {
            $savedItems++;
        }
        foreach ($KICKOUT as $key => $value)
            $allowed_keys[] = $key;
        foreach ($NEXT_N_OBJ as $key => $wert) {
            if (!in_array($key, $allowed_keys))
                unset($NEXT_N_OBJ[$key]);
            else
                $NEXT_N_OBJ[$key] = $TCMASTER->db->real_escape_string($NEXT_N_OBJ[$key]);
        }
        unset($NEXT_N_OBJ['id']);
        if ($dbuse === false)
            insert_table(TBL_CMS_OTIMER, $NEXT_N_OBJ);
    }

    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=&msg=' . base64_encode($savedItems . 'Termine wurden gespeichert.') . (($msge_dbluse !=
        "") ? '&msge=' . base64_encode('Folgende Termine wurden wegen &Uuml;berschneidung nicht gespeichert:[BR]' . $msge_dbluse) : ''));
    exit;
}
#*********************************
# SAVE && ADD APPOINTMENT
#*********************************
if ($_POST['aktion'] == 'a_save') {
    $FORM = $_POST['FORM'];
    $FORM['comments_ma'] = strip_tags($FORM['comments_ma']);
    $FORM['comments_cu'] = strip_tags($FORM['comments_cu']);
    $FORM['prog_employeeid'] = intval($_POST['employid']);
    $FORM['time_from'] = $FORM['ndate'] . ' ' . $_POST['hour'] . ':' . $_POST['min'] . ':00';
    $FORM['time_to'] = $OTIMER_OBJ->calcendtime($_POST['hour'], $_POST['min'], $_POST['duration'], $FORM['ndate'], TRUE);
    $FORM['kid'] = $_SESSION['KOBJ']['kid'];
    $k_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . intval($FORM['kid']) . "'");
    $PROG = $OTIMER_OBJ->loadProg($FORM['prog_id']);
    $FORM['prog_title'] = $TCMASTER->db->real_escape_string($PROG['pr_admintitle']);
    $employee_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . intval($_POST['employid']));
    $FORM['prog_employee'] = $TCMASTER->db->real_escape_string($employee_obj['mitarbeiter_name']);
    $msge = '';
    if ($FORM['kid'] == 0) {
        $msge .= 'Kunde nicht ausgewählt[BR]';
    }
    //Validierung, ob es eine Doppelbuchung gibt
    list($Y, $m, $d) = explode('-', $FORM['ndate']);
    $DATE_ARR = array();
    $DATE_ARR = $OTIMER_OBJ->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgroup_id'], false, 'tmonth');
    $appointment = $OTIMER_OBJ->genTimeSpanObj($FORM['time_from'], $FORM['time_to'], 'DATE');
    if ($OTIMER_OBJ->doubleUsed($appointment, intval($_POST['dateid'])) == TRUE) {
        $msge_dbluse = 'Es liegt eine Termin&uuml;berschneidung vor.[BR]';
    }
    // Validierung der Arbeitszeiten des ausgewählten Mitarbeiters
    if ($OTIMER_OBJ->inEmployeeWorkingTime($FORM['ndate'], $_POST['groupid'], $_POST['employid'], $appointment) === FALSE) {
        $msge_employ = '[BR]Gew&auml;hlte Zeit deckt sich nicht mit Arbeitszeiten des Mitarbeiters ' . $employee_obj['mitarbeiter_name'];
    }

    //Speichern - neu anlegen
    if ($msge == "" && $msge_employ == "") {
        if (intval($_POST['dateid']) == 0) {
            $FORM['mid'] = $_SESSION['mitarbeiter'];
            $FORM['created_date'] = date('Y-m-d');
            if ($msge_dbluse == '') {
                $_POST['dateid'] = $id = insert_table(TBL_CMS_OTIMER, $FORM);
                $did_insert = True;
            }
        }
        else {
            update_table(TBL_CMS_OTIMER, 'id', $_POST['dateid'], $FORM);
            $did_insert = false;
        }
        if ($did_insert === TRUE) {
            $TCMASTER->LOGCLASS->addLog('INSERT', 'new online timer item (ID:' . $id . ') ' . $FORM['ndate'] . ', ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ',' . $k_obj['email']);
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=&msg=' . base64_encode('{LBLA_SAVED}'));
        }
        else {
            $OTIMER_OBJ->msg('{LBLA_SAVED}');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=editdate&dateid=' . (int)$_REQUEST['dateid'] . '&seldate=' . $FORM['ndate']);
        }
        exit;
    }
    else {
        $_GET['msge'] = base64_encode($msge . $msge_dbluse . $msge_employ);
        $_GET['aktion'] = $_POST['refaktion'];
        $_GET['seldate'] = $FORM['ndate'];
    }
}

function uptins_table($table, $id_name, $id_value, $FORM, $admin = 0) {
    if (is_array($FORM)) {
        if ((int)$id_value > 0) {
            update_table($table, $id_name, $id_value, $FORM);
        }
        else {
            insert_table($table, $FORM);
        }
    }
}


if ($_GET['gid'] > 0)
    $_SESSION['otgroup_id'] = (int)$_GET['gid'];
$_GET['uselang'] = ($_GET['uselang'] == 0) ? $gbl_config['std_lang_id'] : $_GET['uselang'];

$menu = array(
    "{LBL_TERMINE}" => "aktion=",
    "{LBL_ARBEITSZEITEN} " . my_date('d.m.Y', $_SESSION['seldate']) => "aktion=worktime",
    "{LBL_ARBEITSZEITEN} " . my_date('m/Y', $_SESSION['seldate']) => "aktion=worktimem",
    "{LBL_OTOVERVIEW}" => "aktion=overview",
    "{LBL_OTPROGRAMS}" => "aktion=otprograms",
    "{LBL_OTTHEME}" => "aktion=otgroups",
    "{LBL_OTBLACKLIST}" => "aktion=blacklist",
    "{LBL_CONFIG}" => "aktion=conf");
$ADMINOBJ->set_top_menu($menu);

$ADMINOBJ->inc_tpl('otimer');


#*********************************
# Kalendar Übersicht
#*********************************
if ($_GET['aktion'] == "") {
    $future_year = date('Y') + 2;
    if ($_REQUEST['set_year'] > 0) {
        $Y = $_SESSION['ONLINRE']['set_year'] = $_REQUEST['set_year'];
    }
    $_SESSION['ONLINRE']['set_year'] = ($_SESSION['ONLINRE']['set_year'] == 0) ? date('Y') : $_SESSION['ONLINRE']['set_year'];
    $OTOBJ = array(
        'future_year' => $future_year,
        'current_day' => date('d'),
        'current_month' => date('m'),
        'set_year' => $_SESSION['ONLINRE']['set_year']);
    $smarty->assign('OTOBJ', $OTOBJ);


    $k = 0;
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_OTIMER_GROUPS . " ORDER BY groupname");
    while ($row = $kdb->fetch_array_names($result)) {
        if (intval($_SESSION['otgroup_id']) == 0 && $k == 0)
            $_SESSION['otgroup_id'] = $row['id'];
        $sel_box .= '<li' . (($row['id'] == $_SESSION['otgroup_id']) ? ' class="active"' : '') . '><a href="' . $_SERVER['PHP_SELF'] . '?epage=otimer.inc&seldate=' . $Y .
            '-' . $m . '-' . $d . '&gid=' . $row['id'] . '">' . $row['groupname'] . '</a></li>';
        $k++;
    }
    $smarty->assign('sel_box', $sel_box);
    unset($sel_box);

    $_SESSION['otgroup_id'] = intval($_SESSION['otgroup_id']);
    /*  $result = $kdb->query("SELECT NL.id AS NLID,K.*,NL.*,NG.*,NG.id AS NGID
    FROM " . TBL_CMS_OTIMER . " NL
    INNER JOIN " . TBL_CMS_OTIMER_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['otgroup_id'] . ")
    LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
    WHERE ndate>='" . $Y . "-01-01' AND ndate<='" . $Y . "-12-31'
    GROUP BY NL.id ORDER BY ndate DESC");
    $OTIMER->build_date_table_admin($result, $Y);
    unset($result);
    */
    $smarty->assign('seldateform', $sel_date);
    $DATE_ARR = array();
    $DATE_ARR = $OTIMER_OBJ->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgroup_id'], false, 'tmonth');
    $smarty->assign('mdates', $DATE_ARR['year']);
    $smarty->assign('mdates_month', $DATE_ARR['month']);
    $smarty->assign('mdates_day', $DATE_ARR['day']);
    $smarty->assign('clock_table', $DATE_ARR['clock_table']);
    $smarty->assign('inlay', $inlay);
    $smarty->assign('cal_month', $m);
    $smarty->assign('cal_year', $Y);
    $smarty->assign('cal_day', $d);
    $smarty->assign('subbtngo', kf::gen_admin_sub_btn('GO'));
    $smarty->assign('seldate_us', $_SESSION['seldate']);

    if ($_GET['aktion'] != 'edit')
        $smarty->assign('cal_table', $cal_table);

    unset($DATE_ARR);

}


#*********************************
# Termin editieren / anlegen
#*********************************
if ($_GET['aktion'] == 'dbo_edit') {
    $CG = $kdb->query_first("SELECT *	FROM " . TBL_CMS_OTIMER_GROUPS . "	WHERE id=" . $_GET['id']);
    $ADMINOBJ->content .= '
        <h3>' . $CG['groupname'] . '</h3>
	<form action="<%$PHPSELF%>" method="post">
	<input type="hidden" name="epage" value="' . $_GET['epage'] . '">
	<h6>{LBL_TITLE}</h6>
	<table class="table table-striped table-hover">
        <tr><th colspan="2">&Uuml;bersetzungen</th></tr>';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY post_lang");
    while ($row = $kdb->fetch_array_names($result)) {
        $FORM_CON = $kdb->query_first("SELECT * FROM " . TBL_CMS_OTIMER_GCON . " WHERE g_id='" . $_GET['id'] . "' AND lang_id=" . intval($row['id']));
        $ADMINOBJ->content .= '<tr><td>Titel (' . $row['post_lang'] . '):</td>
		<td>
		<input ' . kf::gen_inputtext_field($FORM_CON['g_title']) . ' name="FORM_LANG[' . $FORM_CON['id'] . '][g_title]">
		<input type="hidden" value="' . intval($row['id']) . '" name="FORM_LANG[' . $FORM_CON['id'] . '][lang_id]">
		</td></tr>';
    }
    $ADMINOBJ->content .= '</table>';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " ORDER BY groupname");
    while ($row = $kdb->fetch_array_names($result)) {
        $perm_checkoxes .= '<input type="checkbox" ' . ((get_data_count(TBL_CMS_OTIMER_PERM, 'perm_did', "perm_did=" . $_GET['id'] . " AND perm_group_id=" . $row['id']) >
            0) ? 'checked' : '') . ' name="CUSTGROUP[' . $row['id'] . ']" value="' . $row['id'] . '"> ' . $row['groupname'] . '<br>';
    }
    if ($perm_checkoxes != "") {
        $ADMINOBJ->content .= '<h3>{LBL_PERMISSIONS}</h3>' . $perm_checkoxes;
    }
    $ADMINOBJ->content .= '
	<input type="hidden" name="tid" value="' . $_GET['id'] . '">
	<input type="hidden" name="aktion" value="setallperm"><%$subbtn%></form>';
}

#********************************* MelDa2014!
# PROGRAM EDITOR
#*********************************
if ($_GET['aktion'] == 'otprograms_edit') {
    $CG = $kdb->query_first("SELECT *
	FROM " . TBL_CMS_OTIMER_PROG . "
	WHERE id=" . $_GET['id'] . "
	");
    $ADMINOBJ->content .= '
	<div class="page-header"><h1>' . $CG['pr_admintitle'] . '</h1></div>
	<div style="width:800px">
<fieldset>
<legend>Einstellungen</legend>
	<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
	<input type="hidden" name="epage" value="' . $_GET['epage'] . '">
	<input type="hidden" name="msid" value="' . $_GET['msid'] . '">
	<table width="800" class="table table-striped table-hover">
	<tr>
	  <td>admin. {LBL_TITLE}:</td>
		<td><input ' . kf::gen_inputtext_field($CG['pr_admintitle']) . ' name="FORM[pr_admintitle]"></td>
	</tr>		
	<tr>
	  <td>admin. {LBL_DESCRIPTION}:</td>
		<td><input ' . kf::gen_inputtext_field($CG['pr_description']) . ' name="FORM[pr_description]"></td>
	</tr>	
	<tr>
	  <td width="300">Ausf&uuml;hrende {LBL_EMPLOYEE}:</td>
		<td>';
    $employs = explode(';', $CG['pr_employees']);
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id<>100 ORDER BY mitarbeiter_name");
    while ($row = $kdb->fetch_array_names($result)) {
        $ADMINOBJ->content .= '<input type="checkbox" ' . ((in_array($row['id'], $employs)) ? 'checked="checked"' : '') . ' value="' . $row['id'] .
            '" name="employees[]">' . $row['mitarbeiter_name'] . '<br>';
    }
    $ADMINOBJ->content .= '	</td>
	</tr>
	<tr>
	  <td>{LBL_PROGRAM} {LBL_DURATION}:</td>
		<td><input ' . kf::gen_inputtext_field($CG['pr_duration'] * 1) . ' name="FORM[pr_duration]"></td>
	</tr>	
	
	</table>' . kf::gen_admin_sub_btn('{LA_SAVE}') . '

	<table width="800" class="table table-striped table-hover"><tr colspan="2"><th>&Uuml;bersetzungen</th></tr>';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
    while ($row = $kdb->fetch_array_names($result)) {
        $FORM_CON = $kdb->query_first("SELECT * FROM " . TBL_CMS_OTIMER_PROG_LANG . " WHERE pr_prog_id='" . $_GET['id'] . "' AND lang_id=" . intval($row['id']));
        $ADMINOBJ->content .= '<tr><td>{LBL_TITLE} (' . $row['post_lang'] . '):</td>
		<td>
		<input ' . kf::gen_inputtext_field($FORM_CON['pr_title']) . ' name="FORM_LANG[' . intval($row['id']) . '][' . $FORM_CON['id'] . '][pr_title]">
		<input type="hidden" value="' . intval($row['id']) . '" name="FORM_LANG[' . intval($row['id']) . '][' . $FORM_CON['id'] . '][lang_id]">
		</td></tr>
		<tr><td colspan="2">' . create_html_editor('FORM_LANG[' . intval($row['id']) . '][' . $FORM_CON['id'] . '][pr_description]', $FORM_CON['pr_description'], 300) .
            '</td></tr>
		';
    }
    $ADMINOBJ->content .= '</table>';
    $ADMINOBJ->content .= '
	<input type="hidden" name="tid" value="' . $_GET['id'] . '">
	<input type="hidden" name="aktion" value="a_saveprog">' . kf::gen_admin_sub_btn('{LA_SAVE}') . '</form>
	</fieldset>
	</div>';
}


#*********************************
# EDIT & ADD APPOINTMENT
#*********************************
if ($_GET['aktion'] == 'editdate' || $_GET['aktion'] == 'addnewdate') {
    $_GET['id'] = intval($_GET['id']);
    $N_OBJ = array();
    list($Y, $m, $d) = explode("-", $_GET['seldate']);

    if ($_GET['id'] > 0) {
        $PROG = $OTIMER_OBJ->loadProg($_GET['id']);
    }
    else {
        $PROG = $OTIMER_OBJ->loadProg();
    }

    if ($_GET['aktion'] == 'addnewdate') {
        $DAY = $OTIMER_OBJ->loadDayOpt($_GET['seldate'], $_SESSION['otgroup_id']);
        $N_OBJ['DAY'] = $DAY;
        $N_OBJ['time_from'] = date('Y-m-d H:i:s');
        unset($_SESSION['KOBJ']);
    }

    if ($_GET['aktion'] == 'editdate') {
        if ($_GET['dateid'] == 0) {
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
            exit;
        }
        $N_OBJ = $OTIMER_OBJ->loadAppointment($_GET['dateid']);
        $DAY = $N_OBJ['DAY'];
        if ($N_OBJ['prog_id'] > 0 && $_GET['id'] == 0) {
            $PROG = $OTIMER_OBJ->loadProg($N_OBJ['prog_id']);
        }
        $_GET['seldate'] = $N_OBJ['ndate'];
        if ($_GET['setkid'] == 0)
            $_GET['setkid'] = $N_OBJ['kid'];
    }

    if ($_GET['setkid'] > 0) {
        $_SESSION['KOBJ'] = $KOBJ = $kdb->query_first("SELECT * FROM
		" . TBL_CMS_CUST . " P 		
		WHERE P.kid=" . $_GET['setkid'] . " LIMIT 1");
        $smarty->assign('KOBJ', $KOBJ);
    }
    $DATE_ARR = array();
    $DATE_ARR = $OTIMER_OBJ->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgroup_id'], false, 'tmonth');

    if ($N_OBJ['prog_employeeid'] == 0 && (int)$_SESSION['OTIMERLOG']['last_emp_id'] > 0 && $_GET['aktion'] == 'addnewdate') {
        $N_OBJ['prog_employeeid'] = (int)$_SESSION['OTIMERLOG']['last_emp_id'];
    }

    $smarty->assign('OTDATE_OBJ', $OTIMER_OBJ->setOTDateObj($N_OBJ['time_from'], $_SESSION['otgroup_id']));
    $smarty->assign('group_id', $_SESSION['otgroup_id']);
    $smarty->assign('N_OBJ', $N_OBJ);
    $smarty->assign('aktion', $_GET['aktion']);
    $smarty->assign('dateid', $_GET['dateid']);
    $smarty->assign('id', $PROG['PROGID']);
    $smarty->assign('seldate_us', $_GET['seldate']);
    $smarty->assign('subbtn', kf::gen_admin_sub_btn('{LA_SAVE}'));
    $smarty->assign('PROG', $PROG);
    $smarty->assign('DAY', $DAY);
    $smarty->assign('mitselect', build_options_for_selectbox_opt(TBL_CMS_ADMINS, 'id', 'mitarbeiter_name', 'WHERE id<>100 ORDER BY mitarbeiter_name', $N_OBJ['prog_employee'],
        '', ''));
    $smarty->assign('jav_prog_select', $OTIMER_OBJ->buildProgrammSelectJavaAdmin($PROG));
    $smarty->assign('mdates_day', $DATE_ARR['day']);
    $smarty->assign('mdates_day_count', count($DATE_ARR['day']));
    $smarty->assign('seldate', my_date('d.m.Y', $_GET['seldate']));
    $smarty->assign('clock_table', $DATE_ARR['clock_table']);
    $ADMINOBJ->inc_tpl('otimer.edit');
    unset($DATE_ARR);
}
#*********************************
# SHOW CONFIG
#*********************************
if ($_GET['aktion'] == 'conf') {
    $ADMINOBJ->content .= '<div class="page-header"><h1>Konfiguration</h1></div>';
    $result = $kdb->query("SELECT C.*,CG.catgroup AS Ccatgroup, CG.id AS CGID	FROM " . TBL_CMS_GBLCONFIG . " C, " . TBL_CMS_CONFGROUPS . " CG 
				WHERE CG.id=18 AND CG.id=C.gid ORDER BY CG.catgroup,C.morder");
    $ADMINOBJ->content .= $CONFIG_OBJ->buildTable(18, 18);
}

#*********************************
# REPEAT APPOINTMENT
#*********************************
if ($_GET['aktion'] == 'repeatappointment') {
    if ((int)$_GET['dateid'] == 0) {
        header('location: ' . $_SERVER['PHP_SELF'] . '?msge=' . base64_encode('Kein Tag ausgew&auml;hlt'));
        exit;
    }
    $N_OBJ = $OTIMER_OBJ->loadAppointment($_GET['dateid']);
    $PROG = $OTIMER_OBJ->loadProg($N_OBJ['prog_id']);
    $KOBJ = $kdb->query_first("SELECT * FROM		" . TBL_CMS_CUST . " P 				WHERE P.kid=" . $N_OBJ['kid'] . " LIMIT 1");
    $smarty->assign('N_OBJ', $N_OBJ);
    $smarty->assign('KOBJ', $KOBJ);
    $smarty->assign('PROG', $PROG);
    $smarty->assign('dateid', $_GET['dateid']);
    $smarty->assign('subbtn', kf::gen_admin_sub_btn('{LA_SAVE}'));
    for ($i = 1; $i <= 100; $i++)
        $timespan .= '<option value="' . $i . '">' . $i . 'x</option>';
    $REPEAT = array('weekday' => $cal->getWeekdayAsString($N_OBJ['date']['d'], $N_OBJ['date']['m'], $N_OBJ['date']['Y']), 'timespan' => $timespan);
    $smarty->assign('REPEAT', $REPEAT);
    $ADMINOBJ->inc_tpl('otimer.repeat');
}



#*********************************
# REPEAT WORKTIME
#*********************************
if ($_GET['aktion'] == 'repeatworktime') {
    $DAY = $kdb->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYOPT . " WHERE id=" . $_GET['dayid']);
    $DAY['ger_date'] = $cal->date2DateGerman($DAY['day_date']);
    $DAYMID = $kdb->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYWORKTIME . " WHERE dt_mid=" . $_GET['mid'] . " AND dt_dayid=" . $_GET['dayid']);
    $DAYMID['dt_duration'] = $cal->timeDurationInHours($DAYMID['dt_from'], $DAYMID['dt_to']);
    $EMPL = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . $_GET['mid']);
    $smarty->assign('subbtn', kf::gen_admin_sub_btn('{LA_SAVE}'));
    for ($i = 1; $i <= 100; $i++)
        $timespan .= '<option value="' . $i . '">' . $i . 'x</option>';
    list($Yd, $md, $dd) = explode('-', $DAY['day_date']);
    $REPEAT = array('weekday' => $cal->getWeekdayAsString($dd, $md, $Yd), 'timespan' => $timespan);
    $smarty->assign('REPEAT', $REPEAT);
    $smarty->assign('DAYMID', $DAYMID);
    $smarty->assign('EMPLOYEE', $EMPL);
    $smarty->assign('DAY', $DAY);
    $ADMINOBJ->inc_tpl('otimer.mitrepeat');
}

unset($OTIMER_OBJ);
$OTIMER->parse_to_smarty();

?>