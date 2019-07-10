<?PHP

class newssub_admin_class extends keimeno_class {


    /**
     * newssub_admin_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * newssub_admin_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('NEWSSUB') != null) {
            $this->NEWSSUB = array_merge($this->smarty->getTemplateVars('NEWSSUB'), $this->NEWSSUB);
            $this->smarty->clearAssign('NEWSSUB');
        }
        $this->smarty->assign('NEWSSUB', $this->NEWSSUB);
    }

    /**
     * newssub_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE modident='newssub' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * newssub_admin_class::load_groups_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_groups_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_RGROUPS . " WHERE 1 ORDER BY groupname");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }


    /**
     * newssub_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array(
            'tm_modident' => 'newssub',
            'tm_content' => '{TMPL_NEWSSUBINLAY_' . $cont_matrix_id . '}<% assign var=cont_matrix_id value="' . $cont_matrix_id . '" %><%include file="' . $R['tpl_name'] .
                '.tpl"%>',
            'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }
}
