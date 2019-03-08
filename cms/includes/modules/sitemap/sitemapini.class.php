<?php

/**
 * @package    sitemap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class site_mapini_class extends keimeno_class {

    var $urls = array();
    var $db = NULL;
    var $MAP = NULL;

    /**
     * site_mapini_class::__construct()
     * 
     * @param mixed $smTable
     * @param mixed $langTable
     * @return
     */
    function __construct($smTable, $langTable) {
        parent::__construct();
        $this->MAP = new SiteMap($smTable, $langTable, TBL_CMS_SMDEFS, $this->db, CMS_ROOT);
        $this->MAP->set_sitemap_name('sitemap');
        $this->MAP->host = self::get_http_protocol() . '://www.' . FM_DOMAIN . PATH_CMS;
    }

    /**
     * site_mapini_class::buildUrlTable()
     * 
     * @param mixed $langid
     * @return
     */
    function buildUrlTable($langid = -1) {
        $this->alllang = $langid == -1;
        $urls = array();
        $params['alllang'] = $this->alllang;
        $params['langid'] = (int)$langid;
        $params = exec_evt('xmlsitemap', $params, $this);
        $urls = (array )$params['urls'];        
        SiteMap::create($urls);
    }


}