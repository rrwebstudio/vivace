<?php

class Widget extends Template {  

    public $profile_id;
    public $thread_id;
    public $thread_subject;
    public $position;   

    function set_thread($thread_id) {
        $this->thread_id = $thread_id;
    }

    function set_position($position) {
        $this->position = $position;
    }

    function set_profile($profile_id) {
        $this->profile_id= $profile_id;
    }

    function get_user(){
        global $connect_db;
        $user_id = $this->user_id;

        // Prepare sql statement to get profile data
        $check_user = $connect_db->prepare("SELECT first_name, last_name, email, company_name, company_address, phone_number, mobile_number, user_type, avatar, follower_discount FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $user_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($first_name, $last_name, $email_address, $company_name, $company_address, $phone_number, $mobile_number, $user_type, $avatar, $follower_discount);
        $check_user->fetch();

        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;
        $user['email_address'] = $email_address;
        $user['company_name'] = $company_name;
        $user['company_address'] = $company_address;
        $user['phone_number'] = $phone_number;
        $user['mobile_number'] = $mobile_number;
        $user['user_type'] = $user_type;
        $user['avatar'] = $avatar;
        $user['follower_discount'] = $follower_discount;

        return $user;
    }

    function get_avatar() {
        global $connect_db;
        $profile_id = $this->profile_id;

        // Prepare sql statement to get profile data
        $check_user = $connect_db->prepare("SELECT company_name, avatar FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $profile_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($company_name, $avatar);
        // statement result
        if($check_user->num_rows == 1) {                                               
            // Get result
          if($check_user->fetch()) {
                if($avatar) {
                    $avatar = '<img class="img-fluid rounded-circle" src="'.SITE_URL.'/uploads/'.$avatar.'" style="width:200px;">';
                } else {
                    $avatar = '<img class="rounded-circle img-fluid" src="https://ui-avatars.com/api/?size=128&name='.$company_name.'">';
                }
          }
        }

        return $avatar;
    }

    function dashboard_links() {
        global $widget;
        $user = $widget->get_user();
        $dashboard_home = $user['user_type'] == 'company' ? 'My Listings' : 'Feed';
        $nav = '
        <div class="card p-4 mb-4">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard">
                    <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                        <path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                    </svg> 
                    '.$dashboard_home.'</a></li>';
                    if($user['user_type'] == 'company'){
                        $nav .='
                        <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard&view=discount_settings">
                        <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                        <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z"/>
                        <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z"/>
                        </svg>
                        Discount Settings</a></li>';
                    }                    
                    $nav .='
                    <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard&action=edit_profile">
                    <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                    </svg>
                    Edit Profile</a></li>
                    <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard&action=update_password">
                    <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pass" viewBox="0 0 16 16">
                    <path d="M5.5 5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5Zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3Z"/>
                    <path d="M8 2a2 2 0 0 0 2-2h2.5A1.5 1.5 0 0 1 14 1.5v13a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-13A1.5 1.5 0 0 1 3.5 0H6a2 2 0 0 0 2 2Zm0 1a3.001 3.001 0 0 1-2.83-2H3.5a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-13a.5.5 0 0 0-.5-.5h-1.67A3.001 3.001 0 0 1 8 3Z"/>
                    </svg>
                    Change Password</a></li>';

                    if($user['user_type'] == 'company'){
                        $nav .='
                    <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard&action=post_listing">
                    <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                    </svg>
                    Post a Listing</a></li>';
                    }                    

                    $nav .='
                </ul> 
            </div>
        </div>
        ';
        return $nav;
    }

    function hello_user(){
        global $connect_db, $widget;
        $current_user = $this->user_id;


        // Prepare sql statement to get profile data
        $check_user = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $current_user);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($company_name);
        $check_user->fetch();
        $company = isset($_POST['company_name']) ? $_POST['company_name'] : $company_name;
        $content = '
        <div class="px-5 py-4 bg-black position-relative text-white shadow-lg" style="z-index: 1;">
            <div class="row mb-4">                
                <div class="col-3"><div class="border rounded-circle">';
                $widget->set_profile($current_user);
                $content .= $widget->get_avatar();
                $content .='</div></div>
                <div class="col">
                    <h2 class="h5 fw-bold">'.$company.'</h2>
                    <p><a href="?page=account_dashboard&action=edit_profile&update=photo" class="text-white">Update Company Logo</a></p>
                </div>
            </div> 
            <div class="row gx-2">                
                <div class="col-auto mx-auto">
                <a class="btn btn-secondary" href="?page=view_profile&id='.$current_user.'" role="button">View Profile</a>
                </div>              
            </div>            
        </div>
        ';

        return $content;
    }

    function user_data(){
        global $connect_db, $widget;
        $user_id = $this->user_id;
        $user = $widget->get_user();

        // Prepare sql statement to get profile data
        $check_user = $connect_db->prepare("SELECT first_name, company_name, company_address, phone_number, mobile_number, user_type FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $user_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($first_name, $company_name, $company_address, $phone_number, $mobile_number, $user_type);
        $check_user->fetch();
        $content = '
        <div class="card dash-card border-0 rounded-0 position-absolute top-0 start-0 shadow mx-4" style="z-index: 1;">
            <article class="card-body px-5 py-4">
            <h1 class="card-title mt-2 mb-4 h2">Hi, '.$first_name.'</h1>
            <div class="row">
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Company Name:</label>
                    '.$company_name.'
                    </div> 
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Company Address:</label>
                    '.$company_address.'
                    </div>
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Phone Number:</label>
                    '.$phone_number.'
                    </div> 
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Mobile Number:</label>
                    '.$mobile_number.'
                    </div> 
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Account Type:</label>
                    '.$user_type.'
                    </div>
                </div>
                <div class="col">
                    <div class="mb-4">';
                    if($user['user_type'] == 'company'){
                        $content .='
                    <label class="d-block h6">Total Listings:</label>
                    '.$widget->get_listing_count();
                    }                    
                    $content .='
                    </div>
                </div>
            </div>
            </article>
        </div>
        ';
        return $content;
    }

    function followers() {
        global $widget;
        return '
        <div class="card p-4 mb-4">
            <h4 class="h3 mb-0 fw-bold text-center">'.$widget->get_follower_count().'</h4>
            <p class="text-success fw-bold mb-0 text-center">Total Followers</p>
            <div class="row mt-3">
                <div class="col text-center">
                <a class="btn btn-secondary btn-sm" href="?page=account_dashboard&view=followers" role="button">View Followers</a>
                </div>
            </div>
        </div>
        ';
    }

    function message_box() {
        global $widget;
        $unread_count = $widget->get_unread_count();
        $content = '
        <div class="card p-4 mb-4">
            <h4 class="h5 mb-4">Messages';
        if($unread_count > 0){
            $content .='
            <span class="badge rounded-pill bg-danger">
                '.$unread_count.'
                <span class="visually-hidden">unread messages</span>
            </span>'; 
        }
            $content .='
            </h4>';
            $widget->set_position('side');
            $content .= $widget->get_messages();            
            $content .= '
            <div class="row mt-3 bg-light p-2">
                <div class="col text-center">
                <a class="fw-bold text-dark" href="?page=account_dashboard&view=messages" role="button">View Messages</a>
                </div>
            </div>
        </div>
        ';

        return $content;
    }

    function get_listings(){
        global $connect_db, $widget;
        $user_id = $this->user_id;
        $profile_id = $this->profile_id;

        // Get posts by the logged in user from the database
        $get_posts = $connect_db->prepare("SELECT 
        ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo, discount
        FROM posts WHERE user = ? ORDER BY modified_date DESC");
        $get_posts->bind_param('i', $profile_id);
        // Execute search and get results
        $get_posts->execute();
        $result = $get_posts->get_result();

        if($result->num_rows > 0) {
            $listings = '<div id="listings">';
            while ($row = $result -> fetch_assoc()) {
                //Get poster's contact details from database
                $get_author = $connect_db->prepare("SELECT email, company_name, follower_discount FROM users WHERE ID = ?");
                $get_author->bind_param('i', $row['user']);
                $get_author->execute();
                $get_author->store_result();
                $get_author->bind_result($author_email, $author_company, $global_discount);
                $get_author->fetch();
                // Get post thumbnail
                $bg_setphoto = SITE_URL.'/uploads/'.$row['set_photo'];
                // Format money
                $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
                $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
                // Get discount if applicable
                $applicable_discount = isset($row['discount']) && $row['discount'] > 0 ? $row['discount'] : $global_discount;     
                $discount_amount =  $row['rent_price'] * ($applicable_discount / 100);
                $discount_price = $row['rent_price'] - $discount_amount;
                $widget->set_profile($row['user']);
                $ref_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";  
                // Show result
                $listings .= '
                <div class=" post post-'.$row['ID'].' card p-0 shadow mb-4">
                    <div class="card-body p-0">
                        <div class="row m-0">';
                            $listings .= '<div class="col-3 bg-image rounded-start" style="background-image:url('.$bg_setphoto.');">';                                                                                                                 
                            $listings .= '
                            </div>
                            <div class="col p-3">                                                      
                                <div class="post-content position-relative" style="height: 220px; overflow: hidden;">
                                    <h2 class="card-title h4 mb-1 d-flex align-items-center"><span class="listing_title">'.$row['title'].' </span></h2>'
                                    .$row['content'].
                                '</div>
                            </div>  
                            <div class="col-4 py-3 pe-4">
                            <div class="alert alert-success py-1 px-2">
                            <div class="row gx-1 d-flex align-items-center mb-2 bg-white p-1 rounded">
                            <div class="col-auto">
                                <div class="round-circle" style="width: 20px;">'
                                .$widget->get_avatar().
                                '</div>
                            </div>
                            <div class="col">
                                <a class="text-dark fw-bold" href="?page=view_profile&id='.$row['user'].'">'.$author_company.'</a>
                            </div>
                            </div>';
                            $is_following = $widget->is_following();                                          
                            $listings .='
                            <div class="row">
                                <div class="col fw-bold price-text text-center">';
                                if($global_discount > 0 && $is_following == true ) {
                                    $listings .='<span class="text-decoration-line-through">₱'.$money_formatter->format($row['rent_price']).'</span>
                                    <span class="text-danger fw-bold">₱'.$money_formatter->format($discount_price).'</span></p>';
                                } else {
                                    $listings .='₱'.$money_formatter->format($row['rent_price']).'</p>';
                                }                                                
                                $listings .='
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                <div class="d-grid gap-2">
                                    <a class="btn btn-success btn-sm" href="?page=search&view_listing='.$row['ID'].'" role="button">View Details</a>
                                </div>                                    
                                </div>
                            </div>
                            </div>
                            <div class="alert alert-light border py-1 px-2">
                                <div class="post-meta">
                                    <div class="meta-date">';
                                    $listings .= '
                                        Posted on '.$row['post_date'].'
                                    </div>
                                </div>
                            </div>                    
                            ';

                            if($user_id != $profile_id) {
                                
                                $check_bookmark = $connect_db->prepare("SELECT ID FROM bookmarks WHERE client_id = ? AND listing_id = ?");
                                $check_bookmark -> bind_param('ii', $user_id, $row['ID']);
                                $check_bookmark->execute();
                                $check_bookmark -> store_result();
                                $check_bookmark -> bind_result($bookmark_id);
                                if($check_bookmark->num_rows == 0) {
                                    $listings .='<a href="'.$ref_url.'&bookmark='.$row['ID'].'" class="bg-light btn d-inline py-1 px-2 small text-uppercase">Bookmark</a>';
                                }
                            }

                            if($user_id == $profile_id) {
                                $listings .= '<a class="bg-light btn d-inline py-1 px-2 small text-uppercase" href="?page=account_dashboard&action=edit_listing&id='.$row['ID'].'">Edit</a>';
                                $listings .= '<a class="bg-light btn d-inline py-1 px-2 small text-uppercase" href="?page=account_dashboard&action=delete_listing&id='.$row['ID'].'">Delete</a>';
                            }
                            $listings .='
                            </div>                            
                        </div>                   
                    </div>
                </div>
                ';
            }
            $listings .= '</div>';
        } else {
            $listings ='No listings found</p>';
        }

        return $listings;
    }

    function get_bookmarks(){
        global $connect_db, $widget;
        $user_id = $this->user_id;

        // Get bookmarks by the logged in user from the database
        $get_bookmarks = $connect_db->prepare("SELECT  listing_id
        FROM bookmarks WHERE client_id = ? ORDER BY bookmark_date DESC");
        $get_bookmarks->bind_param('i', $user_id);
        // Execute search and get results
        $get_bookmarks->execute();
        $bookmarks = $get_bookmarks->get_result();

        if($bookmarks->num_rows > 0) {
            $listings = '<div id="listings">';
            while ($row = $bookmarks -> fetch_assoc()) {
                // Get post data from database
                $get_posts = $connect_db->prepare("SELECT ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo, discount FROM posts WHERE ID = ?");
                $get_posts->bind_param('i', $row['listing_id']);
                $get_posts->execute();
                $get_posts->store_result();
                $get_posts->bind_result($postid, $userid, $title, $contenttxt, $postdate, $modifieddate, $rentprice, $eventcat, $eventtype, $venuecat, $venuetype, $setphoto, $discount);
                $get_posts->fetch();

                //Get poster's contact details from database
                $get_author = $connect_db->prepare("SELECT email, company_name, follower_discount FROM users WHERE ID = ?");
                $get_author->bind_param('i', $userid);
                $get_author->execute();
                $get_author->store_result();
                $get_author->bind_result($author_email, $author_company, $global_discount);
                $get_author->fetch();
                // Get post thumbnail
                $bg_setphoto = SITE_URL.'/uploads/'.$setphoto;
                // Format money
                $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
                $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
                // Get discount if applicable
                $applicable_discount = $discount > 0 ? $discount : $global_discount;     
                $discount_amount =  $rentprice * ($applicable_discount / 100);
                $discount_price = $rentprice - $discount_amount;
                $widget->set_profile($userid);
                $ref_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";  
                // Show result
                $listings .= '
                <div class=" post post-'.$postid.' card p-0 shadow mb-4">
                    <div class="card-body p-0">
                        <div class="row m-0">';
                            $listings .= '<div class="col-3 bg-image rounded-start" style="background-image:url('.$bg_setphoto.');">';                                                                                                                 
                            $listings .= '
                            </div>
                            <div class="col p-3">                                                      
                                <div class="post-content position-relative" style="height: 220px; overflow: hidden;">
                                    <h2 class="card-title h4 mb-1 d-flex align-items-center"><span class="listing_title">'.$title.' </span></h2>'
                                    .$contenttxt.
                                '</div>
                            </div>  
                            <div class="col-4 py-3 pe-4">
                            <div class="alert alert-success py-1 px-2">
                            <div class="row gx-1 d-flex align-items-center mb-2 bg-white p-1 rounded">
                            <div class="col-auto">
                                <div class="round-circle" style="width: 20px;">'
                                .$widget->get_avatar().
                                '</div>
                            </div>
                            <div class="col">
                                <a class="text-dark fw-bold" href="?page=view_profile&id='.$userid.'">'.$author_company.'</a>
                            </div>
                            </div>';
                            $is_following = $widget->is_following();                                          
                            $listings .='
                            <div class="row">
                                <div class="col fw-bold price-text text-center">';
                                if($global_discount > 0 && $is_following == true ) {
                                    $listings .='<span class="text-decoration-line-through">₱'.$money_formatter->format($rentprice).'</span>
                                    <span class="text-danger fw-bold">₱'.$money_formatter->format($discount_price).'</span></p>';
                                } else {
                                    $listings .='₱'.$money_formatter->format($rentprice).'</p>';
                                }                                                
                                $listings .='
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                <div class="d-grid gap-2">
                                    <a class="btn btn-success btn-sm" href="?page=search&view_listing='.$postid.'" role="button">View Details</a>
                                </div>                                    
                                </div>
                            </div>
                            </div>
                            <div class="alert alert-light border py-1 px-2">
                                <div class="post-meta">
                                    <div class="meta-date">';
                                    $listings .= '
                                        Posted on '.$modifieddate.'
                                    </div>
                                </div>
                            </div>                    
                            ';

                            $listings .='<a href="'.$ref_url.'&remove_bookmark='.$postid.'" class="bg-light btn d-inline py-1 px-2 small text-uppercase">Remove Bookmark</a>';

                            $listings .='
                            </div>                            
                        </div>                   
                    </div>
                </div>
                ';
            }
            $listings .= '</div>';
        } else {
            $listings ='No bookmarks found</p>';
        }

        return $listings;
    }

    function profile_user_data(){
        global $connect_db;
        $profile_id = $this->profile_id;

        $check_user = $connect_db->prepare("SELECT email, company_name, company_address, first_name, last_name, phone_number, mobile_number, user_type, avatar FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $profile_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($email, $company_name, $company_address, $first_name, $last_name, $phone_number, $mobile_number, $user_type, $avatar);
        //var_dump($check_user->fetch());

        // statement result
        if($check_user->num_rows == 1) {                                               
                // Get result
            if($check_user->fetch()) {
                $content = '
                <div class="card p-4 mb-4">
                    <h4 class="h5 mb-4">Company Details</h4>
                    <div class="mb-3">
                        <strong class="d-block">Company Address:</strong>
                        '.$company_address.'
                    </div>
                    <div class="mb-4">
                        <strong class="d-block">Contact Person:</strong>
                        '.$first_name.' '.$last_name.'
                    </div>
                    <div class="mb-4">
                        <strong class="d-block">Phone Number:</strong>
                        '.$phone_number.'
                    </div>
                    <div class="mb-4">
                        <strong class="d-block">Mobile Number:</strong>
                        '.$mobile_number.'
                    </div>
                </div>
                ';
            }
        } else {
            $content = '';
        }

        return $content;
    }

    function get_follower_count() {
        global $connect_db;
        $profile_id = $this->profile_id;

        // Get followers
        $get_followers = $connect_db->prepare("SELECT 
        ID, client_id
        FROM follows WHERE company_id = ?");
        $get_followers->bind_param('i', $profile_id);
        // Execute search and get results
        $get_followers->execute();
        $followers = $get_followers->get_result();

        return $followers->num_rows;
    }

    function get_following_count() {
        global $connect_db;
        $profile_id = $this->profile_id;

        // Get following
        $get_following = $connect_db->prepare("SELECT 
        ID, company_id
        FROM follows WHERE client_id = ?");
        $get_following->bind_param('i', $profile_id);
        // Execute search and get results
        $get_following->execute();
        $following = $get_following->get_result();

        return $following->num_rows;
    }

    function get_follow_list() {
        global $connect_db, $widget;
        $profile_id = $this->profile_id;
        $page = $this->page;
        

        // Get following
        $get_following = $connect_db->prepare("SELECT 
        ID, company_id
        FROM follows WHERE client_id = ?");
        $get_following->bind_param('i', $profile_id);
        // Execute search and get results
        $get_following->execute();
        $following = $get_following->get_result();

        if($following->num_rows > 0) {
            $content = '<div id="follow_list"><div class="card p-4 mb-4">';
            if($page == 'account_dashboard'){
                $content .= '<h3 class="mb-5">Following</h4>';
            } else {
                $content .= '<h4 class="h5 mb-4">Following</h4>';
            }            
            $row_count = 1;
            while ($row = $following -> fetch_assoc()) {
                $check_user = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
                $check_user -> bind_param('i', $row['company_id']);
                $check_user->execute();
                $check_user -> store_result();
                $check_user -> bind_result($company_name);
                $check_user->fetch();
                $widget->set_profile($row['company_id']);
                $class = $following->num_rows > 1 ? ($following->num_rows == $row_count ? '' : 'mb-2 border-bottom') : '';
                $content .= '
                <div class="row d-flex align-items-center pb-2 '.$class.'">';
                if($page == 'account_dashboard'){
                    $content .= '<div class="col-1"><a href="?page=view_profile&id='.$row['company_id'].'">'.$widget->get_avatar().'</a></div>';
                } else {
                    $content .= '<div class="col-2"><a href="?page=view_profile&id='.$row['company_id'].'">'.$widget->get_avatar().'</a></div>';
                }                 
                $content .= '<div class="col"><a class="text-dark fw-bold" href="?page=view_profile&id='.$row['company_id'].'">'.$company_name.'</a></div>
                <div class="col text-end"><a class="following small btn btn-outline-dark rounded-0 btn border-3 border-light fw-bold" href="?page=view_profile&id='.$row['company_id'].'&action=unfollow&ref='.$page.'">Following</a></div>
                </div>
                ';
                $row_count++;
            }
            $content .= '</div></div>';
        } else {
            $content = '';
        }

        return $content;
    }

    function get_follower_list() {
        global $connect_db, $widget;
        $profile_id = $this->profile_id;
        $page = $this->page;

        // Get following
        $get_following = $connect_db->prepare("SELECT 
        ID, client_id
        FROM follows WHERE company_id = ?");
        $get_following->bind_param('i', $profile_id);
        // Execute search and get results
        $get_following->execute();
        $following = $get_following->get_result();

        if($following->num_rows > 0) {
            $content = '<div id="follower_list">';         
            $row_count = 1;
            while ($row = $following -> fetch_assoc()) {
                $check_user = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
                $check_user -> bind_param('i', $row['client_id']);
                $check_user->execute();
                $check_user -> store_result();
                $check_user -> bind_result($company_name);
                $check_user->fetch();
                $widget->set_profile($row['client_id']);
                $class = $following->num_rows > 1 ? ($following->num_rows == $row_count ? '' : 'mb-2 border-bottom') : '';
                $content .= '
                <div class="row d-flex align-items-center pb-2 '.$class.'">';
                if($page == 'account_dashboard'){
                    $content .= '<div class="col-1"><a href="?page=view_profile&id='.$row['client_id'].'">'.$widget->get_avatar().'</a></div>';
                } else {
                    $content .= '<div class="col-2"><a href="?page=view_profile&id='.$row['client_id'].'">'.$widget->get_avatar().'</a></div>';
                }                 
                $content .= '<div class="col"><a class="text-dark fw-bold" href="?page=view_profile&id='.$row['client_id'].'">'.$company_name.'</a></div>
                </div>
                ';
                $row_count++;
            }
            $content .= '</div>';
        } else {
            $content = '';
        }

        return $content;
    }

    function get_listing_count() {
        global $connect_db;
        $profile_id = $this->profile_id;

        // Get posts by the logged in user from the database
        $get_posts = $connect_db->prepare("SELECT 
        ID
        FROM posts WHERE user = ?");
        $get_posts->bind_param('i', $profile_id);
        // Execute search and get results
        $get_posts->execute();
        $result = $get_posts->get_result();

        return $result->num_rows;
    }

    function profile_box(){
        global $connect_db, $widget, $current_user;
        $profile_id = $this->profile_id;
        $page = $this->page;
        $col_size = $page == 'search' ? 'col-3' : 'col';
        $ref_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $is_following = $widget->is_following();

         $check_user = $connect_db->prepare("SELECT company_name, user_type FROM users WHERE ID = ?");
         $check_user -> bind_param('i', $profile_id);
         $check_user->execute();
         $check_user -> store_result();
         $check_user -> bind_result($company_name, $user_type);
         $check_user->fetch();
        
        $content = '
        <div class="card p-4 mb-4 border-0 shadow">
            <div class="row d-flex align-items-center mb-4">
            <div class="col-auto">
                <h4 class="h5 mb-0">'.$company_name.'</h4>
            </div>';
            if($page == 'search'){
                $content .='
                <div class="col text-end">
                    <a class="btn btn-light btn-sm small" href="?page=view_profile&id='.$profile_id.'" role="button">View Profile</a>
                </div>
                ';
            }            
            $content .='            
            </div>            
            <div class="row d-flex align-items-center">
                <div class="'.$col_size.'">
                '.$widget->get_avatar().'                  
                </div>
                <div class="col fw-bold">';
                if($user_type == 'company'){
                    $content .= '
                    <p class="mb-0">'.$widget->get_listing_count().' Listings</p>
                    <p class="mb-0">'.$widget->get_follower_count().' Followers</p>';
                } else {
                    $content .= '
                    <p class="mb-0">'.$widget->get_following_count().' Following</p>';
                }             
                $content .= '
                </div>
            </div>';

            if($page == 'search' && $current_user != $profile_id) {
                $content .='
                <div class="d-grid gap-2 mt-4">';
                if($is_following == false) {
                    $content .='<a class="btn btn-outline-dark border-light border-3 rounded-0 fw-bold" href="'.$ref_url.'&follow='.$profile_id.'" role="button">Follow</a>';
                } else {
                    $content .='<a class="following btn btn-outline-dark border-light border-3 rounded-0 fw-bold" href="'.$ref_url.'&unfollow='.$profile_id.'" role="button">Following</a>';
                }
                $content .='
                <a class="btn btn-dark rounded-0 fw-bold" href="?page=account_dashboard&action=create_message&recipient_id='.$profile_id.'" role="button">Send Inquiry</a>
                </div>
                ';
            }
            $content .='
        </div>
        ';

        return $content;
    }

    function browse_by_events() {
        // Require category arrays
        require(ROOT_PATH.'data/categories.php');

        $search  = array('(', ')', ' / ', ' - ',' ');
        $replace = array('', '', '_', '_', '_');

        $content = '
        <div class="card p-4 mb-4">
            <h4 class="h5 mb-4">Browse by Events</h4>
            <div class="mb-3">';

            foreach($event_types as $event_type){
                $slug = strtolower(str_replace($search, $replace, $event_type));
                $content .='<a href="?page=search&view_tag='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$event_type.'</a> ';
            }

            $content .='
            </div>
            
            <div>
            ';

            foreach($event_cat as $event){
                $slug = strtolower(str_replace($search, $replace, $event));
                $content .='<a href="?page=search&view_category='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$event.'</a> ';
            }
             
            $content .= '
            </div>
        </div>
        ';

        return $content;
    }

    function browse_by_venue() {
        // Require category arrays
        require(ROOT_PATH.'data/categories.php');

        $search  = array('(', ')', ' / ', ' - ',' ');
        $replace = array('', '', '_', '_', '_');

        $content = '
        <div class="card p-4 mb-4">
            <h4 class="h5 mb-4">Browse by Venue</h4>
            <div class="mb-3">';

            foreach($venue_type as $venue_type){
                $slug = strtolower(str_replace($search, $replace, $venue_type));
                $content .='<a href="?page=search&view_tag='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$venue_type.'</a> ';
            }

            $content .='
            </div>
            
            <div>
            ';

            foreach($venue_cat as $venue){
                $slug = strtolower(str_replace($search, $replace, $venue));
                $content .='<a href="?page=search&view_category='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$venue.'</a> ';
            }
             
            $content .= '
            </div>
        </div>
        ';

        return $content;
    }

    function get_messages() {
        global $connect_db, $widget, $current_user;
        $user_id = $this->user_id;
        $position = $this->position;

        $content ='';
        
        // Prepare sql statement to get messages
        $messages = $connect_db -> prepare('SELECT ID, thread_id, sender_id, recipient_id, message_subject, message_body, sender_status, recipient_status, message_date, is_first_message
        FROM messages WHERE (sender_id = ? OR recipient_id = ?) AND is_first_message = "1"');
        $messages -> bind_param('ii', $user_id, $user_id);
        $messages -> execute();
        $messages = $messages -> get_result();

        if($messages->num_rows > 0) {
            $i = 0;
            while ($message = $messages -> fetch_assoc()) {
                $i++;
                $col_size = $position == 'side' ? 'col-3' : 'col-2';
                $align_class = $position == 'side' ? 'd-flex align-items-center' : '';                
                $user = $current_user == $message['recipient_id'] ? $message['sender_id']: $message['recipient_id'];                                
                $widget->set_profile($user);

                // Prepare sql statement to get profile data
                $sender = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
                $sender->bind_param('i', $user);
                $sender->execute();
                $sender->store_result();
                $sender->bind_result($username);
                $sender->fetch();                

                $latest_message = $connect_db->prepare("SELECT message_body, sender_id, recipient_id, sender_status, recipient_status
                FROM messages WHERE thread_id = ? ORDER BY ID desc LIMIT 1");
                $latest_message->bind_param('i', $message['thread_id']);
                $latest_message->execute();
                $latest_message->store_result();
                $latest_message->bind_result($latestmsg, $senderid, $recipientid, $senderstatus, $recipientstatus);            
                $latest_message->fetch();
                $read_status = ($current_user == $senderid) ? $senderstatus : $recipientstatus;                
                if($read_status == 1){
                    $row_class = '';
                } else {
                    $row_class = $i % 2 == 0 ? 'bg-light' : '';
                }
                $unread_class = ($read_status == 0) ? 'unread fw-bold alert alert-primary rounded-0 border-0' : '';                             

                if($position == 'side'){
                    $content .= '<div class="row mb-0 gx-3 p-3 '.$align_class.' '.$row_class.' '.$unread_class.'">';                                        
                    $content .= '<div class="'.$col_size.'">';
                    $content .= $widget->get_avatar();
                    $content .= '</div>';
                    $content .= '<div class="col">';
                    $content .= '<a class="text-dark" href="?page=account_dashboard&view=messages&thread_id='.$message['thread_id'].'"><strong>'.$message['message_subject'].'</strong>';
                    $content .= '<p class="mb-0">'.substr(strip_tags($latestmsg),0,20).'...</p></a>';
                    $content .= '</div>';
                    $content .= '</div>';
                }  else {
                    $content .= '<div class="row p-3 mb-0 '.$row_class.' '.$unread_class.'">';
                    $content .= '<div class="col">';
                    $content .= '<div class="row gx-3">';                                        
                    $content .= '<div class="'.$col_size.'">';
                    $content .= $widget->get_avatar();
                    $content .= '</div>';
                    $content .= '<div class="col">';
                    $content .= '<a class="text-dark" href="?page=account_dashboard&view=messages&thread_id='.$message['thread_id'].'"><strong>'.$username.'</strong>';
                    $content .= '<p class="mb-0">'.$message['message_date'].'</p></a>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="col">';
                    $content .= '<p class="mb-0"><a class="text-dark" href="?page=account_dashboard&view=messages&thread_id='.$message['thread_id'].'"><strong>'.$message['message_subject'].'</strong></p>';
                    $content .= '<p class="mb-0"><a class="text-dark" href="?page=account_dashboard&view=messages&thread_id='.$message['thread_id'].'">'.substr(strip_tags($latestmsg),0,40).'...</p>';
                    $content .= '</div>';
                    $content .= '</div>';
                }                
            }
        } else {
            $content .= 'There are no messages in your inbox';
        }

        return $content;
        
    }

    function get_message_thread() {
        global $connect_db, $widget, $current_user;
        $thread_id = $this->thread_id;

        $content ='';
        
        // Prepare sql statement to get messages
        $messages = $connect_db -> prepare('SELECT ID, thread_id, sender_id, recipient_id, message_subject, message_body, sender_status, recipient_status, message_date, is_first_message
        FROM messages WHERE thread_id = ?');
        $messages -> bind_param('i', $thread_id);
        $messages -> execute();
        $messages = $messages -> get_result();

        if($messages->num_rows > 0) {
            $i =0;            
            while ($message = $messages -> fetch_assoc()) {
                $i++;
                $sender_name = '';
                $recipient_name = '';

                // Mark as read if applicable
                $message_id = $message['ID'];
                $read_status = 1;
                if($message['recipient_id'] === $current_user){

                    $mark_read = "UPDATE messages SET
                        recipient_status ='$read_status'
                        WHERE ID='$message_id'";

                    // If successful, show confirmation text and link
                    if ($connect_db->query($mark_read) === TRUE) {
                        // marked as read
                    }
                }
                // Prepare sql statement to get profile data
                $sender = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
                $sender->bind_param('i', $message['sender_id']);
                $sender->execute();  
                $sender->store_result();
                $sender->bind_result($sender_name);
                $sender->fetch();

                $recipient = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
                $recipient->bind_param('i', $message['recipient_id']);
                $recipient->execute();
                $recipient->store_result();
                $recipient->bind_result($recipient_name);
                $recipient->fetch();

                // Display message content
                $content .= $i === 1 ? '<h5 class="border-bottom mb-3 pb-3 border-light">'.$message['message_subject'].'</h5>' : '';
                $content .= '<div class="row gx-3 p-3">';                                        
                $content .= '<div class="col-1">';
                $widget->set_profile($message['sender_id']);
                $content .= $widget->get_avatar();
                $content .= '</div>';
                $content .= '<div class="col">';
                $content .= '<div class="row d-flex align-items-center">';
                $content .= '<div class="col">';
                $content .= '<strong>'.$sender_name.'</strong>';
                $content .= '<p class="small">To: '.$recipient_name.'</p>';
                $content .= '</div>';
                $content .= '<div class="col text-end">';
                $content .= '<p class="text-muteed">'.$message['message_date'].'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= $message['message_body'];
                $content .= '</div>';
                $content .= '</div>';
            }
        }

        return $content;
        
    }

    function get_thread_info() {
        global $connect_db, $current_user;
        $thread_id = $this->thread_id;

        $message_info = array();
        
        // Prepare sql statement to get messages
        $messages = $connect_db -> prepare('SELECT sender_id, recipient_id, message_subject
        FROM messages WHERE thread_id = ? ORDER BY ID desc LIMIT 1');
        $messages -> bind_param('i', $thread_id);
        $messages -> execute();
        $messages = $messages -> get_result();

        if($messages->num_rows > 0) {
            $i =0;            
            while ($message = $messages -> fetch_assoc()) {
                $i++;
                if($i === 1){
                    $message_info['subject'] = $message['message_subject'];
                    $message_info['recipient_id'] = $message['sender_id'] == $current_user ? $message['recipient_id'] : $message['sender_id'];
                }               

            }
        }

        return $message_info;
        
    }

    function get_unread_count() {
        global $connect_db;
        $user_id = $this->user_id;

        $content ='';
        
        // Prepare sql statement to get messages
        $recipent_status = 0;
        $messages = $connect_db -> prepare('SELECT ID
        FROM messages WHERE recipient_id = ? AND recipient_status = ?');
        $messages -> bind_param('ii', $user_id, $recipent_status);
        $messages -> execute();
        $messages = $messages -> get_result();


        return $messages->num_rows;
        
    }

    function is_following(){
        global $connect_db;
        $current_user = $this->user_id;
        $profile_id = $this->profile_id;
        $is_following = false;          
        $check_follow = $connect_db->prepare("SELECT company_id FROM follows WHERE client_id = ? AND company_id = ?");
        $check_follow -> bind_param('ii', $current_user, $profile_id);
        $check_follow->execute();
        $check_follow -> store_result();
        $check_follow -> bind_result($company_id);
        $check_follow->fetch();

        if($check_follow->num_rows == 1) {
            $is_following = true;
        }

        return $is_following;
    }
}