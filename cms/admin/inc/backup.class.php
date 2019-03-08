<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



define('TBL_CMS_TPLBACKUP', TBL_CMS_PREFIX . 'backup');

class backup_class extends keimeno_class {

    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    function add($content, $type, $tid = 0, $langid = 1, $file = "") {
        $arr = array(
            'b_content' => $this->db->real_escape_string($content),
            'b_type' => $type,
            'b_file' => trim(str_replace(CMS_ROOT, "", $file)),
            'b_tid' => (int)$tid,
            'b_langid' => (int)$langid,
            'b_mid' => (int)$_SESSION['mitarbeiter'],
            'b_employee' => $this->db->real_escape_string($_SESSION['admin_obj']['mitarbeiter_name']),
            'b_time' => time());
        insert_table(TBL_CMS_TPLBACKUP, $arr);
        $result = $this->db->query_first("SELECT b_time FROM " . TBL_CMS_TPLBACKUP . " ORDER BY b_time DESC LIMIT 5000,1");
        if ($result['id'] > 0)
            $this->db->query("DELETE FROM " . TBL_CMS_TPLBACKUP . " WHERE b_time<=" . intval($result['b_time']));
    }

    function load_backups($tid) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLBACKUP . " WHERE b_tid=" . $tid . " AND b_type='SYSTPL' ORDER BY b_time DESC LIMIT 100");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['date'] = date('d.m.Y H:i:s', $row['b_time']);
            $arr[] = $row;
        }
        return (array )$arr;
    }

    function load_backups_by_type($type) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLBACKUP . " WHERE b_type='" . $type . "' ORDER BY b_time DESC LIMIT 100");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['date'] = date('d.m.Y H:i:s', $row['b_time']);
            $arr[] = $row;
        }
        return (array )$arr;
    }

    function get_backup_by_id($id) {
        return $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLBACKUP . " WHERE id=" . (int)$id);
    }

    function restore($id) {
        $back = $this->get_backup_by_id($id);
        $ORG = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . (int)$back['b_tid'] . " AND lang_id=" . (int)$back['b_langid']);
        $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string($back['b_content']) . "' WHERE id=" . $ORG['id']);
    }

    function restore_css($id) {
        $back = $this->get_backup_by_id($id);
        file_put_contents(CMS_ROOT . $back['b_file'], $back['b_content']);
        $this->LOGCLASS->addLog('MODIFY', $back['b_file'] . ' stylesheet restore from ' . date('Y-m-d H:i:s', $back['b_time']));
    }
}

?>