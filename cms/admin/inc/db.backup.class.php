<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class db_backup_class extends keimeno_class {

    var $BACKUPCMS = array();
    /**
     * db_backup_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * db_backup_class::cmd_backuphomepage()
     * 
     * @return
     */
    function cmd_backuphomepage() {
        try {
            $tar = "kb_" . date('Y-m-d_H_i_s') . "_" . gen_sid(6) . ".tar.gz";
            exec("cd db_backup;tar cvfz " . $tar . " ../../ --exclude='../../admin/db_backup'");
            $this->LOGCLASS->addLog('BACKUP', 'CMS backup created');
            self::msg("done");
        }
        catch (Exception $e) {
            self::msge($e->getMessage());
        }
        $this->ej();
    }

    /**
     * db_backup_class::cmd_importcmsbackup()
     * 
     * @return
     */
    function cmd_importcmsbackup() {
        if (file_exists('./db_backup/' . $_GET['file'])) {
            exec("tar -C ../ -xvvzf ./db_backup/" . $_GET['file']);
        }
        keimeno_class::msg("done");
        $this->TCR->tb();
    }

    /**
     * db_backup_class::cmd_createbackup()
     * 
     * @return
     */
    function cmd_createbackup() {
        try {
            $this->backup(CMS_ROOT . 'admin/db_backup/mysqlbackup_' . date('Y-m-d_H_i_s') . '_' . gen_sid(6) . '.sql');
            $this->LOGCLASS->addLog('BACKUP', 'SQL backup created');
            self::msg("done");
        }
        catch (Exception $e) {
            self::msge($e->getMessage());
        }
        $this->ej();
    }

    /**
     * db_backup_class::cmd_a_delback()
     * 
     * @return
     */
    function cmd_a_delback() {
        delete_file(CMS_ROOT . 'admin/db_backup/' . $_GET['ident']);
        $this->LOGCLASS->addLog('DELETE', $_GET['ident'] . ' backup file deleted');
        keimeno_class::msg("done");
        $this->ej();
    }

    /**
     * db_backup_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('BACKUPCMS', $this->BACKUPCMS);
    }

    /**
     * db_backup_class::cmd_initbackup()
     * 
     * @return
     */
    function cmd_initbackup() {
        $this->clean();
        $k = 0;
        if (!is_dir('./db_backup'))
            mkdir('./db_backup');
        $folder = './db_backup/';
        if ($dirhandle = opendir($folder)) {
            while (false !== ($oldcachefile = readdir($dirhandle))) {
                $CacheDirOldFilesAge[$oldcachefile] = fileatime($folder . $oldcachefile);
                if ($CacheDirOldFilesAge[$oldcachefile] == 0) {
                    $CacheDirOldFilesAge[$oldcachefile] = filemtime($folder . $oldcachefile);
                }
            }
        }
        arsort($CacheDirOldFilesAge);
        foreach ($CacheDirOldFilesAge as $file => $filedate) {
            if ($file != '.' && $file != '..') {
                $k++;
                $filesize = filesize('./db_backup/' . $file);
                $tfz += $filesize;
                $this->BACKUPCMS['files'][] = array(
                    'file' => $file,
                    'num' => $k,
                    'filesize' => human_file_size($filesize),
                    'date' => date("d.m.Y H:i:s", filemtime('./db_backup/' . $file)),
                    'delicon' => kf::gen_del_icon($file, true, 'a_delback'),
                    'ismysql' => strstr($file, 'mysql'));
            }
        }
        $this->BACKUPCMS['usedspace'] = human_file_size($tfz);
    }

    /**
     * db_backup_class::backup()
     * 
     * @param mixed $fname
     * @return
     */
    function backup($fname) {
        @unlink($fname);
        $f = fopen($fname, "w");
        $tables = $this->db->query("SHOW TABLE STATUS FROM " . $this->db->database . ";");
        while ($cells = mysqli_fetch_array($tables)) {
            if (strstr($cells[0], TBL_CMS_PREFIX)) {
                $table = $cells[0];
                fwrite($f, "DROP TABLE IF EXISTS `$table`;\n");
                $res = mysqli_query($this->db->link_id, "SHOW CREATE TABLE `$table`");
                if ($res) {
                    $create = mysqli_fetch_array($res);
                    $create[1] .= ";";
                    $line = str_replace("\n", "", $create[1]);
                    fwrite($f, $line . "\n");
                    $data = mysqli_query($this->db->link_id, "SELECT * FROM `$table`");
                    $num = mysqli_num_fields($data);
                    while ($row = mysqli_fetch_array($data)) {
                        $line = "INSERT INTO `$table` VALUES(";
                        for ($i = 1; $i <= $num; $i++) {
                            $line .= "'" . $this->db->real_escape_string($row[$i - 1]) . "', ";
                        }
                        $line = substr($line, 0, -2);
                        fwrite($f, $line . ");\n");
                    }
                }
            }
        }
        fclose($f);
        @unlink($fname . '.gz');
        exec("gzip " . $fname);
    }

    /**
     * db_backup_class::import()
     * 
     * @param mixed $fname
     * @return
     */
    function import($fname) {
        exec('gunzip ./db_backup/' . $fname);
        $nfname = str_replace('.gz', '', $fname);
        $sql = implode("", file('./db_backup/' . $nfname));
        if ($sql != "") {
            $sql_lines = explode("\n", $sql);
            foreach ($sql_lines as $key => $sql_exec)
                if ($sql_exec != "")
                    mysqli_query($this->db->link_id, $sql_exec);
        }
        exec("gzip ./db_backup/" . $nfname);
    }

    /**
     * db_backup_class::clean()
     * 
     * @param integer $max_files
     * @return
     */
    function clean($max_files = 10) {
        $folder = './db_backup/';
        if (!is_dir($folder))
            mkdir($folder, 0750);
        $CacheDirOldFilesAge = array();
        if ($dirhandle = opendir($folder)) {
            while (false !== ($oldcachefile = readdir($dirhandle))) {
                $CacheDirOldFilesAge[$oldcachefile] = fileatime($folder . $oldcachefile);
                if ($CacheDirOldFilesAge[$oldcachefile] == 0) {
                    $CacheDirOldFilesAge[$oldcachefile] = filemtime($folder . $oldcachefile);
                }
            }
        }
        asort($CacheDirOldFilesAge);
        $TotalCachedFiles = count($CacheDirOldFilesAge);
        foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
            if ($TotalCachedFiles > $max_files) {
                $TotalCachedFiles--;
                if ($oldcachefile != '.' && $oldcachefile != '..' && !is_dir($folder . $oldcachefile))
                    unlink($folder . $oldcachefile);
            }
            else {
                break;
            }
        }
        clearstatcache();
    }

}
