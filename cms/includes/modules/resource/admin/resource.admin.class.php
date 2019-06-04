<?php

/**
 * @package    resource
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class resource_admin_class extends resource_master_class {

    protected $RESOURCE = array();
    var $languages = array();

    protected $varmap = array(
        'edt' => 'VARCHAR(255)',
        'hedt' => 'TEXT',
        'sc' => 'TEXT',
        'sel' => 'TEXT',
        'link' => 'VARCHAR(255)',
        'seli' => 'TEXT',
        'img' => 'VARCHAR(255)',
        'faw' => 'VARCHAR(255)',
        'file' => 'VARCHAR(255)',
        'rdate' => 'DATE',
        'resid' => 'INT(11)',
        'radio' => 'INT(1)',
        );

    protected $tplvars = array(
        "edt" => "Edit Field",
        "hedt" => "WYSIWYG Editor",
        "sc" => "HTML Script",
        "sel" => "Select Box",
        "seli" => "Select Box with Index",
        "img" => "Image File Upload",
        "link" => "Link",
        "faw" => "Font Awesome Icon",
        "file" => "File",
        "resid" => "Resource Verbindung",
        "radio" => "Schalter",
        "rdate" => "Datum");

    /**
     * resource_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->RESOURCE = array();
        $this->lang = new language_class();
        $this->lang->options['sql_table'] = TBL_CMS_LANG;
        $this->languages = $this->lang->load_langs();
        foreach ($this->languages as $lang) {
            if ($lang['approval'] == 1) {
                $this->RESOURCE['active_lang']++;
                $this->RESOURCE['languages'][] = $lang;
            }
        }
    }

    /**
     * resource_admin_class::get_resrc()
     * 
     * @return
     */
    function get_resrc() {
        return $this->RESOURCE;
    }

    /**
     * resource_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->RESOURCE['tplvars'] = $this->tplvars;
        $this->RESOURCE['max_file_upload_size'] = self::human_filesize(self::get_maximum_file_uploadsize());

        $this->smarty->assign('RESOURCE', $this->RESOURCE);
    }

    /**
     * resource_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
    }

    /**
     * resource_admin_class::gen_table_name()
     * 
     * @param mixed $f_name
     * @return
     */
    public static function gen_table_name($f_name) {
        $f_name = $org_name = self::kill_white_spaces(strtolower('resrc_ds_' . substr(self::only_alphanums($f_name), 0, 10)));
        $k = 0;
        while (get_data_count(TBL_RESRC_TABLES, '*', "f_table='" . $f_name . "'") > 0) {
            $k++;
            $f_name = $org_name . '_' . $k;
        }
        return $f_name;
    }

    /**
     * resource_admin_class::fix()
     * 
     * @return void
     */
    function fix() {
        $arr = $this->load_flx_tpls();
        foreach ($arr as $row) {
            $row = self::real_escape($row);
            if (get_data_count(TBL_RESRC_TABLES, '*', "f_table='" . str_replace(TBL_CMS_PREFIX, '', $row['f_table']) . "'") == 0) {
                insert_table(TBL_RESRC_TABLES, array(
                    'f_table' => str_replace(TBL_CMS_PREFIX, '', $row['f_table']),
                    'f_name' => $row['f_name'],
                    'f_rid' => $row['id']));
                $this->db->query("UPDATE " . TBL_RESRCDV . " SET v_table='" . str_replace(TBL_CMS_PREFIX, '', $row['f_table']) . "' WHERE v_varname NOT LIKE 'fv_%' AND v_ftid=" .
                    $row['id']);
                $this->db->query("UPDATE " . TBL_RESRC . " SET f_table='" . str_replace(TBL_CMS_PREFIX, '', $row['f_table']) . "' WHERE id=" . $row['id']);
            }
            $result = $this->db->query("SELECT id,t_tpl FROM  " . TBL_FLXTPL . "        WHERE t_tpl LIKE '%row.dataset %'");
            while ($flxtpl = $this->db->fetch_array_names($result)) {
                $t_tpl = self::real_escape($flxtpl['t_tpl']);
                $t_tpl = str_replace('row.dataset', 'row.dataset.' . $row['f_table'], $t_tpl);
                $this->db->query("UPDATE " . TBL_FLXTPL . " SET t_tpl='" . $t_tpl . "' WHERE id=" . $flxtpl['id']);
            }

            $result = $this->db->query("SELECT id,t_tpl FROM  " . TBL_RESRCPL . "        WHERE t_tpl LIKE '%resrc.dataset %'");
            while ($flxtpl = $this->db->fetch_array_names($result)) {
                $t_tpl = self::real_escape($flxtpl['t_tpl']);
                $t_tpl = str_replace('resrc.dataset', 'resrc.dataset.' . $row['f_table'], $t_tpl);
                $this->db->query("UPDATE " . TBL_RESRCPL . " SET t_tpl='" . $t_tpl . "' WHERE id=" . $flxtpl['id']);
            }
        }
    }

    /**
     * resource_admin_class::cmd_save_flx_table()
     * 
     * @return
     */
    function cmd_save_flx_table() {
        dao_class::update_table(TBL_RESRC, array('f_sitemap' => 0), array());
        foreach ($_POST['FORM'] as $id => $row) {
            update_table(TBL_RESRC, 'id', $id, $row);
        }
        $this->ej();
    }

    /**
     * resource_admin_class::created_flextpl()
     * 
     * @return id
     */
    function create_flextpl($FORM, $db_name = "") {
        $FORM = self::trim_array($FORM);
        $FORM['f_table'] = $this->gen_table_name($FORM['f_name']);
        $id = insert_table(TBL_RESRC, $FORM);
        insert_table(TBL_RESRC_TABLES, array(
            'f_table' => $this->gen_table_name($FORM['f_name']),
            'f_name' => ($db_name != "") ? $db_name : $FORM['f_name'],
            'f_rid' => $id));
        if ($id > 0) {
            $this->db->query("CREATE TABLE `" . TBL_CMS_PREFIX . $FORM['f_table'] . "` (`id` int(11) NOT NULL,
                `ds_order` INT NOT NULL DEFAULT '0',
                `ds_group` INT NOT NULL DEFAULT '0',
                `ds_langid` INT NOT NULL DEFAULT '1',
                `ds_cid` INT NOT NULL DEFAULT '0',
                `ds_settings` BLOB NOT NULL,
                PRIMARY KEY (`id`,`ds_langid`)) 
                ENGINE = MYISAM ");
        }
        return $id;
    }

    /**
     * resource_admin_class::db_create_table()
     * 
     * @param mixed $table
     * @param mixed $id
     * @return void
     */
    function db_create_table($table, $id) {
        $db_name = $table;
        $table = $this->gen_table_name($table);
        insert_table(TBL_RESRC_TABLES, array(
            'f_table' => $table,
            'f_name' => ($db_name != "") ? $db_name : $table,
            'f_rid' => $id));
        $this->db->query("CREATE TABLE `" . TBL_CMS_PREFIX . $table . "` (`id` int(11) NOT NULL,
                `ds_order` INT NOT NULL DEFAULT '0',
                `ds_group` INT NOT NULL DEFAULT '0',
                `ds_langid` INT NOT NULL DEFAULT '1',
                `ds_cid` INT NOT NULL DEFAULT '0',
                `ds_settings` BLOB NOT NULL,
                PRIMARY KEY (`id`,`ds_langid`)) 
                ENGINE = MYISAM ");
        return $table;
    }

    /**
     * resource_admin_class::cmd_add_new_table()
     * 
     * @return void
     */
    function cmd_add_new_table() {
        $table = $this->db_create_table($_GET['table'], $_GET['id']);
        self::msg('angelegt');
        $this->ej('set_resrc_table', "'" . $table . "','" . $_GET['table'] . "'");
    }


    /**
     * resource_admin_class::cmd_add_rsrc()
     * 
     * @return
     */
    function cmd_add_rsrc() {
        $this->create_flextpl($_POST['FORM']);
        $this->ej('reload_rsrc_table', 1);
    }

    /**
     * resource_admin_class::cmd_ax_create_flextpl()
     * 
     * @return
     */
    function cmd_ax_create_flextpl() {
        $id = $this->create_flextpl($_REQUEST['FORM']);
        ECHO json_encode(array('id' => $id));
        $this->hard_exit();
    }

    /**
     * resource_admin_class::set_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_opt(&$row) {
        $row['icons'][] = kf::gen_edit_icon($row['id'], '&section=edit');
        $row['icons'][] = kf::gen_del_icon($row['FID'], true, 'del_resrc');
        #   $row['row_count'] = ($row['f_table'] != "") ? get_data_count(TBL_CMS_PREFIX . $row['f_table'], '*', "1") : 0;
        return $row;
    }

    /**
     * resource_admin_class::delete_complete_resrc()
     * dev.keimeno.de/admin/run.php?epage=resource.inc&ident=6&cmd=del_resrc
     * 
     * @param mixed $id
     * @return
     */
    function delete_complete_resrc($id) {
        $FLEX = $this->load_resrc($id);
        $tables = $this->load_tables_of_resrc($id);
        foreach ($tables as $table) {
            $arr = $this->load_dataset($table['f_table']);
            foreach ($arr as $key => $row) {
                foreach ($row as $column => $value) {
                    foreach ($this->languages as $lang) {
                        if (substr($column, -4) == '_img' && $value != "") {
                            $this->deldatasetimg($id, $row['id'], $column, $table, $lang['id']);
                        }
                        if (substr($column, -4) == '_file' && $value != "") {
                            $this->deldatasetfile($id, $row['id'], $column, $table, $lang['id']);
                        }
                    }
                }
            }
        }
        # $arr = $this->load_flexvars_table($id);

        $this->delflexvarimg_by_flxtid($id);
        $this->del_resrc_var_file_by_flxtid($id);


        $tables = $this->load_tables_of_resrc($id);
        foreach ($tables as $row) {
            $this->db->query("DROP TABLE IF EXISTS `" . TBL_CMS_PREFIX . $row['f_table'] . "`");
            $this->db->query("DELETE FROM " . TBL_RESRC_TABLES . " WHERE f_table='" . $row['f_table'] . "'");
        }

        $this->db->query("DELETE FROM " . TBL_RESRC . " WHERE id=" . $id);
        $this->db->query("DELETE FROM " . TBL_RESRCDV . " WHERE v_ftid=" . $id);
        $this->db->query("DELETE FROM " . TBL_RESRCVARS . " WHERE v_ftid=" . $id);
        $this->db->query("DELETE FROM " . TBL_RESRC_CONTENT . " WHERE c_ftid=" . $id);

        # $this->db->query("DELETE FROM " . TBL_RESRCPL . " WHERE t_ftid=" . $id);
        # $this->db->query("DELETE FROM " . TBL_FLXGROUPS . " WHERE g_ftid=" . $id);
    }

    /**
     * resource_admin_class::cmd_del_table()
     * 
     * @return void
     */
    function cmd_del_table() {
        $resrc = $this->load_resrc($_GET['id']);
        $tables = $this->load_tables_of_resrc($id, $_GET['table']);
        foreach ($tables as $table) {
            $arr = $this->load_dataset($table['f_table']);
            foreach ($arr as $key => $row) {
                foreach ($row as $column => $value) {
                    foreach ($this->languages as $lang) {
                        if (substr($column, -4) == '_img' && $value != "") {
                            $this->deldatasetimg($id, $row['id'], $column, $table, $lang['id']);
                        }
                        if (substr($column, -4) == '_file' && $value != "") {
                            $this->deldatasetfile($id, $row['id'], $column, $table, $lang['id']);
                        }
                    }
                }
            }
        }
        $this->db->query("DROP TABLE IF EXISTS `" . TBL_CMS_PREFIX . $_GET['table'] . "`");
        $this->db->query("DELETE FROM " . TBL_RESRC_TABLES . " WHERE f_table='" . $_GET['table'] . "'");
        $this->ej('reload_dataset_vars', "'" . $resrc['f_table'] . "'");
    }

    /**
     * resource_admin_class::cmd_del_resrc()
     * 
     * @return
     */
    function cmd_del_resrc() {
        $this->delete_complete_resrc($_GET['ident']);
        $this->ej();
    }

    /**
     * resource_admin_class::load_rsrctable()
     * 
     * @return
     */
    function load_rsrctable() {
        $arr = $this->load_flx_tpls();
        foreach ($arr as $key => $row) {
            $arr[$key] = $this->set_opt($row);
        }
        $this->RESOURCE['table'] = (array )$arr;
    }

    /**
     * resource_admin_class::cmd_load_rsrctable()
     * 
     * @return
     */
    function cmd_load_rsrctable() {
        $this->load_rsrctable();
        $this->parse_to_smarty();
        kf::echo_template('resource.table');
    }

    /**
     * resource_admin_class::cmd_delflxgroup()
     * 
     * @return
     */
    /* function cmd_delflxgroup() {
    $this->db->query("DELETE FROM " . TBL_FLXGROUPS . " WHERE id=" . $_GET['ident']);
    $this->db->query("UPDATE " . TBL_RESRCDV . " SET v_gid=0 WHERE v_gid=" . $_GET['ident']);
    $this->ej();
    }
    */
    /**
     * resource_admin_class::load_flex_groups()
     * 
     * @param mixed $ftid
     * @return
     */
    /*  function load_flex_groups($ftid) {
    $arr = $this->load_groups($ftid);
    foreach ($arr as $key => $row) {
    $arr[$key]['icons'][] = kf::gen_del_icon($row['id'], true, 'delflxgroup');
    }
    $this->RESOURCE['flextpl']['groups'] = $arr;
    return $this->RESOURCE['flextpl']['groups'];
    }*/

    /**
     * resource_admin_class::cmd_reload_groups()
     * 
     * @return
     */
    /* function cmd_reload_groups() {
    $this->load_flex_groups($_GET['ftid']);
    $this->parse_to_smarty();
    kf::echo_template('resource.group.table');
    }*/

    /**
     * resource_admin_class::cmd_del_content()
     * 
     * @return void
     */
    function cmd_del_content() {
        $result = $this->db->query("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_cid=" . (int)$_GET['ident']);
        while ($row = $this->db->fetch_array_names($result)) {
            foreach ($this->languages as $lang) {
                $this->delflexvarimg($row['v_cid'], $row['v_vid'], $lang['id']);
                $this->del_resrc_var_file($row['v_cid'], $row['v_vid'], $lang['id']);
            }
        }
        $this->db->query("DELETE FROM " . TBL_RESRCVARS . " WHERE v_cid=" . (int)$_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_RESRC_CONTENT . " WHERE id=" . (int)$_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='resource' AND pi_relatedid='" . (int)$_GET['ident'] . "'");
        $this->ej();
    }


    /**
     * resource_admin_class::cmd_sort_content_table()
     * 
     * @return void
     */
    function cmd_sort_content_table() {
        $ids = (string )$_GET['ids'];
        $ids = explode(',', $ids);
        $k = 0;
        foreach ($ids as $id) {
            $k += 10;
            $log = dao_class::get_data_first(TBL_RESRC_CONTENT, array('id' => $id));
            dao_class::update_table(TBL_RESRC_CONTENT, array('c_sort' => $k), array('id' => $id));
        }
        $this->ej();
    }

    /**
     * resource_admin_class::load_menu()
     * 
     * @return
     */
    function load_menu() {
        $arr = array();
        $result = $this->db->query("SELECT id,parent,description FROM  " . TBL_CMS_TEMPLATES . " 
        WHERE gbl_template=0 ORDER BY parent,morder");
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[$row['id']] = $row;
        }
        return $arr;
    }

    /**
     * resource_admin_class::cmd_save_table_name()
     * 
     * @return void
     */
    function cmd_save_table_name() {
        dao_class::update_table(TBL_RESRC_TABLES, array('f_name' => $_GET['f_table']), array('f_table' => $_GET['ident']));
        $this->ej();
    }

    /**
     * resource_admin_class::load_resrc_for_edit()
     * 
     * @param mixed $id
     * @param integer $content_matrix_id
     * @param integer $gid
     * @return
     */
    function load_resrc_for_edit($id, $content_matrix_id = 0, $table = "", $langid = 1) {
        # load language
        $langid = ((int)$langid <= 0) ? 1 : $langid;
        $this->RESOURCE['flextpl'] = $this->set_opt($this->load_resrc($id));
        $table = ($table == "") ? $this->RESOURCE['flextpl']['f_table'] : $table;
        $this->RESOURCE['flextpl']['smarty_table'] = self::get_smarty_flexvar($table);
        $this->RESOURCE['flextpl']['datasetvars'] = $this->load_dataset_vars($id, $table);
        $this->RESOURCE['content_table'] = $this->load_content_table($id);
        $this->RESOURCE['content'] = $this->load_content($content_matrix_id);
        $this->RESOURCE['tables'] = $this->load_tables_of_resrc($id);
        $this->RESOURCE['all_resrc'] = $this->load_flx_tpls();

        foreach ($this->RESOURCE['tables'] as $key => $row) {
            if ($row['f_table'] == $table) {
                $this->RESOURCE['tables'][$key]['selected'] = true;
                $this->RESOURCE['table'] = $this->RESOURCE['tables'][$key];
            }
            else {
                $this->RESOURCE['tables'][$key]['selected'] = false;
            }
        }

        $dataset = array();

        # HTML Vorlagen
        $this->RESOURCE['flextpl']['tpls'] = $this->load_tpl_table($id);
        foreach ($this->RESOURCE['flextpl']['tpls'] as $key => $row) {
            $this->RESOURCE['flextpl']['tpls'][$key]['icons'][] = kf::gen_del_icon($row['id'], true, 'deltpl');
        }


        # load links
        $this->nested->label_parent = 'parent';
        $this->nested->label_id = 'id';
        $nested_menu = $this->nested->create_result_and_array_by_array($this->load_menu(), 0, 0, -1);
        $this->RESOURCE['nested_menu'] = $nested_menu;
        $this->RESOURCE['menu_selectox'] = $this->nested->outputtree_select();

        # Data Set Vars
        $tables = $this->load_tables_of_resrc($id);
        foreach ($tables as $db_table) {
            $arr = $this->load_dataset_vars_table($id, $db_table['f_table']);
            foreach ($arr as $key => $row) {
                $arr[$key]['icons'][] = kf::gen_del_icon($row['id'], true, 'deldatasetvar', '', '&langid=' . $langid . '&table=' . $db_table['f_table']);
                $arr[$key]['varname'] = htmlspecialchars('<%$row.' . $row['v_varname'] . '%>');
                if ($arr[$row['v_col']]['v_type'] == 'hedt') {

                    $arr[$key]['htmleditor'] = create_html_editor('FORM[' . $row['v_col'] . ']', '', 200, 'Full');
                }
                elseif ($arr[$row['v_col']]['v_type'] == 'sel') {
                    $arr[$key]['select'] = explode('|', $row['v_opt']['sel']['values']);
                }
                elseif ($arr[$row['v_col']]['v_type'] == 'seli') {
                    $option_pairs = explode('|', $row['v_opt']['seli']['values']);
                    foreach ($option_pairs as $ok => $opt) {
                        list($optkey, $optval) = explode(';', $opt);
                        $arr[$key]['select'][$optkey] = $optval;
                    }
                }
                elseif ($arr[$row['v_col']]['v_type'] == 'resid') {
                    $resid = (int)$row['v_opt']['resrc']['id'];
                    $arr[$key]['resrc_table'] = $this->load_content_table($resid);
                }

            }
            $this->RESOURCE['flextpl']['tables'][$db_table['f_table']] = $arr;
            if ($table == $db_table['f_table']) {
                $this->RESOURCE['flextpl']['datasetvarsdb'] = $arr;
            }
        }


        # load dataset values / content
        $arr_header = $arr = array();
        $arr = $this->load_dataset_for_plugin($table, $content_matrix_id, $gid, $langid);


        # build header
        if (is_array($arr)) {
            foreach ($arr as $key => $row) {
                array_shift($row);
                $row_id = $row['id'];
                foreach ($row as $column => $value) {
                    if (!isset($arr_header[$column]) && !in_array($column, $this->forbidden_column_arr)) {
                        #  if ($gid == 0 || $this->RESOURCE['flextpl']['datasetvarsdb'][$column]['v_gid'] == $gid) {
                        $arr_header[$column] = $this->RESOURCE['flextpl']['datasetvarsdb'][$column]['v_name'];
                        #}
                    }
                }
            }

            foreach ($arr as $key => $row) {
                $row_id = $row['id'];
                foreach ($row as $column => $value) {
                    if (!in_array($column, $this->forbidden_column_arr)) {

                        $dataset[$row_id]['row'][$column] = $value;
                        $dataset[$row_id]['ds_order'] = $row['ds_order'];

                        $dataset[$row_id]['column'][$column] = $this->RESOURCE['flextpl']['datasetvarsdb'][$column];
                        if ($dataset[$row_id]['column'][$column]['v_type'] == 'hedt') {
                            $dataset[$row_id]['column'][$column]['htmleditor'] = create_html_editor('FORM[' . $column . ']', $value, 200, 'Full');
                        }
                        if ($dataset[$row_id]['column'][$column]['v_type'] == 'sel') {
                            $dataset[$row_id]['column'][$column]['select'] = explode('|', $this->RESOURCE['flextpl']['datasetvarsdb'][$column]['v_opt']['sel']['values']);
                        }
                        if ($dataset[$row_id]['column'][$column]['v_type'] == 'seli') {
                            $option_pairs = explode('|', $this->RESOURCE['flextpl']['datasetvarsdb'][$column]['v_opt']['seli']['values']);
                            foreach ($option_pairs as $ok => $opt) {
                                list($optkey, $optval) = explode(';', $opt);
                                $dataset[$row_id]['column'][$column]['select'][$optkey] = $optval;
                            }
                        }
                        if ($dataset[$row_id]['column'][$column]['v_type'] == 'resid') {
                            $resid = (int)$this->RESOURCE['flextpl']['datasetvarsdb'][$column]['v_opt']['resrc']['id'];
                            $dataset[$row_id]['column'][$column]['resrc_table'] = $this->load_content_table($resid);
                        }
                        if ($dataset[$row_id]['column'][$column]['v_type'] == 'img') {
                            $img = ($value != "") ? $this->froot . $value : CMS_ROOT . 'images/opt_no_pic.jpg';

                            $dataset[$row_id]['column'][$column]['thumb'] = (self::get_ext($img) == 'svg' ? PATH_CMS . 'file_data/resource/images/' . basename($img) : './' . CACHE .
                                graphic_class::makeThumb($img, 107, 70, 'admin/' . CACHE, true, 'crop', "", "", 'center'));
                        }
                        if ($dataset[$row_id]['column'][$column]['v_type'] == 'file') {
                            $dataset[$row_id]['column'][$column]['file_root'] = $this->file_root . $value;
                        }
                        #}

                    }
                }
                $dataset[$row_id]['ds_settings'] = (array )$row['ds_settings'];
                $dataset[$row_id]['icons'][] = kf::gen_del_icon($row_id, true, 'deldataset', '', '&flxtid=' . $id . '&langid=' . $langid . '&table=' . $table);
            }
        }
        $this->RESOURCE['flextpl']['dataset'] = $dataset;
        $this->RESOURCE['flextpl']['dataset_header'] = $arr_header;
        unset($arr);

        # Flex Vars
        $this->RESOURCE['flextpl']['flexvars'] = $this->load_flexvars_table($id, "", true);
        $flexvarsdata = $this->load_flexvars_for_plugin($content_matrix_id, $langid);

        foreach ($this->RESOURCE['flextpl']['flexvars'] as $v_vid => $row) {
            $value = $flexvarsdata[$row['id']]['v_value'];
            $this->RESOURCE['flextpl']['flexvars'][$v_vid]['value'] = (string )$value;
            $this->RESOURCE['flextpl']['flexvars'][$v_vid]['icons'][] = kf::gen_del_icon($row['id'], true, 'delflexvar', '', '&langid=' . $langid);
            $this->RESOURCE['flextpl']['flexvars'][$v_vid]['varname'] = htmlspecialchars('<%$resrc.var.' . $row['v_varname'] . '%>');
            $this->RESOURCE['flextpl']['flexvars'][$v_vid]['v_settings'] = $flexvarsdata[$row['id']]['v_settings'];
            if ($row['v_type'] == 'hedt') {
                $this->RESOURCE['flextpl']['flexvars'][$v_vid]['htmleditor'] = create_html_editor('FORMFLEXVAR[' . $row['id'] . ']', $value, 200, 'Full');
            }
            elseif ($row['v_type'] == 'sel') {
                $this->RESOURCE['flextpl']['flexvars'][$v_vid]['select'] = explode('|', $row['v_opt']['sel']['values']);
            }
            elseif ($row['v_type'] == 'seli') {
                $option_pairs = explode('|', $row['v_opt']['seli']['values']);
                foreach ($option_pairs as $ok => $opt) {
                    list($optkey, $optval) = explode(';', $opt);
                    $this->RESOURCE['flextpl']['flexvars'][$v_vid]['select'][$optkey] = $optval;
                }
            }
            elseif ($row['v_type'] == 'resid') {
                $this->RESOURCE['flextpl']['resrc_table'] = $this->load_content_table($row['v_opt']['resrc']['id']);
            }
            elseif ($row['v_type'] == 'img') {
                $img = ($value != "") ? $this->froot . $value : CMS_ROOT . 'images/opt_no_pic.jpg';
                $this->RESOURCE['flextpl']['flexvars'][$v_vid]['thumb'] = (self::get_ext($img) == 'svg') ? PATH_CMS . 'file_data/resource/images/' . basename($img) : './' .
                    CACHE . graphic_class::makeThumb($img, 60, 60, 'admin/' . CACHE, true, 'crop', "", "", 'center');
            }
            elseif ($row['v_type'] == 'file') {
                $this->RESOURCE['flextpl']['flexvars'][$v_vid]['file_root'] = $this->file_root . $value;
            }
            elseif ($row['v_type'] == 'rdate') {
                $this->RESOURCE['flextpl']['flexvars'][$v_vid]['value'] = my_date('d.m.Y', $value);
            }

        }

    }

    /**
     * resource_admin_class::load_resrc_structure()
     * 
     * @param mixed $resrc_id
     * @return
     */
    function load_resrc_structure($resrc_id) {
        $resrc = $this->load_resrc($resrc_id);
        $vars_structure = $this->load_flexvars_table($resrc_id, "");
        $tables = $this->load_tables_of_resrc($resrc_id);
        $dataset_structure = $this->load_dataset_vars_table($resrc_id, "");
        return array(
            'resrc' => $resrc,
            'vars_structure' => $vars_structure,
            'dataset_structure' => $dataset_structure,
            'tables' => $tables);
    }


    /**
     * resource_admin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $this->load_resrc_for_edit($_GET['id'], 0, $_GET['table']);
    }

    /**
     * resource_admin_class::cmd_ax_editflextpl()
     * 
     * @return
     */
    function cmd_ax_editflextpl() {
        $this->load_resrc_for_edit($_GET['id'], 0, $_GET['table']);
        $this->parse_to_smarty();
        kf::echo_template('resource');
    }

    /**
     * resource_admin_class::cmd_ax_start()
     * 
     * @return
     */
    function cmd_ax_start() {
        $this->parse_to_smarty();
        kf::echo_template('resource');
    }

    /**
     * resource_admin_class::cmd_rename_flextpls()
     * 
     * @return
     */
    function cmd_rename_flextpls() {
        $FORM = (array )$_REQUEST['FORM'];
        update_table(TBL_RESRC, 'id', $_GET['id'], $FORM);
        echo json_encode(array('id' => $_GET['id']));
        $this->hard_exit();
    }

    /**
     * resource_admin_class::cmd_axdelresrcbytree()
     * 
     * @return
     */
    function cmd_axdelresrcbytree() {
        $this->delete_complete_resrc($_GET['id']);
        $this->msg("{LBL_DELETED}");
        $this->ej('reload_flextpl_tree');
    }

    /**
     * resource_admin_class::cmd_deltpl()
     * 
     * @return
     */
    function cmd_deltpl() {
        $this->db->query("DELETE FROM " . TBL_RESRCPL . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * resource_admin_class::delete_datasetvar()
     * 
     * @param mixed $id
     * @return
     */
    function delete_datasetvar($id) {
        $datasetvar = $this->db->query_first("SELECT * FROM " . TBL_RESRCDV . " WHERE id=" . $id);
        # $flex = $this->load_resrc($datasetvar['v_ftid']);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PREFIX . $datasetvar['v_table'] . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            if (is_file($this->froot . $row[$datasetvar['v_col']]))
                @unlink($this->froot . $row[$datasetvar['v_col']]);
            if (is_file($this->file_root . $row[$datasetvar['v_col']]))
                @unlink($this->file_root . $row[$datasetvar['v_col']]);
        }
        $this->db->query("ALTER TABLE `" . TBL_CMS_PREFIX . $datasetvar['v_table'] . "` DROP `" . $datasetvar['v_col'] . "` ");
        $this->db->query("DELETE FROM " . TBL_RESRCDV . " WHERE id=" . $id);
    }

    /**
     * resource_admin_class::cmd_delflexvar()
     * 
     * @return
     */
    function cmd_delflexvar() {
        $this->db->query("DELETE FROM " . TBL_RESRCDV . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * resource_admin_class::cmd_deldatasetvar()
     * 
     * @return
     */
    function cmd_deldatasetvar() {
        $this->delete_datasetvar($_GET['ident']);
        $this->ej();
    }

    /**
     * resource_admin_class::cmd_reload_dataset_vars()
     * 
     * @return
     */
    function cmd_reload_dataset_vars() {
        $this->load_resrc_for_edit($_GET['id'], $_GET['content_matrix_id'], $_GET['table']);
        $this->parse_to_smarty();
        kf::echo_template('resource.datasetvars.table');
    }

    /**
     * resource_admin_class::cmd_reload_htmltpl()
     * 
     * @return
     */
    function cmd_reload_htmltpl() {
        $this->cmd_edit();
        $this->parse_to_smarty();
        kf::echo_template('resource.htmltpl.table');
    }

    /**
     * resource_admin_class::cmd_add_dataset_var()
     * 
     * @return
     */
    function cmd_add_dataset_var() {
        $FORM = $this->trim_array($_POST['FORM']);
        if (isset($_POST['FORMOPT'])) {
            $FORM['v_opt'] = serialize($_POST['FORMOPT']);
        }
        $FLEX = $this->set_opt($this->load_resrc($FORM['v_ftid']));
        $datasetvars = $this->load_dataset_vars($FORM['v_ftid'], $FORM['v_table']);
        $cols = array();
        foreach ($datasetvars as $key => $row) {
            $cols[$row['Field']] = $row['Field'];
        }

        if ($_POST['varid'] > 0) {
            update_table(TBL_RESRCDV, 'id', $_POST['varid'], $FORM);
        }
        else {
            $columnname = strtolower(self::remove_white_space(self::only_alphanums($FORM['v_name'])));
            $columnname = ((strlen($columnname) > 9) ? substr($columnname, 0, 9) : $columnname) . '_' . $FORM['v_type'];

            $org_name = $columnname;
            $k = 0;
            while (isset($cols[$columnname])) {
                $k++;
                $columnname = $k . $org_name;
            }
            $this->db->query("ALTER TABLE `" . TBL_CMS_PREFIX . $FORM['v_table'] . "` ADD `" . $columnname . "` " . $this->varmap[$FORM['v_type']] . " NOT NULL");
            $FORM['v_col'] = $columnname;
            $FORM['v_varname'] = $this->gen_dsvar_name($FORM['v_name']);
            insert_table(TBL_RESRCDV, $FORM);
        }

        $this->ej('reload_dataset_vars', "'" . $FORM['v_table'] . "'");
    }

    /**
     * resource_admin_class::cmd_reload_flexvars_vars()
     * 
     * @return
     */
    function cmd_reload_flexvars_vars() {
        $this->load_resrc_for_edit($_GET['id'], 0);
        $this->parse_to_smarty();
        kf::echo_template('resource.flexvars.table');
    }


    /**
     * resource_admin_class::cmd_add_flexvars_var()
     * 
     * @return
     */
    function cmd_add_flexvars_var() {
        $FORM = $this->trim_array($_POST['FORM']);
        if (isset($_POST['FORMOPT']))
            $FORM['v_opt'] = serialize($_POST['FORMOPT']);
        if (isset($_POST['varid']) && (int)$_POST['varid'] > 0) {
            update_table(TBL_RESRCDV, 'id', $_POST['varid'], $FORM);
        }
        else {
            $FORM['v_varname'] = $this->gen_var_name($FORM['v_name']);
            insert_table(TBL_RESRCDV, $FORM);
        }
        $this->ej('reload_flexvars_vars');
    }

    /**
     * resource_admin_class::cmd_save_htmltpl()
     * 
     * @return
     */
    function cmd_save_htmltpl() {
        $FORM = $this->trim_array($_POST['FORM']);
        update_table(TBL_RESRCPL, 'id', $_POST['id'], $FORM);
        $this->ej();
    }

    /**
     * resource_admin_class::cmd_edittpl()
     * 
     * @return
     */
    function cmd_edittpl() {
        $this->RESOURCE['flxedit'] = $this->db->query_first("SELECT * FROM " . TBL_RESRCPL . " WHERE id=" . $_GET['id']);
        $this->load_resrc_for_edit($_GET['flxid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.htmleditor');
    }

    /**
     * resource_admin_class::cmd_show_flxvar_editor()
     * 
     * @return
     */
    function cmd_show_flxvar_editor() {
        $this->load_resrc_for_edit($_GET['flxid']);
        $this->RESOURCE['flxvaredit'] = $this->load_flexvar($_GET['varid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.flexvar.editor');
    }

    /**
     * resource_admin_class::cmd_add_flxhtmltpl()
     * 
     * @return
     */
    function cmd_add_flxhtmltpl() {
        $FORM = $this->trim_array($_POST['FORM']);
        insert_table(TBL_RESRCPL, $FORM);
        $this->ej('reload_htmltpl');
    }

    /**
     * resource_admin_class::cmd_save_group_table()
     * 
     * @return
     */
    /*  function cmd_save_group_table() {
    $FORM = (array )$_POST['FORM'];
    foreach ($FORM as $key => $row) {
    if ($row['g_ident'] == "") {
    $row['g_ident'] = $this->gen_group_name($row['g_name']);
    }
    update_table(TBL_FLXGROUPS, 'id', $key, $row);
    }
    $this->ej();
    }*/

    /**
     * resource_admin_class::cmd_save_flexvar_table()
     * 
     * @return
     */
    function cmd_save_flexvar_table() {
        $FORM = (array )$_POST['FORM'];
        foreach ($FORM as $key => $row) {
            $FORM[$key]['id'] = $key;
        }
        $FORM = $this->sort_multi_array($FORM, 'v_order', SORT_ASC, SORT_NUMERIC);
        foreach ($FORM as $key => $row) {
            $k += 10;
            $row['v_order'] = $k;
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_RESRCDV, 'id', $id, $row);
        }
        if (isset($_POST['dataset'])) {
            $this->ej('reload_dataset_vars', "'" . $_POST['table'] . "'");
        }
        else {
            $this->ej('reload_flexvars_vars');
        }
    }

    /**
     * resource_admin_class::load_template_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_template_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_RESRC . " WHERE 1 ORDER BY f_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * resource_admin_class::delflexvarimg_by_flxtid()
     * 
     * @param mixed $v_ftid
     * @return
     */
    function delflexvarimg_by_flxtid($v_ftid) {
        $result = $this->db->query("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_ftid=" . $v_ftid);
        while ($row = $this->db->fetch_array_names($result)) {
            foreach ($this->languages as $lang) {
                $this->delflexvarimg($row['v_cid'], $row['v_vid'], $lang['id']);
            }
        }
    }

    /**
     * resource_admin_class::del_resrc_var_file_by_flxtid()
     * 
     * @param mixed $v_ftid
     * @return
     */
    function del_resrc_var_file_by_flxtid($v_ftid) {
        $result = $this->db->query("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_ftid=" . $v_ftid);
        while ($row = $this->db->fetch_array_names($result)) {
            foreach ($this->languages as $lang) {
                $this->del_resrc_var_file($row['v_cid'], $row['v_vid'], $lang['id']);
            }
        }
    }

    /**
     * resource_admin_class::del_resrc_var_file()
     * 
     * @param mixed $content_matrix_id
     * @param mixed $rowid
     * @return
     */
    function del_resrc_var_file($content_matrix_id, $rowid, $langid = 1) {
        $rowid = (int)$rowid;
        if ($rowid > 0) {
            $ROW = $this->db->query_first("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_langid=" . (int)$langid . " AND v_cid=" . $content_matrix_id . " AND v_vid=" . $rowid);
            if (is_file($this->file_root . $ROW['v_value']))
                @unlink($this->file_root . $ROW['v_value']);
            $this->delete_flexvar_value($content_matrix_id, $rowid, $langid);
        }
    }

    /**
     * resource_admin_class::delflexvarimg()
     * 
     * @param mixed $content_matrix_id
     * @param mixed $rowid
     * @return
     */
    function delflexvarimg($v_cid, $rowid, $langid) {
        $rowid = (int)$rowid;
        if ($rowid > 0) {
            $ROW = $this->db->query_first("SELECT * FROM " . TBL_RESRCVARS . " WHERE v_langid=" . (int)$langid . " AND v_cid=" . $v_cid . " AND v_vid=" . $rowid);
            if (is_file($this->froot . $ROW['v_value']))
                delete_file($this->froot . $ROW['v_value']);
            $this->delete_flexvar_value($v_cid, $rowid, $langid);
        }
    }

    /**
     * resource_admin_class::cmd_save_flxvar_for_plugin()
     * 
     * @return
     */
    function cmd_save_flxvar_for_plugin() {
        # save flex var values
        $flxvars = (array )$_POST['FORMFLEXVAR'];
        $v_settings = (array )$_POST['SETTINGS'];
        $CM = (array )$_POST['CM'];

        $content_matrix_id = (int)$_POST['content_matrix_id'];
        $langid = (int)$_POST['langid'];
        $langid = ($langid <= 0) ? 1 : $langid;

        if ($content_matrix_id == 0) {
            $CM['c_itime'] = time();
            $content_matrix_id = insert_table(TBL_RESRC_CONTENT, $CM);
        }
        else {
            update_table(TBL_RESRC_CONTENT, 'id', $content_matrix_id, $CM);
        }

        #save images
        if (!is_dir(CMS_ROOT . 'file_data/resource/'))
            mkdir(CMS_ROOT . 'file_data/resource/', 0775);

        if (!is_dir($this->froot))
            mkdir($this->froot, 0775);
        if (!is_dir($this->file_root))
            mkdir($this->file_root, 0775);

        if (isset($_FILES['datei']) && is_array($_FILES['datei']['name'])) {
            foreach ($_FILES['datei']['name'] as $id => $fname) {
                if ($fname != "" && self::is_image($_FILES['datei']['tmp_name'][$id])) {
                    # remove existing one
                    $this->delflexvarimg($content_matrix_id, $id, $langid);
                    $fname = $this->unique_filename($this->froot, $CM['c_label'] . '.' . self::get_ext($fname));
                    $target = $this->froot . $fname;

                    if (!move_uploaded_file($_FILES['datei']['tmp_name'][$id], $target)) {
                        $this->msge('Image file error: ' . self::file_upload_err_to_txt($_FILES['datei']['tmp_name'][$id]));
                    }
                    else {
                        chmod($target, 0755);
                        if (self::get_ext($fname) != 'svg') {
                            graphic_class::resize_picture_imageick('../file_data/resource/images/' . $fname, '../file_data/resource/images/' . $fname, 2100, 2000);
                        }
                        $this->delete_flexvar_value($content_matrix_id, $id, $langid);
                        insert_table(TBL_RESRCVARS, array(
                            'v_cid' => $content_matrix_id,
                            'v_vid' => $id,
                            'v_langid' => $langid,
                            'v_ftid' => $_POST['flxid'],
                            'v_value' => $fname));
                    }
                }
            }
        }

        if (isset($_FILES['fdatei']) && is_array($_FILES['fdatei']['name'])) {
            foreach ($_FILES['fdatei']['name'] as $id => $fname) {
                if ($fname != "") {
                    # remove existing one
                    $this->del_resrc_var_file($content_matrix_id, $id, $langid);
                    $fname = $this->unique_filename($this->file_root, $fname);
                    $target = $this->file_root . $fname;
                    if (!move_uploaded_file($_FILES['fdatei']['tmp_name'][$id], $target)) {
                        $this->msge('Image file error: ' . self::file_upload_err_to_txt($_FILES['fdatei']['tmp_name'][$id]));
                    }
                    else {
                        chmod($target, 0755);
                        $this->delete_flexvar_value($content_matrix_id, $id, $langid);
                        insert_table(TBL_RESRCVARS, array(
                            'v_cid' => $content_matrix_id,
                            'v_vid' => $id,
                            'v_langid' => $langid,
                            'v_ftid' => $_POST['flxid'],
                            'v_value' => $fname));
                    }
                }
            }
        }

        # save
        foreach ($flxvars as $id => $value) {
            $this->delete_flexvar_value($content_matrix_id, $id, $langid);
            $value = self::html_editor_transform_content($value);
            $value_def = dao_class::get_data_first(TBL_RESRCDV, array('id' => $id));
            if ($value_def['v_type'] == 'rdate') {
                $value = self::date_to_sqldate($value);
            }
            insert_table(TBL_RESRCVARS, array(
                'v_cid' => $content_matrix_id,
                'v_vid' => $id,
                'v_langid' => $langid,
                'v_ftid' => $_POST['flxid'],
                'v_value' => $value));
        }

        #save settings
        foreach ($v_settings as $id => $row) {
            $this->db->query("UPDATE " . TBL_RESRCVARS . " SET v_settings='" . serialize($row) . "'  WHERE v_langid=" . $langid . " AND v_cid=" . $content_matrix_id .
                " AND v_vid=" . $id);
        }

        # rebuild page_inde
        $this->rebuild_page_index();
        $this->ej('reload_resource', (int)$_POST['flxid']);

    }


    /**
     * resource_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    /* function save_homepage_integration($params) {
    $cont_matrix_id = (int)$params['id'];
    if ($params['FORM']['flxtid'] > 0) {
    $FLEX = $this->load_resrc($params['FORM']['flxtid']);
    $upt = array('tm_content' => '{TMPL_FLXTPL_' . $cont_matrix_id . '}', 'tm_pluginfo' => $FLEX['f_name']);
    update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }
    }*/

    /**
     * resource_admin_class::cmd_plugin_load_tpls()
     * 
     * @return
     */
    function cmd_plugin_load_tpls() {
        if ($_GET['flxid'] > 0) {
            $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id']);
            $this->RESOURCE['plugopt'] = $this->load_plug_opt($_GET['content_matrix_id']);
        }
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.sel');
    }


    /**
     * resource_admin_class::cmd_deldataset()
     * 
     * @return
     */
    function cmd_deldataset() {
        $this->load_resrc_for_edit($_GET['flxtid']);
        $table = TBL_CMS_PREFIX . $_GET['table'];
        $FDS = $this->db->query_first("SELECT * FROM " . $table . " WHERE id=" . $_GET['ident']);
        foreach ($FDS as $column => $value) {
            if (substr($column, -4) == '_img') {
                @unlink($this->froot . $value);
            }
            if (substr($column, -5) == '_file') {
                @unlink($this->file_root . $value);
            }
        }
        $this->db->query("DELETE FROM " . $table . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * resource_admin_class::deldatasetimg()
     * 
     * @param mixed $flxid
     * @param mixed $rowid
     * @param mixed $column
     * @return
     */
    function deldatasetimg($flxid, $rowid, $column, $table, $langid = 1) {
        $FLEX = $this->load_resrc($flxid);
        $ROW = $this->db->query_first("SELECT * FROM " . TBL_CMS_PREFIX . $table . " WHERE id=" . $rowid . " AND ds_langid=" . (int)$langid);
        @unlink($this->froot . $ROW[$column]);
        dao_class::update_table(TBL_CMS_PREFIX . $table, array($column => ''), array('id' => $rowid, 'ds_langid' => $langid));
    }

    /**
     * resource_admin_class::deldatasetfile()
     * 
     * @param mixed $flxid
     * @param mixed $rowid
     * @param mixed $column
     * @return
     */
    function deldatasetfile($flxid, $rowid, $column, $table, $langid = 1) {
        $FLEX = $this->load_resrc($flxid);
        $ROW = $this->db->query_first("SELECT * FROM " . TBL_CMS_PREFIX . $table . " WHERE id=" . $rowid . " AND ds_langid=" . (int)$langid);
        @unlink($this->file_root . $ROW[$column]);
        dao_class::update_table(TBL_CMS_PREFIX . $table, array($column => ''), array('id' => $rowid, 'ds_langid' => $langid));
    }

    /**
     * resource_admin_class::cmd_deldatasetimg()
     * 
     * @return
     */
    function cmd_deldatasetimg() {
        $this->deldatasetimg($_GET['flxid'], $_GET['rowid'], $_GET['column'], $_GET['table'], $_GET['langid']);
        $this->hard_exit();
    }

    /**
     * resource_admin_class::cmd_deldatasetfile()
     * 
     * @return
     */
    function cmd_deldatasetfile() {
        $this->deldatasetfile($_GET['flxid'], $_GET['rowid'], $_GET['column'], $_GET['table'], $_GET['langid']);
        $this->hard_exit();
    }

    /**
     * resource_admin_class::cmd_delflexvarimg()
     * 
     * @return
     */
    function cmd_delflexvarimg() {
        $this->delflexvarimg($_GET['content_matrix_id'], $_GET['rowid'], $_GET['langid']);
        $this->hard_exit();
    }

    /**
     * resource_admin_class::cmd_del_resrc_var_file()
     * 
     * @return void
     */
    function cmd_del_resrc_var_file() {
        $this->del_resrc_var_file($_GET['content_matrix_id'], $_GET['rowid'], $_GET['langid']);
        $this->hard_exit();
    }


    /**
     * resource_admin_class::cmd_save_dataset_table()
     * 
     * @return
     */
    function cmd_save_dataset_table() {
        $this->load_resrc_for_edit($_POST['flxid'], $_POST['content_matrix_id'], $_POST['table']);
        $FORM = (array )$_POST['FORM'];
        foreach ($FORM as $key => $row) {
            $FORM[$key]['id'] = $key;
        }
        $FORM = $this->sort_multi_array($FORM, 'ds_order', SORT_ASC, SORT_NUMERIC);
        foreach ($FORM as $key => $row) {
            $k += 10;
            $row['ds_order'] = $k;
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_CMS_PREFIX . $_POST['table'], 'id', $id, $row);
        }
        $this->ej('reload_dataset', $_POST['content_matrix_id'] . ',' . $_POST['langid'] . ",'" . $_POST['table'] . "'");
    }

    /**
     * resource_admin_class::cmd_load_tpl_tree()
     * 
     * @return
     */
    function cmd_load_tpl_tree() {
        global $PERM;
        if ($PERM->perm['core_acc_resource'] == 1) {
            $this->load_rsrctable();
            $this->smarty->assign('flextpl_list', $this->RESOURCE['table']);
            $this->parse_to_smarty();
            kf::echo_template('resource.tree');
        }
        else
            $this->hard_exit();
    }

    /**
     * resource_admin_class::cmd_add_group()
     * 
     * @return
     */
    function cmd_add_group() {
        $FORM = (array )$_POST['FORM'];
        $FORM['g_ident'] = $this->gen_group_name($FORM['g_name']);
        insert_table(TBL_FLXGROUPS, $FORM);
        $this->ej('reload_groups', $FORM['g_ftid']);
    }

    /**
     * resource_admin_class::cmd_reload_html_help()
     * 
     * @return
     */
    function cmd_reload_html_help() {
        $this->RESOURCE['flxedit'] = $this->db->query_first("SELECT * FROM " . TBL_RESRCPL . " WHERE id=" . (int)$_GET['id']);
        $this->load_resrc_for_edit($_GET['flxid'], 0);
        $this->parse_to_smarty();
        kf::echo_template('resource.htmltpl.help');
    }

    /**
     * resource_admin_class::cmd_save_html_table()
     * 
     * @return void
     */
    function cmd_save_html_table() {
        $FORM = (array )$_POST['FORM'];
        $FORMSET = (array )$_POST['FORMSET'];
        dao_class::update_table(TBL_RESRCPL, array('t_use' => 0), array('t_ftid' => $_POST['t_ftid']));
        dao_class::update_table(TBL_RESRCPL, array('t_use' => 1), array('id' => $FORM['t_use'], 't_ftid' => $_POST['t_ftid']));
        foreach ($FORMSET as $id => $row) {
            update_table(TBL_RESRCPL, 'id', $id, $row);
        }
        $this->ej();
    }


    /**
     * resource_admin_class::on_autorun_admin()
     * 
     * @param mixed $params
     * @return
     */
    function on_autorun_admin($params) {
        $this->load_rsrctable();
        $this->parse_to_smarty();
        return $params;
    }


    /**
     * resource_admin_class::cmd_add_con()
     * 
     * @return void
     */
    function cmd_show_add_content() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], 1);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.flxvars');
    }

    /**
     * resource_admin_class::cmd_show_add_content_by_lang()
     * 
     * @return void
     */
    function cmd_show_add_content_by_lang() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], $_GET['langid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.form');
    }


    /**
     * resource_admin_class::cmd_show_add_datasets_by_lang()
     * 
     * @return void
     */
    function cmd_show_add_datasets_by_lang() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], $_GET['langid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.dataset.form');
    }


    /**
     * resource_admin_class::cmd_reload_dataset()
     * 
     * @return
     */
    function cmd_reload_dataset() {
        $this->cmd_show_add_datasets();
    }

    /**
     * resource_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['resrcid'];
        $R = dao_class::get_data_first(TBL_RESRC, array('id' => $id));
        $upt = array('tm_content' => '{TMPL_RESRCDETAIL_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['f_name']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
        update_table(TBL_CMS_TEMPLATES, 'id', $cont_matrix_id, array('xml_sitemap' => 0));
    }

    /**
     * resource_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_RESRC . " WHERE 1 ORDER BY f_name");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

}
