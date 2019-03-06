<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class dashboard_class extends keimeno_class {

    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    function cmd_load_se_chart() {
        $colors = array(
            "#BDC3C7",
            "#9B59B6",
            "#E74C3C",
            "#26B99A",
            "#3498DB");
        $k = 0;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_SPIDER . " ORDER BY lasthit DESC, anzahl DESC LIMIT " . count($colors));
        while ($row = $this->db->fetch_array_names($result)) {

            $labels[] = $row['searchengine'];
            $data[] = $row['anzahl'];
            $backgroundColor[] = $colors[$k];
            $datas[] = array(
                'label' => $row['searchengine'],
                'data' => $row['anzahl'],
                'color' => $colors[$k]);
            $k++;
        }
        echo json_encode($datas);
        $this->hard_exit();
        /*
        echo json_encode(array('labels' => $labels, 'datasets' => array(
        'data' => $data,
        'backgroundColor' => $backgroundColor,
        'hoverBackgroundColor' => $backgroundColor)));
        $this->hard_exit();
        */
    }

    function cmd_load_visitor_chart() {
        $set_days = ((int)$_GET['days'] == 0) ? 30 : ($_GET['days'] - 1);
        $oneMonthAgo = strtotime('-' . ($set_days - 1) . ' day', time());
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_VISITORS . " 
        WHERE cs_date>='" . date('Y-m-d', $oneMonthAgo) . "' 
        ORDER BY cs_date ASC LIMIT " . $set_days);
        while ($row = $this->db->fetch_array_names($result)) {
            $days[$row['cs_date']] = array($row['cs_date'], $row['cs_hits']);
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
            'grid' => array(
                'hoverable' => 'true',
                'clickable' => 'true',
                'aboveData' => 'true',
                'color' => '#3f3f3f',
                'autoHighlight' => 'true',
                #'labelMargin' => '10',
                'axisMargin' => '0',
                'borderWidth' => '0',
                'borderColor' => 'null',
                'minBorderMargin' => '5',
                'mouseActiveRadius' => '100'),
            'lines' => array(
                'show' => 'true',
                'fill' => 'true',
                'lineWidth' => '2',
                # 'steps' => 'false'
                ),
            'points' => array(
                'show' => 'true',
                'radius' => '4.5',
                'symbol' => 'circle',
                #'lineWidth' => '3'
                ));
        if (count($data) > 50) {
            unset($options['xaxis']);
        }
        $series_list[] = array(
            'label' => '',
            'data' => $data,
            'lines' => array('fillColor' => 'rgba(150, 202, 89, 0.12)'),
            'points' => array('fillColor' => '#fff'),
            'color' => '#96CA59');
        ECHO json_encode(array('serielist' => $series_list, 'foptions' => $options));
        $this->hard_exit();
    }


    function initdash() {

        /*   if ($_SESSION['USED_SPACE'] == 0) {
        $_SESSION['USED_SPACE'] = exec("du -s " . SHOP_ROOT);
        $_SESSION['USED_SPACE'] = explode("/", $_SESSION['USED_SPACE']);
        }
        */
        $dbsize = $rowcount = 0;
        $result = $this->db->query("SHOW TABLE STATUS FROM " . DB_DATABASE . " LIKE '" . TBL_CMS_PREFIX . "%'");
        while ($row = $this->db->fetch_array_names($result)) {
            $dbsize += $row['Data_length'] + $row['Index_length'];
            $rowcount += $row['Rows'];
        }
        $pcount = get_data_count(TBL_CMS_TEMPLATES, "id", "admin=0 AND gbl_template=0 AND c_type='T'");
        $kcount = get_data_count(TBL_CMS_CUST, "kid", "1");

        # Suchmaschinen
        $spiders = array();
        $spiderq = $this->db->query("SELECT * FROM " . TBL_CMS_SPIDER . " ORDER BY lasthit DESC, anzahl DESC LIMIT 10");
        while ($row = $this->db->fetch_array_names($spiderq)) {
            $row['lasthit'] = date("d.m.Y H:i", $row['lasthit']);
            $row['todaytrue'] = date("dmY", (int)$row['lasthit']) == date("dmY", time());
            $spiders[] = $row;
        }

        #  $result = $this->db->query("SELECT * FROM " . TBL_CMS_GBLCONFIG);
        #  while ($row = $this->db->fetch_array_names($result)) {
        #      $gblconfigarr[] = $row;
        #  }

        # active module
        $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
        foreach ($xml_modules->modules->children() as $module) {
            if ($module->settings->active == 'true' && ($module->settings->iscore == 'false' || $module->settings->iscore == '')) {
                $active_modules[] = array('mod_name' => (string )$module->settings->module_name, 'version' => (string )$module->settings->version);
            }
        }

        # visitor count
        $VSUM = $this->db->query_first("SELECT SUM(cs_hits) AS VSUM FROM " . TBL_CMS_VISITORS);
        $FIRST = $this->db->query_first("SELECT cs_date FROM " . TBL_CMS_VISITORS . " ORDER BY cs_date ASC LIMIT 1");
        $LAST = $this->db->query_first("SELECT cs_date FROM " . TBL_CMS_VISITORS . " ORDER BY cs_date DESC LIMIT 1");
        $seconds_diff = strtotime($LAST['cs_date']) - strtotime($FIRST['cs_date']);
        $days = floor($seconds_diff / 3600 / 24);

        $infoarr = array(
            'template_count' => get_data_count(TBL_CMS_TEMPLATES, "id", "admin=1 AND gbl_template=1"),
            'page_count_pers' => (($pcount > 0) ? $pcount : '-'),
            'inlay_count' => get_data_count(TBL_CMS_TEMPLATES, "id", "admin=0 AND c_type='B'"),
            'cust_count' => (($kcount > 0) ? $kcount : '-'),
            'cust_count_tarif' => (($this->gbl_config['set_memcount'] > 0) ? $this->gbl_config['set_memcount'] : '-'),
            # 'webspace' => human_file_size($_SESSION['USED_SPACE'][0] * 1024),
            'dbspace' => human_file_size($dbsize),
            'topl_count' => get_data_count(TBL_CMS_TOPLEVEL, "id", "1"),
            'nowonline' => get_data_count(TBL_CMS_NOWON, 'zeit', "1"),
            'path_cms' => PATH_CMS,
            'pcount' => $pcount,
            'visitorcount' => $VSUM['VSUM'],
            'visitor_since' => my_date('d.m.Y', $FIRST['cs_date']),
            'visitorcount_perday' => ($days > 0) ? round($VSUM['VSUM'] / $days) : 0,
            'kcount' => $kcount,
            'spiders' => $spiders,
            'active_modules' => $active_modules);
        # 'gblconfigarr' => $gblconfigarr);
        $this->smarty->assign('cmsinfo', $infoarr);
    }

}

?>