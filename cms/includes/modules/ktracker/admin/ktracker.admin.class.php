<?php

/**
 * @package    ktracker
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');

class ktracker_admin_class extends ktracker_master_class {

    protected $KTRACKER = array();

    /**
     * ktracker_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->KTRACKER['compains'] = $this->load_compains();
        $this->init();
    }


    /**
     * ktracker_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('KTRACKER', $this->KTRACKER);
    }

    /**
     * ktracker_admin_class::cmd_del_camp()
     * 
     * @return
     */
    function cmd_del_camp() {
        $this->db->query("DELETE FROM " . TBL_KTRACKER . " WHERE id=" . $_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_KTRACKER_LOG . " WHERE kl_id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * ktracker_admin_class::init()
     * 
     * @return
     */
    function init() {
        $TREE = new nestedArrClass();
        $TREE->db = $this->db;
        $TREE->label_id = 'id';
        $TREE->label_parent = 'parent';
        $TREE->label_column = 'description';
        $TREE->create_result_and_array("SELECT id, parent, description, approval FROM " . TBL_CMS_TEMPLATES . "  WHERE gbl_template=0 AND c_type='T' 
        ORDER BY parent,morder", 0, 0, -1);
        $this->KTRACKER['websitetree'] = $TREE->outputtree_select();

    }

    /**
     * ktracker_admin_class::cmd_add()
     * 
     * @return
     */
    function cmd_add() {
        insert_table(TBL_KTRACKER, array('k_title' => '#NEU'));
        $this->KTRACKER['compains'] = $this->load_compains();
    }

    /**
     * ktracker_admin_class::cmd_save_table()
     * 
     * @return
     */
    function cmd_save_table() {
        foreach ($_POST['FORM'] as $id => $row) {
            update_table(TBL_KTRACKER, 'id', $id, $row);
        }

        $this->ej();
    }

    /**
     * ktracker_admin_class::cmd_load_ktracker_chart()
     * 
     * @return
     */
    function cmd_load_ktracker_chart() {
        $set_days = ((int)$_GET['days'] == 0) ? 30 : ($_GET['days'] - 1);
        $oneMonthAgo = strtotime('-' . ($set_days - 1) . ' day', time());
        $result = $this->db->query("SELECT * FROM " . TBL_KTRACKER_LOG . " 
        WHERE kl_id='" . $_GET['id'] . "'
        AND kl_date>='" . date('Y-m-d', $oneMonthAgo) . "'  
        ORDER BY kl_date ASC LIMIT " . $set_days);
        while ($row = $this->db->fetch_array_names($result)) {
            $days[$row['kl_date']] = array($row['kl_date'], $row['kl_count']);
        }

        $day_tab = array();
        $td = date('Y-m-d', $oneMonthAgo);
        for ($i = 0; $i < $set_days; $i++) {
            if (!is_array($days[$td])) {
                $day_tab[$td] = array($td, 0);
            }
            else {
                $day_tab[$td] = array($td, $days[$td][1]);
            }
            $td = date('Y-m-d', strtotime('+1 day', strtotime($td)));
        }

        unset($days);

        $x = 0;
        foreach ($day_tab as $date => $row) {
            $x++;
            $data[] = array($x, $row[1]);
            $ticks[] = array($x, date('d', strtotime($row[0])));
        }

        $xaxis = array(
            'ticks' => $ticks,
            'tickDecimals' => 0,
            'tickSize' => 1);
        $yaxis = array('zoomRange' => array(0.1, 10), 'panRange' => array(-10, 10));
        $options = array(
            'xaxis' => $xaxis,
            'grid' => array('hoverable' => 'true', 'clickable' => true),
            'lines' => array('show' => 'true', 'fill' => true),
            'points' => array('show' => 'true'));
        if (count($data) > 50) {
            unset($options['xaxis']);
        }
        $series_list[] = array(
            'label' => '',
            'data' => $data,
            'color' => '#A1C517');
        echo json_encode(array('serielist' => $series_list, 'foptions' => $options));
        $this->hard_exit();
    }


}

?>