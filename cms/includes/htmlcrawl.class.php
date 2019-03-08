<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

class htmlcrawl_class extends keimeno_class {

    var $html = "";
    var $langid = 1;
    var $wordscores = array();
    var $blocklist = array();
    var $metas = array();
    var $wortvorkommen = array();
    var $SEO = array();
    var $bonus = array(
        'title' => 6,
        'h1' => 6,
        'h2' => 5,
        'h3' => 4,
        'h4' => 3,
        'h5' => 2,
        'h6' => 1,
        'legend' => 2,
        'a' => 1,
        'strong' => 2,
        'b' => 2);

    /**
     * htmlcrawl_class::__construct()
     * 
     * @param string $inhalt
     * @return
     */
    function __construct($inhalt = "") {
        parent::__construct();
        $this->blocklist = array_unique(explode(',', $this->gbl_config['si_blockwords']));
        $this->inhalt = (string )$inhalt;
    }

    /**
     * htmlcrawl_class::get_first_tag()
     * 
     * @param string $tag
     * @return
     */
    function get_first_tag($tag = 'h1') {
        $html = $this->inhalt;
        $del_tags = array('%'); // remove smarty joker
        foreach ($del_tags as $tag) {
            $html = preg_replace('#<' . $tag . '\b.+?</' . $tag . '>#', '', $html);
        }
        preg_match_all('#<' . $tag . '\b.+?</' . $tag . '>#', $html, $hits);
        foreach ($hits[0] as $el) {
            return strip_tags($el);
        }
    }

    /**
     * htmlcrawl_class::generate_meta_title()
     * 
     * @return
     */
    function generate_meta_title() {
        $meta_title = $this->get_first_tag('h1');
        if ($meta_title == "") {
            $meta_title = $this->get_first_tag('h2');
        }
        if ($meta_title == "") {
            $meta_title = $this->get_first_tag('h3');
        }
        if ($meta_title == "") {
            $meta_title = $this->get_first_tag('strong');
        }
        return trim($meta_title);
    }

    /**
     * htmlcrawl_class::get_bonus_tags_count()
     * 
     * @return
     */
    function get_bonus_tags_count() {
        $html = $this->inhalt;
        $del_tags = array('%'); // remove smarty joker
        foreach ($del_tags as $tag) {
            $html = preg_replace('#<' . $tag . '\b.+?</' . $tag . '>#', '', $html);
        }
        foreach ($this->bonus as $tag => $value) {
            preg_match_all('#<' . $tag . '\b.+?</' . $tag . '>#', $html, $hits);
            foreach ($hits[0] as $el) {
                $el = trim(strip_tags($el));
                if (!empty($el)) {
                    $bon[$tag]['count']++;
                    $bon[$tag]['words'][] = $el;
                }
            }
        }
        $this->SEO['htags'] = $bon;
    }

    /**
     * htmlcrawl_class::format_content()
     * 
     * @return
     */
    public static function format_content($inhalt) {
        $inhalt = mb_strtolower(html_entity_decode($inhalt, ENT_COMPAT, 'UTF-8'), "UTF-8");
        $inhalt = preg_replace('/&#?\w+;/', ' ', $inhalt);
        if (!get_magic_quotes_runtime())
            $inhalt = addslashes($inhalt);
        $inhalt = preg_replace('/\s+/', ' ', $inhalt); //entfernet zeilenumbrueche, whitespace
        return $inhalt;
    }

    /**
     * htmlcrawl_class::getPartOfArray()
     * 
     * @param mixed $list
     * @param mixed $from
     * @param mixed $limit
     * @return
     */
    private static function getPartOfArray($list, $from, $limit) {
        if (count($list) == 0) {
            return $list;
        }
        $temp_list = array_chunk($list, $limit);
        $pos = floor($from / $limit);
        if (count($temp_list) > 0) {
            return $temp_list[$pos];
        }
        else {
            return $list;
        }
    }

