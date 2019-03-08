<?php

/**
 * @package    menus
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class menus_class extends menus_master_class {

    var $MENUS = array();

    /**
     * menus_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->TREE = new cms_tree_class();
    }

    /**
     * menus_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('MENUS') != null) {
            $this->MENUS = array_merge($this->smarty->getTemplateVars('MENUS'), $this->MENUS);
            $this->smarty->clearAssign('MENUS');
        }
        $this->smarty->assign('MENUS', $this->MENUS);
    }

    /**
     * menus_class::menu_opt_fe()
     * 
     * @param mixed $arr
     * @return
     */
    function menu_opt_fe(&$arr) {
        foreach ($arr as $key => $row) {
            $row['children'] = (array)$row['children'];
            if (count($row['children']) > 0) {
                $this->menu_opt_fe($row['children']);
            }
            else {
                $arr[$key] = $this->TREE->set_tree_item_option($row);
            }
        }
    }


    /**
     * menus_class::parse_mmenus()
     * 
     * @param mixed $params
     * @return
     */
    function parse_mmenus($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];

        # Plugin Integration
        if (strstr($html, '{TMPL_MMENU_')) {
            preg_match_all("={TMPL_MMENU_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_MMENU_", "}");
                $cont_matrix_id = intval(strtolower(str_replace($rep, "", $wert)));
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $MMENU = $this->load_menu($PLUGIN_OPT['menuid']);

                # Load nested Menu
                $nested_menu = $this->nested->create_result_and_array_by_array($this->load_mmenu_matrix_fe($PLUGIN_OPT['menuid'], $this->GBL_LANGID), 0, 0, -1);
                $this->menu_opt_fe($nested_menu);
                $this->smarty->assign('TMPL_MMENUS_' . $cont_matrix_id, $nested_menu);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=mmenu value=$TMPL_MMENUS_' . $cont_matrix_id . ' %>
                ' . $MMENU['m_tpl'], $html);
            }
            self::allocate_memory($MMENU);
        }

        $params['html'] = $html;
        return $params;
    }

    /**
     * menus_class::parse_mmenu_manuel()
     * 
     * @param mixed $params
     * @return
     */
    function parse_mmenu_manuel($params) {
        global $user_object;
        $html = $params['html'];
        $langid = $params['langid'];

        # manulle integration
        if (strstr($html, '{TMPL_MENU_')) {
            preg_match_all("={TMPL_MENU_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_MENU_", "}");
                $menu_id = intval(strtolower(str_replace($rep, "", $wert)));
                $MMENU = $this->load_menu($menu_id);

                # Load nested Menu
                $nested_menu = $this->nested->create_result_and_array_by_array($this->load_mmenu_matrix_fe($menu_id, $this->GBL_LANGID), 0, 0, -1);
                $this->menu_opt_fe($nested_menu);
                $this->smarty->assign('TMPL_MENUS_' . $menu_id, $nested_menu);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=mmenu value=$TMPL_MENUS_' . $menu_id . ' %>
                ' . $MMENU['m_tpl'], $html);
            }
            self::allocate_memory($MMENU);
        }
        $params['html'] = $html;
        return $params;
    }

}
