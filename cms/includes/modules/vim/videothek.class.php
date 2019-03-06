<?php

# Scripting by Trebaxa Company(R) 2011    									*

/**
 * @package    vim
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


defined('IN_SIDE') or die('Access denied.');


class videothek_class extends modules_class {

    var $VIMCLASS = NULL;
    var $VIM = array();
    var $fix_stocks = array('YT' => 'YouTube', 'VI' => 'Vimeo'); #
    var $max_per_page = 50;

    /**
     * videothek_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->VIMCLASS = new vimeocms_class();
        $this->YTV = new video_yt_class();
        $this->VI = new vimeocms_class();
    }


    /**
     * videothek_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        # if (is_array($this->VIMCLASS->VIM)) $this->VIM = array_merge($this->VIM, $this->VIMCLASS->VIM);
        $this->VIM['fix_stocks'] = $this->fix_stocks;
        $this->VIM['YTOPTIONS'] = $_SESSION['VI']['YTOPTIONS'];
        $this->VIM['stocktype'] = $_REQUEST['stocktype'];
        $this->YTV->parse_to_smarty();
        $this->VIM['loggedin'] = ($this->VI->oauth_access_token != null);
        $this->VIM['state'] = $_SESSION['vimeo_state'];
        $this->VIM['vi_authlink'] = $this->VI->build_auth_link();
        $this->smarty->assign('VIM', $this->VIM);
    }

    /**
     * videothek_class::set_video_opt_fe()
     * 
     * @param mixed $row
     * @param bool $startseite
     * @return
     */
    function set_video_opt_fe(&$row, $startseite = false) {
        $row['v_recorded_ger'] = my_date('d.m.Y', $row['v_upload_date']);
        $row['v_duration'] = $this->seconds_to_hms($row['v_videoduration'], true);
        list($hours, $min, $sec) = explode(':', $row['v_duration']);
        if ($hours == '00') {
            $row['v_duration'] = $min . ':' . $sec;
        }
        if ($startseite === true) {
            $row['thumbnail'] = $this->kf::gen_thumbnail($row['vthumbnailurl'], $row['v_videoid'], $this->gbl_config['vimthumbwidth_fe'], $this->gbl_config['vimthumbheight_fe']);
        }
        else {
            $row['thumbnail'] = $this->kf::gen_thumbnail($row['vthumbnailurl'], $row['v_videoid']);
        }
    }

    /**
     * videothek_class::set_video_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_video_opt(&$row) {
        $this->set_video_opt_fe($row);
    }

    /**
     * videothek_class::load_latest()
     * 
     * @return
     */
    function load_latest() {
        if ($this->gbl_config['mod_vimeo'] == 1) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOS . " WHERE v_approve=1 ORDER BY v_lastupdate DESC LIMIT 10");
            while ($row = $this->db->fetch_array_names($result)) {
                $this->set_video_opt_fe($row, true);
                $this->VIM['video_list_latest'][] = $row;
            }
        }
    }

    /**
     * videothek_class::load_for_frontpage()
     * 
     * @return
     */
    function load_for_frontpage() {
        if ($this->gbl_config['mod_vimeo'] == 1) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOS . " WHERE v_approve=1 AND v_frontpage ORDER BY v_order");
            while ($row = $this->db->fetch_array_names($result)) {
                $this->set_video_opt_fe($row, true);
                $this->VIM['video_list_frontpage'][] = $row;
            }
        }
    }

    /**
     * videothek_class::cmd_vim_save_videos()
     * 
     * @return
     */
    function cmd_vim_save_videos() {
        $VID_ARR = (array )$this->TCR->POST['VID'];
        $VIDS_ARR = (array )$this->TCR->POST['VIDS'];
        if (count($VIDS_ARR) > 0) {
            foreach ($VIDS_ARR as $id)
                $this->db->query("UPDATE " . TBL_CMS_VIDEOS . " SET v_frontpage=0 WHERE v_videoid='" . $id . "' ");
            $VID_ARR = sort_db_result($VID_ARR, 'v_order', SORT_ASC, SORT_NUMERIC);
            $k = 0;
            foreach ($VID_ARR as $id => $VID) {
                $k += 10;
                $VID['v_order'] = $k;
                update_table(TBL_CMS_VIDEOS, 'v_videoid', $id, $VID);
            }
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }


    /**
     * videothek_class::cmd_load_videos_fe()
     * 
     * @return
     */
    function cmd_load_videos_fe() {
        $result_count = $this->db->query_first("SELECT COUNT(*) AS C FROM " . TBL_CMS_VIDEOS . " V, " . TBL_CMS_VIDEO_TOCAT . " VC
             WHERE V.v_approve=1 AND VC.vcm_videoid=V.v_videoid
             " . (($this->TCR->GET['videocid'] > 0) ? " AND VC.vcm_cid=" . (int)$this->TCR->GET['videocid'] : "") . " ");
        $this->VIM['video_totalcount'] = $result_count['C'];

        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOS . " V," . TBL_CMS_VIDEO_TOCAT . " VC 
        WHERE V.v_approve=1 AND VC.vcm_videoid=V.v_videoid
        " . (($this->TCR->GET['videocid'] > 0) ? " AND VC.vcm_cid=" . (int)$this->TCR->GET['videocid'] : "") . " 
        ORDER BY V.v_videotitle  
        ");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_video_opt($row);
            $this->VIM['video_list'][] = $row;
        }
    }

    /**
     * videothek_class::cmd_search_videos_fe()
     * 
     * @return
     */
    function cmd_search_videos_fe() {
        $start = (int)$this->TCR->GET['start'];
        $step = $this->gbl_config['vim_videosperpage'];
        $result_count = $this->db->query_first("SELECT COUNT(*) AS C FROM " . TBL_CMS_VIDEOS . " V 
             WHERE V.v_approve=1 AND V.v_videotitle LIKE '%" . $this->TCR->GET['sword'] . "%'");
        $this->VIM['video_totalcount'] = $result_count['C'];
        $this->VIM['video_start'] = $start + $step;
        $this->VIM['video_startprevious'] = $start - $step;
        $this->VIM['video_start'] = ($this->VIM['video_start'] > $this->VIM['video_totalcount']) ? $this->VIM['video_totalcount'] : $this->VIM['video_start'];
        $this->VIM['video_startnow'] = $start + 1;
        $this->VIM['video_step'] = $step;
        $this->VIM['video_search'] = true;
        $this->VIM['video_hasnext'] = $this->VIM['video_start'] < $this->VIM['video_totalcount'];
        $this->VIM['video_hasprevious'] = $this->VIM['video_startnow'] - 1 > 0;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOS . " V 
        LEFT JOIN " . TBL_CMS_VIDEO_TOCAT . " VC ON (VC.vcm_videoid=V.v_videoid)
        WHERE V.v_approve=1 AND 
        V.v_videotitle LIKE '%" . $this->TCR->GET['sword'] . "%'
        GROUP BY V.v_videoid
        ORDER BY V.v_videotitle  
        LIMIT " . $start . "," . $step . "
        ");

        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_video_opt($row);
            $this->VIM['video_list'][] = $row;
        }
        $this->parse_to_smarty();
        ECHORESULTCOMPILEDFE('<% include file="videothek_videoliste.tpl" %>');
    }

    /**
     * videothek_class::cmd_load_video_fe()
     * 
     * @return
     */
    function cmd_load_video_fe() {
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEOS . " WHERE v_videoid='" . $this->TCR->REQUEST['id'] . "' ORDER BY v_videotitle");
        $this->set_video_opt($R);
        $this->VIM['video'] = $R;
        #echoarr($this->VIM['video']);
    }

    /**
     * videothek_class::cmd_save_cat_table()
     * 
     * @return
     */
    function cmd_save_cat_table() {
        $cats = $_POST['CATS'];
        if (is_array($cats)) {
            $cats = sort_db_result($cats, 'ytc_order', SORT_ASC, SORT_NUMERIC);
            $k = 0;
            foreach ($cats as $id => $row) {
                $k += 10;
                $row['ytc_order'] = $k;
                $id = $row['id'];
                unset($row['id']);
                update_table(TBL_CMS_VIDEOCATS, 'id', $id, $row);
            }
        }
        $this->TCR->set_url_tag('starttree');
        $this->TCR->reset_cmd('showall');
        $this->TCR->add_url_tag('section', 'cats');
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * videothek_class::buildATree()
     * 
     * @param integer $node_id
     * @param integer $startree
     * @return
     */
    function buildATree($node_id = 0, $startree = 0) {
        $node_id = (int)$node_id;
        $startree = (int)$startree;
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'approval_col' => 'ytc_approval',
            'visible_col' => 'ytc_visible',
            'atree_toadd' => 'section=cats&cmd=showall',
            'admin_edit_aktion' => 'catedit'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_name", 0, 0, -1);
        $menutree->atree_toadd = 'section=' . $_REQUEST['section'];
        $this->VIM['admin_tree'] = $menutree->output_as_admin_nav($node_id);
    }

    /**
     * videothek_class::cmd_axapprove_cat_item()
     * 
     * @return
     */
    function cmd_axapprove_cat_item() {
        $parts = explode('-', $this->TCR->REQUEST['id']);
        $id = $parts[1];
        $this->set_approve($this->TCR->REQUEST['value'], $id);
        $this->hard_exit();
    }

    /**
     * videothek_class::set_approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function set_approve($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_VIDEOCATS . " SET ytc_approval='" . (int)$value . "' WHERE id=" . (int)$id . " LIMIT 1");
    }

    /**
     * videothek_class::load_cat_table()
     * 
     * @param mixed $starttree
     * @return
     */
    function load_cat_table($starttree) {
        $cats = array();
        $order = ($this->TCR->GET['order'] != "") ? $this->TCR->GET['order'] : 'order';
        $direc = ($this->TCR->GET['direc'] == "DESC") ? 'DESC' : 'ASC';
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOCATS . " 
		WHERE 
		" . (($this->TCR->REQUEST['wordfilter'] != "") ? " 
        ytc_name LIKE '%" . strip_tags($this->TCR->REQUEST['wordfilter']) . "%'" : "ytc_parent=" . (int)$starttree . "") . " 
		ORDER BY ytc_" . $order . " " . $direc);
        while ($row = $this->db->fetch_array_names($result)) {
            $k++;
            $row['childcount'] = get_data_count(TBL_CMS_VIDEOCATS, 'id', "ytc_parent=" . $row['id']);
            $row['icons'][] = kf::gen_edit_icon($row['id'], '&section=cats', 'catedit');
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['ytc_approval'], 'axapprove_cat_item');
            $row['icons'][] = kf::gen_del_icon_reload($row['id'], 'cat_delete', '{LBLA_CONFIRM}', '&starttree=' . $starttree);
            $row['morder'] *= 1;
            $cats[] = $row;
        }
        $this->VIM['cat_table'] = $cats;
    }

    /**
     * videothek_class::get_cat_name()
     * 
     * @param mixed $id
     * @return
     */
    function get_cat_name($id) {
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEOCATS . " WHERE id=" . (int)$id);
        return $C['ytc_name'];
    }

    /**
     * videothek_class::build_selectbox_arr()
     * 
     * @return
     */
    function build_selectbox_arr() {
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'sign' => ' -> '));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_order", 0, 0, -1);
        $this->VIM['cat_selectbox_arr'] = $menutree->outputtree_select();
        $this->VIM['cat_tree'] = $menutree->menu_array;

        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEOCATS . " WHERE id=" . (int)$this->TCR->REQUEST['videocid']);
        $id = ($C['ytc_parent'] == 0) ? $this->TCR->REQUEST['videocid'] : $C['ytc_parent'];
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_name", $id, 0, -1);
        $this->VIM['cat_selectbox_subarr'] = $menutree->outputtree_select();

        #echoarr($menutree->menu_array);
        unset($menutree);
    }

    /**
     * videothek_class::build_tree_selectbox()
     * 
     * @param mixed $selected_id
     * @param mixed $block_id
     * @return
     */
    function build_tree_selectbox($selected_id, $block_id) {
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'sign' => '|_'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_name", 0, 0, -1);
        $this->VIM['cat_selectbox'] = $menutree->output_as_selectbox((int)$selected_id, (int)$block_id, array('key' => '0', 'value' => '{LA_NOMATTERALL}'));
        unset($menutree);
    }

    /**
     * videothek_class::cmd_cat_delete()
     * 
     * @return
     */
    function cmd_cat_delete() {
        $id = (int)$this->TCR->GET['id'];
        $this->load_cat($id);
        $this->TCR->set_just_turn_back(true);
        if (get_data_count(TBL_CMS_VIDEOCATS, 'id', "ytc_parent=" . $id) > 0) {
            $this->TCR->add_msge('Has children');
            return;
        }
        if (get_data_count(TBL_CMS_VIDEO_TOCAT, '*', "vcm_cid=" . $id) > 0) {
            $this->TCR->add_msge('Has videos');
            return;
        }
        $this->db->query("DELETE FROM " . TBL_CMS_VIDEOCATS . " WHERE id=" . $id . " LIMIT 1");
        $this->TCR->add_msg('{LBL_DELETED}');
    }

    /**
     * videothek_class::load_cat()
     * 
     * @param mixed $id
     * @return
     */
    function load_cat($id) {
        $id = (int)$id;
        $this->CATOBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEOCATS . " WHERE id=" . (int)$id);
        if ($id == 0) {
            $this->CATOBJ['ytc_approval'] = 1;
        }
        $this->VIM['CATOBJ'] = $this->CATOBJ;
    }

    /**
     * videothek_class::build_tree_selectbox_leafsonly()
     * 
     * @param mixed $selected_id
     * @param mixed $block_id
     * @return
     */
    function build_tree_selectbox_leafsonly($selected_id, $block_id) {
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'sign' => '->'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_name", 0, 0, -1);
        $this->VIM['cat_selectbox'] = $menutree->output_as_selectbox_leafsonly((int)$selected_id, (int)$block_id);
        unset($menutree);
    }
    /**
     * videothek_class::cat_breadcrumb_update()
     * 
     * @param mixed $cid
     * @return
     */
    function cat_breadcrumb_update($cid) {
        $cid = (int)$cid;
        $childs = $this->build_flatarr_of_children($cid);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOCATS . " C WHERE " . $childs['sql_cid_filter']);
        while ($row = $this->db->fetch_array_names($result)) {
            $bread_crumb = array();
            $this->create_path($row['id'], $bread_crumb);
            $bread_crumb = array_reverse($bread_crumb, true);
            $CATUPT = array('ytc_path' => trim($this->db->real_escape_string(implode('/', $bread_crumb))));
            $CATUPT['ytc_path'] = '/' . $CATUPT['ytc_path'];
            update_table(TBL_CMS_VIDEOCATS, 'id', $row['id'], $CATUPT);
        }
    }

    /**
     * videothek_class::create_path()
     * 
     * @param mixed $cid
     * @param mixed $bread_crumb
     * @return
     */
    function create_path($cid, &$bread_crumb) {
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEOCATS . " WHERE id=" . (int)$cid);
        $bread_crumb[] = $C['ytc_name'];
        if ($C['ytc_parent'] > 0) {
            $this->create_path($C['ytc_parent'], $bread_crumb);
        }
    }

    /**
     * videothek_class::cmd_alphasort_cats()
     * 
     * @return
     */
    function cmd_alphasort_cats() {
        $cats = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOCATS . " WHERE ytc_parent=" . (int)$this->TCR->GET['parent'] . " ORDER BY ytc_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $cats[] = $row;
        }
        $k = 0;
        foreach ($cats as $key => $row) {
            $k += 10;
            $this->db->query("UPDATE " . TBL_CMS_VIDEOCATS . " SET ytc_order=" . $k . " WHERE id=" . $row['id']);
        }
        $this->TCR->add_msg('{LBL_DONE}');
        $this->TCR->set_just_turn_back(true);
    }


    /**
     * videothek_class::build_flatarr_of_children()
     * 
     * @param mixed $cid
     * @param string $label_id
     * @return
     */
    function build_flatarr_of_children($cid, $label_id = 'C.id') {
        $cid = (int)$cid;
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'sign' => '|_'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_name", $cid, 0, -1);
        $menutree->build_flat_obj_arr($menutree->menu_array, $child_ids);
        if (is_array($child_ids))
            foreach ($child_ids as $child)
                $ids[] = $child['id'];
        $ids[] = $cid;
        $ids = array_unique($ids);
        $cids_str = implode(',', $ids);
        if ($cids_str[0] == ',')
            $cids_str = substr(1, $cids_str);
        $sql_cid_filter = " " . $label_id . " IN (" . $cids_str . ") ";
        unset($menutree);
        return array('cat_ids' => $child_ids, 'sql_cid_filter' => $sql_cid_filter);
    }

    /**
     * videothek_class::cmd_cat_savecat()
     * 
     * @return
     */
    function cmd_cat_savecat() {
        $FORM = $this->TCR->POST['FORM'];
        $id = (int)$this->TCR->POST['id'];
        if ($_POST['FORM']['ytc_name'] == "") {
            $this->VIM['fault_form'] = TRUE;
            $this->TCR->reset_cmd($_POST['orgaktion']);
            $this->load_cat($id);
            $this->VIM['CATOBJ'] = array_merge($this->VIM['CATOBJ'], $FORM);
            $this->TCR->set_fault_form(true);
            return;
        }

        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'label_column' => 'ytc_name',
            'bread_break_sym' => '/'));
        if ($id > 0) {
            update_table(TBL_CMS_VIDEOCATS, 'id', $id, $FORM);
        }
        else {
            $id = insert_table(TBL_CMS_VIDEOCATS, $FORM);
        }
        // Path Update
        $this->cat_breadcrumb_update($id);

        $this->TCR->reset_cmd('catedit');
        $this->TCR->add_url_tag('section', 'cats');
        $this->TCR->add_url_tag('id', $id);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * videothek_class::load_all_ytcats()
     * 
     * @return
     */
    function load_all_ytcats() {
        $this->YTV->load_all_ytcats();
    }

    /**
     * videothek_class::load_iso_table()
     * 
     * @return
     */
    function load_iso_table() {
        $handle = fopen(CMS_ROOT . 'admin/tpl/lngpacks/lng_iso_639-1.csv', "r");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $this->iso_list[strtoupper($data[1])] = array('lname' => $data[0], 'localid' => strtoupper($data[1]));
        }
        fclose($handle);
        $this->iso_list = sort_db_result($this->iso_list, 'lname', SORT_ASC, SORT_STRING);
        $this->VIM['iso_list'] = $this->iso_list;
        return $this->iso_list;
    }

    /**
     * videothek_class::add_tree_selectboxes()
     * 
     * @param mixed $cid_array
     * @param string $okey
     * @return
     */
    function add_tree_selectboxes($cid_array, $okey = 'query') {
        $this->VIM[$okey]['cat_selectboxes'] = array();
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'sign' => '|_'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_name", 0, 0, -1);
        $k = 0;
        if (is_array($cid_array)) {
            foreach ($cid_array as $key => $cid) {
                $k++;
                if ($k > 1) {
                    $this->VIM[$okey]['cat_selectboxes'][$k] = $menutree->output_as_selectbox((int)$cid, 0, array('key' => 0, 'value' => '- NONE -'));
                }
                else {
                    $this->VIM[$okey]['cat_selectboxes'][$k] = $menutree->output_as_selectbox((int)$cid, 0);
                }
            }
        }
        $diff = 3 - $k;
        if ($diff > 0) {
            for ($i = 1; $i <= $diff; $i++) {
                if ($diff == 3 && $i == 1) {
                    $this->VIM[$okey]['cat_selectboxes'][] = $menutree->output_as_selectbox(0, 0, array());
                }
                else {
                    $this->VIM[$okey]['cat_selectboxes'][] = $menutree->output_as_selectbox(0, 0, array('key' => 0, 'value' => '- NONE -'));
                }
            }
        }

        unset($menutree);
    }

    /**
     * videothek_class::cmd_query_run()
     * 
     * @return
     */
    function cmd_query_run() {
        if ($_REQUEST['YTOPTIONS']['startIndex'] <= 1) {
            $_SESSION['VI']['YTOPTIONS'] = $_REQUEST['YTOPTIONS'];
            $_SESSION['VI']['CIDS'] = $_REQUEST['CIDS'];
            if ($_REQUEST['YTOPTIONS']['searchTerm'] == "" && $_REQUEST['YTOPTIONS']['vp_author'] == "") {
                $this->VIM['YTOPTIONS'] = $_SESSION['VI']['YTOPTIONS'];
                $this->VIM['fault_form'] = TRUE;
                $this->TCR->set_fault_form(true);
                return;
            }
        }
        $this->YTV->YT['FORM'] = $_SESSION['VI']['YTOPTIONS'];
        if ($_SESSION['VI']['YTOPTIONS']['vi_stock'] == 'YT') {
            if ($_REQUEST['YTOPTIONS']['startIndex'] < 1000)
                $RET = $this->YTV->sync($_REQUEST['YTOPTIONS']);
        }
        if ($_SESSION['VI']['YTOPTIONS']['vi_stock'] == 'VI') {
            if ($_REQUEST['YTOPTIONS']['startIndex'] < 1000)
                $RET = $this->VI->search($_REQUEST['YTOPTIONS']);
        }
        if ($RET['TotalResults'] - $RET['FORM']['YTOPTIONS']['startIndex'] > 0 && $RET['FORM']['YTOPTIONS']['startIndex'] < 1000 && $RET['FORM']['YTOPTIONS']['startIndex'] <=
            $_REQUEST['YTOPTIONS']['maxTotalLimit']) {
            $url = $_SERVER['PHP_SELF'] . "?return=" . $this->TCR->GET['return'] . "&epage=" . $_REQUEST['epage'] . "&section=qrun&cmd=" . $_REQUEST['cmd'] . '&' .
                http_build_query($RET['FORM']);
            $smarty = $this->smarty;
            include (CMS_ROOT . 'admin/inc/smarty.inc.php');
            HEADER("Refresh: 1;  URL=" . $url);
            $this->VIM['sync_status'] = $RET;
            $this->parse_to_smarty();
            $content = '<% include file="video.run.tpl" %>';
            ECHORESULT(kf::translate_admin(smarty_compile($content)));
            die;
        }
        else {
            header('location:' . $_SERVER['PHP_SELF'] . '?return=' . $this->TCR->GET['return'] . '&epage=' . $_REQUEST['epage'] .
                '&FORM[vp_stock]=YT&cmd=load_result&section=showresult&msg=' . base64_encode('{LBL_DONE}'));
        }
        exit;
    }

    /**
     * videothek_class::cmd_approve_videos()
     * 
     * @return
     */
    function cmd_approve_videos() {
        $vids = $this->TCR->POST['VIDEOIDS'];
        if (is_array($vids)) {
            foreach ($vids as $key => $id) {
                $I = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEO_CACHE . " WHERE yt_videoid='" . $id . "' LIMIT 1");
                $this->db->query("DELETE FROM " . TBL_CMS_VIDEOS . " WHERE v_videoid='" . $id . "'");
                $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_CACHE . " WHERE yt_videoid='" . $id . "'");
                $I['v_apptime'] = time();
                $I['v_valtime'] = time();
                $I['v_stock'] = $_SESSION['VI']['YTOPTIONS']['vi_stock'];
                $I['v_videosrcname'] = $this->fix_stocks[$I['v_stock']];
                $I['v_wlulng'] = $_SESSION['VI']['YTOPTIONS']['vp_wlulng'];
                $VIDEO = array();
                foreach ($I as $key => $wert) {
                    $vkey = str_replace('yt_', 'v_', $key);
                    $VIDEO[$vkey] = $this->db->real_escape_string($I[$key]);
                }
                insert_table(TBL_CMS_VIDEOS, $VIDEO);
                $this->add_video_to_catmatrix($id, $_SESSION['VI']['CIDS']);
            }
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * videothek_class::add_video_to_catmatrix()
     * 
     * @param mixed $video_id
     * @param mixed $cids
     * @return
     */
    function add_video_to_catmatrix($video_id, $cids) {
        $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_TOCAT . " WHERE vcm_videoid='" . $video_id . "'");
        foreach ($cids as $key => $cid) {
            $I = array('vcm_videoid' => $video_id, 'vcm_cid' => $cid);
            if ($cid > 0)
                replace_db_table(TBL_CMS_VIDEO_TOCAT, $I);
        }
    }

    /**
     * videothek_class::kf()
     * 
     * @return
     */
    function kf::gen_thumbnail($url, $id, $width = 0, $height = 0) {
        $height = ($height == 0) ? $this->gbl_config['vmthumb_height'] : $height;
        $width = ($width == 0) ? $this->gbl_config['vmthumb_width'] : $width;
        if ($url != "" && $this->is_valid_url($url) && in_array(strtolower($this->get_ext(basename($url))), array(
            'jpg',
            'png',
            'gif'))) {
            $fname = CMS_ROOT . CACHE . 'video_' . $id . '_' . basename($url);
            if (!file_exists($fname)) {
                $img_binary = $this->get_remote_file($url);
                file_put_contents($fname, $img_binary);
            }
            $G = new graphic_class();
            $img_name = $G->makeThumb($fname, $width, $height, './' . CACHE, TRUE, 'crop');
            unset($G);
            return PATH_CMS . CACHE . basename($img_name);
        }
        else {

            $G = new graphic_class();
            $fname = CMS_ROOT . 'includes/modules/vim/images/no_picture.gif';
            $img_name = $G->makeThumb($fname, $width, $height, './' . CACHE, TRUE, 'crop');
            unset($G);
            return PATH_CMS . CACHE . basename($img_name);
        }
    }

    /**
     * videothek_class::get_remote_file()
     * 
     * @param mixed $url
     * @return
     */
    function get_remote_file($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLINFO_NAMELOOKUP_TIME, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    /**
     * videothek_class::set_video_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_video_options(&$row) {
        $row['v_lastupdate_date'] = my_date('d.m.Y', $row['v_lastupdate']);
        $row['v_uploaddate'] = my_date('d.m.Y', $row['v_upload_date']);
        $row['v_apptime_datetime'] = date('d.m.Y', $row['v_apptime']);
        $row['author'] = ($row['v_author_realname'] == "") ? $row['v_author_username'] : $row['v_author_realname'];
        $row['vtags'] = str_replace(';', ', ', $row['v_videotags']);
        $row['pathes'] = array();
        $row['v_vtitlealpha'] = $this->format_file_name($row['v_videotitle']);
        $row['v_videoduration_min'] = printMenge($row['v_videoduration'] / 60);
        if ($row['v_preview'] != "") {
            $G = new graphic_class();
            $fname = CMS_ROOT . VITHUMB_PATH . $row['v_preview'];
            $img_name = $G->makeThumb($fname, $this->gbl_config['vimthumbheight_fe'], $this->gbl_config['vimthumbheight_fe'], './' . CACHE, TRUE, 'crop');
            unset($G);
            $row['localkf::thumb'] = PATH_CMS . CACHE . basename($img_name);
            # echo $row['localkf::thumb'];
        }
        else {
            $row['localkf::thumb'] = $this->kf::gen_thumbnail($row['ytthumbnailurl'], $row['yt_videoid']);
        }
    }

    /**
     * videothek_class::set_video_options_search()
     * 
     * @param mixed $row
     * @return
     */
    function set_video_options_search(&$row) {
        $row['yt_lastupdate_date'] = my_date('d.m.Y', $row['yt_lastupdate']);
        $row['yt_uploaddate'] = my_date('d.m.Y', $row['yt_upload_date']);
        $row['yt_apptime_datetime'] = date('d.m.Y', $row['yt_apptime']);
        $row['author'] = ($row['yt_author_realname'] == "") ? $row['yt_author_username'] : $row['yt_author_realname'];
        $row['vtags'] = str_replace(';', ', ', $row['yt_videotags']);
        $row['pathes'] = array();
        $row['yt_vtitlealpha'] = $this->format_file_name($row['yt_videotitle']);
        $row['yt_videoduration_min'] = printMenge($row['yt_videoduration'] / 60);
        if ($row['yt_preview'] != "") {
            $G = new graphic_class();
            $fname = CMS_ROOT . VITHUMB_PATH . $row['yt_preview'];
            $img_name = $G->makeThumb($fname, $this->gbl_config['vimthumbheight_fe'], $this->gbl_config['vimthumbheight_fe'], './' . CACHE, TRUE, 'crop');
            unset($G);
            $row['localkf::thumb'] = PATH_CMS . CACHE . basename($img_name);
            # echo $row['localkf::thumb'];
        }
        else {
            $row['localkf::thumb'] = $this->kf::gen_thumbnail($row['ytthumbnailurl'], $row['yt_videoid']);
        }
    }


    /**
     * videothek_class::cmd_load_result()
     * 
     * @return
     */
    function cmd_load_result() {
        $start = (int)$_REQUEST['start'];
        $start = ($start == 0) ? 0 : $start;
        $vresult = array();
        $this->TCR->GET['order'] = ($this->TCR->GET['order'] == "") ? 'VP.videotitle' : $this->TCR->GET['order'];
        $order = (strstr($this->TCR->GET['order'], 'VP.')) ? str_replace('VP.', 'VP.yt_', $this->TCR->GET['order']) : str_replace('M.', 'M.vq_', $this->TCR->GET['order']);
        $direc = ($this->TCR->GET['direc'] == "") ? 'ASC' : $this->TCR->GET['direc'];
        $sql = "SELECT * FROM " . TBL_CMS_VIDEO_CACHE . " VP
	WHERE 1
	ORDER BY " . $order . " " . $direc . "
	LIMIT " . $start . ", " . $this->max_per_page;
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_video_options_search($row);
            $row['approved_video_count'] = get_data_count(TBL_CMS_VIDEOS, 'v_videoid', "v_videoid='" . $row['yt_videoid'] . "'");
            $vresult[] = $row;
        }
        $this->VIM['video_sorting']['direc'] = ($direc == 'ASC') ? 'DESC' : 'ASC';
        $this->VIM['video_list'] = $vresult;
        $this->VIM['video_totalcount'] = get_data_count(TBL_CMS_VIDEO_CACHE, '*', "1");
        $this->genPaging($this->VIM['video_totalcount'], $start, array('order' => $this->TCR->GET['order'], 'direc' => $this->TCR->GET['direc']));
    }

    /**
     * videothek_class::gen_paging_link_admin()
     * 
     * @param mixed $start
     * @param string $toadd
     * @return
     */
    function gen_paging_link_admin($start, $toadd = '') {
        return $this->modify_url($_SERVER['PHP_SELF'] . '?start=' . $start, $this->parse_query($toadd));
    }
    /**
     * videothek_class::genPaging()
     * 
     * @param mixed $total
     * @param mixed $ovStart
     * @param mixed $sorting
     * @return
     */
    function genPaging($total, $ovStart, $sorting = array()) {
        if (is_array($_REQUEST['QFILTER'])) {
            foreach ($_REQUEST['QFILTER'] as $key => $value) {
                $add .= '&QFILTER[' . $key . ']=' . $value;
            }
        }
        if (is_array($sorting)) {
            foreach ($sorting as $key => $value) {
                $add .= '&' . $key . '=' . urlencode($value);
            }
        }

        $toadd = $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . $add . '&id=' . $_REQUEST['id'] . '&cid=' . $_REQUEST['cid'] . '&section=' . $_REQUEST['section'] .
            '&aktion=' . $_REQUEST['aktion'];
        $start = (isset($ovStart)) ? abs((int)$ovStart) : 0;
        $total_pages = ceil($total / $this->max_per_page);
        $akt_page = round($start / $this->max_per_page) + 1;
        if ($total_pages > 0)
            $akt_pages = $akt_page . '/' . $total_pages;
        $start = ($start > $total) ? $total - $this->max_per_page : $start;
        $next_pages_arr = $back_pages_arr = array();
        if ($start > 0)
            $newStartBack = ($start - $this->max_per_page < 0) ? 0 : ($start - $this->max_per_page);
        if ($start > 0) {
            for ($i = $this->pro_num_prepages - 1; $i >= 0; $i--) {
                if ($newStartBack - ($i * $this->max_per_page) >= 0) {
                    $back_pages_arr[] = array(
                        'link' => $_SERVER['PHP_SELF'] . '?start=' . ($newStartBack - ($i * $this->max_per_page)),
                        'linkadmin' => $this->gen_paging_link_admin(($newStartBack - ($i * $this->max_per_page)), $toadd),
                        'index' => ($akt_page - $i - 1));
                }
            }
        }
        if ($start + $this->max_per_page < $total) {
            $newStart = $start + $this->max_per_page;
            for ($i = 0; $i < $this->pro_num_prepages; $i++) {
                if ($newStart + ($i * $this->max_per_page) < $total) {
                    $next_pages_arr[] = array(
                        'link' => '',
                        'linkadmin' => $this->gen_paging_link_admin(($newStart + ($i * $this->max_per_page)), $toadd),
                        'index' => ($akt_page + $i + 1));
                }
            }
        }
        #	die;
        $_paging['start'] = $start;
        $_paging['total_pages'] = $total_pages;
        $_paging['startback'] = $newStartBack;
        $_paging['newstart'] = $newStart;
        $_paging['base_link_admin'] = $this->modify_url($_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'], $this->parse_query($toadd));
        $_paging['back_pages'] = $back_pages_arr;
        $_paging['akt_page'] = $akt_page;
        $_paging['next_pages'] = $next_pages_arr;
        $_paging['backlink'] = $this->gen_paging_link_admin($newStartBack, $toadd);
        $_paging['nextlink'] = $this->gen_paging_link_admin($newStart, $toadd);
        $_paging['count_total'] = $total;
        $this->smarty->assign('paging', $_paging);
        return $_paging;
    }

    /**
     * videothek_class::build_tree_selectbox_filtered()
     * 
     * @param mixed $selected_id
     * @param mixed $block_id
     * @return
     */
    function build_tree_selectbox_filtered($selected_id, $block_id) {
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'ytc_name',
            'label_parent' => 'ytc_parent',
            'label_id' => 'id',
            'sign' => '|_'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_VIDEOCATS . " ORDER BY ytc_parent,ytc_name", 0, 0, -1);
        # $this->remove_cats_by_country_relationship($menutree);
        $this->VIM['cat_selectbox'] = $menutree->output_as_selectbox((int)$selected_id, (int)$block_id, array('key' => '0', 'value' => '{LA_NOMATTERALL}'));
        unset($menutree);
    }

    /**
     * videothek_class::cmd_videolist()
     * 
     * @return
     */
    function cmd_videolist() {
        $this->build_tree_selectbox_filtered($_REQUEST['QFILTER']['cid'], 0);
        $this->load_video_srcs();
        $this->load_videos_by_cat($_REQUEST['QFILTER']['cid'], $_REQUEST['start']);
    }

    /**
     * videothek_class::load_video_srcs()
     * 
     * @return
     */
    function load_video_srcs() {
        $result = $this->db->query("SELECT v_stock FROM " . TBL_CMS_VIDEOS . " WHERE 1 GROUP BY v_stock");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->VIM['stock_list'][] = $row;
        }
    }

    /**
     * videothek_class::request_videos_from_db()
     * 
     * @param mixed $QFILTER
     * @param mixed $childs
     * @param mixed $videos
     * @param mixed $c_sql
     * @param integer $start
     * @param integer $limit
     * @param mixed $orderby
     * @param mixed $direc
     * @return
     */
    function request_videos_from_db($QFILTER, $childs, &$videos, $c_sql, $start = 0, $limit = 50, $orderby, $direc) {
        $sqladd = " FROM " . TBL_CMS_VIDEOCATS . " C, " . TBL_CMS_VIDEO_TOCAT . " VCAT,	 
	 " . TBL_CMS_VIDEOS . " V 
	 WHERE
		" . (($QFILTER['yt_stock'] != "") ? "V.v_stock='" . $QFILTER['yt_stock'] . "' AND " : "") . "
	C.id=VCAT.vcm_cid
	AND V.v_videoid=VCAT.vcm_videoid
	" . (($childs['sql_cid_filter'] != "") ? " AND (" . $childs['sql_cid_filter'] . ")" : '') . "
	 " . ((!empty($QFILTER['searchword'])) ? " AND (LOWER(V.v_videotitle) LIKE LOWER('%" . trim($QFILTER['searchword']) .
            "%') OR LOWER(V.v_videotags) LIKE LOWER('%" . trim($QFILTER['searchword']) . "%'))" : "");

        $sql = "SELECT *,V.v_videoid AS VID,C.id AS CID " . $sqladd . "
	GROUP BY V.v_videoid
	ORDER BY " . $orderby . " " . $direc . " 
	LIMIT " . (int)$start . "," . (int)$limit;
        #	echo $sql;
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->set_video_options($row);
            #  $row['lastexec_datetime'] = ($row['yp_lastexec'] > 0) ? date('d.m.Y H:i:s', $row['yp_lastexec']) : '-';
            $row['approved_date'] = ($row['v_apptime'] > 0) ? date('d.m.Y H:i:s', $row['v_apptime']) : '-';
            $videos[$row['VID']] = $row;
        }
        $C = $this->db->query("SELECT COUNT(V.v_videoid) AS VCOUNT " . $sqladd . " GROUP BY V.v_videoid");
        return (int)$this->db->num_rows($C);
    }

    /**
     * videothek_class::load_video_pathes()
     * 
     * @param string $videoid
     * @return
     */
    function load_video_pathes($videoid = '') {
        $sql = "SELECT *,C.id AS CID
	FROM " . TBL_CMS_VIDEOCATS . " C, " . TBL_CMS_VIDEO_TOCAT . " VCAT
	WHERE C.id=VCAT.vcm_cid	
	" . (($videoid != "") ? " AND VCAT.vcm_videoid='" . $videoid . "'" : "") . "
	GROUP BY vcm_videoid
	ORDER BY C.ytc_path";

        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $pathes[$row['vcm_videoid']][] = $row['ytc_path'];
        }
        return $pathes;
    }

    /**
     * videothek_class::load_videos_by_cat()
     * 
     * @param mixed $cid
     * @param mixed $start
     * @return
     */
    function load_videos_by_cat($cid, $start) {
        $cid = (int)$cid;
        $QFILTER = $_REQUEST['QFILTER'];
        $start = (int)$start;
        $videos = $videos_query = $videos_adhoc = array();
        $c_sql = "";
        if (is_array($_REQUEST['QFILTER'])) {
            $_SESSION['QFILTER'] = $_REQUEST['QFILTER'];
        }
        else {
            $QFILTER = $_SESSION['QFILTER'];
        }

        if ($cid > 0) {
            $childs = $this->build_flatarr_of_children($cid);
        }
        $videos = array();
        $orderby = strval($_GET['col']);
        $orderby = ($orderby == "") ? "v_lastupdate" : $orderby;
        $orderby = 'V.' . $orderby;
        $direc = ($_GET['direc'] == 'ASC') ? 'ASC' : 'DESC';
        $video_filtered_count = (int)$this->request_videos_from_db($QFILTER, $childs, $videos, $c_sql, $start, 50, $orderby, $direc);

        if (count($videos) > 0) {
            //adding pathes & queries
            $pathes_by_videos = $this->load_video_pathes();

            foreach ($videos as $key => $V) {
                if (is_array($pathes_by_videos[$V['VID']]))
                    $videos[$key]['pathes'] = $pathes_by_videos[$V['VID']];
            }
        }
        //sorting
        $direc = ($_GET['direc'] == 'ASC') ? 'SORT_ASC' : 'SORT_DESC';
        $sorttype = ($_GET['sorttype'] == 'NUM') ? 'SORT_NUMERIC' : 'SORT_STRING';
        $direc = ($_GET['direc'] == 'ASC') ? 'DESC' : 'ASC';
        $this->smarty->assign('FILTER', array('direc' => $direc));
        $this->smarty->assign('QFILTER', $QFILTER);
        $this->smarty->assign('qfilter_query', http_build_query(array('QFILTER' => $QFILTER)));
        $this->VIM['video_list'] = $videos;
        $this->VIM['video_filtered_count'] = $video_filtered_count;
        $this->VIM['video_totalcount'] = get_data_count(TBL_CMS_VIDEOS, '*', "1");
        $this->genPaging($this->VIM['video_filtered_count'], $start);
    }

    /**
     * videothek_class::delete_video()
     * 
     * @param mixed $id
     * @return
     */
    function delete_video($id) {
        $V = $this->db->query_first("SELECT * FROM " . TBL_CMS_VIDEOS . " WHERE v_videoid='" . $id . "'");
        delete_file(CMS_ROOT . VITHUMB_PATH . $V['v_preview']);
        $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_CACHE . " WHERE 1");
        $this->db->query("DELETE FROM " . TBL_CMS_VIDEOS . " WHERE v_videoid='" . $id . "'");
        $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_TOCAT . " WHERE vcm_videoid='" . $id . "'");
    }

    /**
     * videothek_class::cmd_vim_delete_videos()
     * 
     * @return
     */
    function cmd_vim_delete_videos() {
        $_SESSION['QFILTER'] = $_POST['QFILTER'];
        if (is_array($_POST['VIDEOIDS'])) {
            foreach ($_POST['VIDEOIDS'] as $video_id) {
                $this->delete_video($video_id);
            }
        }
        $this->TCR->set_url_tags(array('start', 'id'));
        $this->TCR->add_url_tag('section', 'videomanager');
        $this->TCR->add_url_tag('QFILTER', array('QFILTER' => $_POST['QFILTER']));
        $this->TCR->reset_cmd('videolist');
        $this->TCR->add_msg('{LBLA_SAVED}');
    }

    /**
     * videothek_class::cmd_load_via_videos()
     * 
     * @return
     */
    function cmd_load_via_videos() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VIDEOS . " V 
        LEFT JOIN " . TBL_CMS_VIDEO_TOCAT . " VC ON (VC.vcm_videoid=V.v_videoid)  
        WHERE v_syncby='VIA'
        GROUP BY v_videoid 
        ORDER BY v_videotitle");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->VI->set_video_opt($row);
            $this->VIM['video_list'][] = $row;
        }
    }

    /**
     * videothek_class::cmd_save_via_videos()
     * 
     * @return
     */
    function cmd_save_via_videos() {
        $cids = $this->TCR->POST['CIDS'];
        if (is_array($cids)) {
            foreach ($cids as $video_id => $cid) {
                $this->add_video_to_catmatrix($video_id, array($cid));
            }
        }
        $this->TCR->set_just_turn_back(true);
        $this->TCR->add_msg('{LBLA_SAVED}');
    }
}

?>