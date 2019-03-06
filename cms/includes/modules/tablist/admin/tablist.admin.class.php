<?php

/**
 * @package    tablist
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class tablistadmin_class extends keimeno_class {

    private $TABLIST = array();

    /**
     * tablistadmin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * tablistadmin_class::cmd_approverow()
     * 
     * @return
     */
    function cmd_approverow() {
        $this->db->query("UPDATE " . TBL_CMS_TABLIST . " SET approval=" . $_GET['value'] . " WHERE id=" . $_GET['ident']);
        $this->hard_exit();
    }

    /**
     * tablistadmin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('TABLIST', $this->TABLIST);
    }
    /**
     * tablistadmin_class::cmd_save_tab_table()
     * 
     * @return
     */
    function cmd_save_tab_table() {
        foreach ((array )$_POST['FORM'] as $id => $row) {
            update_table(TBL_CMS_TABLIST, 'id', $id, $row);
        }
        $this->ej();
    }

    /**
     * tablistadmin_class::cmd_save_tab()
     * 
     * @return
     */
    function cmd_save_tab() {
        $FORM = $_POST['FORM'];
        $FORM_CON = $_POST['FORM_CON'];
        update_table(TBL_CMS_TABLIST, 'id', $_POST['id'], $FORM);
        if ($_POST['conid'] > 0)
            update_table(TBL_CMS_TABLIST_CONTENT, 'id', $_POST['conid'], $FORM_CON);
        else {
            $FORM_CON['tab_id'] = $_POST['id'];
            insert_table(TBL_CMS_TABLIST_CONTENT, $FORM_CON);
        }
        $this->hard_exit();
    }

    /**
     * tablistadmin_class::cmd_list()
     * 
     * @return
     */
    function cmd_list() {
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " 
            WHERE lang_id=" . $_REQUEST['lang_id'] . " AND tab_id='" . $_REQUEST['id'] . "' LIMIT 1");
        kf::output($this->buildTable(unserialize($TAB_OBJC['content'])));
    }

    /**
     * tablistadmin_class::addColumn()
     * 
     * @param mixed $TABLE
     * @return
     */
    function addColumn($TABLE) {
        if (is_array($TABLE)) {
            foreach ($TABLE as $key => $row) {
                $row[] = '';
                $TABLE[$key] = $row;
            }
        }
        return $TABLE;
    }


    /**
     * tablistadmin_class::cmd_addcol()
     * 
     * @return
     */
    function cmd_addcol() {
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE lang_id=" . $_GET['lang_id'] . " AND tab_id='" . $_GET['id'] .
            "' LIMIT 1");
        $TABLE = $this->addColumn(unserialize($TAB_OBJC['content']));
        $TAB_OBJC['content'] = serialize($TABLE);
        if ($TAB_OBJC['id'] > 0)
            update_table(TBL_CMS_TABLIST_CONTENT, 'id', $TAB_OBJC['id'], $TAB_OBJC);
        else {
            $TAB_OBJC['lang_id'] = $_GET['lang_id'];
            $TAB_OBJC['tab_id'] = $_GET['id'];
            insert_table(TBL_CMS_TABLIST_CONTENT, $TAB_OBJC);
        }
        kf::output($this->buildTable($TABLE));
    }

    /**
     * tablistadmin_class::delRow()
     * 
     * @param mixed $TABLE
     * @param mixed $index
     * @return
     */
    function delRow($TABLE, $index) {
        $NEW_TAB = array();
        if (is_array($TABLE)) {
            foreach ($TABLE as $key => $row) {
                if ($key != $index)
                    $NEW_TAB[$key] = $row;
            }
        }
        return $NEW_TAB;
    }

    /**
     * tablistadmin_class::cmd_delrow()
     * 
     * @return
     */
    function cmd_delrow() {
        #list($tmp, $id) = explode('-', $_GET['id']);
        $id = $_GET['ident'];
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE lang_id=" . $_GET['lang_id'] . " AND tab_id='" . $id . "' LIMIT 1");
        $TABLE = $this->delRow(unserialize($TAB_OBJC['content']), $_GET['index']);
        $TAB_OBJC['content'] = serialize($TABLE);
        update_table(TBL_CMS_TABLIST_CONTENT, 'id', $TAB_OBJC['id'], $TAB_OBJC);
        $this->ej();
    }

    /**
     * tablistadmin_class::buildTable()
     * 
     * @param mixed $TABLE
     * @return
     */
    function buildTable($TABLE) {
        $last_row = array();
        if (is_array($TABLE)) {
            foreach ($TABLE as $row_index => $row) {
                $tr = "";
                if (is_array($row)) {
                    $numcols = 0;
                    foreach ($row as $col_index => $td) {
                        if ($_SESSION['tabextview'] == 1) {
                            $tr .= '<td><textarea class="form-control" rows=3 cols=30 name="ROW[' . $row_index . '][' . $col_index . ']">' . htmlspecialchars(stripslashes(base64_decode($td))) .
                                '</textarea></td>';
                        }
                        else {
                            $tr .= '<td><input ' . kf::gen_inputtext_field(stripslashes(base64_decode($td))) . ' name="ROW[' . $row_index . '][' . $col_index . ']"></td>';
                        }
                        $numcols++;
                    }
                    $k++;
                    #kf::gen_del_icon_reload($_REQUEST['id'], 'delrow', '{LBL_CONFIRM}', '&lang_id=' . $_REQUEST['lang_id'] . '&index=' . $row_index)
                    $tab .= '<tr>
					<td ><span class="greenimportant">' . $row_index . '</span>
						' . kf::gen_del_icon($_REQUEST['id'], false, 'delrow', '', '&lang_id=' . $_REQUEST['lang_id'] . '&index=' . $row_index) . '
						</td>
						<td>
						 
                         <a  title="nach oben bewegen" href="javascript:void(0);" onClick="mup(' . $row_index .
                        ');"><img src="./images/arrow_up.png" class="icon"></a>
                         <a  title="nach unten bewegen" href="javascript:void(0);" onclick="mdown(' . $row_index .
                        ');"><img src="./images/arrow_down.png" class="icon"></a>
                        
						</td>
						<td>	<a href="javascript:void(0);" onClick="simple_load(\'tablist\',\'<%$PHPSELF%>?epage=<%$epage%>&cmd=insertrow&position=' . ($row_index + 0.5) . '&id=' .
                        $_REQUEST['id'] . '&lang_id=' . $_REQUEST['lang_id'] . '\');">				
						<img src="./images/add.png"  alt="{LBL_ADD}">
						</a>
						</td>
						' . $tr . '</tr>';
                }
                $last_row = $row;
            }
            $header .= '<td></td><td></td>';
            if (is_array($last_row)) {
                foreach ($last_row as $ident => $td) {
                    $header .= '<td align="right">' . kf::gen_del_icon_reload($_REQUEST['id'], 'delcol', '{LBL_CONFIRM}', '&lang_id=' . $_REQUEST['lang_id'] . '&index=' . $ident) .
                        '</td>';
                    $hnums .= '<td ><b>' . ($ident + 1) . '</b></td>';
                }
            }
            return '<table class="table table-striped table-hover"><tr><td></td><td></td><td></td>' . $hnums . '</tr><thead><tr><th></th>' . $header . '</tr></thead>' . $tab .
                '</table>
            <script>set_ajaxdelete_icons("{LBL_CONFIRM}", "<%$epage%>");</script>';
        }
        return '';
    }

    /**
     * tablistadmin_class::cmd_insertrow()
     * 
     * @return
     */
    function cmd_insertrow() {
        $last_row = array();
        $TAB_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST . " NL WHERE NL.id=" . $_REQUEST['id']);
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE lang_id=" . $_REQUEST['lang_id'] . " AND tab_id='" . $_REQUEST['id'] .
            "' LIMIT 1");
        $TABLE = unserialize($TAB_OBJC['content']);
        $col_count = 0;
        if (is_array($TABLE)) {
            //ermittle höchste anzahl spalten
            foreach ($TABLE as $key => $row) {
                $col_count = ((count($TABLE[$key]) > $col_count) ? count($TABLE[$key]) : $col_count);
            }
            $col_count = (($col_count == 0) ? 1 : $col_count);
            // erzwinge, dass alle zeile die maximale spaltenanzahl haben
            foreach ($TABLE as $key => $row) {
                if (count($row) < $col_count) {
                    for ($i = 0; $i < ($col_count - count($row)); $i++) {
                        $row[] = '';
                    }
                    $TABLE[$key] = $row;
                }
                $last_row = $row;
            }
        }
        //erzeuge neue Zeile
        foreach ($last_row as $key => $row)
            $blank_row[] = '';
        if ($_REQUEST['position'] > 0) {
            $TABLE[$_REQUEST['position']] = $blank_row;
        }
        else {
            $TABLE[] = $blank_row;
        }

        // Table neu sortieren und nummerieren lassen
        ksort($TABLE);
        $new_rows = array();
        $k = 0;
        foreach ($TABLE as $tr_index => $td) {
            $new_rows[($k * 10)] = $TABLE[$tr_index];
            $k++;
        }
        $TABLE = $new_rows;

        // speichern
        $TAB_OBJC['content'] = serialize($TABLE);

        if ($TAB_OBJC['id'] > 0)
            update_table(TBL_CMS_TABLIST_CONTENT, 'id', $TAB_OBJC['id'], $TAB_OBJC);
        else {
            $TAB_OBJC['lang_id'] = $_REQUEST['lang_id'];
            $TAB_OBJC['tab_id'] = $_REQUEST['id'];
            insert_table(TBL_CMS_TABLIST_CONTENT, $TAB_OBJC);
        }
        kf::output($this->buildTable($TABLE));
    }

    /**
     * tablistadmin_class::sortROWS_()
     * 
     * @param mixed $rows
     * @return
     */
    function sortROWS_($rows) {
        // Zeile und Richtung ermitteln
        $move_index = -1;
        foreach ($rows as $tr_index => $td) {
            if ($_POST['sort_btn']['MOVEUP_' . $tr_index] != '') {
                $move_index = $tr_index;
                $direct_up = true;
                $tomove_line = $rows[$tr_index];
                break;
            }
            if ($_POST['sort_btn']['MOVEDOWN_' . $tr_index] != '') {
                $move_index = $tr_index;
                $direct_up = false;
                $tomove_line = $rows[$tr_index];
                break;
            }
        }

        // BEWEGEN
        if ($move_index >= 0) {
            foreach ($rows as $tr_index => $td) {
                if ($tr_index == $move_index) {
                    // nach unten
                    if ($direct_up == false && $tr_index + 1 < count($rows)) {
                        $rows[$tr_index] = $rows[$tr_index + 1];
                        $rows[$tr_index + 1] = $tomove_line;
                    }
                    if ($direct_up == true && $tr_index - 1 >= 0) {
                        // nach oben
                        $rows[$tr_index] = $rows[$tr_index - 1];
                        $rows[$tr_index - 1] = $tomove_line;
                    }
                }
            }
        }
        $new_rows = array();
        $k = 0;
        foreach ($rows as $tr_index => $td) {
            $new_rows[($k * 10)] = $rows[$tr_index];
            $k++;
        }
        return $new_rows;
    }

    /**
     * tablistadmin_class::sortROWS()
     * 
     * @param mixed $rows
     * @param mixed $move_index
     * @param mixed $direct_up
     * @return
     */
    function sortROWS($rows, $move_index, $direct_up) {
        $tomove_line = $rows[$move_index];
        // BEWEGEN
        if ($move_index >= 0) {
            foreach ($rows as $tr_index => $td) {
                if ($tr_index == $move_index) {
                    // nach unten
                    if ($direct_up == false && $tr_index + 1 < count($rows)) {
                        $rows[$tr_index] = $rows[$tr_index + 1];
                        $rows[$tr_index + 1] = $tomove_line;
                    }
                    if ($direct_up == true && $tr_index - 1 >= 0) {
                        // nach oben
                        $rows[$tr_index] = $rows[$tr_index - 1];
                        $rows[$tr_index - 1] = $tomove_line;
                    }
                }
            }
        }
        $new_rows = array();
        $k = 0;
        foreach ($rows as $tr_index => $td) {
            if (count($rows[$tr_index]) > 0) {
                $new_rows[($k * 10)] = $rows[$tr_index];
                $k++;
            }
        }
        return $new_rows;
    }

    /**
     * tablistadmin_class::cmd_sortit()
     * 
     * @return
     */
    function cmd_sortit() {
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " 
            WHERE lang_id=" . $_REQUEST['lang_id'] . " AND tab_id='" . $_REQUEST['id'] . "' LIMIT 1");
        $ROWS = unserialize($TAB_OBJC['content']);
        $ROWS = $this->sortROWS($ROWS, $_REQUEST['index'], $_REQUEST['up'] == 1);
        foreach ($ROWS as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $index => $td) {
                    $value[$index] = $td;
                }
            }
            $TABLE[] = $value;
        }

        $TAB_OBJ['content'] = serialize($TABLE);
        update_table(TBL_CMS_TABLIST_CONTENT, 'id', $TAB_OBJC['id'], $TAB_OBJ);
        $this->cmd_list();
    }

    /**
     * tablistadmin_class::cmd_a_savetab()
     * 
     * @return
     */
    function cmd_a_savetab() {
        $ROW = $_POST['ROW'];
        $FORM = $_POST['FORM'];
        $FORM_CON = $_POST['FORM_CON'];
        if (is_array($ROW)) {
            foreach ($ROW as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $index => $td) {
                        $value[$index] = base64_encode($td);
                    }
                }
                $TABLE[] = $value;
            }
        }
        $TAB_OBJC = array('content' => serialize($TABLE));
        $cont = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " 
            WHERE lang_id=" . $_REQUEST['lang_id'] . " AND tab_id='" . $_REQUEST['id'] . "' LIMIT 1");
        update_table(TBL_CMS_TABLIST_CONTENT, 'id', $cont['id'], $TAB_OBJC);
        $this->cmd_list();
    }

    /**
     * tablistadmin_class::cmd_del_table()
     * 
     * @return
     */
    function cmd_del_table() {
        $this->db->query("DELETE FROM " . TBL_CMS_TABLIST . " WHERE id=" . intval($_GET['ident']) . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE tab_id=" . intval($_GET['ident']));
        $this->ej();
    }

    /**
     * tablistadmin_class::cmd_calgroups()
     * 
     * @return
     */
    function cmd_calgroups() {
        $result = $this->db->query("SELECT *						FROM " . TBL_CMS_TABLIST_GROUP . "			ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            #  $row['icons'][] = kf::gen_edit_icon($row['id'], '&section=edit_group', 'edit_group');
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_group');
            $this->TABLIST['groups'][] = $row;
        }
    }

    /**
     * tablistadmin_class::cmd_save_group_table()
     * 
     * @return
     */
    function cmd_save_group_table() {
        foreach ((array )$_POST['FORM'] as $id => $row) {
            update_table(TBL_CMS_TABLIST_GROUP, 'id', $id, $row);
        }
        $this->ej();
    }

    /**
     * tablistadmin_class::cmd_add_table()
     * 
     * @return
     */
    function cmd_add_table() {
        $id = insert_table(TBL_CMS_TABLIST_GROUP, $_POST['FORM']);
        $this->TCR->tb();
    }

    /**
     * tablistadmin_class::cmd_del_group()
     * 
     * @return
     */
    function cmd_del_group() {
        if (get_data_count(TBL_CMS_TABLIST, 'id', "group_id=" . $_GET['ident']) == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_TABLIST_GROUP . " WHERE id>1 AND id=" . $_GET['ident']);
            $this->msg('{LBL_DELETED}');
            $this->ej();
        }
        else {
            $this->msge('{LBLA_NOT_DELETED} {LBL_HASSUBCONTENT}');
            $this->ej();
        }

    }

    /**
     * tablistadmin_class::delColumn()
     * 
     * @param mixed $TABLE
     * @param mixed $index
     * @return
     */
    function delColumn($TABLE, $index) {
        if (is_array($TABLE)) {
            foreach ($TABLE as $key => $row) {
                if (is_array($row)) {
                    $NEW_ROW = array();
                    foreach ($row as $ident => $td) {
                        if ($ident != $index)
                            $NEW_ROW[$ident] = $td;
                    }
                    $TABLE[$key] = $NEW_ROW;
                }
            }
        }
        return $TABLE;
    }


    /**
     * tablistadmin_class::cmd_delcol()
     * 
     * @return
     */
    function cmd_delcol() {
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE lang_id=" . $_GET['lang_id'] . " AND tab_id='" . $_GET['id'] .
            "' LIMIT 1");
        $TABLE = $this->delColumn(unserialize($TAB_OBJC['content']), $_GET['index']);
        $TAB_OBJC['content'] = serialize($TABLE);
        update_table(TBL_CMS_TABLIST_CONTENT, 'id', $TAB_OBJC['id'], $TAB_OBJC);
        #HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&uselang=' . $_GET['lang_id'] . '&aktion=edit&id=' . $_GET['id'] . '');
        $this->msg('{LBLA_SAVED}');
        $this->TCR->tb();
    }

    /**
     * tablistadmin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $lang = ($_GET['uselang'] > 0) ? $_GET['uselang'] : 1;
        $TAB_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST . " NL WHERE NL.id=" . $_GET['id']);
        $TAB_OBJC = $this->db->query_first("SELECT * FROM " . TBL_CMS_TABLIST_CONTENT . " WHERE lang_id=" . $lang . " AND tab_id='" . $_GET['id'] . "' LIMIT 1");
        $TAB_OBJC['id'] = intval($TAB_OBJC['id']);
        $TAB_OBJC['lang_id'] = intval($lang);
        $this->smarty->assign('TAB_OBJ', $TAB_OBJ);
        $this->smarty->assign('TAB_OBJC', $TAB_OBJC);
    }

    /**
     * tablistadmin_class::cmd_newtab()
     * 
     * @return
     */
    function cmd_newtab() {
        $FORM = array();
        $FORM['tab_name'] = 'A NEW TABLE';
        $FORM['group_id'] = $_GET['group_id'];
        $FORM['mid'] = $_SESSION['mitarbeiter'];
        insert_table(TBL_CMS_TABLIST, $FORM);
        $this->msg('{LBLA_SAVED}');
        $this->TCR->tb();
    }

}

?>