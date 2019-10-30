<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich::instagram
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2019-10-18
 */

defined('IN_SIDE') or die('Access denied.');
# DEFINE('TBL_TABLE_NAME', TBL_CMS_PREFIX . 'mein_tabelle');

require CMS_ROOT . 'includes/modules/instagram/lib/instagram-php-scraper/vendor/autoload.php';

class instagram_master_class extends modules_class {

    /**
     * __construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
    }

    public static function load_from_cache($cache_file) {
        $feed = array();
        if (is_file($cache_file)) {
            $feed = json_decode(file_get_contents($cache_file), true);
        }
        return $feed;
    }

    /**
     * instagram_master_class::get_insta_stream()
     * 
     * @return void
     */
    public function get_insta_stream($PLUGIN_OPT = array()) {
        if ($this->gblconfig->insta_account != "" && $this->gblconfig->insta_pass != "") {

            $foto_width = ($PLUGIN_OPT['foto_width'] > 0) ? $PLUGIN_OPT['foto_width'] : 2000;
            $foto_height = ($PLUGIN_OPT['foto_height'] > 0) ? $PLUGIN_OPT['foto_height'] : 1000;
            $foto_small_width = ($PLUGIN_OPT['foto_small_width'] > 0) ? $PLUGIN_OPT['foto_small_width'] : 400;
            $foto_small_height = ($PLUGIN_OPT['foto_small_height'] > 0) ? $PLUGIN_OPT['foto_small_height'] : 300;

            $foto_crop_pos = ($PLUGIN_OPT['foto_crop_pos'] > 0) ? $PLUGIN_OPT['foto_crop_pos'] : 'center';
            $ele_count = ($PLUGIN_OPT['ele_count'] > 0) ? $PLUGIN_OPT['ele_count'] : 100;
            $foto_resize_method = ($PLUGIN_OPT['foto_resize_method'] != '') ? $PLUGIN_OPT['foto_resize_method'] : 'resize';
            $cache_file_events = CMS_ROOT . CACHE . 'instagram_' . md5(implode('',$PLUGIN_OPT)) . '.json';
            $arr = array();
            if ($this->gblconfig->insta_cacheactive == 0) {
                @unlink($cache_file_events);
            }
            $instagram = \InstagramScraper\Instagram::withCredentials($this->gblconfig->insta_account, $this->gblconfig->insta_pass, CMS_ROOT . 'cache/');
            # $instagram->login();


            if (file_exists($cache_file_events)) {
                $time_event = filectime($cache_file_events);
            }
            if (!file_exists($cache_file_events) || (time() - $time_event) > ($this->gblconfig->insta_cachetime * 3600)) {
                $nonPrivateAccountMedias = $instagram->getMedias($this->gblconfig->insta_account);
                #  print_r($nonPrivateAccountMedias);
                foreach ($nonPrivateAccountMedias as $media) {
                    if ($PLUGIN_OPT['no_videos'] == 0 || ($PLUGIN_OPT['no_videos'] == 1 && $media->getType() != 'video')) {
                        $arr[] = array(
                            'id' => $media->getId(),
                            'shortcode' => $media->getShortCode(),
                            'created_time' => $media->getCreatedTime(),
                            'title' => '',
                            'name' => '',
                            'caption' => $media->getCaption(),
                            'number_of_comments' => $media->getCommentsCount(),
                            'number_of_likes' => $media->getLikesCount(),
                            'link' => $media->getLink(),
                            'image_url' => $media->getImageHighResolutionUrl(),
                            'media_type' => $media->getType(),
                            'image' => $local_foto,
                            'thumb_small' => $thumb_small,
                            'thumb' => $thumb,
                            'socialtype' => 'instagram');
                    }
                }
                file_put_contents($cache_file_events, json_encode($arr));
            }
            else {
                $arr = self::load_from_cache($cache_file_events);
            }

            # set settings
            foreach ($arr as $key => $row) {
                $arr[$key]['date_ger'] = date('d.m.Y', $row['created_time']);
                $local_foto = $foto_width . 'x' . $foto_width . '_' . md5(basename($row['image_url'])) . '.jpg';
                if (!file_exists(CMS_ROOT . CACHE . $local_foto)) {
                    file_put_contents(CMS_ROOT . CACHE . $local_foto, $this->curl_get_data($row['image_url']));
                }
                $thumb = gen_thumb_image('./' . CACHE . $local_foto, (int)$foto_width, (int)$foto_height, $foto_resize_method, $foto_crop_pos);
                $thumb_small = gen_thumb_image('./' . CACHE . $local_foto, (int)$foto_small_width, (int)$foto_small_height, $foto_resize_method, $foto_crop_pos);
                $arr[$key]['thumb_small'] = $thumb_small;
                $arr[$key]['thumb'] = $thumb;
            }
        }
        return $arr;
    }
}
