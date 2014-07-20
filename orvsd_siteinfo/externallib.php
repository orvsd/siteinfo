<?php
/**
 * ORVSD External Web Service
 *
 * @package    orvsd
 * @copyright  2012 OSU Open Source Lab
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file was taken directly from createcourse with changes made to the best
// of my (Elijah Voigt) ability. Variable names have been changed and stripped
// from the original version.
// This is not a functional plugin as of now.

require_once($CFG->libdir . "/lib.php");
require_once($CFG->libdir . "/externallib.php");

class local_orvsd_site_info_external extends external_api {

  /**
   * Returns description of method parameters
   * @return external_function_parameters
   */
  public static function site_info_parameters() {
    return new external_function_parameters(
      array(
        'time'    => new external_value(PARAM_TEXT, 'Timeframe for report')
      )
    );
  }

  /**
   * Returns number of users on a given moodle site
   * @ return_number_of_users
   */
  public static function return_num_users( $time ) {

    global $CFG, $USER, $DB;
    $status = true;

    $serial = $courseid;

    $param_array = array(
      'time'  => $time,
    );

    $params = self::validate_parameters(self::install_course_parameters(), $param_array);

    $context = get_context_instance(CONTEXT_USER, $USER->id);
    self::validate_context($context);

    if (!has_capability('moodle/user:viewdetails',$context)) {
      throw new moodle_exception('cannotviewprofile');
    }

    return $params['time'] . $USER->firstname ;
  }

  public static function hello_world_returns() {
    return new external_value(PARAM_TEXT, 'Returns moodle site information for a given period of time');
  }
  // return string json_encode($array_value)
}
