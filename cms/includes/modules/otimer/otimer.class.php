<?php

/**
 * @package    otimer
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


DEFINE('TBL_CMS_OTIMER', TBL_CMS_PREFIX . 'ot_dates');
DEFINE('TBL_CMS_OTIMER_GROUPS', TBL_CMS_PREFIX . 'ot_groups');
DEFINE('TBL_CMS_OTIMER_GCON', TBL_CMS_PREFIX . 'ot_gcontent');
DEFINE('TBL_CMS_OTIMER_PERM', TBL_CMS_PREFIX . 'ot_perm');
DEFINE('TBL_CMS_OTIMER_PROG', TBL_CMS_PREFIX . 'ot_programs');
DEFINE('TBL_CMS_OTIMER_PROG_LANG', TBL_CMS_PREFIX . 'ot_program_lang');
DEFINE('TBL_CMS_OTIMER_PROGTODATE', TBL_CMS_PREFIX . 'ot_progtodate');
DEFINE('TBL_CMS_OTIMER_DAYOPT', TBL_CMS_PREFIX . 'ot_dayoptions');
DEFINE('TBL_CMS_OTIMER_DAYWORKTIME', TBL_CMS_PREFIX . 'ot_dayworktime');

class otimer_class extends otimer_master_class {

    var $cal = NULL;


    var $DAY = NULL;
    var $prog_id = 0;
    var $PROG_OBJ = array();
    var $langid = 1;

    /**
     * otimer_class::otimer_class()
     * 
     * @param mixed $cal
     * @param integer $langid
     * @return
     */
    function otimer_class($cal = NULL, $langid = 1) {
        parent::__construct();
        $this->cal = $cal;
        $this->langid = $langid;
        $this->progProEmployee($langid);
    }

    /**
     * otimer_class::init()
     * 
     * @param mixed $cal
     * @param mixed $langid
     * @return
     */
    function init($cal, $langid) {
        $this->cal = $cal;
        $this->langid = $langid;
    }


    /**
     * otimer_class::buildProgrammSelectJavaAdmin()
     * 
     * @param mixed $PROG
     * @return
     */
    function buildProgrammSelectJavaAdmin($PROG) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_OTIMER_PROG . " WHERE pr_approval=1	ORDER BY pr_admintitle");
        while ($row = $this->db->fetch_array_names($result)) {
            $ret .= "<option " . (($PROG['PROGID'] == $row['id']) ? 'selected' : '') . " value=\"http://www." . FM_DOMAIN . str_replace("//", "/", PATH_CMS . $_SERVER['PHP_SELF']) .
                "?" . (($_REQUEST['setkid'] > 0) ? 'setkid=' . $_REQUEST['setkid'] . '&' : '') . "epage=" . $_GET['epage'] . "&aktion=" . $_GET['aktion'] . "&seldate=" . $_GET['seldate'] .
                "&dateid=" . $_GET['dateid'] . "&id=" . $row['id'] . "\">" . $row['pr_admintitle'] . " - " . ($row['pr_duration'] * 60) . "min</option>";
        }
        return $ret;
    }

    /**
     * otimer_class::buildProgrammSelectJavaFE()
     * 
     * @param mixed $PROG
     * @return
     */
    function buildProgrammSelectJavaFE($PROG) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_OTIMER_PROG . " WHERE pr_approval=1	ORDER BY pr_admintitle");
        while ($row = $this->db->fetch_array_names($result)) {
            $ret .= "<option " . (($PROG['PROGID'] == $row['id']) ? 'selected' : '') . " value=\"http://www." . FM_DOMAIN . PATH_CMS . "index.php?page=" . $_GET['page'] .
                "&aktion=" . $_GET['aktion'] . "&seldate=" . $_GET['seldate'] . "&id=" . $row['PROGID'] . "\">" . $row['pr_admintitle'] . " - " . ($row['pr_duration'] * 60) .
                "min</option>";
        }
        return $ret;
    }

    /**
     * otimer_class::buildProgrammSelectJavaAllowed()
     * 
     * @param mixed $PROG
     * @return
     */
    function buildProgrammSelectJavaAllowed($PROG) {
        $possible_progids = array();
        foreach ($this->DAY['employees'] as $mid => $arr) {
            if (is_array($arr['programidlist'])) {
                foreach ($arr['programidlist'] as $prog_id) {
                    $possible_progids[$prog_id] = $prog_id;
                }
            }
        }
        $result = $this->db->query("SELECT *,P.id AS PROGID FROM
	" . TBL_CMS_OTIMER_PROG . " P
	LEFT JOIN " . TBL_CMS_OTIMER_PROG_LANG . " PL ON (PL.pr_prog_id=P.id AND PL.lang_id=" . intval($this->langid) . ")
	WHERE P.pr_approval=1
	GROUP BY P.id
	ORDER BY P.pr_admintitle");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['pr_title'] = ($row['pr_title'] == "") ? $row['pr_admintitle'] : $row['pr_title'];
            if (in_array($row['PROGID'], $possible_progids))
                $ret .= "<option " . (($PROG['PROGID'] == $row['PROGID']) ? 'selected' : '') . " value=\"http://www." . FM_DOMAIN . str_replace("//", "/", PATH_CMS) .
                    "index.php?page=" . $_GET['page'] . "&aktion=" . $_GET['aktion'] . "&seldate=" . $_GET['seldate'] . "&id=" . $row['PROGID'] . "\">" . $row['pr_title'] . " - " . ($row['pr_duration'] *
                    60) . "min</option>";
        }
        return $ret;
    }


    /**
     * otimer_class::loadProg()
     * 
     * @param integer $id
     * @return
     */
    function loadProg($id = 0) {
        $id = intval($id);
        if ($id > 0) {
            $this->PROG_OBJ = $this->db->query_first("SELECT *,P.id AS PROGID FROM
		" . TBL_CMS_OTIMER_PROG . " P
		LEFT JOIN " . TBL_CMS_OTIMER_PROG_LANG . " PL ON (PL.pr_prog_id=P.id AND PL.lang_id=" . intval($this->langid) . ")
		WHERE P.id=" . $id . " GROUP BY P.id");
        }
        else {
            $this->PROG_OBJ = $this->db->query_first("SELECT *,P.id AS PROGID FROM
			" . TBL_CMS_OTIMER_PROG . " P
			LEFT JOIN " . TBL_CMS_OTIMER_PROG_LANG . " PL ON (PL.pr_prog_id=P.id AND PL.lang_id=" . intval($this->langid) . ")
			WHERE P.pr_approval=1
			ORDER BY P.pr_admintitle LIMIT 1");
        }
        $this->PROG_OBJ['pr_title'] = ($this->PROG_OBJ['pr_title'] == '') ? $this->PROG_OBJ['pr_admintitle'] : $this->PROG_OBJ['pr_title'];
        $this->PROG_OBJ['pr_duration_min'] = $this->PROG_OBJ['pr_duration'] * 60;
        $this->PROG_OBJ['employeeids'] = explode(';', $this->PROG_OBJ['pr_employees']);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id<>100 ORDER BY mitarbeiter_name");
        while ($row = $this->db->fetch_array_names($result)) {
            if (in_array($row['id'], $this->PROG_OBJ['employeeids']))
                $this->PROG_OBJ['pref_employees'][] = $row;
            $row['programlist'] = $this->getProgramsFromEmployee($row['id']);
            $row['programs'] = safe_implode($this->progProEmployTextList[$row['id']], ',');
            $this->PROG_OBJ['employees'][] = $row;
        }
        $this->prog_id = $this->PROG_OBJ['PROGID'];
        return $this->PROG_OBJ;
    }


    /**
     * otimer_class::setOTDateObj()
     * 
     * @param mixed $datetime
     * @param mixed $groupid
     * @param bool $load_year_table
     * @return
     */
    function setOTDateObj($datetime, $groupid, $load_year_table = false) { //YYYY-MM-DD
        $OTDATE_OBJ = array();
        $OTDATE_OBJ['date'] = $this->cal->convertDateTime2Array($datetime);
        list($date, $time) = explode(' ', $datetime);
        list($OTDATE_OBJ['hours_day']['selhour'], $OTDATE_OBJ['hours_day']['selmin'], $s) = explode(':', $time);
        if ($OTDATE_OBJ['hours_day']['selhour'] == 0)
            $OTDATE_OBJ['hours_day']['selhour'] = date('H');
        // Oeffnungszeiten
        $zeit_open = explode(':', $this->gbl_config['ot_storeopen']);
        $zeit_close = explode(':', $this->gbl_config['ot_storeclose']);
        for ($i = $zeit_open[0]; $i <= $zeit_close[0]; $i++) {
            $OTDATE_OBJ['hours_overview'][] = ((strlen($i) > 1) ? $i : '0' . $i);
            $OTDATE_OBJ['hours_day']['hours'][] = ((strlen($i) > 1) ? $i : '0' . $i);
        }
        for ($i = 0; $i < 60; $i += $this->gbl_config['ot_interval_min'])
            $OTDATE_OBJ['min_day'][] = ((strlen($i) > 1) ? $i : '0' . $i);
        $OTDATE_OBJ['DAY'] = $this->loadDayOpt($date, $groupid, $load_year_table);
        return $OTDATE_OBJ;
    }


    /**
     * otimer_class::load_year_employee_workingtime()
     * 
     * @param mixed $seldate
     * @param mixed $groupid
     * @return
     */
    function load_year_employee_workingtime($seldate, $groupid) {
        $this->DAY['employees_year'] = array();
        list($Y, $m, $m) = explode("-", $seldate);
        $sql = "SELECT *,M.id AS MID FROM " . TBL_CMS_OTIMER_DAYOPT . " DO, " . TBL_CMS_OTIMER_DAYWORKTIME . "  DW 
			INNER JOIN " . TBL_CMS_ADMINS . " M ON (M.id=DW.dt_mid)
			WHERE DO.day_date>='" . $Y . "-01-01' AND DO.day_date<='" . $Y . "-12-31' 
			AND DO.day_groupid=" . $groupid . " AND DW.dt_dayid=DO.id
			ORDER BY dt_from
			";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->DAY['employees_year'][$row['dt_mid']] = $row;
            $this->DAY['employees_year'][$row['dt_mid']]['dt_from'] = $row['dt_from'];
            $this->DAY['employees_year'][$row['dt_mid']]['dt_fromtime'] = $this->cal->convertDateTime2Array($seldate . ' ' . $row['dt_from']);
            $this->DAY['employees_year'][$row['dt_mid']]['dt_totime'] = $this->cal->convertDateTime2Array($seldate . ' ' . $row['dt_to']);
            $this->DAY['employees_year'][$row['dt_mid']]['dt_today'] = $this->cal->convertDateTime2Array(date('Y-m-d 00:00:00'));
            $this->DAY['employees_year'][$row['dt_mid']]['dt_to'] = $row['dt_to'];
            $this->DAY['employees_year'][$row['dt_mid']]['wid'] = $row['id'];
            $this->DAY['employees_year'][$row['dt_mid']]['dt_duration'] = $this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']);
            $this->DAY['employees_year'][$row['dt_mid']]['dt_durationstr'] = printMenge($this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']));
            $this->DAY['employees_year'][$row['dt_mid']]['programlist'] = $this->getProgramsFromEmployee($row['MID']);
            $this->DAY['employees_year'][$row['dt_mid']]['programs'] = safe_implode($this->progProEmployTextList[$row['MID']], ', '); #implode(', ',$this->progProEmployTextList[$row['MID']]);
            $this->DAY['employees_year'][$row['dt_mid']]['programidlist'] = $this->progProEmployIdList[$row['MID']];
        }
    }

    /**
     * otimer_class::loadAppointment()
     * 
     * @param mixed $id
     * @return
     */
    function loadAppointment($id) {
        $id = intval($id);
        $N_OBJ = $this->db->query_first("SELECT *,NL.id AS DATEID FROM
	" . TBL_CMS_OTIMER . " NL
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.id=" . $id . "
	GROUP BY NL.id
	");
        $N_OBJ['starttime'] = $this->cal->convertDateTime2Array($N_OBJ['time_from']);
        $N_OBJ['endtime'] = $this->cal->convertDateTime2Array($N_OBJ['time_to']);
        $N_OBJ['span_type'] = 'DATE';
        if ($N_OBJ['prog_id'] > 0) {
            $N_OBJ['program_obj'] = $this->loadProg($N_OBJ['prog_id']);
        }
        $N_OBJ['DAY'] = $this->loadDayOpt($N_OBJ['ndate'], $N_OBJ['group_id']);
        return $N_OBJ;
    }


    /**
     * otimer_class::nextOptimalStartTime()
     * 
     * @param mixed $appointment
     * @return
     */
    function nextOptimalStartTime($appointment) {
        $possible_free_time_spans_BEFORE = $possible_free_time_spans_AFTER = array();
        $open_time = $this->cal->convertDateTime2Array($appointment['timefrom']['date']['date'] . ' ' . $this->gbl_config['ot_storeopen']);
        $close_time = $this->cal->convertDateTime2Array($appointment['timefrom']['date']['date'] . ' ' . $this->gbl_config['ot_storeclose']);
        if (is_array($this->TCLOCK_TBL)) {
            foreach ($this->TCLOCK_TBL as $hour_index => $OB) {
                if (is_array($OB['dates'])) {
                    foreach ($OB['dates'] as $start_timeint => $otdate) {
                        // Nimm nur freie Zeiten, die in den Oeffnungszeiten liegen
                        if ($otdate['span_type'] == 'FREE' && $otdate['timefrom']['timeint'] >= $open_time['timeint'] && $otdate['timeto']['timeint'] <= $close_time['timeint']) {
                            // moeglicher freier Bereich
                            if ($otdate['duration_min'] >= $appointment['duration_min']) {
                                // freie Zeit vor oder nach Appointment
                                if ($otdate['timefrom']['timeint'] < $appointment['timefrom']['timeint']) {
                                    $possible_free_time_spans_BEFORE[$otdate['timefrom']['timeint']] = $otdate;
                                }
                                else {
                                    $possible_free_time_spans_AFTER[$otdate['timefrom']['timeint']] = $otdate;
                                }
                            }
                        }
                    }
                }
            }
        }
        krsort($possible_free_time_spans_BEFORE);
        foreach ($possible_free_time_spans_BEFORE as $timefrom_int => $otdate) {
            if ($timefrom_int < $appointment['timefrom']['timeint']) {
                return $otdate;
            }
        }
        ksort($possible_free_time_spans_AFTER);
        foreach ($possible_free_time_spans_AFTER as $timefrom_int => $otdate) {
            if ($timefrom_int > $appointment['timefrom']['timeint']) {
                return $otdate;
            }
        }
    }


    #*********************************
    # OTIMER THEME LIST
    #*********************************
    /**
     * otimer_class::genThemeMenu()
     * 
     * @param mixed $theme_id
     * @return
     */
    function genThemeMenu($theme_id) {
        $k = 0;
        $result = $this->db->query("SELECT * FROM
	" . TBL_CMS_OTIMER_GROUPS . " T
	ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            if (intval($theme_id) == 0 && $k == 0)
                $theme_id = $row['id'];
            $k++;
            $themes[] = array(
                'theme' => (($row['g_title'] != "") ? $row['g_title'] : $row['groupname']),
                'class' => (($row['id'] == $theme_id) ? ' class="selected"' : ''),
                'link' => 'http://www.' . FM_DOMAIN . PATH_CMS . 'index.php?aktion=' . $_GET['aktion'] . '&page=' . $_GET['page'] . '&otgid=' . $row['id']);
        }
        return array(
            'themes' => $themes,
            'first_theme_id' => $theme_id,
            'count',
            count($themes));
    }


    /**
     * otimer_class::genCalTableFrontend()
     * 
     * @param mixed $theme_id
     * @param mixed $seldate
     * @return
     */
    function genCalTableFrontend($theme_id, $seldate) {
        $this->smarty->assign('seldate_is_future', ($seldate > date('Y-m-d')));
        list($Y, $m, $d) = explode('-', $seldate);
        $time_now = $this->cal->date_to_time($seldate);
        $time_past = $time_now - (60 * 60 * 24 * 30 * 3); // 3 Monate
        $time_future = $time_now + (60 * 60 * 24 * 30 * 3); // 3 Monate

        // JAHRESÜBERBLICK nur Termine 3 Monate in der Vergangenheit und 3 Monate in der Zukunft
        $date_arr = $used_days = array();
        $sql = "SELECT NL.approval AS DAPPROV,NL.id AS DATEID,P.*,K.*,NL.*,NG.*,NG.id AS NGID,M.*,K.kid as CUSTID,K.email AS KEMAIL,P.id as PROGID
	FROM " . TBL_CMS_OTIMER . " NL
	INNER JOIN " . TBL_CMS_OTIMER_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $theme_id . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=NL.mid)
	LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=NL.kid)
	LEFT JOIN " . TBL_CMS_OTIMER_PROG . " P ON (P.id=NL.prog_id)	
	WHERE NL.approval=1 AND ndate>='" . date('Y-m-d', $time_past) . "' AND ndate<='" . date('Y-m-d', $time_future) . "'
	GROUP BY NL.id 
	ORDER BY time_from ASC";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $used_days[] = $row['ndate'];
            $row['date'] = (($row['ndate'] != '0000-00-00') ? my_date('d.m.Y', $row['ndate']) : '');
            $row['date_to'] = (($row['ndate_to'] != '0000-00-00') ? my_date('d.m.Y', $row['ndate_to']) : '');
            $row['time_from'] = substr($row['time_from'], 0, 5);
            $row['time_to'] = substr($row['time_to'], 0, 5);
            $row['content'] = base64_decode($row['content']);
            $mdates[] = $row;
            if ($this->cal->date_to_time($row['ndate']) >= $this->cal->date_to_time($Y . '-' . $m . '-01') && $this->cal->date_to_time($row['ndate']) <= $this->cal->
                date_to_time($Y . '-' . $m . '-31'))
                $mdates_month[] = $row;
            if ($this->cal->date_to_time($row['ndate']) == $this->cal->date_to_time($Y . '-' . $m . '-' . $d))
                $mdates_day[] = $row;
            if ($row['DID'] == $_GET['id'])
                $this->smarty->assign('selected_appointment', $row);
        }
        $this->smarty->assign('mdates', $mdates);
        $this->smarty->assign('mdates_month', $mdates_month);
        $this->smarty->assign('mdates_day', $mdates_day);
        unset($mdates);
        unset($mdates_month);
        unset($mdates_day);

        #echoarr($date_arr);die();
        if (is_array($date_arr)) {
            foreach ($date_arr as $date => $d_obj_arr) {
                $info = "";
                if (is_array($d_obj_arr['dobjects'])) {
                    foreach ($d_obj_arr['dobjects'] as $d_obj) {
                        #$info.='<span class=\\\''.(($d_obj['approval']==1) ? 'approved' : 'notapproved').'\\\'>-' . $d_obj['pr_description']. '</span><br>';
                        $info .= '-' . $d_obj['pr_description'] . '<br>';
                    }
                }
                $date_arr[$date]['info'] = $info;
            }
        }


        $pn = array('&laquo;' => $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&page=' . $_GET['page'] . '&seldate=' . $Y . '-' . $this->cal->getPrevMonth() .
                '-' . $d, '&raquo;' => $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&page=' . $_GET['page'] . '&seldate=' . $Y . '-' . $this->cal->getNextMonth() .
                '-' . $d);

        for ($month = 1; $month <= 12; $month++) {
            $month = (strlen($month) == 1) ? '0' . $month : $month;
            $tage = date("t", mktime(0, 0, 0, $month, 1, $Y));
            $days = array();
            for ($tag = 1; $tag <= $tage; $tag++) {
                $tag = (strlen($tag) == 1) ? '0' . $tag : $tag;
                $onm = "";

                if ($this->cal->date_to_time($Y . '-' . $month . '-' . $tag) == $this->cal->date_to_time(date('Y-m-d'))) {
                    $days[intval($tag)] = array(
                        $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $_GET['page'] . '&seldate=' . $Y . '-' . $month . '-' . $tag,
                        'today',
                        $tag,
                        $onm);
                }
                else {
                    if (in_array($Y . '-' . $month . '-' . $tag, $used_days)) {
                        $days[intval($tag)] = array(
                            $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $_GET['page'] . '&seldate=' . $Y . '-' . $month . '-' . $tag,
                            'full_day',
                            $tag,
                            $onm);
                    }
                    else
                        $days[intval($tag)] = array(
                            $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $_GET['page'] . '&seldate=' . $Y . '-' . $month . '-' . $tag,
                            'empty_day',
                            $tag);
                }
            }
            $year_tabs[$month]['table'] .= generate_calendar($Y, $month, $days, 3, $_SERVER['PHP_SELF'] . '?aktion=showmonth&page=' . $_GET['page'] . '&seldate=' . $Y . '-' .
                $month . '-' . $tag, 1);
            $year_tabs[$month]['month'] = intval($month);
            $year_tabs[$month]['year'] = intval($Y);

            if (intval($m) == intval($month))
                $akt_month_days = $days;
        }
        $this->smarty->assign('year_tabs', $year_tabs);
        $this->smarty->assign('seldate', $sel_date);
        $this->smarty->assign('cal_month', $m);
        $this->smarty->assign('cal_year', $Y);
        $this->smarty->assign('cal_day', $d);
        $this->smarty->assign('cal_month_today', date('m'));
        $this->smarty->assign('cal_year_today', date('Y'));
        $this->smarty->assign('cal_day_today', date('d'));
        $this->smarty->assign('cal_month_str', $this->cal->get_month_as_string($m));
        $this->smarty->assign('cal_month_box', generate_calendar($Y, $m, $akt_month_days, 3, $_SERVER['PHP_SELF'] . '?aktion=showmonth&page=' . $_GET['page'] .
            '&seldate=' . $Y . '-' . $m . '-' . $d, 1, $pn));
        unset($year_tabs);
        unset($sel_date);
        unset($akt_month_days);
    }


    /**
     * otimer_class::OnDeleteEmployee()
     * 
     * @param mixed $params
     * @return
     */
    function OnDeleteEmployee($params) {
        $mid = (int)$params['mid'];
        $this->db->query("DELETE FROM " . TBL_CMS_OTIMER_DAYWORKTIME . " WHERE dt_mid=" . $mid . " AND dt_mid>1");
        #  $this->db->query("DELETE FROM " . TBL_CMS_OTIMER . " WHERE mid=" . $mid . " AND mid>1");
        return $params;
    }

}

?>