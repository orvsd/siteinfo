Development
===========

This document is meant to help those who are going to contribute to / need to
work with orvsd_central's moodle siteinfo codebase. The information is split 
up based on files and may be organized based on what certain functions do and 
may be information of certain variables. Everything is ultimately organized 
based on what the OSL developers feel is most important to know diving into our
code.

lib.php
-------
**functions**

`orvsd_siteinfo_init_db()`
    * returns: bool
    * Initializes the Database
    * Depreciated with upgrade to webservice

`orvsd_siteinfo_update_db()`
    * returns: bool
    * Updates the Database
    * Depreciated with upgrade to webservice

`orvsd_siteinfo_usercount($role="none", $timeframe=null)`
    * returns: intval($count)
    * Provides the number of users (totalusers, adminusers, teachers, 
      activeusers)

`orvsd_siteinfo_courselist()`
    * returns: $courselist_string
    * Provides a list of courses+info (courseid, shortname, enrolled users)

`orvsd_siteinfo_get_enrolments($courseid)`
    * returns: $DB->get_field_sql($sql,$params, IGNORE_MISSING)
    * Provides the usercount for a given courseid

externallib.php
---------------
**functions**

`siteinfo_parameter()`
    * returns: new external_function_parameters(array('datetime' => new
      external_value(PARAM_TEXT, 'count users within the last 'n' days',
      VALUE_DEFAULT, 7)))
    * This returns information for Moodle about the type of input the 
      plugin is expecting. The variable description can be found in the
      Moodle interface. 
    * The default value is what is passed to the siteinfo
      function by default.

`siteinfo($datetime)`
    * returns: $sinfo = local_orvsd_siteinfo_external::get_site_info(time()
      - $datetime);
    * This returns a given site's information from the past N days
      (default == 7). It calls get_site_info() in the process.

`siteinfo_returns()`
    * returns: new external_value(PARAM_TEXT, 'Site info.');
    * This tells moodle what the Site Info plugin returns. The PARAM_TEXT
      value can be found in the Moodle interface.

`get_site_info($timeframe)`
    * returns: json_encode($sinfo);
    * This returns a json encoded string that is the given site's information
      including: baseurl, basepath, sitename, sitetype, siteversion,
      siterelease, location, admineamail, totalusers, adminusers, teachers,
      activeusers, totalusers, totalcourses, courses, and timemodified.
    * For more information about these, read the moodle config file and the 
      siteinfo externallib.php.

`user_count($role="none", $timeframe=null)`
    * returns: intval($count);
    * This returns an integer of teachers, managers, course_creators, students,
      guests, authenticated, and frontpage users for a given site over the
      past $timeframe days.

version.php
-----------
`$plugin->version` 
    * Is the date/version string for the current version of the plugin. It's
      input should be formatted YYYYMMDDXX where YYYY is the year, MM is the 
      month, DD is the day, and XX is the version unique to that day.

`$plugin->requires` 
    * Specifies the version of Moodle required to run the version the plugin.

`$plugin->release` 
    * Specifies the release version of the Moodle Plugin.

`$plugin->dependencies` 
    * Tells Moodle which other plugins are required for this plugin to 
      operate correctly. The only dependency for Siteinfo is 
      ('local_orvsd_coursemeta' => ANY_VERSION)

db/services.php
---------------
`$functions`
    * Lists the classname, methodname, classpath, description, and type status
      of the main function in orvsd_central 'local_orvsd_siteinfo_siteinfo'.
    * The naming scheme of 'local_orvsd_siteinfo_siteinfo' is important to
      for Moodle to interpret what we are passing it. 'local' means it is a
      local plugin, 'orvsd_siteinfo' is the name of the plugin, and 'siteinfo'
      is the name of the method in externallib.php.

`$services`
    * Lists the functions, restricted users status, and enabled status for
      'Site Info'.

client/client.php
-----------------
Client.php is used to test webservices. The client.php included in Site Info
is able to request siteinfo for a given Moodle Site and return the jsnonified
info. This is only for testing purposes.

There are a few changes you should make when utilizing client.php for proper
testing:

    * `$token` should be the value of the token assigned to the webservice.
      This can be determined through the moodle interface under the admin
      panel.
    * `domainname` should be set to the domain of the Moodle site.
    * `functionname` is already set to `local_orvsd_siteinfo_siteinfo`, but
      if it isn't this should be equal to the `$functions` variable in
      db/services.php.
    * `$restformat` should be set to `'json'`.
    * `$course1['datetime']` should be set to `14` by default. This is the
      length of time siteinfo will search through.

Everything else should work out of the box for testing siteinfo.
