<?php




/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



class webex_class extends keimeno_class {

    var $folders = array();
    var $menu = array();

    function __construct() {
        parent::__construct();
    }

    function add_folders($folders) {
        global $ADMINOBJ;
        foreach ($folders as $label => $f) {
            $this->folders[md5($f)] = $f;
            $this->menu[$label] = 'working=' . md5($f);
        }
        $this->menu['Webspace Analyzer'] = 'aktion=analyze_dirs';
        $ADMINOBJ->set_top_menu($this->menu);
    }

    function set_folder($GET) {
        $md5_folder = strval(trim($GET['working']));
        if (!empty($md5_folder)) {
            $_SESSION['WEBEXSET']['current_folder'] = $this->folders[$md5_folder];
        }
        else {
            $_SESSION['WEBEXSET']['current_folder'] = array_shift($this->folders);
        }
        $this->smarty->assign('WEBEXSET', $_SESSION['WEBEXSET']);
        return $_SESSION['WEBEXSET']['current_folder'];
    }

    function analyze_dirs($dir, $leer = '', $ebene = 0) {
        $leer .= '&nbsp;&nbsp;&nbsp;';
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            $k = 0;
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    $k++;
                    if (!is_dir($dir . $file)) {
                        if ($file == 'php.ini' || strstr($file, '.bak')) {
                            @unlink($dir . $file);
                            continue;
                        }
                        if (strstr($file, '.php')) {
                            continue;
                        }
                        if ($k > 0 && $headerset == FALSE) {
                            $html_tree .= '<tr class="trsubheader3"><td colspan="3">' . str_replace(SHOP_ROOT, '', $dir) . '</td></tr>';
                            $headerset = TRUE;
                        }
                        $fs = filesize($dir . $file);
                        $this->total_fs += $fs;
                        $this->total_file_count++;
                        $this->total_fs_dir[md5($dir)] = array('dir' => str_replace(SHOP_ROOT, '', $dir), 'total_fs' => $this->total_fs_dir[md5($dir)]['total_fs'] + $fs);
                        $this->file_extentions[strtolower(GetExt($file))] += $fs;
                        $html_tree .= '<tr>
					<td></td>
					<td>' . $file . '</td>
					<td class="text-right"> ' . human_file_size($fs) . '</td>
					</tr>';
                    }
                    else {
                        if (!is_array($this->total_fs_dir[md5($dir . $file)])) {
                            $this->total_fs_dir[md5($dir . $file)] = array('dir' => str_replace(SHOP_ROOT, '', $dir . $file), 'total_fs' => 0);
                        }
                        if ($k > 0 && $headerset == FALSE) {
                            $html_tree .= '<tr class="trsubheader3"><td colspan="3">' . str_replace(SHOP_ROOT, '', $dir . $file) . '</td></tr>';
                        }
                        $html_tree .= $this->analyze_dirs($dir . $file, $leer, ($eben + 1));
                    }
                }
            }
            closedir($openDir);
        }
        return $html_tree;
    }

    function showtree($_XML, $rights, $leer, $ebene) {
        $leer .= '&nbsp;&nbsp;&nbsp;';
        foreach ($_XML as $xml) {
            if ($xml) {
                $mod_valid = true;
                if ($xml->module != "") {
                    $config_name = (string )$xml->module;
                    $mod_valid = $this->gbl_config[$config_name] == 1;
                }
                if ((in_array($xml->id, $_SESSION['mids']) && $mod_valid === TRUE) || ($this->gbl_config['mod_ismaster'] == 1)) {
                    $html_tree .= '<tr class="' . gen_row() . '">';
                    if ($ebene == 0)
                        $html_tree .= '<th>';
                    else
                        $html_tree .= '<td>';
                    if ($ebene == 0)
                        $html_tree .= '</th><th>';
                    else
                        $html_tree .= '</td><td>';
                    $html_tree .= $leer . '<input value="' . $xml->id . '" ' . ((in_array($xml->id, $rights) || !sizeof($rights)) ? 'checked' : '') .
                        ' type="checkbox" name="menue_id[' . $xml->id . ']">' . utf8_decode($xml->Titel);
                    if ($ebene == 0)
                        $html_tree .= '</th>';
                    else
                        $html_tree .= '</td>';
                    $html_tree .= "</tr>\n";
                    if ($xml->Menue)
                        $html_tree .= $this->showtree($xml->Menue, $rights, $leer, ($eben + 1));
                }
                unset($ok);
            }
        }
        return $html_tree;
    }

    function __destruct() {

    }
}

?>