<?php

/**
 * @package    forum
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class forum_master_class extends modules_class {
    var $forum_file_path = "";

    /**
     * forum_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->forum_file_path = FILE_ROOT . 'forum/';
    }


    /**
     * forum_master_class::gen_forum_link()
     * 
     * @param mixed $groupname
     * @param mixed $forumname
     * @return
     */
    public static function gen_forum_link($groupname, $forumname) {
        return '/forum/' . self::format_file_name($groupname) . '/' . self::format_file_name($forumname) . '.html';
    }


    /**
     * forum_master_class::gen_theme_link()
     * 
     * @param mixed $groupname
     * @param mixed $forumname
     * @param mixed $themename
     * @return
     */
    public static function gen_theme_link($groupname, $forumname, $themename) {
        return '/forum/' . self::format_file_name($groupname) . '/' . self::format_file_name($forumname) . '/' . self::format_file_name($themename) . '.html';
    }


    /**
     * forum_master_class::add_theme_pageindex()
     * 
     * @param mixed $forumname
     * @param mixed $threadname
     * @param mixed $id
     * @param string $lngid
     * @return void
     */
    function add_theme_pageindex($groupname, $forumname, $themename, $id, $lngid = '1') {
        $query = array('cmd' => 'st', 'tid' => $id);
        $this->connect_to_pageindex($this->gen_theme_link($groupname, $forumname, $themename), $query, $id, 'forum', $lngid, 0, 0, '/theme');
    }

    /**
     * forum_master_class::add_forum_pageindex()
     * 
     * @param mixed $forumname
     * @param mixed $id
     * @param string $lngid
     * @return void
     */
    function add_forum_pageindex($groupname, $forumname, $id, $lngid = '1') {
        $query = array('cmd' => 'sf', 'fid' => $id);
        $this->connect_to_pageindex($this->gen_forum_link($groupname, $forumname), $query, $id, 'forum', $lngid, 0, 0, '/forum');
    }

    /**
     * forum_master_class::remove_forum_from_pageindex()
     * 
     * @param mixed $forumid
     * @return void
     */
    function remove_forum_from_pageindex($forumid) {
        $this->remove_from_page_index('forum', $forumid, 'forum/forum');
    }

    /**
     * forum_master_class::remove_theme_from_pageindex()
     * 
     * @param mixed $themeid
     * @return void
     */
    function remove_theme_from_pageindex($themeid) {
        $this->remove_from_page_index('forum', $themeid, 'forum/theme');
    }

    /**
     * forum_class::del_forum()
     * 
     * @param mixed $fid
     * @return
     */
    function del_forum($fid) {
        $fid = intval($fid);
        $this->load_themes($fid);
        foreach ($this->themes as $key => $th) {
            $this->del_theme((int)$th['TID']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMTHREADS . " WHERE f_fid=" . $fid);
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMF . " WHERE id=" . $fid);
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMTHEMES . " WHERE t_fid=" . $fid);
        $this->remove_forum_from_pageindex($fid);
    }

    /**
     * forum_master_class::save_fgroup()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @return
     */
    function save_fgroup($FORM, $id) {
        $id = (int)$id;
        if ($id > 0) {
            update_table(TBL_CMS_FORUMGROUPS, 'id', $id, $FORM);
        }
        else {
            insert_table(TBL_CMS_FORUMGROUPS, $FORM);
        }
    }

    /**
     * forum_master_class::load_file_by_id()
     * 
     * @param mixed $id
     * @return
     */
    function load_file_by_id($id) {
        $file = $this->db->query_first("SELECT f_file FROM " . TBL_CMS_FORUMFILES . " WHERE id=" . (int)$id);
        return (array )$file;
    }

    /**
     * forum_master_class::load_group()
     * 
     * @param mixed $id
     * @return
     */
    function load_group($id) {
        $result = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " WHERE id=" . (int)$id);
        return (array )$result;
    }

    /**
     * forum_class::del_thread_file()
     * 
     * @param mixed $threadid
     * @param mixed $fileid
     * @return
     */
    function del_thread_file($fileid) {
        #$this->load_thread((int)$threadid);
        #@unlink($this->forum_file_path . $this->thread['filelist'][$fileid]['f_file']);
        $file = $this->load_file_by_id($_GET['id']);
        @unlink($this->forum_file_path . $file['f_file']);
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMFILES . " WHERE id=" . (int)$fileid);
    }

    /**
     * forum_master_class::update_themecount_of_forum()
     * 
     * @param mixed $forumid
     * @return void
     */
    function update_themecount_of_forum($forumid) {
        $theme_count = get_data_count(TBL_CMS_FORUMTHEMES, 'id', "t_fid=" . $forumid);
        $this->db->query("UPDATE " . TBL_CMS_FORUMF . " SET fn_theme_count=" . $theme_count . " WHERE id=" . $forumid);
    }

    /**
     * forum_master_class::update_threadcount_of_theme_and_forum()
     * 
     * @param mixed $themeid
     * @return void
     */
    function update_threadcount_of_theme_and_forum($themeid) {
        $THEME = $this->load_theme_by_id($themeid);
        $t_thread_count = get_data_count(TBL_CMS_FORUMTHREADS, 'id', "f_tid=" . (int)$themeid);
        $t_thread_count_forum = get_data_count(TBL_CMS_FORUMTHREADS, 'id', "f_fid=" . $THEME['t_fid']);
        $this->db->query("UPDATE " . TBL_CMS_FORUMTHEMES . " SET t_thread_count=" . $t_thread_count . " WHERE id=" . (int)$themeid);
        $this->db->query("UPDATE " . TBL_CMS_FORUMF . " SET fn_thread_count=" . $t_thread_count_forum . " WHERE id=" . (int)$THEME['t_fid']);
        $this->update_themecount_of_forum($THEME['t_fid']);
    }

    /**
     * forum_master_class::load_theme_by_id()
     * 
     * @param mixed $tid
     * @return
     */
    function load_theme_by_id($tid) {
        $T = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_FORUMTHEMES . " T, " . TBL_CMS_FORUMGROUPS . " G, " . TBL_CMS_FORUMF . " F
        WHERE T.id=" . (int)$tid . " 
        AND G.id=F.fn_gid 
        AND F.id=T.t_fid        
        LIMIT 1");
        return $T;
    }

    /**
     * forum_master_class::load_customer_of_theme()
     * 
     * @param mixed $themeid
     * @return
     */
    function load_customer_of_theme($themeid) {
        $THEME = $this->load_theme_by_id($themeid);
        $CUSTOMER = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$THEME['t_kid'] . " LIMIT 1");
        return $CUSTOMER;
    }

    /**
     * forum_class::set_theme_opt()
     * 
     * @param mixed $theme
     * @return
     */
    public static function set_theme_opt(&$theme) {
        $theme['themelink'] = self::gen_theme_link($theme['fg_name'], $theme['fn_name'], $theme['t_name']);
        $theme['themedatetime'] = date('d.m.Y H:i', $theme['t_time']);
        $theme['thread_human_date'] = self::human_date($thread['t_time']);
        $theme['theme_relative_time'] = self::relative_date($thread['t_time']);
        $theme['theme_relative_time_class'] = self::color_class($theme['relative_time']);
        return $theme;
    }


    /**
     * forum_master_class::load_threads_by_theme()
     * 
     * @param mixed $themeid
     * @return
     */
    function load_threads_by_theme($themeid, $start = 0, $limit = 0) {
        $arr = array();
        $result = $this->db->query("SELECT *,TH.id AS THREADID, count(F.id) AS FILECOUNT FROM 
	(" . TBL_CMS_FORUMTHREADS . " TH LEFT JOIN " . TBL_CMS_FORUMFILES . " F ON (F.f_threadid=TH.id) ),
	(" . TBL_CMS_FORUMTHREADS . " THH LEFT JOIN " . TBL_CMS_CUST . " K ON (K.kid=THH.f_kid) )
	WHERE TH.f_tid=" . (int)$themeid . " 
	AND TH.id=THH.id
	GROUP BY TH.id
	ORDER BY TH.f_time ASC
    " . (($start > 0 || $limit > 0) ? " LIMIT " . (int)$start . "," . (int)$limit : "") . "
    ");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }
}
