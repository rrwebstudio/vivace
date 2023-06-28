<?php

# Website is running on Bootstrap
# Bootstrap CSS and JS is being delivered via CDN
# jquery, seelect2, and jquery validation aree also being delivered via CDN

class Header extends Template {

    // Methods    
    function get_html() {
        global $widget;
        $page = $this->page;
        $page_title = str_replace('_', ' ', $page);
        $page_title = ucwords($page_title);
        $html = '    
        <!doctype html>
        <html lang="en" data-bs-theme="auto">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <meta name="description" content="">
    
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
                <link href="../assets/css/style.css?v=0.0.5" rel="stylesheet">
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Montserrat:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">    
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <script
                src="https://code.jquery.com/jquery-3.6.4.min.js"
                integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
                crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
                ';

                if($page == 'account_dashboard') {
                    $html .= '<script src="../assets/js/tinymce.min.js"></script>';
                }
                $html .='                
                <title>Vivace: '.$page_title.'</title>
            </head>';
            if($page == 'login') {
                $html .= '<body class="lh-lg p-0 m-0 bg-image '.$page.'" style="background-image:url('.SITE_URL.'/assets/images/bg_login.jpg)">';
            } else if($page == 'register') {
                $html .= '<body class="lh-lg p-0 m-0 bg-image '.$page.'" style="background-image:url('.SITE_URL.'/assets/images/bg_register.jpg); background-position: 50% 20%;">';
            } else {
                $html .= '<body class="lh-lg p-0 m-0 '.$page.'">';
            }          

        // Navigation
        // Show everywhere except Login and Register Page
        if(!in_array($page, array('login', 'register'))) {             
            $html .= '
            <div id="main-header" class="container-fluid px-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-black border-0 shadow">
            <div class="container-lg">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse d-lg-flex" id="navbar">
                '.SITE_LOGO.'
                <ul class="navbar-nav col-lg-9 justify-content-lg-end text-end">';
                if(empty($_SESSION['user_id'])) {
                    $html .='
                    <li class="nav-item">
                    <a class="nav-link" href="'.SITE_URL.'/?page=login">Login</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="'.SITE_URL.'/?page=register">Register</a>
                    </li>   
                    ';
                } else {
                    $unread_count = $widget->get_unread_count();
                    if(isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'company') {
                        $html .='
                        <li class="nav-item">
                        <a class="btn-primary nav-link" href="'.SITE_URL.'/?page=account_dashboard&action=post_listing">Post a Listing</a>';
                    }                    
                    $html .='
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="'.SITE_URL.'/?page=view_profile&id='.$_SESSION['user_id'].'">My Profile</a>
                    </li>  
                    <li class="nav-item">
                    <a class="nav-link" href="'.SITE_URL.'/?page=account_dashboard">Account Dashboard';
                    if($unread_count > 0){
                        $html .='
                        <span class="badge rounded-pill bg-danger">
                            '.$unread_count.'
                            <span class="visually-hidden">unread messages</span>
                        </span>'; 
                    }
                    $html .='</a>
                    </li>                                      
                    <li class="nav-item">
                    <a class="nav-link" href="'.SITE_URL.'/?page=account_dashboard&action=logout">Logout</a>
                    </li>   
                    ';
                } 
                $html .='                   
                </ul>
                </div>
            </div>
            </nav>
            ';

            // Display Welcome message and search form when in Homepage 
            if(in_array($page, array('home', 'search'))) {
                // Welcome message
                if($page == 'home') {
                    $html .= '<div class="bg-image position-relative" style="background-image:url('.SITE_URL.'/assets/images/bg_search.jpg); background-position: 50% 48%;">';
                }  else if($page == 'search') {
                    $html .= '<div class="bg-image position-relative" style="background-image:url('.SITE_URL.'/assets/images/bg_search2.jpg); background-position: 50% 40%;">';
                }                   
                
                $html .= '
                <div id="welcome-section" class="pt-5 pb-3">
                    <div class="container-lg text-white position-relative" style="z-index: 1;">
                        <h1 class="h3">Search for your multimedia equipment needs.</h1>
                        <p>Start your search below:</p>
                    </div>
                    <div class="bg-overlay" style="background-color: rgba(0, 0, 0, 0.6);">
                </div>
                '; 
                // Search bar
                $form = new Form();
                $html .= $form->search_form(); 
                $html .= '</div>
                
                </div>';               
                
            } else {
                if(in_array($page, array('account_dashboard'))) {
                    // Welcome message
                    $html .= '<div class="bg-image position-relative" style="background-image:url('.SITE_URL.'/assets/images/bg_dashboard2.jpg); background-position: 50% 0%;">';
                    $html .= '
                        <div id="welcome-section" class="pt-5 pb-3">
                            <div class="container-lg">
                                <div class="row gx-5">
                                    <div class="col-4">'
                                        .$widget->hello_user().
                                    '</div>
                                    <div class="col position-relative">'
                                        .$widget->user_data().   
                                    '</div>
                                </div>                                                        
                            </div>
                        </div>
                        <div class="bg-overlay" style="background-color: rgba(0, 0, 0, 0.6);">
                    </div>
                    ';
                }
            }

            $html .= '            
            </div>';
        }  

        $html .= '</div>';

        $html .= ' <div id="content-area" class="container-fluid px-0">';

        return $html;
    }
}