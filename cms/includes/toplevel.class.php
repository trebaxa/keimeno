<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class toplevelcms_class extends keimeno_class {


    /**
     * toplevelcms_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID;
        parent::__construct();
        $this->GBL_LANGID = $GBL_LANGID;
    }


    /**
     * toplevelcms_class::genLevelLink()
     * 
     * @param mixed $id
     * @param mixed $linkname
     * @param integer $lid
     * @return
     */
    function genLevelLink($id, $linkname, $lid = 1) {
        global $HTA_CLASS_CMS;
        return $this->get_local_path() . $HTA_CLASS_CMS->genLink(32, array($linkname, $id));
    }

    #*********************************
    # BUILD TOP LEVEL MENU HORIZONTAL & VERTICAL
    #*********************************
    /**
     * toplevelcms_class::load_toplevel()
     * 
     * @param mixed $TOPLEVEL_OBJ
     * @param mixed $active_page
     * @return
     */
    function load_toplevel($TOPLEVEL_OBJ, $active_page) {
        global $GBL_LANGID;
        $sm_topl = array();
        $this->smarty_values = $tlset = array();
        $local_path = $this->get_local_path();
        $result = $this->db->query("SELECT T.*,TC.*, T.id AS TID, T.description AS TLN,TC.level_name AS TLNML
   FROM " . TBL_CMS_TOPLEVEL . " T 
   LEFT JOIN " . TBL_CMS_TPLCON . " TC ON (TC.tid=T.id AND TC.lang_id=" . $GBL_LANGID . ")
   WHERE T.id>1 AND T.approval=1  
   ORDER BY T.morder");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['level_name'] = ($row['TLNML'] == '') ? $row['TLN'] : $row['TLNML'];
            if ($row['url_redirect'] == "") {
                if ($row['first_page'] > 0) {
                    $PAGEOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " T 
                        WHERE TC.tid=T.id AND T.id=" . $row['first_page'] . " AND TC.lang_id=" . $GBL_LANGID);
                    $url_label = ($PAGEOBJ['t_htalinklabel'] == "") ? $PAGEOBJ['linkname'] : $PAGEOBJ['t_htalinklabel'];
                    $pageid = ($PAGEOBJ['t_htalinklabel'] != "") ? 0 : $row['first_page'];
                    #echo gen_page_link($pageid, $url_label, $GBL_LANGID);
                    $TOPL = array(
                        'link' => gen_page_link($pageid, $url_label, $GBL_LANGID),
                        'TOPID' => $row['TID'],
                        'title' => $row['level_name'],
                        'approval' => $row['approval'],
                        'active' => ($row['first_page'] == $active_page),
                        'icon' => $row['tpl_icon'],
                        'target' => '_self');
                }
                else {
                    # echo $this->genLevelLink($row['TID'], $row['level_name'], $GBL_LANGID);
                    $TOPL = array(
                        'link' => $this->genLevelLink($row['TID'], $row['level_name'], $GBL_LANGID),
                        'TOPID' => $row['TID'],
                        'title' => $row['level_name'],
                        'approval' => $row['approval'],
                        'active' => ($TOPLEVEL_OBJ['TOPID'] == $row['TID']),
                        'icon' => $row['tpl_icon'],
                        'target' => '_self');
                }
            }
            else {
                $TOPL = array(
                    'link' => rtrim($local_path, '/') . $row['url_redirect'],
                    'TOPID' => $row['TID'],
                    'icon' => $row['tpl_icon'],
                    'active' => (START_PAGE == $active_page),
                    'approval' => $row['approval'],
                    'title' => $row['level_name'],
                    'target' => (($row['url_redirect_target']) ? $row['url_redirect_target'] : "_self"));
            }
            $sm_topl[$TOPL['TOPID']] = $TOPL;
        }

        $TOPL_ARR = array(
            'list' => $sm_topl,
            'count' => count($sm_topl),
            'activeid' => $_SESSION['tl'],
            'toplobj' => $TOPLEVEL_OBJ);
        #  echoarr($TOPL_ARR);
        $this->smarty->assign('cmstoplevels', $TOPL_ARR);


    }

}
