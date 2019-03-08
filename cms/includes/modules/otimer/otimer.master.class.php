<?PHP

/**
 * @package    otimer
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */

class otimer_master_class extends modules_class {

    var $progProEmployList = ARRAY();
    var $progProEmployTextList = ARRAY();
    var $progProEmployIdList = ARRAY();

    /**
     * otimer_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $this->cal = new tgcCalendar($d, $m, $Y);
    }

    /**
     * otimer_class::loadDayOpt()
     * 
     * @param mixed $seldate
     * @param mixed $groupid
     * @param bool $load_year_table
     * @return
     */
    function loadDayOpt($seldate, $groupid, $load_year_table = false) {
        $this->DAY = $this->db->query_first("SELECT * FROM " . TBL_CMS_OTIMER_DAYOPT . " WHERE day_date='" . $seldate . "' AND day_groupid=" . $groupid);
        $this->DAY['employees'] = array();
        $result = $this->db->query("SELECT *,M.id AS MID FROM " . TBL_CMS_OTIMER_DAYWORKTIME . "  DW 
			LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=DW.dt_mid)
			WHERE dt_dayid=" . intval($this->DAY['id']) . "
			ORDER BY dt_from
			");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->DAY['employees'][$row['dt_mid']] = $row;
            $this->DAY['employees'][$row['dt_mid']]['dt_from'] = $row['dt_from'];
            $this->DAY['employees'][$row['dt_mid']]['dt_fromtime'] = $this->cal->convertDateTime2Array($seldate . ' ' . $row['dt_from']);
            $this->DAY['employees'][$row['dt_mid']]['dt_totime'] = $this->cal->convertDateTime2Array($seldate . ' ' . $row['dt_to']);
            $this->DAY['employees'][$row['dt_mid']]['dt_today'] = $this->cal->convertDateTime2Array(date('Y-m-d 00:00:00'));
            $this->DAY['employees'][$row['dt_mid']]['dt_to'] = $row['dt_to'];
            $this->DAY['employees'][$row['dt_mid']]['wid'] = $row['id'];
            $this->DAY['employees'][$row['dt_mid']]['dt_duration'] = $this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']);
            $this->DAY['employees'][$row['dt_mid']]['dt_durationstr'] = printMenge($this->cal->timeDurationInHours($row['dt_from'], $row['dt_to']));
            $this->DAY['employees'][$row['dt_mid']]['programlist'] = $this->getProgramsFromEmployee($row['MID']);
            $this->DAY['employees'][$row['dt_mid']]['programs'] = safe_implode($this->progProEmployTextList[$row['MID']], ', '); #implode(', ',$this->progProEmployTextList[$row['MID']]);
            $this->DAY['employees'][$row['dt_mid']]['programidlist'] = $this->progProEmployIdList[$row['MID']];
        }
        $this->DAY['today'] = $this->cal->convertDateTime2Array(date('Y-m-d H:i:s'));
        if ($load_year_table === TRUE)
            $this->load_year_employee_workingtime($seldate, $groupid);
        return $this->DAY;
    }

    /**
     * otimer_class::inEmployeeWorkingTime()
     * 
     * @param mixed $seldate
     * @param mixed $groupid
     * @param mixed $selected_mid
     * @param mixed $appointment
     * @return
     */
    function inEmployeeWorkingTime($seldate, $groupid, $selected_mid, $appointment) {
        if ($this->gbl_config['ot_useworktime'] == 0)
            return TRUE;
        $ret = FALSE;
        $DAY = $this->loadDayOpt($seldate, $groupid);
        foreach ($DAY['employees'] as $mid => $employee) {
            ksort($employee);
            if ($employee['dt_duration'] > 0 && $selected_mid == $mid) {
                $working_timespan = $this->genTimeSpanObj($seldate . ' ' . $employee['dt_from'], $seldate . ' ' . $employee['dt_to'], 'DATE');
                // Wenn Appoinntment innerhalb der Arbeitszeit des Mitarbeiters
                if ($appointment['timefrom']['timeint'] >= $working_timespan['timefrom']['timeint'] && $appointment['timeto']['timeint'] <= $working_timespan['timeto']['timeint']) {
                    return TRUE;
                }

            }
        }
        return $ret;
    }

    /**
     * otimer_class::doubleUsed()
     * 
     * @param mixed $appointment
     * @param integer $dateid
     * @return
     */
    function doubleUsed($appointment, $dateid = -1) {
        $dateid = (int)$dateid;
        $TMP_CLOCKTBL = $this->TCLOCK_TBL;
        $index = $appointment['timefrom']['timeint'];
        $hour_index = $appointment['timefrom']['time']['H'];
        if ($this->gbl_config['ot_dbluse'] == 1)
            return FALSE;
        if (is_array($TMP_CLOCKTBL)) { # has 24h of specific date
            #   echoarr($TMP_CLOCKTBL);
            foreach ($TMP_CLOCKTBL as $hour_index => $OB) {
                if (is_array($OB['dates'])) {
                    foreach ($OB['dates'] as $start_timeint => $otdate) {
                        if ($otdate['DATEID'] != $dateid && $otdate['span_type'] == 'DATE' && $appointment['span_type'] == 'DATE') {
                            if ((($appointment['timefrom']['timeint'] >= $start_timeint && $appointment['timefrom']['timeint'] < $otdate['timeto']['timeint']) || ($appointment['timeto']['timeint'] >
                                $start_timeint && $appointment['timeto']['timeint'] <= $otdate['timeto']['timeint']) || ($start_timeint > $appointment['timefrom']['timeint'] && $start_timeint <
                                $appointment['timeto']['timeint']))) { // Appoint. Startzeitpunkt innerhalb eines vorhanden Termin liegt oder Ende, dann...
                                return TRUE;
                            }
                        }
                    }
                }
            }
        }
        return FALSE;
    }

    /**
     * otimer_class::calcendtime()
     * 
     * @param mixed $hour
     * @param mixed $min
     * @param mixed $duration
     * @param mixed $seldate
     * @param bool $sql_format
     * @return
     */
    public static function calcendtime($hour, $min, $duration, $seldate, $sql_format = false) {
        list($Y, $m, $d) = explode('-', $seldate);
        $start_time = mktime($hour, $min, 0, $m, $d, $Y);
        $end_time = $start_time + (60 * 60 * $duration);
        if ($sql_format)
            return date('Y-m-d H:i:s', $end_time);
        else
            return date('d.m.Y H:i:s', $end_time);
    }


    /**
     * otimer_master_class::genTimeSpanObj()
     * 
     * @param mixed $timefrom_str
     * @param mixed $timeto_str
     * @param mixed $SPAN_TYPE
     * @return
     */
    function genTimeSpanObj($timefrom_str, $timeto_str, $SPAN_TYPE) {
        $TIMESPNOBJ['timefrom'] = $this->cal->convertDateTime2Array($timefrom_str);
        $TIMESPNOBJ['timeto'] = $this->cal->convertDateTime2Array($timeto_str);
        $TIMESPNOBJ['hour'] = $TIMESPNOBJ['timefrom']['time']['H'];
        $TIMESPNOBJ['duration_min'] = ($TIMESPNOBJ['timeto']['timeint'] - $TIMESPNOBJ['timefrom']['timeint']) / 60; // Minutes
        $TIMESPNOBJ['span_type'] = $SPAN_TYPE;
        $TIMESPNOBJ['width_procent'] = floor((100 / 60) * $TIMESPNOBJ['duration_min']) - 0;
        $TIMESPNOBJ['width_procent'] = ($TIMESPNOBJ['width_procent'] > 100) ? 100 : $TIMESPNOBJ['width_procent'];
        $TIMESPNOBJ['used'] = TRUE;
        return $TIMESPNOBJ;
    }

    /**
     * otimer_master_class::getProgramsFromEmployee()
     * 
     * @param mixed $mid
     * @return
     */
    function getProgramsFromEmployee($mid) {
        return $this->progProEmployList[$mid];
    }

    /**
     * otimer_master_class::progProEmployee()
     * 
     * @param integer $langid
     * @return
     */
    function progProEmployee($langid = 1) {
        $MITPROGLIST = $MITPROGTEXTLIXT = $MITPROGIDLIST = array();
        $result = $this->db->query("SELECT *,P.id AS PROGID FROM
		" . TBL_CMS_OTIMER_PROG . " P
		LEFT JOIN " . TBL_CMS_OTIMER_PROG_LANG . " PL ON (PL.pr_prog_id=P.id AND PL.lang_id=" . intval($langid) . ")
		WHERE P.pr_approval=1 
		GROUP BY P.id
		ORDER BY P.pr_admintitle");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['pr_title'] = ($row['pr_title'] == "") ? $row['pr_admintitle'] : $row['pr_title'];
            $m_list = explode(';', $row['pr_employees']);
            foreach ($m_list as $mid) {
                $row['pr_duration_min'] = printMenge($row['pr_duration'] * 60);
                $MITPROGLIST[$mid][$row['PROGID']] = $row;
                $MITPROGTEXTLIXT[$mid][] = $row['pr_title'];
                $MITPROGIDLIST[$mid][] = $row['PROGID'];
            }
        }
        $this->progProEmployList = $MITPROGLIST; //Programm Obj
        $this->progProEmployTextList = $MITPROGTEXTLIXT; // Programm Namen
        $this->progProEmployIdList = $MITPROGIDLIST; // Programm IDs
    }

    /**
     * otimer_master_class::gen_approve_icon_reload()
     * 
     * @param mixed $id
     * @param mixed $value
     * @param string $add
     * @param string $siteurl
     * @param string $akt
     * @return
     */
    function gen_approve_icon_reload($id, $value, $add = '', $siteurl = '', $akt = 'a_approve') {
        if ($siteurl == "")
            $siteurl = $_SERVER['PHP_SELF'];
        if ($value == 1) {
            return "<a href=\"" . $siteurl . "?epage=" . $_GET['epage'] . "&aktion=" . $akt . "&value=0&id=" . $id . $add . "\"><img title=\"{LBLA_APPROVED}\" src=\"./images/page_visible.png\" ></a>";
        }
        else {
            return "<a href=\"" . $siteurl . "?epage=" . $_GET['epage'] . "&aktion=" . $akt . "&value=1&id=" . $id . $add . "\"><img title=\"{LBLA_NOTAPPROVED}\" src=\"./images/page_notvisible.png\" ></a>";
        }
    }

    /**
     * otimer_master_class::genMissedImgTag()
     * 
     * @param mixed $id
     * @param mixed $value
     * @param string $add
     * @param string $siteurl
     * @param string $akt
     * @return
     */
    function genMissedImgTag($id, $value, $add = '', $siteurl = '', $akt = 'setmissed') {
        if ($siteurl == "")
            $siteurl = $_SERVER['PHP_SELF'];
        if ($value == 1) {
            return "<a class='btn btn-default' href=\"" . $siteurl . "?epage=" . $_GET['epage'] . "&aktion=" . $akt . "&value=0&id=" . $id . $add . "\" title=\"{LBLA_OTNOTMISSED}\"><i class='fa fa-check-circle-o text-success'></i></a>";
        }
        else {
            return "<a class='btn btn-default' href=\"" . $siteurl . "?epage=" . $_GET['epage'] . "&aktion=" . $akt . "&value=1&id=" . $id . $add . "\" title=\"{LBLA_OTMISSED}\"><i class='fa fa-ban text-danger'></i></a>";
        }
    }

    /**
     * otimer_master_class::genBlockImgTag()
     * 
     * @param mixed $id
     * @param mixed $value
     * @param string $akt
     * @param string $add
     * @return
     */
    function genBlockImgTag($id, $value, $akt = 'setblock', $add = '') {
        if ($siteurl == "")
            $siteurl = $_SERVER['PHP_SELF'];
        if ($value == 1) {
            return "<a class='btn btn-default' href=\"" . $siteurl . "?epage=" . $_GET['epage'] . "&aktion=" . $akt . "&value=0&id=" . $id . $add . "\" title=\"{LBLA_OTBLOCK}\"><i class='fa fa-user-times text-danger'></i></a>";
        }
        else {
            return "<a class='btn btn-default' href=\"" . $siteurl . "?epage=" . $_GET['epage'] . "&aktion=" . $akt . "&value=1&id=" . $id . $add . "\" title=\"{LBLA_OTNOTBLOCK}, jetzt sperren\"><i class='fa fa-user text-success'></i></a>";
        }
    }


    /**
     * otimer_master_class::set_missed_date()
     * 
     * @param mixed $id
     * @param mixed $value
     * @return
     */
    function set_missed_date($id, $value) {
        $this->db->query("UPDATE " . TBL_CMS_OTIMER . " SET missed='" . $value . "' WHERE id='" . (int)$id . "' LIMIT 1");
    }

    /**
     * otimer_master_class::set_blocked_customer_by_date()
     * 
     * @param mixed $dateid
     * @param mixed $value
     * @return
     */
    function set_blocked_customer_by_date($dateid, $value) {
        $OT = $this->db->query_first("SELECT * FROM " . TBL_CMS_OTIMER . " WHERE id=" . (int)$dateid);
        $this->set_blocked_customer($OT['kid'], $value);
    }

    /**
     * otimer_master_class::set_blocked_customer()
     * 
     * @param mixed $kid
     * @param mixed $value
     * @return
     */
    function set_blocked_customer($kid, $value) {
        $this->db->query("UPDATE " . TBL_CMS_OTIMER . " SET block_kid='" . $value . "' WHERE kid=" . (int)$kid);
    }

    /**
     * otimer_master_class::genCommentImgTag()
     * 
     * @param mixed $appointment
     * @return
     */
    function genCommentImgTag($appointment) {
        $appointment_id = (int)$appointment['DATEID'];
        if ($appointment['comments_cu'] != "" || $appointment['comments_ma'] != "") {
            $icon = '<a href="javascript:void(0);" class="btn btn-default"><i data-toggle="tooltip" data-placement="left" title="' . htmlspecialchars($appointment['comments_cu']) .
                '|' . htmlspecialchars($appointment['comments_ma']) . '" class="fa fa-comment text-success" ></i></a>';
        }
        else {
            $icon = '<a data-toggle="tooltip" data-placement="left" title="Kunden Anmerkungen nicht vorhanden" href="javascript:void(0);" class="btn btn-default"><i  class="fa fa-comment text-danger" ></i></a>';
        }
        return $icon;
    }

    /**
     * otimer_master_class::gen_del_icon_reload()
     * 
     * @param mixed $id
     * @param string $akt
     * @param string $confirm
     * @param string $toadd
     * @return
     */
    function gen_del_icon_reload($id, $akt = 'a_del', $confirm = '{LBL_CONFIRM}', $toadd = '') {
        return '<a ' . gen_java_confirm($confirm) . ' href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&id=' . $id . $toadd . '&aktion=' . $akt .
            '"><img src="./images/page_delete.png" title="{LBL_DELETE}" ></a>';
    }
    /**
     * otimer_master_class::genCloneImgTagADMIN()
     * 
     * @param mixed $id
     * @param string $akt
     * @return
     */
    function genCloneImgTagADMIN($id, $akt = 'repeatappointment') {
        return '<a class="btn btn-default" href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=' . $akt . '&dateid=' . $id .
            '"><i class="fa fa-clone"></i></a>';
    }

    /**
     * otimer_master_class::gen_edit_icon()
     * 
     * @param mixed $id
     * @param string $toadd
     * @param string $a
     * @param string $idc
     * @return
     */
    function gen_edit_icon($id, $toadd = '', $a = 'edit', $idc = 'id') {
        return '<a class="btn btn-default"  title="{LBLA_EDIT}" href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&' . $idc . '=' . $id . '&aktion=' . $a .
            $toadd . '"><i class="fa fa-pencil-square-o><!----></i></a>';
    }

    /**
     * otimer_master_class::addAppointmentToTimeTable()
     * 
     * @param mixed $appointment
     * @return
     */
    function addAppointmentToTimeTable($appointment) {
        $index = $appointment['timefrom']['timeint'];
        $hour_index = $appointment['timefrom']['time']['H'];
        #if ($this->doubleUsed($appointment)==TRUE) return false;
        $index_new = $index;
        if ($this->TCLOCK_TBL[$hour_index]['dates'][$index]['used'] === TRUE) {
            $k = 0;
            while ($this->TCLOCK_TBL[$hour_index]['dates'][$index_new]['used'] === TRUE) {
                $k++;
                $index_new = $index . '_' . $k;
            }
        }
        ksort($appointment);
        $this->TCLOCK_TBL[$hour_index]['dates'][$index_new] = $appointment;
        return true;
    }

    /**
     * otimer_master_class::buildDateArr_MonthDay()
     * 
     * @param mixed $Y
     * @param mixed $m
     * @param mixed $d
     * @param mixed $theme_id
     * @param bool $onlyapp
     * @param string $range_type
     * @return
     */
    function buildDateArr_MonthDay($Y, $m, $d, $theme_id, $onlyapp = true, $range_type = 'tyear') {
        $sql = "SELECT NL.approval AS DAPPROV,NL.id AS DATEID,P.*,K.*,NL.*,NG.*,NG.id AS NGID,M.*,K.kid as CUSTID,K.email AS KEMAIL,P.id as PROGID
	FROM " . TBL_CMS_OTIMER . " NL
	INNER JOIN " . TBL_CMS_OTIMER_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $theme_id . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=NL.mid)
	LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=NL.kid)
	LEFT JOIN " . TBL_CMS_OTIMER_PROG . " P ON (P.id=NL.prog_id)	
	WHERE " . (($onlyapp === TRUE) ? " NL.approval=1 AND " : '');
        if ($range_type == 'tyear') {
            $sql .= " ndate>='" . $Y . "-01-01' AND ndate<='" . $Y . "-12-31'";
        }
        else
            if ($range_type == 'tmonth') {
                $sql .= " ndate>='" . $Y . "-" . $m . "-01' AND ndate<='" . $Y . "-" . $m . "-31'";
            }
            else {
                $sql .= " ndate>='" . $Y . "-" . $m . "-" . $d . "' AND ndate<='" . $Y . "-" . $m . "-" . $d . "'";
            }
            $sql .= "GROUP BY NL.id 
	ORDER BY time_from ASC";
        $result = $this->db->query($sql);
        $this->cal_table .= '<br>';
        for ($i = 0; $i <= 23; $i++) {
            $hour = (strlen($i) > 1) ? $i : '0' . $i;
            $this->TCLOCK_TBL[$hour] = array();
            $this->TCLOCK_TBL[$hour]['dates'] = array();
        }
        while ($row = $this->db->fetch_array_names($result)) {
            if (ISADMIN == 1) {
                $row['icon_del'] = kf::gen_del_icon($row['DATEID'], true, 'delete_appointment');
                $row['icon_edit'] = kf::gen_edit_icon($row['DATEID'], '&seldate=' . $Y . '-' . $m . '-' . $d, 'editdate', 'dateid');
                $row['icon_approve'] = kf::gen_approve_icon($row['DATEID'], $row['DAPPROV'], 'approve_appointment');
            }
            $row['icon_clone'] = $this->genCloneImgTagADMIN($row['DATEID']);
            $row['icon_missed'] = $this->genMissedImgTag($row['DATEID'], $row['missed'], '&column=missed&idcol=id');
            $row['icon_block'] = $this->genBlockImgTag($row['DATEID'], $row['block_kid']);
            $row['icon_comment'] = $this->genCommentImgTag($row);
            $row['icon_blockc'] = $this->genBlockImgTag($row['kid'], $row['block_kid'], 'setblockcust');
            # $row['inlay'] = genCalGroupImplement($row['NGID']);
            $row['date'] = (($row['ndate'] != '0000-00-00') ? my_date('d.m.Y', $row['ndate']) : '');
            $row['timefrom'] = $this->cal->convertDateTime2Array($row['time_from']);
            $row['timeto'] = $this->cal->convertDateTime2Array($row['time_to']);
            $row['duration_min'] = ($row['timeto']['timeint'] - $row['timefrom']['timeint']) / 60; // Minutes
            $row['used'] = TRUE;
            $row['span_type'] = 'DATE';
            $rest_zur_vollen_stunde = 60 - $row['timefrom']['time']['i'];
            if ($rest_zur_vollen_stunde > $row['duration_min']) {
                $row['width_procent'] = floor((100 / 60) * $row['duration_min']); #$rest_zur_vollen_stunde;
                $duration_within_hour = $row['duration_min'];
            }
            else {
                $row['width_procent'] = floor((100 / 60) * $rest_zur_vollen_stunde);
                $duration_within_hour = $rest_zur_vollen_stunde;
            }
            $row['overhead_min'] = $row['duration_min'] - $rest_zur_vollen_stunde;
            $row['overhead_min'] = ($row['overhead_min'] < 0) ? 0 : $row['overhead_min'];
            $mdates[] = $row;
            if ($this->cal->date_to_time($row['ndate']) >= $this->cal->date_to_time($Y . '-' . $m . '-01') && $this->cal->date_to_time($row['ndate']) <= $this->cal->
                date_to_time($Y . '-' . $m . '-31'))
                $mdates_month[] = $row;
            if ($this->cal->date_to_time($row['ndate']) == $this->cal->date_to_time($Y . '-' . $m . '-' . $d)) {
                $mdates_day[] = $row;
                $hour_index = $row['timefrom']['time']['H'];
                $this->addAppointmentToTimeTable($row);
                $this->TCLOCK_TBL[$hour_index]['duration_min'] += $duration_within_hour; #$row['duration_min'];
                $this->TCLOCK_TBL[$hour_index]['hour'] = $row['timefrom']['time']['H'];
                $this->TCLOCK_TBL[$hour_index]['date_us'] = $row['timefrom']['date_us'];

                $next_hour = $row['timefrom']['time']['H'];
                $overhead_min = $row['overhead_min'];
                //************** ADD OVER TIME SPANS *************
                while ($overhead_min > 0) {
                    $next_hour += 1;
                    $next_hour = (strlen($next_hour) > 1) ? $next_hour : '0' . $next_hour;
                    $OVER_TIMESPAN_OBJ = $this->genTimeSpanObj($row['timefrom']['date_us'] . ' ' . ($next_hour) . ':00:00', $row['timeto']['datime_us'], 'OVER');
                    $this->TCLOCK_TBL[$next_hour]['duration_min'] += $OVER_TIMESPAN_OBJ['duration_min'];
                    $OVER_TIMESPAN_OBJ['prog_title'] = $row['prog_title'];
                    $overhead_min = $OVER_TIMESPAN_OBJ['duration_min'] - 60;
                    $OVER_TIMESPAN_OBJ['overhead_min'] = ($overhead_min > 0) ? 60 : $overhead_min;
                    $OVER_TIMESPAN_OBJ['lastblock'] = $OVER_TIMESPAN_OBJ['overhead_min'] < 60;
                    $this->addAppointmentToTimeTable($OVER_TIMESPAN_OBJ);
                }

            }

        }
        unset($result);
        //************************************************
        //************** ADD FREE TIME SPANS *************
        //************************************************

        foreach ($this->TCLOCK_TBL as $hour => $darr) {
            $darr['date_count'] = count($darr['dates']);
            $darr['free_time'] = 60 - $darr['duration_min'];
            if ($darr['date_us'] == '')
                $darr['date_us'] = $Y . '-' . $m . '-' . $d;
            $this->TCLOCK_TBL[$hour]['hour'] = $hour;
            ksort($darr['dates']);


            //FREIE STUNDE komplett (60min)
            if ($darr['free_time'] > 0 && count($darr['dates']) == 0) {
                $SPAN_TIME_OBJ = $this->GenTimeSpanObj($darr['date_us'] . ' ' . $hour . ':00:00', $darr['date_us'] . ' ' . ($hour + 1) . ':00:00', 'FREE');
                $this->addAppointmentToTimeTable($SPAN_TIME_OBJ);
            }


            // zum Teil freie Stunde
            if ($darr['free_time'] > 0 && count($darr['dates']) > 0) {
                $k = $last_end_time = 0;
                $last_end_obj = $this->cal->convertDateTime2Array($darr['date_us'] . ' ' . ($hour) . ':00:00');
                foreach ($darr['dates'] as $timeint => $otdate) {
                    $k++;
                    // wenn letzter und einziger Termin in der Stunde (freie Zeit NACH Termin)
                    if ($k == $darr['date_count']) {
                        $free_time = 60 - $otdate['timeto']['time']['i'];
                        if ($free_time > 0) {
                            $SPAN_TIME_OBJ = $this->GenTimeSpanObj($otdate['timeto']['datime_us'], $darr['date_us'] . ' ' . ($hour + 1) . ':00:00', 'FREE');
                            if ($SPAN_TIME_OBJ['duration_min'] > 0)
                                $this->addAppointmentToTimeTable($SPAN_TIME_OBJ);
                        }
                    }

                    // freie Zeit VOR Termin
                    $free_time = $otdate['timefrom']['time']['i'] - $last_end_time;
                    if ($free_time > 0) {
                        $last_end_time = (strlen($last_end_time) > 1) ? $last_end_time : '0' . $last_end_time;
                        $SPAN_TIME_OBJ = $this->GenTimeSpanObj($last_end_obj['date_us'] . ' ' . $last_end_obj['time']['H'] . ':' . $last_end_time . ':' . $last_end_obj['time']['s'], $otdate['timefrom']['datime_us'],
                            'FREE');
                        $this->addAppointmentToTimeTable($SPAN_TIME_OBJ);
                    }
                    $last_end_obj = $otdate['timeto'];
                    $last_end_time = $otdate['timeto']['time']['i'];
                }
                ksort($this->TCLOCK_TBL[$hour]['dates']);
            }
        }
        #echoarr($mdates);die;'year' => $mdates,
        $arr = array(
            'day' => $mdates_day,
            'year' => $mdates,
            'month' => $mdates_month,
            'clock_table' => $this->TCLOCK_TBL);

        return $arr;
    }

    /**
     * uptins_table()
     * 
     * @param mixed $table
     * @param mixed $id_name
     * @param mixed $id_value
     * @param mixed $FORM
     * @param integer $admin
     * @return
     */
    function uptins_table($table, $id_name, $id_value, $FORM, $admin = 0) {
        if (count($FORM) > 0) {
            $objekt = $this->db->query_first("SELECT * FROM $table WHERE $id_name='$id_value'");
            if (intval($objekt[$id_name]) > 0) {
                foreach ($FORM as $key => $wert) {
                    if ($objekt[$key] != $wert) {
                        if ($sqlquery)
                            $sqlquery .= ', ';
                        $sqlquery .= "$key='$wert'";
                    }
                }
                if (!$sqlquery)
                    return false;
                $sql = "UPDATE $table SET $sqlquery WHERE $id_name='$id_value'";
                if ($admin == 1)
                    echo $sql;
                if ($sqlquery)
                    $this->db->query($sql);
                return $objekt[$id_name];
            }
            else {
                return insert_table($table, $FORM, $admin);
            }

        }
        else
            return 0;
    }

    /**
     * otimer_master_class::load_appointment()
     * 
     * @param mixed $id
     * @return
     */
    function load_appointment($id) {
        $id = intval($id);
        $N_OBJ = $this->db->query_first("SELECT NL.*,NL.id AS DATEID,K.nachname,K.vorname FROM
            	" . TBL_CMS_OTIMER . " NL
                LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=NL.kid)                
            	WHERE NL.id=" . $id . "
	");
        return (array )$N_OBJ;
    }

}
