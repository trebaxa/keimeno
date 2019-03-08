<?php

/**
 * @package    tagcloud
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class tagcloud_class extends keimeno_class {

    var $tag_words = array();
    var $tag_table = "";
    var $tag_table_rel = "";
    var $system_path = "";
    var $minlen = 0;
    var $maxlen = 0;

    /**
     * tagcloud_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->tag_words = array();
        $this->langid = ((int)$GBL_LANGID == 0) ? 1 : $GBL_LANGID;
    }

    /**
     * tagcloud_class::cmd_approvetag()
     * 
     * @return
     */
    function cmd_approvetag() {
        $this->approve_tag($_GET['ident'], $_GET['value']);
        $this->hard_exit();
    }

    /**
     * tagcloud_class::approve_tag()
     * 
     * @param mixed $id
     * @param mixed $value
     * @return
     */
    function approve_tag($id, $value) {
        $this->db->query("UPDATE " . TBL_CMS_TAGS . " SET tag_approved='" . $value . "' WHERE id='" . (int)$id . "' LIMIT 1");
    }

    /**
     * tagcloud_class::gen_tag_link()
     * 
     * @param mixed $word
     * @return
     */
    function gen_tag_link($word) {
        $prefix_lng = ($_SESSION['GBL_LANGID'] == $this->gbl_config['std_lang_id']) ? '' : $_SESSION['GBL_LOCAL_ID'] . '/';
        $link = SSL_PATH_SYSTEM . PATH_CMS . $prefix_lng . $this->gblconfig->tagcloud_perma_link . '/' . $this->format_file_name($word) . '.html';
        return $link;
    }

    /**
     * tagcloud_class::delete_tag()
     * 
     * @param mixed $id
     * @return
     */
    function delete_tag($id) {
        $this->db->query("DELETE FROM " . TBL_CMS_TAGS . " WHERE id=" . (int)$id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_TAGS_REL . " WHERE tag_id=" . (int)$id);
    }

    /**
     * tagcloud_class::delete_tags_smaller_count()
     * 
     * @param mixed $tag_count
     * @return
     */
    function delete_tags_smaller_count($tag_count) {
        $tag_count = (int)$tag_count;
        $sql = "SELECT id,tag_id, COUNT( tag_id ) AS Count
	FROM " . TBL_CMS_TAGS_REL . "
	GROUP BY tag_id
	HAVING Count <= " . $tag_count;
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $ids[] = $row['tag_id'];
        }
        while (count($ids) > 0) {
            $remove_ids = $ids;
            $k = 0;
            $sql = "";
            foreach ($remove_ids as $key => $tag_id) {
                $sql .= ($sql != '') ? ' OR tag_id=' . $tag_id : ' tag_id=' . $tag_id;
                $k++;
                if ($k >= 10) {
                    break;
                }
                unset($ids[$key]);
            }
            if (!empty($sql))
                $this->db->query("DELETE FROM " . TBL_CMS_TAGS_REL . " WHERE " . $sql);
        }
    }

    /**
     * tagcloud_class::delete_all_tags()
     * 
     * @return
     */
    function delete_all_tags() {
        $this->db->query("TRUNCATE TABLE " . TBL_CMS_TAGS);
        $this->db->query("TRUNCATE TABLE " . TBL_CMS_TAGS_REL);
    }


    /**
     * tagcloud_class::mass_delete_tag()
     * 
     * @param mixed $ids
     * @return
     */
    function mass_delete_tag($ids) {
        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $key => $id) {
                $this->delete_tag($id);
            }
        }
    }

    /**
     * tagcloud_class::mass_approve_tag()
     * 
     * @param mixed $ids
     * @return
     */
    function mass_approve_tag($ids) {
        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $key => $id) {
                $this->approve_tag($id, 1);
            }
        }
    }

    /**
     * tagcloud_class::mass_disapprove_tag()
     * 
     * @param mixed $ids
     * @return
     */
    function mass_disapprove_tag($ids) {
        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $key => $id) {
                $this->approve_tag($id, 0);
            }
        }
    }


    /**
     * tagcloud_class::build_relation_printout()
     * 
     * @param mixed $column
     * @param mixed $targettable
     * @param mixed $target_column
     * @param mixed $target_title
     * @return
     */
    function build_relation_printout($column, $targettable, $target_column, $target_title) {
        $rela_desc = $rela = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TAGS_REL . " T, " . $targettable . " TR
	WHERE TR." . $target_column . "=T." . $column . "	
	GROUP BY T.id
	ORDER BY TR." . $target_title);
        while ($row = $this->db->fetch_array_names($result)) {
            $rela[$row['tag_id']][$row[$target_column]] = array('title' => $row[$target_title]);
            $rela_desc[$row['tag_id']][$row[$target_column]] = $row[$target_title];
        }
        foreach ($this->tag_words as $tag_name => $value) {
            $this->tag_words[$tag_name]['reltarget'] = $rela[$value['TAGID']];
            $this->tag_words[$tag_name]['reltarget_string'] = implode(", ", $rela_desc[$value['TAGID']]);
        }
    }

    /**
     * tagcloud_class::save_single_tag()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @return
     */
    function save_single_tag($FORM, $id) {
        $id = (int)$id;
        update_table(TBL_CMS_TAGS, "id", $id, $FORM);
    }

    /**
     * tagcloud_class::delete_realtions()
     * 
     * @param mixed $relids_array
     * @return
     */
    function delete_realtions($relids_array) {
        if (is_array($relids_array)) {
            $sql = "";
            foreach ($relids_array as $key => $relid) {
                $sql .= ($sql != "") ? " OR id=" . $relid : "id=" . $relid;
            }
            if ($sql != "")
                $this->db->query("DELETE FROM " . TBL_CMS_TAGS_REL . " WHERE " . $sql);
        }
    }

    /**
     * tagcloud_class::load_single_tag()
     * 
     * @param mixed $id
     * @param string $column
     * @param string $targettable
     * @param string $target_column
     * @param string $target_title
     * @return
     */
    function load_single_tag($id, $column = '', $targettable = '', $target_column = '', $target_title = '') {
        $id = (int)$id;
        $TAG = $this->db->query_first("SELECT T.*,T.id AS TAGID,T.tag_name AS TAGNAME
	FROM " . TBL_CMS_TAGS . " T, " . TBL_CMS_LANG . " L
	WHERE T.id=" . $id . " AND L.id=T.tag_langid");
        $this->smarty->assign('singletag', $TAG);
        $result = $this->db->query("SELECT T.*,T.id AS RELID,TA.*,TA." . $target_title . " AS TTITLE FROM " . TBL_CMS_TAGS_REL . " T, " . $targettable . " TA 
		WHERE TA." . $target_column . "=T." . $column . " AND T.tag_id=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $relation[] = $row;
        }
        $this->smarty->assign('singletag_relation', $relation);
    }

    /**
     * tagcloud_class::load_tags()
     * 
     * @param string $column
     * @param string $targettable
     * @param string $target_column
     * @param string $target_title
     * @param string $orderby
     * @return
     */
    function load_tags($column = '', $targettable = '', $target_column = '', $target_title = '', $orderby = 'TAGNAME') {
        $this->tag_words = array();
        $orderby = (empty($orderby) ? 'TAGNAME' : (string )$orderby);
        $result = $this->db->query("SELECT T.*,T.id AS TAGID, COUNT(TR.tag_id) AS TAGCOUNT,T.tag_name AS TAGNAME,L.bild,L.post_lang,TR.* 
	FROM " . TBL_CMS_LANG . " L," . TBL_CMS_TAGS . " T, " . TBL_CMS_TAGS_REL . " TR
	WHERE L.id=T.tag_langid AND TR.tag_id=T.id AND L.id=" . $this->langid . "
	GROUP BY T.id
	ORDER BY " . $orderby);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icon_del'] = kf::gen_del_icon_reload($row['TAGID'], 'a_deltag', '{LBL_CONFIRM}', '&epage=' . $_GET['epage']);
            $row['icon_approve'] = kf::gen_approve_icon($row['TAGID'], $row['tag_approved'], 'approvetag');
            #kf::gen_approve_icon_reload($row['TAGID'], $row['tag_approved'], '&epage=' . $_GET['epage']);
            $row['icon_edit'] = kf::gen_edit_icon($row['TAGID'], '', 'edit', '&epage=' . $_GET['epage']);
            $row['icon_edit'] = kf::gen_edit_icon($row['TAGID'], '&epage=' . $_GET['epage'], 'edit');
            $row['tag_createdate'] = my_date('d.m.Y', $row['tag_createdate']);
            $img = "";
            if (!empty($row['bild']))
                $img = '<img title="' . $row['post_lang'] . '" alt="' . $row['post_lang'] . '" src="' . kf::gen_thumbnail('/images/' . $row['bild'], 20, 20) . '" >';
            $row['licon'] = $img;
            $this->tag_words[$row['TAGID']] = $row;
            if ($row['tag_approved'] == 1)
                $approved++;
            else
                $notapproved++;
        }
        #ksort($this->tag_words);
        if ($orderby == 'TAGNAME') {
            $this->tag_words = sort_db_result($this->tag_words, $orderby, SORT_ASC, SORT_STRING);
        }
        else {
            $this->tag_words = sort_db_result($this->tag_words, $orderby, SORT_DESC, SORT_NUMERIC);
        }
        #if ($column!="") $this->build_relation_printout($column, $targettable, $target_column, $target_title);
        $tagcloud = array(
            'wordcount' => count($this->tag_words),
            'approved' => (int)$approved,
            'notapproved' => (int)$notapproved,
            'words' => $this->tag_words);
        $this->smarty->assign('tagcloudobj', $tagcloud);
    }

    /**
     * tagcloud_class::build_tagcloud()
     * 
     * @return
     */
    function build_tagcloud() {
        $this->tag_words = array();
        $result = $this->db->query("SELECT T.*,T.id AS TAGID, COUNT(TR.tag_id) AS TAGCOUNT 
	FROM " . TBL_CMS_TAGS . " T, " . TBL_CMS_TAGS_REL . " TR 
	WHERE T.tag_approved=1 AND T.tag_langid=" . $this->langid . " AND TR.tag_id=T.id
	GROUP BY T.id
	LIMIT " . (int)$this->gbl_config['tag_num']);
        $counts = array();
        while ($row = $this->db->fetch_array_names($result)) {
            $tag_word = array(
                'word' => strtolower($row['tag_name']),
                'tcount' => $row['TAGCOUNT'],
                'link' => $this->gen_tag_link($row['tag_name']));
            $counts[] = $row['TAGCOUNT'];
            $this->tag_words[$row['tag_name']] = $tag_word;
        }

        $minFS = $this->gbl_config['tagcloud_font_min'];
        $maxFS = $this->gbl_config['tagcloud_font_max'];
        $maxFC = $this->gbl_config['tagcloud_color_max'];
        $minFC = $this->gbl_config['tagcloud_color_min'];
        $min = (count($counts) > 0) ? min($counts) : 1;
        $max = (count($counts) > 0) ? max($counts) : 1;

        foreach ($this->tag_words as $tag_name => $value) {
            if ((log($max) - log($min)) > 0)
                $wg = (log($value['tcount']) - log($min)) / (log($max) - log($min));
            else
                $wg = 1;
            $this->tag_words[$tag_name]['fontsize'] = $minFS + round(($maxFS - $minFS) * $wg);
            $this->tag_words[$tag_name]['fontcolor'] = $minFC + round(($maxFC - $minFC) * $wg);
        }
        ksort($this->tag_words);
        $tagcloud = array(
            'wordcount' => count($this->tag_words),
            'max' => $max,
            'min' => $min,
            'words' => $this->tag_words);
        $this->smarty->assign('tagcloud', $tagcloud);
    }

    /**
     * tagcloud_class::save_tag()
     * 
     * @param mixed $FORM
     * @param mixed $kid
     * @param bool $approve
     * @return
     */
    function save_tag($FORM, $kid, $approve = false) {
        if ($this->gbl_config['tagcloud_only_customers'] == 0 || ISADMIN == 1 || ($this->gbl_config['tagcloud_only_customers'] == 1 && $kid > 0 && ISADMIN == 0)) {
            $words = explode(' ', $FORM['name']);
            foreach ($words as $key => $word) {
                $word = trim($word);
                $weiter = false;
                if ($this->minlen > 0 && $this->maxlen > 0) {
                    $weiter = (strlen($word) <= $this->maxlen && strlen($word) >= $this->minlen);
                }
                if ($this->minlen == 0 && $this->maxlen == 0 && !empty($word))
                    $weiter = true;
                if ($weiter === TRUE) {
                    $tag_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_TAGS . " WHERE tag_langid=" . (int)$this->langid . " AND tag_name='" . $word . "'");
                    if ($tag_obj['id'] == 0) {
                        $tag_obj['id'] = insert_table(TBL_CMS_TAGS, array(
                            'tag_name' => $word,
                            'tag_approved' => $this->gbl_config['tag_autoaccept'],
                            'tag_langid' => (int)$this->langid));
                    }
                    $TAG = Array();
                    $TAG['tag_id'] = $tag_obj['id'];
                    $TAG['tag_kid'] = (int)$kid;
                    $TAG['tag_pid'] = (int)$FORM['pid'];
                    $TAG['tag_createdate'] = date('Y-m-d');
                    if (trim($word) != "" && get_data_count(TBL_CMS_TAGS_REL, 'id', "tag_pid=" . $TAG['tag_pid'] . " AND tag_id=" . $TAG['tag_id'] . " AND tag_kid=" . $TAG['tag_kid'] .
                        "") == 0) {
                        insert_table(TBL_CMS_TAGS_REL, $TAG);
                    }
                }
            }
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * tagcloud_class::cmd_savetagcloud()
     * 
     * @return
     */
    function cmd_savetagcloud() {
        $this->save_tag($_POST['TAGFORM'], $user_object['kid']);
        $this->msg('{LBLA_SAVED}');
        header('location: ' . $_SERVER['PHP_SELF'] . '?aktion=' . $_POST['last_aktion'] . '&page=' . $_POST['page']);
        $this->hard_exit();
    }


    /**
     * tagcloud_class::cmd_tagsearch()
     * 
     * @return
     */
    function cmd_tagsearch() {
        global $GBL_LANGID;
        $request_query = str_replace('.html', '', $_SERVER['REQUEST_URI']);
        $search_tag = trim(strip_tags(array_pop(explode('/', $request_query))));
        $tag_result = array();
        $sql = "SELECT  *, TP.id AS PAGEID
			FROM " . TBL_CMS_TEMPLATES . " TP, " . TBL_CMS_TEMPCONTENT . " P, " . TBL_CMS_TAGS_REL . " TR, " . TBL_CMS_TAGS . " T
			WHERE TP.approval=1 
				AND TR.tag_pid=P.tid 
				AND T.id=TR.tag_id
				AND TP.id=P.tid
				AND TP.url_redirect='' 
				AND TP.c_type='T' 
				AND P.lang_id=" . $GBL_LANGID . "
			AND T.tag_name='" . $search_tag . "'";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
            $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['PAGEID'];
            $tag_result[] = array(
                'title' => $row['linkname'],
                'shortdescription' => substr(strip_tags($row['content']), 1, 300),
                'link' => gen_page_link($tid, $url_label, $GBL_LANGID));
        }
        $this->smarty->assign('tag_result', $tag_result);
    }


}

?>