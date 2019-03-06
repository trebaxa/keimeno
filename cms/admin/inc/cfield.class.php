<?PHP

# Scripting by Trebaxa Company(R) 2008    									*

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


DEFINE('TBL_CMS_CUSTOMFIELDS', TBL_CMS_PREFIX . 'custfields');
DEFINE('TBL_CMS_CUSTOMFIELDSCONT', TBL_CMS_PREFIX . 'custfields_cont');

/**
 * cfield_class
 * 
 * @package Keimeno
 * @author Trebaxa GmbH&Co.KG
 * @copyright 2016
 * @version 1.0
 * @access public
 */

class cfield_class extends keimeno_class {

    var $table = "";
    var $tbl_prefix = "CF_";
    var $ident = "K";
    var $log = "";

    var $CF_TYPES = array(
        'I' => array(
            'label' => '{LBL_CFNUMERIC}',
            'sqldef' => "int(11) NOT NULL DEFAULT '0'",
            'input' => 'text'),
        'D' => array(
            'label' => '{LBL_CFDATE}',
            'sqldef' => "date NOT NULL DEFAULT '0000-00-00'",
            'input' => 'text'),
        'DC' => array(
            'label' => '{LBL_CFDECIMAL}',
            'sqldef' => "decimal(10,2) NOT NULL DEFAULT '0.00'",
            'input' => 'text'),
        'S' => array(
            'label' => '{LBL_CFSTRING}',
            'sqldef' => 'varchar(255) NOT NULL',
            'input' => 'text'),
        'T' => array(
            'label' => '{LBL_CFTEXT}',
            'sqldef' => 'text NOT NULL',
            'input' => 'textarea'),
        'L' => array(
            'label' => '{LBL_CFLIST}',
            'sqldef' => 'varchar(255) NOT NULL',
            'input' => 'select'),
        'C' => array(
            'label' => '{LBL_CFCHECKBOX}',
            'sqldef' => "int(6) NOT NULL DEFAULT '0'",
            'input' => 'checkbox'));

    /**
     * cfield_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * cfield_class::cmd_a_del()
     * 
     * @return
     */
    function cmd_a_del() {
        if ($this->delField($_GET['ident'])) {
            keimeno_class::msg('{LBL_DELETED}');
        }
        else {
            keimeno_class::msge('{LBL_NOTDELETED}');
        }
        $this->ej();
    }

    /**
     * cfield_class::cmd_a_msave()
     * 
     * @return
     */
    function cmd_a_msave() {
        $this->msave($_POST['FORM']);
        $this->ej();
    }


    /**
     * cfield_class::delField()
     * 
     * @param mixed $id
     * @return
     */
    function delField($id) {
        $result = $this->db->query("DELETE FROM " . TBL_CMS_CUSTOMFIELDS . " WHERE id=" . $id . " AND cf_ident='" . $this->ident . "' LIMIT 1");
        if ($result) {
            mysqli_query($this->db->link_id, "ALTER TABLE " . TBL_CMS_CUST . " DROP " . $this->tbl_prefix . $id);
            $res = true;
        }
        else
            $res = false;
        $this->syncFields();
        return $res;
    }

    /**
     * cfield_class::msave()
     * 
     * @param mixed $FORM
     * @return
     */
    function msave($FORM) {
        $SQL_ARR = array();
        if (count($FORM) > 0) {
            foreach ($FORM as $key => $FORM_SET) {
                if (count($FORM_SET) > 0) {
                    foreach ($FORM_SET as $id => $value) {
                        $SQL_ARR[$id][$key] = $value;
                    }
                }
            }
        }
        if (count($SQL_ARR) > 0) {
            $last_bis = 0;
            foreach ($SQL_ARR as $id => $SQL_SET) {
                $SQL_SET['cf_verify'] = ($SQL_SET['cf_verify'] == 1) ? 1 : 0;
                $SQL_SET['cf_duty'] = ($SQL_SET['cf_duty'] == 1) ? 1 : 0;
                $SQL_SET['cf_search'] = ($SQL_SET['cf_search'] == 1) ? 1 : 0;
                update_table(TBL_CMS_CUSTOMFIELDS, 'id', $id, $SQL_SET);
            }
        }
        $this->syncFields();
    }

