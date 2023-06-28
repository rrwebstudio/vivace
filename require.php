<?php

# CONSTANTS
define( 'SITE_NAME', 'VIVACE' );
define( 'SITE_URL', 'https://vivace.rrwebstudio.com' );
define('ROOT_PATH', dirname(__DIR__) . '/html/');
define('SITE_LOGO', '<a id="text-logo" class="navbar-brand col-lg-3 me-0" href="'.SITE_URL.'">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-mic-fill" viewBox="0 0 16 16">
<path d="M5 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3z"/>
<path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z"/>
</svg> '.SITE_NAME.'</a>');

/*

DATABASE

*/

include('db/db_connect.php');

/*

TEMPLATES

*/
include('template.php');
include('templates/header.php');
include('templates/footer.php');
include('templates/content.php');
include('templates/forms.php');
include('templates/widgets.php');
include('templates/category_tag.php');

/*

PAGES

*/
include('pages/home.php');
include('pages/register.php');
include('pages/login.php');
include('pages/account_dashboard.php');
include('pages/search.php');
include('pages/view_listing.php');
include('pages/view_profile.php');

/*

OTHERS

*/

include('data/json.php');
include('functions/sendmail.php');
include('functions/randompassword.php');