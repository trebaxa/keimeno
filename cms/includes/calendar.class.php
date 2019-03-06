<?php

/**
 * $Id: tgcCalendar.php,v 1.6 2004/07/12 01:29:34 luckec Exp $
 *
 * tgcCalendar is a simple calendar class that helps you with handling dates and
 * allows you to generate a simple tabular calendar
 *
 * @package     tgcCalendar
 * @author      Carsten Lucke <luckec@tool-garage.de>
 * @copyright   Carsten Lucke <http://www.tool-garage.de>
 */

/**
 * include directory
 * @access  public
 */
define('tgcCALENDAR_INCLUDEDIR', 'include');

/**
 * stylesheet directory
 * @access  public
 */

define('tgcCALENDAR_STYLEDIR', 'styles');

/**
 * template directory
 * @access  public
 */
define('tgcCALENDAR_TEMPLATEDIR', 'templates');

/**
 * image directory
 * @access  public
 */
define('tgcCALENDAR_IMAGEDIR', 'img');

/**
 * image directory
 * @access  public
 */
define('tgcCALENDAR_ZEROFILL', false);

/**
 * tgcCalendar is a simple calendar class that helps you with handling dates and
 * allows you to generate a simple tabular calendar
 *
 * @package      tgcCalendar
 * @access       public
 * @version      1.1.1
 * @author       Carsten Lucke                   <luckec@tool-garage.de>
 */
class tgcCalendar {

    /**
     * actual day
     *
     * @access   private
     * @var      int
     */
    var $day = null;

    /**
     * actual month
     *
     * @access   private
     * @var      int
     */
    var $month = null;

    /**
     * actual year
     *
     * @access   private
     * @var      int
     */
    var $year = null;

    /**
     * value for the href-param in the navigation-arrows' <a> TAG
     *
     * @access   private
     * @var      string
     */
    var $_arrowUrl = null;

    /**
     * First day in tabular calendar output
     *
     * sunday = 0, monday = 1, ..., saturday = 6
     *
     * @access   public
     * @var      int
     */
    var $_firstDay = 1;

    /**
     * callback function name
     *
     * @access   private
     * @var      string
     */
    var $_callback = null;

    /**
     * template-file used by getCalendarMarkup()
     *
     * @access   private
     * @var      string
     */
    var $_templateFile = 'default.tmpl';

    /**
     * request vars for usage with user-callback
     *
     * @access   private
     * @var      array
     */
    var $_vars = null;

    /**
     * Priority of stylesheet-classes
     *
     * If a calendar field could belong to two or more styleclasses, 
     * then the one with the highest priority will be applied.
     *
     * @access   private
     * @var      array
     */
    var $_stylePriority = array(
        'today' => 1000,
        'sunday' => 800,
        'weekend' => 100,
        'future' => 400,
        'ago' => 200);


    /**
     * Constructor
     *
     * Creates an object of tgcCalendar. The param $locale can be used to
     * make the class work according to the desired language settings.
     * Default language is German ('de_DE'). You can change this via constructor or 
     * {@link setLocale()}.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with actual date
     * $cal1   =& new tgcCalendar();
     * echo $cal1->toString() . '<br /><br />';
     * 
     * // creates an object with explicit date
     * $day    =   9;
     * $month  =   9;
     * $year   =   1980;
     * $cal2   =& new tgcCalendar($day, $month, $year);
     * echo $cal2->toString() . '<br /><br />';
     * </code>
     *
     * @access   public
     * @param    int     $d      day
     * @param    int     $m      month
     * @param    int     $y      year
     * @param    string   $locale language-setting
     * @see      setLocale()
     */
    function __construct($d = null, $m = null, $y = null, $locale = 'de_DE') {
        $this->day = is_null($d) ? $this->day = (int)date('d') : $d;
        $this->month = is_null($m) ? $this->month = (int)date('m') : $m;
        $this->year = is_null($y) ? $this->year = (int)date('Y') : $y;

        $this->setLocale($locale);

        $this->setCalLinkUrl($_SERVER['PHP_SELF']);

        $this->_collectRequestVars(array('m', 'y'));
    }

    /**
     * Set the locale information.
     *
     * Use this method to make the calendar's output fit your language-standards.
     *
     * @access   public
     * @param    string      $locale     language code
     * @return   string      return value of the systems setlocale function
     */
    function setLocale($locale) {
        return setlocale(LC_TIME, $locale);
    }

    /**
     * set the object's date
     *
     * @deprecated   use setDate() instead
     * @access       public
     * @param        int     $d      day
     * @param        int     $m      month
     * @param        int     $y      year  
     * @see          setDate()
     */
    function set_date($d, $m, $y) {
        return $this->setDate($d, $m, $y);
    }

    /**
     * set the object's date
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with actual date
     * $cal    =& new tgcCalendar();
     * echo $cal->toString() . '<br /><br />';
     * 
     * // reset the object's date-settings
     * $cal->setDate(9, 9, 1980);
     * echo $cal->toString() . '<br /><br />';
     * </code>
     *
     * @access   public
     * @param    int     $d      day
     * @param    int     $m      month
     * @param    int     $y      year
     * @see      setDay(), setMonth(), setYear()
     */
    function setDate($d, $m, $y) {
        $this->day = $d;
        $this->month = $m;
        $this->year = $y;
    }

    /**
     * Set the object's day.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with actual date
     * $cal    =& new tgcCalendar();
     * echo $cal->toString() . '<br /><br />';
     * 
     * // reset the object's date-settings
     * $cal->setDay(9);
     * $cal->setMonth(9);
     * $cal->setYear(1980);
     * echo $cal->toString() . '<br /><br />';
     * </code>
     *
     * @access   public
     * @param    int     $d  day
     * @see      setDate(), setMonth(), setYear()
     */
    function setDay($d) {
        $this->day = $d;
    }

    /**
     * Set the object's month.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with actual date
     * $cal    =& new tgcCalendar();
     * echo $cal->toString() . '<br /><br />';
     * 
     * // reset the object's date-settings
     * $cal->setDay(9);
     * $cal->setMonth(9);
     * $cal->setYear(1980);
     * echo $cal->toString() . '<br /><br />';
     * </code>
     *
     * @access   public
     * @param    int     $m  month
     * @see      setDate(), setDay(), setYear()
     */
    function setMonth($m) {
        $this->month = $m;
    }

