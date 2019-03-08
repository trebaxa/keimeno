<?php

/**
 * @package    flickr
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class flickr_class extends flickr_master_class {

    var $FLICKR = array();

    /**
     * flickr_class::__construct()
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
     * flickr_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('FLICKR') != null) {
            $this->FLICKR = array_merge($this->smarty->getTemplateVars('FLICKR'), $this->FLICKR);
            $this->smarty->clearAssign('FLICKR');
        }
        $this->smarty->assign('FLICKR', $this->FLICKR);
    }

    /**
     * flickr_class::cronjob()
     * 
     * @param mixed $params
     * @param mixed $exec_class
     * @return
     */
    function cronjob($params, $exec_class) {
        $start = $exec_class->get_micro_time();
        $xml = $this->get_own_fotos();

        if ($xml->photos->attributes()->total > get_data_count(TBL_CMS_FLICKRSTREAM, '*', "1")) {
            foreach ($this->FLICKR['ownfotos'] as $key => $foto) {
                if (get_data_count(TBL_CMS_FLICKRSTREAM, '*', "p_localefile='" . basename($foto['url']) . "'") == 0) {
                    $this->grab_foto_to_db($foto);
                }
            }
        }
        $sidegentime = number_format($exec_class->get_micro_time() - $start, 4, ".", ".");
        $exec_class->feedback .= '<li>Flickr Stream (' . $sidegentime . ' sek)</li>';
    }

    /**
     * flickr_class::load_fotos()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_fotos($PLUGIN_OPT) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FLICKRSTREAM . " WHERE 1 ORDER BY p_time " . $PLUGIN_OPT['sortdirec'] . " LIMIT " . (int)$PLUGIN_OPT['foto_count']);
        while ($row = $this->db->fetch_array_names($result)) {
            $row[thumb] = gen_thumb_image('/file_data/flickr/' . $row['p_localefile'], (int)$PLUGIN_OPT['foto_width'], (int)$PLUGIN_OPT['foto_height'], $PLUGIN_OPT['foto_resize_method']);
            $row['date'] = date('d.m.Y', $row['p_time']);
            $this->FLICKR['fotostream'][] = $row;
        }
    }

    /**
     * flickr_class::parse_flickrstream()
     * 
     * @param mixed $params
     * @return
     */
    function parse_flickrstream($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_FLICKR_FOTOSTREAM_')) {
            preg_match_all("={TMPL_FLICKR_FOTOSTREAM_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {       
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->load_fotos($PLUGIN_OPT);
                $this->smarty->assign('TMPL_FLICKR_STREAM_' . $cont_matrix_id, $this->FLICKR['fotostream']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=flickrstream value=$TMPL_FLICKR_STREAM_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

}
