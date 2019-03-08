<?php

/**
 * @package    news
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


DEFINE('NEWS_PATH', 'file_data/news/');

class newsfe_class extends modules_class {

    private $langid = 1;

    /**
     * newsfe_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->langid = $GBL_LANGID;
    }

    /**
     * newsfe_class::cmd_print_as_pdf()
     * 
     * @return
     */
    function cmd_print_as_pdf() {
        global $GBL_LANGID;
        $vorlage = get_template(580);
        $N_OBJ = $this->db->query_first("SELECT NL.id AS NLID,K.*,NL.*,NC.*,NG.*
		FROM " . TBL_CMS_NEWSLIST . " NL
		INNER JOIN " . TBL_CMS_NEWSGROUPS . " NG ON (NG.id=NL.group_id)
		LEFT JOIN " . TBL_CMS_NEWSCONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $GBL_LANGID . ")
		LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
		WHERE NL.approval=1 AND NL.id=" . $_GET['id'] . " 
		GROUP BY NL.id LIMIT 1");
        $N_OBJ['date'] = my_date('d.m.Y', $N_OBJ['ndate']);
        $N_OBJ['content'] = $N_OBJ['content'];
        $N_OBJ['introduction'] = strip_tags($N_OBJ['introduction']);
        $N_OBJ['detail_link'] = '/index.php?page=860&aktion=show&id=' . $N_OBJ['NLID'];

        $N_OBJ['img'] = ($N_OBJ['picture'] != "") ? gen_thumb_image('./file_server/newslist/' . $N_OBJ['picture'], 300, 200, $this->gbl_config['optthumb_border']) : '';
        $vorlage = str_replace('{TMPL_INHALT}', $N_OBJ['content'], $vorlage);

        gen_pdf_onfly($vorlage);
    }

    /**
     * newsfe_class::parse_newslist()
     * 
     * @param mixed $params
     * @return
     */
    function parse_newslist($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];

        if (strstr($html, '{TMPL_NEWSINLAY_')) {
            $NEWS_OBJ = new news_class($langid);
            preg_match_all("={TMPL_NEWSINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_NEWSINLAY_", "}");
                $cont_matrix_id = intval(strtolower(str_replace($rep, "", $wert)));
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $NEWS_OBJ->load_newslist($PLUGIN_OPT['sort_column'], $PLUGIN_OPT['sortdirec'], $PLUGIN_OPT['groupid'], $langid, $PLUGIN_OPT['news_count'], $PLUGIN_OPT, $cont_matrix_id);
                $NEWSGROUP = $this->db->query_first("SELECT *,id AS NGID FROM " . TBL_CMS_NEWSGROUPS . " WHERE id=" . (int)$PLUGIN_OPT['groupid'] . " LIMIT 1");
                $this->smarty->assign('TMPL_NEWSGROUPOBJ_' . $cont_matrix_id, $NEWSGROUP);
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$PLUGIN_OPT['tplid']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=newsgroup value=$TMPL_NEWSGROUPOBJ_' . $cont_matrix_id . ' %>
                <% assign var=newslist value=$TMPL_NEWSGROUP_' . $cont_matrix_id . ' %>
                <% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
            }
            keimeno_class::allocate_memory($NEWS_OBJ);
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * newsfe_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        global $HTA_CLASS_CMS;
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='news' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $params = array_merge($params, $SM);
            $result_lang = $this->db->query("SELECT id,post_lang,language FROM " . TBL_CMS_LANG . " WHERE " . (($params['alllang'] === true) ? '' : " id=" . $params['langid'] .
                " AND ") . " approval=1 ORDER BY post_lang");
            while ($rowl = $this->db->fetch_array($result_lang)) {
                $result = $this->db->query("SELECT NL.id AS NLID,NL.*,NC.*
			FROM " . TBL_CMS_NEWSLIST . " NL
			LEFT JOIN " . TBL_CMS_NEWSCONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $rowl['id'] . ")
			WHERE NL.approval=1
			GROUP BY NL.id ORDER BY ndate DESC");
                while ($row = $this->db->fetch_array_names($result)) {
                    $url['url'] = self::get_http_protocol() . '://www.' . FM_DOMAIN . PATH_CMS . $HTA_CLASS_CMS->genLink(49, array(
                        $row['title'],
                        860,
                        $row['NID'],
                        $row['group_id']));
                    #  $url['url'] = 'http://www.' . FM_DOMAIN . PATH_CMS . 'index.php?templang=' . $rowl['id'] . '&page=860&aktion=show&id=' . $row['NLID'];
                    $url['frecvent'] = $params['sm_changefreq'];
                    $url['priority'] = $params['sm_priority'];
                    $params['urls'][] = $url;
                }
            }
        }
        return (array )$params;
    }

    /**
     * newsfe_class::gen_rss_feed()
     * 
     * @return
     */
    function gen_rss_feed() {
        $data = array();
        $sql = "SELECT NL.id AS NLID,K.*,NL.*,NC.*,NG.*,K.email AS MEMAIL, NC.content AS HTML
	FROM " . TBL_CMS_NEWSLIST . " NL
	INNER JOIN " . TBL_CMS_NEWSGROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . intval($_GET['gid']) . ")
	LEFT JOIN " . TBL_CMS_NEWSCONTENT . " NC ON (NL.id=NC.nid AND (NC.lang_id=" . $this->langid . " ) )
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1
	GROUP BY NL.id ORDER BY ndate DESC";
        #	echo $sql;die;
        $result = $this->db->query($sql);
        $k = 0;
        while ($row = $this->db->fetch_array_names($result)) {
            $k++;
            $row['feed_title'] = $row['title'];
            $row['feed_entryLink'] = htmlentities($this->gbl_config['opt_site_domain'] . 'index.php?page=860&aktion=show&id=' . $row['NLID']);
            $row['feed_email'] = $row['MEMAIL'];
            $row['feed_author'] = $row['mitarbeiter_name'];
            $row['body'] = format_meta($row['introduction'], 5000);
            $len = (strlen($row['HTML']) > 5000) ? 5000 : strlen($row['HTML']);
            $row['body'] = (trim($row['introduction']) == "") ? substr(str_replace(array(
                "\n",
                "\t",
                "\r"), '', utf8_encode(html_entity_decode(strip_tags($row['HTML'])))), 0, $len) : $row['body'];
            $row['feed_pubdate'] = gmdate('Y-m-d\TH:i:s\Z', $row['timeint']);
            $row['feed_guid'] = $row['nid'];
            if ($k == 1) {
                list($width, $height) = calcOptWidthHeight(PICS_GAL_ROOT . $row['pic_name'], 300, 200);
                $row['image'] = ($row['picture'] != "") ? gen_thumb_image('./file_server/newslist/' . $row['picture'], $width, $height, 0) : '';
                $first_row = $row;
            }
            $data[] = $row;
        }

        $metadata = array(
            'title' => $first_row['groupname'],
            'description' => $first_row['groupname'],
            'language' => 'DE', # $_SESSION['lang_content']['local']
            'link' => htmlentities($this->gbl_config['opt_site_domain'] . 'index.php?page=' . $_GET['page']),
            'image' => $this->gbl_config['opt_site_domain'] . $first_row['image'],
            'pubdate' => gmdate('Y-m-d\TH:i:s\Z', time()),
            'email' => FM_EMAIL);

        $template = get_template(590);
        include_once (CMS_ROOT . 'includes/rss.class.php');
        $RSS = new rss_class($data, $metadata, $template, $this->langid);
        $RSS->genRSS();
    }
}

?>