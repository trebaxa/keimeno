<?php

/**
 * @package    tablist
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class tablist_class extends modules_class {

    /**
     * tablist_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        #$this->TCR = new kcontrol_class($this);
    }

    /**
     * tablist_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TABLIST . " ORDER BY tab_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * tablist_class::parse_table_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_table_inlay($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_TABLE_')) {
            preg_match_all("={TMPL_TABLE_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                #                $rep = array("{TMPL_TABLE_", "}");
                #              $gid = intval(strtolower(str_replace($rep, "", $wert)));
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $gid = (int)$PLUGIN_OPT['tabid'];
                $TAB = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE tab_id=" . $gid . " AND lang_id=" . $langid);
                $TAB['plugopt'] = $PLUGIN_OPT;
                $result = $this->db->query("SELECT NL.id AS NLID,K.*,NL.*,NC.*,NG.*,T.tpl_name
		FROM " . TBL_CMS_TABLIST . " NL
		INNER JOIN " . TBL_CMS_TABLIST_GROUP . " NG ON (NG.id=NL.group_id) 
		INNER JOIN " . TBL_CMS_TEMPLATES . " T ON (T.id=NL.tpl) 		
		LEFT JOIN " . TBL_CMS_TABLIST_CONTENT . " NC ON (NL.id=NC.tab_id AND NC.lang_id=" . $langid . ")
		LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
		WHERE NL.id=" . $gid . " AND NL.approval=1
		GROUP BY NL.id ORDER BY title");
                while ($row = $this->db->fetch_array_names($result)) {
                    $TABLE = unserialize($row['content']);
                    if (is_array($TABLE)) {
                        foreach ($TABLE as $key => $value) {
                            if (is_array($value)) {
                                foreach ($value as $index => $td) {
                                    $value[$index] = stripslashes(base64_decode($td));
                                }
                            }
                            $TABLE[$key] = $value;
                        }
                    }
                    $TAB_OBJ = $row;
                }

                $this->smarty->assign('TMPL_TABLE_' . $cont_matrix_id, $TABLE);
                $this->smarty->assign('TMPL_TABOBJ_' . $cont_matrix_id, $TAB);
                if ($TAB_OBJ['tpl_name'] != "")
                    $html = str_replace('{TMPL_TABLE_' . $cont_matrix_id . '}', '<% assign var=tabobj value=$TMPL_TABOBJ_' . $cont_matrix_id .
                        ' %><% assign var=tablist value=$TMPL_TABLE_' . $cont_matrix_id . ' %><% include file="' . $TAB_OBJ['tpl_name'] . '.tpl" %>', $html);
                else
                    $html = str_replace('{TMPL_TABLE_' . $cont_matrix_id . '}', "", $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * tablist_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tabid'];
        $result = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST . " WHERE id=" . $id);
        $upt = array('tm_content' => '{TMPL_TABLE_' . (int)$cont_matrix_id . '}', 'tm_pluginfo' => $result['tab_name']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

}

?>