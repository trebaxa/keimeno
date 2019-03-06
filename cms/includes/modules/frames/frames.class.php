<?php

/**
 * @package    frames
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


define('TBL_CMS_FRAMECOLORS', TBL_CMS_PREFIX . 'frame_color');
define('TBL_CMS_FRAMEDEF', TBL_CMS_PREFIX . 'frame_def');
DEFINE('FRAME_PATH_FOLDER', 'images/frames/');
DEFINE('FRAME_PATH', './' . FRAME_PATH_FOLDER);
DEFINE('FRAME_PATH_FOTO', 'includes/modules/frames/data/profil_foto/');
DEFINE('FRAME_SAVE_PATH', FRAME_PATH . 'frame_data/');
DEFINE('FOTO_HEIGHT_THUMB_PX', 200); #200
DEFINE('FOTO_HEIGHT_FULL_PX', 900); #200
DEFINE('DPI', 200);
DEFINE('FOTO_HEIGHT_PX', 200);

#echo FOTO_HEIGHT_PX;die;

class frames_class extends keimeno_class
{

    var $FRAMES = array();

    /**
     * frames_class::__construct()
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
        $this->GRAPHIC_FUNC = new graphic_class();
    }

    /**
     * frames_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('FRAMES', $this->FRAMES);
    }

    /**
     * frames_class::frame_images()
     * 
     * @param mixed $params
     * @return
     */
    function frame_images($params)
    {
        if ($this->gblconfig->frame_active == 0) {
            return $params;
        }
        if (is_array($params['image_list'])) {
            $frame_cl = new frame_class();
            $frame_cl->load_frame_colors();
            foreach ($params['image_list'] as $key => $img) {
                # Thumbnail
                $thumb = PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb($img['img_image'],
                    1500, 1000, './' . CACHE, true, 'crop');
                $frame_cl->set_opt($this->gblconfig->frame_default, '', '', $frame_cl->
                    frame_color_ids[rand(0, count($frame_cl->frame_color_ids) - 1)]);
                $frame_cl->FOTO_HEIGHT_PX = FOTO_HEIGHT_THUMB_PX;
                $frame_cl->ink = $img['img_thheight'];
                $frame_cl->frame_pic = CMS_ROOT . 'cache/' . basename($thumb);
                $frame_cl->genFramePic();
                $params['image_list'][$key]['img_src'] = PATH_CMS . 'cache/' . basename($frame_cl->
                    cachefile_root);

                # Vollbild
                $frame_cl->ink = $img['img_fullheight'];
                $frame_cl->FOTO_HEIGHT_PX = FOTO_HEIGHT_FULL_PX;
                $frame_cl->frame_pic = CMS_ROOT . 'cache/' . basename($thumb);
                $frame_cl->genFramePic();
                $params['image_list'][$key]['img_redfullsize'] = PATH_CMS . 'cache/' . basename($frame_cl->
                    cachefile_root);
            }
            keimeno_class::allocate_memory($frame_cl);
        }
        return $params;
    }

}

?>