    /**
     * cfield_class::cmd_a_custm()
     * 
     * @return
     */
    function cmd_a_custm() {
        header('location:kreg.php');
        exit;
    }

    /**
     * cfield_class::cmd_save_lang()
     * 
     * @return
     */
    function cmd_save_lang() {
        $this->save($_POST['FORM'], $_POST['cf_id'], $_POST['lids']);
        $this->ej();
    }

    /**
     * cfield_class::save()
     * 
     * @param mixed $FORM
     * @param mixed $cf_id
     * @param mixed $lids
     * @return
     */
    function save($FORM, $cf_id, $lids) {
        foreach ($lids as $key => $wert) {
            $result = $this->db->query("SELECT COUNT(id) FROM " . TBL_CMS_CUSTOMFIELDSCONT . " WHERE cf_id='" . $cf_id . "' AND langid='" . $wert . "'");
            while ($row = $this->db->fetch_array($result)) {
                $count = $row[0];
            }
            if ($count > 0) {
                $this->db->query("UPDATE " . TBL_CMS_CUSTOMFIELDSCONT . " SET cf_label='" . $FORM[$wert]['cf_label'] . "',content='" . $FORM[$wert]['content'] .
                    "' WHERE cf_id='" . $cf_id . "' AND langid='" . $wert . "'");
            }
            else {
                $this->db->query("INSERT INTO " . TBL_CMS_CUSTOMFIELDSCONT . " SET cf_label='" . $FORM[$wert]['cf_label'] . "',content='" . $FORM[$wert]['content'] .
                    "', langid='" . $wert . "', cf_id='" . $cf_id . "'");
            }
        }
    }

    /**
     * cfield_class::newSet()
     * 
     * @return
     */
    function newSet() {
        $NEWROW = array();
        $NEWROW['cf_name'] = '#NEW FIELD';
        $NEWROW['cf_ident'] = $this->ident;
        $NEWROW['cf_type'] = "S";
        insert_table(TBL_CMS_CUSTOMFIELDS, $NEWROW);
        $new_id = $this->db->insert_id();
        update_table(TBL_CMS_CUSTOMFIELDS, 'id', $new_id, $NEWROW);
        $this->syncFields();
    }

