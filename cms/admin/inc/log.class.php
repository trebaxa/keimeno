<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class log_class {
    var $max_lines = 100;
    var $employee = "";
    var $table = "";
    var $log = "";
    var $actions = array(
        'INSERT' => 'insert',
        'MODIFY' => 'modified',
        'CLONE' => 'cloned',
        'EBAYACTION' => 'eBay action',
        'DELETE' => 'deleted',
        'DOWNLOAD' => 'download',
        'LOGGEDIN' => 'logged in',
        'LOGGEDIN_FAIL' => 'login failure',
        'IMPORT' => 'imported',
        'UPLOAD' => 'uploaded',
        'MOVE' => 'moved',
        'UPDATE' => 'updated',
        'BACKUP' => 'backup',
        'WORM_PROTECTOR' => 'hacking by worm',
        'FORM_PROTECTOR' => 'hacking within formular',
        'INJECTION_PROTECTOR' => 'illegal word in $_POST etc.',
        'INVALID_TOKEN' => 'hacking: invalid token',
        'ACCESS_DENIED' => 'access denied',
        'INFO' => 'info',
        'RESET' => 'reseted',
        'DOUBLE_USE' => 'double used',
        'REGISTER' => 'registered',
        'SENDMAIL' => 'send mail',
        'ENTER_PROTECTED_AREA' => 'entered protected area',
        'FORM_FAILURE' => 'formular failure',
        'FAILURE' => 'failure',
        'ILLEGAL' => 'illegal call',
        'LOGGEDOUT' => 'logged out');


    /**
     * log_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $kdb, $gbl_config;
        $this->db = $kdb;
        if (defined('ISADMIN') && ISADMIN == 1) {
            $this->employee = $_SESSION['mitarbeiter_name'];
        }
        else {
            $this->employee = 'VISITOR';
        }
        $this->gbl_config = $gbl_config;
        $this->LOG['log_time'] = "";
        $this->LOG['log_person'] = "";
        $this->LOG['log_action'] = "";
        $this->LOG['log_msg'] = "";
        $this->LOG['log_ip'] = "";
        $this->LOG['log_browser'] = "";
        $this->LOG['log_date'] = "";
    }

    /**
     * log_class::add_to_log()
     * 
     * @param mixed $FORM
     * @return
     */
    function add_to_log($FORM) {
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $wert) {
                if ($sqlquery)
                    $sqlquery .= ', ';
                $sqlquery .= "$key='$wert'";
            }
            $sql = "INSERT INTO " . TBL_CMS_LOG . " SET " . $sqlquery;
            if ($sqlquery)
                $this->db->query($sql);
        }
    }


    /**
     * log_class::format_string_to_xls()
     * 
     * @param mixed $input
     * @return
     */
    function format_string_to_xls($input) {
        $input = str_replace('"' . "\t", '"' . "\\t", $input);
        $input = str_replace("\r\n", "\\r\\n", $input);
        $rep = array("|", ";");
        $input = str_replace($rep, "", $input);
        if (is_numeric($input))
            $input = str_replace('.', ',', $input);
        if (strtotime($input) && count(explode("-", $input)) == 3)
            $input = date("d.m.Y", strtotime($input)); // DATE
        else
            if (is_numeric($input) && strlen($input) == 10)
                $input = date("d.m.Y H:i:s", $input); // TIME INT
        return trim(strip_tags($input));
    }

    /**
     * log_class::getIpAddress()
     * 
     * @return
     */
    function getIpAddress() {
        return self::anonymizing_ip((empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) :
            $_SERVER['HTTP_CLIENT_IP']));
    }

    /**
     * log_class::anonymizing_ip()
     * 
     * @param mixed $ip
     * @return
     */
    public static function anonymizing_ip($ip) {
        if (strpos($ip, ".") == true) {
            return preg_replace('#(?:\.\d+){1}$#', '.0', $ip);
        }
        else {
            return preg_replace('~[0-9]*:[0-9]+$~', 'XXXX:XXXX', $ip);
        }
    }

    /**
     * log_class::addLog()
     * 
     * @param mixed $action_index
     * @param mixed $msg
     * @param bool $hackattack
     * @return
     */
    function addLog($action_index, $msg, $hackattack = false) {
        $this->LOG['log_date'] = date('Y-m-d');
        $this->LOG['log_time'] = time();
        $this->LOG['log_msg'] = $msg;
        $this->LOG['log_browser'] = $_SERVER['HTTP_USER_AGENT'];
        if (!defined('ISADMIN') && $hackattack === false) {
            $this->LOG['log_ip'] = (($this->gbl_config['log_use_ip'] == 1) ? $this->getIpAddress() : keimeno_class::anonymizing_ip($this->getIpAddress()));
        }
        else
            $this->LOG['log_ip'] = $this->getIpAddress();
        $this->LOG['log_action'] = $action_index;
        $this->LOG['log_person'] = $this->employee;
        foreach ($this->LOG as $key => $wert)
            $this->LOG[$key] = $this->db->real_escape_string($this->LOG[$key]);
        if ($this->employee != "support")
            $this->add_to_log($this->LOG);
        #$this->db->query("DELETE FROM ".TBL_CMS_LOG." ORDER BY log_time DESC LIMIT 100");
    }

    /**
     * log_class::make_xls_format()
     * 
     * @param mixed $fieldlist
     * @param mixed $fieldnames
     * @param mixed $table
     * @param mixed $where
     * @param mixed $orderby
     * @param mixed $filename
     * @return
     */
    function make_xls_format($fieldlist, $fieldnames, $table, $where, $orderby, $filename) {
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: inline; filename=\"$filename.xls\"");
        $fieldnames = explode(",", $fieldnames);
        foreach ($fieldnames as $key => $wert) {
            echo '"' . $wert . '"' . "\t";
        }
        echo "\r\n";
        $result = $this->db->query("SELECT " . $fieldlist . " FROM " . $table . " WHERE " . $where . " ORDER BY " . $orderby);
        $_FIELDS = explode(",", $fieldlist);
        while ($data = $this->db->fetch_array_names($result)) {
            foreach ($_FIELDS as $key) {
                if (strstr($key, ".")) {
                    $parts = explode(".", $key);
                    $key = $parts[1];
                }
                echo '"' . $this->format_string_to_xls($data[$key]) . '"' . "\t";
            }
            echo "\r\n";
        }
        exit;
    }

    /**
     * log_class::clean_log()
     * 
     * @return
     */
    function clean_log() {
        $time = strtotime("-4 week", time());
        $date = date("Y-m-d", $time);
        $this->db->query("DELETE FROM " . TBL_CMS_LOG . " WHERE log_date<'" . $date . "'");
    }

    /**
     * log_class::genXLS()
     * 
     * @return
     */
    function genXLS() {
        unset($this->LOG['log_ip']);
        foreach ($this->LOG as $key => $value) {
            $fieldnames .= ($fieldnames == "") ? $key : ',' . $key;
        }
        $this->make_xls_format($fieldnames, $fieldnames, TBL_CMS_LOG, "1", 'log_time', 'log_file');
    }


    /**
     * log_class::genTable()
     * 
     * @return
     */
    function genTable() {
        unset($sql);
        ksort($this->actions);
        $table = "";
        $sql .= (($_GET['PERSON'] != "") ? "AND log_person='" . $_GET['PERSON'] . "'" : '');
        $sql .= (($_GET['ACTION'] != "") ? "AND log_action='" . $_GET['ACTION'] . "'" : '');
        $sql .= (($_GET['IP'] != "") ? "AND log_ip='" . $_GET['IP'] . "'" : '');
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LOG . " WHERE (log_time>0) " . $sql . " ORDER BY log_time DESC LIMIT 3000");
        $table .= '<thead><tr>
			<th></th>			
			<th>Time</th>
			<th>IP</th>
			<th>User</th>
			<th>Action</th>
			<th>Info</th>			
			<th>User Agent</th>
		</tr></thead>';
        if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array_names($result)) {

                $z++;
                $table .= '<tr">
		<td>' . $z . '</td>
			<td >' . date("d.m.Y H:i:s", $row['log_time']) . '</td>
			<td ><a href="' . $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&IP=' . $row['log_ip'] . '&PERSON=&ACTION=' . $_GET['ACTION'] . '&epage=' . $_REQUEST['epage'] .
                    '">' . $row['log_ip'] . '</a></td>			
			<td >' . $row['log_person'] . '</td>
			<td >' . $row['log_action'] . '</td>
			<td >' . $row['log_msg'] . '</td>			
			<td ><small>' . $row['log_browser'] . '</small></td>
		</tr>';
            }
            $content = '<div class="btn-group">
            	<a class="btn btn-default" href="' . $_SERVER['PHP_SELF'] . '?aktion=log_download&epage=' . $_REQUEST['epage'] . '">Download Log</a>
                <a class="ajax-link btn btn-default" href="' . $_SERVER['PHP_SELF'] . '?aktion=alogtab&epage=' . $_REQUEST['epage'] . '">Filter reset</a>
                </div>
