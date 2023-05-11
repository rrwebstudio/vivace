<?php

class Widget {

    public $user_id;

    function set_user($user_id) {
        $this->user_id = $user_id;
    }

    function get_avatar() {
        $user_id = $this->user_id;


        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        // Prepare sql statement to get profile data
        $check_user = $connect_db->prepare("SELECT company_name, avatar FROM users WHERE ID = ?");
        $check_user -> bind_param('s', $user_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($company_name, $avatar);
        // statement result
        if($check_user->num_rows == 1) {                                               
            // Get result
          if($check_user->fetch()) {
                if($avatar) {
                    $avatar = '<img class="shadow img-fluid" src="'.$avatar.'">';
                } else {
                    $avatar = '<img class="rounded-circle img-fluid" src="https://ui-avatars.com/api/?size=128&name='.$company_name.'">';
                }
          }
        }

        // Close connection to db
        $connect_db->close();

        return $avatar;
    }

    function dashboard_links() {
        $dashboard_home = $_SESSION['account_type'] == 'company' ? 'My Listings' : 'Feed';
        $nav = '
        <div class="card p-4 mb-4">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard">
                    <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                        <path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                    </svg> 
                    '.$dashboard_home.'</a></li>
                    <li class="list-group-item"><a class="text-dark fw-bold" href="?page=account_dashboard&view=discounts">
                    <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                    <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z"/>
                    <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z"/>
                    </svg>
                    My Discounts</a></li>
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

                    if($_SESSION['account_type'] == 'company'){
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
        $avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null;
        $content = '
        <div class="px-5 py-4 bg-black position-relative text-white shadow-lg" style="z-index: 1;">
            <div class="row mb-4">                
                <div class="col-auto">';

                if ($avatar) {
                    $content .='
                        <img src="'.$avatar.'">
                    ';
                } else {
                    $content .='
                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name='.$_SESSION['company_name'].'">
                    ';
                }


                $content .='</div>
                <div class="col">
                    <h2 class="h5 fw-bold">'.$_SESSION['company_name'].'</h2>
                    <p><a href="?page=account_dashboard&action=edit_profile&update=photo" class="text-white">Upload Company Logo</a></p>
                </div>
            </div> 
            <div class="row gx-2">                
                <div class="col-auto mx-auto">
                <a class="btn btn-secondary" href="?page=view_profile&id='.$_SESSION['user_id'].'" role="button">View Profile</a>
                </div>              
            </div>            
        </div>
        ';

        return $content;
    }

    function user_data(){
        return '
        <div class="card dash-card border-0 rounded-0 position-absolute top-0 start-0 shadow mx-4" style="z-index: 1;">
            <article class="card-body px-5 py-4">
            <h1 class="card-title mt-2 mb-4 h2">Hi, '.$_SESSION['first_name'].'</h1>
            <div class="row">
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Company Name:</label>
                    '.$_SESSION['company_name'].'
                    </div> 
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Company Address:</label>
                    '.$_SESSION['company_address'].'
                    </div>
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Phone Number:</label>
                    '.$_SESSION['phone_number'].'
                    </div> 
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Mobile Number:</label>
                    '.$_SESSION['mobile_number'].'
                    </div> 
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Account Type:</label>
                    '.$_SESSION['account_type'].'
                    </div>
                </div>
                <div class="col">
                    <div class="mb-4">
                    <label class="d-block h6">Total Listings:</label>
                    '.$_SESSION['account_type'].'
                    </div>
                </div>
            </div>
            </article>
        </div>
        ';
    }

    function welcome_box() {
        return '
        <div class="card p-4 mb-4">
            <h4 class="h5 mb-4">Welcome to your Dashboard</h4>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur mollis eu risus eget suscipit.
            Suspendisse sollicitudin purus ac magna bibendum, ac hendrerit mauris sagittis.
            Integer dictum, metus eget blandit convallis, massa arcu faucibus lacus, dapibus gravida justo magna sit amet metus. 
        </div>
        ';
    }

    function total_sales() {
        // Format money
        $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
        $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        return '
        <div class="alert alert-success text-center border-2 mb-4 py-5">
            <h4 class="h3 mb-0 fw-bold"><span class="small text-muted position-relative" style="top: -10px;">₱</span> '.$money_formatter->format(576894).'</h4>
            <p class="text-success fw-bold mb-0">Total Earnings</p>
        </div>
        ';
    }

    function followers() {
        return '
        <div class="card p-4 mb-4">
            <h4 class="h3 mb-0 fw-bold text-center">1095</h4>
            <p class="text-success fw-bold mb-0 text-center">Total Followers</p>
            <div class="row mt-3">
                <div class="col">
                Newest Followers:

                <p class="mb-0 fw-bold"><a class="text-dark" href="#">rrmoonie</a>, <a class="text-dark href="#">menma</a>, <a class="text-dark href="#">pepper_dinnner</a></p>
                </div>
            </div>
        </div>
        ';
    }

    function message_box() {
        return '
        <div class="card p-4 mb-4">
            <h4 class="h5 mb-4">Messages
            <span class="badge rounded-pill bg-info">
                1
                <span class="visually-hidden">unread messages</span>
            </span>
            </h4>
            <a class="row unread fw-bold py-3 px-1 bg-light d-flex align-items-center text-dark" href="#">
                <div class="col-auto">
                <img class="rounded-circle" src="https://ui-avatars.com/api/?name=John+Doe">
                </div>
                <div class="col-auto">
                    <span class="d-block">Hello!</span>
                    <p class="fst-italic text-muted mb-0">I have an inquirry regarding...</p>
                </div>
            </a>
            <a class="row py-3 px-1 d-flex align-items-center text-dark" href="#">
                <div class="col-auto">
                <img class="rounded-circle" src="https://ui-avatars.com/api/?name=F+S">
                </div>
                <div class="col-auto">
                    <span class="d-block">Birthday Party Set</span>
                    <p class="fst-italic text-muted mb-0">I have an inquirry regarding...</p>
                </div>
            </a>
            <a class="row py-3 px-1 d-flex align-items-center text-dark" href="#">
                <div class="col-auto">
                <img class="rounded-circle" src="https://ui-avatars.com/api/?name=M+R">
                </div>
                <div class="col-auto">
                    <span class="d-block">Question about wedding set</span>
                    <p class="fst-italic text-muted mb-0">I have an inquirry regarding...</p>
                </div>
            </a>
        </div>
        ';
    }

    function get_listings(){
        $user_id = $this->user_id;
        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        // Get posts by the logged in user from the database
        $get_posts = $connect_db->prepare("SELECT 
        ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo
        FROM posts WHERE user = ?");
        $get_posts->bind_param('i', $user_id);
        // Execute search and get results
        $get_posts->execute();
        $result = $get_posts->get_result();

        if($result->num_rows > 0) {
            $listings = '<div id="listings">';
            while ($row = $result -> fetch_assoc()) {
                //Get poster's contact details from database
                $get_author = $connect_db->prepare("SELECT email, company_name FROM users WHERE ID = ?");
                $get_author->bind_param('i', $row['user']);
                $get_author->execute();
                $get_author->store_result();
                $get_author->bind_result($author_email, $author_company);
                $get_author->fetch();
                // Get post thumbnail
                $bg_setphoto = SITE_URL.'/uploads/'.$row['set_photo'];
                // Format money
                $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
                $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
                // Show result
                $listings .= '
                <div class=" post post-'.$row['ID'].' card p-0 shadow mb-4">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-3">
                                <div class="ratio ratio-1x1 bg-image rounded-start" style="background-image:url('.$bg_setphoto.')"></div>
                            </div>
                            <div class="col p-3">                                                      
                                <div class="post-content position-relative" style="height: 165px; overflow: hidden;">
                                    <h2 class="card-title h4 mb-1 d-flex align-items-center"><span class="listing_title">'.$row['title'].' </span></h2>'
                                    .$row['content'].
                                '</div>
                            </div>  
                            <div class="col-4 py-3 pe-4">
                            <div class="alert alert-success py-1 px-2">
                            <a class="alert-link" href="?page=search&view_company='.$row['user'].'">'.$author_company.'</a>
                                <div class="row">
                                    <div class="col fw-bold price-text">
                                    ₱'.$money_formatter->format($row['rent_price']).'
                                    </div>
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
                            <a href="#" class="btn rounded-0 d-inline py-1 px-2 small text-uppercase" style="background-color: #ffc107;">Voucher Code 1</a>
                            <a href="#" class="btn rounded-0 d-inline py-1 px-2 small text-uppercase" style="background-color: #ffc107;">Voucher Code 2</a>';

                            if($user_id == $_SESSION['user_id']) {
                                $listings .= '<span class="bi ms-2 mt-2 d-block edit_listing"><a class="text-dark fw-bold" href="?page=account_dashboard&action=edit_listing&id='.$row['ID'].'">
                                <svg class="bi me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                                Edit Listing</a></span>';
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

        // Close connection to db
        $connect_db->close();

        return $listings;
    }

    function profile_user_data(){
        $user_id = $this->user_id;
        
        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        $check_user = $connect_db->prepare("SELECT email, company_name, company_address, first_name, last_name, phone_number, mobile_number, user_type, avatar FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $user_id);
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
                    <h4 class="h5 mb-4">'.$company_name.'</h4>
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
        }        

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
}