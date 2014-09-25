<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * siteinfo plugin function library 
 *
 * @package    local
 * @subpackage orvsd_siteinfo
 * @copyright  2013 OSU Open Source Lab (http://osuosl.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Get the site's info and return as json string
 * @param timeframe : user count within the last `timeframe` seconds
 * @return string
 */
function orvsd_siteinfo_get_site_info($timeframe) {
    global $CFG, $SITE;

    // teachers = regular and non-editing teachers
    $teachers = orvsd_siteinfo_usercount("teacher",null);
    
    $courselist_string = orvsd_siteinfo_courselist();

    $siteinfo = new stdClass();
    $siteinfo->baseurl      = $CFG->wwwroot;
    $siteinfo->basepath     = $CFG->dirroot;
    $siteinfo->sitename     = $SITE->fullname;
    $siteinfo->sitetype     = "moodle";
    $siteinfo->siteversion  = $CFG->version;
    $siteinfo->siterelease  = $CFG->release;
    $siteinfo->location     = php_uname('n'); 
    $siteinfo->adminemail   = $CFG->supportemail;
    $siteinfo->totalusers   = orvsd_siteinfo_usercount(null, null);
    $siteinfo->adminusers   = intval($CFG->siteadmins);
    $siteinfo->teachers     = $teachers;
    $siteinfo->activeusers  = orvsd_siteinfo_usercount(null, $timeframe);
    $siteinfo->totalcourses = count($courselist);
    $siteinfo->courses      = $courselist_string;
    $siteinfo->timemodified = time();
    
    /* We ignore using a database and just get the site info
     * and format it to json and just return that
     */

    return json_encode($siteinfo);

    //$DB->insert_record('siteinfo', $siteinfo);

    //return true;
}

/**
 * generate list of courses installed here
 * @return array
 */
function orvsd_siteinfo_courselist() {
  global $CFG, $DB;
  // get all course idnumbers
  $table = 'coursemeta';
  $conditions = null;
  $params = null;
  $sort = 'courseid';
  $fields = 'courseid,shortname,serial';
  $courses = $DB->get_records($table,$conditions,$sort,$fields);
//  print_r($courses);
  $course_list = array();
  foreach($courses as $course) {
      $shortname = preg_replace('/"/', '', $course->shortname);
      $shortname = preg_replace("/'/", " ", $shortname);
      $enrolled = orvsd_siteinfo_get_enrolments($course->courseid);
      $course_list[] = '{"serial":"' . $course->serial . 
                        '","shortname":"' . htmlentities($shortname) . 
                        '","enrolled":' . $enrolled . '}';
  }

    $courselist_string = '';

    if (count($course_list) > 0) {
     $courselist_string = "[" . implode(',', $course_list) . "]";
    }

    return $courselist_string;
}

/**
 * Get student enrollments for this course 
 * @return array
 */
function orvsd_siteinfo_get_enrolments($courseid) {
  global $CFG, $DB;

  $sql = "select count(userid) 
          from mdl_enrol
          left join mdl_user_enrolments
            on mdl_user_enrolments.enrolid=mdl_enrol.id
          where mdl_enrol.roleid=5
          and mdl_enrol.courseid=$courseid";
  
  $params = null;
  return $DB->get_field_sql($sql,$params, IGNORE_MISSING);
}



/**
 * Initialise the siteinfo table with this site's info
 * @return bool
 */
function orvsd_siteinfo_init_db() {
    global $CFG, $DB, $SITE;

    // timeframe - default is within the last month, 
    // i.e time() - 2592000 seconds (30 days)
    // other options:
    // in the last week = time() - 604800
    $timeframe = time() - 2592000;
    
    // teachers = regular and non-editing teachers
    $teachers = orvsd_siteinfo_usercount("teacher",null);
    
    $courselist_string = orvsd_siteinfo_courselist();

    $siteinfo = new stdClass();
    $siteinfo->baseurl      = $CFG->wwwroot;
    $siteinfo->basepath     = $CFG->dirroot;
    $siteinfo->sitename     = $SITE->fullname;
    $siteinfo->sitetype     = "moodle";
    $siteinfo->siteversion  = $CFG->version;
    $siteinfo->siterelease  = $CFG->release;
    $siteinfo->location     = php_uname('n'); 
    $siteinfo->adminemail   = $CFG->supportemail;
    $siteinfo->totalusers   = orvsd_siteinfo_usercount(null, null);
    $siteinfo->adminusers   = intval($CFG->siteadmins);
    $siteinfo->teachers     = $teachers;
    $siteinfo->activeusers  = orvsd_siteinfo_usercount(null, $timeframe);
    $siteinfo->totalcourses = count($courselist);
    $siteinfo->courses      = $courselist_string;
    $siteinfo->timemodified = time();
    
    /* We need to ignore using a database and just get the site info
     * and format it to a json format and just return that.
     */


    $DB->insert_record('siteinfo', $siteinfo);

    return true;
}

/**
 * Update the siteinfo table with this site's info
 * this will get called on certain events, see events.php
 * @return bool
 */
function orvsd_siteinfo_update_db() {
    global $CFG, $DB, $SITE;
    // timeframe - default is within the last month, 
    // i.e time() - 2592000 seconds (30 days)
    // other options:
    // in the last week = time() - 604800
    $timeframe = time() - 2592000;
    
    // teachers = regular and non-editing teachers
    $teachers = orvsd_siteinfo_usercount("teacher",null);
    
    $courselist_string = orvsd_siteinfo_courselist();

    $siteinfo = new stdClass();
    $siteinfo->id           = 1;
    $siteinfo->baseurl      = $CFG->wwwroot;
    $siteinfo->basepath     = $CFG->dirroot;
    $siteinfo->sitename     = $SITE->fullname;
    $siteinfo->sitetype     = "moodle";
    $siteinfo->siteversion  = $CFG->version;
    $siteinfo->siterelease  = $CFG->release;
    $siteinfo->location     = php_uname('n');
    $siteinfo->adminemail   = $CFG->supportemail;
    $siteinfo->totalusers   = orvsd_siteinfo_usercount(null, null);
    $siteinfo->adminusers   = intval($CFG->siteadmins);
    $siteinfo->teachers     = $teachers;
    $siteinfo->activeusers  = orvsd_siteinfo_usercount(null, $timeframe);
    $siteinfo->totalcourses = count($courselist);
    $siteinfo->courses      = $courselist_string;
    $siteinfo->timemodified = time();

    try {
        $DB->update_record('siteinfo', $siteinfo);
    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
        return false;
    }
    return true;  
}

/**
 * Count users
 * @return int
 */
function orvsd_siteinfo_usercount($role="none", $timeframe=null) {
    global $CFG, $DB;

    switch ($role) {
      case "teacher":
        $role_condition = "IN (3,4)";
        break;
      case "manager":
        $role_condition = "= 1";
        break;
      case "course_creator":
        $role_condition = "= 2";
        break;
      case "student":
        $role_condition = "= 5";
        break;
      case "guest":
        $role_condition = "= 6";
        break;
      case "authed":
        $role_condition = "= 7";
        break;
      case "frontpage":
        $role_condition = "= 8";
        break;
      default:
        $role = false;
    }

    if ($timeframe) {
      //sql += (append WHERE clause to sql to limit by activity date)
      $where = "AND mdl_user.lastaccess > $timeframe";
    } else {
      $where = '';
    }

    if($role) {
      $sql = "SELECT COUNT(DISTINCT userid)
              FROM mdl_role_assignments
              LEFT JOIN mdl_user
              ON mdl_user.id = mdl_role_assignments.userid
              WHERE mdl_role_assignments.roleid $role_condition
              $where";

    } else {
      $sql = "SELECT COUNT(*) 
                FROM mdl_user
               WHERE mdl_user.deleted = 0
               AND mdl_user.confirmed = 1
               $where";
    }

    $count = $DB->count_records_sql($sql, null);

    return intval($count);
}

// The following functions are taken directly from orvsd_installcourse's lib.php
 
function orvsd_siteinfo_init() {
  global $CFG, $DB;

  $event_data = new stdClass();
  $event_data->modulename = 'ORVSD';

  orvsd_siteinfo_update($event_data);
}

function orvsd_siteinfo_update($event_data) {
    global $CFG, $DB;

    $ws_config = $DB->get_record('config', array('name'=>'enablewebservices'));
    $protocols_config = $DB->get_record('config', array('name'=>'webserviceprotocols'));

    if ($ws_config->value == 0) {
        echo "Web Service is off turning it on now...";
        $ws_config->value =1;
        $success = $DB->update_record('config', $ws_config);
        if ($success) {
            echo "Success!<br>";
        } else {
            echo "Failed!<br>";
        }
    }

    if (!$protocols_config) {
        $protocols_config = new stdClass();
        $protocols_config->name = 'webserviceprotocols';
        $protocols_config->value = 'rest';
        echo "Web Services REST protocol is not enabled, enabling now...";
        $success = $DB->insert_record('config', $protocols_config);
        if ($success) {
            echo "Success!<br>";
        } else {
            echo "Failed!<br>";
        }
    } else {
        if(strpos($protocols_config->value, "rest") === false) {

            echo "Web Services REST protocol is not enabled, enabling now...";
            $protocols_config->value .= ',rest';
            $success = $DB->update_record('config', $protocols_config);

            if ($success) {
                echo "Success!<br>";
            } else {
                echo "Failed!<br>";
            }
        }
    }

    $service_id = $DB->get_field('external_services',
      'id', array('component'=>'local_orvsd'), IGNORE_MISSING);

    if($service_id) {
        echo "Site Info web service is already installed, updating... <br>";
        $token_id = $DB->get_field('external_tokens',
            'id', array('externalserviceid'=>$service_id), IGNORE_MISSING);
    } else {
        echo "Site Info web service is not already installed, installing... <br>";
        $token_id = false;
    }

    $external_token = new stdClass();
    $external_token->token = "13f6df8a8b66742e02f7b3791710cf84";
    $external_token->tokentype = 0;
    $external_token->userid = 2;
    $external_token->contextid = 1;
    $external_token->creatorid = 2;
    $external_token->iprestriction = "140.211.167/31,140.211.5.0/24,10.0.0.0/8,127.0.0.1";
    //
    $external_token->validuntil = 0;
    $external_token->timecreated = time();

    if($service_id) {
        if($token_id) {
            echo "Updating Create Course token for user Admin... <br>";
            $external_token->externalserviceid = $service_id;
            $external_token->id = $token_id;

            try {
                $DB->update_record('external_tokens', $external_token);
            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "<br>";
                return false;
            }
        } else {
            echo "Installing Create Course token for use Admin... <br>";
            $external_token->externalserviceid = $service_id;
            try {
                $DB->insert_record('external_tokens', $eternal_token);
            } catch (Exception $e) {
                return false;
            }
        }

    } else {
        $tmp = $DB->get_records_sql('SHOW TABLE STATUS WHERE name = "mdl_external_services"', null); 

        $service_id = $tmp['mdl_external_services']->auto_increment;
        $external_token->externalserviceid = $service_id;
        echo "Installing Create course token for user Admin... <br>";

        try {
            $DB->insert_record('external_tokens', $external_token);
        } catch (Exception $e) {
            return false;
        }
    }


    return true;
}
