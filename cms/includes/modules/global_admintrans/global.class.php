<?php



/**
 * @package    global_admintrans
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class global_admintrans_class extends modules_class
{

    /**
     * global_admintrans_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * global_admintrans_class::interpreter()
     * 
     * @param mixed $method
     * @return
     */
    function interpreter($method)
    {
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }


}
?>