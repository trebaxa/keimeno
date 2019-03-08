<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


DEFINE('TBL_CMS_LAYOUTFILES', TBL_CMS_PREFIX . 'layoutfiles');

class layout_class extends keimeno_class {

    protected $line_break = "";

    /**
     * layout_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->line_break = PHP_EOL;
    }

    /**
     * layout_class::gen_img_list()
     * 
     * @return
     */
    function gen_img_list() {
        $this->LAY['imglist'] = array(
            array(
                'label' => '{LBL_FILE} (PNG)',
                'title' => 'Browser Icon',
                'target' => '../favicon.png'),
            array(
                'label' => '{LBL_FILE} (PNG)',
                'title' => 'CAPTCHA Image 1',
                'target' => '../images/opt_captcha1.png'),
            array(
                'label' => '{LBL_FILE} (PNG)',
                'title' => 'CAPTCHA Image 2',
                'target' => '../images/opt_captcha2.png'),
            array(
                'label' => '{LBL_FILE} (PNG)',
                'title' => 'CAPTCHA Image 3',
                'target' => '../images/opt_captcha3.png'),
            array(
                'label' => '{LBL_FILE} (PNG)',
                'title' => 'CAPTCHA Image 4',
                'target' => '../images/opt_captcha4.png'),
            array(
                'label' => '{LBL_FILE} (JPG)',
                'title' => 'Mitglied (kein Bild)',
                'target' => '../images/opt_member_nopic.jpg'));
        foreach ($this->LAY['imglist'] as $key => $img) {
            $this->LAY['imglist'][$key]['exists'] = file_exists($img['target']);
            $this->LAY['imglist'][$key]['ident'] = md5($img['target']);
            $this->LAY['imglist'][$key]['viewable'] = (strstr($img['target'], ".jpg") || strstr($img['target'], ".gif") || strstr($img['target'], ".png"));
            if (($this->LAY['imglist'][$key]['viewable'] == TRUE || strstr(basename($img['target']), '.ico')) && $this->LAY['imglist'][$key]['exists'] == True) {
                list($width_foto_px, $height_foto_px) = getimagesize($img['target']);
                $this->LAY['imglist'][$key]['width'] = $width_foto_px;
                $this->LAY['imglist'][$key]['height'] = $height_foto_px;
                if (strstr(basename($img['target']), '.ico')) {
                    $this->LAY['imglist'][$key][thumb] = $img['target'];
                }
                else {
                    $this->LAY['imglist'][$key][thumb] = kf::gen_thumbnail(str_replace("../", "/", $img['target']), 160, 90, 0) . '?a=' . rand(0, 10000);
                }

            }
        }
        foreach ($this->LAY['imglist'] as $key => $img) {
            $this->LAY['images'][md5($img['target'])] = $img;
        }
    }

    /**
     * layout_class::cmd_dellaypic()
     * 
     * @return
     */
    function cmd_dellaypic() {
        $this->gen_img_list();
        $file = $this->LAY['images'][$_GET['ident']]['target'];
        if (file_exists('../images/' . $file))
            delete_file('../images/' . $file);
        $this->gen_img_list();
        $this->parse_to_smarty();
        kf::echo_template('layout.img');
    }

    /**
     * layout_class::cmd_savecss()
     * 
     * @return
     */
    function cmd_savecss() {
        $BACKUP = new backup_class();
        $BACKUP->add(file_get_contents(CMS_ROOT . 'layout.css'), 'CSS');
        $this->allocate_memory($BACKUP);
        file_put_contents(CMS_ROOT . 'layout.css', trim(stripslashes($_POST['FORM']['layout'])));
        $this->LOGCLASS->addLog('MODIFY', 'live stylesheet changed');
        $this->hard_exit();
    }

