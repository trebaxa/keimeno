<?php




/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class perm_class extends keimeno_class {

    var $perm = array();
    var $table = array();
    var $sqltable = "";
    var $sqltablegr = "";


    function __construct() {
        parent::__construct();
    }

    function removeIdFromArray($groups, $gid) {
        $NEWSET = array();
        foreach ($groups as $key => $saved_gid) {
            if ($saved_gid <> $gid)
                $NEWSET[] = $saved_gid;
        }
        return $NEWSET;
    }

    function buildSQLArray($groups) {
        $sql = "";
        $groups = array_unique($groups);
        foreach ($groups as $key => $saved_gid) {
            $sql .= (($sql != "") ? ';' : '') . $saved_gid;
        }
        return $sql;
    }

    function delGroup($gid) {
        $gid = intval($gid);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_APERMISSIONS);
        while ($row = $this->db->fetch_array_names($result)) {
            $NEWSET = array();
            $groups = explode(';', $row['p_groups']);
            $groups = $this->removeIdFromArray($groups, $gid);
            $NEWSET['p_groups'] = $this->buildSQLArray($groups);
            update_table(TBL_CMS_APERMISSIONS, 'p_name', $row['p_name'], $NEWSET);
        }
    }

    function load_perm_groups() {
        $result = $this->db->query("SELECT* FROM " . TBL_CMS_APERMISSIONSGROUPS . " WHERE 1	ORDER BY g_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->groups['groups'][] = $row;
        }
        return (array )$this->groups;
    }

    function loadPermFromGroup($gid) {
        $gid = intval($gid);
        $this->perm = $this->table = array();
        $result = $this->db->query("SELECT *,PG.id AS GID FROM " . TBL_CMS_APERMISSIONS . " P, " . TBL_CMS_APERMISSIONSGROUPS . " PG
	WHERE P.p_gid=PG.id
	ORDER BY PG.g_title ASC, P.p_subgroup, P.p_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['smarty_tag'] = htmlspecialchars('<%$PERM.' . $row['p_name'] . '%>');
            $groups = explode(';', $row['p_groups']);
            $row['p_value'] = in_array($gid, $groups) || $gid == 1;
            if ($row['g_mod'] == '' || is_module_installed($row['g_mod']) === TRUE) {
                $this->perm[$row['p_name']] = $row['p_value'];
                $this->table[$row['GID']]['list'][] = $row;
                $this->table[$row['GID']]['g_title'] = $row['g_title'];
            }
        }
        #  echoarr( $this->table);
        return $this->table;
    }

    function removeGroupFromPerm($gid, $pname) {
        $P = $this->db->query_first("SELECT * FROM " . TBL_CMS_APERMISSIONS . " WHERE p_name='" . $pname . "'");
        $groups = explode(';', $P['p_groups']);
        $groups = $this->removeIdFromArray($groups, $gid);
        $NEWSET = array();
        $NEWSET['p_groups'] = $this->buildSQLArray($groups);
        update_table(TBL_CMS_APERMISSIONS, 'p_name', $pname, $NEWSET);
        return $groups;
    }

    function savePerms($gid, $FORM) {
        $FORM = (array )$FORM;
        $gid = intval($gid);
        $this->loadPermFromGroup($gid);

        //entferne Berechtigung
        foreach ($this->perm as $pname_check => $value) {
            if (!array_key_exists($pname_check, $FORM)) {
                $this->removeGroupFromPerm($gid, $pname_check);
            }
        }

        // addiere Berechtigung
        foreach ($FORM as $pname => $value) {
            $P = $this->db->query_first("SELECT * FROM " . TBL_CMS_APERMISSIONS . " WHERE p_name='" . $pname . "'");
            $groups = explode(';', $P['p_groups']);
            $groups[] = $gid;
            $NEWSET = array('p_groups' => $this->buildSQLArray(array_unique($groups)));
            update_table(TBL_CMS_APERMISSIONS, 'p_name', $P['p_name'], $NEWSET);
        }

    }

    function allowAllToGid($gid) {
        $gid = intval($gid);
        $this->loadPermFromGroup($gid);
        foreach ($this->perm as $pname => $value) {
            $P = $this->db->query_first("SELECT * FROM " . TBL_CMS_APERMISSIONS . " WHERE p_name='" . $pname . "'");
            $groups = explode(';', $P['p_groups']);
            $groups[] = $gid;
            $NEWSET = array();
            $NEWSET['p_groups'] = $this->buildSQLArray($groups);
            update_table(TBL_CMS_APERMISSIONS, 'p_name', $P['p_name'], $NEWSET);
        }
    }


}

?>