    /**
     * htmlcrawl_class::extract_meta_tags()
     * 
     * @return
     */
    function extract_meta_tags() {
        #    echo htmlspecialchars($this->inhalt);
        preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', stripslashes($this->inhalt), $match);

        if (isset($match) && is_array($match) && count($match) == 3) {
            $originals = $match[0];
            $names = $match[1];
            $values = $match[2];


            if (count($originals) == count($names) && count($names) == count($values)) {
                $metaTags = array();

                for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
                    $metaTags[$names[$i]] = array('html' => htmlentities($originals[$i]), 'value' => $values[$i]);
                }
            }
        }
        # get title tag extra
        $s_title = preg_match('#<title>(.+?)</title>#', $this->inhalt, $treffer) ? $treffer[1] : $this->pfad;
        $metaTags['title'] = array('html' => '', 'value' => stripslashes($s_title));
        $this->metas = $metaTags;
    }

    /**
     * htmlcrawl_class::gen_meta_keywords_se()
     * 
     * @param mixed $meta_keywords
     * @param string $delimiter
     * @return
     */
    function gen_meta_keywords_se(&$meta_keywords, $delimiter = ',', $no_index = false) {
        $this->gbl_config['metakey_count'] = ($this->gbl_config['metakey_count'] <= 0) ? 9 : (int)$this->gbl_config['metakey_count'];
        $this->inhalt = self::format_content($this->inhalt);
        $this->get_words_width_score($no_index);
        foreach ($this->wordscores as $key => $word) {
            if (strlen($word['word']) < 5) {
                unset($this->wordscores[$key]);
            }
        }
        $this->wordscores = $this->sort_multi_array($this->wordscores, 'score', SORT_DESC, SORT_NUMERIC, 'count', SORT_DESC, SORT_NUMERIC);
        if (is_array($this->wordscores)) {
            $words = Array();
            $arr = self::getPartOfArray($this->wordscores, 0, $this->gbl_config['metakey_count']);
            if (is_array($arr)) {
                while ($sw = array_shift($arr)) {
                    $words[] = $sw['word'];
                }
                $meta_keywords = implode($delimiter, $words);
            }
        }
        return (string )$meta_keywords;
    }

    /**
     * htmlcrawl_class::fetch_words()
     * 
     * @param mixed $_inhalt
     * @param mixed $_score
     * @return
     */
    function fetch_words($_inhalt, $_score, $no_index = false) {
        $_inhalt = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $_inhalt);
        $_inhalt = strip_tags($_inhalt);
        #$_inhalt = preg_replace('/\W+/',' ',$_inhalt);
        $_inhalt = preg_replace('/\[^\pL]/u', ' ', $_inhalt); //UTF8 kompatibel, umlaute bleiben erhalten
        $_inhalt = preg_replace('/[^\w\pL]/u', ' ', $_inhalt); // entferhnt nun hier sauber alle Satzzeichen
        $words = preg_split('/\s+/', trim($_inhalt));
        # wort häufigkeit
        $word_count = array_count_values($words);
        $words = array_unique($words);
        foreach ($words as $key => $word) {
            if (strlen($word) < (int)$this->gbl_config['si_minlen'] && $_score == 0)
                continue;
            if (is_numeric($word))
                continue;
            if (in_array($word, $this->blocklist))
                continue;
            if ($no_index == false) {
                // plain text add
                $word_obj = $this->db->query_first_obj("SELECT id FROM " . TBL_CMS_SINWORDS . " WHERE si_word='" . ($word) . "' COLLATE utf8_bin");
                #  $word_obj = mysqli_fetch_object($result);
                if ($word_obj->id == 0) {
                    $this->db->query("INSERT INTO " . TBL_CMS_SINWORDS . " SET si_langid=" . (int)$this->langid . ",si_word='" . $word . "'");
                    $this->word_id = $this->db->insert_id();
                }
                else
                    $this->word_id = $word_obj->id;
            }
            else {
                $this->word_id = $key;
            }
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
     * htmlcrawl_class::get_words_width_score()
     * 
     * @return
     */
    function get_words_width_score($no_index = false) {
        # echo htmlspecialchars( $this->inhalt);
        $del_tags = array('script', 'style');
        foreach ($del_tags as $tag) {
            $this->inhalt = preg_replace('#<' . $tag . '\b.+?</' . $tag . '>#', '', $this->inhalt);
        }

        foreach (array_keys($this->bonus) as $tag) {
            preg_match_all('#<' . $tag . '\b.+?</' . $tag . '>#', $this->inhalt, $hits);
            foreach ($hits[0] as $el) {
                $this->fetch_words($el, $this->bonus[$tag], $no_index);
            }
        }
        $this->fetch_words($this->inhalt, 1, $no_index);
    }

    /**
     * htmlcrawl_class::manage_site()
     * 
     * @param integer $block_ident
     * @return
     */
    function manage_site($block_ident = 0) {
        if ((int)$_REQUEST[$this->page_ident] > 0 || $this->all_index === TRUE) {
            $this->pfad = getenv("REQUEST_URI");
            $tmp_pfad = str_replace(explode(',', $this->gbl_config['si_blockquery']), '*', $this->pfad);
            if ($tmp_pfad != $this->pfad && $this->gbl_config['si_blockquery'] != "")
                return;
            $exists = false;
            $sql = "SELECT id FROM " . TBL_CMS_SINSITES . " WHERE s_url='" . $this->pfad . "' LIMIT 1";
            $result = mysqli_query($this->db->link_id, $sql);
            $site_obj = mysqli_fetch_object($result);
            $this->inhalt = self::format_content($this->inhalt);
            $s_title = preg_match('#<title>(.+?)</title>#', $this->inhalt, $treffer) ? $treffer[1] : $this->pfad;
            $s_title = ucfirst(stripslashes($s_title));
            $this->extract_meta_tags();
            //Keywords erhalten einen Score eines H2 Tags
            $keywords = preg_split('/\s+/', $this->metas['keywords']['value']);
            if (count($keywords) > 6)
                array_splice($keywords, 6);
            foreach ($keywords as $key => $kw) {
                $this->inhalt .= '<h2>' . $kw . '</h2>';
            }
            if ($site_obj->id == 0) {
                $WREL = array(
                    's_url' => $this->pfad,
                    's_title' => $this->db->real_escape_string($s_title),
                    's_lastcrawl' => time(),
                    's_firstcrawl' => time(),
                    's_pid' => (int)$_REQUEST[$this->page_ident],
                    's_langid' => (int)$this->langid,
                    's_short' => $this->db->real_escape_string(strip_tags($this->metas['description']['value'])),
                    's_keywords' => $this->db->real_escape_string(strip_tags($this->metas['keywords']['value'])));
                if (($block_ident > 0 && $block_ident != (int)$_REQUEST[$this->page_ident]) || $block_ident == 0)
                    $this->site_id = insert_table(TBL_CMS_SINSITES, $WREL);
            }
            else {
                $this->site_id = $site_obj->id;
                mysqli_query($this->db->link_id,"UPDATE " . TBL_CMS_SINSITES . " SET s_lastcrawl=" . time() . "
		,s_title='" . $this->db->real_escape_string($s_title) . "'
		,s_short='" . $this->db->real_escape_string(strip_tags($this->metas['description']['value'])) . "'
		,s_keywords='" . $this->db->real_escape_string(strip_tags($this->metas['keywords']['value'])) . "'
		 WHERE id=" . $this->site_id);
                $exists = true;
            }
            if ($exists == false || ($this->gbl_config['si_active'] == 1 && $exists == true)) {
                $this->analyse();
            }
        }
    }


    /**
     * htmlcrawl_class::analyse()
     * 
     * @return
     */
    function analyse() {
        if ($this->site_id > 0) {
            $this->get_words_width_score();
            $this->db->query("DELETE FROM " . TBL_CMS_SINREL . " 
			WHERE sr_siteid=" . $this->site_id . " 
				OR (sr_inserttime>0 AND sr_inserttime<" . (time() - (60 * 60 * 24 * 30 * 12)) . ")"); // aelter 12 Monat
            if (is_array($this->wortvorkommen)) {
                foreach ($this->wortvorkommen as $wordid => $score) {
                    $WREL = array(
                        'sr_word_id' => (int)$wordid,
                        'sr_score' => (int)$score,
                        'sr_siteid' => (int)$this->site_id,
                        'sr_inserttime' => time());
                    insert_table(TBL_CMS_SINREL, $WREL);
                }
            }
        }
    }
}
