<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

class kdb {

    var $database = "";

    protected static $_linkid = 0;
    var $query_id = 0;
    var $record = array();

    var $errdesc = "";
    var $errno = 0;
    var $show_error = 1;

    var $server = "";
    var $user = "";
    var $password = "";
    var $row_index;

    var $appname = "";
    var $query_counter = 0;
    var $query_hist = "";
    var $max_query_duration = 0.00;


    /**
     * kdb::__construct()
     * 
     * @return void
     */
    function __construct() {

    }

    /**
     * kdb::get_micro_time()
     * 
     * @return
     */
    private static function get_micro_time() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * kdb::set_link_id()
     * 
     * @param mixed $linkid
     * @return void
     */
    public static function set_link_id($linkid) {
        static::$_linkid = $linkid;
    }

    /**
     * kdb::get_link_id()
     * 
     * @return
     */
    public static function get_link_id() {
        return static::$_linkid;
    }

    /**
     * kdb::connect()
     * 
     * @param bool $utf8
     * @return
     */
    function connect($utf8 = TRUE) {
        if (!isset($_SESSION['DEBUG']))
            $_SESSION['DEBUG'] = 0;
        if (0 == $this->link_id) {
            $this->link_id = @mysqli_connect($this->server, $this->user, $this->password, $this->database);

            if (!$this->link_id) {
                $this->error("Link-ID == false, connect failed. Wrong Password or User or Database. Please check. " . mysqli_connect_error());
            }
            else {
                if ($utf8 === TRUE) {
                    $this->query("SET CHARACTER SET utf8");
                }
                $this->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
            }
            self::set_link_id($this->link_id);
        }
    }

    /**
     * kdb::disconnect()
     * 
     * @return
     */
    function disconnect() {
        if (!$this->link_id) {
            mysqli_close($this->link_id);
        }
    }


    /**
     * kdb::get_err()
     * 
     * @return string
     */
    function get_err() {
        $this->error = mysqli_error($this->link_id);
        return $this->error;
    }

    /**
     * kdb::geterrno()
     * 
     * @return string
     */
    function geterrno() {
        $this->errno = mysqli_errno($this->link_id);
        return $this->errno;
    }

    /**
     * kdb::data_seek()
     * 
     * @param mixed $result
     * @param integer $position
     * @return
     */
    function data_seek($result, $position = 0) {
        mysqli_data_seek($result, $position);
    }

    /**
     * kdb::truncate_table()
     * 
     * @param mixed $table
     * @return
     */
    function truncate_table($table) {
        $this->query("TRUNCATE TABLE " . $table);
    }

    /**
     * kdb::real_escape_string()
     * 
     * @param mixed $str
     * @return
     */

    public static function real_escape_string($str) {
        if (self::get_link_id())
            $str = mysqli_real_escape_string(self::get_link_id(), $str);
        return $str;
    }

    /**
     * kdb::query()
     * 
     * @param mixed $query_string
     * @param integer $admin
     * @return
     */
    function query($query_string, $admin = 0) {
        $this->query_counter += 1;
        $startt = self::get_micro_time();
        if ($admin == 1)
            echo $query_string . '<br>';
        $this->query_id = mysqli_query($this->link_id, $query_string);
        $dauer = self::get_micro_time() - $startt;
        if ($dauer > $this->max_query_duration && $_SESSION['DEBUG'] == 1)
            $this->query_hist .= $this->query_counter . "\t" . number_format($dauer, 4, ",", ".") . "\t" . $this->format_string_to_xls($query_string) . "\t" . __TRAIT__ . "\r\n";
        if (!$this->query_id) {
            $this->error("Invalid SQL: " . $query_string . "<br>" . mysqli_error($this->link_id));
        }

        return $this->query_id;
    }


    /**
     * kdb::fetch_array()
     * 
     * @param integer $query_id
     * @return
     */
    function fetch_array($query_id = false) {
        $this->record = @mysqli_fetch_array($query_id);
        return $this->record;
    }

    /**
     * kdb::fetch_array_names()
     * 
     * @param integer $query_id
     * @return
     */
    function fetch_array_names($query_id = false) {
        $this->record = @mysqli_fetch_array($query_id, MYSQLI_ASSOC);
        return $this->record;
    }

    /**
     * kdb::free_result()
     * 
     * @param integer $query_id
     * @return
     */
    function free_result($query_id = false) {
        return @mysqli_free_result($query_id);
    }


