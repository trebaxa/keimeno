<?php




/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



class custgroup_class extends keimeno_class {

    protected $CUSTGROUPS = array();

    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    function parse_to_smarty() {
        $this->smarty->assign('CUSTGROUPS', $this->CUSTGROUPS);
    }

    function cmd_coll() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION . " ORDER BY col_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['count'] = count(explode_string_by_ident($row['col_groups'], ';'));
            # $row['icons'][] = kf::gen_std_icon($row['id'], 'page_white_edit.png', '{LBL_ADDGROUP}', 'id', 'a_addcolgroup', '&collid=' . $row['id'], $_SERVER['PHP_SELF']);
            $row['icons'][] = kf::gen_approve_icon($row['id'], $row['approval'], 'approve_col');
            $row['icons'][] = kf::gen_del_icon($row['id'], false, 'delete_col');
            $this->CUSTGROUPS['collections'][] = $row;
        }
    }

    function cmd_approve_col() {
        $this->db->query("UPDATE " . TBL_CMS_COLLECTION . " SET approval='" . $_GET['value'] . "' WHERE id='" . $_GET['ident'] . "' LIMIT 1");
        $this->hard_exit();
    }

    function cmd_msave_col() {
        foreach ((array )$_POST['FORM'] as $key => $row) {
            update_table(TBL_CMS_COLLECTION, 'id', $key, $row);
        }
        $this->hard_exit();
    }


    function cmd_add_col_list() {
        $list = explode("\n", $_POST['collist']);
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                $value = trim($value);
                if ($value != "")
                    $this->db->query("INSERT INTO " . TBL_CMS_COLLECTION . " SET col_name='" . $value . "'");
            }
        }
        keimeno_class::msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&section=coll&cmd=coll');
        $this->hard_exit();
    }

    function cmd_delete_col() {
        $this->db->query("DELETE FROM " . TBL_CMS_CUSTCOLGROUPS . " WHERE col_id=" . $_GET['ident']);
        $this->db->query("DELETE FROM " . TBL_CMS_COLLECTION . " WHERE id=" . intval($_GET['ident']));
        $this->ej();
    }


    function cmd_add_group_list() {
        $list = explode("\n", $_POST['grouplist']);
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                $value = trim($value);
                if ($value != "") {
                    $FORM['groupname'] = $value;
                    $FORM['cms_approval'] = 1;
                    $id = insert_table(TBL_CMS_CUSTGROUPS, $FORM);
                    $this->setPerm($id);
                    if ($_POST['collid'] > 0) {
                        $COL = $this->db->query_first("SELECT * FROM " . TBL_CMS_COLLECTION . " WHERE id=" . $_POST['collid']);
                        $COL['col_groups'] .= (($COL['col_groups'] != "") ? ';' : '') . $id;
                        $this->db->query("UPDATE " . TBL_CMS_COLLECTION . " SET col_groups='" . $COL['col_groups'] . "'  WHERE id=" . $_POST['collid']);
                    }
                }
            }
        }
        keimeno_class::msg('{LBLA_SAVED}');
        $this->ej('reloadgroups');
    }

    function cmd_reloadgroups() {
        $this->cmd_all();
        $this->parse_to_smarty();
        kf::echo_template('custgroup.list');
    }

    function cmd_approve_group() {
        $this->db->query("UPDATE " . TBL_CMS_CUSTGROUPS . " SET cms_approval='" . $_GET['value'] . "' WHERE id='" . $_GET['ident'] . "' LIMIT 1");
        $this->hard_exit();
    }


    function cmd_delgroup() {
        if (get_data_count(TBL_CMS_CUSTTOGROUP, 'kid', "gid='" . $_GET['ident'] . "'") > 0) {
            keimeno_class::msge('Gruppe kann nicht gel&ouml;scht werden, da die Gruppe Mitglieder enth&auml;lt');
            $this->ej();
        }
        else {
            $this->db->query("DELETE FROM " . TBL_CMS_CUSTGROUPS . " WHERE id='" . $_GET['ident'] . "' AND id!=1000 AND id!=1100 LIMIT 1");
            $this->ej();
        }
    }


    function cmd_all() {
        $result = $this->db->query("SELECT *,G.id AS GID, COUNT(kid) AS KCOUNT 
  	FROM " . TBL_CMS_CUSTGROUPS . " G 
  	LEFT JOIN " . TBL_CMS_CUSTTOGROUP . " UG ON (UG.gid=G.id) 
  	WHERE G.id<>1000 
  	GROUP BY G.id ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['GID'], '&section=edit');
            $row['icons'][] = (($row['GID'] != 1000 && $row['GID'] != 1100) ? kf::gen_del_icon($row['GID'], false, 'delgroup') : '');
            #  $row['icons'][] = kf::gen_std_icon($row['GID'], 'add.png', 'Kunde hinzuf&uuml;gen', 'id', 'addkunde', '&section=addkunde', $_SERVER['PHP_SELF']);
            $row['icons'][] = kf::gen_std_icon($row['GID'], 'fa-list-alt', 'Kunden anzeigen', 'id', 'showcustomer', '&section=showcustomer', $_SERVER['PHP_SELF']);
            $row['icons'][] = kf::gen_approve_icon($row['GID'], $row['cms_approval'], 'approve_group');
            $this->CUSTGROUPS['custgroups'][] = $row;
        }

        $cols = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION . " ORDER BY col_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->CUSTGROUPS['collections'][] = $row;
        }
    }

    function setPerm($group_id) {
        $PERM = $_POST['PERM'];
        $PERMOD = $_POST['PERMOD'];
        $this->db->query("DELETE FROM " . TBL_CMS_CUSTPERM . " WHERE group_id=" . intval($group_id) . "");

        if (is_array($_POST['pageids'])) {
            foreach ($_POST['pageids'] as $key => $page_id) {
                $sql = "";
                if (is_array($PERM[$page_id])) {
                    foreach ($PERM[$page_id] as $col => $val) {
                        $sql .= (($sql != "") ? ',' : '') . $col . "=" . intval($val);
                    }
                    $this->db->query("INSERT INTO " . TBL_CMS_CUSTPERM . " SET page_id=" . $page_id . ",group_id=" . intval($group_id) . "," . $sql . " ");
                }
            }
        }

        if (is_array($_POST['pagemods'])) {
            foreach ($_POST['pagemods'] as $modname => $mod) {
                $sql = "";
                if (is_array($PERMOD[$modname])) {
                    foreach ($PERMOD[$modname] as $col => $val) {
                        $sql .= (($sql != "") ? ',' : '') . $col . "=" . intval($val);
                    }
                    $sql = "INSERT INTO " . TBL_CMS_CUSTPERM . " SET page_id=0,module='" . $modname . "',group_id=" . intval($group_id) . "," . $sql . " ";
                    $this->db->query($sql);
                }
            }
        }

    }

    function cmd_save_group() {
        $FORM = (array )$_POST['FORM'];
        $id = (int)$_POST['id'];
        if ($id > 0) {
            $this->setPerm($_POST['id']);
            update_table(TBL_CMS_CUSTGROUPS, 'id', $id, $FORM);
        }
        else {
            $result = $this->db->query("SELECT COUNT(id) FROM " . TBL_CMS_CUSTGROUPS . " WHERE groupname='" . $FORM['groupname'] . "'");
            while ($row = $this->db->fetch_array($result)) {
                $count = $row[0];
            }
            if ($count > 0)
                $this->msge('Name schon vorhanden.');
            foreach ($FORM as $key => $wert) {
                if (strlen($wert) == 0) {
                    $this->msge('Bitte alle Felder ausf&uuml;llen.');
                    break;
                }
            }
            if ($this->has_errors() == false) {
                $id = insert_table(TBL_CMS_CUSTGROUPS, $FORM);
                $this->setPerm($id);
                keimeno_class::msg('Erfolgreich angelegt...');
                $this->ej('reloadgroups');
            }
        }
        $this->ej();
    }

    function cmd_edit() {
        global $MODULE;
        $this->CUSTGROUPS['group'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " WHERE id=" . $_GET['id'] . " LIMIT 1");
        $result = $this->db->query("SELECT *,T.id AS PAGEID FROM " . TBL_CMS_TEMPLATES . "	T 
  		LEFT JOIN " . TBL_CMS_CUSTPERM . " P ON (P.page_id=T.id AND P.group_id=" . intval($_GET['id']) . ") 
  		WHERE T.has_perm=1 AND modident=''
  		GROUP BY T.id
  		ORDER BY T.description");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->CUSTGROUPS['pages'][] = $row;
        }

        foreach ($MODULE as $key => $row) {
            if ($row['hasperm'] === TRUE) {
                $PO = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUSTPERM . " WHERE group_id=" . intval($_GET['id']) . " AND module='" . $key . "'");
                $this->CUSTGROUPS['perm'][] = array(
                    'key' => $key,
                    'CP' => $PO,
                    'module_name' => $row['module_name']);
            }
        }
    }

    function cmd_custsearch() {
        $wort = trim($_GET['sw']);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " 
	WHERE kid LIKE ('%" . $wort . "%') COLLATE utf8_bin
	OR email LIKE ('%" . $wort . "%') COLLATE utf8_bin
	OR firma LIKE ('%" . $wort . "%') COLLATE utf8_bin
	OR nachname LIKE ('%" . $wort . "%') COLLATE utf8_bin
	OR email_notpublic LIKE ('%" . $wort . "%') COLLATE utf8_bin	
	OR vorname LIKE ('%" . $wort . "%') COLLATE utf8_bin
    ORDER BY nachname
    LIMIT 50");
        while ($row = $this->db->fetch_array($result)) {
            $row['icons'][] = kf::gen_std_icon($row['kid'], 'fa-plus', 'Kunde hinzuf&uuml;gen', 'kid', 'add_customer_to_group', '&id=' . $_GET['id'], $_SERVER['PHP_SELF']);
            $this->CUSTGROUPS['customers'][] = $row;
        }
        $this->parse_to_smarty();
        kf::echo_template('custgroup.custsearch');
    }

    function cmd_add_customer_to_group() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid='" . $_GET['kid'] . "'");
        while ($row = $this->db->fetch_array_names($result)) {
            $groups[$row['gid']] = $row['gid'];
        }
        $groups[$_GET['id']] = $_GET['id'];
        $groups = array_unique($groups);
        $user_obj = new member_class();
        $user_obj->setKid($_GET['kid']);
        $user_obj->setMemGroups($groups, array(), true, false);
        keimeno_class::msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=all&section=start');
        $this->hard_exit();
    }

    function cmd_removekunde() {
        $user_obj = new member_class();
        $groups = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTTOGROUP . " WHERE kid='" . $_GET['ident'] . "'");
        while ($row = $this->db->fetch_array_names($result)) {
            $groups[$row['gid']] = $row['gid'];
        }
        unset($groups[$_GET['gid']]);
        $groups = array_unique($groups);
        $user_obj->setKid($_GET['ident']);
        $user_obj->setMemGroups($groups, array(), true, false);
        $this->ej();
    }

    function cmd_showcustomer() {
        $this->CUSTGROUPS['group'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " G WHERE G.id=" . $_GET['id']);
        $result = $this->db->query("SELECT K.*, G.id AS GID,UG.id AS UGID,UG.* 
			FROM " . TBL_CMS_CUSTGROUPS . " G," . TBL_CMS_CUSTTOGROUP . " UG ," . TBL_CMS_CUST . " K 
			WHERE UG.gid=G.id AND G.id=" . $_GET['id'] . " AND K.kid=UG.kid ORDER BY nachname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['kid'], false, 'removekunde', '', '&gid=' . $row['GID']);
            $this->CUSTGROUPS['customers'][] = $row;
        }
    }

    function cmd_a_addcolgroup() {
        $this->CUSTGROUPS['COLL_OBJ'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_COLLECTION . " G WHERE G.id=" . $_GET['collid']);
        $result = $this->db->query("SELECT *,G.id AS GID,COUNT(kid) AS KCOUNT
	FROM " . TBL_CMS_CUSTGROUPS . " G 
	LEFT JOIN " . TBL_CMS_CUSTTOGROUP . " UG ON (UG.gid=G.id)
	WHERE G.id>1 AND G.id<>1000 GROUP BY G.id ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['checked'] = ((in_array($row['GID'], explode_string_by_ident($this->CUSTGROUPS['COLL_OBJ']['col_groups']))) ? 'checked' : '');
            $this->CUSTGROUPS['groups'][] = $row;
        }
    }

    function cmd_addnewcol() {
        $this->db->query("INSERT INTO " . TBL_CMS_COLLECTION . " SET col_name='{LBL_NEWCOLLECTION}'");
        keimeno_class::msg('update done.');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&section=coll&cmd=coll');
        $this->hard_exit();
    }
}

?>