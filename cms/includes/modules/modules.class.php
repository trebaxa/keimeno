<?php

/**
 * @package    Keimeno
 * @author Harald Petrich 
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('IN_SIDE') or die('Access denied.');

class modules_class extends keimeno_class {

    var $err_log = "";
    var $ok_log = "";
    protected $xml_modules = "";

    /**
     * modules_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->err_log = "";
        $this->ok_log = "";
    }

    /**
     * modules_class::global_script_loader()
     * 
     * @param mixed $file
     * @param mixed $exec_class
     * @return
     */
    function global_script_loader($file, $exec_class = null) {
        $dh = opendir(MODULE_ROOT);
        while (false !== ($module_loader_dir = readdir($dh))) {
            if ($module_loader_dir != '.' && $module_loader_dir != '..' && $module_loader_dir != '' && is_dir(MODULE_ROOT . $module_loader_dir)) {
                // Loading Script
                $fname = MODULE_ROOT . $module_loader_dir . '/mod.' . $file . '.inc.php';
                if (file_exists($fname)) {
                    include ($fname);
                }
            }
        }
    }

    /**
     * modules_class::execute_event()
     * 
     * @param mixed $eventname
     * @param mixed $exec_class
     * @param string $params
     * @return
     */
    public function execute_event($eventname, $exec_class = NULL, $params = "") {
        #  echo $eventname.'<br>';
        if (!$this->xml_modules) {
            if (file_exists(MODULE_ROOT . 'config_all_modules.xml')) {
                $this->xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
            }
            else {
                throw new kException('XML error (events): File "config_all_modules.xml" is missing.');
            }
        }
        $return_result = array();
        foreach ($this->xml_modules->modules->children() as $module) {
            if (isset($module->events)) {
                foreach ($module->events->children() as $event) {
                    if (strtolower($event->attributes()->eventname) == strtolower($eventname)) {
                        if ($event->attributes()->method == 'exec') {
                            $class_name = strval($event->attributes()->classname);
                            $C = new $class_name();
                            if (is_object($C->TCR)) {
                                $C->TCR->interpreterfe();
                            }
                            unset($C);
                        }

                        if ($event->attributes()->method == 'include') {
                            $fname = MODULE_ROOT . $module->settings->id . '/mod.' . strval($event->attributes()->file) . '.inc.php';
                            if (file_exists($fname)) {
                                include ($fname);
                            }
                            else {
                                throw new kException('XML error (events): File "' . $fname . '" is missing.');
                            }
                        }

                        if ($event->attributes()->method == 'class') {
                            $class_name = strval($event->attributes()->classname);

                            $C = new $class_name();
                            $function = strval($event->attributes()->function);
                            if (method_exists($C, $function)) {

                                try {
                                    if (!empty($params) || !empty($exec_class)) {
                                        $params = $C->$function($params, $exec_class);
                                    }
                                    else {
                                        $C->$function();
                                    }
                                }
                                catch (Exception $e) {
                                    throw new kException($class_name . ' ' . $e->getMessage());
                                }
                            }
                            else {
                                throw new kException('Missing function in "' . $class_name . '" called "' . $function . '".');
                            }

                            unset($C);
                        }
                    }
                }
            }
        }
        return (array )$params;
    }


    /**
     * modules_class::array_to_xml()
     * 
     * @param mixed $student_info
     * @param mixed $xml_student_info
     * @return
     */
    private function array_to_xml($student_info, &$xml_student_info) {
        foreach ($student_info as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml_student_info->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }
                else {
                    $this->array_to_xml($value, $xml_student_info);
                }
            }
            else {
                $xml_student_info->addChild("$key", "$value");
            }
        }
    }


    /**
     * modules_class::load_admin_translation()
     * 
     * @param mixed $langid
     * @param mixed $LANGS
     * @param mixed $MODULE
     * @return
     */
    function load_admin_translation($langid, $LANGS, $MODULE) {
        foreach ($MODULE as $module_id => $MOD) {
            $module_loader_dir = (isset($MOD['submodpath']) && $MOD['submodpath'] != "") ? MODULE_ROOT . $MOD['submodpath'] : MODULE_ROOT . $module_id . '/';
            $lng_xml_pack = $module_loader_dir . 'admin/language_' . $LANGS[$langid]['local'] . '.xml';
            if (file_exists($lng_xml_pack)) {
                $xml = simplexml_load_file($lng_xml_pack);
                if ($xml) {
                    foreach ($xml->replacements as $joker) {
                        $this->ADMIN_MOD_TRANS[md5($joker->replacement)] = array('joker' => $joker->replacement, 'value' => $joker->value);
                    }
                }
            }

            $blocked_mods = array('content', 'inlay'); #'global_admintrans'

            if ((!in_array($module_id, $blocked_mods)) && ((is_module_installed($MOD['mod_ident']) && $MOD['general_mod'] == false) || $MOD['general_mod'] == TRUE)) {
                $this->ADMIN_MOD_TRANSPAGES[$module_id] = array(
                    'path' => $module_loader_dir . 'admin/',
                    'mod_id' => $module_id,
                    'mod_name' => $MOD['module_name'],
                    'mod_allowed' => (isset($this->gbl_config['mod_' . $MOD['mod_ident']]) && $this->gbl_config['mod_' . $MOD['mod_ident']] == 1 || $MOD['general_mod'] == TRUE));
            }
        }
    }


    /**
     * modules_class::exec_sql()
     * 
     * @param mixed $sql
     * @return
     */
    function exec_sql($sql) {
        if (!$this->db->link_id)
            die('No DB connect');
        $result = mysqli_query($this->db->link_id, $sql);
        if (!$result)
            $this->err_log .= date("Y-m-d") . "-" . date("H:i:s") . ": '" . $this->db->get_err() . "'=>" . $sql . "\n";
        else
            $this->ok_log .= date("Y-m-d") . "-" . date("H:i:s") . ": " . $sql . "\n";
    }

    /**
     * modules_class::get_template()
     * 
     * @param mixed $tplid
     * @return
     */
    protected function get_template($tplid) {
        return $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$tplid);
    }

    /**
     * modules_class::load_plug_opt()
     * 
     * @param mixed $cont_matrix_id
     * @return
     */
    function load_plug_opt($cont_matrix_id) {
        $PLUGIN_OPT = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $cont_matrix_id);
        $PLUGIN_OPT = unserialize($PLUGIN_OPT['tm_plugform']);
        if (isset($PLUGIN_OPT['tplid']) && !isset($PLUGIN_OPT['tpl_name'])) {
            $TPL = $this->get_template($PLUGIN_OPT['tplid']);
            $PLUGIN_OPT['tpl_name'] = $TPL['tpl_name'];
        }
        return (array )$PLUGIN_OPT;
    }

    /**
     * modules_class::set_ident_to_cm()
     * 
     * @param mixed $tplid
     * @return void
     */
    public function set_ident_to_cm($tplid, $cont_matrix_id, $ident) {
        $R = $this->get_template($tplid);
        $upt = array('tm_content' => '{TMPL_' . $ident . '_' . (int)$cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }


    /**
     * modules_class::set_content_mode()
     * 
     * @param mixed $cont_matrix_id
     * @param string $mode
     * @param string $php
     * @return void
     */
    function set_content_mode($cont_matrix_id, $mode = 'content', $php = '') {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $cont_matrix_id);
        update_table(TBL_CMS_TEMPLATES, 'id', $TPL['tm_tid'], array('module_id' => $mode, 'php' => $php));
    }

    /**
     * modules_class::connect_to_pageindex()
     * 
     * @param mixed $link
     * @param mixed $query
     * @param mixed $pi_relatedid
     * @param mixed $modident
     * @param mixed $lngid
     * @param integer $pi_dynamic
     * @param integer $page_connected_id
     * @param string $pi_prefix_add
     * @return void
     */
    function connect_to_pageindex($link, $query, $pi_relatedid, $modident, $lngid, $pi_dynamic = 0, $page_connected_id = 0, $pi_prefix_add = "") {
        $LNG = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANG . " WHERE id=" . $lngid);
        if ($page_connected_id == 0) {
            $PAGE_CONNECTED = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=0 AND module_id='" . $modident . "' LIMIT 1");
            if ($PAGE_CONNECTED['id'] == 0)
                $PAGE_CONNECTED = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 AND module_id='" . $modident . "'");
            $page_connected_id = $PAGE_CONNECTED['id'];
        }
        if ($pi_dynamic == 0) {
            $new_link = $link;
            while (get_data_count(TBL_CMS_PAGEINDEX, '*', "pi_link='" . $new_link . "'") > 0) {
                $new_link = str_replace('.html', '', $link);
                $k++;
                $new_link .= $k . '.html';
            }
        }
        else {
            $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='" . $modident . "'");
            $pi_relatedid = 0;
            $new_link = $link;
            while (get_data_count(TBL_CMS_PAGEINDEX, '*', "pi_link='" . $new_link . "'") > 0) {
                $new_link = $link;
                if (substr($new_link, -1) == '/') {
                    $new_link = substr($new_link, 0, -1);
                }
                $k++;
                $new_link .= $k;
                $new_link = self::add_trailing_slash($new_link, true);
            }
        }
        $arr_of_query = explode('/', $new_link);
        array_shift($arr_of_query);
        $pi_prefix = array_shift($arr_of_query) . $pi_prefix_add;
        $ident = md5($modident . $pi_relatedid . $lngid . $pi_prefix);
        $arr = array(
            'pi_link' => $new_link,
            'pi_prefix' => $pi_prefix,
            'pi_ident' => $ident,
            'pi_page' => (int)$page_connected_id,
            'pi_relatedid' => $pi_relatedid,
            'pi_query' => serialize($query),
            'pi_dynamic' => $pi_dynamic,
            'pi_modident' => $modident,
            'pi_local' => $LNG['local']);
        if (get_data_count(TBL_CMS_PAGEINDEX, '*', "pi_ident='" . $ident . "'") > 0) {
            unset($arr['pi_ident']);
            update_table(TBL_CMS_PAGEINDEX, 'pi_ident', $ident, $arr);
        }
        else {
            insert_table(TBL_CMS_PAGEINDEX, $arr);
        }

    }

    /**
     * modules_class::remove_from_page_index()
     * 
     * @param mixed $modident
     * @param mixed $pi_relatedid
     * @param mixed $pi_prefix
     * @return
     */
    function remove_from_page_index($modident, $pi_relatedid, $pi_prefix) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $ident = md5($modident . $pi_relatedid . $row['id'] . $pi_prefix);
            $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_ident='" . $ident . "'");
        }
    }

    /**
     * modules_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_templates_for_plugin_by_modident($modident, $params) {
        $list = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE layout_group=1 AND modident='" . $modident .
            "' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * modules_class::parse_plugin_template()
     * 
     * @param mixed $params
     * @param mixed $ident
     * @return
     */
    function parse_plugin_template($params, $ident) {
        $html = $params['html'];
        $langid = $params['langid'];
        $ident = strtoupper($ident);
        if (strstr($html, '{TMPL_' . $ident . '_')) {
            preg_match_all("={TMPL_" . $ident . "_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $PLUGIN_OPT['cont_matrix_id'] = $cont_matrix_id;
                $this->smarty->assign('PLUG_OPT_' . $cont_matrix_id, $PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=plginopt value=$PLUG_OPT_' . $cont_matrix_id . ' %>
                <% assign var=cont_matrix_id value="' . $cont_matrix_id . '" %>
                <%include file="' . $PLUGIN_OPT['tpl_name'] . '.tpl"%>', $html);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * modules_class::save_plugin_integration()
     * 
     * @param mixed $params
     * @param mixed $ident
     * @return void
     */
    function save_plugin_integration($params, $ident) {
        $cont_matrix_id = (int)$params['id'];
        $id = (int)$params['FORM']['tplid'];
        $R = array('description' => $ident);
        if ($id > 0) {
            $R = $this->dao->load_template($id);
        }
        $upt = array(
            'tm_modident' => $ident,
            'tm_content' => '{TMPL_' . strtoupper($ident) . '_' . $cont_matrix_id . '}',
            'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, self::real_escape($upt));
        return $params;
    }


}
