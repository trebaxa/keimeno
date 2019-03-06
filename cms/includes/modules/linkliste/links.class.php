<?php

/**
 * @package    linkliste
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


DEFINE('LBICO_PATH', 'file_data/links/');
DEFINE('ICON_FIELD', 'attfileico');
DEFINE('ICON_PREFIX', 'LINKS_ICO');
DEFINE('TBL_CMS_LINKS', TBL_CMS_PREFIX . 'links');
DEFINE('TBL_CMS_LINKS_CATS', TBL_CMS_PREFIX . 'links_cats');
DEFINE('TBL_CMS_LINKS_TMATRIX', TBL_CMS_PREFIX . 'links_toplmatrix');
DEFINE('TBL_CMS_LINKS_TOPLSET', TBL_CMS_PREFIX . 'links_toplset');

class links_class extends keimeno_class {

    var $BALINKS = array();

    /**
     * links_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GRAPHIC_FUNC;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = $GRAPHIC_FUNC;
        $this->BALINKS = array();
    }


    /**
     * links_class::delete_link()
     * 
     * @param mixed $id
     * @return
     */
    function delete_link($id) {
        $id = (int)$id;
        $linklist_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_LINKS . " WHERE id=" . $id . " LIMIT 1");
        delete_file(CMS_ROOT . LBICO_PATH . $linklist_obj['rl_bild']);
        delete_file(CMS_ROOT . LBICO_PATH . $linklist_obj['rl_flash']);
        #        $this->VPLOG->vp_addlog('delete RL link [' . $id . ']: ' . $linklist_obj['rl_title'], 'RL_LINK_DELETE');
    }


    /**
     * links_class::approve_link()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve_link($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET approval=" . (int)$value . " WHERE id=" . (int)$id . " LIMIT 1");
    }

    /**
     * links_class::approve_group()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve_group($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_LINKS_CATS . " SET lc_approval=" . (int)$value . " WHERE id=" . (int)$id . " LIMIT 1");
    }

    /**
     * links_class::delete_listed_links()
     * 
     * @param mixed $id_list
     * @return
     */
    function delete_listed_links($id_list) {
        if (count($id_list) > 0) {
            foreach ($id_list as $key => $wert) {
                $linklist_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . (int)$wert . " LIMIT 1");
                $this->db->query("DELETE FROM " . TBL_CMS_LINKS . " WHERE id=" . (int)$wert . " LIMIT 1");
                delete_file('..' . PICS_SCR_ROOT . $linklist_obj['bild']);
            }
        }
    }

    /**
     * links_class::move_cat()
     * 
     * @param mixed $id_list
     * @return
     */
    function move_cat($id_list) {
        if (count($id_list) > 0) {
            foreach ($id_list as $key => $wert) {
                $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET spon_alb='" . $cat_alb . "' WHERE id=" . (int)$wert);
            }
        }
    }


    /**
     * links_class::get_first_cid_fe()
     * 
     * @return
     */
    function get_first_cid_fe() {
        $LG = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS_CATS . " WHERE lc_approval=1 LIMIT 1");
        return (int)$LG['id'];
    }

    /**
     * links_class::load_group()
     * 
     * @param mixed $id
     * @return
     */
    function load_group($id) {
        $this->lc_group = $this->db->query_first("SELECT C.*, COUNT(L.id) AS LINKCOUNT FROM " . TBL_CMS_LINKS_CATS . " C
	LEFT JOIN " . TBL_CMS_LINKS . " L ON (L.cat_id=C.id)
	WHERE C.id=" . (int)$id . "
	GROUP BY C.id");
        $this->BALINKS['lgroup'] = $this->lc_group;
    }

    /**
     * links_class::load_groups()
     * 
     * @return
     */
    function load_groups() {
        $result = $this->db->query("SELECT C.*, COUNT(L.id) AS LINKCOUNT FROM " . TBL_CMS_LINKS_CATS . " C
	LEFT JOIN " . TBL_CMS_LINKS . " L ON (L.cat_id=C.id)
	GROUP BY C.id
	ORDER BY C.lc_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id'], '', 'groupedit', 'cid');
            $row['icons'][] = kf::gen_del_icon_reload($row['id'], 'deletegroup', '{LBL_CONFIRM}', '', 'cid');
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['lc_approval'], 'axapprovegroup');
            $this->lc_groups[$row['id']] = $row;
        }
        $this->smarty->assign('linklist_groups', $this->lc_groups);
        $this->BALINKS['linklist_groups'] = $this->lc_groups;
    }

    /**
     * links_class::load_groups_fe()
     * 
     * @return
     */
    function load_groups_fe() {
        $result = $this->db->query("SELECT C.*, COUNT(L.id) AS LINKCOUNT FROM " . TBL_CMS_LINKS_CATS . " C
	LEFT JOIN " . TBL_CMS_LINKS . " L ON (L.cat_id=C.id)
	WHERE C.lc_approval=1
	GROUP BY C.id
	ORDER BY C.lc_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->lc_groups[] = $row;
        }
        $this->smarty->assign('linklist_groups', $this->lc_groups);
        $this->BALINKS['linklist_groups'] = $this->lc_groups;
    }

    /**
     * links_class::save_group()
     * 
     * @param mixed $FORM
     * @param mixed $cid
     * @return
     */
    function save_group($FORM, $cid) {
        $cid = (int)$cid;
        if ($cid > 0) {
            update_table(TBL_CMS_LINKS_CATS, 'id', $cid, $FORM);
        }
        else {
            $cid = insert_table(TBL_CMS_LINKS_CATS, $FORM);
        }
        return $cid;
    }

    /**
     * links_class::del_group()
     * 
     * @param mixed $id
     * @return
     */
    function del_group($id) {
        $id = (int)$id;
        if (get_data_count(TBL_CMS_LINKS, 'id', "cat_id=" . $id) == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_LINKS_CATS . " WHERE id=" . $id);
            return true;
        }
        return false;
    }

    /**
     * links_class::load_toplevel_of_banner()
     * 
     * @param mixed $id
     * @param mixed $row
     * @return
     */
    function load_toplevel_of_banner($id, $row) {
        $row['toplevel'] = $row['toplevellist'] = array();
        $result = $this->db->query("SELECT T.* FROM " . TBL_CMS_LINKS_TMATRIX . " M, " . TBL_CMS_TOPLEVEL . " T 
             WHERE M.l_tid=T.id AND M.l_lid=" . (int)$id);
        while ($row2 = $this->db->fetch_array_names($result)) {
            $row['toplevellist'][] = $row2['description'];
        }
        return $row;
    }

    /**
     * links_class::load_all_toplevel()
     * 
     * @return
     */
    function load_all_toplevel() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " T 
            LEFT JOIN " . TBL_CMS_LINKS_TOPLSET . " TS ON (TS.ts_tid=T.id) 
            WHERE 1 GROUP BY T.id 
            ORDER BY description");
        while ($row2 = $this->db->fetch_array_names($result)) {
            $this->BALINKS['toplevel'][] = $row2;
        }
    }
    /**
     * links_class::load_all_country()
     * 
     * @return
     */
    function load_all_country() {
        $result = $this->db->query("SELECT T.* FROM " . TBL_CMS_LAND . " T WHERE 1");
        while ($row2 = $this->db->fetch_array_names($result)) {
            $this->BALINKS['countries'][] = $row2;
        }
    }

    /**
     * links_class::set_link_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_link_opt(&$row) {
        $row['print_link'] = str_replace(array('http://'), '', $row['url']);
        $row['parsed_url'] = parse_url($row['url']);
        $row['print_url'] = str_replace('www.', '', $row['parsed_url']['host']);
        $row['lb_approve_date_ger'] = date('d.m.Y', $row['lb_approve_date']);
        $row['picture'] = PICS_SCR_ROOT . $row['bild'];
        $row['picturethumb'] = PATH_CMS . 'admin/' . CACHE . $this->GRAPHIC_FUNC->makeThumb('../' . LBICO_PATH . $row['bild'], 100, 75, 'admin/' . CACHE, true, 'crop');
        $row['RLFCK'] = create_html_editor('FORM[sp_comment]', $row['sp_comment'], 100, 'Basic');
        $row['selected_locations'] = array();
        $row['lb_type_written'] = ($row['lb_type'] == 'S') ? 'Script' : (($row['lb_type'] == 'U') ? 'Banner' : 'Flash');
        $row = $this->load_toplevel_of_banner($row['id'], $row);
        switch ($row['lb_position']) {
            case 'TL':
                $row['pos'] = 'Top Left';
                break;
            case 'TC':
                $row['pos'] = 'Top Center';
                break;
            case 'TR':
                $row['pos'] = 'Top Right';
                break;
            case 'ML':
                $row['pos'] = 'Middle Left';
                break;
            case 'MC':
                $row['pos'] = 'Middle Center';
                break;
            case 'MR':
                $row['pos'] = 'Middle Right';
                break;
            case 'BL':
                $row['pos'] = 'Bottom Left';
                break;
            case 'BC':
                $row['pos'] = 'Bottom Center';
                break;
            case 'BR':
                $row['pos'] = 'Bottom Right';
                break;

        }

    }

    /**
     * links_class::load_links()
     * 
     * @param integer $cid
     * @param string $word
     * @param mixed $PFILTER
     * @return
     */
    function load_links($cid = 0, $word = "", $PFILTER = array()) {
        if (count($PFILTER) > 0) {
            $result = $this->db->query("SELECT S.* FROM " . TBL_CMS_LINKS . " S WHERE 1 
  " . (($PFILTER['cat'] > 0) ? " AND S.cat_id=" . (int)$PFILTER['cat'] : "") . " 
  " . (($PFILTER['wort'] != "") ? " AND S.title LIKE '%" . $PFILTER['wort'] . "%'" : "") . "
  " . (($PFILTER['type'] != "") ? " AND S.lb_type='" . $PFILTER['type'] . "'" : "") . "
  ORDER BY S.s_order");
        }
        else {
            $result = $this->db->query("SELECT S.* FROM " . TBL_CMS_LINKS . " S WHERE 1 
  " . (($cid > 0) ? " AND S.cat_id=" . (int)$cid : "") . " 
  ORDER BY S.s_order");
        }
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id'], "", "edit_link", 'id');
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['approval']);
            $row['icons'][] = kf::gen_del_icon_ajax($row['id']);
            $this->set_link_opt($row);
            $this->link_list[] = $row;
        }

        $this->smarty->assign('linkliste', $this->link_list);
    }


    /**
     * links_class::load_links_fe()
     * 
     * @param integer $cid
     * @return
     */
    function load_links_fe($cid = 0) {
        $sm_values = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LINKS . " WHERE approval=1 
" . (((int)$cid > 0) ? " AND cat_id=" . (int)$cid : "") . "
ORDER BY s_order DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            #   echo PICS_SCR_ROOT . $row['bild'];
            $this->set_link_opt_fe($row);
            # $row['picture'] = PICS_SCR_ROOT . $row['bild'];
            # if ($this->gbl_config['links_useresize'] == 1) {
            #     $row['picturethumb'] = PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb('.' . PICS_SCR_ROOT . $row['bild'], $this->gbl_config['links_pic_width'], $this->
            #         gbl_config['links_pic_height'], './' . CACHE, TRUE, 'resize');
            # }
            # else {
            #     $row['picturethumb'] = '.' . PICS_SCR_ROOT . $row['bild'];
            # }
            $sm_values[] = $row;
        }
        $this->smarty->assign('linktable', $sm_values);
    }

    /**
     * links_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->BALINKS['PFILTER'] = (array )$_SESSION['LINKL']['PFILTER'];
        $this->smarty->assign('BALINK', $this->BALINKS);
    }

    /**
     * links_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * links_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        $this->BALINKS['CONFIG'] = $CONFIG_OBJ->buildTable(26, 26);
    }

    /**
     * links_class::cmd_search()
     * 
     * @return
     */
    function cmd_search() {
        if (is_array($_REQUEST['PFILTER']))
            $_SESSION['LINKL']['PFILTER'] = $_REQUEST['PFILTER'];
        $this->load_links(0, 0, $_REQUEST['PFILTER']);
    }

    /**
     * links_class::cmd_groupman()
     * 
     * @return
     */
    function cmd_groupman() {
        $this->load_group($_REQUEST['cid']);
    }
    /**
     * links_class::cmd_groupedit()
     * 
     * @return
     */
    function cmd_groupedit() {
        $this->load_group($_REQUEST['cid']);
    }


    /**
     * links_class::cmd_edit_link()
     * 
     * @return
     */
    function cmd_edit_link() {
        $this->BALINKS['rl'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . (int)$_GET['id'] . " LIMIT 1");
        $this->set_link_opt($this->BALINKS['rl']);
        $RH = $this->validate_url_status($this->BALINKS['rl']['url']);
        $this->BALINKS['rl']['page_ok'] = ($RH['http_code'] == 200);
        $this->BALINKS['rl']['page_info'] = $RH;
        unset($RH);
        if ($this->TCR->GET['cid'] == 0) {
            $_SESSION['WLU']['RL']['cat_id'] = $this->BALINKS['rl']['cat_id'];
        }
        else {
            $this->BALINKS['rl']['cat_id'] = $_SESSION['WLU']['RL']['cat_id'] = $this->TCR->GET['cid'];
        }
        $this->BALINKS['rl']['CAT'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS_CATS . " WHERE id=" . (int)$this->BALINKS['rl']['cat_id']);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE 1 ORDER BY description ASC");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->BALINKS['rl']['toplevel'][] = $row;
        }

        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LINKS_TMATRIX . " WHERE l_lid=" . (int)$_GET['id']);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->BALINKS['rl']['tmatrix'][] = $row['l_tid'];
        }
        $this->BALINKS['rl']['tmatrix'] = (array )$this->BALINKS['rl']['tmatrix'];
    }

    /**
     * links_class::is_valid_url()
     * 
     * @param mixed $url
     * @return
     */
    function is_valid_url($url = null) {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * links_class::set_link_opt_fe()
     * 
     * @param mixed $row
     * @return
     */
    function set_link_opt_fe(&$row) {
        $row['print_link'] = str_replace(array('http://'), '', $row['url']);
        $row['parsed_url'] = parse_url($row['url']);
        $row['print_url'] = str_replace('www.', '', $row['parsed_url']['host']);
        $row['valid_url'] = $this->is_valid_url($row['url']);
        $row['catlink'] = PATH_CMS . 'index.php?page=' . (int)$this->gbl_config['links_pageid'] . '&cid=' . $row['cat_id'];
        $row['approve_date_ger'] = date('d.m.Y', $row['approve_date']);
        $row['picture'] = LBICO_PATH . $row['bild'];
        $thumb = $this->GRAPHIC_FUNC->makeThumb('./' . LBICO_PATH . $row['bild'], $this->gbl_config['links_pic_width'], $this->gbl_config['links_pic_height'], CACHE, true,
            'resize');
        $row['picturethumb'] = (($thumb != "") ? PATH_CMS . CACHE . $thumb : "");
        $row['lb_script_js'] = str_replace("'", '"', $row['lb_script']);
        if ($this->gbl_config['links_countviews'] == 1) {
            $row['vblink'] = PATH_CMS . 'includes/modules/linkliste/links.vb.inc.php?cmd=print_banner&id=' . $row['id'];
        }
        else {
            $row['vblink'] = $row['picturethumb'];
        }
        $row['vboutlink'] = PATH_CMS . 'includes/modules/linkliste/links.vb.inc.php?cmd=out&id=' . $row['id'];

    }

    /**
     * links_class::load_banner_matrix()
     * 
     * @return
     */
    function load_banner_matrix() {
        $MATRIX = $MATRIX_ALL = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LINKS . " L," . TBL_CMS_LINKS_TMATRIX . " M 
        WHERE M.l_lid=L.id AND L.approval=1 ORDER BY RAND()");
        while ($row = $this->db->fetch_array_names($result)) {
            $y = $row['lb_verticalpos'];
            $x = $row['lb_horpos'];
            $this->set_link_opt_fe($row);
            if ($MATRIX[$row['l_tid']][$x][$y]['id'] == 0) {
                $MATRIX[$row['l_tid']][$x][$y] = $row;
            }
            $MATRIX_ALL[$row['l_tid']][$x][$y][] = $row;
        }
        #  echoarr($MATRIX);
        $this->smarty->assign('BANNERMATRIXRAND', $MATRIX);
        $this->smarty->assign('BANNERMATRIXALL', $MATRIX_ALL);
        # echoarr($MATRIX_ALL);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LINKS_CATS . " WHERE 1 ORDER BY lc_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $link_cats[] = $row;
        }
        $this->smarty->assign('link_cats', $link_cats);
    }

    /**
     * links_class::validate_url_status()
     * 
     * @param mixed $url
     * @return
     */
    function validate_url_status($url) {
        #	$url = 'http://www.sherrysheyla.tk';
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
        $url = trim($url);
        if (!strstr($url, '/www.')) {
            # $url=str_replace('http://','http://www.',$url);
        }
        #echo $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLINFO_NAMELOOKUP_TIME, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_exec($ch);
        $info = curl_getinfo($ch); #echoarr($info);die;
        curl_close($ch);
        return $info;
    }


    /**
     * links_class::cmd_axdelete_icon()
     * 
     * @return
     */
    function cmd_axdelete_icon() {
        $parts = explode('-', $this->TCR->GET['id']);
        $id = (int)$parts[1];
        $linklist_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . $id . " LIMIT 1");
        delete_file(CMS_ROOT . LBICO_PATH . $linklist_obj['bild']);
        $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET bild='' WHERE id=" . $id);
        $this->hard_exit();
    }

    /**
     * links_class::cmd_save_link()
     * 
     * @return
     */
    function cmd_save_link() {
        $id = $this->save_link($this->TCR->POST['FORM'], $this->TCR->POST['id'], $_FILES, $this->TCR->POST['TOPLEVEL']);
        $this->TCR->reset_cmd('edit_link');
        $this->TCR->add_url_tag('id', $id);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * links_class::save_link()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @param mixed $FILES
     * @param mixed $TOPLEVEL
     * @return
     */
    function save_link($FORM, $id, $FILES, $TOPLEVEL = array()) {

        $FORM['url'] = (!strstr($FORM['url'], 'http://')) ? 'http://' . $FORM['url'] : $FORM['url'];
        $FORM['sp_comment'] = strip_tags($FORM['sp_comment']);
        $FORM['lb_verticalpos'] = strtoupper(substr($FORM['lb_position'], 0, 1));
        $FORM['lb_horpos'] = strtoupper(substr($FORM['lb_position'], 1, 1));
        if (substr($FORM['url'], -1, 1) == '/')
            $FORM['url'] = substr($FORM['url'], 0, strlen($FORM['url']) - 1);
        if ($id == 0) {
            $FORM['lb_mid_name'] = $this->db->real_escape_string($_SESSION['mitarbeiter_name']);
            $FORM['lb_mid'] = $_SESSION['admin_obj']['id'];
            $FORM['approval'] = 1;
            $FORM['lb_approve_date'] = time();
            $id = insert_table(TBL_CMS_LINKS, $FORM); #  $this->VPLOG->vp_addlog('add link [' . $id . ']: ' . $FORM['title'], 'LINK_ADD');
        }
        else {
            $linklist_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . $id . " LIMIT 1");
            if ($linklist_obj['lb_approve_date'] == 0)
                $FORM['lb_approve_date'] = time();
            update_table(TBL_CMS_LINKS, 'id', $id, $FORM); #  $this->VPLOG->vp_addlog('update RL link [' . $id . ']: ' . $FORM['title'], 'RL_LINK_UPDATE');
        }
        if (!is_dir(CMS_ROOT . LBICO_PATH))
            mkdir(CMS_ROOT . LBICO_PATH, 0775); // ICON
        if ($FILES['aicon']['name'] != "") {
            if (validate_upload_file($FILES['aicon'])) {
                $RetVal = explode('.', $FILES['aicon']['name']);
                $file_extention = strtolower($RetVal[count($RetVal) - 1]);
                if ($file_extention == 'jpeg')
                    $file_extention = 'jpg';
                $new_file_name = CMS_ROOT . LBICO_PATH . 'related_link_icon_' . (int)$id . '.' . $file_extention;
                move_uploaded_file($FILES['aicon']['tmp_name'], $new_file_name);
                clean_cache_like($new_file_name);
                chmod($new_file_name, 0755);
                $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET bild='" . basename($new_file_name) . "' WHERE id=" . (int)$id . " LIMIT 1");
            }
        }
        // FLASH
        if ($FILES['flashfile']['name'] != "") {
            $RetVal = explode('.', $FILES['flashfile']['name']);
            $file_extention = strtolower($RetVal[count($RetVal) - 1]);
            if ($file_extention == 'swf') {
                $new_file_name = CMS_ROOT . LBICO_PATH . 'related_link_swf_' . (int)$id . '.' . $file_extention;
                move_uploaded_file($FILES['flashfile']['tmp_name'], $new_file_name);
                clean_cache_like($new_file_name);
                chmod($new_file_name, 0755);
                $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET rl_flash='" . basename($new_file_name) . "' WHERE id=" . (int)$id . " LIMIT 1");
            }
        }

        $this->db->query("DELETE FROM " . TBL_CMS_LINKS_TMATRIX . " WHERE l_lid=0 OR l_lid=" . (int)$id);
        $toplevel = (array )$TOPLEVEL;
        foreach ($toplevel as $key => $tid) {
            $this->db->query("INSERT INTO " . TBL_CMS_LINKS_TMATRIX . " SET l_lid=" . (int)$id . " , l_tid=" . $tid);
        }

        return $id;
    }

    /**
     * links_class::cmd_delete_link()
     * 
     * @return
     */
    function cmd_delete_link() {
        $this->delete_link($this->TCR->GET['id']);
        $this->TCR->add_msg('{LBL_DELETED}');
    }

    /**
     * links_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        $parts = explode('-', $this->TCR->GET['id']);
        $this->delete_link((int)$parts[1]);
        $this->hard_exit();
    }


    /**
     * links_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        $parts = explode('-', $this->TCR->GET['id']);
        $id = (int)$parts[1];
        $this->approve_link($this->TCR->GET['value'], $id);
        $this->hard_exit();
    }

    /**
     * links_class::cmd_axapprovegroup()
     * 
     * @return
     */
    function cmd_axapprovegroup() {
        $parts = explode('-', $this->TCR->GET['id']);
        $id = (int)$parts[1];
        $this->approve_group($this->TCR->GET['value'], $id);
        $this->hard_exit();
    }

    /**
     * links_class::cmd_save_link_table()
     * 
     * @return
     */
    function cmd_save_link_table() {
        $category = $this->TCR->POST['category'];
        $sorting = $this->TCR->POST['orders'];
        if (count($sorting) > 0) {
            $sarr = array();
            foreach ($sorting as $id => $value) {
                $sarr[$id] = array(
                    's_order' => (int)$sorting[$id],
                    'cat_id' => (int)$category[$id],
                    'id' => $id);
            }

            $sarr = sort_db_result($sarr, 's_order ', SORT_ASC, SORT_NUMERIC); #echoarr($sarr);
            $i = 0;
            foreach ($sarr as $key => $row) {
                $i += 10;
                $row['s_order'] = $i;
                $id = $row['id'];
                unset($row['id']);
                update_table(TBL_CMS_LINKS, 'id', $id, $row);
            }

        }
        $this->TCR->add_msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * links_class::cmd_save_metas()
     * 
     * @return
     */
    function cmd_save_metas() {
        $id_list_save = (array )$this->TCR->POST['metaids'];
        $id_list_del = (array )$this->TCR->POST['metaidsdel'];
        $rows = (array )$this->TCR->POST['ROW'];
        if (count($rows) > 0) {
            foreach ($rows as $id => $row) {
                if (in_array($id, $id_list_save))
                    update_table(TBL_CMS_LINKS, 'id', $id, $row);
                if (in_array($id, $id_list_del))
                    $this->delete_link($id);
            }
        }
        $this->TCR->add_msg('{LBL_DONE}');
    }


    /**
     * links_class::cmd_delete_listed_links()
     * 
     * @return
     */
    function cmd_delete_listed_links() {
        $id_list = $this->TCR->POST['metaids'];
        if (count($id_list) > 0) {
            foreach ($id_list as $key => $wert) {
                $this->delete_link($wert);
            }
        }
        $this->TCR->add_msg('{LBL_DELETED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * links_class::cmd_show_meta_import()
     * 
     * @return
     */
    function cmd_show_meta_import() {
        if (count($this->TCR->POST['metaids']) > 0) {
            foreach ($this->TCR->POST['metaids'] as $key => $wert) {
                $RL = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . (int)$wert . " LIMIT 1");
                $metas = get_data_by_url($RL['url']);
                if ($metas['title'] == "SITE NOT FOUND")
                    $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET title='SITE NOT FOUND' WHERE id=" . $wert . " LIMIT 1");
                $RL['metas'] = (array )$metas;
                $this->BALINKS['meta_rl'][] = $RL;
            }
        }
    }

    /**
     * links_class::cmd_savegroup()
     * 
     * @return
     */
    function cmd_savegroup() {
        $FORM = $this->TCR->POST['FORM'];
        $cid = (int)$this->TCR->POST['cid'];
        if ($cid > 0) {
            update_table(TBL_CMS_LINKS_CATS, 'id', $cid, $FORM);
        }
        else {
            $cid = insert_table(TBL_CMS_LINKS_CATS, $FORM);
        }
        $this->TCR->reset_cmd('groupedit');
        $this->TCR->add_url_tag('cid', $cid);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * links_class::cmd_save_toplevel_settings()
     * 
     * @return
     */
    function cmd_save_toplevel_settings() {
        foreach ($this->TCR->POST['FORM'] as $tid => $row) {
            $this->db->query("DELETE FROM " . TBL_CMS_LINKS_TOPLSET . " WHERE ts_tid=" . $tid);
            $row['ts_tid'] = $tid;
            insert_table(TBL_CMS_LINKS_TOPLSET, $row);
        }
        $this->hard_exit();
    }

    /**
     * links_class::cmd_print_banner()
     * 
     * @return
     */
    function cmd_print_banner() {
        $bannerid = (int)$_GET['id'];
        $hash = md5($bannerid . REAL_IP);
        if ($_SESSION['linklist']['bannerviewed'][date('Y-m-d')][$hash] == 0) {
            $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET lb_views=lb_views+1 WHERE id=" . $bannerid);
            $_SESSION['linklist']['bannerviewed'][date('Y-m-d')][$hash]++;
        }
        $B = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . $bannerid);
        $B['picture'] = CMS_ROOT . LBICO_PATH . $row['bild'];
        if ($this->gbl_config['links_useresize'] == 1) {
            $thumb = $this->GRAPHIC_FUNC->makeThumb('../../../' . LBICO_PATH . $B['bild'], $this->gbl_config['links_pic_width'], $this->gbl_config['links_pic_height'],
                CACHE, true, 'resize');
            $B['picturethumb'] = (($thumb != "") ? PATH_CMS . CACHE . $thumb : "");
            $banner = CMS_ROOT . CACHE . $thumb;
        }
        else {
            $banner = CMS_ROOT . LBICO_PATH . $B['bild'];
        }
        echo file_get_contents($banner);
        $this->hard_exit();
    }

    /**
     * links_class::cmd_out()
     * 
     * @return
     */
    function cmd_out() {
        $bannerid = (int)$_GET['id'];
        $hash = md5($bannerid . REAL_IP);
        if ($_SESSION['linklist']['bannerclicked'][date('Y-m-d')][$hash] == 0) {
            $this->db->query("UPDATE " . TBL_CMS_LINKS . " SET lb_clicks=lb_clicks+1 WHERE id=" . $bannerid);
            $_SESSION['linklist']['bannerclicked'][date('Y-m-d')][$hash]++;
        }
        $B = $this->db->query_first("SELECT * FROM " . TBL_CMS_LINKS . " WHERE id=" . $bannerid);
        header('location: ' . $B['url']);
        $this->hard_exit();
    }

    /**
     * links_class::cmd_deletegroup()
     * 
     * @return
     */
    function cmd_deletegroup() {
        $r = $this->del_group($_GET['cid']);
        if ($r === true) {
            $this->msg("{LBL_DELETED}");
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=groupman');
        }
        else {
            $this->msge("beinhaltet noch Links");
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=groupman');
        }
        exit;
    }

}
