<?PHP

/**
 * @package    Keimeno::safeupload
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2017-08-23
 */

defined('IN_SIDE') or die('Access denied.');
# DEFINE('TBL_TABLE_NAME', TBL_CMS_PREFIX . 'mein_tabelle');

class safeupload_master_class extends modules_class {

    function __construct() {
        parent::__construct();
    }

    /**
     * safeupload_master_class::remove_file_log()
     * 
     * @param mixed $id
     * @param mixed $kid
     * @return void
     */
    public static function remove_file_log($id, $kid) {
        $customer_file_root = memindex_master_class::get_path($kid);
        if (file_exists($customer_file_root . 'file_download_log.csv')) {
            $table = fopen($customer_file_root . 'file_download_log.csv', 'r');
            $temp_table = fopen($customer_file_root . 'file_download_log_temp.csv', 'w');

            while (($data = fgetcsv($table, 1000, ";")) !== FALSE) {
                if ($data[0] == $id) { // this is if you need the first column in a row
                    continue;
                }
                fputcsv($temp_table, $data, ";");
            }
            fclose($table);
            fclose($temp_table);
            rename($customer_file_root . 'file_download_log_temp.csv', $customer_file_root . 'file_download_log.csv');
        }
    }
}
