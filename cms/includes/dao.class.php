<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('IN_SIDE') or die('Access denied.');

class dao_class extends keimeno_class {
    public static $cdb = null;

    /**
     * dao_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
    }

    public static function set_db($ddb) {
        static::$cdb = $ddb;
    }

    /**
     * dao_class::load_template()
     * 
     * @param mixed $id
     * @return
     */
    public static function load_template($id) {
        return static::$cdb->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id='" . (int)$id . "' LIMIT 1");
    }

    /**
     * dao_class::load_template_content()
     * 
     * @param mixed $id
     * @return
     */
    public static function load_template_content($id) {
        return self::get_data_first(TBL_CMS_TEMPCONTENT, array('id' => (int)$id));
    }

    /**
     * dao_class::load_template_content_by_tid()
     * 
     * @param mixed $id
     * @return
     */
    public static function load_template_content_by_tid($tid, $langid = 1) {
        return self::get_data_first(TBL_CMS_TEMPCONTENT, array('tid' => $tid, 'lang_id' => $langid));
    }


    /**
     * dao_class::load_template()
     * 
     * @param mixed $id
     * @param integer $langid
     * @return
     */
    public static function get_template_content($id, $langid = 1) {
        $result = static::$cdb->query_first("SELECT C.content FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C WHERE C.tid=T.id AND C.lang_id=" . $langid .
            " AND T.id='" . (int)$id . "' LIMIT 1");
        return $result['content'];
    }