    /**
     * Set the object's year.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with actual date
     * $cal    =& new tgcCalendar();
     * echo $cal->toString() . '<br /><br />';
     * 
     * // reset the object's date-settings
     * $cal->setDay(9);
     * $cal->setMonth(9);
     * $cal->setYear(1980);
     * echo $cal->toString() . '<br /><br />';
     * </code>
     *
     * @access   public
     * @param    int     $y  year
     * @see      setDay(), setMonth(), setDate()
     */
    function setYear($y) {
        $this->year = $y;
    }

    /**
     * get date
     *
     * @deprecated   use getDay(), getMonth() and getYear() instead
     * @access       public
     * @return       array       numerical array with items day, month, year
     */
    function get_date() {
        return array(
            $this->day,
            $this->month,
            $this->year);
    }

    /**
     * get the object's actual day
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // read the object's date-values
     * echo 'day: ' . $cal->getDay() . '<br />';
     * echo 'month: ' . $cal->getMonth() . '<br />';
     * echo 'year: ' . $cal->getYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   int     actual day
     * @see      getPrevDay(), getNextDay()
     */
    function getDay() {
        return $this->day;
    }

    /**
     * Get the next day
     *
     * You can define by param if you want the next day as int or as complete tgcCalendar object.
     * Int just tells you the number of the next day.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // get the next day as int
     * echo 'next day as int: ' . $cal->getNextDay() . '<br />';
     * // get the next day as new object
     * $nextDay    =   $cal->getNextDay('asObject');
     * echo 'next day as object: ' . $nextDay->toString();
     * </code>
     *
     * @access   public
     * @param    string  $type       possible types are 'asInt' and 'asObject'
     * @return   mixed   next day
     * @see      getDay(), getPrevDay()
     */
    function &getNextDay($type = 'asInt') {
        $actualTs = mktime(0, 0, 0, (int)$this->month, (int)$this->day, (int)$this->year);
        $nextDayTs = $actualTs + 60 * 60 * 24;
        if ($type == 'asInt') {
            return intval(date('j', $nextDayTs));
        }
        return new tgcCalendar(intval(date('j', $nextDayTs)), intval(date('n', $nextDayTs)), intval(date('Y', $nextDayTs)));
    }

    /**
     * Get the previous day
     *
     * You can define by param if you want the previous day as int or as complete tgcCalendar object.
     * Int just tells you the number of the previous day.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // get the previous day as int
     * echo 'previous day as int: ' . $cal->getPrevDay() . '<br />';
     * // get the previous day as new object
     * $prevDay    =   $cal->getPrevDay('asObject');
     * echo 'previous day as object: ' . $prevDay->toString();
     * </code>
     *
     * @access   public
     * @param    string  $type       possible types are 'asInt' and 'asObject'
     * @return   mixed   previous day
     * @see      getDay(), getNextDay()
     */
    function &getPrevDay($type = 'asInt') {
        $actualTs = mktime(0, 0, 0, (int)$this->month, (int)$this->day, (int)$this->year);
        $nextDayTs = $actualTs - 60 * 60 * 24;
        if ($type == 'asInt') {
            return intval(date('j', $nextDayTs));
        }
        return new tgcCalendar(intval(date('j', $nextDayTs)), intval(date('n', $nextDayTs)), intval(date('Y', $nextDayTs)));
    }


    /**
     * get the object's actual month
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // read the object's date-values
     * echo 'day: ' . $cal->getDay() . '<br />';
     * echo 'month: ' . $cal->getMonth() . '<br />';
     * echo 'year: ' . $cal->getYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   int     actual month
     * @see      getNextMonth(), getPrevMonth()
     */
    function getMonth() {
        return $this->month;
    }

    /**
     * get the previous month
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(1, 1, 2003);
     * echo $cal->toString() . '<br /><br />';
     * 
     * // get the previous day as int
     * echo 'previous day as int: ' . $cal->getPrevDay() . '<br />';
     * echo 'previous month as int: ' . $cal->getPrevMonth() . '<br />';
     * echo 'previous year as int: ' . $cal->getPrevYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   int     previous month
     * @todo     decide whether it is useful to return an object here
     * @see      getMonth(), getNextMonth()
     */
    function getPrevMonth($type = 'asInt') {
        //        if ($this->month != 1) {
        //            if ($type == 'asInt') {
        //                return $this->month - 1;
        //            }
        //            return new tgcCalendar($this->day, $this->month - 1, $this->year);
        //
        //        }
        //
        //        if ($type == 'asInt') {
        //            return 12;
        //        }
        //        return new tgcCalendar($this->day, 12, $this->year - 1);

        if ($this->month != 1) {
            return $this->month - 1;
        }
        return 12;
    }

    /**
     * get the previous month
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(31, 12, 2003);
     * echo $cal->toString() . '<br /><br />';
     * 
     * // get the next day as int
     * echo 'next day as int: ' . $cal->getNextDay() . '<br />';
     * echo 'next month as int: ' . $cal->getNextMonth() . '<br />';
     * echo 'next year as int: ' . $cal->getNextYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   int     next month
     * @todo     decide whether it is useful to return an object here
     * @see      getMonth(), getPrevMonth()
     */
    function getNextMonth($type = 'asInt') {
        //        if ($this->month != 12) {
        //            if ($type == 'asInt') {
        //                return $this->month + 1;
        //            }
        //            return new tgcCalendar($this->day, $this->month + 1, $this->year);
        //
        //        }
        //
        //        if ($type == 'asInt') {
        //            return 1;
        //        }
        //        return new tgcCalendar($this->day, 1, $this->year + 1);

        if ($this->month != 12) {
            return $this->month + 1;
        }
        return 1;
    }

