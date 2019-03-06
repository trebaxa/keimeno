<?PHP
/**
 * @package    B8
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */
defined('IN_SIDE') or die('Access denied.');
DEFINE('TBL_CMS_B8WORDS', TBL_CMS_PREFIX . 'b8_wordlist');

class b8_master_class extends modules_class {

    var $b8 = null;
    protected $b8_is_ready = true;

    /**
     * b8_master_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        # $config_b8 = array('storage' => 'dba');
        $this->b8_is_ready = true;
        #$config_storage = array('database' => 'wordlist.db', 'handler' => 'gdbm');
        # Tell b8 to use the new-style HTML extractor
        $config_lexer = array('old_get_html' => FALSE, 'get_html' => TRUE);

        # Tell the degenerator to use multibyte operations
        # (needs PHP's mbstring module! If you don't have it, set 'multibyte' to FALSE)
        $config_degenerator = array('multibyte' => TRUE);

        $config_b8 = array('storage' => 'mysqli');

        $config_storage = array(
            'database' => DB_DATABASE,
            'table_name' => TBL_CMS_PREFIX . 'b8_wordlist',
            'host' => DB_HOST,
            'user' => DB_USER,
            'pass' => DB_PASSWORD);

        # Include the b8 code
        require MODULE_ROOT . 'b8/b8/b8/b8.php';

        # Create a new b8 instance
        try {
            $this->b8 = new b8($config_b8, $config_storage, $config_lexer, $config_degenerator);
        }
        catch (Exception $e) {
            #  echo "<b>example:</b> Could not initialize b8.<br />\n";
            #  echo "<b>Error message:</b> ", $e->getMessage();
            #  echo "\n\n</div>\n\n</body>\n\n</html>";
            #  exit();
            $this->b8_is_ready = false;
            $this->db->query("INSERT IGNORE INTO `" . TBL_CMS_PREFIX . 'b8_wordlist' . "` (`token`, `count_ham`) VALUES ('b8*dbversion', '3')");
            $this->db->query("INSERT IGNORE INTO `" . TBL_CMS_PREFIX . 'b8_wordlist' . "` (`token`, `count_ham`, `count_spam`) VALUES ('b8*texts', '0', '0')");
        }

    }

    /**
     * b8_master_class::microtimeFloat()
     * 
     * @return
     */
    function microtimeFloat() {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * b8_master_class::formatRating()
     * 
     * @param mixed $rating
     * @return
     */
    function formatRating($rating) {
        if ($rating === FALSE)
            return array(
                'rating' => sprintf("%5f", 0),
                'red' => 0,
                'green' => 0);

        $red = floor(255 * $rating);
        $green = floor(255 * (1 - $rating));
        $arr = array(
            'rating' => sprintf("%5f", $rating),
            'red' => $red,
            'green' => $green);

        # return "<span style=\"color:rgb($red, $green, 0);\"><b>" . sprintf("%5f", $rating) . "</b></span>";
        return (array )$arr;

    }


}

?>