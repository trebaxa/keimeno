<?php

/**
 * @package    downloadcenter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


DEFINE('DOWNCENTER', 'downloadcenter/');

/**
 * gen_download_url()
 * 
 * @param mixed $template_id
 * @return
 */
function gen_download_url($template_id) {
    return '{DOWNLOAD_TPL_' . $template_id . '}';
}

class downc_class extends keimeno_class {


    /**
     * downc_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

    }

    /**
     * downc_class::genDownloadLink()
     * 
     * @param mixed $id
     * @param mixed $filename
     * @return
     */
    function genDownloadLink($id, $filename) {
        $TPL = $this->db->query_first("SELECT id FROM " . TBL_CMS_TEMPLATES . " WHERE modident='downloadcenter' LIMIT 1");
        return $_SERVER['PHP_SELF'] . '?' . htmlentities('page=' . $TPL['id'] . '&cmd=dcdownload&id=' . $id);
    }


    /**
     * get_value_from_table()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param string $where
     * @return
     */
    function get_value_from_table($table, $column, $where = '') {
        $result = $this->db->query("SELECT $column FROM $table WHERE $where LIMIT 1");
        while ($row = $this->db->fetch_array($result)) {
            return $row[0];
        }
    }

    /**
     * downc_class::cmd_dcdownload()
     * 
     * @return
     */
    function cmd_dcdownload() {
        $id = (int)$_GET['id'];
        $FILE_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE id='" . (int)$id . "' LIMIT 1");
        if ($FILE_OBJ['approval'] == 0) {
            $this->msge('Access denied');
            header('location: ' . PATH_CMS . 'index.html');
            exit;
        }
        if ($this->get_value_from_table(TBL_CMS_DC_LOG, "dcid", "dcid=" . $id . " AND dcdate='" . date("Y-m-d") . "'") > 0) {
            $this->db->query("UPDATE " . TBL_CMS_DC_LOG . " SET hits=hits+1 WHERE dcid=" . $id . " AND dcdate='" . date("Y-m-d") . "' LIMIT 1");
        }
        else {
            $this->db->query("INSERT INTO " . TBL_CMS_DC_LOG . " SET hits=1,dcdate='" . date("Y-m-d") . "', dcid=" . $id);
        }
        $this->direct_download(FILE_ROOT . $FILE_OBJ['file']);
    }

    /**
     * downc_class::cmd_show_downloads()
     * 
     * @return
     */
    function cmd_show_downloads() {
        $smarty_values = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE approval='1' ORDER BY morder");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['link'] = $this->genDownloadLink($row['id'], basename($row['file']));
            $row['fileexists'] = is_file(FILE_ROOT . $row['file']);
            $row['title'] = ($row['title'] == "") ? basename($row['file']) : $row['title'];
            if (file_exists(FILE_ROOT . $row['file']))
                $row['filesize'] = human_file_size(filesize(FILE_ROOT . $row['file']));
            else
                $row['filesize'] = human_file_size(0);
            if (is_file(PICS_GAL_ROOT . $row['icon'])) {
                list($width, $height) = calcOptWidthHeight(PICS_GAL_ROOT . $row['icon'], $this->gbl_config['opt_boxthumb_width'], 100);
            }
            $folder = '.' . str_replace(basename($row['icon']), "", $row['icon']);
            $row['icon'] = gen_thumb_picture(basename($row['icon']), $this->gbl_config['opt_boxthumb_width'], $height, "center", 0, '', '', '_self', $folder, '1', '1');
            if ($row['fileexists'] == true)
                $smarty_values[] = $row;
        }
        $this->smarty->assign('downloads', $smarty_values);
    }

    /**
     * downc_class::aftersmartycompile()
     * 
     * @param mixed $params
     * @return
     */
    function aftersmartycompile($params) {
        $html = $params['html'];
        if (strstr($html, '{DOWNLOAD_TPL_')) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_DOWNCENTER);
            while ($row = $this->db->fetch_array_names($result)) {
                $html = fill_temp(gen_download_url($row['id']), $this->genDownloadLink($row['id'], basename($row['file'])), $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

}

?>