    /**
     * get the object's actual year
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // read the object's date-values
     * echo 'day: ' . $cal->getDay() . '<br />';
     * echo 'month: ' . $cal->getMonth() . '<br />';
     * echo 'year: ' . $cal->getYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   int     actual year
     * @see      getPrevYear(), getNextYear()
     */
    function getYear() {
        return $this->year;
    }

    /**
     * get the previous year
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(1, 1, 2003);
     * echo $cal->toString() . '<br /><br />';
     * 
     * // get the previous day as int
     * echo 'previous day as int: ' . $cal->getPrevDay() . '<br />';
     * echo 'previous month as int: ' . $cal->getPrevMonth() . '<br />';
     * echo 'previous year as int: ' . $cal->getPrevYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   int         previous year
     * @todo     decide whether it is useful to return an object here
     * @see      getPrevYear(), getYear()
     */
    function getPrevYear($type = 'asInt') {
        //        if ($type == 'asInt') {
        //            return $this->year - 1;
        //        }
        //        return new tgcCalendar($this->day, $this->month, $this->year - 1);

        return $this->year - 1;
    }

    /**
     * get the next year
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(31, 12, 2003);
     * echo $cal->toString() . '<br /><br />';
     * 
     * // get the next day as int
     * echo 'next day as int: ' . $cal->getNextDay() . '<br />';
     * echo 'next month as int: ' . $cal->getNextMonth() . '<br />';
     * echo 'next year as int: ' . $cal->getNextYear() . '<br />';
     * </code>
     *
     * @access   public
     * @return   mixed       next year
     * @todo     decide whether it is useful to return an object here
     * @see      getPrevYear(), getYear()
     */
    function getNextYear($type = 'asInt') {
        //        if ($type == 'asInt') {
        //            return $this->year + 1;
        //        }
        //        return new tgcCalendar($this->day, $this->month, $this->year + 1);

        return $this->year + 1;
    }

    /**
     * Set the href-url for the tabular-output's arrows
     *
     * @access       public
     * @param        string      $url    url
     */
    function setCalLinkUrl($url) {
        $this->_arrowUrl = $url;
    }

    /**
     * Get the name of the weekday.
     *
     * @deprecated   use getWeekdayAsString() instead
     * @static       can be statically called, if you provide all the parameters
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       string  weekday
     */
    function get_weekday_as_string($d = null, $m = null, $y = null) {
        return $this->getWeekdayAsString($d, $m, $y);
    }

    /**
     * Get the name of the weekday.
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // prints the weekday's name
     * echo 'weekday as string: ' . $cal->getWeekdayAsString() . '<br />';
     * // prints weekday's abbreviated name
     * echo 'weekday as short string: ' . $cal->getWeekdayAsShortString() . '<br />';
     * // prints the number of the weekday
     * echo 'number of the weekday: ' . $cal->getWeekdayAsNum() . '<br /><br />';
     * 
     * // !!! static method calls !!!
     * // prints the weekday's name
     * echo 'weekday as string: ' . tgcCalendar::getWeekdayAsString(9, 9, 1980) . '<br />';
     * // prints weekday's abbreviated name
     * echo 'weekday as short string: ' . tgcCalendar::getWeekdayAsShortString(9, 9, 1980) . '<br />';
     * // prints the number of the weekday
     * echo 'number of the weekday: ' . tgcCalendar::getWeekdayAsNum(9, 9, 1980) . '<br /><br />';
     * </code>
     *
     * @static       can be statically called, if you provide all the parameters
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       string  weekday
     * @see          getWeekdayAsNum(), getWeekdayAsShortString()
     */
    function getWeekdayAsString($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }

