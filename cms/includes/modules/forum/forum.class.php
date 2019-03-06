<?php

/**
 * @package    forum
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


DEFINE('TBL_CMS_FORUMGROUPS', TBL_CMS_PREFIX . 'forum_groups');
DEFINE('TBL_CMS_FORUMTHREADS', TBL_CMS_PREFIX . 'forum_threads');
DEFINE('TBL_CMS_FORUMF', TBL_CMS_PREFIX . 'forum_f');
DEFINE('TBL_CMS_FORUMTHEMES', TBL_CMS_PREFIX . 'forum_themes');
DEFINE('TBL_CMS_FORUMFILES', TBL_CMS_PREFIX . 'forum_files');
DEFINE('TBL_CMS_FORUMSREL', TBL_CMS_PREFIX . 'forum_sinrel');
DEFINE('TBL_CMS_FORUMSWORDS', TBL_CMS_PREFIX . 'forum_sinwords');

require (CMS_ROOT . 'includes/modules/forum/lib/vendor/autoload.php');


class forum_class extends forum_master_class {

    var $ftable = array();
    var $pageid = 0;
    var $forum_id = 0;
    var $theme_id = 0;
    var $user_object = null;
    var $thread = null;
    var $threadid = 0;
    var $bbcode = null;
    var $index_search = null;
    var $meta_keywords = "";
    var $meta_title = "";
    var $meta_description = "";
    

    /**
     * forum_class::forum_class()
     * 
     * @param integer $pageid
     * @return
     */
    function __construct($pageid = 0) {
        global $user_object;
        parent::__construct();
       
        if (isset($_GET['fid']))
            $_GET['fid'] = (int)$_GET['fid'];
        if (isset($_GET['tid']))
            $_GET['tid'] = (int)$_GET['tid'];
        if (isset($_GET['threadid']))
            $_GET['threadid'] = (int)$_REQUEST['threadid'];
        if (isset($_POST['id']))
            $_POST['id'] = (int)$_POST['id'];
        $this->TCR = new kcontrol_class($this);
        $this->pageid = (int)$pageid;
        $this->memberclass = new member_class();
        #php56 composer.phar require golonka/bbcodeparser
        $this->bbcode = new Golonka\BBCode\BBCodeParser;
        $this->bbcode->setParser('ul', '/\[ul\](.*?)\[\/ul\]/s', '<ul>$1</ul>', '$1');
        $this->bbcode->setParser('ol', '/\[ol\](.*?)\[\/ol\]/s', '<ol>$1</ol>', '$1');
        $this->bbcode->setParser('li', '/\[li\](.*?)\[\/li\]/s', '<li>$1</li>', '$1');
        # $exception = array('linebreak');
        #$this->bbcode->except($exception);


        $this->set_user($user_object);
        $this->theme_id = (int)$_GET['tid'];
        $this->forum_id = (int)$_GET['fid'];
        $this->threadid = (int)$_GET['threadid'];
        $this->secure_request();
        $this->index_search = new forum_search_class();
    }


    /**
     * forum_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        global $user_object;
        $this->set_metas();
        $this->FORUM['user'] = $user_object;
        if ($this->smarty->getTemplateVars('FORUM') != null) {
            $this->FORUM = array_merge($this->smarty->getTemplateVars('FORUM'), $this->FORUM);
            $this->smarty->clearAssign('FORUM');
        }
        $this->smarty->assign('FORUM', $this->FORUM);
    }

    /**
     * forum_class::secure_request()
     * 
     * @return void
     */
    function secure_request() {
        return;
        if (isset($_REQUEST['cmd'])) {
            $protected_actions = array(
                'answer',
                'newtheme',
                'answerthread',
                'savetheme',
                'delthread',
                'st',
                'sf',
                'a_delfile',
                'deltheme');
            if (!in_array($_REQUEST['cmd'], $protected_actions)) {
                $this->LOGCLASS->addLog('ILLEGAL', 'forum call, cmd=' . $_REQUEST['cmd']);
                firewall_class::report_hacking('Hacking forum, wrong cmd');
                $this->msge('Hacking');
                header('location:' . PATH_CMS . 'index.html');
                exit;
            }
        }
    }

    /**
     * forum_class::cmd_delforumfile()
     * 
     * @return void
     */
    function cmd_delforumfile() {
        if ($this->user_object['PERMOD']['forum']['edit'] === true || $this->user_object['kid'] == $this->thread['f_kid']) {
            $this->del_thread_file($_GET['fileid']);
            $this->msg('{LBL_DELETED}');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=answer&threadid=' . $this->threadid . '&page=' . $_GET['page']);
        }
        else {
            firewall_class::report_hacking('Forum, delete attachment without permissions');
            $this->msge('{LBL_NOPERMISSIONS}');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=answer&threadid=' . $this->threadid . '&page=' . $_GET['page'] . '&page=' . $_GET['page']);
        }
        exit;
    }

    /**
     * forum_class::cmd_delthread()
     * 
     * @return void
     */
    function cmd_delthread() {
        if ($this->threadid > 0) {
            if ($this->user_object['PERMOD']['forum']['del'] === true || $this->user_object['kid'] == $this->thread['f_kid']) {
                $THREAD = $this->load_thread($this->threadid);
                $this->del_thread($this->threadid);
                $this->update_threadcount_of_theme_and_forum($THREAD['f_tid']);
                $this->msg('{LBL_DELETED}');
            }
            else {
                firewall_class::report_hacking('Forum, delete thread without permissions');
                $this->msge('{LBL_NOPERMISSIONS}');
            }
        }
        $this->ej();
    }

    /**
     * forum_class::cmd_deltheme()
     * 
     * @return void
     */
    function cmd_deltheme() {
        if ($this->user_object['PERMOD']['forum']['del'] === true) {
            $this->del_theme($this->theme_id);
            $this->msg('{LBL_DELETED}');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=sf&fid=' . $_GET['fid'] . '&page=' . $_GET['page']);
        }
        else {
            firewall_class::report_hacking('Forum, delete theme without permissions');
            $this->msge('{LBL_NOPERMISSIONS}');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=sf&fid=' . $_GET['fid'] . '&page=' . $_GET['page'] . '&page=' . $_GET['page']);
        }
        exit;
    }


    /**
     * forum_class::init()
     * 
     * @return void
     */
    function init() {
        $this->load_groups_fe();
        if ($this->threadid > 0) {
            $this->load_thread($this->threadid);
        }
        if ($this->theme_id > 0) {
            $this->load_theme($this->theme_id);
        }
    }

    /**
     * forum_class::cmd_newtheme()
     * 
     * @return void
     */
    function cmd_newtheme() {
        $this->load_forum_fe($_GET['fid']);
    }

    /**
     * forum_class::cmd_st()
     * 
     * @return void
     */
    function cmd_st() {
        $this->load_threads($_GET['tid']);
        $thread = $this->threads[0];
        $this->load_forum_fe($thread['f_fid']);
    }

    /**
     * forum_class::cmd_sf()
     * lädt Themen in einem Forum
     * @return void
     */
    function cmd_sf() {
        $this->load_themes($this->forum_id);
        $this->load_forum_fe($this->forum_id);
    }


    /**
     * forum_class::set_user()
     * 
     * @param mixed $user_object
     * @return
     */
    function set_user($user_object) {
        $this->user_object = $user_object;
    }

    /**
     * forum_class::set_user_obj()
     * 
     * @param mixed $userobj
     * @return
     */
    function set_user_obj($userobj) {
        $this->userobj = $userobj;
    }


    /**
     * forum_class::set_metas()
     * 
     * @return
     */
    function set_metas() {
        global $meta_keywords, $meta_title, $meta_description;
        if (!empty($this->meta_keywords))
            $meta_keywords = $this->meta_keywords;
        if (!empty($this->meta_description))
            $meta_description = $this->meta_description;
        if (!empty($this->meta_title))
            $meta_title = $this->meta_title;
    }


    /**
     * forum_class::delete_fgroup()
     * 
     * @param mixed $id
     * @return
     */
    function delete_fgroup($id) {
        $id = intval($id);
        $this->load_forums($id);
        foreach ($this->foren as $key => $row) {
            $this->del_forum($row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMGROUPS . " WHERE id=" . $id);
        $this->LOGCLASS->addLog('DELETE', 'Forum group deletion complete ' . $id);
    }


    /**
     * forum_class::del_theme()
     * 
     * @param mixed $themeid
     * @return
     */
    function del_theme($themeid) {
        $THEME = $this->load_theme_by_id($themeid);
        $this->load_threads((int)$themeid);
        foreach ($this->threads as $key => $thr) {
            $this->del_thread($thr['THREADID']);
        }
        $this->update_threadcount_of_theme_and_forum($themeid);
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMTHEMES . " WHERE id=" . (int)$themeid);
        $this->update_themecount_of_forum($THEME['t_fid']);
        $this->remove_theme_from_pageindex($themeid);
    }


    /**
     * forum_class::del_thread()
     * 
     * @param mixed $threadid
     * @return
     */
    function del_thread($threadid) {
        $thread['filelist'] = array();
        $this->load_files((int)$threadid, $thread);
        foreach ($thread['filelist'] as $key => $file) {
            @unlink($this->forum_file_path . $file['f_file']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMTHREADS . " WHERE id=" . (int)$threadid . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMFILES . " WHERE f_threadid=" . (int)$threadid);

    }


    /**
     * forum_class::load_groups_fe()
     * 
     * @return
     */
    function load_groups_fe() {
        $this->FORUM['fgroups'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " WHERE fg_approved=1 ORDER BY fg_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['foren'] = $this->load_forums_fe($row['id']);
            $this->FORUM['fgroups'][] = $row;
        }
        return $this->FORUM['fgroups'];
    }


    /**
     * forum_class::load_forums_fe()
     * 
     * @param mixed $groupid
     * @return
     */
    function load_forums_fe($groupid) {
        $this->foren = array();
        $result = $this->db->query("SELECT *,G.id AS GID,F.id AS FID, F.fn_thread_count AS THREADCOUNT, F.fn_theme_count AS THEMECOUNT
		FROM " . TBL_CMS_FORUMGROUPS . " G, " . TBL_CMS_FORUMF . " F 
		WHERE G.id=" . (int)$groupid . "
		AND G.id=F.fn_gid 		 
		AND F.fn_approved=1 
		ORDER BY F.fn_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_forum_opt_fe($row);
            $row['lastthread'] = $this->get_last_thread_by_forum($row['FID']);
            $row['hastodaythread'] = (date('Y-m-d', $row['lastthread']['f_time']) == date('Y-m-d')) ? true : false;
            $this->foren[$row['FID']] = $row;
        }
        return $this->foren;
    }

    /**
     * forum_class::load_forum_fe()
     * 
     * @param mixed $fid
     * @return
     */
    function load_forum_fe($fid) {
        $F = $this->db->query_first("SELECT *,G.id AS GID,F.id AS FID, COUNT(TH.id) AS THREADCOUNT FROM " . TBL_CMS_FORUMGROUPS . " G, " . TBL_CMS_FORUMF . " F
		LEFT JOIN " . TBL_CMS_FORUMTHREADS . " TH ON (TH.f_fid=F.id)
		WHERE G.id=F.fn_gid 
		AND F.fn_approved=1 
		AND F.id=" . (int)$fid . "
		GROUP BY F.id
		ORDER BY fn_order");
        $this->set_forum_opt_fe($F);
        $this->smarty->assign('forumobj', $F);
        $this->meta_title = $F['fn_name'];
        return $F;
    }


    /**
     * forum_class::get_last_thread()
     * 
     * @param mixed $themeid
     * @return
     */
    function get_last_thread($themeid) {
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMTHREADS . " WHERE f_tid=" . (int)$themeid . " ORDER BY f_time DESC LIMIT 1");
        $this->set_thread_opt($T, false);
        return $T;
    }

    /**
     * forum_class::get_last_thread_by_forum()
     * 
     * @param mixed $forumid
     * @return
     */
    function get_last_thread_by_forum($forumid) {
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMTHREADS . " WHERE f_fid=" . (int)$forumid . " ORDER BY f_time DESC LIMIT 1");
        $this->set_thread_opt($T, false);
        return $T;
    }

    /**
     * forum_class::load_themes()
     * 
     * @param mixed $fid
     * @return
     */
    function load_themes($fid) {
        $this->themes = array();
        $result = $this->db->query("SELECT *,T.id AS TID,count(TH.id) AS THCOUNT,F.id AS FID,G.id AS GID  
		FROM " . TBL_CMS_FORUMGROUPS . " G, " . TBL_CMS_FORUMF . " F, " . TBL_CMS_FORUMTHEMES . " T 
		LEFT JOIN " . TBL_CMS_FORUMTHREADS . " TH ON (TH.f_tid=T.id)
		WHERE t_fid=" . (int)$fid . " 
        AND G.id=F.fn_gid 
        AND F.id=T.t_fid
		GROUP BY T.id
		ORDER BY T.t_time DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_theme_opt($row);
            $row['lastthread'] = $this->get_last_thread($row['TID']);
            $row['THCOUNT'] -= 1;
            $row['hastodaythread'] = (date('Y-m-d', $row['lastthread']['f_time']) == date('Y-m-d')) ? true : false;
            $this->themes[] = $row;
        }
        $this->smarty->assign('forum_themes', $this->themes);
    }

    /**
     * forum_class::load_theme()
     * 
     * @param mixed $tid
     * @return
     */
    function load_theme($tid) {
        $this->db->query("UPDATE " . TBL_CMS_FORUMTHEMES . " SET t_hits=t_hits+1 WHERE id=" . (int)$tid);
        $THEME = $this->db->query_first("SELECT *,T.id AS TID,F.id AS FID,G.id AS GID 
        FROM " . TBL_CMS_FORUMTHEMES . " T, " . TBL_CMS_FORUMGROUPS . " G, " . TBL_CMS_FORUMF . " F
        WHERE T.id=" . (int)$tid . " 
        AND G.id=F.fn_gid 
        AND F.id=T.t_fid        
        LIMIT 1");
        $THEME = $this->set_theme_opt($THEME);
        $this->smarty->assign('forumtheme', $THEME);
        if ($THEME['id'] > 0) {
            $this->forum_id = $THEME['t_fid'];
            $this->meta_title = $THEME['t_name'];
        }
        return $THEME;
    }

    /**
     * forum_class::load_threads()
     * 
     * @param mixed $themeid
     * @return
     */
    function load_threads($themeid) {
        $this->threads = array();
        $threads = $this->load_threads_by_theme($themeid);
        foreach ($threads as $row) {
            $pcontent .= $row['f_text'] . ' ';
            $this->meta_description = ($this->meta_description == "") ? format_meta($row['f_text']) : '';
            $this->set_thread_opt($row);
            if ($row['FILECOUNT'] > 0) {
                $this->load_files($row['THREADID'], $row);
            }
            $row['user']['img'] = ($row['picture'] != "") ? gen_thumb_image('./images/members/' . $row['picture'], $this->gbl_config['forum_mem_picwidth'], $this->
                gbl_config['forum_mem_picheight'], 'crop') : gen_thumb_image('./images/opt_member_nopic.jpg', $this->gbl_config['forum_mem_picwidth'], $this->gbl_config['forum_mem_picheight'],
                'crop');
            $row['user']['datum_ger'] = my_date('d.m.Y', $row['datum']);
            $row['user']['userlink'] = $this->memberclass->genCustomerLink($row['f_kid'], 'member', $this->langid);
            $this->threads[] = $row;
        }
        $this->smarty->assign('forum_threads', $this->threads);
        if (count($this->threads) > 0) {
            $this->forum_id = $this->threads[0]['f_fid'];
        }
        $this->theme_id = (int)$themeid;
        $this->meta_keywords = $this->gen_meta_keywords($pcontent);
        return $this->threads;
    }


    /**
     * forum_class::set_thread_opt()
     * 
     * @param mixed $thread
     * @param bool $do_bb_compile
     * @return
     */
    function set_thread_opt(&$thread, $do_bb_compile = true) {
        $thread['thread_datetime'] = date('d.m.Y H:i', $thread['f_time']);
        $thread['thread_today'] = date('Y-m-d', $thread['f_time']) == date('Y-m-d');
        $yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        $thread['thread_yesterday'] = date('Y-m-d', $thread['f_time']) == $yesterday;
        $thread['thread_time'] = date('H:i', $thread['f_time']);
        $thread['thread_human_date'] = $this->human_date($thread['f_time']);
        $thread['relative_time'] = $this->relative_date($thread['f_time']);
        $thread['relative_time_class'] = $this->color_class($thread['relative_time']);
        list($vorname, $nachname, $username) = explode('|', $thread['f_user']);
        $thread['user'] = array(
            'nachname' => $nachname,
            'vorname' => $vorname,
            'uservn' => ucfirst($vorname) . ' ' . ucfirst(substr($nachname, 0, 1)) . '.',
            'username' => $username);
        $thread['f_text'] = htmlspecialchars($thread['f_text']);
        if ($do_bb_compile === true) {
            $thread['f_text_bbcode'] = str_replace('</li><br />', '</li>', $this->bbcode->parse($thread['f_text']));
            $thread['plaintext'] = strip_tags($thread['f_text_bbcode']);
        }
        $thread['filelist'] = array();
    }

    /**
     * forum_class::set_forum_opt_fe()
     * 
     * @param mixed $forum
     * @return
     */
    function set_forum_opt_fe(&$forum) {
        $forum['forumlink'] = self::gen_forum_link($forum['fg_name'], $forum['fn_name']);
    }


    /**
     * forum_class::load_latest_threads()
     * 
     * @return
     */
    function load_latest_threads() {
        $this->threads = array();
        $sql = "SELECT *,T.id AS TID,TH.id AS THREADID, F.id AS FID,T.t_thread_count AS THREADCOUNT 
	FROM " . TBL_CMS_FORUMF . " F, " . TBL_CMS_FORUMTHREADS . " TH, " . TBL_CMS_FORUMTHEMES . " T, " . TBL_CMS_FORUMGROUPS . " G
	WHERE T.t_approval=1 
	AND T.id=TH.f_tid 
	AND F.id=TH.f_fid
	AND F.fn_gid=G.id
	ORDER BY TH.f_time DESC 
	LIMIT 100";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            if (!array_key_exists($row['TID'], $this->threads)) {
                $this->set_thread_opt($row, false);
                $this->set_theme_opt($row);
                $this->set_forum_opt_fe($row);
                $this->threads[$row['TID']] = $row;
            }
            if (count($this->threads) == $this->gbl_config['forum_latestthread_count'])
                break;
        }
        $this->smarty->assign('forum_latest_threads', $this->threads);
        if (count($this->threads) > 0) {
            $this->forum_id = $this->threads[0]['f_fid'];
        }
    }

    /**
     * forum_class::load_thread()
     * 
     * @param mixed $threadid
     * @param bool $dobbcode
     * @return
     */
    function load_thread($threadid, $dobbcode = true) {
        $this->thread = $this->db->query_first("SELECT *,id AS THREADID FROM " . TBL_CMS_FORUMTHREADS . " WHERE id=" . (int)$threadid);
        $this->set_thread_opt($this->thread, $dobbcode);
        if ($this->thread['THREADID'] > 0) {
            $this->forum_id = $this->thread['f_fid'];
            $this->theme_id = $this->thread['f_tid'];
        }
        $this->threadid = (int)$threadid;
        $this->load_files($this->threadid, $this->thread);
        $this->smarty->assign('forum_thread', $this->thread);
        return $this->thread;
    }


    /**
     * forum_class::cmd_fdownload()
     * 
     * @return void
     */
    function cmd_fdownload() {
        $file = $this->load_file_by_id($_GET['id']);
        $this->direct_download($this->forum_file_path . $file['f_file']);
    }


    /**
     * forum_class::cmd_send_img_tobrowser()
     * 
     * @return void
     */
    function cmd_send_img_tobrowser() {
        $file = $this->load_file_by_id($_GET['id']);
        self::send_image_to_browser($this->forum_file_path . $file['f_file']);
    }


    /**
     * forum_class::set_file_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_file_opt(&$row) {
        global $GRAPHIC_FUNC;
        $row['humanfilesize'] = human_file_size($row['f_size']);
        $row['uploadtime'] = date("d.m.Y H:i:s", $row['f_inserttime']);
        $row['thumbnail'] = '';
        $row['resu'] = '';
        $row['ispicture'] = false;
        if (($row['f_ext'] == 'jpg' || $row['f_ext'] == 'gif' || $row['f_ext'] == 'png' || $row['f_ext'] == 'jpeg') && file_exists($this->forum_file_path . $row['f_file'])) {
            list($width_px, $height_px) = getimagesize($this->forum_file_path . $row['f_file']);
            $row['resu'] = $width_px . 'x' . $height_px;
            $width = 30;
            $height = 30;
            $cache_file = './cache/thumb_' . $width . 'x' . $height . '_' . $row['f_file'];
            $row['thumbnail'] = thumbit_fe('../data/forum/' . $row['f_file'], $width, $height);
            $row['ispicture'] = true;
        }
        else {
            if (file_exists(CMS_ROOT . FILETYPES_PATH . $row['f_ext'] . '.png')) {
                $row['thumbnail'] = PATH_CMS . FILETYPES_PATH . $row['f_ext'] . '.png';
            }
        }

    }

    /**
     * forum_class::load_files()
     * 
     * @param mixed $threadid
     * @param mixed $thread
     * @return
     */
    function load_files($threadid, &$thread) {
        $threadid = (int)$threadid;
        $result = $this->db->query("SELECT *,id AS FILEID FROM " . TBL_CMS_FORUMFILES . " WHERE f_threadid=" . $threadid . " ORDER BY f_inserttime ASC");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_file_opt($row);
            $thread['filelist'][$row['FILEID']] = $row;
        }
    }

    /**
     * forum_class::cmd_savetheme()
     * 
     * @return void
     */
    function cmd_savetheme() {
        $tid = $this->save_theme($_POST['FORM'], $_POST['id']);
        $this->save_thread($_POST['FORMTHREAD'], $tid);
        $this->msg('{LBL_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=st&tid=' . $tid . '&page=' . (int)$_POST['page']);
        exit;
    }

    /**
     * forum_class::save_theme()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @return
     */
    function save_theme($FORM, $id) {
        $id = (int)$id;
        $FORM['t_name'] = strip_tags($FORM['t_name']);
        $FORUM = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMF . " WHERE id=" . (int)$FORM['t_fid']);
        $GROUP = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMGROUPS . " WHERE id=" . (int)$FORUM['fn_gid']);
        if ($id > 0) {
            update_table(TBL_CMS_FORUMTHEMES, 'id', $id, $FORM);
        }
        else {
            $FORM['t_time'] = time();
            $FORM['t_kid'] = $this->user_object['kid'];
            $id = insert_table(TBL_CMS_FORUMTHEMES, $FORM);
        }
        $this->update_themecount_of_forum($FORM['t_fid']);
        $this->add_theme_pageindex($GROUP['fg_name'], $FORUM['fn_name'], $FORM['t_name'], $id);
        $this->index_search->index_theme($id);
        return $id;
    }

    /**
     * forum_class::cmd_answerthread()
     * 
     * @return void
     */
    function cmd_answerthread() {
        $this->save_thread($_POST['FORMTHREAD'], (int)$_POST['FORMTHREAD']['f_tid'], (int)$_POST['threadid']);
        $this->msg('{LBL_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?cmd=st&tid=' . (int)$_POST['FORMTHREAD']['f_tid'] . '&page=' . $_POST['page']);
        exit;
    }

    /**
     * forum_class::save_thread()
     * 
     * @param mixed $FORM
     * @param mixed $themeid
     * @param integer $id
     * @return
     */
    function save_thread($FORM, $themeid, $id = 0) {
        $id = (int)$id;
        if ($this->user_object['kid'] > 0) {
            $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_FORUMTHEMES . " WHERE id=" . (int)$themeid);
            $FORM['f_text'] = $FORM['f_text'];
            $FORM['f_tid'] = (int)$themeid;
            $FORM['f_fid'] = $T['t_fid'];
            if ($id > 0) {
                update_table(TBL_CMS_FORUMTHREADS, 'id', $id, $FORM);
            }
            else {
                $FORM['f_time'] = time();
                $FORM['f_kid'] = $this->user_object['kid'];
                $FORM['f_user'] = $this->db->real_escape_string($this->user_object['vorname']) . "|" . $this->db->real_escape_string($this->user_object['nachname']) . "|" . $this->
                    db->real_escape_string($this->user_object['username']);
                $id = insert_table(TBL_CMS_FORUMTHREADS, $FORM);
            }
            $this->file_upload($_FILES, $id);
            $this->update_threadcount_of_theme_and_forum($themeid);
            $this->index_search->index_theme($themeid);
        }
        return $id;
    }


    /**
     * forum_class::file_upload()
     * 
     * @param mixed $FILES
     * @param mixed $id
     * @return
     */
    function file_upload($FILES, $id) {
        $id = (int)$id;
        if (!is_dir($this->forum_file_path)) {
            mkdir($this->forum_file_path, 0755);
        }
        if (isset($FILES['datei']['name']) && $FILES['datei']['name'] != "") {
            if (!validate_upload_file($FILES['datei'])) {
                $this->msge($_SESSION['upload_msge']);
                header('location: ' . $_SERVER['PHP_SELF'] . '?cmd=answer&threadid=' . $_POST['threadid'] . '&epage=' . $_GET['epage']);
                exit;
            }
            $k = 0;
            $new_file_name = $this->format_file_name($FILES['datei']['name']);
            $new_file_name = self::unique_filename($this->forum_file_path, $new_file_name);
            move_uploaded_file($FILES['datei']['tmp_name'], $this->forum_file_path . $new_file_name);
            chmod($this->forum_file_path . $new_file_name, 0755);
            $AFILE = array();
            $AFILE['f_file'] = $new_file_name;
            $AFILE['f_ext'] = self::get_ext($new_file_name);
            $AFILE['f_threadid'] = $id;
            $AFILE['f_inserttime'] = time();
            $AFILE['f_size'] = filesize($this->forum_file_path . $filename);
            insert_table(TBL_CMS_FORUMFILES, $AFILE);
            return true;
        }
        else
            return false;
    }

    /**
     * forum_class::update_all_counter()
     * 
     * @return
     */
    function update_all_counter() {
        $result = $this->db->query("SELECT *,F.id AS FID	FROM  " . TBL_CMS_FORUMF . " F ");
        while ($row = $this->db->fetch_array_names($result)) {
            $thread_count = get_data_count(TBL_CMS_FORUMTHREADS, 'id', "f_fid=" . $row['FID']);
            $theme_count = get_data_count(TBL_CMS_FORUMTHEMES, 'id', "t_fid=" . $row['FID']);
            $this->db->query("UPDATE " . TBL_CMS_FORUMF . " SET fn_thread_count=" . $thread_count . ", fn_theme_count=" . $theme_count . " WHERE id=" . $row['FID']);
        }
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FORUMTHEMES);
        while ($row = $this->db->fetch_array_names($result)) {
            $thread_count = get_data_count(TBL_CMS_FORUMTHREADS, 'id', "f_tid=" . $row['id']);
            $this->db->query("UPDATE " . TBL_CMS_FORUMTHEMES . " SET t_thread_count=" . $thread_count . " WHERE id=" . $row['id']);
        }

    }

    /**
     * forum_class::autorun()
     * 
     * @return
     */
    function autorun() {
        global $user_object;
        $this->set_user($user_object);
        $this->load_latest_threads();
        $this->smarty->assign('forum_path', PATH_CMS . 'includes/modules/forum/');
        $this->smarty->assign('FORUM_FILE_PATH', $this->forum_file_path);
    }


    /**
     * forum_class::parse_forum()
     * 
     * @param mixed $params
     * @return
     */
    function parse_forum($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_FORUMINLAY_')) {
            preg_match_all("={TMPL_FORUMINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $this->smarty->assign('TMPL_FORUM_' . $cont_matrix_id, $PLUGIN_OPT);
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$PLUGIN_OPT['tplid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=forumsettings value=$TMPL_FORUM_' . $cont_matrix_id . ' %>                
                <% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
            }
            $this->init();
            $this->parse_to_smarty();
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * forum_class::cmd_forumsearch()
     * 
     * @return void
     */
    function cmd_forumsearch() {
        $search_result = array();
        $this->index_search->search($_REQUEST['FORM'], $search_result);
        $this->FORUM['searched_themes'] = array(
            'search_result' => $search_result,
            'search_count' => count($search_result),
            'search_time' => round(($end - $start), 4),
            );
        $this->parse_to_smarty();
        echo_template_fe('forum_forum-search-result');
    }

    function cmd_forumsearchdetail() {
        $search_result = array();
        $this->index_search->search($_REQUEST['FORM'], $search_result);
        $this->FORUM['searched_themes'] = array(
            'search_result' => $search_result,
            'search_count' => count($search_result),
            'search_time' => round(($end - $start), 4),
            );
        $this->parse_to_smarty();
        echo_template_fe('forum_forum-search-result');
    }

    /**
     * forum_class::cmd_load_forum_js()
     * 
     * @return void
     */
    function cmd_load_forum_js() {
        $this->init();
        $this->parse_to_smarty();
        echo_template_fe('forum_list');
    }

    /**
     * forum_class::cmd_freport()
     * 
     * @return void
     */
    function cmd_freport() {
        if ($this->user_object['kid'] > 0) {
            $threadid = (int)$_GET['threadid'];
            $themeid = (int)$_GET['themeid'];
            $THREAD = $this->load_thread($threadid);
            $THEME = $this->set_theme_opt($this->load_theme_by_id($themeid));
            $email_content = file_get_contents(CMS_ROOT . 'includes/modules/forum/etpl/email.report.tpl');
            $REPORT = array(
                'thread' => $THREAD,
                'theme' => $THEME,
                'domain' => $_SERVER['SERVER_NAME'],
                'reporter' => $this->user_object,
                'reported_time' => date('d.m.Y H:i:s'));
            $this->smarty->assign('REPORT', $REPORT);
            $email_content = smarty_compile($email_content);
            $subject = smarty_compile('REPORT [<%$REPORT.thread.id%>]');
            $result = $this->db->query("SELECT K.* FROM  " . TBL_CMS_CUSTTOGROUP . " G, " . TBL_CMS_CUST . " K 
        WHERE G.kid=K.kid AND G.gid=" . $this->gblconfig->forum_modgroup);
            while ($row = $this->db->fetch_array_names($result)) {
                $this->LOGCLASS->addLog('SENDMAIL', 'forum thread report:threadid ' . $THREAD['id'] . ', ' . $THREAD['f_kid'] . '; report from:' . $this->user_object['username']);
                send_easy_mail_to($row['email'], $email_content, $subject);
            }
            $this->msg('Beitrag reported');
        }
        else {
            firewall_class::report_hacking('Forum, report thread with no access');
            $this->msge('No access');
        }
        $this->ej();
    }

    /**
     * forum_class::psitemap()
     * 
     * @param mixed $params
     * @return
     */
    function psitemap($params) {
        $tree = $params['menu_arr'];
        $groups = $this->load_groups_fe();
        $tree['forum'] = array(
            'catlabel' => 'Forum',
            'catlink' => PATH_CMS . 'forum.html',
            'children' => array());
        foreach ($groups as $gkey => $group) {
            foreach ($group['foren'] as $key => $forum) {
                $tree['forum']['children'][] = array('catlabel' => $forum['fn_name'], 'catlink' => $forum['forumlink']);
            }
        }
        $params['menu_arr'] = $tree;
        return $params;
    }

    /**
     * forum_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='forum' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $params = array_merge($params, $SM);
            $groups = $this->load_groups_fe();
            $params['urls'][] = array(
                'frecvent' => $params['sm_changefreq'],
                'priority' => $params['sm_priority'],
                'url' => self::get_http_protocol() . '://' . $_SERVER['SERVER_NAME'] . PATH_CMS . 'forum.html',
                );
            foreach ($groups as $gkey => $group) {
                foreach ($group['foren'] as $key => $forum) {
                    #   $tree['forum']['children'][] = array('catlabel' => $forum['fn_name'], 'catlink' => $forum['forumlink']);
                    $params['urls'][] = array(
                        'frecvent' => $params['sm_changefreq'],
                        'priority' => $params['sm_priority'],
                        'url' => self::get_http_protocol() . '://' . $_SERVER['SERVER_NAME'] . $forum['forumlink'],
                        );
                }
            }
        }
        return (array )$params;
    }

}
