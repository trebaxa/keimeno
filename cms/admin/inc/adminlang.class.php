<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


define('LNG_ROOT', CMS_ROOT . 'admin/tpl/lngpacks/');

class adminlang_class extends keimeno_class {

    var $lng_path = '';

    /**
     * adminlang_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * adminlang_class::interpreter()
     * 
     * @return
     */
    function interpreter($method) {
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    /**
     * adminlang_class::cmd_at_download()
     * 
     * @return
     */
    function cmd_at_download() {
        $file = CMS_ROOT . 'includes/modules/' . $_GET['mod'] . '/admin/language_' . $_GET['localid'] . '.xml';
        $this->direct_download($file);
    }


    /**
     * adminlang_class::cmd_at_upload_xml()
     * 
     * @return
     */
    function cmd_at_upload_xml() {
        $f = strtolower(basename($_FILES['datei']['name']));
        list($name, $localid_ext) = explode('_', $f);
        list($localid, $ext) = explode('.', $localid_ext);
        $localid = strtolower($localid);
        if (get_data_count(TBL_CMS_LANG_ADMIN, 'id', "local='" . $localid . "'") == 1 && strstr($f, '.xml') && strstr($f, '_')) {
            $lng_xml_pack = $this->lng_path . 'language_' . $localid . '.xml';
            move_uploaded_file($_FILES['datei']['tmp_name'], $lng_xml_pack);
            $this->TCR->set_just_turn_back();
        }
        else {
            $this->msge('{LBL_INVALIDFILE}');
            $this->TCR->set_just_turn_back();
        }
    }


    /**
     * adminlang_class::at_load_xml()
     * 
     * @return
     */
    function at_load_xml() {
        $needed_jokers = array();
        //alle joker einsammeln aus allen Sprachdateien
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE 1 ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $lng_xml_pack = $this->lng_path . 'language_' . $row['local'] . '.xml';
            $local_arr[$row['local']] = $row['local'];
            if (file_exists($lng_xml_pack)) {
                $xml = @simplexml_load_file($lng_xml_pack);
                if ($xml) {
                    foreach ($xml->replacements as $joker) {
                        $needed_jokers[trim(strval($joker->replacement))][$row['local']] = array(
                            'joker_sec' => $this->format_joker(strval($joker->replacement)),
                            'value' => '',
                            );
                    }
                }
            }
        }
        foreach ($needed_jokers as $joker => $lng) {
            foreach ($local_arr as $localid) {
                $needed_jokers[$joker][$localid] = array(
                    'joker_sec' => $joker,
                    'value' => '',
                    );
            }
            ksort($needed_jokers[$joker]);
        }
        #echoarr($needed_jokers);die;
        // einzelene Sprachen laden
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE 1 ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['local'] != "") {
                $row['local'] = strtolower($row['local']);
                $needed_jokers = $this->at_load_xml_by_localid($row['local'], $needed_jokers);
            }
        }
        ksort($needed_jokers);
        $this->ATRANS['all_jokers'] = $needed_jokers;
    }

    /**
     * adminlang_class::at_load_xml_by_localid()
     * 
     * @return
     */
    function at_load_xml_by_localid($local_id, $needed_jokers) {
        if ($local_id != "") {
            $local_id = strtolower($local_id);
            $merged_array = $used_jokers = array();
            $lng_xml_pack = $this->lng_path . 'language_' . $local_id . '.xml';
            if (file_exists($lng_xml_pack)) {
                $xml = @simplexml_load_file($lng_xml_pack);
                if ($xml) {
                    foreach ($xml->replacements as $joker) {
                        $needed_jokers[trim(strval($joker->replacement))][$local_id] = array(
                            'joker' => trim(strval($joker->replacement)),
                            'vlen' => strlen(trim(strval($joker->value))),
                            'joker_sec' => $this->format_joker(strval($joker->replacement)),
                            'value' => $this->format_value($joker->value),
                            'icons' => array( #kf::gen_del_icon_reload(trim(strval($joker->replacement)),'at_delete_joker','{LBL_CONFIRM}','&orgaktion=' . $_GET['aktion'])
                                    kf::gen_del_icon(trim(strval($joker->replacement)), false, 'at_delete_joker')));
                    }
                }
            }
            #	$merged_array = array_merge($needed_jokers, $used_jokers);
        }
        return $needed_jokers;
    }

    /**
     * adminlang_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->ATRANS['lng_path'] = $this->lng_path;
        $this->ATRANS['mod'] = $_SESSION['at_mod'];
        $this->smarty->assign('ATRANS', $this->ATRANS);
    }


    /**
     * adminlang_class::cmd_at_importmissing()
     * 
     * @return
     */
    function cmd_at_importmissing() {
        $this->at_import_missing_languagefiles();
        $this->TCR->set_just_turn_back();
    }


    /**
     * adminlang_class::at_import_missing_languagefiles()
     * 
     * @return
     */
    function at_import_missing_languagefiles() {
        $this->curl_get_data_to_file(UPDATE_SERVER . 'langxml.tar.gz', CMS_ROOT . 'langxml.tar.gz');
        # unpack without overwrite
        # exec('tar -C ../ -kxvvzf ../langxml.tar.gz');
        # @unlink(CMS_ROOT . 'langxml.tar.gz');
        self::untar_archive(CMS_ROOT . 'langxml.tar.gz', CMS_ROOT . 'includes/modules');
    }

    /**
     * adminlang_class::import_missing()
     * 
     * @return
     */
    function import_missing() {
        $this->curl_get_data_to_file(UPDATE_SERVER . 'langxml.tar.gz', CMS_ROOT . 'langxml.tar.gz');
        mkdir(CMS_ROOT . 'cache/includes', 0775);
        mkdir(CMS_ROOT . 'cache/includes/modules', 0775);
        self::untar_archive(CMS_ROOT . 'langxml.tar.gz', CMS_ROOT . 'cache/includes/modules');
        #  exec('tar -C ../cache/ -kxvvzf ../langxml.tar.gz');
        # @unlink(CMS_ROOT . 'langxml.tar.gz');
        $this->implement_missing_jokers(CMS_ROOT . 'cache/includes/modules/');
        $this->delete_dir_with_subdirs(CMS_ROOT . 'cache/includes/');
    }

    /**
     * adminlang_class::complete_lang_missing()
     * 
     * @return void
     */
    function complete_lang_missing() {
        try {
            $tar_file_gz = CMS_ROOT . 'langxml.tar.gz';
            $this->curl_get_data_to_file(UPDATE_SERVER . 'langxml.tar.gz', $tar_file_gz);
            mkdir(CMS_ROOT . 'cache/includes', 0775);
            mkdir(CMS_ROOT . 'cache/includes/modules', 0775);
            $phar = new PharData(CMS_ROOT . 'langxml.tar.gz');
            $phar->decompress();
            unset($phar);
            $tarfile = str_replace('.gz', '', $tar_file_gz);
            $phar_tar = new PharData($tarfile);
            $phar_tar->extractTo(CMS_ROOT . 'cache/includes/modules', null, true);
            $phar_tar->extractTo(CMS_ROOT . 'includes/modules', null, true);
            @unlink($tarfile);
            @unlink($tar_file_gz);
        }
        catch (Exception $e) {
            echo $e->getMessage();
            if ($delete == true) {
                @unlink($tarfile);
                @unlink($tar_file_gz);
            }
        }
        $this->implement_missing_jokers(CMS_ROOT . 'cache/includes/modules/');
        $this->delete_dir_with_subdirs(CMS_ROOT . 'cache/includes/');
    }

    /**
     * adminlang_class::at_import_missing_jokers()
     * 
     * @return
     */
    function at_import_missing_jokers() {
        $this->import_missing();
        $this->TCR->set_just_turn_back();
    }

    /**
     * adminlang_class::implement_missing_jokers()
     * 
     * @return
     */
    function implement_missing_jokers($dir) {
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            $k = 0;
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir . $file)) {
                        if (!strstr($file, '.xml')) {
                            continue;
                        }
                        $this->compare_and_update($dir . $file);
                    }
                    else {
                        $this->implement_missing_jokers($dir . $file);
                    }
                }
            }
            closedir($openDir);
        }
    }

    /**
     * adminlang_class::replacement_exists()
     * 
     * @return
     */
    function replacement_exists($rkey, $ta_arr) {
        if (!is_array($ta_arr))
            return false;
        foreach ($ta_arr as $key => $replacement) {
            if ($rkey == $replacement['REPLACEMENT'])
                return TRUE;
        }
        return FALSE;
    }

    /**
     * adminlang_class::format_joker()
     * 
     * @return
     */
    function format_joker($joker) {
        $joker = trim(strtoupper(strip_tags($joker)));
        $joker = str_replace(array(
            '{',
            '}',
            '.',
            ','), '', $joker);
        return preg_replace("/[^0-9a-zA-Z_]/", "", $joker);
    }

    /**
     * adminlang_class::format_value()
     * 
     * @return
     */
    function format_value($v) {
        return trim(stripslashes(strval($v)));
    }

    /**
     * adminlang_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        $parts = explode('-', $this->TCR->GET['id']);
        $this->delete_link((int)$parts[1]);
        die;
    }

    /**
     * adminlang_class::cmd_at_delete_joker()
     * 
     * @return
     */
    function cmd_at_delete_joker() {
        $id = $_GET['ident'];
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE approval=1 ORDER BY s_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $xml_file = $this->lng_path . 'language_' . $row['local'] . '.xml';
            if (file_exists($xml_file)) {
                $ta_arr = $this->parse_xmlfile_to_array($xml_file);
                if (is_array($ta_arr['LNGPACK'])) {
                    foreach ($ta_arr['LNGPACK'] as $tkey => $replacement) {
                        if ($replacement['REPLACEMENT'] != $id)
                            $lng_arr[$replacement['REPLACEMENT']] = $replacement['VALUE'];
                    }
                }
                $xmlcode = $this->build_xml_string($lng_arr);
                file_put_contents($xml_file, $xmlcode);
            }
        }
        $this->ej();
    }

    /**
     * adminlang_class::at_add_jokers()
     * 
     * @return
     */
    function at_add_jokers($FORM) {
        if (is_array($FORM)) {
            foreach ($FORM as $key => $joker) {
                if ($_POST['he'] == 1)
                    $joker = utf8_encode(html_entity_decode($joker));
                $value = $this->format_value($joker);
                if (!strstr($joker, 'LA_') && !strstr($joker, 'LBL_')) {
                    $joker = $this->format_joker(html_entity_decode($joker));
                    $joker = str_replace('_', '', $this->format_file_name($joker));
                    if (strlen($joker) > 21)
                        $joker = substr($joker, 0, 21);
                }
                if ((int)$key == 0 && !is_numeric($key))
                    $joker = $key;
                if ($joker != "") {
                    if (!strstr($joker, 'LA_') && !strstr($joker, 'LBL_'))
                        $joker = 'LA_' . $joker;
                    $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG_ADMIN . " WHERE approval=1 ORDER BY s_order");
                    while ($row = $this->db->fetch_array_names($result)) {
                        $lng_xml_pack = $this->lng_path . 'language_' . $row['local'] . '.xml';
                        if (file_exists($lng_xml_pack))
                            $ta_arr = $this->parse_xmlfile_to_array($lng_xml_pack);
                        if (!is_array($ta_arr['LNGPACK'])) {
                            unset($ta_arr);
                            $ta_arr['LNGPACK'] = array();
                        }
                        if (!$this->replacement_exists(strval($joker), $ta_arr['LNGPACK'])) {
                            $ta_arr['LNGPACK'][] = array('REPLACEMENT' => strval($joker), 'VALUE' => $value);
                        }
                        if (is_array($ta_arr['LNGPACK'])) {
                            foreach ($ta_arr['LNGPACK'] as $tkey => $replacement) {
                                $lng_arr[$replacement['REPLACEMENT']] = $replacement['VALUE'];
                            }
                            $xmlcode = $this->build_xml_string($lng_arr);
                            file_put_contents($lng_xml_pack, $xmlcode);
                        }
                    }
                }
            }
        }
    }

    /**
     * adminlang_class::at_add_translation()
     * 
     * @return
     */
    function at_add_translation() {
        $this->at_add_jokers($_POST['FORM']);
        if ($_POST['MULTIADD'] != "") {
            $FORM = explode("\n", trim($_POST['MULTIADD']));
        }
        $this->at_add_jokers($FORM);
        return array(
            'status' => true,
            'msge' => '',
            'msg' => '{LBLA_SAVED}',
            'redirect' => $_SERVER['PHP_SELF'] . '?aktion=' . $_POST['orgaktion'] . '&epage=' . $_POST['epage']);
    }

    /**
     * adminlang_class::compare_and_update()
     * 
     * @return
     */
    function compare_and_update($imp_file) {
        $target_file = str_replace('cache/', '', $imp_file);
        #	die($target_file);
        if (file_exists($target_file)) {
            $ta_arr = $this->parse_xmlfile_to_array($target_file);
            $ta_arr = $ta_arr['LNGPACK'];
            $org_arr = $this->parse_xmlfile_to_array($imp_file);
            $org_arr = $org_arr['LNGPACK'];
            $added = false;
            foreach ($org_arr as $key => $replacement) {
                if (!$this->replacement_exists($replacement['REPLACEMENT'], $ta_arr)) {
                    $added = true;
                    $ta_arr[] = array('REPLACEMENT' => $replacement['REPLACEMENT'], 'VALUE' => $replacement['VALUE']);
                }
            }
            if ($added == TRUE) {
                foreach ($ta_arr as $key => $replacement) {
                    $lng_arr[$replacement['REPLACEMENT']] = $replacement['VALUE'];
                }
                $xmlcode = $this->build_xml_string($lng_arr);
                file_put_contents($target_file, $xmlcode);
            }
        }

    }

    /**
     * adminlang_class::build_xml_string()
     * 
     * @return
     */
    function build_xml_string($lng_arr) {
        $xmlcode = '<lngpack>
 ';
        $k = 0;
        if (is_array($lng_arr)) {
            foreach ($lng_arr as $joker => $value) {
                $k++;
                $xmlcode .= '<replacements id="' . $k . '">
<replacement>' . $this->format_joker($joker) . '</replacement>
	<value><![CDATA[' . $this->format_value($value) . ']]></value>
</replacements>
';
            }
        }
        $xmlcode .= '</lngpack>';
        return $xmlcode;
    }

    /**
     * adminlang_class::save_arr_to_xml()
     * 
     * @return
     */
    function save_arr_to_xml($lng_table) {
        if (is_array($lng_table)) {
            foreach ($lng_table as $localid => $lng_arr) {
                $target_file = $this->lng_path . 'language_' . $localid . '.xml';
                $xmlcode = $this->build_xml_string($lng_arr);
                file_put_contents($target_file, $xmlcode);
            }
        }
    }

    /**
     * adminlang_class::cmd_updatexml()
     * 
     * @return
     */
    function cmd_updatexml() {
        $lng_table = $_GET['ATLNG'];
        if (is_array($lng_table)) {
            foreach ($lng_table as $localid => $lng_arr) {
                $post_key = key($lng_arr);
                $pos_val = $lng_arr[$post_key];
                $target_file = $this->lng_path . 'language_' . $localid . '.xml';
                $ta_arr = $this->parse_xmlfile_to_array($target_file);
                $ta_arr = $ta_arr['LNGPACK'];
                foreach ($ta_arr as $key => $replacement) {
                    if ($replacement['REPLACEMENT'] == $post_key) {
                        $ta_arr[$key] = array('REPLACEMENT' => $replacement['REPLACEMENT'], 'VALUE' => $pos_val);
                        break;
                    }
                }
            }
            $xml_arr_for_save = array();
            foreach ($ta_arr as $key => $row) {
                $xml_arr_for_save[$row['REPLACEMENT']] = $row['VALUE'];
            }
            unset($ta_arr);
            $xmlcode = $this->build_xml_string($xml_arr_for_save);
            file_put_contents($target_file, $xmlcode);
        }
        $this->hard_exit();
    }


    /**
     * adminlang_class::import()
     * 
     * @return
     */
    function import() {
        $result = $this->db->query("SELECT language,langarray FROM " . TBL_CMS_LANG_ADMIN . " ORDER BY s_order");
        while ($row = $this->db->fetch_array($result)) {
            $lang_sets = explode('!#!', $row[langarray]);
            foreach ($lang_sets as $langset) {
                $lang_row = explode('!:!', $langset);
                $values[$lang_row[0]][$row['language']] = $lang_row[1];
            }
        }
        unset($result);
        $this->xml_fname = 'global_language_pack';
        foreach ($values as $replacement => $key) {
            $FORM['de'][$replacement] = $key[1];
            $FORM['en'][$replacement] = $key[2];

        }
        foreach ($FORM as $localid => $F) {
            $lng_xml_pack = LNG_ROOT . 'language_' . $this->xml_fname . '_' . $localid . '.xml';
            #$ta_arr = $this->parse_xmlfile_to_array($lng_xml_pack);
            $ta_arr = array();
            if (!is_array($ta_arr['LNGPACK'])) {
                unset($ta_arr);
                $ta_arr['LNGPACK'] = array();
            }
            foreach ($F as $joker => $value) {
                if (!$this->replacement_exists(strval($joker), $ta_arr['LNGPACK'])) {
                    $ta_arr['LNGPACK'][] = array('REPLACEMENT' => $joker, 'VALUE' => $value);
                }
            }
            if (is_array($ta_arr['LNGPACK'])) {
                foreach ($ta_arr['LNGPACK'] as $tkey => $replacement) {
                    $lng_arr[$replacement['REPLACEMENT']] = $replacement['VALUE'];
                }
                #echoarr($lng_arr);die;
                $xmlcode = $this->build_xml_string($lng_arr);
                file_put_contents($lng_xml_pack, $xmlcode);
            }
        }
    }


    /**
     * adminlang_class::translate()
     * 
     * @return
     */
    function translate(&$html, $localid, $epage = '') {
        $input = array();
        $output = array();
        $fname = str_replace('.inc', '', $epage);
        $lng_xml_pack = LNG_ROOT . 'language_' . $fname . '_' . $localid . '.xml';

        if (file_exists($lng_xml_pack)) {
            $ta_arr = $this->parse_xmlfile_to_array($lng_xml_pack);
            if (is_array($ta_arr['LNGPACK'])) {
                foreach ($ta_arr['LNGPACK'] as $key => $replacement) {
                    $input[] = '{' . $replacement['REPLACEMENT'] . '}';
                    $output[] = $replacement['VALUE'];
                }
            }
        }
        $html = str_replace($input, $output, $html);
    }

    /**
     * adminlang_class::translate_globalpack()
     * 
     * @return
     */
    function translate_globalpack(&$html, $localid) {
        $this->input = (array )$this->input;
        $this->output = (array )$this->output;
        $lng_xml_pack = CMS_ROOT . 'includes/modules/global_admintrans/admin/language_' . $localid . '.xml';
        if (file_exists($lng_xml_pack) && count($this->input) == 0) {
            $this->ta_arr = $this->parse_xmlfile_to_array($lng_xml_pack);
            if (is_array($this->ta_arr['LNGPACK'])) {
                foreach ($this->ta_arr['LNGPACK'] as $key => $replacement) {
                    $this->input[] = '{' . $replacement['REPLACEMENT'] . '}';
                    $this->output[] = $replacement['VALUE'];
                }
            }
        }
        $html = str_replace($this->input, $this->output, $html);
        return $html;
    }

}
