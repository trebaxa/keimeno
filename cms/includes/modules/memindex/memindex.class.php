<?php

/**
 * @package    memindex
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_CMS_CUSTCOLGROUPS', TBL_CMS_PREFIX . 'custcolgroups');
DEFINE('TBL_CMS_COLLECTION', TBL_CMS_PREFIX . 'cust_collect');

class memindex_class extends memindex_master_class {

    /**
     * memindex_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object, $user_obj;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->user_obj = $user_obj;
        $this->MEMINDEX = $this->selected_item = array();
        $this->memberclass = new member_class();
        $this->GRAPHIC_FUNC = new graphic_class();
        $this->customerlist();
    }

    /**
     * memindex_class::cmd_intelisearch()
     * 
     * @return
     */
    function cmd_intelisearch() {
        $word = strip_tags(trim($_POST['word']));
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " K, " . TBL_CMS_CUSTTOGROUP . " GMA, " . TBL_CMS_RGROUPS . " G 
            WHERE cms_isindex=1 AND K.kid=GMA.kid AND G.id=GMA.gid AND 
                (nachname LIKE '%" . $word . "%' 
                OR ort LIKE '%" . $word . "%' 
                OR LOWER(G.groupname) LIKE '%" . strtolower($word) . "%'
                OR firma LIKE '%" . $word . "%') 
                GROUP BY K.kid
                ORDER BY nachname ASC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row = $this->memberclass->setOptions($row, true);
            $row['collectiontogroup'] = $this->get_coll_and_groups_of_customer($row['kid']);
            $found_members[] = $row;
        }
        $this->MEMINDEX['foundmembers'] = (array )$found_members;
    }

    /**
     * memindex_class::cmd_detailsearch()
     * 
     * @return
     */
    function cmd_detailsearch() {
        $direct = (intval($_GET['direct']) == 0) ? 'ASC' : 'DESC';
        if ($this->has_errors() == false) {
            $sql_add = "";
            if (is_array($_POST['FORM'])) {
                foreach ($_POST['FORM'] as $db_column => $sword) {
                    $sword = trim(strip_tags($sword));
                    if ($sword != "")
                        $sql_add .= (($sql_add != "") ? ' AND ' : '') . $db_column . " LIKE '%" . $sword . "%' ";
                }
            }
            if (is_array($_POST['FORM_NOTEMPTY'])) {
                foreach ($_POST['FORM_NOTEMPTY'] as $db_column => $sword) {
                    $sword = trim(strip_tags($sword));
                    if ($sword != "")
                        $sql_add .= (($sql_add != "") ? ' AND ' : '') . "C." . $db_column . " LIKE '%" . $sword . "%' ";
                    else
                        self::msge('Feld ' . $db_column . ' ist leer');
                }
            }
            if ($sql_add != "")
                $sql_add = ' AND (' . $sql_add . ')';
            if ($this->has_errors() == false) {
                $kid_arr = array();
                # search with group filter
                $kid_sql = "";
                if (intval($_POST['TABLE']['membergroups']) > 0) {
                    $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTCOLGROUPS . " CCG, " . TBL_CMS_COLLECTION . " COL WHERE CCG.col_id=COL.id AND COL.approval=1 ");
                    while ($COL_OBJ = $this->db->fetch_array_names($result)) {
                        $group_ids = explode_string_by_ident($COL_OBJ['groups']);
                        if (in_array(intval($_POST['TABLE']['membergroups']), $group_ids)) {
                            $kid_arr[] = $COL_OBJ['kid'];
                        }
                    }
                }

                # serach with collection group filter
                $collgroup_sql = "";
                $coll_id = $groupid = 0;
                if ($_POST['TABLE']['col_groupid'] != "") {
                    list($coll_id, $groupid) = explode('-', $_POST['TABLE']['col_groupid']);
                    $result = $this->db->query("SELECT * 
	                   FROM " . TBL_CMS_CUSTCOLGROUPS . " CCG, " . TBL_CMS_COLLECTION . " COL 
	                   WHERE COL.id=" . (int)$coll_id . " AND CCG.col_id=COL.id AND COL.approval=1 
	                   ORDER BY COL.col_name");
                    $group_ids = array();
                    while ($row = $this->db->fetch_array_names($result)) {
                        $group_ids = explode_string_by_ident($row['groups']);
                        if (count($group_ids) > 0 && in_array($groupid, $group_ids)) {
                            $kid_arr[] = $row['kid'];
                        }
                    }

                }
                $kid_sql = "";
                if (count($kid_arr) > 0) {
                    $kid_sql = " AND C.kid IN (" . implode(',', $kid_arr) . ")";
                }

                # execute search
                $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " C
                    WHERE C.cms_isindex=1 " . $sql_add . " " . $kid_sql . "
                    ORDER BY nachname " . $direct);
                while ($row = $this->db->fetch_array_names($result)) {
                    $row = $this->memberclass->setOptions($row, true);
                    $row['collectiontogroup'] = $this->get_coll_and_groups_of_customer($row['kid']);
                    $found_members[] = $row;
                }
            }
        }
        $this->MEMINDEX['foundmembers'] = (array )$found_members;
    }

    /**
     * memindex_class::cmd_sabc()
     * 
     * @return
     */
    function cmd_sabc() {
        $aliste = array();
        $aliste = str_split(strip_tags(trim($_GET['abc'])));
        $sql = "";
        if (count($aliste) > 0) {
            foreach ($aliste as $buchstabe) {
                $sql .= (($sql != "") ? ' OR ' : '') . " nachname LIKE '" . $buchstabe . "%'";
            }
        }
        if ($sql != "")
            $sql = ' AND (' . $sql . ')';
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE cms_isindex=1 " . $sql . " ORDER BY nachname " . $_GET['direct'] . "");
        while ($row = $this->db->fetch_array_names($result)) {
            $label = member_class::gen_link_label($row);
            $row['link'] = $this->memberclass->genCustomerLink($label);
            $row = $this->memberclass->setOptions($row, true);
            $found_members[] = $row;
        }
        $this->MEMINDEX['foundmembers'] = (array )$found_members;
    }

    /**
     * memindex_class::get_member_count_of_group()
     * 
     * @param mixed $groupid
     * @return
     */
    function get_member_count_of_group($groupid) {
        $result = $this->db->query_first("SELECT COUNT(*) AS KCOUNT FROM " . TBL_CMS_CUSTTOGROUP . " G," . TBL_CMS_CUST . " K
	WHERE K.cms_isindex=1
	AND K.kid=G.kid
	AND G.gid=" . (int)$groupid);
        return (int)$result['KCOUNT'];
    }

    /**
     * memindex_class::cmd_grouplist_by_collection_json()
     * 
     * @return
     */
    function cmd_grouplist_by_collection_json() {
        $arr = array();
        $result = $this->load_collection($_GET['colid']);
        if (count($result['groups']) > 0) {
            foreach ($result['groups'] as $key => $row) {
                $arr[] = array(
                    'groupname' => $row['groupname'],
                    'id' => $row['id'],
                    'memcount' => $this->get_member_count_of_group($row['id']));
            }
        }
        echo json_encode(array('list' => $arr));
        $this->hard_exit();
    }

    /**
     * memindex_class::cmd_load_customers_of_group()
     * 
     * @return
     */
    function cmd_load_customers_of_group() {
        $memberlist = array();
        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUSTTOGROUP . " G," . TBL_CMS_CUST . " K
	WHERE K.cms_isindex=1
	AND K.kid=G.kid
	AND G.gid=" . (int)$_GET['groupid'] . "
	ORDER BY K.nachname");
        while ($CUSTOMER = $this->db->fetch_array_names($result)) {
            $label = member_class::gen_link_label($CUSTOMER);
            $CUSTOMER['link'] = $this->memberclass->genCustomerLink($label);
            $CUSTOMER = $this->memberclass->setOptions($CUSTOMER, true);
            $memberlist[] = $CUSTOMER;
        }
        $this->smarty->assign('memberlist', $memberlist);
        if ($_GET['axcall'] == 1) {
            echo_template_fe('member_table');
        }
    }

    /**
     * memindex_class::cmd_load_customers_of_group_and_collection()
     * 
     * @return
     */
    function cmd_load_customers_of_group_and_collection() {
        $memberlist = array();
        $result = $this->db->query("SELECT K.* FROM " . TBL_CMS_CUSTCOLGROUPS . " G," . TBL_CMS_CUST . " K
	WHERE K.cms_isindex=1
    AND K.kid=G.kid
	AND (G.groups LIKE '%" . (int)$_GET['groupid'] . ";%' OR G.groups LIKE '%" . (int)$_GET['groupid'] . "') 
    ORDER BY K.nachname    
    ");
        while ($CUSTOMER = $this->db->fetch_array_names($result)) {
            $label = member_class::gen_link_label($CUSTOMER);
            $CUSTOMER['link'] = $this->memberclass->genCustomerLink($label);
            $CUSTOMER = $this->memberclass->setOptions($CUSTOMER, true);
            $memberlist[] = $CUSTOMER;
        }
        $this->smarty->assign('memberlist', $memberlist);
        if ($_GET['axcall'] == 1) {
            echo_template_fe('member_table');
        }
    }


    /**
     * memindex_class::customerlist()
     * 
     * @return
     */
    function customerlist() {
        $msge = "";
        $this->smarty_values = array();
        if (isset($_POST['FORM'])) {
            $_SESSION['MSUCHEFORM'] = $_POST['FORM'];
            $this->smarty->assign('searchform', $_POST['FORM']);
            $this->smarty->assign('searchform_notempty', $_POST['FORM_NOTEMPTY']);
        }
        $_GET['direct'] = (isset($_GET['direct']) && intval($_GET['direct']) == 0) ? 'ASC' : 'DESC';
        $collections = array();
        $result = null;
        $collections = $this->load_collections();
        if (isset($_GET['colid'])) {
            $collections = $this->load_collections($_GET['colid']);
        }

        if (isset($_GET['aktion']) && $_GET['aktion'] == 'showall') {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE cms_isindex=1 ORDER BY nachname " . $_GET['direct'] . "");
        }
        else
            if (isset($_GET['aktion']) && $_GET['aktion'] == 'showcol') {
                $this->load_collection($_GET['colid']);
            }
            else
                if (isset($_GET['aktion']) && $_GET['aktion'] == 'showgroup') {
                    $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " K, " . TBL_CMS_CUSTTOGROUP . " G 
            WHERE K.cms_isindex=1 
            AND K.kid=G.kid 
            AND G.gid=" . (int)$_GET['id'] . " 
            ORDER BY nachname " . $_GET['direct'] . "");
                    $this->MEMINDEX['member_group'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_RGROUPS . " G WHERE G.id=" . intval($_GET['id']) . " LIMIT 1");
                    $collections = $this->load_collections((int)$_GET['id']);
                }
                else
                    if (isset($_GET['aktion']) && $_GET['aktion'] == 'showalpha' && $_GET['abc'] == "") {
                        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE cms_isindex=1 AND nachname LIKE '" . $_GET['letter'] . "%' ORDER BY nachname " . $_GET['direct'] .
                            "");
                    }


        if ($result && count($_SESSION['err_msgs']) == 0) {
            while ($row = $this->db->fetch_array_names($result)) {
                $label = member_class::gen_link_label($row);
                $row['link'] = $this->memberclass->genCustomerLink($label);
                $row = $this->memberclass->setOptions($row, true);
                $this->smarty_values[] = $row;
            }
            $this->smarty->assign('memberlist', $this->smarty_values);
            $this->smarty->assign('memberlistcount', $this->db->num_rows($result));
        }
        # $this->smarty->assign('global_err', $err_gbl_arr);

        $this->smarty->assign('collections', $collections);
    }


    /**
     * memindex_class::load_groups_by_ids()
     * 
     * @param mixed $group_ids
     * @return
     */
    function load_groups_by_ids($group_ids) {
        $arr = array();
        $total_members = 0;
        if (count($group_ids) > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_RGROUPS . " G WHERE id IN (" . implode(',', $group_ids) . ")");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['memcount'] = $this->get_member_count_of_group($row['id']);
                $total_members += $row['memcount'];
                $arr[] = $row;
            }
        }
        return array('groups' => $arr, 'total_members' => $total_members);
    }


    /**
     * memindex_class::load_collections()
     * 
     * @param integer $group_id
     * @return
     */
    function load_collections($group_id = 0) {

        $group_id = (int)$group_id;
        # Gruppen gruppiert nach Collections
        $COL_OBJs = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION . " WHERE approval=1 ORDER BY col_name");
        while ($COL_OBJ = $this->db->fetch_array_names($COL_OBJs)) {
            $group_ids = explode_string_by_ident($COL_OBJ['col_groups']);
            $mems = array();
            if (in_array($group_id, $group_ids) && $group_id > 0) {
                $result = $this->db->query("SELECT K.*,G.groups AS CGROUPS FROM " . TBL_CMS_CUSTCOLGROUPS . " G," . TBL_CMS_CUST . " K 
			WHERE K.cms_isindex=1 
			AND K.kid=G.kid 
			AND G.col_id=" . $COL_OBJ['id'] . " 
			ORDER BY K.nachname");
                while ($CUSTOMER = $this->db->fetch_array_names($result)) {
                    $group_ids = explode_string_by_ident($CUSTOMER['CGROUPS']);
                    $label = member_class::gen_link_label($CUSTOMER);
                    $CUSTOMER['link'] = $this->memberclass->genCustomerLink($label);
                    $CUSTOMER = $this->memberclass->setOptions($CUSTOMER, true);
                    if (in_array($group_id, $group_ids))
                        $mems[] = $CUSTOMER;
                }
            }
            #	if (count($mems) > 0) {
            $arr = $this->load_groups_by_ids($group_ids);
            $collections[] = array(
                'col_name' => $COL_OBJ['col_name'],
                'col_obj' => $COL_OBJ,
                'groups' => $arr['groups'],
                'total_mem_count' => $arr['total_members'],
                'members' => $mems);
            #	}
        } // WHILE
        return (array )$collections;
    }

    /**
     * memindex_class::load_collection()
     * 
     * @param mixed $col_id
     * @return
     */
    function load_collection($col_id) {
        $col_id = (int)$col_id;
        $groups = array();
        $COL_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_COLLECTION . " WHERE id=" . $col_id . " AND approval=1");
        $group_ids = explode_string_by_ident($COL_OBJ['col_groups']);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_RGROUPS . " G WHERE 1 ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            if (in_array($row['id'], $group_ids)) {
                $groups[$row['id']] = $row;
            }
        }
        $mems = array();
        $G_OBJs = $this->db->query("SELECT K.*,G.groups AS CGROUPS FROM " . TBL_CMS_CUSTCOLGROUPS . " G," . TBL_CMS_CUST . " K
	WHERE K.cms_isindex=1
	AND K.kid=G.kid
	AND G.col_id=" . $col_id . "
	ORDER BY K.nachname");
        while ($CUSTOMER = $this->db->fetch_array_names($G_OBJs)) {
            foreach ($group_ids as $gid) {
                $gids = explode_string_by_ident($CUSTOMER['CGROUPS']);
                $label = member_class::gen_link_label($CUSTOMER);
                $CUSTOMER['link'] = $this->memberclass->genCustomerLink($label);
                $CUSTOMER = $this->memberclass->setOptions($CUSTOMER, true);
                if (in_array($gid, $gids)) {
                    $mems[$gid]['customers'][] = $CUSTOMER;
                    $mems[$gid]['group'] = $groups[$gid];
                }
            }
        }
        $collection = array('col_obj' => $COL_OBJ, 'members' => $mems);

        $this->smarty->assign('collection', $collection);
        $this->smarty->assign('custgroups', $groups);
        return array('collection' => (array )$collection, 'groups' => (array )$groups);
    }


    /**
     * memindex_class::parse_to_smarty_fe()
     * 
     * @return
     */
    function parse_to_smarty_fe() {
        if ($this->smarty->getTemplateVars('MEMINDEX') != null) {
            $this->MEMINDEX = array_merge($this->smarty->getTemplateVars('MEMINDEX'), $this->MEMINDEX);
            $this->smarty->clearAssign('MEMINDEX');
        }
        $this->smarty->assign('MEMINDEX', $this->MEMINDEX);
        $this->smarty->assign('selected_item', $this->selected_item);
    }

    /**
     * memindex_class::init_index()
     * 
     * @return
     */
    function init_index() {
        # load customer groups
        $this->MEMINDEX['groups'] = array();
        $result = $this->db->query("SELECT G.*,G.id AS GID FROM " . TBL_CMS_CUSTGROUPS . " G WHERE G.cms_approval=1 ORDER BY G.groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['link'] = $this->memberclass->genCustomerGroupLink($row['id'], $row['groupname'], $this->GBL_LANGID);
            $this->MEMINDEX['groups'][] = $row;
        }

        # load collections
        $this->MEMINDEX['collections'] = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION . " ORDER BY id");
        while ($row = $this->db->fetch_array_names($result)) {
            $group_ids = explode_string_by_ident($row['col_groups']);
            $row['groups'] = array();
            if (count($group_ids) > 0) {
                $groups = $this->db->query("SELECT * FROM " . TBL_CMS_RGROUPS . " G WHERE id IN (" . implode(',', $group_ids) . ") ORDER BY groupname");
                while ($group = $this->db->fetch_array_names($groups)) {
                    $group['link'] = $this->memberclass->genCustomerGroupLink($group['id'], $group['groupname'], $this->GBL_LANGID);
                    $row['groups'][] = $group;
                }
            }
            $this->MEMINDEX['collections'][] = $row;
        }


        // Alpha Index
        $alpha = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z');
        $arr = array();
        foreach ($alpha as $bs) {
            $arr[] = array('letter' => $bs, 'link' => $this->memberclass->genCustomerAlphaLink($bs));
        }
        $this->smarty->assign('alphaliste', $alpha);
        $this->smarty->assign('alphagroupliste', $arr);
        $alpha = array(
            'ABC',
            'DEF',
            'GHI',
            'JKL',
            'MNO',
            'PQR',
            'STU',
            'VWX',
            'YZ');
        $this->smarty_values = array();
        foreach ($alpha as $bs) {
            $this->MEMINDEX['alphagroupliste2'][] = array('letter' => $bs, 'link' => $_SERVER['SCRIPT_URI'] . '?cmd=sabc&abc=' . $bs);
        }
    }


    /**
     * memindex_class::cmd_showcustomer()
     * 
     * @return
     */
    function cmd_showcustomer() {
        $kid = intval($_GET['id']);
        if ($kid == 0) {
            return;
        }
        $K_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K WHERE K.cms_isindex=1 AND K.kid=" . (int)$kid . " LIMIT 1");

        #load image
        if ($K_OBJ['picture'] != "" && !file_exists(CMS_ROOT . 'images/members/' . $K_OBJ['picture'])) {
            $K_OBJ['picture'] = '';
            update_table(TBL_CMS_CUST, 'kid', $kid, $K_OBJ);
        }

        # load secure files
        $this->MEMINDEX['myfiles'] = self::load_files($kid);

        $K_OBJ = $this->memberclass->setOptions($K_OBJ, true);
        $profil_img = ($K_OBJ['picture'] != "") ? './images/members/' . $K_OBJ['picture'] : './images/opt_member_nopic.jpg';
        $K_OBJ['img_detail'] = thumbit_fe($profil_img, $this->gblconfig->mem_detail_width, $this->gblconfig->mem_detail_height);

        # load groups of customer
        $result = $this->db->query("SELECT *,G.id AS GID
        	FROM " . TBL_CMS_RGROUPS . " G, " . TBL_CMS_CUSTTOGROUP . " CTG
        	WHERE CTG.kid=" . intval($kid) . " AND CTG.gid=G.id
        	ORDER BY G.groupname");
        $memg_ids = $collections = array();
        while ($row = $this->db->fetch_array_names($result)) {
            $K_OBJ['member_groups'][$row['GID']] = $row;
            $memg_ids[$row['GID']] = $row['GID'];
        }

        # load collections
        $result = $this->db->query("SELECT *
            	FROM " . TBL_CMS_COLLECTION . "
            	ORDER BY id");
        while ($row = $this->db->fetch_array_names($result)) {
            $group_ids = explode_string_by_ident($row['col_groups']);
            foreach ($group_ids as $gid) {
                if (in_array($gid, $memg_ids)) {
                    $collections[$row['id']]['col_name'] = $row['col_name'];
                    $collections[$row['id']]['groups'][] = $K_OBJ['member_groups'][$gid];
                }
            }
        }
        $K_OBJ['collections'] = $collections;

        $K_OBJ['collectiontogroup'] = $this->get_coll_and_groups_of_customer($kid);
        $this->smarty->assign('member', $K_OBJ);
    }

    /**
     * memindex_class::get_coll_and_groups_of_customer()
     * 
     * @return
     */
    function get_coll_and_groups_of_customer($kid) {
        # hole Kunde Collection und Gruppen Zuordnungen
        $result = $this->db->query("SELECT * 
	FROM " . TBL_CMS_CUSTCOLGROUPS . " CCG, " . TBL_CMS_COLLECTION . " COL 
	WHERE CCG.kid=" . intval($kid) . " AND CCG.col_id=COL.id AND COL.approval=1 
	ORDER BY COL.col_name");
        $group_ids = $colgroups = array();
        while ($COL_OBJ = $this->db->fetch_array_names($result)) {
            $add_obj = array();
            $group_ids = explode_string_by_ident($COL_OBJ['groups']);
            if (count($group_ids) > 0) {
                $sql = "";
                foreach ($group_ids as $gid)
                    $sql .= (($sql != "") ? ' OR ' : '') . "id=" . $gid;
                $G_OBJs = $this->db->query("SELECT * FROM " . TBL_CMS_RGROUPS . " WHERE " . $sql . " ORDER BY groupname");
                while ($G_OBJ = $this->db->fetch_array_names($G_OBJs)) {
                    if ($G_OBJ['cms_approval'] == 1)
                        $add_obj['groups'][] = $G_OBJ;
                }
            }
            if (count($add_obj['groups']) > 0) {
                $add_obj['collection'] = $COL_OBJ;
                $colgroups[] = $add_obj;
            }
        }
        return $colgroups;
    }


    /**
     * memindex_class::load_latest_members()
     * 
     * @return
     */
    function load_latest_members() {
        $userobj = new member_class();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE cms_isindex=1 
        " . (($this->gblconfig->mem_lp_onlypics == 1) ? " AND picture!=''" : "") . "
        ORDER BY datum DESC LIMIT " . intval($this->gbl_config['mem_lastregcount']));
        while ($row = $this->db->fetch_array_names($result)) {
            $label = member_class::gen_link_label($row);
            $row['link'] = $this->memberclass->genCustomerLink($label);
            $row = $userobj->setOptions($row);
            $row['thumb'] = ($row['picture'] != "") ? gen_thumb_image('./images/members/' . $row['picture'], $this->gblconfig->mem_lp_fotowidth, $this->gblconfig->
                mem_lp_fotoheight, $this->gblconfig->mem_lp_fmethod) : gen_thumb_image('./images/opt_member_nopic.jpg', $this->gblconfig->mem_lp_fotowidth, $this->gblconfig->
                mem_lp_fotoheight, $this->gblconfig->mem_lp_fmethod);
            $this->MEMINDEX['lastcustomers'][$row['kid']] = $row;
        }
        self::allocate_memory($userobj);
        $this->parse_to_smarty_fe();
    }

    /**
     * memindex_class::on_delete_customer()
     * 
     * @param mixed $params
     * @return
     */
    function on_delete_customer($params) {
        $kid = (int)$params['kid'];
        self::delete_dir_with_subdirs(memindex_master_class::get_path($kid));
        if (defined('SHOP_CUST_USE') && SHOP_CUST_USE === true && get_data_count(TBL_ABO, 'kid', "kid=" . $kid) > 0) {
            include_once (SHOP_ROOT . 'admin/inc/abo.class.php');
            $ABO = new abo_class();
            $result = $this->db->query("SELECT * FROM " . TBL_ABO . " WHERE kid=" . $kid);
            while ($row = $this->db->fetch_array_names($result)) {
                $ABO->arcive_abo($row['id']);
                $this->LOGCLASS->addLog('UPDATE', $FORM['kid'] . ' ' . $FORM['nachname'] . ' Abo archived. ID: ' . $row['id']);
            }
            unset($ABO);
        }
        return $params;
    }

    /**
     * memindex_class::try_load_user()
     * 
     * @param mixed $email
     * @return
     */
    function try_load_user($email) {
        $email = trim(strip_tags($email));
        $e_object = array();
        if ($this->gbl_config['login_mode'] == 'PUBLIC_EMAIL') {
            $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.email='" . $email . "' ");
        }
        else
            if ($this->gbl_config['login_mode'] == 'NONE_PUBLIC_EMAIL') {
                $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.email_notpublic='" . $email . "' ");
            }
            else
                if ($this->gbl_config['login_mode'] == 'USERNAME') {
                    $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.username='" . $email . "' ");
                }
                else
                    if ($this->gbl_config['login_mode'] == 'KNR') {
                        $e_object = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " K," . TBL_CMS_LAND . " L WHERE K.land=L.id AND K.kid='" . $email . "' ");
                    }
        return (array )$e_object;
    }

    /**
     * memindex_class::try_load_user_by_password()
     * 
     * @param mixed $email
     * @param mixed $password
     * @return array
     */
    function try_load_user_by_password($email, $password) {
        $e_object = $this->try_load_user($email);
        $e_object['login_msge'] = "";
        if (verfriy_password($password, $e_object['passwort']) === false && $e_object['kid'] > 0) {
            $e_object['login_msge'] = '{LBL_INVALID_PASSWORD}';
            $e_object['feedback'] = 'INVALID_PASSWORT';
        }
        if ($e_object['kid'] <= 0) {
            $e_object['login_msge'] = '{LBL_ACCOUNTNOTFOUND}';
            $e_object['feedback'] = 'ACCOUNT_NOTFOUND';
        }
        if ($e_object['sperren'] == 1 && $e_object['kid'] > 0) {
            $e_object['login_msge'] = '{MSG_ACCOUNTDEAKT}';
            $e_object['feedback'] = 'ACCOUNT_BLOCKED';
        }
        return $e_object;
    }

    /**
     * memindex_class::cmd_sendpass()
     * 
     * @return
     */

    function cmd_sendpass() {
        if (!validate_email_input($_REQUEST['email'])) {
            self::msge('{ERR_EMAIL}');
        }
        if ($this->has_errors() == false) {
            $e_object = $this->try_load_user($_REQUEST['email']);
            if ($e_object['kid'] > 0) {
                $e_object['passwort'] = gen_sid(6);
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . $e_object['passwort'] . "' WHERE kid='" . $e_object['kid'] . "' LIMIT 1");
                send_mail_to(replacer(get_email_template(980), $e_object['kid']));
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET passwort='" . encrypt_password($e_object['passwort']) . "' WHERE kid='" . $e_object['kid'] . "' LIMIT 1");
                $this->LOGCLASS->addLog('SENDMAIL', 'new password send: "<a href="index.php?kwort=' . $_REQUEST['email'] . '">' . $_REQUEST['email'] . '</a>"');
                if ($_REQUEST['autosubmit'] == 1) {
                    echo $_GET['callback'] . '({"status" : "OK"})';
                    $this->hard_exit();
                }
                else {
                    $this->msg('{LBL_EMAILERHALTEN}');
                    HEADER("location: " . self::get_domain_url());
                    $this->hard_exit();
                }
            }
            else {
                $this->LOGCLASS->addLog('FORM_FAILURE', 'password not send (unknown email): "<a href="index.php?kwort=' . $_POST['email'] . '">' . $_POST['email'] . '</a>"');
                self::msge('{LBL_ACCOUNTNOTFOUND}');
                $this->smarty->assign('loginform_err', $err_arr);
                if ($_REQUEST['autosubmit'] == 1) {
                    echo $_GET['callback'] . '({"status" : "FAILED"})';
                    $this->hard_exit();
                }
                if ($_REQUEST['ajaxform'] == 1) {
                    $this->echo_json_fb('loginresult');
                }
            }
        }

    }


    /**
     * memindex_class::cmd_actpro()
     * 
     * @return
     */
    function cmd_actpro() {
        if (isset($_GET['sec']) && isset($_GET['hash']) && $_GET['sec'] > 0) {
            $kid = (int)$_GET['sec'];
            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $kid . " LIMIT 1");
            $hash = sha1($kid . $k_obj['passwort']);
            if ($hash == $_GET['hash']) {
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET sperren=0 WHERE kid=" . $kid);
                if (REAL_IP == $k_obj['ip']) {
                    $_SESSION['kid'] = $k_obj['kid'];
                    $_SESSION['password'] = $k_obj['passwort'];
                    $this->user_obj->login($k_obj);
                }
                $this->LOGCLASS->addLog('UPDATE', 'profil activation ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
                #HEADER('location:' . SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] . '&section=accactivate');
                $this->msg('Account activated');
                HEADER('location:' . self::get_domain_url());
                exit;
            }
            else {
                $this->LOGCLASS->addLog('FAILURE', 'profil activation fails: ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
                firewall_class::report_hacking('Activating account by mail hacking');
                $this->msge('Account not activated');
                HEADER('location:' . self::get_domain_url());
                exit;
            }
        }
    }

    /**
     * memindex_class::cmd_login()
     * 
     * @return
     */
    function cmd_login() {
        $_SESSION['kid'] = -1;
        $_SESSION['password'] = '';
        $pass = $_REQUEST['pass'];
        $e_object = $this->try_load_user_by_password($_REQUEST['email'], $pass);
        if ($e_object['login_msge'] != "") {
            $this->msge($e_object['login_msge']);
            HEADER("Location: " . SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . "?page=" . $_REQUEST['page'] . "&hash=" . md5($e_object['feedback'] . $_REQUEST['email']) .
                "&feedback=" . $e_object['feedback'] . "&ikey=" . $_POST['email']);
            $this->hard_exit();
        }

        if ($e_object['kid'] > 0 && $_REQUEST['email'] != '') {
            $_SESSION['kid'] = $e_object['kid'];
            $_SESSION['password'] = $e_object['passwort'];
            $this->memberclass->login($e_object);
            $loaded_user = $e_object;

            if (!is_dir(CMS_ROOT . 'file_server/members')) {
                mkdir(CMS_ROOT . 'file_server/members', 0777);
            }
            if (!is_dir(CMS_ROOT . 'file_server/members/' . (int)$e_object['kid'])) {
                mkdir(CMS_ROOT . 'file_server/members/' . (int)$e_object['kid'], 0777);
            }

            // set cookie
            if ($_POST['stayloggedin'] == 1) {
                $this->memberclass->set_login_cookie($e_object);
            }

            $params = array('user' => $e_object);
            $params = exec_evt('OnLoginSuccess', $params);
            $e_object = $params['user'];

            if ($_POST['redirect'] != "" && self::get_domain_url() . $_POST['redirect'] != self::get_domain_url() . $_SERVER['PHPSELF']) {
                #   $query_add['sid_id'] = session_id();
                #  $query_add['msg'] = base64_encode("{MSG_LOGINOK}");
                $query_add['loginok'] = 1;
                $query_add['setcookie'] = (int)$_POST['stayloggedin'];
                $redirect = $_POST['redirect'];
                $url_arr = parse_url($redirect);
                $query = explode("&", $url_arr['query']);
                foreach ($query as $q) {
                    list($key, $value) = explode("=", $q);
                    if ($key == 'msge' || $key == 'msg') {
                        $redirect = preg_replace('/&?' . $key . '=' . $value . '/', '', $redirect);
                    }
                }
                $url = $this->modify_url(self::get_domain_url() . $redirect, $query_add);
                HEADER("Location: " . $url);
            }
            else {
                self::msg('{MSG_LOGINOK}');
                HEADER("Location: " . self::get_domain_url() . "index.php?" . (($_POST['stayloggedin'] == 1) ? 'setcookie=1&' : '') . "&loginok=1");
            }
            exit;
        }
        $this->LOGCLASS->addLog('FORM_FAILURE', 'Customer log in failed: "<a href="index.php?kwort=' . $_POST['email'] . '">' . $_POST['email'] . '</a>"');
        self::msge('{MSG_ERRLOGIN}');
    }

    /**
     * memindex_class::cmd_loggoutssl()
     * 
     * @return
     */
    function cmd_loggoutssl() {
        $MAIN_CLASS = new main_class();
        $MAIN_CLASS->cmd_stdlogout();
    }

    /**
     * memindex_class::cmd_resendact()
     * 
     * @return
     */
    function cmd_resendact() {
        if ($_GET['ikey'] != "" && $_GET['hash'] != "") {
            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . trim($_GET['ikey']) . "' LIMIT 1");
            if (md5($_GET['feedback'] . $k_obj['email']) == $_GET['hash']) {
                $this->LOGCLASS->addLog('SENDMAIL', 'resend activation mail: ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
                send_mail_to(replacer(get_email_template(940), $k_obj['kid'])); // Template "Reaktiviierung"
                HEADER('location:' . SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?page=7&msg=' . base64_encode('{LBL_E-MAILGESENDET}'));
                exit;
            }
        }
    }

    /**
     * memindex_class::gen_xmlsitemap()
     * 
     * @param mixed $params
     * @return
     */
    function gen_xmlsitemap($params) {
        $SM = $this->db->query_first("SELECT * FROM " . TBL_CMS_SITEMAP . " WHERE sm_ident='content' AND sm_active=1");
        if ($SM['sm_active'] == 1) {
            $params = array_merge($params, $SM);
            $result = $this->db->query("SELECT G.*, COUNT(CU.kid) AS KCOUNT,G.id AS GID FROM (" . TBL_CMS_CUST . " CU," . TBL_CMS_CUSTGROUPS . " G)
        LEFT JOIN " . TBL_CMS_CUSTTOGROUP . " K ON (K.gid=G.id) 
        WHERE G.cms_approval=1 AND CU.cms_isindex=1 AND CU.kid=K.kid
        GROUP BY G.id ORDER BY G.groupname");
            while ($row = $this->db->fetch_array_names($result)) {
                $url['url'] = self::get_http_protocol() . '://www.' . FM_DOMAIN . $this->memberclass->genCustomerGroupLink($row['id'], $row['groupname'], $rowl['id']);
                $url['frecvent'] = $SM['sm_changefreq'];
                $url['priority'] = $SM['sm_priority'];
                $params['urls'][] = $url;
            }
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE cms_isindex=1 ORDER BY nachname");
            while ($row = $this->db->fetch_array_names($result)) {
                $label = member_class::gen_link_label($row);
                $url['url'] = self::get_http_protocol() . '://www.' . FM_DOMAIN . $this->memberclass->genCustomerLink($label);
                $url['frecvent'] = $SM['sm_changefreq'];
                $url['priority'] = $SM['sm_priority'];
                $params['urls'][] = $url;
            }
        }
        return (array )$params;
    }

    /**
     * memindex_class::init_register()
     * 
     * @return
     */
    function init_register() {
        # COLLECTIONS LOADER
        $glist = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_COLLECTION . " WHERE approval=1 ORDER BY col_name");
        while ($row = $this->db->fetch_array_names($result)) {
            $group_ids = explode_string_by_ident($row['col_groups']);
            if (count($group_ids) > 0) {
                $chk_obj['col_name'] = $row['col_name'];
                $chk_obj['col_id'] = $row['id'];
                foreach ($group_ids as $gid) {
                    $G_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_RGROUPS . " WHERE id=" . $gid);
                    $COL_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUSTCOLGROUPS . " WHERE kid=" . $this->user_object['kid'] . " AND col_id=" . $row['id']);
                    $col_group_ids = explode_string_by_ident($COL_OBJ['groups']);
                    if ($gid != 1000) {
                        $group = array();
                        $group['checked'] = (in_array($gid, $col_group_ids)) ? 'checked' : '';
                        $group['col_id'] = $row['id'];
                        $group['gid'] = $gid;
                        $group['groupname'] = $G_OBJ['groupname'];
                        $chk_obj['groups'][$group['groupname']] = $group;
                    }
                }
            }
            ksort($chk_obj['groups']);
            $glist[] = $chk_obj;
        }
        $this->smarty->assign('member_collections', $glist);
        $this->set_user_form();
    }

    /**
     * memindex_class::cmd_load_customer_files()
     * 
     * @return void
     */
    function cmd_load_customer_files() {
        $kid = (int)$this->user_object['kid'];
        if (isset($_GET['folder']) && $_GET['folder'] != "") {
            $folder = base64_decode($_GET['folder']);
        }
        else {
            $folder = self::get_path($kid);
        }


        if (!is_dir($folder)) {
            die('hacking');
        }
        $this->MEMINDEX['myfiles'] = self::load_files($kid, $folder);
        $this->parse_to_smarty_fe();
        echo_template_fe('mem_file_list');
    }

    /**
     * memindex_class::set_user_form()
     * 
     * @param mixed $FORM
     * @return
     */
    function set_user_form($FORM = array()) {
        if ($this->user_object['kid'] > 0) {
            $cuob = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . $this->user_object['kid'] . "' LIMIT 1");
            if (is_array($FORM))
                $FORM = array_merge($cuob, $FORM);
            else
                $FORM = $cuob;
            //validiere Bild
            if ($FORM['picture'] != "" && !file_exists(CMS_ROOT . 'images/members/' . $FORM['picture'])) {
                $FORM['picture'] = '';
            }
            unset($FORM['passwort']);

            # load files
            $this->MEMINDEX['myfiles'] = self::load_files($this->user_object['kid']);

            # load document tree
            $this->MEMINDEX['tree'] = $this->load_tree_frontend($this->user_object['kid']);

        }

        if ($FORM['land'] == "") {
            $FORM['land'] = $this->gbl_config['default_country'];
        }
        $lang_opt = build_land_selectbox($FORM['land']);


        unset($anrede_select);
        global $anrede_arr;
        foreach ($anrede_arr as $key => $value)
            $anrede_arr[$key] = pure_translation($value, $this->GBL_LANGID);
        asort($anrede_arr);
        $anrede_select = "";
        foreach ($anrede_arr as $key => $value) {
            $anrede_select .= '<option ' . (($key == $FORM['anrede_sign']) ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
        }

        $FORM['salutselect'] = $anrede_select;

        $FORM['countryselect'] = $lang_opt;
        $FORM['birthday'] = my_date('d.m.Y', $FORM['birthday']);
        $this->smarty->clearAssign('kregform');
        $this->smarty->assign('kregform', $FORM);
        $fileup['idForm'] = false;
        $fileup['canDelete'] = true;
        $fileup['file_desc'] = '{LBL_YOURFOTO}';
        $fileup['force_ext'] = 'jpg';
        $fileup['aktion'] = 'a_fileupload';
        $fileup['confirm'] = gen_java_confirm('{LBL_CONFIRM}');
        global $viewable_ext;
        $fileup['isPicture'] = in_array(GetExt($FORM['picture']), $viewable_ext);
        $fileup['ftarget'] = './images/members/member_' . $this->user_object['kid'] . '.jpg';
        $fileup['picture'] = (($FORM['picture']) ? SSL_PATH_SYSTEM . PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(CMS_ROOT . 'images/members/' . $FORM['picture'],
            $this->gbl_config['mem_thumb_x'], $this->gbl_config['mem_thumb_y'], './' . CACHE, true, 'resize') : '');
        if ($FORM['kid'] > 0) {
            $fileup['del_link'] = SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&aktion=a_delkpic&kid=' . $FORM['kid'] . '&fname=' . base64_encode($FORM['picture']);
            if ($FORM['picture'] != '') {
                $fileup['mouseover'] = "onmouseover=\"showtrail('./images/members/" . $FORM['picture'] . "?a=" . rand(0, 10000) . "','Vorschau','','0.0000','','400',400);\" onmouseout=\"hidetrail();\"";
                list($fileup['picture_dim']['width'], $fileup['picture_dim']['height'], $fileup['picture_dim']['type'], $fileup['picture_dim']['attr']) = getimagesize('./images/members/' .
                    $FORM['picture']);
            }
        }
        $this->smarty->assign('fileup', $fileup);

        return (array )$FORM;
    }

    /**
     * memindex_class::validate_custsave()
     * 
     * @param mixed $FORM
     * @param mixed $FORM_NOTEMPTY
     * @return
     */
    function validate_custsave(&$FORM, &$FORM_NOTEMPTY) {
        $err_arr = array();
        if (count($FORM) > 0) {
            $str_arr = array(
                'strasse',
                'ort',
                'bank',
                'nachname',
                'vorname');
            foreach ($str_arr as $key) {
                if ($FORM[$key] != "") {
                    $FORM[$key] = format_name_string($FORM[$key]);
                }
                if ($FORM_NOTEMPTY[$key] != "") {
                    $FORM_NOTEMPTY[$key] = format_name_string($FORM_NOTEMPTY[$key]);
                }
            }
            if (count($FORM_NOTEMPTY) > 0) {
                foreach ($FORM_NOTEMPTY as $key => $value) {
                    if ($value == '') {
                        $this->add_smarty_err($err_arr, $key, '{LBL_MISSING}');
                    }
                    $FORM[$key] = $value;
                }
            }
            if ($this->gbl_config['newsletter_disable_unreg'] == 0) {
                $FORM['mailactive'] = (int)$FORM['mailactive'];
            }
            else
                unset($FORM['mailactive']);
            if (isset($FORM['email'])) {
                $FORM['email'] = strtolower($FORM['email']);
            }
            if (isset($FORM['birthday'])) {
                $FORM['birthday'] = format_date_to_sql_date($FORM['birthday']);
            }


            # check password
            if ($_POST['passwort_free'] != 1) {
                if ($_POST['cmd'] == "insert") {
                    if ($FORM['passwort'] == "") {
                        $this->add_smarty_err($err_arr, 'passwort', '{ERR_PASSWORT}');
                        self::msge('{ERR_PASSWORT}');
                    }

                    if ($FORM['passwort'] != "") {
                        $valid = self::is_strong_password($FORM['passwort']);
                        if (!$valid) {
                            $this->add_smarty_err($err_arr, 'passwort', 'Password not strong enough');
                            self::msge('Passwort ist nicht stark genug');
                        }
                    }
                }
            }

            if (!empty($FORM['anrede_sign'])) {
                $FORM['anrede'] = get_customer_salutation($FORM['anrede_sign']);
                $FORM['geschlecht'] = get_customer_sex($FORM['anrede_sign']);
            }
            $FORM['land'] = ($FORM['land'] == 0) ? 1 : $FORM['land'];

            # CUSTOM FIELDS
            global $GBL_LANGID;
            require_once (CMS_ROOT . 'admin/inc/cfield.class.php');
            $CFIELD = new cfield_class();
            $err_arr = $CFIELD->validateCFieldsInput($FORM, $GBL_LANGID, $err_arr, true);
            $FORM = self::trim_array($FORM);
        }

        if ($_POST['ftarget'] != "" && $_FILES['datei']['name'] != "") {
            $ext_file = strtolower(strrchr($_FILES['datei']['name'], '.'));
            $ext_target = strtolower(strrchr($_POST['ftarget'], '.'));
            $all_ext = array($ext_target, 'jpeg');
            if (!in_array($ext_file, $all_ext)) {
                self::msge('Unerlaubter Dateityp.');
            }
            if (!validate_upload_file($_FILES['datei'], true, true)) {
                self::msge($_SESSION['upload_msge']);
            }
            if (!is_dir(CMS_ROOT . 'images/members'))
                mkdir(CMS_ROOT . 'images/members', 0755);
        }
        $params = array('FORM' => $FORM, 'FORM_NOTEMPTY' => $FORM_NOTEMPTY);
        $params = exec_evt('OnValidateCustomer', $params);
        return (array )$err_arr;
    }

    /**
     * memindex_class::cmd_update()
     * 
     * @return
     */
    function cmd_update() {
        $FORM = $_POST['FORM'];
        $FORM_NOTEMPTY = $_POST['FORM_NOTEMPTY'];
        $err_arr = $this->validate_custsave($FORM, $FORM_NOTEMPTY);
        if (count($err_arr) == 0 && $this->has_errors() == false) {
            if ($FORM['passwort'] != "")
                $FORM['passwort'] = encrypt_password($FORM['passwort']);
            else
                unset($FORM['passwort']);
            if ($_FILES['datei']['name'] != "") {
                $ftarget = CMS_ROOT . 'images/members/' . self::format_file_name('member-' . $this->user_object['nachname'] . '-' . time() . '-' . $this->user_object['kid'] .
                    '.jpg');
                delete_file($ftarget);
                delete_file(CMS_ROOT . 'images/members/' . $this->user_object['picture']);
                update_table(TBL_CMS_CUST, 'kid', $this->user_object['kid'], array('picture' => ''));
                move_uploaded_file($_FILES['datei']['tmp_name'], $ftarget);
                chmod($ftarget, 0755);
                graphic_class::resize_picture_imageick($ftarget, $ftarget, 3000, 3000);
                $FORM['picture'] = basename($ftarget);
                list($width, $height, $type, $atrr) = getimagesize($ftarget);
                $this->LOGCLASS->addLog('UPLOAD', 'foto profil update ' . basename($ftarget) . ', ' . $this->user_object['nachname'] . ', ' . $this->user_object['kid'] . ',' .
                    $_FILES['datei']['name'] . $width . 'x' . $height . ',' . $type);
            }
            require_once (CMS_ROOT . 'admin/inc/cfield.class.php');
            $CFIELD = new cfield_class();
            $FORM = $CFIELD->format_for_saveing($FORM);
            update_table(TBL_CMS_CUST, 'kid', $this->user_object['kid'], $FORM);
            $this->user_obj->setMemGroups($_POST['GROUPS'], $_POST['MEMBERGROUPSCOL'], false, true);

            # rebuild page url index
            $this->rebuild_page_index($this->user_object['kid']);

            $this->LOGCLASS->addLog('UPDATE', 'profil update ' . $this->user_object['nachname'] . ', ' . $this->user_object['kid']);
            $this->msg('{MSG_PROFILGESPEICHERT}');
        }
        $this->smarty->assign('kregform_err', $err_arr);
        $this->set_user_form($FORM);
        $params = array(
            'FORM' => $FORM,
            'FORM_NOTEMPTY' => $FORM_NOTEMPTY,
            'kid' => $this->user_object['kid']);
        $params = exec_evt('OnSaveCustomer', $params);
        HEADER('location:' . self::get_local_path() . 'index.php?page=' . $_REQUEST['page'] . '&profilerr=' . (($has_error == true) ? 1 : 0));
        exit;
    }

    /**
     * memindex_class::cmd_check_username()
     * 
     * @return void
     */
    function cmd_check_username() {
        $username = trim($_GET['username']);
        $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " 
            WHERE username='" . $username . "'
            " . ((isset($_GET['kid']) && $_GET['kid'] > 0) ? " AND kid<>" . (int)$_GET['kid'] : "") . "
            ");
        $arr = array(
            'valid' => ($k_obj['kid'] == 0 && strlen($username) >= 4) ? 1 : 0,
            'username' => trim($_GET['username']),
            'length' => strlen($username),
            'tooshort' => (strlen($username) < 4) ? 1 : 0,
            'exists' => ($k_obj['kid'] > 0) ? 1 : 0);
        echo json_encode($arr);
        $this->hard_exit();
    }

    /**
     * memindex_class::cmd_insert()
     * 
     * @return
     */
    function cmd_insert() {
        $FORM = $_POST['FORM'];

        $FORM_NOTEMPTY = $_POST['FORM_NOTEMPTY'];
        $err_arr = $this->validate_custsave($FORM, $FORM_NOTEMPTY);

        $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . $FORM['email'] . "'");

        # Validierung fuer nicht oeffentliche Email
        if ($this->gbl_config['login_mode'] == 'NONE_PUBLIC_EMAIL') {
            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email_notpublic='" . $FORM['email'] . "'");
            if (!validate_email_input($FORM['email_notpublic'])) {
                $this->add_smarty_err($err_arr, 'email_notpublic', '{ERR_EMAIL}');
                self::msge('{ERR_EMAIL}');
            }
        }
        # Validierung username
        if ($this->gbl_config['login_mode'] == 'USERNAME') {
            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE username='" . $FORM['username'] . "'");
            if (strlen($FORM['username']) < 4) {
                $this->add_smarty_err($err_arr, 'username', '{ERR_USERNAME}');
                self::msge('{ERR_USERNAME}');
            }
        }

        # Schon vorhanden?
        if ($k_obj['kid'] > 0 && (validate_email_input($FORM['email']) || validate_email_input($FORM['email_notpublic']))) {
            $this->add_smarty_err($err_arr, 'email', '{ERR_EMAIL_VORHANDEN}');
            self::msge('{ERR_EMAIL_VORHANDEN}');
        }
        if (!validate_email_input($FORM['email'])) {
            $this->add_smarty_err($err_arr, 'email', '{ERR_EMAIL}');
            self::msge('{ERR_EMAIL}');
        }

        # CAPCHA
        if ($this->gbl_config['capcha_active'] == 1) {
            if (isset($_SESSION['captcha_spam']) and $_POST["securecode"] == $_SESSION['captcha_spam']) {
                unset($_SESSION['captcha_spam']);
            }
            else {
                $this->add_smarty_err($err_arr, 'securecode', '{ERR_SECODE}');
                self::msge('{ERR_SECODE}');
            }

        }

        # Vorname und Nachname
        if (strtolower($FORM['vorname']) == strtolower($FORM['nachname'])) {
            $this->add_smarty_err($err_arr, 'nachname', '{ERR_NAMEEQUAL}');
            self::msge('{ERR_NAMEEQUAL}');
        }

        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            self::msge('invalid token');
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP . ', ' . $_SERVER['REQUEST_URI']);
        }

        if (count($err_arr) == 0 && $this->has_errors() == false) {
            $FORM['monat'] = date("m");
            $FORM['jahr'] = date("Y");
            $FORM['tag'] = date("d");
            $FORM['datum'] = date('Y-m-d');
            $FORM['is_cms'] = 1;
            $FORM['ip'] = REAL_IP;

            require_once (CMS_ROOT . 'admin/inc/cfield.class.php');
            $CFIELD = new cfield_class();
            $FORM = $CFIELD->format_for_saveing($FORM);

            $kid = insert_table(TBL_CMS_CUST, $FORM);
            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid='" . $kid . "'");

            if ($_FILES['datei']['name'] != "") {
                $ftarget = CMS_ROOT . 'images/members/member_' . $k_obj['kid'] . '.jpg';
                delete_file($ftarget);
                move_uploaded_file($_FILES['datei']['tmp_name'], $ftarget);
                chmod($ftarget, 0755);
                graphic_class::resize_picture_imageick($ftarget, $ftarget, 3000, 3000);
                $picture = basename($ftarget);
                list($width, $height, $type, $atrr) = getimagesize($ftarget);
                $this->LOGCLASS->addLog('UPLOAD', 'foto profil insert ' . basename($ftarget) . ', ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ',' . $_FILES['datei']['name'] .
                    $width . 'x' . $height . ',' . $type);
            }
            else
                $picture = '';
            $k_obj['passwort'] = encrypt_password($k_obj['passwort']);
            $k_obj['sessionid'] = session_id();
            if ($this->gbl_config['mem_needactivate'] == 1) {
                unset($_SESSION['kid']);
                unset($_SESSION['password']);
                $k_obj['sperren'] = 1;
            }
            else {
                $_SESSION['kid'] = $k_obj['kid'];
                $_SESSION['password'] = $k_obj['passwort'];
            }

            $k_obj['picture'] = $picture;
            if ($this->gbl_config['login_mode'] != 'USERNAME') {
                $k_obj['username'] = ucfirst($FORM['vorname']) . '_' . ucfirst(substr($FORM['nachname'], 0, 1));
            }
            $this->real_escape($k_obj);
            update_table(TBL_CMS_CUST, 'kid', $k_obj['kid'], $k_obj);
            $this->user_obj->setKid($kid);
            $this->user_obj->setMemGroups($_POST['GROUPS'], $_POST['MEMBERGROUPSCOL'], false, true);
            $this->user_obj->addMemberToGroup(1100);
            $this->LOGCLASS->addLog('INSERT', 'new registration ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);

            # send register mail to customer
            $this->LOGCLASS->addLog('SENDMAIL', 'send registration mail: ' . $k_obj['nachname'] . ', ' . $k_obj['kid'] . ', ' . $k_obj['email']);
            send_mail_to(replacer(get_email_template(990), $k_obj['kid']));

            # Admin. Email aufbauen
            $this->smarty_arr = array('user' => $FORM);
            send_admin_mail(910, $this->smarty_arr);

            # rebuild page url index
            $this->rebuild_page_index($kid);

            # Modul exec
            exec_evt('OnRegisterCustomer', $k_obj);
            self::msg('{LBL_REGOK}');
            HEADER('location:' . SSL_PATH_SYSTEM . PATH_CMS . 'index.php?tl=-1&page=' . START_PAGE);
            exit;
        }

        $this->set_user_form($FORM);
        $this->smarty->assign('kregform_err', $err_arr);
    }

    /**
     * memindex_class::cmd_a_delkpic()
     * 
     * @return
     */
    function cmd_a_delkpic() {
        if ($this->user_object['kid'] > 0) {
            if (delete_file(CMS_ROOT . 'images/members/' . $this->user_object['picture'])) {
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET picture='' WHERE kid='" . $this->user_object['kid'] . "' LIMIT 1");
                self::msge('{LBL_DELETED}');
            }
        }
        header('location: ' . SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page']);
        exit;
    }

    /**
     * memindex_class::cmd_user_file_download()
     * 
     * @return void
     */
    function cmd_user_file_download() {
        if ($this->user_object['kid'] > 0) {
            $folder = self::get_path($this->user_object['kid']);
            if (isset($_GET['folder'])) {
                $folder = base64_decode($_GET['folder']);
            }
            if (!is_dir($folder)) {
                firewall_class::report_hacking('Invalid secure file download ' . $this->user_object['kid']);
                die('hacking');
            }
            $arr = self::load_files($this->user_object['kid'], $folder);
            foreach ($arr as $key => $row) {
                if ($row['hash'] == $_GET['hash']) {
                    self::direct_download($row['file_to_root']);
                }
            }
            firewall_class::report_hacking('Invalid secure file download ' . $this->user_object['kid']);
        }
        else {
            firewall_class::report_hacking('Invalid secure file download. User not logged in. ');
        }
    }

    /**
     * memindex_class::cmd_send_pass_link()
     * 
     * @return void
     */
    function cmd_send_pass_link() {
        $FORM = (array )$_POST['FORM'];
        if (!validate_email_input($FORM['tschapura'])) {
            self::msge('{ERR_EMAIL}');
        }
        $customer = dao_class::get_data_first(TBL_CMS_CUST, array('email' => $FORM['tschapura']));
        if ((int)$customer['kid'] == 0) {
            self::msge('Account nicht gefunden.');
        }
        if (self::has_errors() == false) {
            self::send_newpassword_link($customer, $_POST['page']);
            self::msg('Sie haben eine E-Mail an "' . $FORM['tschapura'] . '" erhalten.');
        }
        HEADER("location: " . self::get_domain_url());
        $this->hard_exit();
    }


    /**
     * memindex_class::cmd_show_setnewpass()
     * 
     * @return void
     */
    function cmd_show_setnewpass() {
        if (!isset($_GET['set'])) {
            $kid = (int)$_GET['kid'];
            $_SESSION['newpasskid'] = (int)$kid;
            $customer = dao_class::get_data_first(TBL_CMS_CUST, array('kid' => $kid));
            $hash = sha1(implode('|', $customer));
            if ($hash != $_GET['hash']) {
                self::msge('Invalid hash ' . $hash);
                firewall_class::report_hacking('Hacking show new pass form');
                unset($_GET['kid']);
                unset($_SESSION['newpasskid']);
                HEADER("location: " . self::get_domain_url());
                $this->hard_exit();
            }

            HEADER("location: " . self::get_domain_url() . 'index.php?page=' . $_GET['page'] . '&cmd=show_setnewpass&set=1');
            $this->hard_exit();
        }
    }

    /**
     * memindex_class::cmd_set_password()
     * 
     * @return void
     */
    function cmd_set_password() {
        $password = $_POST['pass'];
        $passwordwdh = $_POST['passwdh'];

        $kid = (int)$_SESSION['newpasskid'];
        $customer = dao_class::get_data_first(TBL_CMS_CUST, array('kid' => $kid));
        if ($kid == 0) {
            self::msge('Invalid access kid');
            firewall_class::report_hacking('invalid kid on set passwort');
        }
        if ((int)$customer['kid'] == 0) {
            self::msge('Invalid access customer');
            firewall_class::report_hacking('Hacking set password invalid customer');
        }
        if ($password != $passwordwdh) {
            self::msge('Passwörter stimmen nicht überein.');
        }
        $valid = self::is_strong_password($password);
        if (!$valid) {
            self::msge('Passwort ist nicht stark genug');
        }

        if (self::has_errors() == false) {
            $arr = array('passwort' => encrypt_password($password));
            update_table(TBL_CMS_CUST, 'kid', $customer['kid'], $arr);
            $params = array('user' => $customer);
            $params = exec_evt('OnSetNewPassword', $params);
            $this->LOGCLASS->addLog('UPDATE', 'new password set ' . $customer['nachname'] . ', ' . $customer['kid'] . ', ' . $customer['email']);
            unset($_SESSION['newpasskid']);
            self::msg('Passwort wurde neu gesetzt');
            HEADER("location: " . self::get_domain_url() . 'index.php?page=' . $_GET['page'] . '&cmd=show_setpok');
        }
        else {
            #HEADER('location:' . self::gen_setpass_link($customer, (int)$_POST['page']));
            HEADER("location: " . self::get_domain_url() . 'index.php?page=' . $_GET['page'] . '&cmd=show_setnewpass&set=1');
        }
        $this->hard_exit();
    }

}
