<?PHP

/**
 * @package    otimer
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */

class otimer_admin_class extends otimer_master_class {

    var $OTIMER = array();
    var $cal = null;

    /**
     * otimer_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

        # set date
        $_SESSION['seldate'] = ($_GET['seldate'] != "") ? $_GET['seldate'] : $_SESSION['seldate'];
        $_GET['dateid'] = ($_POST['dateid'] != "") ? $_POST['dateid'] : $_GET['dateid'];
        if (isset($_REQUEST['seldate'])) {
            $_GET['seldate'] = $_REQUEST['seldate'];
        }

        $_SESSION['seldate'] = (!isset($_SESSION['seldate']) || strlen($_SESSION['seldate']) < 10) ? date('Y-m-d') : $_SESSION['seldate'];
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $_SESSION['ONLINRE']['set_year'] = $Y;

        $this->cal = new tgcCalendar($d, $m, $Y);


        # uhrzeit table
        for ($h = 7; $h <= 22; $h++) {
            for ($i = 0; $i < 60; $i += 5) {
                $this->OTIMER['times'][] = (strlen($h) == 1 ? '0' . $h : $h) . ':' . (strlen($i) == 1 ? '0' . $i : $i);
            }
        }
        if (isset($_SESSION['otgroup_id']))
            $_SESSION['otgroup_id'] = intval($_SESSION['otgroup_id']);

        $this->progProEmployee($_SESSION['alang_id']);
        $this->clean();
    }

    /**
     * otimer_admin_class::clean()
     * 
     * @return void
     */
    function clean() {
        $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_DAYWORKTIME . " WHERE dt_from='00:00:00' and dt_to='00:00:00'");
    }

    /**
     * otimer_admin_class::cmd_setotdate()
     * 
     * @return void
     */
    function cmd_setotdate() {
        if ($_POST['seldateger'] != "") {
            $_GET['seldate'] = $_SESSION['seldate'] = format_date_to_sql_date($_POST['seldateger']);
            list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
            $_SESSION['ONLINRE']['set_year'] = $Y;
        }
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage']);
        exit;
    }

    /**
     * otimer_admin_class::cmd_a_daysave()
     * 
     * @return void
     */
    function cmd_a_daysave() {
        $FORM = $_POST['FORM'];
        $WORK = $_POST['WORK'];
        if (!isset($_POST['employees']))
            $_POST['employees'] = array();
        $FORM['day_closed'] = intval($FORM['day_closed']);
        if ($_POST['id'] > 0) {
            update_table(TBL_CMS_OTIMER_DAYOPT, 'id', $_POST['id'], $FORM);
        }
        else {
            $FORM['day_groupid'] = $_SESSION['otgroup_id'];
            $_POST['id'] = $id = insert_table(TBL_CMS_OTIMER_DAYOPT, $FORM);
        }

        foreach ($WORK as $mid => $time) {
            $time['dt_dayid'] = $_POST['id'];
            $employee = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . $mid);
            if ($employee['approval'] == 0) {
                $time['dt_from'] = $time['dt_to'] = '00:00:00';
            }
            uptins_table(TBL_CMS_OTIMER_DAYWORKTIME, 'id', $time['id'], $time);
        }
        $this->msg('{LBLA_SAVED}');
        #HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=dayoptions&seldate=' . $_POST['FORM']['day_date']);
        #exit;
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_ax_searchot()
     * 
     * @return void
     */
    function cmd_ax_searchot() {
        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUST . " K WHERE (K.email_notpublic LIKE '%" . $_POST['setvalue'] . "%' OR K.kid LIKE '%" . $_POST['setvalue'] .
            "%' OR K.nachname LIKE '%" . $_POST['setvalue'] . "%' OR K.knownof LIKE '%" . $_POST['setvalue'] . "%' OR K.firma LIKE '%" . $_POST['setvalue'] .
            "%' OR K.vorname LIKE '%" . $_POST['setvalue'] . "%' OR K.email LIKE '%" . $_POST['setvalue'] . "%' ) 
GROUP BY K.kid 
ORDER BY K." . $_POST['orderby'] . " " . $_POST['direc']);
        $ax_content .= '<table class="table table-striped table-hover"><thead><tr>
    <th></th>
    <th>Knr</th>
    <th>Anrede</th>
	<th>Nachname</th>
	<th>Vorname</th>
	<th>Email</th>
	<th>Firma</th>
	<th>PLZ</th>
	<th>Ort</th>	
	</tr></thead>';
        while ($row = $this->db->fetch_array_names($result)) {
            $ax_content .= '<tr>
		<td class="text-left">
        <a title="auswählen" class="btn btn-default" href="' . $_POST['ax_php'] . '?setkid=' . $row['kid'] . '&epage=' . $_POST['epage'] . '&id=' . $_POST['id'] .
                '&seldate=' . $_POST['seldate'] . '&aktion=' . $_POST['orgaktion'] . '&dateid=' . $_POST['dateid'] . '"><i class="fa fa-arrow-right"><!----></i></a>
                </td>
        <td><a href="kreg.php?aktion=show_edit&kid=' . $row['kid'] . '">' . $row['kid'] . '</a></td>
		<td>' . $row['anrede'] . '</td>
		<td><a href="kreg.php?aktion=show_edit&kid=' . $row['kid'] . '">' . $row['nachname'] . '</a></td>
		<td>' . $row['vorname'] . '</td>
		<td>' . $row['email'] . '</td>
		<td>' . $row['firma'] . '</td>
		<td>' . $row['plz'] . '</td>
		<td>' . $row['ort'] . '</td>
		
		</tr>';
        }
        ECHORESULT(pure_translation(kf::translate_admin($ax_content), $_SESSION['alang_id']) . '</table>');
    }


    /**
     * otimer_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->OTIMER['selecteddate'] = $_SESSION['seldate'];
        $this->smarty->assign('OTIMER', $this->OTIMER);
    }

    /**
     * otimer_admin_class::cmd_delete_appointment()
     * 
     * @return
     */
    function cmd_delete_appointment() {
        $appointment = $this->load_appointment($_GET['ident']);
        $this->LOGCLASS->addLog('DELETE', 'appointment deleted in ID:' . $_GET['ident'] . ' (' . $appointment['ndate'] . ',' . $appointment['prog_title'] . ',KNR.:' . $appointment['kid'] .
            ' ' . $appointment['nachname'] . ' ' . $appointment['vorname'] . ')');
        $this->db->query("DELETE FROM " . TBL_CMS_OTIMER . " WHERE id=" . intval($_GET['ident']) . " LIMIT 1");
        $this->msg('{LBLA_SAVED}');
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_load_cal_js_events()
     * 
     * @return
     */
    function cmd_load_cal_js_events() {
        $number_of_days_from_now = 365;
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $now = strtotime($Y . '-12-31');
        $i = 0;
        while ($i <> $number_of_days_from_now) {
            $str_stamp = "- $i day";
            $i++;
            $events[] = array(
                'id' => gen_sid('9'),
                'title' => 'Tag auswählen',
                'start' => date('Y-m-d', strtotime($str_stamp, $now)),
                'end' => date('Y-m-d', strtotime($str_stamp, $now)),
                'backgroundColor' => '#A1C517',
                'borderColor' => '#98BD03',
                'url' => $_SERVER['PHP_SELF'] . "?epage=otimer.inc&seldate=" . date('Y-m-d', strtotime($str_stamp, $now)));
        }


        $result = $this->db->query("SELECT NL.id AS NLID,K.*,NL.*,NG.*,NG.id AS NGID
	FROM " . TBL_CMS_OTIMER . " NL
	INNER JOIN " . TBL_CMS_OTIMER_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['otgroup_id'] . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE ndate>='" . $Y . "-01-01' AND ndate<='" . $Y . "-12-31'
	GROUP BY NL.id ORDER BY ndate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $events[] = array(
                'id' => $row['NLID'],
                'title' => date('H:i', strtotime($row['time_from'])) . '-' . date('H:i', strtotime($row['time_to'])) . ' ' . $row['prog_title'],
                'start' => $row['ndate'],
                'end' => $row['ndate'],
                'url' => $_SERVER['PHP_SELF'] . "?epage=otimer.inc&dateid=" . $row['NLID'] . "&cmd=editdate&seldate=" . $row['ndate']);
        }


        echo json_encode((array )$events);
        $this->hard_exit();
    }

    /**
     * otimer_admin_class::cmd_approve_appointment()
     * 
     * @return
     */
    function cmd_approve_appointment() {
        $this->db->query("UPDATE " . TBL_CMS_OTIMER . " SET approval=" . $_GET['value'] . "  WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_save_pro_table()
     * 
     * @return
     */
    function cmd_save_pro_table() {
        foreach ((array )$_POST['FORM'] as $id => $row) {
            update_table(TBL_CMS_OTIMER_PROG, 'id', $id, $row);
        }
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_approve_program()
     * 
     * @return
     */
    function cmd_approve_program() {
        $this->db->query("UPDATE " . TBL_CMS_OTIMER_PROG . " SET pr_approval=" . $_GET['value'] . "  WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_del_program()
     * 
     * @return
     */
    function cmd_del_program() {
        $this->LOGCLASS->addLog('DELETE', 'programm deleted:' . $_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_PROG . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_add_program()
     * 
     * @return
     */
    function cmd_add_program() {
        $id = insert_table(TBL_CMS_OTIMER_PROG, $_POST['FORM']);
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=otprograms_edit&id=' . $id);
        $this->hard_exit();
    }

    /**
     * otimer_admin_class::cmd_otprograms()
     * 
     * @return
     */
    function cmd_otprograms() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_OTIMER_PROG . " WHERE 1  
        " . ((isset($_GET['mid'])) ? " AND (pr_employees LIKE '%" . $_GET['mid'] . ";%' OR pr_employees LIKE '%;" . $_GET['mid'] . "')" : "") . "
        ORDER BY pr_admintitle");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['pr_approval'], 'approve_program');
            $row['icons'][] = kf::gen_edit_icon($row['id'], '', 'otprograms_edit');
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_program');
            $this->OTIMER['programs'][] = $row;
        }
    }

    /**
     * otimer_admin_class::cmd_otgroups()
     * 
     * @return
     */
    function cmd_otgroups() {
        $result = $this->db->query("SELECT *	FROM " . TBL_CMS_OTIMER_GROUPS . "	ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            #  $row['icons'][] = kf::gen_approve_icon($row['id'], $row['pr_approval'], 'approve_program');
            $row['icons'][] = kf::gen_edit_icon($row['id'], '', 'dbo_edit');
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_group');
            $this->OTIMER['otgroups'][] = $row;
        }
    }


    /**
     * otimer_admin_class::cmd_del_group()
     * 
     * @return
     */
    function cmd_del_group() {
        if (get_data_count(TBL_CMS_OTIMER, 'id', "group_id=" . $_GET['ident']) == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_GROUPS . " WHERE id>1 AND id=" . $_GET['ident']);
            $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_PERM . " WHERE perm_did=" . $_GET['ident']);
            $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_GCON . " WHERE g_id=" . $_GET['ident']);
            $this->msg('{LBL_DELETED}');
            $this->ej();
        }
        else {
            $this->msge('{LBLA_NOT_DELETED} {LBL_HASSUBCONTENT}');
            $this->ej();
        }

    }

    /**
     * otimer_admin_class::cmd_save_group_table()
     * 
     * @return
     */
    function cmd_save_group_table() {
        foreach ((array )$_POST['FORM'] as $id => $row) {
            update_table(TBL_CMS_OTIMER_GROUPS, 'id', $id, $row);
        }
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_add_otgroup()
     * 
     * @return
     */
    function cmd_add_otgroup() {
        $id = insert_table(TBL_CMS_OTIMER_GROUPS, $_POST['FORM']);
        $this->db->query("INSERT INTO " . TBL_CMS_OTIMER_PERM . " SET perm_did=" . $id . ", perm_group_id=1000");
        $this->msg('{LBLA_SAVED}');
        $this->TCR->tb();
    }

    /**
     * otimer_admin_class::cmd_setallperm()
     * 
     * @return
     */
    function cmd_setallperm() {
        # Permissions setzen
        $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_PERM . " WHERE perm_did=" . $_POST['tid']);
        if (is_array($_POST['CUSTGROUP'])) {
            foreach ($_POST['CUSTGROUP'] as $key => $group_id) {
                $this->db->query("INSERT INTO " . TBL_CMS_OTIMER_PERM . " SET perm_did=" . $_POST['tid'] . ", perm_group_id=" . $group_id);
            }
        }

        foreach ($_POST['FORM_LANG'] as $bd_ident => $values) {
            $LANGT = array();
            $LANGT['g_id'] = $_POST['tid'];
            foreach ($values as $td => $value) {
                $LANGT[$td] = $value;
            }
            uptins_table(TBL_CMS_OTIMER_GCON, 'id', $bd_ident, $LANGT);
        }
        $this->msg('{LBLA_SAVED}');
        $this->TCR->tb();
    }

    /**
     * otimer_admin_class::load_black_list()
     * 
     * @return
     */
    function load_black_list() {
        $black_list = array();
        $result = $this->db->query("SELECT *,OT.id AS DATEID FROM " . TBL_CMS_OTIMER . " OT
            LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=OT.kid)
            WHERE block_kid=1");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icon_block'] = $this->genBlockImgTag($row['DATEID'], $row['block_kid']);
            $row['icon_blockc'] = $this->genBlockImgTag($row['kid'], $row['block_kid'], 'setblockcust');
            $row['dateger'] = my_date('d.m.Y', $row['ndate']);
            $black_list[] = $row;
        }
        return $black_list;
    }

    /**
     * otimer_admin_class::cmd_blacklist()
     * 
     * @return
     */
    function cmd_blacklist() {
        $this->smarty->assign('blacklist', $this->load_black_list());
    }


    /**
     * otimer_admin_class::cmd_worktime()
     * 
     * @return
     */
    function cmd_worktime() {
        $this->smarty->assign('wt_table', $this->loadWorkingTimeTable('%' . $_SESSION['seldate'] . '%'));
        $this->smarty->assign('seldate', my_date('d.m.Y', $_SESSION['seldate']));
    }

    /**
     * otimer_admin_class::cmd_delwt()
     * 
     * @return
     */
    function cmd_delwt() {
        $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_DAYWORKTIME . " WHERE id=" . $_GET['ident']);
        # dt_mid=" . intval($_GET['mid']) . " AND dt_dayid=" . intval($_GET['ident']));
        $this->ej();
    }

    /**
     * otimer_admin_class::cmd_dayoptions()
     * 
     * @return void
     */
    function cmd_dayoptions() {
        if ($_GET['seldate'] == '') {
            $this->msge('Kein Datum ausgew&auml;hlt');
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=otimer.inc');
            exit;
        }

        $DAY = $this->db->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYOPT . " WHERE day_date='" . $_GET['seldate'] . "' AND day_groupid=" . $_SESSION['otgroup_id']);
        $DAY['employees'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id<>100 ORDER BY approval DESC,mitarbeiter_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['dt_from'] = '00:00:00';
            $row['dt_to'] = '00:00:00';
            $row['dt_dayid'] = $DAY['id'];
            $row['dt_duration'] = printMenge($this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']));
            $row['programlist'] = $this->getProgramsFromEmployee($row['id']);
            $row['programs'] = safe_implode($this->progProEmployTextList[$row['id']], ', ');
            $row['programidlist'] = $this->progProEmployIdList[$row['id']];
            $DAY['employees'][$row['id']] = $row;
            $emp_ids[] = $row['id'];
        }

        $result = $this->db->query("SELECT * FROM " . TBL_CMS_OTIMER_DAYWORKTIME . " WHERE dt_dayid=" . intval($DAY['id']));
        while ($row = $this->db->fetch_array_names($result)) {
            if (!in_array($row['dt_mid'], $emp_ids))
                $DAY['employees'][$row['id']]['mitarbeiter_name'] = 'UNBEKANNT';
            $DAY['employees'][$row['dt_mid']]['dt_from'] = $row['dt_from'];
            $DAY['employees'][$row['dt_mid']]['dt_to'] = $row['dt_to'];
            $DAY['employees'][$row['dt_mid']]['wid'] = $row['id'];
            $DAY['employees'][$row['dt_mid']]['dt_duration'] = printMenge($this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']));
        }

        #  $ADMINOBJ->inc_tpl('otimer.dayopt');
        $this->smarty->assign('DAY', $DAY);
        #  $this->smarty->assign('subbtn', kf::gen_admin_sub_btn('{LA_SAVE}'));
        $this->smarty->assign('seldate_us', $_GET['seldate']);
        $this->smarty->assign('seldate_ger', $this->cal->date2DateGerman($_GET['seldate']));
    }

    /**
     * otimer_admin_class::loadWorkingTimeTable()
     * 
     * @param string $date
     * @return
     */
    function loadWorkingTimeTable($date = '') {
        if ($date == "") {
            $date = date('Y') . "-%";
        }
        $result = $this->db->query("SELECT *,M.id AS MID,D.id AS DAYID,DW.id AS WTID FROM " . TBL_CMS_OTIMER_DAYOPT . " D, " . TBL_CMS_OTIMER_DAYWORKTIME . " DW
			LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=DW.dt_mid)
			WHERE D.id=DW.dt_dayid AND D.day_date LIKE '" . $date . "'
			ORDER BY D.day_date DESC, DW.dt_from ASC
			");
        while ($row = $this->db->fetch_array_names($result)) {
            $EMP[$row['WTID']] = $row;
            $EMP[$row['WTID']]['dt_from'] = $row['dt_from'];
            $EMP[$row['WTID']]['dt_fromtime'] = $this->cal->convertDateTime2Array($row['day_date'] . ' ' . $row['dt_from']);
            $EMP[$row['WTID']]['dt_today'] = $this->cal->convertDateTime2Array(date('Y-m-d 00:00:00'));
            $EMP[$row['WTID']]['dt_to'] = $row['dt_to'];
            $EMP[$row['WTID']]['wid'] = $row['id'];
            $EMP[$row['WTID']]['dt_duration'] = printMenge($this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']));
            $EMP[$row['WTID']]['duration'] = $this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']);
            $EMP[$row['WTID']]['programlist'] = $this->getProgramsFromEmployee($row['MID']);
            $EMP[$row['WTID']]['programs'] = safe_implode($this->progProEmployTextList[$row['MID']], ', '); #implode(', ',$this->progProEmployTextList[$row['MID']]);
            $EMP[$row['WTID']]['programidlist'] = $this->progProEmployIdList[$row['MID']];
            $EMP[$row['WTID']]['icon_del'] = kf::gen_del_icon($row['WTID'], true, 'delwt');
            #$this->gen_del_icon_reload($row['WTID'], 'delwt', '{LBL_CONFIRM}', '&mid=' . $row['MID'] . '&dayid=' . $row['DAYID']);
        }
        return $EMP;
    }

    /**
     * otimer_admin_class::cmd_worktimem()
     * 
     * @return
     */
    function cmd_worktimem() {
        $this->smarty->assign('wt_table', $this->loadWorkingTimeTable('%' . my_date('Y-m-', $_SESSION['seldate']) . '%'));
        $this->smarty->assign('seldate', my_date('d.m.Y', $_SESSION['seldate']));
    }

    /**
     * otimer_admin_class::cmd_overview()
     * 
     * @return
     */
    function cmd_overview() {
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $DATE_ARR = array();
        $DATE_ARR = $this->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgroup_id'], false, 'tmonth');
        $this->smarty->assign('mdates', $DATE_ARR['year']);
        $this->smarty->assign('mdates_month', $DATE_ARR['month']);
        $this->smarty->assign('mdates_day', $DATE_ARR['day']);
        $this->smarty->assign('clock_table', $DATE_ARR['clock_table']);
        $this->smarty->assign('inlay', $inlay);
        $this->smarty->assign('cal_month', $m);
        $this->smarty->assign('cal_year', $Y);
        $this->smarty->assign('cal_day', $d);
        unset($DATE_ARR);
    }

    /**
     * otimer_admin_class::cmd_calcendtime()
     * 
     * @return void
     */
    function cmd_calcendtime() {
        $feedback_optimal_time = $feedback_work = $feedback = "";
        # list($Y, $m, $d) = explode('-', date('Y-m-d'));
        #  $cal = new tgcCalendar($d, $m, $Y);
        #  $OTIMER = new otimer_class($cal, $_SESSION['alang_id']);
        $endtime_ger = self::calcendtime($_POST['setvalue'], $_POST['setvalue2'], $_POST['duration'], $_POST['seldate']);
        $endtime_us = self::calcendtime($_POST['setvalue'], $_POST['setvalue2'], $_POST['duration'], $_POST['seldate'], TRUE);
        list($Y, $m, $d) = explode('-', $_POST['seldate']);
        list($datum_ger, $zeit_ger) = explode(' ', $endtime_ger);
        $DATE_ARR = $this->buildDateArr_MonthDay($Y, $m, $d, $_SESSION['otgroup_id'], false, 'tmonth');
        $appointment = array();
        $appointment = $this->genTimeSpanObj($_POST['seldate'] . ' ' . $_POST['setvalue'] . ':' . $_POST['setvalue2'] . ':00', $endtime_us, 'DATE');
        $dbl_use = $this->doubleUsed($appointment, intval($_POST['dateid']));
        $feedback = $endtime_ger . (($dbl_use === TRUE) ? '<br><p class="alert alert-danger">Es liegt eine Termin&uuml;berschneidung vor.</p>' : '');
        $employee_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . intval($_POST['setvalue3']));
        $_SESSION['OTIMERLOG']['last_emp_id'] = (int)$_POST['setvalue3'];
        $feedback_work = (($this->inEmployeeWorkingTime($_POST['seldate'], $_POST['groupid'], $_POST['setvalue3'], $appointment) === FALSE) ?
            '<br><p class="alert alert-danger">Ihre gew&auml;hlte Zeit von ' . $_POST['setvalue'] . ':' . $_POST['setvalue2'] . ' bis ' . $zeit_ger . ' Uhr 
  	 deckt sich nicht mit den Arbeitszeiten des Mitarbeiters "' . $employee_obj['mitarbeiter_name'] . '".</p>' : '');
        if ($dbl_use === TRUE) {
            $time_arr = $this->nextOptimalStartTime($appointment);
            if (is_array($time_arr)) {
                $feedback_optimal_time = '<br><p class="alert alert-success">Zeitvorschlag: ' . $time_arr['timefrom']['time']['formatedtime'] . ' Uhr</p>';
            }
        }
        ECHORESULT($feedback . $feedback_work . $feedback_optimal_time);
    }

    /**
     * otimer_admin_class::cmd_repeatmit()
     * 
     * @return void
     */
    function cmd_repeatmit() {
        $DAY = $this->db->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYOPT . " WHERE id=" . $_POST['dayid']);
        $DAYMID = $this->db->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYWORKTIME . " WHERE dt_mid=" . $_POST['mid'] . " AND dt_dayid=" . $_POST['dayid']);
        $savedItems = $already_working = 0;
        $days = array();
        for ($i = 1; $i <= intval($_POST['timespan']); $i++) {
            $NEXT_D_OBJ = $DAY;
            $NEXT_D_OBJ['day_date'] = $this->cal->addTime2Date($DAY['day_date'], 0, 0, ($_POST['repdays'] * $i), 'Y-m-d');
            unset($NEXT_D_OBJ['id']);
            foreach ($NEXT_D_OBJ as $key => $wert)
                $NEXT_D_OBJ[$key] = $this->db->real_escape_string($NEXT_D_OBJ[$key]);
            $DAYCHECK = $this->db->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYOPT . " WHERE day_date='" . $NEXT_D_OBJ['day_date'] . "'");
            if ($DAYCHECK['id'] > 0) {
                $next_day_id = $DAYCHECK['id'];
            }
            else
                $next_day_id = insert_table(TBL_CMS_OTIMER_DAYOPT, $NEXT_D_OBJ);
            $NEXT_DM_OBJ = $DAYMID;
            $NEXT_DM_OBJ['dt_dayid'] = $next_day_id;
            unset($NEXT_DM_OBJ['id']);
            $NEXT_DM_OBJ = $this->real_escape($NEXT_DM_OBJ);
            if (get_data_count(TBL_CMS_OTIMER_DAYWORKTIME, 'id', "dt_from<>'00:00:00' AND dt_mid=" . $_POST['mid'] . " AND dt_dayid=" . $next_day_id) == 0) {
                insert_table(TBL_CMS_OTIMER_DAYWORKTIME, $NEXT_DM_OBJ);
                $savedItems++;
            }
            else {
                $already_working++;
                $days[] = my_date('d.m.Y', $NEXT_D_OBJ['day_date']);
            }
        }
        $this->msg($savedItems . ' neue Arbeitszeiten wurden gespeichert.');
        if ($already_working > 0) {
            $this->msge('An ' . $already_working . ' Tagen  (' . implode(',', $days) . ') arbeitet dieser Mitarbeiter bereits.');
        }
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }

    /* function build_date_table_admin($result, $Y) {
    return;
    if ($this->db->num_rows($result) == 0)
    return;
    $this->db->data_seek($result, 0);
    while ($row = $this->db->fetch_array_names($result)) {
    $used_days[] = $row['ndate'];
    $date_arr[$row['ndate']]['dobjects'][] = $row;
    }

    if (is_array($date_arr)) {
    foreach ($date_arr as $date => $d_obj_arr) {
    $info = "";
    if (is_array($d_obj_arr['dobjects'])) {
    foreach ($d_obj_arr['dobjects'] as $d_obj) {
    $info .= '<span class=\\\'' . (($d_obj['approval'] == 1) ? 'approved' : 'notapproved') . '\\\'>-' . $d_obj['prog_title'] . '</span><br>';
    }
    }
    $date_arr[$date]['info'] = $info;
    }
    }

    $pn = array('&laquo;' => $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&seldate=' . $Y . '-' . $this->cal->getPrevMonth() . '-' . $d, '&raquo;' => $_SERVER['PHP_SELF'] .
    '?epage=' . $_GET['epage'] . '&seldate=' . $Y . '-' . $this->cal->getNextMonth() . '-' . $d);

    #  $this->smarty->assign('cal_month',generate_calendar($Y, $m, $days, 3, NULL, 1, $pn));

    for ($month = 1; $month <= 12; $month++) {
    $month = (strlen($month) == 1) ? '0' . $month : $month;
    $tage = date("t", mktime(0, 0, 0, $month, 1, $Y));
    $days = array();
    for ($tag = 1; $tag <= $tage; $tag++) {
    $tag = (strlen($tag) == 1) ? '0' . $tag : $tag;
    if (in_array($Y . '-' . $month . '-' . $tag, $used_days)) {
    $onm = "onmouseover=\"showtrail('','{LBL_TERMINE} " . my_date('d.m.Y', $Y . '-' . $month . '-' . $tag) . "','" . $date_arr[$Y . '-' . $month . '-' . $tag]['info'] .
    "','0.0000','0','0',200);\" onmouseout=\"hidetrail();\"";
    }
    else
    $onm = "";

    if ($this->cal->date_to_time($Y . '-' . $month . '-' . $tag) == $this->cal->date_to_time(date('Y-m-d'))) {
    $onm = "onmouseover=\"showtrail('','{LBL_TERMINE} {LBL_HEUTE}','" . $date_arr[$Y . '-' . $month . '-' . $tag]['info'] . "','0.0000','0','0',200);\" onmouseout=\"hidetrail();\"";
    $days[intval($tag)] = array(
    $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&seldate=' . $Y . '-' . $month . '-' . $tag,
    'today',
    $tag,
    $onm);
    }
    else {
    if (in_array($Y . '-' . $month . '-' . $tag, $used_days)) {
    $days[intval($tag)] = array(
    $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&seldate=' . $Y . '-' . $month . '-' . $tag,
    'full_day',
    $tag,
    $onm);
    }
    else
    $days[intval($tag)] = array(
    $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&seldate=' . $Y . '-' . $month . '-' . $tag,
    'empty_day',
    $tag);
    }
    }
    #echo '<pre>';print_r($days);
    #    $year_tabs[$month]['table'] .= generate_calendar($Y, $month, $days, 3, NULL, 1, array(), (ISADMIN == 1));
    }
    $this->smarty->assign('year_tabs', $year_tabs);
    unset($year_tabs);
    }
    */

}

?>