<?php

/**
 * @package    calendar
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class event_admin_class extends event_master_class {

    var $EVENT = array();

    /**
     * event_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

        $langid = ($_GET['uselang'] == 0) ? $this->gbl_config['std_lang_id'] : $_GET['uselang'];
        $this->langid = intval($langid);
        if ($_GET['gid'] > 0)
            $_SESSION['calgroup_id'] = $_GET['gid'];
        if ($_POST['FORM']['group_id'] > 0)
            $_GET['gid'] = $_SESSION['calgroup_id'] = $_POST['FORM']['group_id'];
        $_SESSION['seldate'] = ($_GET['seldate'] != "") ? $_GET['seldate'] : $_SESSION['seldate'];

        $_SESSION['seldate'] = (strlen($_SESSION['seldate']) < 10) ? date('Y-m-d') : $_SESSION['seldate'];
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $sel_date = $d . '.' . $m . '.' . $Y;
        list($this->cyear, $this->cmonth, $this->cday) = explode('-', $_SESSION['seldate']);
        $this->cal = new tgcCalendar($d, $m, $Y);
        $defaults = array(
            'langid' => $this->langid,
            'cal_month_today' => date('m'),
            'cal_year_today' => date('Y'),
            'cal_day_today' => date('d'));
        $this->EVENT = array_merge($this->EVENT, $defaults);
        # uhrzeit table
        for ($h = 7; $h <= 22; $h++) {
            for ($i = 0; $i < 60; $i += 5) {
                $this->EVENT['times'][] = (strlen($h) == 1 ? '0' . $h : $h) . ':' . (strlen($i) == 1 ? '0' . $i : $i);
            }
        }
    }


    /**
     * event_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->EVENT['calgroup_id'] = $_SESSION['calgroup_id'];
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $sel_date = $d . '.' . $m . '.' . $Y;
        $this->EVENT['seldate'] = $sel_date;
        $this->smarty->assign('EVENT', $this->EVENT);
    }

    /**
     * event_admin_class::load_groups()
     * 
     * @return
     */
    function load_groups() {
        $k = 0;
        $this->EVENT['CALTHEMES'] = array();
        list($Y, $m, $d) = explode('-', $_SESSION['seldate']);
        $this->EVENT['cal_month'] = $m;
        $this->EVENT['cal_year'] = $Y;
        $this->EVENT['cal_day'] = $d;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CALENDAR_GROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['Y'] = $Y;
            $row['m'] = $m;
            $row['d'] = $d;
            $this->EVENT['CALTHEMES'][] = $row;
            if (intval($_SESSION['calgroup_id']) == 0 && $k == 0)
                $_SESSION['calgroup_id'] = $row['id'];
            if ($row['id'] == $_SESSION['calgroup_id']) {
                $this->EVENT['caltheme'] = $row;
            }
            #$sel_box .= '<option' . (($row['id'] == $_SESSION['calgroup_id']) ? ' selected' : '') . ' value="http://www.' . FM_DOMAIN . PATH_CMS . 'admin/run.php?epage=' .
            #    $_GET['epage'] . '&seldate=' . $Y . '-' . $m . '-' . $d . '&gid=' . $row['id'] . '">' . $row['groupname'] . '</option>';
            $k++;
        }
        $_SESSION['calgroup_id'] = intval($_SESSION['calgroup_id']);

    }

    /**
     * event_admin_class::cmd_load_events()
     * 
     * @return
     */
    function cmd_load_events() {
        $this->load_groups();
        $this->EVENT['FIRST_DATE'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR . " WHERE group_id=" . $_SESSION['calgroup_id'] . " ORDER BY ndate ASC");
        $this->EVENT['LAST_DATE'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR . " WHERE group_id=" . $_SESSION['calgroup_id'] . " ORDER BY ndate DESC");
        if (isset($this->EVENT['FIRST_DATE']['id'])) {
            $this->EVENT['FIRST_DATE']['year'] = my_date('Y', $this->EVENT['FIRST_DATE']['ndate']);
        }
        else {
            $this->EVENT['FIRST_DATE']['year'] = date('Y');
        }
        if (isset($this->EVENT['LAST_DATE']['id'])) {
            $this->EVENT['LAST_DATE']['year'] = my_date('Y', $this->EVENT['LAST_DATE']['ndate']);
        }
        else {
            $this->EVENT['LAST_DATE']['year'] = date('Y') + 1;
        }      
        $this->load_events($_SESSION['calgroup_id']);
    }

    /**
     * event_admin_class::load_events()
     * 
     * @param mixed $groupid
     * @return
     */
    function load_events($groupid) {
        $this->EVENT['mdates_day'] = $this->EVENT['mdates'] = $this->EVENT['mdates_month'] = array();
        $sql = "SELECT NL.id AS EID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID
	FROM " . TBL_CMS_CALENDAR . " NL
	INNER JOIN " . TBL_CMS_CALENDAR_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . (int)$groupid . ")
	LEFT JOIN " . TBL_CMS_CALENDAR_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->langid . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE ndate>='" . $this->cyear . "-01-01' AND ndate<='" . $this->cyear . "-12-31'
	GROUP BY NL.id 
	ORDER BY ndate DESC";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_item_options($row);
            $inlay = $row['inlay'];
            $this->EVENT['mdates'][] = $row;
            if ($this->cal->date_to_time($row['ndate']) >= $this->cal->date_to_time($this->cyear . '-' . $this->cmonth . '-01') && $this->cal->date_to_time($row['ndate']) <=
                $this->cal->date_to_time($this->cyear . '-' . $this->cmonth . '-31'))
                $this->EVENT['mdates_month'][] = $row;
            if ($this->cal->date_to_time($row['ndate']) == $this->cal->date_to_time($this->cyear . '-' . $this->cmonth . '-' . $this->cday))
                $this->EVENT['mdates_day'][] = $row;
        }


        $this->smarty->assign('inlay', $inlay);
    }

    /**
     * event_admin_class::cmd_load_cal_js_events()
     * 
     * @return
     */
    function cmd_load_cal_js_events() {
        $year = date('Y');
        $month = date('m');
        $this->load_events($_GET['gid']);
        foreach ($this->EVENT['mdates'] as $key => $row) {
            $events[] = array(
                'id' => $row['EID'],
                'title' => $row['time_from'] . '-' . $row['time_to'] . ' ' . $row['title'],
                'start' => $row['ndate'],
                'end' => $row['ndate'],
                'url' => $_SERVER['PHP_SELF'] . "?epage=event.inc&id=" . $row['EID'] . "&cmd=edit");
        }
        echo json_encode($events);
        $this->hard_exit();
    }


    /**
     * event_admin_class::cmd_del_item()
     * 
     * @return
     */
    function cmd_del_item() {
        $this->delete_item($_GET['ident']);
        $this->ej();
    }

    /**
     * event_admin_class::set_item_options()
     * 
     * @param mixed $item
     * @param string $pageid
     * @return
     */
    function set_item_options($item, $pageid = '') {
        global $GRAPHIC_FUNC, $HTA_CLASS_CMS;
        $pageid = $_REQUEST['epage'];
        $item['icon'] = ($item['c_icon'] != "") ? PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../' . EVENT_PATH . $item['c_icon'], 90, 90, 'admin/' . CACHE, true,
            'crop') : '';
        $item['icon_del'] = kf::gen_del_icon($item['EID'], true, 'del_item');
        $item['icon_edit'] = kf::gen_edit_icon($item['EID']);
        $item['icon_approve'] = kf::gen_approve_icon($item['EID'], $item['approval']);
        $item['icon_view'] = '<a class="btn btn-default" target="_event" title="{LBL_PREVIEW}" href="..' . PATH_CMS . $HTA_CLASS_CMS->genLink(52, array(
            $item['groupname'],
            $this->gbl_config['events_relid_page'],
            $item['EID'],
            $item['title'])) . '"><i class="fa fa-eye"><!----></i></a>';


        $item['inlay'] = $this->genCalGroupImplement($item['EID']);
        list($item['date_year'], $item['date_month'], $item['date_day']) = explode('-', $item['ndate']);
        $item['date_month'] = my_date('M', $item['ndate']);
        $item['date'] = (($item['ndate'] != '0000-00-00') ? my_date('d.m.Y', $item['ndate']) : '');
        $item['date_to'] = (($item['ndate_to'] != '0000-00-00') ? my_date('d.m.Y', $item['ndate_to']) : '');
        $item['time_from'] = substr($item['time_from'], 0, 5);
        $item['time_to'] = substr($item['time_to'], 0, 5);
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
        $item['gm'] = '<iframe marginwidth="0" marginheight="0" src="http://www.trebaxa.com/gmgen.php?zoom=' . $this->gbl_config['cal_gm_zoom'] . '&height=' . $this->
            gbl_config['cal_gm_height'] . '&width=' . $this->gbl_config['cal_gm_width'] . '&address=' . $item['c_gm_place'] . '" frame width="' . $this->gbl_config['cal_gm_width'] .
            '" scrolling="no" height="' . $this->gbl_config['cal_gm_height'] . '"></iframe>';
        return $item;
    }

    /**
     * event_admin_class::load_item()
     * 
     * @param mixed $id
     * @return
     */
    function load_item($id) {
        global $GRAPHIC_FUNC;
        $id = (int)$id;
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
        $this->nodes = new cms_tree_class();
        $this->nodes->db = $this->db;
        $this->nodes->create_result_and_array("SELECT id, parent, description,approval FROM " . TBL_CMS_TEMPLATES .
            " WHERE c_type='T' AND gbl_template=0 ORDER BY parent,morder", 0, 0, -1);
        $this->event['internal_link_arr'] = $this->nodes->outputtree_select();

    }

    /**
     * event_admin_class::cmd_reload_event_files()
     * 
     * @return void
     */
    function cmd_reload_event_files() {
        $this->cmd_edit();
        $this->parse_to_smarty();
        kf::echo_template('calendar.editor.files');
    }

    /**
     * event_admin_class::cmd_delicon()
     * 
     * @return
     */
    function cmd_delicon() {
        $this->del_icon((int)$_GET['id']);
        $this->msg('{LBL_DELETED}');
        $this->ej('get_event_img');
    }

    /**
     * event_admin_class::set_kid()
     * 
     * @param mixed $kid
     * @param mixed $id
     * @return
     */
    function set_kid($kid, $id) {
        $FORM = array();
        $FORM['c_kid'] = $kid;
        $KOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $kid);
        if ($this->gbl_config['login_mode'] == 'USERNAME') {
            $FORM['c_author'] = $this->db->real_escape_string($KOBJ['username']);
        }
        else {
            $FORM['c_author'] = $this->db->real_escape_string($KOBJ['vorname'] . ', ' . $KOBJ['nachname']);
        }
        update_table(TBL_CMS_CALENDAR, 'id', (int)$id, $FORM);
        $this->load_item($id);
    }

    /**
     * event_admin_class::cmd_set_autor()
     * 
     * @return
     */
    function cmd_set_autor() {
        $this->set_kid($_GET['setkid'], $_GET['id']);
        $this->TCR->tb();
    }

    /**
     * event_admin_class::cmd_del_file()
     * 
     * @return
     */
    function cmd_del_file() {
        if ($this->del_afile($_GET['ident'])) {
            $this->msg('{LBL_DELETED}');
            $this->ej();
        }
        else {
            $this->msge('{LBL_NOTDELETED}');
            $this->ej();
        }
    }

    /**
     * event_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        $this->setApprove($_GET['value'], $_GET['ident']);
        $this->ej();
    }

    /**
     * event_admin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        global $LNGOBJ;
        $FORM_CON = array();
        $this->load_item($_GET['id']);
        $this->EVENT['edit_form'] = array(
            'langselect' => $LNGOBJ->build_lang_select(),
            'event' => $this->event,
            'FORM_CON' => $this->event['FORM_CON'],
            'event_path' => EVENT_PATH,
            'uselang' => $this->langid,
            'id' => (int)$_GET['id'],
            'calgroup_id' => $_SESSION['calgroup_id'],
            'fck' => create_html_editor('FORM_CON[content]', $this->event['FORM_CON']['content'], 500));

        $this->load_groups();
    }

    /**
     * event_admin_class::cmd_get_event_img()
     * 
     * @return void
     */
    function cmd_get_event_img() {
        $this->cmd_edit();
        $this->parse_to_smarty();
        kf::echo_template('calendar.editor.img');
    }

    /**
     * event_admin_class::cmd_dragdroplogfileimage()
     * 
     * @return void
     */
    function cmd_dragdroplogfileimage() {
        if (!validate_upload_file($_FILES['dateiicon'])) {
            echo json_encode(array('status' => 'failed', 'filename' => $_FILES['dateiicon']['name']));
            $this->hard_exit();
            #$_SESSION['upload_msge']
        }
        $eventid = (int)$_GET['id'];
        $project_root = CMS_ROOT . EVENT_PATH;
        if (!is_dir($project_root))
            mkdir($project_root, 0775);

        $new_file_name = $this->unique_filename($project_root, $this->format_file_name($_FILES['dateiicon']['name']));
        move_uploaded_file($_FILES['dateiicon']['tmp_name'], $project_root . $new_file_name);
        chmod($project_root . $new_file_name, 0755);


        # add to index
        $FINFO = array('c_icon' => $new_file_name);
        update_table(TBL_CMS_CALENDAR, 'id', $eventid, $FINFO);
        echo json_encode(array('status' => 'ok', 'filename' => $_FILES['dateiicon']['name']));
        $this->hard_exit();
    }

    /**
     * event_admin_class::cmd_dragdroplogfiledatei()
     * 
     * @return void
     */
    function cmd_dragdroplogfiledatei() {
        if (!validate_upload_file($_FILES['datei'])) {
            echo json_encode(array('status' => 'failed', 'filename' => $_FILES['datei']['name']));
            $this->hard_exit();
            #$_SESSION['upload_msge']
        }
        $eventid = (int)$_GET['id'];
        $new_file_name = $this->format_file_name($FILES['datei']['name']);
        $project_root = CMS_ROOT . EVENT_PATH;
        if (!is_dir($project_root))
            mkdir($project_root, 0775);

        $new_file_name = $this->unique_filename($project_root, $_FILES['datei']['name']);
        move_uploaded_file($_FILES['datei']['tmp_name'], $project_root . $new_file_name);
        chmod($project_root . $new_file_name, 0755);


        # add to index
        $RetVal = explode('.', $new_file_name);
        $file_extention = strtolower($RetVal[count($RetVal) - 1]);
        $AFILE = array();
        $AFILE['f_file'] = $new_file_name;
        $AFILE['f_ext'] = $file_extention;
        $AFILE['f_nid'] = $eventid;
        $AFILE['f_inserttime'] = time();
        $AFILE['f_size'] = filesize($project_root . $new_file_name);
        insert_table(TBL_CMS_CALENDARFILES, $AFILE);
        echo json_encode(array('status' => 'ok', 'filename' => $_FILES['datei']['name']));
        $this->hard_exit();
    }


    /**
     * event_admin_class::save_item()
     * 
     * @param mixed $FORM
     * @param mixed $FORM_CON
     * @param mixed $id
     * @param mixed $conid
     * @param mixed $FILES
     * @return
     */
    function save_item($FORM, $FORM_CON, $id, $conid, $FILES) {
        $id = (int)$id;
        $conid = (int)$conid;
        $FORM['whole_day'] = intval($FORM['whole_day']);
        $FORM['ndate'] = format_date_to_sql_date($FORM['ndate']);
        $FORM['c_lastchange'] = time();
        if (strlen($FORM['time_from']) == 5)
            $FORM['time_from'] .= '.00';
        if (strlen($FORM['time_to']) == 5)
            $FORM['time_to'] .= '.00';
        if ($id == 0) {
            $FORM['mid'] = (int)$_SESSION['mitarbeiter'];
            $FORM['created_date'] = date('Y-m-d');
            $id = insert_table(TBL_CMS_CALENDAR, $FORM);
            $FORM_CON['nid'] = $id;
            insert_table(TBL_CMS_CALENDAR_CONTENT, $FORM_CON);
            $this->LOGCLASS->addLog('INSERT', 'Calendar item [' . $id . '] ' . $FORM_CON['title']);
            $admin_type = 'INSERT';
        }
        else {
            update_table(TBL_CMS_CALENDAR, 'id', $id, $FORM);
            if ($conid > 0) {
                update_table(TBL_CMS_CALENDAR_CONTENT, 'id', $conid, $FORM_CON);
            }
            else {
                $FORM_CON['nid'] = $id;
                insert_table(TBL_CMS_CALENDAR_CONTENT, $FORM_CON);
            }
            $this->LOGCLASS->addLog('UPDATE', 'Calendar item [' . $id . '] ' . $FORM_CON['title']);
            $admin_type = 'UPDATE';
        }
        # $this->file_upload($FILES, $id);
        $this->load_item($id);
        $this->inform_admins($admin_type);
    }

    /**
     * event_admin_class::cmd_save_event()
     * 
     * @return
     */
    function cmd_save_event() {
        $this->save_item($_POST['FORM'], $_POST['FORM_CON'], $_POST['id'], $_POST['conid'], $_FILES);
        $this->msg('{LBLA_SAVED}');
        if (!isset($_POST['id']) || (int)$_POST['id'] == 0) {
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_POST['FORM_CON']['lang_id'] . '&epage=' . $_GET['epage'] . '&aktion=edit&id=' . $this->event['EID']);
            exit;
        }
        $this->ej();
    }

    /**
     * event_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('calendar');
        $this->EVENT['conf'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * event_admin_class::cmd_calgroups()
     * 
     * @return
     */
    function cmd_calgroups() {
        $result = $this->db->query("SELECT *	FROM " . TBL_CMS_CALENDAR_GROUPS . "	ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icon_del'] = kf::gen_del_icon($row['id'], true, 'del_group');
            $row['icon_edit'] = kf::gen_edit_icon($row['id'], '', 'edit_group');
            $this->EVENT['groups'][] = $row;
        }
    }

    /**
     * event_admin_class::cmd_save_group_table()
     * 
     * @return
     */
    function cmd_save_group_table() {
        foreach ($_POST['FORM'] as $key => $row) {
            update_table(TBL_CMS_CALENDAR_GROUPS, 'id', $key, $row);
        }
        $this->ej();
    }

    /**
     * event_admin_class::cmd_add_group()
     * 
     * @return
     */
    function cmd_add_group() {
        $id = insert_table(TBL_CMS_CALENDAR_GROUPS, $_POST['FORM']);
        $this->db->query("INSERT INTO " . TBL_CMS_CALENDAR_PERM . " SET perm_did=" . $id . ", perm_group_id=1000");
        $this->TCR->tb();
    }

    /**
     * event_admin_class::cmd_del_group()
     * 
     * @return
     */
    function cmd_del_group() {
        $id = (int)$_GET['ident'];
        if ($id <= 1) {
            $this->msge('invalid calendar');
            $this->ej();
        }
        $DEL_GROUP = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR_GROUPS . " WHERE id=" . $id);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CALENDAR . " WHERE group_id=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->delete_item($row['id']); //einzel loeschung wegen attached files
        }
        if (get_data_count(TBL_CMS_CALENDAR, 'group_id', "group_id=" . $id) == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR_GROUPS . " WHERE id>1 AND id=" . $id);
            $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR_PERM . " WHERE perm_did=" . $id);
            $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR_GCON . " WHERE g_id=" . $id);
            $this->LOGCLASS->addLog('DELETE', 'Total calendar [' . $DEL_GROUP['groupname'] . '] deleted.');
            $this->msg('{LBL_DELETED}');
        }
        else {
            $this->msge('Has still items. Calendar not empty.');
        }
        $this->ej();
    }

    /**
     * event_admin_class::cmd_edit_group()
     * 
     * @return
     */
    function cmd_edit_group() {
        global $LNGOBJ;
        $CG = $this->db->query_first("SELECT *	FROM " . TBL_CMS_CALENDAR_GROUPS . "	WHERE id=" . $_GET['id'] . "	ORDER BY groupname");
        $this->EVENT['caltheme'] = $this->EVENT['group'] = $CG;
        $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR_GCON . " WHERE lang_id=" . $this->langid . " AND g_id='" . $_GET['id'] . "' LIMIT 1");
        $this->EVENT['group_content'] = $FORM_CON;
        $this->EVENT['langsel'] = $LNGOBJ->build_lang_select();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $perm_checkoxes .= '<div class="checkbox"><label>
                <input type="checkbox" ' . ((get_data_count(TBL_CMS_CALENDAR_PERM, 'perm_did', "perm_did=" . $_GET['id'] . " AND perm_group_id=" . $row['id']) >
                0) ? 'checked' : '') . ' name="CUSTGROUP[' . $row['id'] . ']" value="' . $row['id'] . '"> ' . $row['groupname'] . '</label></div>';
        }
        $this->EVENT['perm_checkoxes'] = $perm_checkoxes;
    }

    /**
     * event_admin_class::set_permissions()
     * 
     * @param mixed $custgroup
     * @param mixed $tid
     * @param mixed $FORM_CON
     * @param mixed $conid
     * @param mixed $FORM
     * @return
     */
    function set_permissions($custgroup, $tid, $FORM_CON, $conid, $FORM) {
        $tid = (int)$tid;
        $conid = (int)$conid;
        # Permissions setzen
        $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR_PERM . " WHERE perm_did=" . $tid);
        if (is_array($custgroup)) {
            foreach ($custgroup as $key => $group_id) {
                $this->db->query("INSERT INTO " . TBL_CMS_CALENDAR_PERM . " SET perm_did=" . $tid . ", perm_group_id=" . $group_id);
            }
        }
        update_table(TBL_CMS_CALENDAR_GROUPS, 'id', $tid, $FORM);
        $CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR_GCON . " WHERE lang_id=" . $this->langid . " AND g_id=" . $tid);
        if ($CON['id'] > 0) {
            update_table(TBL_CMS_CALENDAR_GCON, 'id', $CON['id'], $FORM_CON);
        }
        else {
            $FORM_CON['g_id'] = $tid;
            insert_table(TBL_CMS_CALENDAR_GCON, $FORM_CON);
        }
    }

    /**
     * event_admin_class::cmd_setallperm()
     * 
     * @return
     */
    function cmd_setallperm() {
        $this->set_permissions($_POST['CUSTGROUP'], $_POST['tid'], $_POST['FORM_CON'], $_POST['conid'], $_POST['FORM']);
        $this->ej();
    }


    /**
     * event_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $themes = $this->load_all_themes();
        $events = array(
            'themes' => $themes,
            'first_event' => $this->get_first_event(),
            'last_event' => $this->get_last_event());
        $this->smarty->assign('event', $events);
        return $this->load_templates_for_plugin_by_modident('event', $params);
    }

    /**
     * event_admin_class::load_cal_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_cal_homepage_integration($params) {
        return $this->load_templates_for_plugin_by_modident('event', $params);
    }

    /**
     * event_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration($params) {
        $this->save_plugin_integration($params, 'event');
    }

    /**
     * event_admin_class::save_cal_homepage_integration()
     * 
     * @param mixed $params
     * @return void
     */
    function save_cal_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = (int)$params['FORM']['tplid'];
        $R = array('description' => 'event_cal');
        if ($id > 0) {
            $R = $this->dao->load_template($id);
        }
        $upt = array(
            'tm_modident' => 'event',
            'tm_content' => '{TMPL_CAL_' . $cont_matrix_id . '}',
            'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, self::real_escape($upt));
        $tm = dao_class::get_data_first(TBL_CMS_TEMPMATRIX, array('id' => $cont_matrix_id));
        dao_class::update_table(TBL_CMS_TEMPLATES, array('php' => 'modules/event/events.inc', 'module_id' => 'event'), array('id' => (int)$tm['tm_tid']));
        $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . (int)$tm['tm_tid'] . "' WHERE config_name='events_relid_page'");
    }

}
