<?php

# Page Template
# Header is located in /templates/header.php
# Footer is located in /templates/footer.php
# Frontend is created with Bootstrap and jQuery


// Require require.php, binds everything together
require('require.php');

ob_start();
session_start();

// Connect to DB
include(ROOT_PATH.'db.php');

/*
// Get HTML Template
*/

if( isset($_GET['generate_json']) ) {

    require(ROOT_PATH.'data/categories.php');

    $cat = isset($_GET['generate_json']) ? $_GET['generate_json'] : null;

    // For email validation in registration form page,
    // We get the list of registered emails in the database
    // and put them in array
    $user_emails = [];

    // Check if email is already taken
    $check_emails = $connect_db->prepare("SELECT email FROM users");
    $check_emails->execute();    
    foreach ($check_emails->get_result() as $row){
        $user_emails[] = $row['email'];
    }

    if($cat){
        $json = new JsonURL();
        echo $json->get_json(${$cat});
    }

} else {

    $page_list = ['home', 'login', 'register', 'account_dashboard', 'view_listing', 'view_category', 'search'];    
    $page_name = isset($_GET['page']) ? $_GET['page'] : (  empty($_GET) ? 'home' : 'erro404');
    $current_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $header = new Header($page_name, $current_user);
    $content = new Content($page_name, $current_user);
    $footer = new Footer($page_name, $current_user);
    $widget = new Widget($page_name, $current_user);

    echo
    $header->get_html().
    
    $content->get_template().
    
    $footer->get_html();

}

// Close connection to db
$connect_db->close();

?>