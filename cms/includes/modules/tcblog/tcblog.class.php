<?php

/**
 * @package    tcblog
 *
 * @copyright  Copyright (C) 2006 - 2018 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


define('BLOG_IMG_PATH', 'file_data/tcblog/');

class tcblog_class extends tcblog_master_class {

    /**
     * tcblog_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->BLOG = array();
    }

    /**
     * tcblog_class::get_tags()
     * 
     * @return
     */
    function get_tags() {
        $this->BLOG['tags'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN . " WHERE approval=1");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->BLOG['tags'] = array_merge($this->BLOG['tags'], explode(',', $row['tags']));
        }
        $this->BLOG['tags'] = array_unique($this->BLOG['tags']);
        $this->trim_array($this->BLOG['tags']);
        sort($this->BLOG['tags']);
        foreach ($this->BLOG['tags'] as $tag) {
            if ($tag != "") {
                $tags[md5(strtolower($tag))] = array('tag' => $tag, 'count' => get_data_count(TBL_CMS_PIN, '*', "(tags LIKE '%," . $tag . ",%' OR tags LIKE '" . $tag .
                        ",%' OR tags LIKE '%," . $tag . "' OR tags='" . $tag . "')"));
            }
        }
        $this->BLOG['tags'] = $tags;
    }

    /**
     * tcblog_class::deleteSinglePin()
     * 
     * @param mixed $id
     * @return
     */
    function deleteSinglePin($id) {
        $id = (int)$id;
        $this->db->query("DELETE FROM " . TBL_CMS_PIN . " WHERE id=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $id);
    }

    /**
     * tcblog_class::getAlterPin()
     * 
     * @return
     */
    function getAlterPin() { // Alter in Tagen
        $differenz = time() - strtotime($this->pin_object['ndate']);
        return round($differenz / 86400);
    }

    /**
     * tcblog_class::clean_blog()
     * 
     * @return
     */
    function clean_blog() {
        if ($this->gbl_config['blog_del_days'] > 0) {
            $search_Date = date("Y-m-d", strtotime(date('Y-m-d') . " -" . intval($this->gbl_config['blog_del_days']) . " day"));
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN . " WHERE ndate<'" . $search_Date . "'");
            while ($row = $this->db->fetch_array_names($result)) {
                $this->deleteSinglePin($row['id']);
            }
        }
    }


    /**
     * tcblog_class::cmd_send_comment_blog()
     * 
     * @return
     */
    function cmd_send_comment_blog() {
        $FORM = $this->strip_html((array )$_POST['FORM']);
        if ($_POST['email'] != "") {
            $this->msge('Hacking');
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . self::anonymizing_ip(REAL_IP) . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hack('Blog comment formular, hacking over hidden field');
        }
        if (strip_tags($_POST['FORM']['c_comment']) != $_POST['FORM']['c_comment']) {
            $this->msge('Hacking');
            $this->LOGCLASS->addLog('HACKING', 'hacking over IP ' . self::anonymizing_ip(REAL_IP) . ', ' . $_SERVER['REQUEST_URI']);
            firewall_class::report_hack('Blog comment formular, hacking with HTML in comment field');
        }
        if ($FORM['c_autor'] == "") {
            $this->msge('Autorname fehlt.');
        }
        if ($FORM['c_comment'] == "") {
            $this->msge('Kommentar fehlt.');
        }
        if ($this->gbl_config['captcha_active'] == 1) {
            if (isset($_SESSION['captcha_spam']) AND $_POST["securecode"] == $_SESSION['captcha_spam']) {
                unset($_SESSION['captcha_spam']);
            }
            else {
                $this->msge("{ERR_SECODE}");
                $contact_err['capcha'] = true;
            }
        }
        if (!$this->has_errors()) {
            $FORM['c_ip'] = self::anonymizing_ip(REAL_IP);
            $FORM['c_approved'] = $this->gbl_config['blog_autoapprove_comment'];
            insert_table(TBL_CMS_PIN_COMMENTS, $FORM);
        }
        self::msg('Beitrag eingereicht. Vielen Dank!');
        $this->ej('reload_blog_comments');
    }

    /**
     * tcblog_class::cmd_reload_blog_comments()
     * 
     * @return
     */
    function cmd_reload_blog_comments() {
        $this->load_single_item($_GET['id']);
        $this->parse_to_smarty_fe();
        echo_template_fe('tcblog_blog-comments');
    }


    /**
     * tcblog_class::autorun()
     * 
     * @return
     */
    function autorun() {
        $this->load_latest();
        $this->parse_to_smarty_fe();
    }

    /**
     * tcblog_class::set_item_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_item_opt(&$row, $opt = array()) {
        if (count($opt) > 0) {
            $method = $opt['foto_resize_method'];
            $width = $opt['foto_width'];
            $height = $opt['foto_height'];
        }
        else {
            $method = 'crop';
            $width = $this->gbl_config['blog_max_width'];
            $height = $this->gbl_config['blog_max_height'];
        }
        $row['date'] = my_date('d.m.Y', $row['ndate']);
        $row['perm']['edit'] = ($this->user_object['kid'] == $row['CUSTID'] && $this->user_object['kid'] > 0) || ($this->user_object['ALLPERM']['tcblog']['edit'] == 1);
        $row['perm']['del'] = ($this->user_object['kid'] == $row['CUSTID'] && $this->user_object['kid'] > 0) || ($this->user_object['ALLPERM']['tcblog']['del'] == 1);
        $row['image_exists'] = file_exists(CMS_ROOT . 'file_data/tcblog/' . $row['b_image']) && $row['b_image'] != "";
        $row['tags'] = array_unique(explode(',', $row['tags']));
        $this->trim_array($row['tags']);
        sort($row['tags']);
        $row['image'] = ($row['b_image'] != "") ? gen_thumb_image('file_data/tcblog/' . $row['b_image'], $width, $height, $method) : gen_thumb_image('images/opt_no_pic.jpg',
            $this->gbl_config['blog_max_width'], $this->gbl_config['blog_max_height'], $this->gblconfig->blog_resize_method);
        $row['thumb'] = ($row['b_image'] != "") ? gen_thumb_image('file_data/tcblog/' . $row['b_image'], $width, $height, $method) : gen_thumb_image('images/opt_no_pic.jpg',
            $width, $height, $method);
        $row['thumb_small'] = ($row['b_image'] != "") ? gen_thumb_image('file_data/tcblog/' . $row['b_image'], $this->gbl_config['blog_thumbwidth'], $this->gbl_config['blog_thumbheight'],
            $this->gbl_config['blog_thumbresize']) : gen_thumb_image('images/opt_no_pic.jpg', $this->gbl_config['blog_thumbwidth'], $this->gbl_config['blog_thumbheight'], $this->
            gbl_config['blog_thumbresize']);
        $row['fotos'] = ($row['b_fotos'] != "") ? unserialize($row['b_fotos']) : array();
        $row['fotos'] = (array )$row['fotos'];
        foreach ((array )$row['fotos'] as $key => $foto) {
            if ($foto['foto'] == "") {
                unset($row['fotos'][$key]);
            }
        }
        $prefix_lng = ($_SESSION['GBL_LANGID'] == $this->gbl_config['std_lang_id']) ? '' : '/' . $_SESSION['GBL_LOCAL_ID'];
        $row['detail_link'] = $this->gen_detail_link($row, $prefix_lng);
        $row['comments'] = $this->load_comments($row['DID'], 1);
        return $row;
    }

    /**
     * tcblog_class::load_social_stream()
     * 
     * @return array
     */
    function load_social_stream($PLUGIN_OPT) {
        $opt = array();
        $opt['foto_width'] = ($PLUGIN_OPT['foto_width'] > 0) ? $PLUGIN_OPT['foto_width'] : $this->gblconfig->blog_max_width;
        $opt['foto_height'] = ($PLUGIN_OPT['foto_height'] > 0) ? $PLUGIN_OPT['foto_height'] : $this->gblconfig->blog_max_height;
        $opt['foto_resize_method'] = ($PLUGIN_OPT['foto_resize_method'] != '') ? $PLUGIN_OPT['foto_resize_method'] : 'crop';
        return $this->load_latest($opt);
    }

    /**
     * tcblog_class::load_latest()
     * 
     * @return array
     */
    function load_latest($opt = array()) {
        $this->BLOG['latestitems'] = array();
        $result = $this->db->query("SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NC.id AS CONID,NL.kid AS CUSTID
	FROM " . TBL_CMS_PIN . " NL
	INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id)
	INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=NG.id " . $this->user_object['sql_groups'] . ")
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->GBL_LANGID . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1 
        AND NC.title!=''
        " . ((isset($opt['groupid'])) ? " AND NG.id=" . $opt['groupid'] : "") . "
	GROUP BY NL.id 
    ORDER BY NL.ndate DESC
    LIMIT " . $this->gbl_config['blog_latestcount']);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_item_opt($row, $opt);
            $this->BLOG['latestitems'][] = $row;
        }

        return $this->BLOG['latestitems'];
    }

    /**
     * tcblog_class::load_blog_items()
     * 
     * @param mixed $pingroup_id
     * @param mixed $langid
     * @return
     */
    function load_blog_items($pingroup_id, $langid) {
        $this->BLOG['items'] = array();
        $result = $this->db->query("SELECT NL.id AS DID,K.*,NL.*,NC.*" . (($pingroup_id > 0) ? ",NG.*,NG.id AS NGID" : "") . ",NC.id AS CONID,NL.kid AS CUSTID
	FROM " . TBL_CMS_PIN . " NL
	" . (($pingroup_id > 0) ? "INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $pingroup_id . ")
    INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=NG.id " . $this->user_object['sql_groups'] . ")
    " : "") . "    
	
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $langid . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1 AND NC.title!=''
	GROUP BY NL.id 
    ORDER BY NL.ndate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_item_opt($row);
            $this->BLOG['items'][] = $row;
            if ($row['DID'] == $_GET['id'])
                $this->selected_item = $row;

        }
        return $this->selected_item;
    }

    /**
     * tcblog_class::load_single_item()
     * 
     * @param mixed $id
     * @return
     */
    function load_single_item($id) {
        $this->selected_item = $this->db->query_first("SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NC.id AS CONID,NL.kid AS CUSTID
	FROM " . TBL_CMS_PIN . " NL
	INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['pingroup_id'] . ")
	INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=NG.id " . $this->user_object['sql_groups'] . ")
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->GBL_LANGID . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1 AND NL.id=" . (int)$id . "
	");
        $this->set_item_opt($this->selected_item);
        return $this->selected_item;
    }

    /**
     * tcblog_class::cmd_load_ym()
     * 
     * @return
     */
    function cmd_load_ym() {
        $this->BLOG['items'] = array();
        $result = $this->db->query("SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NC.id AS CONID,NL.kid AS CUSTID, COUNT(NL.id) AS NCOUNT
	FROM " . TBL_CMS_PIN . " NL
	INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['pingroup_id'] . ")
	INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=NG.id " . $this->user_object['sql_groups'] . ")
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->GBL_LANGID . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1
    AND YEAR(NL.ndate)=" . (int)$_GET['year'] . " AND MONTH(NL.ndate)=" . $_GET['month'] . " AND NC.title!=''
	GROUP BY NL.id 
    ORDER BY NL.ndate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['content'] == "") {
                $TITLE = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $row['DID'] . " AND content<>''");
                $row['title'] = $TITLE['title'];
                $row['content'] = $TITLE['content'];
            }
            $this->set_item_opt($row);
            $this->BLOG['items'][] = $row;
        }
        $this->BLOG['daterange'] = $this->BLOG['filter'][$_GET['year']][(int)$_GET['month']] . ' ' . (int)$_GET['year'];
        $this->CORE->set_metas('Blog  ' . (int)$_GET['year'] . '/' . $_GET['month']);
    }

    /**
     * tcblog_class::cmd_load_by_user()
     * 
     * @return
     */
    function cmd_load_by_user() {
        $this->BLOG['items'] = array();
        $result = $this->db->query("SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NC.id AS CONID,NL.kid AS CUSTID, COUNT(NL.id) AS NCOUNT
	FROM " . TBL_CMS_PIN . " NL
	INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['pingroup_id'] . ")
	INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=NG.id " . $this->user_object['sql_groups'] . ")
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->GBL_LANGID . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1
    AND NL.username='" . $_GET['user'] . "' AND NC.title!=''
	GROUP BY NL.id 
    ORDER BY NL.ndate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['content'] == "") {
                $TITLE = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $row['DID'] . " AND content<>''");
                $row['title'] = $TITLE['title'];
                $row['content'] = $TITLE['content'];
            }
            $this->set_item_opt($row);
            $this->BLOG['items'][] = $row;
        }
        $this->CORE->set_metas('Blog Author ' . $_GET['user']);
    }

    /**
     * tcblog_class::cmd_load_by_tag()
     * 
     * @return
     */
    function cmd_load_by_tag() {
        $this->BLOG['items'] = array();
        $result = $this->db->query("SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NC.id AS CONID,NL.kid AS CUSTID, COUNT(NL.id) AS NCOUNT
	FROM " . TBL_CMS_PIN . " NL
	INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['pingroup_id'] . ")
	INNER JOIN " . TBL_CMS_PIN_PERM . " P ON (P.perm_did=NG.id " . $this->user_object['sql_groups'] . ")
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $this->GBL_LANGID . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1
    AND (NL.tags LIKE '%," . $_GET['tag'] . ",%' OR NL.tags LIKE '" . $_GET['tag'] . ",%' OR NL.tags LIKE '%," . $_GET['tag'] . "' OR NL.tags='" . $_GET['tag'] .
            "') 
    AND NC.title!=''
	GROUP BY NL.id 
    ORDER BY NL.ndate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['content'] == "") {
                $TITLE = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $row['DID'] . " AND content<>''");
                $row['title'] = $TITLE['title'];
                $row['content'] = $TITLE['content'];
            }
            $this->set_item_opt($row);
            $this->BLOG['items'][] = $row;
        }
        $this->CORE->set_metas('Blog Tag ' . $_GET['tag']);
    }

    /**
     * tcblog_class::init_filter()
     * 
     * @param mixed $groupid
     * @return void
     */
    function init_filter($groupid) {
        $YOUNGESTITEM = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN . " WHERE 1  AND group_id=" . (int)$groupid . " ORDER BY ndate DESC LIMIT 1");
        $OLDESTTITEM = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN . " WHERE 1  AND group_id=" . (int)$groupid . " 
        ORDER BY ndate ASC LIMIT 1");
        $end_year = (int)my_date('Y', $YOUNGESTITEM['ndate']);
        $start_year = (int)my_date('Y', $OLDESTTITEM['ndate']);
        $filter = $this->BLOG['filter'] = array();
        if ($end_year > 0) {
            for ($i = $end_year; $i >= $start_year; $i--) {
                $this->BLOG['filter'][$i] = array(
                    '1' => '{LBL_JANUAR}',
                    '2' => '{LBL_FEBRUAR}',
                    '3' => '{LBL_MRZ}',
                    '4' => '{LBL_APRIL}',
                    '5' => '{LBL_MAI}',
                    '6' => '{LBL_JUNI}',
                    '7' => '{LBL_JULI}',
                    '8' => '{LBL_AUGUST}',
                    '9' => '{LBL_SEPTEMBER}',
                    '10' => '{LBL_OKTOBER}',
                    '11' => '{LBL_NOVEMBER}',
                    '12' => '{LBL_DEZEMBER}');
                krsort($this->BLOG['filter'][$i]);
            }

            foreach ((array )$this->BLOG['filter'] as $year => $months) {
                foreach ($months as $mon => $label) {
                    $filter[$year][$mon] = array(
                        'month' => $mon,
                        'label' => $label,
                        'count' => get_data_count(TBL_CMS_PIN, '*', "approval=1 AND YEAR(ndate)=" . (int)$year . " AND MONTH(ndate)=" . $mon));
                }
            }
        }

        $this->BLOG['yearfilter'] = (array )$filter;
    }


    /**
     * tcblog_class::parse_to_smarty_fe()
     * 
     * @return
     */
    function parse_to_smarty_fe() {
        $PAGE_CONNECTED = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " TC 
            WHERE T.id=TC.tid AND TC.lang_id=" . $this->GBL_LANGID . " AND T.gbl_template=0 AND T.module_id='tcblog' LIMIT 1");
        if ($PAGE_CONNECTED['id'] == 0)
            $PAGE_CONNECTED = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " TC  
            WHERE T.id=TC.tid AND TC.lang_id=" . $this->GBL_LANGID . " AND T.gbl_template=1 AND T.module_id='tcblog' LIMIT 1");
        $this->BLOG['base_url'] = PATH_CMS . $PAGE_CONNECTED['t_htalinklabel'] . '.html';
        $this->BLOG['blog_page'] = $this->load_default_blog_page();
        $this->BLOG['blog_item'] = $this->selected_item;
        if ($this->smarty->getTemplateVars('BLOG') != NULL) {
            $this->BLOG = array_merge($this->smarty->getTemplateVars('BLOG'), $this->BLOG);
            $this->smarty->clearAssign('BLOG');
        }

        $this->smarty->assign('BLOG', $this->BLOG);
        $this->smarty->assign('selected_item', $this->selected_item);
    }

    /**
     * tcblog_class::cmd_a_save()
     * 
     * @return
     */
    function cmd_a_save() {
        if ($this->gbl_config['blog_guestcanadd'] == 0 || $_REQUEST['valoaction'] != "") {
            firewall_class::report_hack('Blog worm. Try to add content.');
        }

        if (CU_LOGGEDIN == false && $this->gbl_config['blog_guestcanadd'] == 0) {
            header('location:' . PATH_CMS . 'index.php?logout=1');
            $this->hard_exit();
        }
        $this->selected_item['onlineeditor'] = create_html_editor('FORM_CON[content]', $_POST['FORM_CON']['content'], 300, 'Basic');
        $err_arr = array();

        $err_arr = validate_form_empty_smarty($_POST['FORM_NOTEMPTY']);
        $err_arr = validate_form_empty_smarty($_POST['FORM_CON']);
        if (is_array($_POST['FORM_NOTEMPTY']))
            foreach ($_POST['FORM_NOTEMPTY'] as $key => $value)
                $_POST['FORM'][$key] = $_POST['FORM_NOTEMPTY'][$key];
        $_POST['FORM'] = $this->strip_html($_POST['FORM']);
        $_POST['FORM_CON'] = $this->strip_html($_POST['FORM_CON']);
        if (is_array($_POST['FORM'])) {
            foreach ($_POST['FORM'] as $key => $value) {
                $FORM[$key] = $this->selected_item[$key] = trim($_POST['FORM'][$key]);
            }
        }
        if (is_array($_POST['FORM_CON'])) {
            foreach ($_POST['FORM_CON'] as $key => $value) {
                $this->selected_item[$key] = trim($_POST['FORM_CON'][$key]);
            }
        }

        #Username
        if (CU_LOGGEDIN == false) {
            if (empty($_POST['FORM']['username'])) {
                $err_arr['username'] = true;
            }
        }

        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            $this->msge("invalid token.");
            $err_arr['token'] = true;
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        }

        #capcha
        if ($this->gbl_config['captcha_active'] == 1) {
            if (isset($_SESSION['captcha_spam']) AND $_POST["securecode"] == $_SESSION['captcha_spam']) {
                unset($_SESSION['captcha_spam']);
            }
            else {
                $this->msge("{ERR_SECODE}");
                $err_arr['capcha'] = true;
            }
        }

        $this->smarty->assign('form_err', $err_arr);
        if (count($err_arr) == 0) {
            if (intval($_POST['id']) == 0) {
                $FORM['kid'] = $this->user_object['kid'];
                if (CU_LOGGEDIN == true)
                    $FORM['username'] = $this->user_object['username'];
                $FORM['ndate'] = date('Y-m-d');
                $FORM['inserttime'] = time();
                $FORM['approval'] = $this->gbl_config['blog_autoapprove'];
                $_POST['id'] = insert_table(TBL_CMS_PIN, $FORM);
                $_POST['FORM_CON']['nid'] = $_POST['id'];
            }
            else
                update_table(TBL_CMS_PIN, "id", $_POST['id'], $_POST['FORM']);
            if (intval($_POST['id']) > 0 && intval($_POST['conid']) == 0) {
                $_POST['FORM_CON']['nid'] = $_POST['id'];
                insert_table(TBL_CMS_PIN_CONTENT, $_POST['FORM_CON']);
                $this->LOGCLASS->addLog('ADD', 'new blog item saved on IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
            }

            if (intval($_POST['id']) > 0 && intval($_POST['conid']) > 0) {
                if (CU_LOGGEDIN == true) {
                    update_table(TBL_CMS_PIN_CONTENT, "id", $_POST['conid'], $_POST['FORM_CON']);
                    $this->LOGCLASS->addLog('UPDATE', 'blog item updated by IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
                }
            }
            $this->msg('{LBLA_SAVED}');
            HEADER('location:' . PATH_CMS . 'index.php?clid=' . $this->selected_item['lang_id'] . '&page=' . $_REQUEST['page']);
            $this->hard_exit();
        }
        $this->TCR->reset_cmd('pininsertshow');
        $this->smarty->assign('aktion', 'pininsertshow');
    }


    /**
     * tcblog_class::cmd_a_delpin()
     * 
     * @return
     */
    function cmd_a_delpin() {
        if (!CU_LOGGEDIN && $this->gbl_config['blog_guestcanadd'] == 0)
            header('location:' . PATH_CMS . 'index.php?logout=1');
        if ($this->selected_item['perm']['del'] == TRUE) {
            $this->deleteSinglePin((int)$_GET['id']);
        }
        else {
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&msg=' . base64_encode('Sie haben nicht die Berechtigung.'));
            exit;
        }
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&msg=' . base64_encode('{LBL_DELETED}'));
        exit;
    }

    /**
     * tcblog_class::cmd_showpindetail()
     * 
     * @return
     */
    function cmd_showpindetail() {
        $this->fe_init();
        $this->parse_to_smarty_fe();
        ECHORESULTCOMPILEDFE(get_template(680, $this->GBL_LANGID));
    }

    /**
     * tcblog_class::cmd_load_blog_item()
     * 
     * @return
     */
    function cmd_load_blog_item() {
        $this->CORE->set_metas($this->selected_item['title'], strip_tags($this->selected_item['content']), implode(',', (array )$this->selected_item['tags']));
    }


    /**
     * tcblog_class::cronjob()
     * 
     * @param mixed $params
     * @param mixed $exec_class
     * @return
     */
    function cronjob($params, $exec_class) {
        $start = $exec_class->get_micro_time();
        $this->clean_blog();
        $sidegentime = number_format($exec_class->get_micro_time() - $start, 4, ".", ".");
        $exec_class->feedback .= '<li>Blog bereinigt (' . $sidegentime . ' sek)</li>';
    }

    /**
     * tcblog_class::fe_init()
     * 
     * @return
     */
    function fe_init() {
        #  echoarr($_SERVER);
        if (!isset($_SESSION['pingroup_id']) || ($_SERVER['QUERY_STRING'] == 'page=blog' && !isset($_SESSION['pingroup_id']))) {
            $_SESSION['pingroup_id'] = 0;
        }
        if (isset($_GET['pingid']) && (int)$_GET['pingid'] > 0) {
            $_SESSION['pingroup_id'] = (int)$_GET['pingid'];
        }

        $_GET['clid'] = (($_GET['clid'] > 0) ? $_GET['clid'] : $this->GBL_LANGID);
        if (isset($_GET['id'])) {
            $this->selected_item = $this->load_item($_GET['id']);
            $_SESSION['pingroup_id'] = $this->selected_item['group_id'];
            $this->init_filter($_SESSION['pingroup_id']);
        }
        else {
            $this->selected_item = $this->load_blog_items($_SESSION['pingroup_id'], $_GET['clid']);
            $this->init_filter($_SESSION['pingroup_id']);
        }

        $this->genThemeMenu($_SESSION['pingroup_id']);
        $this->get_tags();


        $this->selected_item['editor_input_name'] = 'FORM_CON[content]';
        $this->selected_item['group_id'] = $_SESSION['pingroup_id'];
        $this->BLOG['theme'] = $this->load_group($_SESSION['pingroup_id']);
        $gbl_item = array();
        $gbl_item['insert_link'] = $_SERVER['PHP_SELF'] . '?aktion=pininsertshow&page=' . $_GET['page'];
        $gbl_item['own_obj'] = $this->selected_item['kid'] == $this->user_object['kid'];
        $gbl_item['perm']['add'] = ($this->user_object['ALLPERM']['tcblog']['add'] == true || $this->gbl_config['blog_guestcanadd'] == 1);
        $this->smarty->assign('gblitem', $gbl_item);
        $this->selected_item['perm']['del'] = ($this->user_object['kid'] == $this->selected_item['kid'] && $this->user_object['kid'] > 0) || $this->user_object['ALLPERM']['tcblog']['del'] ==
            1; // SECURE
        if ($_GET['aktion'] == 'pininsertshow' && $this->gbl_config['blog_guestcanadd'] == 0 && ($this->user_object['kid'] <= 0 || (!$gbl_item['perm']['add']))) {
            header('location: ' . PATH_CMS . 'index.html');
            exit;
        }
        if ($this->selected_item['content'] == "" && $this->selected_item['DID'] > 0) {
            $TITLE = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $this->selected_item['DID'] . " AND content<>''");
            $this->selected_item['content'] = $TITLE['content'];
        }
        $this->selected_item['onlineeditor'] = create_html_editor('FORM_CON[content]', $this->selected_item['content'], 300, 'Basic');
        $content_flags = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval='1' ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $qf['templang'] = $row['id'];
            $row['link'] = $_SERVER['PHP_SELF'] . '?clid=' . $row['id'] . '&id=' . $_GET['id'] . '&aktion=pininsertshow&page=' . $_GET['page'];
            $row['icon'] = SSL_PATH_SYSTEM . IMAGE_PATH . $row['bild'];
            $content_flags[] = $row;
        }
        $this->smarty->assign('content_flags', $content_flags);
        if ($this->selected_item['lang_id'] == 0)
            $this->selected_item['lang_id'] = $_GET['clid'];
        $this->selected_item['title'] = $this->selected_item['title'];
        $this->selected_item['introduction'] = strip_tags($this->selected_item['introduction']);
        $this->selected_item['thumb'] = ($this->selected_item['b_image'] != "") ? gen_thumb_image('file_data/tcblog/' . $this->selected_item['b_image'], $this->
            gbl_config['blog_max_width'], $this->gbl_config['blog_max_height'], 'crop') : '';
        $_GET['clid'] = $this->selected_item['lang_id'];

        $opt = array('groupid' => $_SESSION['pingroup_id']);
        $this->BLOG['latest_items_by_blog'] = $this->load_latest($opt);
    }

    /**
     * tcblog_class::parse_socialstream()
     * 
     * @param mixed $params
     * @return
     */
    function parse_blog($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_TCBLOG_')) {
            preg_match_all("={TMPL_TCBLOG_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $_SESSION['pingroup_id'] = (int)$PLUGIN_OPT['themeid'];
                $this->fe_init();
                $html = str_replace($tpl_tag[0][$key], '<% include file="' . $PLUGIN_OPT['tpl_name'] . '.tpl" %>', $html);
            }
        }

        // Latest Blogs Posts
        if (strstr($html, '{TMPL_TCBLOGLATEST_')) {
            preg_match_all("={TMPL_TCBLOGLATEST_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $this->gbl_config['blog_latestcount'] = $PLUGIN_OPT['count'];
                $this->load_latest($PLUGIN_OPT);
                $this->smarty->assign('TMPL_TCBLOGLATEST_' . $cont_matrix_id, $this->BLOG['latestitems']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=latest_blog_items value=$TMPL_TCBLOGLATEST_' . $cont_matrix_id . ' %><% include file="' . $PLUGIN_OPT['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $this->parse_to_smarty_fe();
        $params['html'] = $html;
        return $params;
    }

    /**
     * tcblog_class::load_all()
     * 
     * @param integer $limit
     * @return
     */
    function load_all_for_sitemap($langid = 1, $local = 'de', $limit = 0) {
        $items = array();
        $result = $this->db->query("SELECT NL.id AS DID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NC.id AS CONID,NL.kid AS CUSTID
	FROM " . TBL_CMS_PIN . " NL
	INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id)	
	LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . $langid . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE NL.approval=1 AND NC.title!=''
	GROUP BY NL.id 
    ORDER BY NL.ndate DESC
    " . (($limit > 0) ? "LIMIT " . $this->gbl_config['blog_latestcount'] : ""));
        while ($row = $this->db->fetch_array_names($result)) {
            $row['detail_link'] = $this->gen_detail_link($row, $local);
            $row['fotos'] = ($row['b_fotos'] != "") ? unserialize($row['b_fotos']) : array();
            $row['fotos'] = (array )$row['fotos'];
            foreach ((array )$row['fotos'] as $key => $foto) {
                if ($foto['foto'] == "") {
                    unset($row['fotos'][$key]);
                }
            }
            $row['image_exists'] = file_exists(CMS_ROOT . 'file_data/tcblog/' . $row['b_image']) && $row['b_image'] != "";
            $row['image'] = ($row['image_exists'] == true) ? 'file_data/tcblog/' . $row['b_image'] : '';
            $items[] = $row;
        }

        return $items;
    }

    /**
     * tcblog_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='tcblog' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $result_lang = $this->db->query("SELECT id,post_lang,language FROM " . TBL_CMS_LANG . " WHERE " . (($params['alllang'] === true) ? '' : " id=" . (int)$params['langid'] .
                " AND ") . " approval=1 ORDER BY post_lang");
            while ($rowl = $this->db->fetch_array($result_lang)) {
                $items = $this->load_all_for_sitemap($rowl['id'], $row['local']);
                foreach ($items as $row) {
                    $url = array('images' => array());
                    $url['url'] = rtrim(self::get_domain_url(), "/") . $row['detail_link'];
                    $url['frecvent'] = $params['sm_changefreq'];
                    $url['priority'] = $params['sm_priority'];
                    if (count($row['fotos']) > 0) {
                        foreach ($row['fotos'] as $foto) {
                            $url['images'][] = array(
                                'loc' => self::get_domain_url() . 'file_data/tcblog/fotos/' . $foto['foto'],
                                'caption' => $foto['foto'],
                                );
                        }
                    }
                    if ($row['image_exists'] == true) {
                        $url['images'][] = array(
                            'loc' => self::get_domain_url() . $row['image'],
                            'caption' => $row['title'],
                            );
                    }
                    $params['urls'][] = $url;
                }
            }
        }
        return $params;
    }

}
