<?php  

// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
* REST client for Moodle 2
* Return JSON or XML format
*
* @authorr Jerome Mouneyrac
*/

/// SETUP - NEED TO BE CHANGED
$token = 'c97b467849118653f5d0192961f34378';
$domainname = 'http://school27';
$functionname = 'local_orvsd_siteinfo_siteinfo';

// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version

//////// moodle_user_create_users ////////

/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION

$course1= array();
$course1['datetime'] = 14;
$params = array('course1' => $course1);

print "Calling the REST server with parameters:\n";
print_r($course1);

/// REST CALL
//header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;;
require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2

$restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
$resp = $curl->post($serverurl . '&moodlewsrestformat=json', $course1);
print_r($resp);
