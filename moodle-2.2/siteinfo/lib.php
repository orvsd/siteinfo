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
 * @subpackage siteinfo
 * @copyright  2012 Kenneth Lett (http://osuosl.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Initialise the siteinfo table with this site's info
 * @return bool
 */

function siteinfo_init_db() {
    global $CFG, $DB, $SITE;

    $siteinfo = new stdClass();
    $siteinfo->baseurl      = $CFG->wwwroot;
    $siteinfo->basepath     = $CFG->dirroot;
    $siteinfo->sitename     = $SITE->fullname;
    $siteinfo->sitetype     = "Moodle";
    $siteinfo->siteversion  = $CFG->version;
    $siteinfo->siterelease  = $CFG->release;
    $siteinfo->adminemail   = $CFG->supportemail;
    $siteinfo->totalusers   = siteinfo_usercount(null, null);
    $siteinfo->adminusers   = intval($CFG->siteadmins);
    $siteinfo->teachers     = 0;
    $siteinfo->activeusers  = 0;
    $siteinfo->totalcourses = 0;
    $siteinfo->timemodified = time();

  //  try {
        $DB->insert_record('siteinfo', $siteinfo);
   // } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
    //    return false;
   // }

    return true;
}

/**
 * Update the siteinfo table with this site's info
 * this will get called on certain events, see events.php
 * @return bool
 */
function siteinfo_update_db() {
    global $CFG, $DB, $SITE;

    $siteinfo = new stdClass();
    $siteinfo->id           = 1;
    $siteinfo->baseurl      = $CFG->wwwroot;
    $siteinfo->basepath     = $CFG->dirroot;
    $siteinfo->sitename     = $SITE->fullname;
    $siteinfo->sitetype     = "Moodle";
    $siteinfo->siteversion  = $CFG->version;
    $siteinfo->siterelease  = $CFG->release;
    $siteinfo->adminemail   = $CFG->supportemail;
    $siteinfo->totalusers   = siteinfo_usercount(null, null);
    $siteinfo->adminusers   = intval($CFG->siteadmins);
    $siteinfo->teachers     = 0;
    $siteinfo->activeusers  = 0;
    $siteinfo->totalcourses = 0;
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
function siteinfo_usercount($role, $timeframe) {
    global $CFG, $DB;
    /* @TODO: add logic to extract the number of users in a particular role
        i.e. teacher, and users who have logged in within some timeframe
    
        if(role) {
            sql = (sql to join the roles table and count the users with this role)
        } else {
            sql = (same as below, just count all the non-deleted users)
        }

        if (timeframe) {
            sql += (append WHERE clause to sql to limit by activity date)
        }
    */

    $sql = "SELECT COUNT(*) 
              FROM mdl_user
             WHERE mdl_user.deleted = 0
               AND mdl_user.confirmed = 1;";

    $count = $DB->count_records_sql($sql, null);

    return intval($count);
}

/**
 * Count courses
 * @return int
 * @TODO: write this function 
 */
function siteinfo_coursecount() {

}