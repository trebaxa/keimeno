<?php


/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

class framework_class extends keimeno_class {

    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->load_framework(1);
        $this->load_frameworks();
    }

    function parse_to_smarty() {
        $this->smarty->assign('FRAMEW', $this->FW);
    }

    function load_frameworks() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FRAMEWORKS . " ORDER BY fw_number");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->FW['frameworks'][] = $row;
        }
        return $this->FW['frameworks'];
    }

    function load_framework($id = 1) {
        $id = (int)$id;
        $this->FW['framework'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FRAMEWORKS . " WHERE fw_number=" . $id);
    }

    function cmd_axloadfw() {
        $this->load_framework($_GET['id']);
        $this->parse_to_smarty();
        kf::echo_template('framework.editor');
    }

    function cmd_save_framework() {
        $FORM = $_POST['FORM'];
        $this->db->query("DELETE FROM " . TBL_CMS_FRAMEWORKS . " WHERE fw_number=" . $FORM['fw_number']);
        insert_table(TBL_CMS_FRAMEWORKS, $FORM);
        $this->hard_exit();
    }
}