    /**
     * layout_class::cmd_fileupload()
     * 
     * @return
     */
    function cmd_fileupload() {
        $err = false;
        $ext_file = strtolower(strrchr($_FILES['datei']['name'], '.'));
        $ext_target = strtolower(strrchr($_POST['ftarget'], '.'));
        if ($ext_file != $ext_target) {
            $err_msg = 'Erlaubter Dateityp: ' . $ext_target;
            $err = true;
        }
        if (!validate_upload_file($_FILES['datei'], TRUE)) {
            $err_msg = $_SESSION['upload_msge'];
            $err = true;
        }

        if ($err == false) {
            delete_file($_POST['ftarget']);
            move_uploaded_file($_FILES['datei']['tmp_name'], $_POST['ftarget']);
            chmod($_POST['ftarget'], 0755);
            $thumb = kf::gen_thumbnail(str_replace("../", "/", $_POST['ftarget']), 160, 90);
            delete_file('..' . $thumb);
        }
        $this->gen_img_list();
        $this->LAY['images'][$_POST['ident']]['err'] = $err_msg;
        if ($err == false)
            $this->LAY['images'][$_POST['ident']]['msg'] = '{LBLA_SAVED}';
        $this->parse_to_smarty();
        kf::echo_template('layout.img');
    }

    /**
     * layout_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        #  $this->LAY['css'] = trim(htmlspecialchars(file_get_contents('../layout.css')));
        $this->smarty->assign('LAY', $this->LAY);
    }

    /**
     * layout_class::load_backups()
     * 
     * @return
     */
    function load_backups() {
        $BACKUP = new backup_class();
        $this->LAY['backups'] = $BACKUP->load_backups_by_type('CSS');
        keimeno_class::allocate_memory($BACKUP);
    }

    /**
     * layout_class::cmd_loadbackups()
     * 
     * @return
     */
    function cmd_loadbackups() {
        $this->load_backups();
        $this->parse_to_smarty();
        kf::echo_template('layout.backups');
    }

    /**
     * layout_class::cmd_show_template_backup()
     * 
     * @return
     */
    function cmd_show_template_backup() {
        $this->parse_to_smarty();
        kf::echo_template('layout.tplbackups');
    }

    /**
     * layout_class::cmd_showbackup()
     * 
     * @return
     */
    function cmd_showbackup() {
        $BACKUP = new backup_class();
        $this->LAY['backup'] = $BACKUP->get_backup_by_id($_GET['id']);
        $this->LAY['backup']['date'] = date('d.m.Y H:i:s', $this->LAY['backup']['b_time']);
        $this->parse_to_smarty();
        keimeno_class::allocate_memory($BACKUP);
        kf::echo_template('layout.showbackup');
    }