<div class="row">
    <div class="col-md-6">            
      <div class="form-group">    
	   <label>Person:</label>
	   <select class="form-control"  onChange="location.href=this.options[this.selectedIndex].value">
			<option value=' . $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&ACTION=' . $key . '">- - -</option>';
            $PERSONS = $this->db->query("SELECT log_person FROM " . TBL_CMS_LOG . " GROUP BY log_person ORDER BY log_person");
            while ($row = $this->db->fetch_array_names($PERSONS)) {
                $content .= '<option ' . (($_GET['PERSON'] == $row['log_person']) ? 'selected' : '') . ' value="' . $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&IP=' .
                    $row['log_ip'] . '&ACTION=' . $_GET['ACTION'] . '&PERSON=' . $row['log_person'] . '&epage=' . $_REQUEST['epage'] . '">' . $row['log_person'] . '</option>';
            }

            $content .= '</select>
        </div>    
        <div class="form-group">
	   <label>ACTION :</label>
       <select class="form-control"  onChange="location.href=this.options[this.selectedIndex].value">
			<option value="' . $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&PERSON=' . $_GET['PERSON'] . '&epage=' . $_REQUEST['epage'] . '">- - -</option>';
            foreach ($this->actions as $key => $value) {
                $content .= '<option ' . (($_GET['ACTION'] == $key) ? 'selected' : '') . ' value="' . $_SERVER['PHP_SELF'] . '?aktion=' . $_GET['aktion'] . '&IP=' . $row['log_ip'] .
                    '&PERSON=' . $_GET['PERSON'] . '&ACTION=' . $key . '&epage=' . $_REQUEST['epage'] . '">' . $key . ' | ' . $value . '</option>';
            }
            $content .= '</select>
        </div>    
	   IP : ' . $_GET['IP'] . '
    </div>    
</div>
	<table class="table table-striped table-hover">' . $table . '</table>

	';
            return $content;
        }
        else
            return '<table class="table table-striped table-hover">' . $table . '</table>';
    }


}
