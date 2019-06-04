<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class nestedArrClass extends keimeno_class {

    var $bread_break_sym = '&raquo;';
    var $label_column = 'name';
    var $label_id = 'cid';
    var $label_parent = 'parent';
    var $not_visible_ids = array();
    var $menu_array = array();
    var $sign = ' >';

    /**
     * nestedArrClass::__construct()
     * 
     * @return
     */
    function __construct($db = null) {
        if (is_object($db)) {
            $this->db = $db;
        }
    }

    /**
     * nestedArrClass::build_flat_id_arr()
     * 
     * @param mixed $child_arr
     * @return
     */
    function build_flat_id_arr($child_arr) {
        if (count($child_arr) > 0) {
            foreach ($child_arr as $key => $child) {
                $child_ids .= $child[$this->label_id] . ';';
                if (is_array($child['children']))
                    $child_ids .= $this->build_flat_id_arr($child['children']);
            }
        }
        return $child_ids;
    }

    /**
     * nestedArrClass::build_flat_id_into_arr()
     * 
     * @param mixed $child_arr
     * @param mixed $childids
     * @return
     */
    function build_flat_id_into_arr($child_arr, &$childids) {
        if (count($child_arr) > 0) {
            foreach ($child_arr as $key => $child) {
                $childids[] = $child[$this->label_id];
                if (is_array($child['children']))
                    $this->build_flat_id_into_arr($child['children'], $childids);
            }
        }
    }


    /**
     * nestedArrClass::build_flat_obj_arr()
     * 
     * @param mixed $child_arr
     * @param mixed $tar_arr
     * @return
     */
    function build_flat_obj_arr($child_arr, &$tar_arr = array()) {
        if (count($child_arr) > 0) {
            foreach ($child_arr as $key => $child) {
                $children = $child['children'];
                unset($child['children']);
                $tar_arr[$child[$this->label_id]] = $child;
                if (is_array($children))
                    $this->build_flat_obj_arr($children, $tar_arr);
            }
        }
    }

    /**
     * nestedArrClass::build_flat_obj_arr_of_parent()
     * 
     * @param mixed $child_arr
     * @param mixed $parent
     * @param mixed $tar_arr
     * @param string $parent_name
     * @return
     */
    function build_flat_obj_arr_of_parent($child_arr, $parent, &$tar_arr = array(), $parent_name = 'parent') {
        if (count($child_arr) > 0) {
            foreach ($child_arr as $key => $child) {
                $children = $child['children'];
                unset($child['children']);
                if ($child[$parent_name] == $parent)
                    $tar_arr[$child[$this->label_id]] = $child;
                if (is_array($children))
                    $this->build_flat_obj_arr($children, $tar_arr);
            }
        }
    }

    /**
     * nestedArrClass::in_multiarray()
     * 
     * @param mixed $elem
     * @param mixed $array
     * @param string $fieldname
     * @return
     */
    function in_multiarray($elem, $array, $fieldname = 'cid') {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while ($bottom <= $top) {
            if ($array[$bottom][$fieldname] == $elem)
                return true;
            else
                if (is_array($array[$bottom]))
                    if ($this->in_multiarray($elem, ($array[$bottom])))
                        return true;

            $bottom++;
        }
        return false;
    }

    /**
     * nestedArrClass::breadcrumb_trail()
     * 
     * @param mixed $menu_arr
     * @param mixed $cid
     * @return
     */
    function breadcrumb_trail($menu_arr, $cid) {
        $cid = (int)$cid;
        if (count($menu_arr) > 0) {
            foreach ($menu_arr as $key => $child) {
                $cid_in_childs = false;
                if (is_array($child['children'])) {
                    $cid_in_childs = $this->in_multiarray($cid, $child['children'], $this->label_id);
                }
                if ($cid_in_childs === TRUE || ($cid == $child[$this->label_id] && $child[$this->label_parent] > 0) || ($cid == $child[$this->label_id] && is_array($child['children'])) ||
                    ($cid == $child[$this->label_id] && !is_array($child['children']) && $child[$this->label_parent] == 0)) {
                    $html .= '<li>' . $this->bread_break_sym . '<span ' . (($cid == $child[$this->label_id]) ? ' class="bread_active" ' : "") . '>' . $child[$this->label_column] .
                        '</span>';
                    if (is_array($child['children']) && ($cid == $child[$this->label_id] || $cid_in_childs === TRUE))
                        $html .= '<ul>' . $this->breadcrumb_trail($child['children'], $cid) . "</ul>";
                    $html .= "</li>";
                }
            }
        }
        return $html;
    }

    /**
     * nestedArrClass::breadcrumb_trail_simple()
     * 
     * @param mixed $menu_arr
     * @param mixed $cid
     * @return
     */
    function breadcrumb_trail_simple($menu_arr, $cid) {
        $cid = (int)$cid;
        if (count($menu_arr) > 0) {
            foreach ($menu_arr as $key => $child) {
                $cid_in_childs = false;
                if (is_array($child['children'])) {
                    $cid_in_childs = $this->in_multiarray($cid, $child['children'], $this->label_id);
                }
                if ($cid_in_childs === TRUE || ($cid == $child[$this->label_id] && $child[$this->label_parent] > 0) || ($cid == $child[$this->label_id] && is_array($child['children'])) ||
                    ($cid == $child[$this->label_id] && !is_array($child['children']) && $child[$this->label_parent] == 0)) {
                    $html .= $this->bread_break_sym . $child[$this->label_column];
                    if (is_array($child['children']) && ($cid == $child[$this->label_id] || $cid_in_childs === TRUE))
                        $html .= $this->breadcrumb_trail_simple($child['children'], $cid);

                }
            }
        }
        return $html;
    }

    /**
     * nestedArrClass::create_result_and_array()
     * 
     * @param mixed $sql
     * @param mixed $parent
     * @param mixed $startDepth
     * @param mixed $maxDepth
     * @return
     */
    function create_result_and_array($sql, $parent, $startDepth, $maxDepth) {
        $this->data = $this->load_data_by_sql($sql);
        $arr = array();
        $this->menu_flat_array = array();
        $this->CreateNestedArray($this->data, $arr, $parent, $startDepth, $maxDepth, $this->menu_flat_array);
        $this->menu_array = $arr;
        return $arr;
    }

    /**
     * nestedArrClass::create_result_and_array_by_array()
     * 
     * @param mixed $data
     * @param mixed $parent
     * @param mixed $startDepth
     * @param mixed $maxDepth
     * @return
     */
    function create_result_and_array_by_array($data, $parent, $startDepth, $maxDepth) {
        $this->data = $data;
        $arr = array();
        $this->menu_flat_array = array();
        $this->CreateNestedArray($this->data, $arr, $parent, $startDepth, $maxDepth, $this->menu_flat_array);
        $this->menu_array = $arr;
        return $arr;
    }

    /**
     * nestedArrClass::init()
     * 
     * @param mixed $opt
     * @return
     */
    function init($opt) {
        foreach ($opt as $key => $value)
            $this->$key = $value;
    }

    /**
     * nestedArrClass::CreateNestedArray()
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
        $children = array_filter($data, array($this, "FilterMethod"));
        foreach ($children as $child) {
            $flatarr[$child[$this->label_id]] = $child;
            $arr[$index] = $child;
            $arr[$index]['depth'] = $startDepth;
            //you need to replace $child[$this->label_id] by your name of column, which is holding the id of current entry!
            $this->CreateNestedArray($data, $arr[$index]['children'], $child[$this->label_id], $startDepth, $maxDepth, $flatarr);
            $index++;
        }
    }
    /**
     * nestedArrClass::FilterMethod()
     * 
     * @param mixed $row
     * @return
     */
    function FilterMethod($row) {
        //you need to replace $row[$this->label_parent] by your name of column, which is holding the parent's id of current entry!
        return $row[$this->label_parent] == $this->filter;
    }

    /**
     * nestedArrClass::load_data_by_sql()
     * 
     * @param mixed $sql
     * @return
     */
    function load_data_by_sql($sql) {
        $result = $this->db->query($sql);
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[$row[$this->label_id]] = $row;
        }
        return $data;
    }

    /**
     * nestedArrClass::getBackNew()
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
     * nestedArrClass::outputtree_select()
     * 
     * @return
     */
    function outputtree_select() {
        $categs = array();
        #  echoarr($this->menu_flat_array);
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
                    $categs[$child[$this->label_id]] = ucfirst($root);
                    #echo $root.' - '.human_file_size(memory_get_usage()).'<br>';
                }
                else
                    $categs[$child[$this->label_id]] = ucfirst($child[$this->label_column]);
            }
        }
        #        asort($categs);

        return $categs;
    }


    /**
     * nestedArrClass::outputtree_select_leafsonly()
     * 
     * @return
     */
    function outputtree_select_leafsonly() {
        $categs = array();
        if (count($this->menu_flat_array) > 0) {
            foreach ($this->menu_flat_array as $key => $child) {
                //gibt es Kinder?
                $this->filter = $child[$this->label_id];
                $children = array_filter($this->menu_flat_array, array($this, "FilterMethod"));

                if ($child[$this->label_parent] > 0) {
                    $root = "";
                    $back_path = array();
                    $this->getBackNew($child[$this->label_id], $back_path);
                    $root_arr = array_reverse($back_path);
                    if (count($root_arr) > 0) {
                        $root = implode($this->sign, $root_arr);
                    }
                    if (count($children) == 0)
                        $categs[$child[$this->label_id]] = $root;
                    #echo $root.' - '.human_file_size(memory_get_usage()).'<br>';
                }
                else {
                    if (count($children) == 0)
                        $categs[$child[$this->label_id]] = $child[$this->label_column];
                }
            }
        }
        asort($categs);
        return $categs;
    }

    /**
     * nestedArrClass::getOneNode()
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
     * nestedArrClass::output_as_selectbox()
     * 
     * @param integer $id_select
     * @param string $block_id
     * @param mixed $null_obj
     * @return
     */
    function output_as_selectbox($id_select = 0, $block_id = '', $null_obj = array()) {
        $ret="";
        $tree = $this->outputtree_select();
        $childs = ARRAY();
        if ($block_id > 0) {
            $node = $this->getOneNode((int)$block_id, $this->menu_array);
            if (is_array($node['children']))
                $childs = explode(";", $this->build_flat_id_arr($node['children']));
        }
        if (count($null_obj) > 0) {
            $ret = '<option ' . ($id_select == $null_obj['key'] ? 'selected' : '') . ' value="' . $null_obj['key'] . '">' . $null_obj['value'] . '</option>';
        }
        foreach ($tree as $key => $value) {
            if ($key != $block_id && !in_array($key, $childs) && $value != "" && !in_array($key, $this->not_visible_ids)) {
                $ret .= '<option ' . ($id_select == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
            }
        }
        return $ret;
    }

    /**
     * nestedArrClass::output_as_selectbox_leafsonly()
     * 
     * @param integer $id_select
     * @param string $block_id
     * @param integer $null_obj
     * @return
     */
    function output_as_selectbox_leafsonly($id_select = 0, $block_id = '', $null_obj = 0) {
        $tree = $this->outputtree_select_leafsonly();
        $childs = ARRAY();
        if ($block_id > 0) {
            $node = $this->getOneNode((int)$block_id, $this->menu_array);
            if (is_array($node['children']))
                $childs = explode(";", $this->build_flat_id_arr($node['children']));
        }
        foreach ($tree as $key => $value) {
            if ($key != $block_id && !in_array($key, $childs) && $value != "" && !in_array($key, $this->not_visible_ids)) {
                $ret .= '<option ' . ($id_select == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
            }
        }
        return $ret;
    }

    /**
     * nestedArrClass::output_as_admin_nav()
     * 
     * @param integer $node_id
     * @param bool $printoutall
     * @return
     */
    function output_as_admin_nav($node_id = 0, $printoutall = true) {
        return '
	<a href="javascript:expandTree(\'tree1\');">{LBL_SHOWALL}</a> | <a href="javascript:collapseTree(\'tree1\');">{LBL_CLOSEALL}</a>
	<style type="text/css">   @import url(./js/mktree/mktree.css);  </style>
	<script type="text/javascript" src="./js/mktree/mktree.js"></script>
	<ul class="mktree" id="tree1">' . $this->outputtree_tree_admin_nav($this->menu_array, $node_id, $printoutall) . '</ul>';
    }

    /**
     * nestedArrClass::outputtree_tree_admin_nav()
     * 
     * @param mixed $menue_arr
     * @param mixed $node_id
     * @param bool $printoutall
     * @return
     */
    function outputtree_tree_admin_nav($menue_arr, $node_id, $printoutall = false) { #
        foreach ($menue_arr as $key => $child) {
            $this->menu_array[$key]['printout'] = false;
            if (count($this->allowed_treeids) > 0 && $child['parent'] == 0 && in_array($child[$this->label_id], $this->allowed_treeids))
                $this->menu_array[$key]['printout'] = true;
            if (count($this->allowed_treeids) > 0 && $child['parent'] > 0)
                $this->menu_array[$key]['printout'] = true;
            if ($printoutall === TRUE)
                $this->menu_array[$key]['printout'] = true;
            if ($this->menu_array[$key]['printout'] == true) {
                $a_class = (($child[$this->approval_col] == 1 || $child[$this->visible_col] == 1) ? 'class="li_green_link"' : 'class="li_red_link"');
                if (is_array($child['children']))
                    $a_class = 'class="li_green_link_open"';
                if (($child[$this->approval_col] == 1 || $child[$this->visible_col] == 1) && $node_id == $child['id'])
                    $a_class = 'class="li_green_link_active"';
                if (($child[$this->approval_col] != 1 && $child[$this->visible_col] != 1) && $node_id == $child['id'])
                    $a_class = 'class="li_red_link_active"';
                $edit_link = $this->modify_url($_SERVER['PHP_SELF'] . '?' . $this->atree_toadd, array(
                    'epage' => $_REQUEST['epage'],
                    'toplevel' => $_REQUEST['toplevel'],
                    'tmsid' => $_REQUEST['tmsid'],
                    'msid' => $_REQUEST['msid'],
                    'aktion' => $this->admin_edit_aktion,
                    'id' => $child[$this->label_id]));
                $open_link = $this->modify_url($_SERVER['PHP_SELF'] . '?' . $this->atree_toadd, array(
                    'epage' => $_REQUEST['epage'],
                    'toplevel' => $_REQUEST['toplevel'],
                    'tmsid' => $_REQUEST['tmsid'],
                    'msid' => $_REQUEST['msid'],
                    'aktion' => $_REQUEST['aktion'],
                    'starttree' => $child[$this->label_id]));

                $html .= '<li ' . ((is_array($child['children'])) ? 'class="liOpen"' : '') . '><a ' . $a_class . ' href="' . ((!is_array($child['children'])) ? $edit_link : $open_link) .
                    '">' . (($_GET['starttree'] == $child[$this->label_id]) ? "<b><i>" : "") . $child[$this->label_column] . ((ISCMS != 1) ? '(' . ($child['pcount_active'] + $child['pcount_inactive']) .
                    ')' : '') . (($_GET['starttree'] == $child[$this->label_id]) ? "</b></i>" : "") . '</a>
			' . ((is_array($child['children'])) ? '<a href="' . $edit_link . '"><img title="edit" alt="edit" src="./images/ico_edit.gif" ></a>' : "") . '
			';
                if (is_array($child['children']))
                    $html .= '<ul>' . $this->outputtree_tree_admin_nav($child['children'], $node_id, $printoutall) . "</ul>";
                $html .= "</li>";
            }
        }
        return $html;
    }

}
