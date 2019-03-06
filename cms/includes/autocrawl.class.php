<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class auto_crawl_class extends keimeno_class {

    var $SI = NULL;
    var $langid = 1;

    /**
     * auto_crawl_class::auto_crawl_class()
     * 
     * @param mixed $SI
     * @param integer $langid
     * @return
     */
    function auto_crawl_class($SI = NULL, $langid = 1) {
        parent::__construct();
        $this->SI = $SI;
        $this->set_lang($langid);
    }

    /**
     * auto_crawl_class::set_lang()
     * 
     * @param mixed $langid
     * @return
     */
    function set_lang($langid) {
        $this->langid = ($langid == 0) ? 1 : $langid;
    }


    /**
     * auto_crawl_class::search()
     * 
     * @param mixed $words
     * @param mixed $search_result
     * @return
     */
    function search($words, &$search_result) {
        $search_result = array();
        $words = trim($words);
        if (!get_magic_quotes_gpc())
            $words = addslashes($words);
        if (strlen($words) > 50)
            die('Anfrage zu lang');
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
                $sql = "SELECT DISTINCT S.s_url, S.s_title, S.s_short, S.s_keywords,S.id as SITEID, V.sr_score FROM " . TBL_CMS_SINSITES . " S, " . TBL_CMS_SINWORDS . " W, " .
                    TBL_CMS_SINREL . " V";
                $sql .= " WHERE
			(W.si_word" . $unsharp_sign . "'" . $sb . $unsharp_back . "' COLLATE utf8_bin OR
			SOUNDEX(W.si_word)=SOUNDEX('" . $sb . "'))
			AND V.sr_word_id=W.id
			AND S.id=V.sr_siteid
			AND S.s_langid=" . (int)$this->langid . "
			AND W.si_langid=" . (int)$this->langid . "
			";
                $sql .= " ORDER BY V.sr_score";
                $result = $this->db->query($sql);
                while ($row = $this->db->fetch_array_names($result)) {
                    $sr[md5($sb)][$row['SITEID']] = $row;
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
            $search_result = sort_db_result($search_result, 'sr_score', SORT_DESC, SORT_NUMERIC);
        }
    }

    /**
     * auto_crawl_class::build_search_sql()
     * 
     * @param mixed $words
     * @param mixed $sql
     * @return
     */
    function build_search_sql($words, &$sql) {
        $words = trim($words);
        if (!get_magic_quotes_gpc())
            $words = addslashes($words);
        if (strlen($words) > 50)
            die('Anfrage zu lang');
        if (strlen($words) == 0)
            die('Anfrage leer');
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
        $sql = "SELECT DISTINCT S.s_url, S.s_title, S.s_short, S.s_keywords FROM " . TBL_CMS_SINSITES . " S, " . TBL_CMS_SINWORDS . " W, " . TBL_CMS_SINREL . " V";
        $where = " WHERE W.si_word='" . array_shift($suchbegriffe) . "' 
	AND V.sr_word_id=W.id 
	AND S.id=V.sr_siteid 
	AND S.s_langid=" . (int)$this->langid . "
	AND W.si_langid=" . (int)$this->langid . "
	";
        $order = " ORDER BY V.sr_score";

        while ($sb = array_shift($suchbegriffe)) {
            $nr = count($suchbegriffe);
            $sql .= ", (SELECT DISTINCT S.id, V.sr_score FROM " . TBL_CMS_SINSITES . " S, " . TBL_CMS_SINWORDS . " W, " . TBL_CMS_SINREL . " V 
							WHERE W.si_word='" . $sb . "' COLLATE utf8_bin 
							AND V.sr_word_id=W.id 
							AND S.id=V.sr_siteid 
							AND S.s_langid=" . (int)$this->langid . "
							AND W.si_langid=" . (int)$this->langid . "
							) j" . $nr;
            $where .= " AND S.id=j" . $nr . ".id";
            $order .= " + j" . $nr . ".sr_score";
        }
        $sql .= $where . $order . ' DESC';
    }


    /**
     * auto_crawl_class::auto_crawl()
     * 
     * @param mixed $start
     * @return
     */
    function auto_crawl($start) {
        unset($_SESSION['siurls']);
        $result = $this->db->query("SELECT C.*,L.local FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C, " . TBL_CMS_LANG . " L
	WHERE T.approval=1 
		AND C.linkname<>'' 
        AND t_htalinklabel<>''
		AND C.tid=T.id
		AND gbl_template=0
        AND L.id=C.lang_id
	LIMIT " . $start . ",50");
        while ($row = $this->db->fetch_array_names($result)) {
            $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
            $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['id'];
            $url = self::get_http_protocol() . '://www.' . FM_DOMAIN . gen_page_link($tid, $url_label, $row['lang_id'], $row['local']);
            $_SESSION['siurls'][] = $url;
            keimeno_class::curl_get_data($url);
        }
        return $this->db->num_rows($result);
    }

}
