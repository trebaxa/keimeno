<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('IN_SIDE') or die('Access denied.');
class rss_class extends keimeno_class {


    var $entries = array();
    var $metadata = array();
    var $langid = 1;
    var $template = "";

    /**
     * rss_class::rss_utf8_encode()
     * 
     * @param mixed $string
     * @return
     */
    function rss_utf8_encode($string) {
        if (strtolower(LANG_CHARSET) != 'utf-8') {
            if (function_exists('iconv')) {
                $new = iconv(LANG_CHARSET, 'UTF-8', $string);
                if ($new !== false) {
                    return $new;
                }
                else {
                    return utf8_encode($string);
                }
            }
            else
                if (function_exists('mb_convert_encoding')) {
                    return mb_convert_encoding($string, 'UTF-8', LANG_CHARSET);
                }
                else {
                    return utf8_encode($string);
                }
        }
        else {
            return $string;
        }
    }

    /**
     * rss_class::__construct()
     * 
     * @param mixed $data
     * @param mixed $metadata
     * @param mixed $template
     * @param mixed $langid
     * @return
     */
    function __construct($data, $metadata, $template, $langid) {
        parent::__construct();
        $this->langid = $langid;
        $this->template = $template;

        foreach ($data as $key => $row) {
            foreach ($row as $rkey => $value) {
                $row[$rkey] = $this->rss_utf8_encode($row[$rkey]);
            }
            $this->entries[] = $data[$key];
        }
        foreach ($metadata as $key => $value) {
            $this->metadata[$key] = $this->rss_utf8_encode($metadata[$key]);
        }

    }

    /**
     * rss_class::genRSS()
     * 
     * @return
     */
    function genRSS() {
        $this->smarty->assign('metadata', $this->metadata);
        $this->smarty->assign('entries', $this->entries);
        $this->smarty->assign('namespace_display_dat', 'http://purl.org/rss/1.0/modules/content/');
        $RSS_XML = smarty_compile($this->template);
        $RSS_XML = pure_translation($RSS_XML, $this->langid);
        header('Content-Type: text/xml; charset=utf-8');
        header("Content-Disposition: inline; filename=\"rss.xml\"");
        ECHO $RSS_XML;
        exit;
    }


}
