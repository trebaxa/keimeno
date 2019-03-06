<?php

/**
 * @package    indexsearch
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class indexsearch_admin_class extends indexsearch_master_class {

    protected $INDEXSEARCH = array();

    var $site_id = 0;
    var $word_id = 0;
    var $inhalt = "";
    var $all_index = false;
    var $urls = array();
    var $words = array();


    /**
     * indexsearch_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->inhalt = "";
        $this->blocklist = array_unique(explode(',', $this->gbl_config['si_blockwords']));
        $this->page_ident = 'page';
        $this->all_index = false;
        $this->langid = 1;
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * indexsearch_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('INDEXSEARCH', $this->INDEXSEARCH);
    }


    /**
     * indexsearch_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('indexsearch');
        $this->INDEXSEARCH['conf'] = $CONFIG_OBJ->buildTable();
    }


    /**
     * indexsearch_admin_class::clean_sites()
     * 
     * @return
     */
    function clean_sites() {
        $keys = explode(',', $this->gbl_config['si_blockquery']);
        if (is_array($keys) && count($keys) > 0) {
            foreach ($keys as $value)
                $sql .= (($sql != "") ? ' OR ' : '') . " s_url LIKE '%" . $this->db->real_escape_string($value) . "%' ";
            if ($sql != "") {
                $result = $this->db->query("SELECT * FROM " . TBL_CMS_SINSITES . " WHERE " . $sql);
                while ($row = $this->db->fetch_array_names($result)) {
                    $this->del_single_site($row['id']);
                }
            }
        }
    }

    /**
     * indexsearch_admin_class::cmd_words()
     * 
     * @return
     */
    function cmd_words() {
        $this->load_words((int)$_GET['start']);
    }

    /**
     * indexsearch_admin_class::cmd_load_index()
     * 
     * @return
     */
    function cmd_load_index() {
        $this->load_index();
    }

    /**
     * indexsearch_admin_class::delete_words()
     * 
     * @return
     */
    function delete_words() {
        $_POST['wort'] = trim($_POST['wort']);
        if (!empty($_POST['wort'])) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_SINWORDS . " WHERE si_word LIKE '" . $_POST['wort'] . "'");
            while ($row = $this->db->fetch_array_names($result)) {
                $this->del_single_word($row['id']);
            }
        }
    }

    /**
     * indexsearch_admin_class::reset_index()
     * 
     * @return
     */
    function reset_index() {
        $this->db->query("DELETE FROM " . TBL_CMS_SINWORDS);
        $this->reset_score();
        $this->db->query("DELETE FROM " . TBL_CMS_SINSITES);
    }

    /**
     * indexsearch_admin_class::reset_score()
     * 
     * @return
     */
    function reset_score() {
        $this->db->query("DELETE FROM " . TBL_CMS_SINREL);
    }

    /**
     * indexsearch_admin_class::blockword()
     * 
     * @param mixed $word
     * @return
     */
    function blockword($word) {
        $W = $this->db->query_first("SELECT * FROM " . TBL_CMS_SINWORDS . " WHERE si_word='" . trim($word) . "'");
        $this->del_single_word($W['id']);
        $bw = explode(',', trim($this->gbl_config['si_blockwords']));
        $bw[] = $word;
        $bw = array_unique($bw);
        $this->gbl_config['si_blockwords'] = str_replace(array(
            "\t",
            "\r",
            "\n"), "", implode(',', $bw));
        $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . trim($this->gbl_config['si_blockwords']) . "' WHERE config_name='si_blockwords'");
    }

    /**
     * indexsearch_admin_class::mass_block()
     * 
     * @param mixed $bw
     * @return
     */
    function mass_block($bw) {
        if (is_array($bw)) {
            foreach ($bw as $w) {
                $this->blockword($w);
            }
        }
    }


    /**
     * indexsearch_admin_class::del_single_word()
     * 
     * @param mixed $wid
     * @return
     */
    function del_single_word($wid) {
        $wid = (int)$wid;
        $this->db->query("DELETE FROM " . TBL_CMS_SINWORDS . " WHERE id=" . $wid);
        $this->db->query("DELETE FROM " . TBL_CMS_SINREL . " WHERE sr_word_id=" . $wid);
    }

    /**
     * indexsearch_admin_class::del_single_site()
     * 
     * @param mixed $sid
     * @return
     */
    function del_single_site($sid) {
        $sid = (int)$sid;
        $this->db->query("DELETE FROM " . TBL_CMS_SINSITES . " WHERE id=" . $sid);
        $this->db->query("DELETE FROM " . TBL_CMS_SINREL . " WHERE sr_siteid=" . $sid);
    }

    /**
     * indexsearch_admin_class::cmd_delsite()
     * 
     * @return
     */
    function cmd_delsite() {
        $this->del_single_site($_GET['ident']);
        $this->ej();
    }

    /**
     * indexsearch_admin_class::load_index()
     * 
     * @param integer $start
     * @return
     */
    function load_index($start = 0) {
        $result = $this->db->query("SELECT S.* FROM " . TBL_CMS_SINSITES . " S ORDER BY s_url LIMIT " . (int)$start . ",100");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icon_del'] = kf::gen_del_icon($row['id'], true, 'delsite');
            $this->urls[] = $row;
        }
        $res = array('listarr' => $this->urls, 'count' => get_data_count(TBL_CMS_SINSITES, 'id', "1"));
        $this->smarty->assign('site_urls', $res);

        $_paging = $this->genPaging($start, 100, get_data_count(TBL_CMS_SINSITES, 'id', "1"), '&cmd=load_index&epage=searchindex.inc&msid=' . $_GET['msid']);

        $_paging['count_pro_page'] = 1000;
        $this->smarty->assign('paging', $_paging);
        return $res;
    }

    /**
     * indexsearch_admin_class::cmd_delword()
     * 
     * @return
     */
    function cmd_delword() {
        $this->del_single_word($_GET['ident']);
        $this->ej();
    }


    /**
     * indexsearch_admin_class::cmd_blockword()
     * 
     * @return
     */
    function cmd_blockword() {
        $this->blockword($_GET['word']);
        $this->msg("{LBL_DONE}");
        $this->TCR->tb();
    }

    /**
     * indexsearch_admin_class::cmd_massblock()
     * 
     * @return
     */
    function cmd_massblock() {
        $this->mass_block($_POST['bwords']);
        $this->msg("{LBL_DELETED}");
        $this->TCR->tb();
    }


    /**
     * indexsearch_admin_class::load_words()
     * 
     * @param integer $start
     * @return
     */
    function load_words($start = 0) {
        $start = (int)$start;
        $result = $this->db->query("SELECT W.* FROM " . TBL_CMS_SINWORDS . " W WHERE si_soundex=0 ORDER BY si_word LIMIT " . (int)$start . ",1000");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icon_del'] = kf::gen_del_icon($row['id'], false, 'delword');
            $this->words[] = $row;
        }
        $res = array('listarr' => $this->words, 'count' => count($this->words));
        $this->smarty->assign('site_words', $res);

        $_paging = $this->genPaging($start, 1000, get_data_count(TBL_CMS_SINWORDS, 'id', "1"), '&aktion=words&epage=searchindex.inc&msid=' . $_GET['msid']);

        $_paging['count_pro_page'] = 1000;
        $this->smarty->assign('paging', $_paging);
        return $res;
    }

    /**
     * indexsearch_admin_class::gen_paging_link_admin()
     * 
     * @param mixed $start
     * @param string $toadd
     * @return
     */
    function gen_paging_link_admin($start, $toadd = '') {
        return $_SERVER['PHP_SELF'] . '?start=' . $start . $toadd;
    }

    /**
     * indexsearch_admin_class::genPaging()
     * 
     * @param mixed $ovStart
     * @param mixed $max_paging
     * @param mixed $total
     * @param string $toadd
     * @return
     */
    function genPaging($ovStart, $max_paging, $total, $toadd = '') {
        define('NUM_PREPAGES', 6);
        $start = (isset($ovStart)) ? abs((int)$ovStart) : 0;
        $total_pages = ceil($total / $max_paging);
        $akt_page = round($start / $max_paging) + 1;
        if ($total_pages > 0)
            $akt_pages = $akt_page . '/' . $total_pages;
        $start = ($start > $total) ? $total - $max_paging : $start;
        $next_pages_arr = $back_pages_arr = array();
        if ($start > 0)
            $newStartBack = ($start - $max_paging < 0) ? 0 : ($start - $max_paging);
        if ($start > 0) {
            for ($i = NUM_PREPAGES - 1; $i >= 0; $i--) {
                if ($newStartBack - ($i * $max_paging) >= 0) {
                    $back_pages_arr[] = array(
                        'link' => $_SERVER['PHP_SELF'] . '?start=' . ($newStartBack - ($i * $max_paging)),
                        'linkadmin' => $this->gen_paging_link_admin(($newStartBack - ($i * $max_paging)), $toadd),
                        'index' => ($akt_page - $i - 1));
                }
            }
        }
        if ($start + $max_paging < $total) {
            $newStart = $start + $max_paging;
            for ($i = 0; $i < NUM_PREPAGES; $i++) {
                if ($newStart + ($i * $max_paging) < $total) {
                    $next_pages_arr[] = array(
                        'link' => '',
                        'linkadmin' => $this->gen_paging_link_admin(($newStart + ($i * $max_paging)), $toadd),
                        'index' => ($akt_page + $i + 1));
                }
            }
        }
        #	die;
        $_paging['start'] = $start;
        $_paging['total_pages'] = $total_pages;
        $_paging['startback'] = $newStartBack;
        $_paging['newstart'] = $newStart;
        $_paging['base_link_admin'] = $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . $toadd;
        $_paging['back_pages'] = $back_pages_arr;
        $_paging['akt_page'] = $akt_page;
        $_paging['next_pages'] = $next_pages_arr;
        $_paging['backlink'] = $this->gen_paging_link_admin($newStartBack, $toadd);
        $_paging['nextlink'] = $this->gen_paging_link_admin($newStart, $toadd);
        $_paging['count_total'] = $total;
        return $_paging;
    }

    /**
     * indexsearch_admin_class::exec_tasks()
     * 
     * @param mixed $task
     * @return
     */
    function exec_tasks($task) {
        if ($task == 'reset') {
            $this->reset_index();
        }

        if ($task == 'clean_sites') {
            $this->clean_sites();
        }

        if ($task == 'delete_words') {
            $this->delete_words();
        }

        if ($task == 'autocrawl') {
            if ($this->gblconfig->si_siactive == 0) {
                $this->msge('Bitte erst indexierte Suche aktivieren.');
                $this->TCR->tb();
            }
            else {
                require_once (CMS_ROOT . 'includes/autocrawl.class.php');
                $AC = new auto_crawl_class($this, $this->langid);
                $left = $AC->auto_crawl((int)$_GET['start']);
                die;
                unset($AC);
                return $left;
            }
        }
    }

    /**
     * indexsearch_admin_class::cmd_tasks()
     * 
     * @param mixed $task
     * @return
     */
    function cmd_tasks() {
        global $ADMINOBJ;
        $res = $this->exec_tasks($_REQUEST['task']);
        if ($_REQUEST['task'] == 'autocrawl' && $res > 0) {
            $_SESSION['sicounter']++;
            HEADER('Refresh: 0;  URL=run.php?start=' . ($_SESSION['sicounter'] * $res) . '&epage=' . $_REQUEST['epage'] . '&task=autocrawl&cmd=tasks');
            $ADMINOBJ->content .= '
		<script>showPageLoadInfo();</script>
		<div class="panel panel-default">
  <div class="panel-heading">Crawling</div>
  <div class="panel-body">
		';
            if (is_array($_SESSION['siurls'])) {
                foreach ($_SESSION['siurls'] as $url) { #
                    $ADMINOBJ->content .= $url . '<br>';
                }
            }
            $ADMINOBJ->content .= '
	</div>
</div>';
            include (CMS_ROOT . 'admin/inc/footer.inc.php');
            die;
        }
        else {
            unset($_SESSION['sicounter']);
            $this->msg("{LBL_DONE}");
            HEADER('location:run.php?epage=' . $_REQUEST['epage'] . '&cmd=showtasks');
            exit;
        }
    }

}