    /**
     * cfield_class::syncFields()
     * 
     * @return
     */
    function syncFields() {
        $this->log = "";
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTOMFIELDS . " WHERE cf_ident='" . $this->ident . "' ORDER BY cf_type");
        while ($row = $this->db->fetch_array_names($result)) {
            $exists_col_type = false;
            $column_types = $this->get_all_columns_from_table(TBL_CMS_CUST); # GetAllColTYPEs(TBL_CMS_CUST);
            foreach ($column_types as $column_name => $column_TYPE) {
                if ($column_name == $this->tbl_prefix . $row['id']) {
                    $exists_col_type = true;
                    break;
                }
            }
            $db_type = $this->CF_TYPES[$row['cf_type']];
            if ($exists_col_type == false) {
                $sql = "ALTER TABLE " . TBL_CMS_CUST . " ADD " . $this->tbl_prefix . $row['id'] . " " . $db_type['sqldef'] . "";
                mysqli_query($this->db->link_id, $sql);
                $this->log .= $sql . '<br>';
            }
            else {
                $sql = "ALTER TABLE " . TBL_CMS_CUST . " CHANGE " . $this->tbl_prefix . $row['id'] . " " . $this->tbl_prefix . $row['id'] . " " . $db_type['sqldef'];
                $this->log .= $sql . '<br>';
                mysqli_query($this->db->link_id, $sql);
            }
        }
        unset($_SESSION['CACHED_SQLS']['Q']);
    }

    /**
     * cfield_class::getHTMLInputCFields()
     * 
     * @param mixed $row
     * @param mixed $FORM
     * @return
     */
    function getHTMLInputCFields($row, $FORM) {
        $cf_type = $row['cf_type'];
        if ($cf_type == 'D') {
            $FORM[$this->tbl_prefix . $row['CFID']] = (!empty($FORM[$this->tbl_prefix . $row['CFID']])) ? my_date('d.m.Y', $FORM[$this->tbl_prefix . $row['CFID']]) : '';
        }
        if ($cf_type == 'T') {
            $it = '<textarea class="form-control" name="FORM[' . $this->tbl_prefix . $row['CFID'] . ']" rows="6" cols="60">' . htmlspecialchars($FORM[$this->tbl_prefix . $row['CFID']]) .
                '</textarea>';
        }
        else
            if ($cf_type == 'L') {
                $liste = explode(";", $row['cf_select']);
                foreach ($liste as $key => $value)
                    $lisel .= '<option ' . (($value == $FORM[$this->tbl_prefix . $row['CFID']]) ? 'selected' : '') . ' value="' . htmlspecialchars($value) . '">' . htmlspecialchars($value) .
                        '</option>';
                $it = '<select name="FORM[' . $this->tbl_prefix . $row['CFID'] . ']">' . $lisel . '</select>';
            }
            else
                if ($cf_type == 'C') {
                    $it = '<input value="1" ' . (($FORM[$this->tbl_prefix . $row['CFID']] == 1) ? 'checked' : '') . ' type="checkbox" name="FORM[' . $this->tbl_prefix . $row['CFID'] .
                        ']">';
                }
                else {
                    $it = '<input ' . (($row['cf_duty'] == 1) ? 'required="" ' : '') . ' class="form-control"  placeholder="' . htmlspecialchars($row['cf_label']) . (($cf_type ==
                        'D') ? ' (dd.mm.YYYY)' : '') . '" name="FORM[' . $this->tbl_prefix . $row['CFID'] . ']" type="text" value="' . htmlspecialchars($FORM[$this->tbl_prefix . $row['CFID']]) .
                        '" >';
                }
                return $it;
    }

    /**
     * cfield_class::format_for_saveing()
     * 
     * @param mixed $FORM
     * @return
     */
    function format_for_saveing($FORM) {
        $return_table = $this->dbresult_to_array($this->db->query("SELECT * FROM " . TBL_CMS_CUSTOMFIELDS . " ORDER BY cf_name"));
        foreach ($return_table as $row) {
            if (array_key_exists($this->tbl_prefix . $row['id'], $FORM)) {
                $cf_type = $row['cf_type'];
                if ($cf_type == 'D') {
                    $FORM[$this->tbl_prefix . $row['id']] = (!empty($FORM[$this->tbl_prefix . $row['id']])) ? $this->date_to_sqldate($FORM[$this->tbl_prefix . $row['id']]) : '';
                }
                if ($cf_type == 'DC') {
                    $FORM[$this->tbl_prefix . $row['id']] = (!empty($FORM[$this->tbl_prefix . $row['id']])) ? $this->validate_num_for_sql($FORM[$this->tbl_prefix . $row['id']]) :
                        '0';
                }
                if ($cf_type == 'C') {
                    $FORM[$this->tbl_prefix . $row['id']] = (!empty($FORM[$this->tbl_prefix . $row['id']])) ? 1 : 0;
                }
            }
        }
        return $FORM;
    }

    /**
     * cfield_class::validateCFieldsInput()
     * 
     * @param mixed $FORM
     * @param integer $GBL_LANGID
     * @param string $err_arr
     * @param bool $return_arr
     * @return
     */
    function validateCFieldsInput($FORM, $GBL_LANGID = 1, $err_arr = '', $return_arr = false) {
        $GBL_LANGID = ($GBL_LANGID > 0) ? $GBL_LANGID : 1;
        $return_table = $this->dbresult_to_array($this->db->query("SELECT *,F.id AS CFID FROM " . TBL_CMS_CUSTOMFIELDS . " F LEFT JOIN " . TBL_CMS_CUSTOMFIELDSCONT .
            " FC ON (FC.cf_id=F.id AND FC.langid=" . $GBL_LANGID . ") ORDER BY F.cf_name"));
        foreach ($return_table as $row) {
            if (array_key_exists($this->tbl_prefix . $row['CFID'], $FORM)) {
                $cf_type = $row['cf_type'];
                if ($row['cf_verify'] == 1 && $row['cf_duty'] == 1) {
                    $label = ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']);
                    if ($cf_type == 'D' && strtotime($FORM[$this->tbl_prefix . $row['CFID']]) == FALSE) {
                        $msge .= '[BR]"' . $label . '": {LBL_INVALIDDATE}';
                        $this->addSmartyErr($err_arr, $this->tbl_prefix . $row['CFID'], '{LBL_INVALIDDATE}');
                    }
                    if ($cf_type == 'DC' && is_numeric($FORM[$this->tbl_prefix . $row['CFID']]) == FALSE) {
                        $msge .= '[BR]"' . $label . '": {LBL_INVALIDNUMBER}';
                        $this->addSmartyErr($err_arr, $this->tbl_prefix . $row['CFID'], '{LBL_INVALIDNUMBER}');
                    }
                    if (empty($FORM[$this->tbl_prefix . $row['CFID']]) && array_key_exists($this->tbl_prefix . $row['CFID'], $FORM)) {
                        $msge .= '[BR]"' . $label . '": {LBL_FIELDEMPTY}';
                        $this->addSmartyErr($err_arr, $this->tbl_prefix . $row['CFID'], '{LBL_FIELDEMPTY}');
                    }
                }
            }
        }
        if (!$return_arr)
            return $msge;
        else
            return $err_arr;
    }
    /*
    function validateCFieldsInput_($FORM, $GBL_LANGID = 1) {
    $GBL_LANGID = ($GBL_LANGID > 0) ? $GBL_LANGID : 1;
    $return_table = $this->db->query("SELECT *,F.id AS CFID FROM " . TBL_CMS_CUSTOMFIELDS . " F LEFT JOIN " . TBL_CMS_CUSTOMFIELDSCONT .
    " FC ON (FC.cf_id=F.id AND FC.langid=" . $GBL_LANGID . ") GROUP BY F.id ORDER BY F.cf_name");
    foreach ($return_table as $row) {
    if (array_key_exists($this->tbl_prefix . $row['CFID'], $FORM)) {
    $cf_type = $row['cf_type'];
    if ($row['cf_verify'] == 1 && $row['cf_duty'] == 1) {
    if ($cf_type == 'D' && strtotime($FORM[$this->tbl_prefix . $row['CFID']]) == FALSE) {
    $msge .= '[BR]"' . ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']) . '": {LBL_INVALIDDATE}';
    $this->addSmartyErr($err_arr, $row['cf_name'], '{LBL_INVALIDDATE}');
    }
    if ($cf_type == 'DC' && is_numeric($FORM[$this->tbl_prefix . $row['CFID']]) == FALSE) {
    $msge .= '[BR]"' . ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']) . '": {LBL_INVALIDNUMBER}';
    $this->addSmartyErr($err_arr, $row['cf_name'], '{LBL_INVALIDNUMBER}');
    }
    if (empty($FORM[$this->tbl_prefix . $row['CFID']]) && array_key_exists($this->tbl_prefix . $row['CFID'], $FORM)) {
    $msge .= '[BR]"' . ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']) . '": {LBL_FIELDEMPTY}';
    $this->addSmartyErr($err_arr, $row['cf_name'], '{LBL_FIELDEMPTY}');

    }
    }
    }
    }
    $this->smarty->assign('cfield_err', $err_arr);
    return $msge;
    }
    */
    /**
     * cfield_class::buildSqlSearchOR()
     * 
     * @param mixed $value
     * @return
     */
    function buildSqlSearchOR($value) {
        $key_arr = array();
        $return_table = $this->dbresult_to_array($this->db->query("SELECT * FROM " . TBL_CMS_CUSTOMFIELDS . " WHERE cf_search=1 ORDER BY cf_name"));
        foreach ($return_table as $row) {
            $sql_or .= ((!empty($sql_or)) ? " OR " : '') . $this->tbl_prefix . $row['id'] . " LIKE '%" . $value . "%'";
        }
        return $sql_or;
    }


    /**
     * cfield_class::replaceJoker()
     * 
     * @param mixed $FORM
     * @param mixed $langid
     * @param mixed $html
     * @return
     */
    function replaceJoker($FORM, $langid, $html) {
        $key_arr = array();
        $result = $this->db->query("SELECT *,F.id AS CFID FROM " . TBL_CMS_CUSTOMFIELDS . " F LEFT JOIN " . TBL_CMS_CUSTOMFIELDSCONT .
            " FC ON (FC.cf_id=F.id AND FC.langid=" . (int)$langid . ") ORDER BY F.cf_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $key_arr['{TMPL_CFL_' . $row['CFID'] . '}'] = ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']) . (($row['cf_duty'] == 1) ? '*' : '');
            $key_arr['{TMPL_CFD_' . $row['CFID'] . '}'] = $row['content'];
            $key_arr['{TMPL_CFI_' . $row['CFID'] . '}'] = $this->getHTMLInputCFields($row, $FORM);
        }
        if (count($key_arr) > 0) {
            return strtr($html, $key_arr);
        }
        else {
            return $html;
        }
    }

    /**
     * cfield_class::replaceJokerEmail()
     * 
     * @param mixed $langid
     * @param mixed $html
     * @param mixed $kobj
     * @return
     */
    function replaceJokerEmail($langid, $html, $kobj) {
        $key_arr = array();
        $return_table = $this->dbresult_to_array($this->db->query("SELECT *,F.id AS CFID FROM " . TBL_CMS_CUSTOMFIELDS . " F LEFT JOIN " . TBL_CMS_CUSTOMFIELDSCONT .
            " FC ON (FC.cf_id=F.id AND FC.langid=" . $langid . ") ORDER BY F.cf_name"));
        foreach ($return_table as $row) {
            $key_arr['!!TMPL_CFL_' . $row['CFID'] . '!!'] = ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']);
            $key_arr['!!TMPL_CFC_' . $row['CFID'] . '!!'] = $kobj[$this->tbl_prefix . $row['CFID']];
        }
        return fillArray($key_arr, $html);
    }

    /**
     * cfield_class::dbresult_to_array()
     * 
     * @param mixed $result
     * @return
     */
    function dbresult_to_array($result) {
        $arr = array();
        while ($row = $this->db->fetch_array_names($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

    /**
     * cfield_class::genLegende()
     * 
     * @param mixed $jbs
     * @param mixed $jbe
     * @return
     */
    function genLegende($jbs, $jbe) {
        $key_arr = array();
        $return_table = $this->dbresult_to_array($this->db->query("SELECT *,F.id AS CFID FROM " . TBL_CMS_CUSTOMFIELDS . " F LEFT JOIN " . TBL_CMS_CUSTOMFIELDSCONT .
            " FC ON (FC.cf_id=F.id AND FC.langid=1) ORDER BY F.cf_name"));
        foreach ($return_table as $row) {
            $ret .= $jbs . 'TMPL_CFL_' . $row['CFID'] . $jbe . ' = ' . ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']) . ' "Bezeichnung"<br>';
            $ret .= $jbs . 'TMPL_CFC_' . $row['CFID'] . $jbe . ' = ' . ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_label']) . ' "Inhalt"<br>';
        }
        return $ret;
    }

    /**
     * cfield_class::buildSimpleAdminTable()
     * 
     * @param mixed $FORM
     * @return
     */
    function buildSimpleAdminTable($FORM) {
        $result = $this->db->query("SELECT *,F.id AS CFID FROM " . TBL_CMS_CUSTOMFIELDS . " F LEFT JOIN " . TBL_CMS_CUSTOMFIELDSCONT .
            " FC ON (FC.cf_id=F.id AND FC.langid=1) ORDER BY F.cf_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $cf_rows .= '<tr> <td>' . ((empty($row['cf_label'])) ? $row['cf_name'] : $row['cf_name'] . '/' . $row['cf_label']) . '</td> <td>' . $this->getHTMLInputCFields($row,
                $FORM) . '</td> </tr>';
        }
        return (!empty($cf_rows)) ? '<table class="table table-striped table-hover">' . $cf_rows . '</table>' : '';
    }

    /**
     * cfield_class::load_cf_fields_for_customer()
     * 
     * @param mixed $kid
     * @return
     */
    function load_cf_fields_for_customer($kid) {
        # load user defined columns
        $kid = (int)$kid;
        $arr = array();
        if ($kid > 0) {
            $KOBJ = $this->db->query_first("SELECT K.* , L.land as COUNTRY FROM " . TBL_CMS_CUST . " K, " . TBL_CMS_LAND . " L WHERE L.id=K.land AND K.kid=" . (int)$kid);
            if (is_array($KOBJ)) {
                foreach ($KOBJ as $key => $value) {
                    if (strstr($key, 'CF_')) {
                        list($temp, $cfid) = explode('_', $key);
                        $arr[] = array(
                            'key' => $key,
                            'value' => $value,
                            'CF' => $this->db->query_first("SELECT * FROM " . TBL_CMS_CUSTOMFIELDS . " WHERE id=" . (int)$cfid));
                    }
                }
            }
        }
        return $arr;
    }

}

?>