<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


DEFINE('TREE_LOADED', 1);

class cms_tree_class extends keimeno_class {
    var $filter = 0;
    var $html_menue = "";
    var $menu_array = array();
    var $data;
    var $sign = ' -|_ ';
    var $bread_html = "";
    var $bread_break_sym = "&#187;";
    var $menu_sublevel_sym = "&#187;";
    var $label_column = 'description';
    var $label_column2 = 'name';
    var $label_id = 'id';
    var $label_parent = 'parent';
    var $admin_edit_aktion = "edit";
    var $not_visible_ids = array();
    var $allowed_treeids = array();
    var $GET_GAL_ID = 'gid';
    var $menu_links = array();
    var $active_node = NULL;
    var $active_node_parent = array();
    var $counter_field = "";
    var $treeTemplate = "";
    var $treeTag = "";
    var $used_ids = array();
    var $menu_flat_array = array();

    /**
     * cms_tree_class::cms_tree_class()
     * 
     * @return
     */
    function __construct() {
        $this->active_node_parent = array();
        $this->active_node_parent['children_ids'] = array();
    }

    /**
     * cms_tree_class::load_data_by_sql()
     * 
     * @param mixed $sql
     * @return array
     */
    function load_data_by_sql($sql) {
        $result = $this->db->query($sql);
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[$row[$this->label_id]] = $row;
        }

