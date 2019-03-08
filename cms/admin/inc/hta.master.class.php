<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class htamaster_class extends keimeno_class {

    var $settings = array();
    var $settings_fix = array();

    /**
     * htamaster_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * htamaster_class::load_sslsites_fe()
     * 
     * @return
     */
    function load_sslsites_fe() {
        $this->settings = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_HTA . " ORDER BY hta_fix,hta_description");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->settings[$row['id']] = $row;
            if ($row['hta_fix'] == 1) {
                $local_path = self::get_local_path();
                $this->settings_fix[$row['hta_tmpllink']] = $local_path . $row['hta_prefix'] . $row['hta_fileext'];
                if ($this->settings[$row['id']]['hta_ssl'] == 1) {
                    if ($this->gblconfig->ssl_forcessl == 1) {
                        $domain_parts = explode('.', HOST);
                        $this->ssl_links[$row['hta_tmpllink']] = self::get_domain_url() . $row['hta_prefix'] . $row['hta_fileext'];
                        if (count($domain_parts) > 2) {
                            $this->ssl_links[$row['hta_tmpllink']] = str_replace('www.', '', $this->ssl_links[$row['hta_tmpllink']]);
                        }
                    }
                }
            }
            if ($this->settings[$row['id']]['hta_ssl'] == 1 && $row['hta_fix'] == 1) {
                list($tmp, $page_number) = explode('page=', $row['hta_ref']);
                $this->SSL_SITES_IDS[] = (int)$page_number;
            }
        }
        $this->smarty->assign('HTA_CMSFIXLINKS', $this->settings_fix);
        $this->smarty->assign('HTA_CMSSSLLINKS', $this->ssl_links);
    }


    /**
     * htamaster_class::genAscciiJoker()
     * 
     * @return
     */
    function genAscciiJoker($id) {
        $abc = "";
        $zahl_arr = str_split($id);
        foreach ($zahl_arr as $zkey => $zahl)
            $abc .= chr($zahl + 65); // +65, um im ABC zu landen
        return $abc . '_URL';
    }

    /**
     * htamaster_class::formatLink()
     * 
     * @return
     */
    function formatLink($string = '') {
        $string = rawurldecode(trim(strtolower(utf8_decode($string))));
        $string = str_replace(" ", "-", $string);

        //html-codierung entfernen
        $clean_string = html_entity_decode($string);

        //zeichen ersetzen: array
        $replace = array(
            "д" => "Ae",
            "ж" => "Oe",
            "э" => "Ue",
            "ъ" => "ss",
            "Д" => "ae",
            "Ж" => "oe",
            "Э" => "ue",
            "▄" => "oe",
            "°" => "oe",
            "ф" => "ae",
            "ч" => "th",
            "Ф" => "ae",
            "Ч" => "th",
            "п" => "dh",
            "П" => "dh",
            "│" => "ue",
            "└" => "ae",
            "▌" => "ae",
            "▌Ь" => "i",
            "■" => "oe",
            "≥" => "oe",
            " " => "ue",
            "ф╕" => "e",
            "фП" => "i",
            "г" => "oe",
            "гП" => "i",
            "фо" => "ae",
            "гY" => "ss",
            "го" => "ae",
            "фY" => "ss",
            "+" => "-",
            "&" => "-",
            " - " => "-",
            " " => "-",
            '- -' => '');
        $clean_string = strtr($clean_string, $replace);
        $replace = array('- -' => '', '--' => '-');
        $clean_string = strtr($clean_string, $replace);
        while (strstr($clean_string, "--"))
            $clean_string = str_replace("--", "-", $clean_string);

        //verbliebene fremdzeichen mit leerzeichen ersetzen
        $clean_string = preg_replace("/[^a-zA-Z0-9*_ .,-\/]/", "", $clean_string);
        //mehrfache leerzeichen entfernen
        $clean_string = preg_replace("/( +)/", " ", $clean_string);

        return strtolower($clean_string);
    }

}

?>