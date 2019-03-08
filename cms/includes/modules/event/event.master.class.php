<?PHP

/**
 * @package    calendar
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
DEFINE('EVENT_PATH', 'file_data/events/');
class event_master_class extends modules_class {

    /**
     * event_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * event_master_class::genCalGroupImplement()
     * 
     * @param mixed $id
     * @return
     */
    function genCalGroupImplement($id) {
        return '{TMPL_CALTHEME_' . $id . '}';
    }

    /**
     * event_master_class::setApprove()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function setApprove($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_CALENDAR . " SET approval='" . (($value == 1) ? 1 : 0) . "' WHERE id='" . (int)$id . "' LIMIT 1");
    }

    /**
     * event_master_class::get_first_event()
     * 
     * @return
     */
    function get_first_event($no_history = false) {
        $arr = $this->db->query_first("SELECT *,YEAR(ndate) AS JAHR FROM " . TBL_CMS_CALENDAR . " WHERE 1 " . (($no_history == true) ? " AND YEAR(ndate)>=" . date('Y') :
            "") . " ORDER BY ndate ASC LIMIT 1");
        return $arr;
    }

    /**
     * event_master_class::get_last_event()
     * 
     * @return
     */
    function get_last_event() {
        $arr = $this->db->query_first("SELECT *,YEAR(ndate) AS JAHR FROM " . TBL_CMS_CALENDAR . " ORDER BY ndate DESC LIMIT 1");
        return $arr;
    }

    /**
     * event_master_class::get_event()
     * 
     * @param mixed $id
     * @return
     */
    function get_event($id) {
        return $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR . " WHERE id=" . (int)$id . " LIMIT 1");        
    }

    /**
     * event_master_class::load_all_themes()
     * 
     * @return
     */
    function load_all_themes() {
        $themes = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CALENDAR_GROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $themes[] = $row;
        }
        return $themes;
    }

    /**
     * event_master_class::del_icon()
     * 
     * @param mixed $id
     * @return
     */
    function del_icon($id) {
        $id = (int)$id;
        $nfile = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDAR . " WHERE id=" . $id);
        if (delete_file(CMS_ROOT . EVENT_PATH . $nfile['c_icon'])) {
            $this->db->query("UPDATE " . TBL_CMS_CALENDAR . " SET c_icon='' WHERE id=" . $id);
            $this->LOGCLASS->addLog('DELETE', 'Calendar item iconfile' . $nfile['c_icon'] . ' from ID:' . $id);
            return true;
        }
        return false;
    }


    /**
     * event_master_class::delete_item()
     * 
     * @param mixed $id
     * @return
     */
    function delete_item($id) {
        $id = (int)$id;
        $this->del_icon($id);
        $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_CALENDAR_CONTENT . " WHERE nid=" . $id);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CALENDARFILES . " WHERE f_nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            delete_file(CMS_ROOT . EVENT_PATH . $row['f_file']);
        }
        $this->LOGCLASS->addLog('DELETE', 'Calendar item [' . $id . '] deleted.');
    }

    /**
     * event_master_class::del_afile()
     * 
     * @param mixed $id
     * @return
     */
    function del_afile($id) {
        $id = (int)$id;
        $nfile = $this->db->query_first("SELECT * FROM " . TBL_CMS_CALENDARFILES . " WHERE id=" . $id);
        if (delete_file(CMS_ROOT . EVENT_PATH . $nfile['f_file'])) {
            $this->db->query("DELETE FROM " . TBL_CMS_CALENDARFILES . " WHERE id=" . $id);
            $this->LOGCLASS->addLog('DELETE', 'Calendar item file' . $nfile['f_file'] . ' from ID:' . $id);
            return true;
        }
        return false;
    }

    /**
     * event_master_class::set_filelist()
     * 
     * @param mixed $id
     * @return
     */
    function set_filelist($id) {
        global $GRAPHIC_FUNC;
        $filelist = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CALENDARFILES . " WHERE f_nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['humanfilesize'] = human_file_size($row['f_size']);
            $row['uploadtime'] = date("d.m.Y H:i:s", $row['f_inserttime']);
            $row['thumbnail'] = '';
            $row['resu'] = '';
            if (($row['f_ext'] == 'jpg' || $row['f_ext'] == 'gif' || $row['f_ext'] == 'png' || $row['f_ext'] == 'jpeg')) {
                list($width_px, $height_px) = getimagesize(CMS_ROOT . EVENT_PATH . $row['f_file']);
                $row['resu'] = $width_px . 'x' . $height_px;
            }
            if (ISADMIN == 1) {
                $row['icon_del'] = kf::gen_del_icon($row['id'], false, 'del_file');
                if ($row['resu'] != "")
                    $row['thumbnail'] = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../' . EVENT_PATH . $row['f_file'], 30, 30, 'admin/' . CACHE, true, 'resize');
            }
            else {
                if ($row['resu'] != "")
                    $row['thumbnail'] = PATH_CMS . CACHE . $GRAPHIC_FUNC->makeThumb('./' . EVENT_PATH . $row['f_file'], 30, 30, './' . CACHE, true, 'resize');
            }
            $filelist[] = $row;
        }
        return $filelist;
    }

    /**
     * event_master_class::inform_admins()
     * 
     * @param mixed $type
     * @return
     */
    function inform_admins($type) {
        $admin_email_text = 'Hello,' . "\n\n";
        $allowed = array(
            'title' => 'Titel',
            'EID' => 'ID',
            'c_kid' => 'Author ID',
            'date' => 'Datum',
            'c_author' => 'Author');
        $this->event['title'] = $this->event['FORM_CON']['title'];
        $this->event['c_author'] = ($this->event['c_author'] == "") ? 'unkown' : $this->event['c_author'];
        foreach ($this->event as $key => $value) {
            if (array_key_exists($key, $allowed))
                $admin_email_text .= $allowed[$key] . ': ' . $value . "\n";
        }
        if (count($this->event['filelist']) > 0) {
            $admin_email_text .= "\nAttachments:\n";
            foreach ($this->event['filelist'] as $key => $value) {
                $admin_email_text .= ($key + 1) . '. file: ' . $value['f_file'] . " - " . $value['humanfilesize'] . "\n";
            }
        }
        if (ISADMIN == 0) {
            if ($type == "INSERT") {
                #  send_admin_mail('{LBL_NEWCALITEM} ID:' . $this->event['EID'] . ' "' . $this->event['title'] . '" {LBL_OF} ' . $this->event['c_author'], $admin_email_text);
                $smarty_arr = array('mail' => array('subject' => '{LBL_NEWCALITEM} ID:' . $this->event['EID'] . ' "' . $this->event['title'] . '" {LBL_OF} ' . $this->event['c_author'],
                            'content' => $admin_email_text));
                send_admin_mail(900, $smarty_arr); #general mail template
            }
            if ($type == "UPDATE") {
                #send_admin_mail('{LBL_UPDCALITEM} ID:' . $this->event['EID'] . ' "' . $this->event['title'] . '" {LBL_OF} ' . $this->event['c_author'], $admin_email_text);
                $smarty_arr = array('mail' => array('subject' => '{LBL_UPDCALITEM} ID:' . $this->event['EID'] . ' "' . $this->event['title'] . '" {LBL_OF} ' . $this->event['c_author'],
                            'content' => $admin_email_text));
                send_admin_mail(900, $smarty_arr); #general mail template
            }
        }
    }
}
