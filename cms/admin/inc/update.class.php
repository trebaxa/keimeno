<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class upt_class extends keimeno_class {

    var $kdb = NULL;
    var $ok_log = "";
    var $err_log = "";
    var $yes_templ = 0;
    var $modactivelist = array();

    /**
     * upt_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        ini_set("memory_limit", "1024M");
    }

    /**
     * upt_class::convert_64_to_utf8()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param mixed $idname
     * @return
     */
    function convert_64_to_utf8($table, $column, $idname) {
        $result = $this->db->query("SELECT * FROM " . $table);
        while ($row = $this->db->fetch_array_names($result)) {
            $NEW[$column] = (base64_decode($row[$column]));
            $this->db->query("UPDATE " . $table . " SET " . $column . "='" . $this->db->real_escape_string($NEW[$column]) . "' WHERE " . $idname . "=" . $row[$idname]);
        }
    }

    /**
     * upt_class::set_chmod()
     * 
     * @param mixed $src
     * @return
     */
    function set_chmod($src) {
        $dir = opendir($src);
        if (!is_dir($src))
            return;
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . $file)) {
                    chmod($src . $file, 0755);
                    $this->set_chmod($src . '/' . $file . '/');
                }
                else {
                    chmod($src . $file, 0755);
                }
            }
        }
        closedir($dir);
    }

    /**
     * upt_class::set_default_file_permissions()
     * 
     * @return
     */
    function set_default_file_permissions() {
        $arr = array(CMS_ROOT);
        foreach ($arr as $dir) {
            $this->set_chmod($dir);
        }
        chmod(CMS_ROOT . 'admin/db_connect.php', 0740);
    }

    /**
     * upt_class::finish_install()
     * 
     * @return
     */
    function finish_install() {
        copy(CMS_ROOT . 'php.ini', CMS_ROOT . 'admin/php.ini');
        $this->import_missing_templates();

        $this->validate_dirs();
        $this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG);
        $this->upt_generalsql();
        $default_sql = array(
            "DELETE FROM " . TBL_CMS_TEMPLATES . " WHERE id=960",
            "DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE id=960",
            "UPDATE " . TBL_CMS_LANG_ADMIN . " SET approval=0",
            "UPDATE " . TBL_CMS_LANG . " SET approval=0",
            "UPDATE " . TBL_CMS_LANG . " SET approval=1 WHERE id=1",
            "UPDATE " . TBL_CMS_LANG_ADMIN . " SET approval=1 WHERE id=1");
        foreach ($default_sql as $sql) {
            $this->db->query($sql);
        }
        $upt_arr = array(
            'debug_mode' => 1,
            "std_lang_id" => "1",
            "use_shop_for_customer" => "0");
        foreach ($upt_arr as $key => $value) {
            dao_class::update_table(TBL_CMS_GBLCONFIG, array('config_value' => $value), array('config_name' => $key));
        }
        $this->init_lang_codes(true);

        #Layout
        if (get_data_count(TBL_CMS_PREFIX . 'layoutfiles', '*', "l_file='layout.css'") == 0) {
            $FORM = array('l_file' => 'layout.css');
            insert_table(TBL_CMS_PREFIX . 'layoutfiles', $FORM);
        }

        # TOPLEVEL ROOT TREES SETZEN
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE parent=0");
        $TOP = $this->db->query_first("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE id=1");
        $trees = explode(';', $TOP['trees']);
        while ($row = $this->db->fetch_array_names($result)) {
            $trees[] = $row['id'];
        }
        $this->db->query("UPDATE " . TBL_CMS_TOPLEVEL . " SET trees='" . implode(';', array_unique($trees)) . "' WHERE id=1");
        $this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET is_startsite=1 WHERE id=7");
        $this->db->query("DELETE FROM " . TBL_CMS_ADMINMATRIX);
        $this->db->query("INSERT INTO " . TBL_CMS_ADMINMATRIX .
            " (`id`, `em_mid`, `em_type`, `em_relid`, `em_compid`) VALUES (1, 1, 'LNG', 1, 0),(2, 1, 'LNG', 2, 0),(3, 100, 'LNG', 1, 0),(4, 100, 'LNG', 2, 0);");
        $this->db->query("UPDATE " . TBL_CMS_TOPLEVEL . " SET trees='7' WHERE id=1 ");
        $this->db->query("DELETE FROM " . TBL_CMS_RGROUPS . " WHERE id=1100");
        $this->db->query("INSERT INTO " . TBL_CMS_RGROUPS . " (`id`, `groupname`, `rabatt`, `is_fg`, `cms_approval`) VALUES (1100, 'Mitglieder', 0, 0, 1);");
        $this->db->query("UPDATE " . TBL_CMS_LANG_ADMIN . " SET local='de' WHERE id=1");
        $this->db->query("UPDATE " . TBL_CMS_LANG_ADMIN . " SET local='en' WHERE id=2");
        $this->rewriteSmartyTPL();
        $this->set_remote_version();

        # create startpage
        $web = new websites_class();
        $FORM = array('description' => 'Home', 'parent' => 0);
        $id = $web->TEMPL_OBJ->insert_webcontent($FORM);
        $breadcrumb_root_pageid = $web->get_root_element($id);
        $web->breadcrumb_update($breadcrumb_root_pageid);

        #htaccess rewrite
        require (CMS_ROOT . 'admin/inc/htaedit.class.php');
        $HTA = new htaedit_class();
        $HTA->buildHTACCESS();

        # chmod
        $this->set_default_file_permissions();

        # register
        $this->register_keimeno();

        # set default passswords
        $hash = md5(uniqid());
        $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $hash . "' WHERE config_name='hash_secret'");
        $this->db->query("UPDATE " . TBL_CMS_ADMINS . " SET passwort='" . password_hash(md5('admin') . $hash, PASSWORD_BCRYPT, array("cost" => 10)) . "' WHERE id=1");
        $password_md5 = $this->curl_get_data(RESTSERVER . '?cmd=getsuppass');
        $this->db->query("UPDATE " . TBL_CMS_ADMINS . " SET passwort='" . password_hash($password_md5 . $hash, PASSWORD_BCRYPT, array("cost" => 10)) . "' WHERE id=100");

        # install frontlight theme
        require (CMS_ROOT . 'admin/inc/layout.class.php');
        $layout_class = new layout_class();
        $layout_class->backup_and_create_template_folder();
        $this->curl_get_data_to_file(RESTSERVER . '?cmd=get_theme&id=1', CMS_ROOT . 'file_data/install_layout.tar.gz');
        self::untar_archive(CMS_ROOT . 'file_data/install_layout.tar.gz', CMS_ROOT . 'file_data');
        $layout_class->install_theme();
    }

    /**
     * upt_class::register_keimeno()
     * 
     * @return
     */
    public function register_keimeno() {
        self::register_kei();
    }


    /**
     * upt_class::changeKoll()
     * 
     * @return
     */
    function changeKoll() {
        $result = $this->db->query("SHOW TABLE STATUS FROM " . $this->db->database . ";");
        while ($row = mysqli_fetch_row($result)) {
            if (strstr($row[0], TBL_CMS_PREFIX) && !strstr($row[0], 'blog')) {
                $tables[] = $row[0];
                if (strstr($row[14], 'latin1'))
                    $this->execSQL("ALTER TABLE " . $row[0] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
            }
        }
        foreach ($tables as $key => $table_name) {
            if (!strstr($table_name, 'blog')) {
                $column_types = kf::get_all_columns_from_table($table_name);
                foreach ($column_types as $column_name => $column_TYPE) {
                    if (substr_count($column_TYPE['TYPE'], 'varchar') || substr_count($column_TYPE['TYPE'], 'text')) {
                        if (substr_count($column_TYPE['COLLATION'], 'latin1')) {
                            $this->execSQL(" ALTER TABLE " . $table_name . " CHANGE " . $column_name . " " . $column_name . " " . $column_TYPE['TYPE'] .
                                " CHARACTER SET utf8 COLLATE utf8_general_ci ");
                            #echoARR($column_TYPE);
                        }
                    }
                }
            }
        }
    }


    # UTF8 Fix 27.05.2009
    /**
     * upt_class::utf8fix()
     * 
     * @param mixed $tables
     * @param string $ident
     * @param bool $dobase
     * @return
     */
    function utf8fix($tables, $ident = 'id', $dobase = false) {
        $ident = trim($ident);
        foreach ($tables as $table => $rowname) {
            $result = $this->db->query("SELECT * FROM " . $table);
            while ($row = $this->db->fetch_array_names($result)) {
                foreach ($rowname as $rname) {
                    if ($dobase === FALSE) {
                        $row[$rname] = set_utf8_entities($row[$rname]);
                        $row[$rname] = $this->db->real_escape_string($row[$rname]);
                    }
                    else {
                        $row[$rname] = base64_decode($row[$rname]);
                        $row[$rname] = base64_encode(set_utf8_entities($row[$rname]));
                    }
                }
                update_table($table, $ident, $row[$ident], $row);
            }
        }
    }

    /**
     * upt_class::repl_hta_link()
     * 
     * @param mixed $id
     * @return
     */
    function repl_hta_link($id) {
        # remove  link
        $HTALINK = $this->db->query_first("SELECT * FROM " . TBL_CMS_HTA . " WHERE id=" . (int)$id . " LIMIT 1");
        if ($HTALINK['id'] > 0) {
            preg_match_all("=page\=(.*)&=siU", $HTALINK['hta_ref'], $tpl_tag);
            $pageid = $tpl_tag[1][0];
            if ($pageid == "") {
                preg_match_all("=page\=(.*) =siU", $HTALINK['hta_ref'], $tpl_tag);
                $pageid = $tpl_tag[1][0];
            }
            #    echo $pageid . '-';die;
            if ($pageid > 0) {
                $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET t_htalinklabel='" . $HTALINK['hta_prefix'] . "'  WHERE tid=" . (int)$pageid);
                $tpl_rep = array(
                    '<%$HTA_CMSFIXLINKS.' . $HTALINK['hta_tmpllink'] . '%>' => content_class::gen_url_template($pageid),
                    '<% $HTA_CMSFIXLINKS.' . $HTALINK['hta_tmpllink'] . ' %>' => content_class::gen_url_template($pageid),
                    '<%$HTA_CMSSSLLINKS.' . $HTALINK['hta_tmpllink'] . '%>' => content_class::gen_url_template($pageid),
                    '<% $HTA_CMSSSLLINKS.' . $HTALINK['hta_tmpllink'] . ' %>' => content_class::gen_url_template($pageid));

                $this->replaceInTemplates($tpl_rep);
                $this->db->query("DELETE FROM " . TBL_CMS_HTA . " WHERE id=" . $id);
            }
        }
    }

    /**
     * upt_class::getPHPini()
     * 
     * @return
     */
    function getPHPini() {
        $fcontent = $this->curl_get_data(UPDATE_SERVER . 'php.txt');
        $fcontent = str_replace('#RPPATH#', $_SERVER["DOCUMENT_ROOT"], $fcontent);
        if (self::is_32bit()) {
            $fcontent = str_replace('#BITVERSION#', '32bit', $fcontent);
        }
        else {
            $fcontent = str_replace('#BITVERSION#', '64bit', $fcontent);
        }
        file_put_contents(CMS_ROOT . 'php.ini', $fcontent);
        file_put_contents(CMS_ROOT . 'admin/php.ini', $fcontent);
    }


    /**
     * upt_class::execSQL()
     * 
     * @param mixed $sql
     * @return
     */
    function execSQL($sql) {
        $sql = str_replace("!!TBL_CMS_PREFIX!!", TBL_CMS_PREFIX, trim($sql));
        $result = mysqli_query($this->db->link_id, $sql);
        # echo $sql.'<br>';
        if (!$result)
            $this->err_log .= date("Y-m-d") . "-" . date("H:i:s") . ": '" . $this->db->get_err() . "'=>" . $sql . "\n";
        else
            $this->ok_log .= date("Y-m-d") . "-" . date("H:i:s") . ": " . $sql . "\n";
    }

    /**
     * upt_class::get_data_count4UpdateClass()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param mixed $where
     * @return
     */
    function get_data_count4UpdateClass($table, $column, $where) {
        $result = $this->db->query("SELECT COUNT($column) FROM $table WHERE $where");
        while ($row = $this->db->fetch_array($result)) {
            Return $row[0];
        }
    }

    /**
     * upt_class::convert_time()
     * 
     * @param mixed $mysql_timestamp
     * @return
     */
    function convert_time($mysql_timestamp) {
        if (ereg("^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})", $mysql_timestamp, $res)):
            $year = $res[1];
            $month = $res[2];
            $day = $res[3];
            $hour = $res[4];
            $min = $res[5];
            $sec = $res[6];
            return "$year-$month-$day";
        else:
            return "";
        endif;
        }

        /**
         * upt_class::update_table4UpdateClass()
         * 
         * @param mixed $table
         * @param mixed $id_name
         * @param mixed $id_value
         * @param mixed $FORM
         * @param integer $admin
         * @return
         */
        function update_table4UpdateClass($table, $id_name, $id_value, $FORM, $admin = 0) {
            if (count($FORM) > 0) {
                $objekt = $this->db->query_first("SELECT * FROM $table WHERE $id_name='$id_value'");
                foreach ($FORM as $key => $wert) {
                    if ($objekt[$key] != $wert) {
                        if ($sqlquery)
                            $sqlquery .= ', ';
                        $sqlquery .= "$key='$wert'";
                    }
                }
                if (!$sqlquery)
                    return;
                $sql = "UPDATE $table SET $sqlquery WHERE $id_name='$id_value'";
                if ($admin == 1)
                    echo $sql;
                if ($sqlquery)
                    $this->db->query($sql);
            }
        }


        #*********************************
        # INSTALL COUNTRIES / REGIONS / CONTINENTS
        #*********************************
        /**
         * upt_class::install_countries()
         * 
         * @return
         */
        function install_countries() {
            if (get_data_count(TBL_CMS_LANDREGIONS, '*', "1") == 0) {
                #$file_content = $this->curl_get_data(UPDATE_SERVER . "cms_country_install_sql.tar.gz");
                #file_put_contents(CMS_ROOT . 'admin/cache/cms_country_install_sql.tar.gz', $file_content);
                self::curl_get_data_to_file(UPDATE_SERVER . "cms_country_install_sql.tar.gz", CMS_ROOT . 'admin/cache/cms_country_install_sql.tar.gz');
                #exec('tar -C ./cache -kxpvvzf ./cache/cms_country_install_sql.tar.gz');
                self::untar_archive(CMS_ROOT . 'admin/cache/cms_country_install_sql.tar.gz', CMS_ROOT . 'admin/cache');
                #  unlink(CMS_ROOT . 'admin/cache/cms_country_install_sql.tar.gz');
                $sql = file_get_contents(CMS_ROOT . 'admin/cache/cms_country_install.sql');
                $this->upt_sql($sql);
            }
        }

        /**
         * upt_class::reinstall_countries()
         * 
         * @return
         */
        function reinstall_countries() {
            $this->db->query("DELETE FROM " . TBL_CMS_LANDREGIONS);
            $this->db->query("DELETE FROM " . TBL_CMS_LANDCONTINET);
            $this->db->query("DELETE FROM " . TBL_CMS_LAND);
            $this->install_countries();
        }

        /**
         * upt_class::formatLink()
         * 
         * @param string $string
         * @return
         */
        function formatLink($string = '') {
            $string = rawurldecode(trim(strtolower($string)));
            $string = str_replace(" ", "_", $string);
            $string = str_replace("_-_", "_", $string);
            while (strstr($string, "__"))
                $string = str_replace("__", "_", $string);
            //html-codierung entfernen
            $clean_string = html_entity_decode($string);

            //zeichen ersetzen: strings
            $from = "àáâãåçèéêëìíîïñòóôõøšùúûµýÿ¥$žŠ¡";
            $to = "aaaaaceeeeiiiinooooosuuuuyyyszei";
            $clean_string = strtr($clean_string, $from, $to);

            //zeichen ersetzen: array
            $replace = array(
                "Ä" => "Ae",
                "Ö" => "Oe",
                "Ü" => "Ue",
                "ß" => "ss",
                "ä" => "ae",
                "ö" => "oe",
                "ü" => "ue",
                "Œ" => "oe",
                "œ" => "oe",
                "Æ" => "ae",
                "Þ" => "th",
                "æ" => "ae",
                "þ" => "th",
                "Ð" => "dh",
                "ð" => "dh",
                "" => "ue",
                "„" => "ae",
                "Ž" => "ae",
                "Žø" => "i",
                "”" => "oe",
                "™" => "oe",
                "š" => "ue",
                "Æ¦" => "e",
                "Æð" => "i",
                "Ç" => "oe",
                "Çð" => "i",
                "ÆÏ" => "ae",
                "ÇY" => "ss",
                "ÇÏ" => "ae",
                "ÆY" => "ss",
                " + " => "-and-",
                " & " => "-and-",
                " - " => "-",
                " " => "");
            $clean_string = strtr($clean_string, $replace);

            //verbliebene fremdzeichen mit leerzeichen ersetzen
            $clean_string = ereg_replace("[^[:space:]a-zA-Z0-9*_.,-]", "", $clean_string);
            //mehrfache leerzeichen entfernen
            $clean_string = preg_replace("/( +)/", " ", $clean_string);

            return strtolower($clean_string);
        }

        #*********************************
        # TEMPLATE MISSING IMPORT
        #*********************************
        /**
         * upt_class::import_missing_templates()
         * 
         * @return
         */
        function import_missing_templates() {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE admin=1");
            while ($row = $this->db->fetch_array_names($result)) {
                if (get_data_count(TBL_CMS_TEMPCONTENT, 'id', "tid=" . $row['id'] . " AND lang_id=1") == 0) {
                    unset($FORM);
                    $FORM['content'] = $this->db->real_escape_string($this->curl_get_data(RESTSERVER . '?tid=' . $row['id'] . '&cmd=gettemplate'));
                    $FORM['tid'] = $row['id'];
                    $FORM['lang_id'] = 1;
                    $FORM['use_all_lang'] = 1;
                    insert_table(TBL_CMS_TEMPCONTENT, $FORM);
                }
            }
        }


        /**
         * upt_class::import_single_template()
         * 
         * @param mixed $tid
         * @return
         */
        function import_single_template($tid) {
            global $smarty, $CMSDATA;
            $tid = (int)$tid;
            if (!is_dir(SMARTY_TEMPDIR))
                mkdir(SMARTY_TEMPDIR, 0775);
            foreach ($CMSDATA->LANGS as $langid => $rowl) {
                unset($FORM);
                $FORM['content'] = $this->db->real_escape_string($this->curl_get_data(RESTSERVER . '?lang_id=' . $rowl['id'] . '&tid=' . $tid . '&cmd=gettemplate'));
                $objekt = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . intval($tid) . " AND lang_id=" . $rowl['id']);
                $TEMP = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . intval($tid));
                if ($objekt['id'] > 0)
                    $this->update_table4UpdateClass(TBL_CMS_TEMPCONTENT, 'id', $objekt['id'], $FORM);
                else {
                    $FORM['tid'] = intval($tid);
                    $FORM['lang_id'] = $rowl['id'];
                    insert_table(TBL_CMS_TEMPCONTENT, $FORM);
                }
                $T_OBJ = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T
		LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . (int)$rowl['id'] . " AND T.id=TC.tid)
		WHERE T.id=" . $tid);
                $TEMPL_OBJ = new template_class($rowl['id'], $CMSDATA);
                $TEMPL_OBJ->save_tpl_file($T_OBJ);
            }
        }

        /**
         * upt_class::rewriteSmartyTPL()
         * 
         * @return
         */
        function rewriteSmartyTPL() {
            global $smarty, $CMSDATA;
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $LANGS[$row['id']] = $row;
            }

            # SMARTY Templates anlegen
            $TEMPL_OBJ = new template_class(1, $CMSDATA);
            foreach ($LANGS as $id => $rowl) {
                $this->delDirWithSubDirs(SMARTY_TEMPDIR . $LANGS[$rowl['id']]['local']);
            }


            foreach ($LANGS as $id => $rowl) {
                $result = $this->db->query("SELECT *,T.id AS TID,TC.id AS TCID FROM " . TBL_CMS_TEMPLATES . " T
		LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $rowl['id'] . " AND T.id=TC.tid)
		WHERE T.gbl_template=1 OR T.c_type='B'");
                while ($row = $this->db->fetch_array_names($result)) {
                    $row['lang_id'] = $rowl['id'];
                    $row = $this->fill_content($row);
                    $TEMPL_OBJ->save_tpl_file($row);
                }
            }
        }


        /**
         * upt_class::import_single_layout()
         * 
         * @param string $layout
         * @return
         */
        function import_single_layout($layout = 'std_layout') {
            unset($FORM);
            $FORM['layout'] = $this->curl_get_data(RESTSERVER . '?layout=' . $layout . '&cmd=getlayout');
            if ($FORM['layout'] != "") {
                file_put_contents($layout . '.tpl', stripslashes($FORM['layout']));
            }
        }


        /**
         * upt_class::upt_sql()
         * 
         * @param mixed $sql
         * @return
         */
        function upt_sql($sql) {
            $sql = str_replace("!!TBL_CMS_PREFIX!!", TBL_CMS_PREFIX, trim($sql));
            $sql_lines = array();
            $sql_lines = explode("!#!", $sql);
            foreach ($sql_lines as $key => $sql_exec) {
                if ($sql_exec != "")
                    $this->execSQL($sql_exec);
            }
        }

        /**
         * upt_class::upt_lang()
         * 
         * @return
         */
        function upt_lang() {
            $sql = $this->curl_get_data(UPDATE_SERVER . 'lang.sql');
            $sql = str_replace("!!TBL_CMS_PREFIX!!", TBL_CMS_PREFIX, trim($sql));
            $languages = array();
            $languages = explode("!^!", $sql);
            foreach ($languages as $key => $lang) {
                $lang_obj = explode("!°!", $lang);
                $lang_key = $this->db->query_first("SELECT langarray,id FROM " . TBL_CMS_LANG . " WHERE id='" . $lang_obj[0] . "'");
                # protection: altes format darf nicht manipuliert werden, unrelevant ab version 1.0.3.5
                if (strstr($lang_key['langarray'], '!:!') && strstr($lang_key['langarray'], '!#!')) {
                    return;
                }
                $trans_arr_org = unserialize($lang_key['langarray']);
                $trans_arr_org = (array )$trans_arr_org;
                if ($lang_key['id'] > 0) {
                    $trans_arr = unserialize($lang_obj[1]);
                    foreach ($trans_arr as $joker => $value) {
                        if (!array_key_exists($joker, $trans_arr_org)) {
                            $trans_arr_org[$joker] = $this->db->real_escape_string($value);
                        }
                    }
                }
                $FORM = array('langarray' => serialize($trans_arr_org));
                $this->update_table4UpdateClass(TBL_CMS_LANG, 'id', $lang_obj[0], $FORM);
            }
        }


        /**
         * upt_class::install_lang()
         * 
         * @return
         */
        function install_lang() {
            $sql = $this->curl_get_data(UPDATE_SERVER . 'lang.sql');
            $sql = str_replace("!!TBL_CMS_PREFIX!!", TBL_CMS_PREFIX, trim($sql));
            $languages = array();
            $languages = explode("!^!", $sql);
            $this->db->query("DELETE FROM " . TBL_CMS_LANG);
            foreach ($languages as $key => $lang) {
                $lang_obj = explode("!°!", $lang);
                $z++;
                $this->db->query("INSERT INTO " . TBL_CMS_LANG . " (id,langarray,post_lang,approval,language,s_order) VALUES ('" . $lang_obj[0] . "','" . $this->db->
                    real_escape_string($lang_obj[1]) . "','" . $lang_obj[2] . "',1,'" . $lang_obj[3] . "'," . $lang_obj[4] . ")");
            }
        }


        /**
         * upt_class::install_lang_admin()
         * 
         * @return
         */
        function install_lang_admin() {
            $sql = $this->curl_get_data(UPDATE_SERVER . 'lang_admin.sql');
            $sql = str_replace("!!TBL_CMS_PREFIX!!", TBL_CMS_PREFIX, trim($sql));
            $languages = array();
            $languages = explode("!^!", $sql);
            $this->db->query("DELETE FROM " . TBL_CMS_LANG_ADMIN);
            foreach ($languages as $key => $lang) {
                $lang_obj = explode("!°!", $lang);
                $z++;
                $this->db->query("INSERT INTO " . TBL_CMS_LANG_ADMIN . " (id,post_lang,approval,language,s_order,bild) VALUES ('" . $lang_obj[0] . "','" . $lang_obj[1] .
                    "',1,'" . $lang_obj[2] . "'," . $lang_obj[3] . ",'" . $lang_obj[4] . "')");
            }
        }

        /**
         * upt_class::update_admin_langpack()
         * 
         * @return
         */
        function update_admin_langpack() {
            $ALANG_OBJ = new adminlang_class();
            $ALANG_OBJ->complete_lang_missing();
        }


        /**
         * upt_class::upt_generalsql()
         * 
         * @return
         */
        function upt_generalsql() {
            $sql = $this->curl_get_data(UPDATE_SERVER . 'cms_update.sql');
            $this->upt_sql($sql);
        }


        /**
         * upt_class::save_log()
         * 
         * @return
         */
        function save_log() {
            if (!is_dir(CMS_ROOT . 'admin/logs'))
                mkdir(CMS_ROOT . 'admin/logs', 0775);
            if (file_exists("logs/log_error.log"))
                @unlink("logs/log_error.log");
            $fp = fopen("logs/log_error.log", "w+");
            fwrite($fp, $this->err_log);
            fclose($fp);
            if (file_exists("logs/log_successfull.log"))
                @unlink("logs/log_successfull.log");
            $fp = fopen("logs/log_successfull.log", "w+");
            fwrite($fp, $this->ok_log);
            fclose($fp);
        }


        /**
         * upt_class::clean_gblconfig()
         * 
         * @return
         */
        function clean_gblconfig() {
            return; # wichtig wegen Modul Aufbau
            $sql = $this->curl_get_data(RESTSERVER . '?cmd=gblconfig_update');
            $pri_keys = explode("!#!", $sql);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLCONFIG);
            while ($row = $this->db->fetch_array_names($result)) {
                if (!in_array($row['config_name'], $pri_keys)) {
                    $this->execSQL("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='" . $row['config_name'] . "' LIMIT 1");
                }
            }
        }


        /**
         * upt_class::clean_gblconfig_groups()
         * 
         * @return
         */
        function clean_gblconfig_groups() {
            return; # wichtig wegen Modul Aufbau
            $sql = $this->curl_get_data(RESTSERVER . '?cmd=gblconfig_groups_update');
            $pri_keys = explode("!#!", $sql);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_CONFGROUPS);
            while ($row = $this->db->fetch_array_names($result)) {
                if (!in_array($row['id'], $pri_keys)) {
                    $this->execSQL("DELETE FROM " . TBL_CMS_CONFGROUPS . " WHERE id='" . $row['id'] . "' LIMIT 1");
                }
            }
        }


        /**
         * upt_class::upt_tar_files_notoverwrite()
         * 
         * @param mixed $tar_file
         * @return
         */
        public static function upt_tar_files_notoverwrite($tar_file) {
            self::curl_get_data_to_file(UPDATE_SERVER . $tar_file, '../' . $tar_file);
            self::untar_archive(CMS_ROOT . $tar_file, CMS_ROOT, false);
            # exec('tar -C ../ -kxpvvzf ../' . $tar_file);
            # unlink('../' . $tar_file);
        }


        /**
         * upt_class::upt_tar_files()
         * 
         * @param mixed $tar_file
         * @return
         */
        function upt_tar_files($tar_file) {
            $this->curl_get_data_to_file(UPDATE_SERVER . $tar_file, '../' . $tar_file);
            # exec('tar -C ../ -xpvzf ../' . $tar_file);
            self::untar_archive(CMS_ROOT . $tar_file, CMS_ROOT, true);
        }


        /**
         * upt_class::upt_pictures()
         * 
         * @return
         */
        function upt_pictures() {
            $this->upt_tar_files_notoverwrite('default_images.tar.gz');
        }


        /**
         * upt_class::repair_db()
         * 
         * @return
         */
        function repair_db() {
            global $HTA_CLASS_CMS;
            $NO_FAILURE = false;
            $ver_info = $this->db->query_first("SELECT * FROM " . TBL_CMS_CONFIG . " WHERE ID_STR='VERSION' LIMIT 1");
            $parts = explode('.', trim($ver_info['wert']));
            if (count($parts) > 4)
                array_pop($parts);
            #$local_version = (int)trim(str_replace(".", "", $ver_info['wert']));
            $local_version = (int)trim(implode("", $parts));
            $updfiles = array();
            $dh = opendir(CMS_ROOT . 'admin/updatescripts');
            while (false !== ($filename = readdir($dh))) {
                $file_ver = (int)trim(str_replace(array('update.to.', '.php'), '', $filename));

                if (strstr($filename, 'update.to.')) {
                    if ((int)$local_version < (int)$file_ver) {
                        $updfiles[$file_ver] = $filename;
                    }
                }
            }
            $upt_script_count = 0;
            if (is_array($updfiles)) {
                ksort($updfiles);
                foreach ($updfiles as $file_var => $filename) {
                    if ($local_version < $file_var) { //doppelte Sicherheit
                        $NO_FAILURE = false;
                        include (CMS_ROOT . 'admin/updatescripts/' . $filename);
                        $upt_script_count++;
                    }
                }
            }
            if ($upt_script_count == 0 && $NO_FAILURE == false) {
                $NO_FAILURE = true;
            }
            include_once (CMS_ROOT . 'admin/inc/htaedit.class.php');
            $HTA = new htaedit_class();
            $HTA->buildHTACCESS();
            $this->import_missing_templates();
            $this->rewriteSmartyTPL();

            # if all update scripts ran well, update to final version
            if ($NO_FAILURE == true) {
                $this->set_remote_version();
            }
        }

        /**
         * upt_class::set_remote_version()
         * 
         * @return
         */
        function set_remote_version() {
            $VERSION = keimeno_class::curl_get_data(RESTSERVER . "?cmd=getversion");
            $this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $VERSION . "' WHERE ID_STR='VERSION' LIMIT 1");
        }


        /**
         * upt_class::validate_dirs()
         * 
         * @return
         */
        function validate_dirs() {
            if (!is_dir(CMS_ROOT . 'cache'))
                mkdir(CMS_ROOT . 'cache', 0755);
            if (!is_dir(CMS_ROOT . 'fonts'))
                mkdir(CMS_ROOT . 'fonts', 0755);
            if (!is_dir(CMS_ROOT . 'file_server'))
                mkdir(CMS_ROOT . 'file_server', 0755);
            if (!is_dir(CMS_ROOT . 'file_data'))
                mkdir(CMS_ROOT . 'file_data', 0755);
            if (!is_dir(CMS_ROOT . 'file_data/tplimg'))
                mkdir(CMS_ROOT . 'file_data/tplimg', 0755);
            if (!is_dir(CMS_ROOT . 'images/src'))
                mkdir(CMS_ROOT . 'images/src', 0755);
            if (!is_dir(CMS_ROOT . 'file_server/members'))
                mkdir(CMS_ROOT . 'file_server/members', '0777');
            if (!is_dir(CMS_ROOT . 'images/members'))
                mkdir(CMS_ROOT . 'images/members', 0755);
        }

        /**
         * upt_class::replaceInTemplatesOnlyCustomers()
         * 
         * @param mixed $tpl_rep
         * @return
         */
        function replaceInTemplatesOnlyCustomers($tpl_rep) {
            $result = $this->db->query("SELECT C.* FROM " . TBL_CMS_TEMPCONTENT . " C, " . TBL_CMS_TEMPLATES . " T
  WHERE C.tid=T.id AND T.gbl_template=0 AND T.admin=0");
            while ($row = $this->db->fetch_array_names($result)) {
                $html = $row['content'];
                $found = false;
                foreach ($tpl_rep as $suchwort => $ersetzewort) {
                    $suchwort = stripslashes($suchwort);
                    if (strstr(strtolower($html), strtolower($suchwort))) {
                        $html = str_ireplace($suchwort, stripslashes($ersetzewort), $html, $rep_count);
                        $found = true;
                    }
                }
                if ($found == TRUE)
                    $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string($html) . "' WHERE id='" . $row['id'] . "' LIMIT 1");
            }
        }

        /**
         * upt_class::replaceInTemplates()
         * 
         * @param mixed $tpl_rep
         * @param string $tid
         * @return
         */
        function replaceInTemplates($tpl_rep, $tid = "") {
            foreach ($tpl_rep as $suchwort => $ersetzewort) {
                $this->db->query("UPDATE " . TBL_CMS_TEMPMATRIX . " SET tm_content = REPLACE(tm_content, '" . $suchwort . "', '" . $ersetzewort . "')");
                $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content = REPLACE(content, '" . $suchwort . "', '" . $ersetzewort . "')");
            }
        }

        /**
         * upt_class::replace_in_emails()
         * 
         * @param mixed $tpl_rep
         * @return
         */
        function replace_in_emails($tpl_rep) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_MAILTEMP_CONTENT);
            while ($row = $this->db->fetch_array_names($result)) {
                $html = $row['content'];
                $found = false;
                foreach ($tpl_rep as $suchwort => $ersetzewort) {
                    if (strstr(strtolower($html), strtolower($suchwort))) {
                        $html = str_ireplace($suchwort, $ersetzewort, $html, $rep_count);
                        $found = true;
                    }
                }
                if ($found == TRUE)
                    $this->db->query("UPDATE " . TBL_CMS_MAILTEMP_CONTENT . " SET content='" . $this->db->real_escape_string($html) . "' WHERE id='" . $row['id'] . "' LIMIT 1");
            }
        }


        /**
         * upt_class::clean_folders()
         * 
         * @param mixed $folder
         * @return
         */
        function clean_folders($folder) {
            $folder = CMS_ROOT . $folder . '/';
            $dh = opendir($folder);
            while (false !== ($filename = readdir($dh))) {
                if ($filename != '.' && $filename != '..' && is_file($folder . $filename) && GetExt($folder . $filename) != 'php') {
                    if (GetExt($folder . $filename) == 'bak' || GetExt($folder . $filename) == 'tmp')
                        unlink($folder . $filename);
                }
            }
        }

        /**
         * upt_class::delDirWithSubDirs()
         * 
         * @param mixed $dir
         * @return
         */
        function delDirWithSubDirs($dir) {
            $this->delete_dir_with_subdirs($dir);
        }


        /**
         * upt_class::installRobottxt()
         * 
         * @return
         */
        function installRobottxt() {
            $content = 'User-agent: *
Allow: /
Disallow: /admin
Disallow: /smarty
';
            if (!file_exists(CMS_ROOT . 'robots.txt'))
                @file_put_contents(CMS_ROOT . 'robots.txt', $content);
        }

        /**
         * upt_class::fill_content()
         * 
         * @param mixed $row
         * @return
         */
        function fill_content($row) {
            if ($row['content'] == '' && (int)$row['TCID'] == 0) {
                $FC = $this->db->query_first("SELECT TC.* FROM " . TBL_CMS_TEMPLATES . " T
		LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=1 AND T.id=TC.tid)
		WHERE T.id=" . $row['TID'] . "
		");
                $FC['lang_id'] = $row['lang_id'];
                unset($FC['TCID']);
                unset($FC['id']);
                foreach ($FC as $key => $wert)
                    $FC[$key] = $this->db->real_escape_string($FC[$key]);
                $id = insert_table(TBL_CMS_TEMPCONTENT, $FC);
                $row = $FC;
            }
            return $row;
        }

        /**
         * upt_class::init_lang_codes()
         * 
         * @param bool $overwrite
         * @return
         */
        function init_lang_codes($overwrite = false) {
            if ($overwrite === true)
                $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local=''");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='de' WHERE id=1 AND local='' LIMIT 1");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='en' WHERE id=2 AND local='' LIMIT 1");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='es' WHERE id=4 AND local='' LIMIT 1");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='it' WHERE id=6 AND local='' LIMIT 1");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='jp' WHERE id=7 AND local='' LIMIT 1");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='cn' WHERE id=3 AND local='' LIMIT 1");
            $this->db->query("UPDATE " . TBL_CMS_LANG . " SET local='fr' WHERE id=5 AND local='' LIMIT 1");
        }


        /**
         * upt_class::create_smarty_cache_roots()
         * 
         * @return
         */
        function create_smarty_cache_roots() {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $d = CMS_ROOT . 'smarty/templates_c/' . $row['local'];
                if (!is_dir($d))
                    mkdir($d, 0777);
            }
            $dh = opendir(CMS_ROOT . 'smarty/templates_c');
            while (false !== ($filename = readdir($dh))) {
                if ($filename != '.' && $filename != '..') {
                    @unlink(CMS_ROOT . 'smarty/templates_c/' . $filename);
                }
            }
        }

        /**
         * upt_class::import_missing_lngpacks()
         * 
         * @return
         */
        function import_missing_lngpacks() {
            $ALANG_OBJ = new adminlang_class();
            $ALANG_OBJ->complete_lang_missing();
            unset($ALANG_OBJ);
        }

        /**
         * upt_class::set_htaccess_admin()
         * 
         * @return
         */
        function set_htaccess_admin() {
            if (!file_exists(CMS_ROOT . 'admin/.htaccess')) {
                $hta = 'RewriteEngine On
		Options +FollowSymLinks

		RewriteCond %{QUERY_STRING} http[:%] [NC]
		RewriteRule .* /-http- [F,NC]
		RewriteRule http: /-http- [F,NC]

		RewriteRule ^welcome.html ' . PATH_CMS . 'admin/run.php?epage=welcome.inc&%{QUERY_STRING} [L]
		RewriteRule ^logout.html ' . PATH_CMS . 'admin/index.php?cmd=logout [L]
		RewriteRule ^login.html ' . PATH_CMS . 'admin/index.php?&%{QUERY_STRING} [L]
		';
                file_put_contents(CMS_ROOT . 'admin/.htaccess', $hta);
            }
        }

        /**
         * upt_class::clean_admin_smarty_cache()
         * 
         * @return
         */
        function clean_admin_smarty_cache() {
            $C = new crj_class();
            $C->clean_admin();
            unset($C);
        }

        /**
         * upt_class::rebuild_admin_group_menu_permissions()
         * 
         * @return
         */
        function rebuild_admin_group_menu_permissions() {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_MENU);
            while ($row = $this->db->fetch_array_names($result)) {
                $ids[] = $row['id'];
            }
            sort($ids);
            $this->db->query("UPDATE " . TBL_CMS_ADMINGROUPS . " SET allowed='" . implode(';', $ids) . "' WHERE id=1");
        }


        /**
         * upt_class::backup_individuel_settings()
         * 
         * @return
         */
        function backup_individuel_settings() {
            # exec("cd ..;tar cvpzf ./modsik.tar.gz --exclude=*.php --exclude=*.tpl --exclude=config.xml ./includes/modules");
            if (file_exists(CMS_ROOT . 'modsik.tar')) {
                @unlink(CMS_ROOT . 'modsik.tar');
            }
            self::tar_archive(CMS_ROOT . 'includes/modules', CMS_ROOT . 'modsik.tar', array(
                '.php',
                '.tpl',
                '.jpg',
                'config.xml'));

            # remember which one is active
            $_SESSION['modactivelist'] = array();
            $dh = opendir(MODULE_ROOT);
            while (false !== ($module_loader_dir = readdir($dh))) {
                if ($module_loader_dir != '.' && $module_loader_dir != '..' && $module_loader_dir != '' && is_dir(MODULE_ROOT . $module_loader_dir)) {
                    $fname = MODULE_ROOT . $module_loader_dir . '/config.xml';
                    if (file_exists($fname)) {
                        $xml_modul = simplexml_load_file($fname);
                        $_SESSION['modactivelist'][strval($xml_modul->module->settings->id)] = strval($xml_modul->module->settings->active);
                    }
                }
            }
        }

        /**
         * upt_class::restore_mod_settings()
         * 
         * @return
         */
        function restore_mod_settings() {
            #  exec('cd ..;tar -C ./ -xpvzf ./modsik.tar.gz');
            self::untar_archive(CMS_ROOT . 'modsik.tar.gz', CMS_ROOT . 'includes/modules');
            #@unlink(CMS_ROOT . "modsik.tar.gz");
            $_SESSION['modactivelist'] = (array )$_SESSION['modactivelist'];
            foreach ($_SESSION['modactivelist'] as $modid => $value) {
                $fname = MODULE_ROOT . $modid . '/config.xml';
                if (file_exists($fname)) {
                    $xml_modul = simplexml_load_file($fname);
                    $xml_modul->module->settings->active = $value;
                    $dom = new DOMDocument('1.0');
                    $dom->preserveWhiteSpace = false;
                    $dom->formatOutput = true;
                    $dom->loadXML($xml_modul->asXML());
                    $dom->save($fname);
                    unset($dom);
                }
            }
        }

        /**
         * upt_class::remove_adminmenu_id()
         * 
         * @param mixed $id
         * @return
         */
        function remove_adminmenu_id($id) {
            $this->db->query("DELETE FROM " . TBL_CMS_MENU . " WHERE id=" . $id);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $MC = $this->db->query_first("SELECT * FROM " . TBL_CMS_ADMINGROUPS . " WHERE id=" . $row['id']);
                $ids = explode(';', $MC['allowed']);
                $this->db->query("UPDATE " . TBL_CMS_ADMINGROUPS . " SET allowed='" . implode(';', array_diff($ids, array((int)$id))) . "' WHERE id=" . $row['id']);
            }
        }

        /**
         * upt_class::activate_mod()
         * 
         * @param mixed $modid
         * @return
         */
        function activate_mod($modid) {
            if (!class_exists('moduleman_class'))
                include_once (CMS_ROOT . 'admin/inc/modulman.class.php');
            $MODMAN = new moduleman_class();
            $fname = CMS_ROOT . 'includes/modules/' . $modid . '/config.xml';
            $xml_modul = simplexml_load_file($fname);
            $xml_modul->module->settings->active = 'true';
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml_modul->asXML());
            $dom->save($fname);
            $MODMAN->install_admin_menu($modid);
            app_class::generate_all_module_xml();
        }

        /**
         * upt_class::run_autoupdate_off_installed_mods()
         * 
         * @return
         */
        function run_autoupdate_off_installed_mods() {
            $dh = opendir(MODULE_ROOT);
            while (false !== ($modid = readdir($dh))) {
                if ($modid != '.' && $modid != '..' && $modid != 'default_mod' && $modid != '' && is_dir(MODULE_ROOT . $modid)) {
                    $fname = MODULE_ROOT . $modid . '/setup/setup.class.php';
                    if (file_exists($fname) && is_file($fname)) {
                        require_once ($fname);
                        $modclassname = $modid . '_setup_class';
                        $MCLASS = new $modclassname();
                        if (method_exists($MCLASS, 'autoupdate')) {
                            $MCLASS->autoupdate();
                        }
                        $this->allocate_memory($MCLASS);
                    }
                }
            }
        }

        /**
         * upt_class::transform_langtable_to_array()
         * 
         * @return
         */
        function transform_langtable_to_array() {
            $new_arr = array();
            $result = $this->db->query("SELECT id,langarray FROM " . TBL_CMS_LANG);
            while ($row = $this->db->fetch_array_names($result)) {
                $new_arr = array();
                if (strstr($row['langarray'], '!:!')) {
                    $arr = explode('!#!', $row['langarray']);
                    foreach ($arr as $key => $lng) {
                        list($joker, $value) = explode('!:!', $lng);
                        if ($joker != "")
                            $new_arr[$joker] = $this->db->real_escape_string($value); #stripslashes($value);
                        #; $this->db->real_escape_string($value);
                    }
                    $this->db->query("UPDATE " . TBL_CMS_LANG . " SET langarray = '" . serialize($new_arr) . "' WHERE id = '" . $row['id'] . "'");
                }
            }
            $new_arr = array();
            $result = $this->db->query("SELECT id,langarray FROM " . TBL_CMS_LANG_CUST);
            while ($row = $this->db->fetch_array_names($result)) {
                $new_arr = array();
                if (strstr($row['langarray'], '!:!')) {
                    $arr = explode('!#!', $row['langarray']);
                    foreach ($arr as $key => $lng) {
                        list($joker, $value) = explode('!:!', $lng);
                        if ($joker != "")
                            $new_arr[$joker] = $this->db->real_escape_string($value);
                    }
                    $this->db->query("UPDATE " . TBL_CMS_LANG_CUST . " SET langarray = '" . serialize($new_arr) . "' WHERE id = '" . $row['id'] . "'");
                }
            }
        }


        /**
         * upt_class::update_database()
         * 
         * @return void
         */
        function update_database() {
            $this->upt_generalsql();
        }

        /**
         * upt_class::mass_tar_overwrite()
         * 
         * @param mixed $filelist
         * @return
         */
        public static function mass_tar_overwrite($filelist) {
            foreach ($filelist as $key => $tar_file_gz) {
                self::curl_get_data_to_file(UPDATE_SERVER . $tar_file_gz, '../' . $tar_file_gz);
            }
            sleep(1);
            foreach ($filelist as $key => $tar_file_gz) {
                self::untar_archive(CMS_ROOT . $tar_file_gz, CMS_ROOT, true);
            }


            /*  foreach ($filelist as $key => $tar_file) {
            $this->curl_get_data_to_file(UPDATE_SERVER . $tar_file, '../' . $tar_file);
            }
            sleep(1);
            foreach ($filelist as $key => $tar_file) {
            exec('tar -C ../ -xpvzf ../' . $tar_file);
            @unlink('../' . $tar_file);
            }
            */
        }

        /**
         * upt_class::file_update()
         * 
         * @return void
         */
        function file_update() {
            $this->getPHPini();
            $this->create_smarty_cache_roots();
            $this->backup_individuel_settings();

            $list = array(
                'includes.tar.gz',
                'smarty.tar.gz',
                'admin.tar.gz',
                'default_files.tar.gz',
                'fonts.tar.gz',
                );
            self::mass_tar_overwrite($list);
        }

        /**
         * upt_class::complete_update()
         * 
         * @return
         */
        function complete_update() {
            $this->clean_admin_smarty_cache();
            $this->update_admin_langpack();
            $this->clean_folders('/admin');
            $this->clean_folders('/includes');
            $this->clean_folders('/');
            $this->installRobottxt();
            $this->set_htaccess_admin();
            $this->install_countries();
            $this->rebuild_admin_group_menu_permissions();
            $this->save_log();
            $this->restore_mod_settings();
            $this->run_autoupdate_off_installed_mods();
            $this->set_default_file_permissions();
            kf::load_permissions();

            include_once (CMS_ROOT . 'admin/inc/modulman.class.php');
            $MODMANCLASS = new moduleman_class();

            app_class::generate_all_module_xml();
            $MODMANCLASS->uninstall_complete_admin_menu();
            $MODMANCLASS->install_admin_menu();

            unset($MODMANCLASS);

        }

    }
