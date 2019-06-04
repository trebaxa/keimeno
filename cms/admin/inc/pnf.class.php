<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class pnf_class extends keimeno_class {

    /**
     * pnf_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * pnf_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        $this->smarty->assign('PNF', $this->PNF);
    }

    /**
     * pnf_class::init()
     * 
     * @return void
     */
    function init() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PAGENF . " ORDER BY pnf_time DESC,pnf_calls DESC, pnf_uri ASC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['pnf_time_ger'] = date('d.m.Y H:i', $row['pnf_time']);
            $row['icons'][] = kf::gen_del_icon($row['pnf_hash'], false, 'delete_redirect');
            $this->PNF['ptable'][] = $row;
        }
        $this->PNF['ptable_count'] = count($this->PNF['ptable']);
    }

    /**
     * pnf_class::cmd_delete_redirect()
     * 
     * @return void
     */
    function cmd_delete_redirect() {
        $this->db->query("DELETE FROM " . TBL_CMS_PAGENF . " WHERE pnf_hash='" . $_GET['ident'] . "' LIMIT 1");
        $this->ej();
    }

    /**
     * pnf_class::cmd_pnfsave()
     * 
     * @return void
     */
    function cmd_pnfsave() {
        $FORM = (array )$_POST['FORM'];
        foreach ($FORM as $key => $row) {
            update_table(TBL_CMS_PAGENF, 'pnf_hash', $key, $this->trim_array($row));
        }
        $this->ej();
    }

    /**
     * pnf_class::cmd_clear()
     * 
     * @return void
     */
    function cmd_clear() {
        $this->db->query("DELETE FROM " . TBL_CMS_PAGENF);
        self::msg('table cleared');
        $this->ej('relaod_pnf');
    }

}
