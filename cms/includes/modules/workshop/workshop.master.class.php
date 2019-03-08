<?PHP

/**
 * @package    workshop
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_WS_CITIES', TBL_CMS_PREFIX . 'ws_cities');
DEFINE('TBL_WS_WORKSHOPS', TBL_CMS_PREFIX . 'ws_workshops');
DEFINE('TBL_WS_BOOKINGS', TBL_CMS_PREFIX . 'ws_bookings');

class workshop_master_class extends modules_class {

    var $imgroot = "";

    /**
     * workshop_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->imgroot = CMS_ROOT . 'file_data/workshop/';
    }

    /**
     * workshop_master_class::set_workshop_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_workshop_opt(&$row) {
        $row['date_ger'] = ($row['ws_datetime'] != '0000-00-00') ? date('d.m.Y', $row['ws_datetime']) : '-';
        $row['images'] = unserialize($row['ws_images']);
        $row['ws_price_br'] = self::format_number($row['ws_price_br']);
        $row['images'] = (array )$row['images'];
        foreach ($row['images'] as $key => $img) {
            if ($img == "") {
                unset($row['images'][$key]);
            }
            if (!defined(ISADMIN) && ISADMIN != 1) {
                $row['thumbs'][] = gen_thumb_image('/file_data/workshop/' . $img, $this->gbl_config['wsthumb_width'], $this->gbl_config['wsthumb_height'], 'crop', "center");
            }
        }
        $row['bookings'] = (array )$this->load_bookings_by_workshop($row['id']);
        $row['bookings_count'] = count($row['bookings']);
        $row['bookings_free'] = $row['ws_teilnbis'] - $row['bookings_count'];
        return $row;
    }

    /**
     * workshop_master_class::load_workshop()
     * 
     * @param mixed $id
     * @return
     */
    function load_workshop($id) {
        $WORKSHOP = $this->db->query_first("SELECT W.*,C.c_city FROM " . TBL_WS_WORKSHOPS . " W, " . TBL_WS_CITIES . " C WHERE C.id=W.ws_city AND W.id=" . (int)$id);
        $this->set_workshop_opt($WORKSHOP);
        $this->WORKSHOP['ws'] = $WORKSHOP;
        return $WORKSHOP;
    }

    /**
     * workshop_master_class::load_city()
     * 
     * @param mixed $id
     * @return
     */
    function load_city($id) {
        return $this->db->query_first("SELECT * FROM " . TBL_WS_CITIES . " WHERE id=" . (int)$id);
    }

    /**
     * workshop_master_class::load_bookings_by_workshop()
     * 
     * @param mixed $id
     * @return
     */
    function load_bookings_by_workshop($id) {
        $result = $this->db->query("SELECT * FROM " . TBL_WS_BOOKINGS . " B LEFT JOIN " . TBL_CMS_CUST . " C ON (C.kid=B.wb_kid) 
        WHERE wb_wid=" . (int)$id . " 
        ORDER BY C.nachname, C.vorname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['booking_date_ger'] = date('d.m.Y', $row['wb_time']);
            $arr[] = $row;
        }
        return (array )$arr;
    }

    /**
     * workshop_master_class::connect_customer_with_workshop()
     * 
     * @param mixed $kid
     * @param mixed $wb_wid
     * @return
     */
    function connect_customer_with_workshop($kid, $wb_wid) {
        $this->db->query("DELETE FROM " . TBL_WS_BOOKINGS . " WHERE wb_kid=" . $kid . " AND wb_wid=" . $wb_wid);
        $arr = array(
            'wb_kid' => $kid,
            'wb_wid' => $wb_wid,
            'wb_time' => time());
        return insert_table(TBL_WS_BOOKINGS, $arr);
    }

}

?>