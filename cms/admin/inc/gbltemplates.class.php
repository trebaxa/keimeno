<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class gbltpl_class extends keimeno_class {

    var $TEMPL_OBJ = array();
    var $langid = array();
    var $CMSDATA = array();
    var $js_path = "";

    /**
     * gbltpl_class::__construct()
     * 
     * @param mixed $CMSDATA
     * @param integer $langid
     * @return
     */
    function __construct($CMSDATA = array(), $langid = 1) {
        parent::__construct();
        $this->TEMPL_OBJ = new template_class($langid, $CMSDATA);
        $this->langid = $langid;
        $this->DATA = $CMSDATA;
        $this->js_path = CMS_ROOT . 'file_data/tpljs/';
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * gbltpl_class::cmd_load_start()
     * 
     * @return void
     */
    function cmd_load_start() {
        $LNGOBJ = new language_class();
        $this->smarty->assign('langselect', $LNGOBJ->build_lang_select_smarty($_GET['uselang']));
        kf::echo_template('gbltemplates');
    }

    /**
     * gbltpl_class::set_lang_id()
     * 
     * @param mixed $langid
     * @return
     */
    function set_lang_id(&$langid) {
        if ($langid == 0)
            $langid = 1;
        $this->langid = (int)$langid;
    }

    /**
     * gbltpl_class::cmd_search()
     * 
     * @return
     */
    function cmd_search() {
        $result = $this->db->query("SELECT C.*, T.description,T.id AS TID,T.c_type AS TT,L.bild, T.gbl_template
        FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C, " . TBL_CMS_LANG . " L 
        WHERE L.id=C.lang_id AND C.tid=T.id AND T.gbl_template=1 AND 
        (C.content LIKE '%" . $_POST['FORM']['word'] . "%'  )
        GROUP BY T.id 
        ORDER BY T.description");
        while ($row = $this->db->fetch_array_names($result)) {
            $label = '{LBLA_GBLTEMP}';
            if ($row['TT'] == 'T' && $row['gbl_template'] == 1) {
                $edit_icon = kf::gen_edit_icon($row['TID'], '&epage=gbltemplates.inc&uselang=' . $row['lang_id'] . '', 'edit', 'id', 'run.php');
                $edit_link = 'run.php?epage=gbltemplates.inc&aktion=edit&uselang=' . $row['lang_id'] . '&id=' . $row['TID'];
            }

            $word_arr = array();
            $word_arr = explode($_POST['FORM']['word'], $row['content']);
            $row['word_arr'] = $word_arr;
            $row['edit_link'] = $edit_link;
            $row['edit_icon'] = $edit_icon;
            $row['label'] = $label;
            $row['foundcount'] = count($word_arr);
            $row[thumb] = kf::gen_thumbnail('/images/' . $row['bild'], 20, 20, 0);
            $res_tab[] = $row;
        }
        $searchresult = array('res_tab' => $res_tab, 'totalfound' => count($res_tab));
        $this->smarty->assign('searchresult', $searchresult);
        $this->parse_to_smarty();
        kf::echo_template('gbltemplate.searches');
    }


    /**
     * gbltpl_class::load_gbltemplates()
     * 
     * @param mixed $mod
     * @return
     */
    function load_gbltemplates($mod) {
        $this->TEMPL_OBJ->load_template_list(1, 'T', '', $mod);
        $this->tpl_list = $this->TEMPL_OBJ->tab;
    }

    /**
     * gbltpl_class::set_permission_for_groups()
     * 
     * @param mixed $id
     * @param mixed $CUSTGROUP
     * @return
     */
    function set_permission_for_groups($id, $CUSTGROUP) {
        $id = (int)$id;
        $this->db->query("DELETE FROM " . TBL_CMS_PERMISSIONS . " WHERE perm_tid=" . $id);
        if (is_array($CUSTGROUP)) {
            foreach ($CUSTGROUP as $key => $group_id) {
                $this->db->query("INSERT INTO " . TBL_CMS_PERMISSIONS . " SET perm_tid=" . $id . ", perm_group_id=" . (int)$group_id);
            }
        }
    }

    /**
     * gbltpl_class::load_gbltemplate()
     * 
     * @param mixed $id
     * @return
     */
    function load_gbltemplate($id) {
        $this->TEMPL_OBJ->load_template((int)$id);
        $this->TEMPL_OBJ->template['hta'] = $this->load_hta((int)$id);
        $this->TEMPL_OBJ->template['formcontent'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . (int)$id . "' AND lang_id='" . $this->
            langid . "' LIMIT 1");
        $this->TEMPL_OBJ->template['oeditor'] = '<textarea data-theme="' . $this->gblconfig->ace_theme .
            '" class="form-control se-html" rows="60" style="width:100%;" name="FORMCON[content]">' . trim(htmlspecialchars($this->TEMPL_OBJ->template['formcontent']['content'])) .
            '</textarea>';
        $this->TEMPL_OBJ->template['fixlink'] = htmlspecialchars('<% $HTA_CMSFIXLINKS.' . $this->TEMPL_OBJ->template['hta']['hta_tmpllink'] . ' %>');
        $this->TEMPL_OBJ->template['layout_group'] = (int)$this->TEMPL_OBJ->template['layout_group'];
        include_once (CMS_ROOT . 'admin/inc/framework.class.php');
        $FR = new framework_class();
        $this->TEMPL_OBJ->template['guiframeworks'] = $FR->FW['frameworks'];
        unset($FR);

        # load permission boxes
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $perm_checkoxes .= '<div class="checkbox"><label><input type="checkbox" ' . ((get_data_count(TBL_CMS_PERMISSIONS, 'perm_tid', "perm_tid=" . (int)$id .
                " AND perm_group_id=" . $row['id']) > 0) ? 'checked' : '') . ' name="CUSTGROUP[' . $row['id'] . ']" value="' . $row['id'] . '"> ' . (($row['id'] == 1000) ?
                '<b>' : '') . $row['groupname'] . (($row['id'] == 1000) ? '</b>' : '') . '</label></div>';
        }
        $this->TEMPL_OBJ->template['permboxes'] = $perm_checkoxes;

        $this->smarty->assign('TPLOBJ', $this->TEMPL_OBJ->template);
        $_SESSION['gbltemplatemod'] = $this->TEMPL_OBJ->template['modident'];
    }

    /**
     * gbltpl_class::cmd_show_org_tpl()
     * 
     * @return
     */
    function cmd_show_org_tpl() {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id='" . $_GET['tid'] . "'");
        $modid = $TPL['modident'];
        $dir = MODULE_ROOT . $modid . '/setup/tpl';
        if (is_dir($dir)) {
            $dh = opendir($dir);
            if ($dh) {
                while (false !== ($file = readdir($dh))) {
                    if ($file != '.' && $file != '..') {
                        $elements = explode('-', $file);
                        $this->GBLTPL['tplfiles'][] = str_replace('.tpl', '', $file);
                    }
                }
                closedir($dir);
            }
        }
        $this->parse_to_smarty();
        kf::echo_template('gbltemplate.orgtpl');
    }

    /**
     * gbltpl_class::cmd_loadorgtpl()
     * 
     * @return
     */
    function cmd_loadorgtpl() {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id='" . $_GET['tid'] . "'");
        $modid = $TPL['modident'];
        $this->GBLTPL['tplfilecontent'] = file_get_contents(MODULE_ROOT . $modid . '/setup/tpl/' . $_GET['modfile'] . '.tpl');
        $this->parse_to_smarty();
        kf::echo_template('gbltemplate.orgtpl');
    }

    /**
     * gbltpl_class::cmd_a_delete()
     * 
     * @return
     */
    function cmd_a_delete() {
        $this->TEMPL_OBJ->delete_template($_GET['id']);
        $this->TCR->set_url_tag('configid');
        $this->msg('{LBL_DELETED}');
    }

    function cmd_deltpljson() {
        $this->cmd_a_delete();
        $this->ej('clear_gbltpl_form');
    }

    /**
     * gbltpl_class::gen_tpl_name()
     * 
     * @param mixed $TPL
     * @return
     */
    function gen_tpl_name($TPL) {
        return (($TPL['modident'] != "") ? $TPL['modident'] . '_' : "fe_") . $this->format_tpl_name($TPL['description']);
    }

    /**
     * gbltpl_class::set_tpl_name_in_content_table()
     * 
     * @return
     */
    function set_tpl_name_in_content_table() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 AND modident<>''");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET t_tpl_name='" . $row['tpl_name'] . "' WHERE tid=" . $row['id']);
        }
    }

    /**
     * gbltpl_class::fix_tpl_name()
     * 
     * @param mixed $tid
     * @return
     */
    function fix_tpl_name($tid) {
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id='" . $tid . "'");
        if (strstr($TPL['tpl_name'], 'new-node')) {
            $new_name = $this->gen_tpl_name($TPL); # str_replace('-', '_', $this->format_file_name($TPL['description']));
            if (get_data_count(TBL_CMS_TEMPLATES, 'tpl_name', "tpl_name='" . $new_name . "'") > 0) {
                $new_name .= '_' . $tid;
            }
            $FORM = array('tpl_name' => $new_name);
            update_table(TBL_CMS_TEMPLATES, 'id', $tid, $FORM);
            $this->set_tpl_name_in_content_table();
            $this->rewrite_tpls_of_template($tid);
        }
    }

    /**
     * gbltpl_class::cmd_rename_gbltpl()
     * 
     * @return
     */
    function cmd_rename_gbltpl() {
        $FORM = (array )$_REQUEST['FORM'];
        list($tmp, $tid) = explode('-', $_GET['id']);
        update_table(TBL_CMS_TEMPLATES, 'id', $tid, $FORM);
        $this->fix_tpl_name($tid);
        ECHO json_encode(array('id' => $tid));
        $this->hard_exit();
    }

    /**
     * gbltpl_class::cmd_delete_gbltpl()
     * 
     * @return
     */
    function cmd_delete_gbltpl() {
        $this->TEMPL_OBJ->delete_template($_GET['id']);
        $this->hard_exit();
    }

    /**
     * gbltpl_class::cmd_create_gbltpl()
     * 
     * @return
     */
    function cmd_create_gbltpl() {
        list($tmp, $modid) = explode('-', $_GET['id']);
        $FORM = $_GET['FORM'];
        $FORM['modident'] = $modid;
        $tempalte_id = $this->createnew($FORM);
        ECHO json_encode(array('id' => $tempalte_id, 'modid' => $modid));
        $this->hard_exit();
    }

    /*
    function cmd_createnew() {
    $FORM = $_REQUEST['FORM'];
    $tempalte_id = $this->createnew($FORM);
    #$this->ej('std_load_gbltpl', $tempalte_id.',1,1');
    $this->ej('add_node',$tempalte_id);
    }
    */

    /**
     * gbltpl_class::rewrite_tpls_of_template()
     * 
     * @param mixed $tempalte_id
     * @return
     */
    function rewrite_tpls_of_template($tempalte_id) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $T_OBJ = $this->db->query_first("SELECT *, T.id as TID FROM " . TBL_CMS_TEMPLATES . " T
				LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $row['id'] . " AND T.id=TC.tid)
				WHERE T.id=" . $tempalte_id . "
				");
            $this->TEMPL_OBJ->save_tpl_file($T_OBJ);
        }
    }

    /**
     * gbltpl_class::createnew()
     * 
     * @param mixed $FORM
     * @return
     */
    function createnew($FORM) {
        $FORM['admin'] = 0;
        $FORM['approval'] = 1;
        $FORM['gbl_template'] = 1;
        $FORM['no_fck'] = 1;
        $tempalte_id = insert_table(TBL_CMS_TEMPLATES, $FORM);
        $FORM['tpl_name'] = $this->gen_tpl_name($FORM); # $this->format_file_name($FORM['description'] . '_' . $tempalte_id);
        update_table(TBL_CMS_TEMPLATES, 'id', $tempalte_id, $FORM);

        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $FORMCONTENT['tid'] = $tempalte_id;
            $FORMCONTENT['lang_id'] = $row['id'];
            $content_id = insert_table(TBL_CMS_TEMPCONTENT, $FORMCONTENT);
            $T_OBJ = $this->db->query_first("SELECT *, T.id as TID FROM " . TBL_CMS_TEMPLATES . " T
				LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $FORMCONTENT['lang_id'] . " AND T.id=TC.tid)
				WHERE T.id=" . $FORMCONTENT['tid'] . "
				");
            $this->TEMPL_OBJ->save_tpl_file($T_OBJ);
        }
        $this->set_tpl_name_in_content_table();
        return $tempalte_id;
    }

    /**
     * gbltpl_class::save_gbltpl()
     * 
     * @param mixed $FORM
     * @param mixed $FORMCONTENT
     * @param mixed $tempalte_id
     * @param mixed $content_id
     * @return
     */
    function save_gbltpl($FORM, $FORMCONTENT, $tempalte_id, $content_id) {
        global $MODULE;
        $content_id = (int)$content_id;
        $tempalte_id = (int)$tempalte_id;

        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $tempalte_id);
        if ($T['tpl_name'] == "") {
            $FORM['tpl_name'] = $this->gen_tpl_name($T); #$this->format_file_name($FORM['description'] . '_' . $tempalte_id);
        }

        update_table(TBL_CMS_TEMPLATES, 'id', $tempalte_id, $FORM);

        $FORMCONTENT['tid'] = $tempalte_id;
        $FORMCONTENT['lang_id'] = ($FORMCONTENT['lang_id'] == 0) ? 1 : $FORMCONTENT['lang_id'];

        $TC_OBJ = $this->db->query_first("SELECT id FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $FORMCONTENT['tid'] . " AND lang_id=" . $FORMCONTENT['lang_id']);
        $content_id = (int)$TC_OBJ['id'];
        if ($content_id > 0) {
            update_table(TBL_CMS_TEMPCONTENT, 'id', $content_id, $FORMCONTENT);
        }
        else {
            $content_id = insert_table(TBL_CMS_TEMPCONTENT, $FORMCONTENT);
        }

        $T_OBJ = $this->db->query_first("SELECT *, T.id as TID FROM " . TBL_CMS_TEMPLATES . " T
				LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $FORMCONTENT['lang_id'] . " AND T.id=TC.tid)
				WHERE T.id=" . $FORMCONTENT['tid'] . "
				");
        $this->TEMPL_OBJ->save_tpl_file($T_OBJ);
        $this->LOGCLASS->addLog('MODIFY', 'system template <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&aktion=edit&id=' . $tempalte_id . '">' .
            $FORM['description'] . '</a>');

        if ($this->gbl_config['nomultilang_systemtemplates'] == 1) {
            $this->TEMPL_OBJ->replicateland($tempalte_id, $FORMCONTENT['lang_id']);
        }

        # Modul verlinkung zu PHP
        $MODUPD = array();
        if ($FORM['modident'] != "") {
            $MODUPD['php'] = $MODULE[$FORM['modident']]['php'];
            update_table(TBL_CMS_TEMPLATES, 'id', $tempalte_id, $MODUPD);
        }

        # Backup
        $BACKUP = new backup_class();
        $BACKUP->add($FORMCONTENT['content'], 'SYSTPL', $tempalte_id, $FORMCONTENT['lang_id']);
        $this->allocate_memory($BACKUP);

        # set tpl_name connection
        $this->set_tpl_name_in_content_table();

        # fix tpl_name to reading friendly
        $this->fix_tpl_name($tempalte_id);

        return $tempalte_id;
    }

    /**
     * gbltpl_class::cmd_replicate_lang()
     * 
     * @return
     */
    function cmd_replicate_lang() {
        $langid = (int)$_POST['langid'];
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE id<>" . $langid);
        while ($row = $this->db->fetch_array_names($result)) {
            $result2 = $this->db->query("SELECT *, C.id AS CONID, T.id AS TID 
             FROM " . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT . " C ON (T.id=C.tid AND C.lang_id=" . $row['id'] . ")
             WHERE T.gbl_template=1 ORDER BY T.id");
            while ($TOUPDATE = $this->db->fetch_array_names($result2)) {
                $ORGTEMPALTE = $this->db->query_first("SELECT C.* 
             FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C
             WHERE T.id=C.tid AND T.gbl_template=1 AND C.lang_id='" . $langid . "' AND T.id=" . $TOUPDATE['TID'] . " LIMIT 1");
                $UPDATE = $ORGTEMPALTE;
                $UPDATE['lang_id'] = $TOUPDATE['lang_id'];
                unset($UPDATE['id']);
                foreach ($UPDATE as $key => $value)
                    $UPDATE[$key] = $this->db->real_escape_string($value);

                if ($TOUPDATE['CONID'] > 0) {
                    #  echo $TOUPDATE['TID'].'<br>';
                    update_table(TBL_CMS_TEMPCONTENT, 'id', $TOUPDATE['CONID'], $UPDATE);
                }
                else {
                    # echo 'ADDED '.$TOUPDATE['TID'].'<br>';
                    insert_table(TBL_CMS_TEMPCONTENT, $UPDATE);
                }
            }
        }
        include_once (CMS_ROOT . 'admin/inc/update.class.php');
        $upt_obj = new upt_class();
        $upt_obj->rewriteSmartyTPL();
        $this->ej();
    }

    /**
     * gbltpl_class::cmd_genmeta()
     * 
     * @return
     */
    function cmd_genmeta() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id='" . $_POST['conid'] . "' LIMIT 1");
        header('content-type: text/html; charset=utf-8');
        echo formatMeta(kf::gen_plain_text_content($FORM['content'], $FORM['lang_id']));
        die();
    }

    /**
     * gbltpl_class::cmd_genmetatitle()
     * 
     * @return
     */
    function cmd_genmetatitle() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id='" . $_POST['conid'] . "' LIMIT 1");
        header('content-type: text/html; charset=utf-8');
        echo $gbl_config['opt_site_title'] . ' ' . $FORM['linkname'];
        die();
    }

    /**
     * gbltpl_class::cmd_genkeys()
     * 
     * @return
     */
    function cmd_genkeys() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id='" . $_POST['conid'] . "' LIMIT 1");
        header('content-type: text/html; charset=utf-8');
        echo kf::gen_meta_keywords($FORM['content'], 0, ',', $_POST['uselang']);
        die();
    }


    /**
     * gbltpl_class::cmd_a_save()
     * 
     * @return
     */
    function cmd_a_save() {
        $id = $this->save_gbltpl($_POST['FORM_TEMPLATE'], $_POST['FORMCON'], $_POST['tid'], $_POST['id']);
        # Permissions setzen
        if (isset($_POST['CUSTGROUP']))
            $this->set_permission_for_groups($_POST['tid'], $_POST['CUSTGROUP']);
        $this->ej();
    }

    /**
     * gbltpl_class::cmd_replicateland()
     * 
     * @return
     */
    function cmd_replicateland() {
        $this->TEMPL_OBJ->replicateland($this->TCR->GET['id'], $this->TCR->GET['uselang']);
        #$this->TCR->set_url_tag('configid');
        #$this->TCR->set_url_tag('id');
        #$this->TCR->reset_cmd('edit');
        #$this->TCR->set_url_tag('uselang');
        #$this->TCR->add_msg('{LBLA_SAVED}');
        $this->ej();
    }

    /**
     * gbltpl_class::load_hta()
     * 
     * @param mixed $tid
     * @return
     */
    function load_hta($tid) {
        global $HTA_CLASS_CMS;
        $HTAF = $this->db->query_first("SELECT * FROM " . TBL_CMS_HTA . " WHERE hta_tid=" . (int)$tid . " LIMIT 1");
        for ($i = 1; $i <= $HTAF['hta_starcount']; $i++)
            $stars[] = $HTAF['hta_var' . $i];
        $HTAF['link'] = $HTA_CLASS_CMS->genLink($HTAF['id'], (array )$stars);
        $HTAF['htad'] = array(
            ',',
            '-',
            '_',
            '/');
        $HTAF['hta_fileext'] = str_replace('.', '', $HTAF['hta_fileext']);
        for ($i = 1; $i <= 3; $i++) {
            $HTAF['vars'][$i]['vars'] = $HTAF['hta_var' . $i];
            $HTAF['vars'][$i]['delimiter'] = $HTAF['hta_delimeter' . $i];
            $HTAF['vars'][$i]['vartype'] = $HTAF['hta_vartype' . $i];
        }
        $this->smarty->assign('HTAF', $HTAF);
        return $HTAF;
    }

    /**
     * gbltpl_class::cmd_save_hta()
     * 
     * @return
     */
    function cmd_save_hta() {
        global $HTA_CLASS_CMS;
        $this->TCR->add_url_tag('id', $this->TCR->POST['tid']);
        $this->TCR->reset_cmd('edit');
        $this->TCR->set_url_tag('configid');


        $HTAF = $this->TCR->POST['HTAF'];
        $id = (int)$this->TCR->POST['id'];
        $HTAF['hta_prefix'] = strtolower(ereg_replace("[^a-zA-Z0-9-]", "", $HTAF['hta_prefix']));
        $HTAF['hta_starcount'] = 0;
        $HTAF['hta_allowaddtags'] = (int)$HTAF['hta_allowaddtags'];
        $HTAF['hta_fileext'] = '.' . str_replace('.', '', $HTAF['hta_fileext']);
        if ($HTAF['hta_add'][0] != '&')
            $HTAF['hta_add'] = '&' . $HTAF['hta_add'];
        if ($HTAF['hta_fileext'] == ".")
            $HTAF['hta_fileext'] = "";
        $href = "";
        $ident = 0;
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($HTAF['hta_var' . $i]) && $HTAF['hta_vartype' . $i] <= 2) {
                $HTAF['hta_starcount']++;
                $ident++;
                if ($HTAF['hta_vartype' . $i] == 1) {
                    $href .= '&' . $HTAF['hta_var' . $i] . '=$' . $ident;
                }
            }
        }
        $HTAF['hta_fix'] = 1;
        if (substr($HTAF['hta_add'], 0, 1) != '&' && count($HTAF['hta_add']) > 1)
            $HTAF['hta_add'] = '&' . $HTAF['hta_add'];
        if (substr($href, 0, 1) != '&')
            $href = '&' . $href;
        $HTAF['hta_ref'] = 'index.php?page=' . $this->TCR->POST['tid'] . $HTAF['hta_add'] . $href . (($HTAF['hta_allowaddtags'] == 1) ? '&%{QUERY_STRING} [L]' : "");
        $HTAF['hta_ref'] = str_replace('&&', '&', $HTAF['hta_ref']);
        if ($id > 0) {
            update_table(TBL_CMS_HTA, 'id', $id, $HTAF);
        }
        else {
            if (get_data_count(TBL_CMS_HTA, 'id', "hta_prefix='" . $HTAF['hta_prefix'] . "'") > 0) {
                $this->TCR->add_msge('Prefix existiert bereits. Bitte verwenden Sie einen anderen.');
                return;
            }
            $HTAF['hta_locked'] = 0;
            $id = insert_table(TBL_CMS_HTA, $HTAF);
            $HU = array('hta_tmpllink' => $HTA_CLASS_CMS->genAscciiJoker($id));
            update_table(TBL_CMS_HTA, 'id', $id, $HU);
        }

        $this->TCR->add_msg('{LBLA_SAVED}');
        include_once (CMS_ROOT . 'admin/inc/htaedit.class.php');
        $HTA = new htaedit_class();
        $HTA->buildHTACCESS();
    }

    /**
     * gbltpl_class::load_inuse()
     * 
     * @param mixed $id
     * @return
     */
    function load_inuse($id) {
        $id = (int)$id;
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id);
        $result = $this->db->query("SELECT C.*, T.description,T.id AS TID,T.c_type AS TT,L.bild, T.gbl_template  
             FROM  " . TBL_CMS_LANG . " L," . TBL_CMS_TEMPLATES . " T , " . TBL_CMS_TEMPCONTENT . " C               
             WHERE L.id=C.lang_id AND T.id=C.tid 
             AND C.content LIKE '%" . $T['tpl_name'] . ".tpl%'
             ORDER BY T.description");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->TEMPL_OBJ->list_opt($row);
            $tplinuse[] = $row;
        }
        $tplinuse = (array )$tplinuse;
        $this->smarty->assign('TPLINUSE', $tplinuse);

        # connected sites
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $id . " AND lang_id=" . $this->langid);
        preg_match_all("=<% include file(.*)%>=siU", $T['content'], $tpl_tag);
        preg_match_all("=<%include file(.*)%>=siU", $T['content'], $tpl_tag2);

        $founds = array_merge($tpl_tag[0], $tpl_tag2[0]);
        foreach ($founds as $key => $wert) {
            $wert = strtolower($wert);
            $rep = array(
                '<% include file="',
                '<%include file="',
                '%>',
                '<%',
                '.tpl',
                '"',
                ' ');
            $tpl_name = str_replace($rep, "", $wert);
            $sql .= (($sql != "") ? " OR " : "") . "T.tpl_name='" . $tpl_name . "'";
        }
        if ($sql != "") {
            $result = $this->db->query("SELECT C.*, T.description,T.id AS TID,T.c_type AS TT,L.bild, T.gbl_template  
             FROM  " . TBL_CMS_LANG . " L," . TBL_CMS_TEMPLATES . " T , " . TBL_CMS_TEMPCONTENT . " C               
             WHERE L.id=C.lang_id AND T.id=C.tid 
             AND (" . $sql . ")
             AND C.lang_id=" . $this->langid . "
             ORDER BY T.description");
            while ($row = $this->db->fetch_array_names($result)) {
                $row = $this->TEMPL_OBJ->list_opt($row);
                $tplinuseinside[] = $row;
            }
        }
        $tplinuseinside = (array )$tplinuseinside;
        $this->smarty->assign('TPLINUSEINSIDE', $tplinuseinside);
    }

    /**
     * gbltpl_class::load_backups()
     * 
     * @param mixed $tid
     * @return
     */
    function load_backups($tid) {
        $BACKUP = new backup_class();
        $this->GBLTPL['backups'] = $BACKUP->load_backups($tid);
        keimeno_class::allocate_memory($BACKUP);
    }

    /**
     * gbltpl_class::cmd_showbackup()
     * 
     * @return
     */
    function cmd_showbackup() {
        $BACKUP = new backup_class();
        $this->GBLTPL['backup'] = $BACKUP->get_backup_by_id($_GET['id']);
        $this->GBLTPL['backup']['date'] = date('d.m.Y H:i:s', $this->GBLTPL['backup']['b_time']);
        $this->parse_to_smarty();
        keimeno_class::allocate_memory($BACKUP);
        kf::echo_template('gbltemplate.showbackup');
    }

    /**
     * gbltpl_class::cmd_loadbackups()
     * 
     * @return
     */
    function cmd_loadbackups() {
        $this->load_backups($_GET['tid']);
        $this->parse_to_smarty();
        kf::echo_template('gbltemplate.backups');
    }

    /**
     * gbltpl_class::load_gbltpl()
     * 
     * @return
     */
    function load_gbltpl() {
        $this->load_gbltemplate($_GET['id']);
        $this->load_inuse($_GET['id']);
        $this->TEMPL_OBJ->build_lang_select();
        $this->load_hta($_GET['id']);
    }

    /**
     * gbltpl_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $this->load_gbltpl();
    }

    /**
     * gbltpl_class::cmd_restorebackup()
     * 
     * @return
     */
    function cmd_restorebackup() {
        $BACKUP = new backup_class();
        $BACKUP->restore($_POST['id']);
        keimeno_class::allocate_memory($BACKUP);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * gbltpl_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('GBLTPL', $this->GBLTPL);
    }


    /**
     * gbltpl_class::cmd_load_gbltpl_tree()
     * 
     * @return
     */
    function cmd_load_gbltpl_tree() {
        global $MODULE;
        $arr = $this->dao->load_system_templates();
        foreach ($MODULE as $key => $modul) {
            if ($modul['module_name'] != "" && $modul['iscore'] != "true" && $modul['onlyadmin'] != "true") {
                $modul['children'] = array();
                $modul['haschildren'] = 1;
                $modul['mod_name'] = ucfirst($modul['module_name']);
                $this->GBLTPL['gbltpltree'][$modul['id']] = $modul;
            }

        }
      
        foreach ($arr as $key => $row) {
            if ($MODULE[$row['modident']]['module_name'] != "") {
                $MODULE[$row['modident']]['mod_name'] = ucfirst($MODULE[$row['modident']]['module_name']);
                $row['ischild'] = 1;

                if ($this->GBLTPL['gbltpltree'][$row['modident']]['id'] == "") {
                    $this->GBLTPL['gbltpltree'][$row['modident']] = $MODULE[$row['modident']];
                }
                $this->GBLTPL['gbltpltree'][$row['modident']]['children'][] = $row;
                $this->GBLTPL['gbltpltree'][$row['modident']]['haschildren'] = 1;
            }
            if ($row['modident'] == "") {
                $modident = 'generell';
                $MOD = array(
                    'id' => 'system',
                    'module_name' => 'System',
                    'mod_name' => 'System');

                if ($this->GBLTPL['gbltpltree'][$modident]['id'] == "")
                    $this->GBLTPL['gbltpltree'][$modident] = $MOD;
                $this->GBLTPL['gbltpltree'][$modident]['children'][] = $row;
                $this->GBLTPL['gbltpltree'][$modident]['haschildren'] = 1;
            }
        }
       
        $this->GBLTPL['gbltpltree'] = $this->sort_multi_array($this->GBLTPL['gbltpltree'], 'mod_name', SORT_ASC, SORT_STRING);
        $this->parse_to_smarty();
        kf::echo_template('gbltemplate.orgatree');
    }

    /**
     * gbltpl_class::cmd_load_gbltpl_ax()
     * 
     * @return
     */
    function cmd_load_gbltpl_ax() {
        global $GBL_LANGID, $LANGS, $MODULE;
        $this->load_gbltpl();
        $LNGOBJ = new language_class();
        $this->smarty->assign('langselect', $LNGOBJ->build_lang_select_smarty($_GET['uselang']));
        $M = new modules_class();
        $M->load_admin_translation($GBL_LANGID, $LANGS, $MODULE);
        foreach ($M->ADMIN_MOD_TRANSPAGES as $key => $mod)
            $M->ADMIN_MOD_TRANSPAGES[$key]['module_name'] = ucfirst($mod['mod_name']);

        if (count($M->ADMIN_MOD_TRANSPAGES) > 0)
            $M->ADMIN_MOD_TRANSPAGES = $this->sort_multi_array($M->ADMIN_MOD_TRANSPAGES, 'module_name', SORT_ASC, SORT_STRING);

        $this->smarty->assign('mod_list', $M->ADMIN_MOD_TRANSPAGES);
        unset($M);

        $this->parse_to_smarty();
        kf::echo_template('gbltemplates');
    }

    /**
     * gbltpl_class::cmd_save_script_att()
     * 
     * @return
     */
    function cmd_save_script_att() {
        /* if (!is_dir($this->js_path))
        mkdir($this->js_path, 0775);
        $TPL = $this->db->query_first("SELECT * FROM  " . TBL_CMS_TEMPLATES . " WHERE id=" . $_POST['tpl_id']);
        $FORM = $_POST['FORM'];
        $content = trim(stripslashes($FORM['scripttxt']));
        if ($content != "") {
        file_put_contents($this->js_path . 'js_' . $TPL['tpl_name'] . '.js', $content);
        }
        else
        if (file_exists($this->js_path . 'js_' . $TPL['tpl_name'] . '.js'))
        @unlink($this->js_path . 'js_' . $TPL['tpl_name'] . '.js');
        */
        update_table(TBL_CMS_TEMPLATES, 'id', $_POST['tpl_id'], $_POST['FORM']);
        $this->ej();
    }

}
