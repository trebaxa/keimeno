<?PHP
/**
 * @package    ekomi
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

class ekomi_class extends ekomi_master_class
{

    protected $client = null;
    var $EKOMI = array();

    /**
     * ekomi_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->
            gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        $this->client = new SoapClient("http://api.ekomi.de/v2/wsdl");
    }

    /**
     * ekomi_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        if ($this->smarty->getTemplateVars('EKOMI') != null) {
            $this->EKOMI = array_merge($this->smarty->getTemplateVars('EKOMI'), $this->
                EKOMI);
            $this->smarty->clearAssign('EKOMI');
        }
        $this->smarty->assign('EKOMI', $this->EKOMI);
    }

    /**
     * ekomi_class::cmd_getekomiitemsax()
     * 
     * @return
     */
    function cmd_getekomiitemsax()
    {
        $arr = $this->get_last_bewertungen();
        echo json_encode($arr);
        $this->hard_exit();
    }

    /**
     * ekomi_class::cmd_load_items()
     * 
     * @return
     */
    function cmd_load_items()
    {
        $this->EKOMI['items'] = $this->get_last_bewertungen();
    }

    /**
     * ekomi_class::parse_ekomi()
     * 
     * @param mixed $params
     * @return
     */
    function parse_ekomi($params)
    {
        $html = $params['html'];
        $langid = $params['langid'];
        if (strstr($html, '{TMPL_EKOMI_')) {
            preg_match_all("={TMPL_EKOMI_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES .
                    " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->EKOMI['items'] = $this->get_last_bewertungen();
                $this->EKOMI['items'] = $this->sort_multi_array($this->EKOMI['items'],
                    'create_time', SORT_DESC, SORT_NUMERIC);
                $this->EKOMI['items'] = $this->get_part_of_array($this->EKOMI['items'], 0, (int)
                    $PLUGIN_OPT['limit']);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=EKOMI value=$TMPL_EKOMI_' .
                    $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] . '.tpl" %>', $html);
                $this->smarty->assign('TMPL_EKOMI_' . $cont_matrix_id, $this->EKOMI);
            }
        }
        $params['html'] = $html;

        return $params;
    }

}

?>