<?php

/**
 * @package    indexsearch
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class indexsearch_class extends indexsearch_master_class {

    var $INDEXSEARCH = array();

    /**
     * indexsearch_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * indexsearch_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('INDEXSEARCH') != null) {
            $this->INDEXSEARCH = array_merge($this->smarty->getTemplateVars('INDEXSEARCH'), $this->INDEXSEARCH);
            $this->smarty->clearAssign('INDEXSEARCH');
        }
        $this->smarty->assign('INDEXSEARCH', $this->INDEXSEARCH);
    }

    /**
     * indexsearch_class::autorun()
     * 
     * @return
     */
    function autorun() {
        $PAGE_CONNECTED = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C , " . TBL_CMS_LANG . " L
            WHERE T.id=C.tid AND gbl_template=0 AND module_id='indexsearch' AND L.id=C.lang_id");
        $url_label = ($PAGE_CONNECTED['t_htalinklabel'] == "") ? $PAGE_CONNECTED['linkname'] : $PAGE_CONNECTED['t_htalinklabel'];
        $tid = ($PAGE_CONNECTED['t_htalinklabel'] != "") ? 0 : $PAGE_CONNECTED['id'];
        $url = gen_page_link($tid, $url_label, $PAGE_CONNECTED['lang_id'], $PAGE_CONNECTED['local']);
        $this->INDEXSEARCH['searchformurl'] = $url;
        $this->parse_to_smarty();
    }

    /**
     * indexsearch_class::cmd_indexsearch()
     * 
     * @return
     */
    function cmd_indexsearch() {
        require_once (CMS_ROOT . 'includes/autocrawl.class.php');
        $AC = new auto_crawl_class(null, 0, $this->GBL_LANGID);
        $AC->search($_REQUEST['setvalue'], $search_result);
        unset($AC);
        $SE = array(
            'search_result' => $search_result,
            'search_count' => count($search_result),
            'search_time' => round(($end - $start), 4),
            );
        $this->smarty->assign('SE', $SE);
    }

    /**
     * indexsearch_class::on_output()
     * 
     * @param mixed $params
     * @return
     */
    function on_output($params) {       
        if ($this->gbl_config['si_siactive'] == 1) {
            $SI = new htmlcrawl_class($params['html']);
            $SI->all_index = true;
            $SI->langid = ($params['langid'] == 0) ? 1 : $params['langid'];
            $SI->manage_site(50);
            unset($SI);
        }
        return $params;
    }

    /**
     * indexsearch_class::cmd_siauto_keywords()
     * 
     * @return
     */
    function cmd_siauto_keywords() {
        $sb = trim($_REQUEST['setvalue']) or exit;
        if (!get_magic_quotes_runtime())
            $sb = addslashes($sb);
        if (strlen($sb) > 50)
            exit;
        $sb = mb_strtolower(html_entity_decode($sb), "UTF-8");
        $sb = preg_replace('/\[^\pL]/u', '', $sb); //UTF8 kompatibel, umlaute bleiben erhalten
        $sb = preg_replace('/[^\w\pL]/u', '', $sb); // entferhnt nun hier sauber alle Satzzeichen

        $arr = array();
        $result = $this->db->query("SELECT si_word FROM " . TBL_CMS_SINWORDS . " WHERE si_word LIKE '" . $sb . "%'  COLLATE utf8_bin
        ORDER BY si_word
        LIMIT 10");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row['si_word'];
        }

        $json = array();

        foreach ($arr as $key => $value) {
            #if (strpos(strtolower($value), $q) !== false) {
            $json[] = '"' . $value . '"';
            #}
        }

        #header('Content-type: application/json');
        echo '[' . implode(',', $json) . ']';
        $this->hard_exit();
    }

}
