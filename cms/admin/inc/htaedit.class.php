<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class htaedit_class extends htamaster_class {

    var $HTAEDIT = array();

    /**
     * htaedit_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $HTA_CLASS_CMS;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->HTA_CLASS_CMS = $HTA_CLASS_CMS;
        $this->HTAEDIT['table'] = $this->editTable();
    }

    /**
     * htaedit_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('HTAEDIT', $this->HTAEDIT);
    }

    /**
     * htaedit_class::changeAll()
     * 
     * @param mixed $delimeter
     * @return
     */
    function changeAll($delimeter) {
        $this->db->query("UPDATE " . TBL_CMS_HTA . " SET hta_delimeter1='" . $delimeter . "', hta_delimeter2='" . $delimeter . "', hta_delimeter3='" . $delimeter .
            "', hta_delimeter4='" . $delimeter . "' WHERE 1 ");
        $this->buildHTACCESS();
    }


    /**
     * htaedit_class::cmd_htachange()
     * 
     * @return
     */
    function cmd_htachange() {
        $this->changeAll($_POST['delimeter']);
        $this->LOGCLASS->addLog('MODIFY', '.htaccess changed (all)');
        $this->hard_exit();
    }

    /**
     * htaedit_class::saveTable()
     * 
     * @param mixed $FORM
     * @param mixed $CONFIG
     * @return
     */
    function saveTable($FORM, $CONFIG) {
        foreach ($CONFIG as $key => $value)
            $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $value . "' WHERE config_name='" . $key . "' LIMIT 1 ");
        $SQL_ARR = array();
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $FORM_SET) {
                if (count($FORM_SET) > 0) {
                    foreach ($FORM_SET as $id => $value) {
                        if ($key == 'hta_prefix')
                            $value = strtolower(ereg_replace("[^a-zA-Z0-9-]", "", $value)); //nur buchstaben und zahlen
                        $SQL_ARR[$id][$key] = $value;
                    }
                }
            }
        }
        if (count($SQL_ARR) > 0) {
            foreach ($SQL_ARR as $id => $SQL_SET) {
                $SQL_SET['hta_tmpllink'] = $this->genAscciiJoker($id);
                $SQL_SET['hta_ssl'] = intval($SQL_SET['hta_ssl']);
                $linkhta = $this->db->query_first("SELECT * FROM " . TBL_CMS_HTA . " WHERE id=" . $id);
                $check = $this->db->query_first("SELECT * FROM " . TBL_CMS_HTA . " WHERE hta_locked=0 AND id<>" . $id . " AND hta_prefix='" . $SQL_SET['hta_prefix'] . "' ");
                #   if ($check['id'] == 0)
                if ($SQL_SET['hta_page'] > 0) {
                    $SQL_SET['hta_ref'] = str_replace('page=' . $linkhta['hta_page'], 'page=' . $SQL_SET['hta_page'], $linkhta['hta_ref']);
                }
                update_table(TBL_CMS_HTA, 'id', $id, $SQL_SET);
                #  else
                #     $msge .= $SQL_SET['hta_prefix'] . ' ist bereits in Benutzung.[BR]';
            }
        }
        if (empty($msge))
            $this->buildHTACCESS();
        return $msge;
    }

    #Options +FollowSymLinks
    /*# protect for url incjection with http
    RewriteCond %{QUERY_STRING} http[:%] [NC]
    RewriteRule .* ' . PATH_CMS . '-http- [F,NC]
    RewriteRule http: ' . PATH_CMS . '-http- [F,NC]
    */
    /**
     * htaedit_class::buildHTACCESS()
     * 
     * @return
     */
    function buildHTACCESS() {
        $CONFIG_OBJ = new config_class();
        $this->load_sslsites_fe();
        $CONFIG_OBJ->load();
        $this->gbl_config = $CONFIG_OBJ->gbl_config;
        $text = $this->gbl_config['hta_specialtext_first'] . "\n\nErrorDocument 404 " . self::get_domain_url() . "404.html\n\n<IfModule mod_rewrite.c>\nRewriteEngine On\n# CMS Extra rules\n";
        foreach ($this->settings as $id => $row) {
            $stars = "";
            for ($i = 1; $i <= $row['hta_starcount']; $i++) {
                $stars .= $row['hta_delimeter' . $i] . '(.*)';
            }
            $link = trim($row['hta_ref']);
            $link = str_replace('index.php?', 'index.php?htaid=' . $id . '&', $link);
            $text .= 'RewriteRule ^' . $row['hta_prefix'] . $stars . '\\' . $row['hta_fileext'] . ' ' . PATH_CMS . $link . "\n";
        }
        $text .= "\n# Language Support\nRewriteRule ^([a-z]{2}(\-[A-Z]{2})?)/(.*)\.html " . PATH_CMS . "index.php?page=$3&lngcode=$1&%{QUERY_STRING} [L]\n#RewriteRule ^(.*)/(.*)\.html " .
            PATH_CMS . "index.php?page=$2&lngcode=$1&%{QUERY_STRING} [L]\nRewriteRule ^(.*)\.html " . PATH_CMS . "index.php?page=$1&%{QUERY_STRING} [L]\n\n" . $this->
            gbl_config['hta_specialtext'] . "\n</IfModule>";
        $text = str_replace('!!DOMAIN_PUR!!', FM_DOMAIN, $text);
        file_put_contents(CMS_ROOT . '.htaccess', utf8_decode($text));
    }

    /**
     * htaedit_class::cmd_htasaveconf()
     * 
     * @return
     */
    function cmd_htasaveconf() {
        $msge = $this->saveTable($_POST['FORM'], $_POST['CONFIG']);
        if ($msge != "") {
            # header('location: ' . $_SERVER['PHP_SELF'] . '?ik=3&aktion=htaedit&msge=' . base64_encode($msge));
        }
        else {
            $this->LOGCLASS->addLog('MODIFY', '.htaccess changed');
            #  header('location: ' . $_SERVER['PHP_SELF'] . '?ik=3&aktion=htaedit&msg=' . base64_encode("htaccess gespeichert"));
        }
        exit;
    }

    /**
     * htaedit_class::editTable()
     * 
     * @return
     */
    function editTable() {
        $table = "";
        $this->load_sslsites_fe();
        $this->HTAEDIT['htad'] = array(
            ',',
            '-',
            '_',
            '/');
        foreach ($this->settings as $id => $row) {
            $table .= '<tr>
		<td>' . $row['hta_description'] . '</td>
		<td>' . (($row['hta_locked'] == 0) ? '<input type="text" required class="form-control" name="FORM[hta_prefix][' . $row['id'] . ']" value="' . htmlspecialchars
                ($row['hta_prefix']) . '">&nbsp;' : $row['hta_prefix']);
            for ($i = 1; $i <= $row['hta_starcount']; $i++) {
                $table .= '<select class="form-control" name="FORM[hta_delimeter' . $i . '][' . $row['id'] . ']">';
                foreach ($this->HTAEDIT['htad'] as $key => $trenner)
                    $table .= '<option ' . (($row['hta_delimeter' . $i] == $trenner) ? 'selected' : '') . ' value="' . $trenner . '">' . $trenner . '</option>';
                $table .= '</select>(.*)';
            }
            $table .= '<input maxlength="5" type="text" class="form-control" value="' . htmlspecialchars($row['hta_fileext']) . '" name="FORM[hta_fileext][' . $row['id'] .
                ']">
		</td>';
            if (strstr(strtolower($_SERVER['HTTP_HOST']), 'trebaxa.com')) {
                $table .= '<td><input type="text" required class="form-control" value="' . htmlspecialchars($row['hta_description']) . '" name="FORM[hta_description][' . $row['id'] .
                    ']"><br><small>
		 ' . $row['hta_ref'] . '</small></td>';
                if ($row['hta_fix'] == 1) {
                    $table .= '<td><input ' . (($row['hta_ssl'] == 1) ? 'checked' : '') . ' type="checkbox" value="1" name="FORM[hta_ssl][' . $row['id'] . ']"></td>';
                }
                else {
                    $table .= '<td></td>';
                }
            }
            else {
                $table .= '<td class="text-center"><input type="hidden" value="' . $row['hta_ssl'] . '" name="FORM[hta_ssl][' . $row['id'] . ']">
		  ' . (($row['hta_ssl'] == 1) ? 'SSL Area' : '-') . '</td>';
            }

            $abc = "";
            $zahl_arr = str_split($row['id']);
            foreach ($zahl_arr as $zkey => $zahl)
                $abc .= chr($zahl + 65);
            $table .= '<td>' . (($row['hta_fix'] == 1) ? htmlspecialchars('<% $HTA_CMSFIXLINKS.' . $row['hta_tmpllink'] . ' %>') : '') . '</td>';
            $table .= '<td><input class="form-control" type="text" value="' . htmlspecialchars((int)$row['hta_page']) . '" name="FORM[hta_page][' . $row['id'] .
                ']"></td></tr>';
        }


        $ret .= $table;
        if (file_exists(CMS_ROOT . '.htaccess'))
            $this->HTAEDIT['htaccesstext'] = utf8_encode(file_get_contents(CMS_ROOT . '.htaccess'));

        return $ret;
    }

}

?>