<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


#Magic_quotes must be off since PHP 5.5.
if (!get_magic_quotes_gpc()) {
    function add_slash_arr(&$arr_r) {
        foreach ($arr_r as &$val)
            is_array($val) ? add_slash_arr($val) : $val = addslashes($val);
        unset($val);
    }
    add_slash_arr($_POST);
    add_slash_arr($_GET);
    add_slash_arr($_REQUEST);
}

include (SYSTEM_ROOT . 'admin/db_connect.php');
include ('tab_names.php');

define('PICS_WEB_ADMIN', '../images/');
define('PICS_WEB', '/images/');
define('PICS_CACHE', './cache/');
define('CACHE', 'cache/');
define('PATH_FONTS', './fonts/');
define('FILE_SERVER_FOLDER', 'file_server/');
define('FILE_SERVER', './' . FILE_SERVER_FOLDER);
define('PICS_SCR_ROOT', PATH_CMS . 'images/scr/');
define('IMAGE_PATH', PATH_CMS . 'images/');
define('ADMIN_ROOT', '/admin/');
define('FILETYPES_PATH', 'images/fileext/');
define('PICS_ROOT', './images/');
define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
define('UPDATE_SERVER', 'https://www.keimeno.de/cms_update/');
define('SERVER', 'https://www.keimeno.de/');
define('RESTSERVER', 'https://www.keimeno.de/rest/rest.php');
define('KEIMENO', 1);

# File Root einstellen
if (strlen(PATH_CMS) > 1) {
    $arr = explode('/', str_replace(PATH_CMS, '', CMS_ROOT));
    array_shift($arr);
}
else {
    $arr = explode('/', substr(CMS_ROOT, 1, -1));
}
array_pop($arr);
define('FILE_ROOT', '/' . implode('/', $arr) . '/cmsdata/');
define('FILE_SEVER_ROOT', FILE_ROOT . 'file_server/');


# define cookie name
$cookiename = preg_replace("/[^a-z]/", "", strval($_SERVER['HTTP_HOST']));
define('COOKIENAME', substr($cookiename, 0, 10));

$anrede_index_arr = array(
    "m" => "m",
    "w" => "w",
    "hdr" => "m",
    "fdr" => "w",
    "fprof" => "w",
    "hprof" => "m",
    "hprofdr" => "m",
    "fprofdr" => "w");
$anrede_arr = array(
    "m" => "{LBL_HERR}",
    "w" => "{LBL_FRAU}",
    "hdr" => "{LBL_HDR}",
    "fdr" => "{LBL_FDR}",
    "fprof" => "{LBL_FPROF}",
    "hprof" => "{LBL_HPROF}",
    "hprofdr" => "{LBL_HPROFDR}",
    "fprofdr" => "{LBL_FPROFDR}");

$allowed_ext = $_SESSION['allowed_ext'] = array(
    ".pptx",
    ".xlsx",
    ".ics",
    ".js",
    ".css",
    ".jpeg",
    ".asf",
    ".mpg",
    ".wmv",
    ".doc",
    ".docx",
    ".ppt",
    ".json",
    ".pptx",
    ".odt",
    ".fla",
    ".flv",
    ".mov",
    ".mp3",
    ".mpeg",
    ".png",
    ".rar",
    ".zip",
    ".rtf",
    ".ico",
    ".tar",
    ".gz",
    ".jpg",
    ".svg",
    ".JPG",
    ".gif",
    ".GIF",
    '.txt',
    '.htm',
    '.html',
    '.csv',
    '.xls',
    '.pdf',
    '.ppt',
    '.tar.gz',
    '.gz',
    '.swf',
    '.vsd',
    '.xml');
$viewable_ext = $_SESSION['viewable_ext'] = array(
    ".jpg",
    ".JPG",
    ".gif",
    ".GIF",
    ".jpeg",
    ".JPEG",
    ".png",
    ".PNG");
$forbidden_ext = $_SESSION['forbidden_ext'] = array(
    ".ini",
    ".bmp",
    ".php",
    ".php3",
    ".php2",
    ".php1",
    '.php4',
    '.php5',
    '.pl',
    '.cgi',
    '.asp',
    '.cfm',
    '.bat',
    '.com',
    '.exe');


$anzeige_caption_monate = array(
    'Januar',
    'Februar',
    'März',
    'April',
    'Mai',
    'Juni',
    'Juli',
    'August',
    'September',
    'Oktober',
    'November',
    'Dezember');

$kdb = new kdb;
$kdb->database = DB_DATABASE;
$kdb->server = DB_HOST;
$kdb->user = DB_USER;
$kdb->password = DB_PASSWORD;
$kdb->connect();
