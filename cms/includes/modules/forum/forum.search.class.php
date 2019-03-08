<?php

/**
 * @package    forum
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class forum_search_class extends forum_master_class {
    var $html = "";
    var $langid = 1;
    var $wordscores = array();
    var $blocklist = array();
    var $metas = array();
    var $wortvorkommen = array();
    var $SEO = array();
    var $bonus = array(
        'h1' => 6,
        'h2' => 5,
        'b' => 4,
        'code' => 3,
        'legend' => 2,
        'a' => 1);

    /**
     * forum_search_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * forum_search_class::get_words_width_score()
     * 
     * @return void
     */
    function get_words_width_score($inhalt) {
        foreach (array_keys($this->bonus) as $tag) {
            preg_match_all('/\[' . $tag . '\](.*?)\[\/' . $tag . ']/s', $inhalt, $hits);
            foreach ($hits[0] as $el) {
                $this->fetch_words($el, $this->bonus[$tag]);
            }
        }
        $this->fetch_words($inhalt, 0);

    }

    /**
     * forum_search_class::fetch_words()
     * 
     * @param mixed $_inhalt
     * @param mixed $_score
     * @return void
     */
    function fetch_words($_inhalt, $_score) {
        $_inhalt = mb_strtolower(html_entity_decode($_inhalt, ENT_COMPAT, 'UTF-8'), "UTF-8");
        $_inhalt = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $_inhalt);
        $_inhalt = strip_tags($_inhalt);
        $_inhalt = preg_replace('/\[^\pL]/u', ' ', $_inhalt); //UTF8 kompatibel, umlaute bleiben erhalten
        $_inhalt = preg_replace('/[^\w\pL]/u', ' ', $_inhalt); // entferhnt nun hier sauber alle Satzzeichen
        $words = preg_split('/\s+/', trim($_inhalt));

        # wort häufigkeit
        $word_count = array_count_values($words);
        $words = array_unique($words);
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) < 4)
                continue;
            if (is_numeric($word))
                continue;
            if (in_array($word, $this->blocklist))
                continue;
            // plain text add
            $result = $this->db->query("SELECT id FROM " . TBL_CMS_FORUMSWORDS . " WHERE si_word='" . ($word) . "' COLLATE utf8_bin");
            $word_obj = mysqli_fetch_object($result);
            if ($word_obj->id == 0) {
                $this->word_id = insert_table(TBL_CMS_FORUMSWORDS, array('si_word' => $word));
            }
            else
                $this->word_id = $word_obj->id;
            if (!isset($this->wortvorkommen[$this->word_id]))
                $this->wortvorkommen[$this->word_id] = 0;
            $this->wortvorkommen[$this->word_id] += $_score;
            if (!empty($word)) {
                $this->wordscores[$this->word_id] = array(
                    'score' => $this->wortvorkommen[$this->word_id],
                    'word' => $word,
                    'count' => (int)$word_count[$word]);
            }
        }
    }

    /**
     * forum_search_class::analyse()
     * 
     * @return void
     */
    function analyse($inhalt, $themeid) {
        $this->get_words_width_score($inhalt);
        $this->db->query("DELETE FROM " . TBL_CMS_FORUMSREL . " WHERE sr_themeid=" . $themeid . "
				OR (sr_inserttime>0 AND sr_inserttime<" . (time() - (60 * 60 * 24 * 30 * 12)) . ")"); // aelter 12 Monat
        if (is_array($this->wortvorkommen)) {
            foreach ($this->wortvorkommen as $wordid => $score) {
                $WREL = array(
                    'sr_word_id' => (int)$wordid,
                    'sr_score' => (int)$score,
                    'sr_themeid' => (int)$themeid,
                    'sr_inserttime' => time());
                insert_table(TBL_CMS_FORUMSREL, $WREL);
            }
        }


    }

    /**
     * forum_search_class::index_theme()
     * 
     * @param mixed $themeid
     * @return void
     */
    function index_theme($themeid) {
        $THEME = $this->load_theme_by_id($themeid);
        $CUSTOMER = $this->load_customer_of_theme($themeid);
        $inhalt = '[h1]' . $THEME['t_name'] . '[/h1][h2]' . $CUSTOMER['username'] . '[/h2]';
        $threads = $this->load_threads_by_theme($themeid, 0, 30);
        foreach ($threads as $row) {
            $inhalt .= $row['f_text'] . ' ';
        }

        $this->analyse($inhalt, $themeid);
    }

    /**
     * forum_search_class::search()
     * 
     * @param mixed $words
     * @param mixed $search_result
     * @return void
     */
    function search($FORM, &$search_result) {
        $search_result = array();
        $words = trim($FORM['sword']);
        if (!get_magic_quotes_gpc())
            $words = addslashes($words);
        if (strlen($words) > 50) {
            firewall_class::report_hacking('Forum: search word really to long >50');
            die('Anfrage zu lang');
        }
        if (strlen($words) > 0) {
            $sbs = preg_split('/\s+/', $words);
            foreach ($sbs as $ab) {
                #	$sb = preg_replace('/\W/','', $sb);
                $sb = preg_replace('/\[^\pL]/u', '', $ab); //UTF8 kompatibel, umlaute bleiben erhalten
                $sb = preg_replace('/[^\w\pL]/u', '', $sb); // entferhnt nun hier sauber alle Satzzeichen
                if (strlen($sb) > 1)
                    $suchbegriffe[] = $sb;
            }
            if (count($suchbegriffe) > 4)
                array_splice($suchbegriffe, 4);
            $unsharp_back = '';
            $unsharp_sign = '=';
            if (count($suchbegriffe) == 1) {
                $unsharp_back = '%';
                $unsharp_sign = ' LIKE ';
            }
            $sr = array();
            while ($sb = array_shift($suchbegriffe)) {
                $sql = "SELECT DISTINCT *, S.id as TID, t_fid AS FID, V.sr_score 
                    FROM  " . TBL_CMS_FORUMSWORDS . " W, " . TBL_CMS_FORUMSREL . " V ," . TBL_CMS_FORUMGROUPS . " G, " . TBL_CMS_FORUMF . " F," .
                    TBL_CMS_FORUMTHEMES . " S
                    ";
                $sql .= " WHERE
			(W.si_word" . $unsharp_sign . "'" . $sb . $unsharp_back . "' COLLATE utf8_bin OR
			SOUNDEX(W.si_word)=SOUNDEX('" . $sb . "'))
			AND V.sr_word_id=W.id
			AND S.id=V.sr_themeid			
			AND W.si_langid=1
            AND G.id=F.fn_gid 
            AND F.id=S.t_fid  
            " . (($FORM['fid'] > 0) ? " AND F.id=" . (int)$FORM['fid'] : "") . "          
			";
                $sql .= " ORDER BY V.sr_score";
                $result = $this->db->query($sql);
                while ($row = $this->db->fetch_array_names($result)) {
                    $sr[md5($sb)][$row['TID']] = $this->set_theme_opt($row);
                }
            }
            // Schnittmengen bilden
            $search_result = array_shift($sr);

            foreach ($sr as $key => $rr) {
                $add_score_arr = array_intersect_key($rr, $search_result); // Schnittmenge aus $rr.sr_score bleibt ueber
                foreach ($add_score_arr as $siteid => $site_obj) {
                    $search_result[$siteid]['sr_score'] += $site_obj['sr_score'];
                }
                $search_result = array_intersect_key($search_result, $rr);
            }
            $search_result = (array )$search_result;

            $search_result = self::sort_multi_array($search_result, 'sr_score', SORT_DESC, SORT_NUMERIC);            
        }
        return $search_result;
    }
}
