function get_site_info(timeframe=7)

takes integer `timeframe` which determines the count for `activeusers`
this is the number of days into the past that the user must have logged in to 
be considered active.
`timeframe` defaults to 7.

returns json encoded string
`baseurl` : the website's url
`basepath` : the website's directory
`sitename` : chosen by admin on creation
`sitetype` : hardcoded to the string "moodle"
`siteversion` : date of last update
`siterelease` : moodle version and build (CFG=>release)
`location` : uname -n
`adminemail` : support e-mail
`totalusers` : all users all time
`adminusers` : number of CFG => siteadmins
`teachers` : the all time number of users with role teacher
`activeusers` : the count of any users with activity in the last `timeframe` 
days
`totalcourses` : the number of courses
`courses` : a string representing the list of courses installed (needs more 
documentation)
`timemodified` : the current time (unix timestamp)
