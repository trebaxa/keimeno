<?php

/**
 * @package    frames
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class frames_admin_class extends keimeno_class
{

    protected $FRAMES = array();

    /**
     * frames_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->profil_obj = array(
            'frame_width_cm' => $this->gbl_config['frame_width_cm'],
            'frame_width_inner_cm' => $this->gbl_config['frame_width_inner_cm'],
            'ulappung_cm' => $this->gbl_config['frame_ulappung_cm']);
        $this->load_frame_colors();
    }

    /**
     * frames_admin_class::cmd_deleteframe()
     * 
     * @return
     */
    function cmd_deleteframe()
    {
        $this->db->query("DELETE FROM " . TBL_CMS_FRAMEDEF . " WHERE id=" . $_GET['ident'] .
            " LIMIT 1");
        $this->ej();
    }

    /**
     * frames_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item()
    {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * frames_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('FRAMES', $this->FRAMES);
    }

    /**
     * frames_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf()
    {
        $CONFIG_OBJ = new config_class();
        $this->FRAMES['CONFIG'] = $CONFIG_OBJ->buildTable(55, 55);
    }

    /**
     * frames_admin_class::colorExtract()
     * 
     * @param mixed $id
     * @return
     */
    function colorExtract($id)
    {
        $new_height = 10;
        $r_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_FRAMECOLORS .
            " WHERE id='$id' LIMIT 1");
        if ($r_obj['frame_foto'] && file_exists(CMS_ROOT . FRAME_PATH_FOTO . $r_obj['frame_foto'])) {
            $frame_foto = @ImageCreateFromJPEG(CMS_ROOT . FRAME_PATH_FOTO . $r_obj['frame_foto']);
            list($width_orig, $height_orig) = getimagesize(CMS_ROOT . FRAME_PATH_FOTO . $r_obj['frame_foto']);
            $new_width = round($new_height * $width_orig / $height_orig);
            $image_p = imagecreatetruecolor($new_width, $new_height);
            imageantialias($image_p, true);
            imagecopyresampled($image_p, $frame_foto, 0, 0, 0, 0, $new_width, $new_height, $width_orig,
                $height_orig);
            #   header('Content-type: image/jpeg');
            #  imagejpeg($image_p);

            $a_color = sprintf("%06X", imagecolorat($image_p, 1, 1));

            $b_color = sprintf("%06X", imagecolorat($image_p, 1, $new_width));
            #  echo $new_width.' '.$a_color.' '.$b_color;die;
            return $a_color . '|' . $b_color;
        } else
            return '-1|-1';
    }

    /**
     * frames_admin_class::cmd_a_fotosave()
     * 
     * @return
     */
    function cmd_a_fotosave()
    {
        $id = $_POST['id'];
        if (strrchr($_FILES['datei']['name'], '.') != ".jpg") {
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] .
                '&msge=' . base64_encode("Bitte verwenden Sie das JPG Format."));
            exit;
        }
        $FORM['frame_foto'] = 'PROFIL_FOTO_' . $id . '.jpg';
        if (!is_dir(CMS_ROOT . FRAME_PATH_FOTO))
            mkdir(CMS_ROOT . FRAME_PATH_FOTO, 0755);
        if (!validate_upload_file($_FILES['datei'])) {
            $this->msge($_SESSION['upload_msge']);
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] .
                '&section=start');
            exit;
        }
        move_uploaded_file($_FILES['datei']['tmp_name'], CMS_ROOT . FRAME_PATH_FOTO . $FORM['frame_foto']);
        chmod(CMS_ROOT . FRAME_PATH_FOTO . $FORM['frame_foto'], 0755);
        update_table(TBL_CMS_FRAMECOLORS, 'id', $id, $FORM);
        $this->msg("Bild wurde aktualisiert.");
        header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] .
            '&section=start');
        exit;
    }

    /**
     * frames_admin_class::cmd_a_msave()
     * 
     * @return
     */
    function cmd_a_msave()
    {
        $kname = (array )$_POST['kname'];
        foreach ($kname as $id => $wert) {
            unset($FORM);
            $FORM['kname'] = $kname[$id];
            $FORM['kname_shop'] = $kname_shop[$id];
            $FORM['fcolor_1'] = $_POST["fcolor_1"][$id];
            $FORM['fcolor_2'] = $_POST["fcolor_2"][$id];
            $FORM['fcolor_3'] = $_POST["fcolor_3"][$id];
            $FORM['fcolor_4'] = $_POST["fcolor_4"][$id];
            update_table(TBL_CMS_FRAMECOLORS, 'id', $id, $FORM);

            if ($_POST['dograb'] == 1) {
                foreach ($_POST['color_id'] as $id => $wert) {
                    unset($FORM);
                    $colors = explode('|', $this->colorExtract($id));
                    $FORM['fcolor_1'] = $colors[0];
                    $FORM['fcolor_2'] = $colors[1];
                    if ($FORM['fcolor_1'] != "" && $FORM['fcolor_2'] != "")
                        update_table(TBL_CMS_FRAMECOLORS, 'id', $id, $FORM);
                }
            }
        }

        /*
        foreach ($collection_ids as $collect_id_key => $collect_id_value) {
        unset($FORM);
        $FORM['color_ids'] = "";
        foreach ($collect_id as $cid_arr => $color_id) {
        $ids = explode("_", $cid_arr);
        if ($collect_id_value == $ids[1]) {
        if ($FORM['color_ids'])
        $FORM['color_ids'] .= ';';
        $FORM['color_ids'] .= $color_id;
        }
        }
        update_table(TBL_FRAMECOLLECTION, 'id', $collect_id_key, $FORM);
        }
        */
        keimeno_class::msg('gepeichert');
        $this->hard_exit();
    }

    /**
     * frames_admin_class::cmd_addfoto()
     * 
     * @return
     */
    function cmd_addfoto()
    {
        $this->FRAMES['fcolor'] = $this->db->query_first("SELECT * FROM " .
            TBL_CMS_FRAMECOLORS . " WHERE id='" . $_GET['id'] . "' LIMIT 1");
    }

    /**
     * frames_admin_class::cmd_a_klonen()
     * 
     * @return
     */
    function cmd_a_klonen()
    {
        $f_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_FRAMEDEF .
            " WHERE id='" . $_GET['id'] . "' LIMIT 1");
        unset($f_obj['id']);
        $f_obj['fname'] = $f_obj['fname'] . ' COPY';
        insert_table(TBL_CMS_FRAMEDEF, $this->real_escape($f_obj));
        $this->msg('Erfolgreich geklont...');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] .
            '&section=start');
        exit;
    }


    /**
     * frames_admin_class::load_frame_colors()
     * 
     * @return
     */
    function load_frame_colors()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FRAMECOLORS .
            " ORDER BY kname");
        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['frame_foto'] != "") {
                $row[thumb] = kf::gen_thumbnail('/' . FRAME_PATH_FOTO . $row['frame_foto'], 150,
                    100, 'resize', false);
            }
            # Leiste Thumb
            $row['icon_addfoto'] = kf::gen_std_icon($row['id'], 'fa-file-image-o ',
                'Rahmen Foto hinzuf&uuml;gen', 'id', 'addfoto', '&section=addfoto', $_SERVER['PHP_SELF']);
            $row['random'] = uniqid();
            # Rahmen
            $frame_cl = new frame_class();
            $frame_cl->set_opt($row['id'], $row['fcolor_1'], $row['fcolor_2'], 0);
            $frame_cl->ink = 50;
            $frame_cl->genFramePic();
            $row['preview'] = '../cache/' . basename($frame_cl->cachefile_root);
            $this->FRAMES['colors'][] = $row;
        }
    }

    /**
     * frames_admin_class::load_frame_defs()
     * 
     * @return
     */
    function load_frame_defs()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FRAMEDEF .
            " ORDER BY fname,id,width_cm");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_del_icon($row['id'], false, 'deleteframe');
            $row['icons'][] = kf::gen_clone_icon($row['id']);

            #kf::gen_del_icon_reload($row['id'], 'a_delete');
            $this->FRAMES['framedefs'][] = $row;
        }
    }

    /**
     * frames_admin_class::cmd_loadframedefs()
     * 
     * @return
     */
    function cmd_loadframedefs()
    {
        $this->load_frame_defs();
    }

    /**
     * frames_admin_class::cmd_save_frame_defs()
     * 
     * @return
     */
    function cmd_save_frame_defs()
    {
        foreach ($_POST['FORM'] as $key => $row) {
            $row['profil_type'] = 1;
            $row['frame_width'] = round($this->profil_obj['frame_width_cm'] / $row['height_cm'] *
                FOTO_HEIGHT_PX);
            $row['frame_width_inner'] = round($this->profil_obj['frame_width_inner_cm'] / $row['height_cm'] *
                FOTO_HEIGHT_PX);
            $row['ulappung_px'] = $this->profil_obj['ulappung_cm'] / $row['height_cm'] *
                FOTO_HEIGHT_PX;
            update_table(TBL_CMS_FRAMEDEF, 'id', $key, $row);
        }
        $this->hard_exit();
    }

    /**
     * frames_admin_class::cmd_add_frame()
     * 
     * @return
     */
    function cmd_add_frame()
    {
        insert_table(TBL_CMS_FRAMEDEF, $_POST['FORM']);
        $this->msg('angelegt');
        HEADER('location:' . $_SERVER['PHP_SELF'] .
            '?section=framedefs&cmd=loadframedefs&epage=' . $_POST['epage']);
        $this->hard_exit();
    }


}

?>