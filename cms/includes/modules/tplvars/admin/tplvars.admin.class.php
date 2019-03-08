<?php

/**
 * @package    tplvars
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

DEFINE('TBL_CMS_TPLVARS', TBL_CMS_PREFIX . 'tplvars');
DEFINE('TBL_CMS_TPLS', TBL_CMS_PREFIX . 'tpls');
DEFINE('TBL_CMS_TPLMATRIX', TBL_CMS_PREFIX . 'tpl_matrix');

class tplvars_admin_class extends keimeno_class {

    protected $TPLVARS = array();

    /**
     * tplvars_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * tplvars_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        $this->db->query("DELETE FROM " . TBL_CMS_TPLVARS . " WHERE id=" . (int)$this->TCR->GET['ident']);
        $this->ej();
    }

    /**
     * tplvars_admin_class::delete_tpl()
     * 
     * @param mixed $id
     * @return
     */
    function delete_tpl($id) {
        $this->db->query("DELETE FROM " . TBL_CMS_TPLS . " WHERE id=" . (int)$id);
        $this->db->query("DELETE FROM " . TBL_CMS_TPLMATRIX . " WHERE m_tpl_id=" . (int)$id);
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_modident='tplvars' AND tm_tplvarid=" . (int)$id);
    }

    /**
     * tplvars_admin_class::cmd_deltpl()
     * 
     * @return
     */
    function cmd_deltpl() {
        $this->delete_tpl($_GET['ident']);
        $this->ej();
    }

    /**
     * tplvars_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * tplvars_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('TPLVARS', $this->TPLVARS);
    }

    /**
     * tplvars_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class();
        # $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable(51, 51);
    }

    /**
     * tplvars_admin_class::cmd_savever()
     * 
     * @return
     */
    function cmd_savever() {
        $FORM = (array )$_POST['FORM'];
        $FORM['var_opt'] = serialize($_POST['FORMOPT']);
        $id = (int)$_POST['id'];
        if ($id == 0) {
            insert_table(TBL_CMS_TPLVARS, $FORM);
        }
        else {
            update_table(TBL_CMS_TPLVARS, 'id', $id, $FORM);
        }
        $this->load_vars();
        $this->resave_all_used_content_pages();
        $this->parse_to_smarty();
        $this->echo_json_fb('reloadvars');
    }

    /**
     * tplvars_admin_class::load_vars()
     * 
     * @return
     */
    function load_vars() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLVARS . " WHERE 1 ORDER BY var_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'axdelete_item');
            $this->TPLVARS['vars'][] = $row;
        }
    }

    /**
     * tplvars_admin_class::cmd_load_single_var()
     * 
     * @return
     */
    function cmd_load_single_var() {
        $VAR = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLVARS . " WHERE id=" . (int)$_GET['id']);
        $varopt = unserialize($VAR['var_opt']);
        unset($VAR['var_opt']);
        echo json_encode(array('FORM' => $VAR, 'VAROPT' => $varopt));
        $this->hard_exit();
    }

    /**
     * tplvars_admin_class::load_tpls()
     * 
     * @return
     */
    function load_tpls() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLS . " WHERE 1 ORDER BY tpl_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'deltpl');
            #$row['icons'][] = kf::gen_edit_icon($row['id'], '', 'edittpl');
            $row['icons'][] = kf::gen_ax_edit_icon($row['id'], '', 'ax_edittpl');
            $row['usedcount'] = get_data_count(TBL_CMS_TEMPMATRIX, '*', "tm_tplvarid=" . $row['id']);
            $this->TPLVARS['tpls'][] = $row;
        }
    }

    /**
     * tplvars_admin_class::cmd_loadvars()
     * 
     * @return
     */
    function cmd_loadvars() {
        $this->load_vars();
        $this->parse_to_smarty();
        kf::echo_template('tplvar.table');
    }

    /**
     * tplvars_admin_class::cmd_loadtpls()
     * 
     * @return
     */
    function cmd_loadtpls() {
        $this->load_tpls();
        $this->parse_to_smarty();
        kf::echo_template('tpls.table');
    }

    /**
     * tplvars_admin_class::cmd_addtpl()
     * 
     * @return
     */
    function cmd_addtpl() {
        $FORM = (array )$_POST['FORM']; #  $FORM['var_opt'] = serialize($_POST['FORMOPT']);
        insert_table(TBL_CMS_TPLS, $FORM);
        $this->load_tpls();
        $this->parse_to_smarty();
        $this->echo_json_fb('reloadtpls');
    }

    /**
     * tplvars_admin_class::cmd_edittpl()
     * 
     * @return
     */
    function cmd_edittpl() {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLS . " WHERE id=" . (int)$_GET['id']);
        $this->TPLVARS['loadedtpl'] = $TPL;
        $this->load_vars();
    }

    /**
     * tplvars_admin_class::resave_all_tpls_by_tplid()
     * 
     * @param mixed $tpl_id
     * @return
     */
    function resave_all_tpls_by_tplid($tpl_id) {
        $tpl_id = (int)$tpl_id;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_tplvarid=" . $tpl_id);
        while ($row = $this->db->fetch_array_names($result)) {
            $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLS . " WHERE id=" . $tpl_id);
            $tm_plugopt = self::arr_stripslashes(unserialize($row['tm_plugopt']));
            $tm_content = $this->compile_tpl($R, $tm_plugopt);
            $this->db->query("UPDATE " . TBL_CMS_TEMPMATRIX . " SET tm_content='" . $this->db->real_escape_string($tm_content) . "' WHERE id=" . $row['id']);
        }
    }

    /**
     * tplvars_admin_class::resave_all_used_content_pages()
     * 
     * @return
     */
    function resave_all_used_content_pages() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLVARS . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->resave_all_tpls_by_tplid($row['id']);
        }
    }

    /**
     * tplvars_admin_class::cmd_savetpl()
     * 
     * @return
     */
    function cmd_savetpl() {
        $FORM = (array )$_POST['FORM'];
        update_table(TBL_CMS_TPLS, 'id', $_POST['id'], $FORM);

        // update all used content pages
        $this->resave_all_tpls_by_tplid($_POST['id']);
        $this->echo_json_fb();
    }

    /**
     * tplvars_admin_class::cmd_addvartpl()
     * 
     * @return
     */
    function cmd_addvartpl() {
        $TPLVAR = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLVARS . " WHERE id=" . $_GET['varid']);
        $FORM = array('m_var_id' => $_GET['varid'], 'm_tpl_id' => $_GET['id']);
        $id = insert_table(TBL_CMS_TPLMATRIX, $FORM);
        $placeholder = strtoupper('TMPL_' . $this->only_alphanums($this->remove_white_space($TPLVAR['var_name']) . $id) . '_' . $TPLVAR['var_type']);
        $this->db->query("UPDATE " . TBL_CMS_TPLMATRIX . " SET m_placeholder='" . $placeholder . "' WHERE id=" . $id);
        $this->load_addedvars($_GET['id']);
        $this->parse_to_smarty();
        kf::echo_template('tpls.addedvars');
    }

    /**
     * tplvars_admin_class::cmd_deladdedvar()
     * 
     * @return
     */
    function cmd_deladdedvar() {
        $this->db->query("DELETE FROM " . TBL_CMS_TPLMATRIX . " WHERE id=" . (int)$this->TCR->GET['ident']);
        $this->ej();
    }

    /**
     * tplvars_admin_class::load_addedvars()
     * 
     * @param mixed $id
     * @return
     */
    function load_addedvars($id) {
        $result = $this->db->query("SELECT *,M.id AS MID,V.id AS VID FROM " . TBL_CMS_TPLMATRIX . " M LEFT JOIN " . TBL_CMS_TPLVARS .
            " V ON (V.id=M.m_var_id) WHERE M.m_tpl_id=" . $id . " ORDER BY m_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['MID'], false, 'deladdedvar');
            $row['options'] = unserialize($row['var_opt']);
            $row['options'] = (array )$row['options'];
            $this->TPLVARS['addedvars'][$row['m_placeholder']] = $row;
        }
    }

    /**
     * tplvars_admin_class::cmd_save_added_vars_table()
     * 
     * @return
     */
    function cmd_save_added_vars_table() {
        $FORM = (array )$_POST['FORM'];
        $FORM = $this->sort_multi_array($FORM, 'm_order', SORT_ASC, SORT_NUMERIC);
        foreach ($FORM as $key => $row) {
            $k += 10;
            $row['m_order'] = $k;
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_CMS_TPLMATRIX, 'id', $id, $row);
        }
        $this->ej('reloadaddedvars');
    }

    /**
     * tplvars_admin_class::cmd_reloadaddedvars()
     * 
     * @return
     */
    function cmd_reloadaddedvars() {
        $this->load_addedvars($_GET['id']);
        $this->parse_to_smarty();
        kf::echo_template('tpls.addedvars');
    }

    /**
     * tplvars_admin_class::load_homepage_integration()
     * 
     * @return
     */
    function load_homepage_integration() {
        $this->load_tpls();
        $plugin_opt = array('tpllist' => $this->TPLVARS['tpls']);
        return (array )$plugin_opt;
    }

    /**
     * tplvars_admin_class::compile_tpl()
     * 
     * @param mixed $tpl
     * @param mixed $tm_plugopt
     * @return
     */
    function compile_tpl($tpl, $tm_plugopt) {
        $html = $tpl['tpl_content'];
        $this->load_addedvars($tpl['id']);
        foreach ($tm_plugopt as $key => $value) {
            if (stristr($key, 'HTMLEDIT') || stristr($key, 'SCRIPT')) {
                $value = base64_decode($value);
                $html = str_replace('{' . $key . '}', $value, $html);
                continue;
            }
            else
                if (stristr($key, 'IMGFILE')) {
                    if (!is_dir(CMS_ROOT . 'file_data/tplimg/images/'))
                        mkdir(CMS_ROOT . 'file_data/tplimg/images/', 0775);
                    $GRAF = new graphic_class();
                    if (!file_exists('../file_data/tplimg/' . $value) || !is_file('../file_data/tplimg/' . $value)) {
                        $file = '../images/opt_no_pic.jpg';
                    }
                    else {
                        $file = '../file_data/tplimg/' . $value;
                    }
                    if ($this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_resize'] != 'none') {
                        $thumb = $GRAF->makeThumb($file, $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_width'], $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_height'],
                            'cache', true, $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_resize'], "", "", $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_gravity']);
                        if (is_file(CMS_ROOT . 'cache/' . $thumb) && file_exists(CMS_ROOT . 'cache/' . $thumb)) {
                            copy(CMS_ROOT . 'cache/' . $thumb, CMS_ROOT . 'file_data/tplimg/images/' . $thumb);
                        }
                    }
                    else {
                        $thumb = CMS_ROOT . 'file_data/tplimg/' . $value;
                        if (is_file($thumb) && file_exists($thumb)) {
                            copy($thumb, CMS_ROOT . 'file_data/tplimg/images/' . basename($thumb));
                        }
                        $thumb = basename($thumb);
                    }

                    $html = str_replace('{' . $key . '}', PATH_CMS . 'file_data/tplimg/images/' . $thumb, $html);
                    continue;
                }
                else {
                    $value = stripslashes($value);
                    $html = str_replace('{' . $key . '}', $value, $html);
                    continue;
                }

                #  $html = str_replace('{' . $key . '}', $value, $html);
        }

        # fix wysing editor url link format
        if (strstr($html, '/{URL_TPL_')) {
            $html = str_replace('/{URL_TPL_', '{URL_TPL_', $html);
        }
        return $html;
    }

    /**
     * tplvars_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $tm_plugopt = (array )$_POST['PLUGOPT'];

        // IMG SAVE
        if (isset($_FILES) && is_array($_FILES)) {
            foreach ($_FILES as $file_key => $file) {
                if ($_FILES[$file_key]['tmp_name'] != "") {
                    if (!is_dir(CMS_ROOT . 'file_data/tplimg/'))
                        mkdir(CMS_ROOT . 'file_data/tplimg/', 0775);
                    $TM = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $cont_matrix_id);
                    $plgf = self::arr_stripslashes(unserialize($TM['tm_plugopt']));
                    if (is_file(CMS_ROOT . 'file_data/tplimg/' . $plgf[$file_key]) && file_exists(CMS_ROOT . 'file_data/tplimg/' . $plgf[$file_key])) {
                        # delete image
                        $this->load_addedvars($id);
                        $GRAF = new graphic_class();
                        $thumb = $GRAF->makeThumb('../file_data/tplimg/' . $plgf[$file_key], $this->TPLVARS['addedvars'][$file_key]['options']['imgfile']['foto_width'], $this->TPLVARS['addedvars'][$file_key]['options']['imgfile']['foto_height'],
                            'cache', false, $this->TPLVARS['addedvars'][$file_key]['options']['imgfile']['foto_resize']);
                        @unlink(CMS_ROOT . 'file_data/tplimg/images/' . $thumb);
                        @unlink(CMS_ROOT . 'file_data/tplimg/' . $plgf[$file_key]);
                    }
                    $fname = $this->format_file_name($cont_matrix_id . '_' . basename($_FILES[$file_key]['name']));
                    $target = CMS_ROOT . 'file_data/tplimg/' . $fname;
                    while (file_exists($target)) {
                        $k++;
                        $target = CMS_ROOT . 'file_data/tplimg/' . $k . $fname;
                    }
                    if (!move_uploaded_file($_FILES[$file_key]['tmp_name'], $target)) {
                        die('ERROR');
                    }
                    chmod($target, 0755);
                    $tm_plugopt[$file_key] = basename($target);
                }
            }
        }

        // BASE64 ENCODE
        foreach ($tm_plugopt as $key => $value) {
            if (stristr($key, 'HTMLEDIT') || stristr($key, 'SCRIPT')) {
                $tm_plugopt[$key] = base64_encode(stripcslashes($value));
            }
            else {
                #$tm_plugopt[$key] = strip_tags($value);
            }
        }
        #echoarr($tm_plugopt);
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLS . " WHERE id=" . (int)$id);
        $tm_content = $this->compile_tpl($R, $tm_plugopt);

        $upt = array(
            'tm_content' => $tm_content,
            'tm_tplvarid' => $id,
            'tm_plugopt' => serialize($tm_plugopt),
            'tm_pluginfo' => $R['tpl_name']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
        #die;
    }

    /**
     * tplvars_admin_class::loadpluginform()
     * 
     * @param mixed $id
     * @return
     */
    function loadpluginform($id) {
        $this->TPLVARS['tpl'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLS . " WHERE id=" . (int)$id);
        $this->load_addedvars($id);
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $_GET['content_matrix_id']);
        $this->TPLVARS['tm_plugform'] = self::arr_stripslashes(unserialize($R['tm_plugform']));
        $this->TPLVARS['tm_plugopt'] = self::arr_stripslashes(unserialize($R['tm_plugopt']));
        foreach ((array )$this->TPLVARS['tm_plugopt'] as $key => $value) {
            if (stristr($key, '_HTMLEDIT') || stristr($key, '_SCRIPT')) {
                $this->TPLVARS['tm_plugopt'][$key] = base64_decode($value);
            }
            if (stristr($key, '_IMGFILE')) {
                if (is_file(CMS_ROOT . 'file_data/tplimg/' . $this->TPLVARS['tm_plugopt'][$key])) {
                    $thumb = kf::gen_thumbnail('/file_data/tplimg/' . $this->TPLVARS['tm_plugopt'][$key], 160, 90, 'resize', false);
                    $this->TPLVARS['thumbs'][$key] = trim($thumb);
                }
                else {
                    $this->TPLVARS['thumbs'][$key] = "";
                }
            }

        }

        foreach ((array )$this->TPLVARS['addedvars'] as $key => $value) {
            if ($value['var_type'] == 'htmledit') {
                $this->TPLVARS['addedvars'][$key]['htmleditor'] = create_html_editor('PLUGOPT[' . $value['m_placeholder'] . ']', $this->TPLVARS['tm_plugopt'][$value['m_placeholder']],
                    200, 'Full');
            }
            if ($value['var_type'] == 'select') {
                $this->TPLVARS['selectboxes'][$key] = explode('|', $value['options']['select']['values']);
            }
        }
    }

    /**
     * tplvars_admin_class::cmd_loadpluginform()
     * 
     * @return
     */
    function cmd_loadpluginform() {
        $id = (int)$_GET['id'];
        $this->loadpluginform($id);
        $this->parse_to_smarty();
        kf::echo_template('tpls.plugineditor');
    }

    /**
     * tplvars_admin_class::cmd_reload_img()
     * 
     * @return
     */
    function cmd_reload_img() {
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . (int)$_GET['cmid']);
        $tm_plugopt = self::arr_stripslashes(unserialize($R['tm_plugopt']));
        $thumb = $image = "";
        foreach ((array )$tm_plugopt as $key => $value) {
            if (stristr($key, 'IMGFILE') && $key == $_GET['optkey']) {
                if (is_file(CMS_ROOT . 'file_data/tplimg/' . $value) && file_exists(CMS_ROOT . 'file_data/tplimg/' . $value)) {
                    $thumb = kf::gen_thumbnail('/file_data/tplimg/' . $value, 160, 90, 'resize', false);
                    $image = $value;
                    break;
                }
            }
        }
        echo json_encode(array('thumb' => $thumb, 'image' => $image));
        $this->hard_exit();
    }

    /**
     * tplvars_admin_class::cmd_delimg()
     * 
     * @return
     */
    function cmd_delimg() {
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . (int)$_GET['cmid']);
        $tm_plugopt = unserialize($R['tm_plugopt']);
        foreach ((array )$tm_plugopt as $key => $value) {
            if (stristr($key, 'IMGFILE') && $key == $_GET['optkey']) {
                if (file_exists(CMS_ROOT . 'file_data/tplimg/' . $value) && is_file(CMS_ROOT . 'file_data/tplimg/' . $value)) {
                    $this->load_addedvars($R['tm_tplvarid']);
                    $GRAF = new graphic_class();
                    $thumb = $GRAF->makeThumb('../file_data/tplimg/' . $value, $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_width'], $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_height'],
                        'cache', TRUE, $this->TPLVARS['addedvars'][$key]['options']['imgfile']['foto_resize']);
                    @unlink(CMS_ROOT . 'file_data/tplimg/images/' . $thumb);
                    @unlink(CMS_ROOT . 'file_data/tplimg/' . $value);
                }
                unset($tm_plugopt[$key]);
            }
        }
        $upt = array('tm_plugopt' => serialize($tm_plugopt));
        update_table(TBL_CMS_TEMPMATRIX, 'id', $_GET['cmid'], $this->real_escape($upt));
        $this->hard_exit();
    }

    /**
     * tplvars_admin_class::cmd_load_tpl_tree()
     * 
     * @return
     */
    function cmd_load_tpl_tree() {
        global $PERM;
        if ($PERM->perm['core_acc_usertemplates'] == 1) {
            $this->load_tpls();
            $this->smarty->assign('usertpls_list', $this->TPLVARS['tpls']);
            $this->parse_to_smarty();
            kf::echo_template('tpls.tree');
        }
        else
            $this->hard_exit();
    }

    /**
     * tplvars_admin_class::cmd_ax_edittpl()
     * 
     * @return
     */
    function cmd_ax_edittpl() {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLS . " WHERE id=" . (int)$_GET['id']);
        $this->TPLVARS['loadedtpl'] = $TPL;
        $this->load_vars();
        $this->parse_to_smarty();
        kf::echo_template('tplvars');
    }

    /**
     * tplvars_admin_class::cmd_ax_start()
     * 
     * @return
     */
    function cmd_ax_start() {
        $this->parse_to_smarty();
        kf::echo_template('tplvars');
    }

    /**
     * tplvars_admin_class::cmd_ax_create_usertpl()
     * 
     * @return
     */
    function cmd_ax_create_usertpl() {
        $FORM = (array )$_REQUEST['FORM'];
        $id = insert_table(TBL_CMS_TPLS, $FORM);
        ECHO json_encode(array('id' => $id));
        $this->hard_exit();
    }

    /**
     * tplvars_admin_class::cmd_rename_usertpls()
     * 
     * @return
     */
    function cmd_rename_usertpls() {
        $FORM = (array )$_REQUEST['FORM'];
        update_table(TBL_CMS_TPLS, 'id', $_GET['id'], $FORM);
        ECHO json_encode(array('id' => $_GET['id']));
        $this->hard_exit();
    }

    /**
     * tplvars_admin_class::cmd_axdelusertplsbytree()
     * 
     * @return
     */
    function cmd_axdelusertplsbytree() {
        $this->delete_tpl($_GET['id']);
        $this->msg("{LBL_DELETED}");
        $this->ej('reload_usertpl_tree');
    }


}

?>