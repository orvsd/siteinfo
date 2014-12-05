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

function xmldb_local_orvsd_siteinfo_upgrade($oldversion = 0) {
    global $CFG, $DB;

    try {
        $DB->delete_records('siteinfo');
    } catch (Exception $e) {
        // Do nothing, continue with the upgrade
    }

    // Look up the service, if it doesn't exist, create it
    $service = $DB->get_record('external_services', array('component'=>'local_orvsd_siteinfo'));

    if (!$service) {

        $tmp = $DB->get_records_sql('SHOW TABLE STATUS WHERE name = "mdl_external_services"');
        $service_id = $tmp['mdl_external_services']->auto_increment;

        $service = new stdClass();
        $service->id = $service_id;
    }

    // Check for a token associated to the siteadmin, if none exists, generate
    $admin = $DB->get_record_sql(
        "SELECT value FROM `mdl_config` WHERE `name` LIKE 'siteadmins'",
        null,
        IGNORE_MISSING
    );

    $admin_user = $DB->get_record('user', array('id' => "$admin->value"));
    $existing_tokens = $DB->get_record(
        'external_tokens',
        array(
            'userid' => $admin_user->id,
            'externalserviceid' => $service->id
        )
    );

    if (!$existing_tokens) {
        require('config.php');
        require_once("$CFG->libdir/externallib.php");

        // Generate a new token for the Admin User
        $token = external_generate_token(
            EXTERNAL_TOKEN_PERMANENT,
            $service,
            $admin_user->id,
            context_system::instance(),
            $validuntil=0,
            $IP_RESTRICTION
        );

        $DB->set_field(
            'external_tokens',
            'creatorid',
            "$admin_user->id",
            array("token"=>"$token")
        );
    }



    return true;
}