        return $data;
    }

    /**
     * cms_tree_class::filter_array()
     * 
     * @param mixed $row
     * @return
     */
    function filter_array($row) {
        return $row[$this->label_parent] == $this->filter;
    }

    /**
     * cms_tree_class::create_nested_array_for_tree()
     * 
     * @param mixed $data
     * @param mixed $arr
     * @param mixed $parent
     * @param mixed $startDepth
     * @param mixed $maxDepth
     * @return
     */
    function create_nested_array_for_tree(&$data, &$arr, $parent, $startDepth, $maxDepth) {
        if (!is_array($data))
            return;
        if ($maxDepth-- == 0)
            return;
        $index = 0;
        $startDepth++;

        $this->filter = $parent;
        $children = array_filter($data, array($this, "filter_array"));
        foreach ($children as $child) {
            #	if ( (in_array($child[$this->label_id],$this->used_ids)===TRUE && $startDepth==1) || ($startDepth > 1)) {
            if (in_array($child[$this->label_id], $this->used_ids) === TRUE) {
                $arr[$index] = $child;
                $arr[$index]['depth'] = $startDepth;
                $arr[$index] = $this->set_tree_item_option($arr[$index]);
                //you need to replace $child[$this->label_id] by your name of column, which is holding the id of current entry!
                $this->create_nested_array_for_tree($data, $arr[$index]['children'], $child[$this->label_id], $startDepth, $maxDepth);
                $arr[$index]['children'] = (array)$arr[$index]['children'];
                $arr[$index]['cathaschildren'] = count($arr[$index]['children']) > 0;
                $index++;
            }
        }
    }

    /**
     * cms_tree_class::create_nested_array_to_smarty()
     * 
     * @param mixed $data
     * @param mixed $arr
     * @param mixed $parent
     * @param mixed $startDepth
     * @param mixed $maxDepth
     * @param mixed $allowed_page_ids
     * @return
     */
    function create_nested_array_to_smarty(&$data, &$arr, $parent, $startDepth, $maxDepth, $allowed_page_ids) {
        if (!is_array($data))
            return;
        if ($maxDepth-- == 0)
            return;
        $index = 0;
        $startDepth++;

        $this->filter = $parent;
        $children = array_filter($data, array($this, "filter_array"));
        foreach ($children as $child) {
            if (($startDepth == 1 && in_array($child[$this->label_id], $allowed_page_ids)) || ($startDepth > 1)) {
                $arr[$index] = $child;
                $arr[$index]['depth'] = $startDepth;
                $arr[$index] = $this->set_tree_item_option($arr[$index]);
                $this->create_nested_array_to_smarty($data, $arr[$index]['children'], $child[$this->label_id], $startDepth, $maxDepth, $allowed_page_ids);
                $arr[$index]['children'] = (array)$arr[$index]['children'];
                $arr[$index]['cathaschildren'] = count($arr[$index]['children']) > 0;
                // Ermittlung IDs der Kinder
                $arr[$index]['children_ids'] = array();
                if (is_array($arr[$index]['children']) && count($arr[$index]['children']) > 0) {
                    foreach ($arr[$index]['children'] as $key => $ch) {
                        $arr[$index]['children_ids'][] = $ch[$this->label_id];
                    }
                }

                if (isset($_GET['page']) && $_GET['page'] == $child[$this->label_id]) {
                    $this->active_node = $arr[$index];
                }
                $index++;
            }
        }
    }

    /**
     * cms_tree_class::get_used_ids()
     * 
     * @param mixed $menue_arr
     * @return
     */
    function get_used_ids($menue_arr) {
        global $CORE;
        if (count($menue_arr) > 0) {
            foreach ($menue_arr as $key => $child) {
                $childs = array();
                if (array_key_exists('tid_childs', $child))
                    $childs = unserialize($child['tid_childs']);
                if ($child['parent'] == 0) {
                    $this->used_ids[$child[$this->label_id]] = $child[$this->label_id];
                }
                if (is_object($CORE) && is_array($child['children']) && ($CORE->PAGE == $child[$this->label_id] || in_array($CORE->PAGE, (array )$childs))) {
                    $this->used_ids[$child[$this->label_id]] = $child[$this->label_id];
                    foreach ($child['children'] as $level_child)
                        $this->used_ids[$level_child[$this->label_id]] = $level_child[$this->label_id];
                    if (in_array($CORE->PAGE, (array )$childs)) {
                        $this->get_used_ids($child['children']);
                    }

                }

            }
        }
    }


    /**
     * cms_tree_class::set_tree_item_option()
     * 
     * @param mixed $child
     * @return
     */
    function set_tree_item_option($child) {
        global $gb_config;
        $child[$this->label_column] = ($child[$this->label_column] == "") ? $child[$this->label_column2] : $child[$this->label_column];
        if (!isset($child['linkname']))
            $child['linkname'] = "";
        if (!isset($child['t_htalinklabel']))
            $child['t_htalinklabel'] = "";
        if (!isset($child['t_icon']))
            $child['t_icon'] = "";
        if (!isset($child['url_redirect']))
            $child['url_redirect'] = "";
        $child['catlabel'] = $child['linkname'];
        if ($child['url_redirect'] == "") {
            $url_label = ($child['t_htalinklabel'] != "") ? $child['t_htalinklabel'] : $child['linkname'];
            $tid = ($child['t_htalinklabel'] != "") ? 0 : $child['id'];
            /* if ($child['id'] == START_PAGE) {
            if (be_in_ssl_area == true) {
            $child['catlink'] = "http://www." . FM_DOMAIN . PATH_CMS . "index.html";
            }
            else
            $child['catlink'] = PATH_CMS . 'index.html';
            }
            else {*/
            $child['catlink'] = gen_page_link($tid, $url_label, $_SESSION['GBL_LANGID']);
            #}
            $child['cattarget'] = '_self';
        }
        else {
            $child['cattarget'] = $child['url_redirect_target'];
            $child['catlink'] = $child['url_redirect'];
        }
        $child['catlevel'] = (isset($child['depth'])) ? $child['depth'] : 0;
        $child['modident'] = ((isset($child['modident'])) ? $child['modident'] : "");
        $child['t_icon_img'] = ($child['t_icon'] != "") ? gen_thumb_image(PATH_CMS . "file_data/menu/" . $child['t_icon'], 100, 100, 'crop') : "";
        return $child;
    }

    /**
     * cms_tree_class::CreateNestedArray()
     * 
     * @param mixed $data
     * @param mixed $arr
     * @param mixed $parent
     * @param mixed $startDepth
     * @param mixed $maxDepth
     * @param mixed $flatarr
     * @return
     */
    function CreateNestedArray(&$data, &$arr, $parent, $startDepth, $maxDepth, &$flatarr = array()) {
        if (!is_array($data))
            return;
        if ($maxDepth-- == 0)
            return;
        $index = 0;
        $startDepth++;

        $this->filter = $parent;
        $children = array_filter($data, array($this, "filter_array"));
        foreach ($children as $child) {
            $flatarr[] = $child;
            $arr[$index] = $child;
            $arr[$index]['depth'] = $startDepth;
            $this->CreateNestedArray($data, $arr[$index]['children'], $child[$this->label_id], $startDepth, $maxDepth, $flatarr);
            $index++;
        }
    }

    /**
     * cms_tree_class::create_result_and_array()
     * 
     * @param mixed $sql
     * @param mixed $parent
     * @param mixed $startDepth
     * @param mixed $maxDepth
     * @return array
     */
    function create_result_and_array($sql, $parent, $startDepth, $maxDepth) {
        $this->data = $this->load_data_by_sql($sql);
        $arr = array();
        $this->CreateNestedArray($this->data, $arr, $parent, $startDepth, $maxDepth, $this->menu_flat_array);
        $this->menu_array = $arr;
        return $arr;
    }

    /**
     * cms_tree_class::get_item()
     * 
     * @param mixed $id
     * @param mixed $menue_arr
     * @param mixed $found_child
     * @return
     */
    function get_item($id, $menue_arr, &$found_child = array()) {
        foreach ($menue_arr as $key => $child) {
            if ($id == $child[$this->label_id]) {
                $found_child = $child;
            }
            if (is_array($child['children'])) {
                $this->get_item($id, $child['children'], $found_child);
            }
        }
    }

    /**
     * cms_tree_class::build_core_cms_tree()
     * 
     * @param mixed $dataset
     * @param string $allowed_page_ids
     * @param mixed $exclude
     * @param integer $maxDepth
     * @return
     */
    function build_core_cms_tree($dataset, $allowed_page_ids = "", $exclude = array(), $maxDepth = -1) {
        $arr_org = array();
        $this->data = $dataset;
        $this->parent_flat_arr = array();
        $allowed_page_ids = (array )$allowed_page_ids;
        foreach ((array )$this->data as $key => $row) {
            $this->parent_flat_arr[$row['parent']][] = $this->set_tree_item_option($row);
        }
        $this->create_nested_array_to_smarty($this->data, $arr_org, 0, 0, -1, $allowed_page_ids);
        $this->get_item($this->active_node['parent'], $arr_org, $this->active_node_parent);
        if (!is_array($this->active_node_parent['children_ids']))
            $this->active_node_parent['children_ids'] = array();
        $this->menu_array = $arr_org;
        # Clean Menu Array
        $this->get_used_ids($this->menu_array);
        $this->cleanMenuArr = array();
        $this->create_nested_array_for_tree($this->data, $this->cleanMenuArr, 0, 0, -1);
    }

    /**
     * cms_tree_class::breadcrumb_trails_shop()
     * 
     * @param mixed $menu_arr
     * @return
     */
    function breadcrumb_trails_shop($menu_arr) {
        $_GET['cid'] = ($_POST['cid'] > 0 && $_GET['cid'] == 0) ? $_POST['cid'] : $_GET['cid'];
        if (count($menu_arr) > 0) {
            foreach ($menu_arr as $key => $child) {
                $childs = Array();
                if (is_array($child['children']))
                    $childs = explode(";", $this->buildFlatIdArray($child['children'], $childs));
                if (in_array($_GET['cid'], $childs) || ($_GET['cid'] == $child[$this->label_id] && $child[$this->label_parent] > 0) || ($_GET['cid'] == $child[$this->label_id] &&
                    is_array($child['children']))) {
                    #   if (strlen(pure_translation($child[$this->label_column], $_SESSION['GBL_LANGID'])) > 0) {
                    $html .= '<li>' . $this->bread_break_sym . ' <a ' . (($_GET['cid'] == $child[$this->label_id]) ? ' class="bread_active" ' : "") . 'href="' . genCatLink($child[$this->
                        label_column], $child[$this->label_id]) . '">' . $child[$this->label_column] . '</a>';
                    if (is_array($child['children']) && ($_GET['cid'] == $child[$this->label_id] || in_array($_GET['cid'], $childs)))
                        $html .= '<ul>' . $this->breadcrumb_trails_shop($child['children']) . "</ul>";
                    $html .= "</li>";
                    #   }
                }
            }
        }
        return $html;
    }

    /**
     * cms_tree_class::outputtree_tree()
     * 
     * @param mixed $menue_arr
     * @return
     */
    function outputtree_tree($menue_arr) {
        foreach ($menue_arr as $key => $child) {
            $html .= "<li>" . $child[$this->label_column];
            if (is_array($child['children']))
                $html .= '<ul>' . $this->outputtree_tree($child['children']) . "</ul>";
            $html .= "</li>";
        }
        return $html;
    }


    /**
     * cms_tree_class::outputtree_tree_gal_admin_nav()
     * 
     * @param mixed $menue_arr
     * @param mixed $node_id
     * @param bool $printoutall
     * @param string $toadd
     * @return
     */
    function outputtree_tree_gal_admin_nav($menue_arr, $node_id, $printoutall = false, $toadd = "") {
        foreach ($menue_arr as $key => $child) {
            $this->menu_array[$key]['printout'] = false;
            if (count($this->allowed_treeids) > 0 && $child['parent'] == 0 && in_array($child[$this->label_id], $this->allowed_treeids))
                $this->menu_array[$key]['printout'] = true;
            if (count($this->allowed_treeids) > 0 && $child['parent'] > 0)
                $this->menu_array[$key]['printout'] = true;
            if ($printoutall === TRUE)
                $this->menu_array[$key]['printout'] = true;
            if ($this->menu_array[$key]['printout'] == true) {
                $a_class = (($child['approval'] == 1) ? 'class="li_green_link"' : 'class="li_red_link"');
                if (($child['approval'] == 1) && $node_id == $child['id'])
                    $a_class = 'class="li_green_link_active"';
                if (($child['approval'] != 1) && $node_id == $child['id'])
                    $a_class = 'class="li_red_link_active"';
                $html .= '<li ' . ((is_array($child['children'])) ? 'class="liOpen"' : '') . '><a onclick="showPageLoadInfo();" ' . $a_class . ' href="' . $_SERVER['PHP_SELF'] .
                    '?gid=' . $child[$this->label_id] . '&epage=' . $_GET['epage'] . $toadd . '">' . (($_GET['starttree'] == $child[$this->label_id]) ? "<b><i>" : "") . $child[$this->
                    label_column] . (($_GET['starttree'] == $child[$this->label_id]) ? "</b></i>" : "") . '</a>';
                if (is_array($child['children']))
                    $html .= '<ul>' . $this->outputtree_tree_gal_admin_nav($child['children'], $node_id, $printoutall, $toadd) . "</ul>";
                $html .= "</li>";
            }
        }
        return $html;
    }
    /**
     * cms_tree_class::outputtree_tree_sitemap()
     * 
     * @param mixed $menue_arr
     * @return
     */
    function outputtree_tree_sitemap($menue_arr) {
        foreach ($menue_arr as $key => $child) {
            $html .= '<li><a href="' . genCatLink($child[$this->label_column], $child[$this->label_id]) . '">' . $child[$this->label_column] . '</a>';
            if (is_array($child['children']))
                $html .= '<ul>' . $this->outputtree_tree_sitemap($child['children']) . "</ul>";
            $html .= "</li>";
        }
        return $html;
    }


    /**
     * cms_tree_class::buildFlatIdArray()
     * 
     * @param mixed $child_arr
     * @return
     */
    function buildFlatIdArray($child_arr) {
        if (count($child_arr) > 0) {
            foreach ($child_arr as $key => $child) {
                $child_ids .= $child[$this->label_id] . ';';
                if (is_array($child['children']))
                    $child_ids .= $this->buildFlatIdArray($child['children']);
            }
        }
        return $child_ids;
    }

    /**
     * cms_tree_class::buildFlatIdArrayOneLevel()
     * 
     * @param mixed $child_arr
     * @return
     */
    function buildFlatIdArrayOneLevel($child_arr) {
        if (count($child_arr) > 0) {
            foreach ($child_arr as $key => $child) {
                $child_ids .= $child[$this->label_id] . ';';
            }
        }
        return explode(";", $child_ids);
    }


    /**
     * cms_tree_class::outputtree_nav_gal()
     * 
     * @param mixed $menue_arr
     * @return string
     */
    function outputtree_nav_gal($menue_arr) {
        if (count($menue_arr) > 0) {
            foreach ($menue_arr as $key => $child) {
                if (strlen($child[$this->label_column]) > 0) {
                    $html .= '<li>' . $this->menu_sublevel_sym . '<a ' . (($_GET[$this->GET_GAL_ID] == $child[$this->label_id]) ? 'class="mt_gal_active" ' : "") . 'href="' .
                        genGalleryLink($child[$this->label_id], $child[$this->label_column], $_SESSION['GBL_LANGID']) . '">' . $child[$this->label_column] . '</a>';
                    if (is_array($child['children']))
                        $childs = explode(";", $this->buildFlatIdArray($child['children'], $childs));
                    if (is_array($child['children']) && $_GET[$this->GET_GAL_ID] > 0 && ($_GET[$this->GET_GAL_ID] == $child[$this->label_id] || in_array($_GET[$this->GET_GAL_ID], $childs)))
                        $html .= '<ul>' . $this->outputtree_nav_gal($child['children']) . "</ul>";
                    $html .= "</li>";
                }
            }
        }
        return $html;
    }


    /**
     * cms_tree_class::getOneNodebyParent()
     * 
     * @param mixed $id
     * @param mixed $menue_arr
     * @return
     */
    function getOneNodebyParent($id, $menue_arr) {
        if (count($menue_arr) > 0) {
            foreach ($menue_arr as $key => $child) {
                if ($child[$this->label_parent] == $id)
                    return $child;
                if (is_array($child['children'])) {
                    $found = $this->getOneNode($id, $child['children']);
                    if ($found[$this->label_parent] > 0)
                        return $found;
                }
            }
        }

    }


    /**
     * cms_tree_class::getNodesByParent()
     * 
     * @param mixed $parent
     * @param mixed $menue_arr
     * @param mixed $ret_arr
     * @return
     */
    function getNodesByParent($parent, $menue_arr, $ret_arr) {
        if (count($menue_arr) > 0) {
            foreach ($menue_arr as $key => $child) {
                if ($child[$this->label_parent] == $parent)
                    $ret_arr[] = $child;
                if (is_array($child['children']))
                    $this->getOneNodebyParent($parent, $child['children'], $ret_arr);
            }
        }
        return $ret_arr;
    }

    /**
     * cms_tree_class::hasChildren()
     * 
     * @param mixed $id
     * @return
     */
    function hasChildren($id) {
        if (count($this->data) > 0) {
            foreach ($this->data as $key => $child) {
                if ($child[$this->label_parent] == $id) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    /**
     * cms_tree_class::buildSQLTreeIds()
     * 
     * @param mixed $start_id
     * @return
     */
    function buildSQLTreeIds($start_id) {
        $ret_arr = array();
        $nodes = $this->getNodesByParent($start_id, $this->menu_array, $ret_arr);
        if (count($nodes) > 0) {
            foreach ($nodes as $key => $child) {
                $sql .= ($sql != "" ? " OR " : "") . 'id=' . $child[$this->label_id];
            }
        }
        if ($sql != "")
            return "(" . $sql . ")";
        else
            return "";
    }


    /**
     * cms_tree_class::output_as_html()
     * 
     * @return
     */
    function output_as_html() {
        return '<ul> ' . $this->outputtree_tree($this->menu_array) . '</ul>';
    }


    /**
     * cms_tree_class::output_as_admin_gal_nav()
     * 
     * @param integer $node_id
     * @param bool $printoutall
     * @param bool $show_root
     * @param string $toadd
     * @return
     */
    function output_as_admin_gal_nav($node_id = 0, $printoutall = false, $show_root = false, $toadd = "") {
        return '
		<a href="javascript:expandTree(\'tree1\');">{LBL_SHOWALL}</a> | <a href="javascript:collapseTree(\'tree1\');">{LBL_CLOSEALL}</a>
		' . (($show_root === TRUE) ? '<br><a class="li_red_link" href="' . $_SERVER['PHP_SELF'] . '?gid=0&epage=' . $_GET['epage'] . '">Ebene 0 anzeigen</a>' : '') .
            '
		  <style type="text/css">   @import url(./js/mktree/mktree.css);  </style>
				<script type="text/javascript" src="./js/mktree/mktree.js"></script>
				<ul class="mktree" id="tree1">' . $this->outputtree_tree_gal_admin_nav($this->menu_array, $node_id, $printoutall, $toadd) . '</ul>';
    }
    /**
     * cms_tree_class::output_as_sitemap()
     * 
     * @return
     */
    function output_as_sitemap() {
        return $this->outputtree_tree_sitemap($this->menu_array);
    }

    /**
     * cms_tree_class::output_gal_nav()
     * 
     * @return
     */
    function output_gal_nav() {
        return $this->outputtree_nav_gal($this->menu_array);
    }

    /**
     * cms_tree_class::setShopCatItem()
     * 
     * @param mixed $child
     * @return
     */
    function setShopCatItem($child) {
        global $gb_config;
        $child[$this->label_column] = ($child[$this->label_column] == "") ? $child[$this->label_column2] : $child[$this->label_column];
        $child['catlink'] = genCatLink($child[$this->label_column], $child[$this->label_id]);
        $child['catpic'] = ($gb_config['menu_pic_show'] == 1) ? gen_thumb_image(PICS_ROOT . $child['foto'], $gb_config['menu_pic_width'], $gb_config['menu_pic_height'],
            $gb_config['menu_pic_border'], 0, 1) : '';
        $child['catpcount'] = $child[$this->counter_field];
        $child['catlabel'] = $child[$this->label_column];
        $child['catlevel'] = $child['depth'];
        $child['cid'] = $child['CCID'];
        return $child;
    }


    /**
     * cms_tree_class::buildNotVisibleFilter()
     * 
     * @param mixed $sql
     * @return
     */
    function buildNotVisibleFilter($sql) {
        $this->not_visible_ids = array();
        $ids = $this->load_data_by_sql($sql);
        foreach ($ids as $key => $row) {
            $this->not_visible_ids[] = $row[$this->label_id];
        }
    }

    /**
     * cms_tree_class::getOneNode()
     * 
     * @param mixed $id
     * @param mixed $menue_arr
     * @return
     */
    function getOneNode($id, $menue_arr) {
        if (count($menue_arr) > 0) {
            foreach ($menue_arr as $key => $child) {
                if ($child[$this->label_id] == $id)
                    return $child;
                if (is_array($child['children'])) {
                    $found = $this->getOneNode($id, $child['children']);
                    if ($found[$this->label_id] > 0)
                        return $found;
                }
            }
        }
    }

    /**
     * cms_tree_class::getBackNew()
     * 
     * @param mixed $parent
     * @param mixed $back_path
     * @return
     */
    function getBackNew($parent, &$back_path) {
        $node = $this->data[$parent];
        if (trim($node[$this->label_column]) != "")
            $back_path[] = trim($node[$this->label_column]);
        if ($node[$this->label_parent] > 0)
            $this->getBackNew($node[$this->label_parent], $back_path);
    }

    /**
     * cms_tree_class::outputtree_select()
     * 
     * @return
     */
    function outputtree_select() {
        $categs = array();
        if (count($this->menu_flat_array) > 0) {
            foreach ($this->menu_flat_array as $key => $child) {
                if ($child[$this->label_parent] > 0) {
                    $root = "";
                    $back_path = array();
                    $this->getBackNew($child[$this->label_id], $back_path);
                    $root_arr = array_reverse($back_path);
                    if (count($root_arr) > 0) {
                        $root = implode($this->sign, $root_arr);
                    }
                    $categs[$child[$this->label_id]] = ucfirst($root); #echo $root.' - '.human_file_size(memory_get_usage()).'<br>';
                }
                else
                    $categs[$child[$this->label_id]] = ucfirst($child[$this->label_column]);
            }
        }
        asort($categs);
        return $categs;
    }

    /**
     * cms_tree_class::gen_smarty_selectbox()
     * 
     * @param integer $block_id
     * @return
     */
    function gen_smarty_selectbox($block_id = -1) {
        $tree = $this->outputtree_select();
        $childs = ARRAY();
        if ($block_id > 0) {
            $node = $this->getOneNode((int)$block_id, $this->menu_array);
            if (is_array($node['children']))
                $childs = explode(";", $this->buildFlatIdArray($node['children']));
        }

        foreach ($tree as $key => $value) {
            if ($key != $block_id && !in_array($key, $childs) && $value != "") {
                $sel[$key] = $value;
            }
        }
        return $sel;
    }

    /**
     * cms_tree_class::output_as_selectbox()
     * 
     * @param string $box_name
     * @param mixed $block_id
     * @param integer $id_select
     * @param integer $null_obj
     * @param string $null_name
     * @return
     */
    function output_as_selectbox($box_name = 'parent', $block_id, $id_select = 0, $null_obj = 0, $null_name = '') {
        $tree = $this->outputtree_select();
        $ret = '<select class="form-control" name="' . $box_name . '">';
        if (strlen(trim($null_name)) > 0)
            $ret .= '<option ' . ($id_select == $null_obj ? 'selected' : '') . ' value="' . $null_obj . '">' . $null_name . '</option>';
        $node = $this->getOneNode(intval($block_id), $this->menu_array);
        $childs = ARRAY();
        if (is_array($node['children']))
            $childs = explode(";", $this->buildFlatIdArray($node['children']));
        foreach ($tree as $key => $value) {
            if ($key != $block_id && !in_array($key, $childs) && $value != "" && !in_array($key, $this->not_visible_ids)) {
                $ret .= '<option ' . ($id_select == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
            }
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * cms_tree_class::output_as_selectbox_jumb()
     * 
     * @param string $box_name
     * @param mixed $block_id
     * @param integer $id_select
     * @param integer $null_obj
     * @param string $null_name
     * @param string $toadd
     * @param string $phpself
     * @return
     */
    function output_as_selectbox_jumb($box_name = 'parent', $block_id, $id_select = 0, $null_obj = 0, $null_name = '', $toadd = '', $phpself = '') {
        $phpself = ($phpself != "") ? $phpself : $_SERVER['PHP_SELF'];
        $tree = $this->outputtree_select();
        $ret = '<select class="form-control" name="' . $box_name . '" onChange="location.href=this.options[this.selectedIndex].value">';
        if (strlen(trim($null_name)) > 0)
            $ret .= '<option ' . ($id_select == $null_obj ? 'selected' : '') . ' value="http://www.' . FM_DOMAIN . $phpself . '?' . $null_name . '=' . $null_obj . $toadd .
                '">' . $null_name . '</option>';
        $node = $this->getOneNode(intval($block_id), $this->menu_array);
        $childs = ARRAY();
        if (is_array($node['children']))
            $childs = explode(";", $this->buildFlatIdArray($node['children']));
        foreach ($tree as $key => $value) {
            if ($key != $block_id && !in_array($key, $childs) && $value != "" && !in_array($key, $this->not_visible_ids)) {
                $ret .= '<option ' . ($id_select == $key ? 'selected' : '') . ' value="http://www.' . FM_DOMAIN . $phpself . '?' . $box_name . '=' . $key . $toadd . '">' . $value .
                    '</option>';
            }
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * cms_tree_class::output_as_selectbox_jumb_gallery()
     * 
     * @param string $box_name
     * @param mixed $block_id
     * @param integer $id_select
     * @param integer $null_obj
     * @param string $null_name
     * @param string $toadd
     * @return
     */
    function output_as_selectbox_jumb_gallery($box_name = 'parent', $block_id, $id_select = 0, $null_obj = 0, $null_name = '', $toadd = '') {
        $tree = $this->outputtree_select();
        $ret = '<select class="form-control" name="' . $box_name . '" onChange="location.href=this.options[this.selectedIndex].value">';
        if (strlen(trim($null_name)) > 0)
            $ret .= '<option ' . ($id_select == $null_obj ? 'selected' : '') . ' value="http://www.' . FM_DOMAIN . $_SERVER['PHP_SELF'] . '?' . $null_name . '=' . $null_obj .
                $toadd . '">' . $null_name . '</option>';
        $node = $this->getOneNode(intval($block_id), $this->menu_array);
        $childs = ARRAY();
        if (is_array($node['children']))
            $childs = explode(";", $this->buildFlatIdArray($node['children']));
        foreach ($tree as $key => $value) {
            if ($key != $block_id && !in_array($key, $childs) && $value != "" && !in_array($key, $this->not_visible_ids)) {
                $ret .= '<option ' . ($id_select == $key ? 'selected' : '') . ' value="http://www.' . FM_DOMAIN . genCustPicAlbumLink($key, $value) . '?' . $toadd . '">' . $value .
                    '</option>';
            }
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * cms_tree_class::output_as_selectbox_only_childs()
     * 
     * @param string $box_name
     * @param mixed $template_id
     * @param integer $id_select
     * @param integer $null_obj
     * @param string $null_name
     * @return
     */
    function output_as_selectbox_only_childs($box_name = 'parent', $template_id, $id_select = 0, $null_obj = 0, $null_name = '') {
        $tree = $this->outputtree_select();
        $ret = '<select class="form-control" name="' . $box_name . '">';
        if (strlen(trim($null_name)) > 0)
            $ret .= '<option ' . ($id_select == $null_obj ? 'selected' : '') . ' value="' . $null_obj . '">' . $null_name . '</option>';
        $node = $this->getOneNode(intval($template_id), $this->menu_array);
        $childs = ARRAY();
        if (is_array($node['children']))
            $childs = $this->buildFlatIdArrayOneLevel($node['children']);
        foreach ($tree as $key => $value) {
            if ($key != $template_id && in_array($key, $childs))
                $ret .= '<option' . ($id_select == $key ? ' selected' : '') . ' value="' . $key . '">' . $value . '</option>';
        }
        $ret .= '</select>';
        return $ret;
    }


}
