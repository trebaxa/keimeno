<?php

/**
 * @package    statistic
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


class statistic_class extends keimeno_class {

    protected $STATOBJ = array();

    /**
     * statistic_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * statistic_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('STATOBJ', $this->STATOBJ);
    }

    /**
     * statistic_class::cmd_load_ref_chart()
     * 
     * @return
     */
    function cmd_load_ref_chart() {
        $result = $this->db->query("SELECT SUM(L.user_count) AS COLCOUNT,L.* FROM " . TBL_CMS_REFLOG . " L 
        WHERE user_count>0 GROUP BY L.user_domain 
        ORDER BY user_count DESC,L.lasthit DESC 
        LIMIT 25");
        while ($row = $this->db->fetch_array_names($result)) {
            $data[] = array(
                'label' => $row['user_domain'],
                'data' => $row['COLCOUNT'],
                # 'color' => '#3D96AE'
                );
        }
        echo json_encode($data);
        $this->hard_exit();
    }

    /**
     * statistic_class::cmd_load_ref()
     * 
     * @return
     */
    function cmd_load_ref() {
        $_GET['sortby'] = ($_GET['sortby'] == "") ? 'user_count' : $_GET['sortby'];
        $_GET['dsortby'] = ($_GET['dsortby'] == "") ? 'user_domain' : $_GET['dsortby'];
        $_GET['direct'] = ($_GET['direct'] == "") ? 'DESC' : $_GET['direct'];
        $result = $this->db->query("SELECT SUM(L.user_count) AS COLCOUNT,L.* FROM " . TBL_CMS_REFLOG . " L WHERE user_count>0 GROUP BY L.user_domain ORDER BY " . $_GET['dsortby'] .
            " " . $_GET['direct'] . ",L.lasthit " . $_GET['direct'] . " LIMIT 25");
        $k = 0;
        while ($row = $this->db->fetch_array_names($result)) {
            $row['lasthit'] = (($row['lasthit'] > 0) ? date("d.m.Y H:i:s", $row['lasthit']) : '-');
            $this->STATOBJ['refbyhost'][] = $row;
        }


        # ref
        $result = $this->db->query("SELECT L.* FROM " . TBL_CMS_REFLOG . " L WHERE user_count>0 ORDER BY " . $_GET['sortby'] . " " . $_GET['direct'] . ",L.lasthit " . $_GET['direct'] .
            " LIMIT 25");
        $_GET['direct'] = ($_GET['direct'] == "DESC") ? 'ASC' : 'DESC';
        while ($row = $this->db->fetch_array_names($result)) {
            $row['lasthit'] = (($row['lasthit'] > 0) ? date("d.m.Y H:i:s", $row['lasthit']) : '-');
            if ($row['user_count'] > 0 || $nullpos == 1) {
                $this->STATOBJ['refs'][] = $row;
            }
        }
        $this->parse_to_smarty();
        kf::echo_template('statistic');
    }

    /**
     * statistic_class::cmd_load_se_chart()
     * 
     * @return
     */
    function cmd_load_se_chart() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_SPIDER . " ORDER BY lasthit DESC, anzahl DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $data[] = array(
                'label' => $row['searchengine'],
                'data' => $row['anzahl'],
                # 'color' => '#3D96AE'
                );
        }

        echo json_encode($data);
        $this->hard_exit();
    }

    /**
     * statistic_class::cmd_load_se()
     * 
     * @return
     */
    function cmd_load_se() {
        $core_stat = new stat_class();
        foreach ($core_stat->SPIDERS as $key => $botname) {
            $botname_plain = self::gen_plain_text($botname);
            $_HITS[$botname_plain] = 0;
        }
        $spiderq = $this->db->query("SELECT * FROM " . TBL_CMS_SPIDER . " ORDER BY lasthit DESC, anzahl DESC");
        if ($this->db->num_rows($spiderq) > 0) {
            $k = 0;
            while ($row = $this->db->fetch_array_names($spiderq)) {
                $k++;
                $row['datetoday'] = (date("dmY", $row['lasthit']) == date("dmY", time())) ? true : false;
                $row['lasthit'] = date("d.m.Y H:i", $row['lasthit']);
                $row['lasthit_today'] = date("H:i", $row['lasthit']);
                $this->STATOBJ['spiders'][] = $row;
                $HITS[$row['searchengine']] = 'Y';
            }
        }

        foreach ($core_stat->SPIDERS as $key => $botname) {
            $botname_plain = self::gen_plain_text($botname);
            if ($HITS[$botname_plain] != 'Y' && $core_stat->no_null == false) {
                $this->STATOBJ['bots'][] = $botname_plain;
            }
        }
        $this->parse_to_smarty();
        kf::echo_template('statistic');
    }

    /**
     * statistic_class::cmd_load_wio()
     * 
     * @return
     */
    function cmd_load_wio() {
        $this->db->query("DELETE FROM " . TBL_CMS_NOWON . " WHERE zeit<'" . (time() - (60 * $this->gbl_config['nowon_time'])) . "'");
        $this->STATOBJ['now'] = date("H:i:s");
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_NOWON . " ORDER BY zeit DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['date'] = date("d.m.Y", $row['zeit']);
            $row['time'] = date("H:i:s", $row['zeit']);
            $row['itsme'] = $row['ip'] == getenv('REMOTE_ADDR');
            $this->STATOBJ['whoisonline'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('statistic');
    }

    /**
     * statistic_class::cmd_load_visitors()
     * 
     * @return
     */
    function cmd_load_visitors() {
        $this->parse_to_smarty();
        kf::echo_template('statistic');
    }

    /**
     * statistic_class::cmd_load_browsers()
     * 
     * @return
     */
    function cmd_load_browsers() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_BROWSERLOG . " WHERE b_count>0");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->STATOBJ['browser'][$row['b_type']][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('statistic');
    }

    /**
     * statistic_class::cmd_load_browser_chart()
     * 
     * @return
     */
    function cmd_load_browser_chart() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_BROWSERLOG . " WHERE b_count>0 AND b_type='" . $_GET['type'] . "'");
        while ($row = $this->db->fetch_array_names($result)) {
            $b_data[] = $row;
            #  $total[$row['b_type']] += $row['b_count'];
        }
        if ($_GET['type'] == 'B') {
            $label = 'b_browser';
        }
        elseif ($_GET['type'] == 'BV') {
            $label = 'b_browserv';
        }
        elseif ($_GET['type'] == 'MS') {
            $label = 'b_mobilesystem';
        }
        elseif ($_GET['type'] == 'S') {
            $label = 'b_system';
        }

        foreach ((array )$b_data as $row) {
            $data[] = array(
                'label' => $row[$label],
                'data' => $row['b_count'],
                # 'color' => '#3D96AE'
                );
        }


        echo json_encode($data);
        $this->hard_exit();
    }

}
