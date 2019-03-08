<?php

/**
 * @package    websitesearch
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

class websitesearch_class extends keimeno_class {

    var $WEBSITESEARCH = array();

    /**
     * websitesearch_class::__construct()
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
     * websitesearch_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('WEBSITESEARCH', $this->WEBSITESEARCH);
    }

    /**
     * websitesearch_class::cmd_fulltextsearch()
     * 
     * @return
     */
    function cmd_fulltextsearch() {
        $sm_values = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " C, " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPMATRIX . " M  
        WHERE C.id=M.tm_cid AND M.tm_tid=T.id AND T.url_redirect='' AND T.c_type='T' AND T.id=C.tid AND C.lang_id=" . $this->GBL_LANGID . " AND T.approval='1' 
        AND (
 	      C.linkname LIKE '%" . $_POST['FORM']['keyword'] . "%' 
 	      OR C.content LIKE '%" . $_POST['FORM']['keyword'] . "%' 
          OR M.tm_content LIKE '%" . $_POST['FORM']['keyword'] . "%'
 	      OR C.meta_desc LIKE '%" . $_POST['FORM']['keyword'] . "%' 
 	      OR C.meta_keywords LIKE '%" . $_POST['FORM']['keyword'] . "%'  	
 	      )
        GROUP BY T.id
        ORDER BY T.description");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['title'] = $row['linkname'];
            $row['content'] = strip_tags($row['tm_content']);
            $row['content'] = preg_replace('/({TMPL_)(.*)(})/', '', $row['content']);
            $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
            if ($row['tid'] == START_PAGE) {
                $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['tid'];
                $row['url'] = PATH_CMS . 'index.html';
            }
            else {
                $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['tid'];
                $row['url'] = gen_page_link($tid, $url_label, $this->GBL_LANGID);
            }

            $row['type'] = 'webcontent';
            $sm_values[] = $row;
        }
        $this->smarty->assign('searchresult', $sm_values);
    }

}

?>