    /**
     * kdb::query_first()
     * 
     * @param mixed $query_string
     * @param integer $admin
     * @return
     */
    function query_first($query_string, $admin = 0) {
        $this->query_counter += 1;
        $startt = self::get_micro_time();
        $this->query($query_string, $admin);
        $returnarray = $this->fetch_array_names($this->query_id);
        $dauer = self::get_micro_time() - $startt;
        if ($dauer > $this->max_query_duration && $_SESSION['DEBUG'] == 1)
            $this->query_hist .= $this->query_counter . "\t" . number_format($dauer, 4, ",", ".") . "\t" . $this->format_string_to_xls($query_string) . "\r\n";
        $this->free_result($this->query_id);
        return $returnarray;
    }

    /**
     * kdb::query_first_obj()
     * 
     * @param mixed $query_string
     * @param integer $admin
     * @return
     */
    function query_first_obj($query_string, $admin = 0) {
        $this->query_counter += 1;
        $startt = self::get_micro_time();
        $this->query($query_string, $admin);
        $obj = mysqli_fetch_object($this->query_id);
        $dauer = self::get_micro_time() - $startt;
        if ($dauer > $this->max_query_duration && $_SESSION['DEBUG'] == 1)
            $this->query_hist .= $this->query_counter . "\t" . number_format($dauer, 4, ",", ".") . "\t" . $this->format_string_to_xls($query_string) . "\r\n";
        $this->free_result($this->query_id);
        return (object)$obj;
    }


    /**
     * kdb::sql_fetchrow()
     * 
     * @param integer $query_id
     * @return
     */
    function sql_fetchrow($query_id = 0) {
        if ($query_id) {
            $this->row[$query_id] = @mysqli_fetch_array($query_id);
            return $this->row[$query_id];
        }
        else {
            return false;
        }
    }
    /**
     * kdb::num_rows()
     * 
     * @param integer $query_id
     * @return
     */
    function num_rows($query_id = false) {
        return mysqli_num_rows($query_id);
    }

    /**
     * kdb::insert_id()
     * 
     * @return mixed
     */
    function insert_id() {
        return mysqli_insert_id($this->link_id);
    }


    /**
     * kdb::format_string_to_xls()
     * 
     * @param mixed $input
     * @return string
     */
    function format_string_to_xls($input) {
        $input = $this->remove_line_breaks_and_tabs($input);
        $input = str_replace('"' . "\t", '"' . "\\t", $input);
        $input = str_replace("\r\n", "\\r\\n", $input);
        if (is_numeric($input))
            $input = str_replace('.', ',', $input);
        return $input;
    }

    /**
     * kdb::remove_line_breaks_and_tabs()
     * 
     * @param mixed $str
     * @return
     */
    function remove_line_breaks_and_tabs($str) {
        $rep_arr = array(
            "\n",
            "\t",
            "\r");
        return str_replace($rep_arr, " ", $str);
    }

    /**
     * kdb::error()
     * 
     * @param mixed $msg
     * @return
     */
    function error($msg) {
        $msg = $this->format_string_to_xls($msg);
        echo '<html>
        <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        </head>
        <body>
           <div class="container"> 
            <div class="bg-danger text-danger">SQL ERROR:<b>' . mysqli_error($this->link_id) . '</b><br>' . $msg . '</div>
           </div> 
        </body>
        </html>
        ';
        $log_path = CMS_ROOT;

        if (!is_dir($log_path . 'admin/logs/'))
            mkdir($log_path . 'admin/logs/', 0755);
        $log_file = $log_path . 'admin/logs/sql_log.xls';

        $l_arr = array();
        $l_arr[] = getenv('REMOTE_ADDR') . "\t" . date("d.m.Y") . "\t" . date("H:i:s") . "\t" . $_SERVER['HTTP_HOST'] . "\t" . $_SERVER['PHP_SELF'] . "\t" . $_GET['page'] .
            "\t" . $_GET['aktion'] . "\t" . $_SERVER['REQUEST_URI'] . "\t" . mysqli_error($this->link_id) . "\t" . $msg . "\t" . $_SERVER['HTTP_USER_AGENT'];
        if (count($l_arr) >= 10) {
            unset($l_arr[0]);
        }
        $l_arr = array_reverse($l_arr);
        $lines = "";
        if (count($l_arr) > 0) {
            foreach ($l_arr as $value) {
                $lines .= (($lines != "") ? "\r\n" : '') . $value;
            }
        }

        $fp = fopen($log_file, "w+");
        fwrite($fp, $lines);
        fclose($fp);
        $this->disconnect();
        die();
    }
}
