<?php

/**
 * @package    sitemap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class SiteMap extends keimeno_class {
    var $pages = array();
    var $file;
    var $host = '';
    var $table = '';
    var $table_lang = '';
    var $table_smdefs = '';
    var $db = NULL;
    var $root = "";
    var $sitemap_name = "";

    /**
     * SiteMap::__construct()
     * 
     * @param mixed $table
     * @param mixed $table_lang
     * @param mixed $table_smdefs
     * @param mixed $kdb
     * @param mixed $root
     * @return
     */
    function __construct($table, $table_lang, $table_smdefs, $kdb, $root) {
        parent::__construct();
        $this->table = $table;
        $this->table_lang = $table_lang;
        $this->table_smdefs = $table_smdefs;
        $this->root = $root;
        $this->sitemap_name = 'sitemap';
        $this->set_sitemap_name($this->sitemap_name);
    }

    /**
     * SiteMap::set_sitemap_name()
     * 
     * @param mixed $name
     * @return
     */
    function set_sitemap_name($name) {
        $this->sitemap_name = $name;
        $this->file = $name . '.xml';
    }

    /**
     * SiteMap::genPrio()
     * 
     * @param mixed $sel
     * @return
     */
    function genPrio($sel) {
        for ($i = 0; $i <= 1; $i += 0.1) {
            $sel = (string )$sel;
            $k = (string )$i;
            $t .= '<option ' . (($sel == $k) ? 'selected' : '') . ' value="' . $i . '">' . $i . '</option>';
        }
        return $t;
    }

    /**
     * SiteMap::genFreq()
     * 
     * @param mixed $sel
     * @return
     */
    function genFreq($sel) {
        $d_arr = array("daily", "weekly");
        foreach ($d_arr as $key => $value) {
            $t .= '<option ' . (($sel == $value) ? 'selected' : '') . ' value="' . $value . '">' . $value . '</option>';
        }
        return $t;
    }

    /**
     * SiteMap::saveSMConf()
     * 
     * @param mixed $FORM
     * @return
     */
    function saveSMConf($FORM) {
        $SQL_ARR = array();
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $FORM_SET) {
                if (count($FORM_SET) > 0) {
                    foreach ($FORM_SET as $id => $value) {
                        $SQL_ARR[$id][$key] = $value;
                    }
                }
            }
        }
        if (count($SQL_ARR) > 0) {
            foreach ($SQL_ARR as $id => $SQL_SET) {
                update_table($this->table_smdefs, 'id', $id, $SQL_SET);
                if ($SQL_SET['sm_key'] == "")
                    $SQL_SET['sm_key'] = $SQL_SET['sm_file'];
                if ($SQL_SET['sm_file'] != "") {
                    $SM = $this->db->query_first("SELECT * FROM " . $this->table_smdefs . " WHERE id=" . intval($id) . " LIMIT 1");
                    if ($SM['sm_filetemplate'] != "") {
                        $SM['sm_filetemplate'] = str_replace('{TMPL_AUTHKEY}', $SQL_SET['sm_key'], $SM['sm_filetemplate']);
                        file_put_contents($this->root . $SQL_SET['sm_file'], $SM['sm_filetemplate']);
                    }
                    else {
                        file_put_contents($this->root . $SQL_SET['sm_file'], $SQL_SET['sm_key']);
                    }
                }
            }
        }
    }

    /**
     * SiteMap::delXMLFile()
     * 
     * @param mixed $fname
     * @return
     */
    function delXMLFile($fname) {
        $fname = base64_decode($fname);
        if (strstr($fname, $this->sitemap_name) && strstr($fname, '.xml'))
            @unlink($this->root . $fname);
    }

    /**
     * SiteMap::sendXMLFile()
     * 
     * @param mixed $id
     * @param mixed $fname
     * @return
     */
    function sendXMLFile($id, $fname) {
        $SM = $this->db->query_first("SELECT * FROM " . $this->table_smdefs . " WHERE id=" . intval($id) . " LIMIT 1");
        $SM['sm_lastsend'] = time();
        $SM['sm_count']++;
        unset($SM['id']);
        update_table($this->table_smdefs, 'id', intval($id), $SM);
        $link = str_replace('[sitemap]', $this->host . $fname, $SM['sm_url']);
        header('location:' . $link);
        exit;
    }

    /**
     * SiteMap::genInputField()
     * 
     * @param mixed $word
     * @param integer $maxsize
     * @return
     */
    function genInputField($word, $maxsize = 26) {
        $word = htmlspecialchars(stripslashes($word));
        return ' type="text" class="form-control" value="' . $word . '" size="' . ((strlen($word) > $maxsize) ? strlen($word) + 3 : $maxsize) . '" ';
    }

    /**
     * SiteMap::configTable()
     * 
     * @return
     */
    function configTable() {
        $result = $this->db->query("SELECT * FROM	" . $this->table);
        while ($row = $this->db->fetch_array_names($result)) {
            $tr .= '<tr>
		<td>' . $row['sm_title'] . '</td>
		<td><select class="form-control" name="FORM[' . $row['id'] . '][sm_changefreq]">' . $this->genFreq($row['sm_changefreq']) . '</select></td>
		<td><select class="form-control" name="FORM[' . $row['id'] . '][sm_priority]">' . $this->genPrio($row['sm_priority']) . '</select></td>
		<td>' . kf::gen_approve_icon($row['id'], $row['sm_active'], 'a_smapprove') . '</td>
		</tr>';
        }
        $t .= '<option ' . (($_POST['sm_lang'] == -1) ? 'selected' : '') . ' value="-1">alle/all</option>';
        $result_lang = $this->db->query("SELECT id,post_lang,language FROM " . $this->table_lang . " WHERE approval=1 ORDER BY post_lang");
        while ($rowl = $this->db->fetch_array($result_lang)) {
            $t .= '<option ' . (($_POST['sm_lang'] == $rowl['id']) ? 'selected' : '') . ' value="' . $rowl['id'] . '">' . $rowl['post_lang'] . '</option>';
        }
        $content .= '
        <form action="' . $_SERVER['PHP_SELF'] . '" method="post" class="jsonform">
	<input type="hidden" name="cmd" value="a_googlemaps">
    <input type="hidden" name="epage" value="' . $_REQUEST['epage'] . '">
	<table class="table table-striped table-hover">' . $tr . '
	<tr>
	<td>{LBL_LANGUAGE}:</td>
	<td colspan="3"><select class="form-control" name="sm_lang">' . $t . '</select></td>
	</tr></table>
	' . kf::gen_admin_sub_btn('Generate') . '
	</form>';

        $tab = "";
        $dh = opendir($this->root);
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..' && strstr($filename, ".xml") && strstr($filename, $this->sitemap_name)) {
                $tab .= '<tr>
					<td><a title="download [' . $filename . ']" target="_blank" href="../' . $filename . '">SiteMap ' . $this->host . basename($filename) . '</a></td>
					<td><a title="{LBL_DELETE} [' . $filename . ']"  href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&aktion=delsitemap&file=' .
                    base64_encode($filename) . '"><i class="fa fa-trash"></i></a></td>
					</tr>';
            }
        }
        if ($tab != "") {
            $content .= '<h3>Download</h3><table class="table table-striped table-hover">' . $tab . '</table>
        <h3>Register</h3><form action="' . $_SERVER['PHP_SELF'] . '" method="post" class="stdform form-inline"><table class="table table-striped table-hover">';
            $result = $this->db->query("SELECT * FROM " . $this->table_smdefs . " WHERE 1 ORDER BY sm_service");
            while ($row = $this->db->fetch_array($result)) {
                $content .= '
		  <thead><tr>
		  	<th>' . $row['sm_service'] . '</th>
		  	<th class="text-right">Last register:<em>' . (($row['sm_lastsend'] > 0) ? date('d.m.Y - H:i:s', $row['sm_lastsend']) : '-') . '</em>
		  	 | Send:<em>' . $row['sm_count'] . '</em></th>
		  </tr></thead>
		  ' . (($row['sm_url'] != '-') ? '<tr><td>Eintragungs-URL:</td><td><input class="form-disabled" size="' . (strlen($row['sm_url']) + 3) .
                    '" disabled="disabled" type="text" class="form-control" value="' . htmlspecialchars($row['sm_url']) . '" name="FORM[sm_url][' . $row['id'] . ']"></td></tr>' :
                    '') . '
		  ' . (($row['sm_key'] != '-') ? '<tr><td>Authentifizierungsschlüssel:</td><td><input ' . $this->genInputField($row['sm_key'], 30) . ' name="FORM[sm_key][' .
                    $row['id'] . ']"></td></tr>' : '') . '
		  ' . (($row['sm_file'] != '-') ? '<tr><td>Authentifizierungsdatei:</td><td><input ' . $this->genInputField($row['sm_file'], 30) . ' name="FORM[sm_file][' . $row['id'] .
                    ']"></td></tr>' : '') . '
		 ';
                $content .= '<tr><td colspan="2" style="border-bottom: 1px dotted;">';
                static $add_links = "";
                if (file_exists($this->root . $this->sitemap_name . '.xml')) {
                    $add_links = '	<a title="register ' . $this->sitemap_name . '.xml [all languages]" href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&id=' . $row['id'] .
                        '&aktion=sendsmxml&fname=' . $this->sitemap_name . '.xml" target="_sm">Eintragen [' . $this->sitemap_name . '.xml]</a>';
                }
                $result_lang = $this->db->query("SELECT * FROM " . $this->table_lang . " WHERE approval=1 ORDER BY post_lang");
                while ($rowl = $this->db->fetch_array($result_lang)) {
                    if (file_exists($this->root . $this->sitemap_name . '_lang_' . $rowl['local'] . '.xml')) {
                        $add_links .= (($add_links != "") ? ' | ' : '') . '	<a title="register [' . $rowl['local'] . '] sitemap" href="' . $_SERVER['PHP_SELF'] . '?id=' . $row['id'] .
                            '&aktion=sendsmxml&fname=' . $this->sitemap_name . '_lang_' . $rowl['local'] . '.xml" target="_sm' . $row['id'] . '">Eintragen [' . $rowl['local'] . ']</a>';
                    }
                }
                $content .= $add_links . ((strlen($row['sm_file']) > 3) ? ' | <a href="' . $this->host . $row['sm_file'] . '" target="_sm">Verify Link</a>' : '') . '
		  ' . ((strlen($row['sm_servicelink']) > 0) ? ' | <a title="' . $row['sm_servicelink'] . '" href="' . $row['sm_servicelink'] .
                    '" target="_smservice">Service Link</a>' : '') . '
		  	</td></tr>';
            }
            $content .= '</table><input type="hidden" name="epage" value="' . $_REQUEST['epage'] . '">
            <input type="hidden" name="cmd" value="a_savesmconf">' . kf::gen_admin_sub_btn('{LA_SAVE}') . '
            </form>
            <h3>Preview</h3>' . ((file_exists(CMS_ROOT . $this->file) ? '<iframe width="900" height="900" src="../' . $this->file . '"></iframe>' : '')) . '
		';
        }
        return $content;
    }
    /**
     * SiteMap::escape_xml()
     * 
     * @param mixed $string
     * @return
     */
    private static function escape_xml($string) {
        return str_replace(array(
            '&',
            '"',
            "'",
            '<',
            '>'), array(
            '&amp;',
            '&quot;',
            '&apos;',
            '&lt;',
            '&gt;'), $string);
    }

    /**
     * SiteMap::create()
     * 
     * @return
     */
    public static function create($pages) {
        $str = self::xmlHeader() . self::getPages(self::convert_pages($pages)) . self::xmlFooter();
        self::write2file(CMS_ROOT . 'sitemap.xml', $str);
    }
    /**
     * SiteMap::xmlHeader()
     * 
     * @return
     */
    private static function xmlHeader() {
        $str = '<?xml version="1.0" encoding="UTF-8"?>
        <?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>
        <urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" 
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"  
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
        return $str;
    }

    /**
     * SiteMap::xmlFooter()
     * 
     * @return
     */
    private static function xmlFooter() {
        $str = '
		</urlset>
		';
        return $str;
    }

    /**
     * SiteMap::add_images()
     * 
     * @return
     */
    private static function add_images(array $images) {
        $c = "";
        if (count($images) > 0) {
            $c = '<image:image>';
            foreach ($images as $image) {
                $c .= '
                        <image:loc>' . $image['loc'] . '</image:loc>
                        <image:caption>' . $image['caption'] . '</image:caption>
                   ';
            }
            $c .= '</image:image>';
        }
        return $c;
    }

    /**
     * SiteMap::getPages($pages)
     * 
     * @return
     */
    public static function getPages($pages) {
        # for ($i = 0; $i < count($this->pages['url']); $i++) {
        foreach ($pages as $urlobj) {
            $str .= '
			<url>
				<loc>' . $urlobj['url'] . '</loc>
                ' . ((is_array($urlobj['images']) && count($urlobj['images']) > 0) ? self::add_images($urlobj['images']) : "") . '
				<lastmod>' . date('Y-m-d') . 'T' . date('H:i:s') . '+00:00</lastmod>
				<changefreq>' . $urlobj['frecvent'] . '</changefreq>
				<priority>' . $urlobj['priority'] . '</priority>
			</url>
			';
        }
        return $str;
    }
    /**
     * SiteMap::convert_pages()
     * 
     * @param mixed $url_arr     * 
     * @return
     */
    private static function convert_pages($url_arr) {
        foreach ($url_arr as $key => $row) {
            if (!isset($url['frecvent'])) {
                $url_arr[$key]['frecvent'] = 'daily';
            }
            if (!isset($url['priority'])) {
                $url_arr[$key]['priority'] = '1.0';
            }
            foreach ($row as $i => $value) {
                if (!is_array($value))
                    $url_arr[$key][$i] = self::escape_xml($value);
            }
        }
        return $url_arr;
    }

    /**
     * SiteMap::write2file()
     * 
     * @param mixed $fname
     * @param mixed $string
     * @return
     */
    private static function write2file($fname, $string) {
        @unlink($fname);
        if (file_exists($fname . '.gz'))
            unlink($fname . '.gz');
        @file_put_contents($fname, $string);
        exec("gzip " . $fname);
        @file_put_contents($fname, $string);
    }
}

?>