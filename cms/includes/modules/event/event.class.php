<?php

/**
 * @package    calendar
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


DEFINE('TBL_CMS_CALENDAR', TBL_CMS_PREFIX . 'cal_dates');
DEFINE('TBL_CMS_CALENDAR_CONTENT', TBL_CMS_PREFIX . 'cal_content');
DEFINE('TBL_CMS_CALENDAR_GROUPS', TBL_CMS_PREFIX . 'cal_groups');
DEFINE('TBL_CMS_CALENDAR_PERM', TBL_CMS_PREFIX . 'cal_perm');
DEFINE('TBL_CMS_CALENDAR_GCON', TBL_CMS_PREFIX . 'cal_gcontent');
DEFINE('TBL_CMS_CALENDARFILES', TBL_CMS_PREFIX . 'cal_files');

class event_class extends event_master_class {

    var $nodes = null;
    var $treeleaf = array();

    var $langid = 1;
    var $pageid = 860;
    var $seldate = "";

    var $event = array();
    var $newslist = array();

    var $cyear = 2010;
    var $cday = 01;
    var $cmonth = 01;

    var $year_tabs = array();

    var $EVENTCAL = array();

    /**
     * event_class::event_class()
     * 
     * @param string $langid
     * @param string $seldate
     * @return
     */
    function __construct($langid = '', $seldate = '') {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        if (empty($seldate)) {
            $seldate = date('Y-m-d');
        }
        if (empty($langid)) {
            $langid = 1;
        }
        $this->langid = intval($langid);
        $this->smarty->assign('EVENT_PATH', EVENT_PATH);
        $this->init_date($seldate);
        $this->EVENTCAL['first_event'] = $this->get_first_event(true);
        $this->EVENTCAL['last_event'] = $this->get_last_event();
    }

    /**
     * event_class::init_date()
     * 
     * @param mixed $seldate
     * @return void
     */
    function init_date($seldate) {
        list($this->cyear, $this->cmonth, $this->cday) = explode('-', $seldate);
        $this->seldate = $seldate;
        $this->cal = new tgcCalendar($this->cday, $this->cmonth, $this->cyear);
        $_SESSION['seldate'] = $seldate;
    }

    /**
     * event_class::cmd_showevent()
     * 
     * @return void
     */
    function cmd_showevent() {
        $id = (int)$_GET['id'];
        $event = $this->get_event($id);
        $this->init_date($event['ndate']);
        $this->db->query("UPDATE " . TBL_CMS_CALENDAR . " SET c_views=c_views+1 WHERE id=" . $id);
    }


    /**
     * event_class::set_item_options()
     * 
     * @param mixed $item
     * @param string $pageid
     * @return
     */
    function set_item_options($item, $pageid = 0, $PLUGOPT = array()) {
        global $GRAPHIC_FUNC, $HTA_CLASS_CMS;
        $pageid = ($pageid > 0) ? (int)$pageid : (int)$_GET['page'];
        $width = (isset($PLUGOPT['width'])) ? (int)$PLUGOPT['width'] : $this->gbl_config['event_icon_thumbwidth'];
        $height = (isset($PLUGOPT['height'])) ? (int)$PLUGOPT['height'] : $this->gbl_config['event_icon_thumbheight'];
        $method = (isset($PLUGOPT['method'])) ? $PLUGOPT['method'] : 'resize';

        $width_big = (isset($PLUGOPT['width_big'])) ? (int)$PLUGOPT['width_big'] : 640;
        $height_big = (isset($PLUGOPT['height_big'])) ? (int)$PLUGOPT['height_big'] : 480;
        $method_big = (isset($PLUGOPT['method_big'])) ? $PLUGOPT['method_big'] : 'resize';

        $item['thumb'] = $item['icon'] = ($item['c_icon'] != "") ? PATH_CMS . CACHE . $GRAPHIC_FUNC->makeThumb('./' . EVENT_PATH . $item['c_icon'], $width, $height,
            './' . CACHE, true, $method) : '';
        $item['thumb_big'] = ($item['c_icon'] != "") ? PATH_CMS . CACHE . $GRAPHIC_FUNC->makeThumb('./' . EVENT_PATH . $item['c_icon'], $width_big, $height_big, './' .
            CACHE, true, $method_big) : '';

        $item['thumb_big_size'] = getimagesize(CMS_ROOT . 'cache/' . basename($item['thumb_big']));
        $item['thumb_big_is_landscape'] = $item['thumb_big_size'][0] > $item['thumb_big_size'][1];
        $item['thumb_size'] = getimagesize(CMS_ROOT . 'cache/' . basename($item['thumb']));
        $item['thumb_is_landscape'] = $item['thumb_size'][0] > $item['thumb_size'][1];
        
        $item['inlay'] = $this->genCalGroupImplement($item['EID']);
        list($item['date_year'], $item['date_month'], $item['date_day']) = explode('-', $item['ndate']);
        $item['date_month'] = my_date('M', $item['ndate']);
        $item['date_day'] = my_date('d', $item['ndate']);
        $item['date_month_num'] = (int)my_date('m', $item['ndate']);
        $item['date_year'] = (int)my_date('Y', $item['ndate']);
        $item['date'] = (($item['ndate'] != '0000-00-00') ? my_date('d.m.Y', $item['ndate']) : '');
        $item['date_to'] = (($item['ndate_to'] != '0000-00-00') ? my_date('d.m.Y', $item['ndate_to']) : '');
        $item['time_from'] = substr($item['time_from'], 0, 5);
        $item['age'] = date_diff(date_create($item['created_date']), date_create('now'))->days;
        $item['time_to'] = substr($item['time_to'], 0, 5);
        $item['c_ilink_url'] = content_class::gen_url_template($item['c_ilink']);
        $item['today_month'] = date('m');

        $item['detail_link_popup'] = PATH_CMS . $HTA_CLASS_CMS->genLink(51, array(
            $item['groupname'],
            $pageid,
            $item['EID'],
            $item['title']));
        $item['detail_link_popup_rel'] = PATH_CMS . $HTA_CLASS_CMS->genLink(51, array(
            $item['groupname'],
            $this->gbl_config['events_relid_page'],
            $item['EID'],
            $item['title']));
        $item['detail_link'] = PATH_CMS . $HTA_CLASS_CMS->genLink(52, array(
            $item['groupname'],
            $pageid,
            $item['EID'],
            $item['title']));
        $item['detail_link_rel'] = PATH_CMS . $HTA_CLASS_CMS->genLink(52, array(
            $item['groupname'],
            $this->gbl_config['events_relid_page'],
            $item['EID'],
            $item['title']));
        return $item;
    }

    /**
     * event_class::cmd_showcaldetail()
     * 
     * @return void
     */
    function cmd_showcaldetail() {
        $CORE = new main_class();
        $CORE->set_smarty_defaults();
        $content = get_template($_GET['page'], $GBL_LANGID);
        HEADER('Content-type: text/html; charset=UTF8');
        echo translate_language($content, $GBL_LANGID);
        exit;
    }


    /**
     * event_class::build_year_tablelinks()
     * 
     * @param mixed $page
     * @param mixed $aktion
     * @param mixed $seldate
     * @param mixed $calgroup_id
     * @param mixed $user_object
     * @param integer $selected_id
     * @return
     */
    function build_year_tablelinks($page, $aktion, $seldate, $calgroup_id, $user_object, $selected_id = 0) {
        if (count($this->year_tabs) == 0) {
            list($Y, $m, $d) = explode('-', $seldate);
            $time_now = $this->cal->date_to_time($seldate);
            $time_past = $time_now - (60 * 60 * 24 * 30 * 3); // 3 Monate
            $time_future = $time_now + (60 * 60 * 24 * 30 * 3); // 3 Monate
            $this->year_tabs = array();
            $pn = array('&laquo;' => $_SERVER['PHP_SELF'] . '?cmd=' . $aktion . '&page=' . $page . '&seldate=' . $Y . '-' . $this->cal->getPrevMonth() . '-' . $d, '&raquo;' =>
                    $_SERVER['PHP_SELF'] . '?cmd=' . $aktion . '&page=' . $page . '&seldate=' . $Y . '-' . $this->cal->getNextMonth() . '-' . $d);
            $date_arr = $used_days = $not_approved_items = $mdates_sorted = array();
            // JAHRESÜBERBLICK nur Termine 3 Monate in der Vergangenheit und 3 Monate in der Zukunft
            $sql = "SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NL.id AS EID
	FROM " . TBL_CMS_CALENDAR . " NL
	INNER JOIN " . TBL_CMS_CALENDAR_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $calgroup_id . ")
	INNER JOIN " . TBL_CMS_CALENDAR_PERM . " P ON (P.perm_did=NG.id " . $user_object['sql_groups'] . ")
	LEFT JOIN " . TBL_CMS_CALENDAR_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->langid . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.ndate>='" . $Y . "-01-01' AND NL.ndate<='" . $Y . "-12-31'	
	GROUP BY NL.id ORDER BY NL.ndate DESC";

            $result = $this->db->query($sql);
            while ($row = $this->db->fetch_array_names($result)) {
                if ($row['approval'] == 1)
                    $used_days[] = $row['ndate'];
                $date_arr[$row['ndate']]['dobjects'][] = $row;
                $row = $this->set_item_options($row);
                if ($row['approval'] == 1) {
                    $mdates[] = $row;
                    if ($this->cal->date_to_time($row['ndate']) >= $this->cal->date_to_time($Y . '-' . $m . '-01') && $this->cal->date_to_time($row['ndate']) <= $this->cal->
                        date_to_time($Y . '-' . $m . '-31'))
                        $mdates_month[] = $row;
                    if ($this->cal->date_to_time($row['ndate']) == $this->cal->date_to_time($Y . '-' . $m . '-' . $d))
                        $mdates_day[] = $row;
                    if ($this->cal->date_to_time($row['ndate']) >= $this->cal->date_to_time(date('Y') . "-" . date('m') . "-01"))
                        $mdates_sorted[] = $row;
                    if ($row['EID'] == $selected_id) {
                        $sel_appoint = $row;
                        $sel_appoint['filelist'] = $this->set_filelist($row['EID']);
                    }
                }
                if ($row['approval'] == 0)
                    $not_approved_items[] = $row;
            }

            /*
            if (is_array($date_arr)) {
            foreach ($date_arr as $date => $d_obj_arr) {
            $info = "";
            if (is_array($d_obj_arr['dobjects'])) {
            foreach ($d_obj_arr['dobjects'] as $d_obj) {
            $info .= '<span class=\\\'' . (($d_obj['approval'] == 1) ? 'approved' : 'notapproved') . '\\\'>-' . $d_obj['title'] . '</span><br>';
            }
            }
            $date_arr[$date]['info'] = $info;
            }
            }
            */
            /*
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
            SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $page . '&seldate=' . $Y . '-' . $month . '-' . $tag,
            'today',
            $tag,
            $onm);
            }
            else
            if ($this->cal->date_to_time($Y . '-' . $month . '-' . $tag) == $this->cal->date_to_time($seldate)) {
            $days[intval($tag)] = array(
            SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $page . '&seldate=' . $Y . '-' . $month . '-' . $tag,
            'seldate',
            $tag,
            $onm);
            }
            else {
            if (in_array($Y . '-' . $month . '-' . $tag, $used_days)) {
            $days[intval($tag)] = array(
            SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $page . '&seldate=' . $Y . '-' . $month . '-' . $tag,
            'full_day',
            $tag,
            $onm);
            }
            else
            $days[intval($tag)] = array(
            SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?aktion=showday&page=' . $page . '&seldate=' . $Y . '-' . $month . '-' . $tag,
            'empty_day',
            $tag);
            }
            }
            #      $this->year_tabs[$month]['table'] .= generate_calendar($Y, $month, $days, 3, SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?aktion=showmonth&page=' . $page .
            #   '&seldate=' . $Y . '-' . $month . '-' . $tag, 1);
            $this->year_tabs[$month]['month'] = intval($month);
            $this->year_tabs[$month]['year'] = intval($Y);

            if (intval($m) == intval($month))
            $akt_month_days = $days;
            }
            */
            $mdates_sorted = sort_db_result($mdates_sorted, 'ndate', SORT_ASC, SORT_STRING);
            $CALARR = array(
                'mdates' => $mdates,
                'mdates_month' => $mdates_month,
                'mdates_day' => $mdates_day,
                'mdates_sorted' => $mdates_sorted,
                'selected_appointment' => $sel_appoint,
                'not_approved_items' => $not_approved_items,
                'seldate' => my_date('d.m.Y', $this->seldate),
                'selected_date' => $this->seldate,
                'cal_month_today' => date('m'),
                'cal_year_today' => date('Y'),
                'cal_day_today' => date('d'),
                'cal_month_str' => $this->cal->get_month_as_string($m),
                #  'cal_month_box' => generate_calendar($Y, $m, $akt_month_days, 3, $_SERVER['PHP_SELF'] . '?aktion=showmonth&page=' . $page . '&seldate=' . $Y . '-' . $m . '-' .
                #      $d, 1, $pn),
                'year_tabs' => $this->year_tabs,
                'cal_month' => $m,
                'cal_year' => $Y,
                'cal_day' => $d);
            return $CALARR;
        }
    }

    /**
     * event_class::load_latest_events()
     * 
     * @return
     */
    function load_latest_events($PLUGOPT = array()) {
        $PLUGOPT['limit'] = (isset($PLUGOPT['limit']) ? (int)$PLUGOPT['limit'] : (int)$this->gbl_config['events_latest_limit']);
        $PLUGOPT['sort'] = (isset($PLUGOPT['sort']) ? $PLUGOPT['sort'] : 'ndate');
        $PLUGOPT['sort'] = ($PLUGOPT['sort'] == 'rnd') ? "RAND()" : $PLUGOPT['sort'];
        $PLUGOPT['sort_dirc'] = ($PLUGOPT['sort_dirc'] == 'DESC' ? 'DESC' : 'ASC');

        $latest_events = array();
        $result = $this->db->query("SELECT NL.id AS EID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID
	FROM " . TBL_CMS_CALENDAR . " NL
	INNER JOIN " . TBL_CMS_CALENDAR_GROUPS . " NG ON (NG.id=NL.group_id)
	LEFT JOIN " . TBL_CMS_CALENDAR_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->langid . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE  
        NL.approval=1
        " . ((isset($PLUGOPT['groupid']) && (int)$PLUGOPT['groupid'] > 0) ? " AND NL.group_id=" . (int)$PLUGOPT['groupid'] : "") . "
        " . ((isset($PLUGOPT['year']) && (int)$PLUGOPT['year'] > 0) ? " AND YEAR(ndate)=" . (int)$PLUGOPT['year'] : "") . "
	GROUP BY NL.id 
    ORDER BY " . $PLUGOPT['sort'] . " " . $PLUGOPT['sort_dirc'] . " 
    LIMIT " . $PLUGOPT['limit']);
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_item_options($row, '', $PLUGOPT);
            $latest_events[] = $row;
        }
        $this->smarty->assign('event_latest_items', $latest_events);
        return $latest_events;
    }

    /**
     * event_class::cmd_load_month()
     * 
     * @return
     */
    function cmd_load_month() {
        $arr = array();
        $m = (int)$_GET['month'];
        $result = $this->db->query("SELECT NL.id AS EID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID
	FROM " . TBL_CMS_CALENDAR . " NL
	INNER JOIN " . TBL_CMS_CALENDAR_GROUPS . " NG ON (NG.id=NL.group_id)
	LEFT JOIN " . TBL_CMS_CALENDAR_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->langid . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE YEAR(ndate)='" . $this->cyear . "' 
        AND NL.approval=1
        AND MONTH(ndate)='" . $m . "' 
	GROUP BY NL.id ORDER BY ndate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_item_options($row);
            $arr[] = $row;
        }
        $this->EVENTCAL['monthly_events'] = $arr;
        return $arr;
    }

    /**
     * event_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        for ($i = 1; $i <= 12; $i++) {
            $this->EVENTCAL['months'][] = array('month_num' => (int)date('m', strtotime($this->cyear . '-' . $i . '-01')), 'month_name' => date('M', strtotime($this->cyear .
                    '-' . $i . '-01')));
        }
        for ($i = 1; $i <= 12; $i++) {
            if ($i >= (int)date('m', time()) || $this->cyear > date('Y')) {
                $this->EVENTCAL['months_filtered'][] = array('month_num' => (int)date('m', strtotime($this->cyear . '-' . $i . '-01')), 'month_name' => date('M', strtotime($this->
                        cyear . '-' . $i . '-01')));
            }
        }
        $this->EVENTCAL['today_month'] = (int)date('d');
        if ($this->smarty->getTemplateVars('EVENTCAL') != NULL) {
            $this->EVENTCAL = array_merge($this->smarty->getTemplateVars('EVENTCAL'), $this->EVENTCAL);
            $this->smarty->clearAssign('EVENTCAL');
        }
        $this->smarty->assign('EVENTCAL', $this->EVENTCAL);
    }


    /**
     * event_class::load_item()
     * 
     * @param mixed $id
     * @return
     */
    function load_item($id) {
        global $GRAPHIC_FUNC;
        $id = (int)$id;
        if ($id > 0) {
            $this->event = $this->db->query_first("SELECT *,NL.approval AS APPROVED,NL.id AS EID,NG.* FROM
	" . TBL_CMS_CALENDAR . " NL
	INNER JOIN " . TBL_CMS_CALENDAR_GROUPS . " NG ON (NG.id=NL.group_id)
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)	
	WHERE NL.id=" . (int)$id);
            $this->event['FORM_CON'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR_CONTENT . " WHERE lang_id=" . $this->langid . " AND nid='" . (int)$id .
                "' LIMIT 1");
            #$this->event['fck'] = create_html_editor('FORM_CON[content]',$this->event['FORM_CON']['content'],500)	;
            if ($this->event['EID'] == 0) {
                $this->event['time_from'] = date('H:i:s');
                $this->event['time_to'] = $this->cal->plusHours($this->event['time_from'], 1);
                $this->event['ndate'] = $this->seldate;
            }
            $this->event = $this->set_item_options($this->event);
            $this->event['fck'] = create_html_editor('FORM_CON[content]', $this->event['FORM_CON']['content'], 500);
            $this->event['filelist'] = $this->set_filelist($id);
        }
    }


    /**
     * event_class::build_allowed_group_select()
     * 
     * @param mixed $selid
     * @param mixed $user_object
     * @return
     */
    function build_allowed_group_select($selid, $user_object) {
        global $HTA_CLASS_CMS;
        $sel_box = '<select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">';
        $k = 0;
        $result = $this->db->query("SELECT *,T.id AS CALGID FROM
	" . TBL_CMS_CALENDAR_GROUPS . " T
	LEFT JOIN " . TBL_CMS_CALENDAR_GCON . " NC ON (T.id=NC.g_id AND NC.lang_id=" . $this->langid . ")
	INNER JOIN " . TBL_CMS_CALENDAR_PERM . " P ON (P.perm_did=T.id " . $user_object['sql_groups'] . ")	
	GROUP BY T.id 
	ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            if (intval($selid) == 0 && $k == 0)
                $selid = $row['CALGID'];
            $sel_box .= '<option' . (($row['CALGID'] == $selid) ? ' selected' : '') . ' value="' . $_SERVER['PHPSELF'] . '?aktion=' . $_GET['aktion'] . '&page=' . $_GET['page'] .
                '&seldate=' . $Y . '-' . $m . '-' . $d . '&calgid=' . $row['id'] . '">' . $row['groupname'] . '</option>';
            $cal_themes[] = array(
                'theme' => (($row['g_title'] != "") ? $row['g_title'] : $row['groupname']),
                'class' => (($row['CALGID'] == $selid) ? ' class="selected"' : ''),
                #'link' => 'http://www.'.FM_DOMAIN.PATH_CMS.'index.php?aktion='.$_GET['aktion'].'&page='.$_GET['page'].'&seldate='.$Y.'-'.$m.'-'.$d.'&calgid='.$row['CALGID']);
                'link' => PATH_CMS . $HTA_CLASS_CMS->genLink(53, array(
                    $_GET['page'],
                    $row['CALGID'],
                    (($row['g_title'] != "") ? $row['g_title'] : $row['groupname']))));

        }
        $sel_box .= '</select>';
        $this->smarty->assign('sel_box', $sel_box);
        $this->smarty->assign('themes', $cal_themes);
        $this->smarty->assign('themes_count', count($cal_themes));
        return $selid;
    }


    /**
     * event_class::delete_lang_content()
     * 
     * @param mixed $params
     * @return
     */
    function delete_lang_content($params) {
        $id = (int)$params['id'];
        $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR_CONTENT . " WHERE lang_id=" . (int)$id . " AND lang_id>1");
        return $params;
    }

    /**
     * event_class::preparse()
     * 
     * @param mixed $params
     * @return
     */
    function preparse($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];

        # load calendar plugin
        if (strstr($html, '{TMPL_CAL_')) {
            preg_match_all("={TMPL_CAL_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $PLUGIN_OPT['table'] = $this->load_latest_events($PLUGIN_OPT);
                $this->smarty->assign('calendar_plugopt_' . $cont_matrix_id, $PLUGIN_OPT);
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$PLUGIN_OPT['tplid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=calendar_plugopt value=$calendar_plugopt_' . $cont_matrix_id . ' %>                
                <% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);

            }
        }

        # latest plugin / Events vom Plugin
        if (strstr($html, '{TMPL_EVENT_')) {
            preg_match_all("={TMPL_EVENT_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $PLUGIN_OPT['table'] = $this->load_latest_events($PLUGIN_OPT);
                $this->smarty->assign('TMPL_EVENT_' . $cont_matrix_id, $PLUGIN_OPT);
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$PLUGIN_OPT['tplid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=events value=$TMPL_EVENT_' . $cont_matrix_id . ' %>                
                <% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
            }
        }


        if (strstr($html, '{TMPL_CALINLAY_')) {
            preg_match_all("={TMPL_CALINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_CALINLAY_", "}");
                $calgroup_id = intval(strtolower(str_replace($rep, "", $wert)));
                $EVENT_OBJ = new event_class($langid, date('Y-m-d'));
                $CALOBJ = $EVENT_OBJ->build_year_tablelinks($this->gbl_config['events_relid_page'], 'showday', date('Y-m-d'), $calgroup_id, $user_object);
                $this->smarty->assign('TMPL_CALINLAY_' . intval($calgroup_id), $EVENT_OBJ->year_tabs);
                $this->smarty->assign('cal_inlay', $CALOBJ);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=calinlay value=$TMPL_CALINLAY_' . $calgroup_id . ' %><% include file="calendar_inlay.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * event_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='calendar' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $params = array_merge($params, $SM);
            $result_lang = $this->db->query("SELECT id,post_lang,language FROM " . TBL_CMS_LANG . " WHERE " . (($params['alllang'] === true) ? '' : " id=" . $params['langid'] .
                " AND ") . " approval=1 ORDER BY post_lang");
            while ($rowl = $this->db->fetch_array($result_lang)) {
                list($Y, $m, $d) = explode('-', date('Y-m-d'));
                $result = $this->db->query("SELECT *,T.id AS CALGID FROM
			" . TBL_CMS_CALENDAR_GROUPS . " T
			LEFT JOIN " . TBL_CMS_CALENDAR_GCON . " NC ON (T.id=NC.g_id AND NC.lang_id=" . $rowl['id'] . ")
			INNER JOIN " . TBL_CMS_CALENDAR_PERM . " P ON (P.perm_did=T.id)
			WHERE P.perm_group_id=1000
			ORDER BY groupname");
                while ($row = $this->db->fetch_array_names($result)) {
                    $url['url'] = self::get_http_protocol() . '://www.' . FM_DOMAIN . PATH_CMS . 'index.php?templang=' . $rowl['id'] . '&calgid=' . $row['CALGID'] . '&page=750';
                    $url['frecvent'] = $params['sm_changefreq'];
                    $url['priority'] = $params['sm_priority'];
                    $params['urls'][] = $url;
                }
            }
        }
        return (array )$params;
    }
}