    /**
     * dao_class::sql_result_to_array()
     * 
     * @param mixed $result
     * @return
     */
    public static function sql_result_to_array($result) {
        $arr = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $arr[$row['id']] = $row;
        }
        return $arr;
    }

    /**
     * dao_class::load_frontend_languages()
     * 
     * @return
     */
    public static function load_frontend_languages() {
        $LANGSFE = array();
        $result = static::$cdb->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
        return self::sql_result_to_array($result);
    }

    /**
     * dao_class::update_tpl_content()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @return void
     */
    public static function update_tpl_content($FORM, $id) {
        update_table(TBL_CMS_TEMPCONTENT, 'id', (int)$id, $FORM);
    }

    /**
     * dao_class::update_template()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @return void
     */
    public static function update_template($FORM, $id) {
        update_table(TBL_CMS_TEMPLATES, 'id', (int)$id, $FORM);
    }
    /**
     * dao_class::get_data_first()
     * 
     * @param mixed $table
     * @param mixed $where
     * @param mixed $fields
     * @param bool $debug
     * @return
     */
    public static function get_data_first($table, $where = array(), $fields = array(), $debug = false) {
        return self::get_data($table, $where, $fields, array(), true, $debug);
    }


    /**
     * dao_class::get_data()
     * 
     * @param mixed $table
     * @param mixed $where
     * @param mixed $fields
     * @param mixed $addon
     * @param bool $first
     * @param bool $debug
     * @return
     */
    public static function get_data($table, $where = array(), $fields = array(), $addon = array(), $first = false, $debug = false) {
        $where = (array )$where;
        $fields = (array )$fields;
        $addon = (array )$addon;
        $addon_str = " ";
        if (count($where) == 0) {
            $where = array(' ' => '1');
        }
        if (count($fields) == 0) {
            $fields = array('*');
        }
        $where_str="";
        foreach ((array )$where as $key => $value) {
            if (!is_array($value)) {
                $where_str .= (($where_str != "") ? " AND " : "") . ((trim($key) == "") ? " 1" : $key . "='" . $value . "'");
            }
            else {
                $where_str .= (($where_str != "") ? " AND " : "") . $key . $value[0] . $value[1];
            }
        }
        $fields_str="";
        foreach ((array )$fields as $key => $value) {
            $fields_str .= (($fields_str != "") ? ", " : "") . $value;
        }
        foreach ((array )$addon as $key => $value) {
            $addon_str .= (($addon_str != "") ? " " : "") . $key . " " . $value;
        }
        $debug_num = ($debug == true) ? 1 : 0;
        $arr = array();
        if ($first == true) {
            $arr = static::$cdb->query_first("SELECT " . $fields_str . " FROM " . $table . " WHERE " . $where_str, $debug_num);
        }
        else {
            $result = static::$cdb->query("SELECT " . $fields_str . " FROM " . $table . " WHERE " . $where_str . $addon_str, $debug_num);
            while ($row = static::$cdb->fetch_array_names($result)) {
                $arr[] = $row;
            }
        }
        return (array )$arr;
    }

    /**
     * dao_class::load_system_templates()
     * 
     * @return
     */
    public static function load_system_templates() {
        $arr = array();
        $result = static::$cdb->query("SELECT * FROM  " . TBL_CMS_TEMPLATES . "              
                WHERE gbl_template=1          
                ORDER BY description");
        while ($row = static::$cdb->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * dao_class::load_customer()
     * 
     * @param mixed $kid
     * @return
     */
    public static function load_customer($kid) {
        return static::$cdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$kid);
    }

    /**
     * dao_class::get_count()
     * 
     * @param mixed $table
     * @param mixed $where_arr
     * @param string $column
     * @return
     */
    public static function get_count($table, $where_arr, $column = '*') {
        foreach ((array )$where_arr as $key => $value) {
            $where .= (($where != "") ? " AND " : "") . $key . "='" . $value . "'";
        }
        if (count($where_arr) == 0)
            $where = "1";
        $result = static::$cdb->query("SELECT COUNT(" . $column . ") FROM " . $table . " WHERE " . $where);
        while ($row = static::$cdb->fetch_array($result)) {
            Return $row[0] * 1;
        }
    }

    /**
     * dao_class::get_max()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param mixed $where_arr
     * @return
     */
    public static function get_max($table, $column, $where_arr=array()) {
        foreach ((array )$where_arr as $key => $value) {
            $where .= (($where != "") ? " AND " : "") . $key . "='" . $value . "'";
        }
        if (count($where_arr) == 0)
            $where = "1";
        $result = static::$cdb->query("SELECT MAX($column) FROM " . $table . " WHERE " . $where);
        while ($row = static::$cdb->fetch_array($result)) {
            Return $row[0] * 1;
        }
    }

    /**
     * dao_class::get_sum()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param mixed $where_arr
     * @return
     */
    public static function get_sum($table, $column, $where_arr) {
        foreach ((array )$where_arr as $key => $value) {
            $where .= (($where != "") ? " AND " : "") . $key . "='" . $value . "'";
        }
        if (count($where_arr) == 0)
            $where = "1";
        $result = static::$cdb->query("SELECT SUM($column) FROM " . $table . " WHERE " . $where);
        while ($row = static::$cdb->fetch_array($result)) {
            Return $row[0] * 1;
        }
    }

    /**
     * dao_class::get_minvalue()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param mixed $where
     * @return
     */
    public static function get_minvalue($table, $column, $where_arr = array()) {
        foreach ((array )$where_arr as $key => $value) {
            $where .= (($where != "") ? " AND " : "") . $key . "='" . $value . "'";
        }
        if (count($where_arr) == 0)
            $where = "1";
        $MINV = static::$cdb->query_first("SELECT MIN(" . $column . ") AS MINV FROM " . $table . " WHERE " . $where);
        return $MINV['MINV'];
    }

    /**
     * dao_class::delete_row()
     * 
     * @param mixed $table
     * @param mixed $ident
     * @param string $col
     * @return
     */
    public static function delete_row($table, $ident, $col = 'id') {
        return self::db_delete($table, array($col => $ident));
        #return static::$cdb->query("DELETE FROM `" . $table . "` WHERE " . $col . "=" . $ident);
    }

    /**
     * dao_class::db_delete()
     * 
     * @param mixed $table
     * @param mixed $where
     * @return
     */
    public static function db_delete($table, $where_arr = array()) {
        foreach ((array )$where_arr as $key => $value) {
            $where .= (($where != "") ? " AND " : "") . $key . "='" . $value . "'";
        }
        return static::$cdb->query("DELETE FROM `" . $table . "` WHERE " . $where);
    }

    /**
     * dao_class::update_table()
     * 
     * @param mixed $table
     * @param mixed $set
     * @param mixed $where_arr
     * @param integer $admin
     * @return void
     */
    public static function update_table($table, $set, $where_arr, $admin = 0) {
        global $kdb;
        $where="";
        if (is_array($set)) {
            foreach ((array )$where_arr as $key => $value) {
                $where .= (($where != "") ? " AND " : "") . $key . "='" . $value . "'";
            }
            #$objekt = self::get_data_first($table, $where_arr);
            foreach ($set as $key => $wert) {
                #   if ($objekt[$key] != $wert) {
                if ($sqlquery)
                    $sqlquery .= ', ';
                $sqlquery .= $key . "='" . $wert . "'";
                #  }
            }
            $sql = "UPDATE `" . $table . "` SET " . $sqlquery . " WHERE " . (($where=="") ? "1" : $where);
            if ($admin == 1)
                echo $sql;
            if ($sqlquery != "")
                static::$cdb->query($sql);
        }
    }

}
