<?php

/**
 * @package    socialstream
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

class socialstream_class extends modules_class {

    var $SOCIALSTREAM = array();

    /**
     * socialstream_class::__construct()
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
     * socialstream_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('SOCIALSTREAM') != NULL) {
            $this->SOCIALSTREAM = array_merge($this->smarty->getTemplateVars('SOCIALSTREAM'), $this->SOCIALSTREAM);
            $this->smarty->clearAssign('SOCIALSTREAM');
        }
        $this->smarty->assign('SOCIALSTREAM', $this->SOCIALSTREAM);
    }

    /**
     * socialstream_class::load_stream()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_stream($PLUGIN_OPT) {
        $social_stream = array();

        //FLICKR
        if (class_exists('flickr_class') && (int)$PLUGIN_OPT['no_fl'] != 1) {
            $flickr = new flickr_class();
            $userfl = $flickr->get_own_user();
            $flickr->load_fotos(array(
                'foto_count' => 30,
                'foto_resize_method' => $PLUGIN_OPT['foto_resize_method'],
                'foto_width' => $PLUGIN_OPT['foto_width'],
                'foto_height' => $PLUGIN_OPT['foto_height']));
            $flickr->FLICKR['fotostream'] = (array )$flickr->FLICKR['fotostream'];
            foreach ($flickr->FLICKR['fotostream'] as $key => $row) {
                $row['socialtype'] = 'flickr';
                $row['text'] = $row['p_comment'];
                $row['title'] = $row['p_title'];
                $row['link'] = 'http://www.flickr.com/people/' . (string )$userfl->user->attributes()->nsid;
                $social_stream[$row['p_time']] = $row;
            }
        }

        // FACEBOOK
        if (class_exists('fbwp_class') && (int)$PLUGIN_OPT['no_fb'] != 1) {
            $fb = new fbwp_class();
            $arr = $fb->load_status_fanpage($PLUGIN_OPT);
            foreach ((array )$arr['data'] as $key => $row) {
                if ($row['message'] != "") {
                    $row['socialtype'] = 'fb';
                    $row['text'] = $row['message'];
                    $row['title'] = (($row['caption'] != "") ? $row['caption'] : ucfirst($row['type']));
                    $row['link'] = 'https://www.facebook.com/' . $this->gblconfig->fb_fanpagename;
                    $social_stream[strtotime($row['created_time'])] = $row;
                }
            }
        }

        //TWITTER
        if (class_exists('tw_class') && (int)$PLUGIN_OPT['no_tw'] != 1) {
            $twitter = new tw_class();
            $twuser = $twitter->get_user_info();
            $timeline = $twitter->get_user_timeline($twuser['id']);
            foreach ((array )$timeline as $key => $row) {
                $row['socialtype'] = 'tw';
                $row['link'] = 'http://twitter.com/#!/' . $this->gblconfig->tw_screenname;
                $social_stream[$row['twcreatetime']] = $row;
            }
        }

        //BLOG
        if (class_exists('tcblog_class') && (int)$PLUGIN_OPT['no_blog'] != 1) {
            $blog = new tcblog_class();
            $timeline = $blog->load_social_stream($PLUGIN_OPT);
            foreach ((array )$timeline as $key => $row) {
                $row['socialtype'] = 'blog';
                $row['text'] = $row['content'];
                $row['link'] = $row['detail_link'];
                $row['created_time'] = $row['inserttime'];
                $social_stream[$row['inserttime']] = $row;
            }
        }

        if ($PLUGIN_OPT['sortdirec'] == 'ASC') {
            krsort($social_stream);
        }
        else {
            ksort($social_stream);
        }

        $social_stream = (array )$this->get_part_of_array($social_stream, 0, (int)$PLUGIN_OPT['ele_count']);
        foreach ($social_stream as $ptime => $row) {
            $social_stream[$ptime]['beforexmonths'] = round((time() - $ptime) / 60 / 60 / 24 / 30);
            $social_stream[$ptime]['beforexdays'] = round((time() - $ptime) / 60 / 60 / 24);
            $social_stream[$ptime]['beforexhours'] = round((time() - $ptime) / 60 / 60);
            $social_stream[$ptime]['beforexmin'] = round((time() - $ptime) / 60);
            $social_stream[$ptime]['ptime'] = $ptime;
        }
        $this->SOCIALSTREAM['social_stream'] = (array )$social_stream;
        return (array )$social_stream;
    }

    /**
     * socialstream_class::parse_socialstream()
     * 
     * @param mixed $params
     * @return
     */
    function parse_socialstream($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_SMSTREAM_')) {
            preg_match_all("={TMPL_SMSTREAM_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_SMSTREAM_", "}");
                $cont_matrix_id = intval(strtolower(str_replace($rep, "", $wert)));
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->smarty->assign('TMPL_SOCIALMEDIA_STREAM_' . $cont_matrix_id, $this->load_stream($PLUGIN_OPT));
                $html = str_replace($tpl_tag[0][$key], '<% assign var=socialmediastream value=$TMPL_SOCIALMEDIA_STREAM_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

}
