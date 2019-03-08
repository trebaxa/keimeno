<?PHP
/**
 * @package    ktracker
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');
DEFINE('TBL_KTRACKER', TBL_CMS_PREFIX . 'ktracker');
DEFINE('TBL_KTRACKER_LOG', TBL_CMS_PREFIX . 'ktracker_log');

class ktracker_master_class extends modules_class
{

    /**
     * ktracker_master_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * ktracker_master_class::load_compain()
     * 
     * @param mixed $id
     * @return
     */
    function load_compain($id)
    {
        return $this->db->query_first("SELECT * FROM " . TBL_KTRACKER . " WHERE id=" . (int)
            $id);
    }

    /**
     * ktracker_master_class::load_compains()
     * 
     * @return
     */
    function load_compains()
    {
        $result = $this->db->query("SELECT *,K.id AS KID FROM " . TBL_KTRACKER .
            " K LEFT JOIN " . TBL_CMS_TEMPCONTENT .
            " T ON (T.tid=K.k_page_id AND lang_id=1)  
            WHERE  1
            GROUP BY K.id
            ORDER BY k_title");
        while ($row = $this->db->fetch_array_names($result)) {
            $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
            $row['k_link'] = 'http://www.' . $this->gbl_config['opt_domain'] . gen_page_link(0,
                $url_label, 1);
            $row['icons'][] = kf::gen_del_icon($row['KID'], true, 'del_camp');
            $row['total_clicks'] = $this->db->query_first("SELECT SUM(kl_count) AS KSUM FROM " .
                TBL_KTRACKER_LOG . " WHERE kl_id=" . $row['KID']);
            $arr[] = $row;
        }
        return $arr;
    }

}

?>