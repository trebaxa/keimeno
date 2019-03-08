<?php

/**
 * @package    structure
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class structure_admin_class extends keimeno_class {

    protected $STRUCTURE = array();

    /**
     * structure_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * structure_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $list = array(
            'str_one' => '1',
            'str_two' => '2',
            'str_three' => '3',
            'str_four' => '4',
            'str_five' => '5',
            'str_six' => '6');
        return (array )$list;
    }

    /**
     * structure_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        foreach ($params['FORM']['text'] as $key => $val) {
            $cont .= '<' . $params['FORM']['container'] . ' class="' . $params['FORM']['cssclass'] . ' structcol' . count($params['FORM']['text']) . '">' . $val . '</' . $params['FORM']['container'] .
                '>';
            $params['FORM']['text'][$key] = stripslashes($val);
        }
        $upt = array(
            'tm_content' => stripslashes($cont) . '<div class="clearer"></div>',
            'tm_pluginfo' => strip_tags($cont),
            'tm_plugform' => serialize($params['FORM']));
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }


}

?>