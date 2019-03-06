<?PHP
/**
 * @package    ekomi
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

class ekomi_master_class extends modules_class
{

    /**
     * ekomi_master_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * ekomi_master_class::csv_to_array()
     * 
     * @param string $filename
     * @param string $delimiter
     * @return
     */
    function csv_to_array($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $one_data = array();
                $one_data['date'] = date('d.m.Y', $row[0]);
                $one_data['create_time'] = $row[0];
                $one_data['order_id'] = $row[0];
                $one_data['customer'] = $row[1];
                $one_data['stars'] = $row[2];
                $one_data['review'] = str_replace(array('\n'), "<br>", $row[3]);
                $data[] = $one_data;
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * ekomi_master_class::get_last_bewertungen()
     * 
     * @return
     */
    function get_last_bewertungen()
    {
        #   if (!file_exists(CMS_ROOT . 'admin/cache/ekomi_bewertungen.csv'))
        $url = 'http://api.ekomi.de/get_feedback.php?interface_id=' . $this->gbl_config['ekomi_interface_id'] .
            '&interface_pw=' . $this->gbl_config['ekomi_interface_pw'] .
            '&version=cust-1.0.0&type=csv';
        $this->curl_get_data_to_file($url, CMS_ROOT . 'cache/ekomi_bewertungen.csv');
        return $this->csv_to_array(CMS_ROOT . 'cache/ekomi_bewertungen.csv');
    }

}

?>