        return strftime('%A', mktime(0, 0, 0, (int)$m, (int)$d, (int)$y));
    }

    /**
     * Get the name of the weekday as abbreviated string
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // prints the weekday's name
     * echo 'weekday as string: ' . $cal->getWeekdayAsString() . '<br />';
     * // prints weekday's abbreviated name
     * echo 'weekday as short string: ' . $cal->getWeekdayAsShortString() . '<br />';
     * // prints the number of the weekday
     * echo 'number of the weekday: ' . $cal->getWeekdayAsNum() . '<br /><br />';
     * 
     * // !!! static method calls !!!
     * // prints the weekday's name
     * echo 'weekday as string: ' . tgcCalendar::getWeekdayAsString(9, 9, 1980) . '<br />';
     * // prints weekday's abbreviated name
     * echo 'weekday as short string: ' . tgcCalendar::getWeekdayAsShortString(9, 9, 1980) . '<br />';
     * // prints the number of the weekday
     * echo 'number of the weekday: ' . tgcCalendar::getWeekdayAsNum(9, 9, 1980) . '<br /><br />';
     * </code>
     *
     * @static       can be statically called, if you provide all the parameters
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       string  weekday
     * @see          getWeekdayAsString(), getWeekdayAsNum()
     */
    function getWeekdayAsShortString($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }

        return strftime('%a', mktime(0, 0, 0, (int)$m, (int)$d, (int)$y));
    }

    /**
     * Get the numerical representation of the weekday.
     *
     * Sunday, Monday, ..., Saturday => (0, 1, ..., 6)
     *
     * @deprecated   use getWeekdayAsNum() instead
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       int    weekday
     */
    function get_weekday_as_num($d = null, $m = null, $y = null) {
        $dayNum = $this->getWeekdayAsNum($d, $m, $y);
        if ($dayNum == 0) {
            return 7;
        }
        return $dayNum;
    }

    /**
     * Get the numerical representation of the weekday.
     *
     * Sunday = 0, Monday = 1, ..., Saturday = 6
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // prints the weekday's name
     * echo 'weekday as string: ' . $cal->getWeekdayAsString() . '<br />';
     * // prints weekday's abbreviated name
     * echo 'weekday as short string: ' . $cal->getWeekdayAsShortString() . '<br />';
     * // prints the number of the weekday
     * echo 'number of the weekday: ' . $cal->getWeekdayAsNum() . '<br /><br />';
     * 
     * // !!! static method calls !!!
     * // prints the weekday's name
     * echo 'weekday as string: ' . tgcCalendar::getWeekdayAsString(9, 9, 1980) . '<br />';
     * // prints weekday's abbreviated name
     * echo 'weekday as short string: ' . tgcCalendar::getWeekdayAsShortString(9, 9, 1980) . '<br />';
     * // prints the number of the weekday
     * echo 'number of the weekday: ' . tgcCalendar::getWeekdayAsNum(9, 9, 1980) . '<br /><br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       int    weekday
     * @see          getWeekdayAsString(), getWeekdayAsShortString()
     */
    function getWeekdayAsNum($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        return intval(date('w', mktime(0, 0, 0, (int)$m, (int)$d, (int)$y)));
    }

    /**
     * Get the month's name
     *
     * @deprecated   use getMonthAsString() instead
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       string  month-name
     */
    function get_month_as_string($m = null, $y = null) {
        return $this->getMonthAsString($m, $y);
    }

    /**
     * Get the month's name
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       string  month-name
     */
    function getMonthAsString($m = null, $y = null) {
        if (is_null($m) || is_null($y)) {
            $m = $this->month;
            $y = $this->year;
        }
        return utf8_encode(strftime('%B', mktime(0, 0, 0, (int)$m, 1, (int)$y)));
    }

    /**
     * Get the month's abbreviated name
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       string  month-name
     */
    function getMonthAsShortString($m = null, $y = null) {
        if (is_null($m) || is_null($y)) {
            $m = $this->month;
            $y = $this->year;
        }
        return strftime('%b', mktime(0, 0, 0, (int)$m, 1, (int)$y));
    }

    /**
     * Checks if a day is a saturday or sunday
     *
     * @deprecated   use isDayOfWeekend() instead
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is saturday or sunday, otherwise false
     */
    function is_day_of_weekend($d = null, $m = null, $y = null) {
        return $this->isDayOfWeekend($d, $m, $y);
    }

    /**
     * Checks if a day is a saturday or sunday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * if ($cal->isDayOfWeekend()) {
     *     echo $cal->toString() . ' is a day of the weekend.';
     * } else {
     *     echo $cal->toString() . ' is not a day of the weekend.';
     * }
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is saturday or sunday, otherwise false
     */
    function isDayOfWeekend($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        if ($this->isSaturday($d, $m, $y) || $this->isSunday($d, $m, $y)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a date represents an expected weekday.
     *
     * Generically used by isSunday(), isMonday(), ...
     *
     * @access   private
     * @param        int     $d          day
     * @param        int     $m          month
     * @param        int     $y          year
     * @param        int     $expected   expected day-num
     * @return       boolean true if is the expected day, otherwise false
     */
    function _isExpectedWeekday($d, $m, $y, $expected) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        $dayNum = $this->getWeekdayAsNum($d, $m, $y);
        if ($dayNum == $expected) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a date is a sunday
     *
     * @deprecated   use isSunday() instead
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     */
    function is_sunday($d = null, $m = null, $y = null) {
        return $this->isSunday($d, $m, $y);
    }

    /**
     * Checks if a date is a sunday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * echo 'isMonday(): '; var_dump($cal->isMonday()); echo '<br />';
     * echo 'isTuesday(): '; var_dump($cal->isTuesday()); echo '<br />';
     * echo 'isWednesday(): '; var_dump($cal->isWednesday()); echo '<br />';
     * echo 'isThursday(): '; var_dump($cal->isThursday()); echo '<br />';
     * echo 'isFriday(): '; var_dump($cal->isFriday()); echo '<br />';
     * echo 'isSaturday(): '; var_dump($cal->isSaturday()); echo '<br />';
     * echo 'isSunday(): '; var_dump($cal->isSunday()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isMonday(), isTuesday(), isWednesday(), isThursday(), isFriday(), isSaturday()
     */
    function isSunday($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        return $this->_isExpectedWeekday($d, $m, $y, 0);
    }

    /**
     * Checks if a date is a saturday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * echo 'isMonday(): '; var_dump($cal->isMonday()); echo '<br />';
     * echo 'isTuesday(): '; var_dump($cal->isTuesday()); echo '<br />';
     * echo 'isWednesday(): '; var_dump($cal->isWednesday()); echo '<br />';
     * echo 'isThursday(): '; var_dump($cal->isThursday()); echo '<br />';
     * echo 'isFriday(): '; var_dump($cal->isFriday()); echo '<br />';
     * echo 'isSaturday(): '; var_dump($cal->isSaturday()); echo '<br />';
     * echo 'isSunday(): '; var_dump($cal->isSunday()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isMonday(), isTuesday(), isWednesday(), isThursday(), isFriday(), isSunday()
     */
    function isSaturday($d = null, $m = null, $y = null) {
        return $this->_isExpectedWeekday($d, $m, $y, 6);
    }

    /**
     * Checks if a date is a friday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * echo 'isMonday(): '; var_dump($cal->isMonday()); echo '<br />';
     * echo 'isTuesday(): '; var_dump($cal->isTuesday()); echo '<br />';
     * echo 'isWednesday(): '; var_dump($cal->isWednesday()); echo '<br />';
     * echo 'isThursday(): '; var_dump($cal->isThursday()); echo '<br />';
     * echo 'isFriday(): '; var_dump($cal->isFriday()); echo '<br />';
     * echo 'isSaturday(): '; var_dump($cal->isSaturday()); echo '<br />';
     * echo 'isSunday(): '; var_dump($cal->isSunday()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isMonday(), isTuesday(), isWednesday(), isThursday(), isSaturday(), isSunday()
     */
    function isFriday($d = null, $m = null, $y = null) {
        return $this->_isExpectedWeekday($d, $m, $y, 5);
    }

    /**
     * Checks if a date is a thursday
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isMonday(), isTuesday(), isWednesday(), isFriday(), isSaturday(), isSunday()
     */
    function isThursday($d = null, $m = null, $y = null) {
        return $this->_isExpectedWeekday($d, $m, $y, 4);
    }

    /**
     * Checks if a date is a wednesday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * echo 'isMonday(): '; var_dump($cal->isMonday()); echo '<br />';
     * echo 'isTuesday(): '; var_dump($cal->isTuesday()); echo '<br />';
     * echo 'isWednesday(): '; var_dump($cal->isWednesday()); echo '<br />';
     * echo 'isThursday(): '; var_dump($cal->isThursday()); echo '<br />';
     * echo 'isFriday(): '; var_dump($cal->isFriday()); echo '<br />';
     * echo 'isSaturday(): '; var_dump($cal->isSaturday()); echo '<br />';
     * echo 'isSunday(): '; var_dump($cal->isSunday()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isMonday(), isTuesday(), isThursday(), isFriday(), isSaturday(), isSunday()
     */
    function isWednesday($d = null, $m = null, $y = null) {
        return $this->_isExpectedWeekday($d, $m, $y, 3);
    }

    /**
     * Checks if a date is a tuesday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * echo 'isMonday(): '; var_dump($cal->isMonday()); echo '<br />';
     * echo 'isTuesday(): '; var_dump($cal->isTuesday()); echo '<br />';
     * echo 'isWednesday(): '; var_dump($cal->isWednesday()); echo '<br />';
     * echo 'isThursday(): '; var_dump($cal->isThursday()); echo '<br />';
     * echo 'isFriday(): '; var_dump($cal->isFriday()); echo '<br />';
     * echo 'isSaturday(): '; var_dump($cal->isSaturday()); echo '<br />';
     * echo 'isSunday(): '; var_dump($cal->isSunday()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isMonday(), isWednesday(), isThursday(), isFriday(), isSaturday(), isSunday()
     */
    function isTuesday($d = null, $m = null, $y = null) {
        return $this->_isExpectedWeekday($d, $m, $y, 2);
    }

    /**
     * Checks if a date is a monday
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * // checks if the day is a sunday or saturday
     * echo 'isMonday(): '; var_dump($cal->isMonday()); echo '<br />';
     * echo 'isTuesday(): '; var_dump($cal->isTuesday()); echo '<br />';
     * echo 'isWednesday(): '; var_dump($cal->isWednesday()); echo '<br />';
     * echo 'isThursday(): '; var_dump($cal->isThursday()); echo '<br />';
     * echo 'isFriday(): '; var_dump($cal->isFriday()); echo '<br />';
     * echo 'isSaturday(): '; var_dump($cal->isSaturday()); echo '<br />';
     * echo 'isSunday(): '; var_dump($cal->isSunday()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is the expected day, otherwise false
     * @see          isTuesday(), isWednesday(), isThursday(), isFriday(), isSaturday(), isSunday()
     */
    function isMonday($d = null, $m = null, $y = null) {
        return $this->_isExpectedWeekday($d, $m, $y, 1);
    }

    /**
     * Get the last day of a month
     *
     * If you specify a month and a year, the method can be called statically.
     * If you don't specify any param, then the object's current attributes will be used.
     *
     * @deprecated   use getLastDayOfMonth() instead
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $m      month
     * @param        int     $y      year
     * @return       int     number of the month's last day
     */
    function get_last_day_of_month($m = null, $y = null) {
        return $this->getLastDayOfMonth($m, $y);
    }

    /**
     * Get the last day of a month
     *
     * If you specify a month and a year, the method can be called statically.
     * If you don't specify any param, then the object's current attributes will be used.
     * 
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * echo 'last day of the month: ' . $cal->getLastDayOfMonth() . '<br />';
     * echo 'is last day of the month: '; echo var_dump($cal->isLastDayOfMonth()); echo '<br />';
     * echo 'is today: '; echo var_dump($cal->isToday()); echo '<br />';
     * echo 'is future: '; echo var_dump($cal->isFuture()); echo '<br />';
     * echo 'is ago: '; echo var_dump($cal->isAgo()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $m      month
     * @param        int     $y      year
     * @return       int     number of the month's last day
     */
    function getLastDayOfMonth($m = null, $y = null) {
        if (is_null($m) && is_null($y)) {
            $m = $this->month;
            $y = $this->year;
        }
        return intval(date('t', mktime(0, 0, 0, (int)$m, 1, (int)$y)));
    }

    /**
     * Get the last day of a month
     *
     * If you specify a month and a year, the method can be called statically.
     * If you don't specify any param, then the object's current attributes will be used.
     * 
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * echo 'last day of the month: ' . $cal->getLastDayOfMonth() . '<br />';
     * echo 'is last day of the month: '; echo var_dump($cal->isLastDayOfMonth()); echo '<br />';
     * echo 'is today: '; echo var_dump($cal->isToday()); echo '<br />';
     * echo 'is future: '; echo var_dump($cal->isFuture()); echo '<br />';
     * echo 'is ago: '; echo var_dump($cal->isAgo()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @return       boolean     true if actual object-attributes represent the last day of the month, otherwise false
     */
    function isLastDayOfMonth($m = null, $y = null) {
        if (is_null($m) && is_null($y)) {
            $m = $this->month;
            $y = $this->year;
        }
        if ($this->day == $this->getLastDayOfMonth($m, $y)) {
            return true;
        }
        return false;
    }

    /**
     * Check if a date represents todays day
     *
     * @deprecated   use isToday() instead
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is today, otherwise false
     */
    function is_today($d = null, $m = null, $y = null) {
        return $this->isToday($d, $m, $y);
    }

    /**
     * Check if a date is representing todays day. 
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * echo 'last day of the month: ' . $cal->getLastDayOfMonth() . '<br />';
     * echo 'is last day of the month: '; echo var_dump($cal->isLastDayOfMonth()); echo '<br />';
     * echo 'is today: '; echo var_dump($cal->isToday()); echo '<br />';
     * echo 'is future: '; echo var_dump($cal->isFuture()); echo '<br />';
     * echo 'is ago: '; echo var_dump($cal->isAgo()); echo '<br />';
     * </code>
     *
     * @static       can be statically called if all params are provided
     * @access       public
     * @param        int     $d  day
     * @param        int     $m  month
     * @param        int     $y  year
     * @return       boolean true if is today, otherwise false
     */
    function isToday($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        $dateTs = mktime(0, 0, 0, (int)$m, (int)$d, (int)$y, 0);
        $todayTs = mktime(0, 0, 0, intval(date('n')), intval(date('j')), intval(date('Y')));
        if ($dateTs == $todayTs) {
            return true;
        }
        return false;
    }

    /**
     * Returns the object's current date attributes as string
     *
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with actual date
     * $cal    =& new tgcCalendar();
     * // print the object's date-setting
     * echo $cal->toString() . '<br /><br />';
     * </code>
     *
     * @access   public
     * @param    string      $format     format-string
     * @return   string      string-representation of object's current date-attributes
     */
    function toString($format = '%d.%m.%Y') {
        return strftime($format, mktime(0, 0, 0, (int)$this->month, (int)$this->day, (int)$this->year, 0));
    }

    /**
     * Set the first day for the tabular output
     *
     * sunday = 0, monday = 1, ..., saturday = 6
     *
     * @access   public
     * @param    int         $day    day
     * @see      getCalendarMarkup()
     */
    function setFirstDay($day) {
        $this->_firstDay = $day;
    }

    /**
     * Checks if a day is a future day
     * 
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * echo 'last day of the month: ' . $cal->getLastDayOfMonth() . '<br />';
     * echo 'is last day of the month: '; echo var_dump($cal->isLastDayOfMonth()); echo '<br />';
     * echo 'is today: '; echo var_dump($cal->isToday()); echo '<br />';
     * echo 'is future: '; echo var_dump($cal->isFuture()); echo '<br />';
     * echo 'is ago: '; echo var_dump($cal->isAgo()); echo '<br />';
     * </code>
     *
     * @static   can be statically called if all params are provided
     * @access   public
     * @param    int     $d  day
     * @param    int     $m  month
     * @param    int     $y  year
     * @return   boolean true if day is lying in the future, otherwise false
     */
    function isFuture($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        $dateTs = mktime(0, 0, 0, (int)$m, (int)$d, (int)$y);
        $todayTs = mktime(0, 0, 0, intval(date('n')), intval(date('j')), intval(date('Y')));
        if ($dateTs > $todayTs) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a date is lying in the past
     * 
     * <code>
     * require_once 'include/tgcCalendar.php';
     * 
     * // creates an object with specified date
     * $cal    =& new tgcCalendar(9, 9, 1980);
     * 
     * echo 'last day of the month: ' . $cal->getLastDayOfMonth() . '<br />';
     * echo 'is last day of the month: '; echo var_dump($cal->isLastDayOfMonth()); echo '<br />';
     * echo 'is today: '; echo var_dump($cal->isToday()); echo '<br />';
     * echo 'is future: '; echo var_dump($cal->isFuture()); echo '<br />';
     * echo 'is ago: '; echo var_dump($cal->isAgo()); echo '<br />';
     * </code>
     *
     * @static   can be statically called if all params are provided
     * @access   public
     * @param    int     $d  day
     * @param    int     $m  month
     * @param    int     $y  year
     * @return   boolean true if day is lying in the past, otherwise false
     */
    function isAgo($d = null, $m = null, $y = null) {
        if (is_null($d) || is_null($m) || is_null($y)) {
            $d = $this->day;
            $m = $this->month;
            $y = $this->year;
        }
        $dateTs = mktime(0, 0, 0, (int)$m, (int)$d, (int)$y);
        $todayTs = mktime(0, 0, 0, intval(date('n')), intval(date('j')), intval(date('Y')));
        if ($dateTs < $todayTs) {
            return true;
        }
        return false;
    }

    /**
     * Get the HTML-markup for a tabluar calendar output
     *
     * @access   public
     * @param    boolean $showArrows true if you want navigation arrows, otherwise false
     * @return   string  HTML-markup on success, otherwise an empty string
     * @see      setCalLinkUrl(), setFirstDay()
     * @example  ../demo.php     How tu use this method?!
     */
    function getCalendarMarkup($showArrows = true) {
        $this->_executeCallback();

        // needs patTemplate
        require_once sprintf('%s%spatTemplate.php', tgcCALENDAR_INCLUDEDIR, DIRECTORY_SEPARATOR);

        $d = 1;
        // set object-attributes from request-vars, if available
        if (!is_null($this->_vars) && isset($this->_vars['m']) && isset($this->_vars['y'])) {
            $this->month = $this->_vars['m'];
            $this->year = $this->_vars['y'];
        }
        $m = $this->month;
        $y = $this->year;

        // initialize patTemplate
        $tmpl = new patTemplate('html');
        $tmpl->setBasedir(tgcCALENDAR_TEMPLATEDIR);
        $tmpl->readTemplatesFromFile($this->_templateFile);
        $tmpl->addGlobalVar('IMAGEDIR', tgcCALENDAR_IMAGEDIR);

        $tmpl->addVar('tgcCalendar', 'STYLEDIR', tgcCALENDAR_STYLEDIR);

        // tableheader
        $tmpl->addVar('tgcCalendar', 'ACTUAL_MONTH', $this->getMonthAsString($m, $y));
        $tmpl->addVar('tgcCalendar', 'ACTUAL_YEAR', $this->getYear());
        if ($showArrows) {
            $tmpl->addVar('tgcCalendar_head_arrow_left', 'SHOW_ARROW', 'yes');
            $prevMonth = $this->getPrevMonth();
            $prevYear = $y;
            if ($prevMonth == 12) {
                --$prevYear;
            }
            $getVars = sprintf('m=%d&y=%d', $prevMonth, $prevYear);
            $tmpl->addVar('tgcCalendar_head_arrow_left', 'URL', sprintf('%s?%s', $this->_arrowUrl, $getVars));
            $tmpl->addVar('tgcCalendar_head_arrow_right', 'SHOW_ARROW', 'yes');
            $nextMonth = $this->getNextMonth();
            $nextYear = $y;
            if ($nextMonth == 1) {
                ++$nextYear;
            }
            $getVars = sprintf('m=%d&y=%d', $nextMonth, $nextYear);
            $tmpl->addVar('tgcCalendar_head_arrow_right', 'URL', sprintf('%s?%s', $this->_arrowUrl, $getVars));
        }

        // tableheader weekday-names
        $weekdayNames = $this->_getWeekdayArray();
        $tmpl->addVar('tgcCalendar_weekday_names', 'WEEKDAY', $weekdayNames);

        // tablebody calendar-days
        $format = '%d';
        if (tgcCALENDAR_ZEROFILL) {
            $format = '%02d';
        }
        $firstDayName = $this->getWeekdayAsShortString($d, $m, $y);
        $weekdayCols = array_flip($weekdayNames);
        $firstDayCol = $weekdayCols[$firstDayName];
        $lastDayNum = $this->getLastDayOfMonth($m, $y);
        $lastDayPassed = false;
        while ($d <= $lastDayNum) {
            for ($i = 0; $i < 7; $i++) {
                $style = tgcCalendar::_findStyleClass($d, $m, $y);
                $tmpl->addVar('tgcCalendar_weekdays_col', 'STYLE', $style);
                if (!is_null($this->_callback)) {
                    $tmpl->setAttribute('tgcCalendar_weekdays_colUrlStart', 'visibility', 'visible');
                    $url = sprintf('%s?d=%d&m=%d&y=%d', $this->_arrowUrl, $d, $m, $y);
                    $tmpl->addVar('tgcCalendar_weekdays_colUrlStart', 'STYLE', $style);
                    $tmpl->addVar('tgcCalendar_weekdays_colUrlStart', 'WEEKDAY_URL', $url);
                    $tmpl->setAttribute('tgcCalendar_weekdays_colUrlEnd', 'visibility', 'visible');
                }
                switch ($d) {
                    case 1:
                        if ($i < $firstDayCol) {
                            $tmpl->addVar('tgcCalendar_weekdays_col', 'isEmpty', 'yes');
                        }
                        else {
                            $tmpl->addVar('tgcCalendar_weekdays_col', 'isEmpty', 'no');
                            $tmpl->addVar('tgcCalendar_weekdays_col', 'WEEKDAY', sprintf($format, $d++));
                        }
                        break;

                    case $lastDayNum:

                        $tmpl->addVar('tgcCalendar_weekdays_col', 'isEmpty', 'no');
                        $tmpl->addVar('tgcCalendar_weekdays_col', 'WEEKDAY', sprintf($format, $d++));
                        $lastDayPassed = true;
                        break;

                    default:
                        if (!$lastDayPassed) {
                            $tmpl->addVar('tgcCalendar_weekdays_col', 'isEmpty', 'no');
                            $tmpl->addVar('tgcCalendar_weekdays_col', 'WEEKDAY', sprintf($format, $d++));
                        }
                        else {
                            $tmpl->addVar('tgcCalendar_weekdays_col', 'isEmpty', 'yes');
                        }
                        break;
                }
                $tmpl->parseTemplate('tgcCalendar_weekdays_col', 'a');
                $tmpl->clearTemplate('tgcCalendar_weekdays_colUrlStart');
            }
            $tmpl->parseTemplate('tgcCalendar_weekdays_row', 'a');
            $tmpl->clearTemplate('tgcCalendar_weekdays_col');
        }

        return $tmpl->getParsedTemplate('tgcCalendar');
    }

    /**
     * Finds the appropriate style for a day
     *
     * @static
     * @access   private
     * @param    int     $d  day
     * @param    int     $m  month
     * @param    int     $y  year
     * @return   string  name of the appropriate style-class
     */
    function _findStyleClass($d, $m, $y) {
        arsort($this->_stylePriority);
        // map style-prio array's indexes to function-names
        foreach (array_keys($this->_stylePriority) as $propName) {
            switch ($propName) {
                case 'today':
                    if (tgcCalendar::isToday($d, $m, $y)) {
                        return 'tgcCalendar_tbody_today';
                    }
                    break;
                case 'sunday':
                    if (tgcCalendar::isSunday($d, $m, $y)) {
                        return 'tgcCalendar_tbody_sunday';
                    }
                    break;
                case 'weekend':
                    if (tgcCalendar::isDayOfWeekend($d, $m, $y)) {
                        return 'tgcCalendar_tbody_weekend';
                    }
                    break;
                case 'future':
                    if (tgcCalendar::isFuture($d, $m, $y)) {
                        return 'tgcCalendar_tbody_future';
                    }
                    break;
                case 'ago':
                    if (tgcCalendar::isAgo($d, $m, $y)) {
                        return 'tgcCalendar_tbody_ago';
                    }
                    break;
            }
        }
    }

    /**
     * Set the template-filename that will be used for generating tabular calendar output
     *
     * You can use this method, if you want to let the {@link getCalendarMarkup()} method use another template-file.
     *
     * @access   public
     * @param    string      $filename   filename
     * @see      getCalendarMarkup()
     */
    function setTemplateFile($filename) {
        $this->_templateFile = $filename;
    }

    /**
     * Builds the array of weekday-names that can be applied to the template (cares about first-day)
     *
     * @access   private
     * @return   array       weekday-names
     */
    function _getWeekdayArray() {
        $weekdays = tgcCalendar::_getLocaleWeekdayAbbreviations();
        $wdOrdered = array();

        $wdIndex = $this->_firstDay;
        for ($i = 0; $i < 7; $i++) {
            $wdOrdered[$i] = $weekdays[$wdIndex++];
            if ($wdIndex > 6) {
                $wdIndex = 0;
            }
        }
        return $wdOrdered;
    }

    /**
     * Builds an array that contains the abbreviations of weekday from sunday till saturday
     *
     * Generates an array that contains the weekday's abbreviations. 
     * Uses a dummy_date: 9.9.2001 as it's a sunday so an array('Sun','Mon',...) is generated.
     *
     * @static
     * @access   private
     * @return   array       abbreviations
     */
    function _getLocaleWeekdayAbbreviations() {
        $weekdayNames = array();
        for ($i = 0; $i < 7; $i++) {
            array_push($weekdayNames, tgcCalendar::getWeekdayAsShortString(9 + $i, 9, 2001));
        }
        return $weekdayNames;
    }

    /**
     * Register a callback function that will be called when someone clicks on a calendar day.
     *
     * If no callback function is registered, the calendar-days won't be clickable.
     * You can register a simple function or an object's method as callback. It will be called
     * if someone is clicking a day-link in the tabular output. It get's the values (day, month, year) 
     * of the clicked date to work with them.
     *
     * @access   public
     * @param    string  $name   name of the callback-function
     * @see      getCalendarMarkup()
     */
    function registerCallback($callback) {
        $this->_callback = $callback;
    }

    /**
     * Collects the calendar-request variables from the environment
     *
     * @access   private
     * @param    array       $vars   var-names
     * @return   boolean     true on success, otherwise false
     */
    function _collectRequestVars($vars) {
        if (!is_array($vars)) {
            return false;
        }

        if (!isset($_GET)) {
            global $HTTP_GET_VARS;
            $request = $HTTP_GET_VARS;
        }
        else {
            $request = $_GET;
        }

        $values = array();
        foreach ($vars as $varname) {
            if (!isset($request[$varname])) {
                return false;
            }
            $values[$varname] = $request[$varname];
        }
        $this->_vars = $values;
        return true;
    }

    /**
     * Executes the callback, if one is registered
     *
     * @access   private
     */
    function _executeCallback() {
        if (!is_null($this->_callback)) {
            if (is_array($this->_callback)) {
                if (!method_exists($this->_callback[0], $this->_callback[1])) {
                    return false;
                }
            }
            else {
                if (!function_exists($this->_callback)) {
                    return false;
                }
            }

            $collected = $this->_collectRequestVars(array(
                'd',
                'm',
                'y'));
            if ($collected) {
                call_user_func($this->_callback, $this->_vars['d'], $this->_vars['m'], $this->_vars['y']);
            }
        }
        return true;
    }

    /**
     * Set the priority for the style-classes
     *
     * @access   public
     * @param    array   $prios  array that describes the priorities
     * @return   boolean true on success, otherwise false
     */
    function setStyleClassPriorites($prios) {
        if (isset($prios['ago']) && isset($prios['today']) && isset($prios['future'])) {
            $this->_stylePriority = $prios;
            return true;
        }
        return false;
    }

    /**
     * Get the UNIX timestamp of the current date
     *
     * @access   public
     * @return   int     UNIX timestamp
     */
    function getTimestamp() {
        return mktime(0, 0, 0, (int)$this->month, (int)$this->day, (int)$this->year);
    }

    /**
     * Set the object's day, month and year from a timestamp
     *
     * @access   public
     * @param    int     $timestamp  UNIX timestamp
     */
    function setFromTimestamp($timestamp) {
        $this->day = intval(date('j', $timestamp));
        $this->month = intval(date('n', $timestamp));
        $this->year = intval(date('Y', $timestamp));
    }

    public static function date_to_time($datum) { //YYYY-mm-dd
        list($y, $m, $d) = explode('-', $datum);
        return mktime(0, 0, 0, (int)$m, (int)$d, (int)$y);
    }

    public static function time2time($time) { //H:i:s
        list($H, $i, $s) = explode(':', $time);
        return mktime($H, $i, $s, date('m'), date('d'), date('Y'));
    }

    public static function printMenge($menge) {
        $menge = sprintf("%01.2f", $menge);
        $parts = explode(".", $menge);
        if (($parts[1] * 1) == 0)
            return $parts[0];
        return str_replace(".", ",", $menge);
    }

    function timeDurationInMin($timestart, $timeend) { //H:i:s
        $min = ($this->time2time($timeend) - $this->time2time($timestart)) / 60;
        return $min;
    }

    function timeDurationInHours($timestart, $timeend) { //H:i:s
        $hour = ($this->time2time($timeend) - $this->time2time($timestart)) / 60 / 60;
        return $hour;
    }

    public static function plusHours($time, $addHours = 1) { //H:i:s
        list($H, $i, $s) = explode(':', $time);
        if (($H + $addHours) >= 24)
            $H = ($H + $addHours) - 24;
        else
            $H++;
        return $H . ':' . $i . ':' . $s;
    }

    public static function date2DateGerman($date_us) { //YYYY-mm-dd
        list($Y, $m, $d) = explode('-', $date_us);
        return $d . '.' . $m . '.' . $Y;
    }

    function convertDateTime2Array($datetime) {
        list($date, $time) = explode(' ', $datetime);
        list($H, $i, $s) = explode(':', $time);
        list($Y, $m, $d) = explode('-', $date);
        $DA = array();
        $DA['date']['date'] = $date;
        $DA['date']['Y'] = $Y;
        $DA['date']['m'] = $m;
        $DA['date']['d'] = $d;

        $DA['time']['time'] = $time;
        $DA['time']['H'] = $H;
        $DA['time']['i'] = $i;
        $DA['time']['s'] = $s;


        if (strlen($H) == 1)
            $H = '0' . $H;
        $DA['timeint'] = mktime($H, $i, $s, (int)$m, (int)$d, (int)$Y);
        $DA['date_ger'] = date('d.m.Y', $DA['timeint']);
        $DA['date_us'] = date('Y-m-d', $DA['timeint']);
        $DA['datime_us'] = $datetime;
        $DA['datime_ger'] = date('d.m.Y H:i:s', $DA['timeint']);
        $DA['weekday'] = $this->getWeekdayAsString($DA['date']['d'], $DA['date']['m'], $DA['date']['Y']);
        $DA['time']['formatedtime'] = date('H:i', $DA['timeint']);
        return $DA;
    }

    public static function addTime2Date($sql_date, $add_year = 0, $add_month = 0, $add_day = 0, $outputformat = 'Y-m-d') { //YYYY-MM-DD
        $publictime = str_replace("-", "", $sql_date);
        $sec = substr($publictime, 12, 2);
        $min = substr($publictime, 10, 2);
        $hour = substr($publictime, 8, 2);
        $day = substr($publictime, 6, 2);
        $month = substr($publictime, 4, 2);
        $year = substr($publictime, 0, 4);
        return date($outputformat, mktime(0, 0, 0, $month + $add_month, $day + $add_day, $year + $add_year));
    }

    public static function date2time($datum) { //YYYY-mm-dd
        list($y, $m, $d) = explode('-', $datum);
        return mktime(0, 0, 0, $m, $d, $y);
    }

}
