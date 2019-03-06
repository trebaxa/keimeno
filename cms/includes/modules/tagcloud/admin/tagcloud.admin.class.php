<?php

/**
 * @package    tagcloud
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class tagcloud_admin_class extends modules_class {
    protected $TAGCLOUD = array();

    /**
     * tagcloud_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

    }

    /**
     * tagcloud_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('TAGCLOUD', $this->TAGCLOUD);
    }

    /**
     * tagcloud_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('tagcloud');
        $this->TAGCLOUD['conf'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * tagcloud_admin_class::set_perma_link()
     * 
     * @return
     */
    function set_perma_link() {
        $query = array('cmd' => 'tagsearch');
        $this->connect_to_pageindex(keimeno_class::add_trailing_slash($this->gblconfig->tagcloud_perma_link, true), $query, 0, 'tagcloud', 1, 1);
    }

    /**
     * tagcloud_admin_class::cmd_set_perma_link()
     * 
     * @return
     */
    function cmd_set_perma_link() {
        $this->set_perma_link();
        $this->ej();
    }
}

?>