<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (dirname(__FILE__) . '/inc/initadmin.inc.php');

$CUSTOMER_OBJ = new customer_class();
$CUSTOMER_OBJ->TCR->interpreter();

if (class_exists('memindex_admin_class')) {
    $MEMBER_OBJ = new memindex_admin_class();
    $MEMBER_OBJ->TCR->interpreter();
}


define('NACHNAME_IDX', 0);
define('VORNAME_IDX', 1);
define('STRASSE_IDX', 2);
define('PLZ_IDX', 3);
define('ORT_IDX', 4);
define('TEL_IDX', 5);
define('EMAIL_IDX', 6);
define('FIRMA_IDX', 7);
define('FAX_IDX', 8);
define('LAND_IDX', 9);
define('GESCHLECHT_IDX', 10);
define('WWW_IDX', 11);
define('REGDATUM_IDX', 12);
define('PASSWORD_IDX', 13);
define('KID_IDX', 14);


if ($_POST['cmd'] == "a_import") {
    if ($_FILES['csvfile']['tmp_name'] == "") {
        keimeno_class::msge('Bitte geben Sie eine entsprechende Datei an.');
        HEADER("location:" . $_SERVER['PHP_SELF'] . "?cmd=a_simport");
        exit;
    }

    if (!validate_upload_file($_FILES['csvfile'])) {
        keimeno_class::msge($_SESSION['upload_msge']);
        header('location: ' . $_SERVER['PHP_SELF'] . '?cmd=a_simport');
        exit;
    }
    $f_name = CMS_ROOT . 'admin/cache/customers_import.csv';
    if (!move_uploaded_file($_FILES['csvfile']['tmp_name'], $f_name)) {
        $content .= 'ERROR: ' . './' . $f_name;
        print_r($_FILES);
        die;
    }

    $handle = fopen($f_name, "r");
    $k = 0;
    $_SESSION['CSV_IMPORT'] = "";
    while (($data = fgetcsv($handle, 1000, $_POST['trennzeichen'])) !== FALSE) {
        if (count($data) <= 1) {
            keimeno_class::msge('Fehlerhafte Spalten Anzahl. Richtiges Trennzeichen?');
            header('location: ' . $_SERVER['PHP_SELF'] . '?cmd=a_simport');
            exit;
        }
        if ($_POST['utf8convert'] == 0) {
            foreach ($data as $key => $value)
                $data[$key] = utf8_encode(trim($data[$key]));
        }

        if ($data[KID_IDX] > 0)
            $K_OBJ['kid'] = $data[KID_IDX];
        $K_OBJ['nachname'] = $data[NACHNAME_IDX];
        if (strlen($K_OBJ['nachname']) < 2)
            $K_OBJ['nachname'] = "";
        $K_OBJ['vorname'] = $data[VORNAME_IDX];
        $K_OBJ['strasse'] = $data[STRASSE_IDX];
        $K_OBJ['plz'] = $data[PLZ_IDX];
        $K_OBJ['ort'] = $data[ORT_IDX];
        $K_OBJ['tel'] = $data[TEL_IDX];
        $K_OBJ['fax'] = $data[FAX_IDX];
        $K_OBJ['email'] = $data[EMAIL_IDX];
        $K_OBJ['firma'] = $data[FIRMA_IDX];
        $K_OBJ['homepage'] = $data[WWW_IDX];
        $K_OBJ['mit_id'] = $_POST['FORM']['mit_id'];
        $MITIMP_OBJ = $kdb->query_first("SELECT * FROM " . TBL_CMS_ADMINS . " WHERE id=" . intval($K_OBJ['mit_id']));
        $K_OBJ['mit_name'] = $MITIMP_OBJ['mitarbeiter_name'];
        if ($data[REGDATUM_IDX] != "") {
            $K_OBJ['datum'] = date('Y-m-d', strtotime($data[REGDATUM_IDX]));
        }
        else {
            $K_OBJ['datum'] = date('Y-m-d');
        }
        list($K_OBJ['jahr'], $K_OBJ['monat'], $K_OBJ['tag']) = explode('-', $K_OBJ['datum']);
        $K_OBJ['geschlecht'] = trim(strtolower($data[GESCHLECHT_IDX]));
        $K_OBJ['geschlecht'] = (empty($K_OBJ['geschlecht'])) ? 'f' : $K_OBJ['geschlecht'];
        $ges = array(
            'm',
            'w',
            'f');
        if (!in_array($K_OBJ['geschlecht'], $ges))
            $K_OBJ['geschlecht'] = 'm';
        $K_OBJ['rabatt_gruppe'] = $_POST['FORM']['kundengruppe']; #$data[GROUPID_IDX];
        $K_OBJ['land'] = get_value_from_table(TBL_CMS_LAND, "id", "country_code_2='" . $data[LAND_IDX] . "'");
        if ($K_OBJ['land'] == "")
            $K_OBJ['land'] = get_value_from_table(TBL_CMS_LAND, "id", "land LIKE '%" . $data[LAND_IDX] . "%'");
        if ($K_OBJ['land'] == "")
            $K_OBJ['land'] = get_value_from_table(TBL_CMS_LAND, "id", "transland LIKE '%" . $data[LAND_IDX] . "%'");
        $K_OBJ['passwort'] = $data[PASSWORD_IDX];
        if ($_POST['md5pass'] == 0)
            $K_OBJ['passwort'] = encrypt_password($K_OBJ['passwort']);
        if ($data[PASSWORD_IDX] == "")
            $K_OBJ['passwort'] = gen_sid(6);
        $K_OBJ['agb'] = 1;
        $K_OBJ['mailactive'] = 1;

        if (strstr($K_OBJ['email'], ' ')) {
            list($p1, $p2) = explode(' ', $K_OBJ['email']);
            $K_OBJ['email'] = $p1;
        }

        #Validierung Homepage
        if (!strstr($K_OBJ['homepage'], 'www') && !strstr($K_OBJ['homepage'], 'http://'))
            $K_OBJ['homepage'] = '';
        #Validierung PLZ und Ort
        $K_OBJ['plz'] = trim($K_OBJ['plz']);
        if (strstr($K_OBJ['plz'], ' ')) {
            list($p1, $p2) = explode(' ', $K_OBJ['plz']);
            if (is_numeric($p1) && !is_numeric($p2)) {
                $K_OBJ['plz'] = $p1;
                $K_OBJ['ort'] = $p2;
            }
            if (!is_numeric($p1) && is_numeric($p2)) {
                $K_OBJ['ort'] = '';
            }
            if (!is_numeric($p1) && !is_numeric($p2)) {
                $K_OBJ['plz'] = $p1;
                $K_OBJ['ort'] = $p2;
            }
        }
        $K_OBJ['ort'] = str_replace(array(','), '', $K_OBJ['ort']);
        $K_OBJ['plz'] = str_replace(array(','), '', $K_OBJ['plz']);
        if (!empty($K_OBJ['geschlecht'])) {
            $K_OBJ['anrede'] = get_customer_salutation($K_OBJ['geschlecht']);
            $K_OBJ['geschlecht'] = get_customer_sex($K_OBJ['geschlecht']);
        }

        foreach ($K_OBJ as $key => $value)
            $K_OBJ[$key] = $TCMASTER->db->real_escape_string(trim($K_OBJ[$key]));
        if ($K_OBJ['land'] == "")
            $K_OBJ['land'] = 1; // GERMANY
        $k++;
        if ($_POST['identy'] == 'email') {
            if (trim($data[EMAIL_IDX]) != "")
                $OLD_K = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . $data[EMAIL_IDX] . "'");
        }
        if ($_POST['identy'] == 'names') {
            if (trim($data[NACHNAME_IDX]) != "")
                $OLD_K = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE nachname='" . $data[NACHNAME_IDX] . "' AND vorname='" . $data[VORNAME_IDX] . "'");
        }
        if (!validate_email_input($K_OBJ['email']) && $_POST['emailimport'] == 0)
            $_SESSION['CSV_IMPORT'] .= '<tr><td>' . $k . '</td><td><div class="bg-danger">ung&uuml;ltige Email</div></td><td>' . $K_OBJ['firma'] . '</td><td>' . $K_OBJ['nachname'] .
                '</td><td>' . $K_OBJ['vorname'] . '</td><td>' . $K_OBJ['plz'] . '</td><td>' . $K_OBJ['ort'] . '</td><td>' . $K_OBJ['email'] . '</td><td>' . $K_OBJ['tel'] .
                '</td><td>' . $data[REGDATUM_IDX] . '</td></tr>';
        else
            if ($_POST['changegroup'] == 0 && $OLD_K['kid'] > 0)
                $_SESSION['CSV_IMPORT'] .= '<tr><td>' . $k . '</td><td><div class="bg-danger">Kunde vorhanden</div></td><td>' . $K_OBJ['firma'] . '</td><td>' . $K_OBJ['nachname'] .
                    '</td><td>' . $K_OBJ['vorname'] . '</td><td>' . $K_OBJ['plz'] . '</td><td>' . $K_OBJ['ort'] . '</td><td>' . $K_OBJ['email'] . '</td><td>' . $K_OBJ['tel'] .
                    '</td><td>' . $data[REGDATUM_IDX] . '</td></tr>';
            else
                if ($_POST['changegroup'] == 1 && $OLD_K['kid'] > 0) {
                    $_SESSION['CSV_IMPORT'] .= '<tr><td>' . $k . '</td><td><div class="bg-success">Kunde vorhanden, Datensatz aktualisiert</div></td><td>' . $K_OBJ['firma'] .
                        '</td><td>' . $K_OBJ['nachname'] . '</td><td>' . $K_OBJ['vorname'] . '</td><td>' . $K_OBJ['plz'] . '</td><td>' . $K_OBJ['ort'] . '</td><td>' . $K_OBJ['email'] .
                        '</td><td>' . $K_OBJ['tel'] . '</td><td>' . $data[REGDATUM_IDX] . '</td></tr>';
                    foreach ($K_OBJ as $key => $value) {
                        if ($K_OBJ[$key] == "" && $OLD_K[$key] != "")
                            unset($K_OBJ[$key]);
                    }
                    update_table(TBL_CMS_CUST, 'kid', $OLD_K['kid'], $K_OBJ);
                }
                else {
                    insert_table(TBL_CMS_CUST, $K_OBJ);
                    $_SESSION['CSV_IMPORT'] .= '<tr><td>' . $k . '</td><td><div class="bg-success">OK</div></td><td>' . $K_OBJ['firma'] . '</td><td>' . $K_OBJ['nachname'] .
                        '</td><td>' . $K_OBJ['vorname'] . '</td><td>' . $K_OBJ['plz'] . '</td><td>' . $K_OBJ['ort'] . '</td><td>' . $K_OBJ['email'] . '</td><td>' . $K_OBJ['tel'] .
                        '</td><td>' . $data[REGDATUM_IDX] . '</td></tr>';
                }

    }
    if ($k > 0) {
        $_SESSION['CSV_IMPORT'] = '<hr><div class="page-header"><h1>Folgende Kunden wurden zuletzt (" . date("d.m.Y H:i:s") . ") importiert:</h1></div>
            <table class="table table-striped table-hover" >' . $_SESSION['CSV_IMPORT'] . '</table>';
    }
    else
        $_SESSION['CSV_IMPORT'] = "";
    keimeno_class::msg($k . " Kunden bearbeitet");
    HEADER("location:" . $_SERVER['PHP_SELF'] . "?cmd=a_simport");
    exit;
}

if ($_GET['aktion'] == "edit") {
    HEADER('location:kreg.php?kid=' . $_GET['kid'] . '&cmd=show_edit');
    exit;
}

$menu = array("{LBL_CUSTOMERS}" => '', "CSV Import" => "cmd=a_simport");
$ADMINOBJ->set_top_menu($menu);

if ($_GET['aktion'] == "" && $_GET['kid'] == 0) {
    $CUSTOMER_OBJ->load_custable();
}

$content .= '<% include file="kreg.main.tpl" %>';


$CUSTOMER_OBJ->parse_to_smarty();
if (class_exists('memindex_admin_class')) {
    $MEMBER_OBJ->parse_to_smarty();
}
include (CMS_ROOT . 'admin/inc/footer.inc.php');
