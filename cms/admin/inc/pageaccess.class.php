<?php


# Scripting by Trebaxa Company(R) 2011    									*

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



class pageaccess_class extends keimeno_class {

    protected $page_noaccess = array();

    function __construct() {
        parent::__construct();
    }

    public function load_page_access($groupid = 0) {
        $groupid = ((int)$groupid == 0) ? $_SESSION['admin_obj']['GROUPID'] : (int)$groupid;
        $this->page_noaccess = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_ADMIN_PAGEACCESS . "  WHERE p_groupid=" . (int)$groupid);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->page_noaccess[] = $row['p_id'];
        }
        return (array )$this->page_noaccess;
    }

    public function page_access_valid($pageid, $groupid) {
        if (count($this->page_noaccess) == 0)
            $this->load_page_access($groupid);
        return !in_array($pageid, $this->page_noaccess);
    }
}
