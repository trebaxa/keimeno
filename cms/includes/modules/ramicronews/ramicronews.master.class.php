<?PHP

/**
 * @package    ramicronews
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');
# DEFINE('TBL_TABLE_NAME', TBL_CMS_PREFIX . 'mein_tabelle');
class ramicronews_master_class extends modules_class {

    var $xml_dir = "";

    /**
     * ramicronews_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->xml_dir = CMS_ROOT . 'file_data/ramicronews/';
        if (!is_dir($this->xml_dir))
            mkdir($this->xml_dir, 0775);
    }

    /**
     * ramicronews_master_class::parse_news_to_array()
     * 
     * @param mixed $news
     * @return
     */
    function parse_news_to_array($news) {
        foreach ((array )$news->categorylist->category as $cat) {
            if (!is_array($cat))
                $cats[] = (string )$cat;
        }
        return array(
            'id' => (string )$news->attributes()->id,
            'publishingdate' => date('d.m.Y', strtotime($news->publishingdate)),
            'title' => (string )$news->title,
            'subtitle' => (string )$news->subtitle,
            'introduction' => (string )$news->introduction,
            'content' => (string )$news->content,
            'category' => implode('/', $cats),
            'reference' => (string )$news->references->reference->court . ' ' . (string )$news->references->reference->filenumber . ' ' . date('d.m.Y', strtotime($news->
                references->reference->referencedate)),

            );
    }

    /**
     * ramicronews_master_class::load_news_by_id()
     * 
     * @param mixed $id
     * @return
     */
    function load_news_by_id($id) {
        $fname = $this->xml_dir . $this->gbl_config['ramicron_filename'];
        if (file_exists($fname) && is_file($fname)) {
            $xml = simplexml_load_file($fname);
            foreach ($xml->ranewsflashObjectlist->news as $news) {
                if ((string )$news->attributes()->id == $id) {
                    return $this->parse_news_to_array($news);
                }
            }
        }
    }

    /**
     * ramicronews_master_class::load_items()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_items($PLUGIN_OPT = array()) {
        $k = 0;
        $fname = $this->xml_dir . $this->gbl_config['ramicron_filename'];
        if (file_exists($fname) && is_file($fname)) {
            $xml = simplexml_load_file($fname);
            foreach ($xml->ranewsflashObjectlist->news as $news) {
                if (!isset($PLUGIN_OPT['limit']) || $PLUGIN_OPT['limit'] == 0 || $k <= $PLUGIN_OPT['limit']) {
                    $items[] = $this->parse_news_to_array($news);
                    $k++;
                }
            }
            return (array )$items;
        }
        else
            return array();
    }
}

?>