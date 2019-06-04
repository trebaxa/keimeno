<?php

/**
 * @package    flextemp
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class flextemp_class extends flextemp_master_class {

    var $FLEXTEMP = array();
    protected static $mobile_detect = null;

    /**
     * flextemp_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
        static::$mobile_detect = new Mobile_Detect();
    }

    /**
     * flextemp_class::on_java_compile()
     * 
     * @param mixed $params
     * @return
     */
    function on_java_compile($params) {
        $result = $this->db->query("SELECT * FROM  " . TBL_FLXTPL . " WHERE t_java!=''");
        while ($row = $this->db->fetch_array_names($result)) {
            $params['js_content'] .= $row['t_java'] . PHP_EOL;
        }
        return $params;
    }

    /**
     * flextemp_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('FLEXTEMP') != null) {
            $this->FLEXTEMP = array_merge($this->smarty->getTemplateVars('FLEXTEMP'), $this->FLEXTEMP);
            $this->smarty->clearAssign('FLEXTEMP');
        }
        $this->smarty->assign('FLEXTEMP', $this->FLEXTEMP);
    }


    /**
     * flextemp_class::set_template()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function set_template($PLUGIN_OPT) {
        $TPL = $this->load_html_tpl($PLUGIN_OPT['flxtpl']);
        return $TPL['t_tpl'];
    }

    /**
     * flextemp_class::get_dataset()
     * 
     * @param mixed $PLUGIN_OPT
     * @param mixed $flxtpl
     * @param mixed $cont_matrix_id
     * @return
     */
    function get_dataset($PLUGIN_OPT, $flxtpl, $cont_matrix_id) {
        $arr = array();
        $dataset = $this->load_dataset($flxtpl['f_table'], $cont_matrix_id);
        foreach ($dataset as $kkey => $row) {
            foreach ($row as $column => $value) {
                $thumb = $vident = "";
                $exists = true;
                if (substr($column, -4) == '_img') {
                    $col_opt = $flxtpl['coldef'][$column]['v_opt']['img'];
                    $img = ($value != "") ? './file_data/flextemp/images/' . $value : './images/opt_no_pic.jpg';
                    $gravity = $col_opt['foto_gravity'];
                    if (isset($row['ds_settings']['foto']) && $row['ds_settings']['foto']['foto_gravity'] != 'default') {
                        $gravity = $row['ds_settings']['foto']['foto_gravity'];
                    }
                    if ($col_opt['foto_resize'] != 'none' && self::get_ext($img) != 'svg') {
                        $col_opt = self::get_optimal_size($col_opt);
                        $thumb = thumbit_fe($img, $col_opt['foto_width'], $col_opt['foto_height'], $col_opt['foto_resize'], $gravity);
                    }
                    else {
                        $thumb = str_replace('./', PATH_CMS, $img);
                    }

                    $exists = (is_file(CMS_ROOT . 'file_data/flextemp/images/' . $value) && file_exists(CMS_ROOT . 'file_data/flextemp/images/' . $value));
                    $value = ($value != "") ? PATH_CMS . 'file_data/flextemp/images/' . $value : PATH_CMS . 'images/opt_no_pic.jpg';

                }

                if (substr($column, -5) == '_file') {
                    $col_opt = $flxtpl['coldef'][$column]['v_opt']['file'];
                    $exists = (is_file(CMS_ROOT . 'file_data/flextemp/files/' . $value) && file_exists(CMS_ROOT . 'file_data/flextemp/files/' . $value));
                    $value = ($exists == true) ? PATH_CMS . 'file_data/flextemp/files/' . $value : '';
                }

                if (substr($column, -5) == '_seli') {
                    list($vident, $value) = explode('|', $value);
                }

                if (!in_array($column, $this->forbidden_column_arr)) {
                    $arr[$row['g_ident']][$kkey][$column] = array(
                        'column' => $column,
                        'value' => $value,
                        'hash' => md5($value . $vident . $column),
                        'vident' => $vident,
                        'thumb' => $thumb,
                        'exists' => $exists,
                        'def' => (isset($flxtpl['coldef'][$column])) ? $flxtpl['coldef'][$column] : '',
                        );
                }
            }
        }

        return $arr;
    }

    /**
     * flextemp_class::get_optimal_size()
     * 
     * @param mixed $img_opt
     * @return
     */
    protected static function get_optimal_size($img_opt) {
        if (self::get_config_value('gra_mobile_detect') == 1) {
            $mobile_width = 375;
            $mobile_height = 667;
            $tablet_width = 768;
            $tablet_height = 667;
            if (static::$mobile_detect->isMobile()) {
                if ($img_opt['foto_width'] > $mobile_width) {
                    $img_opt['foto_width'] = $mobile_width;
                }
                if ($img_opt['foto_height'] > $mobile_height) {
                    $img_opt['foto_height'] = $mobile_height;
                }
            }
            elseif (static::$mobile_detect->isTablet()) {
                if ($img_opt['foto_width'] > $tablet_width) {
                    $img_opt['foto_width'] = $tablet_width;
                }
                if ($img_opt['foto_height'] > $tablet_height) {
                    $img_opt['foto_height'] = $tablet_height;
                }
            }
        }
        return $img_opt;
    }

    /**
     * flextemp_class::parse_flxt()
     * 
     * @param mixed $params
     * @return
     */
    function parse_flxt($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        $RM = new resource_class();
        if (strstr($html, '{TMPL_FLXTPL_')) {
            preg_match_all("={TMPL_FLXTPL_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $flxtpl = $this->load_flex_tpl($PLUGIN_OPT['flxtid']);
                $flxtpl['tpl'] = $this->load_flexvars_table($PLUGIN_OPT['flxtid']);
                $flxtpl['coldef'] = $this->load_dataset_vars_table($PLUGIN_OPT['flxtid']);
                $flxtpl['dataset'] = $this->get_dataset($PLUGIN_OPT, $flxtpl, $cont_matrix_id);

                $flexvarsdata = $this->load_flexvars_for_plugin($cont_matrix_id);
                foreach ($flxtpl['tpl'] as $ident => $row) {
                    if ($row['v_type'] == 'img') {
                        $flxtpl['var'][$row['v_varname']] = "";
                        if ($flexvarsdata[$row['id']]['v_value'] != "") {
                            $flxtpl['var']['options'] = $this->get_img_options($flxtpl['tpl'], $row['v_varname']);
                            $flxtpl['var'][$row['v_varname']] = PATH_CMS . 'file_data/flextemp/images/' . $flexvarsdata[$row['id']]['v_value'];

                            # crop if needed
                            if (isset($flxtpl['var']['options']['img']) && $flxtpl['var']['options']['img']['foto_resize'] != 'none') {
                                $img_opt = $flxtpl['var']['options']['img'];
                                $gravity = $img_opt['foto_gravity'];
                                if (isset($flexvarsdata[$row['id']]['v_settings']['foto']) && $flexvarsdata[$row['id']]['v_settings']['foto']['foto_gravity'] != 'default') {
                                    $gravity = $flexvarsdata[$row['id']]['v_settings']['foto']['foto_gravity'];
                                }
                                if (self::get_ext($flexvarsdata[$row['id']]['v_value']) != 'svg') {
                                    $img_opt = self::get_optimal_size($img_opt);
                                    $flxtpl['var'][$row['v_varname']] = thumbit_fe('./file_data/flextemp/images/' . $flexvarsdata[$row['id']]['v_value'], $img_opt['foto_width'], $img_opt['foto_height'],
                                        $img_opt['foto_resize'], $gravity);
                                }
                            }
                        }
                    }
                    elseif ($row['v_type'] == 'file') {
                        $flxtpl['var'][$row['v_varname']] = (is_file(CMS_ROOT . 'file_data/flextemp/files/' . $flexvarsdata[$row['id']]['v_value'])) ? PATH_CMS .
                            'file_data/flextemp/files/' . $flexvarsdata[$row['id']]['v_value'] : '';
                    }
                    elseif ($row['v_type'] == 'resrc') {
                        $arr_resrc = $RM->load_resrc_for_compile($row['v_resrc_id'], $flexvarsdata[$row['id']]['v_settings']);
                        $flxtpl['var'][$row['v_varname']] = $arr_resrc['dataset'];
                        $flxtpl['paging'] = $arr_resrc['paging'];
                    }
                    else {
                        $flxtpl['var'][$row['v_varname']] = $flexvarsdata[$row['id']]['v_value'];
                    }

                }
                $this->smarty->assign('TMPL_FLXTPL_' . $cont_matrix_id, $flxtpl);
                if ($PLUGIN_OPT['flxtpl'] != "") {
                    $html = str_replace($tpl_tag[0][$key], '<% assign var=flxt value=$TMPL_FLXTPL_' . $cont_matrix_id . ' %>' . $this->set_template($PLUGIN_OPT), $html);
                }
                else {
                    $html = str_replace($tpl_tag[0][$key], '', $html);
                }
            }
        }
        $params['html'] = $html;
        return $params;
    }
}
