<?php

/**
 * @package    gallery
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


defined('IN_SIDE') or die('Access denied.');


DEFINE('PICS_GAL_ROOT', PICS_ROOT . 'gallery/');
DEFINE('PICS_GAL_ROOT_ADMIN', '.' . PICS_ROOT . 'gallery/');
DEFINE('TBL_CMS_GLGRCON', TBL_CMS_PREFIX . 'gal_gcontent');
DEFINE('TBL_CMS_GALCON', TBL_CMS_PREFIX . 'gal_content');
DEFINE('TBL_CMS_GALGROUP', TBL_CMS_PREFIX . 'gal_groups');
DEFINE('TBL_CMS_GALPICS', TBL_CMS_PREFIX . 'gal_pics');

include_once (CMS_ROOT . 'includes/modules/gallery/admin/gallery.class.php');

class gal_class extends modules_class {
    var $langid = "";
    var $GID = 0;
    var $nodes_gal = null;
    var $user = array();
    var $GALLERY_OBJ = array();
    var $GRAPHIC_FUNC = null;

    /**
     * gal_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GRAPHIC_FUNC;
        parent::__construct();
        $this->GRAPHIC_FUNC = $GRAPHIC_FUNC;
        $this->TCR = new kcontrol_class($this);
        $this->gblconfig->gal_album_sort = ($this->gblconfig->gal_album_sort == 'manuell') ? 'g_order' : $this->gblconfig->gal_album_sort;
    }

    // ********************
    // GLOBAL MOD FUNCTIONS
    // ********************
    /**
     * gal_class::gen_entry_point_list()
     * 
     * @param mixed $list
     * @param mixed $nodes
     * @param mixed $topl
     * @return
     */
    function gen_entry_point_list(&$list, &$nodes = null, $topl = array()) {
        $list = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALGROUP . " WHERE parent=0 ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $list[] = array(
                'id' => $row['id'],
                'label' => $row['groupname'],
                );
        }
        return $list;
    }

    /**
     * gal_class::set_entry_point()
     * 
     * @param mixed $TOPLEVEL_OBJ
     * @return
     */
    function set_entry_point(&$TOPLEVEL_OBJ) {
        if (isset($_GET['tl']) && $_GET['tl'] > 0) {
            $_GET['gid'] = ($TOPLEVEL_OBJ['first_page'] > 0) ? (int)$TOPLEVEL_OBJ['first_page'] : 0;
            $_GET['aktion'] = 'show_gallery';
            $_GET['page'] = 3;
        }
    }


    /**
     * gal_class::genGalleryImplement()
     * 
     * @param mixed $id
     * @return
     */
    function genGalleryImplement($id) {
        return '{TMPL_GALINLAY_' . $id . '}';
    }

    /**
     * gal_class::cmd_galaxapprove_item()
     * 
     * @return
     */
    function cmd_galaxapprove_item() {
        $parts = explode('-', $this->TCR->GET['id']);
        $id = $parts[1];
        $this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET approval='" . $this->TCR->GET['value'] . "' WHERE id=" . (int)$id . " LIMIT 1");
        $this->hard_exit();
    }

    /**
     * gal_class::cmd_a_savecontent()
     * 
     * @return
     */
    function cmd_a_savecontent() {
        if ($this->TCR->POST['FORM_CON_ID'] > 0)
            update_table(TBL_CMS_GLGRCON, 'id', $this->TCR->POST['FORM_CON_ID'], $this->TCR->POST['FORM_CON']);
        else
            insert_table(TBL_CMS_GLGRCON, $this->TCR->POST['FORM_CON']);

        $this->hard_exit();
    }

    /**
     * gal_class::set_galmenu_opt()
     * 
     * @param mixed $arr
     * @param mixed $new_arr
     * @return
     */
    function set_galmenu_opt($arr, &$new_arr) {
        foreach ($arr as $key => $cat) {
            $cat['catlabel'] = ($cat['g_title'] != "") ? $cat['g_title'] : $cat['groupname'];
            $cat['catlink'] = $this->genGalleryLink($cat['id'], $cat['catlabel'], $_SESSION['GBL_LANGID']);
            $cat['catdescription'] = $cat['g_content'];
            $new_arr[$key] = $cat;
            unset($new_arr[$key]['children']);
            if (is_array($cat['children'])) {
                $this->set_galmenu_opt($cat['children'], $new_arr[$key]['children']);
            }
        }
    }

    /**
     * gal_class::load_tree()
     * 
     * @return
     */
    function load_tree() {
        include_once (CMS_ROOT . 'includes/tree.class.php');
        $this->nodes_gal = new cms_tree_class();
        $this->nodes_gal->db = $this->db;
        $this->nodes_gal->label_column = 'groupname';
        $this->nodes_gal->create_result_and_array("SELECT G.id, G.parent, G.groupname, G.pic_count,g_title,g_content FROM " . TBL_CMS_GALGROUP . " G
        LEFT JOIN " . TBL_CMS_GLGRCON . " C ON (C.g_id=G.id AND lang_id=" . (int)$_SESSION['GBL_LANGID'] . ")
        WHERE G.approval=1 AND G.g_enabled=1
        GROUP BY G.id
        ORDER BY parent,groupname", 0, 0, -1);
        $result = $this->db->query("SELECT T.id, T.parent, T.groupname, pic_count FROM " . TBL_CMS_GALGROUP . " T WHERE parent=0 AND approval=1 ORDER BY T.groupname");
        while ($row = $this->db->fetch_array_names($result))
            $approved_gal_ids[] = $row['id'];
        $result = $this->db->query("SELECT T.id, T.parent, T.groupname, pic_count FROM " . TBL_CMS_GALGROUP . " T WHERE approval=1 ORDER BY T.groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $data_gal[] = $row;
        }
        $gallery_tree = array();
        $this->set_galmenu_opt($this->nodes_gal->menu_array, $gallery_tree);

        $selected_gallery = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALGROUP . " WHERE id =" . $this->GID);
        $parent_gal = $this->db->query_first("SELECT G.* FROM " . TBL_CMS_GALGROUP . " G  
            LEFT JOIN " . TBL_CMS_GLGRCON . " C ON (C.g_id=G.id AND lang_id=" . (int)$_SESSION['GBL_LANGID'] . ") 
            WHERE G.id=" . (int)$selected_gallery['parent']);
        $parent_gal['link'] = $this->genGalleryLink($parent_gal["id"], $parent_gal["groupname"], (int)$_SESSION['GBL_LANGID']);
        $selected_gallery['link'] = $this->genGalleryLink($selected_gallery["id"], $selected_gallery["groupname"], (int)$_SESSION['GBL_LANGID']);
        $gallery_obj = array(
            'album_select' => $this->nodes_gal->output_as_selectbox('FORM[group_id]', '', $this->GID),
            'gid' => $this->GID,
            'albumcount' => get_data_count(TBL_CMS_GALGROUP, 'id', "1"),
            'gallery_html_menu' => $this->nodes_gal->build_core_cms_tree($data_gal, $approved_gal_ids),
            'gallery_tree' => $gallery_tree,
            'parent_gal' => $parent_gal,
            'selected_gallery' => $selected_gallery,
            #   'gallery_breadcrumbs' => $this->nodes_gal->bread_html
            );
        $this->smarty->assign('gallery_obj', $gallery_obj);
        $this->recalc_pic_count($this->nodes_gal->menu_array);
    }

    /**
     * gal_class::recalc_pic_count()
     * 
     * @param mixed $nested_arr
     * @return
     */
    function recalc_pic_count(&$nested_arr) {
        foreach ($nested_arr as $key => $row) {
            $pc = get_data_count(TBL_CMS_GALPICS, 'id', "group_id=" . $row['id']);
            $this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET pic_count=" . (int)$pc . " WHERE id=" . $row['id']);
            if (is_array($row['children']))
                $this->recalc_pic_count($row['children']);
        }
    }

    /**
     * gal_class::genPaging()
     * 
     * @param mixed $total
     * @return
     */
    function genPaging($total) {
        $newStartBack = 0;
        $start = (isset($_GET['start'])) ? abs((int)$_GET['start']) : 0;
        $total_pages = ceil($total / $this->gbl_config['pro_max_paging']);
        $akt_page = round($start / $this->gbl_config['pro_max_paging']) + 1;
        if ($total_pages > 0)
            $akt_pages = $akt_page . '/' . $total_pages;
        $start = ($start > $total) ? $total - $this->gbl_config['pro_max_paging'] : $start;
        $base_link = $_SERVER['PHP_SELF'] . "?page=" . $_GET['page'];
        $base_link .= ($this->GID != "") ? '&gid=' . $this->GID : '';
        $next_pages_arr = $back_pages_arr = array();
        if ($start > 0)
            $newStartBack = ($start - $this->gbl_config['pro_max_paging'] < 0) ? 0 : ($start - $this->gbl_config['pro_max_paging']);
        if ($start > 0) {
            for ($i = $this->gbl_config['pro_num_prepages'] - 1; $i >= 0; $i--) {
                if ($newStartBack - ($i * $this->gbl_config['pro_max_paging']) >= 0) {
                    $back_pages_arr[] = " <a href=\"" . $base_link . "&start=" . ($newStartBack - ($i * $this->gbl_config['pro_max_paging'])) . "\">" . ($akt_page - $i - 1) .
                        "</a>";
                }
            }
        }
        if ($start + $this->gbl_config['pro_max_paging'] < $total) {
            $newStart = $start + $this->gbl_config['pro_max_paging'];
            for ($i = 0; $i < $this->gbl_config['pro_num_prepages']; $i++) {
                if ($newStart - 1 + ($i * $this->gbl_config['pro_max_paging']) < $total) {
                    $next_pages_arr[] = " <a href=\"" . $base_link . "&start=" . ($newStart + ($i * $this->gbl_config['pro_max_paging'])) . "\">" . ($akt_page + $i + 1) . "</a>";
                }
            }
        }
        $_paging['start'] = $start;
        $_paging['total_pages'] = $total_pages;
        $_paging['startback'] = $newStartBack;
        $_paging['newstart'] = $newStart;
        $_paging['base_link'] = $base_link;
        $_paging['back_pages'] = $back_pages_arr;
        $_paging['akt_page'] = $akt_page;
        $_paging['next_pages'] = $next_pages_arr;
        $_paging['product_count_total'] = $total;
        $this->smarty->assign('paging', $_paging);
    }

    /**
     * gal_class::generate_allthumbs()
     * 
     * @param mixed $start
     * @return
     */
    function generate_allthumbs($start) {
        $result = $this->db->query("SELECT A.*, C.*, C.id AS GID
	FROM " . TBL_CMS_GALGROUP . " C," . TBL_CMS_GALPICS . " A
	WHERE A.group_id=C.id
	ORDER BY A.id ASC
	LIMIT " . (int)$start . ",30");
        $arr = sqlresult_to_array($result);
        foreach ($arr as $key => $row) {
            $this->GALLERY_OBJ = $this->db->query_first("SELECT *,G.id AS GID FROM " . TBL_CMS_GALGROUP . " G
		WHERE G.id=" . $row['GID'] . "
		LIMIT 1");
            $img1 = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb('.' . PICS_GAL_ROOT . $row['pic_name'], $row['thumb_width'], $row['thumb_height'], 'admin/' . CACHE, true,
                $this->GALLERY_OBJ['thumb_type']);
            $img2 = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb('.' . PICS_GAL_ROOT . $row['pic_name'], $row['max_width'], $row['max_height'], 'admin/' . CACHE, true,
                'resize');
            copy($img1, '.' . $img1);
            copy($img2, '.' . $img2);
            if (file_exists($img1))
                unlink($img1);
            if (file_exists($img2))
                unlink($img2);
            $pics[] = array('img_src_1' => '.' . $img1, 'img_src_2' => '.' . $img2);
        }
        $this->smarty->assign('adminpictab', $pics);
        return $this->db->num_rows($result);
    }

    /**
     * gal_class::set_picture_options()
     * 
     * @param mixed $arr
     * @param mixed $galleryname
     * @param string $thwidth
     * @param string $thheight
     * @param string $th_type
     * @param string $g_croppos
     * @return
     */
    function set_picture_options($arr, $galleryname, $thwidth = '', $thheight = '', $th_type = '', $g_croppos = 'Center') {
        $image_list = array();
        if ($this->GALLERY_OBJ['thumb_type'] == '')
            $this->GALLERY_OBJ['thumb_type'] = 'resize';
        if ($th_type != "")
            $this->GALLERY_OBJ['thumb_type'] = $th_type;
        if ($this->GALLERY_OBJ['g_croppos'] == "")
            $this->GALLERY_OBJ['g_croppos'] = 'center';
        $this->GALLERY_OBJ['g_croppos'] = ($g_croppos != "") ? $g_croppos : $this->GALLERY_OBJ['g_croppos'];
        $gal_parent = 0;
        if (is_array($arr)) {
            foreach ($arr as $key => $row) {
                $row['g_title'] = ($row['g_title'] == "") ? $row['groupname'] : $row['g_title'];
                $description = "";
                $row['thumb_width'] = ($thwidth > 0) ? (int)$thwidth : $row['thumb_width'];
                $row['thumb_height'] = ($thheight > 0) ? (int)$thheight : $row['thumb_height'];
                if ($row['pic_content'] != "")
                    $description = substr(strip_tags($row['pic_content']), 0, 100);
                if ($row['PICTITLE'] == "")
                    $row['PICTITLE'] = $row['pic_title'];
                if ($row['PICTITLE'] == "")
                    $row['PICTITLE'] = $row['g_title'];
                $gal_parent = $row['parent'];
                if ($this->gbl_config['gal_waterm_auto'] == 1) {
                    $watermark = array(
                        'watermark' => './images/watermark.png',
                        'pos' => $this->gbl_config['gal_waterm_pos'],
                        'trans' => $this->gbl_config['gal_waterm_trans']);
                }
                $img_src = PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $row['pic_name'], $row['thumb_width'], $row['thumb_height'], './' . CACHE, true, $this->
                    GALLERY_OBJ['thumb_type'], '', '', $this->GALLERY_OBJ['g_croppos']);
                $image_list[$row['PICID']] = array(
                    'img_title' => htmlspecialchars($row['PICTITLE']),
                    'img_src' => $img_src,
                    'img_cut_type' => $this->GALLERY_OBJ['thumb_type'] . ' ' . $row['thumb_width'] . 'x' . $row['thumb_height'],
                    'img_copyright' => htmlspecialchars($row['fotoquelle']),
                    'img_orgsrc' => PICS_GAL_ROOT . $row['pic_name'],
                    'img_redfullsize' => PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $row['pic_name'], $row['max_width'], $row['max_height'], './' . CACHE, true,
                        'resize', "", $watermark, $this->GALLERY_OBJ['g_croppos']),
                    'img_image' => PICS_GAL_ROOT . $row['pic_name'],
                    'img_link' => $this->gen_image_detail_link($row['id'], $row['g_title'] . $row['pic_title'], $this->langid),
                    'img_gallink' => $this->genGalleryLink($row["GID"], $row["g_title"], $this->langid),
                    'img_descshort' => $description,
                    'img_id' => $row['PICID'],
                    'img_descplain' => htmlspecialchars(strip_tags($row['pic_content'])),
                    'img_description' => str_replace(array(
                        "\r",
                        "\n",
                        "\t"), "", htmlspecialchars($row['pic_content'])),
                    'img_descriptionplain' => strip_tags(str_replace(array(
                        "\r",
                        "\n",
                        "\t"), "", ($row['pic_content']))),
                    'img_galleryname' => $row['g_title'],
                    'img_thheight' => $row['thumb_height'],
                    'img_thwidth' => $row['thumb_width'],
                    'img_fullheight' => $row['max_height'],
                    'img_fullwidth' => $row['max_width'],
                    'imginfo' => $row);
            }
        }
        $params = exec_evt('OnLoadGalleryImages', array(
            'image_list' => $image_list,
            'gallery_id' => $this->GID,
            'langid' => $this->langid), $this);
        $tarr = explode('_', $galleryname);
        $content_matrix_id = array_pop($tarr);
        $this->smarty->assign($galleryname, $params['image_list']);
        $this->smarty->assign('TMPL_GALOBJ_' . $content_matrix_id, $row);
        $this->smarty->assign('gallery_count', count($params['image_list']));
        $this->smarty->assign('gallery_id', $this->GID);
        return array('parent' => $gal_parent, 'pic_list' => $params['image_list']);
    }

    /**
     * gal_class::get_part_of_array()
     * 
     * @param mixed $list
     * @param mixed $from
     * @param mixed $limit
     * @return
     */
    function get_part_of_array($list, $from, $limit) {
        $temp_list = array_chunk($list, $limit);
        return $temp_list[floor($from / $limit)];
    }

    /**
     * gal_class::init_obj()
     * 
     * @param mixed $langid
     * @param mixed $user
     * @param integer $gid
     * @param bool $treeload
     * @return
     */
    function init_obj($langid, $user, $gid = 0, $treeload = true) {
        $this->langid = (int)$langid;
        $this->user = $user;
        $this->GID = (int)$gid;
        $this->GALLERY_ADMIN = new gallery_class();
        if ($treeload == true)
            $this->load_tree();
    }

    /**
     * gal_class::cmd_showfoto()
     * 
     * @return
     */
    function cmd_showfoto() {
        global $GBL_LANGID;
        $this->langid = (int)$GBL_LANGID;
        $pid = (int)$_GET['pid'];
        $sql = "SELECT A.*,A.id AS PICID, C.id AS GID, C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content , GR.g_title,C.thumb_width,C.thumb_height,C.max_width,C.max_height
            FROM " . TBL_CMS_GALGROUP . " C,
            " . TBL_CMS_GALPICS . " A LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $this->langid . "),
            " . TBL_CMS_GALGROUP . " CC LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=CC.id AND GR.lang_id=" . $this->langid . ")  
            WHERE A.group_id=C.id AND CC.id=A.group_id AND CC.id=C.id AND C.approval=1 AND A.id=" . $pid . "
            GROUP BY A.id
            LIMIT 1";
        $IMAGE[] = $this->db->query_first($sql);
        $R = $this->set_picture_options($IMAGE, 'loaded_foto');
        $R = array_shift($R['pic_list']);
        $this->smarty->assign('loaded_foto', $R);
    }

    /**
     * gal_class::load_random_pics()
     * 
     * @return
     */
    function load_random_pics() {
        global $GBL_LANGID;
        $this->langid = (int)$GBL_LANGID;
        $sql = "SELECT A.*,A.id AS PICID, C.id AS GID, C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content , GR.g_title,C.thumb_width,C.thumb_height,C.max_width,C.max_height
            FROM " . TBL_CMS_GALGROUP . " C,
            " . TBL_CMS_GALPICS . " A LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $this->langid . "),
            " . TBL_CMS_GALGROUP . " CC LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=CC.id AND GR.lang_id=" . $this->langid . ")  
            WHERE A.group_id=C.id AND CC.id=A.group_id AND CC.id=C.id AND C.approval=1
            GROUP BY A.id
            ORDER BY RAND()
            LIMIT " . $this->gbl_config['gal_rndpics_limit'];
        $result = $this->db->query($sql);
        $gallery_random_images = sqlresult_to_array($result);
        $this->set_picture_options($gallery_random_images, 'gallery_random_images', $this->gbl_config['gal_rndpics_thwidth'], $this->gbl_config['gal_rndpics_thheight'],
            $this->gbl_config['galthumb_type_rnd']);
    }

    /**
     * gal_class::load_latest_galgroups()
     * 
     * @return
     */
    function load_latest_galgroups() {
        global $GBL_LANGID;
        $this->langid = (int)$GBL_LANGID;
        $result = $this->db->query("SELECT G.*,P.pic_name,GR.* FROM " . TBL_CMS_GALPICS . " P, " . TBL_CMS_GALGROUP . " G  
            LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=G.id AND GR.lang_id=" . $this->langid . ") 
            WHERE P.group_id=G.id AND G.approval=1 
            GROUP BY G.id 
            ORDER BY g_createdate DESC,P.post_time_int DESC 
            LIMIT " . $this->gblconfig->gal_latestgalcount);
        while ($row = $this->db->fetch_array_names($result)) {
            $row['g_title'] = ($row['g_title'] == "") ? $row['groupname'] : $row['g_title'];
            $row['link'] = $this->genGalleryLink($row['g_id'], $row['g_title'], $this->langid);
            if ($row['picid'] > 0) {
                $PIC = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . $row['picid']);
                $row['pic_name'] = $PIC['pic_name'];
            }
            $row['thumb'] = PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $row['pic_name'], $this->gblconfig->gal_newa_width, $this->gblconfig->
                gal_newa_height, './' . CACHE, true, 'crop', '', '', $row['g_croppos']);
            $latest_galgroups[] = $row;
        }
        $this->smarty->assign('latest_gal_groups', $latest_galgroups);
    }

    /**
     * gal_class::load_latest_pics()
     * 
     * @return
     */
    function load_latest_pics() {
        global $GBL_LANGID;
        $this->langid = (int)$GBL_LANGID;
        $sql = "SELECT A.*,A.id AS PICID, C.id AS GID, C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content , GR.g_title,C.thumb_width,C.thumb_height,C.max_width,C.max_height,C.g_croppos
            FROM " . TBL_CMS_GALGROUP . " C,
            " . TBL_CMS_GALPICS . " A LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $this->langid . "),
            " . TBL_CMS_GALGROUP . " CC LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=CC.id AND GR.lang_id=" . $this->langid . ")  
            WHERE A.group_id=C.id AND CC.id=A.group_id AND CC.id=C.id AND C.approval=1 AND A.approved=1
            GROUP BY A.id
            ORDER BY A.post_time_int DESC 
            LIMIT " . $this->gbl_config['gal_newpics_limit'];
        $result = $this->db->query($sql);
        $gallery_latest_images = sqlresult_to_array($result);
        $this->set_picture_options($gallery_latest_images, 'gallery_latest_images', $this->gbl_config['gal_newpics_thwidth'], $this->gbl_config['gal_newpics_thheight'],
            $this->gbl_config['galthumb_type'], '', '', $row['g_croppos']);
        $this->load_random_pics();
    }


    /**
     * gal_class::set_editor()
     * 
     * @param mixed $pic_id
     * @return
     */
    function set_editor($pic_id) {
        if ($this->user['kid'] == 0)
            return;
        $pic_id = (int)$pic_id;
        $PICTURE = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . $pic_id . " LIMIT 1");
        $PICTURE_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALCON . " WHERE lang_id=1 AND pic_id='" . $pic_id . "' LIMIT 1");
        $EDITOR = array(
            'FORM' => $PICTURE,
            'FORM_CON' => $PICTURE_CON,
            'img_src' => PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $PICTURE['pic_name'], $this->GALLERY_OBJ['thumb_width'], $this->GALLERY_OBJ['thumb_height'],
                './' . CACHE, true, 'resize'),
            'gid' => $this->GID,
            'pic_id' => $pic_id,
            );
        $this->smarty->assign('EDITOR', $EDITOR);
    }


    /**
     * gal_class::load_pic_table()
     * 
     * @param integer $start
     * @return
     */
    function load_pic_table($start = 0) {
        $gal_parent = array(); // laden Gallery ueber ID
        if ($this->GID > 0) {
            $result = $this->db->query("SELECT A.*, C.groupname,C.parent
  FROM " . TBL_CMS_GALGROUP . " C," . TBL_CMS_GALPICS . " A
  LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $this->langid . ")  
  WHERE C.id=" . $this->GID . " AND A.group_id=C.id AND C.approval=1 AND A.approved=1 ORDER BY A.pic_title");
            $this->genPaging($this->db->num_rows($result));
            $GAL = $this->db->query_first("SELECT *,G.id AS GID,GT.g_content AS GALDESC 
  FROM " . TBL_CMS_GALGROUP . " G LEFT JOIN " . TBL_CMS_TEMPLATES . " T ON (T.id=G.tpl),
  " . TBL_CMS_GALGROUP . " GG LEFT JOIN " . TBL_CMS_GLGRCON . " GT ON (GT.g_id=GG.id AND lang_id=" . $this->langid . ")  
  	WHERE G.id=" . $this->GID . "  AND G.approval=1 AND GG.id=G.id
  	GROUP BY G.id
  	LIMIT 1");
            $GAL['tpl_name'] .= '.tpl';
            $GAL['pic_count'] = (int)$this->db->num_rows($result);
            $GAL['gallery_name'] = ($GAL['g_title'] == "") ? $GAL['groupname'] : $GAL['g_title'];
            $this->smarty->assign('GAL_OBJ', $GAL);
            $this->GALLERY_OBJ = $GAL;
            $order = ($GAL['default_order'] != "") ? $GAL['default_order'] : 'post_time_int';
            $direc = ($GAL['default_direc'] != "") ? $GAL['default_direc'] : 'DESC';
            $result = $this->db->query("SELECT A.*,A.id AS PICID, C.id AS GID,C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content,GR.g_title,C.thumb_width,C.thumb_height,C.max_width,C.max_height,C.thumb_type,C.g_croppos
  FROM " . TBL_CMS_GALGROUP . " C," . TBL_CMS_GALPICS . " A
  LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $this->langid . ")
  LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=A.group_id AND GR.lang_id=" . $this->langid . ")  
  WHERE C.id=" . $this->GID . " AND A.group_id=C.id  AND C.approval=1  AND A.approved=1
  GROUP BY A.id
  ORDER BY A." . $order . " " . $direc . " 
  LIMIT " . intval($start) . "," . $this->gbl_config['pro_max_paging']);
            $arr = sqlresult_to_array($result);
            $gal_parent = $this->set_picture_options($arr, 'gallery');
            $this->smarty->assign('gallery_id', $this->GID);
            $this->smarty->assign('gallery_picount', count($gal_parent['pic_list']));
        }
        // SAME LEVEL GALLERIES
        unset($sm_values);
        $groups = $this->db->query("SELECT G.*,P.id AS PICID,count(P.id) AS PICCOUNT,GR.*,G.id AS GID FROM " . TBL_CMS_GALGROUP . " G 
   LEFT JOIN " . TBL_CMS_GALPICS . " P ON (G.id=P.group_id) 
   LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=P.group_id AND GR.lang_id=" . $this->langid . ") 
   WHERE G.parent=" . ((isset($gal_parent['parent'])) ? (int)$gal_parent['parent'] : "0") . "  AND G.approval=1
   GROUP BY G.id 
   ORDER BY G." . $this->gblconfig->gal_album_sort . " 
   " . $this->gblconfig->gal_album_sort_direc);
        while ($row = $this->db->fetch_array_names($groups)) {
            $pic_obj = $this->db->query_first("SELECT P.*, G.groupname, G.id AS GROUPID,G.picid 
      FROM " . TBL_CMS_GALPICS . " P, " . TBL_CMS_GALGROUP . " G 
      WHERE P.id=" . intval($row['picid']) . "  AND G.approval=1
      LIMIT 1");
            if ($pic_obj['pic_name'] == "") {
                $pic_obj = $this->db->query_first("SELECT P.*, G.groupname, G.id AS GROUPID,G.picid 
      FROM " . TBL_CMS_GALPICS . " P, " . TBL_CMS_GALGROUP . " G 
      WHERE P.group_id=" . $row['GID'] . "  AND G.approval=1
      ORDER BY RAND() LIMIT 1");
            }
            if ($pic_obj['pic_name'] == "") {
                $pic_obj = $this->db->query_first("SELECT P.*, G.groupname, G.id AS GROUPID,G.picid 
      FROM " . TBL_CMS_GALPICS . " P, " . TBL_CMS_GALGROUP . " G 
      WHERE P.group_id=G.id AND G.parent=" . (int)$row['GID'] . "  AND G.approval=1
      ORDER BY RAND() LIMIT 1");
            }
            $row['g_title'] = ($row['g_title'] == "") ? $row['groupname'] : $row['g_title'];
            $sm_values[] = array(
                'id' => $row['GID'],
                'subgal_title' => $row['g_title'],
                'subgal_description' => ((isset($row['g_content'])) ? $row['g_content'] : ""),
                'subgal_fotocount' => $row['PICCOUNT'],
                'subgal_link' => $this->genGalleryLink($row["GID"], $row["g_title"], $this->langid),
                'subgal_img' => ($pic_obj['pic_name'] != "") ? '<img alt="' . htmlspecialchars($row["g_title"]) . '" src="' . PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb
                    (PICS_GAL_ROOT . $pic_obj['pic_name'], $this->gbl_config['cat_thumb_width'], $this->gbl_config['cat_thumb_height'], './' . CACHE, true, $this->gbl_config['gal_cat_thumb_type'],
                    '', '', $row['g_croppos']) . '" >' : '',
                'subgal_imgsrc' => ($pic_obj['pic_name'] != "") ? PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $pic_obj['pic_name'], $this->gbl_config['cat_thumb_width'],
                    $this->gbl_config['cat_thumb_height'], './' . CACHE, true, $this->gbl_config['gal_cat_thumb_type'], '', '', $row['g_croppos']) : '',
                'subgal_img_width' => $this->gbl_config['cat_thumb_width'],
                'subgal_img_height' => $this->gbl_config['cat_thumb_height'],
                );
        }
        # die('Test Mode please wait');
        $this->smarty->assign('galleries', $sm_values); // SUB GROUPS
        unset($sm_values);
        $sm_values = "";
        if ($this->GID > 0) {
            $subgroups = $this->db->query("SELECT G.*,count(P.id) AS PICCOUNT,GR.*,G.id AS GID FROM " . TBL_CMS_GALGROUP . " G 
	  LEFT JOIN " . TBL_CMS_GALPICS . " P ON (G.id=P.group_id) 	  
	  LEFT JOIN " . TBL_CMS_GLGRCON . " GR ON (GR.g_id=P.group_id AND GR.lang_id=" . $this->langid . ") 	  
	  WHERE G.parent='" . $this->GID . "' GROUP BY G.id ORDER BY G." . $this->gblconfig->gal_album_sort . " 
   " . $this->gblconfig->gal_album_sort_direc);
            while ($row = $this->db->fetch_array_names($subgroups)) {
                $pic_obj = $this->db->query_first("SELECT P.*, G.groupname, G.id AS GROUPID,G.picid  
	    FROM " . TBL_CMS_GALPICS . " P, " . TBL_CMS_GALGROUP . " G 
	    WHERE P.id=" . $row['picid'] . " 
	    LIMIT 1");
                if ($pic_obj['pic_name'] == "") {
                    $pic_obj = $this->db->query_first("SELECT P.*, G.groupname, G.id AS GROUPID,G.picid 
      FROM " . TBL_CMS_GALPICS . " P, " . TBL_CMS_GALGROUP . " G 
      WHERE P.group_id=" . $row['GID'] . " 
      ORDER BY RAND() LIMIT 1");
                }

                $row['g_title'] = ($row['g_title'] == "") ? $row['groupname'] : $row['g_title'];
                $sm_values[] = array(
                    'id' => $row['GID'],
                    'subgal_title' => $row['g_title'],
                    'subgal_description' => $row['g_content'],
                    'subgal_fotocount' => $row['PICCOUNT'],
                    'subgal_link' => $this->genGalleryLink($row["GID"], $row["g_title"], $this->langid),
                    'subgal_img' => ($pic_obj['pic_name'] != "") ? '<img alt="' . htmlspecialchars($row["g_title"]) . '" src="' . PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb
                        (PICS_GAL_ROOT . $pic_obj['pic_name'], $this->gbl_config['cat_thumb_width'], $this->gbl_config['cat_thumb_height'], './' . CACHE, true, $this->gbl_config['gal_cat_thumb_type'],
                        '', '', $row['g_croppos']) . '" >' : '',
                    'subgal_imgsrc' => PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $pic_obj['pic_name'], $this->gbl_config['cat_thumb_width'], $this->
                        gbl_config['cat_thumb_height'], './' . CACHE, true, $this->gbl_config['gal_cat_thumb_type'], '', '', $row['g_croppos']));
            }
        }

        $this->smarty->assign('subgal', $sm_values);
    }

    /**
     * gal_class::add_users()
     * 
     * @param mixed $pic_arr
     * @return
     */
    function add_users(&$pic_arr) {
        if (is_array($pic_arr)) {
            foreach ($pic_arr as $picid => $row) {
                $sql .= (($sql != "") ? " OR " : "") . " kid=" . $row['pic_kid'];
            }
            if ($sql != "") {
                $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . "  WHERE " . $sql . "");
                while ($row = $this->db->fetch_array_names($result)) {
                    $customers[$row['kid']] = $row;
                }
                $U = new member_class();
                foreach ($pic_arr as $picid => $row) {
                    $pic_arr[$picid]['customer'] = $U->setOptions($customers[$row['pic_kid']], false);
                }
                unset($U);
            }
        }

    }

    /**
     * gal_class::parse_randompic()
     * 
     * @param mixed $params
     * @return
     */
    function parse_randompic($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_GALPICRAND_')) {
            preg_match_all("={TMPL_GALPICRAND_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $gid) {
                $result = $this->db->query("SELECT A.*, A.id AS PICID, C.id AS GID, C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content,T.tpl_name,C.thumb_width,C.thumb_height,C.max_width,C.max_height
		FROM " . TBL_CMS_GALGROUP . " C," . TBL_CMS_TEMPLATES . " T," . TBL_CMS_GALPICS . " A
		LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $langid . ")
		WHERE A.approved=1 AND C.g_enabled=1 AND T.id=610 AND A.group_id=C.id GROUP BY A.id ORDER BY RAND() LIMIT 1");
                $this->init_obj($langid, $user_object, $gid, false);
                $PICLIST = $this->set_picture_options(sqlresult_to_array($result), 'TMPL_GALPICRAND_' . $gid);
                $FOTO = array_shift($PICLIST['pic_list']);
                $this->smarty->assign('TMPL_GALPICRAND_' . $gid, $FOTO);
                if ($this->db->num_rows($result) > 0)
                    $this->db->data_seek($result, 0);
                while ($row = $this->db->fetch_array_names($result)) {
                    $GAL_OBJ = $row;
                    break;
                }
                $html = str_replace($tpl_tag[0][$key], '<% assign var=galleryrandpic value=$TMPL_GALPICRAND_' . $gid . ' %><% include file="' . $GAL_OBJ['tpl_name'] .
                    '.tpl" %>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * gal_class::parse_gallery_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_gallery_inlay($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_GALINLAY_')) {
            preg_match_all("={TMPL_GALINLAY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $GAL_OBJ = $this->load_gallery_for_inlay($PLUGIN_OPT['galleryid'], $langid, $cont_matrix_id, $PLUGIN_OPT);
                $PLUGIN_OPT['tpl_name'] = ($PLUGIN_OPT['tpl_name'] == "") ? $GAL_OBJ['tpl_name'] : $PLUGIN_OPT['tpl_name'];
                if ($PLUGIN_OPT['tpl_name'] != "") {
                    $this->smarty->assign('TMPL_GALGROUP_' . $cont_matrix_id, $GAL_OBJ);
                    $html = str_replace($tpl_tag[0][$key], '<% assign var=galgroup value=$TMPL_GALGROUP_' . $cont_matrix_id . ' %><% assign var=gallery value=$TMPL_GALINLAY_' . $cont_matrix_id .
                        ' %><% include file="' . $PLUGIN_OPT['tpl_name'] . '.tpl" %>', $html);
                }
                else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
                $this->smarty->assign('GAL_OBJ', $GAL_OBJ);
            }
        }
        $params['html'] = $html;
        return $params;
    }


    /**
     * gal_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='gallery' AND layout_group=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * gal_class::load_plugin_gal_list()
     * 
     * @param mixed $params
     * @return
     */
    function load_plugin_gal_list($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALGROUP . " ORDER BY groupname");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * gal_class::load_gallery_for_inlay()
     * 
     * @param mixed $gid
     * @param mixed $langid
     * @param mixed $cont_matrix_id
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_gallery_for_inlay($gid, $langid, $cont_matrix_id, $PLUGIN_OPT) {
        global $user_object;
        $PLUGIN_OPT['default_order'] = ($PLUGIN_OPT['default_order'] == "") ? 'morder' : $PLUGIN_OPT['default_order'];
        $PLUGIN_OPT['default_direc'] = ($PLUGIN_OPT['default_direc'] == "") ? 'ASC' : $PLUGIN_OPT['default_direc'];
        $result = $this->db->query("SELECT A.*, A.id AS PICID, C.id AS GID, C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content,T.tpl_name,C.thumb_width,C.thumb_height,C.max_width,C.max_height,C.id AS GID
		FROM " . TBL_CMS_GALGROUP . " C," . TBL_CMS_TEMPLATES . " T," . TBL_CMS_GALPICS . " A
		LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . (int)$langid . ")
		WHERE A.approved=1 AND C.g_enabled=1 AND T.id=C.tpl AND C.id=" . (int)$gid . " AND A.group_id=C.id
		GROUP BY A.id
		ORDER BY A." . $PLUGIN_OPT['default_order'] . " " . $PLUGIN_OPT['default_direc'] . " LIMIT " . (int)$PLUGIN_OPT['image_count']);
        $this->init_obj($langid, $user_object, $gid, false);
        $this->GID = (int)$gid;
        $GAL_OBJ = $this->db->query_first("SELECT *,G.id AS GID FROM " . TBL_CMS_TEMPLATES . " T," . TBL_CMS_GALGROUP . " G 
            LEFT JOIN " . TBL_CMS_GLGRCON . " GC ON (G.id=GC.g_id AND GC.lang_id=" . (int)$langid . ")
            WHERE G.id=" . $this->GID . " AND T.id=G.tpl LIMIT 1");
        if ($this->db->num_rows($result) > 0)
            $this->db->data_seek($result, 0);
        $pic_list = sqlresult_to_array($result);
        $this->set_picture_options($pic_list, 'TMPL_GALINLAY_' . $cont_matrix_id, $PLUGIN_OPT['image_width'], $PLUGIN_OPT['image_height'], $PLUGIN_OPT['thumb_type'], $PLUGIN_OPT['g_croppos']);
        $GAL_OBJ['gallery_name'] = ($GAL_OBJ['g_title'] == "") ? $GAL_OBJ['groupname'] : $GAL_OBJ['g_title'];
        $random_img = $pic_list[rand(0, count($pic_list) - 1)];
        $GAL_OBJ['picid'] = ($GAL_OBJ['picid'] == 0) ? $random_img['PICID'] : $GAL_OBJ['picid'];
        $GAL_OBJ['galimg'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . (int)$GAL_OBJ['picid']);
        $height = ($PLUGIN_OPT['gal_image_height'] > 0) ? $PLUGIN_OPT['gal_image_height'] : $this->gbl_config['cat_thumb_height'];
        $width = ($PLUGIN_OPT['gal_image_width'] > 0) ? $PLUGIN_OPT['gal_image_width'] : $this->gbl_config['cat_thumb_width'];
        $PLUGIN_OPT['galthumb_type'] = ($PLUGIN_OPT['galthumb_type'] == "") ? 'resize' : $PLUGIN_OPT['galthumb_type'];
        $PLUGIN_OPT['gal_g_croppos'] = ($PLUGIN_OPT['gal_g_croppos'] == "") ? 'Center' : $PLUGIN_OPT['gal_g_croppos'];
        $GAL_OBJ['galimg']['thumb'] = ($GAL_OBJ['galimg']['pic_name'] != "") ? PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT . $GAL_OBJ['galimg']['pic_name'],
            $width, $height, './' . CACHE, true, $PLUGIN_OPT['galthumb_type'], '', '', $PLUGIN_OPT['gal_g_croppos']) : '';
        $this->GALLERY_OBJ = $GAL_OBJ;
        return $GAL_OBJ;
    }

    /**
     * gal_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $gallery_id = $params['FORM']['galleryid'];
        $GAL_OBJ = $this->load_gallery_for_inlay($gallery_id, 1, $cont_matrix_id, array());
        $upt = array('tm_content' => '{TMPL_GALINLAY_' . $cont_matrix_id . '}', 'tm_pluginfo' => $GAL_OBJ['groupname']);
        $upt = $this->real_escape($upt);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $upt);
    }

    /**
     * gal_class::genGalleryLink()
     * 
     * @param mixed $id
     * @param mixed $linkname
     * @param integer $lid
     * @return
     */
    function genGalleryLink($id, $linkname, $lid = 1) {
        $prefix_lng = ($_SESSION['GBL_LANGID'] == $this->gbl_config['std_lang_id']) ? '' : $_SESSION['GBL_LOCAL_ID'] . '/';
        return SSL_PATH_SYSTEM . PATH_CMS . $prefix_lng . $this->gblconfig->gal_path . '/' . $this->format_file_name($linkname) . '.html';
    }


    /**
     * gal_class::psitemap_build_tree()
     * 
     * @param mixed $tree
     * @return
     */
    function psitemap_build_tree(&$tree) {
        foreach ($tree as $key => $album) {
            $tree[$key]['linkname'] = $tree[$key]['catlabel'] = ($album['g_title'] != "") ? $album['g_title'] : $album['groupname'];
            #     $cat['catlink'] = $this->genGalleryLink($cat['id'], $cat['catlabel'], $_SESSION['GBL_LANGID']);
            $tree[$key]['description'] = $album['groupname'];
            $tree[$key]['catlink'] = $this->genGalleryLink($album['id'], $tree[$key]['catlabel'], $_SESSION['GBL_LANGID']);
            if (is_array($album['children']) && count($album['children']) > 0) {
                $this->psitemap_build_tree($album['children']);
            }
        }
    }

    /**
     * gal_class::psitemap_add_tree()
     * 
     * @param mixed $tree
     * @return
     */
    function psitemap_add_tree(&$tree) {
        foreach ($tree as $key => $item) {
            if ($item['modident'] == 'gallery') {
                $tree[$key]['children'] = array_merge((array )$tree[$key]['children'], $this->nodes_gal->menu_array);
                break;
            }
            if (is_array($item['children']) && count($item['children']) > 0) {
                $this->psitemap_add_tree($item['children']);
            }
        }
    }

    /**
     * gal_class::psitemap()
     * 
     * @param mixed $params
     * @return
     */
    function psitemap($params) {
        $this->load_tree();
        $this->psitemap_build_tree($this->nodes_gal->menu_array);
        $this->psitemap_add_tree($params['menu_arr']);
        return $params;
    }

    /**
     * gal_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='gallery' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $params = array_merge($params, $SM);
            $sql_filter = array('approval' => 1);
            if ((int)$params['langid'] > 0) {
                $sql_filter['id'] = (int)$params['langid'];
            }
            $lang_arr = dao_class::get_data(TBL_CMS_LANG, $sql_filter);
            foreach ($lang_arr as $rowl) {
                // Gallery Groups
                $result = $this->db->query("SELECT id, parent, groupname, picid FROM " . TBL_CMS_GALGROUP . " WHERE approval=1 ORDER BY parent,groupname");
                while ($row = $this->db->fetch_array_names($result)) {
                    $galgroup_image = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " A 
                    LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . (int)$rowl['id'] . ") 
                    WHERE A.id=" . (int)$row['picid']);
                    $url = array('images' => array());
                    $url['url'] = self::get_http_protocol() . '://www.' . FM_DOMAIN . $this->genGalleryLink($row["id"], $row["groupname"], $rowl['id']);
                    $url['frecvent'] = $params['sm_changefreq'];
                    $url['priority'] = $params['sm_priority'];
                    if ((int)$row['picid'] > 0) {
                        $url['images'][] = array(
                            'loc' => self::get_http_protocol() . '://www.' . FM_DOMAIN . PATH_CMS . 'images/gallery/' . $galgroup_image['pic_name'],
                            'title' => ($galgroup_image['pic_title'] == "") ? $galgroup_image['pic_name'] : $galgroup_image['pic_title'],
                            );
                    }
                    $params['urls'][] = $url;
                }
                // Gallery Pics
                /*  $result = $this->db->query("SELECT DISTINCT A.*, C.groupname FROM " . TBL_CMS_GALPICS . " A, " . TBL_CMS_GALGROUP .
                " C WHERE A.group_id=C.id ORDER BY A.pic_title");
                while ($row = $this->db->fetch_array_names($result)) {
                $url['url'] = self::get_http_protocol() . '://www.' . FM_DOMAIN . $this->gen_image_detail_link($row['id'], $row['groupname'] . $row['pic_title'], $rowl['id']);
                $url['frecvent'] = $params['sm_changefreq'];
                $url['priority'] = $params['sm_priority'];
                $params['urls'][] = $url;
                }*/
            }
        }
        return (array )$params;
    }

    /**
     * gal_class::genGalleryURL()
     * 
     * @param mixed $template_id
     * @return
     */
    function genGalleryURL($template_id) {
        return '{GALLERY_TPL_' . $template_id . '}';
    }

    /**
     * gal_class::parse_urls()
     * 
     * @param mixed $params
     * @return
     */
    function parse_urls($params) {
        $langid = $params['langid'];
        if (strstr($params['html'], '{GALLERY_TPL_')) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALGROUP);
            while ($row = $this->db->fetch_array_names($result)) {
                $params['html'] = fill_temp($this->genGalleryURL($row['id']), $this->genGalleryLink($row['id'], $row["groupname"], $langid), $params['html']);
            }
        }
        return $params;
    }

    /**
     * gal_class::gen_image_detail_link()
     * 
     * @param mixed $id
     * @param mixed $linkname
     * @param integer $lid
     * @return
     */
    function gen_image_detail_link($id, $linkname, $lid = 1) {
        $prefix_lng = ($_SESSION['GBL_LANGID'] == $this->gbl_config['std_lang_id']) ? '' : $_SESSION['GBL_LOCAL_ID'] . '/';
        return SSL_PATH_SYSTEM . PATH_CMS . $prefix_lng . $this->gblconfig->gal_path_image . '/' . $this->format_file_name($linkname) . '.html';
    }

    /**
     * gal_class::load_gallery_for_tpl_inlay()
     * 
     * @param mixed $gid
     * @param mixed $langid
     * @return
     */
    function load_gallery_for_tpl_inlay($gid, $langid) {
        global $user_object;
        $result = $this->db->query("SELECT A.*, A.id AS PICID, C.id AS GID, C.groupname,C.parent,GC.pic_title AS PICTITLE,GC.pic_content,T.tpl_name,C.thumb_width,C.thumb_height,C.max_width,C.max_height,C.id AS GID
		FROM " . TBL_CMS_GALGROUP . " C," . TBL_CMS_TEMPLATES . " T," . TBL_CMS_GALPICS . " A
		LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . (int)$langid . ")
		WHERE A.approved=1 AND C.g_enabled=1 AND T.id=C.tpl AND C.id=" . (int)$gid . " AND A.group_id=C.id
		GROUP BY A.id
		ORDER BY A.morder");
        $this->init_obj($langid, $user_object, $gid, false);
        $this->GALLERY_OBJ = $this->db->query_first("SELECT *,G.id AS GID FROM " . TBL_CMS_GALGROUP . " G WHERE G.id=" . $gid . "	LIMIT 1");
        $this->GID = (int)$gid;
        if ($this->db->num_rows($result) > 0)
            $this->db->data_seek($result, 0);
        $this->set_picture_options(sqlresult_to_array($result), 'TMPL_GALLERY_' . intval($gid));
        if ($this->db->num_rows($result) > 0)
            $this->db->data_seek($result, 0);
        while ($row = $this->db->fetch_array_names($result)) {
            $GAL_OBJ = $row;
            break;
        }
        $GAL_OBJ['gallery_name'] = ($GAL_OBJ['g_title'] == "") ? $GAL_OBJ['groupname'] : $GAL_OBJ['g_title'];
        return $GAL_OBJ;
    }

    /**
     * gal_class::parse_gallery_tpl_inlay()
     * 
     * @param mixed $params
     * @return
     */
    function parse_gallery_tpl_inlay($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_GALLERY_')) {
            preg_match_all("={TMPL_GALLERY_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $gid) {
                $GAL_OBJ = $this->load_gallery_for_tpl_inlay($gid, $langid);
                if ($GAL_OBJ['tpl_name'] != "") {
                    $html = str_replace($tpl_tag[0][$key], '<% assign var=galgroup value=$TMPL_GALOBJ_' . $cont_matrix_id . ' %><% include file="' . $GAL_OBJ['tpl_name'] .
                        '.tpl" %>', $html);
                }
                else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
            }
        }
        $params['html'] = $html;
        return $params;
    }


}

?>