Development
===========

lib.php
-------
*functions*

`orvsd_siteinfo_init_db()`
    * returns: bool
    * Intializes the Database
    * Depreciated with upgrade to webservice

`orvsd_siteinfo_update_db()`
    * returns: bool
    * Updates the Database
    * Depreciated with upgrade to webservice

`orvsd_siteinfo_usercount($role="none", $timeframe=null)`
    * returns: intval($count)
    * Provides the number of users (totalusers, adminusers, teachers, activeusers)

`orvsd_siteinfo_courselist()`
    * returns: $courselist_string
    * Provides a list of courses+info (courseid, shortname, enrolled users)

`orvsd_siteinfo_get_enrolments($courseid)`
    * returns: $DB->get_field_sql($sql,$params, IGNORE_MISSING)
    * Provides the usercount for a given courseid

externallib.php
---------------
*functions*

`siteinfo_parameter()`
    * returns: new external_function_parameters(array('datetime' => new
      external_value(PARAM_TEXT, 'count users within the last 'n' days',
      VALUE_DEFAULT, 7)))
    * 

`siteinfo($datetime)`
    * returns: $sinfo = local_orvsd_siteinfo_external::get_site_info(time()
      - $datetime);
    * 

`siteinfo_returns()`
    * returns: json_encode($sinfo);
    * 

`get_site_info($timeframe)`
    * returns: intval($count);
    * 

`user_count($role="none", $timeframe=null)`
    * returns:
    * 

version.php
-----------

db/services.php
---------------

client/client.php
-----------------
