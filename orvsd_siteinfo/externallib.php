<?php
/**
 * ORVSD External Web Service
 * provides a facility to provide OSL managed moodle site's information
 *
 * @package		  orvsd
 * @copyright	  2012 OSU Open Soruce lab
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/externallib.php");

class local_orvsd_siteinfo_external extends external_api {

  /**
   * Returns description of method parameters
   * @return external_function_parameters
   */
  public static function site_info_parameters() {
    return new external_function_parameters(
      array('datetime'  => new external_value(PARAM_TEXT, 'Count users within the last `n` days', VALUE_DEFAULT, 7))
    );
  }

  /**
   * Returns REST formatted site-info for a given time-period
   * @return string : siteinfo in json format.
   */
  public static function site_info($datetime) {
  	global $CFG, $USER, $DB;
    $datetime *= 86400; // 86400 seconds per day

    // Include the coursecat methods for creating the category
    require_once($CFG->libdir.'/coursecatlib.php');
    $siteinfo = null;

    $param_array = array(
          'datetime' => $datetime
    );
    $params = self::validate_parameters(self::site_info_parameters(), $param_array);

    //Context validation
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    self::validate_context($context);

    // timeframe - default is within the last month, 
    // i.e time() - 2592000 seconds (within the last 30 days)
    // other options:
    // in the last week = time() - 604800
    $siteinfo = orvsd_siteinfo_get_site_info(time() - $datetime);

    if ($siteinfo > 0) {
      return $siteinfo; 
    } else {
      return "Siteinfo not found...";
    }
  }

  public static function get_siteinfo_returns() {
    return new external_value(PARAM_TEXT, 'Site info.');
  }
}
