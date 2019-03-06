<?php

/**
 * @package    news
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


DEFINE('TBL_CMS_NEWSLIST', TBL_CMS_PREFIX . 'news');
DEFINE('TBL_CMS_NEWSCONTENT', TBL_CMS_PREFIX . 'newscontent');
DEFINE('TBL_CMS_NEWSGROUPS', TBL_CMS_PREFIX . 'news_groups');
DEFINE('TBL_CMS_NEWSFILES', TBL_CMS_PREFIX . 'news_files');

class news_class extends modules_class {

    var $nodes = null;
    var $treeleaf = array();

    var $langid = 1;
    var $pageid = 860;


    var $news = array();
    var $newslist = array();
    var $NEWSGROUP = array();


    /**
     * news_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID;
        parent::__construct();
        if (isset($_GET['uselang'])) {
            $this->langid = intval($_GET['uselang']);
        }
        else {
            $this->langid = $GBL_LANGID;
        }
        $this->smarty->assign('NEWS_PATH', NEWS_PATH);
    }

    /**
     * news_class::setApprove()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function setApprove($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET approval='" . (($value == 1) ? 1 : 0) . "' WHERE id='" . intval($id) . "' LIMIT 1");
    }


    /**
     * news_class::file_upload()
     * 
     * @param mixed $FILES
     * @param mixed $id
     * @return
     */
    function file_upload($FILES, $id) {
        if ($FILES['dateiicon']['name'] != "") {
            if (!validate_upload_file($FILES['dateiicon'])) {
                $this->msge($_SESSION['upload_msge']);
                $this->ej();
            }
            $new_file_name = $this->format_file_name($FILES['dateiicon']['name']);
            if (!is_dir(CMS_ROOT . NEWS_PATH))
                mkdir(CMS_ROOT . NEWS_PATH, 0775);
            while (file_exists(CMS_ROOT . NEWS_PATH . $new_file_name)) {
                $k++;
                $RetVal = explode('.', $new_file_name);
                $file_extention = $RetVal[count($RetVal) - 1];
                $new_file_name = str_replace('.' . $file_extention, '', $new_file_name) . '-' . $k . '.' . $file_extention;
            }
            $new_file_name = 'ICON_' . $new_file_name;
            move_uploaded_file($FILES['dateiicon']['tmp_name'], CMS_ROOT . NEWS_PATH . $new_file_name);
            chmod(CMS_ROOT . NEWS_PATH . $new_file_name, 0755);
            $FINFO = array();
            $FINFO['n_icon'] = $new_file_name;
            update_table(TBL_CMS_NEWSLIST, 'id', $id, $FINFO);
        }

        if ($FILES['datei']['name'] != "") {
            if (!validate_upload_file($FILES['datei'])) {
                $this->msge($_SESSION['upload_msge']);
                $this->ej();
            }
            $k = 0;
            $new_file_name = $this->format_file_name($FILES['datei']['name']);
            if (!is_dir(CMS_ROOT . NEWS_PATH))
                mkdir(CMS_ROOT . NEWS_PATH, 0775);
            while (file_exists(CMS_ROOT . NEWS_PATH . $new_file_name)) {
                $k++;
                $RetVal = explode('.', $new_file_name);
                $file_extention = $RetVal[count($RetVal) - 1];
                $new_file_name = str_replace('.' . $file_extention, '', $new_file_name) . '-' . $k . '.' . $file_extention;
            }
            move_uploaded_file($FILES['datei']['tmp_name'], CMS_ROOT . NEWS_PATH . $new_file_name);
            chmod(CMS_ROOT . NEWS_PATH . $new_file_name, 0755);
            $RetVal = explode('.', $new_file_name);
            $file_extention = strtolower($RetVal[count($RetVal) - 1]);
            $AFILE = array();
            $AFILE['f_file'] = $new_file_name;
            $AFILE['f_ext'] = $file_extention;
            $AFILE['f_nid'] = $id;
            $AFILE['f_inserttime'] = time();
            $AFILE['f_size'] = filesize(CMS_ROOT . NEWS_PATH . $filename);
            insert_table(TBL_CMS_NEWSFILES, $AFILE);
            return true;
        }
        else
            return false;

    }

    /**
     * news_class::inform_admins()
     * 
     * @param mixed $type
     * @return
     */
    function inform_admins($type) {
        $admin_email_text = 'Hello,' . "\n\n";
        $temp = $this->news;
        unset($temp['content']);
        foreach ($temp as $key => $value) {
            $admin_email_text .= $key . ' = ' . $value . "\n";
        }
        if (ISADMIN == 0) {
            if ($type == "INSERT") {
                #send_admin_mail('{LBL_NEWNEWS} ID:' . $this->news['NID'] . ' "' . $this->news['title'] . '" {LBL_OF} ' . $this->news['n_author'], $admin_email_text);
                $smarty_arr = array('mail' => array('subject' => '{LBL_NEWNEWS} ID:' . $this->news['NID'] . ' "' . $this->news['title'] . '" {LBL_OF} ' . $this->news['n_author'],
                            'content' => $admin_email_text));
                send_admin_mail(900, $smarty_arr); #general mail template
            }
            if ($type == "UPDATE") {
                #  send_admin_mail('{LBL_UPDNEWS} ID:' . $this->news['NID'] . ' "' . $this->news['title'] . '" {LBL_OF} ' . $this->news['n_author'], $admin_email_text);
                $smarty_arr = array('mail' => array('subject' => '{LBL_UPDNEWS} ID:' . $this->news['NID'] . ' "' . $this->news['title'] . '" {LBL_OF} ' . $this->news['n_author'],
                            'content' => $admin_email_text));
                send_admin_mail(900, $smarty_arr); #general mail template
            }
        }
    }

    /**
     * news_class::gen_new_news()
     * 
     * @param mixed $langid
     * @return
     */
    function gen_new_news($langid) {
        $FORM = $FORM_CON = array();
        $FORM['ndate'] = date('Y-m-d');
        $FORM['timeint'] = time();
        $FORM['n_lastchange'] = time();
        if ($_SESSION['mitarbeiter'] != 100 && $_SESSION['mitarbeiter'] > 0)
            $FORM['mid'] = $_SESSION['mitarbeiter'];
        $id = insert_table(TBL_CMS_NEWSLIST, $FORM);
        $FORM_CON['title'] = 'NEU/NEW';
        $FORM_CON['nid'] = $id;
        $FORM_CON['lang_id'] = $langid;
        $conid = insert_table(TBL_CMS_NEWSCONTENT, $FORM_CON);
        $this->load_news($id);
        $this->inform_admins('INSERT');
        return array($id, $conid);
    }

    /**
     * news_class::add_pageindex()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @param string $lngid
     * @return
     */
    function add_pageindex($FORM, $id, $lngid = '1') {
        $query = array(
            'cmd' => 'show',
            'id' => $id,
            'gid' => $FORM['group_id']);
        $this->connect_to_pageindex($this->gen_detail_link($FORM), $query, $id, 'news', $lngid);
    }

    /**
     * news_class::save_news()
     * 
     * @param mixed $FORM
     * @param mixed $FORMLANG
     * @param mixed $id
     * @param mixed $conid
     * @param mixed $FILES
     * @return
     */
    function save_news($FORM, $FORMLANG, $id, $conid, $FILES) {
        $id = intval($id);
        $FORM['ndate'] = format_date_to_sql_date($FORM['ndate']);
        $FORM['n_lastchange'] = time();
        $FORM['timeint'] = strtotime($FORM['ndate']);
        if ($_SESSION['mitarbeiter'] != 100 && $_SESSION['mitarbeiter'] > 0 && ISADMIN == 1)
            $FORM['mid'] = $_SESSION['mitarbeiter'];
        update_table(TBL_CMS_NEWSLIST, 'id', $id, $FORM);
        if ($conid > 0)
            update_table(TBL_CMS_NEWSCONTENT, 'id', $conid, $FORMLANG);
        else
            insert_table(TBL_CMS_NEWSCONTENT, $FORMLANG);
        $this->add_pageindex(array_merge($FORM, $FORMLANG), $id, $FORMLANG['lang_id']);
        $this->file_upload($FILES, $id);
        $this->LOGCLASS->addLog('UPDATE', 'News [' . $id . '] ' . $FORMLANG['title']);
        $this->load_news($id);
        $this->inform_admins('UPDATE');
    }


    /**
     * news_class::set_kid()
     * 
     * @param mixed $kid
     * @param mixed $id
     * @return
     */
    function set_kid($kid, $id) {
        $FORM = array();
        $FORM['n_kid'] = $kid;
        $KOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $kid);
        $FORM['n_author'] = $KOBJ['vorname'] . ', ' . $KOBJ['nachname'];
        update_table(TBL_CMS_NEWSLIST, 'id', $id, $FORM);
        $this->load_news($id);
    }

    /**
     * news_class::del_icon()
     * 
     * @param mixed $id
     * @return
     */
    function del_icon($id) {
        $id = intval($id);
        $nfile = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSLIST . " WHERE id=" . $id);
        if (delete_file(CMS_ROOT . NEWS_PATH . $nfile['n_icon'])) {
            $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET n_icon='' WHERE id=" . $id);
            $this->LOGCLASS->addLog('DELETE', 'News iconfile' . $nfile['n_icon'] . ' from ID:' . $id);
            return true;
        }
        return false;
    }

    /**
     * news_class::del_afile()
     * 
     * @param mixed $id
     * @return
     */
    function del_afile($id) {
        $id = intval($id);
        $nfile = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSFILES . " WHERE id=" . $id);
        if (delete_file(CMS_ROOT . NEWS_PATH . $nfile['f_file'])) {
            $this->db->query("DELETE FROM " . TBL_CMS_NEWSFILES . " WHERE id=" . $id);
            $this->LOGCLASS->addLog('DELETE', 'News file' . $nfile['f_file'] . ' from ID:' . $id);
            return true;
        }
        return false;
    }


    /**
     * news_class::set_newslist_options()
     * 
     * @param mixed $row
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function set_newslist_options($row, $PLUGIN_OPT = array()) {
        global $HTA_CLASS_CMS;
        $row['fck'] = create_html_editor('FORM_CON[content]', $row['content'], 500, 'Standard');
        $row['is_today'] = date('Y-m-d') == $row['ndate'];
        $row['relative_time'] = self::relative_date($row['timeint']);
        $row['relative_time_class'] = $this->color_class($row['relative_time']);
        list($row['date_year'], $row['date_month'], $row['date_day']) = explode('-', $row['ndate']);
        $row['date_month'] = my_date('M', $row['ndate']);
        $row['ndate'] = (($row['ndate'] != '0000-00-00') ? my_date('d.m.Y', $row['ndate']) : '');
        $row['date'] = $row['ndate'];
        $row['date_print'] = date('d.m.Y H:i:s', $row['timeint']);
        $row['n_lastchange'] = date('d.m.Y H:i:s', $row['n_lastchange']);
        $row['implement'] = '{TMPL_NEWSINLAY_' . $row['NID'] . '}';
        $row['n_kid'] = ($row['n_kid'] == 0) ? -1 : $row['n_kid'];
        $row['group_ident'] = 'NIDENT' . $row['GINDENT'];
        $row['image'] = $row['n_icon'];
        if (defined('ISADMIN') && ISADMIN == 1) {
            $row['icon_edit'] = kf::gen_edit_icon($row['NID'], '&epage=' . $_GET['epage'], 'edit', 'id', $_SERVER['PHP_SELF']);
            $row['icon_del'] = kf::gen_del_icon_reload($row['NID'], 'a_del', '{LBL_CONFIRM}', '&epage=' . $_GET['epage']);
            $row['icon_approve'] = kf::gen_approve_icon($row['NID'], $row['approval']);
            $row['n_icon'] = ($row['n_icon'] != "") ? kf::gen_thumbnail('/' . NEWS_PATH . $row['n_icon'], $this->gbl_config['news_icon_width'], $this->gbl_config['news_icon_height']) :
                '';
        }
        else {
            $row['date'] = my_date('d.m.Y', $row['ndate']);
            $row['introduction_txt'] = $row['introduction'];
            $row['introduction'] = strip_tags($row['introduction']);
            $row['detail_link'] = $this->gen_detail_link($row);
            if ($PLUGIN_OPT['news_icon_width'] > 0) {
                $row['n_icon'] = ($row['n_icon'] != "") ? gen_thumb_image(NEWS_PATH . $row['n_icon'], $PLUGIN_OPT['news_icon_width'], $PLUGIN_OPT['news_icon_height'], $PLUGIN_OPT['news_icon_type']) :
                    '';
            }
            else {
                $row['n_icon'] = ($row['n_icon'] != "") ? gen_thumb_image(NEWS_PATH . $row['n_icon'], $this->gbl_config['news_icon_width'], $this->gbl_config['news_icon_height']) :
                    '';
            }
        }
        $row['link'] = $this->gen_detail_link($row);
        return $row;
    }

    /**
     * news_class::gen_detail_link()
     * 
     * @param mixed $row
     * @return
     */
    function gen_detail_link($row) {
        $prefix_lng = ($_SESSION['GBL_LANGID'] == $this->gbl_config['std_lang_id']) ? '' : '/' . $_SESSION['GBL_LOCAL_ID'];
        return $prefix_lng . '/news/' . $this->format_file_name($row['title']) . '.html';
    }

    /**
     * news_class::load_news()
     * 
     * @param mixed $id
     * @return
     */
    function load_news($id) {
        global $GRAPHIC_FUNC;
        $id = intval($id);
        if ($id <= 0)
            return false;
        $this->news = $this->db->query_first("SELECT *, A.id AS NID, C.id AS CID,K.*, AG.group_id AS GINDENT FROM 
 		(" . TBL_CMS_NEWSLIST . " A 	LEFT JOIN " . TBL_CMS_NEWSCONTENT . " C ON (C.nid=A.id AND C.lang_id=" . $this->langid . " )),
 		(" . TBL_CMS_NEWSLIST . " AAA	LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=AAA.mid)),
 		(" . TBL_CMS_NEWSLIST . " AA 	LEFT JOIN " . TBL_CMS_CUST . " K ON (AA.n_kid=K.kid)),
 		(" . TBL_CMS_NEWSLIST . " AG 	INNER JOIN " . TBL_CMS_NEWSGROUPS . " NG ON (NG.id=AG.group_id))
 		WHERE AA.id=A.id AND AAA.id=A.id AND AG.id=A.id
 		AND A.id=" . $id . " 
 		LIMIT 1");
        $this->news = $this->set_newslist_options($this->news);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSFILES . " WHERE f_nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['humanfilesize'] = human_file_size($row['f_size']);
            $row['uploadtime'] = date("d.m.Y H:i:s", $row['f_inserttime']);
            $row['thumbnail'] = '';
            $row['resu'] = '';
            if (($row['f_ext'] == 'jpg' || $row['f_ext'] == 'gif' || $row['f_ext'] == 'png' || $row['f_ext'] == 'jpeg')) {
                list($width_px, $height_px) = getimagesize(CMS_ROOT . NEWS_PATH . $row['f_file']);
                $row['resu'] = $width_px . 'x' . $height_px;
            }
            if (ISADMIN == 1) {
                $row['icon_del'] = kf::gen_del_icon_reload($row['id'], 'a_delfile', ' {
                    LBL_CONFIRM}
                ', ' & uselang = ' . $this->langid . ' & id = ' . $id . ' & epage = ' . $_GET['epage'], 'fileid');
                if ($row['resu'] != "")
                    $row['thumbnail'] = PATH_CMS . 'admin / ' . CACHE . $GRAPHIC_FUNC->makeThumb(' . . / ' . NEWS_PATH . $row['f_file'], 30, 30, 'admin /
                    ' . CACHE, true, 'crop');
            }
            else {
                if ($row['resu'] != "")
                    $row['thumbnail'] = PATH_CMS . CACHE . $GRAPHIC_FUNC->makeThumb(' . / ' . NEWS_PATH . $row['f_file'], 30, 30, ' . / ' . CACHE, true, 'crop
                    ');
            }
            $this->news['filelist'][] = $row;
        }
        if (ISADMIN != 1)
            $this->db->query("UPDATE " . TBL_CMS_NEWSLIST . " SET views=views+1 WHERE id=" . $id);
        return $this->news;
    }

    /**
     * news_class::load_group()
     * 
     * @param mixed $gid
     * @return
     */
    function load_group($gid) {
        $gid = intval($gid);
        $this->NEWSGROUP = $this->db->query_first("SELECT *,id AS NGID FROM " . TBL_CMS_NEWSGROUPS . " WHERE id=" . $gid . " LIMIT 1");
        $this->NEWSGROUP['implement'] = ' {
                    TMPL_NEWSINLAY_' . $this->NEWSGROUP['NGID'] . '}
                ';
        $this->smarty->assign('NEWSGROUP', $this->NEWSGROUP);
    }

    /**
     * news_class::load_newslist()
     * 
     * @param mixed $order
     * @param mixed $dc
     * @param mixed $gid
     * @param mixed $langid
     * @param integer $count
     * @param mixed $PLUGIN_OPT
     * @param integer $cont_matrix_id
     * @return
     */
    function load_newslist($order, $dc, $gid, $langid, $count = 0, $PLUGIN_OPT = array(), $cont_matrix_id = 0) {
        $this->newss_approved = array();
        $this->newss_notapproved = array();
        $this->newss = array();
        $count = (int)$count;
        $dc = ($dc == 'DESC') ? 'DESC' : 'ASC';
        if ($dc == "")
            $dc = "DESC";
        if ($order == "")
            $order = "timeint";
        $flipped_dc = ($dc == "ASC") ? "DESC" : "ASC";
        $count_approved = 0;
        $result = $this->db->query("SELECT A.*,AC.*, A.id AS NID,K.*,COUNT(AF.id) AS AFCOUNT, A.group_id AS GINDENT FROM 
 		(" . TBL_CMS_NEWSLIST . " A 	 LEFT JOIN " . TBL_CMS_CUST . " K ON (A.n_kid=K.kid)),
 		(" . TBL_CMS_NEWSLIST . " AA 	 LEFT JOIN " . TBL_CMS_NEWSFILES . " AF ON (AA.id=AF.f_nid)),
 		(" . TBL_CMS_NEWSLIST . " AAA  LEFT JOIN " . TBL_CMS_NEWSCONTENT . " AC ON (AAA.id=AC.nid AND AC.lang_id=" . $langid . ")),
 		(" . TBL_CMS_NEWSLIST . " AAAA LEFT JOIN " . TBL_CMS_ADMINS . " M ON (M.id=AAAA.mid))
 		 WHERE A.group_id=" . intval($gid) . " AND A.approval=1 
 		 AND AA.id=A.id
 		 AND AAA.id=A.id
 		 AND AAAA.id=A.id
 		 GROUP BY A.id
 		 ORDER BY A." . $order . " " . $dc . (($count > 0) ? " LIMIT " . (int)$count : ""));
        $k = 0;
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->set_newslist_options($row, $PLUGIN_OPT);
            $k++;
            if ($k == 1)
                $first_object = $row;
            $this->newss[] = $row;

            if ($row['approval'] == 1) {
                $count_approved++;
                $this->newss_approved[] = $row;
            }
            else {
                $this->newss_notapproved[] = $row;
            }
        }

        $this->smarty->assign('TMPL_NEWSGROUP_' . intval($cont_matrix_id), $this->newss_approved);
        $this->smarty->assign('newsgroup', $first_object);
        $this->smarty->assign('allnewslist', array(
            'table' => $this->newss,
            'newslist_approved' => $this->newss_approved,
            'newslist_notapproved' => $this->newss_notapproved,
            'count' => count($this->newss),
            'count_approved' => $count_approved,
            'flipped_dc' => $flipped_dc,
            ));
    }


    /**
     * news_class::delete_news()
     * 
     * @param mixed $id
     * @return
     */
    function delete_news($id) {
        $id = intval($id);
        $this->remove_from_page_index('news', $id);
        $this->del_icon($id);
        $this->db->query("DELETE FROM " . TBL_CMS_NEWSLIST . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_NEWSCONTENT . " WHERE nid=" . $id);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NEWSFILES . " WHERE f_nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            delete_file(CMS_ROOT . NEWS_PATH . $row['f_file']);
        }
        $this->LOGCLASS->addLog('DELETE', 'News[' . $id . ']deleted . ');
    }


}

?>
