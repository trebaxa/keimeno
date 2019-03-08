<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class langedit_class extends keimeno_class {

    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        if ($_REQUEST['admin'] == 'no' || $_GET['admin'] == 'no') //$_GET wegen AJAX
            {
            $this->WORKING_TAB_LANG = TBL_CMS_LANG;
        }
        else {
            $this->WORKING_TAB_LANG = TBL_CMS_LANG_CUST;
        }
        $this->LANGEDIT['canadd'] = ($this->WORKING_TAB_LANG == TBL_CMS_LANG_CUST);
    }

    function parse_to_smarty() {
        $this->smarty->assign('LANGEDIT', $this->LANGEDIT);
    }

    function format_joker($joker) {
        $joker = trim(strtoupper(strip_tags($joker)));
        $joker = str_replace(array(
            '!:!',
            '!#!',
            '{',
            '}',
            '.',
            ','), '', $joker);
        return preg_replace("/[^0-9a-zA-Z_]/", "", $joker);
    }

    function format_value($value) {
        $value = trim($value);
        $value = str_replace(array(
            '!:!',
            '!#!',
            '{',
            '}'), '', $value);
        return $value;
    }


    function cmd_a_save_name() {
        $_POST['FORM']['local'] = strtolower($_POST['FORM']['local']);
        update_table($this->WORKING_TAB_LANG, 'id', $id, $_POST['FORM']);
        keimeno_class::msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&section=' . $_REQUEST['section'] . '&admin=' . $_REQUEST['admin'] .
            '&aktion=list');
        exit;
    }

    function cmd_updatelang() {
        $lng_table = $_GET['FORM'];
        if (is_array($lng_table)) {
            foreach ($lng_table as $langid => $lng_arr) {
                $post_key = key($lng_arr);
                $pos_val = $lng_arr[$post_key];
                $LANG = $this->db->query_first("SELECT langarray FROM " . $this->WORKING_TAB_LANG . " WHERE id=" . $langid);
                $langarr = unserialize($LANG['langarray']);
                $langarr[$post_key] = $pos_val;
                $this->db->query("UPDATE " . $this->WORKING_TAB_LANG . " SET langarray = '" . $this->db->real_escape_string(serialize($langarr)) . "' WHERE id = '" . $langid .
                    "'", 1);
            }
        }
        $this->hard_exit();
    }


    function cmd_deljoker() {
        $delkey = $_GET['ident'];
        $langq = $this->db->query("SELECT id,post_lang,langarray FROM " . $this->WORKING_TAB_LANG);
        while ($lang = $this->db->fetch_array_names($langq)) {
            $langarr = unserialize($lang['langarray']);
            unset($langarr[$delkey]);
            $this->db->query("UPDATE " . $this->WORKING_TAB_LANG . " SET langarray='" . $this->db->real_escape_string(serialize($langarr)) . "' WHERE id='" . $lang['id'] .
                "'");
            unset($newlang);
        }
        $this->ej();
    }


    function cmd_add_keys() {
        $error = "";
        $result = $this->db->query("SELECT id,langarray FROM " . $this->WORKING_TAB_LANG);
        while ($row = $this->db->fetch_array($result)) {
            $langarr = unserialize($row['langarray']);
            foreach ($_POST['FELD'] as $key => $value) {
                $value = trim($value);
                if ($value != "") {
                    $value = $this->format_value($value);
                    $joker = html_entity_decode($value);
                    $joker = $this->format_joker($joker);
                    $joker = str_replace('_', '', $this->format_file_name($joker));
                    if (strlen($joker) > 21)
                        $joker = substr($joker, 0, 21);
                    $joker = strtoupper('LBL_' . $joker);
                    if (!array_key_exists($joker, $langarr)) {
                        $langarr[$joker] = $value;
                        $this->db->query("UPDATE " . $this->WORKING_TAB_LANG . " SET langarray='" . $this->db->real_escape_string(serialize($langarr)) . "' WHERE id='" . $row['id'] .
                            "'");
                    }
                    else {
                        $error .= "Key $value already exists";
                        break;
                    }

                }
            }
        }
        if (strlen($error) == 0) {
            keimeno_class::msg('{LBLA_SAVED} ');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&section=' . $_REQUEST['section'] . '&admin=' . $_REQUEST['admin']);
        }
        else {
            keimeno_class::msge($error);
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&section=' . $_REQUEST['section'] . '&admin=' . $_REQUEST['admin']);
        }
        exit;
    }


    function cmd_reloadtab() {
        $this->load_table();
        $this->parse_to_smarty();
        kf::echo_template('langedit.table');
    }

    function load_table() {
        // LANG HEADERS
        $result = $this->db->query("SELECT * FROM " . $this->WORKING_TAB_LANG . " ORDER BY s_order");
        $x = 0;
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['bild'])
                $row['flag'] = kf::gen_thumbnail('/images/' . $row['bild'], 20, 20, 0);
            $sort[$x] = $row['id'];
            $x++;
            $this->LANGEDIT['header'][] = $row;
        }
        $this->LANGEDIT['colcount'] = $x;
        $this->LANGEDIT['colcountsub'] = $x + 2;
        // LANG ARR
        $result = $this->db->query("SELECT id,langarray FROM " . $this->WORKING_TAB_LANG . " ORDER BY s_order");
        while ($row = $this->db->fetch_array($result)) {
            $sprache = $row['id'];
            $lang_sets = unserialize($row['langarray']);
            foreach ((array )$lang_sets as $joker => $value) {
                $values[$joker][$sprache] = $value;
            }
        }

        unset($result);
        // ARRAY AUFBAUEN
        if (count($values) > 0) {
            foreach ($values as $keyword => $key) {
                if (strlen($keyword) > 0) {
                    $xkey++;
                    $this->LANGEDIT['lines'][$xkey]['keyword'] = $keyword;

                    $text_encode = '';
                    for ($hex = 0, $_length = strlen($keyword); $hex < $_length; $hex++) {
                        $text_encode .= '&#x' . bin2hex($keyword[$hex]) . ';';
                    }
                    $this->LANGEDIT['lines'][$xkey]['keywordhex'] = $text_encode;
                    for ($i = 0; $i < $x; $i++) {
                        $valwert = $sort[$i];
                        $valkey = $key[$sort[$i]];
                        $this->LANGEDIT['lines'][$xkey]['trans'][] = array(
                            'valwert' => $valwert,
                            'keyword' => $keyword,
                            'valkey' => $valkey);
                    }
                    $this->LANGEDIT['lines'][$xkey]['delicon'] = kf::gen_del_icon($keyword, false, 'deljoker', '', '&admin=' . $_REQUEST['admin']);
                }
            }
        }

    }

    function cmd_download() {
        $LANG = $this->db->query_first("SELECT * FROM " . $this->WORKING_TAB_LANG . " WHERE id=" . (int)$_GET['id']);
        $LANG['langarray'] = utf8_decode($LANG['langarray']);
        $lang_sets = explode('!#!', $LANG['langarray']);
        $fname = CMS_ROOT . 'admin/cache/lang_' . $_REQUEST['admin'] . '_' . $LANG['local'] . '.csv';
        $fp = fopen($fname, 'w');
        foreach ($lang_sets as $langset) {
            $lang_row = explode('!:!', $langset);
            fputcsv($fp, $lang_row, ';', '"');
        }
        fclose($fp);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . basename($fname));
        header('Pragma: no-cache');
        readfile($fname);
        $this->hard_exit();
    }

    function cmd_csvupdate() {
        if (!validate_upload_file($_FILES['datei'])) {
            $this->msge($_SESSION['upload_msge']);
            $this->echo_json_fb('reloadlangtable');
            $this->hard_exit();
        }
        $fname = CMS_ROOT . 'admin/cache/' . strtolower($_FILES['datei']['name']);
        move_uploaded_file($_FILES['datei']['tmp_name'], $fname);
        list($tmp, $adminmode, $local) = explode('_', basename($fname));
        $local = str_replace('.csv', '', $local);
        if ($adminmode == 'no') //$_GET wegen AJAX

            $this->WORKING_TAB_LANG = TBL_CMS_LANG;
        else
            $this->WORKING_TAB_LANG = TBL_CMS_LANG_CUST;
        $LANG = $this->db->query_first("SELECT * FROM " . $this->WORKING_TAB_LANG . " WHERE local='" . $local . "'");
        $lang_sets = explode('!#!', $LANG['langarray']);
        foreach ($lang_sets as $langset) {
            $lang_row = explode('!:!', $langset);
            $values[$lang_row[0]] = $lang_row[1];
        }
        if (($handle = fopen($fname, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $values[$data[0]] = $data[1];
            }
        }
        $newlang = "";
        foreach ($values as $key => $valkey) {
            if ($key != "") {
                if ($newlang != "") {
                    $newlang .= '!#!';
                }
                $newlang .= $key . '!:!' . $this->db->real_escape_string(utf8_encode($valkey));
            }
        }
        $this->db->query("UPDATE " . $this->WORKING_TAB_LANG . " SET langarray='" . $newlang . "' WHERE id='" . (int)$LANG['id'] . "'");
        $this->msg('Update successfull.');
        $this->echo_json_fb('reloadlangtable');
    }
}
