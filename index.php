<?php

# Page Template
# Header is located in /templates/header.php
# Footer is located in /templates/footer.php
# Frontend is created with Bootstrap and jQuery


// Require require.php, binds everything together
require('require.php');

ob_start();
session_start();

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
    // Let's connect to mySQL DB first
    $connect_db = connect_db();
    // Check connection
    if ($connect_db->connect_error) {
        die("Connection failed: " . $connect_db->connect_error);
    }

    // Check if email is already taken
    $check_emails = $connect_db->prepare("SELECT email FROM users");
    $check_emails->execute();    
    foreach ($check_emails->get_result() as $row){
        $user_emails[] = $row['email'];
    }

    // Close connection to db
    $connect_db->close();

    if($cat){
        $json = new JsonURL();
        echo $json->get_json(${$cat});
    }

} else {

    $page_list = ['home', 'login', 'register', 'account_dashboard', 'view_listing', 'view_category', 'search'];    
    $page_name = isset($_GET['page']) ? $_GET['page'] : (  empty($_GET) ? 'home' : 'erro404');

    $header = new Header($page_name);
    $content = new Content($page_name);
    $footer = new Footer($page_name);

    echo
    $header->get_html().
    
    $content->get_template().
    
    $footer->get_html();

}

?>