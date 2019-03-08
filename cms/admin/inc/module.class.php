<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class module_class extends keimeno_class {

    var $MODULE_ROOT = "";
    var $filelist = array();
    var $viewable_ext = array();

    function __construct($MODULE_ROOT) {
        parent::__construct();
        $this->MODULE_ROOT = $MODULE_ROOT;
        $this->TCR = new kcontrol_class($this);
        $this->load_mod_files();
    }

    function cmd_modreload() {
        $this->load_mod_files();
        kf::echo_template('modstyle.filelist');
    }

    function load_mod_files() {
        global $viewable_ext, $allowed_ext;
        foreach ($viewable_ext as $key => $e)
            $this->viewable_ext[] = strtolower(str_replace('.', '', $e));
        foreach ($allowed_ext as $key => $e)
            $this->allowed_ext[] = strtolower(str_replace('.', '', $e));
        $this->filelist = array();
        $editable_files = array(
            'css',
            'js',
            'htm',
            'html');
        $this->fetch_files($this->MODULE_ROOT, $editable_files);
        $this->MODUL = array();
        $this->MODUL['alllist'] = array();
        $this->MODUL['filelist'] = $this->filelist;
        $this->MODUL['alllist'] = array_merge($this->MODUL['alllist'], $this->filelist);
        $this->filelist = array();
        $this->fetch_files($this->MODULE_ROOT, $this->viewable_ext);
        $this->MODUL['imglist'] = $this->filelist;
        $this->MODUL['alllist'] = array_merge($this->MODUL['alllist'], $this->filelist);
        $this->filelist = array();
        $this->fetch_files($this->MODULE_ROOT, $this->allowed_ext, $this->viewable_ext);
        $this->MODUL['restfiles'] = $this->filelist;
        $this->MODUL['alllist'] = array_merge($this->MODUL['alllist'], $this->filelist);
        $this->MODUL['MODULE_ROOT'] = str_replace(CMS_ROOT, '', $this->MODULE_ROOT);
        $this->smarty->assign('MODUL', $this->MODUL);
    }


    function fetch_files($dir, $ext, $exclude = array()) {
        global $GRAPHIC_FUNC;
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir . $file) && (strstr($dir, '/template') || strstr($dir, '/images') || strstr($dir, '/css'))) {
                        $this->MODUL['dirlist'][md5($dir)] = array('dir64' => base64_encode(str_replace($this->MODULE_ROOT, '', $dir)), 'dir' => str_replace($this->MODULE_ROOT, '', $dir));
                        $RetVal = explode('.', $file);
                        $file_extention = strtolower($RetVal[count($RetVal) - 1]);

                        if (in_array($file_extention, $ext) && !in_array($file_extention, $exclude)) {

                            $img_url = str_replace(CMS_ROOT, '', $this->MODULE_ROOT) . str_replace($this->MODULE_ROOT, '', $dir) . $file;

                            $this->filelist[$file_extention][md5($dir . $file)] = array(
                                'file' => str_replace($this->MODULE_ROOT, '', $dir) . $file,
                                'img_url' => $img_url,
                                'size' => human_file_size(filesize($dir . $file)),
                                'dim' => ((in_array($file_extention, array(
                                    'jpg',
                                    'png',
                                    'gif',
                                    'jpeg'))) ? getimagesize($dir . $file) : array()),
                                'ext' => $file_extention,
                                'isimg' => in_array($file_extention, $this->viewable_ext),
                                'thumb' => ((in_array($file_extention, $this->viewable_ext)) ?  kf::gen_thumbnail('/' . $img_url, 30, 30, 1) : ''),
                                'icons' => array(kf::gen_del_icon_ajax(md5($dir . $file), false, 'modfile_delete', '', '&file=' . md5($dir . $file) . '&ext=' . $file_extention)));

                        }
                    }
                    else {
                        $this->fetch_files($dir . $file, $ext, $exclude);
                    }
                }
            }
            closedir($openDir);
        }
    }

    function cmd_modfile_delete() {
        $file = $this->MODUL['alllist'][$this->TCR->GET['ext']][$this->TCR->GET['file']]['file'];
        @unlink($this->MODULE_ROOT . $file);
        $this->delete_cache_file($this->MODULE_ROOT . $file);
        $this->hard_exit();
    }


    function cmd_modfileload() {
        $fname = $this->MODULE_ROOT . base64_decode($this->TCR->REQUEST['modfile']);
        if (file_exists($fname)) {
            $this->MODUL['file_content'] = file_get_contents($fname);
            $this->MODUL['file_name'] = str_replace(CMS_ROOT, '', $fname);
            $this->MODUL['file_lastmod'] = date("d.m.Y H:i:s", filemtime($fname));
            $this->MODUL['file_ext'] = $this->get_ext($fname);
            # $this->set_editor(base64_decode($this->TCR->REQUEST['modfile']));
        }
        $this->smarty->assign('MODUL', $this->MODUL);
    }

    function cmd_axloadfile() {
        $this->cmd_modfileload();
        $this->smarty->assign('MODUL', $this->MODUL);
        kf::simple_output('
 <script>
 	editAreaLoader.setValue("mod_content", "' . htmlspecialchars($this->MODUL['file_content']) . '");
 </script>
 ');
    }

    function cmd_savemodfile() {
        $fname = $this->MODULE_ROOT . base64_decode($this->TCR->REQUEST['modfile']);
        file_put_contents($fname, trim(stripslashes($this->TCR->REQUEST['fc'])));
        $file_content = file_get_contents($fname);
        $this->smarty->assign('file_content', $file_content);
        $this->msg('{LBLA_SAVED}');
        $this->hard_exit();
    }

    function delete_cache_file($target) {
        $RetVal = explode('.', basename($target));
        $file_ext_src = strtolower($RetVal[count($RetVal) - 1]);
        $img_url = str_replace(CMS_ROOT, '', $this->MODULE_ROOT) . str_replace($this->MODULE_ROOT, '', $target);
        $thumb = ((in_array($file_ext_src, $this->viewable_ext)) ?  kf::gen_thumbnail('/' . $img_url, 30, 30, 1) : '');
        $cache_file = str_replace('//', '/', CMS_ROOT . $thumb); #/cache/wilinku_test_resize_30x30.jpg
        if (file_exists($cache_file))
            @unlink($cache_file);
    }


    function cmd_single_file_upload() {
        global $allowed_ext;
        if ($_FILES['datei']['error'][0] != 4 && is_array($_FILES['datei'])) {
            foreach ($_FILES['datei']['name'] as $key => $file_name) {
                if ($_FILES['datei']['error'][$key] == 0) {
                    $target = $this->MODULE_ROOT . base64_decode($this->TCR->REQUEST['target']) . $this->format_file_name($file_name);
                    $RetVal = explode('.', $file_name);
                    $file_ext = strtolower($RetVal[count($RetVal) - 1]);
                    if (in_array('.' . $file_ext, $allowed_ext)) {
                        delete_file($target);
                        move_uploaded_file($_FILES['datei']['tmp_name'][$key], $target);
                        chmod($target, 0755);
                        $this->delete_cache_file($target);
                        $this->msg(kf::translate_admin('{LBLA_SAVED}'));
                    }
                    else {
                        $this->msge('not allowed file: ' . basename($target));
                    }
                }
            }
        }
        else {
            $this->msge('Keine Datei');
        }
        $this->echo_json_fb('reloadfiles');
    }

    function cmd_fileuploadimg() {
        if (is_array($_FILES['datei'])) {
            foreach ($_FILES['datei']['name'] as $key => $file_name) {
                if ($_FILES['datei']['error'][$key] == 0) {
                    $target = $this->MODULE_ROOT . base64_decode($this->TCR->REQUEST['filenames'][$key]);
                    $RetVal = explode('.', $target);
                    $file_ext_target = strtolower($RetVal[count($RetVal) - 1]);
                    $RetVal = explode('.', $file_name);
                    $file_ext_src = strtolower($RetVal[count($RetVal) - 1]);
                    if ($file_ext_target == $file_ext_src) {
                        delete_file($target);
                        move_uploaded_file($_FILES['datei']['tmp_name'][$key], $target);
                        chmod($target, 0755);
                        $this->delete_cache_file($target);
                        $this->msg(kf::translate_admin('{LBLA_SAVED}' . basename($target)));
                    }
                    else {
                        $this->msge('error on file: ' . basename($file_name));
                    }
                }
            }
        }
        $this->echo_json_fb('reloadfiles');
    }


}