    /**
     * layout_class::cmd_restorecss()
     * 
     * @return
     */
    function cmd_restorecss() {
        $BACKUP = new backup_class();
        $BACKUP->restore_css($_POST['id']);
        keimeno_class::allocate_memory($BACKUP);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * layout_class::get_css_files()
     * 
     * @param mixed $root
     * @return
     */
    function get_css_files($root) {
        if (!is_dir($root))
            return;
        $dir_handle = opendir($root);
        while (($entry = readdir($dir_handle)) !== false) {
            if ($entry === '.' || $entry === '..')
                continue;
            if (is_dir($root . DIRECTORY_SEPARATOR . $entry)) {
                $this->get_css_files($root . $entry . DIRECTORY_SEPARATOR);
            }
            else {
                if (strstr($entry, '.css')) { #|| 1 == 1
                    $file = str_replace(CMS_ROOT, "", $root) . $entry;
                    if ($file != "file_data/template/css/template.css")
                        $this->LAY['css_files'][] = array('file' => $file);
                }
            }
        }
    }

    /**
     * layout_class::cmd_load_css_folder()
     * 
     * @return
     */
    function cmd_load_css_folder() {
        $this->LAY['css_files'] = array();
        $this->get_css_files(CMS_ROOT . 'file_server/');
        $this->get_css_files(CMS_ROOT . 'file_data/template/');
        $this->parse_to_smarty();
        kf::echo_template('layout.cssfiles');
    }

    /**
     * layout_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_LAYOUTFILES . " SET l_active='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * layout_class::cmd_load_usedcss()
     * 
     * @return
     */
    function cmd_load_usedcss() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LAYOUTFILES . " ORDER BY l_order");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['l_file'] != "layout.css") {
                $row['icons'][] = kf::gen_del_icon($row['id'], false, 'delete_used_css');
                $row['icons'][] = kf::gen_approve_icon($row['id'], $row['l_active']);
            }
            $this->LAY['used_css_files'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('layout.usedcssfiles');
    }

    /**
     * layout_class::cmd_add_cssfile()
     * 
     * @return
     */
    function cmd_add_cssfile() {
        $file = $_GET['cssfile'];
        if (get_data_count(TBL_CMS_LAYOUTFILES, '*', "l_file='" . $file . "'") == 0) {
            $FORM = array('l_file' => $file);
            insert_table(TBL_CMS_LAYOUTFILES, $FORM);
        }
        $this->compile_css();
        $this->cmd_load_usedcss();
    }

    /**
     * layout_class::cmd_delete_used_css()
     * 
     * @return
     */
    function cmd_delete_used_css() {
        $this->db->query("DELETE FROM " . TBL_CMS_LAYOUTFILES . " WHERE id=" . (int)$this->TCR->GET['ident'] . " LIMIT 1");
        $this->compile_css();
        $this->ej();
    }

    /**
     * layout_class::cmd_save_css_order()
     * 
     * @return
     */
    function cmd_save_css_order() {
        $FORM = (array )$_POST['FORM'];
        $FORM = $this->sort_multi_array($FORM, 'l_order', SORT_ASC, SORT_NUMERIC);
        foreach ($FORM as $key => $row) {
            $k += 10;
            $row['l_order'] = $k;
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_CMS_LAYOUTFILES, 'id', $id, $row);
        }
        $this->compile_css();
        $this->echo_json_fb('load_used_css');
    }

    /**
     * layout_class::cmd_show_css_edit()
     * 
     * @return
     */
    function cmd_show_css_edit() {
        $file = CMS_ROOT . $_GET['file'];
        $this->LAY['cssfilecontent'] = "";
        if (file_exists($file))
            $this->LAY['cssfilecontent'] = trim(htmlspecialchars(file_get_contents($file)));
        $this->parse_to_smarty();
        kf::echo_template('layout.cssedit');
    }

    /**
     * layout_class::compress_css()
     * 
     * @param mixed $buffer
     * @return
     */
    function compress_css($buffer) {
        /* remove comments */
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array(
            "\r\n",
            "\r",
            "\n",
            "\t",
            '  ',
            '    ',
            '    '), '', $buffer);
        return $buffer;
    }

    /**
     * layout_class::compile_css()
     * 
     * @return
     */
    function compile_css() {
        if (!is_dir(CMS_ROOT . 'file_data/template/'))
            mkdir(CMS_ROOT . 'file_data/template/', 0755);
        if (!is_dir(CMS_ROOT . 'file_data/template/css/'))
            mkdir(CMS_ROOT . 'file_data/template/css/', 0755);
        # compile css
        $fc = "";
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LAYOUTFILES . " ORDER BY l_order");
        while ($row = $this->db->fetch_array_names($result)) {
            if (file_exists(CMS_ROOT . $row['l_file'])) {
                $fc .= file_get_contents(CMS_ROOT . $row['l_file']);
            }
            else {
                $this->db->query("DELETE FROM " . TBL_CMS_LAYOUTFILES . " WHERE id=" . $row['id']);
            }
        }
        file_put_contents(CMS_ROOT . 'file_data/template/css/template.css', $this->compress_css($fc));
    }

    /**
     * layout_class::cmd_savecssfile()
     * 
     * @return
     */
    function cmd_savecssfile() {
        $file = CMS_ROOT . $_POST['file'];
        $BACKUP = new backup_class();
        $BACKUP->add(file_get_contents($file), 'CSS', 0, 1, $file);
        $this->allocate_memory($BACKUP);
        file_put_contents($file, trim(stripslashes($_POST['FORM']['layout'])));
        $this->compile_css();
        $this->LOGCLASS->addLog('MODIFY', $file . ' stylesheet changed');
        $this->ej();
    }

    /**
     * layout_class::load_css_files()
     * 
     * @return
     */
    function load_css_files() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LAYOUTFILES . " WHERE l_active=1 ORDER BY l_order");
        while ($row = $this->db->fetch_array_names($result)) {
            if (file_exists(CMS_ROOT . $row['l_file'])) {
                $files[] = $row['l_file'];
            }
            else {
                $this->db->query("DELETE FROM " . TBL_CMS_LAYOUTFILES . " WHERE id=" . $row['id']);
            }
        }
        $this->smarty->assign('cssfiles', $files);
    }

    /**
     * layout_class::make_insert_table()
     * 
     * @param mixed $table
     * @param mixed $FORM
     * @return
     */
    function make_insert_table($table, $FORM) {
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $wert) {
                if ($sqlquery)
                    $sqlquery .= ', ';
                $sqlquery .= "`$key`='" . $this->db->real_escape_string($wert) . "'";
            }
            $sql = "INSERT INTO `" . $table . "` SET " . $sqlquery . PHP_EOL;
        }
        else
            $sql = '';
        return $sql;
    }

    /**
     * layout_class::backup_current_layout()
     * 
     * @return
     */
    function backup_current_layout($name) {
        $tplsysdir = CMS_ROOT . 'file_data/template/';
        if (!is_dir($tplsysdir))
            mkdir($tplsysdir, 0775);

        # install sql
        $filecontent = $filecontent_update = "";

        $tables = array(
            TBL_CMS_TOPLEVEL,
            TBL_CMS_TPLCON,
            TBL_CMS_LAYOUTFILES,
            #TBL_CMS_TPLVARS,
            #  TBL_CMS_TPLS,
            #  TBL_CMS_TPLMATRIX,
            TBL_CMS_TEMPLATES,
            TBL_CMS_TEMPCONTENT,
            TBL_CMS_TEMPMATRIX,
            TBL_CMS_TEMPLATE_PRE,
            TBL_FLXT,
            TBL_FLXTDV,
            TBL_FLXTPL,
            TBL_FLXVARS,
            TBL_FLXGROUPS,
            TBL_RESRC,
            TBL_RESRCDV,
            TBL_RESRC_CONTENT,
            TBL_RESRCVARS);
        foreach ($tables as $table) {
            $filecontent .= "DELETE FROM " . $table . $this->line_break;
            $result = $this->db->query("SELECT * FROM " . $table . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $filecontent .= $this->make_insert_table($table, $row) . $this->line_break;
            }
        }

        # erzeuge FlexTemplate Tables und ResourceTables
        $FLEX = new flextemp_master_class();
        $flex_arr = $FLEX->load_flx_tpls();
        $RESRC = new resource_master_class();
        $resrc_arr = $RESRC->load_flx_tpls();
        $arr = array_merge($flex_arr, $resrc_arr);
        foreach ($arr as $row) {
            $column_types = $this->get_all_columns_of_table($row['f_table']);
            $fields = "";
            foreach ($column_types as $column_name => $column_TYPE) {
                if ($fields != "")
                    $fields .= ',';
                if ($column_types[$column_name]["EXTRA"] != '')
                    $autoinc = ' ' . $column_types[$column_name]["EXTRA"];
                else
                    $autoinc = '';
                if ($column_types[$column_name]["NULL"] == 'YES')
                    $not_null = '';
                else
                    $not_null = ' NOT NULL';
                if ($column_types[$column_name]["DEFAULT"] != '' && $column_types[$column_name]["TYPE"] != 'timestamp')
                    $DEFAULT = " DEFAULT '" . $column_types[$column_name]["DEFAULT"] . "'";
                else
                    $DEFAULT = '';
                if ($column_types[$column_name]["DEFAULT"] != '' && $column_types[$column_name]["TYPE"] == 'timestamp')
                    $DEFAULT = " DEFAULT " . $column_types[$column_name]["DEFAULT"];
                else
                    $DEFAULT = '';
                $fields .= $column_name . " " . $column_types[$column_name]["TYPE"] . $not_null . $DEFAULT . $autoinc;
                if ($column_types[$column_name]["KEY"] == 'PRI') {
                    if ($pri != "")
                        $pri .= ',';
                    $pri .= $column_name; #$fields.=', PRIMARY KEY ('.$column_name.')';
                }
            }
            if ($pri != "")
                $fields .= ', PRIMARY KEY (' . $pri . ')';
            $filecontent .= "CREATE TABLE IF NOT EXISTS " . $row['f_table'] . " (" . $fields . ") ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci" . PHP_EOL;
            $pri = "";
        }
        foreach ($arr as $table_row) {
            $filecontent .= "DELETE FROM " . $table . $this->line_break;
            $result = $this->db->query("SELECT * FROM " . $table_row['f_table'] . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $filecontent .= $this->make_insert_table($table_row['f_table'], $row) . $this->line_break;
            }
        }

        # update sql
        $tables = array(
            # TBL_CMS_TPLS,
            #  TBL_CMS_TPLMATRIX,
            TBL_FLXT,
            TBL_FLXTDV,
            TBL_FLXTPL,
            TBL_FLXVARS,
            TBL_FLXGROUPS,
            TBL_RESRC,
            TBL_RESRCDV,
            TBL_RESRC_CONTENT,
            TBL_RESRCVARS);
        foreach ($tables as $table) {
            $filecontent_update .= "DELETE FROM " . $table . $this->line_break;
            $result = $this->db->query("SELECT * FROM " . $table . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $filecontent_update .= $this->make_insert_table($table, $row) . $this->line_break;
            }
        }

        $filecontent_update .= "DELETE FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1" . $this->line_break;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1");
        while ($row = $this->db->fetch_array_names($result)) {
            $filecontent_update .= $this->make_insert_table(TBL_CMS_TEMPLATES, $row) . $this->line_break;
        }
        $result = $this->db->query("SELECT C.* FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C WHERE C.tid=T.id AND T.gbl_template=1");
        while ($row = $this->db->fetch_array_names($result)) {
            $filecontent_update .= "DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE id=" . $row['id'] . $this->line_break;
            $filecontent_update .= $this->make_insert_table(TBL_CMS_TEMPCONTENT, $row) . $this->line_break;
        }

        $tplsysdir = CMS_ROOT . 'file_data/template/setup_sql/';
        if (is_dir($tplsysdir))
            $this->delete_dir_with_subdirs($tplsysdir);
        if (!is_dir($tplsysdir))
            mkdir($tplsysdir, 0775);
        $filecontent = str_replace(TBL_CMS_PREFIX, "!!TBL_CMS_PREFIX!!", trim($filecontent));
        file_put_contents($tplsysdir . 'user_templates_install.sql', $filecontent);
        $filecontent_update = str_replace(TBL_CMS_PREFIX, "!!TBL_CMS_PREFIX!!", trim($filecontent_update));
        file_put_contents($tplsysdir . 'user_templates_update.sql', $filecontent_update);

        # save list of active apps
        $json_arr = array();
        $active_apps = app_class::load_active_mods_to_array();
        foreach ($active_apps as $akey => $app) {
            foreach ($app as $key => $value) {
                if (in_array($key, array('id', 'module_name')))
                    $json_arr[$akey][$key] = $value;
            }
        }
        file_put_contents(CMS_ROOT . 'file_data/template/active-apps.json', json_encode($json_arr));

        # zip it
        $tar = "template_" . $this->format_file_name($name) . "_" . date('Y-m-d_H_i_s') . ".tar.gz";
        exec("cd " . CMS_ROOT . ";cd file_data;tar cvfz " . $tar . " ./template ./flextemp ./resource ./themeimg ./tplimg");

        if (file_exists(CMS_ROOT . 'file_data/' . $tar)) {
            copy(CMS_ROOT . 'file_data/' . $tar, CMS_ROOT . 'admin/cache/' . $tar);
            @unlink(CMS_ROOT . 'file_data/' . $tar);
        }
        return CMS_ROOT . 'admin/cache/' . $tar;
    }

    /**
     * layout_class::cmd_create_template_backup()
     * 
     * @return
     */
    function cmd_create_template_backup() {
        $tar = $this->backup_current_layout($_POST['FORM']['tpl_name']);
        $this->direct_download($tar);
    }


    /**
     * layout_class::mass_exec_sql()
     * 
     * @param mixed $all_lines
     * @return
     */
    function mass_exec_sql($all_lines) {
        file_put_contents(CMS_ROOT . 'cache/mass.sql', '');
        foreach ($all_lines as $key => $sql_exec) {
            if (substr($sql_exec, 0, 2) == "--") {
                continue;
            }
            $sql_exec = str_replace("!!TBL_CMS_PREFIX!!", TBL_CMS_PREFIX, trim($sql_exec));
            if ($sql_exec != "")
                $res = mysqli_query($this->db->link_id, $sql_exec);
            if (!$res) {
                file_put_contents(CMS_ROOT . 'cache/mass.sql', file_get_contents(CMS_ROOT . 'cache/mass.sql') . PHP_EOL . $sql_exec);
                $k++;
                if ($k == 10) {
                    # return;
                }
            }
        }
    }

    /**
     * layout_class::install_theme()
     * 
     * @param integer $only_update
     * @return void
     */
    function install_theme($only_update = 0) {
        $tplsysdir = CMS_ROOT . 'file_data/template/setup_sql/';
        exec('cd ..;cd file_data;tar -C ./ -xvvzf ./install_layout.tar.gz');
        if ($only_update == 1) {
            if (file_exists($tplsysdir . 'user_templates_update.sql')) {
                $all_sql = file($tplsysdir . 'user_templates_update.sql', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

                #  $all_sql = file_get_contents($tplsysdir . 'user_templates_update.sql');
                $this->mass_exec_sql($all_sql);
            }
        }
        else {
            if (file_exists($tplsysdir . 'user_templates_install.sql')) {
                # $all_sql = file_get_contents($tplsysdir . 'user_templates_install.sql');
                $all_sql = file($tplsysdir . 'user_templates_install.sql', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

                $this->mass_exec_sql($all_sql);
            }

            # removeing pages which are not used from toplvel
            $template = new template_class();
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=0 AND approval=0");
            while ($row = $this->db->fetch_array_names($result)) {
                $template->remove_page_from_all_toplevel($row['id']);
            }

            # adding active pages to toplevel 1
            $WEBSITE = new websites_class();
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=0 AND approval=1");
            while ($row = $this->db->fetch_array_names($result)) {
                $template->add_page_to_toplevel($row['id'], 1);
                # set permissions
                $perm_group_ids = array(1000);
                $WEBSITE->set_permission_for_groups($row['id'], $perm_group_ids);
            }


        }
        @unlink(CMS_ROOT . 'file_data/install_layout.tar.gz');

        # rewrite all templates for smarty
        require_once (CMS_ROOT . 'admin/inc/update.class.php');
        $upt = new upt_class();
        $upt->rewriteSmartyTPL();

        #activate needed apps
        $json_array = json_decode(file_get_contents(CMS_ROOT . 'file_data/template/active-apps.json'));
        foreach ($json_array as $app) {
            $active_ids[] = $app->id;
        }

        $all_mods = app_class::load_all_mods(true);
        foreach ($all_mods as $appid => $app) {
            app_class::set_mod_active_status($appid, in_array($appid, $active_ids));
        }
        app_class::generate_all_module_xml();
    }

    function backup_and_create_template_folder() {
        $this->backup_current_layout('current');
        $dir = CMS_ROOT . 'file_data/template/';
        if (!is_dir($dir))
            mkdir($dir, 0775);

        $this->delete_dir_with_subdirs($dir);
        if (!is_dir($dir))
            mkdir($dir, 0775);
    }

    /**
     * layout_class::cmd_install_layout()
     * 
     * @return
     */
    function cmd_install_layout() {
        if ($_FILES['datei']['tmp_name'] != "") {
            $this->backup_and_create_template_folder();
            move_uploaded_file($_FILES['datei']['tmp_name'], CMS_ROOT . 'file_data/install_layout.tar.gz');
            $this->install_theme($_POST['FORM']['only_update']);
            $this->ej();
        }
        else {
            $this->msge('Keine Datei.');
        }
        $this->ej();
    }

}
