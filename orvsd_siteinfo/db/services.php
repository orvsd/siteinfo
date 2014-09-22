<?php = array(
        'local_orvsd_siteinfo_site_information' => array( 
                'classname'   => 'local_orvsd_siteinfo_external',
                'methodname'  => 'site_info',
                'classpath'   => 'local/orvsd_siteinfo/externallib.php',
                'description' => 'This function returns information for a site
                                        given a timeframe as a parameter',
                'type'        => 'read'
        )
);

// OPTIONAL
// During the plugin installation/upgrade, Moodle installs these services as pre-build services.
// a pre-build service is not editable by admin.
$services = array(
    'Orvsd Siteinfo' => array(
        'functions' => array ('local_orvsd_siteinfo_site_info'),
        'restrictedusers' => 0, // if 1, the admin must manually select which user can use the service.
                                    // (Administration > Plugins > Web Services > Manage Services > Authorised Users)
        'enable'=> 1, // if 0, then token linked to this service won't work
    )
);
